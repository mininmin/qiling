<?php
/**
 * Template Name: Landing Page（落地页）
 * Template Post Type: page
 *
 * 落地页模板 - 用于营销活动、产品推广等专题页面
 * 不显示页头页脚导航，专注于转化
 *
 * @package Developer_Starter
 */

get_header();

// 获取页面模块
$modules = get_post_meta( get_the_ID(), '_developer_starter_modules', true );
$has_modules = ! empty( $modules ) && is_array( $modules );
?>

<div class="page-template template-landing">
    
    <?php if ( $has_modules ) : ?>
        <?php developer_starter_render_page_modules(); ?>
    <?php else : ?>
        
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <?php if ( get_the_content() ) : ?>
                <!-- 如果有页面内容，显示内容 -->
                <section class="landing-hero" style="background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%); padding: 100px 0; text-align: center; color: #fff;">
                    <div class="container">
                        <h1 style="font-size: 3rem; margin-bottom: 20px;"><?php the_title(); ?></h1>
                    </div>
                </section>
                <div class="landing-content section-padding">
                    <div class="container" style="max-width: 900px;">
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- 没有内容时的显示 -->
                <section class="landing-hero" style="background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%); padding: 120px 0; text-align: center; color: #fff;">
                    <div class="container">
                        <h1 style="font-size: 3rem; margin-bottom: 20px;"><?php the_title(); ?></h1>
                        <?php if ( current_user_can( 'edit_pages' ) ) : ?>
                            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 600px; margin: 0 auto 40px;">
                                通过模块构建器为此页面添加内容模块，创建专业的落地页
                            </p>
                            <a href="<?php echo admin_url( 'post.php?post=' . get_the_ID() . '&action=edit' ); ?>" class="btn btn-light btn-lg">
                                编辑页面模块
                            </a>
                        <?php else : ?>
                            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
                                页面内容正在建设中，敬请期待...
                            </p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endwhile; endif; ?>
        
        <!-- 默认CTA（仅在有编辑权限时显示提示） -->
        <?php 
        $phone = developer_starter_get_option( 'company_phone', '' );
        if ( $phone ) : 
        ?>
        <section style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 80px 0; text-align: center; color: #fff;">
            <div class="container">
                <h2 style="font-size: 2rem; margin-bottom: 15px;">准备开始？</h2>
                <p style="opacity: 0.8; margin-bottom: 30px;">立即联系我们，获取专业咨询</p>
                <div style="font-size: 1.5rem; font-weight: 600; margin-bottom: 20px;">
                    📞 <?php echo esc_html( $phone ); ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
    <?php endif; ?>
    
</div>

<?php get_footer(); ?>
