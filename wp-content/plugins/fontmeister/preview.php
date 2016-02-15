<html>
<head>
	<link rel='stylesheet' href='//fonts.googleapis.com/css?family=Dosis' type='text/css' media='all' />
	<style type="text/css">
		body {
			font-family: Tahoma, Geneva, sans-serif; ;
		}
		.wf-loading p { }
		.wf-inactive p { }
		.wf-active p { }
		.wf-loading h1 { }
		.wf-inactive h1 { }
		.wf-active h1 { }
		.normal {
			font-weight: normal;
		}
		.bold {
			font-weight: bold;
		}
		.thin {
			font-weight: 100;
		}
		#fm-preview-panel.px-10 { font-size: 10px; }
		#fm-preview-panel.px-11 { font-size: 11px; }
		#fm-preview-panel.px-12 { font-size: 12px; }
		#fm-preview-panel.px-13 { font-size: 13px; }
		#fm-preview-panel.px-14 { font-size: 14px; }
		#fm-preview-panel.px-15 { font-size: 15px; }
		#fm-preview-panel.px-16 { font-size: 16px; }
		#fm-preview-panel.px-17 { font-size: 17px; }
		#fm-preview-panel.px-18 { font-size: 18px; }
		#fm-preview-panel.px-19 { font-size: 19px; }
		#fm-preview-panel.px-20 { font-size: 20px; }
		.fm-preview-title {
			font-family: 'Dosis', Tahoma, Geneva, sans-serif;
			text-transform: uppercase;
			border-bottom: 1px dotted #ccc;
			font-weight: normal;
			padding-left: 64px;
			background: url(include/icons/FontMeister-48x32.png) no-repeat 0 0;
			padding-bottom: 10px;
		}
		.fm-preview-prelude {
			font-family: Tahoma, Geneva, sans-serif;
			border-bottom: 1px dotted #ccc;
			padding-bottom: 10px;
		}
		#fm-preview-panel {
			font-size: 14px;
		}
		.fm-font-preview-images li {
			list-style: none;
			border-bottom: 1px dotted #ccc;
			padding: 10px 0;
		}
		.fm-font-preview-images img {
			display: block;
		}
		h1 { font-size: 200%; }
		h2 { font-size: 183%; }
		h3 { font-size: 166%; }
		h4 { font-size: 150%; }
		h5 { font-size: 133%; }
		h6 { font-size: 116%; }
	</style>
	<script type="text/javascript">
		<?php
$source = '';
$family = '';
if (isset($_GET['source']) && in_array($_GET['source'], array('Typekit', 'Fontdeck', 'Google Web Fonts'))) {
	$source = htmlentities($_GET['source'], ENT_NOQUOTES);
	if ($source == 'Typekit') {
		if (isset($_GET['family'])) {
			$family = htmlentities($_GET['family'], ENT_NOQUOTES);
		}
		if (isset($_GET['kit'])) {
			$kit = htmlentities($_GET['kit'], ENT_NOQUOTES);
			?>
		WebFontConfig ={
			typekit: {
				id: '<?php echo $kit; ?>'
			}
		};
			<?php
		}
	}
	else if ($source == 'Fontdeck') {
		if (isset($_GET['family'])) {
			$family = htmlentities($_GET['family'], ENT_NOQUOTES);
		}
		if (isset($_GET['project'])) {
			$project = htmlentities($_GET['project'], ENT_NOQUOTES);
			?>
		WebFontConfig ={
			fontdeck: {
				id: '<?php echo $project; ?>'
			}
		};
			<?php
		}
	}
	else if ($source == 'Google Web Fonts') {
		if (isset($_GET['family'])) {
			$family = htmlentities($_GET['family'], ENT_NOQUOTES);
			?>
		WebFontConfig ={
			google: {
				families: ['<?php echo $family; ?>']
			}
		};
			<?php
		}
	}
	?>
(function() {
	var wf = document.createElement('script');
	wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
			'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
	wf.type = 'text/javascript';
	wf.async = 'true';
	var s = document.getElementsByTagName('script')[0];
	s.parentNode.insertBefore(wf, s);
})();
	<?php
}
?>
		function fontmeister_bold_unbold(element) {
			var preview = document.getElementById('fm-preview-panel');
			var tags = new Array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
			for (var i=0; i<tags.length; i++) {
				var h = preview.getElementsByTagName(tags[i])[0];
				h.className = element.value;
			}
			return false;
		}

		function fontmeister_manage_size(element) {
			var preview = document.getElementById('fm-preview-panel');
			preview.className = element.value;
			return false;
		}
	</script>
	<?php
	if ($source == 'Typekit') {
		?>
		<script type='text/javascript' src='//use.typekit.com/<?php echo $kit; ?>.js'></script>
		<script type='text/javascript'>try{Typekit.load();}catch(e){}</script>
		<?php
	}
	else if ($source == 'Fontdeck') {
		?>
	<link rel="stylesheet" href="<?php echo substr($_REQUEST['url'], stripos($_REQUEST['url'], ':') + 1); ?>" type="text/css" />
		<?php
	}
	?>
	<style type="text/css">
		body {
			font-family: <?php echo $family; ?>;
			font-size: 12px;
			padding: 10px;
		}
	</style>
