<?php

/**
 * @file
 * Contains Drupal\entityqueue\EntityQueueInterface.
 */

namespace Drupal\entityqueue;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a EntityQueue entity.
 */
interface EntityQueueInterface extends ConfigEntityInterface {

  /**
   * Get the EntityQueueHandler plugin id.
   *
   * @return string
   */
  public function getHandler();

  /**
   * Set the EntityQueueHandler.
   *
   * @param string $handler
   *   The handler name.
   * @return void
   */
  public function setHandler($handler);

  /**
   * Get the EntityQueueHandler plugin object.
   *
   * @return EntityQueueHandlerInterface
   */
  public function getHandlerPlugin();

}
