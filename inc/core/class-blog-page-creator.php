<?php
/**
 * 博客页面创建器类
 *
 * 当用户选择"博客页面"模板创建页面时，自动填充预设模块内容
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

namespace Developer_Starter\Core;

// 防止直接访问
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 博客页面创建器类
 */
class Blog_Page_Creator {

    /**
     * 构造函数
     */
    public function __construct() {
        // 使用更高优先级确保在 meta-boxes 保存之后执行
        add_action( 'save_post', array( $this, 'on_page_save' ), 99, 2 );
        
        // 添加 AJAX 钩子用于手动填充模块
        add_action( 'wp_ajax_fill_blog_page_modules', array( $this, 'ajax_fill_modules' ) );
    }

    /**
     * 页面保存时的回调
     *
     * @param int     $post_id 页面ID
     * @param WP_Post $post    页面对象
     */
    public function on_page_save( $post_id, $post ) {
        // 只处理页面类型
        if ( $post->post_type !== 'page' ) {
            return;
        }

        // 检查是否为自动保存
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // 检查权限
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // 获取页面模板
        $template = get_post_meta( $post_id, '_wp_page_template', true );

        // 只处理博客页面模板
        if ( $template !== 'templates/template-blog.php' ) {
            return;
        }

        // 检查是否已有模块配置
        $modules = get_post_meta( $post_id, '_developer_starter_modules', true );
        
        // 检查是否已标记为已填充（避免重复填充）
        $filled = get_post_meta( $post_id, '_blog_page_modules_filled', true );
        
        // 如果没有模块（空数组或空值）且尚未填充过，设置默认模块
        if ( ( empty( $modules ) || ! is_array( $modules ) || count( $modules ) === 0 ) && ! $filled ) {
            $this->set_default_modules( $post_id );
            // 标记为已填充，防止后续再次覆盖
            update_post_meta( $post_id, '_blog_page_modules_filled', '1' );
        }
    }

    /**
     * AJAX 手动填充模块
     */
    public function ajax_fill_modules() {
        check_ajax_referer( 'fill_blog_page_modules', 'nonce' );
        
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        
        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => '权限不足' ) );
        }
        
        $this->set_default_modules( $post_id );
        update_post_meta( $post_id, '_blog_page_modules_filled', '1' );
        
        wp_send_json_success( array( 'message' => '模块已填充，请刷新页面' ) );
    }

    /**
     * 设置博客页面的默认模块
     *
     * @param int $page_id 页面ID
     */
    public function set_default_modules( $page_id ) {
        $default_modules = array(
            // 模块1：博客置顶推荐 - 轮播展示精选文章
            array(
                'type' => 'featured_posts',
                'data' => array(
                    'fp_title'           => '',
                    'fp_bg_color'        => '',
                    'fp_layout'          => 'dual',
                    'fp_slider_ratio'    => '65',
                    'fp_slider_height'   => '420px',
                    'fp_autoplay'        => 'yes',
                    'fp_interval'        => '5000',
                    'fp_effect'          => 'fade',
                    'fp_show_arrows'     => 'yes',
                    'fp_show_dots'       => 'yes',
                    'fp_slider_source'   => 'latest',
                    'fp_slider_count'    => '5',
                    'fp_list_source'     => 'latest',
                    'fp_list_count'      => '4',
                    'fp_badge_type'      => 'recommend',
                    'fp_badge_position'  => 'left',
                    'fp_show_category'   => 'yes',
                    'fp_show_date'       => 'yes',
                    'fp_show_excerpt'    => 'no',
                ),
            ),

            // 模块2：博客布局 - 文章列表（支持分页）
            array(
                'type' => 'blog',
                'data' => array(
                    'blog_title'          => '',
                    'blog_subtitle'       => '',
                    'blog_bg_color'       => '',
                    'blog_page_layout'    => 'full',
                    'blog_layout_style'   => 'card',
                    'blog_columns'        => '3',
                    'blog_data_source'    => 'latest',
                    'blog_count'          => '12',
                    'blog_orderby'        => 'date',
                    'blog_show_image'     => 'yes',
                    'blog_image_height'   => '200px',
                    'blog_show_excerpt'   => 'yes',
                    'blog_excerpt_length' => '60',
                    'blog_show_author'    => 'no',
                    'blog_show_date'      => 'yes',
                    'blog_show_category'  => 'yes',
                    'blog_show_tags'      => 'no',
                    'blog_read_more_text' => '阅读全文',
                    'blog_enable_pagination' => 'yes', // 启用分页
                ),
            ),
        );

        update_post_meta( $page_id, '_developer_starter_modules', $default_modules );
    }
}
