<?php
/**
 * The Sketch Custom Template to add custom code (PHP/WORDPRESS)
 * @package WordPress
 * @SketchThemes
 */
?>
<?php
$_color_scheme     		  = esc_attr( get_theme_mod('connexions_lite_pri_color', '#FB4A50') );
$_secondary_color_scheme  = esc_attr( get_theme_mod('connexions_lite_sec_color', '#2E3137') );

$_logo_width     		  = esc_attr( get_theme_mod('connexions_lite_logo_width', '199') );
$_logo_height     		  = esc_attr( get_theme_mod('connexions_lite_logo_height', '50') );

$_background_size = esc_attr( get_theme_mod('background_size', 'auto') );

$hrgb3 = array();
$hrgb3 = connexions_lite_Hex2RGB($_color_scheme);
$hR3   = $hrgb3['red'];
$hG3   = $hrgb3['green'];
$hB3   = $hrgb3['blue'];
$portexphovercolor = "rgba(". $hR3 .",". $hG3 .",". $hB3 .",.8)";
?>
<style type="text/css" rel="stylesheet">

	*::-moz-selection{background: <?php if(isset($_color_scheme)){ echo $_color_scheme; } ?>;color:#fff;}
	::selection {background: <?php if(isset($_color_scheme)){ echo $_color_scheme; } ?>;color:#fff;}

	body.custom-background{ background-size: <?php echo $_background_size; ?>; }

	.bread-title-holder {
		background:<?php echo esc_attr( get_theme_mod('breadcrumbbg_color', '#253151') ).' '.'url("'.esc_url(get_theme_mod('breadcrumbbg_image', '')).'") '.esc_attr(get_theme_mod('breadcrumbbg_repeat', 'no-repeat')).' '.esc_attr( get_theme_mod('breadcrumbbg_attachment', 'scroll') ).' '.esc_attr(get_theme_mod('breadcrumbbg_position_x', 'center')); ?>;
		background-size: <?php echo esc_attr( get_theme_mod('breadcrumbbg_size', 'cover') ); ?>;
	}
	.bread-title-holder .cont_nav_inner a,.bread-title-holder .cont_nav_inner span {
		color: <?php echo esc_attr( get_theme_mod('breadcrumbtxt_color', '#ffffff') ); ?>;
	}

	#navigation ul li:hover a, 
	#navigation ul li.current a, 
	#navigation ul li.current-menu-item a,
	#navigation ul#home li a,
	.btn-small,
	button.newsletter-sent,
	input.send_message,
	.widget-title,
	nav.primary ul li a:hover,
	.team-social,
	.page-navigation .alignleft, .page-navigation .alignright,
	#commentform input[type="submit"]{
		background-color: <?php echo $_color_scheme; ?>;
	}
	#logo img{width:<?php echo $_logo_width.'px'; ?>;height:<?php echo $_logo_height.'px'; ?>}

	a,nav.primary ul li a:active, #section2 .con-feature-icon,
	nav.primary ul li a.selected,
	.yellow,#sidebar .connexion-twitter-widget .tweets li a,
	.news_blog .news-details .conx-author a:hover {
		color: <?php echo $_color_scheme; ?>;
	}
	
	a.large-button:hover, a.small-button:hover, a.medium-button:hover,.subs-newsletter.conx-focus-wrap.active { 
	    background: none repeat scroll 0 0 <?php echo $_color_scheme; ?> !important;
	}

	

	#header.skehead-headernav-shrink .sktmenu-toggle,
	.sktmenu-toggle,.mask .mask-inner .port-buttons a:hover,
	.skepage .team.span3:hover,.skt-number-pb-shown.dream, 
	.subs-newsletter input[type="submit"],.skt-counter, 
	input[type="submit"], div.gmap-close, .conx-submit, 
	#header-trigger:hover, .social_icon ul li a:hover, .error-txt-img img,.ske-container.sktmultisocialstream a, #main .ske-container.sktmultisocialstream a  { background-color: <?php echo $_color_scheme; ?>; }

	a.skt-featured-icons,a#backtop,
	.con-feature-icon:before,
	.con-feature-icon,.reply a, a.comment-edit-link{ background: none repeat scroll 0 0 <?php echo $_color_scheme; ?>; }

	.con-feature-icon,.con-feature-icon:hover:before,
	.con-key-feature:hover .con-feature-icon:before,
	#testimonial-carousel.owl-theme .owl-controls .owl-buttons div,
	.skt-number-pb,.conx-cform-wrap input[type="submit"],
	.subs-newsletter input[type="submit"],#header-trigger, 
	.social_icon ul li a:hover {border-color: <?php echo $_color_scheme; ?>;}


	#skenav li.has_child > a:after{ border-top-color: <?php echo $_color_scheme; ?>; }

	#testimonial-carousel.owl-theme .owl-controls .owl-buttons div, .skt-counter:hover, .skt-counter:hover .skt-counter-h i, #header-trigger {color: <?php echo $_color_scheme; ?>;}
	
	input[type="text"]:focus, input[type="password"]:focus,input[type="email"]:focus,.conx-focus-wrap.active{
		background-color: <?php echo $_color_scheme; ?>; 
	}

	.port_scode_fwrap,
	.mask .mask-inner .port-buttons a,
	.skt_price_table.price_featured,
	.skt_price_table .price_table_inner .price_button a{ background: none repeat scroll 0 0 <?php echo $_secondary_color_scheme; ?>; }

	.protfolio_left:hover .port_overlay { background:<?php echo $portexphovercolor; ?>;opacity:1;}
	.port_scode_fwrap nav.primary ul li a.selected, .port_scode_fwrap nav.primary ul li:hover a { color:<?php echo $_secondary_color_scheme; ?>; background-color: transparent; }
	.port_overlay i.fa:hover { background-color: #2d3035; color: #FFFFFF; }

	.skt-iconbox.iconbox-top .iconbox-content h4,#logo #site-title a,.team-social a:hover, .copyright .copytxt-wrap .copy-txtcolor, .about-content i, #logo #site-description   { color: <?php echo $_color_scheme; ?>; }
	#footer_bottom .copyright p, #footer_bottom .refrence_link { color: <?php echo $_color_scheme; ?>; }
	#footer_bottom .row-fluid [class*="span"] { min-height : 0; }
	.skt-iconbox.iconbox-top .iconbox-content h4:after {background-color: <?php echo $_color_scheme; ?>;}
	.skepost .team .black, .skt-default-page .title .title-border i { color: <?php echo $_color_scheme; ?>; }
	#skenav .max-menu li.current a ,#skenav a:hover ,#sidebar li.ske-container > div a:hover, #sidebar li.ske-container > ul a:hover, #sidebar li.ske-container #wp-calendar tbody a:hover { color: <?php echo $_color_scheme; ?>; }
	.sketch-theme-black .sketch-close {background-color: <?php echo $_color_scheme; ?>; }
	.prot_text_wrap h2 {color: <?php echo $_color_scheme; ?>; margin-bottom: 27px;}
	.skt_price_table.price_featured .price_table_inner .price_button a, .skt_price_table .price_table_inner .price_button a:hover { background-color: <?php echo $_color_scheme; ?>; }
	#wp-calendar.skt-wp-calendar tbody tr td:last-child, #wp-calendar.skt-wp-calendar tbody tr th:last-child{color: <?php echo $_color_scheme; ?>; }

	.reply a:hover, a.comment-edit-link:hover,.page-navigation .alignleft:hover,.page-navigation .alignright:hover{background: <?php echo $_secondary_color_scheme; ?>;}
	.wp-calender-head {background-color: <?php echo $_secondary_color_scheme; ?>; }
	#sidebar li.ske-container #wp-calendar .wp-calender-head a:hover{color: <?php echo $_secondary_color_scheme; ?>; }
	
	/* BUTTONS STYLE */
	a.large-button:hover, a.small-button:hover, a.medium-button:hover{ background: none repeat scroll 0 0 <?php echo $_color_scheme; ?>;color:#fff; }


	/* BLOG STYLE */
	.post-calendar{background-color: <?php echo $_color_scheme; ?>; }
	.skt_blog_title { color: <?php echo $_color_scheme; ?>; margin-bottom: 15px; }
	.news_blog .news-details .skt_blog_commt:hover,.news_blog .news-details .skt_blog_commt a:hover {color: <?php echo $_color_scheme; ?>; }
	.news_full_blog .news-details .skt_blog_commt {color: <?php echo $_color_scheme; ?>; }
	.news_full_blog .full-post-calendar i.fa {color: <?php echo $_color_scheme; ?>; }
	blockquote, .page blockquote,.wp-calendar-head { background: <?php echo $_color_scheme; ?>; }
	.skt_blog_top .image-gallery-slider .postformat-gallerycontrol-nav li a.postformat-galleryactive { background-color: <?php echo $_color_scheme; ?>; }
	.play_button_overlay a.play_btn{background-color: <?php echo $_color_scheme; ?>; }
	.play_button_overlay a.play_btn:hover i.fa.fa-play {color: <?php echo $_color_scheme; ?>; }
	 #connexion-paginate a:hover{background-color: <?php echo $_color_scheme; ?>; }
	 #connexion-paginate .connexion-next, #connexion-paginate .connexion-prev{background-color:<?php echo $_color_scheme; ?>; }
	.author_social .team-social {border: 1px solid <?php echo $_color_scheme; ?>; }
	.author_social .team-social a {color: #FFFFFF;  }
	.author_social .team-social:hover {background:none repeat scroll 0 0 transparent;border:1px solid <?php echo $_color_scheme; ?>;     }
	.author_social .team-social:hover a { color: <?php echo $_color_scheme; ?>; }
	#respond input[type="submit"]:hover {background: none repeat scroll 0 0 <?php echo $_secondary_color_scheme; ?>; }
	.comment-author img.avatar,.commentlist p{border-color:<?php echo $_color_scheme; ?>; }
	/* PAGINATION */
	#connexion-paginate .connexion-page {background-color: <?php echo $_color_scheme; ?>;}
	#connexion-paginate .connexion-current{background-color: <?php echo $_secondary_color_scheme; ?>;}

	/* SHORTCODE */
	.ske_tab_v ul.ske_tabs li.active{border-left-color:<?php echo $_secondary_color_scheme; ?>;}
	.ske_tab_h ul.ske_tabs li.active{border-top-color:<?php echo $_secondary_color_scheme; ?>;}

	/* SIDEBAR STYLE */
	#sidebar #searchform input[type="submit"] {background-color: <?php echo $_color_scheme; ?>; }
	#sidebar #searchform input[type="submit"]:hover, #sidebar #searchform input[type="submit"]:hover .fa.fa-search {background-color: <?php echo $_secondary_color_scheme; ?>; }
	a:hover, #sidebar li.ske-container a:active, #sidebar li.ske-container a:hover { color: <?php echo $_secondary_color_scheme; ?>;  }
	#sidebar  .widget_tag_cloud a:hover,#sidebar .widget_product_tag_cloud a:hover{ background-color: <?php echo $_color_scheme; ?>; border-color:<?php echo $_color_scheme; ?>;  }
	#sidebar li.ske-container.widget_recent_entries li a:hover {color: <?php echo $_color_scheme; ?>; }
	#sidebar li.ske-container.SktFollowContact .follow-icons li a{color: <?php echo $_color_scheme; ?>; border:1px solid <?php echo $_color_scheme; ?>; }
	#sidebar li.ske-container.SktFollowContact .social li a:before {color: <?php echo $_color_scheme; ?>; }
	#sidebar li.ske-container.SktFollowContact .follow-icons li:hover a{background-color: <?php echo $_secondary_color_scheme; ?>; border:1px solid <?php echo $_secondary_color_scheme; ?>; }
	#sidebar li.ske-container.SktFollowContact .social li:hover a:before{color: #FFFFFF; }
	.widget_tag_cloud a:hover,.widget_product_tag_cloud a:hover { background-color: <?php echo $_secondary_color_scheme; ?>;border:1px solid <?php echo $_secondary_color_scheme; ?>; }
	.line {
		border-bottom: 1px solid <?php echo $_color_scheme; ?>;
	}
	
	@media only screen and (max-width : 1024px) {

		#logo {
		    margin-bottom: 10px;
		    margin-top: 14px;
		}
	}

</style>
