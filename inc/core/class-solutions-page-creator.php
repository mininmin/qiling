<?php
/**
 * 解决方案页面创建器类
 *
 * 当用户选择"解决方案"模板创建页面时，自动填充预设模块内容
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
 * 解决方案页面创建器类
 */
class Solutions_Page_Creator {

    /**
     * 构造函数
     */
    public function __construct() {
        // 使用更高优先级确保在 meta-boxes 保存之后执行
        add_action( 'save_post', array( $this, 'on_page_save' ), 99, 2 );
        
        // 添加 AJAX 钩子用于手动填充模块
        add_action( 'wp_ajax_fill_solutions_modules', array( $this, 'ajax_fill_modules' ) );
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

        // 只处理解决方案模板
        if ( $template !== 'templates/template-solutions.php' ) {
            return;
        }

        // 检查是否已有模块配置
        $modules = get_post_meta( $post_id, '_developer_starter_modules', true );
        
        // 检查是否已标记为已填充（避免重复填充）
        $filled = get_post_meta( $post_id, '_solutions_modules_filled', true );
        
        // 如果没有模块（空数组或空值）且尚未填充过，设置默认模块
        if ( ( empty( $modules ) || ! is_array( $modules ) || count( $modules ) === 0 ) && ! $filled ) {
            $this->set_default_modules( $post_id );
            // 标记为已填充，防止后续再次覆盖
            update_post_meta( $post_id, '_solutions_modules_filled', '1' );
        }
    }

