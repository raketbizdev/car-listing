<?php
/**
 * Plugin Name: Car Listing
 * Description: A comprehensive Car Listings plugin that offers a custom post type specifically for listing cars. It comes with integrated widgets, and powerful shortcodes for enhanced flexibility. The plugin provides its own single and archive index template for display and a dedicated settings page for easy customization.
 * Plugin URI: https://github.com/raketbizdev/car-listing
 * Version: 1.0.0
 * Author: Ruel Nopal
 * Author URI: https://rnopal.com
 * License: GPL v2 or later
 */

// Security Measure: Direct File Access Prevention
// This ensures that the PHP file cannot be accessed directly from the browser.

defined('ABSPATH') || exit; // If the ABSPATH constant is not defined, exit the script. This prevents direct access to the file.

// Include core class for the plugin
// This is where the main functionality of the plugin resides. Including this file makes the core class available for the plugin.

require_once plugin_dir_path(__FILE__) . 'inc/class-car-listing-core.php';

// Initialize the Plugin
// This creates an instance of the Car_Listing_Core class, which will initiate the functionalities of the plugin.

Car_Listing_Core::instance();