<?php
/**
 * "Team Member" custom post type — About page leadership grid.
 *
 * Lets the team section on page-about.php be managed from wp-admin the
 * same way Properties are, instead of living as hardcoded markup. Order
 * in the grid comes from the post's menu_order ("Order" field under Page
 * Attributes), and one member can be flagged "Show as Lead" to render in
 * the wide CEO-style card at the top instead of the grid.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NSML_TEAM_CPT', 'nsml_team' );

function nsml_register_team_cpt() {
	register_post_type(
		NSML_TEAM_CPT,
		array(
			'labels'             => array(
				'name'          => __( 'Team', 'nsml' ),
				'singular_name' => __( 'Team Member', 'nsml' ),
				'add_new_item'  => __( 'Add New Team Member', 'nsml' ),
				'edit_item'     => __( 'Edit Team Member', 'nsml' ),
				'all_items'     => __( 'Team', 'nsml' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-groups',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions' ),
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'nsml_register_team_cpt' );

function nsml_team_meta_auth_callback( $allowed, $meta_key, $post_id ) {
	return current_user_can( 'edit_post', $post_id );
}

function nsml_register_team_meta() {
	register_post_meta(
		NSML_TEAM_CPT,
		'nsml_team_role',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => 'nsml_team_meta_auth_callback',
		)
	);
	register_post_meta(
		NSML_TEAM_CPT,
		'nsml_team_is_lead',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => 'nsml_team_meta_auth_callback',
		)
	);
}
add_action( 'init', 'nsml_register_team_meta' );

function nsml_team_meta_box() {
	add_meta_box(
		'nsml_team_details',
		__( 'Team Member Details', 'nsml' ),
		'nsml_render_team_meta_box',
		NSML_TEAM_CPT,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'nsml_team_meta_box' );

function nsml_render_team_meta_box( $post ) {
	wp_nonce_field( 'nsml_save_team_meta', 'nsml_team_meta_nonce' );

	$role    = get_post_meta( $post->ID, 'nsml_team_role', true );
	$is_lead = (bool) get_post_meta( $post->ID, 'nsml_team_is_lead', true );
	?>
	<p>
		<label for="nsml_team_role"><strong><?php esc_html_e( 'Role / Title', 'nsml' ); ?></strong></label><br>
		<input type="text" id="nsml_team_role" name="nsml_team_role" class="widefat" value="<?php echo esc_attr( $role ); ?>" placeholder="Chief Operating Officer">
	</p>
	<p>
		<label>
			<input type="checkbox" id="nsml_team_is_lead" name="nsml_team_is_lead" value="1" <?php checked( $is_lead ); ?>>
			<strong><?php esc_html_e( 'Show as Lead (wide CEO-style card at the top of the team section)', 'nsml' ); ?></strong>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Name = post title. Photo = featured image. Bio = the main content editor below. Use the "Order" field under Page Attributes to control the position in the grid.', 'nsml' ); ?>
	</p>
	<?php
}

function nsml_save_team_meta( $post_id ) {
	if ( ! isset( $_POST['nsml_team_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nsml_team_meta_nonce'] ) ), 'nsml_save_team_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( NSML_TEAM_CPT !== get_post_type( $post_id ) ) {
		return;
	}

	if ( isset( $_POST['nsml_team_role'] ) ) {
		update_post_meta( $post_id, 'nsml_team_role', sanitize_text_field( wp_unslash( $_POST['nsml_team_role'] ) ) );
	}
	update_post_meta( $post_id, 'nsml_team_is_lead', isset( $_POST['nsml_team_is_lead'] ) ? 1 : 0 );
}
add_action( 'save_post', 'nsml_save_team_meta' );
