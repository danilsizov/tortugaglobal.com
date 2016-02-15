<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Square
 */

if ( ! function_exists( 'square_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function square_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( '%s', 'post date', 'square' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'square' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	$comment_count = get_comments_number(); // get_comments_number returns only a numeric value

	if ( comments_open() ) {
		if ( $comment_count == 0 ) {
			$comments = __('No Comments', 'square' );
		} elseif ( $comment_count > 1 ) {
			$comments = $comment_count . __(' Comments', 'square' );
		} else {
			$comments = __('1 Comment', 'square' );
		}
		$comment_link = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
	}else{
		$comment_link = __(' Comment Closed', 'square' );
	}

	echo '<span class="posted-on"><i class="fa fa-clock-o"></i>' . $posted_on . '</span><span class="byline"> ' . $byline . '</span><span class="comment-count"><i class="fa fa-comments-o"></i>' . $comment_link ."</span>"; // WPCS: XSS OK.

}
endif;

if ( ! function_exists( 'square_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function square_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'square' ) );
		if ( $categories_list && square_categorized_blog() ) {
			printf( '<span class="cat-links"><i class="fa fa-folder"></i>' . esc_html__( '%1$s', 'square' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'square' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links"><i class="fa fa-tag"></i>' . esc_html__( '%1$s', 'square' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function square_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'square_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'square_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so square_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so square_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in square_categorized_blog.
 */
function square_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'square_categories' );
}
add_action( 'edit_category', 'square_category_transient_flusher' );
add_action( 'save_post',     'square_category_transient_flusher' );
