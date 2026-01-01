<?php
/**
 * 资源下载页面创建器类
 *
 * 当用户选择"资源下载"模板创建页面时，自动填充预设模块内容
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
 * 资源下载页面创建器类
 */
class Resources_Page_Creator {

    /**
     * 构造函数
     */
    public function __construct() {
        // 使用更高优先级确保在 meta-boxes 保存之后执行
        add_action( 'save_post', array( $this, 'on_page_save' ), 99, 2 );
        
        // 添加 AJAX 钩子用于手动填充模块
        add_action( 'wp_ajax_fill_resources_modules', array( $this, 'ajax_fill_modules' ) );
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

        // 只处理资源下载模板
        if ( $template !== 'templates/template-resources.php' ) {
            return;
        }

        // 检查是否已有模块配置
        $modules = get_post_meta( $post_id, '_developer_starter_modules', true );
        
        // 检查是否已标记为已填充（避免重复填充）
        $filled = get_post_meta( $post_id, '_resources_modules_filled', true );
        
        // 如果没有模块（空数组或空值）且尚未填充过，设置默认模块
        if ( ( empty( $modules ) || ! is_array( $modules ) || count( $modules ) === 0 ) && ! $filled ) {
            $this->set_default_modules( $post_id );
            // 标记为已填充，防止后续再次覆盖
            update_post_meta( $post_id, '_resources_modules_filled', '1' );
        }
    }

