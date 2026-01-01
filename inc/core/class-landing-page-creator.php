<?php
/**
 * 落地页创建器类
 *
 * 当用户选择"Landing Page"模板创建页面时，自动填充预设模块内容
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
 * 落地页创建器类
 */
class Landing_Page_Creator {

    /**
     * 构造函数
     */
    public function __construct() {
        // 使用更高优先级确保在 meta-boxes 保存之后执行
        add_action( 'save_post', array( $this, 'on_page_save' ), 99, 2 );
        
        // 添加 AJAX 钩子用于手动填充模块
        add_action( 'wp_ajax_fill_landing_modules', array( $this, 'ajax_fill_modules' ) );
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

        // 只处理落地页模板
        if ( $template !== 'templates/template-landing.php' ) {
            return;
        }

        // 检查是否已有模块配置
        $modules = get_post_meta( $post_id, '_developer_starter_modules', true );
        
        // 检查是否已标记为已填充（避免重复填充）
        $filled = get_post_meta( $post_id, '_landing_modules_filled', true );
        
        // 如果没有模块（空数组或空值）且尚未填充过，设置默认模块
        if ( ( empty( $modules ) || ! is_array( $modules ) || count( $modules ) === 0 ) && ! $filled ) {
            $this->set_default_modules( $post_id );
            // 标记为已填充，防止后续再次覆盖
            update_post_meta( $post_id, '_landing_modules_filled', '1' );
        }
    }

