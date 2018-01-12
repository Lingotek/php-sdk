<?php

require_once '../src/Lingotek.php';

use Lingotek\Dev;

$access_token = ''; // sandbox token
$community_id = ''; // specify the "Sandbox" community

$client = new \Lingotek\HttpClient\RestClient(array(
  'access_token' => $access_token,
  'base_url' => \Lingotek\HttpClient\RestClient::URL_PRODUCTION
));

// Get Community
$result = $client->get('community/' . $community_id);

$community = is_null($community_id) ? $result->decoded_response->entities[0] : $result->decoded_response; // get first community
$community_id = $community->properties->id;
$community_title = $community->properties->title;
echo "[" . $result->info->http_code . "] community_id: $community_id (\"$community_title\")\n";

// Grab the "Sample Project"
$project_title = "Sample Project";
$project_id = NULL;
$params = array(
  'community_id' => $community_id,
  'limit' => 5000
);
$result = $client->get('project', $params);
$projects = $result->decoded_response->entities;
foreach ($projects as $project) {
  if ($project->properties->title == $project_title) { //if project name is $project_title then use the id
    $project_id = $project->properties->id;
    echo "[" . $result->info->http_code . "] project_id: $project_id (existing: \"" . $project->properties->title . "\")\n";
  }
}

if (is_null($project_id)) {
// Create Project (when project with $project_title was not found)
  $params = array(
    'title' => $project_title,
    'community_id' => $community_id,
    'workflow_id' => 'c675bd20-0688-11e2-892e-0800200c9a66' // machine translation workflow
  );
  $result = $client->post('project', $params);
  $project_id = substr($result->headers->content_location, strrpos($result->headers->content_location, '/') + 1); // temp until API 5 makes newly created id is available in JSON body
  echo "[" . $result->info->http_code . "] project_id: $project_id (created: \"$project_title\")\n";
}

// Create Document
$doc_params = array(
  'title' => 'Sample Document ' . time(), // the title of the document as named on TMS
  'content' => (object) array('title' => 'Sample Document', 'body' => 'The quick brown fox jumped over the lazy dog.'), // content for translation
  'format' => 'JSON',
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
    $result = $client->get('document/{document_id}/status', $params);
  } catch (Exception $e) {
    echo " [" . $e->getCode() . "] "; //"(" . $e->getMessage() . ")\n";
    echo "importing\n";
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
$locale_codes = array('zh_CN','es_MX');
$params = array(
  'document_id' => $document_id,
  'workflow_id' => 'c675bd20-0688-11e2-892e-0800200c9a66' // machine translation workflow
);
foreach ($locale_codes as $locale_code) {
  $params['locale_code'] = $locale_code;
  $result = $client->post('document/{document_id}/translation', $params);
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