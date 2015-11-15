<?php

/**
 * @file
 * Contains \Drupal\entityqueue\Form\EntitySubqueueForm.
 */

namespace Drupal\entityqueue\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the entity subqueue edit forms.
 */
class EntitySubqueueForm extends ContentEntityForm {

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\entityqueue\EntitySubqueueInterface
   */
  protected $entity;

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
   * Constructs a EntitySubqueueForm.
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

    // @todo Consider creating a 'Machine name' field widget.
    $form['name'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\entityqueue\Entity\EntitySubqueue::load',
        'source' => ['title', 'widget', 0, 'value'],
      ),
      '#disabled' => !$this->entity->isNew(),
      '#weight' => -5,
      '#access' => !$this->entity->getQueue()->getHandlerPlugin()->hasAutomatedSubqueues(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $subqueue = $this->entity;
    $status = $subqueue->save();

    $edit_link = $subqueue->toLink($this->t('Edit'), 'edit-form');
    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('The entity subqueue %label has been updated.', ['%label' => $subqueue->label()]));
      $this->logger->notice('The entity subqueue %label has been updated.', ['%label' => $subqueue->label(), 'link' => $edit_link]);
    }
    else {
      drupal_set_message($this->t('The entity subqueue %label has been added.', ['%label' => $subqueue->label()]));
      $this->logger->notice('The entity subqueue %label has been added.', ['%label' => $subqueue->label(), 'link' =>  $edit_link]);
    }

    $queue = $subqueue->getQueue();
    if ($queue->getHandlerPlugin()->supportsMultipleSubqueues()) {
      $form_state->setRedirectUrl($queue->toUrl('subqueue-list'));
    }
    else {
      $form_state->setRedirectUrl($queue->toUrl('collection'));
    }
  }

}
