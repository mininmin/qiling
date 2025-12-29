<?php
/**
 * 首页创建器类
 *
 * 当主题激活时自动创建模块化首页
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
 * 首页创建器类
 */
class Homepage_Creator {

    /**
     * 首页标题
     */
    const PAGE_TITLE = '首页';

    /**
     * 首页别名
     */
    const PAGE_SLUG = 'home';

    /**
     * 构造函数
     */
    public function __construct() {
        // 主题激活时创建首页
        add_action( 'after_switch_theme', array( $this, 'on_theme_activation' ) );
        
        // 显示管理后台通知
        add_action( 'admin_notices', array( $this, 'show_admin_notice' ) );
        
        // 处理通知关闭
        add_action( 'admin_init', array( $this, 'dismiss_notice' ), 5 );
    }

    /**
     * 主题激活时的回调
     */
    public function on_theme_activation() {
        $this->create_modular_homepage();
    }

    /**
     * 创建模块化首页
     */
    public function create_modular_homepage() {
        // 检查是否已存在首页
        $existing_page = get_page_by_path( self::PAGE_SLUG );
        
        if ( $existing_page ) {
            // 页面已存在，更新模板和模块
            update_post_meta( $existing_page->ID, '_wp_page_template', 'templates/template-home.php' );
            
            // 如果没有模块，设置默认模块
            $modules = get_post_meta( $existing_page->ID, '_developer_starter_modules', true );
            if ( empty( $modules ) ) {
                $this->set_default_modules( $existing_page->ID );
            }
            
            // 设置为静态首页
            $this->set_as_frontpage( $existing_page->ID );
            set_transient( 'developer_starter_homepage_notice', 'existing', 300 );
            return $existing_page->ID;
        }

        // 检查是否已存在标题为"首页"的页面
        $pages = get_posts( array(
            'post_type'      => 'page',
            'title'          => self::PAGE_TITLE,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
        ) );
        
        if ( ! empty( $pages ) ) {
            $existing_by_title = $pages[0];
            update_post_meta( $existing_by_title->ID, '_wp_page_template', 'templates/template-home.php' );
            
            $modules = get_post_meta( $existing_by_title->ID, '_developer_starter_modules', true );
            if ( empty( $modules ) ) {
                $this->set_default_modules( $existing_by_title->ID );
            }
            
            $this->set_as_frontpage( $existing_by_title->ID );
            set_transient( 'developer_starter_homepage_notice', 'existing', 300 );
            return $existing_by_title->ID;
        }

        // 创建新首页
        $page_data = array(
            'post_title'   => self::PAGE_TITLE,
            'post_name'    => self::PAGE_SLUG,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => get_current_user_id() ?: 1,
        );

        $page_id = wp_insert_post( $page_data );
        
        if ( $page_id && ! is_wp_error( $page_id ) ) {
            // 设置页面模板为"模块化首页"
            update_post_meta( $page_id, '_wp_page_template', 'templates/template-home.php' );
            
            // 设置默认模块
            $this->set_default_modules( $page_id );
            
            // 设置为静态首页
            $this->set_as_frontpage( $page_id );
            
            // 设置通知
            set_transient( 'developer_starter_homepage_notice', 'created', 300 );
            
            return $page_id;
        }

        return false;
    }

