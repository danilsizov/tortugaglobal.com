jQuery(document).ready(function(){

	jQuery('body').on('submit','#fcb_add_form_modal > form',function(event){
		event.preventDefault();
		var form = jQuery('[name="fcb_form_id"]:checked').val();
		var align = jQuery('[name="fcb_form_align"]:checked').val();
		var code = "[fcb id='"+form+"' align='"+align+"'][/fcb]";
		jQuery('#fcb_add_form_modal').fcbmodal('hide');
		tinymce.execCommand('mceInsertContent', 0, code);
	});

});