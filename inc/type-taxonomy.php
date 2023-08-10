<?php
// inc/type-taxonomy.php

class Type_Taxonomy {

  /**
   * Retrieve the text domain used for localization. 
   * This is abstracted to ensure consistent translation references across the plugin.
   *
   * @return string The text domain used for translations.
   */
  protected function get_text_domain() {
    return Car_Listing_Core::get_text_domain();
  }
  
  public function __construct() {
      add_action('init', array($this, 'register_type_taxonomy'));
  }

  public function register_type_taxonomy() {
      $labels = $this->get_labels();
      $args = $this->get_args($labels);
      
      register_taxonomy('type', array('car_listing'), $args);
  }

  private function get_labels() {
      return array(
          'name'              => _x('Types', 'taxonomy general name', $this->get_text_domain()),
          'singular_name'     => _x('Type', 'taxonomy singular name', $this->get_text_domain()),
          'search_items'      => __('Search Types', $this->get_text_domain()),
          'all_items'         => __('All Types', $this->get_text_domain()),
          'parent_item'       => __('Parent Type', $this->get_text_domain()),
          'parent_item_colon' => __('Parent Type:', $this->get_text_domain()),
          'edit_item'         => __('Edit Type', $this->get_text_domain()),
          'update_item'       => __('Update Type', $this->get_text_domain()),
          'add_new_item'      => __('Add New Type', $this->get_text_domain()),
          'new_item_name'     => __('New Type Name', $this->get_text_domain()),
          'menu_name'         => __('Types', $this->get_text_domain()),
      );
  }

  private function get_args($labels) {
      return array(
        'hierarchical'      => true,                  // Determines if taxonomy is hierarchical like categories (true) or flat like tags (false).
        'labels'            => $labels,                // The array of labels for the taxonomy in the WordPress admin.
        'show_ui'           => true,                  // Whether to show the taxonomy in the WordPress admin UI.
        'show_admin_column' => true,                  // Whether to allow automatic creation of taxonomy columns on associated post-types table.
        'query_var'         => true,                  // Allows querying posts by taxonomy in the front end.
        'rewrite'           => array('slug' => 'type'),
        'show_in_rest'      => true,                  // Makes the taxonomy available to the Block Editor (Gutenberg) through the REST API.
      );
  }
}

// We instantiate the class to make it work.
$newTypeTaxonomy = new Type_Taxonomy();
