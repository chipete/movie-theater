<?php


function wpmt_custom_post_types() {

	//=========== WPMT_Session =============//
	// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => __( 'Session', 'Post Type General Name' ),
		'singular_name'       => __( 'Session', 'Post Type Singular Name' ),
		'menu_name'           => __( 'Sessions' ),
		'parent_item_colon'   => __( 'Parent Session' ),
		'all_items'           => __( 'All Sessions' ),
		'view_item'           => __( 'View Session' ),
		'add_new_item'        => __( 'Add New Session' ),
		'add_new'             => __( 'Add New' ),
		'edit_item'           => __( 'Edit Session' ),
		'update_item'         => __( 'Update Session' ),
		'search_items'        => __( 'Search Session' ),
		'not_found'           => __( 'Not Found' ),
		'not_found_in_trash'  => __( 'Not found in Trash' ),
	);

	// Set other options for Custom Post Type
	$args = array(
		'label'               => __( 'Session' ),
		'description'         => __( 'Sessions' ),
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
		'menu_position'       => 4,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);

	// Registering your Custom Post Type
	register_post_type( 'WPMT_Session', $args );


	//=========== WPMT_Film ================//
	// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => __( 'Film', 'Post Type General Name' ),
		'singular_name'       => __( 'Film', 'Post Type Singular Name' ),
		'menu_name'           => __( 'Films' ),
		'parent_item_colon'   => __( 'Parent Film' ),
		'all_items'           => __( 'All Films' ),
		'view_item'           => __( 'View Film' ),
		'add_new_item'        => __( 'Add New Film' ),
		'add_new'             => __( 'Add New' ),
		'edit_item'           => __( 'Edit Film' ),
		'update_item'         => __( 'Update Film' ),
		'search_items'        => __( 'Search Film' ),
		'not_found'           => __( 'Not Found' ),
		'not_found_in_trash'  => __( 'Not found in Trash' ),
	);

	// Set other options for Custom Post Type
	$args = array(
		'label'               => __( 'Film' ),
		'description'         => __( 'Films' ),
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
	register_post_type( 'WPMT_Film', $args );


	//=========== WPMT_Performance ==========//
	// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => __( 'Performance', 'Post Type General Name' ),
		'singular_name'       => __( 'Performance', 'Post Type Singular Name' ),
		'menu_name'           => __( 'Performances' ),
		'parent_item_colon'   => __( 'Parent Performance' ),
		'all_items'           => __( 'All Performances' ),
		'view_item'           => __( 'View Performance' ),
		'add_new_item'        => __( 'Add New Performance' ),
		'add_new'             => __( 'Add New' ),
		'edit_item'           => __( 'Edit Performance' ),
		'update_item'         => __( 'Update Performance' ),
		'search_items'        => __( 'Search Performance' ),
		'not_found'           => __( 'Not Found' ),
		'not_found_in_trash'  => __( 'Not found in Trash' ),
	);

	// Set other options for Custom Post Type
	$args = array(
		'label'               => __( 'Performance' ),
		'description'         => __( 'Performances' ),
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
	register_post_type( 'WPMT_Performance', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'wpmt_custom_post_types', 0 );