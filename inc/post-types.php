<?php
// inc/post-types.php

class CarListingPostType {

  /**
   * Retrieve the text domain used for localization. 
   * This is abstracted to ensure consistent translation references across the plugin.
   *
   * @return string The text domain used for translations.
   */
  protected function get_text_domain() {
      return Car_Listing_Core::get_text_domain();
  }
  
  /**
   * Constructor method.
   * Hooks the registration of the custom post type to the 'init' action.
   */
  public function __construct() {
    add_action('init', array($this, 'register_car_listing'));
  }

  /**
   * Register the custom post type 'car_listing' with WordPress.
   * It configures and sets up labels and arguments for the post type.
   */
  public function register_car_listing() {
    $labels = $this->get_labels();
    $args = $this->get_args($labels);

    register_post_type('car_listing', $args);
  }

  /**
   * Generate and return the labels for the custom post type.
   * These labels are used by WordPress in the dashboard and other places 
   * where the post type is represented.
   *
   * @return array The array of labels for the custom post type.
   */
  private function get_labels() {
    return array(
      'name'               => _x('Car Listings', 'post type general name', $this->get_text_domain()), // Represents the name of the post type in a general context.
      'singular_name'      => _x('Car Listing', 'post type singular name', $this->get_text_domain()), // The singular name of the post type.
      'menu_name'          => _x('Car Listings', 'admin menu', $this->get_text_domain()), // The text used to display in the admin sidebar menu.
      'name_admin_bar'     => _x('Car Listing', 'add new on admin bar', $this->get_text_domain()), // The name given to the post type in the "Add New" context on the admin bar.
      'add_new'            => _x('Add New', 'car listing', $this->get_text_domain()), // The "Add New" text used in the admin sidebar.
      'add_new_item'       => __('Add New Car Listing', $this->get_text_domain()), // The header text on the "Add New" post screen.
      'new_item'           => __('New Car Listing', $this->get_text_domain()), // The header text when viewing a singular post type item.
      'edit_item'          => __('Edit Car Listing', $this->get_text_domain()), // The header text when editing an existing post type item.
      'view_item'          => __('View Car Listing', $this->get_text_domain()), // The header text when viewing a singular post type item.
      'all_items'          => __('All Car Listings', $this->get_text_domain()), // The text used for the all items list in the menu.
      'search_items'       => __('Search Car Listings', $this->get_text_domain()), // The search placeholder text for the post type.
      'parent_item_colon'  => __('Parent Car Listings:', $this->get_text_domain()), // The text used to describe parent items for hierarchical post types.
      'not_found'          => __('No car listings found.', $this->get_text_domain()), // Message displayed when no items are found.
      'not_found_in_trash' => __('No car listings found in Trash.', $this->get_text_domain()) // Message displayed when no items are found in trash.
    );
}


  /**
   * Generate and return the arguments for registering the custom post type.
   * These arguments configure how the post type behaves and how it's represented in the WordPress admin.
   *
   * @param array $labels The array of labels for the post type.
   * @return array The array of arguments used to register the post type.
   */
  private function get_args($labels) {
    return array(
      'labels'             => $labels,                                        // An array of labels for this post type. 
      'description'        => __('Description.', $this->get_text_domain()),   // A brief description of this post type.
      'public'             => true,                                           // Whether the post type should be publicly queryable.
      'publicly_queryable' => true,                                           // Whether queries can be performed in the front end part of the site.
      'show_ui'            => true,                                           // Whether to generate and allow a UI for managing this post type in the admin.
      'show_in_menu'       => true,                                           // Whether to show this post type in the admin menu.
      'query_var'          => true,                                           // Whether to allow this post type to be queried.
      'rewrite'            => array('slug' => 'car-listing'),                 // Triggers the handling of rewrites for this post type. The 'slug' specifies the URL segment to be used.
      'capability_type'    => 'post',                                         // String to use to build the read, edit, and delete capabilities. 
      'has_archive'        => true,                                           // Enables post type archives. Will use string as archive slug.
      'hierarchical'       => false,                                          // Whether the post type is hierarchical, like pages.
      'menu_position'      => 20,                                              // The position in the menu order where it should appear. 
      'menu_icon'          => 'dashicons-car',                                // The name of the icon to display in the menu (in this case, a car).
      'show_in_rest'       => true,                                           // Whether to include the post type in the REST API.
      'supports'           => array('title','editor', 'thumbnail', 'excerpt', 'author', 'taxonomies') // An array of the features this post type supports.
    );
  }

}
