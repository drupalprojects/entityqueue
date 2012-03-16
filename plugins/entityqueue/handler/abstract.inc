<?php

/**
 * Abstraction of the handling logic of a queue.
 */
interface EntityQueueHandlerInterface {

  /**
   * Factory function: creates a new instance of this handler for a queue.
   *
   * @param EntityQueue $queue
   *   An EntityQueue object.
   *
   * @return EntityQueueHandlerInterface
   */
  public static function getInstance(EntityQueue $queue);

  /**
   * Generates a settings form for this handler.
   */
  public static function settingsForm(EntityQueue $queue);

  /**
   * Returns the entity type label of a queue target type.
   */
  public function getTargetTypeLabel();

  /**
   * Returns the label of a given subqueue.
   */
  public function getSubqueueLabel(EntitySubqueue $subqueue);
}

/**
 * A NULL implementation of EntityQueueHandlerInterface.
 */
class EntityQueueHandler_Broken implements EntityQueueHandlerInterface {

  /**
   * Implements EntityQueueHandlerInterface::getInstance().
   */
  public static function getInstance(EntityQueue $queue) {
    return new EntityQueueHandler_Broken($queue);
  }

  /**
   * Constructs a NULL implementation for a queue.
   */
  protected function __construct(EntityQueue $queue) {
    $this->queue = $queue;
  }

  /**
   * Implements EntityQueueHandlerInterface::settingsForm().
   */
  public static function settingsForm(EntityQueue $queue) {
    $form['handler'] = array(
      '#markup' => t('The selected handler is broken.'),
    );
    return $form;
  }

  /**
   * Implements EntityQueueHandlerInterface::getTargetTypeLabel().
   */
  public function getTargetTypeLabel() {
    return '<em>' . t('Missing entity type (@label)', array('@label' => $this->queue->target_type)) . '</em>';
  }

  /**
   * Implements EntityQueueHandlerInterface::getSubqueueLabel().
   */
  public function getSubqueueLabel(EntitySubqueue $subqueue) {
    return '';
  }
}
