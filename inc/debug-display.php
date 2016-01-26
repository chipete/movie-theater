<?php
/**
 * Created by PhpStorm.
 * User: edit5
 * Date: 1/26/16
 * Time: 12:09 PM
 *
 * This file include is commented out by default.
 */

echo "<div id='container'>";
echo "<div class='generalTitle'> All Show Times </div>";
$showTimeDataAsArray = objectToArray($showTimeData);
displayAllShowTimes($showTimeDataAsArray);
echo "</div>";
echo "<div id='container'>";
echo "<div class='generalTitle'> All Films </div>";
$film = new Film;
$filmDataAsArray = objectToArray($filmData);
displayAllFilms($filmDataAsArray);
echo "</div>";

function displayShowTime($showTime)
{
    echo "ID:" . $showTime->id . "<br />";
    echo "FilmId:" . $showTime->filmId . "<br />";
    echo "FilmPackageId:" . $showTime->filmPackageId . "<br />";
    echo "Title:" . $showTime->title . "<br />";
    echo "ScreenId:" . $showTime->screenId . "<br />";
    echo "Seating:" . $showTime->seating . "<br />";
    echo "AreComplimentariesAllowed:" . $showTime->areComplimentariesAllowed . "<br />";
    echo "ShowType:" . $showTime->showType . "<br />";
    echo "Sales Via:" . $showTime->salesVia[0] . ", " . $showTime->salesVia[1] . "<br />";
    echo "Status:" . $showTime->status . "<br />";
    echo "PreShowStartTime:" . $showTime->preShowStartTime . "<br />";
    echo "FeatureStartTime:" . $showTime->featureStartTime . "<br />";
    echo "FeatureEndTime:" . $showTime->featureEndTime . "<br />";
    echo "CleanupEndTime:" . $showTime->cleanupEndTime . "<br />";
    echo "SeatsAvailable:" . $showTime->seatsAvailable . "<br />";
    echo "SeatsHeld:" . $showTime->seatsHeld . "<br />";
    echo "SeatsHouse:" . $showTime->seatsHouse . "<br />";
    echo "SeatsSold:" . $showTime->seatsSold . "<br />";
    echo "FilmFormat:" . $showTime->filmFormat . "<br />";
    echo "PriceCardName:" . $showTime->priceCardName . "<br />";
    $attributesString = "";
    foreach ($showTime->attributes as $value) {
        $attributesString .= " " . $value;
    }
    echo "Attributes:" . $attributesString . "<br />";
    echo "AudioLanguage:" . $showTime->audioLanguage . "<br />";
}


function displayAllShowTimes($dataAsArray)
{
    for ($i=0; $i< count($dataAsArray); $i++) {
        $showTime = new movietheater_ShowTime;
        $showTime->assignValues($dataAsArray, $i);
        echo "<div class='showTimeTitle'>" . $showTime->title . "</div>";
        echo "<div class='showTimeBody'>";
        displayShowTime($showTime);
        echo "</div>";
    }
}


function displayFilm($film)
{
    echo "ID:" . $film->id . "<br />";
    echo "Title:" . $film->title . "<br />";
    echo "ShortName:" . $film->shortName . "<br />";
    echo "Synopsis:" . $film->synopsis . "<br />";
    echo "Genre:" . $film->genre . "<br />";
    echo "SignageText:" . $film->signageText . "<br />";
    echo "Distributor:" . $film->distributor . "<br />";
    echo "OpeningDate:" . $film->openingDate . "<br />";
    echo "Rating:" . $film->rating . "<br />";
    echo "Status:" . $film->status . "<br />";
    echo "Content:" . $film->content . "<br />";
    echo "Duration:" . $film->duration . "<br />";
    echo "DisplaySequence:" . $film->displaySequence . "<br />";
    echo "NationalCode:" . $film->nationalCode . "<br />";
    echo "Format:" . $film->format . "<br />";
    echo "IsRestricted:" . $film->isRestricted . "<br />";
    echo "People <br />";
    for ($i=0; $i<count($film->people); $i++)
    {
        echo "&nbsp; &nbsp; ID: " . $film->people[$i]["Id"] . "<br />";
        echo "&nbsp; &nbsp; First Name: " . $film->people[$i]["FirstName"] . "<br />";
        echo "&nbsp; &nbsp; Last Name: " . $film->people[$i]["LastName"] . "<br />";
        echo "&nbsp; &nbsp; Role: " . $film->people[$i]["Role"] . "<br />";
        echo "<br />";
    }
    echo "AudioLanguage:" . $film->audioLanguage . "<br />";
    echo "GovernmentFilmTitle:" . $film->governmentFilmTitle . "<br />";
}


function displayAllFilms($dataAsArray)
{
    for ($i=0; $i< count($dataAsArray); $i++) {
        $film = new movietheater_Film;
        $film->assignValues($dataAsArray, $i);
        echo "<div class='filmTitle'>" . $film->title . "</div>";
        echo "<div class='filmBody'>";
        displayFilm($film);
        echo "</div>";
    }
}