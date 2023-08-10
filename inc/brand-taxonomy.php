<?php
// inc/brand-taxonomy.php

class Brand_Taxonomy {

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
   * Constructor: Hooks into WordPress to register the taxonomy when initialized.
   */
  public function __construct() {
      add_action('init', array($this, 'register_brand_taxonomy'));
  }

  /**
   * Registers the 'brand' taxonomy for the 'car_listing' post type.
   */
  public function register_brand_taxonomy() {
    $labels = $this->get_labels();
    $args = $this->get_args($labels);
    
    register_taxonomy('brand', array('car_listing'), $args);
  }

  /**
   * Generates and returns the labels used in the WP admin for the brand taxonomy.
   * 
   * @return array An array of labels.
   */
  private function get_labels() {
    return array(
      'name'              => _x('Brands', 'taxonomy general name', $this->get_text_domain()),
      'singular_name'     => _x('Brand', 'taxonomy singular name', $this->get_text_domain()),
      'search_items'      => __('Search Brands', $this->get_text_domain()),
      'all_items'         => __('All Brands', $this->get_text_domain()),
      'parent_item'       => __('Parent Brand', $this->get_text_domain()),
      'parent_item_colon' => __('Parent Brand:', $this->get_text_domain()),
      'edit_item'         => __('Edit Brand', $this->get_text_domain()),
      'update_item'       => __('Update Brand', $this->get_text_domain()),
      'add_new_item'      => __('Add New Brand', $this->get_text_domain()),
      'new_item_name'     => __('New Brand Name', $this->get_text_domain()),
      'menu_name'         => __('Brands', $this->get_text_domain()),
    );
  }

  /**
   * Generates and returns the arguments needed for registering the taxonomy.
   * 
   * @param array $labels The labels for the taxonomy.
   * @return array An array of arguments.
   */
  private function get_args($labels) {
    return array(
        'hierarchical'      => true,                  // Determines if taxonomy is hierarchical like categories (true) or flat like tags (false).
        'labels'            => $labels,                // The array of labels for the taxonomy in the WordPress admin.
        'show_ui'           => true,                  // Whether to show the taxonomy in the WordPress admin UI.
        'show_admin_column' => true,                  // Whether to allow automatic creation of taxonomy columns on associated post-types table.
        'query_var'         => true,                  // Allows querying posts by taxonomy in the front end.
        'rewrite'           => array('slug' => 'brand'), // Sets the slug for the taxonomy URLs.
        'show_in_rest'      => true,                  // Makes the taxonomy available to the Block Editor (Gutenberg) through the REST API.
    );
  }
}

// We instantiate the class to make it work.
// $newBrandTaxonomy = new Brand_Taxonomy();

