<?php
/**
 * The sidebar containing the secondary widget area, displays on posts.
 * If no active widgets in this sidebar, it will be hidden completely.
 */	
?>
<div id="sidebar" class="ske_widget">
	<ul class="skeside">
		<?php dynamic_sidebar( 'Blog Sidebar' ); ?>
	</ul>
</div>
<!-- #sidebar .skt_widget -->