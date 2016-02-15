<?php
/************************************************
   ENQUQUE STYLES AND JAVASCRIPTS
************************************************/
function connexions_lite_stylesheet(){
	global $wp_version;

	$theme = wp_get_theme();
	$connexions_lite_version = $theme['Version'];

	wp_enqueue_script( 'comment-reply' );    
	wp_enqueue_style( 'connexions-lite-style', get_stylesheet_uri(), false, $connexions_lite_version);
	
	wp_enqueue_script( 'hoverIntent');
	wp_enqueue_script( 'connexions-lite-superfish', get_template_directory_uri().'/js/superfish.js',array('jquery'),'1.0',1);
	wp_enqueue_script( 'connexions-lite-nicescroll', get_template_directory_uri().'/js/jquery.nicescroll.js',array('jquery'),'1.0',1);	
	wp_enqueue_script( 'connexions-lite-waypoints', get_template_directory_uri().'/js/waypoints.js',array('jquery'),'1.0',1);
	wp_enqueue_script( 'connexions-lite-custom_jquery', get_template_directory_uri().'/js/connexions.custom.js',array('jquery'),'1.0',1);
	
	wp_enqueue_style( 'connexions-lite-bootstrap-stylesheet', get_template_directory_uri().'/css/bootstrap-responsive.css', false, $connexions_lite_version);
	wp_enqueue_style( 'connexions-lite-awesome-stylesheet', get_template_directory_uri().'/css/font-awesome.css', false, $connexions_lite_version);
	wp_enqueue_style( 'connexions-lite-font-animation-stylesheet', get_template_directory_uri().'/css/connexions-animation.css', false, $connexions_lite_version);
	wp_enqueue_style( 'connexions-lite-awesome-ie7-stylesheet', get_template_directory_uri().'/css/font-awesome-ie7.css', false, $connexions_lite_version);
	wp_enqueue_style( 'connexions-lite-google-oswald-font','//fonts.googleapis.com/css?family=Oswald:400,300,700&subset=latin,latin-ext', false, $connexions_lite_version);
	wp_enqueue_style( 'connexions-lite-google-lato-font','//fonts.googleapis.com/css?family=Lato:400,300,700', false, $connexions_lite_version);
	wp_enqueue_style( 'connexions-lite-google-raleway-font','//fonts.googleapis.com/css?family=Raleway:400,300,500,600,700', false, $connexions_lite_version);
}
add_action('wp_enqueue_scripts', 'connexions_lite_stylesheet');

function connexions_lite_admin_head() {
	$theme = wp_get_theme();
	$connexions_lite_version = $theme['Version'];
	wp_enqueue_style('connexions-lite-admin-stylesheet', get_template_directory_uri().'/SketchBoard/css/sketch-admin.css', false, $connexions_lite_version);
}
add_action( 'admin_head', 'connexions_lite_admin_head' );

function connexions_lite_head(){
	if(!is_admin()) {
		require_once(get_template_directory().'/includes/connexion-custom.php');
	}
}
add_action('wp_head', 'connexions_lite_head');
