<?php

/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 1/23/2016
 * Time: 12:25 PM
 */
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 1/23/2016
 * Time: 9:40 AM
 */
class WPMT_Tmdb
{

    //tmdb API Key
    var $api_key = '17db82afedb26a09a8343e3c89f8c708';

    //associate available TMDB fields to available WPMT fields
    var $associated_fields = array (
        'adult'             => '',                  //boolean       ie: false
        'backdrop_path'     => '',                  //string        ie: "/8uO0gUM8aNqYLs1OsTBQiXu0fEv.jpg"
        'genre_ids'         => '',                  //array         ie: [ 18 ]
        'id'                => '',                  //tmdb id       ie: 550
        'original_language' => '',                  //              ie: "en"
        'original_title'    => '',                  //              ie: "Fight Club"
        'overview'          => 'synopsis',          //              ie: "A ticking-time-bomb insomniac and a slippery soap salesman channel primal male aggression into a shocking new form of therapy. Their concept catches on, with underground \"fight clubs\" forming in every town, until an eccentric gets in the way and ignites an out-of-control spiral toward oblivion."
        'release_date'      => '',                  //              ie: "1999-10-14"
        'poster_path'       => '',                  //              ie: "/811DjJTon9gD6hZ8nCjSitaIXFQ.jpg"
        'popularity'        => '',                  //num           ie: 4.39844
        'title'             => 'wpmt_film_title',   //              ie: "Fight Club",
        'video'             => '',                  //??            ie: false
        'vote_average'      => '',                  //num           ie: 7.8
        'vote_count'        => '',                  //int           ie: 3527
);


    function call_curl_service ( $query ) {

        $query = urlencode( $query );
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, 'http://api.themoviedb.org/3/search/movie?api_key=' . $this->api_key . '&query=' . $query . '&page=1' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ch, CURLOPT_HEADER, FALSE );

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"
        ));

        $response = curl_exec( $ch );
        curl_close( $ch );

        // decode the json data to make it easier to parse the php
        $search_results = json_decode( $response );
        if ($search_results === NULL) die('Error parsing json');

        // play with the data!
        $movies = $search_results->results;

        foreach ($movies as $movie) {
            // return the first result
            return $movie;
        }

    } // end call_curl_service


    /*
     * update_fields ( $post_id )
     *
     * EXAMPLE
     * update_field ( 'the_field_key', 'the_value', $post_id );
     *
     * MANUAL
     * update_field( 'field_56b0a4dd84ab1', $tmdb_data->original_title, $post_id );
     *
     * USING ASSOCIATIVE ARRAYS
     * update_field( $wpmt_film_fields_ref[ $wpmt_field ], $tmdb_data->{$tmdb_field}, $post_id );
    */

    function update_fields ( $post_id ) {

        // json object
        $tmdb_data = $this->call_curl_service ( get_the_title( $post_id ) );

        //get the field-key reference
        $wpmt_film_fields_ref = get_wpmt_film_fields_ref();

        // if the tmdb_data title is a match
        if ( get_the_title ( $post_id ) == $tmdb_data->title ) {

            foreach ( $this->associated_fields as $tmdb_field => $wpmt_field ) {

                $tmdb_value = $tmdb_data->{$tmdb_field};

                if ( ! empty( $wpmt_field ) ) {
                    if ( empty( get_field( $wpmt_field ) ) && ! empty( $tmdb_value ) ) {

                        $tmdb_value = $this->preprocess_data ( $wpmt_field, $tmdb_value );

                        update_field( $wpmt_film_fields_ref[ $wpmt_field ], $tmdb_value, $post_id );

                    }
                }// end if
            }// end foreach
        } // end if
    }// end update_fields


    function preprocess_data ( $wpmt_field, $tmdb_value ) {

        // if $wpmt_field requires a different input than tmdb_value, then get it ready

        return $tmdb_value;

    }
} // end class
