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

        /*
        $bom        = new WPMT_Bom();
        $bom_data   = $bom->get_movie_info( $title );

        $imdb       = new WPMT_Imdb();
        $imdb_data  = $imdb->get_movie_info( $title );
        */
        $yt         = new WPMT_Youtube();
        $yt_data    = $yt->get_youtube_url( 'WPMT_Film', $title );
        //$yt_data    = $title;


        if ( $yt_data ) {
            update_field( 'field_56a1178eb02a7', $yt_data, $post_id ); //youtube_url
        }
        /*
        if ( $bom_data ) {

            update_field( 'field_56a10c7a26578', $bom_data['poster'], $post_id );     //poster
            update_field( 'field_56a10c7a26578', $bom_data['image640'], $post_id );     //image
        }

        if ( $imdb_data ) {

            if ( get_field( 'wpmt_film_synopsis' ) == '' ) {
                update_field( 'field_56a10c7a26578', $imdb_data['plot'], $post_id ); //synopsis
            }

            if ( get_field( 'wpmt_film_genre' ) == '' ) {
                update_field( 'field_56a10c7a26578', $imdb_data['genre[0]'], $post_id ); //genre
            }

            if ( get_field( 'wpmt_film_directors' ) == '' ) {
                update_field( 'field_56a10c7a26578', $imdb_data['director'], $post_id ); //director
            }

        }//end if
        */

    } //end function


    function get_veezi_people ($array, $role ) {
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


} // end class
