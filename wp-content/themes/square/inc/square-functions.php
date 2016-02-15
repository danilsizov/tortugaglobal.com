<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Square
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function square_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'square_body_classes' );

if( !function_exists( 'square_excerpt' ) ){
	function square_excerpt( $content , $letter_count ){
		$content = strip_shortcodes( $content );
		$content = strip_tags( $content );
		$content = substr( $content, 0 , $letter_count );

		if( strlen( $content ) == $letter_count ){
			$content .= "...";
		}
		return $content;
	}
}

add_filter( 'wp_page_menu_args' , 'square_change_wp_page_menu_args');

if( !function_exists( 'square_change_wp_page_menu_args' ) ){
	function square_change_wp_page_menu_args( $args ){
		$args['menu_class'] = 'sq-menu sq-clearfix';	
		return $args;
	}
}

function square_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] )  ? 'parent' : '', $comment ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
					<?php echo sprintf( '<b class="fn">%s</b>', get_comment_author_link( $comment )  ); ?>
				</div><!-- .comment-author -->

				<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'square' ); ?></p>
				<?php endif; ?>
				<?php edit_comment_link( __( 'Edit', 'square' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .comment-meta -->

			<div class="comment-content">
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

			<div class="comment-metadata sq-clearfix">
				<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
					<time datetime="<?php comment_time( 'c' ); ?>">
						<?php
							/* translators: 1: comment date, 2: comment time */
							printf( __( '%1$s at %2$s', 'square' ), get_comment_date( '', $comment ), get_comment_time() );
						?>
					</time>
				</a>

				<?php
				comment_reply_link( array_merge( $args, array(
					'add_below' => 'div-comment',
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
					'before'    => '<div class="reply">',
					'after'     => '</div>'
				) ) );
				?>
			</div><!-- .comment-metadata -->
		</article><!-- .comment-body -->
<?php
}