<?php

/**
 * Specialized Entityqueue handler.
 */
class EntityReference_SelectionHandler_EntityQueue extends EntityReference_SelectionHandler_Generic {

  /**
   * Overrides EntityReference_SelectionHandler_Generic::getInstance().
   */
  public static function getInstance($field, $instance) {
    return new EntityReference_SelectionHandler_EntityQueue($field, $instance);
  }

  /**
   * Overrides EntityReference_SelectionHandler_Generic::buildEntityFieldQuery().
   */
  public function buildEntityFieldQuery($match = NULL, $match_operator = 'CONTAINS') {
    $handler = EntityReference_SelectionHandler_Generic::getInstance($this->field, $this->instance);
    $query = $handler->buildEntityFieldQuery($match, $match_operator);

    return $query;
  }

  /**
   * Overrides EntityReference_SelectionHandler_Generic::entityFieldQueryAlter().
   */
  public function entityFieldQueryAlter(SelectQueryInterface $query) {
    $handler = EntityReference_SelectionHandler_Generic::getInstance($this->field, $this->instance);
    $handler->entityFieldQueryAlter($query);
  }
}
