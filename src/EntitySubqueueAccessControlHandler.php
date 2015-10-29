<?php

/**
 * @file
 * Contains \Drupal\entityqueue\EntitySubqueueAccessControlHandler.
 */

namespace Drupal\entityqueue;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the entity_subqueue entity type.
 *
 * @see \Drupal\entityqueue\Entity\EntitySubqueue
 */
class EntitySubqueueAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'access content');
        break;

      case 'update':
        return AccessResult::allowedIfHasPermissions($account, ["update {$entity->bundle()} entityqueue", 'manipulate all entityqueues', 'administer entityqueue'], 'OR');
        break;

      case 'delete':
        return AccessResult::allowedIfHasPermissions($account, ["delete {$entity->bundle()} entityqueue", 'manipulate all entityqueues', 'administer entityqueue'], 'OR');
        break;

      default:
        // No opinion.
        return AccessResult::neutral();
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, ["create {$entity_bundle} entityqueue", 'manipulate all entityqueues', 'administer entityqueue'], 'OR');
  }

}
