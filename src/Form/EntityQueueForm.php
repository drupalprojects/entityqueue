<?php

/**
 * @file
 * Contains \Drupal\entityqueue\Form\EntityQueueForm.
 */

namespace Drupal\entityqueue\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for entity queue edit forms.
 */
class EntityQueueForm extends BundleEntityFormBase {

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\entityqueue\EntityQueueInterface
   */
  protected $entity;

  /**
   * The entity queue handler plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $entityQueueHandlerManager;

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
      $container->get('plugin.manager.entityqueue.handler'),
      $container->get('logger.factory')->get('entityqueue')
    );
  }

  /**
   * Constructs a EntityQueueForm.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface
   *   The entity queue handler plugin manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(PluginManagerInterface $entity_queue_handler_manager, LoggerInterface $logger) {
    $this->entityQueueHandlerManager = $entity_queue_handler_manager;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $queue = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $queue->label(),
      '#description' => $this->t("Label for the EntityQueue."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $queue->id(),
      '#machine_name' => [
        'exists' => '\Drupal\entityqueue\Entity\EntityQueue::load',
      ],
      '#disabled' => !$queue->isNew(),
    ];

    $handlers = $this->entityQueueHandlerManager->getAllEntityQueueHandlers();
    $form['handler'] = [
      '#type' => 'radios',
      '#title' => $this->t('Handler'),
      '#options' => $handlers,
      '#default_value' => $queue->getHandler(),
      '#required' => TRUE,
      '#disabled' => !$queue->isNew(),
    ];

    // @todo It should be up to the queue handler to determine what entity types
    // are queue-able.
    $form['target_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type of items to queue'),
      '#options' => \Drupal::entityManager()->getEntityTypeLabels(TRUE),
      '#default_value' => $queue->getTargetEntityTypeId(),
      '#required' => TRUE,
      '#disabled' => !$queue->isNew(),
      '#size' => 1,
    ];

    $form['settings'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['properties'] = [
      '#type' => 'details',
      '#title' => $this->t('Queue properties'),
      '#open' => TRUE,
      '#group' => 'settings',
    ];
    $form['properties']['size'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['form--inline', 'clearfix']],
    ];
    $form['properties']['size']['min_size'] = [
      '#type' => 'number',
      '#size' => 2,
      '#default_value' => $queue->getMinimumSize(),
      '#field_prefix' => $this->t('Restrict this queue to a minimum of'),
    ];
    $form['properties']['size']['max_size'] = [
      '#type' => 'number',
      '#default_value' => $queue->getMaximumSize(),
      '#field_prefix' => $this->t('and a maximum of'),
      '#field_suffix' => $this->t('items.'),
    ];
    $form['properties']['act_as_queue'] = [
      '#type' => 'checkbox',
      '#title' => t('Act as queue'),
      '#default_value' => $queue->getActAsQueue(),
      '#description' => $this->t('When enabled, adding more than the maximum number of items will remove extra items from the top of the queue.'),
      '#states' => [
        'invisible' => [
          ':input[name="max_size"]' => ['value' => 0],
        ],
      ],
    ];

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

    $edit_link = $queue->toLink($this->t('Edit'), 'edit-form');
    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('The entity queue %label has been updated.', ['%label' => $queue->label()]));
      $this->logger->notice('The entity queue %label has been updated.', ['%label' => $queue->label(), 'link' => $edit_link]);
    }
    else {
      drupal_set_message($this->t('The entity queue %label has been added.', ['%label' => $queue->label()]));
      $this->logger->notice('The entity queue %label has been added.', ['%label' => $queue->label(), 'link' =>  $edit_link]);
    }

    $form_state->setRedirectUrl($queue->toUrl('collection'));
  }

}
