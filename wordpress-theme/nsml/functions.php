<?php
/**
 * NSML theme bootstrap.
 *
 * Loads theme setup, asset enqueueing, the Property custom post type,
 * and security hardening. Each concern lives in its own file under inc/
 * so it can be unit tested in isolation (see tests/).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NSML_THEME_VERSION', '1.0.0' );
define( 'NSML_THEME_DIR', get_template_directory() );
define( 'NSML_THEME_URI', get_template_directory_uri() );

require_once NSML_THEME_DIR . '/inc/security.php';
require_once NSML_THEME_DIR . '/inc/setup.php';
require_once NSML_THEME_DIR . '/inc/enqueue.php';
require_once NSML_THEME_DIR . '/inc/cpt-property.php';
require_once NSML_THEME_DIR . '/inc/template-tags.php';
