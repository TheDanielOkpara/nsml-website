<?php
/**
 * Footer. The "Properties" column pulls live nsml_property posts instead
 * of a hardcoded list, so newly added properties appear automatically.
 */
$nsml_footer_properties = get_posts(
	array(
		'post_type'           => NSML_PROPERTY_CPT,
		'post_status'         => 'publish',
		'posts_per_page'      => 12,
		'orderby'             => 'menu_order title',
		'order'               => 'ASC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
	<?php nsml_render_partners_marquee(); ?>
	<footer>
		<div class="footer-inner">
			<div class="footer-top">
				<div>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="f-logo">
						<img src="<?php echo esc_url( nsml_theme_setting( 'logo' ) ); ?>" alt="<?php esc_attr_e( 'Nilayo Sports Management Ltd', 'nsml' ); ?>" class="f-logo-img">
					</a>
					<div class="f-tagline"><?php echo esc_html( nsml_theme_setting( 'tagline' ) ); ?></div>
				</div>

				<div>
					<div class="f-col-title"><?php esc_html_e( 'Navigate', 'nsml' ); ?></div>
					<ul class="f-links">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'nsml' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'nsml' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Services', 'nsml' ); ?></a></li>
						<li><a href="<?php echo esc_url( get_post_type_archive_link( NSML_PROPERTY_CPT ) ?: home_url( '/properties/' ) ); ?>"><?php esc_html_e( 'Properties', 'nsml' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/news/' ) ); ?>"><?php esc_html_e( 'News', 'nsml' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'nsml' ); ?></a></li>
					</ul>
				</div>

				<div>
					<div class="f-col-title"><?php esc_html_e( 'Properties', 'nsml' ); ?></div>
					<ul class="f-links">
						<?php foreach ( $nsml_footer_properties as $nsml_prop ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $nsml_prop ) ); ?>"><?php echo esc_html( get_the_title( $nsml_prop ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="footer-social-col">
					<div class="f-col-title"><?php esc_html_e( 'Follow Us', 'nsml' ); ?></div>
					<div class="footer-social-list">
						<?php if ( nsml_theme_setting( 'social_instagram' ) ) : ?>
						<a href="<?php echo esc_url( nsml_theme_setting( 'social_instagram' ) ); ?>" target="_blank" rel="noopener" aria-label="Instagram" class="social-link">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
						</a>
						<?php endif; ?>
						<?php if ( nsml_theme_setting( 'social_facebook' ) ) : ?>
						<a href="<?php echo esc_url( nsml_theme_setting( 'social_facebook' ) ); ?>" target="_blank" rel="noopener" aria-label="Facebook" class="social-link">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
						</a>
						<?php endif; ?>
						<?php if ( nsml_theme_setting( 'social_twitter' ) ) : ?>
						<a href="<?php echo esc_url( nsml_theme_setting( 'social_twitter' ) ); ?>" target="_blank" rel="noopener" aria-label="X (Twitter)" class="social-link">
							<svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
						</a>
						<?php endif; ?>
						<?php if ( nsml_theme_setting( 'social_linkedin' ) ) : ?>
						<a href="<?php echo esc_url( nsml_theme_setting( 'social_linkedin' ) ); ?>" target="_blank" rel="noopener" aria-label="LinkedIn" class="social-link">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
						</a>
						<?php endif; ?>
						<?php if ( nsml_theme_setting( 'social_youtube' ) ) : ?>
						<a href="<?php echo esc_url( nsml_theme_setting( 'social_youtube' ) ); ?>" target="_blank" rel="noopener" aria-label="YouTube" class="social-link">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.96-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white"/></svg>
						</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="footer-bottom">
				<div class="f-copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( nsml_theme_setting( 'footer_copyright' ) ); ?></div>
				<a href="<?php echo esc_url( nsml_theme_setting( 'footer_credit_url' ) ); ?>" target="_blank" rel="noopener" class="f-credit"><?php echo esc_html( nsml_theme_setting( 'footer_credit' ) ); ?></a>
				<div class="f-cert-pill"><span class="eyebrow-dot"></span><?php echo esc_html( nsml_theme_setting( 'cert_pill' ) ); ?></div>
			</div>
		</div>
	</footer>

<?php wp_footer(); ?>
</body>
</html>
