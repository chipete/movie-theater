<?php

/*
Plugin Name: Movie Theater
Plugin URI: http://lightmarkcreative.com/movietheater
Description: Custom post type “films” and “showtimes” (with ticket links) which can then be displayed as a sortable list on a page, and also individually as posts.  Content can be automatically generated and updated from ticket server xml/json feed.
Version: 1.6.1
Author: Chris, Ryan
Author URI: http://lightmarkcreative.com
License: GPL2
*/


//=========================== Includes =======================//

include('classes/movietheater_ShowTime.php');
include('classes/movietheater_Film.php');

require_once ('inc/custom-post-types.php');
include('inc/load-ticket-servers.php');
//include('inc/movieTheater_settingsPanel.php');


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

// ------------------------------------------------------------------
// Add all your sections, fields and settings during admin_init
// ------------------------------------------------------------------
//

add_action( 'admin_menu', 'my_plugin_menu' );
add_action( 'admin_init', 'eg_settings_api_init' );

function my_plugin_menu() {

    //add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
    add_options_page(
        'Movie Theater Control Panel',
        'Movie Theater CP',
        'manage_options',
        'movie',
        'movie_theater_admin_options'
    );
}
function movie_theater_admin_options()
{

    do_settings_sections( 'movie_theater_settings' );
}

function eg_settings_api_init() {
    // Add the section to reading settings so we can add our
    // fields to it
    add_settings_section(
        'movie_theater_advanced_options',
        'Movie Theater Advanced Settings',
        'eg_setting_section_callback_function',
        'movie_theater_settings'
    );

    // Add the field with the names and function to use for our new
    // settings, put it in our new section
    add_settings_field(
        'eg_movie_theater_checkbox',
        'Test Checkbox',
        'eg_movie_theater_checkbox_setting_callback_function',
        'movie_theater_settings',
        'movie_theater_advanced_options'
    );
    add_settings_field(
        'eg_movie_theater_text',
        'Test text field',
        'eg_movie_theater_text_setting_callback_function',
        'movie_theater_settings',
        'movie_theater_advanced_options'
    );

    // Register our setting so that $_POST handling is done for us and
    // our callback function just has to echo the <input>
    register_setting( 'movie_theater_settings', 'eg_setting_name' );
} // eg_settings_api_init()



// ------------------------------------------------------------------
// Settings section callback function
// ------------------------------------------------------------------
//
// This function is needed if we added a new section. This function
// will be run at the start of our section
//

function eg_setting_section_callback_function() {
    echo '<p>Below are some amazing options I know you are going to want to mess with</p>';
}

// ------------------------------------------------------------------
// Callback function for our example setting
// ------------------------------------------------------------------
//
// creates a checkbox true/false option. Other types are surely possible
//

function eg_movie_theater_checkbox_setting_callback_function() {
    echo '<input name="eg_movie_theater_checkbox" id="eg_movie_theater_checkbox" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'eg_movie_theater_checkbox' ), false ) . ' /> Explanation text';
}
function eg_movie_theater_text_setting_callback_function() {
    echo '<input name="eg_movie_theater_text" id="eg_movie_theater_text" type="text" value="fill me out" /> Explanation text';
    //since this is our last field time for the submit button!
    echo '<br /> <br />';
    echo '<input type="submit" value="Save Changes" />';
}


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



