<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Square
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<figure class="entry-figure">
		<?php 
		if(has_post_thumbnail()):
		$square_image = wp_get_attachment_image_src( get_post_thumbnail_id() , 'square-blog-thumb' );
		?>
		<a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($square_image[0]); ?>" alt="<?php echo esc_attr( get_the_title() ) ?>"></a>
		<?php endif; ?>
	</figure>

	<div class="sq-post-wrapper">
		<header class="entry-header">
			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

			<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
				<?php square_posted_on(); ?>
			</div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->

		<footer class="entry-footer">
			<?php square_entry_footer(); ?>
		</footer><!-- .entry-footer -->
	</div>
</article><!-- #post-## -->

