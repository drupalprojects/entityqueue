<?php

/**
 * A multiple subqueue queue implementation.
 */
class MultipleEntityQueueHandler extends EntityQueueHandlerBase {

  /**
   * Overrides EntityQueueHandlerBase::settingsForm().
   */
  public function settingsForm() {
    return array();
  }

  /**
   * Overrides EntityQueueHandlerBase::subqueueForm().
   */
  public function subqueueForm(EntitySubqueue $subqueue, &$form_state) {

    $values = isset($form_state['values']) ? $form_state['values'] : (array) $subqueue;

    $form = array();
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Subqueue label'),
      '#required' => TRUE,
      '#default_value' => isset($values['label']) ? $values['label'] : '',
    );

    $form['name'] = array(
      '#type' => 'machine_name',
      '#title' => t('Subqueue name'),
      '#required' => TRUE,
      '#default_value' => isset($values['name']) ? $values['name'] : '',
      '#machine_name' => array(
        'exists' => 'entityqueue_subqueue_load',
        'source' => array('label'),
      ),
      '#disabled' => (isset($subqueue->subqueue_id)),
    );
    return $form;
  }

  /**
   * Overrides EntityQueueHandlerBase::getSubqueueLabel().
   */
  public function getSubqueueLabel(EntitySubqueue $subqueue) {
    return $this->queue->label;
  }

  /**
   * Overrides EntityQueueHandlerBase::loadFromCode().
   */
  public function loadFromCode() {
    $this->ensureSubqueue();
  }

  /**
   * Overrides EntityQueueHandlerBase::insert().
   */
  public function insert() {
    $this->ensureSubqueue();
  }

  /**
   * Makes sure that every simple queue has a subqueue.
   */
  protected function ensureSubqueue() {
    global $user;

    $query = new EntityFieldQuery();
    $query
      ->entityCondition('entity_type', 'entityqueue_subqueue')
      ->entityCondition('bundle', $this->queue->name);
    $result = $query->execute();

    // If we don't have a subqueue already, create one now.
    if (empty($result['entityqueue_subqueue'])) {
      $subqueue = entityqueue_subqueue_create();
      $subqueue->queue = $this->queue->name;
      $subqueue->name = $this->queue->name;
      $subqueue->label = $this->getSubqueueLabel($subqueue);
      $subqueue->module = 'entityqueue';
      $subqueue->uid = $user->uid;

      entityqueue_subqueue_save($subqueue);
    }
  }
}
