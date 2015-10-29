<?php

/**
 * @file
 * Contains \Drupal\entityqueue\Entity\EntitySubqueue.
 */

namespace Drupal\entityqueue\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\entityqueue\EntitySubqueueInterface;
use Drupal\user\UserInterface;

/**
 * Defines the EntitySubqueue entity class.
 *
 * @ContentEntityType(
 *   id = "entity_subqueue",
 *   label = @Translation("Entity subqueue"),
 *   bundle_label = @Translation("Entity queue"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\entityqueue\Form\EntitySubqueueForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "edit" = "Drupal\entityqueue\Form\EntitySubqueueForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\entityqueue\EntitySubqueueListBuilder"
 *   },
 *   base_table = "entity_subqueue",
 *   data_table = "entity_subqueue_field_data",
 *   entity_keys = {
 *     "id" = "name",
 *     "bundle" = "queue",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "uid" = "uid"
 *   },
 *   bundle_entity_type = "entity_queue",
 *   field_ui_base_route = "entity.entity_queue.edit_form",
 *   permission_granularity = "bundle",
 *   links = {
 *     "edit-form" = "/admin/structure/entityqueue/{entity_queue}/{entity_subqueue}",
 *     "delete-form" = "/admin/structure/entityqueue/{entity_queue}/{entity_subqueue}/delete"
 *   }
 * )
 */
class EntitySubqueue extends ContentEntityBase implements EntitySubqueueInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getQueueName() {
    return $this->bundle();
  }

  /**
   * {@inheritdoc}
   */
  public function access($operation = 'view', AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($operation == 'create') {
      return parent::access($operation, $account, $return_as_object);
    }

    return \Drupal::entityManager()
      ->getAccessControlHandler($this->entityTypeId)
      ->access($this, $operation, $account, $return_as_object);
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }


  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the subqueue.'))
      ->setReadOnly(TRUE)
      // In order to work around the InnoDB 191 character limit on utf8mb4
      // primary keys, we set the character set for the field to ASCII.
      ->setSetting('is_ascii', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The subqueue UUID.'))
      ->setReadOnly(TRUE);

    $fields['queue'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Queue'))
      ->setDescription(t('The queue (bundle) of this subqueue.'))
      ->setSetting('target_type', 'entity_queue')
      ->setReadOnly(TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 191)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language'))
      ->setDescription(t('The subqueue language code.'))
      ->setDisplayOptions('view', array(
        'type' => 'hidden',
      ))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 2,
      ));

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The username of the subqueue author.'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\entityqueue\Entity\EntitySubqueue::getCurrentUserId')
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the subqueue was created.'))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the subqueue was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->getEntityKey('uid');
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return array(\Drupal::currentUser()->id());
  }

  /**
   * {@inheritdoc}
   */
  public function urlInfo($rel = 'canonical', array $options = []) {
    $url = parent::urlInfo($rel, $options);

    // The 'entity_queue' parameter is needed by the subqueue routes, so we need
    // to add it manually.
    $url->setRouteParameter('entity_queue', $this->bundle());

    return $url;
  }

}
