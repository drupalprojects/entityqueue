<?php

/**
 * @file
 * Contains \Drupal\entityqueue\Plugin\EntityQueueHandler\Simple.
 */

namespace Drupal\entityqueue\Plugin\EntityQueueHandler;

use Drupal\entityqueue\EntityQueueHandlerBase;

/**
 * Defines an entity queue handler that manages a single subqueue.
 *
 * @EntityQueueHandler(
 *   id = "simple",
 *   title = @Translation("Simple")
 * )
 */
class Simple extends EntityQueueHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function supportsMultipleSubqueues() {
    return FALSE;
  }

}
