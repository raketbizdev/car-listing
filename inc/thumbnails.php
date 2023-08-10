<?php
// inc/Thumbnails.php

/**
 * Thumbnails Class
 * 
 * This class is responsible for handling thumbnail display in the WordPress admin area 
 * for the custom post type 'car_listing'. It adds a new column in the posts list for thumbnails, 
 * populates this column with images, and provides a fallback to a default image 
 * set via the ListingSettings.
 * 
 * The main connection with another file (ListingSettings.php) is the shared option name 
 * "car_listing_options", from which this class fetches the default image URL.
 */

class Thumbnails {
    
    // The name of the option where settings like the default image URL are stored.
    // This is the same option name used in the ListingSettings.php for saving settings.
    private $option_name = 'car_listing_options';

    /**
     * Thumbnails constructor.
     * Sets up the necessary WordPress actions and filters to manipulate the admin list view.
     */
    public function __construct() {
        // Add a new column for thumbnails in the 'car_listing' posts list.
        add_filter('manage_car_listing_posts_columns', [$this, 'add_thumbnail_column']);
        
        // Populate the thumbnail column with either the post's thumbnail or the default image.
        add_action('manage_car_listing_posts_custom_column', [$this, 'populate_thumbnail_column'], 10, 2);
        
        // Add custom styles for the thumbnails column.
        add_action('admin_head', [$this, 'thumbnail_admin_styles']);
    }

    /**
     * Adds the thumbnail column to the 'car_listing' post type in the admin list view.
     *
     * @param array $columns The existing columns.
     * @return array The modified columns.
     */
    public function add_thumbnail_column($columns) {
        // Insert a new column named 'thumbnail' after the first column, and return the modified columns.
        return array_slice($columns, 0, 1, true) + ['thumbnail' => __('Thumbs', Car_Listing_Core::get_text_domain())] + array_slice($columns, 1, NULL, true);
    }

    /**
     * Populates the thumbnail column with the appropriate image.
     *
     * @param string $column The current column's ID.
     * @param int $post_id The current post's ID.
     */
    public function populate_thumbnail_column($column, $post_id) {
        if ($column === 'thumbnail') {
            // Fetch the thumbnail of the current post.
            $thumbnail_id = get_post_thumbnail_id($post_id);
            $image = wp_get_attachment_image_src($thumbnail_id, 'thumbnail'); 

            if ($image) {
                // Display the post's thumbnail if available.
                echo '<img src="' . esc_url($image[0]) . '" width="50" height="50" alt="' . sprintf(__('Thumbnail for post %s', Car_Listing_Core::get_text_domain()), esc_attr($post_id)) . '">';
            } else {
                // If no thumbnail is set for the post, fetch the default image from the options.
                $options = get_option($this->option_name);
                $default_image = isset($options['default_image']) ? $options['default_image'] : '';

                if ($default_image) {
                    // Display the default image if set.
                    echo '<img src="' . esc_url($default_image) . '" width="50" height="50" alt="' . sprintf(__('Default thumbnail for post %s', Car_Listing_Core::get_text_domain()), esc_attr($post_id)) . '">';
                } else {
                    // Display a placeholder text if no image is available.
                    echo __('No Thumbnail', Car_Listing_Core::get_text_domain());
                }
            }
        }
    }

    /**
     * Outputs custom styles for the thumbnails column in the 'car_listing' post type list view.
     */
    public function thumbnail_admin_styles() {
        // Custom styles to make the thumbnail column look consistent.
        echo '<style type="text/css">
            .manage-column.column-thumbnail {
                width: 60px;
                text-align: center;
            }
        </style>';
    }
}