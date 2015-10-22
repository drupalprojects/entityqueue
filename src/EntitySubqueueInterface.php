<?php

/**
 * @file
 * Contains \Drupal\entityqueue\EntitySubqueueInterface.
 */

namespace Drupal\entityqueue;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a EntityQueue entity.
 */
interface EntitySubqueueInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {



}
