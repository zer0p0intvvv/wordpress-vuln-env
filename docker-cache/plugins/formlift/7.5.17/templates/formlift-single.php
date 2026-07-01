<?php
/*
Template Name: Infusionsoft Form
Template Post Type: infusion_form
*/
get_header(); ?>
    <div class="wrap">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                <?php
                /* Start the Loop */
                while ( have_posts() ) : the_post();

                echo "<h1>" . get_the_title() . "</h1>";

                echo do_shortcode( sprintf( '[formlift id="%s"]', get_the_ID() ) );

                endwhile; // End of the loop.
                ?>

            </main><!-- #main -->
        </div><!-- #primary -->
    </div><!-- .wrap -->

<?php get_footer();