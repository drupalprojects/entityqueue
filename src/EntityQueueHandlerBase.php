<?php

/**
 * @file
 * Contains the EntityQueueHandlerBase class.
 */

namespace Drupal\entityqueue;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EntityQueueHandlerBase
 * @package Drupal\entityqueue\Plugin\EntityQueue
 */
abstract class EntityQueueHandlerBase extends PluginBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configuration += $this->defaultConfiguration();
  }

}
