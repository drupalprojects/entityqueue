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
  public function settingsForm();

  /**
   * Returns the entity type label of a queue target type.
   */
  public function getTargetTypeLabel();

  /**
   * Returns the label of the queue's handler.
   */
  public function getHandlerLabel();

  /**
   * Returns the label of a given subqueue.
   */
  public function getSubqueueLabel(EntitySubqueue $subqueue);
}

/**
 * An abstract implementation of EntityQueueHandlerInterface.
 */
abstract class EntityQueueHandler_Abstract implements EntityQueueHandlerInterface {

  /**
   * @var array
   *
   * The handler plugin definition.
   */
  protected $plugin;

  /**
   * @var EntityQueue
   *
   * The EntityQueue object.
   */
  protected $queue;

  public static function getInstance(EntityQueue $queue) {
    $entity_type = $queue->target_type;

    // Check if the entity type does exist and has a base table.
    $entity_info = entity_get_info($entity_type);
    if (empty($entity_info['base table'])) {
      return EntityQueueHandler_Broken::getInstance($queue);
    }

    // Check if the queue handler exists.
    ctools_include('plugins');
    $plugin = ctools_get_plugins('entityqueue', 'handler', $queue->handler);
    if (empty($plugin)) {
      return EntityQueueHandler_Broken::getInstance($queue);
    }

    return new $plugin['class']($queue);
  }

  protected function __construct(EntityQueue $queue) {
    $this->queue = $queue;

    ctools_include('plugins');
    $plugin = ctools_get_plugins('entityqueue', 'handler', $this->queue->handler);
    $this->plugin = $plugin;
  }

  public function settingsForm() {}

  public function getTargetTypeLabel() {
    $entity_info = entity_get_info($this->queue->target_type);
    if (!empty($entity_info['base table'])) {
      return $entity_info['label'];
    }

    return '<em>' . t('Missing entity type (@type)', array('@type' => $this->queue->target_type)) . '</em>';
  }

  public function getHandlerLabel() {
    return $this->plugin['title'];
  }

  public function getSubqueueLabel(EntitySubqueue $subqueue) {}
}

/**
 * A NULL implementation of EntityQueueHandlerInterface.
 */
class EntityQueueHandler_Broken extends EntityQueueHandler_Abstract {

  /**
   * Overrides EntityQueueHandler_Abstract::getInstance().
   */
  public static function getInstance(EntityQueue $queue) {
    return new EntityQueueHandler_Broken($queue);
  }

  /**
   * Overrides EntityQueueHandler_Abstract::settingsForm().
   */
  public function settingsForm() {
    $form['handler'] = array(
      '#markup' => t('The selected handler is broken.'),
    );
    return $form;
  }

  /**
   * Overrides EntityQueueHandler_Abstract::getTargetTypeLabel().
   */
  public function getHandlerLabel() {
    return '<em>' . t('Broken/missing handler') . '</em>';
  }

  /**
   * Overrides EntityQueueHandler_Abstract::getSubqueueLabel().
   */
  public function getSubqueueLabel(EntitySubqueue $subqueue) {
    return '';
  }
}
