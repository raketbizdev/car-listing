<?php
// inc/class-car-listing-rest-controller.php

class Car_Listing_REST_Controller extends WP_REST_Controller {

  public function __construct() {
      $this->namespace = 'car-listing/v1';
      $this->rest_base = 'listings';
  }

  public function register_routes() {
      // GET all listings
      register_rest_route($this->namespace, '/' . $this->rest_base, array(
          'methods'             => WP_REST_Server::READABLE,
          'callback'            => array($this, 'get_items'),
          'permission_callback' => array($this, 'get_items_permissions_check'),
      ));

      // GET a single listing by ID
      register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
          'methods'             => WP_REST_Server::READABLE,
          'callback'            => array($this, 'get_item'),
          'permission_callback' => array($this, 'get_item_permissions_check'),
      ));

      // POST (create) a listing
      register_rest_route($this->namespace, '/' . $this->rest_base, array(
          'methods'             => WP_REST_Server::CREATABLE,
          'callback'            => array($this, 'create_item'),
          'permission_callback' => array($this, 'create_item_permissions_check'),
      ));

      // PUT (update) a listing
      register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
          'methods'             => WP_REST_Server::EDITABLE,
          'callback'            => array($this, 'update_item'),
          'permission_callback' => array($this, 'update_item_permissions_check'),
      ));

      // DELETE a listing
      register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
          'methods'             => WP_REST_Server::DELETABLE,
          'callback'            => array($this, 'delete_item'),
          'permission_callback' => array($this, 'delete_item_permissions_check'),
      ));
  }

  // GET all listings
  public function get_items($request) {
      $args = array(
          'post_type' => 'car_listing',
          'posts_per_page' => -1,
      );
      
      $posts = get_posts($args);

      $data = array();
      foreach ($posts as $post) {
          $response = $this->prepare_item_for_response($post, $request);
          $data[] = $this->prepare_response_for_collection($response);
      }

      return new WP_REST_Response($data, 200);
  }

  // GET a single listing by ID
  public function get_item($request) {
      $id = (int) $request['id'];
      $post = get_post($id);

      if (! $post || 'car_listing' !== $post->post_type) {
          return new WP_Error('no_post', 'Post not found', array('status' => 404));
      }

      $response = $this->prepare_item_for_response($post, $request);
      return $response;
  }

  // POST (create) a listing
  public function create_item($request) {
    // This is just a basic example; validate and sanitize your data thoroughly.
    $post_id = wp_insert_post(array(
        'post_type'    => 'car_listing',
        'post_title'   => sanitize_text_field($request->get_param('title')),
        'post_content' => sanitize_text_field($request->get_param('content')),
        'post_excerpt' => sanitize_text_field($request->get_param('excerpt')),
        'post_status'  => 'publish',
    ));

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    // Set feature image if provided
    if ($request->has_param('feature_image')) {
        $attachment_id = attachment_url_to_postid($request->get_param('feature_image'));
        if ($attachment_id) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    // Update taxonomy data
    if ($request->has_param('taxonomy')) {
        $taxonomies = $request->get_param('taxonomy');

        if (isset($taxonomies['brand'])) {
            wp_set_object_terms($post_id, sanitize_text_field($taxonomies['brand']), 'brand');
        }
        if (isset($taxonomies['type'])) {
            wp_set_object_terms($post_id, sanitize_text_field($taxonomies['type']), 'type');
        }
    }

    // Update meta box data
    $meta_keys = array('color', 'price', 'number_of_doors', 'number_of_seats');
    foreach ($meta_keys as $key) {
        if ($request->has_param($key)) {
            update_post_meta($post_id, $key, sanitize_text_field($request->get_param($key)));
        }
    }

    return new WP_REST_Response(array('id' => $post_id), 201);
  }

  // PUT (update) a listing
  public function update_item($request) {
      $id = (int) $request['id'];

      $updated = wp_update_post(array(
          'ID'           => $id,
          'post_title'   => sanitize_text_field($request->get_param('title')),
          'post_content' => sanitize_text_field($request->get_param('content')),
      ));

      if (is_wp_error($updated)) {
          return $updated;
      }

      return new WP_REST_Response(array('id' => $id), 200);
  }

  // DELETE a listing
  public function delete_item($request) {
      $id = (int) $request['id'];
      $post = get_post($id);

      if (! $post || 'car_listing' !== $post->post_type) {
          return new WP_Error('no_post', 'Post not found', array('status' => 404));
      }

      $deleted = wp_delete_post($id, true);

      if (! $deleted) {
          return new WP_Error('not_deleted', 'Error deleting the post', array('status' => 500));
      }

      return new WP_REST_Response(null, 204); // 204 No Content
  }

  public function get_items_permissions_check($request) {
      return true;  // Everyone can read by default.
  }

  public function get_item_permissions_check($request) {
      return true;  // Everyone can read by default.
  }

  public function create_item_permissions_check($request) {
      // Check if current user can publish posts for example.
      return current_user_can('publish_posts');
  }

  public function update_item_permissions_check($request) {
      // Check if current user can edit posts for example.
      return current_user_can('edit_posts');
  }

  public function delete_item_permissions_check($request) {
      // Check if current user can delete posts for example.
      return current_user_can('delete_posts');
  }

  public function prepare_item_for_response($post, $request) {
      $data = array();
      $data['id'] = (int) $post->ID;
      $data['date'] = $post->post_date;
      $data['date_gmt'] = $post->post_date_gmt;
      $data['title'] = $post->post_title;
      $data['content'] = $post->post_content;
      $data['excerpt'] = $post->post_excerpt;
      $data['slug'] = $post->post_name;
      $data['post_type'] = $post->post_type;

      // Assuming the featured media is an image.
      $thumbnail_id = get_post_thumbnail_id($post->ID);
      $data['featured_media_url'] = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';

      return rest_ensure_response($data);
  }
}

// Don't forget to register the REST routes
function car_listing_register_rest_routes() {
  $controller = new Car_Listing_REST_Controller();
  $controller->register_routes();
}
add_action('rest_api_init', 'car_listing_register_rest_routes');
