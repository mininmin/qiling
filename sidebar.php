<?php
/**
 * 侧边栏模板
 *
 * @package Developer_Starter
 */

// 确定使用哪个侧边栏
$sidebar_id = 'sidebar-main';

if ( is_single() ) {
    $sidebar_id = 'sidebar-post';
} elseif ( is_page() ) {
    $sidebar_id = 'sidebar-page';
} elseif ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() || is_product() ) ) {
    $sidebar_id = 'sidebar-shop';
}

if ( ! is_active_sidebar( $sidebar_id ) ) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar" role="complementary">
    <?php dynamic_sidebar( $sidebar_id ); ?>
</aside>
