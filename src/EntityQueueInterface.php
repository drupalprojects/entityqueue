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
