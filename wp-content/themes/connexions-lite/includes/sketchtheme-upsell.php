<?php
/**
 * Title: Theme Upsell.
 *
 * Description: Displays list of all Sketchtheme themes linking to it's pro and free versions.
 *
 *
 * @author   Sketchtheme
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://sketchthemes.com/
 */

// Add stylesheet and JS for sell page.
function connexions_lite_sell_style() {
    // Set template directory uri
    $directory_uri = get_template_directory_uri();
    wp_enqueue_style( 'upsell_style', get_template_directory_uri() . '/css/upsell.css' );
}
add_action( 'admin_init', 'connexions_lite_sell_style' );

// Add upsell page to the menu.
function connexions_lite_add_upsell() {
    $page = add_theme_page( __('Sketch Themes', 'connexions-lite'), __('Sketch Themes', 'connexions-lite'), 'administrator', 'sketch-themes', 'connexions_lite_display_upsell' );
    add_action( 'admin_print_styles-' . $page, 'connexions_lite_sell_style' );
}
add_action( 'admin_menu', 'connexions_lite_add_upsell',12 );

// Define markup for the upsell page.
function connexions_lite_display_upsell() {

    // Set template directory uri
    $directory_uri = get_template_directory_uri().'/images';
    ?>

    <div class="wrap">
    <div class="container-fluid">
    <div id="upsell_container">
    <div class="clearfix row-fluid">
        <div id="upsell_header" class="span12">
            <h2>
                <a href="https://sketchthemes.com/" target="_blank">
                    <img src="<?php echo $directory_uri; ?>/sketch-logo.png" alt="<?php __('Sketch Themes', 'connexions-lite') ?>" />
                </a>
            </h2>
            <div class="donate-info">
              <strong><?php _e('To Activate All Features, Please Upgrade to Pro version!', 'connexions-lite'); ?></strong><br>
              <a title="<?php _e('Upgrade to Pro', 'connexions-lite') ?>" href="https://sketchthemes.com/" target="_blank" class="upgrade"><?php _e('Upgrade to Pro', 'connexions-lite'); ?></a> <a title="<?php _e('Setup Instructions', 'connexions-lite'); ?>" href="<?php echo get_template_directory_uri(); ?>/readme.txt" target="_blank" class="donate"><?php _e('Setup Instructions', 'connexions-lite'); ?></a>
              <a title="<?php _e('Rate Connexions Lite', 'connexions-lite'); ?>" href="https://wordpress.org/support/view/theme-reviews/connexions-lite" target="_blank" class="review"><?php _e('Rate Connexions Lite', 'connexions-lite'); ?></a>
              <a title="<?php _e('Theme Test Drive', 'connexions-lite'); ?>" href="http://trial.sketchthemes.com/wp-signup.php" target="_blank" class="review"><?php _e('Theme Test Drive', 'connexions-lite'); ?></a>
            </div>
        </div>
    </div>
    <div id="upsell_themes" class="clearfix row-fluid">

    <!-- -------------- Connexions Pro ------------------- -->

        <div id="Connexions" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/connexions-corporate-portfolio-onepage-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Connexions.png"  alt="<?php __('Connexions Theme', 'connexions-lite') ?>" width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/connexions-corporate-portfolio-onepage-wordpress-theme/" target="_blank"><h4><?php _e('Connexions- Corporate Business WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e("Connexions is a simple, minimal, responsive, easy to use, one click install, beautiful and Elegent WordPress Theme with Easy Custom Admin Options Created by SketchTheme.com. Using Connexion theme options any one can easily customize this theme according to their need. You can use your own Logo, logo alt text, custom favicon, you can add social links, rss feed to homepage, you can use own copyright text, you can also insert analytics code etc. And all this information can be entered using Connexion Theme Options Panel. You have to just set the content from the Connexion Themes Options Panel and it'll be up ready to use.",'connexions-lite'); ?></p>

                    </div>

                    <a class="free btn btn-success" href="https://wordpress.org/themes/download/connexions-lite.1.0.0.zip?nostats=1" target="_blank"><?php _e( 'Try Connexions Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://sketchthemes.com/samples/connexions-corporate-business-demo/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/Connexions/" target="_blank"><?php _e( 'Buy Connexions Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>



        <!-- -------------- Avis Pro ------------------- -->

        <div id="Avis" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/avis-consulting-business-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Avis.png"  alt="<?php __('Avis Theme', 'connexions-lite') ?>" width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/avis-consulting-business-wordpress-theme/" target="_blank"><h4><?php _e('Avis - Business Consulting WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e("Avis is beautifully carved to bring a highly professional look to these sites. Adding to the convenience, Avis also combines some innovative features to bring about the brilliant site experience.",'connexions-lite'); ?></p>

                    </div>

                    <a class="free btn btn-success" href="https://wordpress.org/themes/download/avis-lite.1.0.2.zip?nostats=1" target="_blank"><?php _e( 'Try Avis Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="https://sketchthemes.com/preview/avis/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/Avis/" target="_blank"><?php _e( 'Buy Avis Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>



        <!-- -------------- LeadSurf Pro ------------------- -->

        <div id="LeadSurf" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/leadsurf-lead-capture-landing-page-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/LeadSurf.png"  alt="<?php __('LeadSurf Theme', 'connexions-lite') ?>" width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/leadsurf-lead-capture-landing-page-wordpress-theme/" target="_blank"><h4><?php _e('LeadSurf - App Launch WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e("Catching up with trend leading to ultra-modern business style, SketchThemes brings to you its highly innovative and distinctly appealing Lead Capture Cum Landing Page WordPress Theme - LeadSurf that serves the users with lead capture form along with app landing page.",'connexions-lite'); ?></p>

                    </div>

                    <a class="free btn btn-success" href="https://wordpress.org/themes/download/leadsurf-lite.1.0.0.zip?nostats=1" target="_blank"><?php _e( 'Try LeadSurf Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://sketchthemes.com/preview/leadsurf/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/LeadSurf/" target="_blank"><?php _e( 'Buy LeadSurf Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>


        <!-- -------------- InstaAppointment Pro ------------------- -->

        <div id="InstaAppointment" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/instaappointment-responsive-appointment-booking-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Insta-Appointment.png"  alt="<?php __('InstaAppointment Theme', 'connexions-lite') ?>" width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/instaappointment-responsive-appointment-booking-wordpress-theme/" target="_blank"><h4><?php _e('InstaAppointment - Responsive Appointment Booking WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e("InstaAppointment Corporate Responsive WordPress Theme has all that a big corporate house, business site, creative agency, engineering, real estate or high-end spa looks for, in order to deck up their websites with next generation features and beautiful appearance. Instaapp Corporate Responsive WordPress Themes is packed with some of the unique features that take your online presence to altogether new level. Instaapp comes with the dedicated sections of Latest Projects and Skills Section along with Full Screen Revolution slider.",'connexions-lite'); ?></p>

                    </div>

                    <a class="free btn btn-success" href="https://wordpress.org/themes/download/instaappointment-lite.1.0.1.zip?nostats=1" target="_blank"><?php _e( 'Try InstaAppointment Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://sketchthemes.com/preview/instaappointment/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/InstaAppointment/" target="_blank"><?php _e( 'Buy InstaAppointment Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>


        <!-- -------------- Eptima Pro ------------------- -->

        <div id="Eptima" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/eptima-corporate-responsive-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Eptima.png"  alt="<?php __('Eptima Theme', 'connexions-lite') ?>" width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/eptima-corporate-responsive-wordpress-theme/" target="_blank"><h4><?php _e('Eptima - Corporate Responsive WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e("Eptima Corporate Responsive WordPress Theme has all that a big corporate house, business site, creative agency, engineering, real estate or high-end spa looks for, in order to deck up their websites with next generation features and beautiful appearance. Eptima Corporate Responsive WordPress Themes is packed with some of the unique features that take your online presence to altogether new level. Eptima comes with the dedicated sections of Latest Projects and Skills Section along with Full Screen Revolution slider.",'connexions-lite'); ?></p>

                    </div>

                    <a class="free btn btn-success" href="https://wordpress.org/themes/eptima-lite/" target="_blank"><?php _e( 'Try Eptima Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://sketchthemes.com/samples/eptima-corporate-demo/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/eptima/" target="_blank"><?php _e( 'Buy Eptima Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>
    <!-- -------------- Rational Pro ------------------- -->

        <div id="Rational" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/rational-impressive-one-pager-responsive-business-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Rational.png"  alt="<?php __('Rational Theme', 'connexions-lite') ?>" width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/rational-impressive-one-pager-responsive-business-wordpress-theme/" target="_blank"><h4><?php _e('Rational - Impressive One Pager Responsive Business WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e("Every business requires an approach that shows the logic behind floating the trade and the exact objective with which the venture is initiated.Adding a rational approach to every business in the first place, the Rational - One Pager Responsive Business WordPress Theme certainly promises to better user's experience.",'connexions-lite'); ?></p>

                    </div>

                    <a class="free btn btn-success" href="https://wordpress.org/themes/download/rational-lite.1.0.0.zip?nostats=1" target="_blank"><?php _e( 'Try Rational Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="https://sketchthemes.com/samples/rational-onepager-demo/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/rational/" target="_blank"><?php _e( 'Buy Rational Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>
    

    <!-- -------------- Incart Pro ------------------- -->

        <div id="Incart" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/incart-responsive-woocommerce-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Incart.png"  alt="<?php __('Incart Theme', 'connexions-lite') ?>" width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/incart-responsive-woocommerce-wordpress-theme/" target="_blank"><h4><?php _e('InCart - Responsive WooCommerce WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e("SketchThemes continues the legacy of best multipurpose responsive business WordPress themes. Here we are gearing up to introduce a new WooCommerce WordPress theme which keeps it at the top priority for the customers to proliferate their business. This classy theme is fully WooCommerce compatible, SEO optimized and fully responsive so forget the fear of screen sizes, SEO optimization, just go and grab it.",'connexions-lite'); ?></p>

                    </div>

                    <a class="free btn btn-success" href="https://wordpress.org/themes/download/incart-lite.1.0.3.zip?nostats=1" target="_blank"><?php _e( 'Try Incart Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://sketchthemes.com/samples/incart-responsive-woocommerce-demo/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/incart/" target="_blank"><?php _e( 'Buy Incart Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>
	
	<!-- -------------- Convac Pro ------------------- -->

	<div id="Convac" class="row-fluid">
		<div class="theme-container">
			<div class="theme-image span3">
				<a href="https://sketchthemes.com/themes/convac-responsive-multi-author-blogging-theme/?ref=advlite" target="_blank">
					<img src="<?php echo $directory_uri; ?>/Convac.png"  alt="<?php __('Convac Theme', 'connexions-lite') ?>" width="300px"/>
				</a>
			</div>
			<div class="theme-info span9">
				<a class="theme-name" href="https://sketchthemes.com/themes/convac-responsive-multi-author-blogging-theme/?ref=advlite" target="_blank"><h4><?php _e('Convac - Responsive Multi Author Blogging Theme','connexions-lite');?></h4></a>

				<div class="theme-description">
					<p><?php _e("Convac is elegant WordPress theme solely designed for web bloggers. The main aim of theme is to improve blog's appearance for the end users and making blog posting a cakewalk task for bloggers. The theme has options and features which can customize blog as per your user and interest. Design for bloggers, theme is equipped with integrated features such as- post share, post like, subscription widget, supports to all post formats and many more.",'connexions-lite'); ?></p>
				</div>

				<a class="free btn btn-success" href="http://wordpress.org/themes/convac-lite/?ref=advlite" target="_blank"><?php _e( 'Try Convac Free', 'connexions-lite' ); ?></a>
				<a class="buy  btn btn-info" href="http://sketchthemes.com/samples/convac-multi-author-blogging-demo/?ref=advlite" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
				<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/convac/" target="_blank"><?php _e( 'Buy Convac Pro', 'connexions-lite' ); ?></a>
				
			</div>
		</div>
	</div>
	
	<!-- -------------- Foodeez Pro ------------------- -->

        <div id="Foodeez" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/foodeez-multi-cuisine-restaurant-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Foodeez.png"  alt="<?php __('Foodeez Theme', 'connexions-lite') ?>" width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/" target="_blank"><h4><?php _e('Foodeez - Multi Cuisine Restaurant WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e("Foodeez Lite is the the classy theme created for Restaurants and hotel Industry. To provide a Classy, elegant theme for the hospitality industry and to fill in the void Foodeez was designed. Every restaurant or hotel desires to showcase it's cuisines and ambiance in a beautiful manner. This was not satiated by any prior theme. Hence, Foodeez was designed. Now you can enjoy a fully Responsive, and Full-width Header Background Image with floating navigation which gets converted into Persistent Navigation on scroll down.The Amazing Parallax section allows you to showcase your Super-Specialty Signature Dishes or your Dish of the Day. In short, if you're a chef, a cook, cuisine guru, food expert, food fanatic, blogger, nutrition expert, diet planner, dietitian, recipe sharer, Fine Dining Lover, or just a food enthusiast, who want to share the love of food. Then this theme is Perfect for you.Clean, Fresh Looks of this WordPress Theme will make you feel wow. The blog page is clean and designed to The Theme is Loaded with feature which you won't find on any other Lite Themes with this much precision and quality.",'connexions-lite'); ?></p>

                    </div>

					<a class="free btn btn-success" href="http://wordpress.org/themes/foodeez-lite" target="_blank"><?php _e( 'Try Foodeez Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://sketchthemes.com/samples/foodeez-multi-cuisine-restaurant-demo/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
					<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/foodeez/" target="_blank"><?php _e( 'Buy Foodeez Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>
	
		<!-- -------------- Advertica Pro ------------------- -->

        <div id="Advertica" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/advertica-the-uber-clean-multipurpose-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Advertica.png"  alt="<?php __('Advertica Theme', 'connexions-lite') ?>" width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/" target="_blank"><h4><?php _e('Advertica - Creative MultiPurpose WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e('A Clean, Multipurpose, Responsive Business WordPress Theme by SketchThemes. With easy customization options one can easily setup a perfect business theme in a few minutes. The striking features of INVERT are Easy Custom Admin Options, 3 Custom Page Templates, Parallax Section, Custom Logo, Custom favicon, Social links Setup, SEO Optimized, Call To Action, Featured Text.','connexions-lite'); ?></p>

                    </div>

					<a class="free btn btn-success" href="http://wordpress.org/themes/advertica-lite" target="_blank"><?php _e( 'Try Advertica Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://sketchthemes.com/samples/advertica-creative-demo/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
					<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/advertica/" target="_blank"><?php _e( 'Buy Advertica Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>
	
		<!-- -------------- Invert Pro ------------------- -->

        <div id="Invert" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/invert-responsive-multipurpose-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Invert.png"  alt="<?php __('Invert Theme', 'connexions-lite') ?>" />
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/invert-responsive-multipurpose-wordpress-theme/" target="_blank"><h4><?php _e('Invert - Responsive MultiPurpose WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e('A Clean, Multipurpose, Responsive Business WordPress Theme by SketchThemes. With easy customization options one can easily setup a perfect business theme in a few minutes. The striking features of INVERT are Easy Custom Admin Options, 3 Custom Page Templates, Parallax Section, Custom Logo, Custom favicon, Social links Setup, SEO Optimized, Call To Action, Featured Text.','connexions-lite'); ?></p>

                    </div>

					<a class="free btn btn-success" href="http://wordpress.org/themes/invert-lite" target="_blank"><?php _e( 'Try Invert Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://sketchthemes.com/samples/invert-business-demo/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
					<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/invert/" target="_blank"><?php _e( 'Buy Invert Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>

        <!-- -------------- BizNez Pro ------------------- -->

        <div id="biznez" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/biznez-multi-purpose-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Biznez.png"   alt="<?php __('BizNez Theme', 'connexions-lite') ?>" />
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/biznez-multi-purpose-wordpress-theme/" target="_blank"><h4><?php _e('BizNez - Multi Purpose Wordpress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e('BizNez is a powerful Responsive Multipurpose WordPress Theme with clean and crispy design. BizNez is modern and flexible enough. It allows you to create your website around any niche without the need of any complicated HTML code knowledge. Be it a Blog, Business, Portfolio, Corporate, Agency, an online store or any other kind of website BizNez will be a good pick for you. With our advanced admin panel, tons of customizations are possible and that&#39;ll help you redefine your website&#39;s client value. You can also change site layout with only a single click. Also, this theme comes with retina ready feature. You can see great details on any devices screen. Woo Commerce and RTL languages are also supported with our BizNez theme.','connexions-lite'); ?></p>

                    </div>

					<a class="free btn btn-success" href="http://wordpress.org/themes/biznez-lite" target="_blank"><?php _e( 'Try Biznez Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://demo.sketchthemes.com/preview-images/biznez/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
					<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/biznez/" target="_blank"><?php _e( 'Buy Biznez Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>

        <!-- -------------- Analytical Pro ------------------- -->

        <div id="analytical" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/analytical-full-width-responsive-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Analytical.png"  alt="<?php __('Analytical Theme', 'connexions-lite') ?>" />
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/analytical-full-width-responsive-wordpress-theme/" target="_blank"><h4><?php _e('Analytical Full Width Responsive Wordpress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e('Analytical is a full width WordPress theme built especially for photographers and others who want to feature more graphics on their website. This theme features a full width background slider which is highly customizable and totally responsive.','connexions-lite');?></p>
                    </div>

					<a class="free btn btn-success" href="http://wordpress.org/themes/analytical-lite" target="_blank"><?php _e( 'Try Analytical Free', 'connexions-lite' ); ?></a>
                    <a class="buy  btn btn-info" href="http://demo.sketchthemes.com/preview-images/analytical/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
					<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/analytical/" target="_blank"><?php _e( 'Buy Analytical Pro', 'connexions-lite' ); ?></a>
                    
                </div>
            </div>
        </div>
		
        <!-- -------------- BizStudio Pro ------------------- -->

        <div id="bizstudio" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/bizstudio-full-width-responsive-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Bizstudio.png"  alt="<?php __('BizStudio Theme', 'connexions-lite') ?>"  width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/bizstudio-full-width-responsive-wordpress-theme/" target="_blank"><h4><?php _e('BizStudio Full Width Responsive Wordpress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e('Bizstudio is a Simple, Minimal, Responsive, One Click Install, Beautiful and Elegent WordPress Theme. Along with the elegent design the theme is highly flexible and customizable with easy to use Admin Panel Options. From a wide range of options some key options are custom two different layout option(full width and with sidebar), 5 widget areas, custom follow us and contact widget, Logo, logo alt text, custom favicon, social links, rss feed button, custom copyright text and many more. Also it is compitable with various popular plugins like All in One SEO Pack, WP Super Cache, Contact Form 7 etc. It is translation ready as well.','connexions-lite')?></p>
                    </div>

					<a class="free btn btn-success" href="http://wordpress.org/themes/bizstudio-lite" target="_blank"><?php _e( 'Try BizStudio Lite', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-info" href="http://demo.sketchthemes.com/preview/bizstudio/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
					<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/bizstudio/" target="_blank"><?php _e( 'Buy BizStudio Pro', 'connexions-lite' ); ?></a>
                </div>
            </div>
        </div>
		
		
		<!-- -------------- Irex Pro ------------------- -->

        <div id="Irex" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/irex-full-width-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Irex.png"  alt="<?php __('Irex Theme', 'connexions-lite') ?>"  width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/irex-full-width-wordpress-theme/" target="_blank"><h4><?php _e('Irex Full Width Portfolio Wordpress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e('Irex is a simple, minimal, responsive, easy to use, one click install, beautiful and Elegent WordPress Theme with Easy Custom Admin Options Created by SketchThemes.com. Using Irex theme options any one can easily customize this theme according to their need. You can use your own Logo, logo alt text, custom favicon, you can add social links, rss feed to homepage, you can use own copyright text etc. And all this information can be entered using Irex Theme Options Panel. You have to just set the content from the Irex  Themes Options Panel and it will be up ready to use.','connexions-lite');?></p>
                    </div>

					<a class="free btn btn-success" href="http://wordpress.org/themes/irex-lite" target="_blank"><?php _e( 'Try Irex Lite', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-info" href="http://demo.sketchthemes.com/preview/irex/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
					<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/irex/" target="_blank"><?php _e( 'Buy Irex Pro', 'connexions-lite' ); ?></a>
                </div>
            </div>
        </div>
		
	
	<!-- -------------- Fullscreen Pro ------------------- -->

        <div id="FullScreen" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/fullscreen-onepager-responsive-wordpress-theme/?ref=litetheme" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Fullscreen.png"  alt="<?php __('Fullscreen Theme', 'connexions-lite') ?>"  width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/fullscreen-onepager-responsive-wordpress-theme/?ref=litetheme" target="_blank"><h4><?php _e('FullScreen - OnePager Responsive WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e('FullScreen is clean, multipurpose and elegant WordPress theme with fully responsive layout. Theme is suited for all photographers, creative, business and portfolio websites. Theme includes lots of features like full-screen slider, modular homepage, layout shortcodes and more. With our advanced admin panel, tons of customizations are possible and that will help you redefine your website client value.','connexions-lite');?></p>
                    </div>

                    <a class="buy btn btn-info" href="http://sketchthemes.com/samples/fullscreen-default-demo/?ref=litetheme" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
					<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/fullscreen/" target="_blank"><?php _e( 'Buy FullScreen Pro', 'connexions-lite' ); ?></a>
                </div>
            </div>
        </div>
		
		
	<!-- -------------- Timeliner Pro ------------------- -->

        <div id="Timeliner" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/timeliner-minimal-ultra-responsive-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Timeliner.png"  alt="<?php __('Timeliner Theme', 'connexions-lite') ?>" />
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/timeliner-minimal-ultra-responsive-wordpress-theme/" target="_blank"><h4><?php _e('Timeliner - Minimal Ultra Responsive WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e('Timeliner is a premier timeline theme for WordPress. Timeliner is a minimal, clean, modern and highly customizable theme. It allows you to create their website around any niche without the need of any complicated HTML code knowledge. Be it a Blog, Portfolio, Corporate, Agency, any other kind of website Timeliner will be a good pick for you. With our advanced admin panel, tons of customizations are possible and that will help you redefine your website&#39;s client value. Also, this theme comes with retina ready feature. You can see great details on any devices screen.','connexions-lite');?></p>
                    </div>

                    <a class="buy  btn btn-info" href="http://sketchthemes.com/samples/timeliner-modeling-agency/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
					<a class="buy btn btn-primary" href="https://www.sketchthemes.com/members/signup/timeliner/" target="_blank"><?php _e( 'Buy Timeliner Pro', 'connexions-lite' ); ?></a>
 
                </div>
            </div>
        </div>

    <!-- -------------- Sketchmini Pro ------------------- -->

        <div id="Sketchmini" class="row-fluid">
            <div class="theme-container">
                <div class="theme-image span3">
                    <a href="https://sketchthemes.com/themes/sketchmini-free-wordpress-theme/" target="_blank">
                        <img src="<?php echo $directory_uri; ?>/Sketchmini.png"  alt="<?php __('Sketchmini Theme', 'connexions-lite') ?>"  width="300px"/>
                    </a>
                </div>
                <div class="theme-info span9">
                    <a class="theme-name" href="https://sketchthemes.com/themes/sketchmini-free-wordpress-theme/" target="_blank"><h4><?php _e('SketchMini Free Responsive WordPress Theme','connexions-lite');?></h4></a>

                    <div class="theme-description">
                        <p><?php _e('SketchMini is a Responsive WordPress Theme Free to use with a GPL. SketchMini has got great features and awesome backend to make use of the available features in the theme. You dont need to be an expert to use this SketchMini theme. SketchMini can act as a great base theme to create any great website.','connexions-lite')?></p>

                    </div>
                    <a class="free btn btn-success" href="https://sketchthemes.com/premium-themes/multipurpose-responsive-wordpress-theme-for-free/#content" target="_blank"><?php _e( 'Download Sketchmini', 'connexions-lite' ); ?></a>
                    <a class="buy btn btn-info" href="https://sketchthemes.com/samples/sketchmini-responsive-demo/" target="_blank"><?php _e( 'View Demo', 'connexions-lite' ); ?></a>
                </div>
            </div>
        </div>

		
    </div>
    <!-- upsell themes -->
    </div>
    <!-- upsell container -->
    </div>
    <!-- container-fluid -->
    </div>
<?php
}

?>