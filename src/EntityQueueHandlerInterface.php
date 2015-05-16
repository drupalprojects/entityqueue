<?php

/**
 * @file
 * Contains the EntityQueueHandlerInterface interface.
 */

namespace Drupal\entityqueue;

use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;

interface EntityQueueHandlerInterface extends PluginFormInterface, ConfigurablePluginInterface {

  /**
   * Whether or not the handler supports multiple subqueues.
   *
   * @return boolean
   */
  public function supportsMultipleSubqueues();


}