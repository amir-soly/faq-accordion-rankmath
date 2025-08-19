<?php
/*
Plugin Name: Easy FAQ Accordion for Rank Math
Plugin URI:  https://github.com/amir-soly/faq-accordion-rankmath
Description: The easiest way to display your Rank Math FAQ schema in a beautiful, responsive accordion.
Version:     1.1
Author:      Amir Soli
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: easy-faq-accordion-for-rank-math
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Load the main plugin class.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-faq-accordion.php';

// Initialize the plugin.
function easy_faq_accordion_run() {
    $plugin = new FAQ_Accordion();
    $plugin->run();
}
easy_faq_accordion_run();