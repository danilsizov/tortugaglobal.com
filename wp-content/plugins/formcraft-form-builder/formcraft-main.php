<?php

	/*
	Plugin Name: FormCraft Basic
	Plugin URI: http://ncrafts.net/
	Description: A beautiful and simple drag-and-drop WordPress form builder
	Author: nCrafts
	Author URI: http://ncrafts.net
	Version: 1.0.5
	Text Domain: formcraft_basic
	*/

	global $fcb_meta, $forms_table, $submissions_table, $views_table, $wpdb;
	$fcb_meta['version'] = '1.0.5';
	$fcb_meta['user_can'] = 'activate_plugins';
	$forms_table = $wpdb->prefix . "formcraft_b_forms";
	$submissions_table = $wpdb->prefix . "formcraft_b_submissions";
	$views_table = $wpdb->prefix . "formcraft_b_views";

	/*
	Create the necessary tables on plugin activation
	*/
	function formcraft_basic_activate()
	{
		global $fcb_meta, $forms_table, $submissions_table, $views_table, $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();
		if($wpdb->get_var("SHOW TABLES LIKE '$forms_table'") != $forms_table) {
			$sql = "CREATE TABLE $forms_table (id mediumint(9) NOT NULL AUTO_INCREMENT,counter INT NOT NULL,name tinytext NOT NULL,created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,html MEDIUMTEXT NULL,builder MEDIUMTEXT NULL,meta_builder MEDIUMTEXT NULL,UNIQUE KEY id (id)) $charset_collate;";
			dbDelta( $sql );
		}

		if($wpdb->get_var("SHOW TABLES LIKE '$submissions_table'") != $submissions_table) {
			$sql = "CREATE TABLE $submissions_table (id mediumint(9) NOT NULL AUTO_INCREMENT,form INT NOT NULL,form_name tinytext NOT NULL,created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,content MEDIUMTEXT NULL,visitor MEDIUMTEXT NULL,UNIQUE KEY id (id)) $charset_collate;";
			dbDelta( $sql );
		}

		if($wpdb->get_var("SHOW TABLES LIKE '$views_table'") != $views_table) {
			$sql = "CREATE TABLE $views_table (id mediumint(9) NOT NULL AUTO_INCREMENT,form INT NOT NULL,views INT NOT NULL,views_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,UNIQUE KEY id (id)) $charset_collate;";
			dbDelta( $sql );
		}
	}
	register_activation_hook( __FILE__, 'formcraft_basic_activate' );

	/* FormCraft icon */
	add_action( 'admin_enqueue_scripts', 'formcraft_basic_admin_scripts' );
	function formcraft_basic_admin_scripts()
	{
		global $fc_meta, $fc_forms_table, $wpdb;
		wp_enqueue_style('fcb-icon-css', plugins_url( 'assets/formcraft-icon.css', __FILE__ ),array(), $fc_meta['version']);
	}


	/* Check if the User is Visiting a Form Page */
	add_action('template_redirect', 'formcraft_basic_redirect_to_form_page', 1);
	function formcraft_basic_redirect_to_form_page()
	{
		global $fcb_meta, $forms_table, $wpdb;
		if(formcraft_basic_check_form_page())
		{
			$form_id = formcraft_basic_check_form_page();
			if(formcraft_basic_check_form_page_access($form_id))
			{
				add_action('wp_head','formcraft_basic_wp_head');
				wp_head();
				$qry = $wpdb->get_var( "SELECT html FROM $forms_table WHERE id='$form_id'" );
				echo "<style>html{margin-top:0px!important;}</style><div id='form-cover' class='formcraft-css' style='padding: 50px 15px'>";
				if (strpos($_SERVER["REQUEST_URI"], '?preview=true'))
				{
					echo "<span class='form-preview'>".__('Preview Mode','formcraft_basic')."</span>";
				}
				echo stripslashes($qry);
				echo "</div>";
				die();
			}
		}
	}
	function formcraft_basic_wp_head()
	{
		global $fcb_meta, $forms_table, $wpdb;
		$url = explode('/',str_replace('?preview=true', '', $_SERVER["REQUEST_URI"]));
		$form_id = $url[ (count($url)-1) ];
		$qry = $wpdb->get_var( "SELECT name FROM $forms_table WHERE id='$form_id'" );
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
		echo '<title>'.get_bloginfo('name').' - '.$qry.'</title>';
	}

	function formcraft_basic_check_form_page()
	{
		global $fcb_meta, $forms_table, $wpdb;
		$url = explode('/',str_replace('?preview=true', '', $_SERVER["REQUEST_URI"]));
		if ( $url[ (count($url)-2) ]=='form' && ctype_digit($url[ (count($url)-1) ]) )
		{
			return $url[ (count($url)-1) ];
		}
		else
		{
			return false;
		}
	}
	/* Check if current requester is allowed form page access */
	function formcraft_basic_check_form_page_access($form_id)
	{
		global $fcb_meta, $forms_table, $wpdb;
		$qry = $wpdb->get_var( "SELECT meta_builder FROM $forms_table WHERE id='$form_id'" );
		$qry = json_decode(stripslashes($qry),1);
		if(isset($qry['config']) && isset($qry['config']['disable_form_link']) && $qry['config']['disable_form_link']==true)
		{
			if (is_user_logged_in())
			{
				if (isset($_GET['preview']) && $_GET['preview']==true)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return true;
		}
	}

	/* Enqueue Styles on Front End Pages, Header */
	add_action( 'wp_enqueue_scripts', 'formcraft_basic_form_styles' );
	function formcraft_basic_form_styles()
	{
		global $fcb_meta, $forms_table, $wpdb;
		$form_id = formcraft_basic_check_form_page();
		if($form_id)
		{
			if(formcraft_basic_check_form_page_access($form_id))
			{
				formcraft_basic_new_view(formcraft_basic_check_form_page());
				status_header( 200 );
			}
		}
		wp_enqueue_style('fcb-main-css', plugins_url( 'assets/css/form.main.css', __FILE__ ),array(), $fcb_meta['version']);
		wp_enqueue_style('fcb-common-css', plugins_url( 'assets/css/common-elements.css', __FILE__ ),array(), $fcb_meta['version']);
		wp_enqueue_style('fcb-fontello-css', plugins_url( 'assets/fontello/css/fcb.css', __FILE__ ),array(), $fcb_meta['version']);
		wp_enqueue_style('fcb-fontello-animation-css', plugins_url( 'assets/fontello/css/animation.css', __FILE__ ),array(), $fcb_meta['version']);
	}

	/* Custom Add Form Button for the WP Editor */
	add_action( 'media_buttons', 'formcraft_basic_custom_button');
	function formcraft_basic_custom_button( ) {
		global $fcb_meta, $forms_table, $wpdb;
		if ( !current_user_can('edit_posts') || !current_user_can('edit_pages') ) { return; }
		$button = '<a href="javascript:void(0);" id="fcb_afb" class="button" title="'.__('Insert FormCraft Basic Form','formcraft_basic').'" data-target="#fcb_add_form_modal" data-toggle="fcbmodal"><img style="padding-left:2px" width="12" src="'.plugins_url( 'assets/images/plus.png', __FILE__ ).'"/>' .__( 'Add Form', 'formcraft_basic' ). '</a>';
		add_action('admin_footer','formcraft_basic_add_modal');
		wp_enqueue_style('fcb-fontello-css', plugins_url( 'assets/fontello/css/fcb.css', __FILE__ ),array(), $fcb_meta['version']);  
		wp_enqueue_style('fcb-common-css', plugins_url( 'assets/css/common-elements.css', __FILE__ ),array(), $fcb_meta['version']);  
		wp_enqueue_style('fcb-modal-css', plugins_url( 'assets/css/fcbmodal.css', __FILE__ ),array(), $fcb_meta['version']);
		wp_enqueue_script('fcb-modal-js', plugins_url( 'assets/js/fcbmodal.js', __FILE__ ));
		wp_enqueue_script('fcb-add-form-button-js', plugins_url( 'assets/js/add-form-button.js', __FILE__ ));
		wp_enqueue_style('fcb-add-form-button-css', plugins_url( 'assets/css/add-form-button.css', __FILE__ ),array(), $fcb_meta['version']);
		echo $button;
	}
	function formcraft_basic_add_modal()
	{
		global $fcb_meta, $forms_table, $wpdb;
		$forms = $wpdb->get_results( "SELECT id,name FROM $forms_table", ARRAY_A );
		echo '<div class="fcbmodal formcraft-css fcbfade" id="fcb_add_form_modal"><form class="fcbmodal-dialog" style="width: 300px"><div class="fcbmodal-content">';
		echo '<div class="fcbmodal-header">'.__('FormCraft Basic','formcraft_basic').'<button class="fcbclose" type="button" class="close" data-dismiss="fcbmodal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';	
		echo '<div class="fcbmodal-body">';
		if ( count($forms)!=0 )
		{
			echo "<div class='fcb-modal-head'>".__('Select Form','formcraft_basic')."</div>";
			foreach ($forms as $key => $value) {
				echo "<label class='select-form'><input ".($key==0?"checked ":"")."type='radio' value='".$value['id']."' name='fcb_form_id'/>".$value['name']."</label>";
			}
			echo "<br><div class='fcb-modal-head'>".__('Select Alignment','formcraft_basic')."</div>";
			echo "<label class='select-alignment'><input checked type='radio' value='left' name='fcb_form_align'/>".__('Left','formcraft_basic')."</label>";
			echo "<label class='select-alignment'><input type='radio' value='center' name='fcb_form_align'/>".__('Center','formcraft_basic')."</label>";
			echo "<label class='select-alignment'><input type='radio' value='right' name='fcb_form_align'/>".__('Right','formcraft_basic')."</label>";
		}
		else
		{
			echo "<center style='letter-spacing:0'>".__("You have no forms","formcraft_basic")."</center>";
		}
		echo '</div>';
		if ( count($forms)!=0 )
		{
			echo '<div class="fcbmodal-footer"><button type="submit" class="button" id="fcb_add_form_to_editor">'.__('Add Form','formcraft_basic').'</button></div>';
		}
		echo '</div></form></div>';
	}


	/* Register a Form View */
	function formcraft_basic_new_view($form_id)
	{
		global $fcb_meta, $forms_table, $submissions_table, $views_table, $wpdb;
		if ( !strpos($_SERVER["REQUEST_URI"], '?preview=true') && ctype_digit($form_id))
		{
			if(!isset($_COOKIE["fcb_".$form_id])) {
				/* 30 min window for counting another view by same user */
				setcookie("fcb_".$form_id, true, time()+1800, '/');
				$time = date('Y-m-d 00:00:00',time()+fcb_offset());
				if($wpdb->get_var( "SELECT COUNT(*) FROM $views_table WHERE views_date = '$time' AND form = $form_id" ))
				{
					$existing = $wpdb->get_var( "SELECT views FROM $views_table WHERE views_date = '$time' AND form = $form_id" );
					$wpdb->update($views_table, array( 'views' => $existing+1 ), array('form'=>$form_id,'views_date'=>$time));
				}
				else
				{
					$rows_affected = $wpdb->insert( $views_table, array( 
						'form' => $form_id,
						'views' => 1,
						'views_date' => $time
						) );
				}
			}
		}
	}


	/* Create a Custom Title for the Form Page */
	function formcraft_basic_modify_title($title, $sep)
	{
		global $fcb_meta, $forms_table, $wpdb;
		$url = explode('/',str_replace('?preview=true', '', $_SERVER["REQUEST_URI"]));
		$form_id = $url[ (count($url)-1) ];
		$qry = $wpdb->get_var( "SELECT name FROM $forms_table WHERE id='$form_id'" );
		return $sep." ".$qry;
	}

	/* Enqueue Scripts / Styles if the user is visiting the Form Page */
	add_action('init','formcraft_basic_check');
	function formcraft_basic_check()
	{
		global $fcb_meta, $forms_table, $submissions_table, $views_table, $wpdb;
		if (is_user_logged_in() && isset($_GET['formcraft_export_form']) && ctype_digit($_GET['formcraft_export_form']) )
		{
			$form_id = $_GET['formcraft_export_form'];
			$data = $wpdb->get_row( "SELECT * FROM $forms_table WHERE id = '$form_id'", ARRAY_A );
			$result = array();
			$result['plugin'] = 'FormCraft Basic';
			$result['created'] = date('Y-m-d H:i:s',time());
			$result['html'] = base64_encode(stripslashes($data['html']));
			$result['builder'] = base64_encode(stripslashes($data['builder']));
			$result['meta_builder'] = base64_encode(stripslashes($data['meta_builder']));
			$result = json_encode($result);

			header("Content-Type: text/plain");
			header('Content-Disposition: attachment; filename="'.$data['name'].'.txt"');
			header("Pragma: no-cache");
			header("Expires: 0");

			print $result;
			die();
		}
		$form_id = formcraft_basic_check_form_page();
		if($form_id)
		{
			add_filter( 'wp_title', 'formcraft_basic_modify_title', 1, 2 );
			wp_enqueue_script('fcb-tooltip-js', plugins_url( 'assets/js/tooltip.min.js', __FILE__ ), array('jquery')); 
			wp_enqueue_script('fcb-form-js', plugins_url( 'assets/js/form.js', __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), $fcb_meta['version']); 
			wp_enqueue_script('fcb-validation-js', plugins_url( 'assets/js/formcraft-validation.js', __FILE__ )); 
			wp_localize_script( 'fcb-validation-js', 'FCB_validation',
				array( 
					'is_required' => __('Required','formcraft_basic'),
					'min_char' => __('Min [min] characters required','formcraft_basic'),
					'max_char' => __('Max [max] characters allowed','formcraft_basic'),
					'allow_email' => __('Invalid email','formcraft_basic'),
					'allow_alphabets' => __('Only alphabets allowed','formcraft_basic'),
					'allow_numbers' => __('Only numbers allowed','formcraft_basic'),
					'allow_alphanumeric' => __('Only alphabets and numbers allowed','formcraft_basic'),
					)
				);
			wp_localize_script( 'fcb-form-js', 'FCB',
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'datepickerLang' => plugins_url( 'assets/js/datepicker-lang/', __FILE__ )
					)
				);
			if (strpos($_SERVER["REQUEST_URI"], '?preview=true'))
			{
				wp_enqueue_script('fcb-toastr-js', plugins_url( 'assets/js/toastr.min.js', __FILE__ ));
			}
			wp_enqueue_style('fcb-form-page-css', plugins_url( 'assets/css/form-page.css', __FILE__ ), array('fcb-main-css'), $fcb_meta['version']);
		}
	}

	function formcraft_basic_shortcode( $atts ) {
		global $fcb_meta, $forms_table, $wpdb;
		wp_enqueue_script('fcb-tooltip-js', plugins_url( 'assets/js/tooltip.min.js', __FILE__ ), array('jquery')); 
		wp_enqueue_script('fcb-form-js', plugins_url( 'assets/js/form.js', __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), $fcb_meta['version']); 
		wp_enqueue_script('fcb-validation-js', plugins_url( 'assets/js/formcraft-validation.js', __FILE__ ));
		wp_localize_script( 'fcb-validation-js', 'FCB_validation',
			array( 
				'is_required' => __('Required','formcraft_basic'),
				'min_char' => __('Min [min] characters required','formcraft_basic'),
				'max_char' => __('Max [max] characters allowed','formcraft_basic'),
				'allow_email' => __('Invalid email','formcraft_basic'),
				'allow_alphabets' => __('Only alphabets allowed','formcraft_basic'),
				'allow_numbers' => __('Only numbers allowed','formcraft_basic'),
				'allow_alphanumeric' => __('Only alphabets and numbers allowed','formcraft_basic'),
				)
			);			
		wp_localize_script( 'fcb-form-js', 'FCB',
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'datepickerLang' => plugins_url( 'assets/js/datepicker-lang/', __FILE__ )
				)
			);
		if (strpos($_SERVER["REQUEST_URI"], '?preview=true'))
		{
			wp_enqueue_script('fcb-toastr-js', plugins_url( 'assets/js/toastr.min.js', __FILE__ ));
		}

		extract( shortcode_atts( array(
			'id' => '1',
			'align' => 'left'
			), $atts ) );

		if ( !ctype_digit($id) )
		{
			return '';
		}
		$html = $wpdb->get_var( "SELECT html FROM $forms_table WHERE id='$id'" );
		return "<div class='formcraft-css align-$align'>".stripcslashes($html)."</div>";
	}
	add_shortcode( 'fcb', 'formcraft_basic_shortcode' );


	/*
	Create New Form Function
	*/
	add_action( 'wp_ajax_formcraft_basic_new_form', 'formcraft_basic_new_form' );
	function formcraft_basic_new_form()
	{
		global $wpdb, $fcb_meta, $forms_table;
		if ( !current_user_can($fcb_meta['user_can']) ) { die(); }
		if ( !isset($_POST['form_name']) || empty($_POST['form_name']) )
		{
			$response = array('failed'=>__('Name is required','formcraft_basic') );
			echo json_encode($response); die();
		}
		$form_name = esc_sql(esc_attr($_POST['form_name']));
		if ( isset($_POST['file']) )
		{
			$file_name = sanitize_file_name($_POST['file']);
			$upload = wp_upload_dir( null );
			$upload['path'] = $upload['basedir'].'/formcraft_basic';
			if ( !file_exists($upload['path']."/".$file_name) )
			{
				$response = array('failed'=>__('File does not exist','formcraft_basic') );
				echo json_encode($response); die();
			}
			$file = file_get_contents($upload['path']."/".$file_name);
			$file = json_decode($file, 1);
			if ( !is_array($file) )
			{
				$response = array('failed'=>__('Invalid JSON File','formcraft_basic') );
				echo json_encode($response); die();				
			}
			if ( !isset($file['plugin']) || $file['plugin']!='FormCraft Basic' )
			{
				$response = array('failed'=>__('Not a form template','formcraft_basic') );
				echo json_encode($response); die();
			}
			$rows_affected = $wpdb->insert( $forms_table, array( 
				'name' => $form_name,
				'created' => current_time('mysql'),
				'modified' => current_time('mysql'),
				'html' => esc_sql(base64_decode($file['html'])),
				'builder' => esc_sql(base64_decode($file['builder'])),
				'meta_builder' => esc_sql(base64_decode($file['meta_builder']))
				) );
			if ($rows_affected==false || !is_int($wpdb->insert_id))
			{
				$response = array('failed'=>__('Could not write to database','formcraft_basic'));
				echo json_encode($response); die();
			}
			else
			{
				unlink($upload['path']."/".$file_name);
			}
			$response = array('success'=>__('Form created. Redirecting.','formcraft_basic'),'redirect'=>'&id='.$wpdb->insert_id);
		}
		else
		{
			$rows_affected = $wpdb->insert( $forms_table, array( 
				'name' => $form_name,
				'created' => current_time('mysql'),
				'modified' => current_time('mysql')
				) );
		}
		if ($rows_affected==false || !is_int($wpdb->insert_id))
		{
			$response = array('failed'=>__('Could not write to database','formcraft_basic'));
			echo json_encode($response); die();
		}
		$response = array('success'=>__('Form created. Redirecting.','formcraft_basic'),'redirect'=>'&id='.$wpdb->insert_id);
		echo json_encode($response); die();
	}


	/*
	Load Form Data in the Form Editor Mode
	*/
	add_action( 'wp_ajax_formcraft_form_data', 'formcraft_form_data' );
	function formcraft_form_data()
	{
		global $wpdb, $forms_table, $fcb_meta;
		if ( !current_user_can($fcb_meta['user_can']) ) { die(); }
		$form_id = $_GET['id'];
		if (!ctype_digit($form_id))
		{
			echo json_encode(array('failed'=>__('Invalid Form ID')));
			die();
		}
		if ($_GET['type']=='builder')
		{
			$builder = $wpdb->get_var( "SELECT builder FROM $forms_table WHERE id=$form_id" );
			echo $builder;
		}
		die();
	}

	/* Delete Submissions */
	add_action( 'wp_ajax_formcraft_basic_del_submissions', 'formcraft_basic_del_submissions' );
	function formcraft_basic_del_submissions()
	{
		global $fcb_meta, $forms_table, $submissions_table, $wpdb;
		if ( !current_user_can($fcb_meta['user_can']) ) { die(); }
		$list = explode(',',$_GET['list']);
		$deleted = 0;
		foreach ($list as $value) {
			if ( !ctype_digit($value) ) { continue; }
			$done = $wpdb->delete( $submissions_table, array('id'=>$value) );
			$deleted = $done==true ? $deleted+1 : $deleted;
		}
		if ($deleted>0)
		{
			echo json_encode(array('success'=>__($deleted.' submission(s) deleted','formcraft_basic') ));
			die();
		}
		else
		{
			echo json_encode(array('failed'=>__('Failed deleting submissions','formcraft_basic') ));
			die();
		}
	}

	/* Delete Form */
	add_action( 'wp_ajax_formcraft_basic_del_form', 'formcraft_basic_del_form' );
	function formcraft_basic_del_form()
	{
		global $fcb_meta, $forms_table, $submissions_table, $wpdb;
		if ( !current_user_can($fcb_meta['user_can']) ) { die(); }
		$form = $_GET['form'];
		if ( !ctype_digit($form) ) { die(); }
		$deleted = $wpdb->delete( $forms_table, array('id'=>$form) );
		if ($deleted>0)
		{
			echo json_encode(array('success'=>__('Form #'.$form.' deleted','formcraft_basic'), 'form_id'=>$form));
			die();
		}
		else
		{
			echo json_encode(array('failed'=>__('Failed deleting form','formcraft_basic') ));
			die();
		}
	}

	/* Get List of Submissions */
	add_action( 'wp_ajax_formcraft_basic_get_submissions', 'formcraft_basic_get_submissions' );
	function formcraft_basic_get_submissions()
	{
		global $fcb_meta, $forms_table, $submissions_table, $wpdb;
		if ( !current_user_can($fcb_meta['user_can']) ) { die(); }
		$page = isset($_POST['page']) && ctype_digit($_POST['page']) ? $_POST['page']-1 : 0;
		$form = isset($_POST['form']) && ctype_digit($_POST['form']) ? $_POST['form'] : 0;
		$per_page = 10;
		$from = $page*$per_page;
		$to = $from + $per_page;
		if ($form==0)
		{
			$submissions = $wpdb->get_results( "SELECT id,form,form_name,created FROM $submissions_table ORDER BY created DESC LIMIT $from, $per_page", ARRAY_A );
			$total = $wpdb->get_var( "SELECT COUNT(*) FROM $submissions_table" );
		}
		else
		{
			$submissions = $wpdb->get_results( "SELECT id,form,form_name,created FROM $submissions_table WHERE form = $form ORDER BY created DESC LIMIT $from, $to", ARRAY_A );
			$total = $wpdb->get_var( "SELECT COUNT(*) FROM $submissions_table WHERE form = $form" );
		}
		if ( is_array($submissions) && count($submissions)>0 )
		{
			foreach ($submissions as $key => $value) {
				$submissions[$key]['created'] = fcb_time_ago(strtotime(current_time('mysql'))-strtotime($submissions[$key]['created']));
			}
			echo json_encode(array('pages'=>ceil($total/$per_page),'submissions'=>$submissions,'total'=>$total));
			die();
		}
		else
		{
			echo json_encode(array('pages'=>'0','total'=>'0'));
			die();
		}
	}

	/* Get Submission Content */
	add_action( 'wp_ajax_formcraft_basic_get_submission_content', 'formcraft_basic_get_submission_content' );
	function formcraft_basic_get_submission_content()
	{
		global $fcb_meta, $forms_table, $submissions_table, $wpdb;
		if ( !current_user_can($fcb_meta['user_can']) ) { die(); }
		if ( !isset($_GET['id']) || !ctype_digit($_GET['id']) )
		{
			die();
		}
		$id = $_GET['id'];
		$submission = $wpdb->get_results( "SELECT id,form,form_name,content FROM $submissions_table WHERE id = $id", ARRAY_A );
		$submission[0]['content'] = json_decode(stripslashes($submission[0]['content']),1);
		foreach ($submission[0]['content'] as $key => $value) {
			$submission[0]['content'][$key]['value'] = fcb_stripslashes_deep($submission[0]['content'][$key]['value']);
		}
		echo json_encode($submission);
		die();
	}


	/*
	Submit The Form
	*/
	add_action( 'wp_ajax_formcraft_basic_form_submit', 'formcraft_basic_form_submit' );
	add_action('wp_ajax_nopriv_formcraft_basic_form_submit', 'formcraft_basic_form_submit');
	function formcraft_basic_form_submit()
	{
		global $fcb_meta, $forms_table, $submissions_table, $wpdb;
		if ( !isset($_POST['id']) || !ctype_digit($_POST['id']) )
		{
			echo json_encode(array('failed'=> __('Invalid Form ID','formcraft_basic') ));
			die();
		}
		if ( isset($_POST['website']) && $_POST['website']!='' )
		{
			echo json_encode(array('failed'=> __('SPAM detected','formcraft_basic') ));
			die();
		}
		$id = $_POST['id'];
		$meta = $wpdb->get_var( "SELECT meta_builder FROM $forms_table WHERE id=$id" );
		$meta = json_decode(stripcslashes($meta),1);
		$errors = array();
		$response = array();
		foreach ($meta['fields'] as $key => $field) {

			$value = isset($_POST[$field['identifier']]) ? $_POST[$field['identifier']] : '';

			/* Check if Required Field */
			if ( isset($field['elementDefaults']['required']) && $field['elementDefaults']['required']==true && empty($value) )
			{
				$errors['errors'][$field['identifier']] = __('Required','formcraft_basic');
			}
			if ( !isset($_POST[$field['identifier']]) ) { continue; }
			
			/* Field Type Validation */
			switch ($field['type']) {
				case 'email':
				if ( trim($value)!='' && filter_var( $value, FILTER_VALIDATE_EMAIL ) == false )
				{
					$errors['errors'][$field['identifier']] = __('Invalid email','formcraft_basic');
				}
				break;
				
				default:
				break;
			}

			/* Explicit Validation */
			if ( isset($field['elementDefaults']) && isset($field['elementDefaults']['Validation']) )
			{
				$spaces = isset($field['elementDefaults']['Validation']['spaces']) && $field['elementDefaults']['Validation']['spaces']==true ? true : false;
				$value_to_check = $spaces==true ? str_replace(' ', '', $value) : $value;
				foreach ($field['elementDefaults']['Validation'] as $type => $validation) {
					switch ($type) {
						case 'allowed':
						if ( $validation=='alphabets' && !ctype_alpha($value_to_check) )
						{
							$errors['errors'][$field['identifier']] = __('Only alphabets allowed','formcraft_basic');
						}
						else if ( $validation=='numbers' && !ctype_digit($value_to_check) )
						{
							$errors['errors'][$field['identifier']] = __('Only numbers allowed','formcraft_basic');
						}
						else if ( $validation=='alphanumeric' && !ctype_alnum($value_to_check) )
						{
							$errors['errors'][$field['identifier']] = __('Only alphabets and numbers allowed','formcraft_basic');
						}
						break;

						case 'minChar':
						if ( !ctype_digit($validation) ) break;
						if ( strlen($value) < $validation )
						{
							$errors['errors'][$field['identifier']] = __('Min '.$validation.' characters required','formcraft_basic');
						}
						break;

						case 'maxChar':
						if ( !ctype_digit($validation) ) break;
						if ( strlen($value) > $validation )
						{
							$errors['errors'][$field['identifier']] = __('Max '.$validation.' characters allowed','formcraft_basic');
						}
						break;

						default:
						break;
					}
				}
			}

		} /* End of Fields Loop */


		/* If validation failed, show errors */
		if ( count($errors)>0 )
		{
			if ( isset($meta['config']['messages']['form_errors']) )
			{
				$response['failed'] = $meta['config']['messages']['form_errors'];
			}
			else
			{
				$response['failed'] = __('Please correct the errors','formcraft_basic');
			}			
			$response['errors'] = $errors;
			echo json_encode($response);
			die();
		}
		/* ELSE All is Well with the Submission */

		/* Clean the User Input */
		foreach ($meta['fields'] as $key => $field) {
			if ( isset($_POST[$field['identifier']]) ) {
				if (is_array($_POST[$field['identifier']]))
				{
					foreach($_POST[$field['identifier']] as $key => $value) {
						$_POST[$field['identifier']][$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
					}
				}
				else
				{
					$_POST[$field['identifier']] = htmlentities($_POST[$field['identifier']], ENT_QUOTES, "UTF-8");
				}
			}
		}

		/* Parse and Organize Input */
		$content = array();
		foreach ($meta['fields'] as $key => $field) {
			if ( $field['type']=='submit' ) { continue; }
			unset($value);
			if ( isset($_POST[$field['identifier']]) ) { $value = $_POST[$field['identifier']]; }
			$value = isset($value) ? $value : '';
			$label = isset($field['elementDefaults']['main_label']) ? $field['elementDefaults']['main_label'] : '';
			$content[] = array('label'=>$label,'value'=>$value,'identifier'=>$field['identifier']);
		}

		$visitor = array();
		$visitor['IP'] = $_SERVER['REMOTE_ADDR'];
		$rows_affected = $wpdb->insert( $submissions_table, array( 
			'form' => $id,
			'form_name' => $wpdb->get_var( "SELECT name FROM $forms_table WHERE id='$id'" ),
			'content' => esc_sql(json_encode($content)),
			'visitor' => esc_sql(json_encode($visitor)),
			'created' => current_time('mysql')
			) );

		/* Written to Database, so it works */
		if ($rows_affected)
		{
			if ( isset($meta['config']['messages']['form_sent']) )
			{
				$response['success'] = $meta['config']['messages']['form_sent'];
			}
			else
			{
				$response['success'] = __('Message Received','formcraft_basic');
			}
		}
		else
		{
			$response['failed'] = __('Failed to Write','formcraft_basic');
			echo json_encode($response); die();
		}

		if ( isset($meta['config']) )
		{
			if ( isset($meta['config']['Email']['recipients']) )
			{
				$emails = fcb_parse_emails($meta['config']['Email']['recipients'], 10);
				$sent = 0;
				if ( is_array($emails) && count($emails)>0 )
				{
					$subject = isset($meta['config']['Email']['subject']) ? $meta['config']['Email']['subject'] : __('New Form Submission','formcraft_basic');
					$subject = fcb_template($content, $subject);

					$from_name = isset($meta['config']['Email']['name_from']) ? $meta['config']['Email']['name_from'] : 'FormCraft';
					$from_name = fcb_template($content, $from_name);

					$from_email = isset($meta['config']['Email']['email_from']) ? $meta['config']['Email']['email_from'] : get_bloginfo('admin_email');
					$from_email = fcb_template($content, $from_email);

					foreach ($emails as $email => $name) {
						$email_content = '';
						$headers = 'From: '.$from_name.' <'.$from_email.'>' . "\r\n";
						foreach ($content as $key => $field) {
							$email_content.= $field['label']."	".( is_array($field['value']) ? implode(', ', $field['value']) : $field['value'] )."\n";
						}
						if (wp_mail( $email, $subject, $email_content, $headers ))
						{
							$sent++;
						}
					}
				}
				if ($sent!=0) {$response['debug']['failed'] = __('Emails not sent','formcraft_basic');}
				else {$response['debug']['success'] = __($sent.' emails sent','formcraft_basic');}
			}
		}
		echo json_encode($response); die();
	}


	/*
	Save Form Data from the Form Editor Mode
	*/
	add_action( 'wp_ajax_formcraft_basic_form_save', 'formcraft_basic_form_save' );
	function formcraft_basic_form_save()
	{
		global $wpdb, $fcb_meta, $forms_table;
		if ( !current_user_can($fcb_meta['user_can']) ) { die(); }
		$form_id = $_POST['id'];
		if (!ctype_digit($form_id))
		{
			echo json_encode(array('failed'=>__('Invalid Form ID')));
			die();
		}
		$builder = $_POST['builder'];
		$meta_builder = esc_sql(stripslashes($_POST['meta_builder']));
		$html = esc_sql(stripslashes($_POST['html']));
		$html = formcraft_basic_replace_comments('<!--RFH-->','<!--RTH-->',$html,'');
		if ( $builder != esc_sql($builder) )
		{
			echo json_encode(array('failed'=>__('Lost in Translation')));
			die();
		}
		if ( $wpdb->update($forms_table, array( 'meta_builder' => $meta_builder, 'builder' => $builder, 'html' => $html, 'modified' => current_time('mysql') ), array('ID'=>$form_id)) === FALSE) {
			echo json_encode(array('failed'=>__('Could not write to database')));
			die();
		} else {
			echo json_encode(array('success'=>__('Form Saved')));
			die();
		}
		die();
	}


	/*
	Save Imported Form File
	*/
	add_action( 'wp_ajax_formcraft_basic_import_file', 'formcraft_basic_import_file' );
	function formcraft_basic_import_file()
	{
		global $wpdb, $fcb_meta;
		if ( !current_user_can($fcb_meta['user_can']) ) { die(); }
		if ( isset($_FILES['form_file']) )
		{
			if ( !isset($_FILES['form_file']['type']) || $_FILES['form_file']['type']!='text/plain' )
			{
				echo json_encode(array('failed'=> __('Invalid File Format','formcraft_basic') ));
				die();
			}
			else
			{
				$filename = urldecode($_FILES["form_file"]["name"]);
				$filename = sanitize_file_name($filename);
				$file = fcb_wp_upload_bits($filename, null, file_get_contents($_FILES["form_file"]["tmp_name"]));
				if ( $file['error']==true )
				{
					echo json_encode(array('failed'=> __('Failed','formcraft_basic'), 'debug' => $file['error'] ));
					die();
				}
				else
				{
					echo json_encode(array('success'=> urlencode($file['name'])));
					die();
				}
			}
		}
		die();
	}


	/*
	Add Dashboard Menu Page
	Every user who can activate a plugin (i.e. every admin user) can access FormCraft
	*/
	add_action('admin_menu', 'formcraft_basic_admin' );
	function formcraft_basic_admin()
	{
		global $wp_version, $fcb_meta;
		$icon_url = $wp_version >= 3.8 ? 'dashicons-list-view' : '';
		add_menu_page( 'FormCraft Basic', 'FormCraft Basic', $fcb_meta['user_can'], 'formcraft_basic_dashboard', 'formcraft_basic_dashboard', $icon_url, '32.0505' );
		add_action( 'admin_enqueue_scripts', 'formcraft_basic_admin_assets' );
	}
	function formcraft_basic_admin_assets($hook)
	{
		global $fcb_meta;
		if ($hook!='toplevel_page_formcraft_basic_dashboard') { return false; }

		/* Basic Styles and Scripts */
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('fcb-modal-js', plugins_url( 'assets/js/fcbmodal.js', __FILE__ ));
		wp_enqueue_script('fcb-toastr-js', plugins_url( 'assets/js/toastr.min.js', __FILE__ ));

		wp_enqueue_style('fcb-common-css', plugins_url( 'assets/css/common-elements.css', __FILE__ ),array(), $fcb_meta['version']);  
		wp_enqueue_style('fcb-modal-css', plugins_url( 'assets/css/fcbmodal.css', __FILE__ ),array(), $fcb_meta['version']);  
		wp_enqueue_style('fcb-fontello-css', plugins_url( 'assets/fontello/css/fcb.css', __FILE__ ),array(), $fcb_meta['version']);  


		/* Dashboard Styles and Scripts */
		wp_enqueue_script('fcb-dashboard-js', plugins_url( 'assets/js/dashboard.js', __FILE__ )); 
		wp_enqueue_script('fcb-fileupload-js', plugins_url( 'assets/js/jquery.fileupload.js', __FILE__ ),array('jquery-ui-widget')); 
		wp_enqueue_script('fcb-tooltip-js', plugins_url( 'assets/js/tooltip.min.js', __FILE__ )); 
		wp_localize_script( 'fcb-dashboard-js', 'FCB_1',
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'confirm_delete' => __("Are you sure you want to delete this form?\nThis action cannot be reversed.", 'formcraft_basic')
				)
			);

		wp_enqueue_style('fcb-zurb-css', plugins_url( 'assets/css/foundation.min.css', __FILE__ ),array(), $fcb_meta['version']);
		wp_enqueue_style('fcb-dashboard-css', plugins_url( 'assets/css/dashboard.css', __FILE__ ),array(), $fcb_meta['version']);


		/* Builder Styles and Scripts */
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style('fcb-main-css', plugins_url( 'assets/css/form.main.css', __FILE__ ),array(), $fcb_meta['version']);

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script('fcb-angular-js', plugins_url( 'assets/js/angular.min.js', __FILE__ )); 
		wp_enqueue_script('fcb-angular-animate-js', plugins_url( 'assets/js/angular-animate.min.js', __FILE__ )); 
		wp_enqueue_script('fcb-angular-sortable-js', plugins_url( 'assets/js/angular-sortable.min.js', __FILE__ )); 
		wp_enqueue_script('fcb-builder-js', plugins_url( 'assets/js/builder.js', __FILE__ )); 
		wp_localize_script( 'fcb-builder-js', 'FCB',
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'baseurl' => get_site_url(),
				'datepickerLang' => plugins_url( 'assets/js/datepicker-lang/', __FILE__ ),
				'form_id' => isset($_GET['id']) ? intval($_GET['id']) : 0
				)
			);
		wp_enqueue_script('fcb-deflate-js', plugins_url( 'assets/js/deflate.all.js', __FILE__ )); 


	}
	function formcraft_basic_dashboard()
	{
		if ( isset($_GET['id']) )
		{
			require_once('views/builder.php');
		}
		else
		{
			require_once('views/dashboard.php');
		}
	}

	/* Common Functions */
	function fcb_formatDate($time) {
		if ($time >= strtotime("today 00:00")) {
			return "Today at ".date("g:i A", $time);
		} elseif ($time >= strtotime("yesterday 00:00")) {
			return "Yesterday at " . date("g:i A", $time);
		} elseif ($time >= strtotime("-6 day 00:00")) {
			return date("l \\a\\t g:i A", $time);
		} else {
			return date("M j, Y", $time);
		}
	}


	function fcb_time_ago($secs){
		$bit = array(
			' year'        => $secs / 31556926 % 12,
			' week'        => $secs / 604800 % 52,
			' day'        => $secs / 86400 % 7,
			' hr'        => $secs / 3600 % 24,
			' min'    => $secs / 60 % 60,
			' sec'    => $secs % 60
			);


		foreach($bit as $k => $v)
		{
			if($v > 1)$ret[] = $v . $k;
			if($v == 1)$ret[] = $v . $k;
			if (isset($ret)&&count($ret)==2){break;}
		}
		if (isset($ret))
		{
			if (count($ret)>1)
			{
				array_splice($ret, count($ret)-1, 0, 'and');
			}
			$ret[] = 'ago';
			return join(' ', $ret);
		}
		return '';
	}

	function fcb_time_pretty($secs){
		$bit = array(
			'year'        => $secs / 31556926 % 12,
			'week'        => $secs / 604800 % 52,
			'day'        => $secs / 86400 % 7,
			'hr'        => $secs / 3600 % 24,
			'm'    => $secs / 60 % 60,
			's'    => $secs % 60
			);


		foreach($bit as $k => $v)
		{
			if($v > 1)$ret[] = $v . $k;
			if($v == 1)$ret[] = $v . $k;
			if (isset($ret)&&count($ret)==2){break;}
		}
		if (isset($ret))
		{
			if (count($ret)>1)
			{
				array_splice($ret, count($ret)-1, 0, 'and');
			}
			return join(' ', $ret);
		}
		return '';
	}

	/* General Function to Remove Text */
	function formcraft_basic_replace_comments($beginning, $end, $string, $replace)
	{
		$loop = false;
		while ($loop==false)
		{
			$beginningPos = null;
			$endPos = null;
			$beginningPos = strpos($string, $beginning);
			$endPos = strpos($string, $end);
			if ( $beginningPos===false || $endPos===false)
			{
				return $string;
				$loop = true;
			}
			$textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
			$string = str_replace($textToDelete, $replace, $string);
			$loop = false;
		}
		return $string;
	}
	function fcb_parse_emails($string, $nos = 20)
	{
		$emails = array();
		if(preg_match_all('/\s*"?([^><,"]+)"?\s*((?:<[^><,]+>)?)\s*/', $string, $matches, PREG_SET_ORDER) > 0)
		{
			$i = 0;
			foreach($matches as $m)
			{
				if ($i>=$nos){break;}
				if(! empty($m[2]))
				{
					if (!filter_var(trim($m[2], '<>'), FILTER_VALIDATE_EMAIL)) {continue;}
					$emails[trim($m[2], '<>')] = trim($m[1]);
				}
				else
				{
					if (!filter_var($m[1], FILTER_VALIDATE_EMAIL)) {continue;}
					$emails[$m[1]] = '';
				}
				$i++;
			}
		}
		return $emails;
	}

	function fcb_template($content, $template)
	{
		preg_match_all('/\[.*?\]/', $template, $matches);
		foreach ($content as $id => $value) {
			if (in_array('['.$value['label'].']', $matches[0])==true)
			{
				$value['value'] = is_array($value['value']) ? implode(", ", $value['value']) : $value['value'];
				if (!empty($value['value']))
				{
					$template = str_replace('['.$value['label'].']', htmlentities($value['value']), $template);
				}
			}
			if (in_array('['.$value['identifier'].']', $matches[0])==true)
			{
				$value['value'] = is_array($value['value']) ? implode(", ", $value['value']) : $value['value'];
				if (!empty($value['value']))
				{
					$template = str_replace('['.$value['identifier'].']', htmlentities($value['value']), $template);
				}
			}
		}
		return $template;
	}
	function fcb_offset()
	{
		return floatval(get_option('gmt_offset'))*60*60;
	}
	function fcb_stripslashes_deep($value)
	{
		$value = is_array($value) ?
		array_map('stripslashes_deep', $value) :
		stripslashes($value);
		return $value;
	}

	function fcb_wp_upload_bits( $name, $deprecated, $bits, $time = null ) {
		if ( !empty( $deprecated ) )
			_deprecated_argument( __FUNCTION__, '2.0' );

		if ( empty( $name ) )
			return array( 'error' => __( 'Empty filename' ) );

		$wp_filetype = wp_check_filetype( $name );
		if ( ! $wp_filetype['ext'] && ! current_user_can( 'unfiltered_upload' ) )
			return array( 'error' => __( 'Invalid file type' ) );

		$upload = wp_upload_dir( $time );
		$upload['path'] = $upload['basedir'].'/formcraft_basic';
		$upload['url'] = $upload['baseurl'].'/formcraft_basic';
		$upload['subdir'] = '/formcraft_basic';

		if ( $upload['error'] !== false )
			return $upload;
		$upload_bits_error = apply_filters( 'wp_upload_bits', array( 'name' => $name, 'bits' => $bits, 'time' => $time ) );
		if ( !is_array( $upload_bits_error ) ) {
			$upload[ 'error' ] = $upload_bits_error;
			return $upload;
		}

		$filename = wp_unique_filename( $upload['path'], $name );

		$new_file = $upload['path'] . "/$filename";
		if ( ! wp_mkdir_p( dirname( $new_file ) ) ) {
			if ( 0 === strpos( $upload['basedir'], ABSPATH ) )
				$error_path = str_replace( ABSPATH, '', $upload['basedir'] ) . $upload['subdir'];
			else
				$error_path = basename( $upload['basedir'] ) . $upload['subdir'];

			$message = sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $error_path );
			return array( 'error' => $message );
		}

		$ifp = @ fopen( $new_file, 'wb' );
		if ( ! $ifp )
			return array( 'error' => sprintf( __( 'Could not write file %s' ), $new_file ) );

		@fwrite( $ifp, $bits );
		fclose( $ifp );
		clearstatcache();

		$stat = @ stat( dirname( $new_file ) );
		$perms = $stat['mode'] & 0007777;
		$perms = $perms & 0000666;
		@ chmod( $new_file, $perms );
		clearstatcache();
		$url = $upload['url'] . "/$filename";

		return array( 'file' => $new_file, 'url' => $url, 'name'=> $filename,'error' => false );
	}


	?>