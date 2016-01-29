<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 1/29/16
 * Time: 8:43 AM
 */


// ------------------------------------------------------------------
// Add all your sections, fields and settings during admin_init
// ------------------------------------------------------------------
//
add_action( 'admin_menu', 'wpmt_menu' );
add_action( 'admin_init', 'wpmt_settings_api_init' );
function wpmt_menu() {
    //add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
    add_options_page(
        'WP Movie Theater Control Panel',
        'WP Movie Theater CP',
        'manage_options',
        'movie',
        'wpmt_admin_options'
    );
}
function wpmt_admin_options()
{
    do_settings_sections( 'wpmt_settings' );
}
function wpmt_settings_api_init() {
    // Add the section to reading settings so we can add our
    // fields to it
    add_settings_section(
        'wpmt_advanced_options',
        'Movie Theater Advanced Settings',
        'eg_setting_section_callback_function',
        'wpmt_settings'
    );
    // Add the field with the names and function to use for our new
    // settings, put it in our new section
    add_settings_field(
        'eg_wpmt_checkbox',
        'Test Checkbox',
        'eg_wpmt_checkbox_setting_callback_function',
        'wpmt_settings',
        'wpmt_advanced_options'
    );
    add_settings_field(
        'eg_wpmt_text',
        'Test text field',
        'eg_wpmt_text_setting_callback_function',
        'wpmt_settings',
        'wpmt_advanced_options'
    );
    // Register our setting so that $_POST handling is done for us and
    // our callback function just has to echo the <input>
    register_setting( 'wpmt_settings', 'eg_setting_name' );
} // eg_settings_api_init()
// ------------------------------------------------------------------
// Settings section callback function
// ------------------------------------------------------------------
//
// This function is needed if we added a new section. This function
// will be run at the start of our section
//
function eg_setting_section_callback_function() {
    echo '<p>Below are some amazing options I know you are going to want to mess with</p>';
}
// ------------------------------------------------------------------
// Callback function for our example setting
// ------------------------------------------------------------------
//
// creates a checkbox true/false option. Other types are surely possible
//
function eg_wpmt_checkbox_setting_callback_function() {
    echo '<input name="eg_wpmt_checkbox" id="eg_wpmt_checkbox" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'eg_wpmt_checkbox' ), false ) . ' /> Explanation text';
}
function eg_wpmt_text_setting_callback_function() {
    echo '<input name="eg_wpmt_text" id="eg_wpmt_text" type="text" value="fill me out" /> Explanation text';
    //since this is our last field time for the submit button!
    echo '<br /> <br />';
    echo '<input type="submit" value="Save Changes" />';
}