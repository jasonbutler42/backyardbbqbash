<?php

function bbq_scripts() {

	/* Add jQuery library. */
	wp_enqueue_script('jquery', true);

	/* Register theme stylesheets. */
	wp_register_style( 'style', get_stylesheet_uri());

	/* Register theme scripts. */
	wp_register_script( 'bbq', get_template_directory_uri() . '/bbq.js', array( 'jquery' ), true );


	/* Add theme scripts */
	wp_enqueue_script( 'bbq' );

	/* Add theme styles */
	wp_enqueue_style( 'style' );



}
add_action( 'wp_enqueue_scripts', 'bbq_scripts');



function bbq_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar' ),
		'id' => 'sidebar-1',
		'description' => __( 'Appears everywhere. only sidebar on site' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s panel panel-default">',
		'after_widget' => '</div></aside>',
		'before_title' => '<div class="widget-title panel-heading">',
		'after_title' => '</div><div class="list-group">',
	) );

	
}
add_action( 'widgets_init', 'bbq_widgets_init' );

/**
 * Filter the page title.
 *
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 */
function bbq_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name', 'display' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'bbq' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'bbq_wp_title', 10, 2 );
