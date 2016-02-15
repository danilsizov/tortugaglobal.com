<?php
/**
 * Plugin Name: FontMeister - The Font Management Plugin
 * Plugin URI: http://www.aquoid.com/news/plugins/fontmeister/
 * Description: FontMeister lets you preview, add and use fonts from popular sources on the web. It supports Google Web Fonts, Typekit, Fontdeck and Font Squirrel.
 * Version: 1.05
 * Author: Sayontan Sinha
 * Author URI: http://mynethome.net/blog
 * License: GNU General Public License (GPL), v3 (or newer)
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright (c) 2009 - 2015 Sayontan Sinha. All rights reserved.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

class FontMeister {
	var $version, $fonts_page_name, $settings_page_name, $options, $font_stack, $font_stack_string, $font_directory, $font_directory_url;
	function __construct() {
		if (!defined('FONTMEISTER_VERSION')) {
			define('FONTMEISTER_VERSION', '1.05');
		}
		$this->options = get_option('fontmeister_options');

		if (!isset($this->options) || !is_array($this->options)) {
			$this->options = array();
		}

		if (!isset($this->options['font_stack']) || is_null($this->options['font_stack']) || trim($this->options['font_stack']) == '') {
			$this->font_stack = array();
			$this->font_stack_string = '';
		}
		else {
			$this->font_stack_string = $this->options['font_stack'];
			$this->font_stack = json_decode(str_replace('&quot;', '"', $this->font_stack_string), true);
		}

		$upload_dir = wp_upload_dir();
		$this->font_directory = trailingslashit($upload_dir['basedir']).'fontmeister';
		$this->font_directory_url = trailingslashit($upload_dir['baseurl']).'fontmeister';

		add_action('admin_menu', array(&$this, 'add_admin_menu'));
		add_action('admin_enqueue_scripts', array(&$this, 'add_admin_scripts'));
		add_action('admin_init', array(&$this, 'admin_init'));
		add_action('wp_enqueue_scripts', array(&$this, 'add_scripts'));
		add_action('wp_head', array(&$this, 'print_direct_scripts'));
		add_action('wp_head', array(&$this, 'print_selectors'));

		add_action('wp_ajax_fontmeister_download_font', array(&$this, 'download_font'));
		add_action('wp_ajax_fontmeister_delete_download', array(&$this, 'delete_download'));

		// Themes
		add_filter('suffusion_font_list', array(&$this, 'add_more_fonts'), 10, 4);

		//TinyMCE
		add_filter('mce_buttons', array(&$this, 'show_font_dropdown'));
		add_filter('tiny_mce_before_init', array(&$this, 'extend_tinymce_dropdown'));
	}

	function add_admin_menu() {
		add_menu_page('FontMeister', 'FontMeister', 'edit_theme_options', 'fontmeister', array(&$this, 'render_options'), plugins_url('include/icons/FontMeister-16.png', __FILE__));
		$this->fonts_page_name = add_submenu_page('fontmeister', 'Fonts', 'Fonts', 'edit_theme_options', 'fontmeister', array(&$this, 'render_options'));
		$this->settings_page_name = add_submenu_page('fontmeister', 'Font Sources', 'Font Sources', 'edit_theme_options', 'fontmeister-settings', array(&$this, 'render_settings'));
		add_action('load-'.$this->fonts_page_name, array(&$this, 'add_meta_boxes'));
		add_action('admin_head-'.$this->fonts_page_name, array(&$this, 'print_direct_scripts'));
	}

	function add_admin_scripts($hook) {
		if (!is_admin()) {
			return;
		}
		if ('post.php' == $hook || 'post-new.php' == $hook) {
			$this->enqueue_fontdeck_styles();
			$this->admin_font_enqueue($this->font_stack);
		}
		if ($this->fonts_page_name == $hook || $this->settings_page_name == $hook) {
			wp_enqueue_script('jquery');
			//wp_enqueue_script('jquery-ui-dialog');
			wp_enqueue_script('wp-lists');
			wp_enqueue_script('postbox');
			wp_enqueue_script('fm-preview', plugins_url('include/js/jquery.colorbox-min.js', __FILE__), array('jquery'), FONTMEISTER_VERSION);
			wp_enqueue_script('fm-js', plugins_url('include/js/admin.js', __FILE__), array('jquery', 'fm-preview'), FONTMEISTER_VERSION);
			wp_enqueue_style('fm-admin', plugins_url('include/css/admin.css', __FILE__), array(), FONTMEISTER_VERSION);
			wp_enqueue_style('fm-dosis', '//fonts.googleapis.com/css?family=Dosis', array(), FONTMEISTER_VERSION);
			wp_enqueue_style('fm-preview', plugins_url('include/css/colorbox/colorbox.css', __FILE__), array(), FONTMEISTER_VERSION);

			$this->enqueue_fontdeck_styles();

			$font_stack = $this->font_stack;
			$this->admin_font_enqueue($font_stack);

			$fonts = json_encode($font_stack);
			$fm_params = array(
				'font_stack' => $fonts,
				'ajaxurl' => admin_url('admin-ajax.php'),
				'font_dir_url' => trailingslashit($this->font_directory_url),
			);
			wp_localize_script('fm-js', 'FontMeisterJS', $fm_params);
		}
	}

	function admin_font_enqueue($font_stack) {
		$google_font_counter = $fs_font_counter = 0;
		foreach ($font_stack as $font) {
			if (isset($font['source']) && $font['source'] == 'Google Web Fonts') {
				$google_font_counter++;
				wp_enqueue_style('fm-google-font-'.$google_font_counter, '//fonts.googleapis.com/css?family='.urlencode($font['family']), array(), null);
			}

			if (isset($font['source']) && $font['source'] == 'Font Squirrel') {
				$fs_font_counter++;
//					wp_enqueue_style('fm-font-squirrel-font-'.$fs_font_counter, trailingslashit($this->font_directory_url).urlencode($font['stub']).'/stylesheet.css', array(), null);

				if (@file_exists(trailingslashit($this->font_directory).'fontmeister.css') && !(isset($this->options['fontsquirrel_combine']) && $this->options['fontsquirrel_combine'] == 'dont-combine')) {
					wp_enqueue_style('fm-font-squirrel-font-'.$fs_font_counter, trailingslashit($this->font_directory_url).'fontmeister.css', array(), null);
				}
				else {
					// Enqueue individually
					if (@file_exists(trailingslashit($this->font_directory).$font['stub']) &&
						@file_exists(trailingslashit($this->font_directory).$font['stub'].'/stylesheet.css')) {
						wp_enqueue_style('fm-font-squirrel-font-'.$fs_font_counter, trailingslashit($this->font_directory_url).$font['stub'].'/stylesheet.css', array(), null);
					}
				}
			}
		}

	}

	/**
	 * Prints scripts directly into the header. This is useful when you cannot enqueue something (e.g. JS code instead of a file, or CSS text)
	 */
	function print_direct_scripts() {
		if ((is_admin() && !isset($_GET['page'])) || (is_admin() && isset($_GET['page']) && $_GET['page'] != 'fontmeister')) {
			return;
		}

		if (isset($this->options['typekit_api_key'])) {
			$api_key = $this->options['typekit_api_key'];
			$url = "https://typekit.com/api/v1/json/kits/";
			$curl_args = array(
				'sslverify' => false,
			);

			$script = '';
			$response = wp_remote_request($url."?token=$api_key", $curl_args);
			global $fontmeister_typekit_text, $fontmeister_typekit_error;
			$fontmeister_typekit_text = $fontmeister_typekit_error = '';
			$kits_in_stack = array();
			$older_version = false;
			foreach ($this->font_stack as $font_in_stack) {
				if ($font_in_stack['source'] == 'Typekit') {
					if (isset($font_in_stack['kit'])) {
						$kits_in_stack[] = $font_in_stack['kit'];
					}
					else {
						$older_version = true;
					}
				}
			}

			if (!is_wp_error($response)) {
				$response = wp_remote_retrieve_body($response);
				$response = json_decode($response);
				if (isset($response->kits) && is_array($response->kits)) {
					$fontmeister_typekit_text .= "<div>";
					$fontmeister_typekit_text .= "<strong>".__('Your kits: ', 'fontmeister')."</strong>";
					$kit_string = "";
					$family_string = "";
					$kit_position = 0;

					$kits = $response->kits;
					foreach ($kits as $kit) {
						if (isset($kit->id)) {
							if (is_admin() || in_array($kit->id, $kits_in_stack) || $older_version) {
								$script .= "<script type='text/javascript' src='//use.typekit.com/{$kit->id}.js'></script>\n";
							}

							if (is_admin()) {
								$kit_position++;
								$kit_url = $url.$kit->id."?token=$api_key";
								$kit_response = wp_remote_request($kit_url, $curl_args);
								if (!is_wp_error($kit_response)) {
									$kit_response = wp_remote_retrieve_body($kit_response);
									$kit_response = json_decode($kit_response);
									if (isset($kit_response->kit->name)) {
										$families = $kit_response->kit->families;
										$kit_string .= "<a id='fm-tk-{$kit_response->kit->id}' class='fm-group-key fm-group-key-{$kit_response->kit->id}  fm-group-key-tk' href='#'>{$kit_response->kit->name} (".count($families).")</a> | ";
										if (count($families) > 0) {
											$family_string .= "<div id='fm-tk-{$kit_response->kit->id}-fonts' class='fm-fonts-for fm-fonts-for-{$kit_response->kit->id} fm-group-key-position-$kit_position fm-group-key-for-tk'><ul>";
											foreach ($families as $family) {
												$family_string .= $this->create_font_line_item(
													'Typekit',
													'tk',
													$family,
													array(
														'family' => 'name',
														'generic' => '',
														'stub' => 'css_stack',
														'variants' => 'variations',
														'subsets' => 'subset',
													),
													$kit->id
												);
											}
											$family_string .= "</ul></div>";
										}
									}
								}
							}
						}
					}
					if ($kit_string != '') {
						$kit_string = substr($kit_string, 0, -2);
						$fontmeister_typekit_text .= $kit_string;
					}
					$fontmeister_typekit_text .= "</div>";
					$fontmeister_typekit_text .= $family_string;
					if (is_admin() || !empty($kits_in_stack) || $older_version) {
						$script .= "<script type='text/javascript'>try{Typekit.load();}catch(e){}</script>\n";
					}
				}
				else {
					$fontmeister_typekit_text .= sprintf(__('No kits found for the API key you provided.', 'fontmeister'));
				}
			}
			else {
				$fontmeister_typekit_error .= $this->connection_failed('Typekit', false);
			}
			echo $script;
		}
	}

	function print_selectors() {
		if (is_admin()) {
			return;
		}

		$selectors = array();
		if (is_array($this->font_stack)) {
			foreach ($this->font_stack as $font) {
				if (isset($font['source'])) {
					switch ($font['source']) {
						case 'Google Web Fonts':
							if (isset($font['selectors']) && trim($font['selectors']) != '') {
								$selectors[] = $font['selectors']." { font-family: \"".$font['family']."\"; } ";
							}
							break;

						case 'Typekit':
							if (isset($font['selectors']) && trim($font['selectors']) != '') {
								$selectors[] = $font['selectors']." { font-family: \"".$font['stub']."\"; } ";
							}
							break;

						case 'Fontdeck':
							if (isset($font['selectors']) && trim($font['selectors']) != '') {
								$selectors[] = $font['selectors']." { font-family: \"".$font['family']."\"; } ";
							}
							break;

						case 'Font Squirrel':
							if (isset($font['variantselectors']) && trim($font['variantselectors']) != '') {
								$variant_selectors = explode('|', $font['variantselectors']);
								$selected_variants = explode(',', $font['selvariants']);
								$variants = explode(',', $font['variants']);
								$len = count($variants);
								for ($i=0; $i<$len; $i++) {
									if (in_array($variants[$i], $selected_variants) && trim($variant_selectors[$i]) != '') {
										$selectors[] = $variant_selectors[$i]." { font-family: {$variants[$i]}; } ";
									}
								}
							}
							break;
					}
				}
			}
		}
		if (is_array($selectors) && count($selectors) > 0) {
			$css = '<style type="text/css">'."\n";
			$css .= implode("\n", $selectors);
			$css .= "\n</style>\n";
			echo $css;
		}
	}

	function add_scripts() {
		if (is_admin()) {
			return;
		}

		$google_family = array();
		$google_subsets = array();
		$fontdeck_included = false;
		$selectors = array();
		if (is_array($this->font_stack)) {
			foreach ($this->font_stack as $font) {
				if (isset($font['source'])) {
					switch ($font['source']) {
						case 'Google Web Fonts':
							$family = urlencode($font['family']);
							if (isset($font['selvariants']) && trim($font['selvariants']) != '') {
								$family .= ':'.$font['selvariants'];
							}
							if (isset($font['selsubsets']) && trim($font['selsubsets']) != '') {
								$subsets = explode(',',$font['selsubsets']);
								$google_subsets = array_merge($google_subsets, $subsets);
							}
							$google_family[] = $family;
							if (isset($font['selectors']) && trim($font['selectors']) != '') {
								$selectors = $font['selectors']." { font-family: \"".$font['family']."\"; } ";
							}
							break;

						case 'Typekit':
							if (isset($font['selectors']) && trim($font['selectors']) != '') {
								$selectors = $font['selectors']." { font-family: \"".$font['stub']."\"; } ";
							}
							break;

						case 'Fontdeck':
							if (isset($this->options['fontdeck_project']) && trim($this->options['fontdeck_project']) != '' && !$fontdeck_included) {
								$domain = $_SERVER['SERVER_NAME'];
								$project = $this->options['fontdeck_project'];
								$url = "https://f.fontdeck.com/s/css/json/$domain/$project.json";
								$curl_args = array(
									'sslverify' => false,
								);

								$response = wp_remote_request($url, $curl_args);
								if (!is_wp_error($response)) {
									$response = wp_remote_retrieve_body($response);
									$response = json_decode($response);
									wp_enqueue_style('fontmeister-fontdeck', $response->cssurl, array(), null);
									$fontdeck_included = true;
								}
							}
							if (isset($font['selectors']) && trim($font['selectors']) != '') {
								$selectors = $font['selectors']." { font-family: \"".$font['stub']."\"; } ";
							}
							break;

						case 'Font Squirrel':
							if (@file_exists(trailingslashit($this->font_directory).'fontmeister.css') && !(isset($this->options['fontsquirrel_combine']) && $this->options['fontsquirrel_combine'] == 'dont-combine')) {
								wp_enqueue_style('fontmeister', trailingslashit($this->font_directory_url).'fontmeister.css', array(), null);
							}
							else {
								// Enqueue individually
								if (@file_exists(trailingslashit($this->font_directory).$font['stub']) &&
									@file_exists(trailingslashit($this->font_directory).$font['stub'].'/stylesheet.css')) {
									wp_enqueue_style('fontmeister-font-squirrel-'.$font['stub'], trailingslashit($this->font_directory_url).$font['stub'].'/stylesheet.css', array(), null);
								}
							}
							break;
					}
				}
			}
		}
		if (count($google_family) > 0) {
			$google_family = 'family='.implode('|', $google_family);
			$url = '//fonts.googleapis.com/css?'.$google_family;
			if (count($google_subsets) > 0) {
				$google_subsets = '&subset='.implode(',', array_unique($google_subsets));
				$url .= $google_subsets;
			}
			wp_enqueue_style('fontmeister-google', $url, array(), null);
		}
	}

	/**
	 * Registers the settings for the WP Settings API.
	 */
	function admin_init() {
		register_setting('fontmeister_options-fonts', 'fontmeister_options', array(&$this, 'validate_options'));
		register_setting('fontmeister_options-settings', 'fontmeister_options', array(&$this, 'validate_options'));
	}

	function add_meta_boxes() {
		add_meta_box('fontmeister-google', 'Google Fonts', array(&$this, 'select_from_google_fonts'), $this->fonts_page_name, 'column1');
		add_meta_box('fontmeister-typekit', 'Typekit', array(&$this, 'select_from_typekit'), $this->fonts_page_name, 'column2');
		add_meta_box('fontmeister-fontdeck', 'Fontdeck', array(&$this, 'select_from_fontdeck'), $this->fonts_page_name, 'column2');
		add_meta_box('fontmeister-font-squirrel', 'Font Squirrel', array(&$this, 'select_from_font_squirrel'), $this->fonts_page_name, 'column2');
	}

	function render_settings() {
		?>
		<div id="fontmeister-wrapper" class='fm-wrapper'>
			<form method="post" action="options.php">
				<h1 class="fm-main-title">FontMeister</h1>
				<div class="fm-box">
					<h2>Google Web Fonts</h2>
					You will need an <a href='https://developers.google.com/console/help/#generatingdevkeys'>API Key</a> to pull up the list of <a href="//www.google.com/webfonts">Google Fonts</a>. Pick the "Create new Browser key" for "Simple API Access".
					<div class="fm-field">
						<label>
							Enter your API Key:
							<input type="text" id="google_api_key" name="fontmeister_options[google_api_key]" value="<?php if (isset($this->options['google_api_key'])) echo $this->options['google_api_key']; ?>"/>
						</label>
					</div>
				</div>

				<div class="fm-box">
					<h2>Typekit</h2>
					You need a <a href='https://typekit.com/account/tokens'>Typekit API key</a> to access your Typekit fonts.
					<div class="fm-field">
						<label>
							Enter your API Key:
							<input type="text" id="typekit_api_key" name="fontmeister_options[typekit_api_key]" value="<?php if (isset($this->options['typekit_api_key'])) echo $this->options['typekit_api_key']; ?>"/>
						</label>
					</div>
				</div>

				<div class="fm-box">
					<h2>Fontdeck</h2>
					You will need to create a <a href="//fontdeck.com">Fontdeck</a> Project to access your fonts from Fontdeck.
					<div class="fm-field">
						<label>
							Enter your project id:
							<input type="text" id="fondeck_project" name="fontmeister_options[fontdeck_project]" value="<?php if (isset($this->options['fontdeck_project'])) echo $this->options['fontdeck_project']; ?>"/>
						</label>
					</div>
				</div>

				<div class="fm-box">
					<h2>Font Squirrel</h2>
					<p>
						Pull fonts from Font Squirrel?<br/>
						<label>
							<input type="radio" name="fontmeister_options[fontsquirrel_pull]" <?php if (isset($this->options['fontsquirrel_pull'])) { checked($this->options['fontsquirrel_pull'], 'pull'); } else { echo 'checked'; } ?> value='pull' /> Pull
						</label>
						<label>
							<input type="radio" name="fontmeister_options[fontsquirrel_pull]" <?php if (isset($this->options['fontsquirrel_pull'])) checked($this->options['fontsquirrel_pull'], 'dont-pull'); ?> value='dont-pull' /> Don't Pull
						</label>
					</p>

					<p>
						Combine Font Squirrel CSS files?<br/>
						<label>
							<input type="radio" name="fontmeister_options[fontsquirrel_combine]" <?php if (isset($this->options['fontsquirrel_combine'])) { checked($this->options['fontsquirrel_combine'], 'combine'); } else { echo 'checked'; } ?> value='combine' /> Combine
						</label>
						<label>
							<input type="radio" name="fontmeister_options[fontsquirrel_combine]" <?php if (isset($this->options['fontsquirrel_combine'])) checked($this->options['fontsquirrel_combine'], 'dont-combine'); ?> value='dont-combine' /> Don't Combine
						</label>
					</p>
				</div>
				<?php
				settings_fields('fontmeister_options-settings');
				?>
				<div class="fm-button-bar">
					<input type="submit" name="Submit" class="fm-submit-button" value="Save" />
				</div>
			</form>
		</div>
<?php
	}

	function render_options() { ?>
		<div id="fontmeister-wrapper" class="fm-wrapper">
			<form method="post" action="options.php">
				<h1 class="fm-main-title">FontMeister - Font Stack</h1>
				<div class="fm-button-bar">
					<input type="submit" name="Submit" class="fm-submit-button" value="Save" />
				</div>
				<?php $this->show_stack(); ?>
				<div style="text-align: center;">
					<em>(Credit to Richard Lederer and his awesome book, "Crazy English" for the 26 letter <a href="http://dictionary.reference.com/browse/pangram">pangram</a>.)</em>
				</div>
				<div class="metabox-holder">
					<div class="postbox-container" style="width: 48%; float: left; margin-right: 4%;">
						<?php do_meta_boxes($this->fonts_page_name, 'column1', null); ?>
					</div>
					<div class="postbox-container" style="width: 48%; float: left;">
						<?php do_meta_boxes($this->fonts_page_name, 'column2', null); ?>
					</div>
				</div>
				<?php
				settings_fields('fontmeister_options-fonts');
				?>
				<input type='hidden' id="fm-preview-link" value="<?php echo plugins_url('preview.php', __FILE__); ?>" />
				<div class="fm-button-bar">
					<input type="submit" name="Submit" class="fm-submit-button" value="Save" />
				</div>
			</form>
		</div>
<?php

		// Save Font Squirrel CSS to the FontMeister directory

		if (!isset($_REQUEST['settings-updated']) || !$_REQUEST['settings-updated']) {
			return;
		}

		$font_faces = array();
		$extensions = array(
			'eot' => 'embedded-opentype',
			'ttf' => 'truetype',
			'otf' => 'opentype',
			'woff' => 'woff',
			'svg' => 'svg',
		);

		$this->setup_wp_filesystem();
		global $wp_filesystem;
		foreach ($this->font_stack as $font) {
			if ($font['source'] == 'Font Squirrel') {
				if (isset($font['stub'])) {
					if (@is_dir(trailingslashit($this->font_directory).$font['stub'])) {
						$current_font_dir = trailingslashit($this->font_directory).$font['stub'];
						$variants = explode(',', $font['variants']);
						$selected_variants = explode(',', $font['selvariants']);
						$files = explode(',', $font['files']);
						$files = array_map(array(&$this, 'add_webfont_to_name'), $files);
						$variant_files = array();
						for ($i = 0; $i < count($variants); $i++) {
							if (in_array($variants[$i], $selected_variants)) {
								$variant_files[$variants[$i]] = array();
								$variant_files[$variants[$i]][] = $files[$i];
								if (@is_dir(trailingslashit($current_font_dir).'web fonts')) {
									$dirlist = $wp_filesystem->dirlist(trailingslashit($current_font_dir).'web fonts');
									foreach ($dirlist as $dir_name => $dir) {
										$variant_files[$variants[$i]][] = trailingslashit('web fonts').trailingslashit($dir_name).$files[$i];
									}
								}
								//$variant_files[$variants[$i]] = $files[$i];
							}
						}

						foreach ($variant_files as $variant => $files) {
							$font_faces[$variant] = array();
							$font_faces[$variant]['special'] = array();
							$font_faces[$variant]['sources'] = array();
							foreach ($extensions as $extension => $format) {
								foreach ($files as $file) {
									if (@file_exists(trailingslashit($current_font_dir).$file.'.'.$extension)) {
										if ($extension == 'eot') {
											$font_faces[$variant]['special'][] = "url('".$font['stub'].'/'.$file.".eot')";
											$font_faces[$variant]['sources'][] = "url('".$font['stub'].'/'.$file.".eot?#iefix') format('embedded-opentype')";
										}
										else if ($extension == 'svg') {
											$font_faces[$variant]['sources'][] = "url('".$font['stub'].'/'.$file.".svg#$variant') format('svg')";
										}
										else {
											$font_faces[$variant]['sources'][] = "url('".$font['stub'].'/'.$file.".$extension') format('$format')";
										}
									}
								}
							}
							if (count($font_faces[$variant]['sources']) === 0) {
								unset($font_faces[$variant]);
							}
						}
					}
				}
			}
		}

		$css = '';
		foreach ($font_faces as $font_face => $specs) {
			$css .= "@font-face {\n";
			$css .= "\tfont-family: \"$font_face\";\n";
			if (isset($specs['special'])) {
				$css .= "\tsrc: ".implode(",\n", $specs['special']).";\n";
			}
			$css .= "\tsrc: ".implode(",\n\t\t", $specs['sources']).";\n";
			$css .= "\tfont-weight: normal;\n";
			$css .= "\tfont-style: normal;\n";
			$css .= "}\n";
		}

		if (!(isset($this->options['fontsquirrel_combine']) && $this->options['fontsquirrel_combine'] == 'dont-combine')) {
			$this->setup_wp_filesystem();
			global $wp_filesystem;
			if (isset($wp_filesystem) && !$wp_filesystem->put_contents(trailingslashit($this->font_directory).'fontmeister.css', $css, FS_CHMOD_FILE)) {
				echo "<div class='error'><p>Failed to save file fontmeister.css. Please check your folder permissions.</p></div>";
			}
		}
	}

	function add_webfont_to_name($value) {
		$ret = $value;
		if (substr_count($value, '-webfont.') === 0) {
			$ret = str_replace('.', '-webfont.', $value);
		}
		$ret = substr($ret, 0, strpos($ret, '.'));
		return $ret;
	}

	function enqueue_fontdeck_styles() {
		global $fontmeister_fontdeck_text, $fontmeister_fontdeck_error;
		$fontmeister_fontdeck_text = $fontmeister_fontdeck_error = '';
		$css_url = '';
		if (isset($this->options['fontdeck_project']) && trim($this->options['fontdeck_project']) != '') {
			$domain = $_SERVER['SERVER_NAME'];
			$project = $this->options['fontdeck_project'];
			$url = "https://fontdeck.com/api/v1/project-info?project=$project&domain=$domain";
			$curl_args = array(
				'sslverify' => false,
			);

			$response = wp_remote_request($url, $curl_args);
			if (!is_wp_error($response)) {
				$response = wp_remote_retrieve_body($response);
				$response = json_decode($response);

				wp_enqueue_style('fm-fontdeck', $response->css, array(), FONTMEISTER_VERSION);
				$css_url = $response->css;

				if (isset($response->provides) && count($response->provides) > 0) {
					$fonts = $response->provides;
					$fontmeister_fontdeck_text .= sprintf(__('The following fonts were found for project %1$s:', 'fontmeister'), $project);
					$fontmeister_fontdeck_text .= "<div id='fm-fd-$project-fonts' class='fm-fonts-for fm-group-key-position-1'>";
					$fontmeister_fontdeck_text .= "<span class='fm-fd-css' style='display: none; visibility: hidden'>".$response->css."</span>";
					$fontmeister_fontdeck_text .= "<ul>";
					foreach ($fonts as $font) {
						$fontmeister_fontdeck_text .= $this->create_font_line_item(
							'Fontdeck',
							'fd',
							$font,
							array(
								'family' => 'name',
								'generic' => '',
								'stub' => 'name',
								'variants' => 'normal',
								'subsets' => '',
							)
						);
					}
					$fontmeister_fontdeck_text .= "</ul>";
					$fontmeister_fontdeck_text .= "</div>";
				}
				else {
					$fontmeister_fontdeck_text .= sprintf(__('No fonts found for project %1$s on %2$s. Make sure your project id is correct and this domain is added to it in your Fontdeck account.', 'fontmeister'), $project, $domain);
				}
			}
			else {
				$fontmeister_fontdeck_error .= $this->connection_failed('Fontdeck', false);
			}
		}
		return $css_url;
	}

	function select_from_google_fonts() {
		if (!isset($this->options['google_api_key']) || trim($this->options['google_api_key']) == '') {
			echo "Please enter <a href='admin.php?page=fontmeister-settings'>your Google API Key</a> to see the available Google Web Fonts.";
			return;
		}

		$api_key = trim($this->options['google_api_key']);
		$url = "https://www.googleapis.com/webfonts/v1/webfonts?key=$api_key";
		$curl_args = array(
			'sslverify' => false,
		);

		$response = wp_remote_request($url, $curl_args);
		if (!is_wp_error($response)) {
			$response = wp_remote_retrieve_body($response);
			$fonts = json_decode($response);
			if (isset($fonts->items) && is_array($fonts->items)) {
				$font_list = $fonts->items;
				$font_map = array();
				foreach ($font_list as $font) {
					$first_char = substr($font->family, 0, 1);
					if (!isset($font_map[$first_char])) {
						$first_char_fonts = array();
					}
					else {
						$first_char_fonts = $font_map[$first_char];
					}
					$first_char_fonts[] = $font;
					$font_map[$first_char] = $first_char_fonts;
				}

				$first_char_index = "";
				$fonts_by_first_letter = "";

				$first_char_position = 0;
				foreach ($font_map as $first_char => $first_char_fonts) {
					$first_char_position++;
					$first_char_index .= "<a href='#' id='fm-gf-$first_char' class='fm-group-key fm-group-key-$first_char fm-group-key-gf'>$first_char</a> | ";
					$fonts_by_first_letter .= "<div id='fm-gf-$first_char-fonts' class='fm-fonts-for fm-fonts-for-$first_char fm-group-key-position-$first_char_position fm-group-key-for-gf'><ul>\n";
					foreach ($first_char_fonts as $font) {
						$fonts_by_first_letter .= $this->create_font_line_item(
							'Google Web Fonts',
							'gf',
							$font,
							array(
								'family' => 'family',
								'generic' => '',
								'stub' => '',
								'variants' => 'variants',
								'subsets' => 'subsets',
							)
						);
					}
					$fonts_by_first_letter .= "</ul></div>\n";
				}

				if ($first_char_index != '') {
					$first_char_index = substr($first_char_index, 0, -2);
				}

				echo "<div>".$first_char_index."</div>";
				echo $fonts_by_first_letter;
			}
		}
	}

	function select_from_font_squirrel() {
		if (isset($this->options['fontsquirrel_pull']) && $this->options['fontsquirrel_pull'] == 'dont-pull') {
			echo "<p>You have chosen not to pull fonts from <a href='http://fontsquirrel.com'>Font Squirrel</a>. You can <a href='admin.php?page=fontmeister-settings'>change this</a>.</p>";
			return;
		}

		echo "<p>Fonts will be downloaded from <a href='http://fontsquirrel.com'>Font Squirrel</a> to ".$this->font_directory.". Only downloaded fonts are available for addition to the stack.</p>";
		$url = "http://www.fontsquirrel.com/api/classifications";
		$response = wp_remote_request($url);
		if (!is_wp_error($response)) {
			$response = wp_remote_retrieve_body($response);
			$classifications = json_decode($response);
			$class_string = '';
			$font_families = array();
			foreach ($classifications as $classification) {
				$sanitized_name = str_replace(' ', '-', urldecode($classification->name));
				$class_string .= "<a id='fm-fs-$sanitized_name' class='fm-group-key fm-group-key-$sanitized_name fm-group-key-fs' href='#'>".urldecode($classification->name)." (".$classification->count.")</a> | ";
				$font_families[$sanitized_name] = array();
			}
			$class_string = rtrim($class_string, ' | ');
			echo $class_string;

			$family_string = '';
			$family_url = 'http://www.fontsquirrel.com/api/fontlist/all';
			$family_response = wp_remote_request($family_url);
			if (!is_wp_error($family_response)) {
				$family_response = wp_remote_retrieve_body($family_response);
				$fonts = json_decode($family_response);
				foreach ($fonts as $font) {
					if (isset($font->classification)) {
						$class = str_replace(' ', '-', urldecode($font->classification));
						if (isset($font_families[$class])) {
							$font_families[$class][] = $font;
						}
					}
				}

				$kit_position = 0;
				foreach ($font_families as $class => $families) {
					$kit_position++;
					$family_string .= "<div id='fm-fs-$class-fonts' class='fm-fonts-for fm-fonts-for-$class fm-group-key-position-$kit_position fm-group-key-for-fs'><ul>";
					foreach ($families as $family) {
						$family_string .= $this->create_font_line_item(
							'Font Squirrel',
							'fs',
							$family,
							array(
								'family' => 'family_name',
								'generic' => '',
								'stub' => 'family_urlname',
								'variants' => '',
								'subsets' => 'subset',
							)
						);
					}
					$family_string .= "</ul></div>";
				}
				echo $family_string;
			}
		}
	}

	function select_from_fontdeck() {
		if (!isset($this->options['fontdeck_project']) || trim($this->options['fontdeck_project']) == '') {
			echo "Please enter <a href='admin.php?page=fontmeister-settings'>your Fontdeck project id</a> to see the available Fontdeck fonts.";
			return;
		}

		global $fontmeister_fontdeck_text, $fontmeister_fontdeck_error;
		if (trim($fontmeister_fontdeck_text) != '') {
			echo $fontmeister_fontdeck_text;
		}
		else if (trim($fontmeister_fontdeck_error) != '') {
			echo $fontmeister_fontdeck_error;
		}
	}

	function select_from_typekit() {
		if (!isset($this->options['typekit_api_key']) || trim($this->options['typekit_api_key']) == '') {
			echo "Please enter <a href='admin.php?page=fontmeister-settings'>your Typekit API Key</a> to see the available Typekit fonts.";
			return;
		}

		global $fontmeister_typekit_text, $fontmeister_typekit_error;
		if ($fontmeister_typekit_text != '') {
			echo $fontmeister_typekit_text;
		}
		else if ($fontmeister_typekit_error != '') {
			echo $fontmeister_typekit_error;
		}
	}

	function select_from_fonts_com() {
		echo "You need an <a href='https://webfonts.fonts.com/en-US/Account/AccountInformation'>authentication key</a> to use fonts from Fonts.com";
	}

	/**
	 * Validation function for the Settings API.
	 *
	 * @param $options
	 * @return array
	 */
	function validate_options($options) {
		$current_options = get_option('fontmeister_options');
		if (isset($current_options) && is_array($current_options)) {
			$options = array_merge($current_options, $options);
		}
		foreach ($options as $option => $option_value) {
			$options[$option] = esc_attr($option_value);
		}
		return $options;
	}

	/**
	 * Display the current font stack for the user. The left panel has a preview of the font, and the right panel has the details about variants, character subsets etc.
	 */
	function show_stack() { ?>
		<div class="fm-font-container">
			<div class="fm-font-preview">
		<?php
		$number_of_fonts = 0;
		echo '<ul id="fm-font-stack">';
		if (isset($this->options['font_stack'])) {
			$font_stack = $this->font_stack;
			$number_of_fonts = count($font_stack);
			foreach ($font_stack as $font) {
				if ($font['stub'] == '') {
					$font_family = "\"{$font['family']}\"";
				}
				else {
					$font_family = $font['stub'];
				}

				$pangram = 'Mr. Jock, TV quiz Ph.D., bags few lynx.';
				if ($font['source'] == 'Font Squirrel') {
					$variants = explode(',', $font['variants']);
					if (count($variants) > 0) {
						$font_family = $variants[0];
					}
				}

				echo "<li><span class='sample' style='font-family: $font_family;'>$pangram</span><span class='fm-stack-meta'><span class='fm-font-family'>{$font['family']}</span> <a href='#' class='fm-remove-font' title='Remove'>&nbsp;</a></span></li>";
			}
		}
		echo '</ul>';
		?>
			</div>
		<?php
		if ($number_of_fonts > 0) { ?>
			<div id="fm-font-details" class="fm-font-details">
				<h2>Preview</h2>
				Select a font from the left to see its details.
			</div>
			<?php
		}
		else { ?>
			<div id="fm-font-details" class="fm-font-details">
				<h2>Add Fonts</h2>
				You have no fonts in your stack. Please add a font first from the sources below. If you don't see any fonts below, make sure you have set up the <a href='admin.php?page=fontmeister-settings'>Font Sources</a> correctly.
			</div>
			<?php
		}
		?>

			<input type='hidden' id="font_stack" name="fontmeister_options[font_stack]" value="<?php echo $this->font_stack_string; ?>" />
		</div>
	<?php
	}

	/**
	 * Displays a font from a source, with a "Preview" and an "Add" button.
	 *
	 * @param $source_system
	 * @param $source_system_prefix
	 * @param $font
	 * @param array $args
	 * @return string
	 */
	function create_font_line_item($source_system, $source_system_prefix, $font, $args = array(), $kit = null) {
		$defaults = array(
			'family' => 'fammily',
			'generic' => '',
			'stub' => '',
			'source' => $source_system,
			'variants' => array(),
			'subsets' => array(),
			'file_names' => array(),
		);

		$args = array_merge($defaults, $args);
		$ret = "<li class='".(isset($kit) ? 'fontkit-'.$kit : '')."'>";
		$ret .= "<span class='fm-list-family'>{$font->$args['family']}</span>";
		$preview = "<a href='#' class='fm-launch-preview fm-launch-preview-$source_system_prefix' title='Preview'>&nbsp;</a>";
		$add = "<a href='#' class='fm-add-font fm-add-font-$source_system_prefix' title='Add'>&nbsp;</a>";
		if ($source_system == 'Font Squirrel') {
			if (@file_exists($this->font_directory) && @file_exists(trailingslashit($this->font_directory).$font->$args['stub'])) {
				$download = '';
				$delete_download = "<a href='#' class='fm-delete-download fm-delete-download-$source_system_prefix' title='Delete Download'>&nbsp;</a>";
				$variant_information = $this->font_squirrel_get_font_information($font->$args['stub']);
				$variant_text = implode(',', $variant_information['variants']);
				$variant_files_text = implode(',', $variant_information['files']);
				$family_id_text = implode(',', array_unique($variant_information['family_ids']));
			}
			else {
				$download = "<a href='#' class='fm-download-font fm-download-font-$source_system_prefix' title='Download'>&nbsp;</a>";
				$add = '';
				$delete_download = '';
			}
		}
		else {
			$download = '';
			$delete_download = '';
		}
		$ret .= "<span class='fm-prev-add'>$preview $add $download $delete_download</span>";
		if (isset($font->$args['stub'])) {
			$ret .= "<span class='fm-font-stub'>".$font->$args['stub']."</span>";
		}
		if (isset($font->$args['generic'])) {
			$ret .= "<span class='fm-font-generic'>".$font->$args['generic']."</span>";
		}
		if (isset($variant_text)) {
			$ret .= "<span class='fm-font-variants'>".$variant_text."</span>";
		}
		else if (isset($font->$args['variants'])) {
			if (is_array($font->$args['variants'])) {
				$variant = implode(',', $font->$args['variants']);
			}
			else {
				$variant = $font->$args['variants'];
			}
			$ret .= "<span class='fm-font-variants'>".$variant."</span>";
		}
		if (isset($variant_files_text)) {
			$ret .= "<span class='fm-font-variants-files'>".$variant_files_text."</span>";
		}
		if (isset($family_id_text)) {
			$ret .= "<span class='fm-font-family-id'>".$family_id_text."</span>";
		}
		if (isset($font->$args['subsets'])) {
			if (is_array($font->$args['subsets'])) {
				$subsets = implode(',', $font->$args['subsets']);
			}
			else {
				$subsets = $font->$args['subsets'];
			}
			$ret .= "<span class='fm-font-subsets'>".$subsets."</span>";
		}
		$ret .= "</li>";
		return $ret;
	}

	function font_squirrel_get_font_information($family) {
		$font_info_url = 'http://www.fontsquirrel.com/api/familyinfo/'.$family;
		$font_info = wp_remote_request($font_info_url);
		if (!is_wp_error($font_info)) {
			$font_info = wp_remote_retrieve_body($font_info);
			$font_variants = json_decode($font_info);
			$variant_array = array();
			$variant_array['variants'] = array();
			$variant_array['files'] = array();
			$variant_array['family_ids'] = array();
			foreach ($font_variants as $font_variant) {
				if (isset($font_variant->fontface_name) && isset($font_variant->filename) && isset($font_variant->family_id)) {
					$variant_array['variants'][] = $font_variant->fontface_name;
					$variant_array['files'][] = $font_variant->filename;
					$variant_array['family_ids'][] = $font_variant->family_id;
				}
			}
			return $variant_array;
		}
		return array();
	}

	/**
	 * Error message to display / return if the user is not connected.
	 *
	 * @param $to_what
	 * @param bool $echo
	 * @return string
	 */
	function connection_failed($to_what, $echo = true) {
		$ret = sprintf(__('Sorry, there was an error accessing %1$s', 'fontmeister'), $to_what);
		if ($echo) {
			echo $ret;
		}
		return $ret;
	}

	/**
	 * This method is meant for themes to invoke, so that the fonts defined by FontMeister are added to the drop-down lists of fonts
	 * that the themes define.
	 *
	 * @param mixed $fonts The current list of fonts
	 * @param string $key_format The format of the key. This key refers to the HTML "value" attribute of the "select" element
	 * @param string $value_format This is the format of the displayed text in the drop-down
	 * @param bool $replace_stub_with_family_if_empty If the font stub isn't present (e.g. Google), this fills it in with the font family
	 * @param string $add_quotes The quote character to add to the fonts. Useful for Typekit which adds double quotes
	 * @return array
	 */
	public function add_more_fonts($fonts, $key_format = "%stub%", $value_format = "%family%", $replace_stub_with_family_if_empty = true, $add_quotes = "'") {
		if (!isset($this->options) || !is_array($this->options) || !isset($this->font_stack) || !is_array($this->font_stack)) {
			return $fonts;
		}

		if (!is_array($fonts)) {
			$fonts = array();
		}

		foreach ($this->font_stack as $font) {
			if ($font['source'] != 'Font Squirrel') {
				$mod_key = $this->substitute_font_parameters($font, $key_format, $replace_stub_with_family_if_empty, $add_quotes);
				$mod_value = $this->substitute_font_parameters($font, $value_format, $replace_stub_with_family_if_empty, $add_quotes);
				$fonts[$mod_key] = $mod_value;
			}
			else if (isset($font['selvariants'])){
				$selected_variants = explode(',', $font['selvariants']);
				foreach ($selected_variants as $selected_variant) {
					$fonts[$selected_variant] = $selected_variant;
				}
			}
		}
		return $fonts;
	}

	/**
	 * This tokenizes the format string for a font drop-down and adds FontMeister's fonts in the specified format. The tokens
	 * are marked using % characters. E.g. %family% will be replaced by the font family.
	 *
	 * @param $font
	 * @param $lexed
	 * @param $replace_stub_with_family_if_empty
	 * @param $add_quotes
	 * @return mixed
	 */
	function substitute_font_parameters($font, $lexed, $replace_stub_with_family_if_empty, $add_quotes) {
		$parsed = $lexed;
		if ($add_quotes) {
			$family = $this->quotify_family($font['family'], $add_quotes);
			$stub = $this->quotify_family($font['stub'], $add_quotes);
		}
		else {
			$family = $font['family'];
			$stub = $font['stub'];
		}

		$parsed = str_replace("%family%", $family, $parsed);
		$parsed = ($replace_stub_with_family_if_empty && trim($stub) == '') ? str_replace("%stub%", $family, $parsed) : str_replace("%stub%", $stub, $parsed);
//		$parsed = str_replace("%stub%", $font['stub'], $parsed);
		$parsed = str_replace("%generic%", $font['generic'], $parsed);
		$parsed = str_replace("%source%", $font['source'], $parsed);
		$parsed = str_replace("%variants%", $font['selvariants'], $parsed);
		$parsed = str_replace("%subsets%", $font['selsubsets'], $parsed);
		return $parsed;
	}

	/**
	 * Changes single quotes to double quotes and vice versa in the font family name. This is used for consistency across the scripts.
	 *
	 * @param $family
	 * @param $add_quotes
	 * @return string
	 */
	function quotify_family($family, $add_quotes) {
		if (!$add_quotes) {
			return $family;
		}
		$family_parts = explode(',', $family);
		$quoted = array();
		foreach ($family_parts as $part) {
			if (stripos($part, ' ') > -1 && substr($part, 0, 1) != '"' && substr($part, 0, 1) != "'") {
				$part = $add_quotes.$part.$add_quotes;
			}
			else if ((substr($part, 0, 1) == '"' && $add_quotes == "'") || (substr($part, 0, 1) == "'" && $add_quotes == '"')) {
				$part = $add_quotes.substr($part, 1, strlen($part) - 2).$add_quotes;
			}
			$quoted[] = $part;
		}
		$family = implode(',', $quoted);
		return $family;
	}

	/**
	 * Adds FontMeister fonts to the TinyMCE drop-down. Typekit fonts don't render properly in the drop-down and in the editor,
	 * because Typekit needs JS and TinyMCE doesn't support that.
	 *
	 * @param $opt
	 * @return array
	 */
	function extend_tinymce_dropdown($opt) {
		if (!is_admin()) {
			return $opt;
		}
		$theme_advanced_fonts = "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats";
		$mce_fonts = array();
		$google_font_counter = 0;
		$content_css = array();
		$fontdeck_included = false;
		foreach ($this->font_stack as $font) {
			if (isset($font['source']) && $font['source'] != 'Font Squirrel') {
				$mce_fonts[] = $font['family'].'='.((isset($font['stub']) && $font['stub'] !== '') ? str_replace('"', '', $font['stub']) : $font['family']);
			}
			else if (isset($font['source'])) {
				$selected_variants = $font['selvariants'];
				$selected_variants = explode(',', $selected_variants);
				foreach ($selected_variants as $selected_variant) {
					$mce_fonts[] = $selected_variant.'='.$selected_variant;
				}
			}

			if (isset($font['source']) && $font['source'] == 'Google Web Fonts') {
				$google_font_counter++;
				wp_enqueue_style('fm-google-font-'.$google_font_counter, '//fonts.googleapis.com/css?family='.urlencode($font['family']), array(), null);
				$content_css[] = '//fonts.googleapis.com/css?family='.urlencode($font['family']);
			}
			else if (isset($font['source']) && $font['source'] == 'Fontdeck') {
				if (!$fontdeck_included) {
					$fontdeck_css_url = $this->enqueue_fontdeck_styles();
					$fontdeck_included = true;
					$content_css[] = $fontdeck_css_url;
				}
			}
		}

		$mce_fonts = implode(';', $mce_fonts);
		$content_css = implode(',', $content_css);
		if (trim($mce_fonts) != '') {
			$theme_advanced_fonts .= ';'.$mce_fonts;
		}
		$opt['font_formats'] = $theme_advanced_fonts; // used to be $opt['theme_advanced_fonts'] in prior WP versions
		if (isset($opt['content_css'])) {
			$opt['content_css'] .= $content_css;
		}
		else {
			$opt['content_css'] = $content_css;
		}
		return $opt;
	}

	/**
	 * Adds the font selection drop-down to the TinyMCE editor in the admin panel.
	 *
	 * @param $buttons
	 * @return array
	 */
	function show_font_dropdown($buttons) {
		if (!is_admin()) {
			return $buttons;
		}

		if (!in_array('fontselect', $buttons)) {
			array_push($buttons, 'fontselect');
		}
		return $buttons;
	}

	/**
	 * Downloads a fontface kit from Font Squirrel and unzips the file to uploads/fontmeister. Unzipping makes use of the
	 * WP call <code>unzip_file</code>, which in turn needs <code>WP_Filesystem</code>
	 *
	 * @return bool
	 */
	function download_font() {
		if (isset($_REQUEST['font_url'])) {
			$font_url = $_REQUEST['font_url'];

			if (!@file_exists($this->font_directory)) {
				if (!wp_mkdir_p($this->font_directory)) {
					echo json_encode(array(
						'error' => "Failed to create directory {$this->font_directory}. Please make sure that you have permissions to create the folder.",
					));
					die();
				}
			}

			$file_path = parse_url($font_url);
			$remote_file_info = pathinfo($file_path['path']);

			if (isset($remote_file_info['extension'])) {
				$remote_file_extension = $remote_file_info['extension'];
			}
			else {
				$remote_file_extension = 'zip';
			}

//			$file_base = $remote_file_info['basename'].'.'.$remote_file_extension;
//			$zip_file_name = trailingslashit($this->font_directory).$file_base;
			$zip_file_name = $remote_file_info['basename'].'.'.$remote_file_extension;

			$this->setup_wp_filesystem();
			$file_response = wp_remote_request($font_url, array('sslverify' => false));
			if (!is_wp_error($file_response)) {
				$zip_file = wp_remote_retrieve_body($file_response);
				global $wp_filesystem;
				if (isset($wp_filesystem) && !$wp_filesystem->put_contents(trailingslashit($this->font_directory).$zip_file_name, $zip_file, FS_CHMOD_FILE)) {
					echo json_encode(array(
						'error' => "Failed to save $zip_file_name to {$this->font_directory}. Please ensure that the directory exists and is writable.",
					));
					die();
				}
			}
			else {
				echo json_encode(array(
					'error' => "Failed to download file to {$this->font_directory}. Please ensure that the directory exists and is writable.",
				));
				die();
			}

			$unzip = unzip_file(trailingslashit($this->font_directory).$zip_file_name, trailingslashit($this->font_directory).$remote_file_info['basename']);
			if (is_wp_error($unzip)) {
				echo json_encode(array(
					'error' => "Failed to unzip the downloaded file.",
				));
				die();
			}

			$variants = $this->font_squirrel_get_font_information($remote_file_info['basename']);
			$variant_names = implode(',', $variants['variants']);
			$variant_files = implode(',', $variants['files']);
			$family_id = implode(',', array_unique($variants['family_ids']));

			echo json_encode(array(
				'success' => "Font downloaded and extracted successfully.",
				'variants' => $variant_names,
				'files' => $variant_files,
				'family_id' => $family_id,
			));
		}
		die();
	}

	/**
	 * Deletes a downloaded zip file and the associated unzipped directory from uploads/fontmeister. Since the directory
	 * has been unzipped using the WP call unzip_file, the deletion requires WP_Filesystem.
	 */
	function delete_download() {
		if (isset($_REQUEST['font_family'])) {
			$font_family = $_REQUEST['font_family'];
			$font_dir = trailingslashit($this->font_directory).'/'.$font_family;
			$fontkit_zip = $font_dir.'.zip';

			if (@file_exists($fontkit_zip)) {
				if (!@unlink($fontkit_zip)) {
					echo json_encode(array('error' => "Failed to delete @fontface kit zip $fontkit_zip"));
					die();
				}
			}

			// Cannot delete the directory, because unzip_file, which has created it, uses WP_Filesystem. So we use WP_Filesystem to delete it.
			$this->setup_wp_filesystem();

			global $wp_filesystem;
			if (isset($wp_filesystem)) {
				$delete_dir = $wp_filesystem->delete($font_dir, true);
				if (!$delete_dir) {
					echo json_encode(array('error' => $delete_dir['error']));
					die();
				}
			}

			echo json_encode(array('success' => "Download deleted"));
		}
		die();
	}

	/**
	 * Sets up the WP_Filesystem object for use by other functions.
	 *
	 * @return bool
	 */
	private function setup_wp_filesystem() {
		$url = wp_nonce_url($this->fonts_page_name);
		if (false === ($creds = request_filesystem_credentials($url, '', false, false))) {
			return true;
		}

		if (!WP_Filesystem($creds)) {
			request_filesystem_credentials($url, '', true, false);
			return true;
		}
		return true;
	}
}

global $fontmeister;
$fontmeister = new FontMeister();
