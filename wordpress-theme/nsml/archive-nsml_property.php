<?php
/**
 * Properties archive (post type 'nsml_property').
 * Markup mirrors properties.html — props-grid is rendered dynamically from
 * published Property posts; the "Upcoming" and "Consultations" sections are
 * not backed by any CPT and are kept as static markup (per spec), with only
 * image src paths updated to the theme's bundled assets.
 *
 * Simplification: the CPT has no per-post "is featured" flag yet, so the
 * first post returned by the query is rendered as the wide .pcard.featured
 * card. A future iteration could add a boolean meta field for this.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$nsml_properties = get_posts(
	array(
		'post_type'      => NSML_PROPERTY_CPT,
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
?>

<style>
	.props-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
	.pcard { padding: 0.25rem; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-xl); position: relative; transition: border-color 0.4s var(--ease); }
	.pcard:hover { border-color: var(--border-hi); }
	.pcard-inner { border-radius: calc(var(--r-xl) - 0.25rem); overflow: hidden; background: var(--surface-2); }
	.pcard-img-wrap { overflow: hidden; height: 220px; position: relative; }
	.pcard-event-logo { position: absolute; bottom: 0.875rem; left: 0.875rem; z-index: 3; background: rgba(255,255,255,0.95); border-radius: 0.625rem; padding: 0.375rem 0.625rem; display: flex; align-items: center; box-shadow: 0 2px 8px rgba(13,31,60,0.15); }
	.pcard-event-logo img { height: 36px; width: auto; max-width: 110px; object-fit: contain; display: block; }
	.pcard.featured .pcard-event-logo img { height: 44px; max-width: 140px; }
	.pcard-img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(0.4) contrast(1.08); transition: transform 0.75s var(--ease), filter 0.75s var(--ease); display: block; }
	.pcard:hover .pcard-img { transform: scale(1.05); filter: grayscale(0.1) contrast(1.1); }
	.pcard-body { padding: 1.75rem; }
	.pcard-tag { display: inline-block; background: var(--accent-glow); color: var(--accent); border: 1px solid var(--accent-ring); border-radius: 9999px; font-size: 0.625rem; font-weight: 700; letter-spacing: 0.11em; text-transform: uppercase; padding: 0.25rem 0.875rem; margin-bottom: 0.875rem; }
	.pcard-title { font-family: var(--font-d); font-size: 1.0625rem; font-weight: 700; letter-spacing: -0.02em; line-height: 1.25; margin-bottom: 0.625rem; color: var(--navy); }
	.pcard-desc { font-size: 0.8125rem; color: var(--text-sub); line-height: 1.7; margin-bottom: 1.25rem; }
	.pcard-stats { display: flex; gap: 1.5rem; flex-wrap: wrap; border-top: 1px solid var(--border); padding-top: 1rem; }
	.pcard-stat-val { font-family: var(--font-d); font-size: 1.125rem; font-weight: 700; letter-spacing: -0.025em; color: var(--text); }
	.pcard-stat-lbl { font-size: 0.6875rem; color: var(--text-muted); margin-top: 0.1rem; }
	.pcard-badge { position: absolute; top: 1.25rem; right: 1.25rem; background: var(--accent); color: #0c0e0d; border-radius: 9999px; font-size: 0.5625rem; font-weight: 700; letter-spacing: 0.09em; text-transform: uppercase; padding: 0.3rem 0.875rem; z-index: 2; }
	.pcard.featured { grid-column: span 3; }
	.pcard.featured .pcard-inner { display: grid; grid-template-columns: 1.1fr 1fr; }
	.pcard.featured .pcard-img-wrap { height: 100%; min-height: 300px; }
	.pcard.featured .pcard-body { padding: 2.5rem; display: flex; flex-direction: column; justify-content: center; }
	.pcard.featured .pcard-title { font-size: 1.375rem; }
	.upcoming-band { background: var(--surface); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 6rem 1.5rem; }
	.upcoming-inner { max-width: 72rem; margin: 0 auto; }
	.upcoming-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 3rem; }
	.uc { padding: 0.25rem; background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-xl); transition: border-color 0.4s var(--ease); }
	.uc:hover { border-color: var(--border-hi); }
	.uc-inner { background: var(--surface-2); border-radius: calc(var(--r-xl) - 0.25rem); overflow: hidden; }
	.uc-img-wrap { position: relative; height: 220px; overflow: hidden; }
	.uc-img-wrap img.uc-hero { width: 100%; height: 100%; object-fit: cover; object-position: center; display: block; transition: transform 0.5s var(--ease); }
	.uc:hover .uc-img-wrap img.uc-hero { transform: scale(1.04); }
	.uc-img-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.5) 100%); }
	.uc-badge { position: absolute; top: 1rem; left: 1rem; font-family: var(--font-d); font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #fff; background: var(--green); padding: 0.3em 0.75em; border-radius: 2rem; }
	.uc-logo-wrap { position: absolute; bottom: 1rem; left: 1rem; }
	.uc-logo-wrap img { height: 48px; width: auto; object-fit: contain; filter: brightness(0) invert(1); }
	.uc-body { padding: 1.5rem; }
	.uc-date { font-family: var(--font-d); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.09em; text-transform: uppercase; color: var(--accent); margin-bottom: 0.4rem; }
	.uc-title { font-family: var(--font-d); font-size: 1.125rem; font-weight: 700; letter-spacing: -0.02em; line-height: 1.2; margin-bottom: 0.5rem; }
	.uc-desc { font-size: 0.875rem; color: var(--text-sub); line-height: 1.7; }
	.consult-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 3rem; }
	.consult-card { padding: 0.25rem; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-xl); transition: border-color 0.4s var(--ease); }
	.consult-card:hover { border-color: var(--border-hi); }
	.consult-inner { background: var(--surface-2); border-radius: calc(var(--r-xl) - 0.25rem); padding: 1.75rem; box-shadow: inset 0 1px 1px rgba(255,255,255,0.03); }
	.consult-type { font-size: 0.625rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.75rem; }
	.consult-title { font-family: var(--font-d); font-size: 1rem; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 0.5rem; }
	.consult-desc { font-size: 0.8125rem; color: var(--text-sub); line-height: 1.65; }
	@media (max-width: 1024px) {
		.props-grid { grid-template-columns: repeat(2, 1fr); }
		.pcard.featured { grid-column: span 2; }
		.consult-grid { grid-template-columns: repeat(2, 1fr); }
	}
	@media (max-width: 768px) {
		.props-grid { grid-template-columns: 1fr; }
		.pcard.featured { grid-column: span 1; }
		.pcard.featured .pcard-inner { grid-template-columns: 1fr; }
		.pcard.featured .pcard-img-wrap { min-height: 200px; }
		.upcoming-grid { grid-template-columns: 1fr; }
		.consult-grid { grid-template-columns: 1fr; }
	}
	.pcard-title-link { text-decoration: none; }
	.pcard-title-link .pcard-title:hover { color: var(--green-dark); }
	.pcard-view-btn { display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; font-weight: 700; color: var(--green-dark); text-decoration: none; margin-top: 1.25rem; transition: gap 0.3s var(--ease), color 0.3s var(--ease); }
	.pcard-view-btn:hover { gap: 0.625rem; color: var(--navy); }
</style>

<div class="page-hero">
	<div class="page-hero-bg" style="background-image:url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=1920&amp;q=80&amp;auto=format&amp;fit=crop')"></div>
	<div class="page-hero-overlay"></div>
	<div class="page-hero-inner">
		<div class="page-hero-label"><?php esc_html_e( 'Our Portfolio', 'nsml' ); ?></div>
		<h1 class="page-hero-h1"><?php esc_html_e( 'World-Class Events', 'nsml' ); ?><br><?php esc_html_e( 'Across', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Africa', 'nsml' ); ?></span></h1>
		<p class="page-hero-p"><?php esc_html_e( "From the continent's strongest marathon brand to beach football and heritage races — NSML owns, manages, and grows Africa's most impactful sporting properties.", 'nsml' ); ?></p>
	</div>
</div>

<div class="section-wrap">
	<div class="section-tag"><?php esc_html_e( 'Active Properties', 'nsml' ); ?></div>
	<h2 class="sec-h2"><?php esc_html_e( 'Our', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Events', 'nsml' ); ?></span></h2>
	<div class="props-grid" style="margin-top:3rem">
		<?php
		$nsml_i = 0;
		foreach ( $nsml_properties as $nsml_property ) :
			$nsml_is_featured  = ( 0 === $nsml_i ); // Known simplification: no per-post "featured" meta field exists yet; first post in menu_order is treated as featured.
			$nsml_card_class   = $nsml_is_featured ? 'pcard featured' : 'pcard';
			$nsml_hero_tag     = get_post_meta( $nsml_property->ID, 'nsml_hero_tag', true );
			$nsml_event_logo   = (int) get_post_meta( $nsml_property->ID, 'nsml_event_logo_id', true );
			$nsml_prop_stats   = array_slice( nsml_get_property_stats( $nsml_property->ID ), 0, 3 );
			$nsml_prop_link    = get_permalink( $nsml_property );
			$nsml_i++;
			?>
			<div class="<?php echo esc_attr( $nsml_card_class ); ?>" data-prop>
				<?php if ( $nsml_is_featured ) : ?>
					<span class="pcard-badge"><?php esc_html_e( 'World Athletics', 'nsml' ); ?></span>
				<?php endif; ?>
				<div class="pcard-inner">
					<div class="pcard-img-wrap">
						<?php if ( has_post_thumbnail( $nsml_property ) ) : ?>
							<?php echo get_the_post_thumbnail( $nsml_property, 'nsml-card', array( 'class' => 'pcard-img', 'loading' => 'lazy' ) ); ?>
						<?php else : ?>
							<img class="pcard-img" src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/logo.png' ); ?>" alt="<?php echo esc_attr( get_the_title( $nsml_property ) ); ?>" loading="lazy">
						<?php endif; ?>
						<?php if ( $nsml_event_logo ) : ?>
							<div class="pcard-event-logo">
								<?php echo wp_get_attachment_image( $nsml_event_logo, 'thumbnail', false, array( 'alt' => esc_attr( get_the_title( $nsml_property ) . ' logo' ) ) ); ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="pcard-body">
						<?php if ( $nsml_hero_tag ) : ?>
							<div class="pcard-tag"><?php echo esc_html( $nsml_hero_tag ); ?></div>
						<?php endif; ?>
						<a href="<?php echo esc_url( $nsml_prop_link ); ?>" class="pcard-title-link"><div class="pcard-title"><?php echo esc_html( get_the_title( $nsml_property ) ); ?></div></a>
						<div class="pcard-desc"><?php echo esc_html( get_the_excerpt( $nsml_property ) ); ?></div>
						<?php if ( ! empty( $nsml_prop_stats ) ) : ?>
							<div class="pcard-stats">
								<?php foreach ( $nsml_prop_stats as $nsml_stat ) : ?>
									<div>
										<div class="pcard-stat-val"><?php echo esc_html( $nsml_stat['value'] ?? '' ); ?></div>
										<div class="pcard-stat-lbl"><?php echo esc_html( $nsml_stat['label'] ?? '' ); ?></div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<a href="<?php echo esc_url( $nsml_prop_link ); ?>" class="pcard-view-btn"><?php esc_html_e( 'View Property', 'nsml' ); ?> &#8599;</a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<!-- UPCOMING PROJECTS (static — not backed by a CPT) -->
<div class="upcoming-band">
	<div class="upcoming-inner">
		<div class="section-tag"><?php esc_html_e( 'Upcoming', 'nsml' ); ?></div>
		<h2 class="sec-h2"><?php esc_html_e( 'Coming', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Soon', 'nsml' ); ?></span></h2>
		<div class="upcoming-grid">
			<div class="uc" data-reveal>
				<div class="uc-inner">
					<div class="uc-img-wrap">
						<img class="uc-hero" src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/events/copa-lagos-hero.jpg' ); ?>" alt="Copa Lagos Beach Soccer">
						<div class="uc-img-overlay"></div>
						<div class="uc-badge">Upcoming &middot; Dec 2026</div>
						<div class="uc-logo-wrap">
							<img src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/events/copa-lagos_web.png' ); ?>" alt="Copa Lagos">
						</div>
					</div>
					<div class="uc-body">
						<a href="<?php echo esc_url( home_url( '/properties/copa-lagos/' ) ); ?>" style="text-decoration:none;color:inherit;">
							<div class="uc-title">Copa Lagos Beach Soccer</div>
						</a>
						<div class="uc-desc">Nigeria's premier beach football and lifestyle event returns to Eko Atlantic City. A three-day high-energy weekend drawing 20,000+ fans, athletes, and lifestyle enthusiasts. Licensee granted by Kinetic Sports — first return since the 2019 edition.</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- CONSULTATIONS (static — not backed by a CPT) -->
<div class="section-wrap">
	<div class="section-tag"><?php esc_html_e( 'Consultations', 'nsml' ); ?></div>
	<h2 class="sec-h2"><?php esc_html_e( "Projects We've", 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Shaped', 'nsml' ); ?></span></h2>
	<p class="sec-p"><?php esc_html_e( 'Beyond our owned properties, NSML has served as technical consultant and brand management partner on some of Africa\'s largest sporting events.', 'nsml' ); ?></p>
	<div class="consult-grid">
		<div class="consult-card" data-reveal>
			<div class="consult-inner">
				<div class="consult-type">Technical Consultant</div>
				<div class="consult-title">National Sports Festival Delta 2022</div>
				<div class="consult-desc">Partnered to help the state win the pitch and execute a top-level sporting festival. 15,000+ athletes across all states of the federation. 8 days in Asaba, Delta State.</div>
			</div>
		</div>
		<div class="consult-card" data-reveal>
			<div class="consult-inner">
				<div class="consult-type">Technical Consultant</div>
				<div class="consult-title">Niger Delta Sports Festival</div>
				<div class="consult-desc">Helped the organising committee execute a top-level sporting festival involving all Niger Delta states. 3,000+ competing athletes over 8 days in Uyo, Akwa Ibom State.</div>
			</div>
		</div>
		<div class="consult-card" data-reveal>
			<div class="consult-inner">
				<div class="consult-type">Sponsorship Consultant</div>
				<div class="consult-title">Asaba 2018 African Senior Athletics Championship</div>
				<div class="consult-desc">5,000+ athletes from 54 African countries. Secured commitments from Zenith Bank, GAC Motors, Rite Foods, Ericsson, Gree Electric, and Lontor.</div>
			</div>
		</div>
		<div class="consult-card" data-reveal>
			<div class="consult-inner">
				<div class="consult-type">Project &amp; Sponsorship Consultant</div>
				<div class="consult-title">F5WC Football Five World Championship</div>
				<div class="consult-desc">Nigeria representative for the world's first 5-A-Side amateur tournament. 1M+ players across 48 countries. Raised over ₦20M from the private sector locally.</div>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
