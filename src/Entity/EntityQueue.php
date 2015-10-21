<?php

/**
 * @file
 * Contains Drupal\entityqueue\Entity\EntityQueue.
 */

namespace Drupal\entityqueue\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\entityqueue\EntityQueueInterface;
use Drupal\Core\Plugin\DefaultSingleLazyPluginCollection;

/**
 * Defines the EntityQueue entity class.
 *
 * @ConfigEntityType(
 *   id = "entityqueue",
 *   label = @Translation("Entity queue"),
 *   handlers = {
 *     "list_builder" = "Drupal\entityqueue\EntityQueueListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entityqueue\Form\EntityQueueForm",
 *       "edit" = "Drupal\entityqueue\Form\EntityQueueForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   admin_permission = "administer entityqueue",
 *   config_prefix = "entityqueue",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "edit-form" = "/admin/structure/entityqueue/{entityqueue}",
 *     "delete-form" = "/admin/structure/entityqueue/{entityqueue}/delete",
 *     "collection" = "/admin/structure/entityqueue"
 *   }
 * )
 */
class EntityQueue extends ConfigEntityBundleBase implements EntityQueueInterface {

  /**
   * The EntityQueue ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The EntityQueue label.
   *
   * @var string
   */
  protected $label;

  /**
   * The minimum number of items that this queue can hold.
   *
   * @var int
   */
  protected $min_size = 0;

  /**
   * The maximum number of items that this queue can hold.
   *
   * @var int
   */
  protected $max_size = 10;

  /**
   * The type of the target entities.
   *
   * @var string
   */
  protected $target_type = '';

  /**
   * The ID of the EntityQueueHandler.
   *
   * @var string
   */
  protected $handler;

  /**
   * The EntityQueueHandler plugin.
   *
   * @var \Drupal\Core\Plugin\DefaultSingleLazyPluginCollection
   */
  protected $handlerPluginCollection;

  /**
   * An array to store and load the EntityQueueHandler plugin configuration.
   *
   * @var array
   */
  protected $handlerConfig = [];

  /**
   * Array of bundle names of the target entities.
   */
  protected $target_bundles = array();

  public function getTargetType() {
    return $this->target_type;
  }

  public function getTargetBundles() {
    return $this->target_bundles;
  }

  /**
   * {@inheritdoc}
   */
  public function getHandler() {
    return $this->handler;
  }

  /**
   * {@inheritdoc}
   */
  public function setHandler($handler) {
    $this->handler = $handler;
    $this->handlerPluginCollection = new DefaultSingleLazyPluginCollection(
      \Drupal::service('plugin.manager.entityqueue.handler'),
      $this->handler, $this->handlerConfig
    );

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHandlerPlugin() {
    return $this->handlerPluginCollection->get($this->handler);
  }

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    $properties = parent::toArray();

    $names = ['handler'];

    foreach ($names as $name) {
      $properties[$name] = $this->get($name);
    }

    return $properties;
  }

}
