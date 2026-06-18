<?php
/**
 * Newsletter signups (the "Never Miss an NSML Story" form on every article).
 *
 * Stored locally in a dedicated `{prefix}nsml_subscribers` table rather than
 * wp_options (which isn't meant for an open-ended, ever-growing list) or a
 * third-party email service (no account/API key assumed). Manage the list
 * under Appearance > Subscribers: search, export to CSV, and remove
 * individual addresses. This theme does not send campaigns itself — export
 * the CSV into whatever email tool you use when you're ready to mail the
 * list (see README.md's "Plugins" section).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NSML_NEWSLETTER_NONCE_ACTION', 'nsml_newsletter_signup' );
define( 'NSML_NEWSLETTER_DB_VERSION', '1.0' );
define( 'NSML_NEWSLETTER_THROTTLE_PREFIX', 'nsml_newsletter_throttle_' );

function nsml_subscribers_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'nsml_subscribers';
}

function nsml_create_subscribers_table() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name      = nsml_subscribers_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	dbDelta(
		"CREATE TABLE {$table_name} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			email VARCHAR(190) NOT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY email (email)
		) {$charset_collate};"
	);

	update_option( 'nsml_newsletter_db_version', NSML_NEWSLETTER_DB_VERSION );
}
add_action( 'after_switch_theme', 'nsml_create_subscribers_table' );

/**
 * Self-heals if the theme is deployed by overwriting files on an already-
 * active install (e.g. over FTP/cPanel File Manager), where
 * after_switch_theme never fires.
 */
function nsml_maybe_create_subscribers_table() {
	if ( get_option( 'nsml_newsletter_db_version' ) !== NSML_NEWSLETTER_DB_VERSION ) {
		nsml_create_subscribers_table();
	}
}
add_action( 'admin_init', 'nsml_maybe_create_subscribers_table' );

/**
 * Pure validation, separate from the AJAX handler so it's unit-testable
 * without booting WordPress.
 */
function nsml_newsletter_email_is_valid( $email ) {
	return is_email( $email ) !== false;
}

add_action( 'wp_ajax_nsml_subscribe_newsletter', 'nsml_handle_newsletter_signup' );
add_action( 'wp_ajax_nopriv_nsml_subscribe_newsletter', 'nsml_handle_newsletter_signup' );

function nsml_handle_newsletter_signup() {
	if ( ! isset( $_POST['nsml_newsletter_nonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_POST['nsml_newsletter_nonce'] ), NSML_NEWSLETTER_NONCE_ACTION ) ) {
		wp_send_json_error( array( 'message' => __( 'Your session expired — please reload the page and try again.', 'nsml' ) ), 403 );
	}

	// Honeypot: a real visitor never fills this hidden field in.
	if ( ! empty( $_POST['nsml_nl_hp'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'nsml' ) ), 400 );
	}

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( $ip ) {
		$throttle_key = NSML_NEWSLETTER_THROTTLE_PREFIX . md5( $ip );
		if ( get_transient( $throttle_key ) >= 5 ) {
			wp_send_json_error( array( 'message' => __( 'Too many attempts — please try again in a few minutes.', 'nsml' ) ), 429 );
		}
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! nsml_newsletter_email_is_valid( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'nsml' ) ), 400 );
	}

	global $wpdb;
	$table = nsml_subscribers_table_name();

	// INSERT IGNORE via the unique key: re-subscribing an existing address
	// is treated as success (no error, no duplicate row), so the response
	// never reveals whether an address was already on the list.
	$wpdb->query(
		$wpdb->prepare(
			"INSERT IGNORE INTO {$table} (email, created_at) VALUES (%s, %s)",
			$email,
			current_time( 'mysql' )
		)
	);

	if ( $ip ) {
		$throttle_key = NSML_NEWSLETTER_THROTTLE_PREFIX . md5( $ip );
		set_transient( $throttle_key, (int) get_transient( $throttle_key ) + 1, 10 * MINUTE_IN_SECONDS );
	}

	wp_send_json_success( array( 'message' => __( "You're subscribed — thanks!", 'nsml' ) ) );
}

