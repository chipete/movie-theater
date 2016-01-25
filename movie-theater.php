<?php

/*
Plugin Name: Movie Theater
Plugin URI: http://lightmarkcreative.com/movietheater
Description: Custom post type “films” and “showtimes” (with ticket links) which can then be displayed as a sortable list on a page, and also individually as posts.  Content can be automatically generated and updated from ticket server xml/json feed.
Version: 1.1
Author: Chris
Author URI: http://lightmarkcreative.com
License: A "Slug" license name e.g. GPL2
*/

/*
 * testing the github repository 1.2
 */

/*
* Load ShowTime and Film Custom Post Types
*/

require_once ('inc/custom-post-types.php');


// Test loading data into a showtime

/**
 * A function used to programmatically create a post in WordPress. The slug, author ID, and title
 * are defined within the context of the function.
 *
 * @returns -1 if the post was never created, -2 if a post with the same title exists, or the ID
 *          of the post if successful.
 */

function programatically_flush_posts() {

	// deletes all posts within a postType

	//$postType = 'showtime';

}

function programmatically_create_post() {

	// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;

	// Setup the author, slug, and title for the post
	$author_id = 1;
	$slug = 'sample-showtime-2';
	$title = 'Dummy Showtime #2';
	$postType = 'showtime';

	// If the page doesn't already exist, then create it
	if( null == get_page_by_title( $title, 'OBJECT', $postType ) ) {

		// Set the post ID so that we know the post was created successfully
		$post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_name'		=>	$slug,
				'post_title'	=>	$title,
				'post_status'	=>	'publish',
				'post_type'		=>	'showtime'
			)
		);

		// Otherwise, we'll stop
	} else {

		// Arbitrarily use -2 to indicate that the page with the title already exists
		$post_id = -2;

	} // end if

} // end programmatically_create_post
add_action( 'init', 'programmatically_create_post', 0 );

//The various calls to this function would look like this:

//$post_id = programmatically_create_post();
//if( -1 == $post_id || -2 == $post_id ) {
	// The post wasn't created or the page already exists
//} // end if