<?php
/**
 * NSML Theme Settings: lets an admin edit the site details that used to be
 * hardcoded in header.php / footer.php (logo, tagline, social links,
 * contact info, footer copyright/credit text) from wp-admin, without
 * touching code. Every getter falls back to the original static-site value
 * when no option has been saved yet, so the site looks identical before
 * and after this feature is added.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NSML_SETTINGS_OPTION', 'nsml_theme_settings' );

/**
 * Default values, taken verbatim from the original hardcoded markup.
 */
function nsml_theme_settings_defaults() {
	return array(
		'logo'             => NSML_THEME_URI . '/assets/images/logo.png',
		'tagline'          => "Africa's leading sports marketing, brand management and procurement agency. World Athletics Certified. Home of the Access Bank Lagos City Marathon.",
		'contact_email'    => 'info@nilayosports.com',
		'contact_address'  => '1, Emmanuel Keshi Street, Magodo Phase 2 GRA, Lagos, Nigeria',
		'social_instagram' => 'https://www.instagram.com/nilayosports',
		'social_facebook'  => 'https://www.facebook.com/share/1DSXqskp56/',
		'social_twitter'   => 'https://x.com/nilayosports',
		'social_linkedin'  => 'https://www.linkedin.com/company/nilayosports/',
		'social_youtube'   => 'https://youtube.com/@nilayosports',
		'footer_copyright' => 'Nilayo Sports Management Ltd. All rights reserved.',
		'footer_credit'    => 'Built by Design Things Studio',
		'footer_credit_url' => 'http://designthngs.com/',
		'cert_pill'        => 'World Athletics Certified 2025',
		'news_pagination_style' => 'load_more',
	);
}

/**
 * Get one setting, falling back to its default.
 */
function nsml_theme_setting( $key ) {
	$defaults = nsml_theme_settings_defaults();
	$saved    = get_option( NSML_SETTINGS_OPTION, array() );
	if ( isset( $saved[ $key ] ) && '' !== $saved[ $key ] ) {
		return $saved[ $key ];
	}
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Sanitize callback for the whole settings array.
 */
function nsml_theme_settings_sanitize( $input ) {
	$clean = array();
	if ( ! is_array( $input ) ) {
		return $clean;
	}

	$text_fields = array( 'tagline', 'contact_address', 'footer_copyright', 'footer_credit', 'cert_pill' );
	foreach ( $text_fields as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$clean[ $key ] = sanitize_text_field( $input[ $key ] );
		}
	}

	if ( isset( $input['contact_email'] ) ) {
		$clean['contact_email'] = sanitize_email( $input['contact_email'] );
	}

	if ( isset( $input['news_pagination_style'] ) && in_array( $input['news_pagination_style'], array( 'load_more', 'numbered' ), true ) ) {
		$clean['news_pagination_style'] = $input['news_pagination_style'];
	}

	$url_fields = array( 'logo', 'social_instagram', 'social_facebook', 'social_twitter', 'social_linkedin', 'social_youtube', 'footer_credit_url' );
	foreach ( $url_fields as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$clean[ $key ] = sanitize_url( $input[ $key ] );
		}
	}

	return $clean;
}

add_action( 'admin_init', 'nsml_theme_settings_register' );
function nsml_theme_settings_register() {
	register_setting(
		'nsml_theme_settings_group',
		NSML_SETTINGS_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'nsml_theme_settings_sanitize',
			'default'           => array(),
		)
	);
}

add_action( 'admin_menu', 'nsml_theme_settings_menu' );
function nsml_theme_settings_menu() {
	add_theme_page(
		__( 'NSML Theme Settings', 'nsml' ),
		__( 'Theme Settings', 'nsml' ),
		'manage_options',
		'nsml-theme-settings',
		'nsml_theme_settings_page'
	);
}

