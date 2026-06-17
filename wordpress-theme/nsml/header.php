<?php
/**
 * Header. Markup mirrors the original static site exactly (nav is still
 * client-side injected by assets/js/nav.js into #nav-root) so the visual
 * output is unchanged; only the data feeding nav.js is now dynamic.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
	<script>window.NSML_NAV = <?php echo wp_json_encode( nsml_nav_config() ); ?>;</script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="noise" aria-hidden="true"></div>
<div id="nav-root"></div>
