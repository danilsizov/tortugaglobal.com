<?php
/**
 * The Header for our theme.
 * @package WordPress
 * @SketchThemes
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if(!is_front_page()){ ?>
<div class="conx-inner-overlay"></div>
<?php } ?>

<div id="index"></div>

<!-- Header -->
<div id="header" class="clearfix">

	<a id="header-trigger" class="fa fa-bars" href="#"></a>

	<!-- top-head-secwrap -->
	<div id="top-head">

		<!-- #logo -->
		<div id="logo">
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

		<!-- top-nav-menu -->
		<div class="ske-menu" id="skenav">
			<?php wp_nav_menu( array ( 'container_class' => 'ske-menu', 'container_id' => 'skenav', 'menu_id' => 'menu', 'theme_location' => 'connexions_main_nav') ); ?>
		</div>
		<!-- top-nav-menu -->

		<!-- Social Links Section -->
		<div class="social_icon">
			<ul class="clearfix">
				<?php if( get_theme_mod('connexions_lite_fbook_link', '#') != '' ) { ?>
					<li class="fb-icon"><a target="_blank" href="<?php echo esc_url( get_theme_mod('connexions_lite_fbook_link', '#') ); ?>"><span class="fa fa-facebook" title="<?php __('Facebook','connexions-lite'); ?>"></span></a></li>
				<?php } ?>
				<?php if( get_theme_mod('connexions_lite_twitter_link', '#') != '' ) { ?>
					<li class="tw-icon"><a target="_blank" href="<?php echo esc_url( get_theme_mod('connexions_lite_twitter_link', '#') ); ?>"><span class="fa fa-twitter" title="<?php __('Twitter','connexions-lite'); ?>"></span></a></li>
				<?php } ?>
				<?php if( get_theme_mod('connexions_lite_gplus_link', '#') != '' ) { ?>
					<li class="gplus-icon"><a target="_blank" href="<?php echo esc_url( get_theme_mod('connexions_lite_gplus_link', '#') ); ?>"><span class="fa fa-google-plus" title="<?php __('Google Plus','connexions-lite'); ?>"></span></a></li>
				<?php } ?>
				<?php if( get_theme_mod('connexions_lite_linkedin_link', '#') != '' ) { ?>
					<li class="linkedin-icon"><a target="_blank" href="<?php echo esc_url( get_theme_mod('connexions_lite_linkedin_link', '#') ); ?>"><span class="fa fa-linkedin" title="<?php __('Linkedin','connexions-lite'); ?>"></span></a></li>
				<?php } ?>
				<?php if( get_theme_mod('connexions_lite_pinterest_link', '#') != '' ) { ?>
					<li class="pinterest-icon"><a target="_blank" href="<?php echo esc_url( get_theme_mod('connexions_lite_pinterest_link', '#') ); ?>"><span class="fa fa-pinterest" title="<?php __('Pinterest','connexions-lite'); ?>"></span></a></li>
				<?php } ?>
				<?php if( get_theme_mod('connexions_lite_flickr_link', '#') != '' ) { ?>
					<li class="flickr-icon"><a target="_blank" href="<?php echo esc_url( get_theme_mod('connexions_lite_flickr_link', '#') ); ?>"><span class="fa fa-flickr" title="<?php __('Flickr','connexions-lite'); ?>"></span></a></li>
				<?php } ?>
				<?php if( get_theme_mod('connexions_lite_dribbble_link', '#') != '' ) { ?>
					<li class="dribbble-icon"><a target="_blank" href="<?php echo esc_url( get_theme_mod('connexions_lite_dribbble_link', '#') ); ?>"><span class="fa fa-dribbble" title="<?php __('dribbble','connexions-lite'); ?>"></span></a></li>
				<?php } ?>
			</ul>
		</div>
		<!-- Social Links Section -->

	</div>
	<!-- top-head-secwrap -->

</div>
<!-- Header -->

<!-- wrapper -->
<div id="wrapper" class="skepage">


<?php if(!is_front_page()) { ?><div class="header-clone"></div><?php } ?>
<!-- Slider Banner Section\\ -->
<?php 
if( is_front_page() ) { 
	 get_template_part("includes/front","bgimage-section");
}
?>
<!-- \\Slider Banner Section -->

<?php 
if(is_archive() || is_home()) {
$connexion_breadcumb = new connexions_lite_breadcrumb_class();
?>
<div class="bread-title-holder">
	<div class="container">
		<div class="row-fluid">
			<div class="container_inner clearfix">

				<!-- #logo -->
					<div id="logo" class="span6">
						<?php if(get_theme_mod('connexions_lite_logo_img', '') != '' ) { ?>
							<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name'); ?>" ><img class="logo" src="<?php echo esc_url(get_theme_mod('connexions_lite_logo_img')); ?>" alt="<?php bloginfo('name'); ?>" /></a>
						<?php } else{ ?>
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

					<?php 
					if(is_home()) { ?>
						<h1 class="title"><?php single_post_title(); ?><i class="fa fa-folder-open-o"></i></h1>
					<?php 
					}elseif(is_author()){ ?>
						<h1 class="title"><?php global $wp_query; $curauth = $wp_query->get_queried_object(); printf( __('Author', 'connexions-lite') . ' / ' . $curauth->display_name ); ?><i class="fa fa-folder-open-o"></i></h1>
					<?php
					}elseif(is_tag()){ ?>
						<h1 class="title"><?php printf( __( 'Tag Archives / %s', 'connexions-lite' ), '<span>' . single_tag_title( '', false ) . '</span>' ); ?><i class="fa fa-folder-open-o"></i></h1>
					<?php
					}elseif(is_category()){ ?>
						<h1 class="title"><?php printf( __( 'Category Archives / %s', 'connexions-lite' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?><i class="fa fa-folder-open-o"></i></h1>
					<?php
					}elseif(is_date()){ ?>
						<h1 class="title">						
							<?php if ( is_day() ) : ?>
							<?php printf( __( 'Daily Archives / <span>%s</span>', 'connexions-lite' ), get_the_date() ); ?>
							<?php elseif ( is_month() ) : ?>
								<?php printf( __( 'Monthly Archives / <span>%s</span>', 'connexions-lite' ), get_the_date('F Y') ); ?>
							<?php elseif ( is_year() ) : ?>
								<?php printf( __( 'Yearly Archives / <span>%s</span>', 'connexions-lite' ), get_the_date('Y') ); ?>
							<?php else : ?>
							<?php _e( 'Blog Archives', 'connexions-lite' ); ?>
						<?php endif; ?><i class="fa fa-folder-open-o"></i></h1>
					<?php
					}elseif(is_tax()){ ?>
						<h1 class="title"><?php printf(single_cat_title( '', false ) . '' ); ?><i class="fa fa-folder-open-o"></i></h1>
					<?php
					}elseif(is_page()){     
					?>
						<h1 class="title"><?php the_title(); ?><i class="fa fa-folder-open-o"></i></h1>
					<?php } ?>
					<?php if ((class_exists('connexions_lite_breadcrumb_class'))) {$connexion_breadcumb->connexions_lite_custom_breadcrumb();} ?>
				</span>
			</div>
		</div>
	</div>
</div>
<?php
}
?>
