<?php

namespace Drupal\socialize\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Component\Utility\Html;

/**
 * Provides an 'Social Shares' Block
 *
 * @Block(
 *   id = "socialize_social_shares",
 *   admin_label = @Translation("Social Shares"),
 * )
 */
class SocialShares extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
  	$config = \Drupal::config('socialize.settings');
    $shares = $config->get('share_links');

    $items = [];
    foreach ($shares as $s) {
      if (isset($s['service']) && $s['service']) {
        $service = Html::escape($s['service']);
        $items[] = [
          '#type' => 'markup',
          '#markup' => '<a href="#" data-socialize-share="' . $service . '">' . ucfirst($service) . '</a>',
        ];
      }
    }
    //print_r($items); exit();

    $render = [
      '#cache' => [
      	'tags' => [
      		'config:socialize.settings',
      	],
      ],
      '#attached' => [
        'library' => [
          'socialize/socialize',
        ],
      ],
      '#type' => 'container',
      '#attributes' => [
        'class' => ['socialize social-share'],
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