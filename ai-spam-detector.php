<?php
/**
 * Plugin Name: AI Spam Comment Detector
 * Description: Detects spam comments using OpenAI GPT-4 and blocks them with inline warnings via AJAX.
 * Version: 1.1
 * Author: Rashed Hossain
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Admin Settings
add_action( 'admin_menu', function () {
	add_options_page( 'AI Spam Detector Settings', 'AI Spam Detector', 'manage_options', 'ai-spam-detector', 'ai_spam_detector_settings_page' );
} );

add_action( 'admin_init', function () {
	register_setting( 'ai_spam_detector_options', 'ai_spam_detector_api_key' );
} );

function ai_spam_detector_settings_page() {
	?>
	<div class="wrap">
		<h2>AI Spam Detector Settings</h2>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'ai_spam_detector_options' );
			do_settings_sections( 'ai_spam_detector_options' );
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">OpenAI API Key</th>
					<td><input type="text" name="ai_spam_detector_api_key" value="<?php echo esc_attr( get_option( 'ai_spam_detector_api_key' ) ); ?>" size="50" /></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

// Enqueue frontend JS
add_action( 'wp_enqueue_scripts', function () {
	if ( is_single() || is_page() ) {
		wp_enqueue_script( 'ai-comment-spam-checker', plugin_dir_url( __FILE__ ) . 'js/ai-comment.js', [], null, true );
		wp_localize_script( 'ai-comment-spam-checker', 'aiSpamAjax', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ai_spam_check_nonce' ),
		] );
	}
} );

// Ajax handler
add_action( 'wp_ajax_nopriv_ai_check_spam', 'ai_ajax_check_spam' );

function ai_ajax_check_spam() {
	check_ajax_referer( 'ai_spam_check_nonce', 'nonce' );

	$comment = sanitize_text_field( $_POST['comment'] ?? '' );
	$api_key = get_option( 'ai_spam_detector_api_key' );

	if ( empty( $api_key ) || empty( $comment ) ) {
		wp_send_json_error( [ 'message' => 'Invalid input or API key.' ] );
	}

	$is_spam = ai_spam_detector_is_spam( $api_key, $comment );

	if ( is_wp_error( $is_spam ) ) {
		wp_send_json_error( [ 'message' => $is_spam->get_error_message() ] );
	}

	if ( $is_spam ) {
		wp_send_json_error( [ 'message' => 'Your comment was flagged as spam and not submitted.' ] );
	}

	wp_send_json_success();
}

// GPT-4 spam check
function ai_spam_detector_is_spam( $api_key, $comment_text ) {
	$endpoint = 'https://api.openai.com/v1/chat/completions';
	$messages = [
		[ 'role' => 'system', 'content' => 'You are a spam classifier. Respond only with "spam" or "not spam".' ],
		[ 'role' => 'user', 'content' => "Is the following a spam comment?\n\"$comment_text\"" ],
	];

	$response = wp_remote_post( $endpoint, [
		'headers' => [
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $api_key,
		],
		'body' => wp_json_encode( [
			'model'    => 'gpt-4',
			'messages' => $messages,
			'max_tokens' => 3,
			'temperature' => 0
		] ),
		'timeout' => 15
	] );

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'api_error', 'AI service unreachable.' );
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( isset( $body['error'] ) ) {
		if ( $code === 401 ) return new WP_Error( 'invalid_key', 'Invalid API Key.' );
		if ( $code === 429 ) return new WP_Error( 'quota_exceeded', 'OpenAI quota exceeded.' );
		return new WP_Error( 'api_error', $body['error']['message'] ?? 'Unknown AI error.' );
	}

	$reply = strtolower( trim( $body['choices'][0]['message']['content'] ?? '' ) );
	return $reply === 'spam';
}