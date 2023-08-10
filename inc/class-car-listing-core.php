<?php
// inc/class-car-listing-core.php

/**
 * Core class for the Car Listing plugin.
 * 
 * This class is responsible for initializing the plugin by loading all necessary dependencies
 * and setting up hooks for the plugin's functionality.
 */

class Car_Listing_Core {
  // Singleton instance to ensure that only one instance of the class is loaded.
  private static $instance = null;

  /**
   * Singleton pattern instance method.
   * Ensures only one instance of the class is loaded and used.
   *
   * @return Car_Listing_Core Singleton instance of the class.
   */
  public static function instance() {
      if (null === self::$instance) {
          self::$instance = new self();
      }
      return self::$instance;
  }

  /**
   * Constructor method.
   * 
   * Private to prevent creating multiple instances and to use it as a Singleton.
   * Loads plugin dependencies and initializes plugin functionalities.
   */
  private function __construct() {
      $this->load_dependencies();
      $this->initialize();
      
  }

  /**
   * Filter for customizing the archive template for car listings.
   *
   * @param string $archive_template The path of the archive template to include.
   * @return string Modified archive template path.
   */
  public function car_listing_archive_template($archive_template) {
    global $post;

    // Use custom archive template for 'car_listing' post type.
    if (is_post_type_archive('car_listing')) {
        $archive_template = plugin_dir_path( __FILE__ ) . 'template/archive-car_listing.php';
    }
    return $archive_template;
  }
  /**
   * Load plugin dependencies.
   * 
   * Includes all necessary files for the plugin.
   */
  private function load_dependencies() {
    // Various dependencies like activation scripts, post types, taxonomies, etc.
    require_once(plugin_dir_path(__FILE__) . 'plugin-activation.php');
    require_once(plugin_dir_path(__FILE__) . 'post-types.php');
    require_once(plugin_dir_path(__FILE__) . 'brand-taxonomy.php');
    require_once(plugin_dir_path(__FILE__) . 'type-taxonomy.php');
    require_once(plugin_dir_path(__FILE__) . 'meta-boxes.php');
    require_once(plugin_dir_path(__FILE__) . 'listing-setting.php');
    require_once(plugin_dir_path(__FILE__) . 'thumbnails.php');
    require_once(plugin_dir_path(__FILE__) . 'widget-listing.php');
    require_once(plugin_dir_path(__FILE__) . 'car-listings-handler-sc.php');

      // ... load other dependencies ...
  }

  /**
   * Initialize the plugin.
   * 
   * Set up custom post types, taxonomies, meta boxes, settings, and other functionalities.
   */
  private function initialize() {
      // Initialize different functionalities
      new CarListingPostType();
      new Brand_Taxonomy();
      new Type_Taxonomy();
      new Meta_Boxes();
      new Car_Listing_Activation();
      new ListingSettings();
      new Thumbnails();
      new CarListingsShortcode();

      add_action('widgets_init', function() {
        register_widget('Car_Listing_Widget');
      });

      $this->enqueue_styles();

      add_filter('single_template', [$this, 'car_listing_single_template']);
      add_filter('archive_template', [$this, 'car_listing_archive_template']);

      // ... initialize other classes ...
  }

   // Text domain for plugin translations.
  protected static $text_domain = 'carlisting';

  // ... other methods and properties for this class ...

  /**
   * Getter method for the text domain.
   * 
   * @return string The text domain for translations.
   */
  public static function get_text_domain() {
      return self::$text_domain;
  }

  /**
   * Setup method to enqueue the plugin's styles.
   * 
   * Uses 'wp_enqueue_scripts' action to hook the style enqueue method.
   */
  private function enqueue_styles() {
    add_action('wp_enqueue_scripts', [$this, 'enqueue_car_listing_styles']);
  }

  /**
   * Enqueue styles specific to the Car Listing feature.
   */
  public function enqueue_car_listing_styles() {
      // Ensure to use plugins_url to get the correct path to your CSS file.
      wp_enqueue_style(
          'car-listing-styles',
          plugins_url('css/car-listing-styles.css', dirname(__FILE__)),
          array(),
          '1.0.0'
      );
  }

  /**
   * Filter for customizing the single post template for car listings.
   *
   * @param string $single_template The path of the single post template to include.
   * @return string Modified single post template path.
   */
  public function car_listing_single_template($single_template) {
    global $post;

    if ($post->post_type == 'car_listing') {
        $single_template = plugin_dir_path( __FILE__ ) . 'template/single-car_listing.php';
    }
    return $single_template;
  }

}