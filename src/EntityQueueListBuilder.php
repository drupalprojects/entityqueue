<?php

/**
 * @file
 * Contains \Drupal\entityqueue\EntityQueueListBuilder.
 */

namespace Drupal\entityqueue;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class that builds a listing of entity queues.
 */
class EntityQueueListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entities = array(
      'enabled' => array(),
      'disabled' => array(),
    );
    foreach (parent::load() as $entity) {
      if ($entity->status()) {
        $entities['enabled'][] = $entity;
      }
      else {
        $entities['disabled'][] = $entity;
      }
    }
    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Queue name');
    $header['id'] = $this->t('Machine name');
    $header['target_type'] = $this->t('Target type');
    $header['handler'] = $this->t('Handler');
    $header['items'] = $this->t('Items');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['target_type'] = $entity->getTargetType();
    $row['handler'] = $entity->getHandlerPlugin()->getPluginDefinition()['title'];
    $row['items'] = '@todo';

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $entities = $this->load();

    $build['#type'] = 'container';
    $build['#attributes']['id'] = 'entity-queue-list';
    $build['#attached']['library'][] = 'core/drupal.ajax';

    $build['enabled']['heading']['#markup'] = '<h2>' . $this->t('Enabled', array(), array('context' => 'Plural')) . '</h2>';
    $build['disabled']['heading']['#markup'] = '<h2>' . $this->t('Disabled', array(), array('context' => 'Plural')) . '</h2>';

    foreach (array('enabled', 'disabled') as $status) {
      $build[$status]['#type'] = 'container';
      $build[$status]['#attributes'] = array('class' => array('entity-queue-list-section', $status));
      $build[$status]['table'] = array(
        '#type' => 'table',
        '#attributes' => array(
          'class' => array('entity-queue-listing-table'),
        ),
        '#header' => $this->buildHeader(),
        '#rows' => array(),
        '#cache' => [
          'contexts' => $this->entityType->getListCacheContexts(),
          'tags' => $this->entityType->getListCacheTags(),
        ],
      );
      foreach ($entities[$status] as $entity) {
        $build[$status]['table']['#rows'][$entity->id()] = $this->buildRow($entity);
      }
    }
    // @todo Use a placeholder for the entity label if this is abstracted to
    // other entity types.
    $build['enabled']['table']['#empty'] = $this->t('There are no enabled queues.');
    $build['disabled']['table']['#empty'] = $this->t('There are no disabled queues.');

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    $operations['edit']['title'] = $this->t('Configure');

    // Add AJAX functionality to enable/disable operations.
    foreach (array('enable', 'disable') as $op) {
      if (isset($operations[$op])) {
        $operations[$op]['url'] = $entity->urlInfo($op);
        // Enable and disable operations should use AJAX.
        $operations[$op]['attributes']['class'][] = 'use-ajax';
      }
    }

    // Allow queue handlers to add their own operations.
    $operations += $entity->getHandlerPlugin()->getQueueListBuilderOperations();

    return $operations;
  }

}
