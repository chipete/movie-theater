<?php
/**
 * Created by PhpStorm.
 * User: edit5
 * Date: 1/26/16
 * Time: 8:07 AM
 *
 * pulls JSON information from veezi API
 */


//========================= Configuration ========================//
/*
 * Currently, you must change $myToken to your
 * Veezi API token (found in your Veezi account)
 * for this plugin to work
 */
//$myToken = "PxWHQDAzZEmWZ7s2HZYCCA2";
//$VeeziAccessToken = 'VeeziAccessToken: ' . $myToken;

//======================== Main Execution ========================//
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
 *
 *
 * sample use:  $showTimeData = callService('https://api.us.veezi.com/v1/websession', $VeeziAccessToken);
 *              $filmData = callService('https://api.us.veezi.com/v1/film', $VeeziAccessToken);
 */


function call_service($url, $headers) {
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

    //Can't use file_get_contents within wordpress framework use wp_remote_request instead
    //$response = wp_remote_request($url, $context);

    //return json_decode( wp_remote_retrieve_body( $response ), true );
}

/* converts data to an Array from array = (stdObject) format
 * Calling function objectToArray($data)
 * This is important because class function assignValues($dataAsArray, $key)
 * requires array data
*/

function call_curl_service ( $api_key, $query ) {

    $query = urlencode( $query );
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, 'http://api.themoviedb.org/3/search/movie?api_key=' . $api_key . '&query=' . $query . '&page=1' );
    //curl_setopt( $ch, CURLOPT_URL, "http://api.themoviedb.org/3/search/movie" );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $ch, CURLOPT_HEADER, FALSE );

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/json"
        //"api_key: 17db82afedb26a09a8343e3c89f8c708"
        //"query: fight+club",
        //"page: 1"
    ));

    $response = curl_exec( $ch );
    curl_close( $ch );

    // decode the json data to make it easier to parse the php
    $search_results = json_decode( $response );
    if ($search_results === NULL) die('Error parsing json');

// play with the data!
    $movies = $search_results->results;

    foreach ($movies as $movie) {
        //return the first result
        return $movie;
    }


}

function object_to_array($d) {
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