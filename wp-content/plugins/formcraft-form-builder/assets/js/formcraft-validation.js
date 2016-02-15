(function( $ ) {

	$.fn.fc_validate = function() {

		if(jQuery(this).attr('data-allow-spaces') && jQuery(this).attr('data-allow-spaces')=='true')
		{
			var alphabets = /^[A-Za-z ]+$/;
			var numbers = /^[0-9 ]+$/;
			var alphanumeric = /^[0-9A-Za-z ]+$/;
			var email =/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,6})+$/;
		}
		else
		{
			var alphabets = /^[A-Za-z]+$/;
			var numbers = /^[0-9]+$/;
			var alphanumeric = /^[0-9A-Za-z]+$/;
			var email =/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,6})+$/;
		}

		var value = jQuery(this).val();
		if (jQuery(this).is('[type="checkbox"]')||jQuery(this).is('[type="radio"]'))
		{
			var name = jQuery(this).attr('name');
			var value = jQuery('[name="'+name+'"]:checked').val();
			value = typeof value=='undefined' ? '' : value;
		}
		var this_element = jQuery(this);

		if(jQuery(this).attr('data-is-required') && jQuery(this).attr('data-is-required')=='true' && value.trim()=='')
		{
			this_element.parents('.form-element').find('.error').text(FCB_validation.is_required);
			this_element.parents('.form-element').addClass('error-field');
			return false;
		}
		if(jQuery(this).attr('data-is-required') && jQuery(this).attr('data-is-required')=='false' && value.trim()=='')
		{
			this_element.parents('.form-element').find('.error').text('');
			this_element.parents('.form-element').removeClass('error-field');
			return true;
		}
		if(jQuery(this).attr('data-min-char') && jQuery(this).attr('data-min-char')>value.length)
		{
			this_element.parents('.form-element').find('.error').text(FCB_validation.min_char.replace('[min]',jQuery(this).attr('data-min-char')));
			this_element.parents('.form-element').addClass('error-field');
			return false;
		}
		if(jQuery(this).attr('data-max-char') && jQuery(this).attr('data-max-char')<value.length)
		{
			this_element.parents('.form-element').find('.error').text(FCB_validation.max_char.replace('[max]',jQuery(this).attr('data-max-char')));
			this_element.parents('.form-element').addClass('error-field');
			return false;
		}
		if(jQuery(this).attr('data-val-type') && jQuery(this).attr('data-val-type')=='email' && !value.match(email))
		{
			this_element.parents('.form-element').find('.error').text(FCB_validation.allow_email);
			this_element.parents('.form-element').addClass('error-field');
			return false;
		}
		if(jQuery(this).attr('data-val-type') && jQuery(this).attr('data-val-type')=='alphabets' && !value.match(alphabets))
		{
			this_element.parents('.form-element').find('.error').text(FCB_validation.allow_alphabets);
			this_element.parents('.form-element').addClass('error-field');
			return false;
		}
		if(jQuery(this).attr('data-val-type') && jQuery(this).attr('data-val-type')=='numbers' && !value.match(numbers))
		{
			this_element.parents('.form-element').find('.error').text(FCB_validation.allow_numbers);
			this_element.parents('.form-element').addClass('error-field');
			return false;
		}
		if(jQuery(this).attr('data-val-type') && jQuery(this).attr('data-val-type')=='alphanumeric' && !value.match(alphanumeric))
		{
			this_element.parents('.form-element').find('.error').text(FCB_validation.allow_alphanumeric);
			this_element.parents('.form-element').addClass('error-field');
			return false;
		}
		this_element.parents('.form-element').removeClass('error-field');
		return true;

	};

}( jQuery ));