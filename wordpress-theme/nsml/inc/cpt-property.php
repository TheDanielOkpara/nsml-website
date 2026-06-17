<?php
/**
 * "Property" custom post type — Lagos Marathon, Abuja Half Marathon, etc.
 *
 * Stores the per-property fields the static prop-hero / prop-sidebar /
 * prop-gallery markup needs, so a single template can render any property
 * regardless of how many gallery photos or stats it has.
 *
 * Security:
 *  - register_post_meta() declares a sanitize_callback for every field, so
 *    raw input can never reach the database unsanitized, whether saved via
 *    the classic meta box below or the REST API / block editor.
 *  - Every field also declares an auth_callback restricting writes to users
 *    who can edit the specific post (capability check, not just "logged in").
 *  - The meta box save handler additionally verifies a nonce and ignores
 *    autosave/revision requests, per the WordPress meta box security guide.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NSML_PROPERTY_CPT', 'nsml_property' );

function nsml_register_property_cpt() {
	register_post_type(
		NSML_PROPERTY_CPT,
		array(
			'labels'             => array(
				'name'          => __( 'Properties', 'nsml' ),
				'singular_name' => __( 'Property', 'nsml' ),
				'add_new_item'  => __( 'Add New Property', 'nsml' ),
				'edit_item'     => __( 'Edit Property', 'nsml' ),
				'all_items'     => __( 'All Properties', 'nsml' ),
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'has_archive'         => 'properties',
			'rewrite'             => array( 'slug' => 'properties' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-flag',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'exclude_from_search' => false,
		)
	);
}
add_action( 'init', 'nsml_register_property_cpt' );

/**
 * Whether the current user may edit meta on a given post — shared by both
 * the REST/meta API auth_callback and the classic meta-box save handler.
 */
function nsml_property_meta_auth_callback( $allowed, $meta_key, $post_id ) {
	return current_user_can( 'edit_post', $post_id );
}

function nsml_sanitize_url_field( $value ) {
	return sanitize_url( (string) $value );
}

function nsml_sanitize_organizer_type( $value ) {
	$value = sanitize_key( (string) $value );
	return in_array( $value, array( 'owned', 'consultant' ), true ) ? $value : 'owned';
}

/**
 * Stats and gallery are stored as JSON arrays. We re-validate the decoded
 * structure (not just "is this a string") before re-encoding, so malformed
 * or oversized payloads can't be persisted.
 */
function nsml_sanitize_stats_json( $value ) {
	$decoded = json_decode( (string) $value, true );
	if ( ! is_array( $decoded ) ) {
		return wp_json_encode( array() );
	}
	$clean = array();
	foreach ( array_slice( $decoded, 0, 12 ) as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$clean[] = array(
			'value' => sanitize_text_field( $row['value'] ?? '' ),
			'label' => sanitize_text_field( $row['label'] ?? '' ),
		);
	}
	return wp_json_encode( $clean );
}

function nsml_sanitize_gallery_json( $value ) {
	$decoded = json_decode( (string) $value, true );
	if ( ! is_array( $decoded ) ) {
		return wp_json_encode( array() );
	}
	$clean = array();
	foreach ( array_slice( $decoded, 0, 24 ) as $row ) {
		if ( ! is_array( $row ) || empty( $row['id'] ) ) {
			continue;
		}
		$clean[] = array(
			'id'   => absint( $row['id'] ),
			'wide' => ! empty( $row['wide'] ),
		);
	}
	return wp_json_encode( $clean );
}

function nsml_register_property_meta() {
	$fields = array(
		'nsml_location'         => array( 'sanitize_text_field' ),
		'nsml_hero_tag'         => array( 'sanitize_text_field' ),
		'nsml_official_website' => array( 'nsml_sanitize_url_field' ),
		'nsml_organizer_type'   => array( 'nsml_sanitize_organizer_type' ),
		'nsml_next_edition'     => array( 'sanitize_text_field' ),
		'nsml_about'            => array( 'wp_kses_post' ),
		'nsml_stats_json'       => array( 'nsml_sanitize_stats_json' ),
		'nsml_gallery_json'     => array( 'nsml_sanitize_gallery_json' ),
		'nsml_sponsor_image_id' => array( 'absint' ),
		'nsml_event_logo_id'    => array( 'absint' ),
	);

	foreach ( $fields as $key => $cb ) {
		register_post_meta(
			NSML_PROPERTY_CPT,
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => $cb[0],
				'auth_callback'     => 'nsml_property_meta_auth_callback',
			)
		);
	}
}
add_action( 'init', 'nsml_register_property_meta' );

