<?php

/**
 * Plugin Name: AutoCred
 * Plugin URI: https://www.bedevious.co.uk/
 * Description: This plugin provides shortcodes to automatically generate WP Types CRED forms based on field groups and rules
 * Version: 0.1
 * Author: Be Devious Web Development
 * Author URI: Plugin URI: https://www.bedevious.co.uk/
 * License: TBC
 */

/**
 * Include all required files for the plugin
 *
 * @category Administration
 */

include_once( 'inc/class/class.auto-cred.php' );


/**
 * Enqueue styles and scripts
 *
 * @category Administration
 */

function slug_enqueue_styles_and_scripts() {

}

add_action( 'wp_enqueue_scripts', 'slug_enqueue_styles_and_scripts' );