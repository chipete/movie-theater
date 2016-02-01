<?php

/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 1/23/2016
 * Time: 12:25 PM
 */

class WPMT_Performance {

//Note commenting indicates film as this is the format importated from Veezi API
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
        update_field( 'field_56afa3a5d0b6d', $this->id, $post_id );
        update_field( 'field_56afa6c0d0b6e', $this->short_name, $post_id );
        update_field( 'field_56afa6cad0b6f', $this->status, $post_id );
        update_field( 'field_56afa6d1d0b70', $this->opening_date, $post_id );

        //film veezi info
        update_field( 'field_56afa6e4d0b72', $this->synopsis, $post_id );
        update_field( 'field_56afa6e9d0b73', $this->genre, $post_id );
        update_field( 'field_56afa983d0b74', $this->rating, $post_id );
        update_field( 'field_56afa98fd0b76', $this->duration, $post_id );
        update_field( 'field_56afa9fcd0b86', $this->distributor, $post_id );
        update_field( 'field_56afa6ddd0b71', $this->format, $post_id );
        update_field( 'field_56afa994d0b77', $this->audio_language, $post_id );
        update_field( 'field_56afa99cd0b78', $this->get_veezi_people($this->people, 'Director'), $post_id );    //this field is director
        update_field( 'field_56afa9a9d0b79', $this->get_veezi_people($this->people, 'Actor'), $post_id );       //this field is actors
        update_field( 'field_56afa9b0d0b7a', $this->content_advisory, $post_id) ;                               //note this field is content advisory

        /*
        //There is currently no fields for these. Included here for possible future implementation
        update_field('', $this->signageText, $post_id);
        update_field('', $this->displaySequence, $post_id);
        update_field('', $this->nationalCode, $post_id);
        update_field('', $this->governmentFilmTitle, $post_id);
        */
    }

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
