<?php
/**
 * Single blog article template (post type 'post').
 * Markup mirrors damilola-pedro-bags-award.html — hero, sticky meta bar,
 * article body, tags, author card, prev/next nav, related stories.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$nsml_thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $nsml_thumb_url ) {
		$nsml_thumb_url = NSML_THEME_URI . '/assets/images/logo.png';
	}

	$nsml_categories  = get_the_category();
	$nsml_cat_name    = ! empty( $nsml_categories ) ? $nsml_categories[0]->name : __( 'News', 'nsml' );
	$nsml_word_count  = str_word_count( wp_strip_all_tags( get_the_content() ) );
	$nsml_read_mins   = max( 1, (int) ceil( $nsml_word_count / 200 ) );
	$nsml_prev_post   = get_previous_post();
	$nsml_next_post   = get_next_post();
	$nsml_related     = nsml_get_related_posts( get_the_ID(), 3 );
	$nsml_news_url    = home_url( '/news/' );
	?>

	<style>
		.article-hero { position: relative; height: 70vh; min-height: 480px; max-height: 680px; overflow: hidden; background: var(--navy); }
		.article-hero-img { position: absolute; inset: 0; background-size: cover; background-position: center; background-repeat: no-repeat; filter: saturate(0.85) contrast(1.05); transition: transform 0.1s linear; will-change: transform; }
		.article-hero-overlay { position: absolute; inset: 0; background: linear-gradient( to bottom, rgba(13,31,60,0.2) 0%, rgba(13,31,60,0.15) 30%, rgba(13,31,60,0.55) 65%, rgba(13,31,60,0.95) 100% ); }
		.article-hero-inner { position: absolute; bottom: 0; left: 0; right: 0; padding: 0 1.5rem 3rem; max-width: 72rem; margin: 0 auto; }
		.article-breadcrumb { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: rgba(255,255,255,0.55); margin-bottom: 1.5rem; text-decoration: none; }
		.article-breadcrumb a { color: rgba(255,255,255,0.55); text-decoration: none; transition: color 0.3s var(--ease); }
		.article-breadcrumb a:hover { color: var(--green); }
		.article-breadcrumb-sep { font-size: 0.75rem; opacity: 0.4; }
		.article-hero-cat { display: inline-block; background: var(--green); color: #ffffff; border-radius: 9999px; font-size: 0.625rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; padding: 0.3rem 0.875rem; margin-bottom: 1.25rem; }
		.article-hero-title { font-family: var(--font-d); font-size: clamp(1.75rem, 4vw, 3rem); font-weight: 800; letter-spacing: -0.03em; line-height: 1.15; color: #ffffff; max-width: 52rem; }
		.article-meta-bar { background: #ffffff; border-bottom: 1px solid var(--border); padding: 1.25rem 1.5rem; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 16px rgba(13,31,60,0.06); }
		.article-meta-bar-inner { max-width: 72rem; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap; }
		.article-meta-left { display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
		.meta-item { display: flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; color: var(--text-muted); }
		.meta-item strong { color: var(--navy); font-weight: 600; }
		.meta-divider { width: 4px; height: 4px; border-radius: 50%; background: var(--border-hi); flex-shrink: 0; }
		.share-row { display: flex; align-items: center; gap: 0.5rem; }
		.share-label { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin-right: 0.25rem; }
		.share-btn { width: 2.25rem; height: 2.25rem; border-radius: 50%; border: 1.5px solid var(--border); background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-muted); font-size: 0.875rem; text-decoration: none; transition: all 0.3s var(--ease); }
		.share-btn:hover { border-color: var(--green); color: var(--green-dark); background: var(--accent-glow); }
		.article-content-wrap { padding: 4rem 1.5rem 5rem; max-width: 72rem; margin: 0 auto; display: grid; grid-template-columns: 1fr min(680px, 100%) 1fr; }
		.article-body { grid-column: 2; }
		.article-body .intro { font-size: 1.1875rem; color: var(--navy); line-height: 1.75; font-weight: 500; margin-bottom: 2rem; border-left: 3px solid var(--green); padding-left: 1.25rem; }
		.article-body p { font-size: 1.0625rem; color: var(--text-sub); line-height: 1.85; margin-bottom: 1.75rem; }
		.article-body h2 { font-family: var(--font-d); font-size: 1.625rem; font-weight: 800; letter-spacing: -0.025em; color: var(--navy); line-height: 1.2; margin: 3rem 0 1.25rem; }
		.article-body h3 { font-family: var(--font-d); font-size: 1.25rem; font-weight: 700; letter-spacing: -0.02em; color: var(--navy); line-height: 1.3; margin: 2.25rem 0 1rem; }
		.article-body .article-inline-img { margin: 2.5rem -2rem; border-radius: var(--r-lg); overflow: hidden; }
		.article-body .article-inline-img img { width: 100%; display: block; height: 400px; object-fit: cover; filter: contrast(1.06) brightness(0.97); }
		.article-body .article-inline-img figcaption { font-size: 0.8125rem; color: var(--text-muted); padding: 0.75rem 0 0; line-height: 1.5; }
		.article-body blockquote { margin: 2.5rem 0; padding: 2rem 2rem 2rem 1.75rem; background: var(--surface); border-left: 4px solid var(--green); border-radius: 0 var(--r-md) var(--r-md) 0; }
		.article-body blockquote p { font-family: var(--font-d); font-size: 1.25rem; font-weight: 700; color: var(--navy); line-height: 1.5; letter-spacing: -0.02em; margin-bottom: 0.75rem; }
		.article-body blockquote cite { font-size: 0.875rem; color: var(--green-dark); font-style: normal; font-weight: 600; }
		.article-body ul, .article-body ol { padding-left: 1.5rem; margin-bottom: 1.75rem; }
		.article-body li { font-size: 1.0625rem; color: var(--text-sub); line-height: 1.8; margin-bottom: 0.5rem; padding-left: 0.375rem; }
		.article-body ul li::marker { color: var(--green); }
		.article-body ol li::marker { color: var(--green); font-weight: 700; }
		.article-body .stats-callout { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin: 2.5rem 0; padding: 0; list-style: none; }
		.article-body .stats-callout li { background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--r-lg); padding: 1.5rem; text-align: center; margin: 0; }
		.article-body .stats-callout li::marker { content: ''; }
		.callout-val { font-family: var(--font-d); font-size: 2rem; font-weight: 800; letter-spacing: -0.04em; color: var(--navy); line-height: 1; display: block; margin-bottom: 0.375rem; }
		.callout-val em { font-style: normal; color: var(--green); font-size: 0.65em; }
		.callout-lbl { font-size: 0.8125rem; color: var(--text-muted); line-height: 1.4; display: block; }
		.article-body hr { border: none; border-top: 1px solid var(--border); margin: 3rem 0; }
		.article-tags { display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border); }
		.tag-label { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-muted); }
		.tag-pill { background: var(--surface); border: 1.5px solid var(--border); border-radius: 9999px; font-size: 0.8125rem; color: var(--text-sub); padding: 0.3rem 0.875rem; text-decoration: none; transition: all 0.3s var(--ease); }
		.tag-pill:hover { border-color: var(--green); color: var(--green-dark); background: var(--accent-glow); }
		.author-card { grid-column: 2; margin-top: 3rem; padding: 0.25rem; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--r-xl); }
		.author-card-inner { background: #ffffff; border-radius: calc(var(--r-xl) - 0.25rem); padding: 2rem; display: flex; gap: 1.5rem; align-items: flex-start; }
		.author-avatar { width: 4rem; height: 4rem; border-radius: 50%; background: var(--accent-glow); border: 2px solid var(--accent-ring); display: flex; align-items: center; justify-content: center; font-family: var(--font-d); font-size: 1.125rem; font-weight: 700; color: var(--green-dark); flex-shrink: 0; }
		.author-name { font-family: var(--font-d); font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.25rem; }
		.author-role { font-size: 0.8125rem; color: var(--green-dark); font-weight: 600; margin-bottom: 0.625rem; }
		.author-bio { font-size: 0.9375rem; color: var(--text-sub); line-height: 1.7; }
		.related-section { background: var(--surface); border-top: 1px solid var(--border); padding: 5rem 1.5rem; }
		.related-inner { max-width: 72rem; margin: 0 auto; }
		.related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-top: 2.5rem; }
		.related-card { background: #ffffff; border: 1.5px solid var(--border); border-radius: var(--r-xl); overflow: hidden; text-decoration: none; display: flex; flex-direction: column; transition: border-color 0.4s var(--ease), box-shadow 0.4s var(--ease), transform 0.4s var(--ease); }
		.related-card:hover { border-color: var(--green); box-shadow: 0 6px 28px rgba(26,184,60,0.1); transform: translateY(-3px); }
		.related-img-wrap { overflow: hidden; height: 180px; }
		.related-img { width: 100%; height: 100%; object-fit: cover; display: block; filter: contrast(1.06) brightness(0.97); transition: transform 0.75s var(--ease), filter 0.75s var(--ease); }
		.related-card:hover .related-img { transform: scale(1.05); filter: saturate(1.1) contrast(1.08); }
		.related-body { padding: 1.5rem; flex: 1; }
		.related-cat { display: inline-block; background: var(--accent-glow); color: var(--green); border: 1px solid var(--accent-ring); border-radius: 9999px; font-size: 0.5875rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; padding: 0.2rem 0.75rem; margin-bottom: 0.75rem; }
		.related-title { font-family: var(--font-d); font-size: 1rem; font-weight: 700; letter-spacing: -0.02em; line-height: 1.3; color: var(--navy); margin-bottom: 0.5rem; }
		.related-date { font-size: 0.8125rem; color: var(--text-muted); margin-top: auto; padding-top: 0.875rem; }
		.article-nav { grid-column: 2; margin-top: 3rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
		.article-nav-link { padding: 1.5rem; background: #ffffff; border: 1.5px solid var(--border); border-radius: var(--r-lg); text-decoration: none; transition: border-color 0.35s var(--ease), box-shadow 0.35s var(--ease); display: flex; flex-direction: column; gap: 0.375rem; }
		.article-nav-link:hover { border-color: var(--green); box-shadow: 0 4px 20px rgba(26,184,60,0.1); }
		.article-nav-dir { font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); display: flex; align-items: center; gap: 0.375rem; }
		.article-nav-link.next .article-nav-dir { justify-content: flex-end; }
		.article-nav-title { font-family: var(--font-d); font-size: 0.9375rem; font-weight: 700; color: var(--navy); line-height: 1.3; }
		.article-nav-link.next .article-nav-title { text-align: right; }
		@media (max-width: 1024px) { .related-grid { grid-template-columns: repeat(2, 1fr); } }
		@media (max-width: 768px) {
			.article-hero { height: auto; min-height: 0; max-height: none; padding-top: 5.5rem; display: flex; flex-direction: column; justify-content: flex-end; }
			.article-hero-img { height: 200px; position: relative; flex-shrink: 0; }
			.article-hero-overlay { display: none; }
			.article-hero-inner { position: static; padding: 1.25rem 1.25rem 1.75rem; background: var(--navy); }
			.article-hero-title { font-size: clamp(1.375rem, 5.5vw, 2rem); }
			.article-breadcrumb { margin-bottom: 0.75rem; font-size: 0.75rem; }
			.article-meta-bar { position: static; padding: 1rem 1.25rem; }
			.article-meta-bar-inner { flex-direction: column; align-items: flex-start; gap: 0.875rem; }
			.article-meta-left { gap: 0.875rem; }
			.share-row { width: 100%; justify-content: flex-start; }
			.article-content-wrap { grid-template-columns: 1fr; padding: 2.5rem 1.25rem 4rem; }
			.article-body { grid-column: 1; }
			.author-card { grid-column: 1; }
			.article-nav { grid-column: 1; grid-template-columns: 1fr; }
			.article-body .intro { font-size: 1.0625rem; }
			.article-body p { font-size: 1rem; }
			.article-body h2 { font-size: 1.375rem; }
			.article-body h3 { font-size: 1.125rem; }
			.article-body .article-inline-img { margin: 2rem 0; }
			.article-body .article-inline-img img { height: 220px; }
			.article-body blockquote p { font-size: 1.0625rem; }
			.article-body .stats-callout { grid-template-columns: 1fr 1fr; gap: 0.75rem; }
			.callout-val { font-size: 1.625rem; }
			.related-grid { grid-template-columns: 1fr; }
			.related-section { padding: 3.5rem 1.25rem; }
		}
		@media (max-width: 480px) { .article-body .stats-callout { grid-template-columns: 1fr; } }
	</style>

	<div class="article-hero">
		<div class="article-hero-img" id="heroParallax" style="background-image:url('<?php echo esc_url( $nsml_thumb_url ); ?>')"></div>
		<div class="article-hero-overlay"></div>
		<div class="article-hero-inner">
			<div class="article-breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'nsml' ); ?></a>
				<span class="article-breadcrumb-sep">&rsaquo;</span>
				<a href="<?php echo esc_url( $nsml_news_url ); ?>"><?php esc_html_e( 'News', 'nsml' ); ?></a>
				<span class="article-breadcrumb-sep">&rsaquo;</span>
				<span><?php echo esc_html( get_the_title() ); ?></span>
			</div>
			<span class="article-hero-cat"><?php echo esc_html( $nsml_cat_name ); ?></span>
			<h1 class="article-hero-title"><?php echo esc_html( get_the_title() ); ?></h1>
		</div>
	</div>

	<div class="article-meta-bar">
		<div class="article-meta-bar-inner">
			<div class="article-meta-left">
				<div class="meta-item">
					<span><?php esc_html_e( 'By', 'nsml' ); ?></span>
					<strong><?php echo esc_html( get_the_author() ); ?></strong>
				</div>
				<span class="meta-divider"></span>
				<div class="meta-item"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></div>
				<span class="meta-divider"></span>
				<div class="meta-item">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: number of minutes */
							_n( '%d min read', '%d min read', $nsml_read_mins, 'nsml' ),
							$nsml_read_mins
						)
					);
					?>
				</div>
			</div>
			<div class="share-row">
				<span class="share-label"><?php esc_html_e( 'Share', 'nsml' ); ?></span>
				<a id="shareX" href="#" target="_blank" rel="noopener" class="share-btn" aria-label="Share on X" title="Share on X">&#x1D54F;</a>
				<a id="shareLI" href="#" target="_blank" rel="noopener" class="share-btn" aria-label="Share on LinkedIn" title="Share on LinkedIn">in</a>
				<a id="shareWA" href="#" target="_blank" rel="noopener" class="share-btn" aria-label="Share on WhatsApp" title="Share on WhatsApp">W</a>
				<button class="share-btn" id="copyLink" aria-label="Copy link" title="Copy link">&#x2398;</button>
			</div>
		</div>
	</div>

	<div class="article-content-wrap">
		<div class="article-body">
			<?php the_content(); ?>

			<?php if ( has_tag() ) : ?>
				<div class="article-tags">
					<span class="tag-label"><?php esc_html_e( 'Tags', 'nsml' ); ?></span>
					<?php
					the_tags( '', '', '' );
					?>
				</div>
			<?php endif; ?>
		</div><!-- /article-body -->

		<div class="author-card">
			<div class="author-card-inner">
				<div class="author-avatar">NS</div>
				<div>
					<div class="author-name"><?php esc_html_e( 'NSML Editorial Team', 'nsml' ); ?></div>
					<div class="author-role"><?php esc_html_e( 'Nilayo Sports Management Ltd', 'nsml' ); ?></div>
					<p class="author-bio"><?php esc_html_e( "The NSML Editorial Team covers sporting events, partnership announcements, and industry insights from across Africa's sports management landscape. For press enquiries, contact the NSML Communications team.", 'nsml' ); ?></p>
				</div>
			</div>
		</div>

		<nav class="article-nav" aria-label="<?php esc_attr_e( 'Article navigation', 'nsml' ); ?>">
			<?php if ( $nsml_prev_post ) : ?>
				<a href="<?php echo esc_url( get_permalink( $nsml_prev_post ) ); ?>" class="article-nav-link prev">
					<span class="article-nav-dir">&larr; <?php esc_html_e( 'Previous', 'nsml' ); ?></span>
					<span class="article-nav-title"><?php echo esc_html( get_the_title( $nsml_prev_post ) ); ?></span>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $nsml_news_url ); ?>" class="article-nav-link prev">
					<span class="article-nav-dir">&larr; <?php esc_html_e( 'Previous', 'nsml' ); ?></span>
					<span class="article-nav-title"><?php esc_html_e( 'Browse more NSML news', 'nsml' ); ?></span>
				</a>
			<?php endif; ?>

			<?php if ( $nsml_next_post ) : ?>
				<a href="<?php echo esc_url( get_permalink( $nsml_next_post ) ); ?>" class="article-nav-link next">
					<span class="article-nav-dir"><?php esc_html_e( 'Next', 'nsml' ); ?> &rarr;</span>
					<span class="article-nav-title"><?php echo esc_html( get_the_title( $nsml_next_post ) ); ?></span>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $nsml_news_url ); ?>" class="article-nav-link next">
					<span class="article-nav-dir"><?php esc_html_e( 'Next', 'nsml' ); ?> &rarr;</span>
					<span class="article-nav-title"><?php esc_html_e( 'Browse more NSML news', 'nsml' ); ?></span>
				</a>
			<?php endif; ?>
		</nav>

	</div><!-- /article-content-wrap -->

	<?php if ( ! empty( $nsml_related ) ) : ?>
		<div class="related-section">
			<div class="related-inner">
				<div class="split-header" style="margin-bottom:0">
					<div>
						<div class="section-tag"><?php esc_html_e( 'More to Read', 'nsml' ); ?></div>
						<h2 class="sec-h2"><?php esc_html_e( 'Related', 'nsml' ); ?> <span class="hi"><?php esc_html_e( 'Stories', 'nsml' ); ?></span></h2>
					</div>
					<a href="<?php echo esc_url( $nsml_news_url ); ?>" class="btn btn-outline btn-sm"><?php esc_html_e( 'All News', 'nsml' ); ?></a>
				</div>

				<div class="related-grid" style="margin-top:2.5rem">
					<?php foreach ( $nsml_related as $nsml_related_post ) : ?>
						<a href="<?php echo esc_url( get_permalink( $nsml_related_post ) ); ?>" class="related-card" data-reveal>
							<div class="related-img-wrap">
								<?php if ( has_post_thumbnail( $nsml_related_post ) ) : ?>
									<?php echo get_the_post_thumbnail( $nsml_related_post, 'nsml-card', array( 'class' => 'related-img', 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<img class="related-img" src="<?php echo esc_url( NSML_THEME_URI . '/assets/images/logo.png' ); ?>" alt="<?php echo esc_attr( get_the_title( $nsml_related_post ) ); ?>" loading="lazy">
								<?php endif; ?>
							</div>
							<div class="related-body">
								<span class="related-cat"><?php esc_html_e( 'News', 'nsml' ); ?></span>
								<div class="related-title"><?php echo esc_html( get_the_title( $nsml_related_post ) ); ?></div>
								<div class="related-date"><?php echo esc_html( get_the_date( 'F j, Y', $nsml_related_post ) ); ?></div>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div style="background:var(--navy);padding:5rem 1.5rem;border-top:3px solid var(--green);">
		<div style="max-width:42rem;margin:0 auto;text-align:center;" data-reveal>
			<div class="eyebrow-pill" style="margin:0 auto 1.5rem;"><?php esc_html_e( 'Stay Updated', 'nsml' ); ?></div>
			<h2 style="color:#fff;"><?php esc_html_e( 'Never Miss an', 'nsml' ); ?> <span style="color:var(--green)"><?php esc_html_e( 'NSML Story', 'nsml' ); ?></span></h2>
			<p style="color:rgba(255,255,255,.7);margin:1rem 0 2rem;"><?php esc_html_e( 'Event dates, results, partnership announcements, and insights — delivered straight to your inbox.', 'nsml' ); ?></p>
			<form id="articleNlForm" novalidate>
				<?php wp_nonce_field( NSML_NEWSLETTER_NONCE_ACTION, 'nsml_newsletter_nonce' ); ?>
				<input type="text" name="nsml_nl_hp" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off">
				<div style="display:flex;flex-wrap:wrap;gap:0.75rem;justify-content:center;">
					<input type="email" id="articleNlEmail" name="email" placeholder="<?php esc_attr_e( 'Your email address', 'nsml' ); ?>" style="flex:1;min-width:240px;max-width:320px;">
					<button type="submit" id="articleNlBtn" class="btn btn-fill">
						<span id="articleNlLabel"><?php esc_html_e( 'Subscribe', 'nsml' ); ?></span>
						<span class="btn-icon">↗</span>
					</button>
				</div>
				<p id="articleNlError" style="color:#ff8a8a;margin-top:0.75rem;display:none;"></p>
				<div id="articleNlSuccess" style="display:none;align-items:center;justify-content:center;gap:0.5rem;margin-top:1rem;color:var(--green);">
					<span>✓</span>
					<span><?php esc_html_e( "You're subscribed — thanks!", 'nsml' ); ?></span>
				</div>
			</form>
		</div>
	</div>

<?php endwhile; ?>

<?php get_footer(); ?>

<script>
( function () {
	var form = document.getElementById( 'articleNlForm' );
	if ( ! form ) {
		return;
	}
	var emailInput = document.getElementById( 'articleNlEmail' );
	var errorEl    = document.getElementById( 'articleNlError' );
	var successEl  = document.getElementById( 'articleNlSuccess' );
	var submitBtn  = document.getElementById( 'articleNlBtn' );

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();
		errorEl.style.display = 'none';

		var formData = new FormData( form );
		formData.append( 'action', 'nsml_subscribe_newsletter' );

		submitBtn.disabled = true;

		fetch( '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		} )
			.then( function ( response ) { return response.json(); } )
			.then( function ( data ) {
				if ( data && data.success ) {
					form.querySelector( 'div' ).style.display = 'none';
					successEl.style.display = 'flex';
				} else {
					errorEl.textContent = ( data && data.data && data.data.message ) || '<?php echo esc_js( __( 'Something went wrong. Please try again.', 'nsml' ) ); ?>';
					errorEl.style.display = 'block';
					submitBtn.disabled = false;
				}
			} )
			.catch( function () {
				errorEl.textContent = '<?php echo esc_js( __( 'Something went wrong. Please try again.', 'nsml' ) ); ?>';
				errorEl.style.display = 'block';
				submitBtn.disabled = false;
			} );
	} );

	emailInput.addEventListener( 'input', function () {
		errorEl.style.display = 'none';
	} );
}() );
</script>
