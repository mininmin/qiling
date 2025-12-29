<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<?php
// 头部设置
$header_bg = developer_starter_get_option( 'header_bg_color', '' );
$header_text_color = developer_starter_get_option( 'header_text_color', '#333333' );
$transparent_home = developer_starter_get_option( 'header_transparent_home', '' );
$hide_search = developer_starter_get_option( 'hide_search_button', '' );
$hide_phone = developer_starter_get_option( 'hide_phone_header', '' );
$show_search = ! $hide_search;
$show_phone = ! $hide_phone;
$primary_color = developer_starter_get_option( 'primary_color', '#2563eb' );

// 电话按钮颜色设置
$phone_bg_transparent = developer_starter_get_option( 'phone_bg_transparent', '' );
$phone_text_transparent = developer_starter_get_option( 'phone_text_transparent', '#ffffff' );
$phone_bg_normal = developer_starter_get_option( 'phone_bg_normal', '' );
$phone_text_normal = developer_starter_get_option( 'phone_text_normal', '#ffffff' );

// 默认值处理
if ( empty( $phone_bg_transparent ) ) {
    $phone_bg_transparent = 'rgba(255,255,255,0.2)';
}
if ( empty( $phone_bg_normal ) ) {
    $phone_bg_normal = "linear-gradient(135deg, {$primary_color} 0%, #7c3aed 100%)";
}

// 确定头部CSS类
$header_classes = array( 'site-header' );
$is_home = is_front_page();

if ( $is_home && $transparent_home ) {
    $header_classes[] = 'header-transparent';
}

// 头部内联样式
$header_style = '';
if ( $header_bg && ! ( $is_home && $transparent_home ) ) {
    if ( strpos( $header_bg, 'gradient' ) !== false ) {
        $header_style = "background: {$header_bg};";
    } else {
        $header_style = "background-color: {$header_bg};";
    }
}
?>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

    <header id="masthead" class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>" style="<?php echo esc_attr( $header_style ); ?>">
        <div class="header-inner">
            <div class="container header-flex">
                <div class="site-branding">
                    <?php 
                    $site_logo = developer_starter_get_option( 'site_logo', '' );
                    if ( $site_logo ) :
                    ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link">
                            <img src="<?php echo esc_url( $site_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="custom-logo" />
                        </a>
                    <?php elseif ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title-link">
                            <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <nav id="site-navigation" class="primary-navigation">
                    <?php
                    if ( has_nav_menu( 'primary' ) ) {
                        wp_nav_menu( array(
                            'theme_location' => 'primary',
                            'menu_id' => 'primary-menu',
                            'container' => false,
                        ) );
                    }
                    ?>
                </nav>

                <div class="header-actions">
                    <?php if ( $show_search ) : ?>
                        <div class="header-search">
                            <button type="button" class="search-toggle" id="search-toggle" title="<?php esc_attr_e( '搜索', 'developer-starter' ); ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <?php 
                    $phone = developer_starter_get_option( 'company_phone', '' );
                    if ( $phone && $show_phone ) : 
                        // 根据透明模式决定初始样式
                        $initial_bg = ( $is_home && $transparent_home ) ? $phone_bg_transparent : $phone_bg_normal;
                        $initial_text = ( $is_home && $transparent_home ) ? $phone_text_transparent : $phone_text_normal;
                        
                        // 构建背景样式
                        $bg_style = strpos( $initial_bg, 'gradient' ) !== false ? "background: {$initial_bg};" : "background: {$initial_bg};";
                    ?>
                        <div class="header-phone" style="<?php echo esc_attr( $bg_style ); ?> color: <?php echo esc_attr( $initial_text ); ?>;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.362 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                            <span><?php echo esc_html( $phone ); ?></span>
                        </div>
                    <?php endif; ?>

                    <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="<?php esc_attr_e( '菜单', 'developer-starter' ); ?>">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="search-overlay" id="search-overlay">
            <div class="search-overlay-inner">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="search" name="s" placeholder="<?php esc_attr_e( '请输入关键词搜索...', 'developer-starter' ); ?>" value="<?php echo get_search_query(); ?>" />
                    <button type="submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </button>
                </form>
                <button type="button" class="search-close" id="search-close">&times;</button>
            </div>
        </div>
        
        <div class="mobile-menu" id="mobile-menu">
            <div class="container">
                <?php
                if ( has_nav_menu( 'primary' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'menu_id' => 'mobile-nav-menu',
                        'container' => false,
                    ) );
                }
                ?>
            </div>
        </div>
    </header>

    <main id="primary" class="site-main">

<?php
// 添加透明头部的滚动行为JS
if ( $is_home && $transparent_home ) :
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var header = document.getElementById('masthead');
    var headerPhone = header.querySelector('.header-phone');
    var scrolled = false;
    
    // 颜色配置
    var phoneColors = {
        transparent: {
            bg: '<?php echo esc_js( $phone_bg_transparent ); ?>',
            text: '<?php echo esc_js( $phone_text_transparent ); ?>'
        },
        normal: {
            bg: '<?php echo esc_js( $phone_bg_normal ); ?>',
            text: '<?php echo esc_js( $phone_text_normal ); ?>'
        }
    };
    
    function checkScroll() {
        if (window.scrollY > 100) {
            if (!scrolled) {
                header.classList.add('header-scrolled');
                // 切换到常规模式颜色
                if (headerPhone) {
                    headerPhone.style.background = phoneColors.normal.bg;
                    headerPhone.style.color = phoneColors.normal.text;
                }
                scrolled = true;
            }
        } else {
            if (scrolled) {
                header.classList.remove('header-scrolled');
                // 切换到透明模式颜色
                if (headerPhone) {
                    headerPhone.style.background = phoneColors.transparent.bg;
                    headerPhone.style.color = phoneColors.transparent.text;
                }
                scrolled = false;
            }
        }
    }
    
    window.addEventListener('scroll', checkScroll);
    checkScroll();
});
</script>
<?php endif; ?>
