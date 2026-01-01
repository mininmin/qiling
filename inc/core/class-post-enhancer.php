<?php
/**
 * Post Enhancer - 文章增强器
 * 
 * 处理代码高亮、TOC生成、浏览量统计、阅读时长等功能
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

namespace Developer_Starter\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Post_Enhancer {

    /**
     * 单例实例
     */
    private static $instance = null;

    /**
     * 获取单例
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 构造函数
     */
    private function __construct() {
        // 代码高亮资源加载
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_prism_assets' ) );
        
        // 浏览量统计
        add_action( 'wp_head', array( $this, 'track_post_views' ) );
        
        // 用户社交字段
        add_filter( 'user_contactmethods', array( $this, 'add_user_social_fields' ) );
        
        // 文章增强样式和脚本
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_article_enhance_assets' ) );
        
        // 注册代码短代码
        add_shortcode( 'code', array( $this, 'code_shortcode' ) );
        
        // 为代码块添加语言类
        add_filter( 'the_content', array( $this, 'enhance_code_blocks' ), 5 );
    }

    /**
     * 加载文章增强样式和脚本
     */
    public function enqueue_article_enhance_assets() {
        if ( ! is_singular( 'post' ) ) {
            return;
        }
        
        // 加载文章增强样式
        wp_enqueue_style(
            'developer-starter-article-enhance',
            DEVELOPER_STARTER_ASSETS . '/css/article-enhance.css',
            array( 'developer-starter-main' ),
            developer_starter_get_assets_version()
        );
        
        // 如果启用了TOC，加载TOC脚本
        $toc_enable = developer_starter_get_option( 'toc_enable', '' );
        if ( $toc_enable ) {
            wp_enqueue_script(
                'developer-starter-article-enhance',
                DEVELOPER_STARTER_ASSETS . '/js/article-enhance.js',
                array( 'jquery' ),
                developer_starter_get_assets_version(),
                true
            );
            
            wp_localize_script( 'developer-starter-article-enhance', 'articleEnhanceConfig', array(
                'tocEnable' => $toc_enable,
                'tocPosition' => developer_starter_get_option( 'toc_position', 'sidebar' ),
                'tocCollapsible' => developer_starter_get_option( 'toc_collapsible', '' ),
                'tocHeadingLevels' => developer_starter_get_option( 'toc_heading_levels', 'h2h3' ),
            ) );
        }
    }

    /**
     * 智能加载 PrismJS 资源
     */
    public function enqueue_prism_assets() {
        if ( ! is_singular( 'post' ) ) {
            return;
        }
        
        $code_highlight_enable = developer_starter_get_option( 'code_highlight_enable', '' );
        if ( ! $code_highlight_enable ) {
            return;
        }
        
        // 检测文章内容是否包含代码块
        global $post;
        $content = $post->post_content;
        
        // 检测 <pre> 或 <code> 标签
        if ( ! preg_match( '/<(pre|code)[^>]*>/i', $content ) ) {
            return;
        }
        
        // 获取CDN设置或使用本地
        $prism_css_cdn = developer_starter_get_option( 'prism_css_cdn', '' );
        $prism_js_cdn = developer_starter_get_option( 'prism_js_cdn', '' );
        
        $css_url = ! empty( $prism_css_cdn ) ? $prism_css_cdn : DEVELOPER_STARTER_ASSETS . '/css/vendor/prism.css';
        $js_url = ! empty( $prism_js_cdn ) ? $prism_js_cdn : DEVELOPER_STARTER_ASSETS . '/js/vendor/prism.js';
        
        // 加载 PrismJS CSS
        wp_enqueue_style( 'prismjs', $css_url, array(), '1.29.0' );
        
        // 加载 PrismJS JS
        wp_enqueue_script( 'prismjs', $js_url, array(), '1.29.0', true );
    }

    /**
     * 为代码块添加行号类 (保留但不默认使用)
     */
    public function add_line_numbers_class( $content ) {
        // 为所有 <pre> 标签添加 line-numbers 类
        $content = preg_replace( '/<pre([^>]*)>/i', '<pre$1 class="line-numbers">', $content );
        return $content;
    }

    /**
     * 统计文章浏览量
     */
    public function track_post_views() {
        if ( ! is_singular( 'post' ) ) {
            return;
        }
        
        $post_views_enable = developer_starter_get_option( 'post_views_enable', '' );
        if ( ! $post_views_enable ) {
            return;
        }
        
        // 检查是否排除管理员
        $exclude_admin = developer_starter_get_option( 'post_views_exclude_admin', '' );
        if ( $exclude_admin && current_user_can( 'manage_options' ) ) {
            return;
        }
        
        $post_id = get_the_ID();
        $count = (int) get_post_meta( $post_id, 'post_views_count', true );
        $count++;
        update_post_meta( $post_id, 'post_views_count', $count );
    }

    /**
     * 获取文章浏览量
     */
    public static function get_post_views( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        $count = get_post_meta( $post_id, 'post_views_count', true );
        return $count ? (int) $count : 0;
    }

    /**
     * 计算阅读时长
     */
    public static function get_reading_time( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        
        $content = get_post_field( 'post_content', $post_id );
        $content = wp_strip_all_tags( $content );
        $word_count = mb_strlen( $content, 'UTF-8' );
        
        $reading_speed = developer_starter_get_option( 'reading_speed', 400 );
        $reading_speed = $reading_speed ? (int) $reading_speed : 400;
        
        $minutes = ceil( $word_count / $reading_speed );
        
        return max( 1, $minutes );
    }

    /**
     * 生成文章目录
     */
    public static function generate_toc( $content ) {
        $toc_enable = developer_starter_get_option( 'toc_enable', '' );
        if ( ! $toc_enable ) {
            return array( 'toc' => '', 'content' => $content );
        }
        
        $heading_levels = developer_starter_get_option( 'toc_heading_levels', 'h2h3' );
        $min_headings = developer_starter_get_option( 'toc_min_headings', 3 );
        $min_headings = $min_headings ? (int) $min_headings : 3;
        
        // 根据设置确定要匹配的标题
        switch ( $heading_levels ) {
            case 'h2':
                $pattern = '/<h2([^>]*)>(.*?)<\/h2>/is';
                break;
            case 'h2h3h4':
                $pattern = '/<h([2-4])([^>]*)>(.*?)<\/h\1>/is';
                break;
            case 'h2h3':
            default:
                $pattern = '/<h([23])([^>]*)>(.*?)<\/h\1>/is';
                break;
        }
        
        preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
        
        if ( count( $matches ) < $min_headings ) {
            return array( 'toc' => '', 'content' => $content );
        }
        
        $toc_items = array();
        $modified_content = $content;
        $offset_adjustment = 0;
        
        foreach ( $matches as $index => $match ) {
            $full_match = $match[0][0];
            $position = $match[0][1];
            
            // 获取标题级别和文本
            if ( $heading_levels === 'h2' ) {
                $level = 2;
                $attrs = $match[1][0];
                $title_text = wp_strip_all_tags( $match[2][0] );
            } else {
                $level = (int) $match[1][0];
                $attrs = $match[2][0];
                $title_text = wp_strip_all_tags( $match[3][0] );
            }
            
            $anchor_id = 'toc-' . $index;
            
            // 检查是否已有 id 属性
            if ( preg_match( '/id=["\']([^"\']+)["\']/i', $attrs, $id_match ) ) {
                $anchor_id = $id_match[1];
                $new_heading = $full_match;
            } else {
                // 添加 id 属性
                $new_heading = preg_replace( 
                    '/<h([2-4])([^>]*)>/i', 
                    '<h$1$2 id="' . $anchor_id . '">', 
                    $full_match 
                );
            }
            
            // 替换内容
            $modified_content = substr_replace( 
                $modified_content, 
                $new_heading, 
                $position + $offset_adjustment, 
                strlen( $full_match ) 
            );
            $offset_adjustment += strlen( $new_heading ) - strlen( $full_match );
            
            $toc_items[] = array(
                'level' => $level,
                'title' => $title_text,
                'anchor' => $anchor_id,
            );
        }
        
        // 生成目录 HTML
        $toc_html = '<nav class="article-toc" id="article-toc">';
        $toc_html .= '<div class="toc-header">';
        $toc_html .= '<span class="toc-title">' . __( '目录', 'developer-starter' ) . '</span>';
        
        $collapsible = developer_starter_get_option( 'toc_collapsible', '' );
        if ( $collapsible ) {
            $toc_html .= '<button class="toc-toggle" aria-label="' . __( '收起目录', 'developer-starter' ) . '">';
            $toc_html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            $toc_html .= '</button>';
        }
        
        $toc_html .= '</div>';
        $toc_html .= '<ul class="toc-list">';
        
        foreach ( $toc_items as $item ) {
            $indent_class = 'toc-level-' . $item['level'];
            $toc_html .= '<li class="toc-item ' . $indent_class . '">';
            $toc_html .= '<a href="#' . esc_attr( $item['anchor'] ) . '" class="toc-link">' . esc_html( $item['title'] ) . '</a>';
            $toc_html .= '</li>';
        }
        
        $toc_html .= '</ul>';
        $toc_html .= '</nav>';
        
        return array( 'toc' => $toc_html, 'content' => $modified_content );
    }

    /**
     * 渲染作者信息卡片
     */
    public static function render_author_box() {
        $author_box_enable = developer_starter_get_option( 'author_box_enable', '' );
        if ( ! $author_box_enable ) {
            return '';
        }
        
        $author_id = get_the_author_meta( 'ID' );
        $show_avatar = developer_starter_get_option( 'author_show_avatar', '1' );
        $show_name = developer_starter_get_option( 'author_show_name', '1' );
        $show_bio = developer_starter_get_option( 'author_show_bio', '1' );
        $show_social = developer_starter_get_option( 'author_show_social', '' );
        
        ob_start();
        ?>
        <div class="author-box">
            <?php if ( $show_avatar ) : ?>
                <div class="author-avatar">
                    <?php echo get_avatar( $author_id, 80 ); ?>
                </div>
            <?php endif; ?>
            
            <div class="author-info">
                <?php if ( $show_name ) : ?>
                    <h4 class="author-name"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></h4>
                <?php endif; ?>
                
                <?php if ( $show_bio ) : 
                    $bio = get_the_author_meta( 'description', $author_id );
                    if ( $bio ) :
                ?>
                    <p class="author-bio"><?php echo esc_html( $bio ); ?></p>
                <?php endif; endif; ?>
                
                <?php if ( $show_social ) : ?>
                    <div class="author-social">
                        <?php echo self::render_social_links( $author_id ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染社交链接
     */
    public static function render_social_links( $user_id ) {
        $social_config = array(
            'user_weibo' => array(
                'option' => 'user_social_weibo',
                'label' => '微博',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M10.098 20.323c-3.977.391-7.414-1.406-7.672-4.02-.259-2.609 2.759-5.047 6.74-5.441 3.979-.394 7.413 1.404 7.671 4.018.259 2.6-2.759 5.049-6.739 5.443zM9.05 17.219c-.384.616-1.208.884-1.829.602-.612-.279-.793-.991-.406-1.593.379-.595 1.176-.861 1.793-.601.622.263.82.972.442 1.592zm1.27-1.627c-.141.237-.449.353-.689.253-.236-.09-.313-.361-.177-.586.138-.227.436-.346.672-.24.239.09.315.36.194.573zm.176-2.719c-1.893-.493-4.033.45-4.857 2.118-.836 1.704-.026 3.591 1.886 4.21 1.983.64 4.318-.341 5.132-2.179.8-1.793-.201-3.642-2.161-4.149zm7.563-1.224c-.346-.105-.577-.18-.405-.645.375-1.016.415-1.891.015-2.514-.75-1.167-2.799-1.105-5.089-.03l-.001.001c-.04.015-.08.021-.105.031-.405.15-.313-.195-.313-.195.6-2.266-.014-3.169-.999-3.345-1.995-.367-4.695 2.236-6.14 4.725l.001-.001c-2.109 3.63-.729 8.055 4.064 9.585 4.999 1.593 10.014-.944 10.015-5.016-.002-.824-.372-1.607-1.043-2.596z"/></svg>',
                'type' => 'link',
            ),
            'user_twitter' => array(
                'option' => 'user_social_twitter',
                'label' => 'X',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
                'type' => 'link',
            ),
            'user_wechat' => array(
                'option' => 'user_social_wechat',
                'label' => '微信',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 0 1 .213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 0 0 .167-.054l1.903-1.114a.864.864 0 0 1 .717-.098 10.16 10.16 0 0 0 2.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348zM5.785 5.991c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178A1.17 1.17 0 0 1 4.623 7.17c0-.651.52-1.18 1.162-1.18zm5.813 0c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178 1.17 1.17 0 0 1-1.162-1.178c0-.651.52-1.18 1.162-1.18zm5.34 2.867c-1.797-.052-3.746.512-5.28 1.786-1.72 1.428-2.687 3.72-1.78 6.22.942 2.453 3.666 4.229 6.884 4.229.826 0 1.622-.12 2.361-.336a.722.722 0 0 1 .598.082l1.584.926a.272.272 0 0 0 .14.047c.134 0 .24-.111.24-.247 0-.06-.023-.12-.038-.177l-.327-1.233a.582.582 0 0 1-.023-.156.49.49 0 0 1 .201-.398C23.024 18.48 24 16.82 24 14.98c0-3.21-2.931-5.837-6.656-6.088V8.89c-.135-.01-.27-.027-.406-.033zm-1.091 2.819c.535 0 .969.44.969.983a.976.976 0 0 1-.969.983.976.976 0 0 1-.969-.983c0-.542.434-.983.97-.983zm4.844 0c.535 0 .969.44.969.983a.976.976 0 0 1-.969.983.976.976 0 0 1-.969-.983c0-.542.434-.983.969-.983z"/></svg>',
                'type' => 'qrcode',
            ),
            'user_github' => array(
                'option' => 'user_social_github',
                'label' => 'GitHub',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>',
                'type' => 'link',
            ),
            'user_bilibili' => array(
                'option' => 'user_social_bilibili',
                'label' => 'B站',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.813 4.653h.854c1.51.054 2.769.578 3.773 1.574 1.004.995 1.524 2.249 1.56 3.76v7.36c-.036 1.51-.556 2.769-1.56 3.773s-2.262 1.524-3.773 1.56H5.333c-1.51-.036-2.769-.556-3.773-1.56S.036 18.858 0 17.347v-7.36c.036-1.511.556-2.765 1.56-3.76 1.004-.996 2.262-1.52 3.773-1.574h.774l-1.174-1.12a1.234 1.234 0 0 1-.373-.906c0-.356.124-.658.373-.907l.027-.027c.267-.249.573-.373.92-.373.347 0 .653.124.92.373L9.653 4.44c.071.071.134.142.187.213h4.267a.836.836 0 0 1 .16-.213l2.853-2.747c.267-.249.573-.373.92-.373.347 0 .662.151.929.4.267.249.391.551.391.907 0 .355-.124.657-.373.906zM5.333 7.24c-.746.018-1.373.276-1.88.773-.506.498-.769 1.13-.786 1.894v7.52c.017.764.28 1.395.786 1.893.507.498 1.134.756 1.88.773h13.334c.746-.017 1.373-.275 1.88-.773.506-.498.769-1.129.786-1.893v-7.52c-.017-.765-.28-1.396-.786-1.894-.507-.497-1.134-.755-1.88-.773zM8 11.107c.373 0 .684.124.933.373.25.249.383.569.4.96v1.173c-.017.391-.15.711-.4.96-.249.25-.56.374-.933.374s-.684-.125-.933-.374c-.25-.249-.383-.569-.4-.96V12.44c0-.373.129-.689.386-.947.258-.257.574-.386.947-.386zm8 0c.373 0 .684.124.933.373.25.249.383.569.4.96v1.173c-.017.391-.15.711-.4.96-.249.25-.56.374-.933.374s-.684-.125-.933-.374c-.25-.249-.383-.569-.4-.96V12.44c.017-.391.15-.711.4-.96.249-.249.56-.373.933-.373z"/></svg>',
                'type' => 'link',
            ),
            'user_zhihu' => array(
                'option' => 'user_social_zhihu',
                'label' => '知乎',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M5.721 0C2.251 0 0 2.25 0 5.719V18.28C0 21.751 2.252 24 5.721 24h12.56C21.751 24 24 21.75 24 18.281V5.72C24 2.249 21.75 0 18.281 0zm1.964 4.078c-.271.73-.5 1.434-.68 2.11h4.587c.545-.006.445 1.575.091 1.575h-4.826c-.062.195-.111.39-.16.586 3.019.259 4.436 1.755 4.436 3.705 0 2.467-1.814 3.473-4.181 3.473-1.271 0-2.222-.376-2.222-.376V13.15s.907.344 2.016.344c1.107 0 1.778-.536 1.778-1.341 0-.805-.671-1.453-1.778-1.453-.939 0-1.885.379-2.619 1.136-.291.299-.573.613-.811.932l-1.571-1.018c.251-.465.503-.912.755-1.341H3.93c-.545 0-.445-1.575-.091-1.575h3.544c.209-.597.473-1.32.791-2.109h-3.68c-.544 0-.445-1.575-.09-1.575h4.392l.01-.009c.544-.611 1.485-.611 1.485.271 0 .271-.045.524-.09.758h4.542c.545 0 .445 1.574.091 1.574H7.685zm11.117 11.237s.18-1.826-.09-1.826h-3.064c-.09 0-.18.091-.18.181v4.086c0 1.305-.36 1.755-1.126 1.755-.764 0-1.394-.543-1.394-.543l-.725 1.305s.995 1.033 2.585 1.033c1.622 0 2.841-1.033 2.841-3.064v-4.572h.856c.09 0 .18-.09.18-.181v-.727c0-.09-.09-.18-.18-.18h-1.531V9.509c0-.09-.09-.18-.18-.18h-1.531c-.09 0-.18.09-.18.18v3.337h-.855c-.09 0-.18.09-.18.18v.727c0 .09.09.181.18.181h2.574z"/></svg>',
                'type' => 'link',
            ),
            'user_website' => array(
                'option' => 'user_social_website',
                'label' => '网站',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>',
                'type' => 'link',
            ),
        );
        
        $output = '';
        
        foreach ( $social_config as $meta_key => $config ) {
            // 检查后台是否启用该社交链接
            if ( ! developer_starter_get_option( $config['option'], '' ) ) {
                continue;
            }
            
            $value = get_user_meta( $user_id, $meta_key, true );
            if ( empty( $value ) ) {
                continue;
            }
            
            if ( $config['type'] === 'qrcode' ) {
                // 微信二维码悬停显示
                $output .= '<span class="social-link social-wechat-qr">';
                $output .= $config['icon'];
                $output .= '<span class="social-label">' . esc_html( $config['label'] ) . '</span>';
                $output .= '<span class="wechat-qr-popup"><img src="' . esc_url( $value ) . '" alt="WeChat QR"></span>';
                $output .= '</span>';
            } else {
                $output .= '<a href="' . esc_url( $value ) . '" class="social-link" target="_blank" rel="noopener">';
                $output .= $config['icon'];
                $output .= '<span class="social-label">' . esc_html( $config['label'] ) . '</span>';
                $output .= '</a>';
            }
        }
        
        return $output;
    }

    /**
     * 渲染版权信息
     */
    public static function render_copyright() {
        $copyright_enable = developer_starter_get_option( 'copyright_enable', '' );
        if ( ! $copyright_enable ) {
            return '';
        }
        
        $content = developer_starter_get_option( 'copyright_content', '' );
        $reprint_notice = developer_starter_get_option( 'copyright_reprint_notice', '' );
        
        // 替换变量
        $replacements = array(
            '{title}' => get_the_title(),
            '{url}' => get_permalink(),
            '{author}' => get_the_author(),
            '{date}' => get_the_date(),
            '{site}' => get_bloginfo( 'name' ),
        );
        
        $content = str_replace( array_keys( $replacements ), array_values( $replacements ), $content );
        
        ob_start();
        ?>
        <div class="post-copyright">
            <div class="copyright-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M14.83 14.83a4 4 0 1 1 0-5.66"></path>
                </svg>
            </div>
            <div class="copyright-content">
                <?php if ( $content ) : ?>
                    <p class="copyright-text"><?php echo wp_kses_post( $content ); ?></p>
                <?php else : ?>
                    <p class="copyright-text">
                        <strong><?php echo esc_html( get_the_title() ); ?></strong><br>
                        <?php echo esc_url( get_permalink() ); ?>
                    </p>
                <?php endif; ?>
                
                <?php if ( $reprint_notice ) : ?>
                    <p class="copyright-notice"><?php echo esc_html( $reprint_notice ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * 添加用户社交字段（根据后台开关）
     */
    public function add_user_social_fields( $methods ) {
        // 根据后台设置动态添加字段
        $fields = array(
            'user_social_weibo' => array( 'key' => 'user_weibo', 'label' => '微博链接' ),
            'user_social_twitter' => array( 'key' => 'user_twitter', 'label' => 'X (Twitter) 链接' ),
            'user_social_wechat' => array( 'key' => 'user_wechat', 'label' => '微信二维码图片URL' ),
            'user_social_github' => array( 'key' => 'user_github', 'label' => 'GitHub 链接' ),
            'user_social_bilibili' => array( 'key' => 'user_bilibili', 'label' => 'B站链接' ),
            'user_social_zhihu' => array( 'key' => 'user_zhihu', 'label' => '知乎链接' ),
            'user_social_website' => array( 'key' => 'user_website', 'label' => '个人网站' ),
        );
        
        foreach ( $fields as $option_key => $field_data ) {
            if ( developer_starter_get_option( $option_key, '' ) ) {
                $methods[ $field_data['key'] ] = __( $field_data['label'], 'developer-starter' );
            }
        }
        
        return $methods;
    }
    
    /**
     * 代码短代码
     * 用法: [code lang="php"]代码内容[/code]
     */
    public function code_shortcode( $atts, $content = null ) {
        $atts = shortcode_atts( array(
            'lang' => 'markup',
            'line' => '',
        ), $atts, 'code' );
        
        $lang = sanitize_text_field( $atts['lang'] );
        $line_attr = $atts['line'] ? ' data-line="' . esc_attr( $atts['line'] ) . '"' : '';
        
        // 解码内容
        $code = html_entity_decode( $content );
        $code = trim( $code );
        
        return '<pre class="language-' . esc_attr( $lang ) . ' line-numbers"' . $line_attr . '><code class="language-' . esc_attr( $lang ) . '">' . esc_html( $code ) . '</code></pre>';
    }
    
    /**
     * 增强代码块 - 为没有语言类的代码块添加默认类
     */
    public function enhance_code_blocks( $content ) {
        if ( ! is_singular( 'post' ) ) {
            return $content;
        }
        
        $code_highlight_enable = developer_starter_get_option( 'code_highlight_enable', '' );
        if ( ! $code_highlight_enable ) {
            return $content;
        }
        
        // 为没有 language- 类的 pre 标签添加默认类
        $content = preg_replace_callback(
            '/<pre([^>]*)><code([^>]*)>/i',
            function( $matches ) {
                $pre_attrs = $matches[1];
                $code_attrs = $matches[2];
                
                // 检查是否已有 language- 类
                if ( strpos( $code_attrs, 'language-' ) === false && strpos( $pre_attrs, 'language-' ) === false ) {
                    // Gutenberg 编辑器可能使用 wp-block-code 类和 lang-* 或 data-lang
                    if ( preg_match( '/class=["\'][^"\']*lang-(\w+)/', $pre_attrs . $code_attrs, $lang_match ) ) {
                        $lang = $lang_match[1];
                    } elseif ( preg_match( '/data-lang=["\']([\w+-]+)["\']/', $pre_attrs . $code_attrs, $lang_match ) ) {
                        $lang = $lang_match[1];
                    } else {
                        $lang = 'markup';
                    }
                    
                    // 添加 language 类
                    if ( strpos( $pre_attrs, 'class=' ) !== false ) {
                        $pre_attrs = preg_replace( '/class=["\']/', 'class="language-' . $lang . ' ', $pre_attrs );
                    } else {
                        $pre_attrs .= ' class="language-' . $lang . '"';
                    }
                    
                    if ( strpos( $code_attrs, 'class=' ) !== false ) {
                        $code_attrs = preg_replace( '/class=["\']/', 'class="language-' . $lang . ' ', $code_attrs );
                    } else {
                        $code_attrs .= ' class="language-' . $lang . '"';
                    }
                }
                
                return '<pre' . $pre_attrs . '><code' . $code_attrs . '>';
            },
            $content
        );
        
        return $content;
    }

    /**
     * 获取正文样式 CSS 变量
     */
    public static function get_content_style_vars() {
        $width_map = array(
            'narrow' => '680px',
            'standard' => '800px',
            'wide' => '960px',
        );
        
        $font_size_map = array(
            'small' => '16px',
            'medium' => '18px',
            'large' => '20px',
        );
        
        $line_height_map = array(
            'compact' => '1.6',
            'standard' => '1.8',
            'relaxed' => '2.0',
        );
        
        $paragraph_spacing_map = array(
            'small' => '1em',
            'medium' => '1.5em',
            'large' => '2em',
        );
        
        $width_key = developer_starter_get_option( 'post_content_width', 'standard' );
        $font_key = developer_starter_get_option( 'post_font_size', 'medium' );
        $line_key = developer_starter_get_option( 'post_line_height', 'standard' );
        $para_key = developer_starter_get_option( 'post_paragraph_spacing', 'medium' );
        $img_width = developer_starter_get_option( 'post_image_max_width', '100' );
        
        return array(
            '--post-content-width' => isset( $width_map[ $width_key ] ) ? $width_map[ $width_key ] : '800px',
            '--post-font-size' => isset( $font_size_map[ $font_key ] ) ? $font_size_map[ $font_key ] : '18px',
            '--post-line-height' => isset( $line_height_map[ $line_key ] ) ? $line_height_map[ $line_key ] : '1.8',
            '--post-paragraph-spacing' => isset( $paragraph_spacing_map[ $para_key ] ) ? $paragraph_spacing_map[ $para_key ] : '1.5em',
            '--post-image-max-width' => $img_width ? $img_width . '%' : '100%',
        );
    }
}
