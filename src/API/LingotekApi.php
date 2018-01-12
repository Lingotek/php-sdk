<?php

namespace Lingotek\API;

use Lingotek\HttpClient\RestClient;
use Lingotek\Dev\Dev;
use Lingotek\API\Exception;
use Lingotek\API\InvalidArgumentException;
use Lingotek\API\RestClientException;
use Lingotek\API\NotFound;
use Lingotek\API\Unauthorized;
use Lingotek\API\InvalidUrlPatternException;
use Lingotek\API\LingotekApiInterface;

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
   * {@inheritdoc}
   */
  public function getLocales() {
    $result = $this->client->get('locale/');
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function addDocument($args) {
    $result = $this->client->post('document', $args);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function patchDocument($args) {
    $result = $this->client->patch('document/{id}', $args);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteDocument($id) {
    $result = $this->client->delete('document/{id}',['id' => $id]);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getDocumentInfo($id) {
    $result = $this->client->get('document/{id}',['id' => $id]);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getDocumentStatus($id) {
    $result = $this->client->get('document/{id}/status', ['id' => $id]);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getDocumentTranslationStatuses($id) {
    $result = $this->client->get('document/{id}/translation', ['id' => $id]);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getDocumentTranslationStatus($id, $locale) {
    $result = $this->client->get('document/{id}/translation/{locale}', ['id' => $id, 'locale' => $locale]);
    return $result;
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function getTranslation($id, $locale, $useSource = FALSE) {
    $result = $this->client->get('document/{id}/content', ['id' => $id, 'locale_code' => $locale, 'use_source' => $useSource]);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteTranslation($id, $locale) {
    $result = $this->client->delete('document/{id}/translation/{locale}', ['id' => $id, 'locale' => $locale]);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getCommunities($args = ['limit' => 1000]) {
    $result = $this->client->get('community', $args);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getProject($project_id) {
    $result = $this->client->get('project/{id}', ['id' => $project_id]);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getProjects($community_id, $args = ['limit' => 1000]) {
    $args['community_id'] = $community_id;
    $result = $this->client->get('project', $args);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getVaults($args = ['limit' => 100, 'is_owned' => TRUE]) {
    $result = $this->client->get('vault', $args);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getWorkflows($community_id, $args = ['limit' => 1000]) {
    $args['community_id'] = $community_id;
    $result = $this->client->get('workflow', $args);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters($args = ['limit' => 1000]) {
    $result = $this->client->get('filter', $args);
    return $result;
  }
}
