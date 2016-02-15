<?php
defined( 'ABSPATH' ) or die( 'Cheating, huh?' );
global $fcb_version, $wpdb, $forms_table;
$form_id = intval($_GET['id']);
$qry = $wpdb->get_results( "SELECT * FROM $forms_table WHERE id = '$form_id'" );
?>
<input type='hidden' id='form_id' value='<?php echo $form_id; ?>'>
<div ng-app='FormCraft'>
	<div class='formcraft-css' ng-controller='FormController' ng-init='AngularInit()'>
		<div class='options-head'>
			<div>
				<a href='admin.php?page=formcraft_basic_dashboard' class='button blue'><i class='icon-angle-left'></i>Dashboard</a>
				<div class='add_fields'>
					<button class='button blue icon-{{Options.show_fields}}' ng-click='Options.show_fields = !Options.show_fields'><i class='icon-angle-down'></i><i class='icon-angle-up'></i><?php _e('Add Field','formcraft_basic') ?></button>
					<div ng-show='Options.show_fields' class='ng-hide'>
						<div ondragstart="drag(event)" ondragend="dragEnd(event)" dnd-draggable class='button' ng-click='addFormElement("oneLineText")'><?php _e('One Line Input','formcraft_basic') ?></div>
						<div ondragstart="drag(event)" ondragend="dragEnd(event)" dnd-draggable class='button' ng-click='addFormElement("email")'><?php _e('Email Input','formcraft_basic') ?></div>
						<div ondragstart="drag(event)" ondragend="dragEnd(event)" dnd-draggable class='button' ng-click='addFormElement("textarea")'><?php _e('Comment Box','formcraft_basic') ?></div>
						<div ondragstart="drag(event)" ondragend="dragEnd(event)" dnd-draggable class='button' ng-click='addFormElement("checkbox")'><?php _e('Checkboxes','formcraft_basic') ?></div>
						<div ondragstart="drag(event)" ondragend="dragEnd(event)" dnd-draggable class='button' ng-click='addFormElement("dropdown")'><?php _e('Dropdown','formcraft_basic') ?></div>
						<div ondragstart="drag(event)" ondragend="dragEnd(event)" dnd-draggable class='button' ng-click='addFormElement("datepicker")'><?php _e('Datepicker','formcraft_basic') ?></div>
						<div ondragstart="drag(event)" ondragend="dragEnd(event)" dnd-draggable class='button' ng-click='addFormElement("customText")'><?php _e('Custom Text','formcraft_basic') ?></div>
						<div ondragstart="drag(event)" ondragend="dragEnd(event)" dnd-draggable class='button' ng-click='addFormElement("submit")'><?php _e('Submit','formcraft_basic') ?></div>
					</div>
				</div>
				<div>
					<button data-toggle="fcbmodal" data-target="#form_options_modal" style='width: 152px' class='button blue'><i class='icon-cog-alt'></i> <?php _e('Form Options','formcraft_basic') ?></button>
				</div>
				<div>
					<button style='width: 135px' id='form_save_button' ng-click='saveForm()' class='button blue'><span class="fcb-spinner small"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></span><span class='one'><i class='icon-up-circled'></i> <?php _e('Save Form','formcraft_basic') ?></span></button>
				</div>
				<div>
					<button style='width: 115px' type='submit' ng-click='saveForm("preview")' id='form_preview_button' class='button blue'><i class='icon-doc-text'></i> <?php _e('Preview','formcraft_basic') ?></button>
				</div>
			</div>
		</div>
		<div class='promo-head'>
			<a data-toggle="fcbmodal" data-target="#upgrade_modal"  class='button blue'><i class='icon-star'></i> <?php _e('Premium Features','formcraft_basic') ?></a>
		</div>
		<div class='form-cover-builder hide-form'>
			<span class='fcb-spinner fcb-spinner-form small'><div class='bounce1'></div><div class='bounce2'></div><div class='bounce3'></div></span>
			<div id='form-cover-html' class='nos-{{Builder.FormElements.length}}' style='width: {{Builder.form_width_nos}}px'>
				<!--RFH-->
				<div class='no-fields' ng-click='Options.show_fields = true'><?php _e('+ Add a Field','formcraft_basic'); ?></div>
				<div style='width: {{Builder.form_width}}' class='form_width_change'><span><?php _e('Width','formcraft_basic') ?></span><input ng-model='Builder.form_width' type='text'/><i data-html='true' data-toggle='tooltip' title='<?php _e('You should use a value like <Strong>300px</strong>, or <Strong>420px</strong> here.<br>You can also define a %-based width, like <strong>50%</strong>, which will set the width of the form to 50% the width of its container. ','formcraft_basic') ?>' class='icon-help'></i></div>
				<!--RTH--><form ondrop="drop(event)" ondragover="allowDrop(event)" dnd-list='Builder.FormElements' data-id='<?php echo $form_id; ?>' class='fcb_form align-{{Builder.form_align}} frame-{{Builder.form_frame}}' style='width: {{Builder.form_width}}; background-color: {{Builder.form_background}}; color: {{Builder.font_color}}; font-size: {{Builder.font_size}}%'><div ng-class-odd="'odd'" ng-class='["form-element", "form-element-"+element.elementDefaults.identifier, "options-"+element.show_options, "index-"+element.show_options]' ng-class-even="'even'" ng-repeat='element in Builder.FormElements' index='{{$index}}' style='width: {{element.elementDefaults.field_width}}'><span class='error'></span><div ng-click='element.show_options = !element.show_options' class='form-element-html' compile='element.element'></div><!--RFH--><span class='options-panel' ondragstart="dragStart(event)" dnd-draggable='element' ondrag="setHeight(event)" dnd-moved='Builder.FormElements.splice($index, 1);' ondragend='removeAnimate()'><i class='icon-move'></i></span><div ng-show='element.show_options' class='form-options'><div class='sub-options'><div title='Field ID'>{{element.elementDefaults.identifier}}</div><span title='Delete Field' ng-click='removeFormElement($index)' class='delete'><i class='icon-trash-1'></i></span><span title='Duplicate Field' ng-click='duplicateFormElement($index)' class='duplicate'><i class='icon-docs'></i></span></div><div class='options-main' compile='element.elementOptions'></div></div><!--RTH--></div></form>
			</div>
		</div>

		<div class="fcbmodal fcbfade" id="form_options_modal">
			<div class="fcbmodal-dialog">
				<div class="fcbmodal-content">
					<div class="fcbmodal-header">
						<button class='fcbclose' type="button" class="close" data-dismiss="fcbmodal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="fcbmodal-title"><?php _e('Form Options','formcraft_basic'); ?></h4>
						<nav class='nav-tabs' data-content='#options_tabs'>
							<span class='active'><?php _e('Styling','formcraft_basic'); ?></span>
							<span><?php _e('Notifications','formcraft_basic'); ?></span>
							<span><?php _e('Embed','formcraft_basic'); ?></span>
							<span><?php _e('Others','formcraft_basic'); ?></span>
						</nav>
					</div>
					<div class="fcbmodal-body">
						<div id='options_tabs' class='nav-content'>
							<div class='active'>
								<div class='group'>
									<h2>
										<?php _e('Form Styling','formcraft_basic'); ?>
									</h2>
									<div>
										<span class='option-label'><?php _e('Form Background','formcraft_basic'); ?></span>
										<input type="text" value="#fff" class="color-picker" ng-model='Builder.form_background'>
									</div>
									<div>
										<span class='option-label'><?php _e('Font Size','formcraft_basic'); ?></span>
										<button class='button' ng-click='Builder.font_size = Builder.font_size + 5'>+</button>
										<button class='button' ng-click='Builder.font_size = Builder.font_size - 5'>-</button>
										&nbsp; {{Builder.font_size}}%
									</div>
									<div>
										<span class='option-label'><?php _e('Font Color','formcraft_basic'); ?></span>
										<input type="text" value="#fff" class="color-picker" ng-model='Builder.font_color'>
									</div>
									<div>
										<label>
											<span class='option-label'>Form Frame</span>
											<input type='checkbox' ng-model='Builder.form_frame' ng-true-value='"hidden"' ng-false-value='"visible"'> <?php _e('Remove','formcraft_basic'); ?>
										</label>
									</div>
								</div>
							</div>
							<div>
								<div class='group'>
									<h2>
										<?php _e('Email Recipients','formcraft_basic'); ?>
									</h2>
									<div>
										<span class='option-label'><?php _e('Send Emails To','formcraft_basic'); ?></span>
										<input type="text" placeholder="dan@example.com, joe@example.com" ng-model='Builder.Config.Email.recipients' style='width: 300px'>
									</div>
								</div>
								<div class='group'>
									<h2>
										<?php _e('Email Settings','formcraft_basic'); ?>
									</h2>
									<div>
										<span class='option-label'><?php _e('From Email','formcraft_basic'); ?></span>
										<input type="text" placeholder="john@example.com" ng-model='Builder.Config.Email.email_from' style='width: 300px'>
										<span class='tooltip-cover'><i data-html='true' class='icon-help tooltip-icon' data-toggle='tooltip' title='This will set the From Email address for the email notifications.<br>You can use an email here, like joe@example.com, or you can populate this with a form value, using the field ID:<br><strong>[field2]</strong>'></i></span>
									</div>
									<div>
										<span class='option-label'><?php _e('From Name','formcraft_basic'); ?></span>
										<input type="text" placeholder="John Doe" ng-model='Builder.Config.Email.name_from' style='width: 300px'>
										<span class='tooltip-cover'><i data-html='true' class='icon-help tooltip-icon' data-toggle='tooltip' title='This will set the From Name address for the email notifications.<bR>You can use a name here, like John Doe, or you can populate this with a form value, using the field ID:<br><strong>[field3]</strong>'></i></span>
									</div>
									<div>
										<span class='option-label'><?php _e('Email Subject','formcraft_basic'); ?></span>
										<input type="text" placeholder="New Form Submission" ng-model='Builder.Config.Email.subject' style='width: 300px'>
										<span class='tooltip-cover'><i data-html='true' class='icon-help tooltip-icon' data-toggle='tooltip' title='You can use form values here, using their field ID: <strong>[field1]</strong>'></i></span>
									</div>
								</div>
							</div>
							<div>
								<div class='group'>
									<h2>
										<?php _e('Dedicated Form Page','formcraft_basic'); ?>
									</h2>
									<div>
										<span class='option-label'><?php _e('Form Link','formcraft_basic'); ?></span>
										<textarea onclick='select()' style='width: 350px' rows='1' class='copy-code' readonly><?php echo get_site_url().'/form/'.$form_id; ?></textarea>
									</div>
									<div>
										<span class='option-label'><?php _e('Public Access','formcraft_basic'); ?></span>
										<label><input type='checkbox' ng-model='Builder.Config.disable_form_link'/> Disable</label>
									</div>
								</div>
								<div class='group'>
									<h2>
										<?php _e('Using Shortcode','formcraft_basic'); ?>
									</h2>
									<div>
										<span class='option-label'><?php _e('Form Alignment','formcraft_basic'); ?></span>
										<label><input type='radio' ng-model='temp_align' name='temp_form_align' value='left'><?php _e('Left','formcraft_basic'); ?></label>&nbsp;
										<label><input type='radio' ng-model='temp_align' name='temp_form_align' value='center'><?php _e('Center','formcraft_basic'); ?></label>&nbsp;
										<label><input type='radio' ng-model='temp_align' name='temp_form_align' value='right'><?php _e('Right','formcraft_basic'); ?></label>
									</div>
									<div>
										<span class='option-label'><?php _e('Shortcode','formcraft_basic'); ?></span>
										<textarea onclick='select()' style='width: 350px' rows='1' class='copy-code' readonly>[fcb id='<?php echo $form_id; ?>' align='{{temp_align}}'][/fcb]</textarea>
									</div>
								</div>
							</div>
							<div>
								<div class='group'>
									<h2>
										<?php _e('Export Form','formcraft_basic'); ?>
									</h2>
									<div>
										<a target='_blank' href='<?php echo get_site_url(); ?>?formcraft_export_form=<?php echo $form_id; ?>' class='button'><?php _e('Export Form File','formcraft_basic'); ?></a>
										<p class='description'>
											You can import this form template on any other WordPress site with the plugin installed.
										</p>
									</div>
								</div>
								<div class='group'>
									<h2>
										<?php _e('Custom Messages','formcraft_basic'); ?>
									</h2>
									<div>
										<span class='option-label'><?php _e('Form Sent','formcraft_basic'); ?></span>
										<input type='text' ng-model='Builder.Config.messages.form_sent' style="width: 365px">
									</div>
									<div>
										<span class='option-label'><?php _e('Validation Errors','formcraft_basic'); ?></span>
										<input type='text' ng-model='Builder.Config.messages.form_errors' style="width: 365px">
									</div>
								</div>
							</div>
						</div>
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
</div>
<?php

?>