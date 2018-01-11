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
    	$this->$client = new RestClient(array(
    		'access_token' => $access_token,
      		'base_url' => RestClient::URL_PRODUCTION
  		));
	}
  public function getLocales() {
    // $this->logger->debug('Starting Locales request: /api/locale with args [limit => 1000]');
    // /** @var ResponseInterface $response */
    // try {
    //   $response = $this->lingotekClient->get('/api/locale', ['limit' => 1000]);
    //   if ($response->getStatusCode() == Response::HTTP_OK) {
    //     $data = json_decode($response->getBody(), TRUE);
    //     $this->logger->debug('getLocales response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    //     return $data;
    //   }
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error requesting locales: %message.', ['%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Error requesting locales: ' . $e->getMessage());
    // }
    // return FALSE;
  }

  public function addDocument($args) {
    // try {
    //   $this->logger->debug('Lingotek::addDocument (POST /api/document) called with ' . var_export($args, TRUE));
    //   $response = $this->lingotekClient->post('/api/document', $args, TRUE);
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error adding document: %message.', ['%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Error adding document: ' . $e->getMessage());
    // }
    // if ($response->getStatusCode() == Response::HTTP_ACCEPTED) {
    //   $data = json_decode($response->getBody(), TRUE);
    //   $this->logger->debug('addDocument response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    //   if (!empty($data['properties']['id'])) {
    //     return $data['properties']['id'];
    //   }
    // }
    // // TODO: log warning
    // return FALSE;
  }

  public function patchDocument($id, $args) {
    // try {
    //   $this->logger->debug('Lingotek::pathDocument (PATCH /api/document) called with id %id and args %args', ['%id' => $id, '%args' => var_export($args, TRUE)]);
    //   $response = $this->lingotekClient->patch('/api/document/' . $id, $args);
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error updating document: %message.', ['%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to patch (update) document: ' . $e->getMessage());
    // }
    // $this->logger->debug('patchDocument response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $response;
  }

  public function deleteDocument($id) {
    // try {
    //   $this->logger->debug('Lingotek::deleteDocument called with id ' . $id);
    //   $response = $this->lingotekClient->delete('/api/document' . '/' . $id);
    // }
    // catch (\Exception $e) {
    //   $http_status_code = $e->getCode();
    //   if ($http_status_code === Response::HTTP_NOT_FOUND) {
    //     $this->logger->error('Error deleting document: %message.', ['%message' =>  $e->getMessage()]);
    //     return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
    //   }
    //   $this->logger->error('Error deleting document: %message.', ['%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to delete document: ' . $e->getMessage(), $http_status_code, $e);
    // }
    // $this->logger->debug('deleteDocument response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $response;
  }

  public function getDocumentInfo($id) {
    // try {
    //   $this->logger->debug('Lingotek::getDocumentInfo called with id ' . $id);
    //   $response = $this->lingotekClient->get('/api/document/' . $id);
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting document info: %message.', ['%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to get document: ' . $e->getMessage());
    // }
    // $this->logger->debug('getDocumentInfo response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $response;
  }

  public function getDocumentStatus($id) {
    // try {
    //   $this->logger->debug('Lingotek::getDocumentStatus called with id ' . $id);
    //   $response = $this->lingotekClient->get('/api/document/' . $id . '/status');
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting document status: %message.', ['%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to get document status: ' . $e->getMessage());
    // }
    // $this->logger->debug('getDocumentStatus response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $response;
  }

  public function getDocumentTranslationStatuses($id) {
    try {
      $response = $this->client->get('/api/document/' . $id . '/translation');
    }
    catch (\Exception $e) {
      // log error
    	throw new RestClientException();
    }
      // log success
    return $response;
  }

  public function getDocumentTranslationStatus($id, $locale) {
    // try {
    //   $this->logger->debug('Lingotek::getDocumentTranslationStatus called with %id and %locale', ['%id' => $id, '%locale' => $locale]);
    //   $response = $this->lingotekClient->get('/api/document/' . $id . '/translation');
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting document translation status (%id, %locale): %message.',
    //     ['%id' => $id, '%locale' => $locale, '%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to get document translation status: ' . $e->getMessage());
    // }
    // $this->logger->debug('getDocumentTranslationStatus response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $response;
  }

  public function addTranslation($id, $locale, $workflow_id = NULL) {
    // try {
    //   $this->logger->debug('Lingotek::addTranslation called with id ' . $id . ' and locale ' . $locale);
    //   $args = ['locale_code' => $locale];
    //   if ($workflow_id) {
    //     $args['workflow_id'] = $workflow_id;
    //   }
    //   $response = $this->lingotekClient->post('/api/document/' . $id . '/translation', $args);
    // }
    // catch (\Exception $e) {
    //   // If the problem is that the translation already exist, don't fail.
    //   if ($e->getCode() === Response::HTTP_BAD_REQUEST) {
    //     $responseBody = json_decode($e->getResponse()->getBody(), TRUE);
    //     if ($responseBody['messages'][0] === 'Translation (' . $locale . ') already exists.') {
    //       $this->logger->info('Added an existing target for %id with %args.',
    //         ['%id' => $id, '%args' => var_export($args, TRUE)]);
    //     }
    //     return new \GuzzleHttp\Psr7\Response(Response::HTTP_CREATED);
    //   }
    //   $this->logger->error('Error requesting translation (%id, %args): %message.',
    //     ['%id' => $id, '%args' => var_export($args, TRUE), '%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to add translation: ' . $e->getMessage());
    // }
    // $this->logger->debug('addTranslation response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $response;
  }

  public function getTranslation($id, $locale, $useSource = FALSE) {
    // try {
    //   $this->logger->debug('Lingotek::getTranslation called with id ' . $id . ' and locale ' . $locale);
    //   $response = $this->lingotekClient->get('/api/document/' . $id . '/content', array('locale_code' => $locale, 'use_source' => $useSource));
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting translation (%id, %locale): %message.',
    //     ['%id' => $id, '%locale' => $locale, '%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to add translation: ' . $e->getMessage());
    // }
    // $this->logger->debug('getTranslation response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $response;
  }

  public function deleteTranslation($id, $locale) {
    // try {
    //   $this->logger->debug('Lingotek::deleteTranslation called with id ' . $id . ' and locale ' . $locale);
    //   $response = $this->lingotekClient->delete('/api/document/' . $id . '/translation', array('locale_code' => $locale));
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting translation (%id, %locale): %message.',
    //     ['%id' => $id, '%locale' => $locale, '%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to add translation: ' . $e->getMessage());
    // }
    // $this->logger->debug('deleteTranslation response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $response;
  }

  public function getCommunities() {
    // try {
    //   $this->logger->debug('Lingotek::getCommunities called.');
    //   $response = $this->lingotekClient->get('/api/community', ['limit' => 100]);
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting communities: %message.', ['%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to get communities: ' . $e->getMessage());
    // }
    // $this->logger->debug('deleteTranslation response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $this->formatResponse($response);
  }

  public function getProject($project_id) {
    // try {
    //   $this->logger->debug('Lingotek::getProject called with id ' . $project_id);
    //   $response = $this->lingotekClient->get('/api/project/' . $project_id);
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting project %project: %message.', ['%project' => $project_id, '%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to get project: ' . $e->getMessage());
    // }
    // $this->logger->debug('getProject response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $response->json();
  }

  public function getProjects($community_id) {
    // try {
    //   $this->logger->debug('Lingotek::getProjects called with id ' . $community_id);
    //   $response = $this->lingotekClient->get('/api/project', array('community_id' => $community_id, 'limit' => 1000));
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting projects for community %community: %message.', ['%community' => $community_id, '%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to get projects: ' . $e->getMessage());
    // }
    // $this->logger->debug('getProjects response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $this->formatResponse($response);
  }

  public function getVaults($community_id) {
    // try {
    //   $this->logger->debug('Lingotek::getVaults called with id ' . $community_id);
    //   // We ignore $community_id, as it is not needed for getting the TM vaults.
    //   $response = $this->lingotekClient->get('/api/vault', array('limit' => 100, 'is_owned' => 'TRUE'));
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting vaults for community %community: %message.', ['%community' => $community_id, '%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to get vaults: ' . $e->getMessage());
    // }
    // $this->logger->debug('getVaults response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $this->formatResponse($response);
  }

  public function getWorkflows($community_id) {
    // try {
    //   $this->logger->debug('Lingotek::getWorkflows called with id ' . $community_id);
    //   $response = $this->lingotekClient->get('/api/workflow', array('community_id' => $community_id, 'limit' => 1000));
    // }
    // catch (\Exception $e) {
    //   $this->logger->error('Error getting workflows for community %community: %message.', ['%community' => $community_id, '%message' =>  $e->getMessage()]);
    //   throw new LingotekApiException('Failed to get workflows: ' . $e->getMessage());
    // }
    // $this->logger->debug('getWorkflows response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    // return $this->formatResponse($response);
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    // try {
    //   $this->logger->debug('Lingotek::getFilters called.');
    //   $response = $this->lingotekClient->get('/api/filter', ['limit' => 1000]);
    // }
    // catch (\Exception $e) {
    //   throw new LingotekApiException('Failed to get filters: ' . $e->getMessage());
    // }
    // return $this->formatResponse($response);
  }
}
