/**
 * Public JavaScript
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Popup ads
		$('.hm-ads-popup').each(function() {
			var $popup = $(this);
			var $close = $popup.find('.hm-close');

			if ($close.length === 0) {
				$close = $('<button class="hm-close">&times;</button>');
				$popup.append($close);
			}

			$close.on('click', function() {
				$popup.fadeOut();
			});

			// Auto-close after 30 seconds
			setTimeout(function() {
				$popup.fadeOut();
			}, 30000);
		});

		// Video progress tracking (optional)
		if (typeof YT !== 'undefined') {
			$('iframe[src*="youtube.com"]').each(function() {
				var player;
				var iframe = this;
				var videoId = $(iframe).attr('src').match(/embed\/([a-zA-Z0-9_-]+)/);

				if (videoId) {
					// YouTube API would be initialized here
					// This is a placeholder for future implementation
				}
			});
		}
	});
})(jQuery);
