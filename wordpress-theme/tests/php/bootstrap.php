<?php
/**
 * PHPUnit bootstrap for the NSML theme's pure-PHP unit tests.
 *
 * We deliberately do NOT load WordPress itself -- these are unit tests for
 * plain-PHP logic (sanitizers, JSON helpers) plus a couple of functions
 * that call a small number of WordPress functions, which Brain Monkey
 * mocks for us. Loading the functions-under-test requires ABSPATH to be
 * defined, since the theme files guard against direct access.
 */

define( 'ABSPATH', __DIR__ . '/' );

require dirname( __DIR__, 2 ) . '/vendor/autoload.php';

/*
 * The theme files call add_action()/register_post_type() etc. at module
 * (file-include) scope. Brain Monkey only mocks WordPress functions for
 * the duration of an individual test (between setUp()/tearDown()), so by
 * the time we `require` these files once here at bootstrap, no test is
 * running yet. We provide minimal real no-op fallbacks for the handful of
 * WP functions invoked at file scope, purely so the files can be included
 * without fatal errors; the actual functions under test
 * (sanitizers, nsml_property_meta_auth_callback, nsml_get_property_meta_array)
 * are called from within tests, where Brain Monkey's mocks (set up in
 * setUp()) take precedence for any WP function they call internally.
 */
if ( ! function_exists( 'add_action' ) ) {
	function add_action( ...$args ) {
		return true;
	}
}
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( ...$args ) {
		return true;
	}
}
if ( ! function_exists( 'register_post_type' ) ) {
	function register_post_type( ...$args ) {
		return true;
	}
}
if ( ! function_exists( 'register_post_meta' ) ) {
	function register_post_meta( ...$args ) {
		return true;
	}
}
if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

require dirname( __DIR__, 2 ) . '/nsml/inc/cpt-property.php';
require dirname( __DIR__, 2 ) . '/nsml/inc/template-tags.php';
require dirname( __DIR__, 2 ) . '/nsml/inc/contact-form.php';
require dirname( __DIR__, 2 ) . '/nsml/inc/newsletter.php';
