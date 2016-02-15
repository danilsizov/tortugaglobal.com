<?php

/**
 * The default template for displaying content. Used for both single and index/archive/search.
*/

?>
<div <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">

	<div class="inner_blog">
	<?php if(is_sticky($post->ID)) { _e("<div class='sticky-post'>Featured</div>",'connexions-lite'); } ?>
			<?php
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),'connexions-lite-standard-thumb');
				}else{
					$thumbnail = 0;
				}
			?>
			<h2 class="skt_blog_title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

			<?php 
				$author_url    = get_author_posts_url(get_the_author_meta( 'ID' ));
				$author_nm     = get_the_author_meta('display_name',$post->post_author);
			?>

			<!--Post meta Details-->
			<div class="meta-details">
					<span class="author-name"><?php _e('by ','connexions-lite'); ?><a href="<?php echo esc_url($author_url); ?>" ><?php echo $author_nm; ?></a></span>
					<span class="date-calendar"><?php the_time('F j, Y') ?></span>
			</div>
			<!--/End Post meta Details-->

			<?php if($thumbnail){ ?><div class="skt_blog_thumbnail"><img src="<?php echo $thumbnail[0]; ?>" alt="<?php the_title(); ?>" class="featured-image alignnon" /></div><?php } ?>								

			<div class="blogtext">
					<?php
				 		$post_content = get_the_excerpt();
				 		echo connexions_lite_limit_words($post_content, 40); 
				  	?>
					<a class="btn_readmore" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php _e('readmore','connexions-lite'); ?></a>
			</div>

			<!--Post Details-->
			<div class="meta-details">
					<span class="post-comments"><i class="fa fa-comments-o"></i><?php comments_popup_link('No Comments ', '1 Comment ', '% Comments ') ; ?></span>
			</div>
			<!--/End Post meta Details-->
	</div>		

</div>
<!-- post -->