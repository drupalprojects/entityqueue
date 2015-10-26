<?php

/**
 * @file
 * Contains \Drupal\entityqueue\Form\EntityQueueForm.
 */

namespace Drupal\entityqueue\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for entity queue edit forms.
 */
class EntityQueueForm extends BundleEntityFormBase {

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory')->get('entityqueue')
    );
  }

  /**
   * Constructs a EntityQueueForm.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

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

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $queue = $this->entity;
    $status = $queue->save();

    $edit_link = $queue->link($this->t('Edit'), 'edit-form');
    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('The entity queue %label has been updated.', array('%label' => $queue->label())));
      $this->logger->notice('The entity queue %label has been updated.', array('%label' => $queue->label(), 'link' => $edit_link));
    }
    else {
      drupal_set_message($this->t('The entity queue %label has been added.', array('%label' => $queue->label())));
      $this->logger->notice('The entity queue %label has been added.', array('%label' => $queue->label(), 'link' =>  $edit_link));
    }

    $form_state->setRedirectUrl($queue->urlInfo('collection'));
  }

}
