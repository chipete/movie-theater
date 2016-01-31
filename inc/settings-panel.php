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
    if (isset($_POST['wpmt_manual_update'])) {
        wpmt_run();

    }
    if (isset($_POST['wpmt_manual_delete_all_posts'])) {
        wpmt_delete_all_posts('WPMT_Film');
        wpmt_delete_all_posts('WPMT_Session');
    }
}
function wpmt_admin_menu() {
    add_options_page( 'WP_Movie_Theater', 'WP Movie Theater', 'manage_options', 'WP_Movie_Theater', 'wpmt_options_page' );
}


function wpmt_admin_menu_init() {
    register_setting( 'wpmt_settings_group', 'wpmt_veezi_token' );
    add_settings_section( 'wpmt_veezi_key_section', 'Veezi Key', 'wpmt_veezi_key_section_callback', 'WP_Movie_Theater' );
    add_settings_field( 'wpmt_veezi_key_field', 'Enter Your Veezi Key', 'wpmt_veezi_key_field_callback', 'WP_Movie_Theater', 'wpmt_veezi_key_section' );

    register_setting( 'wpmt_settings_group', 'wpmt_manual_update' );
    add_settings_section( 'wpmt_manual_controls_section', 'Manually Control WP Movie Theater ', 'wpmt_manual_controls_section_callback', 'WP_Movie_Theater' );
    add_settings_field( 'wpmt_manual_update_field', 'Force film updates', 'wpmt_manual_update_field_callback', 'WP_Movie_Theater', 'wpmt_manual_controls_section' );


    register_setting( 'wpmt_settings_group', 'wpmt_manual_delete_all_posts' );
    add_settings_field( 'wpmt_delete_all_posts_field', 'Delete all film posts', 'wpmt_delete_all_posts_field_callback', 'WP_Movie_Theater', 'wpmt_manual_controls_section' );
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
    echo "<input type='text' size='28' name='wpmt_veezi_token' value='$setting' />";
}
function wpmt_manual_update_field_callback() {
    echo "<input type='submit' name='wpmt_manual_update' value='Manual Update'/>";
}
function wpmt_delete_all_posts_field_callback() {
        echo "<input type='submit' name='wpmt_manual_delete_all_posts' value='Delete All Film Posts'/>";
}

function wpmt_options_page() {
    ?>
    <div class="wrap">
        <h2>My Plugin Options</h2>
        <form action="options.php" method="POST">


            <?php settings_fields( 'wpmt_settings_group' ); ?>
            <?php do_settings_sections( 'WP_Movie_Theater' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
?>