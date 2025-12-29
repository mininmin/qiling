<?php
/**
 * 通用页面模板
 *
 * @package Developer_Starter
 */

get_header();
?>

<div class="page-header" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%); padding: 100px 0 60px;">
    <div class="container">
        <h1 class="page-title" style="color: #fff; text-align: center; font-size: 2.5rem; margin: 0;">
            <?php the_title(); ?>
        </h1>
    </div>
</div>

<article class="page-content section-padding">
    <div class="container">
        <?php
        // 检查是否有模块配置
        $modules = get_post_meta( get_the_ID(), '_developer_starter_modules', true );
        
        if ( ! empty( $modules ) && is_array( $modules ) ) :
            // 渲染模块
            developer_starter_render_page_modules();
        else :
            // 显示普通页面内容
        ?>
            <div class="entry-content" style="max-width: 900px; margin: 0 auto;">
                <?php
                while ( have_posts() ) :
                    the_post();
                    the_content();
                    
                    wp_link_pages( array(
                        'before' => '<div class="page-links">',
                        'after'  => '</div>',
                    ) );
                endwhile;
                ?>
            </div>
        <?php endif; ?>
    </div>
</article>

<?php get_footer(); ?>
