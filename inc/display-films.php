<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 1/29/16
 * Time: 2:38 PM
 */
?>

<!--
<div id='container'>
    <div class='general_title'> All Sessions </div>
    <?php //wpmt_display_sessions(); ?>
</div>
-->

<div id='container'>
    <div class='general_title'> All Films </div>
    <?php wpmt_display_films(); ?>
</div>


<?php function wpmt_display_sessions($film_id) { ?>

    <?php $my_query = new WP_Query( array( 'post_type' => 'WPMT_Session', 'meta_key' => 'wpmt_session_film_id', 'meta_value' => $film_id, 'posts_per_page' => '-1' ) ); ?>

    <?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>

        <div class='wpmt_session_title'> <?php wpmt_display_session(); ?></div>

    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>

<?php } ?>


<?php function wpmt_display_session() { ?>

    <?php the_field('wpmt_session_start'); ?>

<?php } ?>


<?php function wpmt_display_films() { ?>

    <?php // breaks it for some reason: 'meta_key' => 'wpmt_film_hide', 'meta_value' => null, ?>

    <?php $my_query = new WP_Query( array( 'post_type' => 'WPMT_Film', 'posts_per_page' => '-1' ) ); ?>

    <?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>

        <div class='wpmt_session_title'> <?php the_title(); ?></div>
        <div class='wpmt_session_body'>
            <?php wpmt_display_film(); ?>
        </div>

    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>

<?php } ?>


<?php function wpmt_display_film() { ?>
    <p>
    <?php the_field('wpmt_film_synopsis'); ?>
    </p><p>
    <?php wpmt_display_sessions( get_field( 'wpmt_film_id' ) ); ?>
    </p>
<?php } ?>




