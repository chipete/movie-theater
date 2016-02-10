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
			<div class="col-md-4">
				<?php if ( ! empty( get_field( 'wpmt_film_youtube_url' ) ) ) : ?>
					<iframe width="640" height="360" src="<?php echo get_field( 'wpmt_film_youtube_url' ) . '?rel=0&amp;showinfo=0'; ?>" frameborder="0" allowfullscreen></iframe>
				<?php endif ?>
			</div>

			<div class="col-md-4">
				<?php echo wp_get_attachment_image( get_field('wpmt_film_poster'),
					$size = 'wpmt_poster',
					$icon = false,
					$attr = array ( 'alt' => get_the_title( $post ), 'title' => get_the_title( $post ) )
				); ?>
			</div>

			<div class="col-md-4">
				<?php echo wp_get_attachment_image( get_field( 'wpmt_film_image' ),
													$size = 'wpmt_image',
													$icon = false,
													$attr = array ( 'alt' => get_the_title( $post ), 'title' => get_the_title( $post ) )
													);
				?>
			</div>


			<div class="col-md-8">
				<p class="lead"><?php the_field( 'wpmt_film_synopsis' ); ?></p>

				<br />
				<?php //save the post
				global $post;
				$backup = clone $post;
				?>
				<!-- list the date and times -->
				<?php
				if ( wpmt_sessions_exist ( get_field( 'wpmt_film_id' ) ) ) {
					wpmt_display_sessions ( get_field( 'wpmt_film_id' ) );
				}

				else {
					echo "No tickets available at this time";
				}
				?>

				<?php //restore the post
				$post = clone $backup;
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

function wpmt_display_sessions( $film_id=null, $days=null ) {

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

	$my_query2 	= new WP_Query( $args );
	$i			= 0;

	if ( $my_query2->have_posts() ) {

		while ( $my_query2->have_posts() ) {

			$my_query2->the_post();
			$timestamp = strtotime( get_field( 'wpmt_session_start' ) );
			$this_date = date( 'l, M j', $timestamp );

			if ( $i === $days ) {
				return false;
			}

			if ( $this_date != $prev_date ) {
				echo '<br /><strong>' . date( 'l, M j', $timestamp ) . '</strong><br />';
				$prev_date = $this_date;
				$i++;
			}

			echo ' <a class="btn btn-info"
                     href="' . get_field( 'wpmt_session_ticket_url' ) . '"
                     target="_blank">' . date( 'g:ia', $timestamp ) . '</a> ';

		} //end while

	} //endif

} //end function


function wpmt_are_there_sessions( $film_id ) {

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
		return true;
	}
	else {
		return false;
	}
}

?>
