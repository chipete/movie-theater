<!--<link href="css/films.css" type="text/css" rel="stylesheet" /> --> <!-- Used mostly for testing purposes at the moment // -->
<?php

/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 1/23/2016
 * Time: 9:40 AM
 */
class WPMT_Session
{

    var $id;                            //The unique identifier of this session
    var $film_id;                       //If this session is just showing a single film, this is the unique identifier of that film
    var $film_package_id;               //If this session is for a multi/double feature, then this will be the unique identifier of the film package
    var $title;                         //The title of the film or film package showing at this session
    var $screen_id;                     //the unique identifier of the screen that this session is showing on
    var $seating;                       //How seats are allocated for this session. Valid values are:
                                        //Allocated Seats are allocated
                                        //Select Seats are allocated and must be chosen by the POS operator
                                        //Open No seating allocation
    var $are_complimentaries_allowed;   //Are complimentary tickets allows to be used for this session
    var $show_type;                     //Is this session Public or Private
    var $sales_via = Array ();          //What sales channels are tickets for this session available on.
                                        //There could be zero (alothough this is unlikely) to two of these.
                                        //WWW Available for sale via the web POS Avaliable for sales from POS
    var $status;                        //The status of this session. Valid values are Open, Closed, Planned
    var $pre_show_start_time;           //The time the pre-show (trailers) are scheduled to start for this session
    var $feature_start_time;            //The time the feature is scheduled to start
    var $feature_end_time;              //The time the feature is scheduled to end
    var $cleanup_end_time;              //The time the cleanup for this session is scheduled to end
    var $seats_available;               //The number of seats that are available for sale to this session. This includes wheel chair seats, but excludes house seats
    var $seats_held;                    //The number of seats that are being held by unpaid bookings
    var $seats_house;                   //The number of house seats that are still being held for this session
    var $seats_sold;                    //The number of seats that have been sold for this session
    var $film_format;                   //The format that the session will be played in. Possible values are: 2D Film, 2D Digital, 3D Digital
    var $price_card_name;               //The price card is a set of ticket prices that can be applied to a session. For example a cinema may have a price card for matinee sessions and another for their evening sessions. This is the name or the price card for this session.
    var $attributes;                    //The attribute codes assigned to this session. These attribute codes can be matched against attributes found using the Attribute API
    var $audio_language;                //The audio language assigned to this session. This may differ from the original language of the film. This is not able to be set
    var $ticket_url;                    //extra property URL which contains the URL that can be used to purchase tickets to that session if Veezi Web Ticketing is enabled

    function assign_values( $VeeziAPIData, $key )
    {
        //assignValues is designed to streamline the move from
        //VeeziAPI data web session as a multidimensional array
        //to values as ShowTime Class object variables
        $this->id                           = $VeeziAPIData[$key]['Id'];
        $this->film_id                      = $VeeziAPIData[$key]['FilmId'];
        $this->film_package_id              = $VeeziAPIData[$key]['FilmPackageId'];
        $this->title                        = $VeeziAPIData[$key]['Title'];
        $this->screen_id                    = $VeeziAPIData[$key]['ScreenId'];
        $this->seating                      = $VeeziAPIData[$key]['Seating'];
        $this->are_complimentaries_allowed  = $VeeziAPIData[$key]['AreComplimentariesAllowed'];
        $this->show_type                    = $VeeziAPIData[$key]['ShowType'];
        $this->sales_via                    = $VeeziAPIData[$key]['SalesVia'];
        $this->status                       = $VeeziAPIData[$key]['Status'];
        $this->pre_show_start_time          = $VeeziAPIData[$key]['PreShowStartTime'];
        $this->feature_start_time           = $VeeziAPIData[$key]['FeatureStartTime'];
        $this->feature_end_time             = $VeeziAPIData[$key]['FeatureEndTime'];
        $this->cleanup_end_time             = $VeeziAPIData[$key]['CleanupEndTime'];
        $this->seats_available              = $VeeziAPIData[$key]['SeatsAvailable'];
        $this->seats_held                   = $VeeziAPIData[$key]['SeatsHeld'];
        $this->seats_house                  = $VeeziAPIData[$key]['SeatsHouse'];
        $this->seats_sold                   = $VeeziAPIData[$key]['SeatsSold'];
        $this->film_format                  = $VeeziAPIData[$key]['FilmFormat'];
        $this->price_card_name              = $VeeziAPIData[$key]['PriceCardName'];
        $this->attributes                   = $VeeziAPIData[$key]['Attributes'];
        $this->audio_language               = $VeeziAPIData[$key]['AudioLanguage'];
        $this->ticket_url                   = $VeeziAPIData[$key]['Url'];
    }

    function update_fields ( $post_id ) {

        update_field( 'field_56a12595938bf', $this->feature_start_time, $post_id );
        update_field( 'field_56a1261d938c0', $this->feature_end_time, $post_id );
        update_field( 'field_56a12574938be', $this->film_id, $post_id );
        update_field( 'field_56a12677938c1', $this->title, $post_id );
        update_field( 'field_56a126f7938c4', $this->status, $post_id );
        update_field( 'field_56ab9c1f2bc2c', $this->screen_id, $post_id );
        update_field( 'field_56a126bb938c3', $this->seats_available, $post_id );
        update_field( 'field_56af703f7a2ba', $this->ticket_url, $post_id );
    }

} // end class