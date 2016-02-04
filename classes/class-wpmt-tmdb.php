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

    //api-key: 17db82afedb26a09a8343e3c89f8c708

    var $arr = array ();

    function assign_values($api_data, $key ) {
        $this->arr['id']               = $api_data[$key]['Id'];
        $this->arr['title']            = $api_data[$key]['Title'];
        $this->arr['short_name']       = $api_data[$key]['ShortName'];
        $this->arr['synopsis']         = $api_data[$key]['Synopsis'];
        $this->arr['genre']            = $api_data[$key]['Genre'];
        $this->arr['signage_text']     = $api_data[$key]['SignageText'];
        $this->arr['distributor']      = $api_data[$key]['Distributor'];
        $this->arr['opening_date']     = $api_data[$key]['OpeningDate'];
        $this->arr['rating']           = $api_data[$key]['Rating'];
        $this->arr['status']           = $api_data[$key]['Status'];
        $this->arr['content_advisory'] = $api_data[$key]['Content'];
        $this->arr['duration']         = $api_data[$key]['Duration'];
        $this->arr['display_sequence'] = $api_data[$key]['DisplaySequence'];
        $this->arr['national_code']    = $api_data[$key]['NationalCode'];
        $this->arr['format']           = $api_data[$key]['Format'];
        $this->arr['is_restricted']    = $api_data[$key]['IsRestricted'];
        $this->arr['people']           = $api_data[$key]['People'];
        $this->arr['audio_language']   = $api_data[$key]['AudioLanguage'];
        $this->arr['gov_film_title']   = $api_data[$key]['GovernmentFilmTitle'];

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

    }


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


} // end class
