<?php
defined( 'ABSPATH' ) or die( 'Cheating, huh?' );
global $fcb_version, $forms_table, $submissions_table, $views_table, $wpdb;
$forms = $wpdb->get_results( "SELECT id,name,modified FROM $forms_table" );
$time = date('Y-m-d 00:00:00',time()+fcb_offset());

$total_subs = $wpdb->get_var( "SELECT COUNT(*) FROM $submissions_table" );
$today_subs = $wpdb->get_var( "SELECT COUNT(*) FROM $submissions_table WHERE created > '$time'" );
$today_views = $wpdb->get_var( "SELECT SUM(views) FROM $views_table WHERE views_date = '$time'" );

$today_views = $today_views==null?0:$today_views;
$today_subs = $today_subs==null?0:$today_subs;
$total_subs = $total_subs==null?0:$total_subs;
$total_forms = count($forms)==0 ? '0' : count($forms);
?>
<style>
	#toast-container
	{
		top: 10px;
	}
</style>
<div class='formcraft-css'>
	<div class='row'>
		<div class='large-12 column fcb-brand-header'>
			<h1>FormCraft <span>Basic</span></h1>
			<p class='sub-header'> designed by <a target='_blank' href='http://ncrafts.net?ref=formcraft_basic'>nCrafts</a></p>
		</div>
	</div>
	<div class='row' style='position: relative; z-index: 101'>
		<div class='large-12 column'>
			<div id='form_options' class='block'>
				<div class='row fcb-header'>
					<div class='large-2 column' style='color: inherit'>
						<span class='one' style='border-color: inherit'><?php echo $today_views; ?></span>
						<span class='two'><?php _e('form views','formcraft_basic'); ?></span>
						<span class='three'><?php _e('today','formcraft_basic'); ?></span>
					</div>
					<div class='large-2 column' style='color: inherit'>
						<span class='one' style='border-color: inherit'><?php echo $today_subs; ?></span>
						<span class='two'><?php _e('submissions','formcraft_basic'); ?></span>
						<span class='three'><?php _e('today','formcraft_basic'); ?></span>
					</div>
					<div class='large-2 column' style='color: inherit'>
						<span class='one' style='border-color: inherit'><?php echo $total_subs; ?></span>
						<span class='two'><?php _e('total','formcraft_basic'); ?></span>
						<span class='three'><?php _e('submissions','formcraft_basic'); ?></span>
					</div>
					<div class='large-2 column has-hover'>
						<div style='color: inherit' data-toggle="fcbmodal" data-target="#new_form_modal">
							<span class='one' style='border-color: inherit'><i class='icon-plus'></i></span>
							<span class='two'><?php _e('create','formcraft_basic'); ?></span>
							<span class='three'><?php _e('new form','formcraft_basic'); ?></span>
						</div>
					</div>
					<a href='https://wordpress.org/support/view/plugin-reviews/formcraft-form-builder#postform' target='_blank' class='large-2 column has-hover'>
						<span class='one'><i class='icon-heart'></i></span>
						<span class='two'><?php _e('like the plugin?','formcraft_basic'); ?></span>
						<span class='three'><?php _e('give FormCraft rating','formcraft_basic'); ?></span>
					</a>
					<div class='large-2 column has-hover' data-toggle="fcbmodal" data-target="#upgrade_modal">
						<span class='one'><i class='icon-star'></i></span>
						<span class='two'><?php _e('check out','formcraft_basic'); ?></span>
						<span class='three'><?php _e('premium features','formcraft_basic'); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='large-6 column subs_cover'>
			<div class='subs_options'><span><i id='trash-subs' class='icon-trash-1'></i></span></div><h2><?php _e('Your Submissions','formcraft_basic'); ?> <span id='total-submissions'></span></h2>
			<div class='block'>
				<div class='table_list subs_list <?php echo $total_subs==0?'no-subs':''; ?>' cellpadding='0' cellspacing='0'>
					<div class='loader'>
					</div>
					<div class='tr thead'>
						<span style='width:8.5%'><label><input class='subs_checked_parent' name='subs_checked_parent' type='checkbox'></label></span>
						<span style='width:61.5%; padding: 0'>
							<select id='which-form'>
								<option value='0'><?php _e('All Forms','formcraft_basic'); ?></option>
								<?php
								foreach ($forms as $key => $value) {
									echo "<option value='".$value->id."'>".$value->name."</option>";
								}
								?>
							</select>
						</span>
						<span style='width:30%'>Received</span>
					</div>
					<?php
					if ($total_subs!=0)
					{
					?><div class='pagination'>
						<span>1</span>
					</div>
					<?php
				}
					?>
					<div class='tbody'>
					</div>
					<div class='no-subs-content'><?php _e('No Submissions','formcraft_basic'); ?></div>
				</div>
			</div>
		</div>
		<div class='large-6 column'>
			<h2><?php _e('Your Forms','formcraft_basic'); ?> <span>(<?php echo $total_forms; ?>)</span></h2>
			<div class='block'>
				<div class='table_list form_list' cellpadding='0' cellspacing='0'>
					<div class='tr thead'>
						<span style='width:8.5%'>ID</span>
						<span style='width:41.5%'>Name</span>
						<span style='width:10%'>Views</span>
						<span style='width:30%'>Last Edit</span>
						<span style='width:10%'></span>
					</div>
					<div class='tbody'>
						<?php
						if ( $total_forms>0 )
						{
							foreach ($forms as $key => $value) {
								$form_views = $wpdb->get_var( "SELECT SUM(views) FROM $views_table WHERE form = '$value->id'" );
								$form_views = $form_views==0 ? '0' : $form_views;
								?>
								<div class='tr form-<?php echo $value->id; ?>'>
									<span style='width:8.5%'><a href='admin.php?page=formcraft_basic_dashboard&id=<?php echo $value->id; ?>'><?php echo $value->id; ?></a></span>
									<span style='width:41.5%'><a href='admin.php?page=formcraft_basic_dashboard&id=<?php echo $value->id; ?>'><?php echo $value->name; ?></a></span>
									<span style='width:10%'><a href='admin.php?page=formcraft_basic_dashboard&id=<?php echo $value->id; ?>'><?php echo $form_views; ?></a></span>
									<span style='width:30%'><a href='admin.php?page=formcraft_basic_dashboard&id=<?php echo $value->id; ?>'><?php echo fcb_time_ago(strtotime(current_time('mysql'))-strtotime($value->modified)); ?></a></span>
									<span style='width:10%'><i data-id='<?php echo $value->id; ?>' class='trash-icon trash-form icon-trash-1'></i></span>
								</div>
								<?php
							}
						}
						else
						{
							?>
							<span class='no-subs-content' data-toggle="fcbmodal" data-target="#new_form_modal"><?php _e('Create New Form','formcraft_basic'); ?></span>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="fcbmodal fcbfade" id="new_form_modal">
		<div class="fcbmodal-dialog" style="width: 350px">
			<form class="fcbmodal-content" id='new_form'>
			<div class='fcbmodal-header'>
					<button class='fcbclose' type="button" class="close" data-dismiss="fcbmodal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

					<nav class='nav-tabs' data-content='#add_form_tabs'>
						<span class="active"><?php _e('New Form','formcraft_basic'); ?></span>
						<span><?php _e('Import Form','formcraft_basic'); ?></span>
					</nav>
			</div>
				<div class="fcbmodal-body">
					<input type='text' name='form_name' placeholder='<?php _e('Form Name','formcraft_basic'); ?>'>
					<div id='add_form_tabs' class='nav-content'>
						<div class='active'>
						</div>
						<div>
							<div style='margin: 10px 0;position: relative'>
								<span style='width: 100%' class='button button-file'><?php _e('Upload Form Template','formcraft_basic'); ?><input data-url='<?php echo admin_url( 'admin-ajax.php' )."?action=formcraft_basic_import_file"; ?>' type="file" id='import_form_input' name='form_file'/>
								</span>
								<span class='filename'></span>
							</div>
						</div>
					</div>
					<div class="fcbmodal-section">
						<button type="submit" class="button blue wide"><span><?php _e('Create Form','formcraft_basic'); ?></span><span class="fcb-spinner small">
							<span class="bounce1"></span>
							<span class="bounce2"></span>
							<span class="bounce3"></span>
						</span></button>
						<span class='response'></span>
					</div>
				</div>
			</form><!-- /.fcbmodal-content -->
		</div><!-- /.fcbmodal-dialog -->
	</div><!-- /.fcbmodal -->
	<div class="fcbmodal fcbfade" id="submission_modal">
		<div class="fcbmodal-dialog">
			<div class="fcbmodal-content">
				<div class="fcbmodal-header">
					<button class='fcbclose' type="button" class="close" data-dismiss="fcbmodal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="fcbmodal-title"></h4>
				</div>
				<div class="fcbmodal-body">
					BODY
				</div>
				<div class="fcbmodal-footer">
					<button class="button blue small close"><?php _e('Close','formcraft_basic'); ?></button>
				</div>
			</div><!-- /.fcbmodal-content -->
		</div><!-- /.fcbmodal-dialog -->
	</div><!-- /.fcbmodal -->
	<div class="fcbmodal fcbfade" id="upgrade_modal">
		<div class="fcbmodal-dialog">
			<div class="fcbmodal-content">
				<div class="fcbmodal-header">
					<h4 class="fcbmodal-title"><?php _e('What\'s in FormCraft Premium','formcraft_basic') ?></h4>
					<button class='fcbclose' type="button" class="close" data-dismiss="fcbmodal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="fcbmodal-body" style="height: 420px; overflow: auto; padding: 10px 20px">
					<ol>
						<li>
							<h2><?php _e('Conditional Logic','formcraft_basic'); ?></h2>
							<p><?php _e('Use conditional logic to show / hide fields, set email recipients, or custom redirection URLs.','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Math Logic','formcraft_basic'); ?></h2>
							<p><?php _e('Use our live updating math logic to make order and quote forms','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Save Form Progress','formcraft_basic'); ?></h2>
							<p><?php _e('Automatically save your users\'s form data as they type in, allowing them to resume the form later on.','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('20+ Form Fields','formcraft_basic'); ?></h2>
							<p><?php _e('Choose from over 20+ form fields, including emoticon rating, thumb-rating, star rating, matrix, image, captcha, etc ...','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Accept File Uploads','formcraft_basic'); ?></h2>
							<p><?php _e('Add a multi-file upload field, allow your users to upload files.','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Integrations','formcraft_basic'); ?></h2>
							<p><?php _e('Use our free integrations for MailChimp, GetResponse, Campaign Monitor, AWeber','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Popup Forms','formcraft_basic'); ?></h2>
							<p><?php _e('Embed forms on your site as popup, sticky, or fly-in forms.','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Download Templates','formcraft_basic'); ?></h2>
							<p><?php _e('Download forms from our online template gallery, and import them in your plugin.','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Export Data','formcraft_basic'); ?></h2>
							<p><?php _e('Export all your submissions for a spreadsheet.','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Auto-responders','formcraft_basic'); ?></h2>
							<p><?php _e('Send personalized auto-responders to users who fill your form. Customize all the email content of auto-responders.','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Analytics','formcraft_basic'); ?></h2>
							<p><?php _e('Get form analytics inside the WordPress dashboard','formcraft_basic'); ?></p>
						</li>
						<li>
							<h2><?php _e('Documentation and Support','formcraft_basic'); ?></h2>
							<p><?php _e('Get access to our online documentation, and get free support in case you run into an issue.','formcraft_basic'); ?></p>
						</li>
					</ol>
				</div>
				<div class='fcbmodal-footer'>
					<a class='button blue' target='_blank' href='http://codecanyon.net/item/formcraft-premium-wordpress-form-builder/5335056?ref=ncrafts'><?php _e('Get FormCraft Premium','formcraft_basic'); ?> <i class='icon-angle-right' style='position: relative; right: -5px'></i></a>
				</div>
			</div>
		</div>
	</div>
</div>