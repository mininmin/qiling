<?php
/**
 * 表单管理核心类
 *
 * @package Developer_Starter
 * @since 1.0.2
 */

namespace Developer_Starter\Forms;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Form_Manager {

    private static $instance = null;
    
    private $forms_table;
    private $entries_table;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;
        $this->forms_table = $wpdb->prefix . 'developer_forms';
        $this->entries_table = $wpdb->prefix . 'developer_form_entries';
        
        // 主题激活时创建数据表
        add_action( 'after_switch_theme', array( __CLASS__, 'activate' ) );
        
        // 首次加载时检查表是否存在
        add_action( 'admin_init', array( $this, 'maybe_create_tables' ) );
    }
    
    /**
     * 检查并创建数据表（如果不存在）
     */
    public function maybe_create_tables() {
        global $wpdb;
        
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$this->forms_table}'" );
        if ( ! $table_exists ) {
            self::activate();
        }
    }

    /**
     * 激活时创建数据表
     */
    public static function activate() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $forms_table = $wpdb->prefix . 'developer_forms';
        $entries_table = $wpdb->prefix . 'developer_form_entries';

        $sql_forms = "CREATE TABLE IF NOT EXISTS $forms_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            fields LONGTEXT NOT NULL,
            settings TEXT,
            notify_emails TEXT,
            submit_button VARCHAR(100) DEFAULT '提交',
            success_message TEXT,
            limit_per_ip INT DEFAULT 5,
            limit_interval INT DEFAULT 60,
            status VARCHAR(20) DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";

        $sql_entries = "CREATE TABLE IF NOT EXISTS $entries_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            form_id BIGINT(20) UNSIGNED NOT NULL,
            data LONGTEXT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            is_read TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY form_id (form_id),
            KEY is_read (is_read)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_forms );
        dbDelta( $sql_entries );
        
        // 插入预设表单
        self::insert_preset_forms();
    }

    /**
     * 插入预设表单
     */
    private static function insert_preset_forms() {
        global $wpdb;
        $forms_table = $wpdb->prefix . 'developer_forms';
        
        // 检查是否已有表单
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM $forms_table" );
        if ( $count > 0 ) {
            return;
        }
        
        $presets = array(
            array(
                'title' => '联系我们',
                'slug' => 'contact',
                'fields' => json_encode( array(
                    array( 'type' => 'text', 'name' => 'name', 'label' => '您的姓名', 'placeholder' => '请输入姓名', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'email', 'name' => 'email', 'label' => '电子邮箱', 'placeholder' => '请输入邮箱', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'tel', 'name' => 'phone', 'label' => '联系电话', 'placeholder' => '请输入电话', 'required' => false, 'width' => '100' ),
                    array( 'type' => 'textarea', 'name' => 'message', 'label' => '留言内容', 'placeholder' => '请输入您的留言', 'required' => true, 'width' => '100', 'rows' => 5 ),
                ), JSON_UNESCAPED_UNICODE ),
                'submit_button' => '发送留言',
                'success_message' => '感谢您的留言，我们会尽快回复！',
            ),
            array(
                'title' => '报价咨询',
                'slug' => 'quote',
                'fields' => json_encode( array(
                    array( 'type' => 'text', 'name' => 'company', 'label' => '公司名称', 'placeholder' => '请输入公司名称', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'text', 'name' => 'contact', 'label' => '联系人', 'placeholder' => '请输入联系人', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'tel', 'name' => 'phone', 'label' => '联系电话', 'placeholder' => '请输入电话', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'email', 'name' => 'email', 'label' => '电子邮箱', 'placeholder' => '请输入邮箱', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'select', 'name' => 'service', 'label' => '咨询服务', 'required' => true, 'width' => '100', 'options' => array( '网站建设', 'App开发', '小程序开发', '系统定制', 'SEO优化', '其他' ) ),
                    array( 'type' => 'textarea', 'name' => 'requirements', 'label' => '需求描述', 'placeholder' => '请详细描述您的需求', 'required' => true, 'width' => '100', 'rows' => 5 ),
                ), JSON_UNESCAPED_UNICODE ),
                'submit_button' => '立即咨询',
                'success_message' => '感谢您的咨询，我们的顾问会尽快与您联系！',
            ),
            array(
                'title' => '项目需求提交',
                'slug' => 'project',
                'fields' => json_encode( array(
                    array( 'type' => 'text', 'name' => 'project_name', 'label' => '项目名称', 'placeholder' => '请输入项目名称', 'required' => true, 'width' => '100' ),
                    array( 'type' => 'text', 'name' => 'company', 'label' => '公司/组织', 'placeholder' => '请输入公司名称', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'text', 'name' => 'contact', 'label' => '联系人', 'placeholder' => '请输入联系人', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'tel', 'name' => 'phone', 'label' => '联系电话', 'placeholder' => '请输入电话', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'email', 'name' => 'email', 'label' => '电子邮箱', 'placeholder' => '请输入邮箱', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'select', 'name' => 'budget', 'label' => '预算范围', 'required' => true, 'width' => '50', 'options' => array( '1万以下', '1-5万', '5-10万', '10-50万', '50万以上' ) ),
                    array( 'type' => 'select', 'name' => 'timeline', 'label' => '期望工期', 'required' => true, 'width' => '50', 'options' => array( '1个月内', '1-3个月', '3-6个月', '6个月以上' ) ),
                    array( 'type' => 'textarea', 'name' => 'description', 'label' => '项目描述', 'placeholder' => '请详细描述项目需求、功能要求等', 'required' => true, 'width' => '100', 'rows' => 6 ),
                ), JSON_UNESCAPED_UNICODE ),
                'submit_button' => '提交需求',
                'success_message' => '需求已提交，我们的项目经理会在24小时内与您联系！',
            ),
            array(
                'title' => '招商加盟',
                'slug' => 'franchise',
                'fields' => json_encode( array(
                    array( 'type' => 'text', 'name' => 'name', 'label' => '您的姓名', 'placeholder' => '请输入姓名', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'tel', 'name' => 'phone', 'label' => '联系电话', 'placeholder' => '请输入电话', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'text', 'name' => 'city', 'label' => '所在城市', 'placeholder' => '请输入所在城市', 'required' => true, 'width' => '50' ),
                    array( 'type' => 'select', 'name' => 'investment', 'label' => '投资预算', 'required' => true, 'width' => '50', 'options' => array( '10万以下', '10-30万', '30-50万', '50-100万', '100万以上' ) ),
                    array( 'type' => 'radio', 'name' => 'experience', 'label' => '是否有相关行业经验', 'required' => true, 'width' => '100', 'options' => array( '有经验', '无经验' ) ),
                    array( 'type' => 'textarea', 'name' => 'message', 'label' => '其他说明', 'placeholder' => '请输入其他需要说明的信息', 'required' => false, 'width' => '100', 'rows' => 4 ),
                ), JSON_UNESCAPED_UNICODE ),
                'submit_button' => '申请加盟',
                'success_message' => '感谢您的加盟申请，我们的招商经理会尽快与您联系！',
            ),
        );

        foreach ( $presets as $preset ) {
            $wpdb->insert( $forms_table, $preset );
        }
    }

    /**
     * 获取所有表单
     */
    public function get_forms( $args = array() ) {
        global $wpdb;
        
        $defaults = array(
            'status' => '',
            'orderby' => 'id',
            'order' => 'DESC',
        );
        $args = wp_parse_args( $args, $defaults );
        
        $where = '1=1';
        if ( ! empty( $args['status'] ) ) {
            $where .= $wpdb->prepare( ' AND status = %s', $args['status'] );
        }
        
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
        
        return $wpdb->get_results( "SELECT * FROM {$this->forms_table} WHERE $where ORDER BY $orderby" );
    }

    /**
     * 获取单个表单
     */
    public function get_form( $id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->forms_table} WHERE id = %d", $id ) );
    }

    /**
     * 通过别名获取表单
     */
    public function get_form_by_slug( $slug ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->forms_table} WHERE slug = %s", $slug ) );
    }

    /**
     * 保存表单
     */
    public function save_form( $data ) {
        global $wpdb;
        
        $form_data = array(
            'title' => sanitize_text_field( $data['title'] ?? '' ),
            'slug' => sanitize_title( $data['slug'] ?? '' ),
            'fields' => $data['fields'] ?? '[]',
            'settings' => $data['settings'] ?? '',
            'notify_emails' => sanitize_text_field( $data['notify_emails'] ?? '' ),
            'submit_button' => sanitize_text_field( $data['submit_button'] ?? '提交' ),
            'success_message' => wp_kses_post( $data['success_message'] ?? '' ),
            'limit_per_ip' => absint( $data['limit_per_ip'] ?? 5 ),
            'limit_interval' => absint( $data['limit_interval'] ?? 60 ),
            'status' => sanitize_text_field( $data['status'] ?? 'active' ),
        );

        if ( ! empty( $data['id'] ) ) {
            // 更新
            $wpdb->update( $this->forms_table, $form_data, array( 'id' => absint( $data['id'] ) ) );
            return absint( $data['id'] );
        } else {
            // 新建
            $wpdb->insert( $this->forms_table, $form_data );
            return $wpdb->insert_id;
        }
    }

    /**
     * 删除表单
     */
    public function delete_form( $id ) {
        global $wpdb;
        
        // 删除相关提交数据
        $wpdb->delete( $this->entries_table, array( 'form_id' => $id ) );
        
        // 删除表单
        return $wpdb->delete( $this->forms_table, array( 'id' => $id ) );
    }

    /**
     * 保存表单提交
     */
    public function save_entry( $form_id, $data ) {
        global $wpdb;
        
        $entry_data = array(
            'form_id' => absint( $form_id ),
            'data' => is_string( $data ) ? $data : json_encode( $data, JSON_UNESCAPED_UNICODE ),
            'ip_address' => $this->get_client_ip(),
            'user_agent' => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ),
        );

        $wpdb->insert( $this->entries_table, $entry_data );
        return $wpdb->insert_id;
    }

    /**
     * 获取表单提交数据
     */
    public function get_entries( $form_id, $args = array() ) {
        global $wpdb;
        
        $defaults = array(
            'per_page' => 20,
            'page' => 1,
            'orderby' => 'created_at',
            'order' => 'DESC',
        );
        $args = wp_parse_args( $args, $defaults );
        
        $offset = ( $args['page'] - 1 ) * $args['per_page'];
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
        
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->entries_table} WHERE form_id = %d ORDER BY $orderby LIMIT %d OFFSET %d",
            $form_id,
            $args['per_page'],
            $offset
        ) );
    }

    /**
     * 获取提交数量
     */
    public function get_entries_count( $form_id ) {
        global $wpdb;
        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->entries_table} WHERE form_id = %d",
            $form_id
        ) );
    }

    /**
     * 获取未读提交数量
     */
    public function get_unread_count( $form_id = 0 ) {
        global $wpdb;
        
        if ( $form_id > 0 ) {
            return (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->entries_table} WHERE form_id = %d AND is_read = 0",
                $form_id
            ) );
        }
        
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->entries_table} WHERE is_read = 0" );
    }

    /**
     * 标记为已读
     */
    public function mark_as_read( $entry_id ) {
        global $wpdb;
        return $wpdb->update( $this->entries_table, array( 'is_read' => 1 ), array( 'id' => $entry_id ) );
    }

    /**
     * 删除提交
     */
    public function delete_entry( $entry_id ) {
        global $wpdb;
        return $wpdb->delete( $this->entries_table, array( 'id' => $entry_id ) );
    }

    /**
     * 检查 IP 提交限制
     */
    public function check_rate_limit( $form_id ) {
        $form = $this->get_form( $form_id );
        if ( ! $form ) {
            return false;
        }
        
        $ip = $this->get_client_ip();
        $transient_key = 'form_submit_' . $form_id . '_' . md5( $ip );
        $count = (int) get_transient( $transient_key );
        
        if ( $count >= $form->limit_per_ip ) {
            return false;
        }
        
        return true;
    }

    /**
     * 记录提交
     */
    public function record_submission( $form_id ) {
        $form = $this->get_form( $form_id );
        if ( ! $form ) {
            return;
        }
        
        $ip = $this->get_client_ip();
        $transient_key = 'form_submit_' . $form_id . '_' . md5( $ip );
        $count = (int) get_transient( $transient_key );
        
        set_transient( $transient_key, $count + 1, $form->limit_interval * MINUTE_IN_SECONDS );
    }

    /**
     * 获取客户端 IP
     */
    private function get_client_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            return sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            return sanitize_text_field( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] )[0] );
        }
        return sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' );
    }

    /**
     * 发送通知邮件
     */
    public function send_notification( $form, $entry_data ) {
        if ( empty( $form->notify_emails ) ) {
            // 使用管理员邮箱
            $to = get_option( 'admin_email' );
        } else {
            $to = $form->notify_emails;
        }
        
        $subject = sprintf( '[%s] 新的表单提交：%s', get_bloginfo( 'name' ), $form->title );
        
        $message = "您收到一条新的表单提交：\n\n";
        $message .= "表单：{$form->title}\n";
        $message .= "时间：" . current_time( 'Y-m-d H:i:s' ) . "\n\n";
        $message .= "--- 提交内容 ---\n\n";
        
        $fields = json_decode( $form->fields, true );
        if ( is_array( $fields ) && is_array( $entry_data ) ) {
            foreach ( $fields as $field ) {
                $name = $field['name'] ?? '';
                $label = $field['label'] ?? $name;
                $value = $entry_data[ $name ] ?? '';
                
                if ( is_array( $value ) ) {
                    $value = implode( ', ', $value );
                }
                
                $message .= "{$label}: {$value}\n";
            }
        }
        
        $message .= "\n--- 其他信息 ---\n";
        $message .= "IP 地址：" . $this->get_client_ip() . "\n";
        
        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
        
        return wp_mail( $to, $subject, $message, $headers );
    }
}
