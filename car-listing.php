<?php
/**
 * Plugin Name: Car Listing
 * Description: A comprehensive Car Listings plugin that offers a custom post type specifically for listing cars. It comes with integrated widgets, and powerful shortcodes for enhanced flexibility. The plugin provides its own single and archive index template for display and a dedicated settings page for easy customization.
 * Plugin URI: https://wordpress.org/plugins/car-listing
 * Version: 1.0.0
 * Author: Ruel Nopal
 * Author URI: https://rnopal.com
 * License: GPL v2 or later
 */

// Prevent direct file access

defined('ABSPATH') || exit; // Prevent direct file access

// Include core class
require_once plugin_dir_path(__FILE__) . 'inc/class-car-listing-core.php';

// Kick things off
Car_Listing_Core::instance();