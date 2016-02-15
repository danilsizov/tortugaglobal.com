<?php 
/**
 * The template for displaying Error 404 page.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */
?>
<?php get_header(); ?>

<div class="bread-title-holder">
	<div class="container">
		<div class="row-fluid">
			<div class="container_inner clearfix">

				<!-- #logo -->
				<div id="logo" class="span6">
					<?php if( get_theme_mod('connexions_lite_logo_img', '' ) != '' ) { ?>
						<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name'); ?>" ><img class="logo" src="<?php echo esc_url( get_theme_mod('connexions_lite_logo_img') ); ?>" alt="<?php bloginfo('name'); ?>" /></a>
					<?php } elseif ( display_header_text() ) { ?>
					<!-- #description -->
					<div id="site-title" class="logo_desp">
						<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name') ?>" ><?php bloginfo('name'); ?></a> 
						<div id="site-description"><?php bloginfo( 'description' ); ?></div>
					</div>
					<!-- #description -->
					<?php } ?>
				</div>
				<!-- #logo -->

				<span class="span6">
					<h1 class="title"><?php _e('ERROR','connexions-lite'); ?><i class="fa fa-exclamation-circle"></i></h1>
					<?php if ( ( class_exists('connexions_lite_breadcrumb_class') ) ) { 
						$connexion_breadcumb->connexions_lite_custom_breadcrumb();
					} ?>
				</span>
			</div>
		</div>
	</div>
</div>

<div class="page-content">
	<div class="container" id="error-404">
		<div class="row-fluid">
			<div id="content" class="span12">
				<div class="post">
					<div class="skepost _404-page">
						<div class="error-txt-img"><img src="<?php echo get_template_directory_uri(); ?>/images/connexion404.png" /></div>
						<div class="error-txt"><p><?php _e('Sorry, but the requested resource was not found on this site.','connexions-lite'); ?></p></div>
					</div>
					<!-- post --> 
				</div>
				<!-- post -->
			</div>
			<!-- content --> 
		</div>
	</div>
</div>
<?php get_footer(); ?>