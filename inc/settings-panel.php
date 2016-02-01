<head>
    <script>
        wpmt_option_action_in_progress = function(wpmt_progress_id, button_id) {
            document.getElementById(wpmt_progress_id).innerHTML = '<img src="/wp-content/plugins/movie-theater/images/wpmt_indicator.gif" style=' + "'width: 15px; height: 15px;'" + '" /> In Progress... (this may take a few minutes)';
            document.getElementById(button_id).style.display = 'none';
            if (button_id == "wpmt_manual_update") {
                document.getElementById('wpmt_manual_delete_all_posts').disabled = true;
                document.getElementById('wpmt_manual_reset').disabled = true;
            }
            if (button_id == "wpmt_manual_reset") {
                document.getElementById('wpmt_manual_delete_all_posts').disabled = true;
                document.getElementById('wpmt_manual_update').disabled = true;
            }
            if (button_id == "wpmt_manual_delete_all_posts") {
                document.getElementById('wpmt_manual_reset').disabled = true;
                document.getElementById('wpmt_manual_update').disabled = true;
            }

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

function wpmt_do_option_updates()
{
    //need to add a verification of user priviledges in here
    if (isset($_POST['wpmt_manual_reset'])) {
        wpmt_delete_all_posts('WPMT_Film');
        wpmt_delete_all_posts('WPMT_Performance');
        wpmt_delete_all_posts('WPMT_Session');
        wpmt_run();
    }
    if (isset($_POST['wpmt_manual_update'])) {
        wpmt_run();

    }
    if (isset($_POST['wpmt_manual_delete_all_posts'])) {
        wpmt_delete_all_posts('WPMT_Film');
        wpmt_delete_all_posts('WPMT_Performance');
        wpmt_delete_all_posts('WPMT_Session');
    }

}
function wpmt_admin_menu() {
    add_options_page( 'WP_Movie_Theater', 'WP Movie Theater', 'manage_options', 'WP_Movie_Theater', 'wpmt_options_page' );
}


function wpmt_admin_menu_init() {

    add_settings_section( 'wpmt_veezi_key_section', 'Veezi Key', 'wpmt_veezi_key_section_callback', 'WP_Movie_Theater' );
        register_setting( 'wpmt_settings_group', 'wpmt_veezi_token' );
        add_settings_field( 'wpmt_veezi_key_field', 'Enter Your Veezi Key', 'wpmt_veezi_key_field_callback', 'WP_Movie_Theater', 'wpmt_veezi_key_section' );

    add_settings_section( 'wpmt_manual_controls_section', 'Manually Control WP Movie Theater ', 'wpmt_manual_controls_section_callback', 'WP_Movie_Theater' );
        add_settings_field( 'wpmt_manual_update_field', 'Update film and performance posts', 'wpmt_manual_update_field_callback', 'WP_Movie_Theater', 'wpmt_manual_controls_section' );
        add_settings_field( 'wpmt_delete_all_posts_field', 'Delete all film and performance posts', 'wpmt_delete_all_posts_field_callback', 'WP_Movie_Theater', 'wpmt_manual_controls_section' );
        add_settings_field( 'wpmt_manual_reset_field', 'Delete all current film and performance posts and then update', 'wpmt_manual_reset_field_callback', 'WP_Movie_Theater', 'wpmt_manual_controls_section' );
}
// ==============  Section Callback functions ================= //
function wpmt_veezi_key_section_callback() {
    echo 'Your Veezi API key can be found in your Veezi account: https://my.us.veezi.com/Api/Index';
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
function wpmt_manual_reset_field_callback() {
    echo "<input type='submit' name='wpmt_manual_reset' id='wpmt_manual_reset' Onclick='wpmt_option_action_in_progress(" . '"wpmt_manual_reset_progress"' . ", " . '"wpmt_manual_reset"' . ")' value='Reset'/> ";
    echo "<span id='wpmt_manual_reset_progress'></span>";
}
function wpmt_manual_update_field_callback() {
    echo "<input type='submit' name='wpmt_manual_update' id='wpmt_manual_update' Onclick='wpmt_option_action_in_progress(" . '"wpmt_manual_update_progress"' . ", " . '"wpmt_manual_update"' . ")' value='Update'/> ";
    echo "<span id='wpmt_manual_update_progress'></span>";
}
function wpmt_delete_all_posts_field_callback() {
        echo "<input type='submit' name='wpmt_manual_delete_all_posts' id='wpmt_manual_delete_all_posts' Onclick='wpmt_option_action_in_progress(" . '"wpmt_manual_delete_all_posts_progress"' . ", " . '"wpmt_manual_delete_all_posts"' . ")' value='Delete Posts'/> ";
        echo "<span id='wpmt_manual_delete_all_posts_progress'></span>";
}


function wpmt_options_page() {
    ?>
    <div class="wrap">
        <h2>My Plugin Options</h2>
        <form action="options.php" method="POST">
            <?php settings_fields( 'wpmt_settings_group' ); ?>
            <?php do_settings_sections( 'WP_Movie_Theater' ); ?>
        </form>
    </div>
    <?php
}
?>