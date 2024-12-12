<?php 
function aseel_customize_register( $wp_customize ) 
{
	$wp_customize->get_setting( 'blogname' )->transport          = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport   = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport  = 'postMessage';

	$wp_customize->selective_refresh->add_partial( 'blogname', array(
		'selector' => '.site-title a',
		'render_callback' => 'aseel_customize_partial_blogname',
	) );
	$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
		'selector' => '.site-description',
		'render_callback' => 'aseel_customize_partial_blogdescription',
	) );

	$wp_customize->add_setting( 'colorscheme', array(
		'default'           => 'light',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'aseel_sanitize_colorscheme',
	) );

	$wp_customize->add_setting( 'colorscheme_hue', array(
		'default'           => 250,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'absint', // The hue is stored as a positive integer.
	) );

	$wp_customize->add_control( 'colorscheme', array(
		'type'    => 'radio',
		'label'    => __( 'Color Scheme', 'aseel' ),
		'choices'  => array(
			'light'  => __( 'Light', 'aseel' ),
			'dark'   => __( 'Dark', 'aseel' ),
			'custom' => __( 'Custom', 'aseel' ),
		),
		'section'  => 'colors',
		'priority' => 5,
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'colorscheme_hue', array(
		'mode' => 'hue',
		'section'  => 'colors',
		'priority' => 6,
	) ) );

	$wp_customize->add_section( 'theme_options', array(
		'title'    => __( 'Theme Options', 'aseel' ),
		'priority' => 130, // Before Additional CSS.
	) );

	$wp_customize->add_setting( 'page_layout', array(
		'default'           => 'two-column',
		'sanitize_callback' => 'aseel_sanitize_page_layout',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( 'page_layout', array(
		'label'       => __( 'Page Layout', 'aseel' ),
		'section'     => 'theme_options',
		'type'        => 'radio',
		'description' => __( 'When the two-column layout is assigned, the page title is in one column and content is in the other.', 'aseel' ),
		'choices'     => array(
			'one-column' => __( 'One Column', 'aseel' ),
			'two-column' => __( 'Two Column', 'aseel' ),
		),
		'active_callback' => 'aseel_is_view_with_layout_option',
	) );

	$num_sections = apply_filters( 'aseel_front_page_sections', 4 );
	for ( $i = 1; $i < ( 1 + $num_sections ); $i++ ) 
	{
		$wp_customize->add_setting( 'panel_' . $i, array(
			'default'           => false,
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control( 'panel_' . $i, array(
			/* translators: %d is the front page section number */
			'label'          => sprintf( __( 'Front Page Section %d Content', 'aseel' ), $i ),
			'description'    => ( 1 !== $i ? '' : __( 'Select pages to feature in each area from the dropdowns. Add an image to a section by setting a featured image in the page editor. Empty sections will not be displayed.', 'aseel' ) ),
			'section'        => 'theme_options',
			'type'           => 'dropdown-pages',
			'allow_addition' => true,
			'active_callback' => 'aseel_is_static_front_page',
		) );

		$wp_customize->selective_refresh->add_partial( 'panel_' . $i, array(
			'selector'            => '#panel' . $i,
			'render_callback'     => 'aseel_front_page_section',
			'container_inclusive' => true,
		) );
	}
}
add_action( 'customize_register', 'aseel_customize_register' );

function aseel_sanitize_page_layout( $input ) 
{
	$valid = array(
		'one-column' => __( 'One Column', 'aseel' ),
		'two-column' => __( 'Two Column', 'aseel' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	}

	return '';
}

function aseel_sanitize_colorscheme( $input ) {
	$valid = array( 'light', 'dark', 'custom' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return 'light';
}

function aseel_customize_partial_blogname() {
	bloginfo( 'name' );
}

function aseel_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

function aseel_is_static_front_page() {
	return ( is_front_page() && ! is_home() );
}

function aseel_is_view_with_layout_option() {
	// This option is available on all pages. It's also available on archives when there isn't a sidebar.
	return ( is_page() || ( is_archive() && ! is_active_sidebar( 'sidebar-1' ) ) );
}

function aseel_customize_preview_js()
{
	wp_enqueue_script( 'aseel-customize-preview', get_theme_file_uri( '/assets/js/customize-preview.js' ), array( 'customize-preview' ), '1.0', true );
}
add_action( 'customize_preview_init', 'aseel_customize_preview_js' );

function aseel_panels_js() 
{
	wp_enqueue_script( 'aseel-customize-controls', get_theme_file_uri( '/assets/js/customize-controls.js' ), array(), '1.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'aseel_panels_js' );

function aseel_posts_infinite_scroll ( WP_Customize_Manager $wp_customize ) 
{
	$wp_customize->add_section( 'posts_infinite_scroll_section' , array(
		'title'       => __( 'Infinite Scrolling / Pagination'),
		'priority'    => 30,
		'description' => 'Posts display mode.',
	) );
	$wp_customize->add_setting( 'infinite_scroll', array(
        'default'    => true
    ) );
	$wp_customize->add_control(
    new WP_Customize_Control(
        $wp_customize,
        'infinite_scroll',
			array(
				'label'     => __('Select how to display your posts', 'aseel'),
				'section'   => 'posts_infinite_scroll_section',
				'type'      => 'radio',
				'choices' => array(
					true => __( 'Infinite scrolling' ),
					false => __( 'Pagination' )
				),
			)
		)
	);
	$wp_customize->get_setting( 'infinite_scroll'  )->transport = 'postMessage';
}
add_action( 'customize_register', 'aseel_posts_infinite_scroll' );

