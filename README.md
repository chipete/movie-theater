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