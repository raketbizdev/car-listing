<?php
// inc/type-taxonomy.php

class Type_Taxonomy {

  private $text_domain = 'your-plugin-textdomain';
  
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
          'name'              => _x('Types', 'taxonomy general name', $this->text_domain),
          'singular_name'     => _x('Type', 'taxonomy singular name', $this->text_domain),
          'search_items'      => __('Search Types', $this->text_domain),
          'all_items'         => __('All Types', $this->text_domain),
          'parent_item'       => __('Parent Type', $this->text_domain),
          'parent_item_colon' => __('Parent Type:', $this->text_domain),
          'edit_item'         => __('Edit Type', $this->text_domain),
          'update_item'       => __('Update Type', $this->text_domain),
          'add_new_item'      => __('Add New Type', $this->text_domain),
          'new_item_name'     => __('New Type Name', $this->text_domain),
          'menu_name'         => __('Types', $this->text_domain),
      );
  }

  private function get_args($labels) {
      return array(
          'hierarchical'      => true,
          'labels'            => $labels,
          'show_ui'           => true,
          'show_admin_column' => true,
          'query_var'         => true,
          'rewrite'           => array('slug' => 'type'),
      );
  }
}

// We instantiate the class to make it work.
$newTypeTaxonomy = new Type_Taxonomy();