    /**
     * AJAX 手动填充模块
     */
    public function ajax_fill_modules() {
        check_ajax_referer( 'fill_resources_modules', 'nonce' );
        
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        
        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => '权限不足' ) );
        }
        
        $this->set_default_modules( $post_id );
        update_post_meta( $post_id, '_resources_modules_filled', '1' );
        
        wp_send_json_success( array( 'message' => '模块已填充，请刷新页面' ) );
    }

    /**
     * 设置资源下载页面的默认模块
     *
     * @param int $page_id 页面ID
     */
    public function set_default_modules( $page_id ) {
        // 获取页面标题用于动态内容
        $page_title = get_the_title( $page_id );
        if ( empty( $page_title ) ) {
            $page_title = '资源下载中心';
        }
        
        $default_modules = array(
            // 模块1：Banner - 资源下载页面顶部
            array(
                'type' => 'banner',
                'data' => array(
                    'banner_title'    => $page_title,
                    'banner_subtitle' => '获取我们的APP、软件工具和企业资料',
                    'banner_btn_text' => '立即下载',
                    'banner_btn_url'  => '#app-downloads',
                    'banner_btn2_text' => '查看文档',
                    'banner_btn2_url'  => '#documents',
                    'banner_bg_image' => '',
                    'banner_bg_color' => 'linear-gradient(135deg, #0f172a 0%, #1e40af 50%, #7c3aed 100%)',
                    'banner_height'   => '450',
                ),
            ),

            // 模块2：下载中心 - 移动端APP
            array(
                'type' => 'downloads',
                'data' => array(
                    'downloads_title'    => '📱 移动端 APP',
                    'downloads_subtitle' => '随时随地，便捷办公',
                    'downloads_columns'  => '2',
                    'downloads_items'    => array(
                        array(
                            'title'       => '企业移动APP (iOS)',
                            'size'        => '89.5 MB',
                            'file'        => '#',
                            'icon'        => '🍎',
                            'format'      => 'IPA',
                            'date'        => '2024-12-20',
                            'description' => '适用于 iPhone 和 iPad，需要 iOS 14.0 或更高版本',
                        ),
                        array(
                            'title'       => '企业移动APP (Android)',
                            'size'        => '76.2 MB',
                            'file'        => '#',
                            'icon'        => '🤖',
                            'format'      => 'APK',
                            'date'        => '2024-12-20',
                            'description' => '适用于 Android 8.0 及以上版本',
                        ),
                        array(
                            'title'       => '轻量版APP (iOS)',
                            'size'        => '45.8 MB',
                            'file'        => '#',
                            'icon'        => '📲',
                            'format'      => 'IPA',
                            'date'        => '2024-12-15',
                            'description' => '精简功能版本，占用空间更少',
                        ),
                        array(
                            'title'       => '轻量版APP (Android)',
                            'size'        => '38.6 MB',
                            'file'        => '#',
                            'icon'        => '📲',
                            'format'      => 'APK',
                            'date'        => '2024-12-15',
                            'description' => '适合存储空间有限的设备',
                        ),
                    ),
                ),
            ),

            // 模块3：下载中心 - 桌面软件
            array(
                'type' => 'downloads',
                'data' => array(
                    'downloads_title'    => '💻 桌面客户端',
                    'downloads_subtitle' => '功能强大的桌面办公软件',
                    'downloads_columns'  => '2',
                    'downloads_items'    => array(
                        array(
                            'title'       => '企业管理系统 (Windows)',
                            'size'        => '156.8 MB',
                            'file'        => '#',
                            'icon'        => '🪟',
                            'format'      => 'EXE',
                            'date'        => '2024-12-18',
                            'description' => '支持 Windows 10/11 64位系统',
                        ),
                        array(
                            'title'       => '企业管理系统 (macOS)',
                            'size'        => '142.3 MB',
                            'file'        => '#',
                            'icon'        => '🍏',
                            'format'      => 'DMG',
                            'date'        => '2024-12-18',
                            'description' => '支持 macOS 12.0 及以上版本，兼容 Apple Silicon',
                        ),
                        array(
                            'title'       => '数据同步工具',
                            'size'        => '28.5 MB',
                            'file'        => '#',
                            'icon'        => '🔄',
                            'format'      => 'EXE',
                            'date'        => '2024-12-10',
                            'description' => '本地数据与云端同步工具，支持断点续传',
                        ),
                        array(
                            'title'       => '报表生成器',
                            'size'        => '35.2 MB',
                            'file'        => '#',
                            'icon'        => '📊',
                            'format'      => 'EXE',
                            'date'        => '2024-12-08',
                            'description' => '快速生成各类业务报表，支持Excel/PDF导出',
                        ),
                    ),
                ),
            ),

            // 模块4：下载中心 - 企业文档
            array(
                'type' => 'downloads',
                'data' => array(
                    'downloads_title'    => '📚 企业资料与文档',
                    'downloads_subtitle' => '财务报告、技术文档与产品资料',
                    'downloads_columns'  => '3',
                    'downloads_items'    => array(
                        // 财务报告
                        array(
                            'title'       => '2024年度财务报告',
                            'size'        => '8.5 MB',
                            'file'        => '#',
                            'icon'        => '📈',
                            'format'      => 'PDF',
                            'date'        => '2024-12-28',
                            'description' => '公司年度财务报表及经营分析',
                        ),
                        array(
                            'title'       => '2024年Q3季度报告',
                            'size'        => '4.2 MB',
                            'file'        => '#',
                            'icon'        => '📊',
                            'format'      => 'PDF',
                            'date'        => '2024-10-15',
                            'description' => '第三季度财务数据与业务概览',
                        ),
                        array(
                            'title'       => '2024年Q2季度报告',
                            'size'        => '3.8 MB',
                            'file'        => '#',
                            'icon'        => '📊',
                            'format'      => 'PDF',
                            'date'        => '2024-07-12',
                            'description' => '第二季度财务数据与业务概览',
                        ),
                        // 技术文档
                        array(
                            'title'       => 'API接口文档',
                            'size'        => '2.1 MB',
                            'file'        => '#',
                            'icon'        => '🔧',
                            'format'      => 'PDF',
                            'date'        => '2024-12-01',
                            'description' => '开发者必备的API接口说明文档',
                        ),
                        array(
                            'title'       => '系统部署指南',
                            'size'        => '5.6 MB',
                            'file'        => '#',
                            'icon'        => '📖',
                            'format'      => 'PDF',
                            'date'        => '2024-11-20',
                            'description' => '私有化部署的详细安装配置指南',
                        ),
                        array(
                            'title'       => '技术白皮书',
                            'size'        => '3.2 MB',
                            'file'        => '#',
                            'icon'        => '📋',
                            'format'      => 'PDF',
                            'date'        => '2024-10-08',
                            'description' => '技术架构设计及安全说明',
                        ),
                        // 产品资料
                        array(
                            'title'       => '产品手册',
                            'size'        => '12.8 MB',
                            'file'        => '#',
                            'icon'        => '📘',
                            'format'      => 'PDF',
                            'date'        => '2024-11-15',
                            'description' => '全面的产品功能介绍与操作指南',
                        ),
                        array(
                            'title'       => '用户快速入门',
                            'size'        => '1.5 MB',
                            'file'        => '#',
                            'icon'        => '🚀',
                            'format'      => 'PDF',
                            'date'        => '2024-12-05',
                            'description' => '新用户快速上手指南',
                        ),
                        array(
                            'title'       => '企业宣传册',
                            'size'        => '18.6 MB',
                            'file'        => '#',
                            'icon'        => '🎨',
                            'format'      => 'PDF',
                            'date'        => '2024-09-20',
                            'description' => '公司介绍、产品服务及成功案例',
                        ),
                    ),
                ),
            ),

            // 模块5：FAQ - 下载相关常见问题
            array(
                'type' => 'faq',
                'data' => array(
                    'faq_title' => '下载常见问题',
                    'faq_items' => array(
                        array(
                            'question' => 'APP安装后无法打开怎么办？',
                            'answer'   => 'iOS用户请确保在"设置-通用-VPN与设备管理"中信任企业证书。Android用户请确保已开启"允许安装未知来源应用"选项。如仍有问题，请联系技术支持。',
                        ),
                        array(
                            'question' => '下载的文件是否安全？',
                            'answer'   => '所有下载文件均经过严格安全检测，使用HTTPS加密传输。软件安装包均有数字签名，请放心下载使用。如发现可疑链接，请及时联系我们。',
                        ),
                        array(
                            'question' => '如何获取历史版本的软件？',
                            'answer'   => '本页面仅提供最新稳定版本的下载。如需历史版本，请联系客服或技术支持团队，我们将根据您的需求提供相应版本。',
                        ),
                        array(
                            'question' => '企业批量部署如何获取授权？',
                            'answer'   => '企业批量部署需要申请企业授权许可。请联系我们的销售团队，提供企业信息和部署规模，我们将为您提供定制化的授权方案。',
                        ),
                        array(
                            'question' => '财务报告和技术文档需要权限才能下载吗？',
                            'answer'   => '部分内部文档可能需要登录企业账号才能下载。公开的财务报告和产品手册无需登录即可免费下载。',
                        ),
                    ),
                ),
            ),

            // 模块6：CTA行动召唤
            array(
                'type' => 'cta',
                'data' => array(
                    'cta_title'    => '找不到需要的资源？',
                    'cta_subtitle' => '联系我们获取更多资料，或申请定制化解决方案',
                    'cta_btn_text' => '联系我们',
                    'cta_btn_url'  => '/contact/',
                    'cta_bg_color' => 'linear-gradient(135deg, #1e40af 0%, #7c3aed 100%)',
                ),
            ),
        );

        update_post_meta( $page_id, '_developer_starter_modules', $default_modules );
    }
}
