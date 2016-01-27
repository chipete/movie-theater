<!--<link href="css/films.css" type="text/css" rel="stylesheet" /> --> <!-- Used mostly for testing purposes at the moment // -->
<?php

/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 1/23/2016
 * Time: 9:40 AM
 */
class movietheater_ShowTime
{

    var $id; //The unique identifier of this session
    var $filmId; //If this session is just showing a single film, this is the unique identifier of that film
    var $filmPackageId; //If this session is for a multi/double feature, then this will be the unique identifier of the film package
    var $title; //The title of the film or film package showing at this session
    var $screenId; //the unique identifier of the screen that this session is showing on
    var $seating; //How seats are allocated for this session. Valid values are:
    // Allocated Seats are allocated
    // Select Seats are allocated and must be chosen by the POS operator
    //Open No seating allocation
    var $areComplimentariesAllowed; //Are complimentary tickets allows to be used for this session
    var $showType; //Is this session Public or Private
    var $salesVia = Array (); //What sales channels are tickets for this session available on.
    // There could be zero (alothough this is unlikely) to two of these.
    // WWW Available for sale via the web POS Avaliable for sales from POS
    var $status; //The status of this session. Valid values are Open, Closed, Planned
    var $preShowStartTime; //The time the pre-show (trailers) are scheduled to start for this session
    var $featureStartTime; //The time the feature is scheduled to start
    var $featureEndTime; //The time the feature is scheduled to end
    var $cleanupEndTime; //The time the cleanup for this session is scheduled to end
    var $seatsAvailable; //The number of seats that are available for sale to this session. This includes wheel chair seats, but excludes house seats
    var $seatsHeld; //The number of seats that are being held by unpaid bookings
    var $seatsHouse; //The number of house seats that are still being held for this session
    var $seatsSold; //The number of seats that have been sold for this session
    var $filmFormat; //The format that the session will be played in. Possible values are: 2D Film, 2D Digital, 3D Digital
    var $priceCardName; // The price card is a set of ticket prices that can be applied to a session. For example a cinema may have a price card for matinee sessions and another for their evening sessions. This is the name or the price card for this session.
    var $attributes; //The attribute codes assigned to this session. These attribute codes can be matched against attributes found using the Attribute API
    var $audioLanguage; //The audio language assigned to this session. This may differ from the original language of the film. This is not able to be set

    function assignValues($VeeziAPIData, $key)
    {
        //assignValues is designed to streamline the move from
        //VeeziAPI data web session as a multidimensional array
        // to values as ShowTime Class object variables
        $this->id = $VeeziAPIData[$key]['Id'];
        $this->filmId = $VeeziAPIData[$key]['FilmId'];
        $this->filmPackageId = $VeeziAPIData[$key]['FilmPackageId'];
        $this->title = $VeeziAPIData[$key]['Title'];
        $this->screenId = $VeeziAPIData[$key]['ScreenId'];
        $this->seating = $VeeziAPIData[$key]['Seating'];
        $this->areComplimentariesAllowed = $VeeziAPIData[$key]['AreComplimentariesAllowed'];
        $this->showType = $VeeziAPIData[$key]['ShowType'];
        $this->salesVia = $VeeziAPIData[$key]['SalesVia'];
        $this->status = $VeeziAPIData[$key]['Status'];
        $this->preShowStartTime = $VeeziAPIData[$key]['PreShowStartTime'];
        $this->featureStartTime = $VeeziAPIData[$key]['FeatureStartTime'];
        $this->featureEndTime = $VeeziAPIData[$key]['FeatureEndTime'];
        $this->cleanupEndTime = $VeeziAPIData[$key]['CleanupEndTime'];
        $this->seatsAvailable = $VeeziAPIData[$key]['SeatsAvailable'];
        $this->seatsHeld = $VeeziAPIData[$key]['SeatsHeld'];
        $this->seatsHouse = $VeeziAPIData[$key]['SeatsHouse'];
        $this->seatsSold = $VeeziAPIData[$key]['SeatsSold'];
        $this->filmFormat = $VeeziAPIData[$key]['FilmFormat'];
        $this->priceCardName = $VeeziAPIData[$key]['PriceCardName'];
        $this->attributes = $VeeziAPIData[$key]['Attributes'];
        $this->audioLanguage = $VeeziAPIData[$key]['AudioLanguage'];
    }

    function updateShowtimeFields ($post_id) {

        update_field('field_56a12595938bf', $this->featureStartTime, $post_id);
        update_field('field_56a1261d938c0', $this->featureEndTime, $post_id);
        update_field('field_56a12574938be', $this->filmId, $post_id);
        update_field('field_56a12677938c1', $this->title, $post_id);
        update_field('field_56a126f7938c4', $this->status, $post_id);
        update_field('field_56a12692938c2', $this->screenId, $post_id);
        update_field('field_56a126bb938c3', $this->seatsAvailable, $post_id);
    }
}