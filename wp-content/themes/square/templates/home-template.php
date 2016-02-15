<?php
/**
 * Template Name: Home Page
 *
 * @package square
 */

get_header(); ?>

<section id="sq-home-slider-section">
	<div id="sq-bx-slider">
	<?php for ($i=1; $i < 4; $i++) {  
		$square_slider_title = get_theme_mod('square_slider_title'.$i);
		$square_slider_subtitle = get_theme_mod('square_slider_subtitle'.$i);
		$square_slider_image = get_theme_mod('square_slider_image'.$i);
		if( $square_slider_image ){
		?>
		<div class="sq-slide">
			<img src="<?php echo esc_url( $square_slider_image ); ?>">
			
			<?php if( $square_slider_title || $square_slider_subtitle){ ?>
				<div class="sq-slide-caption">
					<div class="sq-slide-cap-title animated fadeInDown">
						<?php echo esc_html( $square_slider_title ); ?>
					</div>

					<div class="sq-slide-cap-desc animated fadeInUp">
						<?php echo esc_html( $square_slider_subtitle ); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php 
		}
	} ?>
	</div>
	<div class="sq-banner-shadow"><img src="<?php echo get_template_directory_uri() ?>/images/banner-shadow.png"></div>
</section>

<section id="sq-featured-post-section" class="sq-section">
	<div class="sq-container">
		<div class="sq-featured-post-wrap sq-clearfix">
			<?php 
			for( $i = 1; $i < 4; $i++ ){
				$square_featured_page_id = get_theme_mod('square_featured_page'.$i); 
				$square_featured_page_icon = get_theme_mod('square_featured_page_icon'.$i);
			
			if($square_featured_page_id){
				$args = array( 'page_id' => $square_featured_page_id );
				$query = new WP_Query($args);
				if($query->have_posts()):
					while($query->have_posts()) : $query->the_post();
				?>
					<div class="sq-featured-post">
						<div class="sq-featured-icon"><i class="fa <?php echo esc_attr( $square_featured_page_icon ); ?>"></i></div>
						<h4><?php the_title(); ?></h4>
						<div class="sq-featured-excerpt">
						<?php 
						if(has_excerpt()){
							echo get_the_excerpt();
						}else{
							echo square_excerpt( get_the_content(), 120); 
						}?>
						</div>
					</div>
				<?php
				endwhile;
				endif;	
				wp_reset_postdata();
				}
			}
			?>
		</div>
	</div>
</section>

<section id="sq-about-us-section" class="sq-section">
	<div class="sq-container sq-clearfix">
		<div class="sq-about-sec">
		<?php 
			$args = array(
				'page_id' => get_theme_mod('square_about_page') 
				);
			$query = new WP_Query($args);
			if($query->have_posts()):
				while($query->have_posts()) : $query->the_post();
			?>
			<h2 class="sq-section-title"><?php the_title(); ?></h2>
			<div class="sq-content"><?php the_content(); ?></div>
			<?php
			endwhile;
			endif;	
			wp_reset_postdata();
		?>
		</div>

		<div class="sq-image-stack">
			<ul id="sq-elasticstack" class="sq-elasticstack">
			<?php 
				$square_about_image_stack = get_theme_mod('square_about_image_stack');
				$square_about_image_stack = explode(',', $square_about_image_stack);
				foreach ($square_about_image_stack as $square_about_image_stack_single) {
					$image = wp_get_attachment_image_src( $square_about_image_stack_single, 'square-about-thumb');
					?>
					<li><img src="<?php echo esc_url( $image[0] ); ?>"></li>
					<?php
				}
			?>
			</ul>
		</div>
	</div>
</section>

<section id="sq-tab-section" class="sq-section">
	<div class="sq-container sq-clearfix">
		<ul class="sq-tab">
			<?php 
				for( $i = 1; $i < 6; $i++ ){
					$square_tab_title = get_theme_mod('square_tab_title'.$i);
					$square_tab_icon = get_theme_mod('square_tab_icon'.$i);
					
					if($square_tab_title){
					?>
					<li>
					<a href="#<?php echo 'sq-tab'.$i; ?>">
					<?php echo '<i class="fa '.esc_attr( $square_tab_icon ).'"></i>'.esc_html( $square_tab_title ); ?>
					</a>
					</li>
				<?php
					}
				}
			?>
		</ul>

		<div class="sq-tab-content">
			<?php 
				for ($i = 1; $i < 6 ; $i++) { 
					$square_tab_page = get_theme_mod('square_tab_page'.$i);
					if($square_tab_page){
					?>
						<div class="sq-tab-pane animated zoomIn" id="<?php echo 'sq-tab'.$i; ?>">
							<?php
							$args = array(
								'page_id' => $square_tab_page
								);
							$query = new WP_Query($args);
							if($query->have_posts()):
								while($query->have_posts()) : $query->the_post();
							?>
							<h2 class="sq-section-title"><?php the_title(); ?></h2>
							<div class="sq-content"><?php the_content(); ?></div>
							<?php
								endwhile;
							endif;	
							wp_reset_postdata();
							?>
						</div>
					<?php
					}
				}
			?>
		</div>
	</div>
</section>

<section id="sq-logo-section" class="sq-section">
	<div class="sq-container">
		<?php
		$square_logo_title = get_theme_mod('square_logo_title');
		?>

		<?php if($square_logo_title){ ?>
		<h2 class="sq-section-title"><?php echo esc_html( $square_logo_title ); ?></h2>
		<?php } ?>

		<?php 
		$square_client_logo_image = get_theme_mod('square_client_logo_image');
		$square_client_logo_image = explode(',', $square_client_logo_image);
		?>

		<div class="sq_client_logo_slider">
		<?php
		foreach ($square_client_logo_image as $square_client_logo_image_single) {
			$image = wp_get_attachment_image_src( $square_client_logo_image_single, 'full');
			?>
			<img src="<?php echo esc_url( $image[0] ); ?>">
			<?php
		}
		?>
		</div>
	</div>
</section>

<?php get_footer(); ?>