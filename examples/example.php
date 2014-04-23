<?php

require_once '../src/Lingotek.php';

use Lingotek\Dev;

$access_token = '5ef6af47-613e-37ff-9d9b-a47f1c750244'; //'b068b8d9-c35b-3139-9fe2-e22ee7998d9f'; // sandbox token

$client = new \Lingotek\RestClient(array(
  'access_token' => $access_token
    ));

// Get Community
$result = $client->get('community');
$community_id = $result->decoded_response->entities[0]->properties->id; // get first community

echo "[" . $result->info->http_code . "] community_id: $community_id\n";

// Grab the "Sample Project"

$params = array(
  'community_id' => $community_id
);
$result = $client->get('project', $params);
$projects = $result->decoded_response->entities;
foreach ($projects as $project) {
  //if project name is "Sample Project" then use the id
  if ($project->properties->title == "Sample Project") {
    $project_id = $project->properties->id;
    echo "[" . $result->info->http_code . "] project_id: $project_id (existing: \"" . $project->properties->title . "\")\n";
  }
}

if (is_null($project_id)) {
// Create Project
  $params = array(
    'title' => 'My New Project',
    'community_id' => $community_id,
    'workflow_id' => 'c675bd20-0688-11e2-892e-0800200c9a66' // machine translation workflow
  );
  $result = $client->post('project', $params);
  $project_id = substr($result->headers->content_location, strrpos($result->headers->content_location, '/') + 1); // temp until API 5 makes newly created id is available in JSON bodycms

  echo "[" . $result->info->http_code . "] project_id: $project_id (created)\n";
}

// Create Document
$doc_params = array(
  'title' => 'My New Document ' . time(),
  'content' => 'The quick brown fox jumped over the lazy dog.',
  'format' => 'PLAINTEXT',
  'locale_code' => 'en_US',
  'project_id' => $project_id
);
$result = $client->post('document', $doc_params);
echo "\theaders->content_location: " . $result->headers->content_location . "\n";
$document_id = substr($result->headers->content_location, strrpos($result->headers->content_location, '/') + 1); // temp until API 5 makes newly created id is available in JSON body

echo "[" . $result->info->http_code . "] document_id: $document_id (created: \"" . $doc_params['title'] . "\")\n";

// Check Import Progress
$params = array(
  'document_id' => $document_id
);
$i = 0;
$done_processing = FALSE;
while (!$done_processing) {
  $i++;
  echo "\t#$i | check status: ";
  try {
    $result = $client->get('document', $params);
  } catch (Exception $e) {
    echo " [" . $e->getCode() . "] ";
    echo "importing\n"; //"(" . $e->getMessage() . ")\n";
    sleep(3);
    continue;
  }
  echo " [" . $result->info->http_code . "] ";
  if (substr($result->info->http_code, 0, 1) == "2") {
    $done_processing = TRUE;
    echo "imported (done!)";
  }
  echo "\n";
  if ($i >= 30) {
    break;
  }
}

// Add Translation Targets
$locale_codes = array('zh_CN'); // Chinese
$params = array(
  'document_id' => $document_id,
    //'workflow_id' => 'c675bd20-0688-11e2-892e-0800200c9a66' // machine translation workflow
);
foreach ($locale_codes as $locale_code) {
  $params['locale_code'] = $locale_code;
  $result = $client->post('target', $params);
  echo "[" . $result->info->http_code . "] translation requested: $document_id => $locale_code \n";
}

// Check Overall Translation Progress
$params = array(
  'document_id' => $document_id,
);
$done = FALSE;
$i = 0;
while (!$done) {
  $i++;
  echo "\t#$i | translation progress: [" . $result->info->http_code . "] ";
  $result = $client->get('document/{document_id}/status', $params);
  $progress = isset($result->decoded_response->properties->progress) ? $result->decoded_response->properties->progress : "x";
  echo $progress . "%";
  if ($progress == 100) {
    echo " (done!)";
    $done = TRUE;
  }
  else {
    sleep(3);
  }
  echo "\n";
  if ($i >= 50) {
    break;
  }
}

echo "download translations: " . implode(", ", $locale_codes) . "\n";

// Download Translation
$params = array(
  'document_id' => $document_id,
  'locale_code' => $locale_code,
);
foreach ($locale_codes as $locale_code) {
  $params['locale_code'] = $locale_code;
  $result = $client->get('document/{document_id}/content', $params);
  $translation = $result->decoded_response;
  $translations[$locale_code] = $translation;
}

// Resources
$results = array(
  'community_id' => $community_id,
  'project_id' => $project_id,
  'document_id' => $document_id,
  'source' => array($doc_params['locale_code'] => $doc_params['content']),
  'target' => $translations
);

echo "\n--- results ---\n";
print_r($results);
