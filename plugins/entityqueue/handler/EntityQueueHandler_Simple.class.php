<?php

/**
 * A simple queue implementation.
 */
class EntityQueueHandler_Simple implements EntityQueueHandlerInterface {

  /**
   * Implements EntityQueueHandlerInterface::getInstance().
   */
  public static function getInstance(EntityQueue $queue) {
    $entity_type = $queue->target_type;

    // Check if the entity type does exist and has a base table.
    $entity_info = entity_get_info($entity_type);
    if (empty($entity_info['base table'])) {
      return EntityQueueHandler_Broken::getInstance($queue);
    }

    return new EntityQueueHandler_Simple($queue);
  }

  /**
   * Constructs a simple implementation for a queue.
   */
  protected function __construct(EntityQueue $queue) {
    $this->queue = $queue;
  }

  /**
   * Implements EntityQueueHandlerInterface::settingsForm().
   */
  public static function settingsForm(EntityQueue $queue) {
    return array();
  }

  /**
   * Implements EntityQueueHandlerInterface::getTargetTypeLabel().
   */
  public function getTargetTypeLabel() {
    $entity_info = entity_get_info($this->queue->target_type);
    return $entity_info['label'];
  }

  /**
   * Implements EntityQueueHandlerInterface::getSubqueueLabel().
   */
  public function getSubqueueLabel(EntitySubqueue $subqueue) {
    return '';
  }
}
