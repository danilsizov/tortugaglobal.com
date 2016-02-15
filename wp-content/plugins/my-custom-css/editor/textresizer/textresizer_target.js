// textsizer
jQuery(document).ready( function() {
	jQuery( "#text-resizer a" ).textresizer({
		target: "#my_custom_css",
		type: "css",
		sizes: [
			// Small. Index 0
			{ "font-size"  : "12px",	},

			// Medium. Index 1
			{ "font-size"  : "14px", },

			// Large. Index 2
			{ "font-size"  : "16px", },

			// Larger. Index 3
			{ "font-size"  : "18px", },
			
			// Giga. Index 4
			{ "font-size"  : "26px", }
		],
		selectedIndex: 1
	});
});