</head>
<body>
<h1 class="fm-preview-title">FontMeister Font Preview</h1>
<?php
if (isset($_GET['source']) && in_array($_GET['source'], array('Typekit', 'Fontdeck', 'Google Web Fonts'))) {
?>
<div class="fm-preview-prelude">
	<p>
		You are previewing the &ldquo;<?php echo $family; ?>&rdquo; stack from <?php echo $source; ?>.
	</p>
	<label>
		Header text:
		<select id="fm-preview-header" onchange="fontmeister_bold_unbold(this);">
			<option value="normal">Regular</option>
			<option value="bold">Bold</option>
			<option value="thin">Thin</option>
		</select>
	</label>
	<label>
		Body font size:
		<select id="fm-preview-sizes" onchange="fontmeister_manage_size(this);">
			<option value="px-10">10px</option>
			<option value="px-11">11px</option>
			<option value="px-12">12px</option>
			<option value="px-13">13px</option>
			<option value="px-14" selected="selected">14px</option>
			<option value="px-15">15px</option>
			<option value="px-16">16px</option>
			<option value="px-17">17px</option>
			<option value="px-18">18px</option>
			<option value="px-19">19px</option>
			<option value="px-20">20px</option>
		</select>
	</label>
</div>

<div id="fm-preview-panel">
	<h1 class="normal">&lt;H1&gt;: Mr. Jock, TV quiz Ph.D., bags few lynx.</h1>
	<h2 class="normal">&lt;H2&gt;: Bawds jog, flick quartz, vex nymph.</h2>
	<h3 class="normal">&lt;H3&gt;: Waltz, nymph, for quick jigs vex Bud.</h3>
	<h4 class="normal">&lt;H4&gt;: Quick wafting zephyrs vex bold Jim.</h4>
	<h5 class="normal">&lt;H5&gt;: Jackadaws love my big sphinx of quartz.</h5>
	<h6 class="normal">&lt;H6&gt;: Pack my box with five dozen liquor jugs.</h6>
	<p>
		&lt;P&gt;: The quick brown fox jumps over the lazy dog. A sentence with all letters of the alphabet is called a pangram.
		The pangrams for H1 to H5 are all from Richard Lederer's book Crazy English.
	</p>
</div>
<?php
}
else if (isset($_GET['source']) && $_GET['source'] == 'Font Squirrel') {
	if (isset($_GET['family'])) {
		$family = htmlentities($_GET['family'], ENT_NOQUOTES);
?>
	<div class="fm-preview-prelude">
		<p>
			You are previewing the &ldquo;<?php echo $family; ?>&rdquo; stack from <?php echo $_GET['source']; ?>.
		</p>
	</div>

<?php
		$font_info_url = 'http://www.fontsquirrel.com/api/familyinfo/'.$family;
		$ch = curl_init($font_info_url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		$variants = json_decode($response);

		echo "<ul class='fm-font-preview-images'>\n";
//		$size = '&size=14';
//		$text = '&text='.urlencode("Bawds jog, flick quartz, vex nymph.");
		$size = '&s=14';
		$text = '&t='.urlencode("Bawds jog, flick quartz, vex nymph.");
		$scale = '&scale=150';
		foreach ($variants as $variant) {
			if (isset($variant->listing_image)) {
				echo "<li>";
				//$font = 'font='.$variant->family_id.'/'.$variant->filename;
				$font = $variant->checksum;
				//$img_url = 'http://www.fontsquirrel.com/utils/font_list_sample.php?'.$font.$size.$text.$scale;
				$img_url = 'http://www.fontsquirrel.com/widgets/test_drive/'.$font.'?'.$size.$text;

				echo "<img src='$img_url' />";
				echo $variant->family_name;
				if (isset($variant->style_name)) {
					echo " (".$variant->style_name.")";
				}
				echo "</li>";
			}
		}
		echo "</ul>\n";
	}
}
?>
</body>
</html>