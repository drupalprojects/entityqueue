<?php

/**
 * @file
 * Contains the CTools Export UI integration code.
 */

/**
 * Defines the CTools Export UI class handler for Entityqueue.
 */
class entityqueue_export_ui extends ctools_export_ui {

  protected $entityType;

  /**
   * Initializes default variables.
   */
  public function __construct() {
    $this->entityType = 'entityqueue_subqueue';
  }

  /**
   * Page callback; Displays a listing of subqueues for a queue.
   */
  public function subqueues_page($js, $input, EntityQueue $queue) {
    $plugin = $this->plugin;
    drupal_set_title($this->get_page_title('subqueues', $queue));

    $header = array(
      array(
        'data' => t('Id'),
        'type' => 'property',
        'specifier' => 'subqueue_id',
        'class' => array('entityqueue-ui-subqueue-id')
      ),
      array(
        'data' => t('Subqueue'),
        'type' => 'property',
        'specifier' => 'label',
        'class' => array('entityqueue-ui-subqueue-label')
      ),
      // @todo Do some magic with EntiyFieldQuery to allow ordering by the
      // number of items in a subqueue.
      array(
        'data' => t('Operations'),
        'class' => array('entityqueue-ui-subqueue-operations')
      ),
    );

    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', $this->entityType);
    $query->entityCondition('bundle', $queue->name);
    $query->pager(50);
    $query->tableSort($header);
    $results = $query->execute();

    $ids = isset($results[$this->entityType]) ? array_keys($results[$this->entityType]) : array();
    $subqueues = $ids ? entity_load($this->entityType, $ids) : array();

    $rows = array();
    foreach ($subqueues as $subqueue) {
      $ops = '';
      if (entity_access('update', 'entityqueue_subqueue', $subqueue)) {
        $edit_op = str_replace('%entityqueue_subqueue', $subqueue->subqueue_id, ctools_export_ui_plugin_menu_path($plugin, 'edit subqueue', $queue->name));
        $ops = l(t('edit items'), $edit_op);
      }
      $rows[] = array(
        'data' => array(
          array(
            'data' => $subqueue->subqueue_id,
            'class' => array('entityqueue-ui-subqueue-id')
          ),
          array(
            'data' => filter_xss_admin($subqueue->label),
            'class' => array('entityqueue-ui-subqueue-label')
          ),
          array(
            'data' => $ops,
            'class' => array('entityqueue-ui-subqueue-operations')
          ),
        ),
      );
    }

    $render = array(
      'table' => array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => t('There are no subqueues to display.'),
      ),
      'pager' => array(
        '#theme' => 'pager'
      ),
    );
    return $render;
  }

  /**
   * Page callback; Displays the subqueue add form.
   */
  public function subqueue_add_page($js, $input, EntityQueue $queue) {
    global $user;
    drupal_set_title(t('Add subqueue to %queue', array('%queue' => $queue->label)), PASS_THROUGH);
    ctools_include('plugins');
    $plugins = ctools_get_plugins('entityqueue', 'handler');
    $subqueue = entityqueue_subqueue_create(array(
      'queue' => $queue->name,
      'module' => $plugins[$queue->handler]['module'],
      'uid' => $user->uid,
    ));

    return drupal_get_form('entityqueue_subqueue_edit_form', $queue, $subqueue);
  }

  /**
   * Page callback; Displays the subqueue edit form.
   */
  public function subqueue_edit_page($js, $input, EntityQueue $queue, EntitySubqueue $subqueue) {
    drupal_set_title(t('Edit %subqueue', array('%subqueue' => $subqueue->label)), PASS_THROUGH);
    return drupal_get_form('entityqueue_subqueue_edit_form', $queue, $subqueue);
  }

  /**
   * Overrides ctools_export_ui::list_form_submit().
   */
  function list_form_submit(&$form, &$form_state) {
    // Add the subqueue_id and the number of items for 'single' queues, and the
    // number of subqueues for the rest.
    // @todo This is quite inefficient to do here but ctools_export_load_object()
    // doesn't help us.
    if (!empty($this->items)) {
      $query = new EntityFieldQuery();
      $query
        ->entityCondition('entity_type', $this->entityType)
        ->entityCondition('bundle', array_keys($this->items), 'IN');
      $result = $query->execute();

      if (!empty($result[$this->entityType])) {
        $handlers = ctools_get_plugins('entityqueue', 'handler');
        $subqueues_to_load = array();

        foreach ($result[$this->entityType] as $name => $subqueue) {
          // Add the number of subqueues first.
          $this->items[$subqueue->queue]->subitems += 1;

          // If this subqueue's bundle is a 'single' queue, load it so we can get
          // its number of items.
          if ($handlers[$this->items[$subqueue->queue]->handler]['queue type'] == 'single') {
            $subqueues_to_load[] = $subqueue->subqueue_id;
          }
        }

        if (!empty($subqueues_to_load)) {
          $subqueues = entity_load($this->entityType, $subqueues_to_load);
          foreach ($subqueues as $subqueue) {
            $field_items = field_get_items($this->entityType, $subqueue, _entityqueue_get_target_field_name($this->items[$subqueue->queue]->target_type));
            $this->items[$subqueue->queue]->subitems = $field_items ? count($field_items) : 0;
            $this->items[$subqueue->queue]->subqueue_id = $subqueue->subqueue_id;
          }
        }
      }
    }

    parent::list_form_submit($form, $form_state);
  }

  /**
   * Overrides ctools_export_ui::list_build_row().
   */
  public function list_build_row($queue, &$form_state, $operations) {
    global $user;
    // Rename the 'Edit' operation, as that will be re-assigned to edit subqueue
    // items.
    $operations['edit']['title'] = t('Configure');

    // Remove the 'subqueues' operation from queue that have a single
    // subqueue, and remove the 'edit subqueue' operation for the rest.
    $handlers = ctools_get_plugins('entityqueue', 'handler');
    if ($handlers[$queue->handler]['queue type'] == 'single') {
      unset($operations['subqueues']);
      $operations['edit subqueue']['href'] = str_replace('%entityqueue_subqueue', $queue->subqueue_id, $operations['edit subqueue']['href']);
    }
    else {
      unset($operations['edit subqueue']);
    }

    // Set up sorting.
    switch ($form_state['values']['order']) {
      case 'disabled':
        $this->sorts[$queue->name] = empty($queue->disabled) . $queue->name;
        break;
      case 'title':
        $this->sorts[$queue->name] = $queue->label;
        break;
      case 'name':
        $this->sorts[$queue->name] = $queue->name;
        break;
      case 'storage':
        $this->sorts[$queue->name] = $queue->type . $queue->name;
        break;
    }

    $item = array(
      '#theme' => 'entityqueue_overview_item',
      '#label' => $queue->label,
      '#name' => $queue->name,
      '#status' => $queue->export_type,
    );

    $target_type = entityqueue_get_handler($queue)->getTargetTypeLabel();
    $handler_label = entityqueue_get_handler($queue)->getHandlerLabel();

    if ($handlers[$queue->handler]['queue type'] == 'single') {
      $subitems = format_plural($queue->subitems, '1 item', '@count items');
    }
    else {
      $subitems = format_plural($queue->subitems, '1 subqueue', '@count subqueues');
    }

    // Remove operations the user doesn't have access to.
    if (!user_access('administer entityqueue', $user)) {
      unset($operations['edit'], $operations['export'], $operations['clone']);
      unset($operations['disable'], $operations['delete']);
    }
    if (!entityqueue_queue_access('update', $queue)) {
      unset($operations['edit subqueue'], $operations['edit']);
    }
    $ops = theme('links__ctools_dropbutton', array('links' => $operations, 'attributes' => array('class' => array('links', 'inline'))));

    $this->rows[$queue->name]['data'][] = array('data' => $ops, 'class' => array('ctools-export-ui-operations'));
    $this->rows[$queue->name] = array(
      'data' => array(
        array('data' => $item, 'class' => array('entityqueue-ui-queue')),
        array('data' => filter_xss_admin($target_type), 'class' => array('entityqueue-ui-target-type')),
        array('data' => filter_xss_admin($handler_label), 'class' => array('entityqueue-ui-handler')),
        array('data' => $subitems, 'class' => array('entityqueue-ui-items')),
        array('data' => $ops, 'class' => array('entityqueue-ui-operations', 'ctools-export-ui-operations')),
      ),
      'title' => t('Machine name: @name', array('@name' => $queue->name)),
      'class' => array(!empty($queue->disabled) ? 'ctools-export-ui-disabled' : 'ctools-export-ui-enabled'),
    );
  }

  /**
   * Overrides ctools_export_ui::list_table_header().
   */
  public function list_table_header() {
    $header = array(
      array('data' => t('Queue'), 'class' => array('entityqueue-ui-queue')),
      array('data' => t('Target type'), 'class' => array('entityqueue-ui-target-type')),
      array('data' => t('Handler'), 'class' => array('entityqueue-ui-handler')),
      array('data' => t('Items'), 'class' => array('entityqueue-ui-items')),
      array('data' => t('Operations'), 'class' => array('entityqueue-ui-operations', 'ctools-export-ui-operations')),
    );

    return $header;
  }
}

