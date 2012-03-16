<?php

/**
 * @file
 * Plugin definition for CTools Export UI integration.
 */

$plugin = array(
  'schema' => 'entityqueue_queue',
  'access' => 'administer entityqueue',

  'menu' => array(
    'menu item' => 'entityqueue',
    'menu title' => 'Entityqueues',
    'menu description' => 'Manage entity queues.',
  ),

  'title singular' => t('queue'),
  'title singular proper' => t('Queue'),
  'title plural' => t('queues'),
  'title plural proper' => t('Queues'),

  'form' => array(
    'settings' => 'entityqueue_export_ui_form',
//    'validate' => 'example_ctools_export_ui_form_validate',
//    'submit' => 'example_ctools_export_ui_form_submit',
  ),

  'handler' => array(
     'class' => 'entityqueue_export_ui',
     'parent' => 'ctools_export_ui',
   ),
);

/**
 * Form to edit the settings of a queue.
 */
function entityqueue_export_ui_form(&$form, &$form_state) {
  ctools_include('plugins');
  $queue = &$form_state['item'];

  // We definitely need a queue label :)
  $form['info']['label']['#required'] = TRUE;

  $handlers = array();
  foreach (ctools_get_plugins('entityqueue', 'handler') as $name => $plugin) {
    $handlers[$name] = $plugin['title'];
  }
  $form['handler'] = array(
    '#title' => t('Handler'),
    '#type' => 'select',
    '#options' => $handlers,
    '#default_value' => $queue->handler,
    '#required' => TRUE,
    '#disabled' => !empty($queue->name) ? TRUE : FALSE,
  );

  // Get valid target entity types.
  $entity_info = entity_get_info();
  unset($entity_info['entitysubqueue']);

  $entity_types = array();
  foreach ($entity_info as $entity_type => $info) {
    $entity_types[$entity_type] = $info['label'];
  }
  $form['target_type'] = array(
    '#title' => t('Type'),
    '#type' => 'select',
    '#options' => $entity_types,
    '#default_value' => $queue->target_type,
    '#required' => TRUE,
    '#disabled' => !empty($queue->name) ? TRUE : FALSE,
    '#ajax' => array(
      'callback' => 'entityqueue_export_ui_target_type_callback',
      'wrapper' => 'entityqueue-target-bundles',
    ),
  );

  $form['settings']['#tree'] = TRUE;

  $target_type = !empty($form_state['values']['target_type']) ? $form_state['values']['target_type'] : $queue->target_type;
  $bundles = array();
  foreach ($entity_info[$target_type]['bundles'] as $bundle_name => $bundle_info) {
    $bundles[$bundle_name] = $bundle_info['label'];
  }
  $form['settings']['target_bundles'] = array(
    '#type' => 'checkboxes',
    '#title' => $target_type == 'node' ? t('Content types') : t('Bundles'),
    '#prefix' => '<div id="entityqueue-target-bundles">',
    '#suffix' => '</div>',
    '#options' => $bundles,
    '#default_value' => !empty($queue->settings['target_bundles']) ? $queue->settings['target_bundles'] : array(),
    '#multiple' => TRUE,
    '#description' => t('The bundles of the entity type that can be referenced. Optional, leave empty for all bundles.')
  );

  $form['settings']['min_size'] = array(
    '#type' => 'textfield',
    '#size' => 3,
    '#default_value' => $queue->settings['min_size'],
    '#prefix' => '<div class="container-inline form-item">',
    '#field_prefix' => t('Restrict this queue to a minimum of'),
  );
  $form['settings']['max_size'] = array(
    '#type' => 'textfield',
    '#size' => 3,
    '#default_value' => $queue->settings['max_size'],
    '#field_prefix' => t('and a maximum of'),
    '#field_suffix' => t('items.'),
    '#suffix' => '</div>',
  );
}

/**
 * Selects just the target bundles dropdown to be returned for re-rendering.
 *
 * @return array
 *  A renderable array representing the target bundles dropdown.
 */
function entityqueue_export_ui_target_type_callback($form, $form_state) {
  return $form['settings']['target_bundles'];
}
