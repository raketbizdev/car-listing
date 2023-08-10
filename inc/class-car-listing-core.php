<?php
// inc/class-car-listing-core.php

class Car_Listing_Core {

  private static $instance = null;

  public static function instance() {
      if (null === self::$instance) {
          self::$instance = new self();
      }
      return self::$instance;
  }

  private function __construct() {
      $this->load_dependencies();
      $this->initialize();
      
  }
  public function car_listing_archive_template($archive_template) {
    global $post;

    if (is_post_type_archive('car_listing')) {
        $archive_template = plugin_dir_path( __FILE__ ) . 'template/archive-car_listing.php';
    }
    return $archive_template;
  }
  private function load_dependencies() {
    require_once(plugin_dir_path(__FILE__) . 'plugin-activation.php');
    require_once(plugin_dir_path(__FILE__) . 'post-types.php');
    require_once(plugin_dir_path(__FILE__) . 'brand-taxonomy.php');
    require_once(plugin_dir_path(__FILE__) . 'type-taxonomy.php');
    require_once(plugin_dir_path(__FILE__) . 'meta-boxes.php');
    require_once(plugin_dir_path(__FILE__) . 'listing-setting.php');
    require_once(plugin_dir_path(__FILE__) . 'thumbnails.php');
    require_once(plugin_dir_path(__FILE__) . 'widget-listing.php');
    require_once(plugin_dir_path(__FILE__) . 'car-listings-handler-sc.php');
    require_once(plugin_dir_path(__FILE__) . 'class-car-listing-rest-controller.php');

      // ... load other dependencies ...
  }

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
      new Car_Listing_REST_Controller();

      add_action('widgets_init', function() {
        register_widget('Car_Listing_Widget');
      });

      $this->enqueue_styles();

      add_filter('single_template', [$this, 'car_listing_single_template']);
      add_filter('archive_template', [$this, 'car_listing_archive_template']);

      // ... initialize other classes ...
  }

  // The text domain for translations.
  protected static $text_domain = 'carlisting';

  // ... other methods and properties for this class ...

  /**
   * Getter method for the text domain.
   * 
   * @return string The text domain.
   */
  public static function get_text_domain() {
      return self::$text_domain;
  }

  /**
   * Enqueue the plugin's styles.
   */
  private function enqueue_styles() {
    add_action('wp_enqueue_scripts', [$this, 'enqueue_car_listing_styles']);
  }

  public function enqueue_car_listing_styles() {
      // Ensure to use plugins_url to get the correct path to your CSS file.
      wp_enqueue_style(
          'car-listing-styles',
          plugins_url('css/car-listing-styles.css', dirname(__FILE__)),
          array(),
          '1.0.0'
      );
  }

  public function car_listing_single_template($single_template) {
    global $post;

    if ($post->post_type == 'car_listing') {
        $single_template = plugin_dir_path( __FILE__ ) . 'template/single-car_listing.php';
    }
    return $single_template;
  }

}