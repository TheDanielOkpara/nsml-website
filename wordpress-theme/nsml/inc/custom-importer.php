<?php
/**
 * NSML Custom Importer.
 *
 * The native WordPress Importer (Tools > Import > WordPress) fetches every
 * image referenced in the WXR file over HTTP from the live domain at
 * import time. On real hosting that fetch can be blocked (e.g. by the
 * source host's anti-scraping rules), which silently leaves every post and
 * property without its images. This importer avoids that failure mode
 * entirely: every image is bundled as a local file in
 * demo-content/images/ and copied straight into the Media Library with
 * wp_insert_attachment(), with no network request involved. It also
 * creates the About / Services / Contact pages, which are not part of the
 * WXR file at all, so "import" produces a complete site in one click.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NSML_IMPORT_NONCE_ACTION', 'nsml_run_import' );

add_action( 'admin_menu', 'nsml_importer_menu' );
function nsml_importer_menu() {
	add_theme_page(
		__( 'NSML Import Content', 'nsml' ),
		__( 'Import Content', 'nsml' ),
		'manage_options',
		'nsml-import-content',
		'nsml_importer_page'
	);
}

/**
 * The manifest ships inside the theme package itself, at
 * nsml/demo-content/, so the theme is a single self-contained zip: no
 * separate upload step needed for the demo content or its images.
 */
function nsml_importer_locate_demo_content() {
	$candidates = array(
		NSML_THEME_DIR . '/demo-content',
		WP_CONTENT_DIR . '/nsml-demo-content',
	);
	foreach ( $candidates as $dir ) {
		if ( file_exists( $dir . '/manifest.php' ) ) {
			return $dir;
		}
	}
	return null;
}

function nsml_importer_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$demo_dir = nsml_importer_locate_demo_content();
	$result   = null;

	if ( isset( $_POST['nsml_import_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nsml_import_nonce'] ), NSML_IMPORT_NONCE_ACTION ) ) {
		$result = nsml_run_custom_import( $demo_dir );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'NSML Import Content', 'nsml' ); ?></h1>
		<p><?php esc_html_e( 'Imports the real blog articles, properties, and the About/Services/Contact pages bundled with this theme, including their images. Images are copied directly from the theme package, so this works even if the importer cannot reach the internet -- unlike Tools > Import.', 'nsml' ); ?></p>

		<?php if ( null === $demo_dir ) : ?>
			<div class="notice notice-error"><p><?php esc_html_e( 'demo-content/manifest.php was not found inside this theme. Make sure the demo-content folder was uploaded along with the rest of the nsml theme files.', 'nsml' ); ?></p></div>
		<?php endif; ?>

		<?php if ( $result ) : ?>
			<div class="notice notice-<?php echo esc_attr( $result['errors'] ? 'warning' : 'success' ); ?>">
				<p><strong><?php esc_html_e( 'Import finished.', 'nsml' ); ?></strong></p>
				<ul style="list-style:disc;margin-left:1.5em;">
					<li><?php printf( esc_html__( 'Pages: %1$d created, %2$d already existed', 'nsml' ), $result['pages_created'], $result['pages_skipped'] ); ?></li>
					<li><?php printf( esc_html__( 'Posts: %1$d created, %2$d already existed', 'nsml' ), $result['posts_created'], $result['posts_skipped'] ); ?></li>
					<li><?php printf( esc_html__( 'Properties: %1$d created, %2$d already existed', 'nsml' ), $result['properties_created'], $result['properties_skipped'] ); ?></li>
					<li><?php printf( esc_html__( 'Team members: %1$d created, %2$d already existed', 'nsml' ), $result['team_created'], $result['team_skipped'] ); ?></li>
					<li><?php printf( esc_html__( 'Images imported into Media Library: %d', 'nsml' ), $result['images_imported'] ); ?></li>
				</ul>
				<?php if ( $result['errors'] ) : ?>
					<p><strong><?php esc_html_e( 'Some items had problems:', 'nsml' ); ?></strong></p>
					<ul style="list-style:disc;margin-left:1.5em;">
						<?php foreach ( $result['errors'] as $err ) : ?>
							<li><?php echo esc_html( $err ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( NSML_IMPORT_NONCE_ACTION, 'nsml_import_nonce' ); ?>
			<?php submit_button( __( 'Import demo content now', 'nsml' ), 'primary', 'submit', true, ( null === $demo_dir ) ? array( 'disabled' => 'disabled' ) : array() ); ?>
		</form>
		<p class="description"><?php esc_html_e( 'Safe to run more than once -- existing pages/posts/properties (matched by slug) are not recreated, but any property/post still missing a logo, gallery, or thumbnail image will have it filled in.', 'nsml' ); ?></p>
	</div>
	<?php
}

