<?php
/**
 * The Template for Site Front-Page
 * @package WordPress
 * @SketchThemes
 */
?>
<!-- HEADER -->
<?php get_header(); ?>


<!-- LANDING PAGE SECTION SECTION -->              
<?php get_template_part( 'includes/front', 'first-landing-section' ); ?>

<!-- LANDING PAGE SECTION SECTION -->
<?php get_template_part( 'includes/front', 'second-landing-section' ); ?>

<!-- LANDING PAGE SECTION SECTION -->
<?php get_template_part( 'includes/front', 'third-landing-section' ); ?>

<?php if ( 'page' == get_option( 'show_on_front' ) ) { ?>
<!-- PAGE EDITER CONTENT -->
	<?php if(have_posts()) : ?>
		<?php while(have_posts()) : the_post(); ?>
			<div id="front-content-box" class="skt-section skt-default-page">
				<div class="container">
					<?php the_content(); ?>
				</div>
			</div>
		<?php endwhile; ?>
	<?php endif; ?> 
<?php } ?>

<?php if ( 'page' != get_option( 'show_on_front' ) ) { ?>
<div id="front-posts-box" class="landing-section skt-section">
	<div class="container">
		<div class="row-fluid skt-default-page">
			<div class="title custicon"><p class="landing-section-heading"><?php echo wp_kses_post( get_theme_mod( 'connexions_lite_home_blog_title', __('LATEST ARTICLES', 'connexions-lite') ) ); ?></p><div class="title-border"><i class="fa fa-comments-o"></i></div></div>
		</div>
		<div class="row-fluid front-blog-wrap">
			<?php $connexions_lite_blogno = get_option('posts_per_page');
				$connexions_lite_latest_loop = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => $connexions_lite_blogno ) );
			?>
			<?php if ( $connexions_lite_latest_loop->have_posts() ) : ?>

			<!-- pagination here -->

				<!-- the loop -->
				<?php while ( $connexions_lite_latest_loop->have_posts() ) : $connexions_lite_latest_loop->the_post(); ?>
					
						<div class="news_blog span4">
							<!--skt_blog_top-->
							<div class="skt_blog_top">
								<div class="post" id="post-<?php the_ID(); ?>">
									<div class="featured-image-shadow-box">
										<?php if( has_post_thumbnail() ) {
											$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),'connexions-lite-front-thumb');
										?>
											<img src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="<?php the_title(); ?>" class="attachment-blog-shortcode-thumb wp-post-image" />
										<?php } else { ?>
											<img src="<?php echo get_template_directory_uri().'/images/front-blog-img.jpg'; ?>" alt="<?php the_title(); ?>" class="attachment-blog-shortcode-thumb wp-post-image" />
										<?php } ?>
									</div>
								</div>
							</div>
							<div class="skt_blog_top">
								<!--Post Details-->
								<?php 
									$author_url    = get_author_posts_url(get_the_author_meta( 'ID' ));
									$author_nm     = get_the_author_meta('display_name',$post->post_author);
								?>
								<div class="news-details clearfix">
								    <p class="post-admin conx-date span5"><?php the_time('F j, Y') ?></p>
									<p class="post-admin conx-author span5"><?php the_author_posts_link(); ?></p>
								    
								</div>
							</div>
							<!-- skt_blog_middle-->
							<div class="skt_blog_middle">
								<h2 class="skt_blog_title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<div class="blogtext">
								<?php
							 		$post_content = get_the_excerpt();
							 		echo connexions_lite_limit_words($post_content, 20); 
							  	?>
								</div>
							</div>
							
							<!-- skt_blog_bottom-->
							<div class="skt_blog_bottom">
								<!--Post Details-->
								<div class="news-details clearfix">
									<p class="post-commentss span6"></p>
									<p class="post-commentss span6"><span class="skt_blog_commt"><i class="fa fa-comments-o"></i><?php comments_popup_link( __('No Comments ', 'connexions-lite'), __('1 Comment ', 'connexions-lite'), __('% Comments ', 'connexions-lite') ) ; ?></span></p>
								</div>
							</div>
							<!-- skt_blog_bottom -->
						</div>

				<?php endwhile; ?>
				<!-- end of the loop -->

				<?php wp_reset_postdata(); ?>

			<?php else : ?>
				<p><?php _e( 'Sorry, no posts matched your criteria.', 'connexions-lite' ); ?></p>
			<?php endif; ?>
		</div>
 	</div>
</div>
<?php } ?>

<!-- FOOTER -->
<?php get_footer(); ?>