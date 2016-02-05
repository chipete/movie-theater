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

    /* ================= ASSOCIATED FIELDS =============== */

    //associate available TMDB fields to available WPMT fields
    var $associated_fields = array (
        'adult'                 => '',                          //boolean       ie: false
        'backdrop_path'         => 'wpmt_film_image',           //string        ie: "/8uO0gUM8aNqYLs1OsTBQiXu0fEv.jpg"
        'belongs_to_collection' => '',                          //              ie: null
        'budget'                => '',                          //int           ie: 63000000
        'genres'                => '',                          //array         ie: [ { "id": 18, "name": "Drama" } ]
        'homepage'              => '',                          //string        ie: ""
        'id'                    => '',                          //tmdb id       ie: 550
        'imdb_id'               => '',                          //imdb id       ie: tt0137523
        'original_language'     => 'wpmt_film_audio_language',  //              ie: "en"
        'original_title'        => '',                          //              ie: "Fight Club"
        'overview'              => 'wpmt_film_synopsis',        //              ie: "A ticking-time-bomb insomniac and a slippery soap salesman channel primal male aggression into a shocking new form of therapy. Their concept catches on, with underground \"fight clubs\" forming in every town, until an eccentric gets in the way and ignites an out-of-control spiral toward oblivion."
        'popularity'            => '',                          //num           ie: 4.39844
        'poster_path'           => 'wpmt_film_poster',          //              ie: "/811DjJTon9gD6hZ8nCjSitaIXFQ.jpg"
        'production_companies'  => '',                          //array         ie: [ { "name": "20th Century Fox", "id": 25 }, { "name": "Fox 2000 Pictures", "id": 711 } ]
        'production_countries'  => '',                          //array         ie: [ { "iso_3166_1": "DE", "name": "Germany" }, { "iso_3166_1": "US", "name": "United States of America" } ]
        'release_date'          => '',                          //              ie: "1999-10-14"
        'revenue'               => '',                          //int           ie: 100853753
        'runtime'               => 'wpmt_film_duration',        //int           ie: 139
        'spoken_languages'      => '',                          //array         ie: [ { "iso_639_1": "en", "name": "English" } ]
        'status'                => '',                          //              ie: "Released"
        'tagline'               => '',                          //              ie: "How much can you know about yourself if you've never been in a fight?"
        'title'                 => 'wpmt_film_title',           //              ie: "Fight Club",
        'video'                 => '',                          //bool          ie: false  (is this a video or a theatrical film/movie?)
        'vote_average'          => '',                          //num           ie: 7.8
        'vote_count'            => '',                          //int           ie: 3527
        'trailer_id'            => 'wpmt_film_youtube_url',     //we make this one from &append_to_response=trailers in get_tmdb_data
        );


    /* ================= FUNCTIONS ===================== */

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

        $tmdb_id                = $this->get_tmdb_id ( get_the_title ( $post_id ) );

        // JSON array
        $tmdb_data              = $this->get_tmdb_data ( $tmdb_id );

        // field-key reference
        $wpmt_film_fields_ref   = get_wpmt_film_fields_ref();

        // if the tmdb_data title is a match
        if ( get_the_title ( $post_id ) == $tmdb_data['title'] ) {

            foreach ( $this->associated_fields as $tmdb_field => $wpmt_field ) {

                $tmdb_value = $tmdb_data[$tmdb_field];

                if ( ! empty( $wpmt_field ) &&
                     //! empty( $tmdb_value ) &&
                     empty( get_field( $wpmt_field ) ) ) {

                    $tmdb_value = $this->pre_process_data ( $wpmt_field, $tmdb_value, $post_id );

                    update_field( $wpmt_film_fields_ref[$wpmt_field], $tmdb_value, $post_id );
                }

            } // end foreach
        } // end if
    } // end update_fields


    function pre_process_data ( $wpmt_field, $tmdb_value, $post_id ) {

        if ( $wpmt_field == 'wpmt_film_image' ) {

            // add base_url
            $photo_url = 'https://image.tmdb.org/t/p/w780' . $tmdb_value;

            // import the photo and return the attachment_id
            return $this->import_photo( $photo_url, $post_id );
        }

        elseif ( $wpmt_field == 'wpmt_film_poster' ) {

            // add base_url
            $photo_url = 'https://image.tmdb.org/t/p/w342' . $tmdb_value;

            // import the photo and return the attachment_id
            return $this->import_photo( $photo_url, $post_id );
        }

        elseif ( $wpmt_field == 'wpmt_film_youtube_url' ) {
            //add base_url
            return 'https://www.youtube.com/embed/' . $tmdb_value;
        }

        else {
            //no pre_process needed
            return $tmdb_value;
        }

    } // end pre_process_data


    function get_tmdb_id ( $query ) {

        $query = urlencode( $query );
        $url = 'http://api.themoviedb.org/3/search/movie?api_key='
               . $this->api_key
               . '&query='
               . $query;

        // JSON Array
        $search_results = $this->call_curl_service ($url);

        $movies = $search_results['results'];

        if (! empty ($movies) ) {
            foreach ( $movies as $movie ) {
                // return the first result
                return $movie['id'];
            }
        }
        else {
            return false;
        }

    } // end get_tmdb_id


    function get_tmdb_data ( $tmdb_id ) {

        $trailer_id = null;

        $url = 'http://api.themoviedb.org/3/movie/'
               . $tmdb_id
               . '?api_key='
               . $this->api_key
               . '&append_to_response=trailers';

        // JSON Array
        $tmdb_data = $this->call_curl_service ( $url );

        //get the first trailer id  trailers -> youtube -> array -> source
        $trailer_id = $tmdb_data['trailers']['youtube'][0]['source'];

        // add the trailer_id to the JSON object
        if ( $trailer_id ) {
            $tmdb_data['trailer_id'] = $trailer_id;
        }

        return $tmdb_data;

    } // end get_tmdb_data


    function import_photo( $photo_url, $post_id ) {

        //$post_id = get_post( $post_id );
        $photo_name = 'test';
        $title      = get_the_title( $post_id );

        if( empty( $post_id ) ) {
            return false;
        }

        if( !class_exists( 'WP_Http' ) ) {
            include_once( ABSPATH . WPINC . '/class-http.php' );
        }

        $photo = new WP_Http();

        //$photo = $photo->request( 'http://example.com/photos/directory/' . $photo_name . '.jpg' );
        $photo = $photo->request( $photo_url );

        if( $photo['response']['code'] != 200 ) {
            return false;
        }

        $attachment = wp_upload_bits( $photo_name . '.jpg', null, $photo['body'], date("Y-m", strtotime( $photo['headers']['last-modified'] ) ) );
        if( !empty( $attachment['error'] ) ) {
            return false;
        }

        $filetype = wp_check_filetype( basename( $attachment['file'] ), null );

        $postinfo = array(
            'post_mime_type'	=> $filetype['type'],
            'post_title'		=> $title . ' tmdb',
            'post_content'		=> '',
            'post_status'		=> 'inherit',
        );

        $filename = $attachment['file'];
        $attach_id = wp_insert_attachment( $postinfo, $filename, $post_id );

        if( !function_exists( 'wp_generate_attachment_data' ) ) {
            require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
        }

        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id,  $attach_data );

        if (! empty ( $attach_id ) ) {
            return $attach_id;
        }

        else {
            return false;
        }

    }//end function import_photos


    function call_curl_service ( $url ) {

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ch, CURLOPT_HEADER, FALSE );

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"
        ));

        $response = curl_exec( $ch );
        curl_close( $ch );

        // decode the json data to make it easier to parse the php
        $search_results = json_decode( $response, true );
        if ($search_results === NULL) {
            die('Error parsing json');
        }

        else {
            return $search_results;
        }

    } // end call_curl_service


} // end class
