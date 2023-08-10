<?php
/**
 * The template for displaying the car listing archive
 *
 * @package Car_Listing
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <header class="page-header">
            <h1 class="page-title">Car Listings</h1>
        </header><!-- .page-header -->

        <?php 
        if ( have_posts() ) : 
            while ( have_posts() ) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php 
                if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail(); ?>
                        </a>
                    </div><!-- .post-thumbnail -->
                <?php else: 
                    $defaultImageURL = ListingSettings::get_default_image_url_static();
                    if ($defaultImageURL) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo esc_url($defaultImageURL); ?>" alt="Default Image">
                            </a>
                        </div><!-- .post-thumbnail -->
                    <?php endif; 
                endif;
            ?>

                <header class="entry-header">
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                </header><!-- .entry-header -->

                <div class="entry-summary">
                    <?php the_excerpt(); ?>
                </div><!-- .entry-summary -->

                <footer class="entry-footer">
                    <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                </footer><!-- .entry-footer -->
            </article><!-- #post-## -->

            <?php endwhile;

            // Pagination can be added here if needed

        else : 
            echo '<p>No car listings found.</p>';
        endif; 
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
