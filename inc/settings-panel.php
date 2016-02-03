<head>
    <script>
        wpmt_option_action_in_progress = function(wpmt_progress_id, button_id) {
            document.getElementById(wpmt_progress_id).innerHTML = '<img src="<?php echo plugins_url('../images/wpmt_indicator.gif', __FILE__); ?>" style=' + "'width: 15px; height: 15px;'" + '" /> In Progress... (this may take a few minutes)';
            document.getElementById(button_id).style.display = 'none';
        }
    </script>
</head>
<?php
/**
 * Created by PhpStorm.
 * User: Chris, Ryan
 * Date: 1/29/16
 * Time: 8:43 AM
 */
add_action( 'admin_init', 'wpmt_do_option_updates' );
add_action( 'admin_init', 'wpmt_admin_menu_init' );
add_action( 'admin_menu', 'wpmt_admin_menu' );

function wpmt_do_option_updates() {
    //need to add a verification of user priviledges in here
    if (isset($_POST['wpmt_manual_update']))
    //$wpmt_updates = get_option('wpmt_manual_updates_checkbox');
        $wpmt_updates = $_POST['wpmt_manual_updates_checkbox'];
    for ($i=0; $i<count($wpmt_updates); $i++) {
        if ($wpmt_updates[$i] == 'force delete all films') {
            wpmt_delete_all_posts('WPMT_Film');
        }
        if ($wpmt_updates[$i] == 'force delete all performances') {
            wpmt_delete_all_posts('WPMT_Performance');
        }
        if ($wpmt_updates[$i] == 'force delete all sessions') {
            wpmt_delete_all_posts('WPMT_Session');
        }
        if ($wpmt_updates[$i] == 'add new films and performances') {
            $my_token = esc_attr(get_option('wpmt_veezi_token'));
            $veezi_access_token = 'VeeziAccessToken: ' . $my_token;
            $film_and_performance_data = call_service('https://api.us.veezi.com/v1/film', $veezi_access_token);
            wpmt_update_posts($film_and_performance_data);
        }
        if ($wpmt_updates[$i] == 'add new sessions') {
            $my_token = esc_attr(get_option('wpmt_veezi_token'));
            $veezi_access_token = 'VeeziAccessToken: ' . $my_token;
            $session_data = call_service('https://api.us.veezi.com/v1/websession', $veezi_access_token);

            //1. delete all sessions
            wpmt_delete_all_posts('WPMT_Session');
            if (NULL == get_posts(array('post_type' => 'WPMT_Session'))) {
                wpmt_add_sessions($session_data);
            }
        }
        if (($wpmt_updates[$i] == 'update film formats') || ($wpmt_updates[$i] == 'update performance formats')) {


            $my_token = esc_attr(get_option('wpmt_veezi_token'));
            $veezi_access_token = 'VeeziAccessToken: ' . $my_token;
            $film_and_performance_data = call_service('https://api.us.veezi.com/v1/film', $veezi_access_token);


            $post_data_as_array = object_to_array($film_and_performance_data);
            for ($c = 0; $c < count($post_data_as_array); $c++) {
                if ( $post_data_as_array[$c]["Genre"] != "Festival" ) {

                    // if the format is 'not a film' and it's not a documentary, then make a performance
                    if (($post_data_as_array[$c]["Format"] == "Not a Film") && ($post_data_as_array[$c]["Genre"] != "Documentary")) {
                        if ($wpmt_updates[$i] == 'update performance formats') {
                            $performance = new WPMT_Performance;
                            $performance->assign_values($post_data_as_array, $c);


                            if (NULL != get_posts(array(
                                    'posts_per_page' => -1,
                                    'post_type' => 'WPMT_Performance',
                                    'meta_key' => 'wpmt_performance_id',
                                    'meta_value' => $performance->id
                                ))
                            ) {
                                $posts = get_posts(array(
                                    'posts_per_page' => -1,
                                    'post_type' => 'WPMT_Performance',
                                    'meta_key' => 'wpmt_performance_id',
                                    'meta_value' => $performance->id
                                ));
                                foreach ($posts as $post) {
                                    $performance->update_performance_format($post->ID);
                                }

                            }
                       }
                    } elseif ($wpmt_updates[$i] == 'update film formats') {
                            $film = new WPMT_Film;
                            $film->assign_values( $post_data_as_array, $c );
                            if (NULL != get_posts(array(
                                    'posts_per_page' => -1,
                                    'post_type' => 'WPMT_Film',
                                    'meta_key' => 'wpmt_film_id',
                                    'meta_value' => $film->id
                                ))
                            ) {
                                $posts = get_posts(array(
                                    'posts_per_page' => -1,
                                    'post_type' => 'WPMT_Film',
                                    'meta_key' => 'wpmt_film_id',
                                    'meta_value' => $film->id
                                ));
                                foreach ($posts as $post) {
                                    $film->update_film_format($post->ID);
                                }

                            }
                        }
                    }
            } // end for loop
        }
    }
}
function wpmt_admin_menu() {
    add_options_page( 'WP_Movie_Theater', 'WP Movie Theater', 'manage_options', 'WP_Movie_Theater', 'wpmt_options_page' );
}


