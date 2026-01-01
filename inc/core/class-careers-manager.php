<?php
/**
 * Careers Manager Class - 招聘管理系统
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Careers_Manager {

    private $positions_table;
    private $applications_table;
    private $option_name = 'developer_starter_careers_options';

    public function __construct() {
        global $wpdb;
        $this->positions_table = $wpdb->prefix . 'ds_careers_positions';
        $this->applications_table = $wpdb->prefix . 'ds_careers_applications';
        
        // 数据表创建
        add_action( 'after_switch_theme', array( $this, 'create_tables' ) );
        add_action( 'init', array( $this, 'maybe_create_tables' ) );
        
        // 后台菜单
        add_action( 'admin_menu', array( $this, 'add_admin_menus' ), 25 );
        
        // 注册设置
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // AJAX处理
        add_action( 'wp_ajax_ds_careers_save_position', array( $this, 'ajax_save_position' ) );
        add_action( 'wp_ajax_ds_careers_delete_position', array( $this, 'ajax_delete_position' ) );
        add_action( 'wp_ajax_ds_submit_careers_application', array( $this, 'handle_application_submit' ) );
        add_action( 'wp_ajax_nopriv_ds_submit_careers_application', array( $this, 'handle_application_submit' ) );
        
        // 加载后台脚本
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    /**
     * 检查并创建数据表
     */
    public function maybe_create_tables() {
        global $wpdb;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$this->positions_table}'" ) !== $this->positions_table ) {
            $this->create_tables();
        }
    }

    /**
     * 创建数据表
     */
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // 职位表
        $sql_positions = "CREATE TABLE IF NOT EXISTS {$this->positions_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(200) NOT NULL,
            department VARCHAR(100) DEFAULT '',
            location VARCHAR(100) DEFAULT '',
            job_type VARCHAR(50) DEFAULT 'fulltime',
            salary VARCHAR(50) DEFAULT '',
            category VARCHAR(50) DEFAULT '',
            description TEXT,
            requirements TEXT,
            sort_order INT DEFAULT 0,
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY category (category),
            KEY sort_order (sort_order)
        ) $charset_collate;";
        
        // 求职申请表
        $sql_applications = "CREATE TABLE IF NOT EXISTS {$this->applications_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            phone VARCHAR(50) DEFAULT '',
            email VARCHAR(100) DEFAULT '',
            position_id BIGINT(20) UNSIGNED DEFAULT 0,
            position_title VARCHAR(200) DEFAULT '',
            message TEXT,
            ip_address VARCHAR(45) DEFAULT '',
            user_agent VARCHAR(255) DEFAULT '',
            is_read TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY position_id (position_id),
            KEY is_read (is_read),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_positions );
        dbDelta( $sql_applications );
        
        // 插入默认职位数据
        $this->insert_default_positions();
    }

    /**
     * 插入默认职位数据
     */
    private function insert_default_positions() {
        global $wpdb;
        
        // 检查是否已有数据
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->positions_table}" );
        if ( $count > 0 ) {
            return;
        }
        
        $default_positions = array(
            array(
                'title' => '高级PHP开发工程师',
                'department' => '技术部',
                'location' => '北京',
                'job_type' => 'fulltime',
                'salary' => '15-25K',
                'category' => 'tech',
                'description' => "负责公司核心业务系统的设计和开发\n参与技术架构设计，保证系统高可用性和扩展性\n编写技术文档，进行代码审查\n指导初级开发人员，参与技术分享",
                'requirements' => "本科及以上学历，计算机相关专业\n5年以上PHP开发经验，熟悉Laravel/ThinkPHP框架\n熟悉MySQL、Redis等数据库，具备性能优化经验\n良好的沟通能力和团队协作精神",
                'sort_order' => 1,
            ),
            array(
                'title' => 'UI/UX设计师',
                'department' => '产品部',
                'location' => '上海',
                'job_type' => 'fulltime',
                'salary' => '12-20K',
                'category' => 'product',
                'description' => "负责公司产品的UI/UX设计工作\n制定设计规范，维护设计系统\n与产品、开发团队紧密协作\n跟踪国际设计趋势，持续优化用户体验",
                'requirements' => "设计相关专业本科及以上学历\n3年以上UI/UX设计经验，有B端产品设计经验优先\n精通Figma、Sketch等设计工具\n具备良好的审美和设计感",
                'sort_order' => 2,
            ),
            array(
                'title' => '新媒体运营专员',
                'department' => '市场部',
                'location' => '深圳',
                'job_type' => 'fulltime',
                'salary' => '8-15K',
                'category' => 'market',
                'description' => "负责公司新媒体账号的日常运营\n策划并执行内容营销活动\n分析运营数据，优化运营策略\n关注行业动态，挖掘热点话题",
                'requirements' => "本科及以上学历，市场营销、新闻传播相关专业\n2年以上新媒体运营经验\n优秀的文案撰写能力和创意策划能力\n熟悉微信、微博、抖音等主流平台规则",
                'sort_order' => 3,
            ),
            array(
                'title' => '人力资源主管',
                'department' => '行政部',
                'location' => '北京',
                'job_type' => 'fulltime',
                'salary' => '12-18K',
                'category' => 'admin',
                'description' => "负责公司招聘工作的全流程管理\n维护和拓展招聘渠道\n参与人力资源政策制定和执行\n负责员工关系管理和企业文化建设",
                'requirements' => "本科及以上学历，人力资源管理相关专业\n3年以上人力资源工作经验\n熟悉劳动法律法规\n优秀的沟通协调能力和抗压能力",
                'sort_order' => 4,
            ),
        );
        
        foreach ( $default_positions as $position ) {
            $wpdb->insert( $this->positions_table, $position );
        }
        
        // 设置默认选项
        $default_options = array(
            'hero_title' => '加入我们',
            'hero_subtitle' => '与优秀的团队一起，创造无限可能。我们期待有才华的你加入！',
            'stat_1_number' => '50+',
            'stat_1_label' => '团队成员',
            'stat_2_number' => '10+',
            'stat_2_label' => '开放职位',
            'stat_3_number' => '5个',
            'stat_3_label' => '办公城市',
            'benefits' => array(
                array( 'icon' => 'money', 'title' => '有竞争力的薪资', 'desc' => '行业领先的薪酬体系，绩效奖金、年终奖金、项目分红' ),
                array( 'icon' => 'shield', 'title' => '五险一金', 'desc' => '足额缴纳五险一金，额外补充商业医疗保险' ),
                array( 'icon' => 'book', 'title' => '培训发展', 'desc' => '完善的培训体系，行业大会、技术分享、读书基金' ),
                array( 'icon' => 'calendar', 'title' => '带薪年假', 'desc' => '入职即享带薪年假，额外享有生日假、婚假等福利假期' ),
                array( 'icon' => 'users', 'title' => '团队活动', 'desc' => '定期团建活动，下午茶、生日会、年度旅游' ),
                array( 'icon' => 'trending', 'title' => '晋升通道', 'desc' => '透明的晋升机制，技术线与管理线双通道发展' ),
            ),
            'hr_phone' => '',
            'hr_email' => '',
            'enable_application' => '1',
        );
        
        update_option( $this->option_name, $default_options );
    }

    /**
     * 注册设置
     */
    public function register_settings() {
        register_setting( 'developer_starter_careers_settings', $this->option_name, array(
            'sanitize_callback' => array( $this, 'sanitize_options' ),
        ) );
    }

    /**
     * 清理选项
     */
    public function sanitize_options( $input ) {
        if ( ! is_array( $input ) ) {
            return array();
        }
        
        $sanitized = array();
        
        // 文本字段
        $text_fields = array( 'hero_title', 'hero_subtitle', 'hero_bg_color', 'stat_1_number', 'stat_1_label', 
                             'stat_2_number', 'stat_2_label', 'stat_3_number', 'stat_3_label',
                             'hr_phone', 'hr_email' );
        foreach ( $text_fields as $field ) {
            $sanitized[ $field ] = isset( $input[ $field ] ) ? sanitize_text_field( $input[ $field ] ) : '';
        }
        
        // 复选框
        $sanitized['enable_application'] = isset( $input['enable_application'] ) ? '1' : '';
        
        // 福利数组
        if ( isset( $input['benefits'] ) && is_array( $input['benefits'] ) ) {
            $sanitized['benefits'] = array();
            foreach ( $input['benefits'] as $benefit ) {
                if ( ! empty( $benefit['title'] ) ) {
                    $sanitized['benefits'][] = array(
                        'icon' => sanitize_text_field( $benefit['icon'] ?? '' ),
                        'title' => sanitize_text_field( $benefit['title'] ?? '' ),
                        'desc' => sanitize_text_field( $benefit['desc'] ?? '' ),
                    );
                }
            }
        }
        
        return $sanitized;
    }

    /**
     * 加载后台脚本
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( strpos( $hook, 'careers' ) === false ) {
            return;
        }
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }

    /**
     * 添加后台菜单
     */
    public function add_admin_menus() {
        // 招聘设置
        add_submenu_page(
            'developer-starter-settings',
            '招聘设置',
            '招聘设置',
            'manage_options',
            'developer-starter-careers-settings',
            array( $this, 'render_settings_page' )
        );
        
        // 职位管理
        add_submenu_page(
            'developer-starter-settings',
            '职位管理',
            '职位管理',
            'manage_options',
            'developer-starter-careers-positions',
            array( $this, 'render_positions_page' )
        );
        
        // 求职申请
        $unread_count = $this->get_unread_applications_count();
        $menu_title = '求职申请';
        if ( $unread_count > 0 ) {
            $menu_title .= ' <span class="awaiting-mod count-' . $unread_count . '"><span class="pending-count">' . $unread_count . '</span></span>';
        }
        
        add_submenu_page(
            'developer-starter-settings',
            '求职申请',
            $menu_title,
            'manage_options',
            'developer-starter-careers-applications',
            array( $this, 'render_applications_page' )
        );
    }

    /**
     * 获取未读申请数量
     */
    private function get_unread_applications_count() {
        global $wpdb;
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->applications_table} WHERE is_read = 0" );
    }

    /**
     * 获取招聘设置
     */
    public static function get_option( $key = null, $default = '' ) {
        $options = get_option( 'developer_starter_careers_options', array() );
        if ( $key === null ) {
            return $options;
        }
        return isset( $options[ $key ] ) ? $options[ $key ] : $default;
    }

    /**
     * 获取所有启用的职位
     */
    public static function get_positions( $category = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ds_careers_positions';
        
        $sql = "SELECT * FROM {$table} WHERE status = 1";
        if ( ! empty( $category ) && $category !== 'all' ) {
            $sql .= $wpdb->prepare( " AND category = %s", $category );
        }
        $sql .= " ORDER BY sort_order ASC, id DESC";
        
        return $wpdb->get_results( $sql );
    }

    /**
     * 获取单个职位
     */
    public static function get_position( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ds_careers_positions';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ) );
    }

    // ==================== 渲染页面 ====================

    /**
     * 渲染招聘设置页面
     */
    public function render_settings_page() {
        $options = get_option( $this->option_name, array() );
        ?>
        <div class="wrap">
            <h1>招聘页面设置</h1>
            
            <form method="post" action="options.php">
                <?php settings_fields( 'developer_starter_careers_settings' ); ?>
                
                <h2 class="title">页面头部设置</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="hero_title">Hero 标题</label></th>
                        <td>
                            <input type="text" id="hero_title" name="<?php echo $this->option_name; ?>[hero_title]" 
                                   value="<?php echo esc_attr( $options['hero_title'] ?? '加入我们' ); ?>" class="regular-text" />
                            <p class="description">招聘页面的主标题（如：招聘精英/加入我们）</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="hero_subtitle">Hero 副标题</label></th>
                        <td>
                            <textarea id="hero_subtitle" name="<?php echo $this->option_name; ?>[hero_subtitle]" 
                                      rows="2" class="large-text"><?php echo esc_textarea( $options['hero_subtitle'] ?? '' ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="hero_bg_color">Hero 背景颜色</label></th>
                        <td>
                            <input type="text" id="hero_bg_color" name="<?php echo $this->option_name; ?>[hero_bg_color]" 
                                   value="<?php echo esc_attr( $options['hero_bg_color'] ?? '' ); ?>" class="regular-text" 
                                   placeholder="如: linear-gradient(135deg, #2563eb 0%, #0891b2 50%, #10b981 100%)" />
                            <p class="description">支持渐变色，留空使用默认渐变（蓝→青→绿）</p>
                        </td>
                    </tr>
                </table>
                
                <h2 class="title">统计数据</h2>
                <table class="form-table">
                    <tr>
                        <th>统计项 1</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_name; ?>[stat_1_number]" 
                                   value="<?php echo esc_attr( $options['stat_1_number'] ?? '50+' ); ?>" 
                                   placeholder="数字" style="width: 100px;" />
                            <input type="text" name="<?php echo $this->option_name; ?>[stat_1_label]" 
                                   value="<?php echo esc_attr( $options['stat_1_label'] ?? '团队成员' ); ?>" 
                                   placeholder="标签" style="width: 150px;" />
                        </td>
                    </tr>
                    <tr>
                        <th>统计项 2</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_name; ?>[stat_2_number]" 
                                   value="<?php echo esc_attr( $options['stat_2_number'] ?? '10+' ); ?>" 
                                   placeholder="数字" style="width: 100px;" />
                            <input type="text" name="<?php echo $this->option_name; ?>[stat_2_label]" 
                                   value="<?php echo esc_attr( $options['stat_2_label'] ?? '开放职位' ); ?>" 
                                   placeholder="标签" style="width: 150px;" />
                        </td>
                    </tr>
                    <tr>
                        <th>统计项 3</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_name; ?>[stat_3_number]" 
                                   value="<?php echo esc_attr( $options['stat_3_number'] ?? '5个' ); ?>" 
                                   placeholder="数字" style="width: 100px;" />
                            <input type="text" name="<?php echo $this->option_name; ?>[stat_3_label]" 
                                   value="<?php echo esc_attr( $options['stat_3_label'] ?? '办公城市' ); ?>" 
                                   placeholder="标签" style="width: 150px;" />
                        </td>
                    </tr>
                </table>
                
                <h2 class="title">公司福利</h2>
                <div id="benefits-container" style="margin-bottom: 20px;">
                    <?php 
                    $benefits = isset( $options['benefits'] ) && is_array( $options['benefits'] ) ? $options['benefits'] : array();
                    $icon_options = array(
                        'money' => '💰 薪资',
                        'shield' => '🛡️ 保险',
                        'book' => '📚 培训',
                        'calendar' => '📅 假期',
                        'users' => '👥 团队',
                        'trending' => '📈 晋升',
                        'heart' => '❤️ 关怀',
                        'star' => '⭐ 福利',
                    );
                    foreach ( $benefits as $idx => $benefit ) : ?>
                        <div class="benefit-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd; position: relative;">
                            <a href="#" class="remove-benefit" style="position: absolute; top: 5px; right: 10px; color: #a00; text-decoration: none;">删除</a>
                            <p style="margin: 0 0 10px;">
                                <label><strong>图标</strong></label><br>
                                <select name="<?php echo $this->option_name; ?>[benefits][<?php echo $idx; ?>][icon]" style="width: 150px;">
                                    <?php foreach ( $icon_options as $val => $label ) : ?>
                                        <option value="<?php echo $val; ?>" <?php selected( $benefit['icon'] ?? '', $val ); ?>><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <p style="margin: 0 0 10px;">
                                <label><strong>标题</strong></label><br>
                                <input type="text" name="<?php echo $this->option_name; ?>[benefits][<?php echo $idx; ?>][title]" 
                                       value="<?php echo esc_attr( $benefit['title'] ?? '' ); ?>" style="width: 100%;" />
                            </p>
                            <p style="margin: 0;">
                                <label><strong>描述</strong></label><br>
                                <input type="text" name="<?php echo $this->option_name; ?>[benefits][<?php echo $idx; ?>][desc]" 
                                       value="<?php echo esc_attr( $benefit['desc'] ?? '' ); ?>" style="width: 100%;" />
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-benefit" class="button">+ 添加福利项</button>
                
                <h2 class="title" style="margin-top: 30px;">HR 联系方式</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="hr_phone">HR 电话</label></th>
                        <td>
                            <input type="text" id="hr_phone" name="<?php echo $this->option_name; ?>[hr_phone]" 
                                   value="<?php echo esc_attr( $options['hr_phone'] ?? '' ); ?>" class="regular-text" />
                            <p class="description">留空则使用主题设置中的公司电话</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="hr_email">HR 邮箱</label></th>
                        <td>
                            <input type="email" id="hr_email" name="<?php echo $this->option_name; ?>[hr_email]" 
                                   value="<?php echo esc_attr( $options['hr_email'] ?? '' ); ?>" class="regular-text" />
                            <p class="description">留空则使用主题设置中的公司邮箱</p>
                        </td>
                    </tr>
                </table>
                
                <h2 class="title">功能开关</h2>
                <table class="form-table">
                    <tr>
                        <th>在线申请</th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo $this->option_name; ?>[enable_application]" value="1" 
                                       <?php checked( $options['enable_application'] ?? '1', '1' ); ?> />
                                启用在线申请功能
                            </label>
                            <p class="description">关闭后，招聘页面将不显示申请表单</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button( '保存设置' ); ?>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            var benefitIndex = <?php echo count( $benefits ); ?>;
            
            $('#add-benefit').on('click', function() {
                var html = '<div class="benefit-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd; position: relative;">' +
                    '<a href="#" class="remove-benefit" style="position: absolute; top: 5px; right: 10px; color: #a00; text-decoration: none;">删除</a>' +
                    '<p style="margin: 0 0 10px;"><label><strong>图标</strong></label><br>' +
                    '<select name="<?php echo $this->option_name; ?>[benefits][' + benefitIndex + '][icon]" style="width: 150px;">' +
                    '<?php foreach ( $icon_options as $val => $label ) : ?><option value="<?php echo $val; ?>"><?php echo $label; ?></option><?php endforeach; ?>' +
                    '</select></p>' +
                    '<p style="margin: 0 0 10px;"><label><strong>标题</strong></label><br>' +
                    '<input type="text" name="<?php echo $this->option_name; ?>[benefits][' + benefitIndex + '][title]" style="width: 100%;" /></p>' +
                    '<p style="margin: 0;"><label><strong>描述</strong></label><br>' +
                    '<input type="text" name="<?php echo $this->option_name; ?>[benefits][' + benefitIndex + '][desc]" style="width: 100%;" /></p>' +
                    '</div>';
                $('#benefits-container').append(html);
                benefitIndex++;
            });
            
            $(document).on('click', '.remove-benefit', function(e) {
                e.preventDefault();
                $(this).closest('.benefit-item').remove();
            });
        });
        </script>
        <?php
    }

    /**
     * 渲染职位管理页面
     */
    public function render_positions_page() {
        global $wpdb;
        
        // 处理操作
        $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
        $id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        
        // 删除操作
        if ( $action === 'delete' && $id && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'delete_position_' . $id ) ) {
            $wpdb->delete( $this->positions_table, array( 'id' => $id ), array( '%d' ) );
            echo '<div class="notice notice-success"><p>职位已删除</p></div>';
        }
        
        // 保存操作
        if ( isset( $_POST['save_position'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'save_position' ) ) {
            $data = array(
                'title' => sanitize_text_field( $_POST['title'] ?? '' ),
                'department' => sanitize_text_field( $_POST['department'] ?? '' ),
                'location' => sanitize_text_field( $_POST['location'] ?? '' ),
                'job_type' => sanitize_text_field( $_POST['job_type'] ?? 'fulltime' ),
                'salary' => sanitize_text_field( $_POST['salary'] ?? '' ),
                'category' => sanitize_text_field( $_POST['category'] ?? '' ),
                'description' => sanitize_textarea_field( $_POST['description'] ?? '' ),
                'requirements' => sanitize_textarea_field( $_POST['requirements'] ?? '' ),
                'sort_order' => intval( $_POST['sort_order'] ?? 0 ),
                'status' => isset( $_POST['status'] ) ? 1 : 0,
            );
            
            $edit_id = isset( $_POST['position_id'] ) ? intval( $_POST['position_id'] ) : 0;
            
            if ( $edit_id > 0 ) {
                $wpdb->update( $this->positions_table, $data, array( 'id' => $edit_id ) );
                echo '<div class="notice notice-success"><p>职位已更新</p></div>';
            } else {
                $wpdb->insert( $this->positions_table, $data );
                echo '<div class="notice notice-success"><p>职位已添加</p></div>';
            }
            $action = ''; // 重置为列表视图
        }
        
        // 编辑模式
        if ( $action === 'edit' || $action === 'add' ) {
            $position = $action === 'edit' && $id ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->positions_table} WHERE id = %d", $id ) ) : null;
            $this->render_position_form( $position );
            return;
        }
        
        // 列表视图
        $positions = $wpdb->get_results( "SELECT * FROM {$this->positions_table} ORDER BY sort_order ASC, id DESC" );
        ?>
        <div class="wrap">
            <h1>
                职位管理
                <a href="<?php echo admin_url( 'admin.php?page=developer-starter-careers-positions&action=add' ); ?>" class="page-title-action">添加新职位</a>
            </h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>职位名称</th>
                        <th style="width: 100px;">部门</th>
                        <th style="width: 80px;">地点</th>
                        <th style="width: 80px;">类型</th>
                        <th style="width: 100px;">薪资</th>
                        <th style="width: 80px;">分类</th>
                        <th style="width: 60px;">排序</th>
                        <th style="width: 60px;">状态</th>
                        <th style="width: 120px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $positions ) ) : ?>
                        <tr><td colspan="10" style="text-align: center; padding: 40px;">暂无职位，请添加</td></tr>
                    <?php else : ?>
                        <?php 
                        $job_types = array( 'fulltime' => '全职', 'parttime' => '兼职', 'intern' => '实习' );
                        $categories = array( 'tech' => '技术', 'product' => '产品', 'market' => '市场', 'admin' => '职能' );
                        foreach ( $positions as $pos ) : ?>
                            <tr>
                                <td><?php echo esc_html( $pos->id ); ?></td>
                                <td><strong><?php echo esc_html( $pos->title ); ?></strong></td>
                                <td><?php echo esc_html( $pos->department ); ?></td>
                                <td><?php echo esc_html( $pos->location ); ?></td>
                                <td><?php echo esc_html( $job_types[ $pos->job_type ] ?? $pos->job_type ); ?></td>
                                <td><?php echo esc_html( $pos->salary ); ?></td>
                                <td><?php echo esc_html( $categories[ $pos->category ] ?? $pos->category ); ?></td>
                                <td><?php echo esc_html( $pos->sort_order ); ?></td>
                                <td>
                                    <?php if ( $pos->status ) : ?>
                                        <span style="color: #22c55e;">启用</span>
                                    <?php else : ?>
                                        <span style="color: #94a3b8;">禁用</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url( 'admin.php?page=developer-starter-careers-positions&action=edit&id=' . $pos->id ); ?>">编辑</a> |
                                    <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=developer-starter-careers-positions&action=delete&id=' . $pos->id ), 'delete_position_' . $pos->id ); ?>" 
                                       onclick="return confirm('确定删除此职位？');" style="color: #dc2626;">删除</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * 渲染职位编辑表单
     */
    private function render_position_form( $position = null ) {
        $is_edit = $position !== null;
        ?>
        <div class="wrap">
            <h1><?php echo $is_edit ? '编辑职位' : '添加新职位'; ?></h1>
            
            <form method="post">
                <?php wp_nonce_field( 'save_position' ); ?>
                <?php if ( $is_edit ) : ?>
                    <input type="hidden" name="position_id" value="<?php echo esc_attr( $position->id ); ?>" />
                <?php endif; ?>
                
                <table class="form-table">
                    <tr>
                        <th><label for="title">职位名称 <span style="color: red;">*</span></label></th>
                        <td>
                            <input type="text" id="title" name="title" 
                                   value="<?php echo esc_attr( $position->title ?? '' ); ?>" class="regular-text" required />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="department">部门</label></th>
                        <td>
                            <input type="text" id="department" name="department" 
                                   value="<?php echo esc_attr( $position->department ?? '' ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="location">工作地点</label></th>
                        <td>
                            <input type="text" id="location" name="location" 
                                   value="<?php echo esc_attr( $position->location ?? '' ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="job_type">工作类型</label></th>
                        <td>
                            <select id="job_type" name="job_type">
                                <option value="fulltime" <?php selected( $position->job_type ?? '', 'fulltime' ); ?>>全职</option>
                                <option value="parttime" <?php selected( $position->job_type ?? '', 'parttime' ); ?>>兼职</option>
                                <option value="intern" <?php selected( $position->job_type ?? '', 'intern' ); ?>>实习</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="salary">薪资范围</label></th>
                        <td>
                            <input type="text" id="salary" name="salary" 
                                   value="<?php echo esc_attr( $position->salary ?? '' ); ?>" placeholder="如：15-25K" style="width: 150px;" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="category">分类标签</label></th>
                        <td>
                            <select id="category" name="category">
                                <option value="tech" <?php selected( $position->category ?? '', 'tech' ); ?>>技术研发</option>
                                <option value="product" <?php selected( $position->category ?? '', 'product' ); ?>>产品设计</option>
                                <option value="market" <?php selected( $position->category ?? '', 'market' ); ?>>市场运营</option>
                                <option value="admin" <?php selected( $position->category ?? '', 'admin' ); ?>>职能管理</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="description">职位描述</label></th>
                        <td>
                            <textarea id="description" name="description" rows="6" class="large-text"><?php echo esc_textarea( $position->description ?? '' ); ?></textarea>
                            <p class="description">每行一条，将显示为列表</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="requirements">任职要求</label></th>
                        <td>
                            <textarea id="requirements" name="requirements" rows="6" class="large-text"><?php echo esc_textarea( $position->requirements ?? '' ); ?></textarea>
                            <p class="description">每行一条，将显示为列表</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="sort_order">排序</label></th>
                        <td>
                            <input type="number" id="sort_order" name="sort_order" 
                                   value="<?php echo esc_attr( $position->sort_order ?? 0 ); ?>" style="width: 80px;" />
                            <p class="description">数字越小越靠前</p>
                        </td>
                    </tr>
                    <tr>
                        <th>状态</th>
                        <td>
                            <label>
                                <input type="checkbox" name="status" value="1" <?php checked( $position->status ?? 1, 1 ); ?> />
                                启用此职位
                            </label>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="save_position" class="button button-primary" value="保存职位" />
                    <a href="<?php echo admin_url( 'admin.php?page=developer-starter-careers-positions' ); ?>" class="button">返回列表</a>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * 渲染求职申请页面
     */
    public function render_applications_page() {
        global $wpdb;
        
        // 处理操作
        if ( isset( $_GET['action'] ) && isset( $_GET['id'] ) && isset( $_GET['_wpnonce'] ) ) {
            $id = intval( $_GET['id'] );
            if ( wp_verify_nonce( $_GET['_wpnonce'], 'application_action' ) ) {
                if ( $_GET['action'] === 'mark_read' ) {
                    $wpdb->update( $this->applications_table, array( 'is_read' => 1 ), array( 'id' => $id ), array( '%d' ), array( '%d' ) );
                } elseif ( $_GET['action'] === 'delete' ) {
                    $wpdb->delete( $this->applications_table, array( 'id' => $id ), array( '%d' ) );
                }
            }
        }
        
        // 获取申请列表
        $applications = $wpdb->get_results( "SELECT * FROM {$this->applications_table} ORDER BY created_at DESC LIMIT 100" );
        ?>
        <div class="wrap">
            <h1>求职申请管理</h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 80px;">姓名</th>
                        <th style="width: 120px;">电话</th>
                        <th style="width: 150px;">邮箱</th>
                        <th style="width: 150px;">应聘职位</th>
                        <th>自我介绍</th>
                        <th style="width: 150px;">申请时间</th>
                        <th style="width: 60px;">状态</th>
                        <th style="width: 120px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $applications ) ) : ?>
                        <tr><td colspan="9" style="text-align: center; padding: 40px;">暂无求职申请</td></tr>
                    <?php else : ?>
                        <?php foreach ( $applications as $app ) : ?>
                            <tr style="<?php echo $app->is_read ? '' : 'background: #fff9e6;'; ?>">
                                <td><?php echo esc_html( $app->id ); ?></td>
                                <td><strong><?php echo esc_html( $app->name ); ?></strong></td>
                                <td><?php echo esc_html( $app->phone ); ?></td>
                                <td><?php echo esc_html( $app->email ); ?></td>
                                <td><?php echo esc_html( $app->position_title ); ?></td>
                                <td><?php echo esc_html( wp_trim_words( $app->message, 20 ) ); ?></td>
                                <td><?php echo esc_html( $app->created_at ); ?></td>
                                <td>
                                    <?php if ( $app->is_read ) : ?>
                                        <span style="color: #22c55e;">已读</span>
                                    <?php else : ?>
                                        <span style="color: #f59e0b; font-weight: bold;">未读</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ( ! $app->is_read ) : ?>
                                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=developer-starter-careers-applications&action=mark_read&id=' . $app->id ), 'application_action' ); ?>">标记已读</a> |
                                    <?php endif; ?>
                                    <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=developer-starter-careers-applications&action=delete&id=' . $app->id ), 'application_action' ); ?>" 
                                       onclick="return confirm('确定删除此申请？');" style="color: #dc2626;">删除</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    // ==================== AJAX 处理 ====================

    /**
     * 处理求职申请提交
     */
    public function handle_application_submit() {
        // 验证 nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ds_careers_application_nonce' ) ) {
            wp_send_json_error( array( 'message' => '安全验证失败' ) );
        }
        
        // 检查是否启用申请
        $options = get_option( $this->option_name, array() );
        if ( empty( $options['enable_application'] ) ) {
            wp_send_json_error( array( 'message' => '在线申请已关闭' ) );
        }
        
        // 频率限制
        $ip = $this->get_client_ip();
        if ( $this->is_rate_limited( $ip ) ) {
            wp_send_json_error( array( 'message' => '提交过于频繁，请稍后再试' ) );
        }
        
        // 清理输入
        $name = sanitize_text_field( $_POST['name'] ?? '' );
        $phone = sanitize_text_field( $_POST['phone'] ?? '' );
        $email = sanitize_email( $_POST['email'] ?? '' );
        $position_id = intval( $_POST['position_id'] ?? 0 );
        $position_title = sanitize_text_field( $_POST['position_title'] ?? '' );
        $message = sanitize_textarea_field( $_POST['message'] ?? '' );
        
        // 验证必填
        if ( empty( $name ) ) {
            wp_send_json_error( array( 'message' => '请填写姓名' ) );
        }
        if ( empty( $phone ) && empty( $email ) ) {
            wp_send_json_error( array( 'message' => '请填写联系电话或邮箱' ) );
        }
        if ( empty( $position_title ) ) {
            wp_send_json_error( array( 'message' => '请选择应聘职位' ) );
        }
        
        // 插入数据库
        global $wpdb;
        $result = $wpdb->insert(
            $this->applications_table,
            array(
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'position_id' => $position_id,
                'position_title' => $position_title,
                'message' => $message,
                'ip_address' => $ip,
                'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 0, 255 ) : '',
                'is_read' => 0,
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s' )
        );
        
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => '提交失败，请稍后重试' ) );
        }
        
        wp_send_json_success( array( 'message' => '申请已提交！我们会尽快与您联系' ) );
    }

    /**
     * 获取客户端IP
     */
    private function get_client_ip() {
        $ip = '';
        if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] )[0];
        } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return sanitize_text_field( $ip );
    }

    /**
     * 检查频率限制
     */
    private function is_rate_limited( $ip ) {
        global $wpdb;
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->applications_table} WHERE ip_address = %s AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)",
            $ip
        ) );
        return $count >= 3; // 5分钟内最多3次
    }
}
