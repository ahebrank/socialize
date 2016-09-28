<?php

namespace Drupal\socialize\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an 'Social Links' Block
 *
 * @Block(
 *   id = "socialize_social_links",
 *   admin_label = @Translation("Social Links"),
 * )
 */
class SocialLinks extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
  	$config = \Drupal::config('socialize.settings');
    $links = $config->get('link_links');

    $items = [];
    foreach ($links as $l) {
      $url = \Drupal::service('path.validator')->getUrlIfValid($l['url']);
      $url->setAbsolute();
      $items[] = [
        '#type' => 'link',
        '#title' => $l['title'],
        '#url' => $url,
      ];
    }

    $render = [
      '#cache' => [
      	'tags' => [
      		'config:socialize.settings',
      	],
      ],
      '#type' => 'container',
      '#attributes' => [
        'class' => ['socialize social-links'],
      ],
      'links' => [
      	'#theme' => 'item_list',
        '#type' => 'ul',
      	'#items' => $items,
      ],
    ];

    return $render;
  }
}