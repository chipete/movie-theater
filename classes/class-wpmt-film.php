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
class WPMT_Film
{

    var $id;                        //The unique identifier of this film
    var $title;                     //The full title of this film
    var $short_name;                //The name to display when there is a limited number of space available (max 10 characters)
    var $synopsis;                  //Plot synopsis for this film
    var $genre;                     //The genre of this film
    var $signage_text;              //The title that is displayed in POS (max 20 characters)
    var $distributor;               //The name of the distributor
    var $opening_date;              //When did this film first show in the country of this Veezi customer
    var $rating;                    //Censor rating. Values vary depending on country
    var $status;                    //Valid values are: Active, Inactive, Deleted
    var $content_advisory;          // Content advisory for the film
    var $duration;                  //The duration of the film in minutes
    var $display_sequence;          //Determines the order in which films are listed on POS.
                                    // A lower number indicates a higher position.
    var $national_code;             // If the government of the Veezi customer's country requires film data to be reported,
                                    // the film's report code should be entered here.
    var $format;                    //The format that this film will play in. Possible values are 2D Film, 2D Digital, 3D Digital,3D HFR, Not a Film
                                    //The "Not a Film" option can be used when a cinema wants to schedule a show that is not a film, like a play or a concert.
    var $is_restricted;             //Is this film be restricted to adults only.
    var $people = Array();          //The people involved with the film. The valid values for Role are: Actor, Director, Producer
    var $audio_language;            //The original audio language of the film.
    var $government_film_title;     //If the government of the Veezi customer's country requires film data to be reported,
                                    // the film's reporting title should be entered here.

    function assign_values( $veezi_api_data, $key ) {
        $this->id               = $veezi_api_data[$key]['Id'];
        $this->title            = $veezi_api_data[$key]['Title'];
        $this->short_name       = $veezi_api_data[$key]['ShortName'];
        $this->synopsis         = $veezi_api_data[$key]['Synopsis'];
        $this->genre            = $veezi_api_data[$key]['Genre'];
        $this->signage_text     = $veezi_api_data[$key]['SignageText'];
        $this->distributor      = $veezi_api_data[$key]['Distributor'];
        $this->opening_date     = $veezi_api_data[$key]['OpeningDate'];
        $this->rating           = $veezi_api_data[$key]['Rating'];
        $this->status           = $veezi_api_data[$key]['Status'];
        $this->content_advisory = $veezi_api_data[$key]['Content'];
        $this->duration         = $veezi_api_data[$key]['Duration'];
        $this->display_sequence = $veezi_api_data[$key]['DisplaySequence'];
        $this->national_code    = $veezi_api_data[$key]['NationalCode'];
        $this->format           = $veezi_api_data[$key]['Format'];
        $this->is_restricted    = $veezi_api_data[$key]['IsRestricted'];
        $this->people           = $veezi_api_data[$key]['People'];
        $this->audio_language   = $veezi_api_data[$key]['AudioLanguage'];
        $this->gov_film_title   = $veezi_api_data[$key]['GovernmentFilmTitle'];

    }


    function update_fields ( $post_id ) {

        //film admin info
        update_field( 'field_56a10c7a26578', $this->id, $post_id );
        update_field( 'field_56a10d337a3d1', $this->short_name, $post_id );
        update_field( 'field_56a111df1ffab', $this->status, $post_id );
        update_field( 'field_56a118f80afd1', $this->opening_date, $post_id );

        //film veezi info
        update_field( 'field_56a10e0618f4a', $this->synopsis, $post_id );
        update_field( 'field_56a10e1918f4b', $this->genre, $post_id );
        update_field( 'field_56a10e3118f4c', $this->rating, $post_id );
        update_field( 'field_56a10eb518f4e', $this->duration, $post_id );
        update_field( 'field_56a11844f6114', $this->distributor, $post_id );
        update_field( 'field_56ab9761e9e1e', $this->format, $post_id );
        update_field( 'field_56a10ef718f50', $this->audio_language, $post_id );
        update_field( 'field_56a1185ff6115', $this->get_veezi_people($this->people, 'Director'), $post_id );    //this field is director
        update_field( 'field_56a11869f6116', $this->get_veezi_people($this->people, 'Actor'), $post_id );       //this field is actors
        update_field( 'field_56a119ad9561d', $this->content_advisory, $post_id) ;                               //note this field is content advisory

        /*
        //There is currently no fields for these. Included here for possible future implementation
        update_field('', $this->signageText, $post_id);
        update_field('', $this->displaySequence, $post_id);
        update_field('', $this->nationalCode, $post_id);
        update_field('', $this->governmentFilmTitle, $post_id);
        */
    }


