<?php

namespace Lingotek;

use Lingotek\RestClient;
use Lingotek\Dev;
use Lingotek\Exception;
use Lingotek\InvalidArgumentException;
use Lingotek\RestClientException;
use Lingotek\NotFound;
use Lingotek\Unauthorized;
use Lingotek\InvalidUrlPatternException;
use Lingotek\LingotekApiInterface;

/**
 * Lingotek SDK
 *
 * Copyright (c) 2014 Lingotek, Inc.
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 *
 *
 * Usage: readme.md
 */

class LingotekApi implements LingotekApiInterface {
  	protected $client;

  	public function __construct($access_token) {
    	$this->client = new RestClient(array(
    		'access_token' => $access_token,
      		'base_url' => RestClient::URL_PRODUCTION
  		));
	}

  /**
   * Get all supported locales
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getLocales() {
    $result = $this->client->get('locale/');
    return $result;
  }

  /**
   * Add a new document to an existing project
   * @param $args
   * Required arguments to be included in $args:
   *  title (Document name)
   *  locale_code (Document language)
   *  project_id (Project to associate the document to)
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function addDocument($args) {
    $result = $this->client->post('document', $args);
    return $result;
  }

  /**
   * Update an existing document
   * @param $args
   * Required arguments to be included in $args:
   *  'id' => 98dee54e-d5c7-4935-be0f-ac5a3617be00
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function patchDocument($args) {
    $result = $this->client->patch('document/{id}', $args);
    return $result;
  }

  /**
   * Delete an existing Document
   * @param $id
   *  Required string that represents the Document ID
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function deleteDocument($id) {
    $result = $this->client->delete('document/{id}',['id' => $id]);
    return $result;
  }

  /**
   * Get a document which the active user has access to
   * @param $id
   *  Required string that represents the Document ID
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getDocumentInfo($id) {
    $result = $this->client->get('document/{id}',['id' => $id]);
    return $result;
  }

  /**
   * Get the status of an existing document
   * @param $id
   *  Required string that represents the Document ID
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getDocumentStatus($id) {
    $result = $this->client->get('document/{id}/status', ['id' => $id]);
    return $result;
  }

  /**
   * Get the status of all translations for an existing document
   * @param $id
   *  Required string that represents the Document ID
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getDocumentTranslationStatuses($id) {
    $result = $this->client->get('document/{id}/translation', ['id' => $id]);
    return $result;
  }

  /**
   * Get the status of a specific translation for an existing document
   * @param $id
   *  Required string Id of the document to which a translation will be added
   * @param $locale
   *  Required string Locale code for the translation
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getDocumentTranslationStatus($id, $locale) {
    $result = $this->client->get('document/{id}/translation/{locale}', ['id' => $id, 'locale' => $locale]);
    return $result;
  }

  /**
   * Request translations for an existing document
   * @param $id
   *  Required string Id of the document to which a translation will be added
   * @param $locale_code
   *  Required string Locale Code of the language to use for the translation
   * @param $workflow_id
   *  Optional string Id of the workflow to use when creating the translation
   * @param $due_date
   *  Optional string Due date to use for the translation
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function addTranslation($id, $locale, $workflow_id = NULL) {
    $args = [
      'id' => $id,
      'locale_code' => $locale
    ];
    if ($workflow_id) {
      $args['workflow_id'] = $workflow_id;
    }
    $result = $this->client->post('document/{id}/translation', $args);
    return $result;
  }

  /**
   * Request translations for an existing document
   * @param $id
   *  Required string Id of the document to which a translation will be added
   * @param $locale_code
   *  Required string Locale Code of the language to use for the translation
   * @param $workflow_id
   *  Optional string Id of the workflow to use when creating the translation
   * @param $due_date
   *  Optional string Due date to use for the translation
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getTranslation($id, $locale, $useSource = FALSE) {
    $result = $this->client->get('document/{id}/content', ['id' => $id, 'locale_code' => $locale, 'use_source' => $useSource]);
    return $result;
  }

  public function deleteTranslation($id, $locale) {
    $result = $this->client->delete('document/{id}/translation', ['id' => $id, 'locale_code' => $locale]);
    return $result;
  }

  public function getCommunities() {
    $result = $this->client->get('community', ['limit' => 100]);
    return $result;
  }

  public function getProject($project_id) {
    $result = $this->client->get('project/{id}', ['id' => $project_id]);
    return $result;
  }

  public function getProjects($community_id) {
    $result = $this->client->get('project', ['community_id' => $community_id, 'limit' => 1000]);
    return $result;
  }

  public function getVaults($community_id) {
    $result = $this->client->get('vault', ['limit' => 100, 'is_owned' => TRUE]);
    return $result;
  }

  public function getWorkflows($community_id) {
    $result = $this->client->get('workflow', ['community_id'=>$community_id, 'limit'=>1000]);
    return $result;
  }

  public function getFilters() {
    $result = $this->client->get('filter', ['limit' => 1000]);
    return $result;
  }
}