    /**
     * AJAX 手动填充模块
     */
    public function ajax_fill_modules() {
        check_ajax_referer( 'fill_landing_modules', 'nonce' );
        
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        
        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => '权限不足' ) );
        }
        
        $this->set_default_modules( $post_id );
        update_post_meta( $post_id, '_landing_modules_filled', '1' );
        
        wp_send_json_success( array( 'message' => '模块已填充，请刷新页面' ) );
    }

    /**
     * 设置落地页的默认模块
     *
     * @param int $page_id 页面ID
     */
    public function set_default_modules( $page_id ) {
        // 获取页面标题用于动态内容
        $page_title = get_the_title( $page_id );
        if ( empty( $page_title ) ) {
            $page_title = '专业解决方案';
        }
        
        $default_modules = array(
            // 模块1：Banner - 引人注目的首屏
            array(
                'type' => 'banner',
                'data' => array(
                    'banner_title'    => $page_title,
                    'banner_subtitle' => '专业团队 · 定制方案 · 快速交付 · 全程服务',
                    'banner_btn_text' => '立即咨询',
                    'banner_btn_url'  => '#contact-form',
                    'banner_btn2_text' => '了解更多',
                    'banner_btn2_url'  => '#features',
                    'banner_bg_image' => '',
                    'banner_bg_color' => 'linear-gradient(135deg, #1e3a8a 0%, #7c3aed 50%, #ec4899 100%)',
                    'banner_height'   => '600',
                ),
            ),

            // 模块2：统计数据 - 建立信任
            array(
                'type' => 'stats',
                'data' => array(
                    'stats_title'    => '',
                    'stats_subtitle' => '',
                    'stats_bg_color' => '#ffffff',
                    'stats_items'    => array(
                        array(
                            'number' => '10+',
                            'label'  => '年行业经验',
                            'icon'   => '🏆',
                        ),
                        array(
                            'number' => '500+',
                            'label'  => '成功案例',
                            'icon'   => '📈',
                        ),
                        array(
                            'number' => '98%',
                            'label'  => '客户满意度',
                            'icon'   => '⭐',
                        ),
                        array(
                            'number' => '24h',
                            'label'  => '响应时间',
                            'icon'   => '⚡',
                        ),
                    ),
                ),
            ),

            // 模块3：服务展示 - 核心优势
            array(
                'type' => 'services',
                'data' => array(
                    'services_title'    => '我们的核心优势',
                    'services_subtitle' => '选择我们，就是选择专业与可靠',
                    'services_bg_color' => '#f8fafc',
                    'services_items'    => array(
                        array(
                            'icon'  => '🎯',
                            'title' => '精准定位',
                            'desc'  => '深入了解您的需求，提供量身定制的解决方案，确保每一分投入都能产生最大价值',
                        ),
                        array(
                            'icon'  => '⚡',
                            'title' => '高效执行',
                            'desc'  => '专业团队协同作业，标准化流程管理，确保项目按时高质量交付',
                        ),
                        array(
                            'icon'  => '🛡️',
                            'title' => '安全可靠',
                            'desc'  => '采用业界领先的安全技术和标准，全方位保障您的数据和业务安全',
                        ),
                        array(
                            'icon'  => '🤝',
                            'title' => '全程陪伴',
                            'desc'  => '从咨询到售后，专属客户经理一对一服务，让您全程无忧',
                        ),
                    ),
                ),
            ),

            // 模块4：图文模块 - 产品/方案介绍
            array(
                'type' => 'image_text',
                'data' => array(
                    'image_text_layout'  => 'left',
                    'image_text_title'   => '为什么选择我们？',
                    'image_text_content' => '<p>我们不只是服务提供商，更是您的长期战略合作伙伴。凭借多年的行业积累和持续创新，我们已帮助数百家企业实现业务突破。</p><ul><li>✓ <strong>专业团队</strong> - 100+ 资深专家，平均从业经验 8 年以上</li><li>✓ <strong>技术领先</strong> - 持续研发投入，保持技术和方案的先进性</li><li>✓ <strong>服务保障</strong> - 7×24 小时技术支持，快速响应需求</li><li>✓ <strong>效果导向</strong> - 以客户成功为目标，用数据说话</li></ul>',
                    'image_text_button'  => '查看案例',
                    'image_text_url'     => '#cases',
                    'image_text_image'   => '',
                ),
            ),

            // 模块5：流程展示 - 合作步骤
            array(
                'type' => 'process',
                'data' => array(
                    'process_title'    => '合作流程',
                    'process_subtitle' => '简单四步，开启您的成功之旅',
                    'process_bg_color' => '#ffffff',
                    'process_items'    => array(
                        array(
                            'icon'    => '📞',
                            'title'   => '免费咨询',
                            'desc'    => '联系我们，详细沟通您的需求和目标',
                            'icon_bg' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        ),
                        array(
                            'icon'    => '📋',
                            'title'   => '方案制定',
                            'desc'    => '根据需求分析，制定专属解决方案',
                            'icon_bg' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                        ),
                        array(
                            'icon'    => '🚀',
                            'title'   => '快速落地',
                            'desc'    => '专业团队高效执行，按时高质交付',
                            'icon_bg' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                        ),
                        array(
                            'icon'    => '📈',
                            'title'   => '持续优化',
                            'desc'    => '定期复盘，持续优化，确保长期成功',
                            'icon_bg' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                        ),
                    ),
                ),
            ),

            // 模块6：客户评价
            array(
                'type' => 'testimonials',
                'data' => array(
                    'testimonials_title'    => '客户好评',
                    'testimonials_subtitle' => '听听他们怎么说',
                    'testimonials_bg_color' => '#f8fafc',
                    'testimonials_items'    => array(
                        array(
                            'content' => '与他们合作是我们做过最正确的决定之一。专业的团队、高效的执行、贴心的服务，让我们的项目顺利上线并取得了超预期的效果。',
                            'author'  => '张总',
                            'company' => '某科技公司 CEO',
                            'avatar'  => '',
                        ),
                        array(
                            'content' => '他们不仅提供了优质的产品，更重要的是真正理解我们的业务需求。在整个合作过程中，沟通顺畅，响应及时，非常专业。',
                            'author'  => '李经理',
                            'company' => '某制造企业 IT总监',
                            'avatar'  => '',
                        ),
                        array(
                            'content' => '从前期咨询到后期维护，每一个环节都让人放心。特别是售后服务，有问题随时响应，真正做到了客户至上。',
                            'author'  => '王女士',
                            'company' => '某电商平台 运营总监',
                            'avatar'  => '',
                        ),
                    ),
                ),
            ),

            // 模块7：企业优势/特点
            array(
                'type' => 'features',
                'data' => array(
                    'features_title'    => '我们的承诺',
                    'features_subtitle' => '用专业和诚信赢得您的信任',
                    'features_bg_color' => '#ffffff',
                    'features_items'    => array(
                        array(
                            'icon'  => '💯',
                            'title' => '品质保证',
                            'desc'  => '严格的质量管控体系，确保交付物达到最高标准',
                        ),
                        array(
                            'icon'  => '💰',
                            'title' => '透明报价',
                            'desc'  => '明确的价格体系，无隐藏费用，物超所值',
                        ),
                        array(
                            'icon'  => '🔒',
                            'title' => '保密协议',
                            'desc'  => '严格的保密措施，全方位保护您的商业机密',
                        ),
                        array(
                            'icon'  => '🎁',
                            'title' => '增值服务',
                            'desc'  => '免费提供培训、文档等增值服务，助您快速上手',
                        ),
                    ),
                ),
            ),

            // 模块8：联系表单 CTA
            array(
                'type' => 'cta',
                'data' => array(
                    'cta_title'    => '立即行动，抢占先机',
                    'cta_subtitle' => '填写表单，我们的专家顾问将在 24 小时内与您联系，为您提供免费咨询服务',
                    'cta_btn_text' => '立即咨询',
                    'cta_btn_url'  => '/contact/',
                    'cta_bg_color' => 'linear-gradient(135deg, #1e3a8a 0%, #7c3aed 100%)',
                ),
            ),
        );

        update_post_meta( $page_id, '_developer_starter_modules', $default_modules );
    }
}
