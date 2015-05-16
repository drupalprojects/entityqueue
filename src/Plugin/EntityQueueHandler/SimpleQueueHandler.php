<?php
/**
 * @file
 * Contains \Drupal\entityqueue\Plugin\EntityQueueHandler\SimpleQueueHandler.
 */

namespace Drupal\entityqueue\Plugin\EntityQueueHandler;

use Drupal\entityqueue\EntityQueueHandlerBase;

/**
 * Class SimpleQueueHandler
 * @package Drupal\entityqueue\Plugin\EntityQueueHandler
 *
 * Implements an EntityQueue Handler.
 *
 * @EntityQueueHandler(
 *   id = "EntityQueueHandler_simple",
 *   title = @Translation("Simple"),
 * )
 */
class SimpleQueueHandler extends EntityQueueHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function supportsMultipleSubqueues() {
    return false;
  }

}
