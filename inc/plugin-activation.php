<?php
// inc/plugin-activation.php

/**
 * Car_Listing_Activation Class.
 * 
 * This class is responsible for handling the activation and deactivation hooks
 * of the 'Car Listing' plugin. It registers the required post types and taxonomies
 * on activation and cleans up on deactivation.
 */
class Car_Listing_Activation {

  /**
   * Constructor method.
   * 
   * Initializes and sets up activation and deactivation hooks for the plugin.
   */
  public function __construct() {
      // Set the activate() method as the callback for when the plugin is activated.
      register_activation_hook(__FILE__, array($this, 'activate'));
      
      // Set the deactivate() method as the callback for when the plugin is deactivated.
      register_deactivation_hook(__FILE__, array($this, 'deactivate'));

      // Hook to disable Gutenberg editor for the 'car_listing' post type.
      add_filter('use_block_editor_for_post_type', array($this, 'disable_gutenberg_for_custom_post_type'), 10, 2);
  }

  /**
   * Method to be executed when the plugin is activated.
   * 
   * Registers the custom post type and taxonomies, then flushes the rewrite rules
   * to ensure that the new post type and taxonomy URLs are available.
   */
  public function activate() {
      $this->register_post_type_and_taxonomies();
      // Flush the rewrite rules to make sure permalinks work for our new post type and taxonomies.
      flush_rewrite_rules();
  }

  /**
   * Register the custom post type and taxonomies.
   * 
   * Creates and initializes the custom post type and taxonomies required by the plugin.
   */
  private function register_post_type_and_taxonomies() {
      // Registering the custom post type
      $postType = new CarListingPostType();
      $postType->register();

      // Registering the brand taxonomy
      $brandTaxonomy = new Brand_Taxonomy();
      $brandTaxonomy->register();

      // Registering the type taxonomy
      $typeTaxonomy = new Type_Taxonomy();
      $typeTaxonomy->register();
  }

  /**
   * Disable Gutenberg (Block) editor for 'car_listing' post type.
   * 
   * @param bool $can_edit Whether the post type can be edited or not.
   * @param string $post_type The post type being checked.
   * @return bool Whether Gutenberg editor should be used.
   */
  public function disable_gutenberg_for_custom_post_type($can_edit, $post_type) {
      if ($post_type === 'car_listing') {
          return false;
      }
      return $can_edit;
  }

  /**
   * Method to be executed when the plugin is deactivated.
   * 
   * Cleans up the plugin data, flushes rewrite rules to ensure the URLs remain consistent.
   */
  public function deactivate() {
      // Flush the rewrite rules to clean up permalinks.
      flush_rewrite_rules();
  }
}

// Instantiate the class to ensure the activation and deactivation hooks are set.
$newActivation = new Car_Listing_Activation();
