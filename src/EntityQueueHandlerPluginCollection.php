<?php

/**
 * @file
 * Contains \Drupal\entityqueue\EntityQueueHandlerPluginCollection.
 */

namespace Drupal\entityqueue;

use Drupal\Core\Plugin\DefaultSingleLazyPluginCollection;

/**
 * Provides a container for lazily loading EntityQueueHandler plugins.
 */
class EntityQueueHandlerPluginCollection extends DefaultSingleLazyPluginCollection {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\entityqueue\EntityQueueHandlerInterface
   */
  public function &get($instance_id) {
    return parent::get($instance_id);
  }

  /**
   * {@inheritdoc}
   */
  public function addInstanceId($id, $configuration = NULL) {
    // @todo Open a core bug report, the parent class should take care of this..
    $this->instanceId = $id;
    $this->instanceIDs = array_filter($this->instanceIDs);

    parent::addInstanceId($id, $configuration);
  }

}
