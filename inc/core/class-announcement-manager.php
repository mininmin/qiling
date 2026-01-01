<?php
/**
 * 公告管理器类
 *
 * 多功能公告系统，支持多种公告类型、条件显示、频率控制
 *
 * @package Developer_Starter
 * @since 1.0.2
 */

namespace Developer_Starter\Core;

// 防止直接访问
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 公告管理器类
 */
class Announcement_Manager {

    /**
     * 构造函数
     */
    public function __construct() {
        // 前端加载公告
        add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ) );
        add_action( 'wp_footer', array( $this, 'render_announcement' ) );
        
        // AJAX 处理
        add_action( 'wp_ajax_dismiss_announcement', array( $this, 'ajax_dismiss' ) );
        add_action( 'wp_ajax_nopriv_dismiss_announcement', array( $this, 'ajax_dismiss' ) );
    }

    /**
     * 检查是否应该在当前页面显示公告
     *
     * @return bool
     */
    public function should_display() {
        // 检查公告是否启用
        $enabled = developer_starter_get_option( 'announcement_enable', '' );
        if ( ! $enabled ) {
            return false;
        }
        
        // 检查公告内容是否为空
        $content = developer_starter_get_option( 'announcement_content', '' );
        $title = developer_starter_get_option( 'announcement_title', '' );
        if ( empty( $content ) && empty( $title ) ) {
            return false;
        }
        
        // 检查显示页面
        if ( ! $this->check_display_pages() ) {
            return false;
        }
        
        return true;
    }

    /**
     * 检查是否在允许显示的页面
     *
     * @return bool
     */
    private function check_display_pages() {
        $display_on = developer_starter_get_option( 'announcement_display_on', 'all' );
        
        switch ( $display_on ) {
            case 'all':
                return true;
                
            case 'homepage':
                return is_front_page() || is_home();
                
            case 'pages':
                if ( ! is_page() ) {
                    return false;
                }
                $page_ids = developer_starter_get_option( 'announcement_page_ids', '' );
                if ( empty( $page_ids ) ) {
                    return false;
                }
                $ids = array_map( 'intval', array_filter( explode( ',', $page_ids ) ) );
                return in_array( get_the_ID(), $ids );
                
            case 'posts':
                if ( ! is_single() ) {
                    return false;
                }
                $post_ids = developer_starter_get_option( 'announcement_post_ids', '' );
                if ( empty( $post_ids ) ) {
                    return false;
                }
                $ids = array_map( 'intval', array_filter( explode( ',', $post_ids ) ) );
                return in_array( get_the_ID(), $ids );
                
            case 'categories':
                if ( ! is_category() && ! ( is_single() && has_category() ) ) {
                    return false;
                }
                $cat_ids = developer_starter_get_option( 'announcement_category_ids', array() );
                if ( empty( $cat_ids ) || ! is_array( $cat_ids ) ) {
                    return false;
                }
                if ( is_category() ) {
                    return in_array( get_queried_object_id(), $cat_ids );
                }
                // 文章页检查是否属于指定分类
                $post_cats = wp_get_post_categories( get_the_ID() );
                return ! empty( array_intersect( $post_cats, $cat_ids ) );
                
            default:
                return true;
        }
    }

    /**
     * 条件加载前端资源
     */
    public function maybe_enqueue_assets() {
        if ( ! $this->should_display() ) {
            return;
        }
        
        // 加载公告 CSS
        wp_enqueue_style( 
            'developer-starter-announcement', 
            DEVELOPER_STARTER_ASSETS . '/css/announcement.css', 
            array(), 
            DEVELOPER_STARTER_VERSION 
        );
        
        // 加载公告 JS
        wp_enqueue_script( 
            'developer-starter-announcement', 
            DEVELOPER_STARTER_ASSETS . '/js/announcement.js', 
            array(), 
            DEVELOPER_STARTER_VERSION, 
            true 
        );
        
        // 传递数据到 JS
        $frequency = developer_starter_get_option( 'announcement_frequency', 'always' );
        $allow_dismiss = developer_starter_get_option( 'announcement_allow_dismiss', '1' );
        $announcement_id = developer_starter_get_option( 'announcement_id', '' );
        if ( empty( $announcement_id ) ) {
            $announcement_id = 'ann_' . md5( developer_starter_get_option( 'announcement_title', '' ) . developer_starter_get_option( 'announcement_content', '' ) );
        }
        
        wp_localize_script( 'developer-starter-announcement', 'dsAnnouncement', array(
            'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'announcement_nonce' ),
            'frequency'    => $frequency,
            'allowDismiss' => $allow_dismiss === '1',
            'announcementId' => $announcement_id,
        ) );
    }

    /**
     * 渲染公告 HTML
     */
    public function render_announcement() {
        if ( ! $this->should_display() ) {
            return;
        }
        
        $type = developer_starter_get_option( 'announcement_type', 'normal' );
        $title = developer_starter_get_option( 'announcement_title', '' );
        $content = developer_starter_get_option( 'announcement_content', '' );
        $image = developer_starter_get_option( 'announcement_image', '' );
        $btn_text = developer_starter_get_option( 'announcement_btn_text', '' );
        $btn_url = developer_starter_get_option( 'announcement_btn_url', '' );
        $allow_dismiss = developer_starter_get_option( 'announcement_allow_dismiss', '1' );
        $frequency = developer_starter_get_option( 'announcement_frequency', 'always' );
        
        // 公告唯一ID
        $announcement_id = developer_starter_get_option( 'announcement_id', '' );
        if ( empty( $announcement_id ) ) {
            $announcement_id = 'ann_' . md5( $title . $content );
        }
        
        $type_class = 'announcement-' . esc_attr( $type );
        ?>
        <div id="ds-announcement" class="ds-announcement <?php echo $type_class; ?>" data-id="<?php echo esc_attr( $announcement_id ); ?>" style="display: none;">
            <div class="announcement-overlay"></div>
            <div class="announcement-modal">
                <button type="button" class="announcement-close" aria-label="关闭">&times;</button>
                
                <?php if ( $type === 'image' && $image ) : ?>
                    <div class="announcement-image">
                        <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
                    </div>
                <?php endif; ?>
                
                <div class="announcement-body">
                    <?php if ( $type === 'marketing' ) : ?>
                        <div class="announcement-badge">限时活动</div>
                    <?php endif; ?>
                    
                    <?php if ( $title ) : ?>
                        <h3 class="announcement-title"><?php echo esc_html( $title ); ?></h3>
                    <?php endif; ?>
                    
                    <?php if ( $type === 'image_text' && $image ) : ?>
                        <div class="announcement-inline-image">
                            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $content ) : ?>
                        <div class="announcement-content">
                            <?php echo wp_kses_post( wpautop( $content ) ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $btn_text && $btn_url ) : ?>
                        <div class="announcement-action">
                            <a href="<?php echo esc_url( $btn_url ); ?>" class="announcement-btn">
                                <?php echo esc_html( $btn_text ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ( $allow_dismiss === '1' && $frequency === 'always' ) : ?>
                    <div class="announcement-dismiss">
                        <label>
                            <input type="checkbox" id="announcement-today-dismiss" />
                            <span>今日不再显示</span>
                        </label>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        
        // 输出动态样式
        $this->output_custom_styles( $type );
    }

    /**
     * AJAX 处理关闭公告
     */
    public function ajax_dismiss() {
        check_ajax_referer( 'announcement_nonce', 'nonce' );
        
        // 这里可以记录用户关闭行为，目前使用 cookie 在前端处理
        wp_send_json_success();
    }
    
    /**
     * 输出自定义样式
     * 
     * @param string $type 公告类型
     */
    private function output_custom_styles( $type ) {
        $styles = array();
        
        // 普通/图片/图文公告按钮样式
        if ( in_array( $type, array( 'normal', 'image', 'image_text' ) ) ) {
            $normal_btn_bg = developer_starter_get_option( 'announcement_normal_btn_bg', '' );
            $normal_btn_color = developer_starter_get_option( 'announcement_normal_btn_color', '' );
            $normal_btn_hover_bg = developer_starter_get_option( 'announcement_normal_btn_hover_bg', '' );
            
            if ( ! empty( $normal_btn_bg ) ) {
                $styles[] = '.announcement-normal .announcement-btn, .announcement-image .announcement-btn, .announcement-image_text .announcement-btn { background: ' . esc_attr( $normal_btn_bg ) . '; }';
            }
            
            if ( ! empty( $normal_btn_color ) ) {
                $styles[] = '.announcement-normal .announcement-btn, .announcement-image .announcement-btn, .announcement-image_text .announcement-btn { color: ' . esc_attr( $normal_btn_color ) . '; }';
            }
            
            if ( ! empty( $normal_btn_hover_bg ) ) {
                $styles[] = '.announcement-normal .announcement-btn:hover, .announcement-image .announcement-btn:hover, .announcement-image_text .announcement-btn:hover { background: ' . esc_attr( $normal_btn_hover_bg ) . '; }';
            }
        }
        
        // 营销活动公告样式
        if ( $type === 'marketing' ) {
            $marketing_modal_bg = developer_starter_get_option( 'announcement_marketing_modal_bg', '' );
            $marketing_btn_bg = developer_starter_get_option( 'announcement_marketing_btn_bg', '' );
            $marketing_btn_color = developer_starter_get_option( 'announcement_marketing_btn_color', '' );
            $marketing_btn_hover_bg = developer_starter_get_option( 'announcement_marketing_btn_hover_bg', '' );
            
            if ( ! empty( $marketing_modal_bg ) ) {
                $styles[] = '.announcement-marketing .announcement-modal { background: ' . esc_attr( $marketing_modal_bg ) . '; }';
            }
            
            if ( ! empty( $marketing_btn_bg ) ) {
                $styles[] = '.announcement-marketing .announcement-btn { background: ' . esc_attr( $marketing_btn_bg ) . '; }';
            }
            
            if ( ! empty( $marketing_btn_color ) ) {
                $styles[] = '.announcement-marketing .announcement-btn { color: ' . esc_attr( $marketing_btn_color ) . '; }';
            }
            
            if ( ! empty( $marketing_btn_hover_bg ) ) {
                $styles[] = '.announcement-marketing .announcement-btn:hover { background: ' . esc_attr( $marketing_btn_hover_bg ) . '; }';
            }
        }
        
        // 输出样式
        if ( ! empty( $styles ) ) {
            echo '<style id="announcement-custom-styles">' . implode( ' ', $styles ) . '</style>';
        }
    }
}
