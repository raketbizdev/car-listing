<?php
// inc/widget-listing.php

/**
 * Car Listing Widget Class
 * 
 * This class is responsible for providing a widget on the frontend to display 
 * car listings. The widget settings allow specifying the number of listings to show.
 * Each listing will display the title which is clickable, leading to the full listing.
 */

// inc/widget-listing.php

class Car_Listing_Widget extends WP_Widget {

  protected function get_text_domain() {
      return Car_Listing_Core::get_text_domain();
  }

  public function __construct() {
      parent::__construct(
          'car_listing_widget',
          __('Car Listing Widget', $this->get_text_domain()),
          ['description' => __('A widget to display car listings', $this->get_text_domain())]
      );
  }

  public function widget($args, $instance) {
      echo $args['before_widget'];
      echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
      $this->display_listings($instance);
      echo $args['after_widget'];
  }

  public function form($instance) {
      $title = $this->get_instance_value($instance, 'title', __('New Car Listing', $this->get_text_domain()));
      $num_listings = $this->get_instance_value($instance, 'num_listings', 5);

      $this->render_form_field('title', 'text', $title, __('Title:', $this->get_text_domain()));
      $this->render_form_field('num_listings', 'number', $num_listings, __('Number of Listings to Show:', $this->get_text_domain()), ['min' => 1]);
  }

  public function update($new_instance, $old_instance) {
      return [
          'title' => $this->sanitize_instance_value($new_instance, 'title'),
          'num_listings' => intval($this->get_instance_value($new_instance, 'num_listings', 5))
      ];
  }
  protected function get_default_image() {
    $listingSettings = new ListingSettings();
    return $listingSettings->get_default_image_url();
  }
  protected function get_instance_value($instance, $key, $default = '') {
      return isset($instance[$key]) ? $instance[$key] : $default;
  }

  protected function sanitize_instance_value($instance, $key, $default = '') {
      return isset($instance[$key]) ? sanitize_text_field($instance[$key]) : $default;
  }

  protected function render_form_field($field_name, $type, $value, $label, $attributes = []) {
      $attributes_str = '';
      foreach ($attributes as $attr_key => $attr_value) {
          $attributes_str .= sprintf('%s="%s" ', $attr_key, esc_attr($attr_value));
      }

      echo '<p>';
      echo '<label for="' . esc_attr($this->get_field_id($field_name)) . '">' . $label . '</label>';
      echo '<input class="widefat" id="' . esc_attr($this->get_field_id($field_name)) . '" name="' . esc_attr($this->get_field_name($field_name)) . '" type="' . $type . '" value="' . esc_attr($value) . '" ' . $attributes_str . '>';
      echo '</p>';
  }


  protected function display_listings($instance) {
      $num_listings = isset($instance['num_listings']) ? $instance['num_listings'] : 5;

      $query_args = [
          'post_type' => 'car_listing',
          'posts_per_page' => $num_listings,
          'post_status' => 'publish',
      ];

      $car_listings = new WP_Query($query_args);

      if ($car_listings->have_posts()) {
          echo '<ul class="car-listings-widget">';
          while ($car_listings->have_posts()) {
              $car_listings->the_post();

              if (has_post_thumbnail()) {
                  $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
              } else {
                  $thumbnail_url = $this->get_default_image();
              }

              echo '<li>';
              echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr(get_the_title()) . '" class="car-listing-thumb"> ';
              echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
              echo '</li>';
          }
          echo '</ul>';
          wp_reset_postdata();
      } else {
          echo __('No car listings found.', $this->get_text_domain());
      }
  }

}

function register_car_listing_widget() {
  register_widget('Car_Listing_Widget');
}
add_action('widgets_init', 'register_car_listing_widget');

