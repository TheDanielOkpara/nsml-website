<?php
/**
 * Blog index ("News"). Used for the default posts index, and for whatever
 * page is set as Settings > Reading's "Posts page" (see README's "Pretty
 * /news/ URL" step) — WordPress always routes the posts index through
 * home.php in preference to index.php when it exists.
 *
 * Static markup mirrors news.html exactly: one featured article up top,
 * then a grid of standard cards. How visitors get past the first page is
 * an Appearance > Theme Settings > News page choice (nsml_news_index_query()
 * in inc/theme-settings.php sets posts_per_page accordingly on the main
 * query before this template ever runs):
 *
 * - "Load More" (the original static site's behavior): one big batch of
 *   posts, with everything past the first 6 pre-rendered but hidden and
 *   revealed by a button — a fixed client-side reveal, not real paging.
 * - "Numbered pages": real WordPress pagination (next_posts_link()-style
 *   /page/2/ URLs), 6 posts per page, via nsml_render_news_pagination().
 *
 * The category filter pills are cosmetic in the original static page too
 * (no real per-category filtering), so that's preserved as-is rather than
 * wired to a taxonomy that doesn't exist in the demo content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$nsml_numbered_mode  = ( 'numbered' === nsml_theme_setting( 'news_pagination_style' ) );
$nsml_on_first_page  = ! is_paged();
$nsml_featured_post  = null;
$nsml_visible_posts  = array();
$nsml_extra_posts    = array();

if ( have_posts() ) {
	$nsml_index = 0;
	while ( have_posts() ) {
		the_post();
		if ( 0 === $nsml_index && $nsml_on_first_page ) {
			$nsml_featured_post = get_post();
		} elseif ( $nsml_numbered_mode || $nsml_index < 6 ) {
			$nsml_visible_posts[] = get_post();
		} else {
			$nsml_extra_posts[] = get_post();
		}
		$nsml_index++;
	}
	wp_reset_postdata();
}
?>

<style>
	/* ── NEWS ARTICLE CARDS ───────────────── */
	.news-grid {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		gap: 1.5rem;
	}

	/* Featured post — spans full row */
	.article-featured {
		grid-column: 1 / -1;
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 0;
		background: #ffffff;
		border: 1.5px solid var(--border);
		border-radius: var(--r-xl);
		overflow: hidden;
		transition: border-color 0.4s var(--ease), box-shadow 0.4s var(--ease);
	}

	.article-featured:hover {
		border-color: var(--green);
		box-shadow: 0 8px 40px rgba(26,184,60,0.1);
	}

	.article-featured .article-img-wrap {
		height: 100%;
		min-height: 340px;
		max-height: 460px;
	}

	.article-featured .article-body {
		padding: 3rem;
		display: flex;
		flex-direction: column;
		justify-content: center;
	}

	.article-featured .article-title {
		font-size: clamp(1.5rem, 2.5vw, 2rem);
		display: -webkit-box;
		-webkit-line-clamp: 3;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}

	.article-featured .article-excerpt {
		display: -webkit-box;
		-webkit-line-clamp: 3;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}

	/* Standard article card */
	.article-card {
		background: #ffffff;
		border: 1.5px solid var(--border);
		border-radius: var(--r-xl);
		overflow: hidden;
		display: flex;
		flex-direction: column;
		transition: border-color 0.4s var(--ease), box-shadow 0.4s var(--ease), transform 0.4s var(--ease);
	}

	.article-card:hover {
		border-color: var(--green);
		box-shadow: 0 6px 28px rgba(26,184,60,0.1);
		transform: translateY(-3px);
	}

	/* Shared image wrapper */
	.article-img-wrap {
		overflow: hidden;
		height: 220px;
		flex-shrink: 0;
	}

	.article-img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
		filter: contrast(1.06) brightness(0.97);
		transition: transform 0.75s var(--ease), filter 0.75s var(--ease);
	}

	.article-card:hover .article-img,
	.article-featured:hover .article-img {
		transform: scale(1.05);
		filter: saturate(1.1) contrast(1.08);
	}

	/* Shared body */
	.article-body {
		padding: 1.75rem;
		display: flex;
		flex-direction: column;
		flex: 1;
	}

	.article-meta {
		display: flex;
		align-items: center;
		gap: 0.75rem;
		margin-bottom: 1rem;
		flex-wrap: wrap;
	}

	.article-cat {
		display: inline-block;
		background: var(--accent-glow);
		color: var(--green);
		border: 1px solid var(--accent-ring);
		border-radius: 9999px;
		font-size: 0.6rem;
		font-weight: 700;
		letter-spacing: 0.12em;
		text-transform: uppercase;
		padding: 0.25rem 0.875rem;
	}

	.article-date {
		font-size: 0.8125rem;
		color: var(--text-muted);
	}

	.article-title {
		font-family: var(--font-d);
		font-size: 1.1875rem;
		font-weight: 700;
		letter-spacing: -0.025em;
		line-height: 1.25;
		color: var(--navy);
		margin-bottom: 0.75rem;
		text-decoration: none;
		display: block;
		transition: color 0.3s var(--ease);
	}

	.article-title:hover { color: var(--green-dark); }

	.article-excerpt {
		font-size: 0.9375rem;
		color: var(--text-sub);
		line-height: 1.7;
		flex: 1;
		margin-bottom: 1.5rem;
	}

	.article-read-more {
		display: inline-flex;
		align-items: center;
		gap: 0.5rem;
		font-size: 0.875rem;
		font-weight: 600;
		color: var(--green-dark);
		text-decoration: none;
		transition: gap 0.3s var(--ease), color 0.3s var(--ease);
		margin-top: auto;
	}

	.article-read-more:hover {
		gap: 0.875rem;
		color: var(--navy);
	}

	.article-read-more-arrow {
		font-size: 1rem;
		transition: transform 0.3s var(--ease);
	}

	.article-read-more:hover .article-read-more-arrow {
		transform: translate(3px, -2px);
	}

	/* ── CATEGORIES FILTER ────────────────── */
	.filter-bar {
		display: flex;
		align-items: center;
		gap: 0.625rem;
		flex-wrap: wrap;
		margin-bottom: 3rem;
	}

	.filter-btn {
		background: transparent;
		border: 1.5px solid var(--border);
		border-radius: 9999px;
		padding: 0.5rem 1.25rem;
		font-family: var(--font-b);
		font-size: 0.8125rem;
		font-weight: 600;
		color: var(--text-sub);
		cursor: pointer;
		transition: all 0.3s var(--ease);
	}

	.filter-btn:hover,
	.filter-btn.active {
		background: var(--navy);
		border-color: var(--navy);
		color: #ffffff;
	}

	.filter-btn.active-green {
		background: var(--green);
		border-color: var(--green);
		color: #ffffff;
	}

	/* ── NEWSLETTER BAND ──────────────────── */
	.newsletter-band {
		background: var(--surface);
		border-top: 1px solid var(--border);
		border-bottom: 1px solid var(--border);
		padding: 5rem 1.5rem;
	}

	.newsletter-inner {
		max-width: 48rem;
		margin: 0 auto;
		text-align: center;
	}

	.newsletter-inner .section-tag {
		justify-content: center;
		margin-bottom: 1.5rem;
	}

	.newsletter-h2 {
		font-family: var(--font-d);
		font-size: clamp(1.875rem, 3.5vw, 2.75rem);
		font-weight: 800;
		letter-spacing: -0.03em;
		line-height: 1.1;
		color: var(--navy);
		margin-bottom: 1rem;
	}

	.newsletter-h2 .hi { color: var(--green); }

	.newsletter-p {
		font-size: 1.0625rem;
		color: var(--text-sub);
		line-height: 1.7;
		margin-bottom: 2.5rem;
	}

	.newsletter-form {
		display: flex;
		gap: 0.75rem;
		max-width: 36rem;
		margin: 0 auto;
	}

	.newsletter-input {
		flex: 1;
		background: #ffffff;
		border: 1.5px solid var(--border);
		border-radius: 9999px;
		padding: 0.875rem 1.5rem;
		font-family: var(--font-b);
		font-size: 0.9375rem;
		color: var(--navy);
		outline: none;
		transition: border-color 0.3s var(--ease);
	}

	.newsletter-input::placeholder { color: var(--text-muted); }

	.newsletter-input:focus {
		border-color: var(--green);
		box-shadow: 0 0 0 3px rgba(26,184,60,0.12);
	}

	.newsletter-disclaimer {
		font-size: 0.8125rem;
		color: var(--text-muted);
		margin-top: 1rem;
	}

	/* ── RESPONSIVE ───────────────────────── */
	@media (max-width: 1024px) {
		.news-grid { grid-template-columns: repeat(2, 1fr); }
		.article-featured { grid-template-columns: 1fr; }
		.article-featured .article-img-wrap { min-height: 240px; }
	}

	@media (max-width: 768px) {
		.news-grid { grid-template-columns: 1fr; }
		.article-featured { grid-column: 1; }
		.article-featured .article-body { padding: 1.5rem; }
		.filter-bar { gap: 0.5rem; }
		.filter-btn { padding: 0.4375rem 1rem; font-size: 0.75rem; }
		.newsletter-form { flex-direction: column; gap: 0.75rem; }
		.newsletter-input { border-radius: var(--r-lg); }
		.newsletter-band { padding: 3.5rem 1.25rem; }
	}

	@media (max-width: 480px) {
		.article-img-wrap { height: 180px; }
	}
