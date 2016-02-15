<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Square
 */

?>

	</div><!-- #content -->

	<footer id="sq-colophon" class="sq-site-footer">
		<div id="sq-top-footer">
			<div class="sq-container">
				<div class="sq-top-footer sq-clearfix">
					<div class="sq-footer sq-footer1">
						<?php if(is_active_sidebar('square-footer1')): 
							dynamic_sidebar('square-footer1');
						endif;
						?>	
					</div>

					<div class="sq-footer sq-footer2">
						<?php if(is_active_sidebar('square-footer2')): 
							dynamic_sidebar('square-footer2');
						endif;
						?>	
					</div>

					<div class="sq-footer sq-footer3">
						<?php if(is_active_sidebar('square-footer3')): 
							dynamic_sidebar('square-footer3');
						endif;
						?>	
					</div>

					<div class="sq-footer sq-footer4">
						<?php if(is_active_sidebar('square-footer3')): 
							dynamic_sidebar('square-footer3');
						endif;
						?>	
					</div>
				</div>
			</div>
		</div>

		<div id="sq-middle-footer">
			<div class="sq-container">
				<?php if(is_active_sidebar('square-about-footer')): 
					dynamic_sidebar('square-about-footer');
				endif;
				?>
			</div>
		</div>

		<div id="sq-bottom-footer">
			<div class="sq-container">
				<div class="sq-site-info sq-clearfix">
					<?php printf( esc_html__( 'WordPress Theme', 'square' ) ); ?>
					<span class="sep"> | </span>
					<?php printf( esc_html__( '%1$s by %2$s', 'square' ), 'Square', '<a href="http://hashthemes.com" rel="designer">Hash Themes</a>' ); ?>
				</div><!-- #site-info -->
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
