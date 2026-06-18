<?php
/**
 * Single Property template (post type 'nsml_property').
 * Markup mirrors lagos-marathon.html — prop-hero, stats band, content
 * (body + sidebar), and the event gallery.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$nsml_post_id       = get_the_ID();
	$nsml_thumb_url     = get_the_post_thumbnail_url( $nsml_post_id, 'full' );
	if ( ! $nsml_thumb_url ) {
		$nsml_thumb_url = NSML_THEME_URI . '/assets/images/logo.png';
	}
	$nsml_location      = get_post_meta( $nsml_post_id, 'nsml_location', true );
	$nsml_hero_tag      = get_post_meta( $nsml_post_id, 'nsml_hero_tag', true );
	$nsml_official_site = get_post_meta( $nsml_post_id, 'nsml_official_website', true );
	$nsml_organizer     = get_post_meta( $nsml_post_id, 'nsml_organizer_type', true );
	$nsml_organizer     = $nsml_organizer ? $nsml_organizer : 'owned';
	$nsml_next_edition  = get_post_meta( $nsml_post_id, 'nsml_next_edition', true );
	$nsml_about         = get_post_meta( $nsml_post_id, 'nsml_about', true );
	$nsml_sponsor_id    = (int) get_post_meta( $nsml_post_id, 'nsml_sponsor_image_id', true );
	$nsml_event_logo_id = (int) get_post_meta( $nsml_post_id, 'nsml_event_logo_id', true );
	$nsml_stats         = nsml_get_property_stats( $nsml_post_id );
	$nsml_gallery       = nsml_get_property_gallery( $nsml_post_id );
	$nsml_archive_url   = get_post_type_archive_link( NSML_PROPERTY_CPT );

	$nsml_organizer_label = ( 'consultant' === $nsml_organizer )
		? __( 'Consultant & Organized By', 'nsml' )
		: __( 'Owned and Organized By', 'nsml' );
	?>

	<style>
		.prop-hero { min-height: 70vh; display: flex; flex-direction: column; justify-content: flex-end; padding: 9rem 1.5rem 3rem; position: relative; overflow: hidden; background: var(--navy); }
		.prop-hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center; background-repeat: no-repeat; filter: contrast(1.08) brightness(0.85); will-change: transform; }
		.prop-hero-overlay { position: absolute; inset: 0; background: linear-gradient( to bottom, rgba(13,31,60,0.3) 0%, rgba(13,31,60,0.5) 50%, rgba(13,31,60,0.95) 100% ); }
		.prop-hero-inner { position: relative; z-index: 2; max-width: 72rem; margin: 0 auto; width: 100%; }
		.prop-back { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: rgba(255,255,255,0.55); text-decoration: none; margin-bottom: 1.5rem; transition: color 0.3s var(--ease); }
		.prop-back:hover { color: var(--green); }
		.prop-page-badge { display: inline-block; background: var(--green); color: #ffffff; border-radius: 9999px; font-size: 0.625rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 0.3rem 0.875rem; margin-bottom: 1rem; }
		.prop-hero-tag { display: inline-block; background: var(--accent-glow); color: var(--green); border: 1px solid var(--accent-ring); border-radius: 9999px; font-size: 0.625rem; font-weight: 700; letter-spacing: 0.11em; text-transform: uppercase; padding: 0.25rem 0.875rem; margin-bottom: 1rem; }
		.prop-hero-title { font-family: var(--font-d); font-size: clamp(1.875rem, 4.5vw, 3.75rem); font-weight: 800; letter-spacing: -0.03em; color: #ffffff; line-height: 1.1; margin-bottom: 1rem; max-width: 52rem; }
		.prop-hero-location { font-size: 0.9375rem; color: rgba(255,255,255,0.55); display: flex; align-items: center; gap: 0.375rem; margin-bottom: 1.75rem; }
		.prop-hero-actions { display: flex; align-items: center; gap: 0.875rem; flex-wrap: wrap; }
		.prop-stats-band { background: var(--navy); border-bottom: 3px solid var(--green); }
		.prop-stats-inner { max-width: 72rem; margin: 0 auto; display: grid; grid-template-columns: repeat(4, 1fr); }
		.prop-stat-item { padding: 2.5rem 2rem; text-align: center; border-right: 1px solid rgba(255,255,255,0.1); }
		.prop-stat-item:last-child { border-right: none; }
		.prop-stat-val { font-family: var(--font-d); font-size: clamp(1.75rem, 3.5vw, 2.75rem); font-weight: 800; letter-spacing: -0.03em; color: #ffffff; line-height: 1; margin-bottom: 0.5rem; }
		.prop-stat-lbl { font-size: 0.8125rem; color: rgba(255,255,255,0.5); line-height: 1.4; }
		.prop-content { max-width: 72rem; margin: 0 auto; padding: 5rem 1.5rem; display: grid; grid-template-columns: 1fr 340px; gap: 5rem; align-items: start; }
		.prop-body p { font-size: 1.0625rem; color: var(--text-sub); line-height: 1.85; margin-bottom: 1.75rem; }
		.prop-sidebar { display: flex; flex-direction: column; gap: 1.25rem; }
		.prop-sidebar-card { padding: 0.25rem; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--r-xl); }
		.prop-sidebar-card-inner { background: #ffffff; border-radius: calc(var(--r-xl) - 0.25rem); padding: 1.75rem; }
		.prop-sidebar-title { font-family: var(--font-d); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--green); margin-bottom: 1.25rem; }
		.sponsor-logo-pill { display: inline-flex; align-items: center; justify-content: center; background: #ffffff; border: 1px solid var(--border); border-radius: 9999px; padding: 0.4rem 1.125rem; margin: 0.25rem 0.25rem 0 0; height: 2.375rem; }
		.sponsor-logo-pill img { height: 100%; max-width: 5.5rem; object-fit: contain; display: block; }
		.sponsor-pill { display: inline-block; background: var(--surface); border: 1px solid var(--border); border-radius: 9999px; font-size: 0.8125rem; font-weight: 600; color: var(--navy); padding: 0.3rem 0.875rem; margin: 0.25rem 0.25rem 0 0; }
		.prop-next { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9375rem; color: var(--text-sub); font-weight: 500; }
		.prop-next strong { color: var(--navy); }
		.prop-cta-band { background: var(--green); padding: 4rem 1.5rem; text-align: center; }
		.prop-cta-band h2 { font-family: var(--font-d); font-size: clamp(1.625rem, 3vw, 2.5rem); font-weight: 800; letter-spacing: -0.03em; color: #ffffff; margin-bottom: 1rem; }
		.prop-cta-band p { font-size: 1.0625rem; color: rgba(255,255,255,0.75); margin-bottom: 2rem; max-width: 36rem; margin-left: auto; margin-right: auto; line-height: 1.7; }
		.btn-white-solid { display: inline-flex; align-items: center; gap: 0.75rem; background: #ffffff; color: var(--green-dark); font-family: var(--font-b); font-size: 0.9375rem; font-weight: 700; text-decoration: none; border-radius: 9999px; padding: 0.9375rem 1.375rem 0.9375rem 1.875rem; transition: all 0.4s var(--ease); }
		.btn-white-solid:hover { background: var(--navy); color: #ffffff; transform: scale(0.97); }
		@media (max-width: 1024px) {
			.prop-content { grid-template-columns: 1fr; gap: 3rem; }
			.prop-stats-inner { grid-template-columns: repeat(2, 1fr); }
			.prop-stat-item:nth-child(2) { border-right: none; }
			.prop-stat-item:nth-child(3) { border-top: 1px solid rgba(255,255,255,0.1); border-right: 1px solid rgba(255,255,255,0.1); }
			.prop-stat-item:nth-child(4) { border-top: 1px solid rgba(255,255,255,0.1); }
		}
		@media (max-width: 768px) {
			.prop-hero { min-height: 0; padding: 8rem 1.25rem 2.5rem; }
			.prop-hero-title { font-size: clamp(1.625rem, 7vw, 2.5rem); }
			.prop-stats-inner { grid-template-columns: repeat(2, 1fr); }
			.prop-content { padding: 3rem 1.25rem; }
		}
		.prop-gallery-section { padding: 5rem 1.5rem; max-width: 72rem; margin: 0 auto; }
		.prop-gallery-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; grid-auto-flow: dense; }
		.gallery-slot-wide { grid-column: span 2; }
		.gallery-img-wrap { border-radius: var(--r-lg); overflow: hidden; aspect-ratio: 4 / 3; background: var(--surface); border: 2px dashed var(--border-hi); transition: border-color 0.3s var(--ease); }
		.gallery-slot-wide .gallery-img-wrap { aspect-ratio: 16 / 7; }
		.gallery-img-wrap:hover { border-color: var(--green); }
		.gallery-img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.7s cubic-bezier(0.32,0.72,0,1); }
		.gallery-img-wrap:hover .gallery-img { transform: scale(1.04); }
		@media (max-width: 768px) {
			.prop-gallery-grid { grid-template-columns: repeat(2, 1fr); }
			.gallery-slot-wide { grid-column: span 2; }
			.prop-gallery-section { padding: 3.5rem 1.25rem; }
		}
		@media (max-width: 480px) {
			.prop-gallery-grid { grid-template-columns: 1fr; }
			.gallery-slot-wide { grid-column: span 1; }
		}
		.event-logo-wrap { margin-bottom: 1.25rem; }
		.event-logo { height: 70px; width: auto; max-width: 220px; object-fit: contain; display: block; filter: brightness(1.05); }
	</style>

	<div class="prop-hero">
		<div class="prop-hero-bg" id="propHeroBg" style="background-image:url('<?php echo esc_url( $nsml_thumb_url ); ?>')"></div>
		<div class="prop-hero-overlay"></div>
		<div class="prop-hero-inner">
			<a href="<?php echo esc_url( $nsml_archive_url ); ?>" class="prop-back">&larr; <?php esc_html_e( 'All Properties', 'nsml' ); ?></a>
			<span class="prop-page-badge"><?php esc_html_e( 'World Athletics Certified', 'nsml' ); ?></span>
			<?php if ( $nsml_event_logo_id ) : ?>
				<div class="event-logo-wrap">
					<?php echo wp_get_attachment_image( $nsml_event_logo_id, 'medium', false, array( 'class' => 'event-logo', 'alt' => esc_attr( get_the_title() . ' ' . __( 'logo', 'nsml' ) ) ) ); ?>
				</div>
			<?php endif; ?>
			<?php if ( $nsml_hero_tag ) : ?>
				<div class="prop-hero-tag"><?php echo esc_html( $nsml_hero_tag ); ?></div>
			<?php endif; ?>
			<h1 class="prop-hero-title"><?php echo esc_html( get_the_title() ); ?></h1>
			<?php if ( $nsml_location ) : ?>
				<div class="prop-hero-location">&#128205; <?php echo esc_html( $nsml_location ); ?></div>
			<?php endif; ?>
			<div class="prop-hero-actions">
				<?php if ( $nsml_official_site ) : ?>
					<a href="<?php echo esc_url( $nsml_official_site ); ?>" target="_blank" rel="noopener" class="btn btn-fill">
						<span><?php esc_html_e( 'Visit Official Website', 'nsml' ); ?></span>
						<span class="btn-icon"><?php echo nsml_btn_icon_svg(); ?></span>
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-outline-white"><?php esc_html_e( 'Partner With Us', 'nsml' ); ?></a>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $nsml_stats ) ) : ?>
		<div class="prop-stats-band">
			<div class="prop-stats-inner">
				<?php foreach ( $nsml_stats as $nsml_stat ) : ?>
					<div class="prop-stat-item">
						<div class="prop-stat-val"><?php echo esc_html( $nsml_stat['value'] ?? '' ); ?></div>
						<div class="prop-stat-lbl"><?php echo esc_html( $nsml_stat['label'] ?? '' ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="prop-content">
		<div class="prop-body" data-reveal>
			<div class="section-tag"><?php esc_html_e( 'About This Property', 'nsml' ); ?></div>
			<?php echo wp_kses_post( $nsml_about ); ?>

			<?php if ( $nsml_next_edition ) : ?>
				<div class="prop-next">
					<span class="eyebrow-dot"></span>
					<?php esc_html_e( 'Next Edition:', 'nsml' ); ?> <strong><?php echo esc_html( $nsml_next_edition ); ?></strong>
				</div>
			<?php endif; ?>
		</div>

		<div class="prop-sidebar">
			<?php if ( $nsml_sponsor_id ) : ?>
				<div class="prop-sidebar-card" data-reveal>
					<div class="prop-sidebar-card-inner">
						<div class="prop-sidebar-title"><?php esc_html_e( 'Sponsors & Partners', 'nsml' ); ?></div>
						<?php
						echo wp_get_attachment_image(
							$nsml_sponsor_id,
							'large',
							false,
							array(
								'style' => 'width:100%;border-radius:0.625rem;margin-top:0.75rem;display:block;',
								'alt'   => esc_attr( get_the_title() . ' ' . __( 'Sponsors & Partners', 'nsml' ) ),
							)
						);
						?>
					</div>
				</div>
			<?php endif; ?>

			<div class="prop-sidebar-card" data-reveal>
				<div class="prop-sidebar-card-inner">
					<div class="prop-sidebar-title"><?php echo esc_html( $nsml_organizer_label ); ?></div>
					<div style="font-size:0.9375rem;color:var(--text-sub);line-height:1.65;">
						<strong style="display:block;color:var(--navy);font-family:var(--font-d);margin-bottom:0.25rem;"><?php esc_html_e( 'Nilayo Sports Management Ltd', 'nsml' ); ?></strong>
						<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" style="color:var(--green);font-weight:600;text-decoration:none;font-size:0.875rem;display:inline-flex;align-items:center;gap:0.375rem;margin-top:0.875rem;"><?php esc_html_e( 'Get in touch', 'nsml' ); ?> &#8599;</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $nsml_gallery ) ) : ?>
		<div class="prop-gallery-section">
			<div class="prop-gallery-inner" style="max-width:72rem;margin:0 auto;">
				<div class="section-tag"><?php esc_html_e( 'Event Gallery', 'nsml' ); ?></div>
				<h2 class="sec-h2" style="margin-bottom:2rem;"><?php esc_html_e( 'From the', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Ground', 'nsml' ); ?></span></h2>
				<div class="prop-gallery-grid">
					<?php foreach ( $nsml_gallery as $nsml_image ) : ?>
						<?php
						$nsml_image_id = isset( $nsml_image['id'] ) ? absint( $nsml_image['id'] ) : 0;
						if ( ! $nsml_image_id ) {
							continue;
						}
						$nsml_slot_class = ! empty( $nsml_image['wide'] ) ? 'gallery-slot gallery-slot-wide' : 'gallery-slot';
						?>
						<div class="<?php echo esc_attr( $nsml_slot_class ); ?>">
							<div class="gallery-img-wrap">
								<?php echo wp_get_attachment_image( $nsml_image_id, 'large', false, array( 'class' => 'gallery-img', 'loading' => 'lazy' ) ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="prop-cta-band">
		<h2><?php esc_html_e( 'Interested in Sponsoring This Event?', 'nsml' ); ?></h2>
		<p><?php esc_html_e( 'Partner with NSML to associate your brand with one of Africa\'s most impactful sporting properties. We create meaningful connections between brands and millions of fans, athletes, and communities.', 'nsml' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn-white-solid">
			<span><?php esc_html_e( 'Start a Conversation', 'nsml' ); ?></span>
			<span class="btn-icon" style="width:2rem;height:2rem;border-radius:50%;background:rgba(0,0,0,0.1);display:flex;align-items:center;justify-content:center;"><?php echo nsml_btn_icon_svg(); ?></span>
		</a>
	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
