<?php

/*
Plugin Name: Movie Theater
Plugin URI: http://lightmarkcreative.com/movietheater
Description: Custom post type “films” and “showtimes” (with ticket links) which can then be displayed as a sortable list on a page, and also individually as posts.  Content can be automatically generated and updated from ticket server xml/json feed.
Version: 1.3
Author: Chris, Ryan
Author URI: http://lightmarkcreative.com
License: A "Slug" license name e.g. GPL2
*/

/*
 * Version 1.3 Includes the following
 * ==================================
 * Adds test information to WP database
 * Merged data from old program veezi-film.php which:
 * 		- Contacts veezi to obtain stdObject data for Film and Showtime
 * 		- Converts stdObject to multidimensional arrays
 * 		- Contains functions that store information into classes with Objects film and showtime
 * 		- Has the ability to display all information received from Veezi (currently commented out)
 */

/*
* Load ShowTime and Film Custom Post Types
*/
////////////////////////////// Includes  //////////////////////////////////

include('classes/ShowTime.php');
include('classes/Film.php');
require_once ('inc/custom-post-types.php');

///////////////////////////////////////////////////////////////////////////


/////////////////////////// Configuration /////////////////////////////////
/*
 * Currently, you must change $myToken to your
 * Veezi API token (found in your Veezi account)
 * for this plugin to work
 */
$myToken = "PxWHQDAzZEmWZ7s2HZYCCA2";

//////////////////////////////////////////////////////////////////////////



/////////////////////////// Main Execution ////////////////////////////////
/**
 * Call web service that returns json using file_get_contents.
 *
 * You PHP installation must have php_openssl extension enabled.
 *
 * example: callService('http://myhost/myservice', 'X-Api-Key: mykey', 'X-Extra-Header: header-value')
 *
 * @param        $url             URL to call.
 * @param        $headers Headers to supply, in format 'Header1: value1', 'Header2: value2', ...
 * @return       mixed            JSON coverted into a PHP variable.
 */

$VeeziAccessToken = 'VeeziAccessToken: ' . $myToken;
$showTimeData = callService('https://api.us.veezi.com/v1/websession', $VeeziAccessToken);
$filmData = callService('https://api.us.veezi.com/v1/film', $VeeziAccessToken);

/* converts data to an Array from array = (stdObject) format
 * Calling function objectToArray($data)
 * This is important because class function assignValues($dataAsArray, $key)
 * requires array data
*/



//echo "<div id='container'>";
//echo "<div class='generalTitle'> All Show Times </div>";
//$showTimeDataAsArray = objectToArray($showTimeData);
//displayAllShowTimes($showTimeDataAsArray);
//echo "</div>";
//echo "<div id='container'>";
//echo "<div class='generalTitle'> All Films </div>";
//$film = new Film;
//$filmDataAsArray = objectToArray($filmData);
//displayAllFilms($filmDataAsArray);
//echo "</div>";

add_action( 'init', 'programmatically_create_post', 0 );


//////////////////////////////////////////////////////////////////////////

/////////////////////////// functions ////////////////////////////////////

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


//The various calls to this function would look like this:

//$post_id = programmatically_create_post();
//if( -1 == $post_id || -2 == $post_id ) {
	// The post wasn't created or the page already exists
//} // end if


function callService($url, $headers) {
	// Leave $url alone, and treat other arguments as headers
	$headers = array_slice(func_get_args(), 1);
	// Create context
	$context = stream_context_create(array(
		'http' => array (
			'method' => "GET",
			'header' => $headers
		)
	));

	// Make the request, saving the response
	$result = $data=file_get_contents($url, false, $context);

	return json_decode($result);
}


function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}


