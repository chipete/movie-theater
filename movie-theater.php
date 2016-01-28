<?php

/*
Plugin Name: Movie Theater
Plugin URI: http://lightmarkcreative.com/movietheater
Description: Custom post type “films” and “showtimes” (with ticket links) which can then be displayed as a sortable list on a page, and also individually as posts.  Content can be automatically generated and updated from ticket server xml/json feed.
Version: 1.6.0
Author: Chris, Ryan
Author URI: http://lightmarkcreative.com
License: GPL2
*/


//=========================== Includes =======================//

include('classes/movietheater_ShowTime.php');
include('classes/movietheater_Film.php');

require_once ('inc/custom-post-types.php');
include('inc/load-ticket-servers.php');



//=========================== Actions & Filters ==============//


// when the plugin is activated, add runMovieTheater to the wp-cron schedule hourly:

register_activation_hook(__FILE__, 'my_activation');

function my_activation() {
    wp_schedule_event(time(), 'hourly', 'my_hourly_event');
}

add_action('my_hourly_event', 'runMovieTheater');

//clean the scheduler on deactivation:

register_deactivation_hook(__FILE__, 'my_deactivation');

function my_deactivation() {
	wp_clear_scheduled_hook('my_hourly_event');
}


//=========================== Functions ======================//

//main function that runs everything

function runMovieTheater() {

    $myToken = "PxWHQDAzZEmWZ7s2HZYCCA2";
    $VeeziAccessToken = 'VeeziAccessToken: ' . $myToken;

    $showTimeData = callService('https://api.us.veezi.com/v1/websession', $VeeziAccessToken);
    $filmData = callService('https://api.us.veezi.com/v1/film', $VeeziAccessToken);

    updateAllFilms($filmData);
    deleteAllPosts('Showtime');

    //if all the showtimes have been deleted, add the new ones
    if(NULL == get_Posts(array('post_type'=> 'Showtime')) ) {
    addAllNewShowTimes($showTimeData);
    }
}

function updateAllFilms($filmData)
{
    $filmDataAsArray = objectToArray($filmData);
    for ($i=0; $i< count($filmDataAsArray); $i++) {
        $film = new movietheater_Film;
        $film->assignValues($filmDataAsArray, $i);
        if (($film->status == "Inactive") || ($film->status == "Deleted"))
        {
            $inactiveFilms = get_posts(array(
                'numberposts'	=> -1,
                'post_type'		=> 'film',
                'meta_key'		=> 'id',
                'meta_value'	=> $film->id
            ));
            foreach( $inactiveFilms as $mypost ) {
                wp_delete_post($mypost->ID, true);
                // Set to False if you want to send them to Trash.
            }
        }
        elseif (null == get_posts(array(
                'numberposts'	=> -1,
                'post_type'		=> 'film',
                'meta_key'		=> 'id',
                'meta_value'	=> $film->id
            )))
        {
            $post_id = makeNewPost($film->title, 'film'); //we should ultimately change the post type to Film rather than film
            $film->updateFilmFields($post_id);
        }

    }

}


function deleteAllPosts($postType) {

    // deletes all posts within a postType
    $mycustomposts = get_posts( array( 'post_type' => $postType, 'numberposts' => -1) );
    foreach( $mycustomposts as $mypost ) {
        wp_delete_post($mypost->ID, true);
        // Set to False if you want to send them to Trash.
    }
}


function addAllNewShowTimes($showTimeData)
{
    $showTimeDataAsArray = objectToArray($showTimeData);

    for ($i=0; $i< count($showTimeDataAsArray); $i++) {

        $showTime = new movietheater_ShowTime;
        $showTime->assignValues($showTimeDataAsArray, $i);

        $post_id = makeNewPost($showTime->title, 'Showtime');
        $showTime->updateShowtimeFields($post_id);
    }
}


function makeNewPost($title, $postType) {
//helper function to add a new post

    // Setup the author
    $author_id = 1;

    $post_id = wp_insert_post(
        array(
            'comment_status'	=>	'closed',
            'ping_status'		=>	'closed',
            'post_author'		=>	$author_id,
            'post_title'	=>	$title,
            'post_status'	=>	'publish',
            'post_type'		=>	$postType
        )
    );

    return $post_id;
}