    /**
     * 设置默认模块
     *
     * @param int $page_id 页面ID
     */
    private function set_default_modules( $page_id ) {
        $default_modules = array(
            // Banner横幅模块
            array(
                'type' => 'banner',
                'data' => array(
                    'title'       => '欢迎来到我们的网站',
                    'subtitle'    => '专业、高效、值得信赖的企业服务',
                    'description' => '我们致力于为客户提供优质的产品与服务，助力企业快速发展',
                    'btn_text'    => '了解更多',
                    'btn_url'     => '#services',
                    'btn2_text'   => '联系我们',
                    'btn2_url'    => '#contact',
                    'bg_color'    => 'linear-gradient(135deg, #2563eb 0%, #7c3aed 100%)',
                ),
            ),
            // 服务模块
            array(
                'type' => 'services',
                'data' => array(
                    'title'    => '我们的服务',
                    'subtitle' => '专业团队为您提供全方位解决方案',
                    'items'    => array(
                        array(
                            'icon'  => '🚀',
                            'title' => '快速响应',
                            'desc'  => '7x24小时服务，快速响应客户需求',
                        ),
                        array(
                            'icon'  => '🛡️',
                            'title' => '安全可靠',
                            'desc'  => '严格的安全标准，保障数据安全',
                        ),
                        array(
                            'icon'  => '💡',
                            'title' => '创新技术',
                            'desc'  => '采用前沿技术，持续创新升级',
                        ),
                        array(
                            'icon'  => '🤝',
                            'title' => '专业支持',
                            'desc'  => '经验丰富的团队，提供专业技术支持',
                        ),
                    ),
                ),
            ),
            // 特性模块
            array(
                'type' => 'features',
                'data' => array(
                    'title'    => '为什么选择我们',
                    'subtitle' => '多年行业经验，值得信赖',
                    'items'    => array(
                        array(
                            'title' => '专业团队',
                            'desc'  => '拥有经验丰富的专业团队',
                        ),
                        array(
                            'title' => '品质保障',
                            'desc'  => '严格的质量控制体系',
                        ),
                        array(
                            'title' => '贴心服务',
                            'desc'  => '全程跟踪的客户服务',
                        ),
                    ),
                ),
            ),
            // 数据统计模块
            array(
                'type' => 'stats',
                'data' => array(
                    'items' => array(
                        array(
                            'number' => '10+',
                            'label'  => '年行业经验',
                        ),
                        array(
                            'number' => '500+',
                            'label'  => '服务客户',
                        ),
                        array(
                            'number' => '1000+',
                            'label'  => '成功案例',
                        ),
                        array(
                            'number' => '99%',
                            'label'  => '客户满意度',
                        ),
                    ),
                ),
            ),
            // CTA行动召唤模块
            array(
                'type' => 'cta',
                'data' => array(
                    'title'    => '准备好开始了吗？',
                    'subtitle' => '立即联系我们，获取专属解决方案',
                    'btn_text' => '立即咨询',
                    'btn_url'  => '/contact/',
                    'bg_color' => 'linear-gradient(135deg, #2563eb 0%, #7c3aed 100%)',
                ),
            ),
            // 新闻模块
            array(
                'type' => 'news',
                'data' => array(
                    'title'    => '最新动态',
                    'subtitle' => '了解我们的最新资讯',
                    'count'    => 3,
                    'columns'  => 3,
                ),
            ),
            // 联系模块
            array(
                'type' => 'contact',
                'data' => array(
                    'title'        => '联系我们',
                    'subtitle'     => '有任何问题，欢迎随时联系',
                    'show_form'    => true,
                    'show_info'    => true,
                    'show_map'     => false,
                ),
            ),
        );

        update_post_meta( $page_id, '_developer_starter_modules', $default_modules );
    }

    /**
     * 设置为静态首页
     *
     * @param int $page_id 页面ID
     */
    private function set_as_frontpage( $page_id ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $page_id );
    }

    /**
     * 显示管理后台通知
     */
    public function show_admin_notice() {
        $notice_type = get_transient( 'developer_starter_homepage_notice' );
        
        if ( ! $notice_type ) {
            return;
        }

        $dismiss_url = add_query_arg( 'developer_starter_dismiss_notice', '1' );
        $page_id = get_option( 'page_on_front' );

        if ( $notice_type === 'created' ) {
            $message = sprintf(
                '🎉 <strong>启灵主题</strong> 已自动为您创建了模块化首页！<a href="%s">编辑首页</a> | <a href="%s">查看网站</a>',
                admin_url( 'post.php?post=' . $page_id . '&action=edit' ),
                home_url( '/' )
            );
        } else {
            $message = sprintf(
                '✅ <strong>启灵主题</strong> 已将现有首页设置为网站主页！<a href="%s">编辑首页</a> | <a href="%s">查看网站</a>',
                admin_url( 'post.php?post=' . $page_id . '&action=edit' ),
                home_url( '/' )
            );
        }

        echo '<div class="notice notice-success is-dismissible" style="padding: 12px 15px;">';
        echo wp_kses_post( $message );
        echo ' <a href="' . esc_url( $dismiss_url ) . '" style="margin-left: 15px; color: #666;">不再显示</a>';
        echo '</div>';
    }

    /**
     * 处理通知关闭
     */
    public function dismiss_notice() {
        if ( isset( $_GET['developer_starter_dismiss_notice'] ) && $_GET['developer_starter_dismiss_notice'] === '1' ) {
            delete_transient( 'developer_starter_homepage_notice' );
            
            // 重定向回当前页面
            wp_safe_redirect( remove_query_arg( 'developer_starter_dismiss_notice' ) );
            exit;
        }
    }
}
