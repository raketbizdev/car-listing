<?php
// inc/meta-boxes.php

class Meta_Boxes {
  /**
   * Constructor function that hooks into WordPress to register meta boxes and save their data.
   */
  public function __construct() {
    // Hooks into WordPress to add meta boxes for the "car_listing" post type
    add_action('add_meta_boxes', array($this, 'add_car_listing_meta_boxes'));
    // Hooks into WordPress to save the meta box data when the post is saved
    add_action('save_post', array($this, 'save_car_listing_meta_boxes'));
  }
  /**
   * Registers the meta box for displaying car details on the "car_listing" post editor page.
   */
  public function add_car_listing_meta_boxes() {
     // Adds a meta box with the ID 'car_listing_details' to the "car_listing" post type
    add_meta_box('car_listing_details', 'Car Details', array($this, 'display_car_listing_meta_boxes'), 'car_listing', 'normal', 'high');
  }

  /**
   * Displays the fields in the car details meta box.
   *
   * @param WP_Post $post The object for the current post/page.
   */
  public function display_car_listing_meta_boxes($post) {
       // Retrieve current values from the post meta or set default ones
      $color = get_post_meta($post->ID, 'color', true);
      $price = get_post_meta($post->ID, 'price', true);
      $number_of_doors = get_post_meta($post->ID, 'number_of_doors', true);
      $number_of_seats = get_post_meta($post->ID, 'number_of_seats', true);

      // Output a nonce field for security during the save operation
      wp_nonce_field('car_listing_save_meta', 'car_listing_meta_nonce');

      // Display form fields for each of the meta values
      $this->display_meta_box_field('color', 'Color: ', $color);
      $this->display_meta_box_field('price', 'Price: ', $price);
      $this->display_meta_box_field('number_of_doors', 'Number of Doors: ', $number_of_doors, 'number');
      $this->display_meta_box_field('number_of_seats', 'Number of Seats: ', $number_of_seats, 'number');
  }

  /**
   * Utility function to display individual fields within the meta box.
   *
   * @param string $field_id ID of the field.
   * @param string $field_label Display label for the field.
   * @param mixed $field_value Current value of the field.
   * @param string $field_type Type of input field (e.g., 'text', 'number').
   */

  private function display_meta_box_field($field_id, $field_label, $field_value, $field_type = 'text') {
      echo '<div class="inside"><label for="' . $field_id . '">' . $field_label . '</label>';
      echo '<input type="' . $field_type . '" id="' . $field_id . '" name="' . $field_id . '" value="' . esc_attr($field_value) . '" /></div>';
  }

  /**
   * Saves the data from the car details meta box.
   *
   * @param int $post_id The ID of the post being saved.
   */
  public function save_car_listing_meta_boxes($post_id) {
      // Check if nonce is set & verify its validity
      if (!isset($_POST['car_listing_meta_nonce']) || !wp_verify_nonce($_POST['car_listing_meta_nonce'], 'car_listing_save_meta')) {
          return;
      }

        // If the post is being auto-saved, skip saving our meta box data.
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
          return;
      }

      // Check if the current user has permission to edit the post.
      if (!current_user_can('edit_post', $post_id)) {
          return;
      }

      // Sanitize and save each field in the meta box to the post's meta data
      $this->update_meta_field($post_id, 'color', 'sanitize_text_field');
      $this->update_meta_field($post_id, 'price', 'sanitize_text_field');
      $this->update_meta_field($post_id, 'number_of_doors', 'intval');
      $this->update_meta_field($post_id, 'number_of_seats', 'intval');
  }
  /**
   * Utility function to sanitize and update a meta field's value.
   *
   * @param int $post_id The ID of the post being saved.
   * @param string $field_id ID of the field.
   * @param callable $sanitize_callback The sanitization callback function to use.
   */
  private function update_meta_field($post_id, $field_id, $sanitize_callback) {
      update_post_meta($post_id, $field_id, call_user_func($sanitize_callback, $_POST[$field_id]));
  }
}

// We instantiate the class to make it work.
// $newMetaBoxes = new Meta_Boxes();
