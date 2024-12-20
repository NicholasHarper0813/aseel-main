<?php get_header(); ?>
<div class="wrap">
	<?php if ( have_posts() ) : ?>
		<header class="page-header">
			<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="taxonomy-description">', '</div>' );
			?>
		</header>
	<?php endif; ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) : ?>
			<?php 
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/post/content', get_post_format() );
			endwhile;

			the_posts_pagination( array(
				'prev_text' => aseel_get_svg( array( 'icon' => 'arrow-left' ) ) . '<span class="screen-reader-text">' . __( 'Previous page', 'aseel' ) . '</span>',
				'next_text' => '<span class="screen-reader-text">' . __( 'Next page', 'aseel' ) . '</span>' . aseel_get_svg( array( 'icon' => 'arrow-right' ) ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'aseel' ) . ' </span>',
			) );

		else :
			get_template_part( 'template-parts/post/content', 'none' );
		endif; ?>
		</main>
	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer();
