<?php
/**
 * The template for displaying single car listings
 *
 * @package Car_Listing
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

    <?php
    while ( have_posts() ) : the_post();

        // Display post title, content, etc.
        

        // Display the featured image if available, otherwise use the default from ListingSettings
        if (has_post_thumbnail()) {
            the_post_thumbnail('full'); // Display the full size, you can change to other sizes like 'medium' or 'thumbnail'
        } else {
            $defaultImageUrl = ListingSettings::get_default_image_url_static();
            if (!empty($defaultImageUrl)) {
                echo '<img src="' . esc_url($defaultImageUrl) . '" alt="Default Car Listing Image">';
            }
        }

        the_title('<h1>', '</h1>');
        the_content();

        // Fetch the meta values
        $color = get_post_meta(get_the_ID(), 'color', true);
        $price = get_post_meta(get_the_ID(), 'price', true);
        $number_of_doors = get_post_meta(get_the_ID(), 'number_of_doors', true);
        $number_of_seats = get_post_meta(get_the_ID(), 'number_of_seats', true);

        // Display the meta values
        echo '<div class="car-listing-meta">';
        echo '<p><strong>Color:</strong> ' . esc_html($color) . '</p>';
        echo '<p><strong>Price:</strong> ' . esc_html($price) . '</p>';
        echo '<p><strong>Number of Doors:</strong> ' . intval($number_of_doors) . '</p>';
        echo '<p><strong>Number of Seats:</strong> ' . intval($number_of_seats) . '</p>';
        echo '</div>';

    endwhile; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
