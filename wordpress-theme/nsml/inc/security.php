<?php
/**
 * Security hardening.
 *
 * Theme-level hardening only (anything that genuinely needs wp-config.php,
 * such as DISALLOW_FILE_EDIT or DB credentials, is documented in
 * wordpress-theme/SECURITY.md instead — a theme cannot safely write to
 * wp-config.php at runtime).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Stop leaking the exact WP version in markup / feeds — reduces target fingerprinting. */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/** Disable XML-RPC: not used by this theme and a common brute-force / pingback abuse vector. */
add_filter( 'xmlrpc_enabled', '__return_false' );

/** Remove the REST API's user-listing endpoint for unauthenticated requests (user enumeration). */
function nsml_restrict_rest_user_endpoint( $result ) {
	if ( is_wp_error( $result ) || is_user_logged_in() ) {
		return $result;
	}
	if ( isset( $GLOBALS['wp']->query_vars['rest_route'] )
		&& false !== stripos( $GLOBALS['wp']->query_vars['rest_route'], '/wp/v2/users' ) ) {
		return new WP_Error(
			'rest_forbidden',
			__( 'Sorry, you are not allowed to do that.', 'nsml' ),
			array( 'status' => 401 )
		);
	}
	return $result;
}
add_filter( 'rest_authentication_errors', 'nsml_restrict_rest_user_endpoint' );

/** Hide author archive enumeration via ?author=N for unauthenticated visitors. */
function nsml_block_author_enum() {
	if ( ! is_user_logged_in() && isset( $_GET['author'] ) && is_numeric( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only redirect guard, no state change.
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'nsml_block_author_enum' );

/** Send a conservative set of security headers (defence in depth alongside server/.htaccess config). */
function nsml_security_headers( $headers ) {
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['X-Frame-Options']        = 'SAMEORIGIN';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	$headers['Permissions-Policy']     = 'geolocation=(), microphone=(), camera=()';
	return $headers;
}
add_filter( 'wp_headers', 'nsml_security_headers' );

/** Disable comments site-wide: this theme has no comment templates and an unused comment form is an open spam/XSS surface. */
function nsml_disable_comments_support() {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'page', 'comments' );
	remove_post_type_support( 'nsml_property', 'comments' );
}
add_action( 'init', 'nsml_disable_comments_support', 100 );

add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
