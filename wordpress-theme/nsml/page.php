<?php
/**
 * Generic fallback page template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div style="padding: 120px 24px 80px; max-width: 960px; margin: 0 auto;">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</div>
<?php
get_footer();
