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
  public function getLocales() {
    $result = $this->client->get('locale/');
    return $result;
  }

  public function addDocument($args) {
    $result = $this->client->post('document', $args);
    return $result;
  }

  /*
   * Updates a Document
   * Required arguments to be included in $args:
   *  'id' => 98dee54e-d5c7-4935-be0f-ac5a3617be00
   */
  public function patchDocument($args) {
    $result = $this->client->patch('document/{id}', $args);
    return $result;
  }

  /*
   * Deletes a Document
   */
  public function deleteDocument($id) {
    $result = $this->client->delete('document/' . $id);
    return $result;
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
    try {
    //   $this->logger->debug('Lingotek::addTranslation called with id ' . $id . ' and locale ' . $locale);
      $args = ['locale_code' => $locale];
      if ($workflow_id) {
        $args['workflow_id'] = $workflow_id;
      }
      $response = $this->client->post('/api/document/' . $id . '/translation', $args);
    }
    catch (\Exception $e) {
      // If the problem is that the translation already exist, don't fail.
      if ($e->getCode() === Response::HTTP_BAD_REQUEST) {
        $responseBody = $e->getResponse()->getBody();
        if ($responseBody['messages'][0] === 'Translation (' . $locale . ') already exists.') {
          //$this->logger->info('Added an existing target for %id with %args.', ['%id' => $id, '%args' => var_export($args, TRUE)]);
        }
        return new \GuzzleHttp\Psr7\Response(Response::HTTP_CREATED);
      }
      //$this->logger->error('Error requesting translation (%id, %args): %message.', ['%id' => $id, '%args' => var_export($args, TRUE), '%message' =>  $e->getMessage()]);
      throw new RestClientException('Failed to add translation: ' . $e->getMessage());
    }
    //$this->logger->debug('addTranslation response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    return $response;
  }

  public function getTranslation($id, $locale, $useSource = FALSE) {
    try {
      //$this->logger->debug('Lingotek::getTranslation called with id ' . $id . ' and locale ' . $locale);
      $response = $this->client->get('/api/document/' . $id . '/content', array('locale_code' => $locale, 'use_source' => $useSource));
    }
    catch (\Exception $e) {
      //$this->logger->error('Error getting translation (%id, %locale): %message.', ['%id' => $id, '%locale' => $locale, '%message' =>  $e->getMessage()]);
      throw new RestClientException('Failed to add translation: ' . $e->getMessage());
    }
    //$this->logger->debug('getTranslation response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    return $response;
  }

  public function deleteTranslation($id, $locale) {
    try {
      //this->logger->debug('Lingotek::deleteTranslation called with id ' . $id . ' and locale ' . $locale);
      $response = $this->client->delete('/api/document/' . $id . '/translation', array('locale_code' => $locale));
    }
    catch (\Exception $e) {
      //$this->logger->error('Error getting translation (%id, %locale): %message.', ['%id' => $id, '%locale' => $locale, '%message' =>  $e->getMessage()]);
      throw new RestClientException('Failed to add translation: ' . $e->getMessage());
    }
    //$this->logger->debug('deleteTranslation response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    return $response;
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
    try {
      // LOGGER HERE
      $response = $this->client->get('/api/project/'.$project_id);
    }
    catch (\Exception $e) {
      // LOGGER HERE
      throw new RestClientException('Failed to get project: '.$e->getMessage());
    }
    return $response;
  }

  public function getProjects($community_id) {
    try {
      //$this->logger->debug('Lingotek::getProjects called with id ' . $community_id);
      $response = $this->client->get('/api/project', array('community_id' => $community_id, 'limit' => 1000));
    }
    catch (\Exception $e) {
      //$this->logger->error('Error getting projects for community %community: %message.', ['%community' => $community_id, '%message' =>  $e->getMessage()]);
      throw new RestClientException('Failed to get projects: ' . $e->getMessage());
    }
    //$this->logger->debug('getProjects response received, code %code and body %body', ['%code' => $response->getStatusCode(), '%body' => (string) $response->getBody(TRUE)]);
    return $response;
  }

  public function getVaults($community_id) {
      try {
        // LOGGER HERE
        $response = $this->client->get('/api/vault', ['limit' => 100, 'is_owned' => TRUE]);
      }
      catch (\Exception $e) {
        // LOGGER HERE
        throw new RestClientException('Failed to get vaults: '.$e->getMessage());
      }
      return $response;
  }

  public function getWorkflows($community_id) {
      try {
        // LOGGER HERE
        $response = $this->client->get('/api/workflow', ['community_id'=>$community_id, 'limit'=>1000]);
      }
      catch (\Exception $e) {
        // LOGGER HERE
        throw new RestClientException('Failed to get workflows: '.$e->getMessage());
      }
      return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    try {
      // LOGGER HERE
      $response = $this->client->get('/api/filter', ['limit' => 1000]);
    }
    catch (\Exception $e) {
      // LOGGER HERE
      throw new RestClientException('Failed to get filters: '.$e->getMessage());
    }
    return $response;
  }
}
