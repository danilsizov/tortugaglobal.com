<?php
/**
 * The Template for displaying all single posts.
 * @package WordPress
 * @SketchThemes
 */
?>
<?php get_header(); ?>
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
					<h1 class="title"><?php single_post_title(); ?><i class="fa fa-pencil-square-o"></i></h1>
					<?php if ((class_exists('connexions_lite_breadcrumb_class'))) {$connexion_breadcumb->connexions_lite_custom_breadcrumb();} ?>
				</span>
			</div>
		</div>
	</div>
</div>
<!-- Container-->
<div class="container clearfix skt-blog-page" style="padding-top:0px;padding-bottom:0;">
	<div class="row-fluid">
		<!-- blog post -->
		<div class="fullblog clearfix">
			<div class="news_full_blog span8">
				<?php if(have_posts()) : ?>
				<?php while(have_posts()) : the_post(); ?>
		<div class="inner_blog">
			<h2 class="skt_blog_title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php 
				$author_url    = get_author_posts_url(get_the_author_meta( 'ID' ));
				$author_nm     = get_the_author_meta('display_name',$post->post_author);
			?>

			<!--Post meta Details-->
			<div class="meta-details">
					<span class="author-name"><?php _e('by ','connexions-lite'); ?><a href="<?php echo esc_url($author_url); ?>" ><?php echo $author_nm; ?></a></span>									
					<span class="date-calendar"><?php the_time('F j, Y') ?></span>
					<span class="date-calendar"><?php the_tags('Tag ',',',''); ?></span>

			</div>
			<!--/End Post meta Details-->


			<div class="post" id="post-<?php the_ID(); ?>">

					<!--Standard-->
					<?php if( has_post_thumbnail() ) { ?>
						<div class="skt_blog_thumbnail">
							<?php the_post_thumbnail('connexions-lite-standard-thumb'); ?>
						</div>
					<?php } ?>
					<div class="blogtext">
						<?php the_content(); ?>
						<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages :','connexions-lite').'</strong>','after' => '</p>', __('number','connexions-lite'),)); ?>
						<?php edit_post_link(__('Edit','connexions-lite'), '', ''); ?>
					</div>

					<!--Post Details-->
					<div class="meta-details">
							<span class="post-comments"><i class="fa fa-comments-o"></i><?php comments_popup_link('No Comments ', '1 Comment ', '% Comments ') ; ?></span>
					</div>
					<!--/End Post meta Details-->

			</div>


		</div>
		<?php endwhile; ?>
		<!-- Page Navigation Section starts -->
		<?php if(get_adjacent_post(false, '', true) || get_adjacent_post(false, '', false)){ ?>
		<div class="page-navigation clearfix">
			<div class="alignleft"><?php next_post_link('&larr; %link') ?></div>
			<div class="alignright"><?php previous_post_link('%link &rarr;') ?></div>
		</div>
		<?php } ?>
		<!-- \\Page Navigation Section ends -->
		<div class="author-comment-section clearfix">
			<!-- Post Comments Section -->
			<div class="news_comments">
				<?php if ('open' == $post->comment_status){ ?><h2 class="black mb"><?php _e('COMMENTS','connexions-lite'); ?></h2><?php } ?>
				<?php comments_template(); ?>
			</div>
			<!-- \\Post Comments Section -->
		</div>
		<?php else : ?>
		<h2><?php _e('Not Found','connexions-lite'); ?></h2>
		<?php endif; ?>	
	</div>
	<!-- #Sidebar// -->
	<div id="sidebar" class="span4">
		<?php get_sidebar(); ?>
	</div>
	<!-- //#Sidebar -->
</div>
</div>
</div>
<!-- /Container--> 
<?php get_footer(); ?>