/**
 * Side-load one local file into the Media Library. No HTTP request is
 * made: the file is read straight from disk and copied into uploads.
 * Returns the new attachment ID, or 0 on failure.
 */
function nsml_import_local_image( $abs_path, $title = '' ) {
	if ( ! file_exists( $abs_path ) ) {
		return 0;
	}

	$filename = wp_unique_filename( wp_upload_dir()['path'], basename( $abs_path ) );
	$contents = file_get_contents( $abs_path );
	if ( false === $contents ) {
		return 0;
	}

	$upload = wp_upload_bits( $filename, null, $contents );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$filetype = wp_check_filetype( $upload['file'], null );
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title ? $title : preg_replace( '/\.[^.]+$/', '', basename( $abs_path ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );

	return $attachment_id;
}

/**
 * The 6 current NSML team members, used to seed the "Team" admin screen
 * (see inc/cpt-team.php) on first import. Unlike posts/properties, this
 * isn't part of the auto-generated manifest.php -- the source photos
 * live in the theme's own assets/images/team/, not demo-content/images/,
 * because they were already shipped as static page-about.php markup
 * before the Team CPT existed.
 */
function nsml_get_team_seed_data() {
	return array(
		array(
			'name'    => 'Olopade Yetunde',
			'role'    => 'Chief Executive Officer',
			'photo'   => 'yetunde.jpg',
			'is_lead' => true,
			'bio'     => "A seasoned expert in events marketing, sponsorship activation, and brand partnerships with over 20 years of experience. Yetunde specialises in large-scale sports events, corporate collaborations, and impactful brand engagements — driving Nilayo's strategic vision and setting industry benchmarks in sports marketing across Africa.",
		),
		array(
			'name'    => 'Olopade Adenike',
			'role'    => 'Chief Operating Officer',
			'photo'   => 'adenike.jpg',
			'is_lead' => false,
			'bio'     => "The operational architect behind NSML's growth. Adenike ensures that every sponsorship commitment, event logistics plan, and client relationship is executed to the highest standard. She brings the same precision to a community 10KM race as to a World Athletics-certified marathon — because NSML's reputation is built on delivery, every single time.",
		),
		array(
			'name'    => 'Odeh Emmanuel',
			'role'    => 'Project Lead',
			'photo'   => 'emmanuel.jpg',
			'is_lead' => false,
			'bio'     => 'A results-driven professional with expertise in business development, entrepreneurship, and team leadership. With extensive experience in project execution, Emmanuel drives business growth and operational efficiency. Backed by certifications in Business Administration and Management, his strategic vision and hands-on leadership make him a key asset in project management and business development at Nilayo.',
		),
		array(
			'name'    => 'Adekite Bolaji',
			'role'    => 'Admin Lead',
			'photo'   => 'bolaji.jpg',
			'is_lead' => false,
			'bio'     => "An experienced HR professional with a BA in English Language, an MBA, and multiple professional certifications. Bolaji specialises in talent management, employee relations, and organisational development — nurturing a dynamic work environment. Committed to excellence and innovation, she drives Nilayo Sports' success by attracting and developing top talent.",
		),
		array(
			'name'    => 'Odi Jide',
			'role'    => 'Brand & Comms Lead',
			'photo'   => 'jide.jpg',
			'is_lead' => false,
			'bio'     => "A seasoned media and communications professional with expertise in broadcast journalism, brand strategy, creative advertising, and media production. With over a decade of experience, Jide blends analytical insight with creative storytelling to strengthen Nilayo's brand presence. A trained journalist from the Nigerian Institute of Journalism and an alumnus of O2 Academy Lagos, he plays a key role in shaping the company's communication and marketing strategies.",
		),
		array(
			'name'    => 'Omoniyi Sandra',
			'role'    => 'Business Strategist',
			'photo'   => 'sandra.jpg',
			'is_lead' => false,
			'bio'     => "A specialist in sports management, strategy, and operations with a Master's in Sport Management and Administration and over a decade of experience. Sandra develops strategic solutions that drive revenue and brand growth, excels in forging high-impact partnerships, and creates sustainable business models that elevate sports properties. She combines strategic insight with deep industry expertise to deliver innovative solutions for brands and stakeholders.",
		),
	);
}

/**
 * Run the full import. $demo_dir is the directory containing manifest.php
 * and the images/ subfolder.
 */
function nsml_run_custom_import( $demo_dir ) {
	$result = array(
		'pages_created'       => 0,
		'pages_skipped'       => 0,
		'posts_created'       => 0,
		'posts_skipped'       => 0,
		'properties_created'  => 0,
		'properties_skipped'  => 0,
		'team_created'        => 0,
		'team_skipped'        => 0,
		'images_imported'     => 0,
		'errors'              => array(),
	);

	if ( null === $demo_dir || ! file_exists( $demo_dir . '/manifest.php' ) ) {
		$result['errors'][] = __( 'manifest.php not found; nothing imported.', 'nsml' );
		return $result;
	}

	$manifest = include $demo_dir . '/manifest.php';
	if ( ! is_array( $manifest ) ) {
		$result['errors'][] = __( 'manifest.php did not return a valid array.', 'nsml' );
		return $result;
	}

	$images_dir   = trailingslashit( $demo_dir ) . 'images/';
	$image_cache  = array(); // relative path => attachment ID, so a file referenced by multiple items is only uploaded once.

	$import_image = function ( $rel_path, $title = '' ) use ( $images_dir, &$image_cache, &$result ) {
		if ( ! $rel_path ) {
			return 0;
		}
		if ( isset( $image_cache[ $rel_path ] ) ) {
			return $image_cache[ $rel_path ];
		}
		$id = nsml_import_local_image( $images_dir . $rel_path, $title );
		if ( $id ) {
			$result['images_imported']++;
		}
		$image_cache[ $rel_path ] = $id;
		return $id;
	};

	/**
	 * Fills in any image fields a property is currently missing. Properties
	 * created by an earlier, less-complete import run can be left without a
	 * thumbnail, sponsor logo, event logo, or gallery forever, because the
	 * main loop below completely skips existing properties by slug. Running
	 * the import again should be able to "top up" those properties instead
	 * of requiring them to be deleted and recreated.
	 */
	$backfill_property_media = function ( $post_id, $prop ) use ( $import_image, &$result ) {
		if ( ! has_post_thumbnail( $post_id ) && ! empty( $prop['hero_image'] ) ) {
			$att_id = $import_image( $prop['hero_image'], $prop['title'] );
			if ( $att_id ) {
				set_post_thumbnail( $post_id, $att_id );
			}
		}

		if ( ! get_post_meta( $post_id, 'nsml_sponsor_image_id', true ) && ! empty( $prop['sponsor_image'] ) ) {
			$att_id = $import_image( $prop['sponsor_image'], $prop['title'] . ' sponsor' );
			if ( $att_id ) {
				update_post_meta( $post_id, 'nsml_sponsor_image_id', $att_id );
			}
		}

		if ( ! get_post_meta( $post_id, 'nsml_event_logo_id', true ) && ! empty( $prop['event_logo'] ) ) {
			$att_id = $import_image( $prop['event_logo'], $prop['title'] . ' logo' );
			if ( $att_id ) {
				update_post_meta( $post_id, 'nsml_event_logo_id', $att_id );
			}
		}

		$existing_gallery = json_decode( get_post_meta( $post_id, 'nsml_gallery_json', true ), true );
		if ( empty( $existing_gallery ) && ! empty( $prop['gallery'] ) ) {
			$gallery_ids = array();
			foreach ( $prop['gallery'] as $g ) {
				$att_id = $import_image( $g['image'], $prop['title'] . ' gallery' );
				if ( $att_id ) {
					$gallery_ids[] = array( 'id' => $att_id, 'wide' => (bool) $g['wide'] );
				}
			}
			if ( $gallery_ids ) {
				update_post_meta( $post_id, 'nsml_gallery_json', wp_json_encode( $gallery_ids ) );
			}
		}
	};

	// --- Pages -------------------------------------------------------------
	foreach ( (array) $manifest['pages'] as $page ) {
		$existing = get_page_by_path( $page['slug'], OBJECT, 'page' );
		if ( $existing ) {
			$result['pages_skipped']++;
			continue;
		}
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => $page['title'],
				'post_name'    => $page['slug'],
				'post_status'  => 'publish',
				'post_content' => '',
			)
		);
		if ( is_wp_error( $page_id ) || ! $page_id ) {
			$result['errors'][] = sprintf( __( 'Could not create page "%s".', 'nsml' ), $page['title'] );
			continue;
		}
		update_post_meta( $page_id, '_wp_page_template', $page['template'] );
		$result['pages_created']++;
	}

	// --- Posts ---------------------------------------------------------------
	foreach ( (array) $manifest['posts'] as $post_data ) {
		$existing = get_page_by_path( $post_data['slug'], OBJECT, 'post' );
		if ( $existing ) {
			$result['posts_skipped']++;
			if ( ! has_post_thumbnail( $existing->ID ) && ! empty( $post_data['hero_image'] ) ) {
				$att_id = $import_image( $post_data['hero_image'], $post_data['title'] );
				if ( $att_id ) {
					set_post_thumbnail( $existing->ID, $att_id );
				}
			}
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_title'   => $post_data['title'],
				'post_name'    => $post_data['slug'],
				'post_status'  => 'publish',
				'post_date'    => $post_data['date'],
				'post_content' => $post_data['content'],
			)
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			$result['errors'][] = sprintf( __( 'Could not create post "%s".', 'nsml' ), $post_data['title'] );
			continue;
		}

		if ( ! empty( $post_data['tags'] ) ) {
			wp_set_post_tags( $post_id, $post_data['tags'], false );
		}

		if ( ! empty( $post_data['hero_image'] ) ) {
			$att_id = $import_image( $post_data['hero_image'], $post_data['title'] );
			if ( $att_id ) {
				set_post_thumbnail( $post_id, $att_id );
			} else {
				$result['errors'][] = sprintf( __( 'Could not import hero image for post "%s".', 'nsml' ), $post_data['title'] );
			}
		}

		$result['posts_created']++;
	}

	// --- Properties ------------------------------------------------------------
	foreach ( (array) $manifest['properties'] as $prop ) {
		$existing = get_page_by_path( $prop['slug'], OBJECT, NSML_PROPERTY_CPT );
		if ( $existing ) {
			$result['properties_skipped']++;
			$backfill_property_media( $existing->ID, $prop );
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => NSML_PROPERTY_CPT,
				'post_title'   => $prop['title'],
				'post_name'    => $prop['slug'],
				'post_status'  => 'publish',
				'post_content' => $prop['about_html'],
			)
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			$result['errors'][] = sprintf( __( 'Could not create property "%s".', 'nsml' ), $prop['title'] );
			continue;
		}

		update_post_meta( $post_id, 'nsml_location', $prop['location'] );
		update_post_meta( $post_id, 'nsml_hero_tag', $prop['hero_tag'] );
		update_post_meta( $post_id, 'nsml_official_website', $prop['website'] );
		update_post_meta( $post_id, 'nsml_organizer_type', $prop['organizer_type'] );
		update_post_meta( $post_id, 'nsml_next_edition', $prop['next_edition'] );
		update_post_meta( $post_id, 'nsml_about', $prop['about_html'] );
		update_post_meta( $post_id, 'nsml_stats_json', wp_json_encode( $prop['stats'] ) );

		$backfill_property_media( $post_id, $prop );
		if ( ! empty( $prop['hero_image'] ) && ! has_post_thumbnail( $post_id ) ) {
			$result['errors'][] = sprintf( __( 'Could not import hero image for property "%s".', 'nsml' ), $prop['title'] );
		}

		$result['properties_created']++;
	}

	// --- Team members ----------------------------------------------------------
	// Photos live in the theme's own assets/images/team/, not demo-content/images/,
	// so they're copied directly rather than through $import_image().
	$team_photos_dir = trailingslashit( NSML_THEME_DIR ) . 'assets/images/team/';
	foreach ( nsml_get_team_seed_data() as $member ) {
		$slug     = sanitize_title( $member['name'] );
		$existing = get_page_by_path( $slug, OBJECT, NSML_TEAM_CPT );
		if ( $existing ) {
			$result['team_skipped']++;
			if ( ! has_post_thumbnail( $existing->ID ) && ! empty( $member['photo'] ) ) {
				$att_id = nsml_import_local_image( $team_photos_dir . $member['photo'], $member['name'] );
				if ( $att_id ) {
					set_post_thumbnail( $existing->ID, $att_id );
					$result['images_imported']++;
				}
			}
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => NSML_TEAM_CPT,
				'post_title'   => $member['name'],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_content' => $member['bio'],
			)
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			$result['errors'][] = sprintf( __( 'Could not create team member "%s".', 'nsml' ), $member['name'] );
			continue;
		}

		update_post_meta( $post_id, 'nsml_team_role', $member['role'] );
		update_post_meta( $post_id, 'nsml_team_is_lead', $member['is_lead'] ? 1 : 0 );

		if ( ! empty( $member['photo'] ) ) {
			$att_id = nsml_import_local_image( $team_photos_dir . $member['photo'], $member['name'] );
			if ( $att_id ) {
				set_post_thumbnail( $post_id, $att_id );
				$result['images_imported']++;
			}
		}

		$result['team_created']++;
	}

	// --- Reading settings ("Posts page") --------------------------------------
	// Makes the blog index land at a pretty /news/ URL out of the box, instead
	// of requiring the manual Settings > Reading step from the README. Only
	// runs if no Posts page is configured yet, so it never overwrites a choice
	// the site owner already made themselves.
	if ( ! get_option( 'page_for_posts' ) ) {
		$home_page = get_page_by_path( 'home', OBJECT, 'page' );
		$news_page = get_page_by_path( 'news', OBJECT, 'page' );
		if ( $news_page ) {
			update_option( 'show_on_front', 'page' );
			if ( $home_page ) {
				update_option( 'page_on_front', $home_page->ID );
			}
			update_option( 'page_for_posts', $news_page->ID );
			flush_rewrite_rules();
		}
	}

	return $result;
}
