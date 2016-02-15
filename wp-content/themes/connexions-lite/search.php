<?php
/**
 * The template for displaying Search Results pages.
 * @package WordPress
 * @SketchThemes
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

					<div id="logo" class="span6">
						<?php if( get_theme_mod('connexions_lite_logo_img', '' ) != '' ) { ?>
							<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name'); ?>" ><img class="logo" src="<?php echo esc_url( get_theme_mod('connexions_lite_logo_img') ); ?>" alt="<?php bloginfo('name'); ?>" /></a>
						<?php } else { ?>
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
						<h1 class="title"><?php printf( __( 'Search Results for / %s', 'connexions-lite' ), '<span>' . get_search_query() . '</span>' ); ?><i class="fa fa-search"></i></h1>
						<?php if ((class_exists('connexions_lite_breadcrumb_class'))) {$connexion_breadcumb->connexions_lite_custom_breadcrumb();} ?>
					</span>
				</div>
			</div>
		</div>
	</div>

	<!-- Container-->
	<div class="container clearfix">
		<div class="row-fluid">
			<!-- blog post -->
				<div class="fullblog clearfix">
					<div class="news_full_blog span8">
						<?php
							if(have_posts()) :
							while(have_posts()) : the_post();
						?>
						<?php if(is_sticky($post->ID)) { _e("<div class='sticky-post'>featured</div>",'connexions-lite'); } ?>
						<?php get_template_part( 'content', get_post_format() ); ?>
						<?php endwhile; ?>
						<!-- Page Navigation Section starts -->
						
							<div class="navigation blog-navigation">	
								<div class="alignleft"><?php previous_posts_link(__('&larr;Previous','connexions-lite')) ?></div>		
								<div class="alignright"><?php next_posts_link(__('Next&rarr;','connexions-lite'),'') ?></div>    						
							</div>  
							
						<!-- \\Page Navigation Section ends -->
					<?php else : ?>
					<h2><?php _e('Apologies, but no results were found for the requested archive.','connexions-lite'); ?></h2>
					<?php endif; ?>		
					<!-- /end blog post -->
				</div>
				<!-- #Sidebar// -->
				<div id="sidebar" class="span4">
					<?php get_sidebar(); ?>
				</div>
				<!-- //#Sidebar -->
			</div> <!-- //fullblog -->
		</div>
  </div>
  <!-- /Container--> 
</div>
<!--/Blog --> 
<?php
get_footer();
?>