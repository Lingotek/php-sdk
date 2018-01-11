<?php

/**
 * @file
 * Contains \Drupal\lingotek\Remote\LingotekApiInterface.
 */

namespace Lingotek;

interface LingotekApiInterface {
  /**
   * Get the available locales on Lingotek.
   *
   * @return array|bool
   *   Array of locales (as in de-DE, es-ES). FALSE if there is an error.
   */
  public function getLocales(); //TJ

  public function addDocument($args); //TJ

  public function patchDocument($id, $args); //TJ
  public function deleteDocument($id); //TJ
  public function getDocumentTranslationStatuses($id); //TJ
  public function getDocumentTranslationStatus($id, $locale); //Joey
  public function getDocumentInfo($id); //Joey
  public function getDocumentStatus($id); //Joey
  public function addTranslation($id, $locale, $workflow_id = NULL); //Stephanie
  public function getTranslation($id, $locale, $useSource); //Stephanie
  public function deleteTranslation($id, $locale); //Stephanie
  public function getCommunities(); //Joey
  public function getProjects($community_id); //Stephanie
  public function getProject($token, $id); //Chris 
  public function getVaults($community_id); //Chris
  public function getWorkflows($community_id); //Chris

  /**
   * Get the available filters on Lingotek.
   *
   * @return
   *   Array of filters as in (id, label). FALSE if there is an error.
   */
  public function getFilters(); //Chris

}
