<?php
// inc/ListingSettings.php

class ListingSettings {

  /**
   * Retrieve the text domain used for localization. 
   * This is abstracted to ensure consistent translation references across the plugin.
   *
   * @return string The text domain used for translations.
   */
  protected function get_text_domain() {
    return Car_Listing_Core::get_text_domain();
  }


  private $option_name = 'car_listing_options';

  // Constructor: It's called when an object is instantiated from this class.
  public function __construct() {

    // Hooks into WordPress' admin interface actions.
    // Add a new admin menu for the plugin settings.
    add_action('admin_menu', [$this, 'add_admin_menu']);

      // Hook for initializing settings of the plugin
    add_action('admin_init', [$this, 'settings_init']);

    // Enqueue necessary scripts when admin pages load.
    add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

  // Register a new shortcode for front-end display.
    add_shortcode('car_listings', [$this, 'render_car_listings_shortcode']);
  }

  // This function is used to enqueue necessary scripts for media upload functionalities in WordPress' admin panel.
  public function enqueue_admin_scripts() {
      wp_enqueue_media();
  }

  // Create a settings page in WordPress admin panel.
  public function add_admin_menu() {
    global $submenu;

    // Translation-ready strings for the page title and menu title.
    $page_title = __('Listing Settings', $this->get_text_domain());
    $menu_title = __('Settings', $this->get_text_domain());

    // Modify an existing submenu item if it matches a particular name.
    if (isset($submenu['edit.php?post_type=car_listing'])) {
      foreach ($submenu['edit.php?post_type=car_listing'] as $key => $menu_item) {
        if ($menu_item[0] == 'Car Listing') {
          $submenu['edit.php?post_type=car_listing'][$key][2] = 'car_listing_settings';
        }
      }
    }

    // Add a new submenu page under a custom post type 'car_listing'.
    add_submenu_page(
      'edit.php?post_type=car_listing',
      $page_title,
      $menu_title,
      'manage_options',
      'car_listing_settings',
      [$this, 'display_settings_page']
    );
  }


  // Display the actual content of our custom settings page.
  public function display_settings_page() {
    // Check if a particular tab is active. This is useful for tabbed settings interfaces.
      $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'main_settings';
      ?>
      <div class="wrap">
          <h2><?php echo esc_html(__('Car Listing Settings', $this->get_text_domain())); ?></h2>

          <h2 class="nav-tab-wrapper">
              <a href="?post_type=car_listing&page=car_listing_settings&tab=main_settings" class="nav-tab <?php echo $active_tab == 'main_settings' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Main Settings', $this->get_text_domain()); ?></a>
              <a href="?post_type=car_listing&page=car_listing_settings&tab=shortcodes" class="nav-tab <?php echo $active_tab == 'shortcodes' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Shortcodes', $this->get_text_domain()); ?></a>
          </h2>

          <?php if ($active_tab == 'main_settings') : ?>
              <!-- Settings Form -->
              <form action="options.php" method="post">
                  <?php
                  settings_fields($this->option_name);
                  do_settings_sections('car_listing');
                  submit_button();
                  ?>
              </form>
          <?php else : ?>
              <!-- Instructions Section -->
              <div style="background-color: #f1f1f1; padding: 15px; margin-bottom: 20px;">
                  <h3>Car listing Shortcodes</h3>
                  <p><strong>How to use:</strong></p>
                  <ul>
                      <li>
                          <code>[car_listings]</code>: Use this shortcode to display the car listings. By default, it will use the settings you configure here.
                      </li>
                      <li>
                          <code>[car_listings brand="Toyota" type="SUV"]</code>: Use specific attributes to filter the car listings.
                      </li>
                  </ul>
              </div>
          <?php endif; ?>
      </div>
      <?php
  }
 // Register and initialize the custom settings.
  public function settings_init() {
      // Arguments for registering our setting. Helps in validation and sanitization.
      $args = [
          'type' => 'string',
          'sanitize_callback' => [$this, 'validate_options'],
          'default' => NULL,
      ];
       // Register the setting with WordPress.
      register_setting($this->option_name, $this->option_name, $args);

      // Define sections and fields for our settings.
      $this->define_sections();
      $this->define_fields();
  }

