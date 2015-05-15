<?php
/**
 * @file
 * Contains the EntityQueueHandler annotation plugin.
 */

namespace Drupal\entityqueue\Annotation;

use Drupal\Component\Annotation\Plugin;


/**
 * Defines a QueueHandler annotation object.
 *
 * @Annotation
 */
class QueueHandler extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The title of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;
}