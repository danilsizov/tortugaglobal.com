<?php
	if(is_page() && !is_front_page()){
		$connexion_breadcumb = new connexions_lite_breadcrumb_class();
	?>
		<div class="bread-title-holder">
			<div class="container">
				<div class="row-fluid">
					<div class="container_inner clearfix">

						<!-- #logo -->
						<div id="logo" class="span6">
							<?php if( get_theme_mod('connexions_lite_logo_img', '' ) != '' ) { ?>
								<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name'); ?>" ><img class="logo" src="<?php echo esc_url( get_theme_mod('connexions_lite_logo_img') ); ?>" alt="<?php bloginfo('name'); ?>" /></a>
							<?php } elseif ( display_header_text() ) { ?>
							<!-- #description -->
							<div id="site-title" class="logo_desp">
								<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name') ?>" ><?php bloginfo('name'); ?></a> 
								<div id="site-description"><?php bloginfo( 'description' ); ?></div>
							</div>
							<!-- #description -->
							<?php } ?>
						</div>
						<!-- #logo -->

						<span class="span6">
								<h1 class="title"><?php single_post_title(); ?><i class="fa fa-folder-open-o"></i></h1>
							<?php if ((class_exists('connexions_lite_breadcrumb_class'))) {$connexion_breadcumb->connexions_lite_custom_breadcrumb();} ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
?>


<div class="skt-default-page">

	<div class="skt-page-overlay"></div>

	<!-- Container-->
	<div class="container post-wrap rpage_wrap">
		<div class="row-fluid">
		<?php 
			if(have_posts()) :
				while(have_posts()) : the_post();
		?>
				<div class="clearfix">
						<!-- #content// -->	
						<div id="content">
							<div class="post span12" id="post-<?php the_ID(); ?>">
								<div class="skepost innerpages clearfix">
									<?php the_content(); ?>
									<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages :','connexions-lite').'</strong>','after' => '</p>', __('number','connexions-lite'),)); ?>
								</div>
								<?php edit_post_link(__('Edit','connexions-lite'), '', ''); ?>
							<!-- skepost --> 
							<div class="author-comment-section clearfix">
							<!-- Post Comments Section -->
							<div class="news_comments">
								<?php if ('open' == $post->comment_status){ ?><h2 class="black mb"><?php _e('COMMENTS','connexions-lite'); ?></h2><?php } ?>
								<?php comments_template(); ?>
							</div>
							<!-- \\Post Comments Section -->
							</div>
							</div>
							<!-- post -->

							<?php endwhile; ?>
							<?php else : ?>
								<div class="post">
									<h2><?php _e('Page Does Not Exist','connexions-lite'); ?></h2>
								</div>
							<?php endif; ?>
						</div>
						<!-- \\#content -->
					</div>	
		</div>
	</div>
	<!-- /Container--> 
</div>