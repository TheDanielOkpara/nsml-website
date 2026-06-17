<?php
/**
 * Asset loading.
 *
 * Reuses the exact CSS/JS shipped with the original static site so the
 * rendered output is pixel-identical — no redesign, no regression risk.
 * Cache-busting uses filemtime() instead of a hardcoded version string so
 * browsers always pick up new CSS/JS after a deploy without a manual bump.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nsml_asset_version( $relative_path ) {
	$file = NSML_THEME_DIR . '/' . $relative_path;
	return file_exists( $file ) ? filemtime( $file ) : NSML_THEME_VERSION;
}

function nsml_enqueue_assets() {
	wp_enqueue_style(
		'nsml-fonts',
		'https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'nsml-styles',
		NSML_THEME_URI . '/assets/css/styles.css',
		array(),
		nsml_asset_version( 'assets/css/styles.css' )
	);

	wp_enqueue_script(
		'gsap',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
		array(),
		'3.12.5',
		true
	);
	wp_enqueue_script(
		'gsap-scrolltrigger',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
		array( 'gsap' ),
		'3.12.5',
		true
	);

	wp_enqueue_script(
		'nsml-nav',
		NSML_THEME_URI . '/assets/js/nav.js',
		array(),
		nsml_asset_version( 'assets/js/nav.js' ),
		true
	);
	wp_enqueue_script(
		'nsml-main',
		NSML_THEME_URI . '/assets/js/main.js',
		array( 'gsap', 'gsap-scrolltrigger' ),
		nsml_asset_version( 'assets/js/main.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'nsml_enqueue_assets' );

/**
 * Remove the inline nav-injecting bootstrap from nav.js (it self-appends
 * main.js via document.createElement) now that WP enqueues main.js
 * properly — avoids loading main.js twice.
 */
function nsml_dequeue_duplicate_main_js() {
	wp_add_inline_script( 'nsml-nav', 'window.__nsmlMainJsEnqueued = true;', 'before' );
}
add_action( 'wp_enqueue_scripts', 'nsml_dequeue_duplicate_main_js', 20 );
