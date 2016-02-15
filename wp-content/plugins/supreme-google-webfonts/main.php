<?php
/*
 * Plugin Name: Supreme Google Webfonts
 * Plugin URI: https://wordpress.org/plugins/supreme-google-webfonts/
 * Description: Adds all currently available Google webfonts into a nice dropdown list in your visual editor.  Simply select your Google Webfont and apply it to your text.  Also includes font size selection to apply to Google Webfonts.
 * Author: Josh Lobe, igmoweb
 * Version: 2.0.1
 * License: GPL2
 * Text Domain: sgf
 * Domain Path: /lang/
*/



class Supreme_Google_Webfonts {

	public function __construct() {
		add_action( 'admin_footer', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'wp_footer', array( $this, 'enqueue_styles' ) );

		add_filter( 'mce_buttons_3', array( $this, 'tinymce_add_buttons' ), 1 );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_custom_options' ) );

		add_action( 'admin_init', array( $this, 'settings_api_init' ) );

		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}
    
    function activate() {
    	$options = get_option( 'sgf_settings', false );
    	if ( $options === false ) {
    		update_option( 'sgf_settings', array( 'fonts' => array() ) );
    	}
    }

    function load_plugin_textdomain() {
		load_plugin_textdomain( 'sgf', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 
	}

    /*
     * Enqueue style-file, if it exists.
     */

    function enqueue_admin_styles( $hook ) {
    	$screen = get_current_screen();

    	if ( 'options-writing' === $screen->id ) {
    		$urls = $this->get_fonts_urls();
    		$i = 1;
    		foreach ( $urls as $url ) {
				wp_enqueue_style( 'sgf-google-fonts-' . $i, $url );
				$i++;
    		}
    	}
    	else {
    		$this->enqueue_styles();
    	}
    }


    function enqueue_styles() { 
		$settings = sgf_get_options();
		$fonts = $settings['fonts'];

		$all_fonts = sgf_get_fonts_list();
		$fonts = array_intersect_key( $all_fonts, $fonts );

		if ( ! $fonts )
			return;

		$urls = $this->get_fonts_urls( array_keys( $fonts ) );

		$i = 1;
    	foreach ( $urls as $url ) {
			wp_enqueue_style( 'sgf-google-fonts-' . $i, $url );
			$i++;
    	}
    }

    function get_fonts_urls( $include = false ) {
    	$all_fonts = sgf_get_fonts_list();
    	$fonts = array();

    	if ( is_array( $include ) ) {
    		foreach ( $all_fonts as $font_slug => $font ) {
    			if ( in_array( $font_slug, $include ) )
    				$fonts[ $font_slug ] = $font;
    		}
    	}
    	else {
    		$fonts = $all_fonts;
    	}

    	$protocol = is_ssl() ? 'https://' : 'http://';
		$gfonts_url = $protocol . 'fonts.googleapis.com/css';
		$fonts = wp_list_filter( $fonts, array( 'is_google_font' => true ) ); 
		$fonts = array_keys( $fonts );

		// We are going to split it in chunks as we shouldn't load all the fonts in the same URL
		$fonts = array_chunk( $fonts, 100 );

		$urls = array();
		foreach ( $fonts as $fonts_chunk ) {
			$urls[] = $gfonts_url . '?family=' . implode( '|', $fonts_chunk );
		}

		return $urls;

    }


    /**
	 * Add our buttons to the editor
	 * 
	 * @param  array $buttons Current list of buttons in the editor
	 * @return array new buttons list
	 */
	function tinymce_add_buttons( $buttons ) {
		$settings = sgf_get_options();
		$fonts = $settings['fonts'];

		if ( ! $fonts )
			return $buttons;

		return array_merge(
			array( 'fontselect', 'fontsizeselect' ),
			$buttons
		);
	}
	
	/**
	 * Custom font options for Tiny MCE
	 * 
	 * @param  array $opt Custom options
	 * @return array New options
	 */
	function tinymce_custom_options( $opt ) {  // Our custom Google options are added to the array.
		global $wp_version;

		$all_fonts = sgf_get_fonts_list();

		$settings = sgf_get_options();
		$fonts = $settings['fonts'];

		if ( ! $fonts )
			return $opt;

		$fonts = array_intersect_key( $all_fonts, $fonts );
		$fonts = array_merge( wp_list_filter( $all_fonts, array( 'is_google_font' => false ) ), $fonts );

		$advanced_fonts = array();
		foreach ( $fonts as $slug => $font ) {
			$advanced_fonts[] = $font['name'] . '=' . $font['editor_settings'];
		}


		if ( version_compare( $wp_version, '3.9', '<' ) ) {
			if ( ! empty( $opt['theme_advanced_fonts'] ) )
				$opt['theme_advanced_fonts'] .= ';' . implode( ';', $advanced_fonts );
			else
				$opt['theme_advanced_fonts'] = implode( ';', $advanced_fonts );

			if ( ! empty( $opt['content_css'] ) )
				$opt['content_css'] .= ',' . implode( ',', $this->get_fonts_urls( array_keys( $fonts ) ) );
			else
				$opt['content_css'] = implode( ',', $this->get_fonts_urls( array_keys( $fonts ) ) );	
			
			
		}
		else {
			if ( ! empty( $opt['font_formats'] ) )
				$opt['font_formats'] .= ';' . implode( ';', $advanced_fonts );
			else
				$opt['font_formats'] = implode( ';', $advanced_fonts );

			if ( ! empty( $opt['content_css'] ) )
				$opt['content_css'] .= ',' . implode( ',', $this->get_fonts_urls( array_keys( $fonts ) ) );
			else
				$opt['content_css'] = implode( ',', $this->get_fonts_urls( array_keys( $fonts ) ) )	;
		}

		return $opt;
		
	}

	function settings_api_init() {
		register_setting( 'writing', 'sgf_settings', array( $this, 'validate_settings' ) );
		add_settings_section( 'sgf-settings-fonts-section', '', array( $this, 'render_fonts_section' ), 'writing' );
		add_settings_field( 'sgf-settings-fonts-field', __( 'Fonts', 'sgf' ), array( $this, 'render_fonts_field' ), 'writing', 'sgf-settings-fonts-section' );
	}

	function render_fonts_section() {
		?>
			<h3><?php echo esc_html( __( 'Supreme Google Fonts', 'sgf' ) ); ?></h3>
			<p><?php _e( '<strong>Note:</strong> Check those fonts that you are going to use. requesting a lot of fonts may make your pages slow to load.', 'sgf' ); ?></p>
		<?php
	}



	function render_fonts_field() {
		$fonts = sgf_get_fonts_list();
		$fonts = wp_list_filter( $fonts, array( 'is_google_font' => true ) );

		$settings = sgf_get_options();

		?>	
			<label>
				<input type="checkbox" name="" value="" onclick="sgf_toggle_fonts_checkboxes(this)"/>
				<?php _e( 'Toggle all', 'sgf' ); ?>
			</label>

			<script>
				function sgf_toggle_fonts_checkboxes( source ) {
					var checkboxes = document.getElementsByClassName( 'sgf-checkbox' );
					
					var value = source.checked;

					[].forEach.call(checkboxes, function ( element ) {
						element.checked = value;
					});

				    return false;
				}
			</script>
			
			<ul class="sgf-fonts">
		<?php
		foreach ( $fonts as $slug => $font ) {
			$checked = checked( array_key_exists( $slug, $settings['fonts'] ), true, false );
			?>
				
					<li>
						<label for="font-<?php echo esc_attr( $slug ); ?>">
							<input type="checkbox" class="sgf-checkbox" id="font-<?php echo esc_attr( $slug ); ?>" <?php echo $checked; ?> name="sgf_settings[sgf-fonts][<?php echo esc_attr( $slug ); ?>]">
							<span style="font-family: '<?php echo $font['name']; ?>'"><?php echo $font['name']; ?></span>
						</label>
					</li>
				
			<?php
		}
		?>
			</ul>
		<?php

		?>
		<style>
			.sgf-fonts li {
				display: inline-block;
				width:25%;
			}
		</style>
		<?php
	}

	function validate_settings( $input ) {
		if ( ! current_user_can( 'manage_options' ) )
			return $options;

		$new_options = array(
			'fonts' => array()
		);

		if ( empty( $input['sgf-fonts'] ) || ! is_array( $input['sgf-fonts'] ) )
			return $new_options;

		$fonts = sgf_get_fonts_list();
		foreach( $input['sgf-fonts'] as $font => $value ) {
			if ( array_key_exists( $font, $fonts ) )
				$new_options['fonts'][ $font ] = $fonts[ $font ]['name'];
		}

		return $new_options;
	}
	
}

new Supreme_Google_Webfonts();



function sgf_get_options() {
	$options = get_option( 'sgf_settings', array() );
	return wp_parse_args( $options, sgf_get_default_options() );
}

function sgf_get_default_options() {
	$fonts = wp_list_filter( sgf_get_fonts_list(), array( 'is_google_font' => true ) );
	$fonts = wp_list_pluck( $fonts, 'name' );

	return array(
		'fonts' => $fonts
	);
}

function sgf_get_fonts_list() {
	$fonts = array(
		'Andale+Mono' => array(
			'name' => 'Andale Mono',
			'editor_settings' => 'andale mono,times',
			'is_google_font' => false
		),
		'Arial' => array(
			'name' => 'Arial',
			'editor_settings' => 'arial,helvetica,sans-serif',
			'is_google_font' => false
		),
		'Arial+Black' => array(
			'name' => 'Arial Black',
			'editor_settings' => 'arial black,avant garde',
			'is_google_font' => false
		),
		'Book+Antiqua' => array(
			'name' => 'Book Antiqua',
			'editor_settings' => 'book antiqua,palatino',
			'is_google_font' => false
		),
		'Comic+Sans+MS' => array(
			'name' => 'Comic Sans MS',
			'editor_settings' => 'comic sans ms,sans-serif',
			'is_google_font' => false
		),
		'Courier+New' => array(
			'name' => 'Courier New',
			'editor_settings' => 'courier new,courier',
			'is_google_font' => false
		),
		'Georgia' => array(
			'name' => 'Georgia',
			'editor_settings' => 'georgia,palatino',
			'is_google_font' => false
		),
		'Helvetica' => array(
			'name' => 'Helvetica',
			'editor_settings' => 'helvetica',
			'is_google_font' => false
		),
		'Impact' => array(
			'name' => 'Impact',
			'editor_settings' => 'impact,chicago',
			'is_google_font' => false
		),
		'Symbol' => array(
			'name' => 'Symbol',
			'editor_settings' => 'symbol',
			'is_google_font' => false
		),
		'Tahoma' => array(
			'name' => 'Tahoma',
			'editor_settings' => 'tahoma,arial,helvetica,sans-serif',
			'is_google_font' => false
		),
		'Terminal' => array(
			'name' => 'Terminal',
			'editor_settings' => 'terminal,monaco',
			'is_google_font' => false
		),
		'Times+New+Roman' => array(
			'name' => 'Times New Roman',
			'editor_settings' => 'times new roman,times',
			'is_google_font' => false
		),
		'Trebuchet+MS' => array(
			'name' => 'Trebuchet MS',
			'editor_settings' => 'trebuchet ms,geneva',
			'is_google_font' => false
		),
		'Verdana' => array(
			'name' => 'Verdana',
			'editor_settings' => 'verdana,geneva',
			'is_google_font' => false
		),
		'Webdings' => array(
			'name' => 'Webdings',
			'editor_settings' => 'webdings',
			'is_google_font' => false
		),
		'Wingdings' => array(
			'name' => 'Wingdings',
			'editor_settings' => 'wingdings,zapf dingbats',
			'is_google_font' => false
		),
		"ABeeZee" => array(
			'name' => "ABeeZee",
			'editor_settings' => "ABeeZee, sans-serif",
			'is_google_font' => true
		),
		"Abel" => array(
			'name' => "Abel",
			'editor_settings' => "Abel, sans-serif",
			'is_google_font' => true
		),
		"Abril+Fatface" => array(
			'name' => "Abril Fatface",
			'editor_settings' => "Abril Fatface, display",
			'is_google_font' => true
		),
		"Aclonica" => array(
			'name' => "Aclonica",
			'editor_settings' => "Aclonica, sans-serif",
			'is_google_font' => true
		),
		"Acme" => array(
			'name' => "Acme",
			'editor_settings' => "Acme, sans-serif",
			'is_google_font' => true
		),
		"Actor" => array(
			'name' => "Actor",
			'editor_settings' => "Actor, sans-serif",
			'is_google_font' => true
		),
		"Adamina" => array(
			'name' => "Adamina",
			'editor_settings' => "Adamina, serif",
			'is_google_font' => true
		),
		"Advent+Pro" => array(
			'name' => "Advent Pro",
			'editor_settings' => "Advent Pro, sans-serif",
			'is_google_font' => true
		),
		"Aguafina+Script" => array(
			'name' => "Aguafina Script",
			'editor_settings' => "Aguafina Script, handwriting",
			'is_google_font' => true
		),
		"Akronim" => array(
			'name' => "Akronim",
			'editor_settings' => "Akronim, display",
			'is_google_font' => true
		),
		"Aladin" => array(
			'name' => "Aladin",
			'editor_settings' => "Aladin, handwriting",
			'is_google_font' => true
		),
		"Aldrich" => array(
			'name' => "Aldrich",
			'editor_settings' => "Aldrich, sans-serif",
			'is_google_font' => true
		),
		"Alef" => array(
			'name' => "Alef",
			'editor_settings' => "Alef, sans-serif",
			'is_google_font' => true
		),
		"Alegreya" => array(
			'name' => "Alegreya",
			'editor_settings' => "Alegreya, serif",
			'is_google_font' => true
		),
		"Alegreya+SC" => array(
			'name' => "Alegreya SC",
			'editor_settings' => "Alegreya SC, serif",
			'is_google_font' => true
		),
		"Alegreya+Sans" => array(
			'name' => "Alegreya Sans",
			'editor_settings' => "Alegreya Sans, sans-serif",
			'is_google_font' => true
		),
		"Alegreya+Sans+SC" => array(
			'name' => "Alegreya Sans SC",
			'editor_settings' => "Alegreya Sans SC, sans-serif",
			'is_google_font' => true
		),
		"Alex+Brush" => array(
			'name' => "Alex Brush",
			'editor_settings' => "Alex Brush, handwriting",
			'is_google_font' => true
		),
		"Alfa+Slab+One" => array(
			'name' => "Alfa Slab One",
			'editor_settings' => "Alfa Slab One, display",
			'is_google_font' => true
		),
		"Alice" => array(
			'name' => "Alice",
			'editor_settings' => "Alice, serif",
			'is_google_font' => true
		),
		"Alike" => array(
			'name' => "Alike",
			'editor_settings' => "Alike, serif",
			'is_google_font' => true
		),
		"Alike+Angular" => array(
			'name' => "Alike Angular",
			'editor_settings' => "Alike Angular, serif",
			'is_google_font' => true
		),
		"Allan" => array(
			'name' => "Allan",
			'editor_settings' => "Allan, display",
			'is_google_font' => true
		),
		"Allerta" => array(
			'name' => "Allerta",
			'editor_settings' => "Allerta, sans-serif",
			'is_google_font' => true
		),
		"Allerta+Stencil" => array(
			'name' => "Allerta Stencil",
			'editor_settings' => "Allerta Stencil, sans-serif",
			'is_google_font' => true
		),
		"Allura" => array(
			'name' => "Allura",
			'editor_settings' => "Allura, handwriting",
			'is_google_font' => true
		),
		"Almendra" => array(
			'name' => "Almendra",
			'editor_settings' => "Almendra, serif",
			'is_google_font' => true
		),
		"Almendra+Display" => array(
			'name' => "Almendra Display",
			'editor_settings' => "Almendra Display, display",
			'is_google_font' => true
		),
		"Almendra+SC" => array(
			'name' => "Almendra SC",
			'editor_settings' => "Almendra SC, serif",
			'is_google_font' => true
		),
		"Amarante" => array(
			'name' => "Amarante",
			'editor_settings' => "Amarante, display",
			'is_google_font' => true
		),
		"Amaranth" => array(
			'name' => "Amaranth",
			'editor_settings' => "Amaranth, sans-serif",
			'is_google_font' => true
		),
		"Amatic+SC" => array(
			'name' => "Amatic SC",
			'editor_settings' => "Amatic SC, handwriting",
			'is_google_font' => true
		),
		"Amethysta" => array(
			'name' => "Amethysta",
			'editor_settings' => "Amethysta, serif",
			'is_google_font' => true
		),
		"Anaheim" => array(
			'name' => "Anaheim",
			'editor_settings' => "Anaheim, sans-serif",
			'is_google_font' => true
		),
		"Andada" => array(
			'name' => "Andada",
			'editor_settings' => "Andada, serif",
			'is_google_font' => true
		),
		"Andika" => array(
			'name' => "Andika",
			'editor_settings' => "Andika, sans-serif",
			'is_google_font' => true
		),
		"Angkor" => array(
			'name' => "Angkor",
			'editor_settings' => "Angkor, display",
			'is_google_font' => true
		),
		"Annie+Use+Your+Telescope" => array(
			'name' => "Annie Use Your Telescope",
			'editor_settings' => "Annie Use Your Telescope, handwriting",
			'is_google_font' => true
		),
		"Anonymous+Pro" => array(
			'name' => "Anonymous Pro",
			'editor_settings' => "Anonymous Pro, monospace",
			'is_google_font' => true
		),
		"Antic" => array(
			'name' => "Antic",
			'editor_settings' => "Antic, sans-serif",
			'is_google_font' => true
		),
		"Antic+Didone" => array(
			'name' => "Antic Didone",
			'editor_settings' => "Antic Didone, serif",
			'is_google_font' => true
		),
		"Antic+Slab" => array(
			'name' => "Antic Slab",
			'editor_settings' => "Antic Slab, serif",
			'is_google_font' => true
		),
		"Anton" => array(
			'name' => "Anton",
			'editor_settings' => "Anton, sans-serif",
			'is_google_font' => true
		),
		"Arapey" => array(
			'name' => "Arapey",
			'editor_settings' => "Arapey, serif",
			'is_google_font' => true
		),
		"Arbutus" => array(
			'name' => "Arbutus",
			'editor_settings' => "Arbutus, display",
			'is_google_font' => true
		),
		"Arbutus+Slab" => array(
			'name' => "Arbutus Slab",
			'editor_settings' => "Arbutus Slab, serif",
			'is_google_font' => true
		),
		"Architects+Daughter" => array(
			'name' => "Architects Daughter",
			'editor_settings' => "Architects Daughter, handwriting",
			'is_google_font' => true
		),
		"Archivo+Black" => array(
			'name' => "Archivo Black",
			'editor_settings' => "Archivo Black, sans-serif",
			'is_google_font' => true
		),
		"Archivo+Narrow" => array(
			'name' => "Archivo Narrow",
			'editor_settings' => "Archivo Narrow, sans-serif",
			'is_google_font' => true
		),
		"Arimo" => array(
			'name' => "Arimo",
			'editor_settings' => "Arimo, sans-serif",
			'is_google_font' => true
		),
		"Arizonia" => array(
			'name' => "Arizonia",
			'editor_settings' => "Arizonia, handwriting",
			'is_google_font' => true
		),
		"Armata" => array(
			'name' => "Armata",
			'editor_settings' => "Armata, sans-serif",
			'is_google_font' => true
		),
		"Artifika" => array(
			'name' => "Artifika",
			'editor_settings' => "Artifika, serif",
			'is_google_font' => true
		),
		"Arvo" => array(
			'name' => "Arvo",
			'editor_settings' => "Arvo, serif",
			'is_google_font' => true
		),
		"Asap" => array(
			'name' => "Asap",
			'editor_settings' => "Asap, sans-serif",
			'is_google_font' => true
		),
		"Asset" => array(
			'name' => "Asset",
			'editor_settings' => "Asset, display",
			'is_google_font' => true
		),
		"Astloch" => array(
			'name' => "Astloch",
			'editor_settings' => "Astloch, display",
			'is_google_font' => true
		),
		"Asul" => array(
			'name' => "Asul",
			'editor_settings' => "Asul, sans-serif",
			'is_google_font' => true
		),
		"Atomic+Age" => array(
			'name' => "Atomic Age",
			'editor_settings' => "Atomic Age, display",
			'is_google_font' => true
		),
		"Aubrey" => array(
			'name' => "Aubrey",
			'editor_settings' => "Aubrey, display",
			'is_google_font' => true
		),
		"Audiowide" => array(
			'name' => "Audiowide",
			'editor_settings' => "Audiowide, display",
			'is_google_font' => true
		),
		"Autour+One" => array(
			'name' => "Autour One",
			'editor_settings' => "Autour One, display",
			'is_google_font' => true
		),
		"Average" => array(
			'name' => "Average",
			'editor_settings' => "Average, serif",
			'is_google_font' => true
		),
		"Average+Sans" => array(
			'name' => "Average Sans",
			'editor_settings' => "Average Sans, sans-serif",
			'is_google_font' => true
		),
		"Averia+Gruesa+Libre" => array(
			'name' => "Averia Gruesa Libre",
			'editor_settings' => "Averia Gruesa Libre, display",
			'is_google_font' => true
		),
		"Averia+Libre" => array(
			'name' => "Averia Libre",
			'editor_settings' => "Averia Libre, display",
			'is_google_font' => true
		),
		"Averia+Sans+Libre" => array(
			'name' => "Averia Sans Libre",
			'editor_settings' => "Averia Sans Libre, display",
			'is_google_font' => true
		),
		"Averia+Serif+Libre" => array(
			'name' => "Averia Serif Libre",
			'editor_settings' => "Averia Serif Libre, display",
			'is_google_font' => true
		),
		"Bad+Script" => array(
			'name' => "Bad Script",
			'editor_settings' => "Bad Script, handwriting",
			'is_google_font' => true
		),
		"Balthazar" => array(
			'name' => "Balthazar",
			'editor_settings' => "Balthazar, serif",
			'is_google_font' => true
		),
		"Bangers" => array(
			'name' => "Bangers",
			'editor_settings' => "Bangers, display",
			'is_google_font' => true
		),
		"Basic" => array(
			'name' => "Basic",
			'editor_settings' => "Basic, sans-serif",
			'is_google_font' => true
		),
		"Battambang" => array(
			'name' => "Battambang",
			'editor_settings' => "Battambang, display",
			'is_google_font' => true
		),
		"Baumans" => array(
			'name' => "Baumans",
			'editor_settings' => "Baumans, display",
			'is_google_font' => true
		),
		"Bayon" => array(
			'name' => "Bayon",
			'editor_settings' => "Bayon, display",
			'is_google_font' => true
		),
		"Belgrano" => array(
			'name' => "Belgrano",
			'editor_settings' => "Belgrano, serif",
			'is_google_font' => true
		),
		"Belleza" => array(
			'name' => "Belleza",
			'editor_settings' => "Belleza, sans-serif",
			'is_google_font' => true
		),
		"BenchNine" => array(
			'name' => "BenchNine",
			'editor_settings' => "BenchNine, sans-serif",
			'is_google_font' => true
		),
		"Bentham" => array(
			'name' => "Bentham",
			'editor_settings' => "Bentham, serif",
			'is_google_font' => true
		),
		"Berkshire+Swash" => array(
			'name' => "Berkshire Swash",
			'editor_settings' => "Berkshire Swash, handwriting",
			'is_google_font' => true
		),
		"Bevan" => array(
			'name' => "Bevan",
			'editor_settings' => "Bevan, display",
			'is_google_font' => true
		),
		"Bigelow+Rules" => array(
			'name' => "Bigelow Rules",
			'editor_settings' => "Bigelow Rules, display",
			'is_google_font' => true
		),
		"Bigshot+One" => array(
			'name' => "Bigshot One",
			'editor_settings' => "Bigshot One, display",
			'is_google_font' => true
		),
		"Bilbo" => array(
			'name' => "Bilbo",
			'editor_settings' => "Bilbo, handwriting",
			'is_google_font' => true
		),
		"Bilbo+Swash+Caps" => array(
			'name' => "Bilbo Swash Caps",
			'editor_settings' => "Bilbo Swash Caps, handwriting",
			'is_google_font' => true
		),
		"Bitter" => array(
			'name' => "Bitter",
			'editor_settings' => "Bitter, serif",
			'is_google_font' => true
		),
		"Black+Ops+One" => array(
			'name' => "Black Ops One",
			'editor_settings' => "Black Ops One, display",
			'is_google_font' => true
		),
		"Bokor" => array(
			'name' => "Bokor",
			'editor_settings' => "Bokor, display",
			'is_google_font' => true
		),
		"Bonbon" => array(
			'name' => "Bonbon",
			'editor_settings' => "Bonbon, handwriting",
			'is_google_font' => true
		),
		"Boogaloo" => array(
			'name' => "Boogaloo",
			'editor_settings' => "Boogaloo, display",
			'is_google_font' => true
		),
		"Bowlby+One" => array(
			'name' => "Bowlby One",
			'editor_settings' => "Bowlby One, display",
			'is_google_font' => true
		),
		"Bowlby+One+SC" => array(
			'name' => "Bowlby One SC",
			'editor_settings' => "Bowlby One SC, display",
			'is_google_font' => true
		),
		"Brawler" => array(
			'name' => "Brawler",
			'editor_settings' => "Brawler, serif",
			'is_google_font' => true
		),
		"Bree+Serif" => array(
			'name' => "Bree Serif",
			'editor_settings' => "Bree Serif, serif",
			'is_google_font' => true
		),
		"Bubblegum+Sans" => array(
			'name' => "Bubblegum Sans",
			'editor_settings' => "Bubblegum Sans, display",
			'is_google_font' => true
		),
		"Bubbler+One" => array(
			'name' => "Bubbler One",
			'editor_settings' => "Bubbler One, sans-serif",
			'is_google_font' => true
		),
		"Buda" => array(
			'name' => "Buda",
			'editor_settings' => "Buda, display",
			'is_google_font' => true
		),
		"Buenard" => array(
			'name' => "Buenard",
			'editor_settings' => "Buenard, serif",
			'is_google_font' => true
		),
		"Butcherman" => array(
			'name' => "Butcherman",
			'editor_settings' => "Butcherman, display",
			'is_google_font' => true
		),
		"Butterfly+Kids" => array(
			'name' => "Butterfly Kids",
			'editor_settings' => "Butterfly Kids, handwriting",
			'is_google_font' => true
		),
		"Cabin" => array(
			'name' => "Cabin",
			'editor_settings' => "Cabin, sans-serif",
			'is_google_font' => true
		),
		"Cabin+Condensed" => array(
			'name' => "Cabin Condensed",
			'editor_settings' => "Cabin Condensed, sans-serif",
			'is_google_font' => true
		),
		"Cabin+Sketch" => array(
			'name' => "Cabin Sketch",
			'editor_settings' => "Cabin Sketch, display",
			'is_google_font' => true
		),
		"Caesar+Dressing" => array(
			'name' => "Caesar Dressing",
			'editor_settings' => "Caesar Dressing, display",
			'is_google_font' => true
		),
		"Cagliostro" => array(
			'name' => "Cagliostro",
			'editor_settings' => "Cagliostro, sans-serif",
			'is_google_font' => true
		),
		"Calligraffitti" => array(
			'name' => "Calligraffitti",
			'editor_settings' => "Calligraffitti, handwriting",
			'is_google_font' => true
		),
		"Cambo" => array(
			'name' => "Cambo",
			'editor_settings' => "Cambo, serif",
			'is_google_font' => true
		),
		"Candal" => array(
			'name' => "Candal",
			'editor_settings' => "Candal, sans-serif",
			'is_google_font' => true
		),
		"Cantarell" => array(
			'name' => "Cantarell",
			'editor_settings' => "Cantarell, sans-serif",
			'is_google_font' => true
		),
		"Cantata+One" => array(
			'name' => "Cantata One",
			'editor_settings' => "Cantata One, serif",
			'is_google_font' => true
		),
		"Cantora+One" => array(
			'name' => "Cantora One",
			'editor_settings' => "Cantora One, sans-serif",
			'is_google_font' => true
		),
		"Capriola" => array(
			'name' => "Capriola",
			'editor_settings' => "Capriola, sans-serif",
			'is_google_font' => true
		),
		"Cardo" => array(
			'name' => "Cardo",
			'editor_settings' => "Cardo, serif",
			'is_google_font' => true
		),
		"Carme" => array(
			'name' => "Carme",
			'editor_settings' => "Carme, sans-serif",
			'is_google_font' => true
		),
		"Carrois+Gothic" => array(
			'name' => "Carrois Gothic",
			'editor_settings' => "Carrois Gothic, sans-serif",
			'is_google_font' => true
		),
		"Carrois+Gothic+SC" => array(
			'name' => "Carrois Gothic SC",
			'editor_settings' => "Carrois Gothic SC, sans-serif",
			'is_google_font' => true
		),
		"Carter+One" => array(
			'name' => "Carter One",
			'editor_settings' => "Carter One, display",
			'is_google_font' => true
		),
		"Caudex" => array(
			'name' => "Caudex",
			'editor_settings' => "Caudex, serif",
			'is_google_font' => true
		),
		"Cedarville+Cursive" => array(
			'name' => "Cedarville Cursive",
			'editor_settings' => "Cedarville Cursive, handwriting",
			'is_google_font' => true
		),
		"Ceviche+One" => array(
			'name' => "Ceviche One",
			'editor_settings' => "Ceviche One, display",
			'is_google_font' => true
		),
		"Changa+One" => array(
			'name' => "Changa One",
			'editor_settings' => "Changa One, display",
			'is_google_font' => true
		),
		"Chango" => array(
			'name' => "Chango",
			'editor_settings' => "Chango, display",
			'is_google_font' => true
		),
		"Chau+Philomene+One" => array(
			'name' => "Chau Philomene One",
			'editor_settings' => "Chau Philomene One, sans-serif",
			'is_google_font' => true
		),
		"Chela+One" => array(
			'name' => "Chela One",
			'editor_settings' => "Chela One, display",
			'is_google_font' => true
		),
		"Chelsea+Market" => array(
			'name' => "Chelsea Market",
			'editor_settings' => "Chelsea Market, display",
			'is_google_font' => true
		),
		"Chenla" => array(
			'name' => "Chenla",
			'editor_settings' => "Chenla, display",
			'is_google_font' => true
		),
		"Cherry+Cream+Soda" => array(
			'name' => "Cherry Cream Soda",
			'editor_settings' => "Cherry Cream Soda, display",
			'is_google_font' => true
		),
		"Cherry+Swash" => array(
			'name' => "Cherry Swash",
			'editor_settings' => "Cherry Swash, display",
			'is_google_font' => true
		),
		"Chewy" => array(
			'name' => "Chewy",
			'editor_settings' => "Chewy, display",
			'is_google_font' => true
		),
		"Chicle" => array(
			'name' => "Chicle",
			'editor_settings' => "Chicle, display",
			'is_google_font' => true
		),
		"Chivo" => array(
			'name' => "Chivo",
			'editor_settings' => "Chivo, sans-serif",
			'is_google_font' => true
		),
		"Cinzel" => array(
			'name' => "Cinzel",
			'editor_settings' => "Cinzel, serif",
			'is_google_font' => true
		),
		"Cinzel+Decorative" => array(
			'name' => "Cinzel Decorative",
			'editor_settings' => "Cinzel Decorative, display",
			'is_google_font' => true
		),
		"Clicker+Script" => array(
			'name' => "Clicker Script",
			'editor_settings' => "Clicker Script, handwriting",
			'is_google_font' => true
		),
		"Coda" => array(
			'name' => "Coda",
			'editor_settings' => "Coda, display",
			'is_google_font' => true
		),
		"Coda+Caption" => array(
			'name' => "Coda Caption",
			'editor_settings' => "Coda Caption, sans-serif",
			'is_google_font' => true
		),
		"Codystar" => array(
			'name' => "Codystar",
			'editor_settings' => "Codystar, display",
			'is_google_font' => true
		),
		"Combo" => array(
			'name' => "Combo",
			'editor_settings' => "Combo, display",
			'is_google_font' => true
		),
		"Comfortaa" => array(
			'name' => "Comfortaa",
			'editor_settings' => "Comfortaa, display",
			'is_google_font' => true
		),
		"Coming+Soon" => array(
			'name' => "Coming Soon",
			'editor_settings' => "Coming Soon, handwriting",
			'is_google_font' => true
		),
		"Concert+One" => array(
			'name' => "Concert One",
			'editor_settings' => "Concert One, display",
			'is_google_font' => true
		),
		"Condiment" => array(
			'name' => "Condiment",
			'editor_settings' => "Condiment, handwriting",
			'is_google_font' => true
		),
		"Content" => array(
			'name' => "Content",
			'editor_settings' => "Content, display",
			'is_google_font' => true
		),
		"Contrail+One" => array(
			'name' => "Contrail One",
			'editor_settings' => "Contrail One, display",
			'is_google_font' => true
		),
		"Convergence" => array(
			'name' => "Convergence",
			'editor_settings' => "Convergence, sans-serif",
			'is_google_font' => true
		),
		"Cookie" => array(
			'name' => "Cookie",
			'editor_settings' => "Cookie, handwriting",
			'is_google_font' => true
		),
		"Copse" => array(
			'name' => "Copse",
			'editor_settings' => "Copse, serif",
			'is_google_font' => true
		),
		"Corben" => array(
			'name' => "Corben",
			'editor_settings' => "Corben, display",
			'is_google_font' => true
		),
		"Courgette" => array(
			'name' => "Courgette",
			'editor_settings' => "Courgette, handwriting",
			'is_google_font' => true
		),
		"Cousine" => array(
			'name' => "Cousine",
			'editor_settings' => "Cousine, monospace",
			'is_google_font' => true
		),
		"Coustard" => array(
			'name' => "Coustard",
			'editor_settings' => "Coustard, serif",
			'is_google_font' => true
		),
		"Covered+By+Your+Grace" => array(
			'name' => "Covered By Your Grace",
			'editor_settings' => "Covered By Your Grace, handwriting",
			'is_google_font' => true
		),
		"Crafty+Girls" => array(
			'name' => "Crafty Girls",
			'editor_settings' => "Crafty Girls, handwriting",
			'is_google_font' => true
		),
		"Creepster" => array(
			'name' => "Creepster",
			'editor_settings' => "Creepster, display",
			'is_google_font' => true
		),
		"Crete+Round" => array(
			'name' => "Crete Round",
			'editor_settings' => "Crete Round, serif",
			'is_google_font' => true
		),
		"Crimson+Text" => array(
			'name' => "Crimson Text",
			'editor_settings' => "Crimson Text, serif",
			'is_google_font' => true
		),
		"Croissant+One" => array(
			'name' => "Croissant One",
			'editor_settings' => "Croissant One, display",
			'is_google_font' => true
		),
		"Crushed" => array(
			'name' => "Crushed",
			'editor_settings' => "Crushed, display",
			'is_google_font' => true
		),
		"Cuprum" => array(
			'name' => "Cuprum",
			'editor_settings' => "Cuprum, sans-serif",
			'is_google_font' => true
		),
		"Cutive" => array(
			'name' => "Cutive",
			'editor_settings' => "Cutive, serif",
			'is_google_font' => true
		),
		"Cutive+Mono" => array(
			'name' => "Cutive Mono",
			'editor_settings' => "Cutive Mono, monospace",
			'is_google_font' => true
		),
		"Damion" => array(
			'name' => "Damion",
			'editor_settings' => "Damion, handwriting",
			'is_google_font' => true
		),
		"Dancing+Script" => array(
			'name' => "Dancing Script",
			'editor_settings' => "Dancing Script, handwriting",
			'is_google_font' => true
		),
		"Dangrek" => array(
			'name' => "Dangrek",
			'editor_settings' => "Dangrek, display",
			'is_google_font' => true
		),
		"Dawning+of+a+New+Day" => array(
			'name' => "Dawning of a New Day",
			'editor_settings' => "Dawning of a New Day, handwriting",
			'is_google_font' => true
		),
		"Days+One" => array(
			'name' => "Days One",
			'editor_settings' => "Days One, sans-serif",
			'is_google_font' => true
		),
		"Delius" => array(
			'name' => "Delius",
			'editor_settings' => "Delius, handwriting",
			'is_google_font' => true
		),
		"Delius+Swash+Caps" => array(
			'name' => "Delius Swash Caps",
			'editor_settings' => "Delius Swash Caps, handwriting",
			'is_google_font' => true
		),
		"Delius+Unicase" => array(
			'name' => "Delius Unicase",
			'editor_settings' => "Delius Unicase, handwriting",
			'is_google_font' => true
		),
		"Della+Respira" => array(
			'name' => "Della Respira",
			'editor_settings' => "Della Respira, serif",
			'is_google_font' => true
		),
		"Denk+One" => array(
			'name' => "Denk One",
			'editor_settings' => "Denk One, sans-serif",
			'is_google_font' => true
		),
		"Devonshire" => array(
			'name' => "Devonshire",
			'editor_settings' => "Devonshire, handwriting",
			'is_google_font' => true
		),
		"Dhurjati" => array(
			'name' => "Dhurjati",
			'editor_settings' => "Dhurjati, sans-serif",
			'is_google_font' => true
		),
		"Didact+Gothic" => array(
			'name' => "Didact Gothic",
			'editor_settings' => "Didact Gothic, sans-serif",
			'is_google_font' => true
		),
		"Diplomata" => array(
			'name' => "Diplomata",
			'editor_settings' => "Diplomata, display",
			'is_google_font' => true
		),
		"Diplomata+SC" => array(
			'name' => "Diplomata SC",
			'editor_settings' => "Diplomata SC, display",
			'is_google_font' => true
		),
		"Domine" => array(
			'name' => "Domine",
			'editor_settings' => "Domine, serif",
			'is_google_font' => true
		),
		"Donegal+One" => array(
			'name' => "Donegal One",
			'editor_settings' => "Donegal One, serif",
			'is_google_font' => true
		),
		"Doppio+One" => array(
			'name' => "Doppio One",
			'editor_settings' => "Doppio One, sans-serif",
			'is_google_font' => true
		),
		"Dorsa" => array(
			'name' => "Dorsa",
			'editor_settings' => "Dorsa, sans-serif",
			'is_google_font' => true
		),
		"Dosis" => array(
			'name' => "Dosis",
			'editor_settings' => "Dosis, sans-serif",
			'is_google_font' => true
		),
		"Dr+Sugiyama" => array(
			'name' => "Dr Sugiyama",
			'editor_settings' => "Dr Sugiyama, handwriting",
			'is_google_font' => true
		),
		"Droid+Sans" => array(
			'name' => "Droid Sans",
			'editor_settings' => "Droid Sans, sans-serif",
			'is_google_font' => true
		),
		"Droid+Sans+Mono" => array(
			'name' => "Droid Sans Mono",
			'editor_settings' => "Droid Sans Mono, monospace",
			'is_google_font' => true
		),
		"Droid+Serif" => array(
			'name' => "Droid Serif",
			'editor_settings' => "Droid Serif, serif",
			'is_google_font' => true
		),
		"Duru+Sans" => array(
			'name' => "Duru Sans",
			'editor_settings' => "Duru Sans, sans-serif",
			'is_google_font' => true
		),
		"Dynalight" => array(
			'name' => "Dynalight",
			'editor_settings' => "Dynalight, display",
			'is_google_font' => true
		),
		"EB+Garamond" => array(
			'name' => "EB Garamond",
			'editor_settings' => "EB Garamond, serif",
			'is_google_font' => true
		),
		"Eagle+Lake" => array(
			'name' => "Eagle Lake",
			'editor_settings' => "Eagle Lake, handwriting",
			'is_google_font' => true
		),
		"Eater" => array(
			'name' => "Eater",
			'editor_settings' => "Eater, display",
			'is_google_font' => true
		),
		"Economica" => array(
			'name' => "Economica",
			'editor_settings' => "Economica, sans-serif",
			'is_google_font' => true
		),
		"Ek+Mukta" => array(
			'name' => "Ek Mukta",
			'editor_settings' => "Ek Mukta, sans-serif",
			'is_google_font' => true
		),
		"Electrolize" => array(
			'name' => "Electrolize",
			'editor_settings' => "Electrolize, sans-serif",
			'is_google_font' => true
		),
		"Elsie" => array(
			'name' => "Elsie",
			'editor_settings' => "Elsie, display",
			'is_google_font' => true
		),
		"Elsie+Swash+Caps" => array(
			'name' => "Elsie Swash Caps",
			'editor_settings' => "Elsie Swash Caps, display",
			'is_google_font' => true
		),
		"Emblema+One" => array(
			'name' => "Emblema One",
			'editor_settings' => "Emblema One, display",
			'is_google_font' => true
		),
		"Emilys+Candy" => array(
			'name' => "Emilys Candy",
			'editor_settings' => "Emilys Candy, display",
			'is_google_font' => true
		),
		"Engagement" => array(
			'name' => "Engagement",
			'editor_settings' => "Engagement, handwriting",
			'is_google_font' => true
		),
		"Englebert" => array(
			'name' => "Englebert",
			'editor_settings' => "Englebert, sans-serif",
			'is_google_font' => true
		),
		"Enriqueta" => array(
			'name' => "Enriqueta",
			'editor_settings' => "Enriqueta, serif",
			'is_google_font' => true
		),
		"Erica+One" => array(
			'name' => "Erica One",
			'editor_settings' => "Erica One, display",
			'is_google_font' => true
		),
		"Esteban" => array(
			'name' => "Esteban",
			'editor_settings' => "Esteban, serif",
			'is_google_font' => true
		),
		"Euphoria+Script" => array(
			'name' => "Euphoria Script",
			'editor_settings' => "Euphoria Script, handwriting",
			'is_google_font' => true
		),
		"Ewert" => array(
			'name' => "Ewert",
			'editor_settings' => "Ewert, display",
			'is_google_font' => true
		),
		"Exo" => array(
			'name' => "Exo",
			'editor_settings' => "Exo, sans-serif",
			'is_google_font' => true
		),
		"Exo+2" => array(
			'name' => "Exo 2",
			'editor_settings' => "Exo 2, sans-serif",
			'is_google_font' => true
		),
		"Expletus+Sans" => array(
			'name' => "Expletus Sans",
			'editor_settings' => "Expletus Sans, display",
			'is_google_font' => true
		),
		"Fanwood+Text" => array(
			'name' => "Fanwood Text",
			'editor_settings' => "Fanwood Text, serif",
			'is_google_font' => true
		),
		"Fascinate" => array(
			'name' => "Fascinate",
			'editor_settings' => "Fascinate, display",
			'is_google_font' => true
		),
		"Fascinate+Inline" => array(
			'name' => "Fascinate Inline",
			'editor_settings' => "Fascinate Inline, display",
			'is_google_font' => true
		),
		"Faster+One" => array(
			'name' => "Faster One",
			'editor_settings' => "Faster One, display",
			'is_google_font' => true
		),
		"Fasthand" => array(
			'name' => "Fasthand",
			'editor_settings' => "Fasthand, serif",
			'is_google_font' => true
		),
		"Fauna+One" => array(
			'name' => "Fauna One",
			'editor_settings' => "Fauna One, serif",
			'is_google_font' => true
		),
		"Federant" => array(
			'name' => "Federant",
			'editor_settings' => "Federant, display",
			'is_google_font' => true
		),
		"Federo" => array(
			'name' => "Federo",
			'editor_settings' => "Federo, sans-serif",
			'is_google_font' => true
		),
		"Felipa" => array(
			'name' => "Felipa",
			'editor_settings' => "Felipa, handwriting",
			'is_google_font' => true
		),
		"Fenix" => array(
			'name' => "Fenix",
			'editor_settings' => "Fenix, serif",
			'is_google_font' => true
		),
		"Finger+Paint" => array(
			'name' => "Finger Paint",
			'editor_settings' => "Finger Paint, display",
			'is_google_font' => true
		),
		"Fira+Mono" => array(
			'name' => "Fira Mono",
			'editor_settings' => "Fira Mono, monospace",
			'is_google_font' => true
		),
		"Fira+Sans" => array(
			'name' => "Fira Sans",
			'editor_settings' => "Fira Sans, sans-serif",
			'is_google_font' => true
		),
		"Fjalla+One" => array(
			'name' => "Fjalla One",
			'editor_settings' => "Fjalla One, sans-serif",
			'is_google_font' => true
		),
		"Fjord+One" => array(
			'name' => "Fjord One",
			'editor_settings' => "Fjord One, serif",
			'is_google_font' => true
		),
		"Flamenco" => array(
			'name' => "Flamenco",
			'editor_settings' => "Flamenco, display",
			'is_google_font' => true
		),
		"Flavors" => array(
			'name' => "Flavors",
			'editor_settings' => "Flavors, display",
			'is_google_font' => true
		),
		"Fondamento" => array(
			'name' => "Fondamento",
			'editor_settings' => "Fondamento, handwriting",
			'is_google_font' => true
		),
		"Fontdiner+Swanky" => array(
			'name' => "Fontdiner Swanky",
			'editor_settings' => "Fontdiner Swanky, display",
			'is_google_font' => true
		),
		"Forum" => array(
			'name' => "Forum",
			'editor_settings' => "Forum, display",
			'is_google_font' => true
		),
		"Francois+One" => array(
			'name' => "Francois One",
			'editor_settings' => "Francois One, sans-serif",
			'is_google_font' => true
		),
		"Freckle+Face" => array(
			'name' => "Freckle Face",
			'editor_settings' => "Freckle Face, display",
			'is_google_font' => true
		),
		"Fredericka+the+Great" => array(
			'name' => "Fredericka the Great",
			'editor_settings' => "Fredericka the Great, display",
			'is_google_font' => true
		),
		"Fredoka+One" => array(
			'name' => "Fredoka One",
			'editor_settings' => "Fredoka One, display",
			'is_google_font' => true
		),
		"Freehand" => array(
			'name' => "Freehand",
			'editor_settings' => "Freehand, display",
			'is_google_font' => true
		),
		"Fresca" => array(
			'name' => "Fresca",
			'editor_settings' => "Fresca, sans-serif",
			'is_google_font' => true
		),
		"Frijole" => array(
			'name' => "Frijole",
			'editor_settings' => "Frijole, display",
			'is_google_font' => true
		),
		"Fruktur" => array(
			'name' => "Fruktur",
			'editor_settings' => "Fruktur, display",
			'is_google_font' => true
		),
		"Fugaz+One" => array(
			'name' => "Fugaz One",
			'editor_settings' => "Fugaz One, display",
			'is_google_font' => true
		),
		"GFS+Didot" => array(
			'name' => "GFS Didot",
			'editor_settings' => "GFS Didot, serif",
			'is_google_font' => true
		),
		"GFS+Neohellenic" => array(
			'name' => "GFS Neohellenic",
			'editor_settings' => "GFS Neohellenic, sans-serif",
			'is_google_font' => true
		),
		"Gabriela" => array(
			'name' => "Gabriela",
			'editor_settings' => "Gabriela, serif",
			'is_google_font' => true
		),
		"Gafata" => array(
			'name' => "Gafata",
			'editor_settings' => "Gafata, sans-serif",
			'is_google_font' => true
		),
		"Galdeano" => array(
			'name' => "Galdeano",
			'editor_settings' => "Galdeano, sans-serif",
			'is_google_font' => true
		),
		"Galindo" => array(
			'name' => "Galindo",
			'editor_settings' => "Galindo, display",
			'is_google_font' => true
		),
		"Gentium+Basic" => array(
			'name' => "Gentium Basic",
			'editor_settings' => "Gentium Basic, serif",
			'is_google_font' => true
		),
		"Gentium+Book+Basic" => array(
			'name' => "Gentium Book Basic",
			'editor_settings' => "Gentium Book Basic, serif",
			'is_google_font' => true
		),
		"Geo" => array(
			'name' => "Geo",
			'editor_settings' => "Geo, sans-serif",
			'is_google_font' => true
		),
		"Geostar" => array(
			'name' => "Geostar",
			'editor_settings' => "Geostar, display",
			'is_google_font' => true
		),
		"Geostar+Fill" => array(
			'name' => "Geostar Fill",
			'editor_settings' => "Geostar Fill, display",
			'is_google_font' => true
		),
		"Germania+One" => array(
			'name' => "Germania One",
			'editor_settings' => "Germania One, display",
			'is_google_font' => true
		),
		"Gidugu" => array(
			'name' => "Gidugu",
			'editor_settings' => "Gidugu, sans-serif",
			'is_google_font' => true
		),
		"Gilda+Display" => array(
			'name' => "Gilda Display",
			'editor_settings' => "Gilda Display, serif",
			'is_google_font' => true
		),
		"Give+You+Glory" => array(
			'name' => "Give You Glory",
			'editor_settings' => "Give You Glory, handwriting",
			'is_google_font' => true
		),
		"Glass+Antiqua" => array(
			'name' => "Glass Antiqua",
			'editor_settings' => "Glass Antiqua, display",
			'is_google_font' => true
		),
		"Glegoo" => array(
			'name' => "Glegoo",
			'editor_settings' => "Glegoo, serif",
			'is_google_font' => true
		),
		"Gloria+Hallelujah" => array(
			'name' => "Gloria Hallelujah",
			'editor_settings' => "Gloria Hallelujah, handwriting",
			'is_google_font' => true
		),
		"Goblin+One" => array(
			'name' => "Goblin One",
			'editor_settings' => "Goblin One, display",
			'is_google_font' => true
		),
		"Gochi+Hand" => array(
			'name' => "Gochi Hand",
			'editor_settings' => "Gochi Hand, handwriting",
			'is_google_font' => true
		),
		"Gorditas" => array(
			'name' => "Gorditas",
			'editor_settings' => "Gorditas, display",
			'is_google_font' => true
		),
		"Goudy+Bookletter+1911" => array(
			'name' => "Goudy Bookletter 1911",
			'editor_settings' => "Goudy Bookletter 1911, serif",
			'is_google_font' => true
		),
		"Graduate" => array(
			'name' => "Graduate",
			'editor_settings' => "Graduate, display",
			'is_google_font' => true
		),
		"Grand+Hotel" => array(
			'name' => "Grand Hotel",
			'editor_settings' => "Grand Hotel, handwriting",
			'is_google_font' => true
		),
		"Gravitas+One" => array(
			'name' => "Gravitas One",
			'editor_settings' => "Gravitas One, display",
			'is_google_font' => true
		),
		"Great+Vibes" => array(
			'name' => "Great Vibes",
			'editor_settings' => "Great Vibes, handwriting",
			'is_google_font' => true
		),
		"Griffy" => array(
			'name' => "Griffy",
			'editor_settings' => "Griffy, display",
			'is_google_font' => true
		),
		"Gruppo" => array(
			'name' => "Gruppo",
			'editor_settings' => "Gruppo, display",
			'is_google_font' => true
		),
		"Gudea" => array(
			'name' => "Gudea",
			'editor_settings' => "Gudea, sans-serif",
			'is_google_font' => true
		),
		"Habibi" => array(
			'name' => "Habibi",
			'editor_settings' => "Habibi, serif",
			'is_google_font' => true
		),
		"Halant" => array(
			'name' => "Halant",
			'editor_settings' => "Halant, serif",
			'is_google_font' => true
		),
		"Hammersmith+One" => array(
			'name' => "Hammersmith One",
			'editor_settings' => "Hammersmith One, sans-serif",
			'is_google_font' => true
		),
		"Hanalei" => array(
			'name' => "Hanalei",
			'editor_settings' => "Hanalei, display",
			'is_google_font' => true
		),
		"Hanalei+Fill" => array(
			'name' => "Hanalei Fill",
			'editor_settings' => "Hanalei Fill, display",
			'is_google_font' => true
		),
		"Handlee" => array(
			'name' => "Handlee",
			'editor_settings' => "Handlee, handwriting",
			'is_google_font' => true
		),
		"Hanuman" => array(
			'name' => "Hanuman",
			'editor_settings' => "Hanuman, serif",
			'is_google_font' => true
		),
		"Happy+Monkey" => array(
			'name' => "Happy Monkey",
			'editor_settings' => "Happy Monkey, display",
			'is_google_font' => true
		),
		"Headland+One" => array(
			'name' => "Headland One",
			'editor_settings' => "Headland One, serif",
			'is_google_font' => true
		),
		"Henny+Penny" => array(
			'name' => "Henny Penny",
			'editor_settings' => "Henny Penny, display",
			'is_google_font' => true
		),
		"Herr+Von+Muellerhoff" => array(
			'name' => "Herr Von Muellerhoff",
			'editor_settings' => "Herr Von Muellerhoff, handwriting",
			'is_google_font' => true
		),
		"Hind" => array(
			'name' => "Hind",
			'editor_settings' => "Hind, sans-serif",
			'is_google_font' => true
		),
		"Holtwood+One+SC" => array(
			'name' => "Holtwood One SC",
			'editor_settings' => "Holtwood One SC, serif",
			'is_google_font' => true
		),
		"Homemade+Apple" => array(
			'name' => "Homemade Apple",
			'editor_settings' => "Homemade Apple, handwriting",
			'is_google_font' => true
		),
		"Homenaje" => array(
			'name' => "Homenaje",
			'editor_settings' => "Homenaje, sans-serif",
			'is_google_font' => true
		),
		"IM+Fell+DW+Pica" => array(
			'name' => "IM Fell DW Pica",
			'editor_settings' => "IM Fell DW Pica, serif",
			'is_google_font' => true
		),
		"IM+Fell+DW+Pica+SC" => array(
			'name' => "IM Fell DW Pica SC",
			'editor_settings' => "IM Fell DW Pica SC, serif",
			'is_google_font' => true
		),
		"IM+Fell+Double+Pica" => array(
			'name' => "IM Fell Double Pica",
			'editor_settings' => "IM Fell Double Pica, serif",
			'is_google_font' => true
		),
		"IM+Fell+Double+Pica+SC" => array(
			'name' => "IM Fell Double Pica SC",
			'editor_settings' => "IM Fell Double Pica SC, serif",
			'is_google_font' => true
		),
		"IM+Fell+English" => array(
			'name' => "IM Fell English",
			'editor_settings' => "IM Fell English, serif",
			'is_google_font' => true
		),
		"IM+Fell+English+SC" => array(
			'name' => "IM Fell English SC",
			'editor_settings' => "IM Fell English SC, serif",
			'is_google_font' => true
		),
		"IM+Fell+French+Canon" => array(
			'name' => "IM Fell French Canon",
			'editor_settings' => "IM Fell French Canon, serif",
			'is_google_font' => true
		),
		"IM+Fell+French+Canon+SC" => array(
			'name' => "IM Fell French Canon SC",
			'editor_settings' => "IM Fell French Canon SC, serif",
			'is_google_font' => true
		),
		"IM+Fell+Great+Primer" => array(
			'name' => "IM Fell Great Primer",
			'editor_settings' => "IM Fell Great Primer, serif",
			'is_google_font' => true
		),
		"IM+Fell+Great+Primer+SC" => array(
			'name' => "IM Fell Great Primer SC",
			'editor_settings' => "IM Fell Great Primer SC, serif",
			'is_google_font' => true
		),
		"Iceberg" => array(
			'name' => "Iceberg",
			'editor_settings' => "Iceberg, display",
			'is_google_font' => true
		),
		"Iceland" => array(
			'name' => "Iceland",
			'editor_settings' => "Iceland, display",
			'is_google_font' => true
		),
		"Imprima" => array(
			'name' => "Imprima",
			'editor_settings' => "Imprima, sans-serif",
			'is_google_font' => true
		),
		"Inconsolata" => array(
			'name' => "Inconsolata",
			'editor_settings' => "Inconsolata, monospace",
			'is_google_font' => true
		),
		"Inder" => array(
			'name' => "Inder",
			'editor_settings' => "Inder, sans-serif",
			'is_google_font' => true
		),
		"Indie+Flower" => array(
			'name' => "Indie Flower",
			'editor_settings' => "Indie Flower, handwriting",
			'is_google_font' => true
		),
		"Inika" => array(
			'name' => "Inika",
			'editor_settings' => "Inika, serif",
			'is_google_font' => true
		),
		"Irish+Grover" => array(
			'name' => "Irish Grover",
			'editor_settings' => "Irish Grover, display",
			'is_google_font' => true
		),
		"Istok+Web" => array(
			'name' => "Istok Web",
			'editor_settings' => "Istok Web, sans-serif",
			'is_google_font' => true
		),
		"Italiana" => array(
			'name' => "Italiana",
			'editor_settings' => "Italiana, serif",
			'is_google_font' => true
		),
		"Italianno" => array(
			'name' => "Italianno",
			'editor_settings' => "Italianno, handwriting",
			'is_google_font' => true
		),
		"Jacques+Francois" => array(
			'name' => "Jacques Francois",
			'editor_settings' => "Jacques Francois, serif",
			'is_google_font' => true
		),
		"Jacques+Francois+Shadow" => array(
			'name' => "Jacques Francois Shadow",
			'editor_settings' => "Jacques Francois Shadow, display",
			'is_google_font' => true
		),
		"Jim+Nightshade" => array(
			'name' => "Jim Nightshade",
			'editor_settings' => "Jim Nightshade, handwriting",
			'is_google_font' => true
		),
		"Jockey+One" => array(
			'name' => "Jockey One",
			'editor_settings' => "Jockey One, sans-serif",
			'is_google_font' => true
		),
		"Jolly+Lodger" => array(
			'name' => "Jolly Lodger",
			'editor_settings' => "Jolly Lodger, display",
			'is_google_font' => true
		),
		"Josefin+Sans" => array(
			'name' => "Josefin Sans",
			'editor_settings' => "Josefin Sans, sans-serif",
			'is_google_font' => true
		),
		"Josefin+Slab" => array(
			'name' => "Josefin Slab",
			'editor_settings' => "Josefin Slab, serif",
			'is_google_font' => true
		),
		"Joti+One" => array(
			'name' => "Joti One",
			'editor_settings' => "Joti One, display",
			'is_google_font' => true
		),
		"Judson" => array(
			'name' => "Judson",
			'editor_settings' => "Judson, serif",
			'is_google_font' => true
		),
		"Julee" => array(
			'name' => "Julee",
			'editor_settings' => "Julee, handwriting",
			'is_google_font' => true
		),
		"Julius+Sans+One" => array(
			'name' => "Julius Sans One",
			'editor_settings' => "Julius Sans One, sans-serif",
			'is_google_font' => true
		),
		"Junge" => array(
			'name' => "Junge",
			'editor_settings' => "Junge, serif",
			'is_google_font' => true
		),
		"Jura" => array(
			'name' => "Jura",
			'editor_settings' => "Jura, sans-serif",
			'is_google_font' => true
		),
		"Just+Another+Hand" => array(
			'name' => "Just Another Hand",
			'editor_settings' => "Just Another Hand, handwriting",
			'is_google_font' => true
		),
		"Just+Me+Again+Down+Here" => array(
			'name' => "Just Me Again Down Here",
			'editor_settings' => "Just Me Again Down Here, handwriting",
			'is_google_font' => true
		),
		"Kalam" => array(
			'name' => "Kalam",
			'editor_settings' => "Kalam, handwriting",
			'is_google_font' => true
		),
		"Kameron" => array(
			'name' => "Kameron",
			'editor_settings' => "Kameron, serif",
			'is_google_font' => true
		),
		"Kantumruy" => array(
			'name' => "Kantumruy",
			'editor_settings' => "Kantumruy, sans-serif",
			'is_google_font' => true
		),
		"Karla" => array(
			'name' => "Karla",
			'editor_settings' => "Karla, sans-serif",
			'is_google_font' => true
		),
		"Karma" => array(
			'name' => "Karma",
			'editor_settings' => "Karma, serif",
			'is_google_font' => true
		),
		"Kaushan+Script" => array(
			'name' => "Kaushan Script",
			'editor_settings' => "Kaushan Script, handwriting",
			'is_google_font' => true
		),
		"Kavoon" => array(
			'name' => "Kavoon",
			'editor_settings' => "Kavoon, display",
			'is_google_font' => true
		),
		"Kdam+Thmor" => array(
			'name' => "Kdam Thmor",
			'editor_settings' => "Kdam Thmor, display",
			'is_google_font' => true
		),
		"Keania+One" => array(
			'name' => "Keania One",
			'editor_settings' => "Keania One, display",
			'is_google_font' => true
		),
		"Kelly+Slab" => array(
			'name' => "Kelly Slab",
			'editor_settings' => "Kelly Slab, display",
			'is_google_font' => true
		),
		"Kenia" => array(
			'name' => "Kenia",
			'editor_settings' => "Kenia, display",
			'is_google_font' => true
		),
		"Khand" => array(
			'name' => "Khand",
			'editor_settings' => "Khand, sans-serif",
			'is_google_font' => true
		),
		"Khmer" => array(
			'name' => "Khmer",
			'editor_settings' => "Khmer, display",
			'is_google_font' => true
		),
		"Kite+One" => array(
			'name' => "Kite One",
			'editor_settings' => "Kite One, sans-serif",
			'is_google_font' => true
		),
		"Knewave" => array(
			'name' => "Knewave",
			'editor_settings' => "Knewave, display",
			'is_google_font' => true
		),
		"Kotta+One" => array(
			'name' => "Kotta One",
			'editor_settings' => "Kotta One, serif",
			'is_google_font' => true
		),
		"Koulen" => array(
			'name' => "Koulen",
			'editor_settings' => "Koulen, display",
			'is_google_font' => true
		),
		"Kranky" => array(
			'name' => "Kranky",
			'editor_settings' => "Kranky, display",
			'is_google_font' => true
		),
		"Kreon" => array(
			'name' => "Kreon",
			'editor_settings' => "Kreon, serif",
			'is_google_font' => true
		),
		"Kristi" => array(
			'name' => "Kristi",
			'editor_settings' => "Kristi, handwriting",
			'is_google_font' => true
		),
		"Krona+One" => array(
			'name' => "Krona One",
			'editor_settings' => "Krona One, sans-serif",
			'is_google_font' => true
		),
		"La+Belle+Aurore" => array(
			'name' => "La Belle Aurore",
			'editor_settings' => "La Belle Aurore, handwriting",
			'is_google_font' => true
		),
		"Laila" => array(
			'name' => "Laila",
			'editor_settings' => "Laila, serif",
			'is_google_font' => true
		),
		"Lancelot" => array(
			'name' => "Lancelot",
			'editor_settings' => "Lancelot, display",
			'is_google_font' => true
		),
		"Lato" => array(
			'name' => "Lato",
			'editor_settings' => "Lato, sans-serif",
			'is_google_font' => true
		),
		"League+Script" => array(
			'name' => "League Script",
			'editor_settings' => "League Script, handwriting",
			'is_google_font' => true
		),
		"Leckerli+One" => array(
			'name' => "Leckerli One",
			'editor_settings' => "Leckerli One, handwriting",
			'is_google_font' => true
		),
		"Ledger" => array(
			'name' => "Ledger",
			'editor_settings' => "Ledger, serif",
			'is_google_font' => true
		),
		"Lekton" => array(
			'name' => "Lekton",
			'editor_settings' => "Lekton, sans-serif",
			'is_google_font' => true
		),
		"Lemon" => array(
			'name' => "Lemon",
			'editor_settings' => "Lemon, display",
			'is_google_font' => true
		),
		"Libre+Baskerville" => array(
			'name' => "Libre Baskerville",
			'editor_settings' => "Libre Baskerville, serif",
			'is_google_font' => true
		),
		"Life+Savers" => array(
			'name' => "Life Savers",
			'editor_settings' => "Life Savers, display",
			'is_google_font' => true
		),
		"Lilita+One" => array(
			'name' => "Lilita One",
			'editor_settings' => "Lilita One, display",
			'is_google_font' => true
		),
		"Lily+Script+One" => array(
			'name' => "Lily Script One",
			'editor_settings' => "Lily Script One, display",
			'is_google_font' => true
		),
		"Limelight" => array(
			'name' => "Limelight",
			'editor_settings' => "Limelight, display",
			'is_google_font' => true
		),
		"Linden+Hill" => array(
			'name' => "Linden Hill",
			'editor_settings' => "Linden Hill, serif",
			'is_google_font' => true
		),
		"Lobster" => array(
			'name' => "Lobster",
			'editor_settings' => "Lobster, display",
			'is_google_font' => true
		),
		"Lobster+Two" => array(
			'name' => "Lobster Two",
			'editor_settings' => "Lobster Two, display",
			'is_google_font' => true
		),
		"Londrina+Outline" => array(
			'name' => "Londrina Outline",
			'editor_settings' => "Londrina Outline, display",
			'is_google_font' => true
		),
		"Londrina+Shadow" => array(
			'name' => "Londrina Shadow",
			'editor_settings' => "Londrina Shadow, display",
			'is_google_font' => true
		),
		"Londrina+Sketch" => array(
			'name' => "Londrina Sketch",
			'editor_settings' => "Londrina Sketch, display",
			'is_google_font' => true
		),
		"Londrina+Solid" => array(
			'name' => "Londrina Solid",
			'editor_settings' => "Londrina Solid, display",
			'is_google_font' => true
		),
		"Lora" => array(
			'name' => "Lora",
			'editor_settings' => "Lora, serif",
			'is_google_font' => true
		),
		"Love+Ya+Like+A+Sister" => array(
			'name' => "Love Ya Like A Sister",
			'editor_settings' => "Love Ya Like A Sister, display",
			'is_google_font' => true
		),
		"Loved+by+the+King" => array(
			'name' => "Loved by the King",
			'editor_settings' => "Loved by the King, handwriting",
			'is_google_font' => true
		),
		"Lovers+Quarrel" => array(
			'name' => "Lovers Quarrel",
			'editor_settings' => "Lovers Quarrel, handwriting",
			'is_google_font' => true
		),
		"Luckiest+Guy" => array(
			'name' => "Luckiest Guy",
			'editor_settings' => "Luckiest Guy, display",
			'is_google_font' => true
		),
		"Lusitana" => array(
			'name' => "Lusitana",
			'editor_settings' => "Lusitana, serif",
			'is_google_font' => true
		),
		"Lustria" => array(
			'name' => "Lustria",
			'editor_settings' => "Lustria, serif",
			'is_google_font' => true
		),
		"Macondo" => array(
			'name' => "Macondo",
			'editor_settings' => "Macondo, display",
			'is_google_font' => true
		),
		"Macondo+Swash+Caps" => array(
			'name' => "Macondo Swash Caps",
			'editor_settings' => "Macondo Swash Caps, display",
			'is_google_font' => true
		),
		"Magra" => array(
			'name' => "Magra",
			'editor_settings' => "Magra, sans-serif",
			'is_google_font' => true
		),
		"Maiden+Orange" => array(
			'name' => "Maiden Orange",
			'editor_settings' => "Maiden Orange, display",
			'is_google_font' => true
		),
		"Mako" => array(
			'name' => "Mako",
			'editor_settings' => "Mako, sans-serif",
			'is_google_font' => true
		),
		"Mallanna" => array(
			'name' => "Mallanna",
			'editor_settings' => "Mallanna, sans-serif",
			'is_google_font' => true
		),
		"Mandali" => array(
			'name' => "Mandali",
			'editor_settings' => "Mandali, sans-serif",
			'is_google_font' => true
		),
		"Marcellus" => array(
			'name' => "Marcellus",
			'editor_settings' => "Marcellus, serif",
			'is_google_font' => true
		),
		"Marcellus+SC" => array(
			'name' => "Marcellus SC",
			'editor_settings' => "Marcellus SC, serif",
			'is_google_font' => true
		),
		"Marck+Script" => array(
			'name' => "Marck Script",
			'editor_settings' => "Marck Script, handwriting",
			'is_google_font' => true
		),
		"Margarine" => array(
			'name' => "Margarine",
			'editor_settings' => "Margarine, display",
			'is_google_font' => true
		),
		"Marko+One" => array(
			'name' => "Marko One",
			'editor_settings' => "Marko One, serif",
			'is_google_font' => true
		),
		"Marmelad" => array(
			'name' => "Marmelad",
			'editor_settings' => "Marmelad, sans-serif",
			'is_google_font' => true
		),
		"Marvel" => array(
			'name' => "Marvel",
			'editor_settings' => "Marvel, sans-serif",
			'is_google_font' => true
		),
		"Mate" => array(
			'name' => "Mate",
			'editor_settings' => "Mate, serif",
			'is_google_font' => true
		),
		"Mate+SC" => array(
			'name' => "Mate SC",
			'editor_settings' => "Mate SC, serif",
			'is_google_font' => true
		),
		"Maven+Pro" => array(
			'name' => "Maven Pro",
			'editor_settings' => "Maven Pro, sans-serif",
			'is_google_font' => true
		),
		"McLaren" => array(
			'name' => "McLaren",
			'editor_settings' => "McLaren, display",
			'is_google_font' => true
		),
		"Meddon" => array(
			'name' => "Meddon",
			'editor_settings' => "Meddon, handwriting",
			'is_google_font' => true
		),
		"MedievalSharp" => array(
			'name' => "MedievalSharp",
			'editor_settings' => "MedievalSharp, display",
			'is_google_font' => true
		),
		"Medula+One" => array(
			'name' => "Medula One",
			'editor_settings' => "Medula One, display",
			'is_google_font' => true
		),
		"Megrim" => array(
			'name' => "Megrim",
			'editor_settings' => "Megrim, display",
			'is_google_font' => true
		),
		"Meie+Script" => array(
			'name' => "Meie Script",
			'editor_settings' => "Meie Script, handwriting",
			'is_google_font' => true
		),
		"Merienda" => array(
			'name' => "Merienda",
			'editor_settings' => "Merienda, handwriting",
			'is_google_font' => true
		),
		"Merienda+One" => array(
			'name' => "Merienda One",
			'editor_settings' => "Merienda One, handwriting",
			'is_google_font' => true
		),
		"Merriweather" => array(
			'name' => "Merriweather",
			'editor_settings' => "Merriweather, serif",
			'is_google_font' => true
		),
		"Merriweather+Sans" => array(
			'name' => "Merriweather Sans",
			'editor_settings' => "Merriweather Sans, sans-serif",
			'is_google_font' => true
		),
		"Metal" => array(
			'name' => "Metal",
			'editor_settings' => "Metal, display",
			'is_google_font' => true
		),
		"Metal+Mania" => array(
			'name' => "Metal Mania",
			'editor_settings' => "Metal Mania, display",
			'is_google_font' => true
		),
		"Metamorphous" => array(
			'name' => "Metamorphous",
			'editor_settings' => "Metamorphous, display",
			'is_google_font' => true
		),
		"Metrophobic" => array(
			'name' => "Metrophobic",
			'editor_settings' => "Metrophobic, sans-serif",
			'is_google_font' => true
		),
		"Michroma" => array(
			'name' => "Michroma",
			'editor_settings' => "Michroma, sans-serif",
			'is_google_font' => true
		),
		"Milonga" => array(
			'name' => "Milonga",
			'editor_settings' => "Milonga, display",
			'is_google_font' => true
		),
		"Miltonian" => array(
			'name' => "Miltonian",
			'editor_settings' => "Miltonian, display",
			'is_google_font' => true
		),
		"Miltonian+Tattoo" => array(
			'name' => "Miltonian Tattoo",
			'editor_settings' => "Miltonian Tattoo, display",
			'is_google_font' => true
		),
		"Miniver" => array(
			'name' => "Miniver",
			'editor_settings' => "Miniver, display",
			'is_google_font' => true
		),
		"Miss+Fajardose" => array(
			'name' => "Miss Fajardose",
			'editor_settings' => "Miss Fajardose, handwriting",
			'is_google_font' => true
		),
		"Modern+Antiqua" => array(
			'name' => "Modern Antiqua",
			'editor_settings' => "Modern Antiqua, display",
			'is_google_font' => true
		),
		"Molengo" => array(
			'name' => "Molengo",
			'editor_settings' => "Molengo, sans-serif",
			'is_google_font' => true
		),
		"Molle" => array(
			'name' => "Molle",
			'editor_settings' => "Molle, handwriting",
			'is_google_font' => true
		),
		"Monda" => array(
			'name' => "Monda",
			'editor_settings' => "Monda, sans-serif",
			'is_google_font' => true
		),
		"Monofett" => array(
			'name' => "Monofett",
			'editor_settings' => "Monofett, display",
			'is_google_font' => true
		),
		"Monoton" => array(
			'name' => "Monoton",
			'editor_settings' => "Monoton, display",
			'is_google_font' => true
		),
		"Monsieur+La+Doulaise" => array(
			'name' => "Monsieur La Doulaise",
			'editor_settings' => "Monsieur La Doulaise, handwriting",
			'is_google_font' => true
		),
		"Montaga" => array(
			'name' => "Montaga",
			'editor_settings' => "Montaga, serif",
			'is_google_font' => true
		),
		"Montez" => array(
			'name' => "Montez",
			'editor_settings' => "Montez, handwriting",
			'is_google_font' => true
		),
		"Montserrat" => array(
			'name' => "Montserrat",
			'editor_settings' => "Montserrat, sans-serif",
			'is_google_font' => true
		),
		"Montserrat+Alternates" => array(
			'name' => "Montserrat Alternates",
			'editor_settings' => "Montserrat Alternates, sans-serif",
			'is_google_font' => true
		),
		"Montserrat+Subrayada" => array(
			'name' => "Montserrat Subrayada",
			'editor_settings' => "Montserrat Subrayada, sans-serif",
			'is_google_font' => true
		),
		"Moul" => array(
			'name' => "Moul",
			'editor_settings' => "Moul, display",
			'is_google_font' => true
		),
		"Moulpali" => array(
			'name' => "Moulpali",
			'editor_settings' => "Moulpali, display",
			'is_google_font' => true
		),
		"Mountains+of+Christmas" => array(
			'name' => "Mountains of Christmas",
			'editor_settings' => "Mountains of Christmas, display",
			'is_google_font' => true
		),
		"Mouse+Memoirs" => array(
			'name' => "Mouse Memoirs",
			'editor_settings' => "Mouse Memoirs, sans-serif",
			'is_google_font' => true
		),
		"Mr+Bedfort" => array(
			'name' => "Mr Bedfort",
			'editor_settings' => "Mr Bedfort, handwriting",
			'is_google_font' => true
		),
		"Mr+Dafoe" => array(
			'name' => "Mr Dafoe",
			'editor_settings' => "Mr Dafoe, handwriting",
			'is_google_font' => true
		),
		"Mr+De+Haviland" => array(
			'name' => "Mr De Haviland",
			'editor_settings' => "Mr De Haviland, handwriting",
			'is_google_font' => true
		),
		"Mrs+Saint+Delafield" => array(
			'name' => "Mrs Saint Delafield",
			'editor_settings' => "Mrs Saint Delafield, handwriting",
			'is_google_font' => true
		),
		"Mrs+Sheppards" => array(
			'name' => "Mrs Sheppards",
			'editor_settings' => "Mrs Sheppards, handwriting",
			'is_google_font' => true
		),
		"Muli" => array(
			'name' => "Muli",
			'editor_settings' => "Muli, sans-serif",
			'is_google_font' => true
		),
		"Mystery+Quest" => array(
			'name' => "Mystery Quest",
			'editor_settings' => "Mystery Quest, display",
			'is_google_font' => true
		),
		"NTR" => array(
			'name' => "NTR",
			'editor_settings' => "NTR, sans-serif",
			'is_google_font' => true
		),
		"Neucha" => array(
			'name' => "Neucha",
			'editor_settings' => "Neucha, handwriting",
			'is_google_font' => true
		),
		"Neuton" => array(
			'name' => "Neuton",
			'editor_settings' => "Neuton, serif",
			'is_google_font' => true
		),
		"New+Rocker" => array(
			'name' => "New Rocker",
			'editor_settings' => "New Rocker, display",
			'is_google_font' => true
		),
		"News+Cycle" => array(
			'name' => "News Cycle",
			'editor_settings' => "News Cycle, sans-serif",
			'is_google_font' => true
		),
		"Niconne" => array(
			'name' => "Niconne",
			'editor_settings' => "Niconne, handwriting",
			'is_google_font' => true
		),
		"Nixie+One" => array(
			'name' => "Nixie One",
			'editor_settings' => "Nixie One, display",
			'is_google_font' => true
		),
		"Nobile" => array(
			'name' => "Nobile",
			'editor_settings' => "Nobile, sans-serif",
			'is_google_font' => true
		),
		"Nokora" => array(
			'name' => "Nokora",
			'editor_settings' => "Nokora, serif",
			'is_google_font' => true
		),
		"Norican" => array(
			'name' => "Norican",
			'editor_settings' => "Norican, handwriting",
			'is_google_font' => true
		),
		"Nosifer" => array(
			'name' => "Nosifer",
			'editor_settings' => "Nosifer, display",
			'is_google_font' => true
		),
		"Nothing+You+Could+Do" => array(
			'name' => "Nothing You Could Do",
			'editor_settings' => "Nothing You Could Do, handwriting",
			'is_google_font' => true
		),
		"Noticia+Text" => array(
			'name' => "Noticia Text",
			'editor_settings' => "Noticia Text, serif",
			'is_google_font' => true
		),
		"Noto+Sans" => array(
			'name' => "Noto Sans",
			'editor_settings' => "Noto Sans, sans-serif",
			'is_google_font' => true
		),
		"Noto+Serif" => array(
			'name' => "Noto Serif",
			'editor_settings' => "Noto Serif, serif",
			'is_google_font' => true
		),
		"Nova+Cut" => array(
			'name' => "Nova Cut",
			'editor_settings' => "Nova Cut, display",
			'is_google_font' => true
		),
		"Nova+Flat" => array(
			'name' => "Nova Flat",
			'editor_settings' => "Nova Flat, display",
			'is_google_font' => true
		),
		"Nova+Mono" => array(
			'name' => "Nova Mono",
			'editor_settings' => "Nova Mono, monospace",
			'is_google_font' => true
		),
		"Nova+Oval" => array(
			'name' => "Nova Oval",
			'editor_settings' => "Nova Oval, display",
			'is_google_font' => true
		),
		"Nova+Round" => array(
			'name' => "Nova Round",
			'editor_settings' => "Nova Round, display",
			'is_google_font' => true
		),
		"Nova+Script" => array(
			'name' => "Nova Script",
			'editor_settings' => "Nova Script, display",
			'is_google_font' => true
		),
		"Nova+Slim" => array(
			'name' => "Nova Slim",
			'editor_settings' => "Nova Slim, display",
			'is_google_font' => true
		),
		"Nova+Square" => array(
			'name' => "Nova Square",
			'editor_settings' => "Nova Square, display",
			'is_google_font' => true
		),
		"Numans" => array(
			'name' => "Numans",
			'editor_settings' => "Numans, sans-serif",
			'is_google_font' => true
		),
		"Nunito" => array(
			'name' => "Nunito",
			'editor_settings' => "Nunito, sans-serif",
			'is_google_font' => true
		),
		"Odor+Mean+Chey" => array(
			'name' => "Odor Mean Chey",
			'editor_settings' => "Odor Mean Chey, display",
			'is_google_font' => true
		),
		"Offside" => array(
			'name' => "Offside",
			'editor_settings' => "Offside, display",
			'is_google_font' => true
		),
		"Old+Standard+TT" => array(
			'name' => "Old Standard TT",
			'editor_settings' => "Old Standard TT, serif",
			'is_google_font' => true
		),
		"Oldenburg" => array(
			'name' => "Oldenburg",
			'editor_settings' => "Oldenburg, display",
			'is_google_font' => true
		),
		"Oleo+Script" => array(
			'name' => "Oleo Script",
			'editor_settings' => "Oleo Script, display",
			'is_google_font' => true
		),
		"Oleo+Script+Swash+Caps" => array(
			'name' => "Oleo Script Swash Caps",
			'editor_settings' => "Oleo Script Swash Caps, display",
			'is_google_font' => true
		),
		"Open+Sans" => array(
			'name' => "Open Sans",
			'editor_settings' => "Open Sans, sans-serif",
			'is_google_font' => true
		),
		"Open+Sans+Condensed" => array(
			'name' => "Open Sans Condensed",
			'editor_settings' => "Open Sans Condensed, sans-serif",
			'is_google_font' => true
		),
		"Oranienbaum" => array(
			'name' => "Oranienbaum",
			'editor_settings' => "Oranienbaum, serif",
			'is_google_font' => true
		),
		"Orbitron" => array(
			'name' => "Orbitron",
			'editor_settings' => "Orbitron, sans-serif",
			'is_google_font' => true
		),
		"Oregano" => array(
			'name' => "Oregano",
			'editor_settings' => "Oregano, display",
			'is_google_font' => true
		),
		"Orienta" => array(
			'name' => "Orienta",
			'editor_settings' => "Orienta, sans-serif",
			'is_google_font' => true
		),
		"Original+Surfer" => array(
			'name' => "Original Surfer",
			'editor_settings' => "Original Surfer, display",
			'is_google_font' => true
		),
		"Oswald" => array(
			'name' => "Oswald",
			'editor_settings' => "Oswald, sans-serif",
			'is_google_font' => true
		),
		"Over+the+Rainbow" => array(
			'name' => "Over the Rainbow",
			'editor_settings' => "Over the Rainbow, handwriting",
			'is_google_font' => true
		),
		"Overlock" => array(
			'name' => "Overlock",
			'editor_settings' => "Overlock, display",
			'is_google_font' => true
		),
		"Overlock+SC" => array(
			'name' => "Overlock SC",
			'editor_settings' => "Overlock SC, display",
			'is_google_font' => true
		),
		"Ovo" => array(
			'name' => "Ovo",
			'editor_settings' => "Ovo, serif",
			'is_google_font' => true
		),
		"Oxygen" => array(
			'name' => "Oxygen",
			'editor_settings' => "Oxygen, sans-serif",
			'is_google_font' => true
		),
		"Oxygen+Mono" => array(
			'name' => "Oxygen Mono",
			'editor_settings' => "Oxygen Mono, monospace",
			'is_google_font' => true
		),
		"PT+Mono" => array(
			'name' => "PT Mono",
			'editor_settings' => "PT Mono, monospace",
			'is_google_font' => true
		),
		"PT+Sans" => array(
			'name' => "PT Sans",
			'editor_settings' => "PT Sans, sans-serif",
			'is_google_font' => true
		),
		"PT+Sans+Caption" => array(
			'name' => "PT Sans Caption",
			'editor_settings' => "PT Sans Caption, sans-serif",
			'is_google_font' => true
		),
		"PT+Sans+Narrow" => array(
			'name' => "PT Sans Narrow",
			'editor_settings' => "PT Sans Narrow, sans-serif",
			'is_google_font' => true
		),
		"PT+Serif" => array(
			'name' => "PT Serif",
			'editor_settings' => "PT Serif, serif",
			'is_google_font' => true
		),
		"PT+Serif+Caption" => array(
			'name' => "PT Serif Caption",
			'editor_settings' => "PT Serif Caption, serif",
			'is_google_font' => true
		),
		"Pacifico" => array(
			'name' => "Pacifico",
			'editor_settings' => "Pacifico, handwriting",
			'is_google_font' => true
		),
		"Paprika" => array(
			'name' => "Paprika",
			'editor_settings' => "Paprika, display",
			'is_google_font' => true
		),
		"Parisienne" => array(
			'name' => "Parisienne",
			'editor_settings' => "Parisienne, handwriting",
			'is_google_font' => true
		),
		"Passero+One" => array(
			'name' => "Passero One",
			'editor_settings' => "Passero One, display",
			'is_google_font' => true
		),
		"Passion+One" => array(
			'name' => "Passion One",
			'editor_settings' => "Passion One, display",
			'is_google_font' => true
		),
		"Pathway+Gothic+One" => array(
			'name' => "Pathway Gothic One",
			'editor_settings' => "Pathway Gothic One, sans-serif",
			'is_google_font' => true
		),
		"Patrick+Hand" => array(
			'name' => "Patrick Hand",
			'editor_settings' => "Patrick Hand, handwriting",
			'is_google_font' => true
		),
		"Patrick+Hand+SC" => array(
			'name' => "Patrick Hand SC",
			'editor_settings' => "Patrick Hand SC, handwriting",
			'is_google_font' => true
		),
		"Patua+One" => array(
			'name' => "Patua One",
			'editor_settings' => "Patua One, display",
			'is_google_font' => true
		),
		"Paytone+One" => array(
			'name' => "Paytone One",
			'editor_settings' => "Paytone One, sans-serif",
			'is_google_font' => true
		),
		"Peralta" => array(
			'name' => "Peralta",
			'editor_settings' => "Peralta, display",
			'is_google_font' => true
		),
		"Permanent+Marker" => array(
			'name' => "Permanent Marker",
			'editor_settings' => "Permanent Marker, handwriting",
			'is_google_font' => true
		),
		"Petit+Formal+Script" => array(
			'name' => "Petit Formal Script",
			'editor_settings' => "Petit Formal Script, handwriting",
			'is_google_font' => true
		),
		"Petrona" => array(
			'name' => "Petrona",
			'editor_settings' => "Petrona, serif",
			'is_google_font' => true
		),
		"Philosopher" => array(
			'name' => "Philosopher",
			'editor_settings' => "Philosopher, sans-serif",
			'is_google_font' => true
		),
		"Piedra" => array(
			'name' => "Piedra",
			'editor_settings' => "Piedra, display",
			'is_google_font' => true
		),
		"Pinyon+Script" => array(
			'name' => "Pinyon Script",
			'editor_settings' => "Pinyon Script, handwriting",
			'is_google_font' => true
		),
		"Pirata+One" => array(
			'name' => "Pirata One",
			'editor_settings' => "Pirata One, display",
			'is_google_font' => true
		),
		"Plaster" => array(
			'name' => "Plaster",
			'editor_settings' => "Plaster, display",
			'is_google_font' => true
		),
		"Play" => array(
			'name' => "Play",
			'editor_settings' => "Play, sans-serif",
			'is_google_font' => true
		),
		"Playball" => array(
			'name' => "Playball",
			'editor_settings' => "Playball, display",
			'is_google_font' => true
		),
		"Playfair+Display" => array(
			'name' => "Playfair Display",
			'editor_settings' => "Playfair Display, serif",
			'is_google_font' => true
		),
		"Playfair+Display+SC" => array(
			'name' => "Playfair Display SC",
			'editor_settings' => "Playfair Display SC, serif",
			'is_google_font' => true
		),
		"Podkova" => array(
			'name' => "Podkova",
			'editor_settings' => "Podkova, serif",
			'is_google_font' => true
		),
		"Poiret+One" => array(
			'name' => "Poiret One",
			'editor_settings' => "Poiret One, display",
			'is_google_font' => true
		),
		"Poller+One" => array(
			'name' => "Poller One",
			'editor_settings' => "Poller One, display",
			'is_google_font' => true
		),
		"Poly" => array(
			'name' => "Poly",
			'editor_settings' => "Poly, serif",
			'is_google_font' => true
		),
		"Pompiere" => array(
			'name' => "Pompiere",
			'editor_settings' => "Pompiere, display",
			'is_google_font' => true
		),
		"Pontano+Sans" => array(
			'name' => "Pontano Sans",
			'editor_settings' => "Pontano Sans, sans-serif",
			'is_google_font' => true
		),
		"Port+Lligat+Sans" => array(
			'name' => "Port Lligat Sans",
			'editor_settings' => "Port Lligat Sans, sans-serif",
			'is_google_font' => true
		),
		"Port+Lligat+Slab" => array(
			'name' => "Port Lligat Slab",
			'editor_settings' => "Port Lligat Slab, serif",
			'is_google_font' => true
		),
		"Prata" => array(
			'name' => "Prata",
			'editor_settings' => "Prata, serif",
			'is_google_font' => true
		),
		"Preahvihear" => array(
			'name' => "Preahvihear",
			'editor_settings' => "Preahvihear, display",
			'is_google_font' => true
		),
		"Press+Start+2P" => array(
			'name' => "Press Start 2P",
			'editor_settings' => "Press Start 2P, display",
			'is_google_font' => true
		),
		"Princess+Sofia" => array(
			'name' => "Princess Sofia",
			'editor_settings' => "Princess Sofia, handwriting",
			'is_google_font' => true
		),
		"Prociono" => array(
			'name' => "Prociono",
			'editor_settings' => "Prociono, serif",
			'is_google_font' => true
		),
		"Prosto+One" => array(
			'name' => "Prosto One",
			'editor_settings' => "Prosto One, display",
			'is_google_font' => true
		),
		"Puritan" => array(
			'name' => "Puritan",
			'editor_settings' => "Puritan, sans-serif",
			'is_google_font' => true
		),
		"Purple+Purse" => array(
			'name' => "Purple Purse",
			'editor_settings' => "Purple Purse, display",
			'is_google_font' => true
		),
		"Quando" => array(
			'name' => "Quando",
			'editor_settings' => "Quando, serif",
			'is_google_font' => true
		),
		"Quantico" => array(
			'name' => "Quantico",
			'editor_settings' => "Quantico, sans-serif",
			'is_google_font' => true
		),
		"Quattrocento" => array(
			'name' => "Quattrocento",
			'editor_settings' => "Quattrocento, serif",
			'is_google_font' => true
		),
		"Quattrocento+Sans" => array(
			'name' => "Quattrocento Sans",
			'editor_settings' => "Quattrocento Sans, sans-serif",
			'is_google_font' => true
		),
		"Questrial" => array(
			'name' => "Questrial",
			'editor_settings' => "Questrial, sans-serif",
			'is_google_font' => true
		),
		"Quicksand" => array(
			'name' => "Quicksand",
			'editor_settings' => "Quicksand, sans-serif",
			'is_google_font' => true
		),
		"Quintessential" => array(
			'name' => "Quintessential",
			'editor_settings' => "Quintessential, handwriting",
			'is_google_font' => true
		),
		"Qwigley" => array(
			'name' => "Qwigley",
			'editor_settings' => "Qwigley, handwriting",
			'is_google_font' => true
		),
		"Racing+Sans+One" => array(
			'name' => "Racing Sans One",
			'editor_settings' => "Racing Sans One, display",
			'is_google_font' => true
		),
		"Radley" => array(
			'name' => "Radley",
			'editor_settings' => "Radley, serif",
			'is_google_font' => true
		),
		"Rajdhani" => array(
			'name' => "Rajdhani",
			'editor_settings' => "Rajdhani, sans-serif",
			'is_google_font' => true
		),
		"Raleway" => array(
			'name' => "Raleway",
			'editor_settings' => "Raleway, sans-serif",
			'is_google_font' => true
		),
		"Raleway+Dots" => array(
			'name' => "Raleway Dots",
			'editor_settings' => "Raleway Dots, display",
			'is_google_font' => true
		),
		"Ramabhadra" => array(
			'name' => "Ramabhadra",
			'editor_settings' => "Ramabhadra, sans-serif",
			'is_google_font' => true
		),
		"Rambla" => array(
			'name' => "Rambla",
			'editor_settings' => "Rambla, sans-serif",
			'is_google_font' => true
		),
		"Rammetto+One" => array(
			'name' => "Rammetto One",
			'editor_settings' => "Rammetto One, display",
			'is_google_font' => true
		),
		"Ranchers" => array(
			'name' => "Ranchers",
			'editor_settings' => "Ranchers, display",
			'is_google_font' => true
		),
		"Rancho" => array(
			'name' => "Rancho",
			'editor_settings' => "Rancho, handwriting",
			'is_google_font' => true
		),
		"Rationale" => array(
			'name' => "Rationale",
			'editor_settings' => "Rationale, sans-serif",
			'is_google_font' => true
		),
		"Redressed" => array(
			'name' => "Redressed",
			'editor_settings' => "Redressed, handwriting",
			'is_google_font' => true
		),
		"Reenie+Beanie" => array(
			'name' => "Reenie Beanie",
			'editor_settings' => "Reenie Beanie, handwriting",
			'is_google_font' => true
		),
		"Revalia" => array(
			'name' => "Revalia",
			'editor_settings' => "Revalia, display",
			'is_google_font' => true
		),
		"Ribeye" => array(
			'name' => "Ribeye",
			'editor_settings' => "Ribeye, display",
			'is_google_font' => true
		),
		"Ribeye+Marrow" => array(
			'name' => "Ribeye Marrow",
			'editor_settings' => "Ribeye Marrow, display",
			'is_google_font' => true
		),
		"Righteous" => array(
			'name' => "Righteous",
			'editor_settings' => "Righteous, display",
			'is_google_font' => true
		),
		"Risque" => array(
			'name' => "Risque",
			'editor_settings' => "Risque, display",
			'is_google_font' => true
		),
		"Roboto" => array(
			'name' => "Roboto",
			'editor_settings' => "Roboto, sans-serif",
			'is_google_font' => true
		),
		"Roboto+Condensed" => array(
			'name' => "Roboto Condensed",
			'editor_settings' => "Roboto Condensed, sans-serif",
			'is_google_font' => true
		),
		"Roboto+Slab" => array(
			'name' => "Roboto Slab",
			'editor_settings' => "Roboto Slab, serif",
			'is_google_font' => true
		),
		"Rochester" => array(
			'name' => "Rochester",
			'editor_settings' => "Rochester, handwriting",
			'is_google_font' => true
		),
		"Rock+Salt" => array(
			'name' => "Rock Salt",
			'editor_settings' => "Rock Salt, handwriting",
			'is_google_font' => true
		),
		"Rokkitt" => array(
			'name' => "Rokkitt",
			'editor_settings' => "Rokkitt, serif",
			'is_google_font' => true
		),
		"Romanesco" => array(
			'name' => "Romanesco",
			'editor_settings' => "Romanesco, handwriting",
			'is_google_font' => true
		),
		"Ropa+Sans" => array(
			'name' => "Ropa Sans",
			'editor_settings' => "Ropa Sans, sans-serif",
			'is_google_font' => true
		),
		"Rosario" => array(
			'name' => "Rosario",
			'editor_settings' => "Rosario, sans-serif",
			'is_google_font' => true
		),
		"Rosarivo" => array(
			'name' => "Rosarivo",
			'editor_settings' => "Rosarivo, serif",
			'is_google_font' => true
		),
		"Rouge+Script" => array(
			'name' => "Rouge Script",
			'editor_settings' => "Rouge Script, handwriting",
			'is_google_font' => true
		),
		"Rozha+One" => array(
			'name' => "Rozha One",
			'editor_settings' => "Rozha One, serif",
			'is_google_font' => true
		),
		"Rubik+Mono+One" => array(
			'name' => "Rubik Mono One",
			'editor_settings' => "Rubik Mono One, sans-serif",
			'is_google_font' => true
		),
		"Rubik+One" => array(
			'name' => "Rubik One",
			'editor_settings' => "Rubik One, sans-serif",
			'is_google_font' => true
		),
		"Ruda" => array(
			'name' => "Ruda",
			'editor_settings' => "Ruda, sans-serif",
			'is_google_font' => true
		),
		"Rufina" => array(
			'name' => "Rufina",
			'editor_settings' => "Rufina, serif",
			'is_google_font' => true
		),
		"Ruge+Boogie" => array(
			'name' => "Ruge Boogie",
			'editor_settings' => "Ruge Boogie, handwriting",
			'is_google_font' => true
		),
		"Ruluko" => array(
			'name' => "Ruluko",
			'editor_settings' => "Ruluko, sans-serif",
			'is_google_font' => true
		),
		"Rum+Raisin" => array(
			'name' => "Rum Raisin",
			'editor_settings' => "Rum Raisin, sans-serif",
			'is_google_font' => true
		),
		"Ruslan+Display" => array(
			'name' => "Ruslan Display",
			'editor_settings' => "Ruslan Display, display",
			'is_google_font' => true
		),
		"Russo+One" => array(
			'name' => "Russo One",
			'editor_settings' => "Russo One, sans-serif",
			'is_google_font' => true
		),
		"Ruthie" => array(
			'name' => "Ruthie",
			'editor_settings' => "Ruthie, handwriting",
			'is_google_font' => true
		),
		"Rye" => array(
			'name' => "Rye",
			'editor_settings' => "Rye, display",
			'is_google_font' => true
		),
		"Sacramento" => array(
			'name' => "Sacramento",
			'editor_settings' => "Sacramento, handwriting",
			'is_google_font' => true
		),
		"Sail" => array(
			'name' => "Sail",
			'editor_settings' => "Sail, display",
			'is_google_font' => true
		),
		"Salsa" => array(
			'name' => "Salsa",
			'editor_settings' => "Salsa, display",
			'is_google_font' => true
		),
		"Sanchez" => array(
			'name' => "Sanchez",
			'editor_settings' => "Sanchez, serif",
			'is_google_font' => true
		),
		"Sancreek" => array(
			'name' => "Sancreek",
			'editor_settings' => "Sancreek, display",
			'is_google_font' => true
		),
		"Sansita+One" => array(
			'name' => "Sansita One",
			'editor_settings' => "Sansita One, display",
			'is_google_font' => true
		),
		"Sarina" => array(
			'name' => "Sarina",
			'editor_settings' => "Sarina, display",
			'is_google_font' => true
		),
		"Sarpanch" => array(
			'name' => "Sarpanch",
			'editor_settings' => "Sarpanch, sans-serif",
			'is_google_font' => true
		),
		"Satisfy" => array(
			'name' => "Satisfy",
			'editor_settings' => "Satisfy, handwriting",
			'is_google_font' => true
		),
		"Scada" => array(
			'name' => "Scada",
			'editor_settings' => "Scada, sans-serif",
			'is_google_font' => true
		),
		"Schoolbell" => array(
			'name' => "Schoolbell",
			'editor_settings' => "Schoolbell, handwriting",
			'is_google_font' => true
		),
		"Seaweed+Script" => array(
			'name' => "Seaweed Script",
			'editor_settings' => "Seaweed Script, display",
			'is_google_font' => true
		),
		"Sevillana" => array(
			'name' => "Sevillana",
			'editor_settings' => "Sevillana, display",
			'is_google_font' => true
		),
		"Seymour+One" => array(
			'name' => "Seymour One",
			'editor_settings' => "Seymour One, sans-serif",
			'is_google_font' => true
		),
		"Shadows+Into+Light" => array(
			'name' => "Shadows Into Light",
			'editor_settings' => "Shadows Into Light, handwriting",
			'is_google_font' => true
		),
		"Shadows+Into+Light+Two" => array(
			'name' => "Shadows Into Light Two",
			'editor_settings' => "Shadows Into Light Two, handwriting",
			'is_google_font' => true
		),
		"Shanti" => array(
			'name' => "Shanti",
			'editor_settings' => "Shanti, sans-serif",
			'is_google_font' => true
		),
		"Share" => array(
			'name' => "Share",
			'editor_settings' => "Share, display",
			'is_google_font' => true
		),
		"Share+Tech" => array(
			'name' => "Share Tech",
			'editor_settings' => "Share Tech, sans-serif",
			'is_google_font' => true
		),
		"Share+Tech+Mono" => array(
			'name' => "Share Tech Mono",
			'editor_settings' => "Share Tech Mono, monospace",
			'is_google_font' => true
		),
		"Shojumaru" => array(
			'name' => "Shojumaru",
			'editor_settings' => "Shojumaru, display",
			'is_google_font' => true
		),
		"Short+Stack" => array(
			'name' => "Short Stack",
			'editor_settings' => "Short Stack, handwriting",
			'is_google_font' => true
		),
		"Siemreap" => array(
			'name' => "Siemreap",
			'editor_settings' => "Siemreap, display",
			'is_google_font' => true
		),
		"Sigmar+One" => array(
			'name' => "Sigmar One",
			'editor_settings' => "Sigmar One, display",
			'is_google_font' => true
		),
		"Signika" => array(
			'name' => "Signika",
			'editor_settings' => "Signika, sans-serif",
			'is_google_font' => true
		),
		"Signika+Negative" => array(
			'name' => "Signika Negative",
			'editor_settings' => "Signika Negative, sans-serif",
			'is_google_font' => true
		),
		"Simonetta" => array(
			'name' => "Simonetta",
			'editor_settings' => "Simonetta, display",
			'is_google_font' => true
		),
		"Sintony" => array(
			'name' => "Sintony",
			'editor_settings' => "Sintony, sans-serif",
			'is_google_font' => true
		),
		"Sirin+Stencil" => array(
			'name' => "Sirin Stencil",
			'editor_settings' => "Sirin Stencil, display",
			'is_google_font' => true
		),
		"Six+Caps" => array(
			'name' => "Six Caps",
			'editor_settings' => "Six Caps, sans-serif",
			'is_google_font' => true
		),
		"Skranji" => array(
			'name' => "Skranji",
			'editor_settings' => "Skranji, display",
			'is_google_font' => true
		),
		"Slabo+13px" => array(
			'name' => "Slabo 13px",
			'editor_settings' => "Slabo 13px, serif",
			'is_google_font' => true
		),
		"Slabo+27px" => array(
			'name' => "Slabo 27px",
			'editor_settings' => "Slabo 27px, serif",
			'is_google_font' => true
		),
		"Slackey" => array(
			'name' => "Slackey",
			'editor_settings' => "Slackey, display",
			'is_google_font' => true
		),
		"Smokum" => array(
			'name' => "Smokum",
			'editor_settings' => "Smokum, display",
			'is_google_font' => true
		),
		"Smythe" => array(
			'name' => "Smythe",
			'editor_settings' => "Smythe, display",
			'is_google_font' => true
		),
		"Sniglet" => array(
			'name' => "Sniglet",
			'editor_settings' => "Sniglet, display",
			'is_google_font' => true
		),
		"Snippet" => array(
			'name' => "Snippet",
			'editor_settings' => "Snippet, sans-serif",
			'is_google_font' => true
		),
		"Snowburst+One" => array(
			'name' => "Snowburst One",
			'editor_settings' => "Snowburst One, display",
			'is_google_font' => true
		),
		"Sofadi+One" => array(
			'name' => "Sofadi One",
			'editor_settings' => "Sofadi One, display",
			'is_google_font' => true
		),
		"Sofia" => array(
			'name' => "Sofia",
			'editor_settings' => "Sofia, handwriting",
			'is_google_font' => true
		),
		"Sonsie+One" => array(
			'name' => "Sonsie One",
			'editor_settings' => "Sonsie One, display",
			'is_google_font' => true
		),
		"Sorts+Mill+Goudy" => array(
			'name' => "Sorts Mill Goudy",
			'editor_settings' => "Sorts Mill Goudy, serif",
			'is_google_font' => true
		),
		"Source+Code+Pro" => array(
			'name' => "Source Code Pro",
			'editor_settings' => "Source Code Pro, monospace",
			'is_google_font' => true
		),
		"Source+Sans+Pro" => array(
			'name' => "Source Sans Pro",
			'editor_settings' => "Source Sans Pro, sans-serif",
			'is_google_font' => true
		),
		"Source+Serif+Pro" => array(
			'name' => "Source Serif Pro",
			'editor_settings' => "Source Serif Pro, serif",
			'is_google_font' => true
		),
		"Special+Elite" => array(
			'name' => "Special Elite",
			'editor_settings' => "Special Elite, display",
			'is_google_font' => true
		),
		"Spicy+Rice" => array(
			'name' => "Spicy Rice",
			'editor_settings' => "Spicy Rice, display",
			'is_google_font' => true
		),
		"Spinnaker" => array(
			'name' => "Spinnaker",
			'editor_settings' => "Spinnaker, sans-serif",
			'is_google_font' => true
		),
		"Spirax" => array(
			'name' => "Spirax",
			'editor_settings' => "Spirax, display",
			'is_google_font' => true
		),
		"Squada+One" => array(
			'name' => "Squada One",
			'editor_settings' => "Squada One, display",
			'is_google_font' => true
		),
		"Stalemate" => array(
			'name' => "Stalemate",
			'editor_settings' => "Stalemate, handwriting",
			'is_google_font' => true
		),
		"Stalinist+One" => array(
			'name' => "Stalinist One",
			'editor_settings' => "Stalinist One, display",
			'is_google_font' => true
		),
		"Stardos+Stencil" => array(
			'name' => "Stardos Stencil",
			'editor_settings' => "Stardos Stencil, display",
			'is_google_font' => true
		),
		"Stint+Ultra+Condensed" => array(
			'name' => "Stint Ultra Condensed",
			'editor_settings' => "Stint Ultra Condensed, display",
			'is_google_font' => true
		),
		"Stint+Ultra+Expanded" => array(
			'name' => "Stint Ultra Expanded",
			'editor_settings' => "Stint Ultra Expanded, display",
			'is_google_font' => true
		),
		"Stoke" => array(
			'name' => "Stoke",
			'editor_settings' => "Stoke, serif",
			'is_google_font' => true
		),
		"Strait" => array(
			'name' => "Strait",
			'editor_settings' => "Strait, sans-serif",
			'is_google_font' => true
		),
		"Sue+Ellen+Francisco" => array(
			'name' => "Sue Ellen Francisco",
			'editor_settings' => "Sue Ellen Francisco, handwriting",
			'is_google_font' => true
		),
		"Sunshiney" => array(
			'name' => "Sunshiney",
			'editor_settings' => "Sunshiney, handwriting",
			'is_google_font' => true
		),
		"Supermercado+One" => array(
			'name' => "Supermercado One",
			'editor_settings' => "Supermercado One, display",
			'is_google_font' => true
		),
		"Suwannaphum" => array(
			'name' => "Suwannaphum",
			'editor_settings' => "Suwannaphum, display",
			'is_google_font' => true
		),
		"Swanky+and+Moo+Moo" => array(
			'name' => "Swanky and Moo Moo",
			'editor_settings' => "Swanky and Moo Moo, handwriting",
			'is_google_font' => true
		),
		"Syncopate" => array(
			'name' => "Syncopate",
			'editor_settings' => "Syncopate, sans-serif",
			'is_google_font' => true
		),
		"Tangerine" => array(
			'name' => "Tangerine",
			'editor_settings' => "Tangerine, handwriting",
			'is_google_font' => true
		),
		"Taprom" => array(
			'name' => "Taprom",
			'editor_settings' => "Taprom, display",
			'is_google_font' => true
		),
		"Tauri" => array(
			'name' => "Tauri",
			'editor_settings' => "Tauri, sans-serif",
			'is_google_font' => true
		),
		"Teko" => array(
			'name' => "Teko",
			'editor_settings' => "Teko, sans-serif",
			'is_google_font' => true
		),
		"Telex" => array(
			'name' => "Telex",
			'editor_settings' => "Telex, sans-serif",
			'is_google_font' => true
		),
		"Tenor+Sans" => array(
			'name' => "Tenor Sans",
			'editor_settings' => "Tenor Sans, sans-serif",
			'is_google_font' => true
		),
		"Text+Me+One" => array(
			'name' => "Text Me One",
			'editor_settings' => "Text Me One, sans-serif",
			'is_google_font' => true
		),
		"The+Girl+Next+Door" => array(
			'name' => "The Girl Next Door",
			'editor_settings' => "The Girl Next Door, handwriting",
			'is_google_font' => true
		),
		"Tienne" => array(
			'name' => "Tienne",
			'editor_settings' => "Tienne, serif",
			'is_google_font' => true
		),
		"Tinos" => array(
			'name' => "Tinos",
			'editor_settings' => "Tinos, serif",
			'is_google_font' => true
		),
		"Titan+One" => array(
			'name' => "Titan One",
			'editor_settings' => "Titan One, display",
			'is_google_font' => true
		),
		"Titillium+Web" => array(
			'name' => "Titillium Web",
			'editor_settings' => "Titillium Web, sans-serif",
			'is_google_font' => true
		),
		"Trade+Winds" => array(
			'name' => "Trade Winds",
			'editor_settings' => "Trade Winds, display",
			'is_google_font' => true
		),
		"Trocchi" => array(
			'name' => "Trocchi",
			'editor_settings' => "Trocchi, serif",
			'is_google_font' => true
		),
		"Trochut" => array(
			'name' => "Trochut",
			'editor_settings' => "Trochut, display",
			'is_google_font' => true
		),
		"Trykker" => array(
			'name' => "Trykker",
			'editor_settings' => "Trykker, serif",
			'is_google_font' => true
		),
		"Tulpen+One" => array(
			'name' => "Tulpen One",
			'editor_settings' => "Tulpen One, display",
			'is_google_font' => true
		),
		"Ubuntu" => array(
			'name' => "Ubuntu",
			'editor_settings' => "Ubuntu, sans-serif",
			'is_google_font' => true
		),
		"Ubuntu+Condensed" => array(
			'name' => "Ubuntu Condensed",
			'editor_settings' => "Ubuntu Condensed, sans-serif",
			'is_google_font' => true
		),
		"Ubuntu+Mono" => array(
			'name' => "Ubuntu Mono",
			'editor_settings' => "Ubuntu Mono, monospace",
			'is_google_font' => true
		),
		"Ultra" => array(
			'name' => "Ultra",
			'editor_settings' => "Ultra, serif",
			'is_google_font' => true
		),
		"Uncial+Antiqua" => array(
			'name' => "Uncial Antiqua",
			'editor_settings' => "Uncial Antiqua, display",
			'is_google_font' => true
		),
		"Underdog" => array(
			'name' => "Underdog",
			'editor_settings' => "Underdog, display",
			'is_google_font' => true
		),
		"Unica+One" => array(
			'name' => "Unica One",
			'editor_settings' => "Unica One, display",
			'is_google_font' => true
		),
		"UnifrakturCook" => array(
			'name' => "UnifrakturCook",
			'editor_settings' => "UnifrakturCook, display",
			'is_google_font' => true
		),
		"UnifrakturMaguntia" => array(
			'name' => "UnifrakturMaguntia",
			'editor_settings' => "UnifrakturMaguntia, display",
			'is_google_font' => true
		),
		"Unkempt" => array(
			'name' => "Unkempt",
			'editor_settings' => "Unkempt, display",
			'is_google_font' => true
		),
		"Unlock" => array(
			'name' => "Unlock",
			'editor_settings' => "Unlock, display",
			'is_google_font' => true
		),
		"Unna" => array(
			'name' => "Unna",
			'editor_settings' => "Unna, serif",
			'is_google_font' => true
		),
		"VT323" => array(
			'name' => "VT323",
			'editor_settings' => "VT323, monospace",
			'is_google_font' => true
		),
		"Vampiro+One" => array(
			'name' => "Vampiro One",
			'editor_settings' => "Vampiro One, display",
			'is_google_font' => true
		),
		"Varela" => array(
			'name' => "Varela",
			'editor_settings' => "Varela, sans-serif",
			'is_google_font' => true
		),
		"Varela+Round" => array(
			'name' => "Varela Round",
			'editor_settings' => "Varela Round, sans-serif",
			'is_google_font' => true
		),
		"Vast+Shadow" => array(
			'name' => "Vast Shadow",
			'editor_settings' => "Vast Shadow, display",
			'is_google_font' => true
		),
		"Vesper+Libre" => array(
			'name' => "Vesper Libre",
			'editor_settings' => "Vesper Libre, serif",
			'is_google_font' => true
		),
		"Vibur" => array(
			'name' => "Vibur",
			'editor_settings' => "Vibur, handwriting",
			'is_google_font' => true
		),
		"Vidaloka" => array(
			'name' => "Vidaloka",
			'editor_settings' => "Vidaloka, serif",
			'is_google_font' => true
		),
		"Viga" => array(
			'name' => "Viga",
			'editor_settings' => "Viga, sans-serif",
			'is_google_font' => true
		),
		"Voces" => array(
			'name' => "Voces",
			'editor_settings' => "Voces, display",
			'is_google_font' => true
		),
		"Volkhov" => array(
			'name' => "Volkhov",
			'editor_settings' => "Volkhov, serif",
			'is_google_font' => true
		),
		"Vollkorn" => array(
			'name' => "Vollkorn",
			'editor_settings' => "Vollkorn, serif",
			'is_google_font' => true
		),
		"Voltaire" => array(
			'name' => "Voltaire",
			'editor_settings' => "Voltaire, sans-serif",
			'is_google_font' => true
		),
		"Waiting+for+the+Sunrise" => array(
			'name' => "Waiting for the Sunrise",
			'editor_settings' => "Waiting for the Sunrise, handwriting",
			'is_google_font' => true
		),
		"Wallpoet" => array(
			'name' => "Wallpoet",
			'editor_settings' => "Wallpoet, display",
			'is_google_font' => true
		),
		"Walter+Turncoat" => array(
			'name' => "Walter Turncoat",
			'editor_settings' => "Walter Turncoat, handwriting",
			'is_google_font' => true
		),
		"Warnes" => array(
			'name' => "Warnes",
			'editor_settings' => "Warnes, display",
			'is_google_font' => true
		),
		"Wellfleet" => array(
			'name' => "Wellfleet",
			'editor_settings' => "Wellfleet, display",
			'is_google_font' => true
		),
		"Wendy+One" => array(
			'name' => "Wendy One",
			'editor_settings' => "Wendy One, sans-serif",
			'is_google_font' => true
		),
		"Wire+One" => array(
			'name' => "Wire One",
			'editor_settings' => "Wire One, sans-serif",
			'is_google_font' => true
		),
		"Yanone+Kaffeesatz" => array(
			'name' => "Yanone Kaffeesatz",
			'editor_settings' => "Yanone Kaffeesatz, sans-serif",
			'is_google_font' => true
		),
		"Yellowtail" => array(
			'name' => "Yellowtail",
			'editor_settings' => "Yellowtail, handwriting",
			'is_google_font' => true
		),
		"Yeseva+One" => array(
			'name' => "Yeseva One",
			'editor_settings' => "Yeseva One, display",
			'is_google_font' => true
		),
		"Yesteryear" => array(
			'name' => "Yesteryear",
			'editor_settings' => "Yesteryear, handwriting",
			'is_google_font' => true
		),
		"Zeyada" => array(
			'name' => "Zeyada",
			'editor_settings' => "Zeyada, handwriting",
			'is_google_font' => true
		)
	);

	return apply_filters( 'sgf_fonts_list', $fonts );
}
