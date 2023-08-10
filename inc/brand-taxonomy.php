<?php
// inc/brand-taxonomy.php

class Brand_Taxonomy {

  private $text_domain = 'your-plugin-textdomain';
  
  public function __construct() {
      add_action('init', array($this, 'register_brand_taxonomy'));
  }

  public function register_brand_taxonomy() {
      $labels = $this->get_labels();
      $args = $this->get_args($labels);
      
      register_taxonomy('brand', array('car_listing'), $args);
  }

  private function get_labels() {
      return array(
          'name'              => _x('Brands', 'taxonomy general name', $this->text_domain),
          'singular_name'     => _x('Brand', 'taxonomy singular name', $this->text_domain),
          'search_items'      => __('Search Brands', $this->text_domain),
          'all_items'         => __('All Brands', $this->text_domain),
          'parent_item'       => __('Parent Brand', $this->text_domain),
          'parent_item_colon' => __('Parent Brand:', $this->text_domain),
          'edit_item'         => __('Edit Brand', $this->text_domain),
          'update_item'       => __('Update Brand', $this->text_domain),
          'add_new_item'      => __('Add New Brand', $this->text_domain),
          'new_item_name'     => __('New Brand Name', $this->text_domain),
          'menu_name'         => __('Brands', $this->text_domain),
      );
  }

  private function get_args($labels) {
      return array(
          'hierarchical'      => true,
          'labels'            => $labels,
          'show_ui'           => true,
          'show_admin_column' => true,
          'query_var'         => true,
          'rewrite'           => array('slug' => 'brand'),
      );
  }
}

// We instantiate the class to make it work.
// $newBrandTaxonomy = new Brand_Taxonomy();

