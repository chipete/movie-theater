<?php

/*
Plugin Name: Movie Theater
Plugin URI: http://lightmarkcreative.com/movietheater
Description: Custom post type “films” and “showtimes” (with ticket links) which can then be displayed as a sortable list on a page, and also individually as posts.  Content can be automatically generated and updated from ticket server xml/json feed.
Version: 1.0
Author: Chris
Author URI: http://lightmarkcreative.com
License: A "Slug" license name e.g. GPL2
*/

/*
* Films CPT
*/
function custom_post_type() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Film', 'Post Type General Name', 'twentythirteen' ),
		'singular_name'       => _x( 'Film', 'Post Type Singular Name', 'twentythirteen' ),
		'menu_name'           => __( 'Films', 'twentythirteen' ),
		'parent_item_colon'   => __( 'Parent Film', 'twentythirteen' ),
		'all_items'           => __( 'All Films', 'twentythirteen' ),
		'view_item'           => __( 'View Film', 'twentythirteen' ),
		'add_new_item'        => __( 'Add New Film', 'twentythirteen' ),
		'add_new'             => __( 'Add New', 'twentythirteen' ),
		'edit_item'           => __( 'Edit Film', 'twentythirteen' ),
		'update_item'         => __( 'Update Film', 'twentythirteen' ),
		'search_items'        => __( 'Search Film', 'twentythirteen' ),
		'not_found'           => __( 'Not Found', 'twentythirteen' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
	);

// Set other options for Custom Post Type

	$args = array(
		'label'               => __( 'film', 'twentythirteen' ),
		'description'         => __( 'Films', 'twentythirteen' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
		// You can associate this CPT with a taxonomy or custom taxonomy.
		'taxonomies'          => array( 'genres' ),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);

	// Registering your Custom Post Type
	register_post_type( 'film', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'custom_post_type', 0 );


/*
* Showtimes CPT
*/
function custom_post_type2() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Showtime', 'Post Type General Name', 'twentythirteen' ),
		'singular_name'       => _x( 'Showtime', 'Post Type Singular Name', 'twentythirteen' ),
		'menu_name'           => __( 'Showtimes', 'twentythirteen' ),
		'parent_item_colon'   => __( 'Parent Showtime', 'twentythirteen' ),
		'all_items'           => __( 'All Showtimes', 'twentythirteen' ),
		'view_item'           => __( 'View Showtime', 'twentythirteen' ),
		'add_new_item'        => __( 'Add New Showtime', 'twentythirteen' ),
		'add_new'             => __( 'Add New', 'twentythirteen' ),
		'edit_item'           => __( 'Edit Showtime', 'twentythirteen' ),
		'update_item'         => __( 'Update Showtime', 'twentythirteen' ),
		'search_items'        => __( 'Search Showtime', 'twentythirteen' ),
		'not_found'           => __( 'Not Found', 'twentythirteen' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
	);

// Set other options for Custom Post Type

	$args = array(
		'label'               => __( 'Showtime', 'twentythirteen' ),
		'description'         => __( 'Showtimes', 'twentythirteen' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
		// You can associate this CPT with a taxonomy or custom taxonomy.
		'taxonomies'          => array( 'genres' ),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);

	// Registering your Custom Post Type
	register_post_type( 'showtime', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'custom_post_type2', 0 );