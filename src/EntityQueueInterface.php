<?php

/**
 * @file
 * Contains \Drupal\entityqueue\EntityQueueInterface.
 */

namespace Drupal\entityqueue;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a EntityQueue entity.
 */
interface EntityQueueInterface extends ConfigEntityInterface {

  /**
   * Gets the EntityQueueHandler plugin id.
   *
   * @return string
   */
  public function getHandler();

  /**
   * Sets the EntityQueueHandler.
   *
   * @param string $handler
   *   The handler name.
   *
   * @return $this
   */
  public function setHandler($handler);

  /**
   * Gets the EntityQueueHandler plugin object.
   *
   * @return EntityQueueHandlerInterface
   */
  public function getHandlerPlugin();

  /**
   * Gets the ID of the target entity type.
   *
   * @return string
   *   The target entity type ID.
   */
  public function getTargetEntityTypeId();

  /**
   * Gets the minimum number of items that this queue can hold.
   *
   * @return int
   */
  public function getMinimumSize();

  /**
   * Gets the maximum number of items that this queue can hold.
   *
   * @return int
   */
  public function getMaximumSize();

  /**
   * Returns the behavior of exceeding the maximum number of queue items.
   *
   * If TRUE, when a maximum size is set and it is exceeded, the queue will be
   * truncated to the maximum size by removing items from the front of the
   * queue.
   *
   * @return bool
   */
  public function getActAsQueue();

  /**
   * Loads one or more queues based on their target entity type.
   *
   * @param string $target_entity_type_id
   *   The target entity type ID.
   *
   * @return static[]
   *   An array of entity queue objects, indexed by their IDs.
   */
  public static function loadMultipleByTargetType($target_entity_type_id);

}
