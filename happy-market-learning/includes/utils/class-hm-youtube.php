<?php
/**
 * YouTube Utilities
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/utils
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * YouTube Utilities Class
 */
class HM_YouTube {

	/**
	 * Extract YouTube video ID from URL
	 *
	 * @param string $url YouTube URL.
	 * @return string|false Video ID or false on failure.
	 */
	public static function extract_video_id( $url ) {
		if ( empty( $url ) ) {
			return false;
		}

		$patterns = array(
			'#(?:https?://)?(?:www\.)?(?:youtube\.com/watch\?v=|youtu\.be/)([a-zA-Z0-9_-]{11})#',
			'#(?:https?://)?(?:www\.)?youtube\.com/embed/([a-zA-Z0-9_-]{11})#',
			'#(?:https?://)?(?:www\.)?youtube\.com/v/([a-zA-Z0-9_-]{11})#',
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $url, $matches ) ) {
				return $matches[1];
			}
		}

		return false;
	}

	/**
	 * Validate YouTube URL
	 *
	 * @param string $url YouTube URL.
	 * @return bool True if valid, false otherwise.
	 */
	public static function validate_url( $url ) {
		if ( empty( $url ) ) {
			return false;
		}

		$video_id = self::extract_video_id( $url );
		return false !== $video_id;
	}

	/**
	 * Get YouTube embed URL
	 *
	 * @param string $video_id YouTube video ID.
	 * @param array  $params Optional embed parameters.
	 * @return string Embed URL.
	 */
	public static function get_embed_url( $video_id, $params = array() ) {
		if ( empty( $video_id ) ) {
			return '';
		}

		$default_params = array(
			'autoplay'       => 0,
			'controls'      => 1,
			'modestbranding' => 1,
			'rel'            => 0,
			'showinfo'       => 0,
			'enablejsapi'    => 1,
		);

		$params = wp_parse_args( $params, $default_params );
		$query_string = http_build_query( $params );

		return "https://www.youtube.com/embed/{$video_id}?" . $query_string;
	}

	/**
	 * Get YouTube thumbnail URL
	 *
	 * @param string $video_id YouTube video ID.
	 * @param string $quality Thumbnail quality: default, mqdefault, hqdefault, sddefault, maxresdefault.
	 * @return string Thumbnail URL.
	 */
	public static function get_thumbnail_url( $video_id, $quality = 'hqdefault' ) {
		if ( empty( $video_id ) ) {
			return '';
		}

		$qualities = array( 'default', 'mqdefault', 'hqdefault', 'sddefault', 'maxresdefault' );
		if ( ! in_array( $quality, $qualities, true ) ) {
			$quality = 'hqdefault';
		}

		return "https://img.youtube.com/vi/{$video_id}/{$quality}.jpg";
	}

	/**
	 * Get video metadata from YouTube API (optional)
	 *
	 * @param string $video_id YouTube video ID.
	 * @return array|false Video metadata or false on failure.
	 */
	public static function get_video_metadata( $video_id ) {
		$api_key = get_option( 'hm_youtube_api_key', '' );
		if ( empty( $api_key ) || empty( $video_id ) ) {
			return false;
		}

		$api_url = add_query_arg(
			array(
				'part'       => 'snippet,contentDetails',
				'id'         => $video_id,
				'key'        => $api_key,
			),
			'https://www.googleapis.com/youtube/v3/videos'
		);

		$response = wp_remote_get( $api_url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! isset( $data['items'][0] ) ) {
			return false;
		}

		return $data['items'][0];
	}
}