add_action( 'admin_menu', 'nsml_subscribers_menu' );
function nsml_subscribers_menu() {
	add_theme_page(
		__( 'NSML Subscribers', 'nsml' ),
		__( 'Subscribers', 'nsml' ),
		'manage_options',
		'nsml-subscribers',
		'nsml_subscribers_page'
	);
}

define( 'NSML_SUBSCRIBERS_PER_PAGE', 50 );

function nsml_subscribers_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $wpdb;
	$table = nsml_subscribers_table_name();

	if ( isset( $_GET['nsml_delete_id'] ) && isset( $_GET['_wpnonce'] )
		&& wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'nsml_delete_subscriber' ) ) {
		$wpdb->delete( $table, array( 'id' => absint( $_GET['nsml_delete_id'] ) ), array( '%d' ) );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Subscriber removed.', 'nsml' ) . '</p></div>';
	}

	$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$paged  = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
	$offset = ( $paged - 1 ) * NSML_SUBSCRIBERS_PER_PAGE;

	if ( $search ) {
		$like  = '%' . $wpdb->esc_like( $search ) . '%';
		$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE email LIKE %s", $like ) );
		$rows  = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE email LIKE %s ORDER BY created_at DESC LIMIT %d OFFSET %d",
				$like,
				NSML_SUBSCRIBERS_PER_PAGE,
				$offset
			)
		);
	} else {
		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		$rows  = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
				NSML_SUBSCRIBERS_PER_PAGE,
				$offset
			)
		);
	}

	$total_pages = (int) ceil( $total / NSML_SUBSCRIBERS_PER_PAGE );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'NSML Subscribers', 'nsml' ); ?></h1>
		<p><?php esc_html_e( 'Everyone who signed up via the newsletter form on an article page. Export to CSV and import into your email tool of choice to send a campaign — this theme does not send newsletters itself.', 'nsml' ); ?></p>

		<p>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=nsml_export_subscribers' ), 'nsml_export_subscribers' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Export CSV', 'nsml' ); ?></a>
			<strong style="margin-left:1rem;"><?php printf( esc_html( _n( '%d subscriber total', '%d subscribers total', $total, 'nsml' ) ), $total ); ?></strong>
		</p>

		<form method="get">
			<input type="hidden" name="page" value="nsml-subscribers">
			<p class="search-box">
				<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search by email…', 'nsml' ); ?>">
				<?php submit_button( __( 'Search', 'nsml' ), '', '', false ); ?>
			</p>
		</form>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Email', 'nsml' ); ?></th>
					<th><?php esc_html_e( 'Subscribed', 'nsml' ); ?></th>
					<th><?php esc_html_e( 'Action', 'nsml' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="3"><?php esc_html_e( 'No subscribers yet.', 'nsml' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( $row->email ); ?></td>
							<td><?php echo esc_html( mysql2date( 'F j, Y', $row->created_at ) ); ?></td>
							<td>
								<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'nsml_delete_id', $row->id ), 'nsml_delete_subscriber' ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Remove this subscriber?', 'nsml' ) ); ?>');"><?php esc_html_e( 'Remove', 'nsml' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<?php if ( $total_pages > 1 ) : ?>
			<div class="tablenav">
				<div class="tablenav-pages">
					<?php
					echo paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'format'    => '',
							'current'   => $paged,
							'total'     => $total_pages,
							'add_args'  => $search ? array( 's' => $search ) : array(),
						)
					);
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

add_action( 'admin_post_nsml_export_subscribers', 'nsml_export_subscribers_csv' );
function nsml_export_subscribers_csv() {
	if ( ! current_user_can( 'manage_options' )
		|| ! isset( $_GET['_wpnonce'] )
		|| ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'nsml_export_subscribers' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'nsml' ) );
	}

	global $wpdb;
	$table = nsml_subscribers_table_name();
	$rows  = $wpdb->get_results( "SELECT email, created_at FROM {$table} ORDER BY created_at ASC" );

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=nsml-subscribers-' . gmdate( 'Y-m-d' ) . '.csv' );

	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'email', 'subscribed_at' ) );
	foreach ( $rows as $row ) {
		fputcsv( $out, array( $row->email, $row->created_at ) );
	}
	fclose( $out );
	exit;
}
