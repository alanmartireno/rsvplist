<?php

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides an 'RSVP' List Block.
 *
 * @Block(
 * id = "rsvp_block",
 * admin_lael = @Translation("RSVP Block"),
 * )
 */
class RSVPBlock extends BlockBase {

  /**
   * Build function.
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
  }

  /**
   * Undocumented function.
   */
  public function blockAccess(AccountInterface $account) {

    /** @var \Drupal\node\Entity\Node $node */
    $node = \Drupal::routeMatch()->getParameter('node');

    /** @var \Drupal\rsvplist\EnablerService $enabler
     * $enabler = \Drupal::service('rsvplist.enabler');
     * if ($node instanceof \Drupal\node\NodeInterface) {
     */
    if (isset($node)) {
      $nid = $node->id();
    }
    /** @var \Drupal\rsvplist\Entity\Node $node */
    $enabler = \Drupal::service('rsvplist.enabler');
    if (is_numeric($nid)) {
      if ($enabler->isEnabled($node)) {
        return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
      }

    }
    return AccessResult::forbidden();

  }

}
