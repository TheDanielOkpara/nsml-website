<?php
/**
 * Shared template helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The "Trusted Partners & Affiliations" logo marquee that appears on
 * every page of the static site (homepage, about, services, contact,
 * every article, every property), right before the footer. Identical
 * everywhere, so it's rendered once from footer.php instead of being
 * duplicated per template. Two independent tracks scroll in opposite
 * directions, each with its own 33 logos.
 */
function nsml_partner_logos_track_1() {
	return array(
		array( 'file' => 'world-athletics.png', 'alt' => 'World Athletics' ),
		array( 'file' => 'pngroyale.com-access-bank-plc-logo.png', 'alt' => 'Access Bank' ),
		array( 'file' => 'premium-trust-logo-colour-png.png', 'alt' => 'PremiumTrust Bank' ),
		array( 'file' => 'airtel-logo.png', 'alt' => 'Airtel Nigeria' ),
		array( 'file' => 'new-kia-logo.png', 'alt' => 'KIA Motors' ),
		array( 'file' => 'afnlogo.png', 'alt' => 'AFN' ),
		array( 'file' => 'dana.png', 'alt' => 'Dana Airlines' ),
		array( 'file' => 'air-peace-icon-2048x407-x77lwkmv.png', 'alt' => 'Air Peace' ),
		array( 'file' => 'pinnacle-logo.png', 'alt' => 'Pinnacle Oil' ),
		array( 'file' => 'aquavie.png', 'alt' => 'Aquavie' ),
		array( 'file' => 'firstbank-logo.png', 'alt' => 'First Bank' ),
		array( 'file' => 'keystone.png', 'alt' => 'Keystone Bank' ),
		array( 'file' => 'new-mtn-logo.png', 'alt' => 'MTN' ),
		array( 'file' => 'unilever.png', 'alt' => 'Unilever' ),
		array( 'file' => 'rite-logo.png', 'alt' => 'Rite Foods' ),
		array( 'file' => 'valuejet_approved_logo_png.png', 'alt' => 'Value Jet' ),
		array( 'file' => 'kenya_airways-logo.wine.png', 'alt' => 'Kenya Airways' ),
		array( 'file' => 'casio-logo.png', 'alt' => 'Casio' ),
		array( 'file' => 'ogun-state-logo.png', 'alt' => 'Ogun State' ),
		array( 'file' => 'bayelsa-sports-logo.png', 'alt' => 'Bayelsa Sports' ),
		array( 'file' => 'ramecgroup-logo.png', 'alt' => 'Ramec Group' ),
		array( 'file' => 'greenlife-pharmaceuticals-logo.png', 'alt' => 'Greenlife' ),
		array( 'file' => 'febbs-premium-drinking-water.png', 'alt' => 'FEBBS Water' ),
		array( 'file' => 'binad-table-water.png', 'alt' => 'Binad Water' ),
		array( 'file' => 'tcm-logo.png', 'alt' => 'TCM' ),
		array( 'file' => 'rexona_logo_2015.svg.png', 'alt' => 'Rexona' ),
		array( 'file' => 'premier-cool-deo-01.png', 'alt' => 'Premier Cool' ),
		array( 'file' => 'vitabol-hd-logo.png', 'alt' => 'Vitabol' ),
		array( 'file' => 'waka.png', 'alt' => 'Waka' ),
		array( 'file' => 'what-network-logo.png', 'alt' => 'What Network' ),
		array( 'file' => 'robb-logo.png', 'alt' => 'Robb' ),
		array( 'file' => 'rpp-logo.png', 'alt' => 'RPP' ),
		array( 'file' => 'ogsaa.png', 'alt' => 'OGSAA' ),
	);
}