    function update_external_fields ( $post_id ) {

        $title      = get_the_title( $post_id );

        $bom        = new WPMT_Bom();
        $bom_data   = $bom->get_movie_info( $title );

        $yt         = new WPMT_Youtube();
        $yt_data    = $yt->get_youtube_url( 'WPMT_Film', $title );

        //$rt_data    = $this->get_rotten_tomatoes( $title );

        if ( $yt_data ) {
            update_field( 'field_56a1178eb02a7', $yt_data, $post_id );          //youtube_url
        }

        if ( $bom_data['poster'] ) {
            $bom_poster_id = $bom->import_photo( $post_id, $bom_data['poster'] );
            update_field( 'field_56a113b2b02a2', $bom_poster_id, $post_id );    //poster
        }

        /*
         * This image call does not work, BOM no longer has photos. Looking into Rotten Tomatoes API (See below) or omDb api or iMdb api instead

         if ( $bom_data['image640'] ) {
            $bom_image_id   = $bom->import_photo( $post_id, $bom_data['image640'] );
            update_field( 'field_56a1147cb02a3', $bom_image_id, $post_id );     //image
        }

        //waiting for RT API key to be approved (pending)

        if ( $rt_data ) {
            update_field( 'field_56b0a4dd84ab1', $rt_data['rt_rating'], $post_id );     //rt rating
            update_field( 'field_56b0a4f084ab2', $rt_data['rt_consensus'], $post_id );  //rt consensus
        }
        */

    } //end function


    function update_film_format( $post_id ) {
        update_field( 'field_56a10e1918f4b', $this->genre, $post_id );
        update_field( 'field_56ab9761e9e1e', $this->format, $post_id );
    }

    function get_veezi_people( $array, $role ) {
        //helper function getPeople ($role) that returns a comma seperated list of people of a certain role

        $people = null;

        foreach ( $array as $value ) {

            if ( $role == $value['Role'] ) {
                //add a comma if more than one person in role
                if ( $people ) {
                    $people .= ', ';
                }
                $people .= $value['FirstName'] . ' ' . $value['LastName'];
            }
        }

        return $people;
    }

    function get_rotten_tomatoes( $title )
    {

        $api_key = 'mwwaecxfrzbnstpj7tya9b7k';
        $q = urlencode('Toy Story'); // make sure to url encode an query parameters

        // construct the query with our api_key and the query we want to make
        $endpoint = 'http://api.rottentomatoes.com/api/public/v1.0/movies.json?api_key=' . $api_key . '&q=' . $q;

        // setup curl to make a call to the endpoint
        $session = curl_init($endpoint);

        // indicates that we want the response back
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        // exec curl and get the data back
        $data = curl_exec($session);

        // remember to close the curl session once we are finished retrieveing the data
        curl_close($session);

        // decode the json data to make it easier to parse the php
        $search_results = json_decode($data);
        if ($search_results === NULL) die('Error parsing json');

        // play with the data!
        $movies = $search_results->movies;

        $arr = array ();

        foreach ($movies as $movie) {
            if ($movie->title == $title) {
                $arr['rt_rating']    = $movie->critics_score;
                $arr['rt_title']     = $movie->title;
                $arr['rt_consensus'] = $movie->critics_consensus;
                $arr['rt_synopsis']  = $movie->synopsis;
            }
        }

        return $arr;
    }//end function get_rotten_tomatoes




} // end class