function nsml_theme_settings_field( $key, $label, $type = 'text', $description = '' ) {
	$value = nsml_theme_setting( $key );
	?>
	<tr>
		<th scope="row"><label for="nsml-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<?php if ( 'textarea' === $type ) : ?>
				<textarea id="nsml-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( NSML_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $key ); ?>]" rows="3" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
			<?php else : ?>
				<input type="<?php echo esc_attr( $type ); ?>" id="nsml-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( NSML_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="regular-text">
			<?php endif; ?>
			<?php if ( $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

function nsml_theme_settings_select_field( $key, $label, $options, $description = '' ) {
	$value = nsml_theme_setting( $key );
	?>
	<tr>
		<th scope="row"><label for="nsml-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<select id="nsml-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( NSML_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $key ); ?>]">
				<?php foreach ( $options as $option_value => $option_label ) : ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php if ( $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

function nsml_theme_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'NSML Theme Settings', 'nsml' ); ?></h1>
		<p><?php esc_html_e( 'Edit the site logo, tagline, contact details, social links and footer text that appear in the header and footer on every page. Leave a field blank to use the original default.', 'nsml' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'nsml_theme_settings_group' ); ?>
			<h2 class="title"><?php esc_html_e( 'Branding', 'nsml' ); ?></h2>
			<table class="form-table">
				<?php
				nsml_theme_settings_field( 'logo', __( 'Logo URL', 'nsml' ), 'url', __( 'Full URL to the logo image used in the nav and footer.', 'nsml' ) );
				nsml_theme_settings_field( 'tagline', __( 'Footer tagline', 'nsml' ), 'textarea' );
				nsml_theme_settings_field( 'cert_pill', __( 'Certification pill text', 'nsml' ) );
				?>
			</table>

			<h2 class="title"><?php esc_html_e( 'Contact', 'nsml' ); ?></h2>
			<table class="form-table">
				<?php
				nsml_theme_settings_field( 'contact_email', __( 'Contact email', 'nsml' ), 'email' );
				nsml_theme_settings_field( 'contact_address', __( 'Address', 'nsml' ), 'textarea' );
				?>
			</table>

			<h2 class="title"><?php esc_html_e( 'Social links', 'nsml' ); ?></h2>
			<table class="form-table">
				<?php
				nsml_theme_settings_field( 'social_instagram', __( 'Instagram URL', 'nsml' ), 'url' );
				nsml_theme_settings_field( 'social_facebook', __( 'Facebook URL', 'nsml' ), 'url' );
				nsml_theme_settings_field( 'social_twitter', __( 'X / Twitter URL', 'nsml' ), 'url' );
				nsml_theme_settings_field( 'social_linkedin', __( 'LinkedIn URL', 'nsml' ), 'url' );
				nsml_theme_settings_field( 'social_youtube', __( 'YouTube URL', 'nsml' ), 'url' );
				?>
			</table>

			<h2 class="title"><?php esc_html_e( 'News page', 'nsml' ); ?></h2>
			<table class="form-table">
				<?php
				nsml_theme_settings_select_field(
					'news_pagination_style',
					__( 'Article browsing style', 'nsml' ),
					array(
						'load_more' => __( 'Load More button', 'nsml' ),
						'numbered'  => __( 'Numbered pages (with Next / Previous)', 'nsml' ),
					),
					__( 'How visitors browse past the first page of articles on the News page.', 'nsml' )
				);
				?>
			</table>

			<h2 class="title"><?php esc_html_e( 'Footer text', 'nsml' ); ?></h2>
			<table class="form-table">
				<?php
				nsml_theme_settings_field( 'footer_copyright', __( 'Copyright line (after the year)', 'nsml' ) );
				nsml_theme_settings_field( 'footer_credit', __( 'Credit text', 'nsml' ) );
				nsml_theme_settings_field( 'footer_credit_url', __( 'Credit link URL', 'nsml' ), 'url' );
				?>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * The News page (home.php) reads this setting to decide how to fetch and
 * paginate posts: a real, smaller-paged main query for "Numbered pages",
 * or one large batch (rendered with the later posts pre-loaded but hidden
 * behind "Load More") to match the original static site's fixed reveal.
 * Setting it here, on the main query itself, is what makes WordPress's
 * own /page/2/ URLs and the_posts_pagination() work correctly in
 * "Numbered pages" mode.
 */
define( 'NSML_NEWS_PER_PAGE_NUMBERED', 6 );
define( 'NSML_NEWS_PER_PAGE_LOAD_MORE', 60 );

add_action( 'pre_get_posts', 'nsml_news_index_query' );
function nsml_news_index_query( $query ) {
	if ( is_admin() || ! $query->is_home() || ! $query->is_main_query() ) {
		return;
	}

	$per_page = ( 'numbered' === nsml_theme_setting( 'news_pagination_style' ) )
		? NSML_NEWS_PER_PAGE_NUMBERED
		: NSML_NEWS_PER_PAGE_LOAD_MORE;

	$query->set( 'posts_per_page', $per_page );
}
