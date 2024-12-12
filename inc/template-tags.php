<?php 
if ( ! function_exists( 'aseel_posted_on' ) ) :
function aseel_posted_on() 
{
	$byline = sprintf(
		/* translators: %s: post author */
		__( 'by %s', 'aseel' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author() . '</a></span>'
	);
	echo '<span class="posted-on">' . aseel_time_link() . '</span><span class="byline"> ' . $byline . '</span>';
}
endif;

if ( ! function_exists( 'aseel_time_link' ) ) :
function aseel_time_link() 
{
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) 
	{
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		get_the_date( DATE_W3C ),
		get_the_date(),
		get_the_modified_date( DATE_W3C ),
		get_the_modified_date()
	);

	return sprintf(
		/* translators: %s: post date */
		__( '<span class="screen-reader-text">Posted on</span> %s', 'aseel' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);
}
endif;


if ( ! function_exists( 'aseel_entry_footer' ) ) :
function aseel_entry_footer() 
{
	$separate_meta = __( ', ', 'aseel' );
	$categories_list = get_the_category_list( $separate_meta );
	$tags_list = get_the_tag_list( '', $separate_meta );

	if ( ( ( aseel_categorized_blog() && $categories_list ) || $tags_list ) || get_edit_post_link() ) 
	{
		echo '<footer class="entry-footer">';
			if ( 'post' === get_post_type() ) 
			{
				if ( ( $categories_list && aseel_categorized_blog() ) || $tags_list ) 
				{
					echo '<span class="cat-tags-links">';
						if ( $categories_list && aseel_categorized_blog() ) 
						{
							echo '<span class="cat-links">' . aseel_get_svg( array( 'icon' => 'folder-open' ) ) . '<span class="screen-reader-text">' . __( 'Categories', 'aseel' ) . '</span>' . $categories_list . '</span>';
						}
						if ( $tags_list ) 
						{
							echo '<span class="tags-links">' . aseel_get_svg( array( 'icon' => 'hashtag' ) ) . '<span class="screen-reader-text">' . __( 'Tags', 'aseel' ) . '</span>' . $tags_list . '</span>';
						}

					echo '</span>';
				}
			}

			aseel_edit_link();

		echo '</footer> <!-- .entry-footer -->';
	}
}
endif;


if ( ! function_exists( 'aseel_edit_link' ) ) :
/**
 * Returns an accessibility-friendly link to edit a post or page.
 *
 * This also gives us a little context about what exactly we're editing
 * (post or page?) so that users understand a bit more where they are in terms
 * of the template hierarchy and their content. Helpful when/if the single-page
 * layout with multiple posts/pages shown gets confusing.
 */
function aseel_edit_link() {
	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'aseel' ),
			get_the_title()
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Display a front page section.
 *
 * @param WP_Customize_Partial $partial Partial associated with a selective refresh request.
 * @param integer              $id Front page section to display.
 */
function aseel_front_page_section( $partial = null, $id = 0 ) {
	if ( is_a( $partial, 'WP_Customize_Partial' ) ) {
		// Find out the id and set it up during a selective refresh.
		global $aseelcounter;
		$id = str_replace( 'panel_', '', $partial->id );
		$aseelcounter = $id;
	}

	global $post; // Modify the global post object before setting up post data.
	if ( get_theme_mod( 'panel_' . $id ) ) {
		$post = get_post( get_theme_mod( 'panel_' . $id ) );
		setup_postdata( $post );
		set_query_var( 'panel', $id );

		get_template_part( 'template-parts/page/content', 'front-page-panels' );

		wp_reset_postdata();
	} elseif ( is_customize_preview() ) {
		// The output placeholder anchor.
		echo '<article class="panel-placeholder panel aseel-panel aseel-panel' . $id . '" id="panel' . $id . '"><span class="aseel-panel-title">' . sprintf( __( 'Front Page Section %1$s Placeholder', 'aseel' ), $id ) . '</span></article>';
	}
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function aseel_categorized_blog() {
	$category_count = get_transient( 'aseel_categories' );

	if ( false === $category_count ) {
		// Create an array of all the categories that are attached to posts.
		$categories = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$category_count = count( $categories );

		set_transient( 'aseel_categories', $category_count );
	}

	// Allow viewing case of 0 or 1 categories in post preview.
	if ( is_preview() ) {
		return true;
	}

	return $category_count > 1;
}


/**
 * Flush out the transients used in aseel_categorized_blog.
 */
function aseel_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'aseel_categories' );
}
add_action( 'edit_category', 'aseel_category_transient_flusher' );
add_action( 'save_post',     'aseel_category_transient_flusher' );
