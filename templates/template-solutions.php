<?php
/**
 * Template Name: 解决方案
 * Template Post Type: page
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

get_header();
?>

<div class="page-template template-solutions">
    <!-- Page Header -->
    <div class="page-hero page-hero-sm">
        <div class="container">
            <h1 class="page-hero-title" data-aos="fade-up"><?php the_title(); ?></h1>
            <?php developer_starter_breadcrumb(); ?>
        </div>
    </div>

    <div class="page-content">
        <?php developer_starter_render_page_modules(); ?>

        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <?php if ( get_the_content() ) : ?>
                <section class="content-section section-padding">
                    <div class="container">
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        <?php endwhile; endif; ?>
    </div>
</div>

<?php
get_footer();