function displayShowTime($showTime)
{
	echo "ID:" . $showTime->id . "<br />";
	echo "FilmId:" . $showTime->filmId . "<br />";
	echo "FilmPackageId:" . $showTime->filmPackageId . "<br />";
	echo "Title:" . $showTime->title . "<br />";
	echo "ScreenId:" . $showTime->screenId . "<br />";
	echo "Seating:" . $showTime->seating . "<br />";
	echo "AreComplimentariesAllowed:" . $showTime->areComplimentariesAllowed . "<br />";
	echo "ShowType:" . $showTime->showType . "<br />";
	echo "Sales Via:" . $showTime->salesVia[0] . ", " . $showTime->salesVia[1] . "<br />";
	echo "Status:" . $showTime->status . "<br />";
	echo "PreShowStartTime:" . $showTime->preShowStartTime . "<br />";
	echo "FeatureStartTime:" . $showTime->featureStartTime . "<br />";
	echo "FeatureEndTime:" . $showTime->featureEndTime . "<br />";
	echo "CleanupEndTime:" . $showTime->cleanupEndTime . "<br />";
	echo "SeatsAvailable:" . $showTime->seatsAvailable . "<br />";
	echo "SeatsHeld:" . $showTime->seatsHeld . "<br />";
	echo "SeatsHouse:" . $showTime->seatsHouse . "<br />";
	echo "SeatsSold:" . $showTime->seatsSold . "<br />";
	echo "FilmFormat:" . $showTime->filmFormat . "<br />";
	echo "PriceCardName:" . $showTime->priceCardName . "<br />";
	$attributesString = "";
	foreach ($showTime->attributes as $value) {
		$attributesString .= " " . $value;
	}
	echo "Attributes:" . $attributesString . "<br />";
	echo "AudioLanguage:" . $showTime->audioLanguage . "<br />";
}


function displayAllShowTimes($dataAsArray)
{
	for ($i=0; $i< count($dataAsArray); $i++) {
		$showTime = new ShowTime;
		$showTime->assignValues($dataAsArray, $i);
		echo "<div class='showTimeTitle'>" . $showTime->title . "</div>";
		echo "<div class='showTimeBody'>";
		displayShowTime($showTime);
		echo "</div>";
	}
}


function displayFilm($film)
{
	echo "ID:" . $film->id . "<br />";
	echo "Title:" . $film->title . "<br />";
	echo "ShortName:" . $film->shortName . "<br />";
	echo "Synopsis:" . $film->synopsis . "<br />";
	echo "Genre:" . $film->genre . "<br />";
	echo "SignageText:" . $film->signageText . "<br />";
	echo "Distributor:" . $film->distributor . "<br />";
	echo "OpeningDate:" . $film->openingDate . "<br />";
	echo "Rating:" . $film->rating . "<br />";
	echo "Status:" . $film->status . "<br />";
	echo "Content:" . $film->content . "<br />";
	echo "Duration:" . $film->duration . "<br />";
	echo "DisplaySequence:" . $film->displaySequence . "<br />";
	echo "NationalCode:" . $film->nationalCode . "<br />";
	echo "Format:" . $film->format . "<br />";
	echo "IsRestricted:" . $film->isRestricted . "<br />";
	echo "People <br />";
	for ($i=0; $i<count($film->people); $i++)
	{
		echo "&nbsp; &nbsp; ID: " . $film->people[$i]["Id"] . "<br />";
		echo "&nbsp; &nbsp; First Name: " . $film->people[$i]["FirstName"] . "<br />";
		echo "&nbsp; &nbsp; Last Name: " . $film->people[$i]["LastName"] . "<br />";
		echo "&nbsp; &nbsp; Role: " . $film->people[$i]["Role"] . "<br />";
		echo "<br />";
	}
	echo "AudioLanguage:" . $film->audioLanguage . "<br />";
	echo "GovernmentFilmTitle:" . $film->governmentFilmTitle . "<br />";
}


function displayAllFilms ($dataAsArray)
{
	for ($i=0; $i< count($dataAsArray); $i++) {
		$film = new Film;
		$film->assignValues($dataAsArray, $i);
		echo "<div class='filmTitle'>" . $film->title . "</div>";
		echo "<div class='filmBody'>";
		displayFilm($film);
		echo "</div>";
	}
}


///////////////////////////////////////////////////////////////////////////


