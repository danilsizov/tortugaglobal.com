window.dragged_location = null;

function allowDrop(event) {
	event.preventDefault();
}

function dragStart(event)
{
	jQuery('#form-cover-html .fcb_form').css('height',jQuery('#form-cover-html .fcb_form').outerHeight());
}

function setHeight(event)
{
	var height = Math.max(jQuery(event.target).parent().outerHeight(),0);
	if (height>0)
	{
		window.dndPlaceholderHeight = height;
	}
	jQuery('.fcb_form .dndPlaceholder').css('height',window.dndPlaceholderHeight+'px');
	if ( jQuery('.fcb_form .dndPlaceholder').length!=0 )
	{
		if (jQuery(event.target).parent().hasClass('hide-it')==false)
		{
			jQuery(event.target).parent().addClass('hide-it');
		}
	}
	else
	{
		jQuery(event.target).parent().removeClass('hide-it');
	}
}

function drag(event) {
	window.dnd_active = true;
	window.dragged = jQuery(event.target);
}

function dragEnd(event)
{
	window.dnd_active = false;
}

function drop(event) {
	jQuery('#form-cover-html .fcb_form').css('height','auto');
	window.dndPlaceholderHeight = 0;
	jQuery('.fcb_form .dndPlaceholder').css('height','0px');
	if (window.dnd_active==true)
	{
		jQuery('.fcb_form .hide-it').removeClass('hide-it');
		window.dragged_location = jQuery('.fcb_form .dndPlaceholder').index();
		jQuery('.fcb_form .dndPlaceholder').remove();
		window.dragged.trigger('click');
		event.preventDefault();
	}
}

jQuery(document).ready(function(){
	jQuery('[data-toggle="tooltip"]').tooltip();
	jQuery('.color-picker').wpColorPicker({
		width: 237,
		change: function(event, ui) {
			jQuery(this).val(ui.color.toString()).trigger('change');
		}
	});
	jQuery('body').on('click','.nav-tabs > span',function(){
		var selector = jQuery(this).parent().attr('data-content');
		jQuery(this).parent().find('> span').removeClass('active');
		jQuery(this).addClass('active');
		jQuery(selector).find(' > div').removeClass('active');
		jQuery(selector).find(' > div').eq(jQuery(this).index()).addClass('active');
	});
});


jQuery(document).mouseup(function (e)
{

	/* Minimize the form options and Add Fields if clicked inside the form cover, but outside the form itself */
	var containerOuter = jQuery('.form-cover-builder');
	if (containerOuter.is(e.target)
		|| containerOuter.has(e.target).length !== 0)
	{
		var containerInner = jQuery('.fcb_form');
		if (!containerInner.is(e.target)
			&& containerInner.has(e.target).length === 0)
		{
			if (jQuery('.icon-true').length)
			{
				jQuery('.add_fields').find('> button.blue').trigger('click');
			}
			jQuery('.options-true .form-element-html').trigger('click');
		}
	}
});
toastr.options = {
	"closeButton": false,
	"debug": false,
	"newestOnTop": true,
	"progressBar": false,
	"positionClass": "toast-top-right",
	"preventDuplicates": false,
	"onclick": null,
	"showDuration": "300",
	"hideDuration": "300",
	"timeOut": "3000",
	"extendedTimeOut": "300",
	"showEasing": "linear",
	"hideEasing": "linear",
	"showMethod": "slideDown",
	"hideMethod": "slideUp"
}
function saveFormJQuery(builder)
{
	var meta_builder = {};
	meta_builder.fields = [];
	meta_builder.config = builder.Config;

	for (var i = 0; i < builder.FormElements.length; i++) {
		meta_builder.fields.push({
			identifier: builder.FormElements[i].elementDefaults.identifier,
			type: builder.FormElements[i].type,
			elementDefaults: builder.FormElements[i].elementDefaults
		})
	};
	var meta_builder = encodeURIComponent(angular.toJson(meta_builder));
	var builder = encodeURIComponent(deflate(angular.toJson(builder)));
	var html = encodeURIComponent(jQuery('#form-cover-html').html().trim());
	var data = 'builder='+builder+'&id='+jQuery('#form_id').val()+'&html='+html+'&meta_builder='+meta_builder;
	jQuery('#form_save_button').attr('disabled','disabled');
	jQuery('#form_save_button').addClass('saving');
	jQuery.ajax( {
		url: FCB.ajaxurl,
		type: "POST",
		context: jQuery(this),
		data: 'action=formcraft_basic_form_save&'+data,
		dataType: "json"
	} )
	.done(function(response) {
		if (response.failed)
		{
			toastr["error"](response.failed);
		}
		else if(response.success)
		{
			toastr["success"]("<i class='icon-ok'></i> "+response.success);
		}
		else
		{
			toastr["error"]('Failed Saving. Unknown Error.');
		}
	})
	.fail(function(response) {
		toastr["error"]('Failed Saving');
	})
	.always(function(response) {
		jQuery('#form_save_button').removeClass('saving');
		jQuery('#form_save_button').removeAttr('disabled');
	});
}

