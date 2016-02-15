function fcb_init(){
	jQuery('.datepicker-cover input[type="text"]').each(function(){
		jQuery(this).removeClass('hasDatepicker');
		options = {};
		options.beforeShow = function(input, inst) {
			jQuery('#ui-datepicker-div').removeClass('ui-datepicker').addClass('fcb-datepicker');
		}
		options.onClose = function (input, inst) {
			jQuery(this).trigger('blur');
		}
		options.onSelect = function(input, inst) {
			jQuery(this).trigger('change');
		}
		if ( jQuery(this).attr('data-date-lang') && jQuery(this).attr('data-date-lang')!='en' )
		{
			jQuery.getScript(FCB.datepickerLang+'datepicker-'+jQuery(this).attr('data-date-lang')+'.js');
		}
		if ( jQuery(this).attr('data-date-format') )
		{
			options.dateFormat = jQuery(this).attr('data-date-format');
		}
		if ( jQuery(this).attr('data-date-min') )
		{
			var minDate = new Date(jQuery(this).attr('data-date-min-alt'));
			options.minDate = minDate;
		}
		if ( jQuery(this).attr('data-date-max') )
		{
			var maxDate = new Date(jQuery(this).attr('data-date-max-alt'));
			options.maxDate = maxDate;
		}
		options.nextText = '❯';
		options.prevText = '❮';
		options.hideIfNoPrevNext = true;
		options.changeYear = true;
		options.changeMonth = true;
		options.showAnim = false;
		options.yearRange = "c-20:c+20";
		options.shortYearCutoff = 50;
		jQuery(this).datepicker(options);
	});
}
jQuery(document).ready(function(){
	fcb_init();
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		jQuery('.datepicker-cover input[type="text"]').attr('readonly','readonly');
	}
	jQuery('body').on('blur change','.fcb_form .validation-lenient',function(){
		if (jQuery(this).fc_validate()==false)
		{
			jQuery(this).addClass('validation-strict').removeClass('validation-lenient');
		}
	});
	jQuery('body').on('keyup change','.fcb_form .validation-strict',function(){
		if (jQuery(this).fc_validate()==false)
		{
			//jQuery(this).addClass('validation-strict').removeClass('validation-lenient');
		}
		else
		{
			//jQuery(this).addClass('validation-lenient').removeClass('validation-strict');
		}
	});
	jQuery('.required_field').hide();
	if (typeof toastr!='undefined')
	{
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
	}
	jQuery('body').on('submit','.fcb_form',function(event){
		event.preventDefault();
		form = jQuery(this);
		form.find('.form-element .error').hide();
		form.find('.submit-response').slideUp('fast').html();
		form.find('.submit-cover').addClass('disabled');
		form.find('.form-element').removeClass('error-field');
		form.find('.submit-button').attr('disabled','disabled');
		var data = form.serialize()+'&id='+form.attr('data-id');
		jQuery.ajax( {
			url: FCB.ajaxurl,
			type: "POST",
			context: form,
			data: 'action=formcraft_basic_form_submit&'+data,
			dataType: "json"
		} )
		.done(function(response) {
			if (response.debug)
			{
				if (response.debug.failed)
				{
					if (typeof toastr!='undefined') { toastr["error"]("Error: "+response.debug.failed); }
				}
				if (response.debug.success)
				{
					if (typeof toastr!='undefined') { toastr["success"]("<i class='icon-ok'></i> "+response.debug.success); }
				}
			}
			if (response.failed)
			{
				form.find('.validation-lenient').addClass('validation-strict').removeClass('.validation-lenient');
				form.find('.submit-response').html("<span class='has-error'>"+response.failed+"</span>").slideDown('fast');
				if (response.errors)
				{
					if (response.errors.errors)
					{
						for (field in response.errors.errors) {
							form.find('.form-element-'+field).addClass('error-field');
							form.find('.form-element-'+field+' .error').text(response.errors.errors[field]).show();
						};
					}
				}
			}
			else if (response.success)
			{
				form.append("<div class='final-success'><i class='icon-ok-circle'></i><span>"+response.success+"</span></div>");
				form.addClass('submitted');
				form.find('.form-element').slideUp(700, function(){
					form.find('.form-element').remove();
				});
			}

		})
.fail(function(response) {
	jQuery(this).find('.response').text('Connection error');
})
.always(function(response) {
	form.find('.submit-cover').addClass('enabled');
	form.find('.submit-cover').removeClass('disabled');
	form.find('.submit-button').removeAttr('disabled');
});
});
jQuery('.form-element-html').removeAttr('ondragstart').removeAttr('dnd-draggable').removeAttr('ondrag').removeAttr('draggable');
jQuery('.fcb_form').removeAttr('ondrop').removeAttr('ondragover');
});