function nsml_property_meta_box() {
	add_meta_box(
		'nsml_property_details',
		__( 'Property Details', 'nsml' ),
		'nsml_render_property_meta_box',
		NSML_PROPERTY_CPT,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'nsml_property_meta_box' );

function nsml_render_property_meta_box( $post ) {
	wp_nonce_field( 'nsml_save_property_meta', 'nsml_property_meta_nonce' );

	$location = get_post_meta( $post->ID, 'nsml_location', true );
	$hero_tag = get_post_meta( $post->ID, 'nsml_hero_tag', true );
	$website  = get_post_meta( $post->ID, 'nsml_official_website', true );
	$org_type = get_post_meta( $post->ID, 'nsml_organizer_type', true ) ?: 'owned';
	$next_ed  = get_post_meta( $post->ID, 'nsml_next_edition', true );
	$stats    = get_post_meta( $post->ID, 'nsml_stats_json', true ) ?: '[]';
	$gallery  = get_post_meta( $post->ID, 'nsml_gallery_json', true ) ?: '[]';
	?>
	<p>
		<label for="nsml_location"><strong><?php esc_html_e( 'Location', 'nsml' ); ?></strong></label><br>
		<input type="text" id="nsml_location" name="nsml_location" class="widefat" value="<?php echo esc_attr( $location ); ?>" placeholder="Lagos, Nigeria">
	</p>
	<p>
		<label for="nsml_hero_tag"><strong><?php esc_html_e( 'Hero badge text', 'nsml' ); ?></strong></label><br>
		<input type="text" id="nsml_hero_tag" name="nsml_hero_tag" class="widefat" value="<?php echo esc_attr( $hero_tag ); ?>" placeholder="Flagship Property">
	</p>
	<p>
		<label for="nsml_official_website"><strong><?php esc_html_e( 'Official website URL', 'nsml' ); ?></strong></label><br>
		<input type="url" id="nsml_official_website" name="nsml_official_website" class="widefat" value="<?php echo esc_attr( $website ); ?>" placeholder="https://">
	</p>
	<p>
		<label for="nsml_organizer_type"><strong><?php esc_html_e( 'Organizer card label', 'nsml' ); ?></strong></label><br>
		<select id="nsml_organizer_type" name="nsml_organizer_type">
			<option value="owned" <?php selected( $org_type, 'owned' ); ?>><?php esc_html_e( 'Owned and Organized By', 'nsml' ); ?></option>
			<option value="consultant" <?php selected( $org_type, 'consultant' ); ?>><?php esc_html_e( 'Consultant & Organized By', 'nsml' ); ?></option>
		</select>
	</p>
	<p>
		<label for="nsml_next_edition"><strong><?php esc_html_e( 'Next edition', 'nsml' ); ?></strong></label><br>
		<input type="text" id="nsml_next_edition" name="nsml_next_edition" class="widefat" value="<?php echo esc_attr( $next_ed ); ?>" placeholder="6 February 2027">
	</p>
	<p>
		<label for="nsml_stats_json"><strong><?php esc_html_e( 'Stats band (JSON array of {value,label})', 'nsml' ); ?></strong></label><br>
		<textarea id="nsml_stats_json" name="nsml_stats_json" class="widefat" rows="3"><?php echo esc_textarea( $stats ); ?></textarea>
	</p>
	<p>
		<label for="nsml_gallery_json"><strong><?php esc_html_e( 'Gallery (JSON array of {id, wide})', 'nsml' ); ?></strong></label><br>
		<textarea id="nsml_gallery_json" name="nsml_gallery_json" class="widefat" rows="3"><?php echo esc_textarea( $gallery ); ?></textarea>
		<span class="description"><?php esc_html_e( 'Image IDs come from the Media Library. A future iteration can replace this with a visual picker; the field itself is already REST-exposed.', 'nsml' ); ?></span>
	</p>
	<?php
}

function nsml_save_property_meta( $post_id ) {
	if ( ! isset( $_POST['nsml_property_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nsml_property_meta_nonce'] ) ), 'nsml_save_property_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( NSML_PROPERTY_CPT !== get_post_type( $post_id ) ) {
		return;
	}

	$map = array(
		'nsml_location'         => 'sanitize_text_field',
		'nsml_hero_tag'         => 'sanitize_text_field',
		'nsml_official_website' => 'nsml_sanitize_url_field',
		'nsml_organizer_type'   => 'nsml_sanitize_organizer_type',
		'nsml_next_edition'     => 'sanitize_text_field',
		'nsml_stats_json'       => 'nsml_sanitize_stats_json',
		'nsml_gallery_json'     => 'nsml_sanitize_gallery_json',
	);

	foreach ( $map as $field => $sanitizer ) {
		if ( ! isset( $_POST[ $field ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $field ] );
		update_post_meta( $post_id, $field, call_user_func( $sanitizer, $raw ) );
	}
}
add_action( 'save_post', 'nsml_save_property_meta' );

/**
 * One-time WXR-import follow-up: resolve URL-based meta into real
 * attachment IDs.
 *
 * The WXR demo-content file can't know a gallery image's eventual
 * WordPress attachment post ID at export time (those IDs are assigned by
 * the importer when it creates the attachment posts), so
 * tools/generate_wxr.py instead emits plain-URL postmeta:
 *   - nsml_gallery_urls_json   (JSON array of {url, wide})
 *   - nsml_sponsor_image_url
 *   - nsml_event_logo_url
 *
 * After the native WordPress Importer finishes, every attachment URL it
 * imported is resolvable via attachment_url_to_postid(). The next time
 * wp-admin loads, this hook converts the URL-based meta on every
 * nsml_property post into the real ID-based meta the rest of the theme
 * expects (nsml_gallery_json, nsml_sponsor_image_id, nsml_event_logo_id),
 * then deletes the now-unneeded *_url(s) meta so the conversion only runs
 * once per post.
 */
function nsml_resolve_gallery_urls_to_ids() {
	$query = new WP_Query(
		array(
			'post_type'              => NSML_PROPERTY_CPT,
			'post_status'             => 'any',
			'posts_per_page'          => -1,
			'fields'                  => 'ids',
			'no_found_rows'           => true,
			'update_post_meta_cache'  => false,
			'update_post_term_cache'  => false,
			'meta_query'              => array(
				'relation' => 'OR',
				array( 'key' => 'nsml_gallery_urls_json' ),
				array( 'key' => 'nsml_sponsor_image_url' ),
				array( 'key' => 'nsml_event_logo_url' ),
			),
		)
	);

	foreach ( $query->posts as $post_id ) {
		// Gallery.
		$urls_json = get_post_meta( $post_id, 'nsml_gallery_urls_json', true );
		if ( '' !== $urls_json && ! get_post_meta( $post_id, 'nsml_gallery_json', true ) ) {
			$decoded = json_decode( (string) $urls_json, true );
			if ( is_array( $decoded ) ) {
				$resolved = array();
				foreach ( $decoded as $row ) {
					if ( ! is_array( $row ) || empty( $row['url'] ) ) {
						continue;
					}
					$attachment_id = attachment_url_to_postid( $row['url'] );
					if ( $attachment_id ) {
						$resolved[] = array(
							'id'   => $attachment_id,
							'wide' => ! empty( $row['wide'] ),
						);
					}
				}
				update_post_meta( $post_id, 'nsml_gallery_json', nsml_sanitize_gallery_json( wp_json_encode( $resolved ) ) );
			}
		}
		if ( '' !== $urls_json ) {
			delete_post_meta( $post_id, 'nsml_gallery_urls_json' );
		}

		// Sponsor image.
		$sponsor_url = get_post_meta( $post_id, 'nsml_sponsor_image_url', true );
		if ( '' !== $sponsor_url ) {
			$attachment_id = attachment_url_to_postid( $sponsor_url );
			if ( $attachment_id ) {
				update_post_meta( $post_id, 'nsml_sponsor_image_id', absint( $attachment_id ) );
			}
			delete_post_meta( $post_id, 'nsml_sponsor_image_url' );
		}

		// Event logo.
		$logo_url = get_post_meta( $post_id, 'nsml_event_logo_url', true );
		if ( '' !== $logo_url ) {
			$attachment_id = attachment_url_to_postid( $logo_url );
			if ( $attachment_id ) {
				update_post_meta( $post_id, 'nsml_event_logo_id', absint( $attachment_id ) );
			}
			delete_post_meta( $post_id, 'nsml_event_logo_url' );
		}
	}
}
add_action( 'admin_init', 'nsml_resolve_gallery_urls_to_ids' );