function nsml_partner_logos_track_2() {
	return array(
		array( 'file' => 'bet9ja-logo.png', 'alt' => 'Bet9ja' ),
		array( 'file' => 'cashtoken.png', 'alt' => 'Cash Token' ),
		array( 'file' => 'channelstv-logo-new-1024x941.png', 'alt' => 'Channels TV' ),
		array( 'file' => 'eko-atlantic-logo-clean.png', 'alt' => 'Eko Atlantic' ),
		array( 'file' => 'nord.png', 'alt' => 'Nord' ),
		array( 'file' => 'brila-green-logo-with-fm-.png', 'alt' => 'Brila FM' ),
		array( 'file' => 'lasaa.png', 'alt' => 'LASAA' ),
		array( 'file' => 'fatgbems.png', 'alt' => 'Fatgbems' ),
		array( 'file' => 'hertage.png', 'alt' => 'Heritage Bank' ),
		array( 'file' => 'oraimo_logo2.0.png', 'alt' => 'Oraimo' ),
		array( 'file' => 'easytipping-front-logo.png', 'alt' => 'EasyTipping' ),
		array( 'file' => '2sure-logo.png', 'alt' => '2Sure' ),
		array( 'file' => 'lssc-new-logo.png', 'alt' => 'LSSC' ),
		array( 'file' => 'royal-crown-cola-logo-aefc4cb9e1-seeklogo.com.png', 'alt' => 'Royal Crown Cola' ),
		array( 'file' => 'comag-logo-2023-new.png', 'alt' => 'Comag' ),
		array( 'file' => 'conference-hotel-logo.png', 'alt' => 'Conference Hotel' ),
		array( 'file' => 'aims.png', 'alt' => 'AIMS' ),
		array( 'file' => 'fct.png', 'alt' => 'FCT' ),
		array( 'file' => 'lag.png', 'alt' => 'LAG' ),
		array( 'file' => 'peculiar.png', 'alt' => 'Peculiar' ),
		array( 'file' => 'cr.png', 'alt' => 'CR' ),
		array( 'file' => 'joy.png', 'alt' => 'Joy' ),
		array( 'file' => 'mf-logo1.png', 'alt' => 'MF' ),
		array( 'file' => 'lockup-transparent-background-01.png', 'alt' => 'Partner' ),
		array( 'file' => 'logo_31710a86f0b01cc31d0a2f0c263ad8d4_2x.png', 'alt' => 'Partner' ),
		array( 'file' => 'layer-1-copy-3.png', 'alt' => 'Partner' ),
		array( 'file' => 'img_0537.png', 'alt' => 'Partner' ),
		array( 'file' => 'img_0538.png', 'alt' => 'Partner' ),
		array( 'file' => 'aron-.png', 'alt' => 'Partner' ),
		array( 'file' => '2017_1large_atb.png', 'alt' => 'ATB' ),
		array( 'file' => '1519896687213.png', 'alt' => 'Partner' ),
		array( 'file' => '1280px-suzuki_logo_2.svg.png', 'alt' => 'Suzuki' ),
		array( 'file' => 'png-clipart-bayelsa-state-osun-state-rivers-state-kaduna-state-coat-of-arms-osun-state-bayelsa-state-rivers-state-removebg-preview.png', 'alt' => 'Bayelsa State' ),
	);
}

function nsml_render_partner_logo_set( $logos ) {
	foreach ( $logos as $logo ) {
		?>
		<img src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/partners/web/' . $logo['file'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" loading="lazy" class="partner-logo">
		<?php
	}
}

