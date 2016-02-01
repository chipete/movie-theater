<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/1/16
 * Time: 8:32 AM
 */
?>

<?php function wpmt_display_film() { ?>

	<div id='container'>

		<!-- Film Header -->
		<div class="row">
			<div class="col-md-10">
				<h3><a href = "<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<p class="text-muted"><?php the_field('wpmt_film_genre'); ?> / <?php the_field('wpmt_film_rating'); ?></p>
			</div>
		</div><!-- end of Film Header -->


		<!-- Film Body -->
		<hr /><br />
		<div class="row">

			<!-- comment out image for now
			<div class="col-md-4">
				<img src="http://placehold.it/250x366" alt="<?php// get_field('wpmt_film_short_name'); ?>" title="<?php// get_field('wpmt_film_short_name'); ?>" />
			</div>-->

			<div class="col-md-8">
				<p class="lead"><?php the_field('wpmt_film_synopsis'); ?></p>

				<br />
				<?php //save the post
				global $post;
				$backup = $post;
				?>
				<!-- list the date and times -->
				<?php wpmt_display_sessions( get_field( 'wpmt_film_id' ) ); ?>

				<?php //restore the post
				$post = $backup;
				?>
			</div>

		</div><!-- end of Film Body -->


		<!-- people listing -->
		<br /><br /><hr />
		<div class="row">

			<?php if ( get_field( 'wpmt_film_directors' ) ) : ?>
				<div class="col-md-4">
					<h4>Directors</h4>
					<?php the_field('wpmt_film_directors'); ?>
				</div>
			<?php endif ?>

			<?php if ( get_field( 'wpmt_film_actors' ) ) : ?>
				<br />
				<div class="col-md-4">
					<h4>Actors</h4>
					<?php the_field('wpmt_film_actors'); ?>
				</div>
			<?php endif ?>

		</div><!-- end of people listing -->

		<br /><hr />

	</div><!-- end of container -->

<?php } //end function ?>


<?php
function wpmt_display_sessions( $film_id ) {

	//list sessions by date
	$args = array(
		'post_type'         => 'WPMT_Session',
		'meta_query'        => array(
			array(
				'key'           => 'wpmt_session_film_id',
				'value'         =>  $film_id,
				'compare'       => '=='
			)
		),
		'posts_per_page'    => '-1',
		'meta_key'          => 'wpmt_session_start',
		'orderby'           => 'meta_value',
		'order'             => 'ASC'
	);
	$my_query2 = new WP_Query( $args );

	if ( $my_query2->have_posts() ) {

		echo '<h4>Book Tickets</h4><hr />';


		while ( $my_query2->have_posts() ) {

			$my_query2->the_post();
			$timestamp = strtotime( get_field( 'wpmt_session_start' ) );
			$this_date = date( 'l, M j', $timestamp );

			if ( $this_date != $prev_date ) {
				echo '<h5>' . date( 'l, M j', $timestamp ) . '</h5>';
			}

			echo '<div class="btn-group" role="group" aria-label="">';
			echo '<a class="btn btn-info"
                     href="' . get_field( 'wpmt_session_ticket_url' ) . '"
                     target="_blank">' . date( 'g:ia', $timestamp ) . '</a>';

			if ( $this_date != $prev_date ) {
				echo '</div>';
				$prev_date = $this_date;
			}
		} //end while
	} //endif

} //end function
?>
