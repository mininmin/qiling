<?php
/**
 * Auth Manager Class - 自定义注册登录管理
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

namespace Developer_Starter\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Auth_Manager {

    private $option_name = 'developer_starter_options';

    public function __construct() {
        // AJAX 处理 - 未登录用户
        add_action( 'wp_ajax_nopriv_developer_starter_login', array( $this, 'ajax_login' ) );
        add_action( 'wp_ajax_nopriv_developer_starter_register', array( $this, 'ajax_register' ) );
        add_action( 'wp_ajax_nopriv_developer_starter_forgot_password', array( $this, 'ajax_forgot_password' ) );
        add_action( 'wp_ajax_nopriv_developer_starter_reset_password', array( $this, 'ajax_reset_password' ) );
        
        // AJAX 处理 - 已登录用户（防止已登录用户访问认证页面时的问题）
        add_action( 'wp_ajax_developer_starter_login', array( $this, 'ajax_already_logged_in' ) );
        add_action( 'wp_ajax_developer_starter_register', array( $this, 'ajax_already_logged_in' ) );
        
        // AJAX 获取用户状态（用于缓存兼容 - 已登录用户不受缓存影响）
        add_action( 'wp_ajax_developer_starter_user_status', array( $this, 'ajax_get_user_status' ) );
        add_action( 'wp_ajax_nopriv_developer_starter_user_status', array( $this, 'ajax_get_user_status' ) );
        
        // 重定向默认登录页面
        add_action( 'init', array( $this, 'redirect_default_auth_pages' ) );
        
        // 自动创建认证页面
        add_action( 'after_switch_theme', array( $this, 'create_auth_pages' ) );
        
        // AJAX 头像上传
        add_action( 'wp_ajax_developer_starter_upload_avatar', array( $this, 'ajax_upload_avatar' ) );
    }
    
    /**
     * 已登录用户尝试登录/注册时的响应
     */
    public function ajax_already_logged_in() {
        wp_send_json_success( array(
            'message' => '您已经登录，正在刷新页面...',
            'redirect' => home_url()
        ) );
    }
    
    /**
     * AJAX 获取用户状态（用于缓存兼容）
     * 已登录用户不受任何缓存影响，始终返回真实状态
     */
    public function ajax_get_user_status() {
        // 设置严格的不缓存响应头，确保不被任何缓存层拦截
        nocache_headers();
        
        // 添加额外的缓存控制头（防止CDN和代理缓存）
        header( 'Cache-Control: no-cache, no-store, must-revalidate, private, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );
        header( 'X-Accel-Expires: 0' ); // Nginx 特定
        header( 'Vary: Cookie' ); // 根据Cookie变化
        
        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            
            // 获取个人中心页面URL
            $account_url = get_transient( 'developer_starter_account_url' );
            if ( false === $account_url ) {
                $account_page = get_pages( array(
                    'meta_key' => '_wp_page_template',
                    'meta_value' => 'templates/template-account.php',
                    'number' => 1,
                ) );
                $account_url = ! empty( $account_page ) ? get_permalink( $account_page[0]->ID ) : admin_url( 'profile.php' );
                set_transient( 'developer_starter_account_url', $account_url, DAY_IN_SECONDS );
            }
            
            wp_send_json_success( array(
                'logged_in' => true,
                'user_id' => $current_user->ID,
                'display_name' => $current_user->display_name,
                'email' => $current_user->user_email,
                'avatar_32' => get_avatar_url( $current_user->ID, array( 'size' => 32 ) ),
                'avatar_48' => get_avatar_url( $current_user->ID, array( 'size' => 48 ) ),
                'account_url' => $account_url,
                'admin_url' => current_user_can( 'read' ) ? admin_url() : '',
                'logout_url' => wp_logout_url( home_url() ),
                'can_access_admin' => current_user_can( 'read' ),
            ) );
        } else {
            wp_send_json_success( array(
                'logged_in' => false,
            ) );
        }
    }

    /**
     * 获取选项
     */
    private function get_option( $key, $default = '' ) {
        return developer_starter_get_option( $key, $default );
    }

    /**
     * 重定向默认登录注册页面
     */
    public function redirect_default_auth_pages() {
        if ( ! $this->get_option( 'custom_auth_enable', '' ) ) {
            return;
        }

        global $pagenow;
        
        if ( $pagenow === 'wp-login.php' && ! is_user_logged_in() ) {
            $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'login';
            
            switch ( $action ) {
                case 'register':
                    $page_id = $this->get_option( 'register_page_id', '' );
                    if ( $page_id ) {
                        wp_redirect( get_permalink( $page_id ) );
                        exit;
                    }
                    break;
                case 'lostpassword':
                    $page_id = $this->get_option( 'forgot_password_page_id', '' );
                    if ( $page_id ) {
                        wp_redirect( get_permalink( $page_id ) );
                        exit;
                    }
                    break;
                default:
                    $page_id = $this->get_option( 'login_page_id', '' );
                    if ( $page_id ) {
                        wp_redirect( get_permalink( $page_id ) );
                        exit;
                    }
                    break;
            }
        }
    }

    /**
     * 自动创建认证页面
     */
    public function create_auth_pages() {
        $pages = array(
            'login' => array(
                'title' => '用户登录',
                'template' => 'templates/template-login.php',
                'option_key' => 'login_page_id'
            ),
            'register' => array(
                'title' => '用户注册',
                'template' => 'templates/template-register.php',
                'option_key' => 'register_page_id'
            ),
            'forgot-password' => array(
                'title' => '找回密码',
                'template' => 'templates/template-forgot-password.php',
                'option_key' => 'forgot_password_page_id'
            )
        );

        $options = get_option( $this->option_name, array() );

        foreach ( $pages as $slug => $page ) {
            // 检查页面是否已存在
            $existing = get_page_by_path( $slug );
            if ( ! $existing ) {
                $page_id = wp_insert_post( array(
                    'post_title'   => $page['title'],
                    'post_name'    => $slug,
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_content' => '',
                ) );

                if ( $page_id && ! is_wp_error( $page_id ) ) {
                    update_post_meta( $page_id, '_wp_page_template', $page['template'] );
                    $options[ $page['option_key'] ] = $page_id;
                }
            } else {
                $options[ $page['option_key'] ] = $existing->ID;
            }
        }

        update_option( $this->option_name, $options );
    }

    /**
     * AJAX 登录
     */
    public function ajax_login() {
        check_ajax_referer( 'developer_starter_auth', 'nonce' );

        $username = isset( $_POST['username'] ) ? sanitize_user( $_POST['username'] ) : '';
        $password = isset( $_POST['password'] ) ? $_POST['password'] : '';
        $remember = isset( $_POST['remember'] ) && $_POST['remember'] === 'true';

        if ( empty( $username ) || empty( $password ) ) {
            wp_send_json_error( array( 'message' => '请填写用户名和密码' ) );
        }

        // 验证滑动验证码
        if ( $this->get_option( 'auth_captcha_enable', '' ) ) {
            $captcha = isset( $_POST['captcha_verified'] ) ? $_POST['captcha_verified'] : '';
            if ( $captcha !== 'true' ) {
                wp_send_json_error( array( 'message' => '请完成滑动验证' ) );
            }
        }

        $user = wp_signon( array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember
        ) );

        if ( is_wp_error( $user ) ) {
            wp_send_json_error( array( 'message' => '用户名或密码错误' ) );
        }

        $redirect = $this->get_option( 'login_redirect_url', '' );
        if ( empty( $redirect ) ) {
            $redirect = home_url();
        }

        wp_send_json_success( array(
            'message' => '登录成功，正在跳转...',
            'redirect' => $redirect
        ) );
    }

    /**
     * AJAX 注册
     */
    public function ajax_register() {
        check_ajax_referer( 'developer_starter_auth', 'nonce' );

        if ( ! get_option( 'users_can_register' ) ) {
            wp_send_json_error( array( 'message' => '网站已关闭注册功能' ) );
        }

        $username = isset( $_POST['username'] ) ? sanitize_user( $_POST['username'] ) : '';
        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $password = isset( $_POST['password'] ) ? $_POST['password'] : '';
        $password_confirm = isset( $_POST['password_confirm'] ) ? $_POST['password_confirm'] : '';

        // 基本验证
        if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
            wp_send_json_error( array( 'message' => '请填写所有必填项' ) );
        }

        if ( strlen( $username ) < 3 ) {
            wp_send_json_error( array( 'message' => '用户名至少需要3个字符' ) );
        }

        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => '请输入有效的邮箱地址' ) );
        }

        if ( $password !== $password_confirm ) {
            wp_send_json_error( array( 'message' => '两次输入的密码不一致' ) );
        }

        // 密码强度验证
        $strength = $this->get_option( 'password_strength', 'medium' );
        $strength_check = $this->check_password_strength( $password, $strength );
        if ( ! $strength_check['valid'] ) {
            wp_send_json_error( array( 'message' => $strength_check['message'] ) );
        }

        // 验证滑动验证码
        if ( $this->get_option( 'auth_captcha_enable', '' ) ) {
            $captcha = isset( $_POST['captcha_verified'] ) ? $_POST['captcha_verified'] : '';
            if ( $captcha !== 'true' ) {
                wp_send_json_error( array( 'message' => '请完成滑动验证' ) );
            }
        }

        // 验证注册协议
        if ( $this->get_option( 'register_agreement_enable', '' ) ) {
            $agreement = isset( $_POST['agreement'] ) ? $_POST['agreement'] : '';
            if ( empty( $agreement ) ) {
                wp_send_json_error( array( 'message' => '请阅读并同意用户服务协议' ) );
            }
        }

        // 检查用户名和邮箱是否已存在
        if ( username_exists( $username ) ) {
            wp_send_json_error( array( 'message' => '该用户名已被注册' ) );
        }

        if ( email_exists( $email ) ) {
            wp_send_json_error( array( 'message' => '该邮箱已被注册' ) );
        }

        // 创建用户
        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( array( 'message' => '注册失败：' . $user_id->get_error_message() ) );
        }

        // 自动登录
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id, true );

        $redirect = $this->get_option( 'register_redirect_url', '' );
        if ( empty( $redirect ) ) {
            $redirect = home_url();
        }

        wp_send_json_success( array(
            'message' => '注册成功，正在跳转...',
            'redirect' => $redirect
        ) );
    }

    /**
     * 检查密码强度
     */
    private function check_password_strength( $password, $required_strength ) {
        $length = strlen( $password );
        
        if ( $required_strength === 'weak' ) {
            if ( $length < 6 ) {
                return array( 'valid' => false, 'message' => '密码至少需要6个字符' );
            }
        } elseif ( $required_strength === 'medium' ) {
            if ( $length < 8 ) {
                return array( 'valid' => false, 'message' => '密码至少需要8个字符' );
            }
            if ( ! preg_match( '/[A-Za-z]/', $password ) || ! preg_match( '/[0-9]/', $password ) ) {
                return array( 'valid' => false, 'message' => '密码必须包含字母和数字' );
            }
        } elseif ( $required_strength === 'strong' ) {
            if ( $length < 10 ) {
                return array( 'valid' => false, 'message' => '密码至少需要10个字符' );
            }
            if ( ! preg_match( '/[A-Z]/', $password ) ) {
                return array( 'valid' => false, 'message' => '密码必须包含大写字母' );
            }
            if ( ! preg_match( '/[a-z]/', $password ) ) {
                return array( 'valid' => false, 'message' => '密码必须包含小写字母' );
            }
            if ( ! preg_match( '/[0-9]/', $password ) ) {
                return array( 'valid' => false, 'message' => '密码必须包含数字' );
            }
            if ( ! preg_match( '/[!@#$%^&*(),.?":{}|<>]/', $password ) ) {
                return array( 'valid' => false, 'message' => '密码必须包含特殊字符' );
            }
        }

        return array( 'valid' => true, 'message' => '' );
    }

    /**
     * AJAX 找回密码
     */
    public function ajax_forgot_password() {
        check_ajax_referer( 'developer_starter_auth', 'nonce' );

        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

        if ( empty( $email ) || ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => '请输入有效的邮箱地址' ) );
        }

        // 验证滑动验证码
        if ( $this->get_option( 'auth_captcha_enable', '' ) ) {
            $captcha = isset( $_POST['captcha_verified'] ) ? $_POST['captcha_verified'] : '';
            if ( $captcha !== 'true' ) {
                wp_send_json_error( array( 'message' => '请完成滑动验证' ) );
            }
        }

        $user = get_user_by( 'email', $email );
        if ( ! $user ) {
            // 出于安全考虑，不透露邮箱是否存在
            wp_send_json_success( array( 'message' => '如果该邮箱已注册，您将收到重置密码的邮件' ) );
        }

        // 生成重置链接
        $key = get_password_reset_key( $user );
        if ( is_wp_error( $key ) ) {
            wp_send_json_error( array( 'message' => '发送重置邮件失败，请稍后再试' ) );
        }

        // 获取重置密码页面
        $reset_page_id = $this->get_option( 'forgot_password_page_id', '' );
        $reset_url = $reset_page_id ? add_query_arg( array( 'action' => 'reset', 'key' => $key, 'login' => rawurlencode( $user->user_login ) ), get_permalink( $reset_page_id ) ) : '';

        // 发送邮件
        $site_name = get_bloginfo( 'name' );
        $subject = "[{$site_name}] 密码重置请求";
        $message = "您好，{$user->display_name}！\n\n";
        $message .= "我们收到了您的密码重置请求。如果这不是您本人的操作，请忽略此邮件。\n\n";
        $message .= "点击以下链接重置您的密码：\n";
        $message .= $reset_url . "\n\n";
        $message .= "此链接将在24小时后失效。\n\n";
        $message .= "—— {$site_name}";

        $sent = wp_mail( $email, $subject, $message );

        if ( $sent ) {
            wp_send_json_success( array( 'message' => '重置密码邮件已发送，请查收您的邮箱' ) );
        } else {
            wp_send_json_error( array( 'message' => '邮件发送失败，请稍后再试' ) );
        }
    }

    /**
     * AJAX 重置密码
     */
    public function ajax_reset_password() {
        check_ajax_referer( 'developer_starter_auth', 'nonce' );

        $key = isset( $_POST['key'] ) ? sanitize_text_field( $_POST['key'] ) : '';
        $login = isset( $_POST['login'] ) ? sanitize_user( $_POST['login'] ) : '';
        $password = isset( $_POST['password'] ) ? $_POST['password'] : '';
        $password_confirm = isset( $_POST['password_confirm'] ) ? $_POST['password_confirm'] : '';

        if ( empty( $key ) || empty( $login ) ) {
            wp_send_json_error( array( 'message' => '无效的重置链接' ) );
        }

        if ( empty( $password ) ) {
            wp_send_json_error( array( 'message' => '请输入新密码' ) );
        }

        if ( $password !== $password_confirm ) {
            wp_send_json_error( array( 'message' => '两次输入的密码不一致' ) );
        }

        // 验证密码强度
        $strength = $this->get_option( 'password_strength', 'medium' );
        $strength_check = $this->check_password_strength( $password, $strength );
        if ( ! $strength_check['valid'] ) {
            wp_send_json_error( array( 'message' => $strength_check['message'] ) );
        }

        $user = check_password_reset_key( $key, $login );

        if ( is_wp_error( $user ) ) {
            wp_send_json_error( array( 'message' => '重置链接已失效，请重新申请' ) );
        }

        // 重置密码
        reset_password( $user, $password );

        $login_page_id = $this->get_option( 'login_page_id', '' );
        $redirect = $login_page_id ? get_permalink( $login_page_id ) : wp_login_url();

        wp_send_json_success( array(
            'message' => '密码重置成功，请使用新密码登录',
            'redirect' => $redirect
        ) );
    }
    
    /**
     * AJAX 上传用户头像
     */
    public function ajax_upload_avatar() {
        // 验证用户登录状态
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => '请先登录' ) );
        }
        
        // 检查是否启用头像上传
        $avatar_upload_enable = $this->get_option( 'user_avatar_upload_enable', '' );
        if ( ! $avatar_upload_enable ) {
            wp_send_json_error( array( 'message' => '头像上传功能未启用' ) );
        }
        
        // 验证nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'developer_starter_avatar_upload' ) ) {
            wp_send_json_error( array( 'message' => '安全验证失败，请刷新页面重试' ) );
        }
        
        // 检查文件是否上传
        if ( empty( $_FILES['avatar'] ) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK ) {
            wp_send_json_error( array( 'message' => '文件上传失败，请重试' ) );
        }
        
        $file = $_FILES['avatar'];
        
        // 验证文件类型
        $allowed_types = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
        $finfo = finfo_open( FILEINFO_MIME_TYPE );
        $file_type = finfo_file( $finfo, $file['tmp_name'] );
        finfo_close( $finfo );
        
        if ( ! in_array( $file_type, $allowed_types ) ) {
            wp_send_json_error( array( 'message' => '只允许上传 JPG、PNG、GIF、WebP 格式的图片' ) );
        }
        
        // 验证文件大小（最大2MB）
        $max_size = 2 * 1024 * 1024;
        if ( $file['size'] > $max_size ) {
            wp_send_json_error( array( 'message' => '图片大小不能超过 2MB' ) );
        }
        
        // 使用WordPress媒体库处理上传
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        
        // 设置文件名（使用用户ID作为前缀）
        $user_id = get_current_user_id();
        $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
        $new_filename = 'avatar-' . $user_id . '-' . time() . '.' . $ext;
        $file['name'] = $new_filename;
        
        // 上传到媒体库
        $attachment_id = media_handle_sideload( $file, 0, null, array(
            'test_form' => false,
            'test_size' => true,
        ) );
        
        if ( is_wp_error( $attachment_id ) ) {
            wp_send_json_error( array( 'message' => '头像上传失败：' . $attachment_id->get_error_message() ) );
        }
        
        // 获取附件URL
        $avatar_url = wp_get_attachment_url( $attachment_id );
        
        // 删除旧头像附件（可选：清理存储空间）
        $old_avatar_id = get_user_meta( $user_id, 'custom_avatar_attachment_id', true );
        if ( $old_avatar_id ) {
            wp_delete_attachment( $old_avatar_id, true );
        }
        
        // 保存到用户meta
        update_user_meta( $user_id, 'custom_avatar', $avatar_url );
        update_user_meta( $user_id, 'custom_avatar_attachment_id', $attachment_id );
        
        wp_send_json_success( array(
            'message' => '头像上传成功！',
            'avatar_url' => $avatar_url
        ) );
    }
}
