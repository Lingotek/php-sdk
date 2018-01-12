<?php

require_once '../src/Lingotek.php';

use Lingotek\Dev;

$community_title = 'demos';
$project_title = 'PHP-SDK Demo Project';
$workflow_title = 'Machine Translation';
$access_token = '';
$community_id = '';
$project_id = '';
$workflow_id = '';

// Instantiate a LingotekApi object
$lingotekApi = new \Lingotek\API\LingotekApi('509ee59f-02c9-4a15-b787-652ea91a1284');
$response = $lingotekApi->getCommunities();

// Get the community
$communities = $lingotekApi->getCommunities()['entities'];
foreach ($communities as $index => $information) {
  if ($information->properties->title === $community_title) {
    $community_id = $information->properties->id;
    break;
  }
}

echo 'Community is: ' . $community_title . ' (' . $community_id . ')' . "\n";

// Get the project
$projects = $lingotekApi->getProjects($community_id)['entities'];
foreach ($projects as $index => $information) {
  if ($information->properties->title === $project_title) {
    $project_id = $information->properties->id;
  }
}

echo 'Project is ' . $project_title . ' (' . $project_id . ')' . "\n";

// Get the workflow (machine translation)
$workflows = $lingotekApi->getWorkflows($community_id)['entities'];
foreach ($workflows as $index => $information) {
  if ($information->properties->title === $workflow_title) {
    $workflow_id = $information->properties->id;
  }
 }
 
// Create Document
echo 'Workflow is ' . $workflow_title . ' (' . $workflow_id . ')' . "\n";

// Add a document
$doc_params = array(
  'title' => 'Sample Document ' . time(), // the title of the document as named on TMS
  'content' => (object) array('title' => 'Sample Document', 'body' => 'The quick brown fox jumped over the lazy dog.'), // content for translation
  'format' => 'JSON',
  'locale_code' => 'en_US',
  'project_id' => $project_id
);

$result = $lingotekApi->addDocument($doc_params);
$response_json = $result->response;
$response = json_decode($response_json);
$document_id = $response->properties->id;
 
echo 'Added document to Lingotek TMS: ' . $doc_params['title'] . ' (' . $document_id . ')' . "\n";

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
    $result = $lingotekApi->getDocumentStatus($document_id);
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

echo 'Document finished importing' . "\n";

// Add translation targets
$locale_codes = array('zh_CN','es_MX');
foreach ($locale_codes as $locale_code) {
  $result = $lingotekApi->addTranslation($document_id, $locale_code, $workflow_id);
}

echo 'Added target languages to Lingotek TMS document' . "\n";

$done = FALSE;
$i = 0;
while (!$done) {
  $i++;
  echo "\t#$i | translation progress: [" . $result->info->http_code . "] ";
  $result = $lingotekApi->getDocumentStatus($document_id);
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

echo 'Translations are finished' . "\n";

foreach ($locale_codes as $locale_code) {
  $result = $lingotekApi->getTranslation($document_id, $locale_code);
  $translation = $result->decoded_response;
  echo 'Title for ' . $locale_code . ': ' . $translation->title . "\n";
  echo 'Body for ' . $locale_code . ': ' . $translation->body . "\n\n";
}
