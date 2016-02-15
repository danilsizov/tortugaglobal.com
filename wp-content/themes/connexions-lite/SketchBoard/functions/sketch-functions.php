<?php
/*----------------------------------------------
	SKETCH THEME REQUIRED FUCNTIONS
-----------------------------------------------*/

/************************************************
   SKETCH THEME POST EXCERPT LENGTH
************************************************/
function connexions_lite_excerpt_length($length) {
	return 210;
}
add_filter('excerpt_length', 'connexions_lite_excerpt_length');

/************************************************
   SKETCH THEME CUSTOM PAGE TITLE
************************************************/
function connexions_lite_title($title){
	$skt_title = $title;
	if ( is_home() && !is_front_page() ) {
		$skt_title .= get_bloginfo('name');
	}
	if ( is_front_page() ){
		$skt_title .=  get_bloginfo('name');
		$skt_title .= ' | '; 
		$skt_title .= get_bloginfo('description');
	}
	if ( is_search() ) {
		$skt_title .=  get_bloginfo('name');
	}
	if ( is_author() ) { 
		$skt_title .= get_bloginfo('name');
	}
	if ( is_single() ) {
		$skt_title .= get_bloginfo('name');
	}
	if ( is_page() && !is_front_page() ) {
		$skt_title .= get_bloginfo('name');
	}
	if ( is_category() ) {
		$skt_title .= get_bloginfo('name');
	}
	if ( is_year() ) { 
		$skt_title .= get_bloginfo('name');
	}
	if ( is_month() ) {
		$skt_title .= get_bloginfo('name');
	}
	if ( is_day() ) {
		$skt_title .= get_bloginfo('name');
	}
	if ( is_tag() ) {
		$skt_title .= get_bloginfo('name');
	}
	if ( is_404() ) {
		$skt_title .= get_bloginfo('name');
	}					
	return $skt_title;
}
add_filter( 'wp_title', 'connexions_lite_title' );



/********************************************
 Hex2RGB Function
*********************************************/
function connexions_lite_Hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
} 

/********************************************
	EXCERPT CONTROLL FUNCTION
*********************************************/
function connexions_lite_limit_words($string, $word_limit) {
	$words = explode(' ', $string);
	return implode(' ', array_slice($words, 0, $word_limit));
}

function connexions_lite_round_num($num, $to_nearest) {
   return floor($num/$to_nearest)*$to_nearest;
}

function connexions_lite_page_css_class( $css_class, $page ) {
    global $post;
    if ( $post->ID == $page->ID ) {
        $css_class[] = 'current_page_item';
    }
    return $css_class;
}
add_filter( 'page_css_class', 'connexions_lite_page_css_class', 10, 2 );
