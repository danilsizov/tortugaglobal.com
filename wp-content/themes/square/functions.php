<?php
/**
 * Square functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Square
 */

if ( ! function_exists( 'square_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function square_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Square, use a find and replace
	 * to change 'square' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'square', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );
	add_image_size( 'square-about-thumb', 400, 420, true );
	add_image_size( 'square-blog-thumb', 800, 420, true );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'square' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'square_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', square_fonts_url() ) );
}
endif; // square_setup
add_action( 'after_setup_theme', 'square_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function square_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'square_content_width', 640 );
}
add_action( 'after_setup_theme', 'square_content_width', 0 );

/**
 * Enables the Excerpt meta box in Page edit screen.
 */
function square_add_excerpt_support_for_pages() {
	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'square_add_excerpt_support_for_pages' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function square_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Left Sidebar', 'square' ),
		'id'            => 'square-left-sidebar',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'square' ),
		'id'            => 'square-footer1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'square' ),
		'id'            => 'square-footer2',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 3', 'square' ),
		'id'            => 'square-footer3',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 4', 'square' ),
		'id'            => 'square-footer4',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'About Footer', 'square' ),
		'id'            => 'square-about-footer',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	) );
}
add_action( 'widgets_init', 'square_widgets_init' );

if ( ! function_exists( 'square_fonts_url' ) ) :
/**
 * Register Google fonts for Square.
 *
 * @since Square 1.0
 *
 * @return string Google fonts URL for the theme.
 */
function square_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'square' ) ) {
		$fonts[] = 'Open+Sans:400,300,600,700';
	}

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Inconsolata, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Roboto Condensed font: on or off', 'square' ) ) {
		$fonts[] = 'Roboto+Condensed:300italic,400italic,700italic,400,300,700';
	}

	/*
	 * Translators: To add an additional character subset specific to your language,
	 * translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language.
	 */
	$subset = _x( 'no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'square' );

	if ( 'cyrillic' == $subset ) {
		$subsets .= ',cyrillic,cyrillic-ext';
	} elseif ( 'greek' == $subset ) {
		$subsets .= ',greek,greek-ext';
	} elseif ( 'devanagari' == $subset ) {
		$subsets .= ',devanagari';
	} elseif ( 'vietnamese' == $subset ) {
		$subsets .= ',vietnamese';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' =>  implode( '|', $fonts ) ,
			'subset' =>  $subsets ,
		), '//fonts.googleapis.com/css' );
	}

	return $fonts_url;
}
endif;

/**
 * Enqueue scripts and styles.
 */
function square_scripts() {
	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.js', array(), '2.6.3', true );
	wp_enqueue_script( 'square-bx-slider', get_template_directory_uri() . '/js/jquery.bxslider.js', array('jquery'), '4.1.2', true );
	wp_enqueue_script( 'square-owl-carousel', get_template_directory_uri() . '/js/owl.carousel.js', array('jquery'), '1.3.3', true );

	if(is_page_template( 'templates/home-template.php' )){
		wp_enqueue_script( 'square-draggabilly', get_template_directory_uri() . '/js/draggabilly.pkgd.min.js', array('jquery'), '1.3.3', true );
		wp_enqueue_script( 'square-elastiStack', get_template_directory_uri() . '/js/elastiStack.js', array('jquery'), '1.0.0', true );
	}

	wp_enqueue_script( 'square-custom', get_template_directory_uri() . '/js/square-custom.js', array('jquery'), '20150903', true );
	
	wp_enqueue_style( 'square-fonts', square_fonts_url(), array(), null );
	wp_enqueue_style( 'square-bx-slider', get_template_directory_uri() . '/css/jquery.bxslider.css', array(), '4.1.2' );
	wp_enqueue_style( 'square-animate', get_template_directory_uri() . '/css/animate.css', array(), '1.0' );
	wp_enqueue_style( 'square-fontawesome', get_template_directory_uri() . '/css/font-awesome.css', array(), '4.4.0' );
	wp_enqueue_style( 'square-owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css', array(), '1.3.3' );
	wp_enqueue_style( 'square-owl-theme', get_template_directory_uri() . '/css/owl.theme.css', array(), '1.3.3' );
	wp_enqueue_style( 'square-style', get_stylesheet_uri() );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'square_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/square-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load FontAwesome Array
 */
require get_template_directory() . '/inc/fontawesome-list.php';