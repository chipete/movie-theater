# wp-movie-theater

This plugin is in ALPHA development. Not fully functional. Please do not download or install, may break your system.

requires "Advanced Custom Fields" plugin

As of 1.3 recommended to install "WP Control" to force cron jobs to run (and therefore test the add/remove performances & sessions)


Good example of complex meta_query

<?php
$args = array(
    ‘post_type’ => ‘product’,
    ‘meta_query’ => array(
        ‘relation’ => ‘OR’,
        array(
            ‘key’ => ‘color’,
            ‘value’ => ‘orange’,
            ‘compare’ => ‘=’,
        ),

        array(
            ‘relation’ => ‘AND’,
            array(
                ‘key’ => ‘color’,
                ‘value’ => ‘red’,
                ‘compare’ => ‘=’,
            ),
            array(
                ‘key’ => ‘size’,
                ‘value’ => ‘small’,
                ‘compare’ => ‘=’
            )
        )
    )
);

$query = new WP_Query( $args );
?>





old functions


	//get info from IMDB

	function get_imdb ($title) {

		$imdb_data = $m->getMovieInfo( $title );
		$imdb_return = array();

		if ( $imdb_data ) {

				$imdb_return['director'] = $imdb_data['director'];
				$imdb_return['synopsis'] = $imdb_data['plot'];
				$imdb_return['genre']    = $imdb_data['genre[0]'];

		} // /if $imdb_data
		return $imdb_return;
	}


	//get poster from Box Office Mojo

	function get_boxofficemojo ( $title ) {
		$boxofficemojo = $b->getMovieInfo( $title ) );
		$bom_return = array();

		if ( $boxofficemojo ) {
			$bom_return['image'] = $boxofficemojo['image640'];
			$bom_return['poster'] = $boxofficemojo['poster'];
		}
	}



to-do:

delete 'unattached' media from media library (make that an option in settings)
check off what you want to manually run, then a "go" button

ie: