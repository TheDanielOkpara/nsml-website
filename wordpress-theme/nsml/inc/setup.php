<?php
/**
 * Theme setup: supports, menus, image sizes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nsml_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'script', 'style' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );

	// Matches the .article-hero-img / .prop-hero-bg crop used across the static site.
	add_image_size( 'nsml-hero', 1920, 1080, true );
	add_image_size( 'nsml-card', 840, 472, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'nsml' ),
			'footer'  => __( 'Footer Navigation', 'nsml' ),
		)
	);
}
add_action( 'after_setup_theme', 'nsml_theme_setup' );

/**
 * Register widget areas used by the footer "Properties" column, in case
 * the site owner wants to manage that list without editing the theme.
 */
function nsml_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer Properties', 'nsml' ),
			'id'            => 'footer-properties',
			'description'   => __( 'Links shown in the footer "Properties" column.', 'nsml' ),
			'before_widget' => '<li>',
			'after_widget'  => '</li>',
			'before_title'  => '<div class="f-col-title">',
			'after_title'   => '</div>',
		)
	);
}
add_action( 'widgets_init', 'nsml_widgets_init' );
