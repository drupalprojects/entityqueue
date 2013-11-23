<?php

/**
 * @file
 * Hooks provided by Entityqueue.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * This hook allows modules to provide their own queues.
 *
 * @return array
 *   An associative array containing EntityQueue objects.
 */
function hook_entityqueue_default_queues() {
  $queues = array();

  $queue = new EntityQueue();
  $queue->disabled = FALSE; /* Edit this to true to make a default queue disabled initially */
  $queue->api_version = 1;
  $queue->name = 'featured_articles';
  $queue->label = 'Featured articles';
  $queue->handler = 'simple';
  $queue->target_type = 'node';
  $queue->settings = array(
    'target_bundles' => array(),
    'min_size' => 0,
    'max_size' => 0,
    'subqueue_label' => '',
  );
  $queues['featured_articles'] = $queue;

  return $queues;
}

/**
 * @} End of "addtogroup hooks".
 */