</style>

<div class="page-hero">
	<div class="page-hero-bg" style="background-image:url('https://images.unsplash.com/photo-1552674605-db5fecabfe68?w=1920&q=80&auto=format&fit=crop')"></div>
	<div class="page-hero-overlay"></div>
	<div class="page-hero-inner">
		<div class="page-hero-label"><?php esc_html_e( 'Latest Updates', 'nsml' ); ?></div>
		<h1 class="page-hero-h1"><?php esc_html_e( 'News &', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Insights', 'nsml' ); ?></span></h1>
		<p class="page-hero-p"><?php esc_html_e( 'Event announcements, partnership news, athlete stories, and insights from the frontlines of African sport.', 'nsml' ); ?></p>
	</div>
</div>

<div class="section-wrap">

	<div class="filter-bar">
		<button class="filter-btn active-green" type="button"><?php esc_html_e( 'All', 'nsml' ); ?></button>
		<button class="filter-btn" type="button"><?php esc_html_e( 'Events', 'nsml' ); ?></button>
		<button class="filter-btn" type="button"><?php esc_html_e( 'Partnerships', 'nsml' ); ?></button>
		<button class="filter-btn" type="button"><?php esc_html_e( 'Athletes', 'nsml' ); ?></button>
		<button class="filter-btn" type="button"><?php esc_html_e( 'Industry', 'nsml' ); ?></button>
		<button class="filter-btn" type="button"><?php esc_html_e( 'Club News', 'nsml' ); ?></button>
	</div>

	<?php if ( empty( $nsml_featured_post ) && empty( $nsml_visible_posts ) ) : ?>
		<p><?php esc_html_e( 'No news yet — check back soon.', 'nsml' ); ?></p>
	<?php else : ?>
		<div class="news-grid">

			<?php if ( $nsml_featured_post ) : ?>
				<?php nsml_render_news_featured( $nsml_featured_post ); ?>
			<?php endif; ?>

			<?php foreach ( $nsml_visible_posts as $nsml_post ) : ?>
				<?php nsml_render_news_card( $nsml_post ); ?>
			<?php endforeach; ?>

			<?php foreach ( $nsml_extra_posts as $nsml_post ) : ?>
				<?php nsml_render_news_card( $nsml_post, true ); ?>
			<?php endforeach; ?>

		</div><!-- /news-grid -->

		<?php if ( $nsml_numbered_mode ) : ?>
			<?php nsml_render_news_pagination(); ?>
		<?php elseif ( ! empty( $nsml_extra_posts ) ) : ?>
			<div style="text-align:center;margin-top:3.5rem;" id="loadMoreWrap">
				<button id="loadMoreBtn" type="button" style="display:inline-flex;align-items:center;gap:0.75rem;cursor:pointer;font-size:0.9375rem;font-weight:600;padding:0.9375rem 2rem;border-radius:9999px;border:1.5px solid var(--border);background:transparent;color:var(--navy);transition:all 0.4s cubic-bezier(0.32,0.72,0,1);font-family:var(--font-b);">
					<span id="loadMoreLabel"><?php esc_html_e( 'Load More Articles', 'nsml' ); ?></span>
					<span id="loadMoreSpinner" style="display:none;width:1.125rem;height:1.125rem;border:2px solid var(--border);border-top-color:var(--green);border-radius:50%;animation:spin 0.7s linear infinite;"></span>
				</button>
			</div>
		<?php endif; ?>

	<?php endif; ?>

</div><!-- /section-wrap -->

<!-- NEWSLETTER SIGN-UP -->
<div class="newsletter-band">
	<div class="newsletter-inner" data-reveal>
		<div class="section-tag" style="justify-content:center;"><?php esc_html_e( 'Stay Informed', 'nsml' ); ?></div>
		<h2 class="newsletter-h2"><?php esc_html_e( 'Get', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'NSML News', 'nsml' ); ?></span><br><?php esc_html_e( 'in Your Inbox', 'nsml' ); ?></h2>
		<p class="newsletter-p"><?php esc_html_e( 'Event dates, partnership announcements, and sports industry insights — delivered directly to you. No spam, unsubscribe anytime.', 'nsml' ); ?></p>
		<form class="newsletter-form" id="newsNewsletterForm" novalidate>
			<?php wp_nonce_field( NSML_NEWSLETTER_NONCE_ACTION, 'nsml_newsletter_nonce' ); ?>
			<input type="text" name="nsml_nl_hp" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off">
			<input
				type="email"
				id="newsEmail"
				name="email"
				class="newsletter-input"
				placeholder="<?php esc_attr_e( 'Enter your email address', 'nsml' ); ?>"
				aria-label="<?php esc_attr_e( 'Email address', 'nsml' ); ?>"
				autocomplete="email">
			<button type="submit" id="newsSubBtn" class="btn btn-fill" style="white-space:nowrap;flex-shrink:0;">
				<span id="newsSubLabel"><?php esc_html_e( 'Subscribe', 'nsml' ); ?></span>
				<span class="btn-icon">↗</span>
			</button>
		</form>
		<p class="newsletter-disclaimer" id="newsDisclaimer"><?php esc_html_e( 'By subscribing you agree to receive email communications from NSML. Unsubscribe at any time.', 'nsml' ); ?></p>
		<p id="newsEmailError" style="font-size:0.8125rem;color:#e53935;font-weight:500;margin-top:0.5rem;display:none;"></p>
		<div id="newsSuccess" style="display:none;align-items:center;gap:0.75rem;justify-content:center;padding:1rem;background:rgba(26,184,60,0.08);border:1px solid var(--accent-ring);border-radius:var(--r-lg);margin-top:1rem;">
			<span style="color:var(--green-dark);font-size:1.125rem;">✓</span>
			<span style="font-size:0.9375rem;color:var(--navy);font-weight:600;"><?php esc_html_e( "You're subscribed — thanks!", 'nsml' ); ?></span>
		</div>
	</div>
</div>

<?php get_footer(); ?>

<script>
( function () {
	/* ── SCROLL REVEAL ─────────────────────── */
	var io = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry, i ) {
			if ( ! entry.isIntersecting ) {
				return;
			}
			var el = entry.target;
			setTimeout( function () {
				el.style.transition = 'opacity 0.75s cubic-bezier(0.32,0.72,0,1) ' + ( i * 70 ) + 'ms, transform 0.75s cubic-bezier(0.32,0.72,0,1) ' + ( i * 70 ) + 'ms';
				el.style.opacity = '1';
				el.style.transform = 'none';
			}, 0 );
			io.unobserve( el );
		} );
	}, { threshold: 0.06 } );

	document.querySelectorAll( '[data-reveal]:not(.extra-article)' ).forEach( function ( el ) { io.observe( el ); } );

	/* ── LOAD MORE ─────────────────────────── */
	var loadMoreBtn  = document.getElementById( 'loadMoreBtn' );
	var loadMoreWrap = document.getElementById( 'loadMoreWrap' );
	var extras       = document.querySelectorAll( '.extra-article' );

	if ( loadMoreBtn ) {
		loadMoreBtn.addEventListener( 'click', function () {
			var label   = document.getElementById( 'loadMoreLabel' );
			var spinner = document.getElementById( 'loadMoreSpinner' );

			label.textContent = '<?php echo esc_js( __( 'Loading…', 'nsml' ) ); ?>';
			spinner.style.display = 'block';
			loadMoreBtn.disabled = true;

			setTimeout( function () {
				extras.forEach( function ( el, i ) {
					el.style.display = '';
					setTimeout( function () {
						el.style.transition = 'opacity 0.75s cubic-bezier(0.32,0.72,0,1), transform 0.75s cubic-bezier(0.32,0.72,0,1)';
						el.style.opacity    = '1';
						el.style.transform  = 'none';
					}, i * 120 );
				} );

				setTimeout( function () {
					loadMoreWrap.style.display = 'none';
				}, extras.length * 120 + 400 );
			}, 800 );
		} );
	}

	/* ── CATEGORY FILTER (visual only — no real per-category taxonomy) ── */
	var filterBtns = document.querySelectorAll( '.filter-btn' );

	filterBtns.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			filterBtns.forEach( function ( b ) {
				b.classList.remove( 'active-green', 'active' );
			} );
			btn.classList.add( 'active-green' );
		} );
	} );

	/* ── NEWSLETTER SIGNUP ──────────────────── */
	var newsForm    = document.getElementById( 'newsNewsletterForm' );
	var newsEmailEl = document.getElementById( 'newsEmail' );
	var newsErrEl   = document.getElementById( 'newsEmailError' );
	var newsSuccess = document.getElementById( 'newsSuccess' );
	var newsDiscl   = document.getElementById( 'newsDisclaimer' );
	var newsSubBtn  = document.getElementById( 'newsSubBtn' );

	if ( newsForm ) {
		newsForm.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			newsErrEl.style.display = 'none';

			var formData = new FormData( newsForm );
			formData.append( 'action', 'nsml_subscribe_newsletter' );

			newsSubBtn.disabled = true;

			fetch( '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			} )
				.then( function ( response ) { return response.json(); } )
				.then( function ( data ) {
					if ( data && data.success ) {
						newsForm.style.display    = 'none';
						newsDiscl.style.display   = 'none';
						newsSuccess.style.display = 'flex';
					} else {
						newsErrEl.textContent = ( data && data.data && data.data.message ) || '<?php echo esc_js( __( 'Something went wrong. Please try again.', 'nsml' ) ); ?>';
						newsErrEl.style.display = 'block';
						newsSubBtn.disabled = false;
					}
				} )
				.catch( function () {
					newsErrEl.textContent = '<?php echo esc_js( __( 'Something went wrong. Please try again.', 'nsml' ) ); ?>';
					newsErrEl.style.display = 'block';
					newsSubBtn.disabled = false;
				} );
		} );

		newsEmailEl.addEventListener( 'input', function () {
			newsErrEl.style.display = 'none';
		} );
	}
}() );
</script>