angular.module('FormCraft', ['ngAnimate','dndLists'])
.directive('compile', function($compile) {
	return function(scope, element, attrs) {
		scope.$watch(
			function(scope) {
				return scope.$eval(attrs.compile);
			},
			function(value) {
				element.html(value);
				$compile(element.contents())(scope);
			}
			);
	};
})
.directive('checkboxList', function() {
	return {
		require: 'ngModel',
		link: function($scope, $element, $attrs, ngModelCtrl) {
			$scope.$watch($attrs.ngModel, function(){
				if (ngModelCtrl.$viewValue)
				{				
					var temp = ngModelCtrl.$viewValue.split('\n');
					$scope.element.elementDefaults.options_list_show=[];
					for (x in temp)
					{
						if (temp[x].indexOf('==')!=-1)
						{
							var temp2 = temp[x].split('==');
							$scope.element.elementDefaults.options_list_show.push({
								value:  temp2[0],
								show: temp2[1]
							});	
						}
						else
						{
							$scope.element.elementDefaults.options_list_show.push({
								value:  temp[x],
								show: temp[x]
							});	
						}
					}
				}
			});
		}

	}
})
.directive('updateLabel', function() {
	return {
		require: 'ngModel',
		link: function($scope, $element, $attrs, ngModelCtrl) {
			$scope.$watch($attrs.ngModel, function(){
				if ($element[0].checked)
				{
					$element.parent().addClass('active');
				}
				else
				{
					$element.parent().removeClass('active');
				}
			});
		}

	}
})
.directive('angularColor', function() {
	return {
		require: 'ngModel',
		link: function($scope, $element, $attrs, ngModelCtrl) {
			$scope.$watch($attrs.ngModel, function(){
				$element.wpColorPicker({
					width: 237,
					change: function(event, ui) {
						jQuery(this).val(ui.color.toString()).trigger('change');
					}
				});
			});
		}

	}
})
.directive('tooltip', function() {
	return {
		link: function($scope, $element, $attrs, ngModelCtrl) {
			$element.tooltip({html:true});
		}

	}
})
.directive('datepicker', function() {
	return {
		restrict: 'A',
		require: 'ngModel',
		link: function($scope, $element, $attrs, ngModelCtrl) {
			$attrs.$observe(
				"dateFormat",
				function innerObserveFunction() {
					$element.datepicker( "option", "dateFormat", $attrs.dateFormat );
					$element.trigger('change');
				}
				);
			$scope.$watch($attrs.ngModel, function(){
				var date = jQuery.datepicker.formatDate( "yy/mm/dd", $element.datepicker( "getDate" ) );
				if($attrs.ngModel=='element.elementDefaults.minDate')
				{
					$scope.element.elementDefaults.minDateAlt = date;
				}
				if($attrs.ngModel=='element.elementDefaults.maxDate')
				{
					$scope.element.elementDefaults.maxDateAlt = date;
				}
			});
			$attrs.$observe(
				"dateLang",
				function innerObserveFunction() {
					if ($attrs.dateLang!='en')
					{
						$element.datepicker( "option", "dateFormat", $attrs.dateFormat );
						$element.datepicker( "option", "altFormat", 'yy-mm-dd' );
						//jQuery.getScript(FCB.datepickerLang+'datepicker-'+$attrs.dateLang+'.js');
					}
				}
				);
			$attrs.$observe(
				"defaultDate",
				function innerObserveFunction() {
					$element.datepicker( "option", "dateFormat", $attrs.dateFormat );
					$element.datepicker( "option", "altFormat", 'yy-mm-dd' );
					$element.datepicker( "setDate", $attrs.defaultDate );
				}
				);
			$attrs.$observe(
				"dateMin",
				function innerObserveFunction() {
					$element.datepicker( "option", "dateFormat", $attrs.dateFormat );
					$element.datepicker( "option", "altFormat", 'yy-mm-dd' );
					$element.datepicker( "option", "minDate", $attrs.dateMin );
				}
				);
			$attrs.$observe(
				"dateMax",
				function innerObserveFunction() {
					$element.datepicker( "option", "dateFormat", $attrs.dateFormat );
					$element.datepicker( "option", "altFormat", 'yy-mm-dd' );
					$element.datepicker( "option", "maxDate", $attrs.dateMax );
				}
				);
			options = {};
			options.beforeShow = function(input, inst) {
				jQuery('#ui-datepicker-div').removeClass('ui-datepicker').addClass('fcb-datepicker');
			}
			options.onSelect = function(input, inst) {
				jQuery(this).trigger('change');
			}
			options.nextText = '❯';
			options.prevText = '❮';
			options.hideIfNoPrevNext = true;
			options.changeYear = true;
			options.changeMonth = true;
			options.showAnim = false;
			options.yearRange = "c-20:c+20";
			$element.datepicker(options);
		}

	}
})
.controller('FormController', function($scope, $locale, $http) {

	$scope.FormElements = function() {
		$http.get(FCB.ajaxurl+'?action=formcraft_form_data&type=builder&id='+jQuery('#form_id').val()).success(function(response){
			if (response.trim()=='')
			{
				$scope.Builder = {};
				$scope.Builder.Config = {};
				$scope.Builder.Config.messages = {};
				$scope.Builder.FormElements = [];
				$scope.Builder.Options = {};
				$scope.Options = {};
			}
			else
			{
				$scope.Builder = jQuery.evalJSON(inflate(decodeURIComponent(response.trim())));
			}
			$scope.Builder.form_width = $scope.Builder.form_width==undefined ? '420px' : $scope.Builder.form_width;
			$scope.Builder.form_frame = $scope.Builder.form_frame==undefined ? 'visible' : $scope.Builder.form_frame;
			$scope.Builder.font_size = $scope.Builder.font_size==undefined ? 100 : $scope.Builder.font_size;
			$scope.Builder.font_color = $scope.Builder.font_color==undefined ? '#666666' : $scope.Builder.font_color;
			$scope.Builder.Config.messages.form_sent = $scope.Builder.Config.messages.form_sent==undefined ? 'Message Sent' : $scope.Builder.Config.messages.form_sent;
			$scope.Builder.Config.messages.form_errors = $scope.Builder.Config.messages.form_errors==undefined ? 'Please correct the errors and try again' : $scope.Builder.Config.messages.form_errors;
			jQuery('.form-cover-builder').removeClass('hide-form');
			$scope.$watch('Builder.form_width', function(newValue, oldValue) {
				$scope.Builder.form_width_nos = parseInt($scope.Builder.form_width)+760;
			});

		});
}
$scope.temp_align = 'left';
$scope.FormElements();
$scope.saveForm = function(followup){
	if (followup=='preview')
	{
		if (typeof previewForm=='undefined')
		{
			previewForm = window.open(FCB.baseurl+'/form/'+FCB.form_id+'?preview=true', 'myWindow');
		}
		else
		{
			previewForm = window.open(FCB.baseurl+'/form/'+FCB.form_id+'?preview=true', 'myWindow');
			if (previewForm.document.getElementById('form-cover')!=null)
			{
				previewForm.document.getElementById('form-cover').innerHTML = "<span class='fcb-spinner form-spinner small' style='display: block; margin: 0 auto'><div class='bounce1'></div><div class='bounce2'></div><div class='bounce3'></div></span>";
			}
		}
		previewForm.location = FCB.baseurl+'/form/'+FCB.form_id+'?preview=true', 'myWindow';
	}
	saveFormJQuery($scope.Builder);
}

$scope.removeFormElement = function ($index)
{
	$scope.Builder.FormElements.splice($index, 1);
}
$scope.duplicateFormElement = function ($index)
{
	var len = $scope.Builder.FormElements.length;
	$scope.Builder.FormElements[len] = angular.copy($scope.Builder.FormElements[$index]);
	$scope.Builder.elements_counter = $scope.Builder.elements_counter + 1;
	$scope.Builder.FormElements[len].elementDefaults.identifier = 'field'+$scope.Builder.elements_counter;
}

$scope.commonFieldTemplate = "<label class='w-3'><input type='checkbox' ng-model='element.elementDefaults.required'> Required Field</label>";
$scope.fieldHTMLTemplate = {};
$scope.fieldOptionTemplate = {};

$scope.dateLang = ['af','ar-DZ','ar','az','be','bg','bs','ca','cs','cy-GB','da','de','el','en-AU','en-GB','en-NZ','eo','es','et','eu','fa','fi','fo','fr-CA','fr-CH','fr','gl','he','hi','hr','hu','hy','id','is','it-CH','it','ja','ka','kk','km','ko','ky','lb','lt','lv','mk','ml','ms','nb','nl-BE','nl','nn','no','pl','pt-BR','pt','rm','ro','ru','sk','sl','sq','sr-SR','sr','sv','ta','th','tj','tr','uk','vi','zh-CN','zh-HK','zh-TW'];

$scope.fieldHTMLTemplate.oneLineText = "<div class='oneLineText-cover field-cover'><span compile='element.elementDefaults.main_label'></span><div ng-click='$event.stopPropagation()'><input type='text' name='{{element.elementDefaults.identifier}}' data-min-char='{{element.elementDefaults.Validation.minChar}}' data-max-char='{{element.elementDefaults.Validation.maxChar}}' data-val-type='{{element.elementDefaults.Validation.allowed}}' data-is-required='{{element.elementDefaults.required}}' data-allow-spaces='{{element.elementDefaults.Validation.spaces}}' class='validation-lenient'></div></div>";
$scope.fieldOptionTemplate.oneLineText = "<label class='w-2'><span>Label</span><input type='text' ng-model='element.elementDefaults.main_label'></label><label class='w-1'><span>Width</span><input type='text' ng-model='element.elementDefaults.field_width'></label><label class='w-3'><span>Validation</span><select ng-model='element.elementDefaults.Validation.allowed'><option value=''>None</option><option value='alphabets'>Only Alphabets</option><option value='numbers'>Only Numbers</option><option value='alphanumeric'>Only Alphabets & Numbers</option></select></label><label class='w2-1'><span>Min Chars</span><input type='text' ng-model='element.elementDefaults.Validation.minChar'></label><label class='w2-1'><span>Max Chars</span><input type='text' ng-model='element.elementDefaults.Validation.maxChar'></label><label class='w-3'><input type='checkbox' ng-model='element.elementDefaults.Validation.spaces'> Allow Spaces</label><div compile='commonFieldTemplate'></div>";

$scope.fieldHTMLTemplate.email = "<div class='email-cover field-cover'><span compile='element.elementDefaults.main_label'></span><div ng-click='$event.stopPropagation()'><input type='text' data-val-type='email' data-is-required='{{element.elementDefaults.required}}' name='{{element.elementDefaults.identifier}}' class='validation-lenient'></div></div>";
$scope.fieldOptionTemplate.email = "<label class='w-2'><span>Label</span><input type='text' ng-model='element.elementDefaults.main_label'></label><label class='w-1'><span>Width</span><input type='text' ng-model='element.elementDefaults.field_width'></label><div compile='commonFieldTemplate'></div>";

$scope.fieldHTMLTemplate.textarea = "<div class='textarea-cover field-cover'><span compile='element.elementDefaults.main_label'></span><div ng-click='$event.stopPropagation()'><textarea class='validation-lenient' name='{{element.elementDefaults.identifier}}' value='' rows='{{element.elementDefaults.field_height}}' data-min-char='{{element.elementDefaults.Validation.minChar}}' data-max-char='{{element.elementDefaults.Validation.maxChar}}' data-is-required='{{element.elementDefaults.required}}'></textarea></div></div>";
$scope.fieldOptionTemplate.textarea = "<label class='w-2'><span>Label</span><input type='text' ng-model='element.elementDefaults.main_label'></label><label class='w-1'><span>Width</span><input type='text' ng-model='element.elementDefaults.field_width'></label><label class='w-1'><span>Rows</span><input type='text' ng-model='element.elementDefaults.field_height'></label><label class='w-1'><span>Min Chars</span><input type='text' ng-model='element.elementDefaults.Validation.minChar'></label><label class='w-1'><span>Max Chars</span><input type='text' ng-model='element.elementDefaults.Validation.maxChar'></label><div compile='commonFieldTemplate'></div>";

$scope.fieldHTMLTemplate.checkbox = "<div class='checkbox-cover field-cover'><span compile='element.elementDefaults.main_label'></span><div ng-click='$event.stopPropagation()'><label ng-repeat='opt in element.elementDefaults.options_list_show'><input type='{{element.elementDefaults.allow_multiple}}' data-is-required='{{element.elementDefaults.required}}' name='{{element.elementDefaults.identifier}}[]' value='{{opt.value}}' class='validation-lenient'><span compile='opt.show'></span></label></div></div>";
$scope.fieldOptionTemplate.checkbox = "<label class='w-2'><span>Label</span><input type='text' ng-model='element.elementDefaults.main_label'></label><label class='w-1'><span>Width</span><input type='text' ng-model='element.elementDefaults.field_width'></label><label class='w-3'><i class='icon-help' tooltip data-toggle='tooltip' title='You can set the value of the checkbox different from the text, using this pattern: <br><strong>100==Apple</strong><br>Here, <strong>100</strong> would be the value, and <strong>Apple</strong> would be the text.'></i><span>Options</span><textarea rows='5' ng-model='element.elementDefaults.options_list' checkbox-list></textarea></label><label class='w-3'><input type='checkbox' ng-model='element.elementDefaults.allow_multiple' ng-true-value='"+'"checkbox"'+"' ng-false-value='"+'"radio"'+"'> Allow Multiple Selections</label><div compile='commonFieldTemplate'></div>";

$scope.fieldHTMLTemplate.dropdown = "<div class='dropdown-cover field-cover'><span compile='element.elementDefaults.main_label'></span><div ng-click='$event.stopPropagation()'><select data-is-required='{{element.elementDefaults.required}}' class='validation-lenient' name='{{element.elementDefaults.identifier}}'><option value='{{opt.value}}' ng-repeat='opt in element.elementDefaults.options_list_show'>{{opt.show}}</option></select></div></div>";
$scope.fieldOptionTemplate.dropdown = "<label class='w-2'><span>Label</span><input type='text' ng-model='element.elementDefaults.main_label'></label><label class='w-1'><span>Width</span><input type='text' ng-model='element.elementDefaults.field_width'></label><label class='w-3'><i class='icon-help' tooltip data-toggle='tooltip' title='You can set the value of the dropdown options different from the text, using this pattern: <br><strong>en==English</strong><br>Here, <strong>en</strong> would be the value, and <strong>English</strong> would be the text.'></i><span>Options</span><textarea rows='5' ng-model='element.elementDefaults.options_list' checkbox-list></textarea></label><div compile='commonFieldTemplate'></div>";

$scope.fieldHTMLTemplate.datepicker = "<div class='datepicker-cover field-cover'><span compile='element.elementDefaults.main_label'></span><div ng-click='$event.stopPropagation()'><input type='text' class='validation-lenient' data-is-required='{{element.elementDefaults.required}}' datepicker data-date-format='{{element.elementDefaults.dateFormat}}' data-date-min='{{element.elementDefaults.minDate}}' data-date-min-alt='{{element.elementDefaults.minDateAlt}}' data-date-max-alt='{{element.elementDefaults.maxDateAlt}}' data-date-max='{{element.elementDefaults.maxDate}}' data-date-lang='{{element.elementDefaults.dateLang}}' name='{{element.elementDefaults.identifier}}' ng-model='temp'></div></div>";
$scope.fieldOptionTemplate.datepicker = "<label class='w-2'><span>Label</span><input type='text' ng-model='element.elementDefaults.main_label'></label><label class='w-1'><span>Width</span><input type='text' ng-model='element.elementDefaults.field_width'></label><label class='w2-1'><span>Lang</span><select ng-model='element.elementDefaults.dateLang'><option value='en'>English</option><option ng-repeat='lang in dateLang' ng-value='lang'>{{lang}}</option></select></label><label class='w2-1'><span>Format</span><select ng-model='element.elementDefaults.dateFormat'><option>M d, yy</option><option>d M yy</option><option>yy-mm-dd</option><option>dd/mm/yy</option><option>mm/dd/yy</option></select></label><label class='w2-1'><span>Min Date</span><input data-default-date='{{element.elementDefaults.minDate}}' type='text' data-date-format='{{element.elementDefaults.dateFormat}}' datepicker ng-model='element.elementDefaults.minDate'></label><label class='w2-1'><span>Max Date</span><input data-default-date='{{element.elementDefaults.maxDate}}' type='text' data-date-format='{{element.elementDefaults.dateFormat}}' datepicker ng-model='element.elementDefaults.maxDate' data-date-min='{{element.elementDefaults.minDate}}'></label><div compile='commonFieldTemplate'></div>";

$scope.fieldHTMLTemplate.customText = "<div class='customText-cover field-cover'><div class='full' compile='element.elementDefaults.html' style='text-align: {{element.elementDefaults.alignment}}; color: {{element.elementDefaults.font_color}}'></div></div>";
$scope.fieldOptionTemplate.customText = "<label class='w-3'><span>Text Content</span><textarea rows='5' ng-model='element.elementDefaults.html'></textarea></label><label class='w2-1'><span>Width</span><input type='text' ng-model='element.elementDefaults.field_width'></label><div class='label w-3 hide-checkbox align-icons'><p>Text Alignment</p><label><input type='radio' update-label name='{{element.elementDefaults.identifier}}_name' ng-model='element.elementDefaults.alignment' value='left'><i class='icon-align-left'></i></label><label><input type='radio' update-label name='{{element.elementDefaults.identifier}}_name' ng-model='element.elementDefaults.alignment' value='center'><i class='icon-align-center'></i></label><label><input type='radio' update-label name='{{element.elementDefaults.identifier}}_name' ng-model='element.elementDefaults.alignment' value='right'><i class='icon-align-right'></i></label></div><div class='w-3'><p>Font Color</p><input angular-color type='text' value='#fff' class='color-picker' ng-model='element.elementDefaults.font_color'></div>";

$scope.fieldHTMLTemplate.submit = "<div class='wide-{{element.elementDefaults.isWide}} submit-cover field-cover' style='text-align: {{element.elementDefaults.alignment}}'><button style='background-color: {{element.elementDefaults.button_color}};border-color: {{element.elementDefaults.border_color}}; color: {{element.elementDefaults.font_color}}' type='submit' class='button submit-button'><span class='text'>{{element.elementDefaults.main_label}}</span><span class='spin-cover'><i style='color: {{element.elementDefaults.font_color}}' class='icon-spin5 animate-spin'></i></span></button></div><div class='submit-response'></div><input type='text' class='required_field' name='website'>";
$scope.fieldOptionTemplate.submit = "<label class='w-2'><span>Label</span><input type='text' ng-model='element.elementDefaults.main_label'></label><label class='w-1'><span>Width</span><input type='text' ng-model='element.elementDefaults.field_width'></label><div class='label w-3 hide-checkbox align-icons'><p>Button Alignment</p><label><input type='radio' update-label name='{{element.elementDefaults.identifier}}_name' ng-model='element.elementDefaults.alignment' value='left'><i class='icon-align-left'></i></label><label><input type='radio' update-label name='{{element.elementDefaults.identifier}}_name' ng-model='element.elementDefaults.alignment' value='center'><i class='icon-align-center'></i></label><label><input type='radio' update-label name='{{element.elementDefaults.identifier}}_name' ng-model='element.elementDefaults.alignment' value='right'><i class='icon-align-right'></i></label></div><div class='w-3'><p>Button Color</p><input angular-color type='text' value='#fff' class='color-picker' ng-model='element.elementDefaults.button_color'></div><div class='w-3'><p>Border Color</p><input angular-color type='text' value='#fff' class='color-picker' ng-model='element.elementDefaults.border_color'></div><div class='w-3'><p>Font Color</p><input angular-color type='text' value='#fff' class='color-picker' ng-model='element.elementDefaults.font_color'></div><label class='w-3'><input type='checkbox' ng-model='element.elementDefaults.isWide'> Wide Button</label>";

$scope.addFormElement = function (type, position) {
	var total = 0;
	total = total + $scope.Builder.FormElements.length;

	$scope.elementTemp = {};
	$scope.elementTemp.field_width = '100%';
	$scope.Builder.elements_counter = $scope.Builder.elements_counter==undefined ? 1 : $scope.Builder.elements_counter + 1;
	var temp_var = $scope.Builder.elements_counter;
	$scope.elementTemp.identifier = 'field'+parseInt(temp_var);
	$scope.elementTemp.required = false;

	switch (type)
	{

		case 'oneLineText':
		$scope.element = "<div compile='fieldHTMLTemplate."+type+"'></div>";
		$scope.elementOptions = "<div compile='fieldOptionTemplate."+type+"'></div>";
		$scope.elementTemp.main_label = 'Your Name';
		break;

		case 'email':
		$scope.element = "<div compile='fieldHTMLTemplate."+type+"'></div>";
		$scope.elementOptions = "<div compile='fieldOptionTemplate.email'></div>";
		$scope.elementTemp.main_label = 'Your Email';
		break;

		case 'textarea':
		$scope.element = "<div compile='fieldHTMLTemplate."+type+"'></div>";
		$scope.elementOptions = "<div compile='fieldOptionTemplate."+type+"'></div>";
		$scope.elementTemp.main_label = 'Comments';
		$scope.elementTemp.field_height = '5';
		break;

		case 'checkbox':
		$scope.element = "<div compile='fieldHTMLTemplate."+type+"'></div>";
		$scope.elementOptions = "<div compile='fieldOptionTemplate."+type+"'></div>";
		$scope.elementTemp.main_label = 'Favorite Fruits';
		$scope.elementTemp.allow_multiple = 'checkbox';
		$scope.elementTemp.options_list = 'Apple\nOrange\nWatermelon';
		break;

		case 'dropdown':
		$scope.element = "<div compile='fieldHTMLTemplate."+type+"'></div>";
		$scope.elementOptions = "<div compile='fieldOptionTemplate."+type+"'></div>";
		$scope.elementTemp.main_label = 'Language';
		$scope.elementTemp.options_list = '==Select An Option\nEnglish\nFrench\nSpanish';
		break;

		case 'datepicker':
		$scope.element = "<div compile='fieldHTMLTemplate."+type+"'></div>";
		$scope.elementOptions = "<div compile='fieldOptionTemplate."+type+"'></div>";
		$scope.elementTemp.main_label = 'Date';
		$scope.elementTemp.dateLang = 'en';
		break;

		case 'customText':
		$scope.element = "<div compile='fieldHTMLTemplate."+type+"'></div>";
		$scope.elementOptions = "<div compile='fieldOptionTemplate."+type+"'></div>";
		$scope.elementTemp.html = 'Add some text or <strong>HTML</strong> here';
		$scope.elementTemp.font_color = '#666666';
		$scope.elementTemp.alignment = 'left';
		break;

		case 'submit':
		$scope.element = "<div compile='fieldHTMLTemplate."+type+"'></div>";
		$scope.elementOptions = "<div compile='fieldOptionTemplate."+type+"'></div>";
		$scope.elementTemp.main_label = 'Submit Form';
		$scope.elementTemp.isWide = false;
		$scope.elementTemp.alignment = 'right';
		$scope.elementTemp.button_color = '#eeeeee';
		$scope.elementTemp.border_color = '#cfcfcf';
		$scope.elementTemp.font_color = '#666666';
		break;

	}
	position = window.dragged_location==null ? $scope.Builder.FormElements.length : window.dragged_location;
	$scope.Builder.FormElements.splice(position,0,{
		element: $scope.element,
		type: type,
		elementOptions: $scope.elementOptions,
		show_options: true,
		elementDefaults: $scope.elementTemp
	});
	$scope.Options.show_fields = false;
	window.dragged_location = null;
	window.dragged = false;
}
});