function wpmt_admin_menu_init() {

    add_settings_section( 'wpmt_veezi_key_section', 'Veezi Key', 'wpmt_veezi_key_section_callback', 'WP_Movie_Theater' );
        register_setting( 'wpmt_settings_group', 'wpmt_veezi_token' );
        add_settings_field( 'wpmt_veezi_key_field', 'Enter Your Veezi Key', 'wpmt_veezi_key_field_callback', 'WP_Movie_Theater', 'wpmt_veezi_key_section' );

    add_settings_section( 'wpmt_veezi_custom_filters_section', 'Change and Add Filters', 'wpmt_veezi_custom_filters_callback', 'WP_Movie_Theater' );
        register_setting( 'wpmt_settings_group', 'wpmt_overwrite_format' );
        add_settings_field( 'wpmt_import_format_field', 'Overwrite format', 'wpmt_import_format_field_callback', 'WP_Movie_Theater', 'wpmt_veezi_custom_filters_section' );


    add_settings_section( 'wpmt_manual_controls_section', 'Manually Run WP Movie Theater ', 'wpmt_manual_controls_section_callback', 'WP_Movie_Theater' );
        add_settings_field( 'wpmt_manual_update_field', 'Manual Update', 'wpmt_manual_update_field_callback', 'WP_Movie_Theater', 'wpmt_manual_controls_section' );

}
// ==============  Section Callback functions ================= //
function wpmt_veezi_key_section_callback() {
    echo 'Your Veezi API key can be found in your Veezi account: https://my.us.veezi.com/Api/Index';
}
function wpmt_veezi_custom_filters_callback() {
    echo 'These options allow you to customize how WP Movie Theater imports film data from your ticket server';
}
function wpmt_manual_controls_section_callback() {
    echo "Use these settings to manually control actions of WP Movie Theater";
}

// ==============  Field Callback functions ================= //
function wpmt_veezi_key_field_callback() {
    $setting = esc_attr( get_option( 'wpmt_veezi_token' ) );
    if (esc_attr( get_option( 'wpmt_veezi_token' ) ) != "") {
        $wpmt_veezi_token_submit_button_value = "Update Token";
    }
    else {
        $wpmt_veezi_token_submit_button_value = "Save Token";
    }
    echo "<input type='text' size='28' name='wpmt_veezi_token' value='$setting' /> ";
    echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="' . $wpmt_veezi_token_submit_button_value . '"  />';
}

function wpmt_import_format_field_callback()
{
    if ((esc_attr( get_option( 'wpmt_overwrite_format', 'true' ) ) == "true") || (null == esc_attr( get_option( 'wpmt_overwrite_format'))))  {
        $overwrite_format = "checked";
    }
    else {
        $overwrite_format = "";
    }
    echo "<input type='hidden' name='wpmt_overwrite_format' value='No' />";
    echo "<input type='checkbox' name='wpmt_overwrite_format' value='true'" . $overwrite_format . " /> Changes made on the ticket server to the format of a film or performance  will automatically overwrite the film and performance posts on the website";
    echo '<br /><br /><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  />';
}

function wpmt_manual_update_field_callback() {

    echo "<input type='checkbox' name='wpmt_manual_updates_checkbox[]' value='force delete all films' /> Force delete all films<br />";
    echo "<input type='checkbox' name='wpmt_manual_updates_checkbox[]' value='force delete all performances' /> Force delete all performances<br />";
    echo "<input type='checkbox' name='wpmt_manual_updates_checkbox[]' value='force delete all sessions' /> Force delete all show times (sessions)<br /> <br />";

    echo "<input type='checkbox' name='wpmt_manual_updates_checkbox[]' value='add new films and performances' /> Add new films/performances<br />";
    echo "<input type='checkbox' name='wpmt_manual_updates_checkbox[]' value='add new sessions' /> Add new show times (sessions)<br /><br />";

    echo "<input type='checkbox' name='wpmt_manual_updates_checkbox[]' value='update film formats' /> Overwrite Film formats and genres <br />";
    echo "<input type='checkbox' name='wpmt_manual_updates_checkbox[]' value='update performance formats' /> Overwrite Performance formats and genres <br/> <br />";

    echo "<input type='submit' name='wpmt_manual_update' class='button button-primary' id='wpmt_manual_update' Onclick='wpmt_option_action_in_progress(" . '"wpmt_manual_update_progress"' . ", " . '"wpmt_manual_update"' . ")' value='Run Manual Updates'/> ";
    echo "<span id='wpmt_manual_update_progress'></span>";
}

function wpmt_options_page() {
    ?>
    <div class="wrap">
        <h2>WP Movie Theater Control Panel</h2>
        <form action="options.php" method="POST">
            <?php settings_fields( 'wpmt_settings_group' ); ?>
            <?php do_settings_sections( 'WP_Movie_Theater' ); ?>
        </form>
    </div>
    <?php
}
?>