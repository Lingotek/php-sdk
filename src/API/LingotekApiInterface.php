<?php

/**
 * @file
 * Contains \Drupal\lingotek\Remote\LingotekApiInterface.
 */

namespace Lingotek\API;

interface LingotekApiInterface {

  /**
   * Get all supported locales
   * @return $result
   *  Full cURL response
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getLocales();

  /**
   * Add a new document to an existing project
   * @param $args
   * Required arguments to be included in $args:
   *  title (Document name)
   *  locale_code (Document language)
   *  project_id (Project to associate the document to)
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function addDocument($args);

  /**
   * Update an existing document
   * @param $args
   * Required arguments to be included in $args:
   *  'id' => 98dee54e-d5c7-4935-be0f-ac5a3617be00
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function patchDocument($args);

  /**
   * Delete an existing Document
   * @param $id
   *  Required string that represents the Document ID
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function deleteDocument($id);

  /**
   * Get a document which the active user has access to
   * @param $id
   *  Required string that represents the Document ID
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getDocumentTranslationStatuses($id);

  /**
   * Get the status of an existing document
   * @param $id
   *  Required string that represents the Document ID
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getDocumentTranslationStatus($id, $locale);

  /**
   * Get the status of all translations for an existing document
   * @param $id
   *  Required string that represents the Document ID
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getDocumentInfo($id);

  /**
   * Get the status of a specific translation for an existing document
   * @param $id
   *  Required string Id of the document to which a translation will be added
   * @param $locale
   *  Required string Locale code for the translation
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getDocumentStatus($id);

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
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function addTranslation($id, $locale, $workflow_id = NULL);

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
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function getTranslation($id, $locale, $useSource);

  /**
   * Delete an existing translation
   * @param $id
   *  Required string Document uid for that translation
   * @param $locale
   *  Required string Locale code to be deleted from document
   * @return $result
   *  Full cURL response
   *
   * For more information see https://devzone.lingotek.com/api-explorer
   */
  public function deleteTranslation($id, $locale);

  /**
  * Get all communities which the active user is a member of
  * @param $args
  *  Optional array of parameters for the API call
  * @return $result
  *   Full cURL response
  *
  * For more information see https://devzone.lingotek.com/api-explorer
  */
  public function getCommunities($args = ['limit' => 1000]);

  /**
  * Get a project which the active user has access to
  * @param $project_id
  *  Required string Project ID
  * @return $result
  *   Full cURL response
  *
  * For more information see https://devzone.lingotek.com/api-explorer
  */
  public function getProjects($community_id, $args = ['limit' => 1000]);

  /**
  * Get all projects which the active user has access to
  * @param $community_id
  *  Required string Community ID for Desired projects
  * @param $args
  *  Optional array of parameters for the API call
  * @return $result
  *   Full cURL response
  *
  * For more information see https://devzone.lingotek.com/api-explorer
  */
  public function getProject($id);

  /**
  * Get all vaults which the active user has access to
  * @param $args
  *  Optional array of parameters for the API call
  * @return $result
  *   Full cURL response
  *
  * For more information see https://devzone.lingotek.com/api-explorer
  */
  public function getVaults($args = ['limit' => 100, 'is_owned' => TRUE]);

  /**
  * Get all workflows which the active user has access to
  * @param $community_id
  *  Required string Community ID for Desired workflows
  * @param $args
  *  Optional array of parameters for the API call
  * @return $result
  *   Full cURL response
  *
  * For more information see https://devzone.lingotek.com/api-explorer
  */
  public function getWorkflows($community_id, $args = ['limit' => 1000]);

  /**
  * Get all filter configurations which the active user has access to
  * @param $args
  *  Optional array of parameters for the API call
  * @return $result
  *   Full cURL response
  *
  * For more information see https://devzone.lingotek.com/api-explorer
  */
  public function getFilters($args = ['limit' => 1000]);

}
