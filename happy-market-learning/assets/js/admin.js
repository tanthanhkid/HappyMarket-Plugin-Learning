/**
 * Admin JavaScript
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Ad management
		var adIndex = $('.hm-ad-item').length;

		$('.hm-add-ad').on('click', function() {
			var adHtml = '<div class="hm-ad-item" data-index="' + adIndex + '">' +
				'<h4>Ad #' + (adIndex + 1) + '</h4>' +
				'<table class="form-table">' +
				'<tr><th><label>Image URL:</label></th>' +
				'<td><input type="url" name="hm_lesson_ads[' + adIndex + '][image_url]" class="regular-text" />' +
				'<button type="button" class="button hm-upload-image">Upload Image</button></td></tr>' +
				'<tr><th><label>Link URL:</label></th>' +
				'<td><input type="url" name="hm_lesson_ads[' + adIndex + '][link_url]" class="regular-text" /></td></tr>' +
				'<tr><th><label>Alt Text:</label></th>' +
				'<td><input type="text" name="hm_lesson_ads[' + adIndex + '][alt_text]" class="regular-text" /></td></tr>' +
				'<tr><th><label>Position:</label></th>' +
				'<td><select name="hm_lesson_ads[' + adIndex + '][position]">' +
				'<option value="sidebar">Sidebar</option>' +
				'<option value="popup">Popup</option>' +
				'<option value="before_video">Before Video</option>' +
				'<option value="after_video">After Video</option>' +
				'<option value="between_video">Between Video</option>' +
				'</select></td></tr>' +
				'<tr><th><label>Active:</label></th>' +
				'<td><input type="checkbox" name="hm_lesson_ads[' + adIndex + '][active]" value="1" checked /></td></tr>' +
				'</table>' +
				'<button type="button" class="button hm-remove-ad">Remove Ad</button>' +
				'</div>';

			$('#hm-ads-container').append(adHtml);
			adIndex++;
		});

		$(document).on('click', '.hm-remove-ad', function() {
			$(this).closest('.hm-ad-item').remove();
		});

		// Media uploader for ads
		$(document).on('click', '.hm-upload-image', function(e) {
			e.preventDefault();
			var button = $(this);
			var input = button.siblings('input[type="url"]');

			var mediaUploader = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Use this image'
				},
				multiple: false
			});

			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				input.val(attachment.url);
			});

			mediaUploader.open();
		});

		// YouTube URL validation
		$('#hm_lesson_youtube_url').on('blur', function() {
			var url = $(this).val();
			if (url) {
				$.ajax({
					url: hmAdmin.ajaxUrl,
					type: 'POST',
					data: {
						action: 'hm_validate_youtube_url',
						url: url,
						nonce: hmAdmin.nonce
					},
					success: function(response) {
						if (response.success && response.data.video_id) {
							// Update preview if needed
							console.log('Video ID:', response.data.video_id);
						}
					}
				});
			}
		});

		// Product search (WooCommerce)
		if ($('#hm_product_search').length) {
			var productSearchTimeout;
			$('#hm_product_search').on('input', function() {
				clearTimeout(productSearchTimeout);
				var searchTerm = $(this).val();

				if (searchTerm.length < 2) {
					return;
				}

				productSearchTimeout = setTimeout(function() {
					// AJAX product search would go here
					// This is a placeholder for future implementation
				}, 300);
			});
		}
	});
})(jQuery);
