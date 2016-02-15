$ = jQuery.noConflict();

jQuery(document).ready(function($) {
	$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	postboxes.add_postbox_toggles('fontmeister');

	var FontMeisterFontStack = FontMeisterJS.font_stack;
	FontMeisterFontStack = $.parseJSON(FontMeisterFontStack);
	var FontMeisterFontVariations = {
		n1: 'Thin',
		n2: 'Extra Light',
		n3: 'Light',
		n4: 'Regular',
		n5: 'Medium',
		n6: 'Demi-bold',
		n7: 'Bold',
		n8: 'Heavy',
		n9: 'Black',
		n10: 'Extra Black',
		i1: 'Thin Italic',
		i2: 'Extra Light Italic',
		i3: 'Light Italic',
		i4: 'Regular Italic',
		i5: 'Medium Italic',
		i6: 'Demi-bold Italic',
		i7: 'Bold Italic',
		i8: 'Heavy Italic',
		i9: 'Black Italic',
		i10: 'Extra Black Italic',
		'100': 'Thin',
		'200': 'Extra Light',
		'300': 'Light',
		'400': 'Regular',
		'500': 'Medium',
		'600': 'Demi-bold',
		'700': 'Bold',
		'800': 'Heavy',
		'900': 'Black',
		'1000': 'Extra Black',
		'100italic': 'Thin Italic',
		'200italic': 'Extra Light Italic',
		'300italic': 'Light Italic',
		'400italic': 'Regular Italic',
		'500italic': 'Medium Italic',
		'600italic': 'Demi-bold Italic',
		'700italic': 'Bold Italic',
		'800italic': 'Heavy Italic',
		'900italic': 'Black Italic',
		'1000italic': 'Extra Black Italic'
	}

	$('.fm-group-key').click(function() {
		var clickedId = this.id;
		var showId = clickedId + '-fonts';
		if ($(this).hasClass('fm-group-key-gf')) {
			$('.fm-group-key-for-gf').removeClass('shown').addClass('hidden');
		}
		if ($(this).hasClass('fm-group-key-tk')) {
			$('.fm-group-key-for-tk').removeClass('shown').addClass('hidden');
		}
		if ($(this).hasClass('fm-group-key-fs')) {
			$('.fm-group-key-for-fs').removeClass('shown').addClass('hidden');
		}
		$('#' + showId).removeClass('hidden').addClass('shown');
		return false;
	});

	$(document).on('click', '#fm-font-stack li', function() {
		$(this).addClass('selected').siblings().removeClass('selected');
		var fontFamily = $(this).find('.fm-font-family').text();
		var len = FontMeisterFontStack.length;
		for (var i=0; i<len; i++) {
			var font = FontMeisterFontStack[i];
			if (fontFamily == font.family) {
				var details = "<h2>" + font.family + "</h2>";
				if (font.generic != '') {
					details += "<strong>Generic</strong>: <em>" + font.generic + "</em><br/>";
				}
				if (font.source != '') {
					details += "<strong>From</strong>: <em>" + font.source + "</em>";
				}
				var j, checked, match, matching, variantStr, subsetStr;
				variantStr = '';
				subsetStr = '';
				if (typeof font.variants != 'undefined') {
					var variants, selvariants;
					if (font.variants.indexOf(',') < 0) {
						variants = new Array();
						variants[0] = font.variants;
					}
					else {
						variants = font.variants.split(',');
					}

					selvariants = font.selvariants;

					var variantSelectors;
					var vlen = variants.length;
					var weight, style;
					if (vlen > 0) {
						variantStr += "<div class='fm-all-variants'>";
						variantStr += "<h3>";
						variantStr += "Variants";
						if (font.source == 'Font Squirrel') {
							variantStr += '<span class="selectors">Selectors</span>';
						}
						variantStr += "</h3>";
						variantStr += "<ul class='fm-variant-list'>";
						if (vlen == 1) {
							variantStr += "<li>";
							if (typeof FontMeisterFontVariations[variants[0]] != 'undefined') {
								weight = FontMeisterFontVariations[variants[0]];
								variantStr += weight + ' (' + variants[0] + ')';
							}
							else {
								variantStr += variants[0].substr(0, 1).toUpperCase() + variants[0].substr(1);
							}
							if (font.source == 'Font Squirrel') {
								variantSelectors = font.variantselectors;
								variantStr += '<input type="text" class="fm-variant-font-selector fm-variant-font-selector-' + variants[0] + ' fm-variant-font-selector-base-' + font.stub + '" value="' + variantSelectors +'"/>';
							}
							variantStr += "</li>"
						}
						else {
							if (font.source == 'Font Squirrel') {
								variantSelectors = font.variantselectors;
								variantSelectors = variantSelectors.split('|');
							}

							for (j=0; j<vlen; j++) {
								variantStr += "<li>";
								variantStr += "<label>";
								match = new RegExp("\\b"+variants[j]+"\\b");
								matching = match.exec(selvariants);
								if (matching != null) {
									checked = " checked='checked' ";
								}
								else {
									checked = '';
								}

								variantStr += "<input type='checkbox' " + checked + " class='fm-variant-list-cb' id='fm-variant-" + variants[j] + "'/>";
								if (typeof FontMeisterFontVariations[variants[j]] != 'undefined') {
									weight = FontMeisterFontVariations[variants[j]];
									variantStr += weight + ' (' + variants[j] + ')';
								}
								else {
									variantStr += variants[j].substr(0, 1).toUpperCase() + variants[j].substr(1);
								}
								variantStr += "</label>";
								if (font.source == 'Font Squirrel') {
									variantStr += '<input type="text" class="fm-variant-font-selector fm-variant-font-selector-' + variants[j] + ' fm-variant-font-selector-base-' + font.stub + '" value="' + variantSelectors[j] + '" />';
								}
								variantStr += "</li>";
							}
						}
						variantStr += "</ul>";
						variantStr += '</div>';
					}
				}

				if (typeof font.subsets != 'undefined' && font.subsets != '') {
					var subsets, selsubsets;
					selsubsets = font.selsubsets;
					if (font.subsets.indexOf(',') < 0) {
						subsets = new Array();
						subsets[0] = font.subsets;
					}
					else {
						subsets = font.subsets.split(',');
					}

					var slen = subsets.length;
					if (slen > 0) {
						subsetStr += "<div class='fm-all-subsets'>";
						subsetStr += "<h3>Subsets</h3>";
						subsetStr += "<ul class='fm-subset-list'>";
						if (slen == 1) {
							subsetStr += "<li>" + subsets[0].substr(0, 1).toUpperCase() + subsets[0].substr(1) + "</li>";
						}
						else {
							for (j=0; j<slen; j++) {
								subsetStr += "<li>";
								subsetStr += "<label>";
								match = new RegExp("\\b"+subsets[j]+"\\b");
								matching = match.exec(selsubsets);
								if (matching != null) {
									checked = " checked='checked' ";
								}
								else {
									checked = '';
								}
								subsetStr += "<input type='checkbox' " + checked + " class='fm-subset-list-cb' id='fm-subset-" + subsets[j] + "'/>";
								subsetStr += subsets[j].substr(0, 1).toUpperCase() + subsets[j].substr(1);
								subsetStr += "</label>";
								subsetStr += "</li>";
							}
						}
						subsetStr += "</ul>";
						subsetStr += '</div>';
					}
				}
				if (subsetStr == '' || variantStr == '') {
					details += variantStr + subsetStr;
				}
				else {
					details += '<div class="fm-variant-subset">' + variantStr + subsetStr + '</div>'
				}

				if (font.source != 'Font Squirrel') {
					var selectorStr = '';
					if (typeof font.selectors != 'undefined' && font.selectors != '') {
						selectorStr = font.selectors;
					}
					details += "<div class='fm-selector'><h3>Selectors</h3>" +
						"<label>CSS Selectors (Enter a comma-separated list. E.g. h1,h2,.pagetitle,#post-id):" +
						"<input type='text' id='fm-font-selectors' value='" + selectorStr + "'/>" +
						"</label></div>";
				}
				$('#fm-font-details').hide().html(details).fadeIn();
				break;
			}
		}
	});

	$(document).on('change', '.fm-variant-list-cb,.fm-subset-list-cb,#fm-font-selectors', function() {
		var fontName = $(this).parents('#fm-font-details').find('h2').text();
		var selectionId;
		if ($(this).hasClass('fm-variant-list-cb')) {
			selectionId = this.id.substr(11);
		}
		else if ($(this).hasClass('fm-subset-list-cb')) {
			selectionId = this.id.substr(10);
		}
		var len = FontMeisterFontStack.length;
		var font;
		for (var i=0; i<len; i++) {
			if (FontMeisterFontStack[i].family == fontName) {
				font = FontMeisterFontStack[i];
				var selections;
				if ($(this).hasClass('fm-variant-list-cb')) {
					selections = font.selvariants;
				}
				else if ($(this).hasClass('fm-subset-list-cb')) {
					selections = font.selsubsets;
				}
				if ($(this).hasClass('fm-variant-list-cb') || $(this).hasClass('fm-subset-list-cb')) {
					if (selections.indexOf(',') > -1) {
						selections = selections.split(',');
					}
					else {
						selections = new Array(selections);
					}
					selections = $.map(selections, function(value) {
						if (selectionId == value) {
							return null;
						}
						return value;
					});
					selections = selections.join(',');
				}
				else {
					selections = $(this).val();
				}
				if ($(this).hasClass('fm-variant-list-cb')) {
					FontMeisterFontStack[i].selvariants = selections;
				}
				else if ($(this).hasClass('fm-subset-list-cb')) {
					FontMeisterFontStack[i].selsubsets = selections;
				}
				else {
					FontMeisterFontStack[i].selectors = selections;
				}
				break;
			}
		}
		$('#font_stack').val(JSON.stringify(FontMeisterFontStack));
	});

	$(document).on('change', '.fm-variant-font-selector', function() {
		var classes = $(this).attr('class').split(/\s+/);
		var len = classes.length;
		for (var i=0; i<len; i++) {
			if (classes[i].substr(0, 30) == 'fm-variant-font-selector-base-') {
				var stub = classes[i].substr(30);
				var fontLen = FontMeisterFontStack.length;
				for (var j=0; j<fontLen; j++) {
					if (typeof FontMeisterFontStack[j].stub != 'undefined' && FontMeisterFontStack[j].stub == stub) {
						var variantSelectors = new Array();
						var selectors = $('.fm-variant-font-selector');
						$.each(selectors, function(index){
							variantSelectors[index] = $(this).val();
						});
						variantSelectors = variantSelectors.join('|');
						FontMeisterFontStack[j].variantselectors = variantSelectors;
					}
				}
				break;
			}
		}
		$('#font_stack').val(JSON.stringify(FontMeisterFontStack));
	});

	$(document).on('click', '.fm-add-font', function() {
		var family = $(this).parents('.fm-fonts-for li').children('.fm-list-family').text();
		var len = FontMeisterFontStack.length;
		for (var i=0; i<len; i++) {
			if (family == FontMeisterFontStack[i].family) {
				return false;
			}
		}

		var lineItem = $(this).parents('.fm-fonts-for li');
		var source, variants, subsets, generic, stub, files, link, familyID, fontFamily, variantSelectors, kit;
		files = '';
		familyID = '';
		fontFamily = '';
		variantSelectors = '';
		kit = '';
		if ($(this).hasClass('fm-add-font-gf')) {
			link = $('<link>');
			link.attr({
				type: 'text/css',
				rel: 'stylesheet',
				href: 'http://fonts.googleapis.com/css?family=' + encodeURIComponent(family)
			});
			$('head').append(link);
			source = 'Google Web Fonts';
			generic = '';
			stub = '';
			variants = $(lineItem).children('.fm-font-variants').text();
			subsets = $(lineItem).children('.fm-font-subsets').text();
		}
		else if ($(this).hasClass('fm-add-font-tk')) {
			source = 'Typekit';
			generic = '';
			stub = $(lineItem).children('.fm-font-stub').text();
			variants = $(lineItem).children('.fm-font-variants').text();
			subsets = $(lineItem).children('.fm-font-subsets').text();
			kit = $(lineItem).attr('class');
			if (kit.length > 0) {
				kit = kit.substr(8, kit.length - 8);
			}
		}
		else if ($(this).hasClass('fm-add-font-fd')) {
			source = 'Fontdeck';
			generic = '';
			stub = '';
			variants = $(lineItem).children('.fm-font-variants').text();
			subsets = $(lineItem).children('.fm-font-subsets').text();
		}
		else if ($(this).hasClass('fm-add-font-fs')) {
			source = 'Font Squirrel';
			generic = '';
			stub = $(lineItem).children('.fm-font-stub').text();
			link = $('<link>');
			link.attr({
				type: 'text/css',
				rel: 'stylesheet',
				href: FontMeisterJS.font_dir_url + stub + '/stylesheet.css'
			});
			$('head').append(link);
			variants = $(lineItem).children('.fm-font-variants').text();
			var variantArray = variants.split(',');
			var selectorArray = new Array();
			for (var ctr = 0; ctr < variantArray.length; ctr++) {
				selectorArray[ctr] = '';
			}
			variantSelectors = selectorArray.join('|');
			if (variants.indexOf(',') > 0) {
				fontFamily = variants.substr(0, variants.indexOf(','));
			}
			else {
				fontFamily = variants;
			}
			files = $(lineItem).children('.fm-font-variants-files').text();
			subsets = $(lineItem).children('.fm-font-subsets').text();
			familyID = $(lineItem).children('.fm-font-family-id').text();
		}

		if (fontFamily == '') {
			if (stub == '') {
				fontFamily = "\"" + family + "\"";
			}
			else {
				fontFamily = stub;
			}
		}

		$('#fm-font-stack').append($("<li><span class='sample' style='font-family: " + fontFamily + ";'>Mr. Jock, TV quiz Ph.D., bags few lynx.</span><span class='fm-stack-meta'><span class='fm-font-family'>" + family + "</span> <a href='#' class='fm-remove-font' title='Remove'>&nbsp;</a></span></span></li>").hide().fadeIn());
		FontMeisterFontStack[FontMeisterFontStack.length] = {
			family: family,
			familyID: familyID,
			source: source,
			stub: stub,
			generic: generic,
			variants: variants,
			selvariants: variants,
			variantselectors: variantSelectors,
			files: files,
			subsets: subsets,
			selsubsets: subsets,
			kit: kit
		};

		$('#font_stack').val(JSON.stringify(FontMeisterFontStack));
		$('html, body').animate({
			scrollTop: 0
		}, 'slow');
		return false;
	});

	$(document).on('click', '.fm-download-font', function(e) {
		var button = $(this);
		var lineItem = $(this).parents('.fm-fonts-for li');
		var addButton = "<a href='#' class='fm-add-font fm-add-font-fs' title='Add'>&nbsp;</a>";
		var deleteButton = "<a href='#' class='fm-delete-download fm-delete-download-fs' title='Delete Download'>&nbsp;</a>";
		var family = $(lineItem).children('.fm-font-stub').text();
		var variants = $(lineItem).children('.fm-font-variants');
		if (variants.length == 0) {
			variants = $("<span class='fm-font-variants'></span>").appendTo(lineItem);
		}

		if ($(this).hasClass('fm-download-font-fs')) {
			var url = "http://www.fontsquirrel.com/fontfacekit/" + encodeURIComponent(family);
			$('<div class="fm-font-wip">&nbsp;</div>').prependTo($(this).parents('li'));
			$.post(FontMeisterJS.ajaxurl, "action=fontmeister_download_font&font_url=" + url, function(data) {
				var response = $.parseJSON(data);
				if (typeof response.success == 'undefined') {
					alert("Font download failed!");
				}
				else {
					if (typeof response.variants != 'undefined') {
						$(variants).text(response.variants);
					}
					$(button).fadeOut(500, function(e) {
						var buttonPanel = $(lineItem).children('.fm-prev-add');
						$(this).remove();
						$(buttonPanel).append(addButton).fadeIn();
						$(buttonPanel).append(deleteButton).fadeIn();
					});
				}
				$('.fm-font-wip').remove();
			});
		}

		return false;
	});

	$(document).on('click', '.fm-delete-download', function(e) {
		var button = $(this);
		var downloadButton = "<a href='#' class='fm-download-font fm-download-font-fs' title='Download'>&nbsp;</a>";
		var family = $(this).parents('.fm-fonts-for li').children('.fm-font-stub').text();
		if ($(this).hasClass('fm-delete-download-fs')) {
			$('<div class="fm-font-wip">&nbsp;</div>').prependTo($(this).parents('li'));
			$.post(FontMeisterJS.ajaxurl, "action=fontmeister_delete_download&font_family=" + family, function(data) {
				var response = $.parseJSON(data);
				if (typeof response.success == 'undefined') {
					alert("Font deletion failed!");
				}
				else {
					$(button).fadeOut(500, function(e) {
						var buttonPanel = $(this).parents('.fm-fonts-for li').children('.fm-prev-add');
						var addButton = $(buttonPanel).children('.fm-add-font');
						$(addButton).fadeOut().remove();
						$(this).remove();
						$(buttonPanel).append(downloadButton).fadeIn();
					});
				}
				$('.fm-font-wip').remove();
			});
		}

		return false;
	});

	$(document).on('click', '.fm-remove-font', function() {
		var font_to_remove = $(this).siblings('.fm-font-family').text();
		FontMeisterFontStack = $.map(FontMeisterFontStack, function(value) {
			if (font_to_remove == value['family']) {
				return null;
			}
			return value;
		});
		$(this).parents('#fm-font-stack li').fadeOut(500, function(){
			$(this).remove();
			if ($('#fm-font-stack').children().length == 0) {
				$('.fm-font-details').html("<h2>Add Fonts</h2>You have no fonts in your stack. Please add a font first from the sources below. If you don't see any fonts below, make sure you have set up the <a href='admin.php?page=fontmeister-settings'>Font Sources</a> correctly.");
			}
			else if ($('.fm-font-details h2').text() == font_to_remove) {
				$('.fm-font-details').html('<h2>Preview</h2>Select a font from the left to see its details.');
			}
		});
		$('#font_stack').val(JSON.stringify(FontMeisterFontStack));
		return false;
	});

	$('.fm-launch-preview').click(function() {
		var source, kit, panel, family, url;
		if ($(this).hasClass('fm-launch-preview-gf')) {
			source = 'Google Web Fonts';
			kit = '';
			family = $(this).parents('.fm-fonts-for li').find('.fm-list-family').text();
			family = '&family=' + family;
			url = '';
		}
		else if ($(this).hasClass('fm-launch-preview-tk')) {
			source = 'Typekit';
			panel = $(this).parents('.fm-fonts-for');
			kit = $(panel).attr('id');
			kit = kit.substr(0, kit.lastIndexOf('-'));
			kit = '&kit=' + kit.substr(6);
			family = $(this).parents('.fm-fonts-for li').find('.fm-font-stub').text();
			family = '&family=' + family;
			url = '';
		}
		else if ($(this).hasClass('fm-launch-preview-fd')) {
			source = 'Fontdeck';
			panel = $(this).parents('.fm-fonts-for');
			url = $(panel).children('.fm-fd-css');
			kit = $(panel).attr('id');
			kit = kit.substr(0, kit.lastIndexOf('-'));
			kit = '&project=' + kit.substr(6);
			family = $(this).parents('.fm-fonts-for li').find('.fm-font-stub').text();
			family = '&family=' + family;
			url = '&url=' + url.text();
		}
		else if ($(this).hasClass('fm-launch-preview-fs')) {
			source = 'Font Squirrel';
			panel = $(this).parents('.fm-fonts-for');
			kit = '';
			family = $(this).parents('.fm-fonts-for li').find('.fm-font-stub').text();
			family = '&family=' + family;
			url = '';
		}

		$.colorbox({
			iframe: true,
			width: '750px',
			height: '600px',
			href: $('#fm-preview-link').val() + '?source=' + source + kit + family + url
		});
		return false;
	});
});