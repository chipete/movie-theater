<?php
/**
 * Created by PhpStorm.
 * User: edit5
 * Date: 1/26/16
 * Time: 2:16 PM
 */
// cURL cannot do real non-blocking calls, and the fractional timeout values are only
// supported since cURL 7.15.5 but are not supported in WP_Http_Curl. So if one of these
// conditions are met, we rather use PHP Streams to not delay the execution here (e.g. by cron).
if ($args['blocking'] == false || ceil($args['timeout']) != $args['timeout']) {
    $available_transports = array( 'streams' );
} else {
    $available_transports = array( 'curl', 'streams' );
}

$request_order = apply_filters( 'http_api_transports', $available_transports, $args, $url );

