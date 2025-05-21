<?php
/**
 * Plugin Name: AI Spam Comment Detector
 * Description: Detects spam comments using OpenAI GPT-4 and blocks them with inline warnings.
 * Version: 1.0
 * Author: Rashed Hossain
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start session for inline error handling.
add_action( 'init', function () {
	if ( ! session_id() ) {
		session_start();
	}
}, 1 );

// Add settings page
add_action( 'admin_menu', function () {
	add_options_page(
		'AI Spam Detector Settings',
		'AI Spam Detector',
		'manage_options',
		'ai-spam-detector',
		'ai_spam_detector_settings_page'
	);
} );

// Register API key setting
add_action( 'admin_init', function () {
	register_setting(
		'ai_spam_detector_options',
		'ai_spam_detector_api_key',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
} );

// Settings page callback
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

// Show inline error on comment form
add_action( 'comment_form_before', function () {
	if ( isset( $_SESSION['ai_spam_comment_error'] ) ) {
		echo '<p style="color:red; font-weight:bold;">' . esc_html( $_SESSION['ai_spam_comment_error'] ) . '</p>';
		unset( $_SESSION['ai_spam_comment_error'] );
	}
} );

// Check spam and block it silently
add_filter( 'pre_comment_approved', function ( $approved, $commentdata ) {
	$api_key         = get_option( 'ai_spam_detector_api_key' );
	$comment_content = $commentdata['comment_content'] ?? '';

	if ( empty( $api_key ) || strlen( $comment_content ) < 10 ) {
		return $approved;
	}

	$result = ai_spam_detector_is_spam( $api_key, $comment_content );

	if ( $result === true ) {
		$_SESSION['ai_spam_comment_error'] = __( 'Your comment was detected as spam and was not submitted.', 'ai-spam-comment-detector' );
		return 'spam';
	} elseif ( is_wp_error( $result ) ) {
		$_SESSION['ai_spam_comment_error'] = $result->get_error_message();
		return 'spam';
	}

	return $approved;
}, 10, 2 );

// Check if comment is spam using GPT-4
function ai_spam_detector_is_spam( $api_key, $comment_text ) {
	$endpoint = 'https://api.openai.com/v1/chat/completions';

	$messages = array(
		array(
			'role'    => 'system',
			'content' => 'You are a comment moderation assistant. Determine if a comment is spam.',
		),
		array(
			'role'    => 'user',
			'content' => "Is the following comment spam? Reply only with one word: 'spam' or 'not spam'.\n\nComment:\n\"$comment_text\"",
		),
	);

	$args = array(
		'headers' => array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $api_key,
		),
		'body'    => wp_json_encode( array(
			'model'       => 'gpt-4',
			'messages'    => $messages,
			'max_tokens'  => 3,
			'temperature' => 0,
		) ),
		'timeout' => 15,
	);

	$response = wp_remote_post( $endpoint, $args );

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'api_error', __( 'AI Service is currently unreachable. Please try again later.', 'ai-spam-comment-detector' ) );
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( isset( $body['error'] ) ) {
		$error_msg = $body['error']['message'] ?? __( 'Unknown error from AI API.', 'ai-spam-comment-detector' );

		if ( $code === 401 ) {
			return new WP_Error( 'invalid_key', __( 'Invalid API Key. Please check your key in plugin settings.', 'ai-spam-comment-detector' ) );
		} elseif ( $code === 429 ) {
			return new WP_Error( 'quota_exceeded', __( 'Quota exceeded. Please wait or upgrade your OpenAI plan.', 'ai-spam-comment-detector' ) );
		} else {
			return new WP_Error( 'api_error', sprintf( __( 'OpenAI API error: %s', 'ai-spam-comment-detector' ), esc_html( $error_msg ) ) );
		}
	}

	$reply = strtolower( trim( $body['choices'][0]['message']['content'] ?? '' ) );

	return $reply === 'spam';
}