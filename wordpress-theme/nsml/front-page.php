<?php
/**
 * Homepage template. Static markup mirrors index.html exactly, except the
 * "From the Frontlines" news section which pulls the 3 latest posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$nsml_home_news = get_posts(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 3,
	)
);
?>

<style>
	.hero { min-height: 100dvh; display: flex; align-items: center; position: relative; overflow: hidden; background: var(--navy); padding: 8rem 1.5rem 5rem; }
	.hero-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: 72% center; opacity: 0.55; will-change: transform; }
	.hero-overlay { position: absolute; inset: 0; background: linear-gradient(to right, rgba(13,31,60,1) 0%, rgba(13,31,60,0.97) 30%, rgba(13,31,60,0.7) 55%, rgba(13,31,60,0.2) 80%, rgba(13,31,60,0.08) 100%), linear-gradient(to bottom, rgba(13,31,60,0.6) 0%, transparent 20%, transparent 80%, rgba(13,31,60,0.8) 100%); }
	.hero-inner { position: relative; z-index: 2; max-width: 72rem; width: 100%; margin: 0 auto; }
	.hero-content { max-width: 52rem; }
	.hero-eyebrow { margin-bottom: 2rem; opacity: 0; transform: translateY(1rem); }
	.hero-h1 { font-family: var(--font-d); font-size: clamp(2.25rem, 8vw, 6rem); font-weight: 800; line-height: 1.05; letter-spacing: -0.035em; color: #ffffff; margin-bottom: 1.75rem; }
	.hero-line { display: block; opacity: 0; transform: translateY(2.25rem); }
	.hero-h1 .hi { color: var(--green); }
	.hero-p { font-size: clamp(1rem, 1.6vw, 1.15rem); color: rgba(255,255,255,0.68); max-width: 36rem; margin-bottom: 2.75rem; line-height: 1.75; opacity: 0; transform: translateY(1.5rem); }
	.hero-btns { display: flex; gap: 0.875rem; flex-wrap: wrap; opacity: 0; transform: translateY(1.5rem); }
	.hero-trust { margin-top: 3.5rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; opacity: 0; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); }
	.trust-label { font-size: 0.6875rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-right: 0.25rem; }
	.trust-item { font-size: 0.8125rem; color: rgba(255,255,255,0.5); font-weight: 500; }
	.trust-sep { width: 3px; height: 3px; border-radius: 50%; background: rgba(255,255,255,0.18); flex-shrink: 0; }
	.teaser { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }
	.teaser-text .section-tag { margin-bottom: 1.5rem; }
	.teaser-text .sec-h2 { margin-bottom: 1.25rem; }
	.teaser-text p { font-size: 1.0625rem; color: var(--text-sub); line-height: 1.8; margin-bottom: 2.25rem; max-width: 34rem; }
	.teaser-metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem; }
	.metric { padding: 0.25rem; background: #ffffff; border: 1.5px solid var(--border); border-radius: var(--r-lg); transition: border-color 0.35s var(--ease), box-shadow 0.35s var(--ease); }
	.metric:hover { border-color: var(--green); box-shadow: 0 4px 20px rgba(26,184,60,0.1); }
	.metric-inner { background: var(--surface); border-radius: calc(var(--r-lg) - 0.25rem); padding: 1.625rem 1.375rem; }
	.metric-val { font-family: var(--font-d); font-size: 2.25rem; font-weight: 800; letter-spacing: -0.04em; color: var(--navy); line-height: 1; margin-bottom: 0.5rem; }
	.metric-accent { color: var(--green); font-size: 0.7em; }
	.metric-lbl { font-size: 0.8125rem; color: var(--text-muted); line-height: 1.45; }
	.feat-section { padding: 6rem 1.5rem; background: var(--surface); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
	.feat-section-inner { max-width: 72rem; margin: 0 auto; }
	.feat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
	.feat-grid .prop-card-lg { grid-column: span 2; display: flex; flex-direction: column; }
	.prop-card { background: #ffffff; border: 1.5px solid var(--border); border-radius: var(--r-xl); overflow: hidden; position: relative; transition: border-color 0.4s var(--ease), box-shadow 0.4s var(--ease), transform 0.4s var(--ease); }
	.prop-card:hover { border-color: var(--green); box-shadow: 0 8px 32px rgba(26,184,60,0.12); transform: translateY(-3px); }
	.prop-img-wrap { overflow: hidden; height: 200px; }
	.prop-card-lg .prop-img-wrap { flex: 1; height: auto; min-height: 280px; }
	.prop-img { width: 100%; height: 100%; object-fit: cover; filter: contrast(1.06) brightness(0.97); transition: transform 0.75s var(--ease), filter 0.75s var(--ease); display: block; }
	.prop-card:hover .prop-img { transform: scale(1.05); filter: saturate(1.1) contrast(1.08); }
	.prop-body { padding: 1.5rem; }
	.prop-tag { display: inline-block; background: var(--accent-glow); color: var(--green); border: 1px solid var(--accent-ring); border-radius: 9999px; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; padding: 0.25rem 0.875rem; margin-bottom: 0.75rem; }
	.prop-title { font-family: var(--font-d); font-size: 1rem; font-weight: 700; letter-spacing: -0.02em; line-height: 1.25; color: var(--navy); margin-bottom: 0.5rem; }
	.prop-card-lg .prop-title { font-size: 1.25rem; }
	.prop-desc { font-size: 0.8125rem; color: var(--text-sub); line-height: 1.65; margin-bottom: 1.25rem; }
	.prop-badge { position: absolute; top: 1rem; right: 1rem; background: var(--green); color: #ffffff; border-radius: 9999px; font-size: 0.5625rem; font-weight: 700; letter-spacing: 0.09em; text-transform: uppercase; padding: 0.3rem 0.875rem; z-index: 2; }
	.cta-band { background: var(--navy); padding: 0; position: relative; overflow: hidden; }
	.cta-band-bg { position: absolute; inset: 0; background: url('<?php echo esc_url( NSML_THEME_URI . '/assets/images/brand-hero.jpg' ); ?>') 70% center / cover no-repeat; opacity: 0.12; }
	.cta-band-inner { max-width: 72rem; margin: 0 auto; padding: 6rem 1.5rem; display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 3rem; position: relative; }
	.cta-band h2 { font-family: var(--font-d); font-size: clamp(2rem, 4vw, 3.25rem); font-weight: 800; letter-spacing: -0.035em; line-height: 1.1; color: #ffffff; }
	.cta-band h2 em { font-style: normal; color: var(--green); }
	.cta-band p { font-size: 1.0625rem; color: rgba(255,255,255,0.55); margin-top: 0.875rem; max-width: 38rem; line-height: 1.7; }
	.cta-band-btns { display: flex; flex-direction: column; gap: 0.75rem; align-items: flex-end; flex-shrink: 0; }
	.btn-white { display: inline-flex; align-items: center; gap: 0.75rem; background: #ffffff; color: var(--navy); font-family: var(--font-b); font-size: 0.9375rem; font-weight: 700; text-decoration: none; border-radius: 9999px; padding: 0.9375rem 1.375rem 0.9375rem 1.875rem; transition: all 0.4s var(--ease); white-space: nowrap; }
	.btn-white:hover { background: var(--green); color: #ffffff; transform: scale(0.97); }
	.btn-white:hover .btn-icon { transform: translate(2px,-2px) scale(1.1); }
	.btn-ghost-white { display: inline-flex; align-items: center; gap: 0.625rem; background: transparent; color: rgba(255,255,255,0.6); font-family: var(--font-b); font-size: 0.875rem; font-weight: 500; text-decoration: none; border-radius: 9999px; border: 1px solid rgba(255,255,255,0.2); padding: 0.75rem 1.5rem; transition: all 0.4s var(--ease); white-space: nowrap; }
	.btn-ghost-white:hover { color: #ffffff; border-color: rgba(255,255,255,0.5); }
	@media (max-width: 1024px) {
		.teaser { grid-template-columns: 1fr; gap: 3rem; }
		.feat-grid { grid-template-columns: 1fr 1fr; }
		.feat-grid .prop-card-lg { grid-column: span 2; }
		.cta-band-inner { grid-template-columns: 1fr; }
		.cta-band-btns { flex-direction: row; align-items: flex-start; }
	}
	@media (max-width: 768px) {
		.hero { align-items: flex-start; padding: 6.5rem 1.25rem 3.5rem; }
		.hero-bg { background-position: 80% center; }
		.hero-content { max-width: 100%; }
		.hero-trust { gap: 0.625rem; }
		.feat-grid { grid-template-columns: 1fr; }
		.feat-grid .prop-card-lg { grid-column: span 1; }
		.prop-card-lg .prop-img-wrap { height: 200px; }
		.teaser-metrics { grid-template-columns: 1fr 1fr; }
		.cta-band-inner { padding: 4rem 1.25rem; grid-template-columns: 1fr; gap: 2rem; }
		.cta-band-btns { flex-direction: column; align-items: flex-start; }
		.cta-band h2 { font-size: clamp(1.625rem, 7vw, 2.5rem); }
	}
	.home-news-card { background: #ffffff; border: 1.5px solid var(--border); border-radius: var(--r-lg); overflow: hidden; display: block; text-decoration: none; transition: border-color 0.4s var(--ease), box-shadow 0.4s var(--ease), transform 0.4s var(--ease); }
	.home-news-card:hover { border-color: var(--green); box-shadow: 0 6px 24px rgba(26,184,60,0.1); transform: translateY(-3px); }
	.home-news-card:hover img { transform: scale(1.05) !important; filter: saturate(1.1) contrast(1.08) !important; }
	@media (max-width: 768px) { #homeNewsGrid { grid-template-columns: 1fr !important; } }
	@media (max-width: 480px) {
		.teaser-metrics { grid-template-columns: 1fr 1fr; }
		.metric-val { font-size: 1.75rem; }
		.hero-trust { display: none; }
		.feat-section { padding: 3.5rem 1.25rem; }
	}
</style>

<section class="hero">
	<video class="hero-bg" id="heroBg" autoplay muted loop playsinline poster="<?php echo esc_url( NSML_THEME_URI . '/assets/images/brand-hero.jpg' ); ?>">
		<source src="<?php echo esc_url( NSML_THEME_URI . '/assets/videos/hero-video.mp4' ); ?>" type="video/mp4">
	</video>
	<div class="hero-overlay"></div>
	<div class="hero-inner">
		<div class="hero-content">
			<div class="eyebrow-pill hero-eyebrow" id="heroEyebrow">
				<span class="eyebrow-dot"></span>
				<?php esc_html_e( "World Athletics Certified — Africa's Strongest Marathon Brand 2025", 'nsml' ); ?>
			</div>
			<h1 class="hero-h1">
				<span class="hero-line" id="hl1"><?php esc_html_e( "Africa's Strongest", 'nsml' ); ?></span>
				<span class="hero-line" id="hl2"><?php esc_html_e( 'Force in', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Sports', 'nsml' ); ?></span></span>
			</h1>
			<p class="hero-p" id="heroP">
				<?php esc_html_e( 'Sports marketing, brand management and procurement that elevates athletes, brands and communities from grassroots to global recognition.', 'nsml' ); ?>
			</p>
			<div class="hero-btns" id="heroBtns">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-fill">
					<span><?php esc_html_e( 'Partner With Us', 'nsml' ); ?></span>
					<span class="btn-icon">&#8599;</span>
				</a>
				<a href="<?php echo esc_url( get_post_type_archive_link( NSML_PROPERTY_CPT ) ?: home_url( '/properties/' ) ); ?>" class="btn btn-outline-white"><?php esc_html_e( 'Explore Properties', 'nsml' ); ?></a>
			</div>
			<div class="hero-trust" id="heroTrust">
				<span class="trust-label"><?php esc_html_e( 'Trusted by', 'nsml' ); ?></span>
				<span class="trust-item">World Athletics</span><span class="trust-sep"></span>
				<span class="trust-item">CAA</span><span class="trust-sep"></span>
				<span class="trust-item">NFF</span><span class="trust-sep"></span>
				<span class="trust-item">AFN</span><span class="trust-sep"></span>
				<span class="trust-item">NPFL</span><span class="trust-sep"></span>
				<span class="trust-item">NNL</span><span class="trust-sep"></span>
				<span class="trust-item">IAU</span>
			</div>
		</div>
	</div>
</section>

<div class="ticker">
	<div class="ticker-track">
		<div class="ticker-set">
			<span class="tick hi">Lagos City Marathon</span><span class="tick-dot"></span>
			<span class="tick">440,000+ Participants</span><span class="tick-dot"></span>
			<span class="tick hi">World Athletics Certified</span><span class="tick-dot"></span>
			<span class="tick">Africa's Strongest Marathon Brand 2025</span><span class="tick-dot"></span>
			<span class="tick hi">Abuja City Half Marathon</span><span class="tick-dot"></span>
			<span class="tick">Copa Lagos Beach Soccer</span><span class="tick-dot"></span>
			<span class="tick hi">Abeokuta 10KM Race</span><span class="tick-dot"></span>
			<span class="tick">Enugu City Marathon</span><span class="tick-dot"></span>
			<span class="tick hi">Stormers Sports Club</span><span class="tick-dot"></span>
			<span class="tick">IAU African Championship</span><span class="tick-dot"></span>
			<span class="tick hi">National Sports Festival</span><span class="tick-dot"></span>
		</div>
		<div class="ticker-set" aria-hidden="true">
			<span class="tick hi">Lagos City Marathon</span><span class="tick-dot"></span>
			<span class="tick">440,000+ Participants</span><span class="tick-dot"></span>
			<span class="tick hi">World Athletics Certified</span><span class="tick-dot"></span>
			<span class="tick">Africa's Strongest Marathon Brand 2025</span><span class="tick-dot"></span>
			<span class="tick hi">Abuja City Half Marathon</span><span class="tick-dot"></span>
			<span class="tick">Copa Lagos Beach Soccer</span><span class="tick-dot"></span>
			<span class="tick hi">Abeokuta 10KM Race</span><span class="tick-dot"></span>
			<span class="tick">Enugu City Marathon</span><span class="tick-dot"></span>
			<span class="tick hi">Stormers Sports Club</span><span class="tick-dot"></span>
			<span class="tick">IAU African Championship</span><span class="tick-dot"></span>
			<span class="tick hi">National Sports Festival</span><span class="tick-dot"></span>
		</div>
	</div>
</div>

<div class="photo-slider">
	<div class="photo-slider-track">
		<div class="photo-slider-set">
			<?php for ( $nsml_i = 1; $nsml_i <= 14; $nsml_i++ ) : ?>
				<div class="photo-slide"><img src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/slider/slider-' . sprintf( '%02d', $nsml_i ) . '.jpg' ); ?>" alt="NSML event moment" loading="lazy"></div>
			<?php endfor; ?>
		</div>
		<div class="photo-slider-set" aria-hidden="true">
			<?php for ( $nsml_i = 1; $nsml_i <= 14; $nsml_i++ ) : ?>
				<div class="photo-slide"><img src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/slider/slider-' . sprintf( '%02d', $nsml_i ) . '.jpg' ); ?>" alt="" loading="lazy"></div>
			<?php endfor; ?>
		</div>
	</div>
</div>

<div class="section-wrap">
	<div class="teaser">
		<div class="teaser-text">
			<div class="section-tag"><?php esc_html_e( 'Who We Are', 'nsml' ); ?></div>
			<h2 class="sec-h2"><?php esc_html_e( 'Transforming Sports', 'nsml' ); ?><br><?php esc_html_e( 'Across', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Africa', 'nsml' ); ?></span></h2>
			<p><?php esc_html_e( 'We are a sports marketing, brand management, and procurement agency that thrives on dynamism and innovation. With deep relationships with World Athletics, NFF, CAA, and AFN, we promote sporting activities from grassroots to international fame — creating lasting legacies that inspire generations.', 'nsml' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="btn btn-fill btn-sm">
				<span><?php esc_html_e( 'Our Story', 'nsml' ); ?></span>
				<span class="btn-icon">&#8599;</span>
			</a>
		</div>
		<div class="teaser-metrics">
			<div class="metric"><div class="metric-inner"><div class="metric-val">700K<span class="metric-accent">+</span></div><div class="metric-lbl"><?php esc_html_e( 'Participants across all events', 'nsml' ); ?></div></div></div>
			<div class="metric"><div class="metric-inner"><div class="metric-val">&#8358;4B<span class="metric-accent">+</span></div><div class="metric-lbl"><?php esc_html_e( 'Raised annually from private sector', 'nsml' ); ?></div></div></div>
			<div class="metric"><div class="metric-inner"><div class="metric-val">12<span class="metric-accent">+</span></div><div class="metric-lbl"><?php esc_html_e( 'Major sporting properties managed', 'nsml' ); ?></div></div></div>
			<div class="metric"><div class="metric-inner"><div class="metric-val">54</div><div class="metric-lbl"><?php esc_html_e( 'African nations reached through events', 'nsml' ); ?></div></div></div>
		</div>
	</div>
</div>

<div class="stats-strip">
	<div class="stats-inner">
		<div class="stat" data-count="11" data-suffix="+ Yrs">
			<div class="stat-val"><span class="count-num">0</span><span class="stat-suffix">+</span></div>
			<div class="stat-lbl"><?php esc_html_e( 'Years delivering world-class sports events', 'nsml' ); ?></div>
		</div>
		<div class="stat" data-count="70" data-suffix="+">
			<div class="stat-val"><span class="count-num">0</span><span class="stat-suffix">+</span></div>
			<div class="stat-lbl"><?php esc_html_e( 'National and global brand partners', 'nsml' ); ?></div>
		</div>
		<div class="stat" data-count="20000" data-suffix="+">
			<div class="stat-val"><span class="count-num">0</span><span class="stat-suffix">+</span></div>
			<div class="stat-lbl"><?php esc_html_e( 'Athletes at single-event peak', 'nsml' ); ?></div>
		</div>
		<div class="stat" data-count="8" data-suffix=" Cities">
			<div class="stat-val"><span class="count-num">0</span><span class="stat-suffix">&nbsp;Cities</span></div>
			<div class="stat-lbl"><?php esc_html_e( 'Active event footprint across Nigeria', 'nsml' ); ?></div>
		</div>
	</div>
</div>

<div class="feat-section">
	<div class="feat-section-inner">
		<div class="split-header">
			<div>
				<div class="section-tag"><?php esc_html_e( 'Featured Properties', 'nsml' ); ?></div>
				<h2 class="sec-h2"><?php esc_html_e( 'Flagship', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Events', 'nsml' ); ?></span></h2>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( NSML_PROPERTY_CPT ) ?: home_url( '/properties/' ) ); ?>" class="btn btn-outline btn-sm"><?php esc_html_e( 'View All', 'nsml' ); ?></a>
		</div>
		<div class="feat-grid">
			<div class="prop-card prop-card-lg" data-reveal>
				<span class="prop-badge">World Athletics</span>
				<div class="prop-img-wrap">
					<img class="prop-img" src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/events/lagos/lagos-card.jpg' ); ?>" alt="Access Bank Lagos City Marathon runners" loading="lazy">
				</div>
				<div class="prop-body">
					<div class="prop-tag">Flagship Property</div>
					<div class="prop-title">Access Bank Lagos City Marathon</div>
					<div class="prop-desc">Africa's Strongest Marathon Brand 2025 — a World Athletics Global Certification. 10th anniversary edition delivered in 2025 with 440,000+ participants since 2016, raising over ₦3 billion annually from the private sector.</div>
					<a href="<?php echo esc_url( home_url( '/properties/lagos-marathon/' ) ); ?>" class="btn btn-fill btn-sm">
						<span><?php esc_html_e( 'View Property', 'nsml' ); ?></span>
						<span class="btn-icon">&#8599;</span>
					</a>
				</div>
			</div>

			<div style="display:flex;flex-direction:column;gap:1.25rem;">
				<div class="prop-card" data-reveal>
					<div class="prop-img-wrap">
						<img class="prop-img" src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/events/abeokuta-hero.jpg' ); ?>" alt="Abeokuta 10KM Race" loading="lazy">
					</div>
					<div class="prop-body">
						<div class="prop-tag">Heritage Race</div>
						<div class="prop-title">Abeokuta 10KM Race</div>
						<div class="prop-desc">120,000+ cumulative participants since 2019. Long-term commitments from Lotus Bank, Access Bank, Airtel, Rite Foods, JAC Motors and more.</div>
						<a href="<?php echo esc_url( home_url( '/properties/abeokuta-race/' ) ); ?>" class="btn btn-fill btn-sm">
							<span><?php esc_html_e( 'View Property', 'nsml' ); ?></span>
							<span class="btn-icon">&#8599;</span>
						</a>
					</div>
				</div>

				<div class="prop-card" data-reveal>
					<div class="prop-img-wrap">
						<img class="prop-img" src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/events/abuja-hero.jpg' ); ?>" alt="PremiumTrust Bank Abuja City Half Marathon" loading="lazy">
					</div>
					<div class="prop-body">
						<div class="prop-tag">International</div>
						<div class="prop-title">PremiumTrust Bank Abuja City Half Marathon</div>
						<div class="prop-desc">50,000+ participants. First World Athletics-supervised half marathon in Nigeria's capital. Next edition November 2026.</div>
						<a href="<?php echo esc_url( home_url( '/properties/abuja-marathon/' ) ); ?>" class="btn btn-fill btn-sm">
							<span><?php esc_html_e( 'View Property', 'nsml' ); ?></span>
							<span class="btn-icon">&#8599;</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="section-wrap">
	<div class="split-header">
		<div>
			<div class="section-tag"><?php esc_html_e( 'Latest News', 'nsml' ); ?></div>
			<h2 class="sec-h2"><?php esc_html_e( 'From the', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Frontlines', 'nsml' ); ?></span></h2>
		</div>
		<a href="<?php echo esc_url( home_url( '/news/' ) ); ?>" class="btn btn-outline btn-sm"><?php esc_html_e( 'All News', 'nsml' ); ?></a>
	</div>
	<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;" id="homeNewsGrid">
		<?php foreach ( $nsml_home_news as $nsml_news_post ) : ?>
			<a href="<?php echo esc_url( get_permalink( $nsml_news_post ) ); ?>" class="home-news-card" data-reveal>
				<div style="overflow:hidden;height:190px;border-radius:var(--r-lg) var(--r-lg) 0 0;">
					<?php if ( has_post_thumbnail( $nsml_news_post ) ) : ?>
						<?php
						echo get_the_post_thumbnail(
							$nsml_news_post,
							'nsml-card',
							array(
								'loading' => 'lazy',
								'style'   => 'width:100%;height:100%;object-fit:cover;display:block;filter:contrast(1.06) brightness(0.97);transition:transform 0.7s cubic-bezier(0.32,0.72,0,1),filter 0.7s cubic-bezier(0.32,0.72,0,1);',
							)
						);
						?>
					<?php else : ?>
						<img src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/logo.png' ); ?>" alt="<?php echo esc_attr( get_the_title( $nsml_news_post ) ); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
					<?php endif; ?>
				</div>
				<div style="padding:1.375rem;">
					<div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.875rem;">
						<span style="background:var(--accent-glow);color:var(--green-dark);border:1px solid var(--accent-ring);border-radius:9999px;font-size:0.5875rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:0.2rem 0.75rem;"><?php esc_html_e( 'News', 'nsml' ); ?></span>
						<span style="font-size:0.8125rem;color:var(--text-muted);"><?php echo esc_html( get_the_date( 'F Y', $nsml_news_post ) ); ?></span>
					</div>
					<div style="font-family:var(--font-d);font-size:1rem;font-weight:700;letter-spacing:-0.02em;line-height:1.3;color:var(--navy);margin-bottom:0.625rem;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;"><?php echo esc_html( get_the_title( $nsml_news_post ) ); ?></div>
					<span style="font-size:0.875rem;font-weight:600;color:var(--green-dark);display:inline-flex;align-items:center;gap:0.375rem;transition:gap 0.3s;"><?php esc_html_e( 'Read More', 'nsml' ); ?> <span style="transition:transform 0.3s;">&#8599;</span></span>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</div>

<div class="cta-band">
	<div class="cta-band-bg"></div>
	<div class="cta-band-inner">
		<div data-reveal>
			<h2><?php esc_html_e( 'Ready to build something', 'nsml' ); ?> <em><?php esc_html_e( 'legendary', 'nsml' ); ?></em> <?php esc_html_e( 'together?', 'nsml' ); ?></h2>
			<p><?php esc_html_e( 'When you partner with NSML, your brand connects with millions of athletes, fans, and communities across Africa. More than visibility — your brand stands alongside inspiring stories of people pushing limits.', 'nsml' ); ?></p>
		</div>
		<div class="cta-band-btns" data-reveal>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn-white">
				<span><?php esc_html_e( 'Start a Conversation', 'nsml' ); ?></span>
				<span class="btn-icon">&#8599;</span>
			</a>
			<a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" class="btn-ghost-white"><?php esc_html_e( 'Our Services', 'nsml' ); ?></a>
		</div>
	</div>
</div>

<?php get_footer(); ?>
