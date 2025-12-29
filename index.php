<?php
/**
 * The main template file
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

get_header();
?>

<div class="content-area">
    <div class="container">
        <div class="content-wrapper <?php echo is_active_sidebar( 'sidebar-main' ) ? 'has-sidebar' : ''; ?>">
            <div class="main-content">
                <?php if ( have_posts() ) : ?>
                    
                    <?php if ( is_home() && ! is_front_page() ) : ?>
                        <header class="page-header">
                            <h1 class="page-title"><?php single_post_title(); ?></h1>
                        </header>
                    <?php endif; ?>

                    <div class="posts-grid">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <?php get_template_part( 'template-parts/content/content', get_post_type() ); ?>
                        <?php endwhile; ?>
                    </div>

                    <?php the_posts_pagination( array(
                        'mid_size'  => 2,
                        'prev_text' => __( '← 上一页', 'developer-starter' ),
                        'next_text' => __( '下一页 →', 'developer-starter' ),
                    ) ); ?>

                <?php else : ?>
                    <?php get_template_part( 'template-parts/content/content', 'none' ); ?>
                <?php endif; ?>
            </div>

            <?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
                <aside id="secondary" class="widget-area">
                    <?php dynamic_sidebar( 'sidebar-main' ); ?>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
get_footer();
