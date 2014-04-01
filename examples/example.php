<?php

require_once '../src/Lingotek.php';

use Lingotek\Dev;

$access_token = 'b068b8d9-c35b-3139-9fe2-e22ee7998d9f'; // sandbox token

$client = new \Lingotek\RestClient(array(
  'access_token' => $access_token
    ));

// Get Community
$result = $client->get('community');
$community_id = $result->decoded_response->entities[0]->properties->id; // get first community

echo "community_id: $community_id\n";

// Create Project
$params = array(
  'title' => 'My New Project',
  'community_id' => $community_id
);
$result = $client->post('project', $params);
$project_id = substr($result->headers->content_location, strrpos($result->headers->content_location, '/') + 1); // temp until API 5 makes newly created id is available in JSON bodycms

echo "project_id: $project_id (created)\n";

sleep(2);

// Create Document
$params = array(
  'title' => 'My New Document',
  'content' => 'The quick brown fox jumped over the lazy dog. (Time:' . time() . ')',
  'format' => 'PLAINTEXT',
  'locale_code' => 'en_US',
  'project_id' => $project_id
);
$result = $client->post('document', $params);
$document_id = substr($result->headers->content_location, strrpos($result->headers->content_location, '/') + 1); // temp until API 5 makes newly created id is available in JSON body

echo "document_id: $document_id (created)\n";

// Add Translation Target
$locale_code = 'zh_CN'; // Chinese
$params = array(
  'document_id' => $document_id,
  'locale_code' => $locale_code
);
$result = $client->post('target', $params);
print_r($result);

echo "translation requested: $document_id => $locale_code \n";

sleep(3);

// Download Translation
$params = array(
  'document_id' => $document_id,
  'locale_code' => $locale_code,
);
$translation = $client->get('document/{document_id}/content', $params);

echo "$translation \n";

$translations[] = $translation;
// Resources
$results = array(
  'community_id' => $community_id,
  'project_id' => $project_id,
  'document_id' => $document_id,
  'translations' => $translations
);

print_r($results);