<?php

function connexions_lite_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	$wp_customize->remove_control('header_textcolor');

	// ====================================
	// = Background Image Size for custom-background
	// ====================================
	$wp_customize->add_setting( 'background_size', array(
		'default'        => 'auto',
		'theme_supports' => 'custom-background',
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('background_size', array(
		'label' => __('Breadcrumb Background Image Size','connexions-lite'),
		'section' => 'background_image',
		'settings' => 'background_size',
	));
	
	// ====================================
	// = Connextions Lite Theme Pannel
	// ==================================== 
	$wp_customize->add_panel( 'sketchthemes', array(
		'title' => __( 'Connextions Lite Options', 'connexions-lite'),
		'priority' => 10,
	) );

	// ====================================
	// = Connextions Lite Theme Sections
	// ====================================
	$wp_customize->add_section( 'home_page_settings' , array(
		'title' => __('Home Landing Page Section','connexions-lite'),
		'panel' => 'sketchthemes',
		'active_callback' => 'is_front_page'
	) );
	$wp_customize->add_section( 'header_settings' , array(
		'title' => __('Header Settings','connexions-lite'),
		'panel' => 'sketchthemes',
	) );
	$wp_customize->add_section( 'breadcrumb_settings' , array(
		'title' => __('Breadcrumb Settings','connexions-lite'),
		'panel' => 'sketchthemes',
	) );
	$wp_customize->add_section( 'blog_page_settings' , array(
		'title' => __('Blog Page Settings','connexions-lite'),
		'panel' => 'sketchthemes',
	) );
	$wp_customize->add_section( 'footer_settings' , array(
		'title' => __('Footer Settings','connexions-lite'),
		'panel' => 'sketchthemes',
	) );

	// ====================================
	// = General Settings Sections
	// ====================================
	$wp_customize->add_setting( 'connexions_lite_pri_color', array(
		'default'           => '#FB4A50',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'connexions_lite_pri_color', array(
		'label'       => __( 'Primary Color Scheme', 'connexions-lite' ),
		'description' => __( 'Theme Primary Color.', 'connexions-lite' ),
		'section'     => 'colors',
	) ) );
	$wp_customize->add_setting( 'connexions_lite_sec_color', array(
		'default'           => '#555555',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'connexions_lite_sec_color', array(
		'label'       => __( 'Secondary Color Scheme', 'connexions-lite' ),
		'description' => __( 'Theme Secondary Color.', 'connexions-lite' ),
		'section'     => 'colors',
	) ) );

	$wp_customize->add_setting( 'connexions_lite_text_homenav_item', array(
		'default'        => __('Home', 'connexions-lite'),
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_text_homenav_item', array(
		'label' => __('"Home" menu item Text in Navigation','connexions-lite'),
		'description' => __( 'Change Home menu item text in navigation', 'connexions-lite'),
		'section' => 'menu_locations',
	));

	// ====================================
	// = Header Settings Sections
	// ====================================
	$wp_customize->add_setting( 'connexions_lite_logo_img', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control(  new WP_Customize_Image_Control( $wp_customize, 'connexions_lite_logo_img', array(
		'label' => __( 'Logo Image', 'connexions-lite' ),
		'section' => 'header_settings',
		'mime_type' => 'image',
	) ) );

	$wp_customize->add_setting( 'connexions_lite_logo_width', array(
		'default'        => '199',
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_logo_width', array(
		'label' => __('Logo Width','connexions-lite'),
		'description' => __( 'Enter logo image width in pixel', 'connexions-lite'),
		'section' => 'header_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_logo_height', array(
		'default'        => '50',
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_logo_height', array(
		'label' => __('Logo Height','connexions-lite'),
		'description' => __( 'Enter logo image height in pixel', 'connexions-lite'),
		'section' => 'header_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_fbook_link', array(
		'default'        => '#',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('connexions_lite_fbook_link', array(
		'label' => __('Facebook Link', 'connexions-lite'),
		'description' => __('Enter Facebook Link.', 'connexions-lite'),
		'section' => 'header_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_twitter_link', array(
		'default'        => '#',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('connexions_lite_twitter_link', array(
		'label' => __('Twitter Link', 'connexions-lite'),
		'description' => __('Enter Twitter link.', 'connexions-lite'),
		'section' => 'header_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_gplus_link', array(
		'default'        => '#',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('connexions_lite_gplus_link', array(
		'label' => __('Google Plus Link', 'connexions-lite'),
		'description' => __('Enter Google Plus link.', 'connexions-lite'),
		'section' => 'header_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_linkedin_link', array(
		'default'        => '#',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('connexions_lite_linkedin_link', array(
		'label' => __('Linkedin Link', 'connexions-lite'),
		'description' => __('Enter Linkedin link.', 'connexions-lite'),
		'section' => 'header_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_pinterest_link', array(
		'default'        => '#',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('connexions_lite_pinterest_link', array(
		'label' => __('Pinterest Link', 'connexions-lite'),
		'description' => __('Enter Pinterest link.', 'connexions-lite'),
		'section' => 'header_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_flickr_link', array(
		'default'        => '#',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('connexions_lite_flickr_link', array(
		'label' => __('Flickr Link', 'connexions-lite'),
		'description' => __('Enter Flickr link.', 'connexions-lite'),
		'section' => 'header_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_dribbble_link', array(
		'default'        => '#',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('connexions_lite_dribbble_link', array(
		'label' => __('Dribbble Link', 'connexions-lite'),
		'description' => __('Enter Dribbble link.', 'connexions-lite'),
		'section' => 'header_settings',
	));

	// ====================================
	// = Breadcrumb Settings Sections
	// ====================================
	$wp_customize->add_setting( 'breadcrumbtxt_color', array(
		'default'           => '#ffffff' ,
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'breadcrumbtxt_color', array(
		'label'       => __( 'Breadcrumb Text Color', 'connexions-lite' ),
		'section'     => 'breadcrumb_settings',
	) ) );
	$wp_customize->add_setting( 'breadcrumbbg_color', array(
		'default'           => '#253151' ,
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'breadcrumbbg_color', array(
		'label'       => __( 'Breadcrumb Background Color', 'connexions-lite' ),
		'section'     => 'breadcrumb_settings',
	) ) );
	$wp_customize->add_setting( 'breadcrumbbg_image', array(
		'default'        => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'breadcrumbbg_image', array(
		'label' => __( 'Breadcrumb Background Image', 'connexions-lite' ),
		'section' => 'breadcrumb_settings',
	) ) );
	$wp_customize->add_setting( 'breadcrumbbg_repeat', array(
		'default'        => 'no-repeat',
		'sanitize_callback' => 'connexions_lite_sanitize_background_repeat',
	) );
	$wp_customize->add_control( 'breadcrumbbg_repeat', array(
		'label'      => __( 'Breadcrumb Background Repeat', 'connexions-lite' ),
		'section'    => 'breadcrumb_settings',
		'type'       => 'radio',
		'choices'    => array(
			'no-repeat'  => __('No Repeat', 'connexions-lite'),
			'repeat'     => __('Tile', 'connexions-lite'),
			'repeat-x'   => __('Tile Horizontally', 'connexions-lite'),
			'repeat-y'   => __('Tile Vertically', 'connexions-lite'),
		),
		'active_callback' => 'connexions_lite_active_breadcrumb_image',
	) );
	$wp_customize->add_setting( 'breadcrumbbg_position_x', array(
		'default'        => 'center',
		'sanitize_callback' => 'connexions_lite_sanitize_background_position',
	) );
	$wp_customize->add_control( 'breadcrumbbg_position_x', array(
		'label'      => __( 'Breadcrumb Background Position', 'connexions-lite'),
		'section'    => 'breadcrumb_settings',
		'type'       => 'radio',
		'choices'    => array(
			'left'       => __('Left', 'connexions-lite'),
			'center'     => __('Center', 'connexions-lite'),
			'right'      => __('Right', 'connexions-lite'),
		),
		'active_callback' => 'connexions_lite_active_breadcrumb_image',
	) );
	$wp_customize->add_setting( 'breadcrumbbg_attachment', array(
		'default'        => 'scroll',
		'sanitize_callback' => 'connexions_lite_sanitize_background_attachment',
	) );
	$wp_customize->add_control( 'breadcrumbbg_attachment', array(
		'label'      => __( 'Breadcrumb Background Attachment', 'connexions-lite'),
		'section'    => 'breadcrumb_settings',
		'type'       => 'radio',
		'choices'    => array(
			'scroll'     => __('Scroll', 'connexions-lite'),
			'fixed'      => __('Fixed', 'connexions-lite'),
		),
		'active_callback' => 'connexions_lite_active_breadcrumb_image',
	) );
	
	$wp_customize->add_setting( 'breadcrumbbg_size', array(
		'default'        => 'cover',
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('breadcrumbbg_size', array(
		'label' => __('Breadcrumb Background Image Size','connexions-lite'),
		'section' => 'breadcrumb_settings',
		'active_callback' => 'connexions_lite_active_breadcrumb_image',
	));

	// ====================================
	// = Home Page Settings Sections
	// ====================================
	$wp_customize->add_setting( 'connexions_lite_home_blog_title', array(
		'default'        => __('LATEST ARTICLES', 'connexions-lite'),
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_home_blog_title', array(
		'label' => __('Home Blog Section Title','connexions-lite'),
		'section' => 'home_page_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_rat_first_section_title', array(
		'default'        => __('KEY FEATURES', 'connexions-lite'),
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_rat_first_section_title', array(
		'label' => __('Home First Section Title','connexions-lite'),
		'description' => __('(Create a custom link in Menus and put <b>#section1</b> in URL and Navigation Label according to you for landing page.))<br/>Enter title for home first section.', 'connexions-lite'),
		'section' => 'home_page_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_rat_first_section_content', array(
		'default'        => '<div class="skepost clearfix text-center"><div class="inner_pages_content">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', 'connexions-lite').'</div><div class="page-container clearfix"><div class="con-key-feature span3 skt_animate_when_almost_visible small-to-large skt_start_animation"><div class="con-icon-wrap"><div class="con-feature-icon"><i class="fa fa-desktop"></i></div></div><div class="con-icon-title">'.__('ADVANCED ADMIN','connexions-lite').'</div><div class="con-icon-desc">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard', 'connexions-lite').'</div></div><div class="con-key-feature span3 skt_animate_when_almost_visible small-to-large skt_start_animation"><div class="con-icon-wrap"><div class="con-feature-icon"><i class="fa fa-eye"></i></div></div><div class="con-icon-title">'.__('FOR ALL DEVICES', 'connexions-lite').'</div><div class="con-icon-desc">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard', 'connexions-lite').'</div></div><div class="con-key-feature span3 skt_animate_when_almost_visible small-to-large skt_start_animation"><div class="con-icon-wrap"><div class="con-feature-icon"><i class="fa fa-cogs"></i></div></div><div class="con-icon-title">'.__('CUSTOMIZATION', 'connexions-lite').'</div><div class="con-icon-desc">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard', 'connexions-lite').'</div></div><div class="con-key-feature span3 skt_animate_when_almost_visible small-to-large skt_start_animation"><div class="con-icon-wrap"><div class="con-feature-icon"><i class="fa fa-code"></i></div></div><div class="con-icon-title">'.__('WEB DEVELOPMENT', 'connexions-lite').'</div><div class="con-icon-desc">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard', 'connexions-lite').'</div></div></div></div>',
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_rat_first_section_content', array(
		'type' => 'textarea',
		'label' => __('Home First Section Content','connexions-lite'),
		'description' => __('Enter content for Home First Section', 'connexions-lite'),
		'section' => 'home_page_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_rat_second_section_title', array(
		'default'        => __('ABOUT US', 'connexions-lite'),
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_rat_second_section_title', array(
		'label' => __('Home Second Section Title','connexions-lite'),
		'description' => __('(Create a custom link in Menus and put <b>#section1</b> in URL and Navigation Label according to you for landing page.))<br/>Enter title for home second section.', 'connexions-lite'),
		'section' => 'home_page_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_rat_second_section_content', array(
		'default'        => '<div class="skepost clearfix text-center"><div class="inner_pages_content">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', 'connexions-lite').'</div><div class="page-container clearfix"><div class="con-key-feature span3 skt_animate_when_almost_visible small-to-large skt_start_animation"><div class="con-icon-wrap"><div class="con-feature-icon"><i class="fa fa-desktop"></i></div></div><div class="con-icon-title">'.__('WE ARE CREATIVE','connexions-lite').'</div><div class="con-icon-desc">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard', 'connexions-lite').'</div></div><div class="con-key-feature span3 skt_animate_when_almost_visible small-to-large skt_start_animation"><div class="con-icon-wrap"><div class="con-feature-icon"><i class="fa fa-eye"></i></div></div><div class="con-icon-title">'.__('WE ARE COLL NERDS', 'connexions-lite').'</div><div class="con-icon-desc">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard', 'connexions-lite').'</div></div><div class="con-key-feature span3 skt_animate_when_almost_visible small-to-large skt_start_animation"><div class="con-icon-wrap"><div class="con-feature-icon"><i class="fa fa-cogs"></i></div></div><div class="con-icon-title">'.__('WE ARE PASSIONATE', 'connexions-lite').'</div><div class="con-icon-desc">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard', 'connexions-lite').'</div></div><div class="con-key-feature span3 skt_animate_when_almost_visible small-to-large skt_start_animation"><div class="con-icon-wrap"><div class="con-feature-icon"><i class="fa fa-code"></i></div></div><div class="con-icon-title">'.__('WE ARE INNOVATIVE', 'connexions-lite').'</div><div class="con-icon-desc">'.__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard', 'connexions-lite').'</div></div></div></div>',
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_rat_second_section_content', array(
		'type' => 'textarea',
		'label' => __('Home Second Section Content','connexions-lite'),
		'description' => __('Enter content for Home Second Section', 'connexions-lite'),
		'section' => 'home_page_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_rat_third_section_title', array(
		'default'        => __('OUR SKILLS', 'connexions-lite'),
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_rat_third_section_title', array(
		'label' => __('Home Third Section Title','connexions-lite'),
		'description' => __('(Create a custom link in Menus and put <b>#section1</b> in URL and Navigation Label according to you for landing page.))<br/>Enter title for home third section.', 'connexions-lite'),
		'section' => 'home_page_settings',
	));

	$wp_customize->add_setting( 'connexions_lite_rat_third_section_content', array(
		'default'        => __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', 'connexions-lite'),
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_rat_third_section_content', array(
		'type' => 'textarea',
		'label' => __('Home Third Section Content','connexions-lite'),
		'description' => __('Enter content for Home Third Section', 'connexions-lite'),
		'section' => 'home_page_settings',
	));


	// ====================================
	// = Blog Page Settings Sections
	// ====================================
	$wp_customize->add_setting( 'connexions_lite_blogpage_heading', array(
		'default'        => __('Blog', 'connexions-lite'),
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
	));
	$wp_customize->add_control('connexions_lite_copyright', array(
		'label' => __('Copyright Text','connexions-lite'),
		'section' => 'blog_page_settings',
		'settings' => 'connexions_lite_copyright',
	));


	// ====================================
	// = Footer Settings Sections
	// ====================================
	$wp_customize->add_setting( 'connexions_lite_copyright', array(
		'default'        => __('Proudly Powered by WordPress', 'connexions-lite'),
		'sanitize_callback' => 'connexions_lite_sanitize_textarea',
		'transport' => 'postMessage',
	));
	$wp_customize->add_control('connexions_lite_copyright', array(
		'label' => __('Copyright Text','connexions-lite'),
		'section' => 'footer_settings',
	));

}
add_action( 'customize_register', 'connexions_lite_customize_register' );

/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since Twenty Fifteen 1.0
 */
function connexions_lite_customize_preview_js() {
	wp_enqueue_script( 'connexions-lite-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20150924', true );
}
add_action( 'customize_preview_init', 'connexions_lite_customize_preview_js' );


// sanitize textarea
function connexions_lite_sanitize_textarea( $input ) {
	return wp_kses_post( force_balance_tags( $input ) );
}

function connexions_lite_sanitize_on_off( $input ) {
	$valid = array(
		'on' =>'ON',
		'off'=> 'OFF'
    );
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}

function connexions_lite_sanitize_background_repeat( $input ) {
	$valid = array(
		'no-repeat'  => __('No Repeat', 'connexions-lite'),
		'repeat'     => __('Tile', 'connexions-lite'),
		'repeat-x'   => __('Tile Horizontally', 'connexions-lite'),
		'repeat-y'   => __('Tile Vertically', 'connexions-lite'),
    );
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}

function connexions_lite_sanitize_background_position( $input ) {
	$valid = array(
		'left'       => __('Left', 'connexions-lite'),
		'center'     => __('Center', 'connexions-lite'),
		'right'      => __('Right', 'connexions-lite'),
    );
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}

function connexions_lite_sanitize_background_attachment( $input ) {
	$valid = array(
		'scroll'     => __('Scroll', 'connexions-lite'),
		'fixed'      => __('Fixed', 'connexions-lite'),
    );
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}

function connexions_lite_active_breadcrumb_image( $control ) {
	if ( $control->manager->get_setting('breadcrumbbg_image')->value() != '' ) {
		return true;
	} else {
		return false;
	}
}