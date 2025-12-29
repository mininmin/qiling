<?php
/**
 * Message Manager Class - 留言管理系统
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Message_Manager {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'developer_starter_messages';
        
        add_action( 'after_switch_theme', array( $this, 'create_table' ) );
        add_action( 'wp_ajax_ds_submit_message', array( $this, 'handle_message_submit' ) );
        add_action( 'wp_ajax_nopriv_ds_submit_message', array( $this, 'handle_message_submit' ) );
        add_action( 'admin_menu', array( $this, 'add_messages_menu' ), 20 ); // Priority 20 to load after main menu
        
        // Create table on init if not exists
        add_action( 'init', array( $this, 'maybe_create_table' ) );
    }

    public function maybe_create_table() {
        global $wpdb;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$this->table_name}'" ) !== $this->table_name ) {
            $this->create_table();
        }
    }

    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            phone VARCHAR(50) DEFAULT '',
            email VARCHAR(100) DEFAULT '',
            message TEXT NOT NULL,
            ip_address VARCHAR(45) DEFAULT '',
            user_agent VARCHAR(255) DEFAULT '',
            is_read TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY is_read (is_read),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public function handle_message_submit() {
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ds_message_nonce' ) ) {
            wp_send_json_error( array( 'message' => '安全验证失败' ) );
        }
        
        // Rate limiting by IP
        $ip = $this->get_client_ip();
        if ( $this->is_rate_limited( $ip ) ) {
            wp_send_json_error( array( 'message' => '提交过于频繁，请稍后再试' ) );
        }
        
        // Sanitize inputs - prevent SQL injection
        $name = sanitize_text_field( isset( $_POST['name'] ) ? $_POST['name'] : '' );
        $phone = sanitize_text_field( isset( $_POST['phone'] ) ? $_POST['phone'] : '' );
        $email = sanitize_email( isset( $_POST['email'] ) ? $_POST['email'] : '' );
        $message = sanitize_textarea_field( isset( $_POST['message'] ) ? $_POST['message'] : '' );
        
        // Validate required fields
        if ( empty( $name ) || empty( $message ) ) {
            wp_send_json_error( array( 'message' => '请填写必填项' ) );
        }
        
        if ( empty( $phone ) && empty( $email ) ) {
            wp_send_json_error( array( 'message' => '请填写联系电话或邮箱' ) );
        }
        
        // Insert into database
        global $wpdb;
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'name'       => $name,
                'phone'      => $phone,
                'email'      => $email,
                'message'    => $message,
                'ip_address' => $ip,
                'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 0, 255 ) : '',
                'is_read'    => 0,
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
        );
        
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => '提交失败，请稍后重试' ) );
        }
        
        // Send email notification
        $this->send_email_notification( $name, $phone, $email, $message );
        
        wp_send_json_success( array( 'message' => '留言提交成功，我们会尽快与您联系！' ) );
    }

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

    private function is_rate_limited( $ip ) {
        global $wpdb;
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE ip_address = %s AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)",
            $ip
        ) );
        return $count >= 3; // Max 3 submissions per minute
    }

    private function send_email_notification( $name, $phone, $email, $message ) {
        $send_to_admin = developer_starter_get_option( 'smtp_send_to_admin', '' );
        if ( ! $send_to_admin ) {
            return;
        }
        
        $admin_email = get_option( 'admin_email' );
        $site_name = get_bloginfo( 'name' );
        
        $subject = "[{$site_name}] 新留言通知";
        
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: #fff; padding: 30px; border-radius: 10px 10px 0 0;'>
                <h2 style='margin: 0;'>📬 新留言通知</h2>
                <p style='margin: 10px 0 0; opacity: 0.9;'>您收到一条新的网站留言</p>
            </div>
            <div style='background: #f8fafc; padding: 30px; border: 1px solid #e2e8f0; border-top: none;'>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 10px 0; border-bottom: 1px solid #e2e8f0; font-weight: bold; width: 100px;'>姓名</td>
                        <td style='padding: 10px 0; border-bottom: 1px solid #e2e8f0;'>" . esc_html( $name ) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 0; border-bottom: 1px solid #e2e8f0; font-weight: bold;'>电话</td>
                        <td style='padding: 10px 0; border-bottom: 1px solid #e2e8f0;'>" . esc_html( $phone ) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 0; border-bottom: 1px solid #e2e8f0; font-weight: bold;'>邮箱</td>
                        <td style='padding: 10px 0; border-bottom: 1px solid #e2e8f0;'>" . esc_html( $email ) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 0; font-weight: bold; vertical-align: top;'>留言</td>
                        <td style='padding: 10px 0;'>" . nl2br( esc_html( $message ) ) . "</td>
                    </tr>
                </table>
            </div>
            <div style='background: #1e293b; color: #94a3b8; padding: 20px; border-radius: 0 0 10px 10px; text-align: center; font-size: 12px;'>
                <p style='margin: 0;'>此邮件由 {$site_name} 自动发送</p>
            </div>
        </div>";
        
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        
        wp_mail( $admin_email, $subject, $body, $headers );
    }

    public function add_messages_menu() {
        $unread_count = $this->get_unread_count();
        $menu_title = '留言管理';
        if ( $unread_count > 0 ) {
            $menu_title .= ' <span class="awaiting-mod count-' . $unread_count . '"><span class="pending-count">' . $unread_count . '</span></span>';
        }
        
        add_submenu_page(
            'developer-starter-settings',
            '留言管理',
            $menu_title,
            'manage_options',
            'developer-starter-messages',
            array( $this, 'render_messages_page' )
        );
    }

    private function get_unread_count() {
        global $wpdb;
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} WHERE is_read = 0" );
    }

    public function render_messages_page() {
        global $wpdb;
        
        // Handle actions
        if ( isset( $_GET['action'] ) && isset( $_GET['id'] ) && isset( $_GET['_wpnonce'] ) ) {
            $id = intval( $_GET['id'] );
            if ( wp_verify_nonce( $_GET['_wpnonce'], 'ds_message_action' ) ) {
                if ( $_GET['action'] === 'mark_read' ) {
                    $wpdb->update( $this->table_name, array( 'is_read' => 1 ), array( 'id' => $id ), array( '%d' ), array( '%d' ) );
                } elseif ( $_GET['action'] === 'delete' ) {
                    $wpdb->delete( $this->table_name, array( 'id' => $id ), array( '%d' ) );
                }
            }
        }
        
        // Get messages
        $messages = $wpdb->get_results( "SELECT * FROM {$this->table_name} ORDER BY created_at DESC LIMIT 100" );
        ?>
        <div class="wrap">
            <h1>留言管理</h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 100px;">姓名</th>
                        <th style="width: 120px;">电话</th>
                        <th style="width: 150px;">邮箱</th>
                        <th>留言内容</th>
                        <th style="width: 150px;">时间</th>
                        <th style="width: 60px;">状态</th>
                        <th style="width: 100px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $messages ) ) : ?>
                        <tr><td colspan="8" style="text-align: center; padding: 40px;">暂无留言</td></tr>
                    <?php else : ?>
                        <?php foreach ( $messages as $msg ) : ?>
                            <tr style="<?php echo $msg->is_read ? '' : 'background: #fff9e6;'; ?>">
                                <td><?php echo esc_html( $msg->id ); ?></td>
                                <td><strong><?php echo esc_html( $msg->name ); ?></strong></td>
                                <td><?php echo esc_html( $msg->phone ); ?></td>
                                <td><?php echo esc_html( $msg->email ); ?></td>
                                <td><?php echo esc_html( wp_trim_words( $msg->message, 30 ) ); ?></td>
                                <td><?php echo esc_html( $msg->created_at ); ?></td>
                                <td>
                                    <?php if ( $msg->is_read ) : ?>
                                        <span style="color: #22c55e;">已读</span>
                                    <?php else : ?>
                                        <span style="color: #f59e0b; font-weight: bold;">未读</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ( ! $msg->is_read ) : ?>
                                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=developer-starter-messages&action=mark_read&id=' . $msg->id ), 'ds_message_action' ); ?>">标记已读</a> |
                                    <?php endif; ?>
                                    <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=developer-starter-messages&action=delete&id=' . $msg->id ), 'ds_message_action' ); ?>" 
                                       onclick="return confirm('确定删除此留言？');" style="color: #dc2626;">删除</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
