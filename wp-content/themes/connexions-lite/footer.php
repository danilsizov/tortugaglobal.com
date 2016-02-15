<?php
/**
 * The template for displaying the footer.
 * @package WordPress
 * @SketchThemes
 */
?>

<?php $connexions_copyright = get_theme_mod('connexions_lite_copyright', 'Proudly Powered by WordPress'); ?>

<!-- #Footer Area Starts -->
<footer id="footer-area">

<!-- Footer-->

	<div class="footer">
		<!-- Footer Top Section Start -->
		<div id="footer_top">
			<div id="footer_arrow"><a href="JavaScript:void(0);" title="<?php _e('Back To Top','connexions-lite')?>" id="backtop"><span><?php _e('TOP','connexions-lite'); ?></span></a></div>
		</div>
		<!-- Footer Top Section End -->

		<!-- Footer Bootom Section Start -->
		<div id="footer_bottom">
			<!-- container Start -->
			<div class="container clearfix">
				<div class="row-fluid">
					<!-- Footer Copyright Section Start -->
					<div class="copyright span6"><p><?php echo wp_kses_post($connexions_copyright); ?></div>
					<!-- Footer Copyright Section End -->

					<!-- Footer Theme Start -->
					<div class="refrence_link span6">
						<div class="copytxt-wrap"><div class="copy-txt"><?php _e('CONNEXIONS BY ','connexions-lite'); ?><a href="<?php echo esc_url('https://sketchthemes.com', 'connexions-lite'); ?>" target="_blank" title="WordPress Themes"><span class="copy-txtcolor"><?php _e('SKETCHTHEMES','connexions-lite'); ?></span></a></div></div>
					</div>
					<!-- Footer Theme Start End -->
				</div>
			</div>
			<!-- container End -->
		</div>
		<!-- Footer Bootom Section End -->

	</div>

	<!--/Footer-->
</footer>
<!-- #Footer Area Ends -->
</div>

<!-- #wrapper -->
<?php wp_footer(); ?>
</body>
</html>