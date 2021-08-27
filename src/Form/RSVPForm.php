<?php

namespace Drupal\rsvplist\Form;

use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Provides an RSVP Email form.
 */
class RSVPForm extends FormBase {

  /**
   * Method get form id.
   */
  public function getFormId() {
    return 'rsvp_email_form';
  }

  /**
   * BuildForm function.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $node = \Drupal::routeMatch()->getParameter('node');
    if (isset($node)) {
      $nid = $node->id();
    }
    $form['email'] = [
      '#title' => $this->t('Email Address'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => $this->t("We'll send updates to the email address your
      provide"),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];
    return $form;
  }

  /**
   * Method of email validation.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    $email_validator = \Drupal::service('email.validator');
    if ($value == !$email_validator->isValid($value)) {
      $form_state->setErrorByName('email', $this->t(
        'The email address is not valid.',
        ['%mail' => $value]
      ));
      return;
    }
    $node = \Drupal::routeMatch()->getParameter('node');
    // Check if email already is set for this node.
    $select = Database::getConnection()->select('rsvplist', 'r');
    $select->fields('r', ['nid']);
    $select->condition('nid', $node->id());
    $select->condition('mail', $value);
    $results = $select->execute();
    if (!empty($results->fetchCol())) {
      // We found a row with this nid and email.
      $form_state->setErrorByName('email', $this->t('The Address %mail is already subscribed to this list.',
      ['%mail' => $value]));
    }
  }

  /**
   * Method submitForm submit message of successfull.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = User::load(\Drupal::currentUser()->id());
    \Drupal::database()->insert('rsvplist')
      ->fields([
        'mail' => $form_state->getValue('email'),
        'nid' => $form_state->getValue('nid'),
        'uid' => $user->id(),
        'created' => time(),
      ])->execute();
    \Drupal::messenger()->addMessage($this->t('Thanks for your RSVP,
    you are on the list event'));

  }

}
