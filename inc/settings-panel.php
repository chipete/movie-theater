<?php
/**
 * Created by PhpStorm.
 * User: Chris, Ryan
 * Date: 1/29/16
 * Time: 8:43 AM
 */


add_action( 'admin_menu', 'wpmt_admin_menu' );
function wpmt_admin_menu() {
    add_options_page( 'WP_Movie_Theater', 'WP Movie Theater', 'manage_options', 'WP_Movie_Theater', 'wpmt_options_page' );
}


add_action( 'admin_init', 'wpmt_admin_menu_init' );
function wpmt_admin_menu_init() {
    register_setting( 'wpmt_settings_group', 'wpmt_veezi_token' );
    add_settings_section( 'veezi_key_section', 'Veezi Key', 'veezi_key_section_callback', 'WP_Movie_Theater' );
    add_settings_field( 'veezi_key_field', 'Enter Your Veezi Key', 'veezi_key_field_callback', 'WP_Movie_Theater', 'veezi_key_section' );
}

function veezi_key_section_callback() {
    echo 'Your Veezi API key can be found in your Veezi account: https://my.us.veezi.com/Api/Index';
}

function veezi_key_field_callback() {
    $setting = esc_attr( get_option( 'wpmt_veezi_token' ) );
    echo "<input type='text' size='28' name='wpmt_veezi_token' value='$setting' />";
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