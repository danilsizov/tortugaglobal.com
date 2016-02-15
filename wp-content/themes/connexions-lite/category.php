<?php
/**
 * The template for displaying Category Archive pages.
 * @package WordPress
 * @SketchThemes
 */
?>
<?php
get_header(); 
?>

<!-- Container-->
	<div class="container clearfix" style="padding-top:0px;padding-bottom:0;">
		<div class="row-fluid">
			<!-- blog post -->
				<div class="fullblog clearfix">
					<div class="news_full_blog span8">
						<?php
							if(have_posts()) :
							while(have_posts()) : the_post();
						?>
						<?php get_template_part( 'content', get_post_format() ); ?>
						<?php endwhile; ?>
						<!-- Page Navigation Section starts -->
						
							<div class="navigation blog-navigation">	
								<div class="alignleft"><?php previous_posts_link(__('&larr;Previous','connexions-lite')) ?></div>		
								<div class="alignright"><?php next_posts_link(__('Next&rarr;','connexions-lite'),'') ?></div>    		
							</div>  
							
						<!-- \\Page Navigation Section ends -->
					<?php else : ?>
					<h2><?php _e('Apologies, but no results were found for the requested archive.','connexions-lite'); ?></h2>
					<?php endif; ?>		
					<!-- /end blog post -->
				</div>
				<!-- #Sidebar// -->
				<div id="sidebar" class="span4">
					<?php get_sidebar(); ?>
				</div>
				<!-- //#Sidebar -->
			</div> <!-- //fullblog -->
		</div>
  </div>
  <!-- /Container--> 
</div>
<!--/Blog --> 
<?php
get_footer();
?>