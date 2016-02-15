var lastChecked = null;
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

function getSubmissions(page, form)
{
	form = (typeof form === "undefined") ? 0 : form;
	jQuery('.subs_list .loader').show();
	jQuery('.subs_list').removeClass('no-subs');
	jQuery.ajax( {
		url: FCB_1.ajaxurl,
		type: "POST",
		context: jQuery(this),
		data: 'action=formcraft_basic_get_submissions&page='+page+'&form='+form,
		dataType: "json"
	} )
	.done(function(response) {
		jQuery('.subs_list .tbody').html('');
		jQuery('.subs_list .loader').hide();
		if (response.total)
		{
			jQuery('#total-submissions').text("("+response.total+")");
		}
		for (line in response.submissions)
		{
			var new_line = '';
			var new_line = new_line + "<div class='tr'>";
			var new_line = new_line + "<span style='width:8.5%'><label><input value='"+response.submissions[line].id+"' class='subs_checked' name='subs_checked' type='checkbox'></label></span>";
			var new_line = new_line + "<span style='width:61.5%'><a class='load-submission' data-toggle='fcbmodal' data-target='#submission_modal' data-id='"+response.submissions[line].id+"'>"+response.submissions[line].form_name+"</a></span>";
			var new_line = new_line + "<span style='width:30%'><a class='load-submission' data-toggle='fcbmodal' data-target='#submission_modal' data-id='"+response.submissions[line].id+"'>"+response.submissions[line].created+"</a></span>";
			var new_line = new_line + "</div>";
			jQuery('.subs_list .tbody').append(new_line);
		}
		var i = 1;
		jQuery('.subs_list .pagination').html('');
		while (i <= response.pages) {
			var add_class = i==page ? 'active' : '';
			jQuery('.subs_list .pagination').append('<span class="'+add_class+'">'+i+'</span>');
			i++;
		}
		if(response.pages==0)
		{
			jQuery('.subs_list').addClass('no-subs');
		}
	})
.fail(function(response) {
	jQuery(this).find('.response').text('Connection error');
})
.always(function(response) {
	jQuery(this).find('button,[type="submit"]').removeAttr('disabled');
	jQuery(this).find('.fcb-spinner').hide();
});
}

