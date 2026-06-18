<?php
/**
 * Shared template helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Real "Related Stories" — replaces the hardcoded Unsplash placeholder
 * cards that shipped in the static site. Pulls the N most recent posts
 * excluding the current one.
 *
 * @return WP_Post[]
 */
function nsml_get_related_posts( $exclude_id, $count = 3 ) {
	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'post__not_in'        => array( $exclude_id ),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	$posts = $query->posts;
	wp_reset_postdata();
	return $posts;
}

/**
 * Decode a property's JSON stats / gallery meta into a plain array.
 * Returns an empty array on any malformed input rather than warning/erroring,
 * since this also feeds output escaping in templates.
 */
function nsml_get_property_meta_array( $post_id, $meta_key ) {
	$raw = get_post_meta( $post_id, $meta_key, true );
	$decoded = json_decode( (string) $raw, true );
	return is_array( $decoded ) ? $decoded : array();
}

function nsml_get_property_stats( $post_id ) {
	return nsml_get_property_meta_array( $post_id, 'nsml_stats_json' );
}

function nsml_get_property_gallery( $post_id ) {
	return nsml_get_property_meta_array( $post_id, 'nsml_gallery_json' );
}

/**
 * Config consumed by assets/js/nav.js so the shared nav component renders
 * real permalinks and correctly highlights the active item, without
 * touching the original static-site markup/behaviour.
 */
function nsml_nav_config() {
	$nav_items = array(
		array( 'key' => 'home', 'label' => __( 'Home', 'nsml' ), 'url' => home_url( '/' ) ),
		array( 'key' => 'about', 'label' => __( 'About', 'nsml' ), 'url' => home_url( '/about/' ) ),
		array( 'key' => 'services', 'label' => __( 'Services', 'nsml' ), 'url' => home_url( '/services/' ) ),
		array( 'key' => 'properties', 'label' => __( 'Properties', 'nsml' ), 'url' => get_post_type_archive_link( NSML_PROPERTY_CPT ) ?: home_url( '/properties/' ) ),
		array( 'key' => 'news', 'label' => __( 'News', 'nsml' ), 'url' => home_url( '/news/' ) ),
		array( 'key' => 'contact', 'label' => __( 'Contact', 'nsml' ), 'url' => home_url( '/contact/' ) ),
	);

	$current = 'home';
	if ( is_page( 'about' ) ) {
		$current = 'about';
	} elseif ( is_page( 'services' ) ) {
		$current = 'services';
	} elseif ( is_page( 'contact' ) ) {
		$current = 'contact';
	} elseif ( is_post_type_archive( NSML_PROPERTY_CPT ) || is_singular( NSML_PROPERTY_CPT ) ) {
		$current = 'properties';
	} elseif ( is_home() || is_singular( 'post' ) || is_page( 'news' ) ) {
		$current = 'news';
	} elseif ( is_front_page() ) {
		$current = 'home';
	}

	$links = array_map(
		function ( $item ) {
			return array(
				'href' => $item['url'],
				'label' => $item['label'],
				'key'  => $item['key'],
			);
		},
		$nav_items
	);

	return array(
		'home'    => home_url( '/' ),
		'logo'    => nsml_theme_setting( 'logo' ),
		'current' => $current,
		'links'   => $links,
		'contact' => home_url( '/contact/' ),
	);
}
