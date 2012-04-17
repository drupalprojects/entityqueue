<?php

/**
 * @file
 * Contains the CTools Export UI integration code.
 */

/**
 * Defines the CTools Export UI class handler for Entityqueue UI.
 */
class entityqueue_export_ui extends ctools_export_ui {

  /**
   * Overrides ctools_export_ui::list_build_row().
   */
  function list_build_row($queue, &$form_state, $operations) {
    // Rename the 'Edit' operation, as that will be re-assigned to edit subqueue
    // items.
    $operations['edit']['title'] = t('Configure');

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
    $handler = entityqueue_get_handler($queue)->getHandlerLabel();

    $ops = theme('links__ctools_dropbutton', array('links' => $operations, 'attributes' => array('class' => array('links', 'inline'))));

    $this->rows[$queue->name]['data'][] = array('data' => $ops, 'class' => array('ctools-export-ui-operations'));
    $this->rows[$queue->name] = array(
      'data' => array(
        array('data' => $item, 'class' => array('entityqueue-ui-queue')),
        array('data' => filter_xss_admin($target_type), 'class' => array('entityqueue-ui-target-type')),
        array('data' => filter_xss_admin($handler), 'class' => array('entityqueue-ui-handler')),
        array('data' => '', 'class' => array('entityqueue-ui-items')),
        array('data' => $ops, 'class' => array('entityqueue-ui-operations', 'ctools-export-ui-operations')),
      ),
      'title' => t('Machine name: ') . check_plain($queue->name),
      'class' => array(!empty($queue->disabled) ? 'ctools-export-ui-disabled' : 'ctools-export-ui-enabled'),
    );
  }

  /**
   * Overrides ctools_export_ui::list_table_header().
   */
  function list_table_header() {
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