  // Define the sections for our settings.
  protected function define_sections() {
      add_settings_section(
          'car_listing_main',
          __('Main Settings', $this->get_text_domain()),
          [$this, 'main_description'],
          'car_listing'
      );
  }

  protected function define_fields() {
    // Define the fields for our settings within the sections.
      add_settings_field(
          'car_listing_display_option',
          __('Display Option', $this->get_text_domain()),
          [$this, 'display_setting'],
          'car_listing',
          'car_listing_main'
      );
      add_settings_field(
          'car_listing_default_image',
          __('Default Listing Image', $this->get_text_domain()),
          [$this, 'default_image_setting'],
          'car_listing',
          'car_listing_main'
      );
  }

   // Display the input control to set the default image for car listings.
  public function default_image_setting() {
    // Fetch stored options and display them as needed (content truncated for brevity).
    $options = get_option($this->option_name);
    $image_url = isset($options['default_image']) ? $options['default_image'] : '';

    echo '<input type="text" id="car-listing-image-url" name="'. $this->option_name .'[default_image]" size="40" value="'. esc_attr($image_url) .'" />';
    echo '<input type="button" id="upload-default-image" class="button" value="'. esc_attr__('Upload Image', $this->get_text_domain()) .'" />';
    echo '<div id="image-preview" style="margin-top:10px;">';

    if ($image_url) {
        echo '<img src="' . esc_url($image_url) . '" style="max-width:150px;max-height:150px;" />';
    }

    echo '</div>';

    // Script for handling the WordPress Media Uploader
    echo '<script>
        jQuery(document).ready(function($) {
            $("#upload-default-image").on("click", function(e) {
                e.preventDefault();

                var image = wp.media({ 
                    title: "Upload Image",
                    multiple: false
                }).open().on("select", function(e){
                    var uploaded_image = image.state().get("selection").first();
                    var image_url = uploaded_image.toJSON().url;
                    $("#car-listing-image-url").val(image_url);

                    // Display the image preview
                    $("#image-preview").html("<img src=\'" + image_url + "\' style=\'max-width:150px;max-height:150px;\' />");
                });
            });
        });
    </script>';
  }

  // Display a description or any additional information for the main settings section.
  public function main_description() {
      echo '<p>' . esc_html(__('Manage settings for Car Listings.', $this->get_text_domain())) . '</p>';
  }
  // Display the input control for the display option.
  public function display_setting() {
     // Fetch stored options and display them as needed (content truncated for brevity).
      $options = get_option($this->option_name);
      $value = isset($options['display']) ? $options['display'] : '';
      echo '<input type="text" name="'. $this->option_name .'[display]" value="'. esc_attr($value) .'" />';
  }

   // Sanitize and validate the user input before it's saved to the database.
  public function validate_options($input) {
     // Ensure safe and valid values are saved (content truncated for brevity).
    $input['display'] = sanitize_text_field($input['display']);
    $input['default_image'] = esc_url_raw($input['default_image']);
    return $input;
  }

  // Retrieve the URL of the default image from the stored options.
  public function get_default_image_url() {
    // Fetch and return the stored image URL.
    $options = get_option($this->option_name);
    return isset($options['default_image']) ? $options['default_image'] : '';  // Returns empty string if not set
  }

  // Similar to the above function but declared as static. Can be called without instantiating the class.
  public static function get_default_image_url_static() {
    // Fetch and return the stored image URL.
    $options = get_option('car_listing_options');
    return isset($options['default_image']) ? $options['default_image'] : '';
  }
}

// Instantiate the class
// $listingSettings = new ListingSettings();