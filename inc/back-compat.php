<?php 
function aseel_switch_theme() {
	switch_theme( WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'aseel_upgrade_notice' );
}
add_action( 'after_switch_theme', 'aseel_switch_theme' );

function aseel_upgrade_notice() 
{
	$message = sprintf( __( 'Aseel requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'aseel' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );
}

function aseel_customize() 
{
	wp_die( sprintf( __( 'Aseel requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'aseel' ), $GLOBALS['wp_version'] ), '', array(
		'back_link' => true,
	) );
}
add_action( 'load-customize.php', 'aseel_customize' );

function aseel_preview() 
{
	if ( isset( $_GET['preview'] ) ) {
		wp_die( sprintf( __( 'Aseel requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'aseel' ), $GLOBALS['wp_version'] ) );
	}
}
add_action( 'template_redirect', 'aseel_preview' );