/**
 * Add all appropriate includes to forms so that caching the form
 * still loads the files that we need.
 */
function _entityqueue_export_ui_add_form_files($form, &$form_state) {
  ctools_form_include($form_state, 'export');
  ctools_form_include($form_state, 'export-ui');

  // Also make sure the plugin .inc and .class.php files are loaded.
  form_load_include($form_state, 'inc', 'entityqueue', '/plugins/ctools/export_ui/entityqueue_export_ui');
  form_load_include($form_state, 'php', 'entityqueue', '/plugins/ctools/export_ui/entityqueue_export_ui.class');
}

/**
 * Form callback; Displays the subqueue edit form.
 */
function entityqueue_subqueue_edit_form($form, &$form_state, EntityQueue $queue, EntitySubqueue $subqueue) {
  // When called using #ajax via ajax_form_callback(), 'export' may
  // not be included so include it here.
  _entityqueue_export_ui_add_form_files($form, $form_state);

  $handler = entityqueue_get_handler($queue);
  $form = $handler->subqueueForm($subqueue, $form_state);
  $form_state['entityqueue_queue'] = $queue;
  $form_state['entityqueue_subqueue'] = $subqueue;

  field_attach_form('entityqueue_subqueue', $subqueue, $form, $form_state);

  // Entity type (bundle) is needed by entity_form_submit_build_entity().
  $form['queue'] = array(
    '#type' => 'value',
    '#default_value' => $queue->name,
  );

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
    '#weight' => 40,
  );

  $form['#validate'][] = 'entityqueue_subqueue_edit_form_validate';
  $form['#submit'][] = 'entityqueue_subqueue_edit_form_submit';

  return $form;
}

/**
 * Validation callback for the subqueue edit form.
 */
function entityqueue_subqueue_edit_form_validate($form, &$form_state) {
  entity_form_field_validate('entityqueue_subqueue', $form, $form_state);
}

/**
 * Submit callback for the subqueue edit form.
 */
function entityqueue_subqueue_edit_form_submit($form, &$form_state) {
  $queue = $form_state['entityqueue_queue'];
  $subqueue = $form_state['entityqueue_subqueue'];

  entity_form_submit_build_entity('entityqueue_subqueue', $subqueue, $form, $form_state);
  $subqueue->save();

  $entityqueue_export_ui_plugin = ctools_get_plugins('ctools', 'export_ui', 'entityqueue_export_ui');
  $plugin_base_path = ctools_export_ui_plugin_base_path($entityqueue_export_ui_plugin);

  $handlers = ctools_get_plugins('entityqueue', 'handler');
  if ($handlers[$queue->handler]['queue type'] == 'single') {
    $form_state['redirect'] = $plugin_base_path;
  }
  else {
    $form_state['redirect'] = $plugin_base_path . '/list/' . $queue->name . '/subqueues';
  }
}
