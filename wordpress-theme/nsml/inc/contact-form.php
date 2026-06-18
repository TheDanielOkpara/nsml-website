<?php
/**
 * Server-side handling for the contact form on page-contact.php.
 *
 * No forms plugin is required: this wires the existing static-site markup
 * straight to wp_mail() over admin-ajax.php. The one thing a theme can't
 * fix on its own is mail deliverability — see README.md's "Plugins" section,
 * which recommends installing WP Mail SMTP so wp_mail() actually leaves the
 * server instead of relying on the host's often-unreliable PHP mail().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NSML_CONTACT_NONCE_ACTION', 'nsml_contact_form' );
define( 'NSML_CONTACT_THROTTLE_PREFIX', 'nsml_contact_throttle_' );

/**
 * The selectable "Area of Interest" values and their human-readable labels,
 * shared between server-side validation and the email subject/body.
 */
function nsml_contact_interest_labels() {
	return array(
		'sponsorship' => __( 'Event Sponsorship', 'nsml' ),
		'brand'       => __( 'Brand Management', 'nsml' ),
		'marketing'   => __( 'Sports Marketing', 'nsml' ),
		'contracts'   => __( 'Contract Negotiations', 'nsml' ),
		'consulting'  => __( 'Sponsorship Consulting', 'nsml' ),
		'procurement' => __( 'Sports Procurement', 'nsml' ),
		'project'     => __( 'Project Management', 'nsml' ),
		'general'     => __( 'General Enquiry', 'nsml' ),
	);
}

/**
 * Pure validation of already-sanitized field values, mirroring the
 * client-side rules in page-contact.php (name >= 2 chars, valid email,
 * a known interest option, message between 20 and 500 characters).
 * Kept separate from nsml_handle_contact_form_submission() so it can be
 * unit tested without booting WordPress or touching $_POST.
 *
 * @return bool
 */
function nsml_contact_form_is_valid( $fname, $lname, $email, $interest, $message ) {
	$interest_labels = nsml_contact_interest_labels();

	return mb_strlen( $fname ) >= 2
		&& mb_strlen( $lname ) >= 2
		&& is_email( $email )
		&& isset( $interest_labels[ $interest ] )
		&& mb_strlen( $message ) >= 20
		&& mb_strlen( $message ) <= 500;
}

add_action( 'wp_ajax_nsml_submit_contact', 'nsml_handle_contact_form_submission' );
add_action( 'wp_ajax_nopriv_nsml_submit_contact', 'nsml_handle_contact_form_submission' );

function nsml_handle_contact_form_submission() {
	if ( ! isset( $_POST['nsml_contact_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['nsml_contact_nonce'] ), NSML_CONTACT_NONCE_ACTION ) ) {
		wp_send_json_error( array( 'message' => __( 'Your session expired — please reload the page and try again.', 'nsml' ) ), 403 );
	}

	// Honeypot: a real visitor never fills this hidden field in.
	if ( ! empty( $_POST['nsml_hp'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'nsml' ) ), 400 );
	}

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( $ip ) {
		$throttle_key = NSML_CONTACT_THROTTLE_PREFIX . md5( $ip );
		if ( get_transient( $throttle_key ) >= 5 ) {
			wp_send_json_error( array( 'message' => __( 'Too many messages sent — please try again in a few minutes.', 'nsml' ) ), 429 );
		}
	}

	$fname    = isset( $_POST['fname'] ) ? sanitize_text_field( wp_unslash( $_POST['fname'] ) ) : '';
	$lname    = isset( $_POST['lname'] ) ? sanitize_text_field( wp_unslash( $_POST['lname'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone    = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$org      = isset( $_POST['org'] ) ? sanitize_text_field( wp_unslash( $_POST['org'] ) ) : '';
	$interest = isset( $_POST['interest'] ) ? sanitize_text_field( wp_unslash( $_POST['interest'] ) ) : '';
	$message  = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$interest_labels = nsml_contact_interest_labels();

	if ( ! nsml_contact_form_is_valid( $fname, $lname, $email, $interest, $message ) ) {
		wp_send_json_error( array( 'message' => __( 'Please check the highlighted fields and try again.', 'nsml' ) ), 400 );
	}

	$to      = nsml_theme_setting( 'contact_email' );
	$subject = sprintf( __( 'New contact form enquiry: %s', 'nsml' ), $interest_labels[ $interest ] );
	$body    = implode(
		"\n",
		array(
			'Name: ' . $fname . ' ' . $lname,
			'Email: ' . $email,
			'Phone: ' . ( $phone ? $phone : '(not provided)' ),
			'Organisation: ' . ( $org ? $org : '(not provided)' ),
			'Area of interest: ' . $interest_labels[ $interest ],
			'',
			'Message:',
			$message,
		)
	);
	$headers = array( 'Reply-To: ' . $fname . ' ' . $lname . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => __( 'Sorry, your message could not be sent. Please email us directly instead.', 'nsml' ) ), 500 );
	}

	if ( $ip ) {
		$throttle_key = NSML_CONTACT_THROTTLE_PREFIX . md5( $ip );
		set_transient( $throttle_key, (int) get_transient( $throttle_key ) + 1, 10 * MINUTE_IN_SECONDS );
	}

	wp_send_json_success( array( 'message' => __( 'Message sent. Thank you for reaching out.', 'nsml' ) ) );
}
