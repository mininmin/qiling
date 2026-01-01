<?php
/**
 * 默认页面模板（带侧边栏）
 *
 * @package Developer_Starter
 */

get_header();

// 检查侧边栏是否激活
$has_sidebar = is_active_sidebar( 'sidebar-page' );
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
            // 模块页面始终全宽
            developer_starter_render_page_modules();
        else :
            // 普通页面内容 - 根据侧边栏状态调整布局
        ?>
            <div class="page-layout <?php echo $has_sidebar ? 'has-sidebar' : 'no-sidebar'; ?>">
                <div class="page-main-content">
                    <div class="entry-content">
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
                </div>
                
                <?php if ( $has_sidebar ) : ?>
                    <div class="page-sidebar">
                        <?php get_sidebar(); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</article>

<?php get_footer(); ?>
