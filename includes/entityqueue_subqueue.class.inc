<?php

/**
 * Main class for subqueue entities.
 */
class EntitySubqueue extends Entity {

  public function __construct($values = array()) {
    parent::__construct($values, 'entityqueue_subqueue');
  }

  /**
   * Returns whether the queue is locked, thus may not be deleted or renamed.
   *
   * Queues provided in code are automatically treated as locked, as well
   * as any fixed queue.
   */
  public function isLocked() {
    return isset($this->status) && empty($this->is_new) && (($this->status & ENTITY_IN_CODE) || ($this->status & ENTITY_FIXED));
  }
}
