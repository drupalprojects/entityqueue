<?php
/**
 * @file
 * Contains \Drupal\entityqueue\Plugin\EntityQueueHandler\SimpleQueueHandler.
 */

namespace Drupal\entityqueue\Plugin\EntityQueue;

use Drupal\entityqueue\EntityQueueHandlerBase;

/**
 * Class SimpleQueueHandler
 * @package Drupal\entityqueue\Plugin\EntityQueueHandler
 *
 * Implements an EntityQueue Handler.
 *
 * @EntityQueueHandler(
 *   id = "EntityQueueHandler_multiple",
 *   title = @Translation("Multiple"),
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
