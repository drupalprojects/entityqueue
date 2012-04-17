<?php

/**
 * A simple queue implementation.
 */
class EntityQueueHandler_Simple extends EntityQueueHandler_Abstract {

  /**
   * Overrides EntityQueueHandler_Abstract::settingsForm().
   */
  public function settingsForm() {
    return array();
  }

  /**
   * Overrides EntityQueueHandler_Abstract::getSubqueueLabel().
   */
  public function getSubqueueLabel(EntitySubqueue $subqueue) {
    return '';
  }
}
