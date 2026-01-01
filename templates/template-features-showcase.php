<?php
/**
 * Template Name: 功能清单展示
 * Template Post Type: page
 *
 * 功能清单展示页面模板 - 展示主题的功能特性
 * 使用功能清单列表模块为核心，配合其他模块丰富页面内容
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

// 加载功能清单展示页面专用样式
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'developer-starter-features-showcase',
        DEVELOPER_STARTER_ASSETS . '/css/features-showcase.css',
        array( 'developer-starter-main' ),
        developer_starter_get_assets_version()
    );
}, 20 );

get_header();

// 获取页面模块
$modules = get_post_meta( get_the_ID(), '_developer_starter_modules', true );
$has_modules = ! empty( $modules ) && is_array( $modules );
?>

<div class="page-template template-features-showcase">
    
    <!-- 页面头部 -->
    <div class="features-showcase-hero">
        <div class="hero-bg-effects">
            <div class="hero-gradient"></div>
            <div class="hero-particles">
                <span></span><span></span><span></span>
                <span></span><span></span><span></span>
            </div>
        </div>
        <div class="container">
            <div class="hero-content" data-aos="fade-up">
                <h1 class="hero-title">
                    <?php the_title(); ?>
                </h1>
                <p class="hero-subtitle">
                    探索主题强大的功能特性，为您的网站提供专业的解决方案
                </p>
                <div class="hero-actions">
                    <a href="#features-content" class="btn btn-light btn-lg">
                        <span>查看功能</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M19 12l-7 7-7-7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 模块内容区域 -->
    <div id="features-content" class="page-content">
        <?php if ( $has_modules ) : ?>
            <?php developer_starter_render_page_modules(); ?>
        <?php else : ?>
            <!-- 没有模块时显示提示 -->
            <section class="section-padding" style="min-height: 40vh; display: flex; align-items: center; justify-content: center;">
                <div class="container text-center">
                    <h2>请配置页面模块</h2>
                    <p style="color: #666; margin-top: 20px;">
                        在后台编辑此页面，使用「页面模块配置」添加模块来构建页面内容。
                    </p>
                    <?php if ( current_user_can( 'edit_pages' ) ) : ?>
                        <a href="<?php echo get_edit_post_link(); ?>" class="btn btn-primary" style="margin-top: 20px;">
                            编辑此页面
                        </a>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>

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

<?php
get_footer();