function nsml_render_partners_marquee() {
	?>
	<div class="partners">
		<div class="partners-lbl"><?php esc_html_e( 'Trusted Partners & Affiliations', 'nsml' ); ?></div>
		<div class="partners-track">
			<div class="partners-set">
				<?php nsml_render_partner_logo_set( nsml_partner_logos_track_1() ); ?>
			</div>
			<div class="partners-set" aria-hidden="true">
				<?php nsml_render_partner_logo_set( nsml_partner_logos_track_1() ); ?>
			</div>
		</div>
		<div class="partners-track partners-track-reverse">
			<div class="partners-set">
				<?php nsml_render_partner_logo_set( nsml_partner_logos_track_2() ); ?>
			</div>
			<div class="partners-set" aria-hidden="true">
				<?php nsml_render_partner_logo_set( nsml_partner_logos_track_2() ); ?>
			</div>
		</div>
	</div>
	<?php
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
 * The single full-width featured card at the top of the News index
 * (home.php) — only ever the first post on page 1.
 */
function nsml_render_news_featured( $post ) {
	?>
	<article class="article-featured" data-reveal>
		<div class="article-img-wrap">
			<?php if ( has_post_thumbnail( $post ) ) : ?>
				<?php echo get_the_post_thumbnail( $post, 'nsml-card', array( 'class' => 'article-img', 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<img class="article-img" src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/logo.png' ); ?>" alt="<?php echo esc_attr( get_the_title( $post ) ); ?>" loading="lazy">
			<?php endif; ?>
		</div>
		<div class="article-body">
			<div class="article-meta">
				<span class="article-cat"><?php esc_html_e( 'News', 'nsml' ); ?></span>
				<span class="article-date"><?php echo esc_html( get_the_date( 'F Y', $post ) ); ?></span>
			</div>
			<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="article-title"><?php echo esc_html( get_the_title( $post ) ); ?></a>
			<p class="article-excerpt"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
			<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="article-read-more"><?php esc_html_e( 'Read Full Story', 'nsml' ); ?> <span class="article-read-more-arrow">↗</span></a>
		</div>
	</article>
	<?php
}

/**
 * Prev/numbers/next pagination for "Numbered pages" mode on the News index,
 * styled to match the rest of the theme's pill buttons.
 */
function nsml_render_news_pagination() {
	$links = paginate_links(
		array(
			'prev_text' => __( '← Previous', 'nsml' ),
			'next_text' => __( 'Next →', 'nsml' ),
			'type'      => 'array',
		)
	);

	if ( empty( $links ) ) {
		return;
	}
	?>
	<nav class="nsml-news-pagination" aria-label="<?php esc_attr_e( 'News pagination', 'nsml' ); ?>" style="display:flex;justify-content:center;gap:0.5rem;flex-wrap:wrap;margin-top:3.5rem;">
		<?php foreach ( $links as $link ) : ?>
			<?php echo wp_kses_post( $link ); ?>
		<?php endforeach; ?>
	</nav>
	<style>
		.nsml-news-pagination .page-numbers {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-width: 2.5rem;
			height: 2.5rem;
			padding: 0 1rem;
			border-radius: 9999px;
			border: 1.5px solid var(--border);
			color: var(--navy);
			font-family: var(--font-b);
			font-size: 0.875rem;
			font-weight: 600;
			text-decoration: none;
			transition: all 0.3s var(--ease);
		}
		.nsml-news-pagination a.page-numbers:hover { border-color: var(--green); color: var(--green-dark); }
		.nsml-news-pagination .page-numbers.current { background: var(--green); border-color: var(--green); color: #ffffff; }
		.nsml-news-pagination .page-numbers.dots { border: none; }
	</style>
	<?php
}

/**
 * One "article-card" on the News index (home.php). $hidden marks posts
 * beyond the first 6 — rendered into the page already, just hidden behind
 * the "Load More" reveal, mirroring news.html's `extra-article` markup
 * (a fixed batch of hidden nodes, not real AJAX pagination).
 */
function nsml_render_news_card( $post, $hidden = false ) {
	$classes = 'article-card' . ( $hidden ? ' extra-article' : '' );
	$style   = $hidden ? ' style="display:none;opacity:0;transform:translateY(2rem);"' : '';
	?>
	<article class="<?php echo esc_attr( $classes ); ?>"<?php echo $style; // phpcs:ignore -- fixed, non-dynamic inline style ?> data-reveal>
		<div class="article-img-wrap">
			<?php if ( has_post_thumbnail( $post ) ) : ?>
				<?php echo get_the_post_thumbnail( $post, 'nsml-card', array( 'class' => 'article-img', 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<img class="article-img" src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/logo.png' ); ?>" alt="<?php echo esc_attr( get_the_title( $post ) ); ?>" loading="lazy">
			<?php endif; ?>
		</div>
		<div class="article-body">
			<div class="article-meta">
				<span class="article-cat"><?php esc_html_e( 'News', 'nsml' ); ?></span>
				<span class="article-date"><?php echo esc_html( get_the_date( 'F Y', $post ) ); ?></span>
			</div>
			<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="article-title"><?php echo esc_html( get_the_title( $post ) ); ?></a>
			<p class="article-excerpt"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
			<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="article-read-more"><?php esc_html_e( 'Read Full Story', 'nsml' ); ?> <span class="article-read-more-arrow">↗</span></a>
		</div>
	</article>
	<?php
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
