<?php
/**
 * SketchThemes functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
*/
/********************************************************
 INCLUDE REQUIRED FILE FOR THEME (PLEASE DON'T REMOVE IT)
*********************************************************/
require_once(get_template_directory() . '/SketchBoard/functions/admin-init.php');
/********************************************************/

/********************************************************
	REGISTERS THE WIDGETS AND SIDEBARS FOR THE SITE 
*********************************************************/
function connexions_lite_widgets_init() 
{
	register_sidebar(array(
		'name' => __('Blog Sidebar','connexions-lite'),
		'id' => 'blog-sidebar',
		'before_widget' => '<li id="%1$s" class="ske-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="ske-title">',
		'after_title' => '</h3>',
	));
}
add_action( 'widgets_init', 'connexions_lite_widgets_init' );


/**
 * Sets up theme defaults and registers the various WordPress features that
 * Connexions Lite supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add Visual Editor stylesheets.
 * @uses add_theme_support() To add support for automatic feed links, post
 * formats, and post thumbnails.
 * @uses register_nav_menu() To add support for a navigation menu.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
*/
function connexions_lite_theme_setup() {
	/*
	* Makes Connexions Lite available for translation.
	*
	* Translations can be added to the /languages/ directory.
	* If you're building a theme based on Twenty Thirteen, use a find and
	* replace to change 'connexions-lite' to the name of your theme in all
	* template files.
	*/
	load_theme_textdomain('connexions-lite', get_template_directory() . '/languages');
	 
	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	add_theme_support( 'title-tag' );

	// This theme allows users to set a custom header.
	add_theme_support( 'custom-header', array( 'flex-width' => true, 'width' => 1600, 'flex-height' => true, 'height' => 750, 'default-image' => get_template_directory_uri() . '/images/header.png') );

	// This theme allows users to set a custom background.
	add_theme_support( 'custom-background', apply_filters( 'connexions_lite_custom_background_args', array('default-color' => 'ffffff', ) ) );

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	/*
	* This theme uses a custom image size for featured images, displayed on
	* "standard" posts and pages.
	*/
	add_theme_support('post-thumbnails');
	add_image_size('connexions-lite-standard-thumb', 700, 350, true);
	add_image_size('connexions-lite-front-thumb', 370, 240, true);

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'connexions_main_nav' => __( 'Main Navigation','connexions-lite'),
	));

	/**
	* SETS UP THE CONTENT WIDTH VALUE BASED ON THE THEME'S DESIGN.
	*/
	global $content_width;
	if ( ! isset( $content_width ) ){
	      $content_width = 900;
	}
}
add_action( 'after_setup_theme', 'connexions_lite_theme_setup' ); 


/**
* Funtion to add CSS class to body
*/
function connexions_lite_add_class( $classes ) {

	if ( 'page' == get_option( 'show_on_front' ) && ( '' != get_option( 'page_for_posts' ) ) && is_front_page() ) {
		$classes[] = 'front-page';
	}
	
	return $classes;
}
add_filter( 'body_class','connexions_lite_add_class' );

/**
 * Filter content with empty post title
 *
 */

function connexions_lite_untitled($title) {
	if ($title == '') {
		return __('Untitled','connexions-lite');
	} else {
		return $title;
	}
}
add_filter('the_title', 'connexions_lite_untitled');


/**
 * Add Customizer 
 */
require get_template_directory() . '/includes/customizer.php';
/**
 * Add Customizer 
 */
require_once(get_template_directory() . '/SketchBoard/functions/admin-init.php');
/**
 * Add Customizer 
 */
require_once(get_template_directory() . '/includes/sketchtheme-upsell.php');