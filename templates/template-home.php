<?php
/**
 * Template Name: 模块化首页
 *
 * 使用页面模块系统构建的首页模板
 *
 * @package Developer_Starter
 */

get_header();
?>

<?php
// 渲染页面配置的所有模块
$modules = get_post_meta( get_the_ID(), '_developer_starter_modules', true );

if ( ! empty( $modules ) && is_array( $modules ) ) :
    // 调用模块管理器渲染
    developer_starter_render_page_modules();
else :
    // 没有配置模块时显示提示
?>
<section class="section-padding" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
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

<?php
// 如果有页面正文内容，也显示出来
while ( have_posts() ) : the_post();
    if ( get_the_content() ) :
?>
<section class="page-content-section section-padding">
    <div class="container">
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    </div>
</section>
<?php 
    endif;
endwhile;
?>

<?php get_footer(); ?>
