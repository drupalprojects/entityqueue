<?php

/**
 * @file
 * Contains the EntityQueueHandlerInterface interface.
 */

namespace Drupal\entityqueue;

interface EntityQueueHandlerInterface extends PluginFormInterface, ConfigurablePluginInterface {

  /**
   * Whether or not the handler supports multiple subqueues.
   *
   * @return boolean
   */
  public function supportsMultipleSubqueues();


}