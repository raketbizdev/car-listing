<?php
// inc/meta-boxes.php

class Meta_Boxes {

  public function __construct() {
      add_action('add_meta_boxes', array($this, 'add_car_listing_meta_boxes'));
      add_action('save_post', array($this, 'save_car_listing_meta_boxes'));
  }

  public function add_car_listing_meta_boxes() {
      add_meta_box('car_listing_details', 'Car Details', array($this, 'display_car_listing_meta_boxes'), 'car_listing', 'normal', 'high');
  }

  public function display_car_listing_meta_boxes($post) {
      // Retrieve current values or set default ones
      $color = get_post_meta($post->ID, 'color', true);
      $price = get_post_meta($post->ID, 'price', true);
      $number_of_doors = get_post_meta($post->ID, 'number_of_doors', true);
      $number_of_seats = get_post_meta($post->ID, 'number_of_seats', true);

      // Output nonce for security
      wp_nonce_field('car_listing_save_meta', 'car_listing_meta_nonce');

      // Display form fields
      $this->display_meta_box_field('color', 'Color: ', $color);
      $this->display_meta_box_field('price', 'Price: ', $price);
      $this->display_meta_box_field('number_of_doors', 'Number of Doors: ', $number_of_doors, 'number');
      $this->display_meta_box_field('number_of_seats', 'Number of Seats: ', $number_of_seats, 'number');
  }

  private function display_meta_box_field($field_id, $field_label, $field_value, $field_type = 'text') {
      echo '<div class="inside"><label for="' . $field_id . '">' . $field_label . '</label>';
      echo '<input type="' . $field_type . '" id="' . $field_id . '" name="' . $field_id . '" value="' . esc_attr($field_value) . '" /></div>';
  }

  public function save_car_listing_meta_boxes($post_id) {
      // Check if nonce is set & verify it
      if (!isset($_POST['car_listing_meta_nonce']) || !wp_verify_nonce($_POST['car_listing_meta_nonce'], 'car_listing_save_meta')) {
          return;
      }

      // If this is an autosave, our form has not been submitted, so we don't want to do anything.
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
          return;
      }

      // Check the user's permissions.
      if (!current_user_can('edit_post', $post_id)) {
          return;
      }

      // Sanitize and update each meta field
      $this->update_meta_field($post_id, 'color', 'sanitize_text_field');
      $this->update_meta_field($post_id, 'price', 'sanitize_text_field');
      $this->update_meta_field($post_id, 'number_of_doors', 'intval');
      $this->update_meta_field($post_id, 'number_of_seats', 'intval');
  }

  private function update_meta_field($post_id, $field_id, $sanitize_callback) {
      update_post_meta($post_id, $field_id, call_user_func($sanitize_callback, $_POST[$field_id]));
  }
}

// We instantiate the class to make it work.
// $newMetaBoxes = new Meta_Boxes();
