<?php 
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) 
{
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

function aseel_setup() {
	load_theme_textdomain( 'aseel' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'aseel-featured-image', 2000, 1200, true );
	add_image_size( 'aseel-thumbnail-avatar', 100, 100, true );

	$GLOBALS['content_width'] = 525;
	register_nav_menus( array(
		'top'    => __( 'Top Menu', 'aseel' ),
		'social' => __( 'Social Links Menu', 'aseel' ),
	) );

	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'audio',
	) );

	add_theme_support( 'custom-logo', array(
		'width'       => 250,
		'height'      => 250,
		'flex-width'  => true,
	) );

	add_theme_support( 'customize-selective-refresh-widgets' );
	add_editor_style( array( 'assets/css/editor-style.css', aseel_fonts_url() ) );

	$starter_content = array(
		'widgets' => array(
			'sidebar-1' => array(
				'text_business_info',
				'search',
				'text_about',
			),

			'sidebar-2' => array(
				'text_business_info',
			),

			'sidebar-3' => array(
				'text_about',
				'search',
			),
		),

		'posts' => array(
			'home',
			'about' => array(
				'thumbnail' => '{{image-sandwich}}',
			),
			'contact' => array(
				'thumbnail' => '{{image-espresso}}',
			),
			'blog' => array(
				'thumbnail' => '{{image-coffee}}',
			),
			'homepage-section' => array(
				'thumbnail' => '{{image-espresso}}',
			),
		),

		'attachments' => array(
			'image-espresso' => array(
				'post_title' => _x( 'Espresso', 'Theme starter content', 'aseel' ),
				'file' => 'assets/images/espresso.jpg', // URL relative to the template directory.
			),
			'image-sandwich' => array(
				'post_title' => _x( 'Sandwich', 'Theme starter content', 'aseel' ),
				'file' => 'assets/images/sandwich.jpg',
			),
			'image-coffee' => array(
				'post_title' => _x( 'Coffee', 'Theme starter content', 'aseel' ),
				'file' => 'assets/images/coffee.jpg',
			),
		),

		'options' => array(
			'show_on_front' => 'page',
			'page_on_front' => '{{home}}',
			'page_for_posts' => '{{blog}}',
		),

		'theme_mods' => array(
			'panel_1' => '{{homepage-section}}',
			'panel_2' => '{{about}}',
			'panel_3' => '{{blog}}',
			'panel_4' => '{{contact}}',
		),

		'nav_menus' => array(
			'top' => array(
				'name' => __( 'Top Menu', 'aseel' ),
				'items' => array(
					'link_home',
					'page_about',
					'page_blog',
					'page_contact',
				),
			),

			'social' => array(
				'name' => __( 'Social Links Menu', 'aseel' ),
				'items' => array(
					'link_yelp',
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				),
			),
		),
	);

	$starter_content = apply_filters( 'aseel_starter_content', $starter_content );

	add_theme_support( 'starter-content', $starter_content );
}
add_action( 'after_setup_theme', 'aseel_setup' );

function aseel_content_width() 
{
	$content_width = $GLOBALS['content_width'];
	$page_layout = get_theme_mod( 'page_layout' );

	if ( 'one-column' === $page_layout )
	{
		if ( aseel_is_frontpage() ) 
		{
			$content_width = 644;
		} 
		elseif ( is_page() ) 
		{
			$content_width = 740;
		}
	}

	if ( is_single() && ! is_active_sidebar( 'sidebar-1' ) ) 
	{
		$content_width = 740;
	}

	$GLOBALS['content_width'] = apply_filters( 'aseel_content_width', $content_width );
}
add_action( 'template_redirect', 'aseel_content_width', 0 );

