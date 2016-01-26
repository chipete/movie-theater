<?php

/*
Plugin Name: Movie Theater
Plugin URI: http://lightmarkcreative.com/movietheater
Description: Custom post type “films” and “showtimes” (with ticket links) which can then be displayed as a sortable list on a page, and also individually as posts.  Content can be automatically generated and updated from ticket server xml/json feed.
Version: 1.4
Author: Chris, Ryan
Author URI: http://lightmarkcreative.com
License: GPL2
*/


//=========================== Includes =======================//

//require_once ('inc/cron-timeout-fix.php');
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


//add_action('init', 'runMovieTheater', 0);
//do_action('trial', $showTimeData);
//add_action( 'init', 'programmatically_create_post', 0 );

//=========================== Functions ======================//

//main function that runs everything

function runMovieTheater() {

    $myToken = "PxWHQDAzZEmWZ7s2HZYCCA2";
    $VeeziAccessToken = 'VeeziAccessToken: ' . $myToken;

    $showTimeData = callService('https://api.us.veezi.com/v1/websession', $VeeziAccessToken);

    deleteAllPosts('Showtime');

    //if all the showtimes have been deleted, add the new ones
    if(NULL == get_Posts(array('post_type'=> 'Showtime')) ) {
        addAllNewShowTimes($showTimeData);
    }
}


function deleteAllPosts($postType) {

    // deletes all posts within a postType, 5000 at a time
    $mycustomposts = get_posts( array( 'post_type' => $postType, 'posts_per_page' => 5000) );
    foreach( $mycustomposts as $mypost ) {
        wp_delete_post($mypost->ID, true);
        // Set to False if you want to send them to Trash.
    }
}


function makeNewPost($title, $postType) {

    // Setup the author, slug, and title for the post
    $author_id = 1;
    //$slug = 'sample-showtime-20';
    //$title = 'Dummy Showtime #20';
    //$postType = 'showtime';

    // If the page doesn't already exist, then create it
    //if(NULL == (get_page_by_title( $title, 'OBJECT', $postType )) ) {

        // Set the post ID so that we know the post was created successfully
        $post_id = wp_insert_post(
            array(
                'comment_status'	=>	'closed',
                'ping_status'		=>	'closed',
                'post_author'		=>	$author_id,
                //'post_name'		=>	$slug,
                'post_title'	=>	$title,
                'post_status'	=>	'publish',
                'post_type'		=>	'Showtime'
            )
        );
    //}
}


function addAllNewShowTimes($showTimeData)
{
    $showTimeDataAsArray = objectToArray($showTimeData);
    for ($i=0; $i< count($showTimeDataAsArray); $i++) {
        $showTime = new movietheater_ShowTime;
        $showTime->assignValues($showTimeDataAsArray, $i);
        makeNewPost($showTime->title, 'Showtime');

        //probably put the (addcustomfieldsinfo) things here
    }
}