jQuery(document).ready(function(){
	jQuery('body').on('click','.subs_list .pagination > span',function(){
		getSubmissions(jQuery(this).text(),jQuery('#which-form').val());
	});
	if (jQuery('.pagination').length)
	{
		jQuery('.subs_list .pagination > span').eq(0).trigger('click');	
	}
	jQuery('#import_form_input').fileupload({
		dataType: 'json',
		add: function(e, data){
			jQuery(this).attr('disabled','disabled');
			var parent = jQuery(this).parent().parent();
			parent.find('.filename').html('<span class="fcb-spinner small"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></span>');
			window.jqXHR = data.submit();
		},
		done: function(e, data){
			jQuery(this).removeAttr('disabled');
			var response = data.result;
			if (response.success)
			{
				if (response.debug)
				{
					toastr["success"]("<i class='icon-ok'></i> "+response.debug);
				}
				jQuery(this).attr('data-file',response.success);
				var parent = jQuery(this).parent().parent();
				parent.find('.filename').html('<i class="icon-ok"></i>');
			}
			else if (response.failed)
			{
				var parent = jQuery(this).parent().parent();
				parent.find('.filename').html('');
				toastr["error"](response.failed);
			}
			else
			{
				var parent = jQuery(this).parent().parent();
				parent.find('.filename').html('');
				toastr["error"]("Unknown Error");
			}
		}
	});
	jQuery('[data-target="#new_form_modal"]').click(function(){
		jQuery('#form_name').focus();
	});
	jQuery('body').on('click','.load-submission',function(){
		var id = jQuery(this).attr('data-id');
		jQuery('#submission_modal .fcbmodal-body').html('loading...');
		jQuery.ajax( {
			url: FCB_1.ajaxurl,
			type: "GET",
			context: jQuery(this),
			data: 'action=formcraft_basic_get_submission_content&id='+id,
			dataType: "json"
		} )
		.done(function(response) {
			jQuery('#submission_modal .fcbmodal-title').text(response[0].form_name);
			var html = '';
			for (field in response[0].content)
			{
				if (typeof response[0].content[field].value=='object')
				{
					response[0].content[field].value = response[0].content[field].value.join(", ");
				}
				html = html + "<div><span class='label'>"+response[0].content[field].label+"</span><span class='value'>"+response[0].content[field].value+"</span></div>";
			}
			jQuery('#submission_modal .fcbmodal-body').html(html);

		})
		.fail(function(response) {
			toastr["error"]("Connection Error");
		});
	});
	jQuery('body').on('click','.trash-form',function(event){
		event.preventDefault();
		var r = confirm(FCB_1.confirm_delete);
		if (r == false) {
			return false;
		}
		var form = jQuery(this).attr('data-id');
		jQuery(this).css('opacity',.2);
		jQuery.ajax( {
			url: FCB_1.ajaxurl,
			type: "GET",
			context: jQuery(this),
			data: 'action=formcraft_basic_del_form&form='+form,
			dataType: "json"
		} )
		.done(function(response) {
			if (response.failed)
			{
				toastr["error"](response.failed);
			}
			else if(response.success)
			{
				jQuery('.form_list .form-'+response.form_id).slideUp();
				toastr["success"]("<i class='icon-ok'></i> "+response.success);
			}
		})
		.fail(function(response) {
			jQuery(this).find('.response').text('Connection error');
		})
		.always(function(response) {
			jQuery(this).find('button,[type="submit"]').removeAttr('disabled');
			jQuery(this).find('.fcb-spinner').hide();
		});
	});
	jQuery('body').on('click','#trash-subs',function(){
		jQuery('.subs_list .loader').show();
		list = [];
		jQuery('.subs_checked:checked').each(function(){
			list.push(jQuery(this).val());
		});
		jQuery.ajax( {
			url: FCB_1.ajaxurl,
			type: "GET",
			context: jQuery(this),
			data: 'action=formcraft_basic_del_submissions&list='+list,
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
				jQuery('.subs_checked_parent').prop('checked', false).trigger('change');
				getSubmissions(1,jQuery('#which-form').val());
			}
		})
		.fail(function(response) {
			toastr["error"]("Connection Error");
		});
	});
	jQuery('body').on('change','#which-form',function(){
		getSubmissions(1,jQuery(this).val());
	});
	jQuery('[data-toggle="tooltip"]').tooltip();
	jQuery('input:file').change(function (){
		var fileName = jQuery(this).val();
		var fileName = fileName.replace(/^.*[\\\/]/, '')
		jQuery(this).parent().parent().find('.filename').text(fileName);
	});
	jQuery('body').on('click','.subs_checked',function(e){
		var checkbox = jQuery('.subs_checked');
		if(!lastChecked) {
			lastChecked = this;
			return;
		}
		if(e.shiftKey) {
			var start = checkbox.index(this);
			var end = checkbox.index(lastChecked);
			checkbox.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked).trigger('change');
		}
		lastChecked = this;
	});
	jQuery('body').on('change','.subs_checked',function(event){
		var len = jQuery('.subs_checked:checked').length;
		if (len==0)
		{
			jQuery('.subs_cover').removeClass('show_options');
			jQuery('.subs_checked_parent').prop('checked', false).trigger('change');
		}
		else
		{
			jQuery('.subs_cover').addClass('show_options');
		}
		if (len==jQuery('.subs_checked').length)
		{
			jQuery('.subs_checked_parent').prop('checked', true).trigger('change');
		}
	});
	jQuery('body').on('change','.subs_checked_parent',function(event){
		if (jQuery(this).is(':checked'))
		{
			jQuery('.subs_checked').each(function(){
				if (!jQuery(this).is(':checked')) {
					jQuery(this).prop('checked', true).trigger('change');
				}
			});
		}
		else
		{
			jQuery('.subs_checked').each(function(){
				if (jQuery(this).is(':checked')) {
					jQuery(this).prop('checked', false).trigger('change');
				}
			});
		}
	});
	jQuery('body').on('submit','#new_form',function(event){
		event.preventDefault();
		var data = jQuery(this).serialize();
		if (jQuery('#import_form_input').attr('data-file'))
		{
			var data = data + '&file='+jQuery('#import_form_input').attr('data-file');
		}
		jQuery(this).find('button,[type="submit"]').attr('disabled','disabled').addClass('fcb-disabled');
		jQuery(this).find('.response').text('').hide();
		jQuery.ajax( {
			url: FCB_1.ajaxurl,
			type: "POST",
			cache: false,
			context: jQuery(this),
			data: 'action=formcraft_basic_new_form&'+data,
			dataType: "json"
		} )
		.done(function(response) {
			if (response.failed)
			{
				jQuery(this).find('.response').text(response.failed).show();
			}
			else if(response.success)
			{
				jQuery(this).find('.response').text(response.success).show();
			}
			if (response.redirect)
			{
				window.location = window.location.href+response.redirect;
			}
		})
		.fail(function(response) {
			jQuery(this).find('.response').text('Connection error').show();
			jQuery(this).find('button,[type="submit"]').removeAttr('disabled').removeClass('fcb-disabled');
		})
		.always(function(response) {
			jQuery(this).find('button,[type="submit"]').removeAttr('disabled').removeClass('fcb-disabled');
		});
	});
});