function aseel_fonts_url() 
{
	$fonts_url = '';
	$libre_franklin = _x( 'on', 'Libre Franklin font: on or off', 'aseel' );

	if ( 'off' !== $libre_franklin ) 
	{
		$font_families = array();
		$font_families[] = 'Libre Franklin:300,300i,400,400i,600,600i,800,800i';
		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

function aseel_resource_hints( $urls, $relation_type ) 
{
	if ( wp_style_is( 'aseel-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'aseel_resource_hints', 10, 2 );

function aseel_widgets_init()
{
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'aseel' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'aseel' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 1', 'aseel' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your footer.', 'aseel' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'aseel' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Add widgets here to appear in your footer.', 'aseel' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'aseel_widgets_init' );

function aseel_excerpt_more( $link )
{
	if ( is_admin() ) 
	{
		return $link;
	}

	$link = sprintf( '<p class="link-more"><a href="%1$s" class="more-link">%2$s</a></p>',
		esc_url( get_permalink( get_the_ID() ) ),
		sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'aseel' ), get_the_title( get_the_ID() ) )
	);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'aseel_excerpt_more' );

function aseel_javascript_detection() 
{
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'aseel_javascript_detection', 0 );

function aseel_pingback_header()
{
	if ( is_singular() && pings_open() ) 
	{
		printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
	}
}
add_action( 'wp_head', 'aseel_pingback_header' );

function aseel_colors_css_wrap() 
{
	if ( 'custom' !== get_theme_mod( 'colorscheme' ) && ! is_customize_preview() ) 
	{
		return;
	}

	require_once( get_parent_theme_file_path( '/inc/color-patterns.php' ) );
	$hue = absint( get_theme_mod( 'colorscheme_hue', 250 ) );
?>
	<style type="text/css" id="custom-theme-colors" <?php if ( is_customize_preview() ) { echo 'data-hue="' . $hue . '"'; } ?>>
		<?php echo aseel_custom_colors_css(); ?>
	</style>
<?php }
add_action( 'wp_head', 'aseel_colors_css_wrap' );

function aseel_scripts() 
{
	wp_enqueue_style( 'aseel-fonts', aseel_fonts_url(), array(), null );
	wp_enqueue_style( 'aseel-style', get_stylesheet_uri() );
	wp_enqueue_style( 'aseel-dist-style', get_theme_file_uri( '/dist/style.css' ));
	
	if(is_home()) 
	{
		wp_enqueue_script( 'aseel-script', get_stylesheet_directory_uri() . '/dist/app.js' , array(), '1.0', true );
	}

	if ( 'dark' === get_theme_mod( 'colorscheme', 'light' ) || is_customize_preview() ) 
	{
		wp_enqueue_style( 'aseel-colors-dark', get_theme_file_uri( '/assets/css/colors-dark.css' ), array( 'aseel-style' ), '1.0' );
	}

	if ( is_customize_preview() )
	{
		wp_enqueue_style( 'aseel-ie9', get_theme_file_uri( '/assets/css/ie9.css' ), array( 'aseel-style' ), '1.0' );
		wp_style_add_data( 'aseel-ie9', 'conditional', 'IE 9' );
	}

	wp_enqueue_style( 'aseel-ie8', get_theme_file_uri( '/assets/css/ie8.css' ), array( 'aseel-style' ), '1.0' );
	wp_style_add_data( 'aseel-ie8', 'conditional', 'lt IE 9' );
	
	wp_enqueue_script( 'html5', get_theme_file_uri( '/assets/js/html5.js' ), array(), '3.7.3' );
	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'aseel-skip-link-focus-fix', get_theme_file_uri( '/assets/js/skip-link-focus-fix.js' ), array(), '1.0', true );

	$aseel_l10n = array(
		'quote'          => aseel_get_svg( array( 'icon' => 'quote-right' ) ),
	);

	if ( has_nav_menu( 'top' ) )
	{
		wp_enqueue_script( 'aseel-navigation', get_theme_file_uri( '/assets/js/navigation.js' ), array( 'jquery' ), '1.0', true );
		$aseel_l10n['expand']         = __( 'Expand child menu', 'aseel' );
		$aseel_l10n['collapse']       = __( 'Collapse child menu', 'aseel' );
		$aseel_l10n['icon']           = aseel_get_svg( array( 'icon' => 'angle-down', 'fallback' => true ) );
	}

	wp_enqueue_script( 'aseel-global', get_theme_file_uri( '/assets/js/global.js' ), array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'jquery-scrollto', get_theme_file_uri( '/assets/js/jquery.scrollTo.js' ), array( 'jquery' ), '2.1.2', true );

	wp_localize_script( 'aseel-skip-link-focus-fix', 'aseelScreenReaderText', $aseel_l10n );

	wp_enqueue_script( 'scrollmagic', 'https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/ScrollMagic.min.js' , array(), '1.0', false );
	wp_enqueue_script( 'scrollmagic-indicators', 'https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/plugins/debug.addIndicators.min.js' , array(), '1.0', false );
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) 
	{
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'aseel_scripts' );

function aseel_content_image_sizes_attr( $sizes, $size ) 
{
	$width = $size[0];

	if ( 740 <= $width ) 
	{
		$sizes = '(max-width: 706px) 89vw, (max-width: 767px) 82vw, 740px';
	}

	if ( is_active_sidebar( 'sidebar-1' ) || is_archive() || is_search() || is_home() || is_page() ) 
	{
		if ( !(is_page() && 'one-column' === get_theme_mod( 'page_options' ) ) && 767 <= $width ) 
		{
			 $sizes = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
		}
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'aseel_content_image_sizes_attr', 10, 2 );

function aseel_header_image_tag( $html, $header, $attr )
{
	if ( isset( $attr['sizes'] ) )
	{
		$html = str_replace( $attr['sizes'], '100vw', $html );
	}
	return $html;
}
add_filter( 'get_header_image_tag', 'aseel_header_image_tag', 10, 3 );

function aseel_post_thumbnail_sizes_attr( $attr, $attachment, $size ) 
{
	if ( is_archive() || is_search() || is_home() ) 
	{
		$attr['sizes'] = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
	}
	else 
	{
		$attr['sizes'] = '100vw';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'aseel_post_thumbnail_sizes_attr', 10, 3 );

function aseel_front_page_template( $template ) 
{
	return is_home() ? '' : $template;
}
add_filter( 'frontpage_template',  'aseel_front_page_template' );

require get_parent_theme_file_path( '/inc/custom-header.php' );
require get_parent_theme_file_path( '/inc/template-tags.php' );
require get_parent_theme_file_path( '/inc/template-functions.php' );
require get_parent_theme_file_path( '/inc/customizer.php' );
require get_parent_theme_file_path( '/inc/icon-functions.php' );
