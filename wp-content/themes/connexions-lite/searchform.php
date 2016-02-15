<?php
/**
 * The template for displaying search forms in SketchThemes
 * @package WordPress
 * @SketchThemes
 */
?>

<!-- #search Form Starts -->

<div class="search-box">

	<form method="get" class="searchform" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">

		<input type="text" class="field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" id="s" placeholder="<?php esc_attr_e( 'Type Here &hellip;', 'connexions-lite' ); ?>" />

		<div class="search-icon">

			<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php _e('Search','connexions-lite');?>" />

		</div>

	</form>

</div>

<!-- #search Form Ends -->