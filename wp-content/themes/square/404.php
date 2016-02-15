<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Square
 */

get_header(); ?>

<header class="sq-main-header">
	<div class="sq-container">
		<h1 class="sq-main-title"><?php esc_html_e( '404 Error', 'square' ); ?></h1>
	</div>
</header><!-- .entry-header -->

<div class="sq-container">

	<p><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'square' ); ?></p>

</div>

<?php get_footer(); ?>
