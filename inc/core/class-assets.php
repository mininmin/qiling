<?php
/**
 * Assets Class - 无外部依赖版本
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

namespace Developer_Starter\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Assets {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
    }

    public function enqueue_styles() {
        // 主样式 (不使用Google Fonts，使用系统字体)
        wp_enqueue_style( 'developer-starter-main', DEVELOPER_STARTER_ASSETS . '/css/main.css', 
            array(), DEVELOPER_STARTER_VERSION );

        // Swiper CSS (支持自定义CDN)
        $swiper_css = developer_starter_get_option( 'swiper_css_url', '' );
        if ( empty( $swiper_css ) ) {
            $swiper_css = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css';
        }
        wp_enqueue_style( 'swiper', $swiper_css, array(), '11.0.5' );

        // 动态 CSS
        wp_add_inline_style( 'developer-starter-main', $this->get_dynamic_css() );

        // 自定义 CSS
        $custom_css = developer_starter_get_option( 'custom_css', '' );
        if ( ! empty( $custom_css ) ) {
            wp_add_inline_style( 'developer-starter-main', $custom_css );
        }
        
        // Iconfont CSS（如果设置了）
        $iconfont_css = developer_starter_get_option( 'iconfont_css_url', '' );
        if ( ! empty( $iconfont_css ) ) {
            wp_enqueue_style( 'iconfont', $iconfont_css, array(), DEVELOPER_STARTER_VERSION );
        }
    }

    public function enqueue_scripts() {
        // Swiper JS (支持自定义CDN)
        $swiper_js = developer_starter_get_option( 'swiper_js_url', '' );
        if ( empty( $swiper_js ) ) {
            $swiper_js = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js';
        }
        wp_enqueue_script( 'swiper', $swiper_js, array(), '11.0.5', true );

        // 主脚本
        wp_enqueue_script( 'developer-starter-main', DEVELOPER_STARTER_ASSETS . '/js/main.js', 
            array( 'swiper' ), DEVELOPER_STARTER_VERSION, true );

        wp_localize_script( 'developer-starter-main', 'developerStarterData', array(
            'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'developer_starter_nonce' ),
            'homeUrl'  => home_url(),
            'themeUrl' => DEVELOPER_STARTER_URI,
        ) );

        if ( is_singular() && comments_open() ) {
            wp_enqueue_script( 'comment-reply' );
        }
    }

    public function admin_assets( $hook ) {
        if ( strpos( $hook, 'developer-starter' ) !== false || $hook === 'post.php' || $hook === 'post-new.php' ) {
            wp_enqueue_media();
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_style( 'developer-starter-admin', DEVELOPER_STARTER_ASSETS . '/css/admin.css', 
                array(), DEVELOPER_STARTER_VERSION );
        }
    }

    private function get_dynamic_css() {
        $primary = developer_starter_get_option( 'primary_color', '#2563eb' );
        $primary_dark = $this->darken_color( $primary, 15 );
        $primary_light = $this->lighten_color( $primary, 10 );
        
        // 菜单hover样式
        $nav_hover_bg = developer_starter_get_option( 'nav_hover_bg', '' );
        $nav_hover_text = developer_starter_get_option( 'nav_hover_text', '#ffffff' );

        $css = ":root{
            --color-primary:{$primary};
            --color-primary-dark:{$primary_dark};
            --color-primary-light:{$primary_light};
        }";
        
        // 如果设置了菜单hover背景色
        if ( ! empty( $nav_hover_bg ) ) {
            $css .= "
            .primary-navigation>ul>li>a:hover,
            .primary-navigation li.current-menu-item>a,
            .primary-navigation li.current_page_item>a {
                background: {$nav_hover_bg};
                color: {$nav_hover_text};
            }";
        }
        
        return $css;
    }

    private function darken_color( $hex, $percent ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) !== 6 ) {
            return '#1d4ed8';
        }
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        
        $r = (int) max( 0, $r - ( $r * $percent / 100 ) );
        $g = (int) max( 0, $g - ( $g * $percent / 100 ) );
        $b = (int) max( 0, $b - ( $b * $percent / 100 ) );
        
        return sprintf( '#%02x%02x%02x', $r, $g, $b );
    }

    private function lighten_color( $hex, $percent ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) !== 6 ) {
            return '#3b82f6';
        }
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        
        $r = (int) min( 255, $r + ( ( 255 - $r ) * $percent / 100 ) );
        $g = (int) min( 255, $g + ( ( 255 - $g ) * $percent / 100 ) );
        $b = (int) min( 255, $b + ( ( 255 - $b ) * $percent / 100 ) );
        
        return sprintf( '#%02x%02x%02x', $r, $g, $b );
    }
}
