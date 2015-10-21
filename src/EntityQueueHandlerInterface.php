<?php

/**
 * @file
 * Contains \Drupal\entityqueue\EntityQueueHandlerInterface.
 */

namespace Drupal\entityqueue;

use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;

/**
 * Provides an interface for an EntityQueueHandler plugin.
 *
 * @see \Drupal\entityqueue\Annotation\EntityQueueHandler
 * @see \Drupal\entityqueue\EntityQueueHandlerManager
 * @see \Drupal\entityqueue\EntityQueueHandlerBase
 * @see plugin_api
 */
interface EntityQueueHandlerInterface extends PluginFormInterface, ConfigurablePluginInterface {

  /**
   * Whether or not the handler supports multiple subqueues.
   *
   * @return bool
   */
  public function supportsMultipleSubqueues();

}
