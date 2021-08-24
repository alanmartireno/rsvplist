<?php

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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
    if ($value == !\Drupal::service('email.validator')->isValid($value)) {
      $form_state->setErrorByName('email', $this->t('The email address is not valid.',
      ['%mail' => $value]));
    }
  }

  /**
   * Method submitForm submit message of successfull.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    return \Drupal::messenger()->addMessage('The form is working');
  }

}