    /**
     * AJAX 手动填充模块
     */
    public function ajax_fill_modules() {
        check_ajax_referer( 'fill_solutions_modules', 'nonce' );
        
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        
        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => '权限不足' ) );
        }
        
        $this->set_default_modules( $post_id );
        update_post_meta( $post_id, '_solutions_modules_filled', '1' );
        
        wp_send_json_success( array( 'message' => '模块已填充，请刷新页面' ) );
    }

    /**
     * 设置解决方案页面的默认模块
     *
     * @param int $page_id 页面ID
     */
    public function set_default_modules( $page_id ) {
        $default_modules = array(
            // 模块1：图文模块 - 解决方案概述（左图右文）
            array(
                'type' => 'image_text',
                'data' => array(
                    'image_text_layout'  => 'left',
                    'image_text_title'   => '全方位解决方案',
                    'image_text_content' => '<p>我们提供专业、高效、定制化的解决方案，帮助企业应对数字化转型过程中的各种挑战。</p><p>基于多年行业经验和技术积累，我们的解决方案已成功服务于数百家企业，涵盖制造、金融、医疗、教育等多个领域。</p><ul><li>✓ 深度定制，贴合业务需求</li><li>✓ 快速部署，降低实施风险</li><li>✓ 持续迭代，保持技术领先</li></ul>',
                    'image_text_button'  => '了解详情',
                    'image_text_url'     => '#features',
                    'image_text_image'   => '',
                ),
            ),

            // 模块2：多列布局 - 核心能力（3列）
            array(
                'type' => 'columns',
                'data' => array(
                    'columns_count' => '3',
                    'columns_items' => array(
                        array(
                            'title'   => '🔍 智能分析',
                            'content' => '<p>利用AI和大数据技术，对业务数据进行深度分析，挖掘潜在价值，为决策提供数据支撑。</p><ul><li>实时数据监控</li><li>智能预警系统</li><li>可视化报表</li></ul>',
                            'image'   => '',
                        ),
                        array(
                            'title'   => '⚡ 高效协同',
                            'content' => '<p>打通部门壁垒，实现信息共享和流程协同，大幅提升团队协作效率和项目交付速度。</p><ul><li>跨部门协作</li><li>流程自动化</li><li>移动办公支持</li></ul>',
                            'image'   => '',
                        ),
                        array(
                            'title'   => '🛡️ 安全可靠',
                            'content' => '<p>采用企业级安全架构，多层防护机制确保数据安全，让您的业务运行无后顾之忧。</p><ul><li>数据加密存储</li><li>权限精细管理</li><li>灾备恢复机制</li></ul>',
                            'image'   => '',
                        ),
                    ),
                ),
            ),

            // 模块3：合作流程 - 实施步骤
            array(
                'type' => 'process',
                'data' => array(
                    'process_title'    => '解决方案实施流程',
                    'process_subtitle' => '科学规范的实施流程，确保项目顺利落地',
                    'process_bg_color' => '#f8fafc',
                    'process_items'    => array(
                        array(
                            'icon'    => '📋',
                            'title'   => '需求调研',
                            'desc'    => '深入了解企业现状和痛点，明确项目目标和范围',
                            'icon_bg' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        ),
                        array(
                            'icon'    => '📐',
                            'title'   => '方案设计',
                            'desc'    => '根据调研结果，定制专属解决方案和实施计划',
                            'icon_bg' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                        ),
                        array(
                            'icon'    => '🔧',
                            'title'   => '开发部署',
                            'desc'    => '敏捷开发模式，快速迭代，分阶段交付成果',
                            'icon_bg' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                        ),
                        array(
                            'icon'    => '🚀',
                            'title'   => '培训上线',
                            'desc'    => '完善的培训体系和上线支持，确保平稳过渡',
                            'icon_bg' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                        ),
                    ),
                ),
            ),

            // 模块4：图文模块 - 技术优势（右图左文）
            array(
                'type' => 'image_text',
                'data' => array(
                    'image_text_layout'  => 'right',
                    'image_text_title'   => '领先的技术架构',
                    'image_text_content' => '<p>采用微服务架构设计，具备高可用、高并发、易扩展的特性，支持私有化部署和混合云方案。</p><p><strong>技术亮点：</strong></p><ul><li>🔹 云原生架构，弹性伸缩</li><li>🔹 微服务设计，模块解耦</li><li>🔹 容器化部署，运维简便</li><li>🔹 多租户支持，资源隔离</li><li>🔹 开放API接口，无缝集成</li></ul>',
                    'image_text_button'  => '技术白皮书',
                    'image_text_url'     => '#',
                    'image_text_image'   => '',
                ),
            ),

            // 模块5：企业优势 - 为什么选择我们
            array(
                'type' => 'features',
                'data' => array(
                    'features_title'    => '为什么选择我们的解决方案',
                    'features_subtitle' => '专业团队 + 成熟产品 + 优质服务 = 成功保障',
                    'features_items'    => array(
                        array(
                            'icon'  => '🏆',
                            'title' => '行业领先',
                            'desc'  => '10+年行业深耕，服务500+企业客户',
                        ),
                        array(
                            'icon'  => '👥',
                            'title' => '专业团队',
                            'desc'  => '200+技术专家，提供全程专业支持',
                        ),
                        array(
                            'icon'  => '📈',
                            'title' => '效果显著',
                            'desc'  => '平均提升30%运营效率，降低20%成本',
                        ),
                        array(
                            'icon'  => '🤝',
                            'title' => '贴心服务',
                            'desc'  => '7×24小时响应，专属客户成功经理',
                        ),
                    ),
                ),
            ),

            // 模块6：多列布局 - 成功案例预览（带图片）
            array(
                'type' => 'columns',
                'data' => array(
                    'columns_count' => '3',
                    'columns_items' => array(
                        array(
                            'title'   => '制造业数字化转型',
                            'content' => '<p style="color: #64748b; font-size: 0.9rem;">帮助某大型制造企业实现生产全流程数字化管理，生产效率提升35%，库存周转率提高28%。</p><a href="#" style="color: var(--color-primary); font-weight: 500;">查看详情 →</a>',
                            'image'   => '',
                        ),
                        array(
                            'title'   => '金融风控系统升级',
                            'content' => '<p style="color: #64748b; font-size: 0.9rem;">为某银行打造智能风控平台，实现风险识别准确率98%，审批效率提升50%。</p><a href="#" style="color: var(--color-primary); font-weight: 500;">查看详情 →</a>',
                            'image'   => '',
                        ),
                        array(
                            'title'   => '医疗信息化建设',
                            'content' => '<p style="color: #64748b; font-size: 0.9rem;">助力某三甲医院建设智慧医疗系统，患者等待时间减少60%，医疗差错率降低75%。</p><a href="#" style="color: var(--color-primary); font-weight: 500;">查看详情 →</a>',
                            'image'   => '',
                        ),
                    ),
                ),
            ),

            // 模块7：CTA行动召唤
            array(
                'type' => 'cta',
                'data' => array(
                    'cta_title'    => '开启您的数字化转型之旅',
                    'cta_subtitle' => '立即联系我们，获取免费需求评估和专属解决方案',
                    'cta_btn_text' => '预约咨询',
                    'cta_btn_url'  => '/contact/',
                    'cta_bg_color' => 'linear-gradient(135deg, #2563eb 0%, #7c3aed 100%)',
                ),
            ),
        );

        update_post_meta( $page_id, '_developer_starter_modules', $default_modules );
    }
}

