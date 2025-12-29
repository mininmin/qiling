<?php
/**
 * The template for displaying archive pages
 *
 * @package Developer_Starter
 */

get_header();
?>

<div class="page-header" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%); padding: 100px 0 60px;">
    <div class="container">
        <h1 class="page-title" style="color: #fff; text-align: center; font-size: 2.5rem; margin: 0;">
            <?php the_archive_title(); ?>
        </h1>
        <?php if ( get_the_archive_description() ) : ?>
            <p style="color: rgba(255,255,255,0.8); text-align: center; margin-top: 15px; max-width: 600px; margin-left: auto; margin-right: auto;">
                <?php echo get_the_archive_description(); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<section class="archive-content section-padding">
    <div class="container">
        <?php if ( have_posts() ) : ?>
            <div class="news-grid grid-cols-3">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article class="news-card" data-aos="fade-up">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="news-thumb">
                                <?php the_post_thumbnail( 'medium_large' ); ?>
                            </a>
                        <?php elseif ( function_exists( 'developer_starter_get_first_image' ) && $first_img = developer_starter_get_first_image( get_the_ID() ) ) : ?>
                            <a href="<?php the_permalink(); ?>" class="news-thumb">
                                <img src="<?php echo esc_url( $first_img ); ?>" alt="<?php the_title_attribute(); ?>" />
                            </a>
                        <?php endif; ?>
                        
                        <div class="news-content">
                            <span class="news-date"><?php echo get_the_date(); ?></span>
                            <h2 class="news-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <p class="news-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 25 ); ?></p>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <nav class="pagination-nav" style="margin-top: 50px; text-align: center;">
                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => '&laquo; 上一页',
                    'next_text' => '下一页 &raquo;',
                ) );
                ?>
            </nav>
            
        <?php else : ?>
            <p class="text-center">暂无内容</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
