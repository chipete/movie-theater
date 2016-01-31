<?php

/*
Plugin Name: WP Movie Theater
Plugin URI: https://github.com/chipete/wp-movie-theater
Description: Custom post type “films” and “sessions” (with ticket links) which can then be displayed as a sortable list on a page, and also individually as posts.  Content can be automatically generated and updated from ticket server xml/json feed.
Version: 1.7.3
Author: Chris, Ryan
Author URI: http://lightmarkcreative.com
License: GPL2
*/


//=========================== Includes =======================//

require_once ( 'classes/class-wpmt-session.php' );
require_once ( 'classes/class-wpmt-film.php' );
require_once ( 'classes/class-wpmt-performance.php' );

require_once ( 'inc/custom-post-types.php' );
require_once ( 'inc/custom-fields.php' );
require_once ( 'inc/load-ticket-servers.php' );
require_once ( 'inc/settings-panel.php' );


//=========================== Actions & Filters ==============//


// when the plugin is activated, add runMovieTheater to the wp-cron schedule hourly:
register_activation_hook( __FILE__, 'wpmt_activation' );

function wpmt_activation() {
    wp_schedule_event( time(), 'hourly', 'wpmt_update_posts' );
}

add_action( 'wpmt_update_posts', 'wpmt_run' );

//clean the scheduler on deactivation:
register_deactivation_hook( __FILE__, 'wpmt_deactivation' );

function wpmt_deactivation() {
	wp_clear_scheduled_hook( 'wpmt_update_posts' );
}


//=========================== Functions ======================//

//main function that runs everything

function wpmt_run() {

    $my_token           = esc_attr( get_option( 'wpmt_veezi_token' ) );
    $veezi_access_token = 'VeeziAccessToken: ' . $my_token;
    $session_data       = call_service( 'https://api.us.veezi.com/v1/websession', $veezi_access_token );
    $film_data          = call_service( 'https://api.us.veezi.com/v1/film', $veezi_access_token );

    //1. delete all sessions
    wpmt_delete_all_posts( 'WPMT_Session' );

    //1-B. for testing, delete all films. This should be commented-out
    //wpmt_delete_all_posts( 'WPMT_Film' );

    //2. if all sessions have been deleted, add the new ones
    if ( NULL == get_posts(array('post_type'=> 'WPMT_Session')) ) {
        wpmt_add_sessions( $session_data );
    }

    //3. update all films
    wpmt_update_films( $film_data );
}


function wpmt_update_films( $film_data )
{
    $film_data_as_array = object_to_array( $film_data );

    for ( $i = 0; $i < count( $film_data_as_array ); $i++ ) {

        $film = new WPMT_Film;
        $film->assign_values( $film_data_as_array, $i );

        if ( ($film->status == "Inactive") || ($film->status == "Deleted") ) {

            $inactive_films = get_posts( array(
                'posts_per_page'=> -1,
                'post_type'		=> 'WPMT_Film',
                'meta_key'		=> 'wpmt_film_id',
                'meta_value'	=> $film->id
            ) );
            foreach ( $inactive_films as $my_post ) {
                wp_delete_post( $my_post->ID, true );
                // Set to False if you want to send them to Trash.
            }

        } elseif ( null == get_posts( array(
                'posts_per_page'=> -1,
                'post_type'		=> 'WPMT_Film',
                'meta_key'		=> 'wpmt_film_id',
                'meta_value'	=> $film->id
            ) ) )  {
            $post_id = wpmt_add_post( $film->title, 'WPMT_Film' ); //we should ultimately change the post type to Film rather than film
            $film->update_fields( $post_id );
        }

    } //end $film_data_as_array for loop

}


function wpmt_delete_all_posts( $post_type ) {

    // deletes all posts within a post_type
    $my_custom_posts = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1 ) );

    foreach( $my_custom_posts as $my_post ) {
        wp_delete_post( $my_post->ID, true );
        // Set to False if you want to send them to Trash.
    }
}


function wpmt_add_sessions( $session_data )
{
    $session_data_as_array = object_to_array( $session_data );

    for ( $i = 0; $i < count( $session_data_as_array ); $i++ ) {

        $session = new WPMT_Session;
        $session->assign_values( $session_data_as_array, $i );

        $post_id = wpmt_add_post( $session->title, 'WPMT_Session' );
        $session->update_fields( $post_id );
    }
}


function wpmt_add_post( $title, $post_type ) {
//helper function to add a new post

    // Setup the author
    $author_id = 1;

    $post_id = wp_insert_post( array(
        'comment_status'=>	'closed',
        'ping_status'	=>	'closed',
        'post_author'	=>	$author_id,
        'post_title'	=>	$title,
        'post_status'	=>	'publish',
        'post_type'		=>	$post_type
        ) );

    return $post_id;
}



