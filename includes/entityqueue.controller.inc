<?php

/**
 * @file
 * The controller for the EntityQueue entity containing CRUD operations.
 */

/**
 * The controller class for queues contains methods for the queue CRUD
 * operations.
 */
class EntityQueueEntityController extends EntityAPIControllerExportable {

  /**
   * Create a default queue.
   *
   * @param array $values
   *   An array of values to set, keyed by property name.
   *
   * @return
   *   An EntityQueue object with all default fields initialized.
   */
  public function create(array $values = array()) {
    $values += array(
      'queue_id' => NULL,
      'name' => NULL,
      'parent_name' => NULL,
      'type' => 'node',
      'subqueue' => '',
      'label' => '',
      'subqueue_label' => '',
      'module' => '',
      'uid' => '',
      'data' => array(),
    );

    return parent::create($values);
  }
}
