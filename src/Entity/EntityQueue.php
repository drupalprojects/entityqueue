<?php

/**
 * @file
 * Contains Drupal\entityqueue\Entity\EntityQueue.
 */

namespace Drupal\entityqueue\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\entityqueue\EntityQueueInterface;
use Drupal\Core\Plugin\DefaultSingleLazyPluginCollection;

/**
 * Defines the EntityQueue entity class.
 *
 * @ConfigEntityType(
 *   id = "entityqueue",
 *   label = @Translation("EntityQueue"),
 *   handlers = {
 *     "list_builder" = "Drupal\entityqueue\Controller\EntityQueueListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entityqueue\Form\EntityQueueForm",
 *       "edit" = "Drupal\entityqueue\Form\EntityQueueForm",
 *       "delete" = "Drupal\entityqueue\Form\EntityQueueDeleteForm"
 *     }
 *   },
 *   config_prefix = "entityqueue",
 *   admin_permission = "administer entityqueue",
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
class EntityQueue extends ConfigEntityBase implements EntityQueueInterface {

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
   * @var int $min_size.
   */
  protected $min_size = 0;

  /**
   * @var int $max_size.
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

  public function __construct(array $values, $entity_type) {
    parent::__construct($values, $entity_type);

    if ($this->handler) {
      $this->handlerPluginCollection = new DefaultSingleLazyPluginCollection(
        \Drupal::service('plugin.manager.entityqueue.handler'),
        $this->handler, $this->handlerConfig
      );
    }
  }

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
