<?php

/**
 * @file
 * Views integration for Entityqueue.
 */

/**
 * Implements hook_views_data_alter().
 *
 * @see entityreference_field_views_data_views_data_alter()
 */
function entityqueue_views_data_alter(&$data) {
  foreach (entityqueue_type_get_name() as $type => $name) {
    $field = field_info_field('entityqueue_target_' . $type);

    // @todo: Temporary workaround until entityqueue_target_* fields are created
    // for an entity type when it's providing module is installed.
    if (!$field) {
      continue;
    }

    foreach ($field['bundles'] as $entity_type => $bundles) {
      $target_entity_info = entity_get_info($field['settings']['target_type']);
      if (isset($target_entity_info['base table'])) {
        $entity_info = entity_get_info($entity_type);

        $data[$target_entity_info['base table']]['entityqueue_relationship']['relationship'] = array(
          'handler' => 'entityqueue_handler_relationship_entityqueue',
          'field_name' => $field['field_name'],
          'field table' => _field_sql_storage_tablename($field),
          'field field' => $field['field_name'] . '_target_id',
          'base' => $entity_info['base table'],
          'base field' => $entity_info['entity keys']['id'],
          'label' => t('Queue'),
          'group' => t('Entityqueue'),
          'title' => t('Queue'),
          'help' => t('Create a relationship to a entityqeuue.'),
        );
      }
    }
  }
}
