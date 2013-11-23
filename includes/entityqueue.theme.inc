<?php

/**
 * @file
 * Contains theme implementations for the Entityqueue module.
 */

/**
 * Returns the HTML used for a queue on the entityqueue overview listing.
 *
 * @ingroup themeable
 */
function theme_entityqueue_overview_item($variables) {
  $output = '<strong>' . check_plain($variables['label']) . '</strong>';
  if ($variables['name']) {
    $output .= ' <small> (' . t('Machine name') . ': ' . check_plain($variables['name']) . ')</small>';
  }
  $output = '<div class="entityqueue-title">' . $output . '</div>';
  if ($variables['status']) {
    $output .= '<div class="entityqueue-status">' . theme('entityqueue_status', array('status' => $variables['status'], TRUE)) . '</div>';
  }
  return $output;
}

/**
 * Themes the exportable status of a queue.
 *
 * @ingroup themeable
 */
function theme_entityqueue_status($variables) {
  $status = $variables['status'];
  $html = $variables['html'];
  if (($status & (EXPORT_IN_CODE | EXPORT_IN_DATABASE)) == (EXPORT_IN_CODE | EXPORT_IN_DATABASE)) {
    $label = t('Database overriding code');
    $help = t('This configuration is provided by a module, but has been changed.');
    return $html ? "<span class='entityqueue-status-overridden' title='$help'>" . $label . "</span>" : $label;
  }
  elseif ($status & EXPORT_IN_CODE) {
    $label = t('In code');
    $help = t('A module provides this configuration.');
    return $html ? "<span class='entityqueue-status-default' title='$help'>" . $label . "</span>" : $label;
  }
  elseif ($status & EXPORT_IN_DATABASE) {
    $label = t('In database');
    $help = t('A custom configuration by a user.');
    return $html ? "<span class='entityqueue-status-custom' title='$help'>" . $label . "</span>" : $label;
  }
}
