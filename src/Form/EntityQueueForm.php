<?php

/**
 * @file
 * Contains Drupal\entityqueue\Form\EntityQueueForm.
 */

namespace Drupal\entityqueue\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EntityQueueForm.
 *
 * @package Drupal\entityqueue\Form
 */
class EntityQueueForm extends EntityForm {
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

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entityqueue = $this->entity;
    $status = $entityqueue->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label EntityQueue.', array(
        '%label' => $entityqueue->label(),
      )));
    }
    else {
      drupal_set_message($this->t('The %label EntityQueue was not saved.', array(
        '%label' => $entityqueue->label(),
      )));
    }
    $form_state->setRedirectUrl($entityqueue->urlInfo('collection'));
  }

}
