<link href="css/films.css" type="text/css" rel="stylesheet" />
<?php
include('classes/ShowTime.php');
include('classes/Film.php');
/*
Plugin Name: Veezi Film
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: Ryan
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
Takes API from Veezi and uses it to populate objects for classes ShowTime and Film
These are then compared to the database to accomplish the following objectives


*/
/////////////////////////// Configuration /////////////////////////////////
/*
 * In order for this plugin to work you must change $myToken to your
 * Veezi API token (found in your Veezi account)
 */
$myToken = "PxWHQDAzZEmWZ7s2HZYCCA2";

///////////////////////////////////////////////////////////////////////////
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

echo "<div id='container'>";
	echo "<div class='generalTitle'> All Show Times </div>";
	$showTimeDataAsArray = objectToArray($showTimeData);
	displayAllShowTimes($showTimeDataAsArray);
echo "</div>";
echo "<div id='container'>";
	echo "<div class='generalTitle'> All Films </div>";
	$film = new Film;
	$filmDataAsArray = objectToArray($filmData);
	displayAllFilms($filmDataAsArray);
echo "</div>";

///////////////////////////////////////////////////////////////////////////
/////////////////////////// functions /////////////////////////////////////


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

?>
