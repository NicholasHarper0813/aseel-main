<?php get_header(); ?>
<div class="wrap">
	<?php if ( is_home() && ! is_front_page() ) : ?>
		<header class="page-header">
			<h1 class="page-title"><?php single_post_title(); ?></h1>
		</header>
	<?php else : ?>
	<header class="page-header">
		<h2 class="page-title"><?php _e( 'Posts', 'aseel' ); ?></h2>
	</header>
	<?php endif; ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			if(!get_theme_mod( 'infinite_scroll' )):
			if ( have_posts() ) :
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
			endif;
			else: ?>
			<div id="app-container"></div>
			<?php endif; ?>
		</main>
	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer();
