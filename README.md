# wp-movie-theater

This plugin is in ALPHA development. Not fully functional. Please do not download or install, especially on a live site. may break your WP install.

*** requires "Advanced Custom Fields" plugin ***


=============== Available Fields ==================


WPMT_Session

wpmt_session_start              ie: 2016-01-14T18:30:00
wpmt_session_end                ie: 2016-01-14T19:04:00
wpmt_session_film_id            ID of film/performance
wpmt_session_film_title         Title of film/performance
wpmt_session_status             ie: Open, Closed, Planned
wpmt_session_screen_id          ie: 4
wpmt_session_seats_available    ie: 137
wpmt_session_ticket_url         ie: https://ticketing.us.veezi.com/purchase/6898?siteToken=TxTIIqmyZE6D2x7lOm%2fRiQ%3d%3d


WPMT_Film

wpmt_film_id
wpmt_film_short_name
wpmt_film_status                ie: Active, Inactive, Deleted
wpmt_film_next_session_start    ie: 2016-01-14T18:30:00
wpmt_film_opening_date
wpmt_film_format
wpmt_film_synopsis
wpmt_film_genre
wpmt_film_rating
wpmt_film_duration              in minutes  ie: 120
wpmt_film_distributor
wpmt_film_audio_language
wpmt_film_directors             comma seperated
wpmt_film_actors                comma seperated
wpmt_film_content_advisory      ie: Rated PG-13 for a mature thematic image and some sci-fi action/violence
wpmt_film_poster                vertical poster approx. 250x366. save format is the attachment_id ie: 5125
wpmt_film_image                 film featured image. Approx. 480x290. save format is the attachment_id ie: 5125
wpmt_film_hide                  boolean
wpmt_film_free                  boolean
wpmt_film_youtube_url           ie: https://www.youtube.com/embed/CTavNwUgapo
wpmt_film_rt_rating
wpmt_film_rt_consensus
wpmt_film_reviews


WPMT_Performance

wpmt_performance_id
wpmt_performance_short_name
wpmt_performance_status                ie: Active, Inactive, Deleted
wpmt_performance_next_session_start    ie: 2016-01-14T18:30:00
wpmt_performance_opening_date
wpmt_performance_format
wpmt_performance_synopsis
wpmt_performance_genre
wpmt_performance_rating
wpmt_performance_duration              in minutes  ie: 120
wpmt_performance_distributor
wpmt_performance_audio_language
wpmt_performance_directors             comma seperated
wpmt_performance_actors                comma seperated
wpmt_performance_content_advisory      ie: Rated PG-13 for a mature thematic image and some sci-fi action/violence
wpmt_performance_poster                vertical poster approx. 250x366. save format is the attachment_id ie: 5125
wpmt_performance_image                 performance featured image. Approx. 480x290. save format is the attachment_id ie: 5125
wpmt_performance_hide                  boolean
wpmt_performance_free                  boolean
wpmt_performance_youtube_url           ie: https://www.youtube.com/embed/CTavNwUgapo
wpmt_performance_reviews