<?php
// inc/car-listings-handler-sc.php

class CarListingsShortcode {

  /**
   * Constructor: Hooks into WordPress to register the 'car_listings' shortcode.
   */
  public function __construct() {
      add_shortcode('car_listings', [$this, 'render_car_listings_shortcode']);
  }

  /**
   * Renders the 'car_listings' shortcode.
   * This method gets the attributes provided with the shortcode, fetches the car listings based on those attributes, 
   * and then returns the generated HTML for those listings.
   *
   * @param array $atts - The attributes provided with the shortcode.
   * @return string - The generated HTML for the listings.
   */
  public function render_car_listings_shortcode($atts) {
      $attributes = shortcode_atts(
          array(
              'count' => 5,
              'brand' => '',
              'type' => ''
          ),
          $atts
      );

      $listings = $this->fetch_car_listings($attributes);
      return $this->generate_listings_html($listings);
  }
  /**
   * Fetches car listings based on provided criteria.
   * It constructs a WP_Query with appropriate arguments based on provided brand and type,
   * and returns an array of fetched listings.
   *
   * @param array $args - Criteria for fetching the listings (like brand, type).
   * @return array - Array of fetched listings.
   */
    protected function fetch_car_listings($args) {
      $query_args = [
          'post_type' => 'car_listing',  // Assuming this is your custom post type
          'posts_per_page' => $args['count'],
          'meta_query' => [],
          'tax_query' => []
      ];

      // If a specific brand was provided in the shortcode
      if (!empty($args['brand'])) {
          $query_args['tax_query'][] = [
              'taxonomy' => 'brand',  // Assuming this is the taxonomy name for the brands
              'field'    => 'slug',
              'terms'    => $args['brand'],
          ];
      }

      // If a specific type was provided in the shortcode
      if (!empty($args['type'])) {
          $query_args['tax_query'][] = [
              'taxonomy' => 'type',   // Assuming this is the taxonomy name for the types
              'field'    => 'slug',
              'terms'    => $args['type'],
          ];
      }

      // Query the posts
      $query = new WP_Query($query_args);

      $listings = [];
      if ($query->have_posts()) {
          while ($query->have_posts()) {
              $query->the_post();
              $id = get_the_ID();
              $listings[] = (object) [
                  'title' => get_the_title($id),
                  'price' => get_post_meta($id, 'price', true),
                  'image' => get_the_post_thumbnail_url($id)
              ];
          }
          wp_reset_postdata();
      }

      return $listings;
  }

  /**
   * Generates the HTML to display fetched car listings.
   * It constructs a series of div elements for each listing, taking care of using a default image if one isn't set.
   *
   * @param array $listings - Array of fetched car listings.
   * @return string - The generated HTML for displaying the listings.
   */
  protected function generate_listings_html($listings) {
      $html = "<div class='car-listings-container'>";

      $settings = new ListingSettings();
      $default_image_url = $settings->get_default_image_url();

      foreach($listings as $listing) {
          // Replace this with appropriate templating logic based on your listing structure
          $html .= "<div class='car-listing'>";
          $html .= "<img src='" . ($listing->image ?: $default_image_url) . "' />";
          $html .= "<h2>{$listing->title}</h2>";
          $html .= "<p>Price: {$listing->price}</p>";
          $html .= "</div>";
      }

      $html .= "</div>";

      return $html;
  }
}
