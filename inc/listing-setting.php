<?php
// inc/ListingSettings.php

class ListingSettings {

    private $option_name = 'car_listing_options';
    private $text_domain = 'your-plugin-textdomain';

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_shortcode('car_listings', [$this, 'render_car_listings_shortcode']);
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_media();  // Enqueue WordPress media scripts
    }

    public function add_admin_menu() {
        global $submenu;

        $page_title = __('Listing Settings', $this->text_domain);
        $menu_title = __('Settings', $this->text_domain);

        // Modify submenu if necessary.
        if (isset($submenu['edit.php?post_type=car_listing'])) {
            foreach ($submenu['edit.php?post_type=car_listing'] as $key => $menu_item) {
                if ($menu_item[0] == 'Car Listing') {
                    $submenu['edit.php?post_type=car_listing'][$key][2] = 'car_listing_settings';
                }
            }
        }

        // Add settings submenu.
        add_submenu_page(
            'edit.php?post_type=car_listing',
            $page_title,
            $menu_title,
            'manage_options',
            'car_listing_settings',
            [$this, 'display_settings_page']
        );
    }

    public function display_settings_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'main_settings';
        ?>
        <div class="wrap">
            <h2><?php echo esc_html(__('Car Listing Settings', $this->text_domain)); ?></h2>

            <h2 class="nav-tab-wrapper">
                <a href="?post_type=car_listing&page=car_listing_settings&tab=main_settings" class="nav-tab <?php echo $active_tab == 'main_settings' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Main Settings', $this->text_domain); ?></a>
                <a href="?post_type=car_listing&page=car_listing_settings&tab=shortcodes" class="nav-tab <?php echo $active_tab == 'shortcodes' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Shortcodes', $this->text_domain); ?></a>
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

    public function settings_init() {
        $args = [
            'type' => 'string',
            'sanitize_callback' => [$this, 'validate_options'],
            'default' => NULL,
        ];
        register_setting($this->option_name, $this->option_name, $args);

        $this->define_sections();
        $this->define_fields();
    }

    protected function define_sections() {
        add_settings_section(
            'car_listing_main',
            __('Main Settings', $this->text_domain),
            [$this, 'main_description'],
            'car_listing'
        );
    }

    protected function define_fields() {
        add_settings_field(
            'car_listing_display_option',
            __('Display Option', $this->text_domain),
            [$this, 'display_setting'],
            'car_listing',
            'car_listing_main'
        );
        add_settings_field(
            'car_listing_default_image',
            __('Default Listing Image', $this->text_domain),
            [$this, 'default_image_setting'],
            'car_listing',
            'car_listing_main'
        );
    }

    public function default_image_setting() {
      $options = get_option($this->option_name);
      $image_url = isset($options['default_image']) ? $options['default_image'] : '';
  
      echo '<input type="text" id="car-listing-image-url" name="'. $this->option_name .'[default_image]" size="40" value="'. esc_attr($image_url) .'" />';
      echo '<input type="button" id="upload-default-image" class="button" value="'. esc_attr__('Upload Image', $this->text_domain) .'" />';
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

    public function main_description() {
        echo '<p>' . esc_html(__('Manage settings for Car Listings.', $this->text_domain)) . '</p>';
    }

    public function display_setting() {
        $options = get_option($this->option_name);
        $value = isset($options['display']) ? $options['display'] : '';
        echo '<input type="text" name="'. $this->option_name .'[display]" value="'. esc_attr($value) .'" />';
    }

    public function validate_options($input) {
      $input['display'] = sanitize_text_field($input['display']);
      $input['default_image'] = esc_url_raw($input['default_image']);
      return $input;
    }

    public function get_default_image_url() {
      $options = get_option($this->option_name);
      return isset($options['default_image']) ? $options['default_image'] : '';  // Returns empty string if not set
    }
  
    public static function get_default_image_url_static() {
      $options = get_option('car_listing_options');
      return isset($options['default_image']) ? $options['default_image'] : '';
    }
}

// Instantiate the class
// $listingSettings = new ListingSettings();