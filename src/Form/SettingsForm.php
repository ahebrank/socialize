<?php

namespace Drupal\socialize\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 *
 * @package Drupal\socialize\Form
 */
class SettingsForm extends ConfigFormBase {

  private $packageName = 'socialize';

  private $supportedShares = [
    'facebook' => 'Facebook',
    'twitter' => 'Twitter', 
  ]; 

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return $this->packageName . '_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [$this->packageName . '.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config($this->packageName . '.settings');
    //print_r($config->get()); exit();

    $form['#title'] = 'Socialize Settings';
    $form['#tree'] = TRUE;
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $shares = $config->get('share_links');
    $links = $config->get('link_links');

    $share_field = $form_state->get('num_shares');
    if (empty($share_field)) {
      $share_field = max(1, count($shares));
      $form_state->set('num_shares', $share_field);
    }

    $form['shares_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Social Shares',
      '#prefix' => '<div id="shares-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $share_field; $i++) {
      $form['shares_fieldset'][$i]['service'] = [
        '#type' => 'select',
        '#title' => 'Share',
        '#options' => array_merge(['' => '- Select a service -'], $this->supportedShares),
        '#default_value' => isset($shares[$i]['service'])? $shares[$i]['service']:'',
      ];
    }

    $form['shares_fieldset']['actions']['add_share'] = [
      '#type' => 'submit',
      '#value' => t('Add a share'),
      '#submit' => ['::addShareCallback'],
      '#ajax' => [
        'callback' => '::shareCallback',
        'wrapper' => 'shares-fieldset-wrapper',
      ],
    ];
    if ($share_field > 1) {
      $form['shares_fieldset']['actions']['remove_share'] = [
        '#type' => 'submit',
        '#value' => t('Remove a share'),
        '#submit' => ['::removeShareCallback'],
        '#ajax' => [
          'callback' => '::shareCallback',
          'wrapper' => 'shares-fieldset-wrapper',
        ],
      ];
    }

    $link_field = $form_state->get('num_links');
    if (empty($link_field)) {
      $link_field = max(1, count($links));
      $form_state->set('num_links', $link_field);
    }

    $form['links_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Social Follow-us Links',
      '#prefix' => '<div id="links-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $link_field; $i++) {
      $form['links_fieldset']['link_fieldset'][$i] = [
        '#type' => 'fieldset',
        '#title' => 'Follow',
      ];
      $form['links_fieldset']['link_fieldset'][$i]['title'] = [
        '#type' => 'textfield',
        '#title' => t('Title'),
        '#default_value' => isset($links[$i]['title'])? $links[$i]['title']:'',
      ];
      $form['links_fieldset']['link_fieldset'][$i]['url'] = [
        '#type' => 'url',
        '#title' => t('URL'),
        '#default_value' => isset($links[$i]['url'])? $links[$i]['url']:'',
      ];
    }
    $form['links_fieldset']['actions']['add_link'] = [
      '#type' => 'submit',
      '#value' => t('Add a link'),
      '#submit' => array('::addLinkCallback'),
      '#ajax' => [
        'callback' => '::linkCallback',
        'wrapper' => 'links-fieldset-wrapper',
      ],
    ];
    if ($link_field > 1) {
      $form['links_fieldset']['actions']['remove_link'] = [
        '#type' => 'submit',
        '#value' => t('Remove a link'),
        '#submit' => array('::removeLinkCallback'),
        '#ajax' => [
          'callback' => '::linkCallback',
          'wrapper' => 'links-fieldset-wrapper',
        ],
      ];
    }


    $form_state->setCached(FALSE);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config($this->packageName . '.settings');
    $form_state->cleanValues();

    $shares = $form_state->getValue('shares_fieldset');
    unset($shares['actions']);
    $config->set('share_links', $shares);

    $links = $form_state->getValue('links_fieldset');
    if (isset($links['link_fieldset'])) {
      $config->set('link_links', $links['link_fieldset']);
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }


  /**
   * Generalize the ajax form updates
   */
  private function updateState(FormStateInterface $form_state, $tracker, $delta) {
    $i = $form_state->get($tracker);
    if ($i + $delta > 0) {
      $form_state->set($tracker, $i + $delta);
    }
    $form_state->setRebuild();
  }

  /**
   * Callback for both share ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function shareCallback(array &$form, FormStateInterface $form_state) {
    $share_field = $form_state->get('num_shares');
    return $form['shares_fieldset'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addShareCallback(array &$form, FormStateInterface $form_state) {
    $this->updateState($form_state, 'num_shares', 1);
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeShareCallback(array &$form, FormStateInterface $form_state) {
    $this->updateState($form_state, 'num_shares', -1);
  }

  /**
   * Callback for both link ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function linkCallback(array &$form, FormStateInterface $form_state) {
    $link_field = $form_state->get('num_links');
    return $form['links_fieldset'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addLinkCallback(array &$form, FormStateInterface $form_state) {
    $this->updateState($form_state, 'num_links', 1);
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeLinkCallback(array &$form, FormStateInterface $form_state) {
    $this->updateState($form_state, 'num_links', -1);
  }


}
