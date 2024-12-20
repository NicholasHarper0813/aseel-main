<?php get_header(); ?>
<div class="wrap">
	<header class="page-header">
		<?php if ( have_posts() ) : ?>
			<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'aseel' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
		<?php else : ?>
			<h1 class="page-title"><?php _e( 'Nothing Found', 'aseel' ); ?></h1>
		<?php endif; ?>
	</header>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/post/content', 'excerpt' );
			endwhile;

			the_posts_pagination( array(
				'prev_text' => aseel_get_svg( array( 'icon' => 'arrow-left' ) ) . '<span class="screen-reader-text">' . __( 'Previous page', 'aseel' ) . '</span>',
				'next_text' => '<span class="screen-reader-text">' . __( 'Next page', 'aseel' ) . '</span>' . aseel_get_svg( array( 'icon' => 'arrow-right' ) ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'aseel' ) . ' </span>',
			) );
		else : ?>
			<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'aseel' ); ?></p>
			<?php
				get_search_form();
		endif;
		?>
		</main>
	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer();
