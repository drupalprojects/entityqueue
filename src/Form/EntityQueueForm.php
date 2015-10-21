<?php

/**
 * @file
 * Contains \Drupal\entityqueue\Form\EntityQueueForm.
 */

namespace Drupal\entityqueue\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base form for entityqueue edit forms.
 */
class EntityQueueForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entityqueue = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entityqueue->label(),
      '#description' => $this->t("Label for the EntityQueue."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $entityqueue->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\entityqueue\Entity\EntityQueue::load',
      ),
      '#disabled' => !$entityqueue->isNew(),
    );

    $handlers = \Drupal::service('plugin.manager.entityqueue.handler')->getAllEntityQueueHandlers();
    $form['handler'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Handler'),
      '#options' => $handlers,
      '#default_value' => $entityqueue->getHandler(),
      '#required' => TRUE,
      '#disabled' => !$entityqueue->isNew(),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, FormStateInterface $form_state) {
    $entity = parent::buildEntity($form, $form_state);
    $entity->setHandler($entity->get('handler'));
    return $entity;
  }

}
