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
class movietheater_Film
{

    var $id; //The unique identifier of this film
    var $title; //The full title of this film
    var $shortName; //The name to display when there is a limited number of space available (max 10 characters)
    var $synopsis; //Plot synopsis for this film
    var $genre; //The genre of this film
    var $signageText; //The title that is displayed in POS (max 20 characters)
    var $distributor; //The name of the distributor
    var $openingDate; //When did this film first show in the country of this Veezi customer
    var $rating; //Censor rating. Values vary depending on country
    var $status; //Valid values are: Active, Inactive, Deleted
    var $content; // Content advisory for the film
    var $duration; //The duration of the film in minutes
    var $displaySequence; //Determines the order in which films are listed on POS.
    // A lower number indicates a higher position.
    var $nationalCode;// If the government of the Veezi customer's country requires film data to be reported,
    // the film's report code should be entered here.
    var $format; //The format that this film will play in. Possible values are 2D Film, 2D Digital, 3D Digital,3D HFR, Not a Film
    //The "Not a Film" option can be used when a cinema wants to schedule a show that is not a film, like a play or a concert.
    var $isRestricted; //Is this film be restricted to adults only.
    var $people = Array(); //The people involved with the film. The valid values for Role are: Actor, Director, Producer
    var $audioLanguage; //The original audio language of the film.
    var $governmentFilmTitle; //If the government of the Veezi customer's country requires film data to be reported,
    // the film's reporting title should be entered here.

    function assignValues($VeeziAPIData, $key)
    {
        $this->id = $VeeziAPIData[$key]['Id'];
        $this->title = $VeeziAPIData[$key]['Title'];
        $this->shortName = $VeeziAPIData[$key]['ShortName'];
        $this->synopsis = $VeeziAPIData[$key]['Synopsis'];
        $this->genre = $VeeziAPIData[$key]['Genre'];
        $this->signageText = $VeeziAPIData[$key]['SignageText'];
        $this->distributor = $VeeziAPIData[$key]['Distributor'];
        $this->openingDate = $VeeziAPIData[$key]['OpeningDate'];
        $this->rating = $VeeziAPIData[$key]['Rating'];
        $this->status = $VeeziAPIData[$key]['Status'];
        $this->content = $VeeziAPIData[$key]['Content'];
        $this->duration = $VeeziAPIData[$key]['Duration'];
        $this->displaySequence = $VeeziAPIData[$key]['DisplaySequence'];
        $this->nationalCode = $VeeziAPIData[$key]['NationalCode'];
        $this->format = $VeeziAPIData[$key]['Format'];
        $this->isRestricted = $VeeziAPIData[$key]['IsRestricted'];
        $this->people = $VeeziAPIData[$key]['People'];
        $this->audioLanguage = $VeeziAPIData[$key]['AudioLanguage'];
        $this->governmentFilmTitle = $VeeziAPIData[$key]['GovernmentFilmTitle'];

    }
    function updateFilmFields ($post_id) {

        //film admin info
        update_field('field_56a10c7a26578', $this->id, $post_id);
        update_field('field_56a10d337a3d1', $this->shortName, $post_id);
        update_field('field_56a111df1ffab', $this->status, $post_id);
        update_field('field_56a118f80afd1', $this->openingDate, $post_id);

        //film veezi info
        update_field('field_56a10e0618f4a', $this->synopsis, $post_id);
        update_field('field_56a10e1918f4b', $this->genre, $post_id);
        update_field('field_56a10e3118f4c', $this->rating, $post_id);
        update_field('field_56a10eb518f4e', $this->duration, $post_id);
        update_field('field_56a11844f6114', $this->distributor, $post_id);
        update_field('field_56a10eca18f4f', $this->format, $post_id);
        update_field('field_56a10ef718f50', $this->audioLanguage, $post_id);
        update_field('field_56a1185ff6115', $this->getPeople($this->people, 'Director'), $post_id); //this field is director
        update_field('field_56a11869f6116', $this->getPeople($this->people, 'Actor'), $post_id); //this field is actors
        update_field('field_56a119ad9561d', $this->isRestricted, $post_id); //note this field is content advisory

        /*
        //There is currently no fields for these. Included here for possible future implementation
        update_field('', $this->signageText, $post_id);
        update_field('', $this->displaySequence, $post_id);
        update_field('', $this->nationalCode, $post_id);
        update_field('', $this->governmentFilmTitle, $post_id);
        */
    }

    function getPeople ($array, $role) {
        //helper function getPeople ($role) that returns a comma seperated list of people of a certain role
        $people = '';

        foreach ($array as $value) {

            if ($role == ''. $value['Role']) {
                //add a comma if more than one person in role
                if ($people != '') {
                    $people .= ', ';
                }
                $people .= $value['FirstName'] . ' ' . $value['LastName'];
            }
        }

        return $people;
    }
}
?>