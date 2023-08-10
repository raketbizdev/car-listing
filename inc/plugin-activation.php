<?php
// inc/plugin-activation.php

// Ensure to include required files
// include_once('type-taxonomy.php');  // Assuming it's in the same directory as this file

class Car_Listing_Activation {

  public function __construct() {
      register_activation_hook(__FILE__, array($this, 'activate'));
      register_deactivation_hook(__FILE__, array($this, 'deactivate'));
  }

  public function activate() {
      $this->register_post_type_and_taxonomies();
      flush_rewrite_rules();
  }

  private function register_post_type_and_taxonomies() {
      // Registering the custom post type and taxonomies

      $postType = new CarListingPostType();
      $postType->register();

      $brandTaxonomy = new Brand_Taxonomy();
      $brandTaxonomy->register();

      $typeTaxonomy = new Type_Taxonomy();
      $typeTaxonomy->register();
  }

  public function deactivate() {
      flush_rewrite_rules();
  }
}

// We instantiate the class to make it work.
$newActivation = new Car_Listing_Activation();
