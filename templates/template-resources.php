<?php
/**
 * Template Name: 资源下载
 * Template Post Type: page
 *
 * 资源下载页面模板 - 支持内置模块布局
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

// 加载资源下载页面专用样式
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'developer-starter-resources',
        DEVELOPER_STARTER_ASSETS . '/css/resources.css',
        array( 'developer-starter-main' ),
        developer_starter_get_assets_version()
    );
}, 20 );

get_header();
?>

<div class="page-template template-resources">
    <!-- Page Header -->
    <div class="page-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 50%, #7c3aed 100%); padding: 100px 0 60px;">
        <div class="container">
            <h1 class="page-title" style="color: #fff; text-align: center; font-size: 2.5rem; margin: 0;" data-aos="fade-up">
                <?php the_title(); ?>
            </h1>
            <p style="text-align: center; color: rgba(255,255,255,0.8); margin-top: 15px; font-size: 1.1rem;" data-aos="fade-up" data-aos-delay="100">
                下载我们的APP、软件工具和企业资料
            </p>
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
