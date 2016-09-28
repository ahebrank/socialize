/**
 * @file share.js
 *
 * Hook up sharing
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  var shareUrl = function(service) {
    var href = href = encodeURIComponent(window.location.href)
    if (service == 'facebook') {
      return 'https://www.facebook.com/sharer/sharer.php?u=' + href
    }
    if (service == 'twitter') {
      return 'https://twitter.com/home?status=' + href
    }
    return '#';
  };

  Drupal.behaviors.socialize = {
    attach: function (context) {
      $('a[data-socialize-share]').each(function() {
        var $link = $(this);
        var url = shareUrl($link.data('socialize-share'));
        $link
          .attr('target', '_blank')
          .attr('href', url);
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
