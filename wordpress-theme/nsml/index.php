<?php
/**
 * Minimal catch-all template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div style="padding: 120px 24px 80px; max-width: 960px; margin: 0 auto;">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article>
				<h2><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
			<?php
		endwhile;
		the_posts_navigation();
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'nsml' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
