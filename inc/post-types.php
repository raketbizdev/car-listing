<?php
// inc/post-types.php

class CarListingPostType {
    
  private $text_domain = 'your-plugin-textdomain';

  public function __construct() {
      add_action('init', array($this, 'register_car_listing'));
  }

  public function register_car_listing() {
      $labels = $this->get_labels();
      $args = $this->get_args($labels);

      register_post_type('car_listing', $args);
  }

  private function get_labels() {
      return array(
          'name'               => _x('Car Listings', 'post type general name', $this->text_domain),
          'singular_name'      => _x('Car Listing', 'post type singular name', $this->text_domain),
          'menu_name'          => _x('Car Listings', 'admin menu', $this->text_domain),
          'name_admin_bar'     => _x('Car Listing', 'add new on admin bar', $this->text_domain),
          'add_new'            => _x('Add New', 'car listing', $this->text_domain),
          'add_new_item'       => __('Add New Car Listing', $this->text_domain),
          'new_item'           => __('New Car Listing', $this->text_domain),
          'edit_item'          => __('Edit Car Listing', $this->text_domain),
          'view_item'          => __('View Car Listing', $this->text_domain),
          'all_items'          => __('All Car Listings', $this->text_domain),
          'search_items'       => __('Search Car Listings', $this->text_domain),
          'parent_item_colon'  => __('Parent Car Listings:', $this->text_domain),
          'not_found'          => __('No car listings found.', $this->text_domain),
          'not_found_in_trash' => __('No car listings found in Trash.', $this->text_domain)
      );
  }

  private function get_args($labels) {
      return array(
          'labels'             => $labels,
          'description'        => __('Description.', $this->text_domain),
          'public'             => true,
          'publicly_queryable' => true,
          'show_ui'            => true,
          'show_in_menu'       => true,
          'query_var'          => true,
          'rewrite'            => array('slug' => 'car-listing'),
          'capability_type'    => 'post',
          'has_archive'        => true,
          'hierarchical'       => false,
          'menu_position'      => 5,
          'menu_icon'          => 'dashicons-car',
          'show_in_rest'       => true,
          'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'author', 'taxonomies')
      );
  }
}
