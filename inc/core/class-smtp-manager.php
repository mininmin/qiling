<?php
/**
 * SMTP Manager Class - 邮件发送配置
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SMTP_Manager {

    /**
     * 加密盐值
     */
    private static $salt = 'developer_starter_smtp_v1';

    public function __construct() {
        add_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
        add_filter( 'wp_mail_from', array( $this, 'set_from_email' ) );
        add_filter( 'wp_mail_from_name', array( $this, 'set_from_name' ) );
    }

    /**
     * 加密密码
     *
     * @param string $password 明文密码
     * @return string 加密后的密码
     */
    public static function encrypt_password( $password ) {
        if ( empty( $password ) ) {
            return '';
        }
        // 如果已经是加密格式，直接返回
        if ( strpos( $password, 'enc:' ) === 0 ) {
            return $password;
        }
        $encrypted = base64_encode( self::$salt . '|' . $password );
        return 'enc:' . $encrypted;
    }

    /**
     * 解密密码
     *
     * @param string $encrypted 加密的密码
     * @return string 明文密码
     */
    public static function decrypt_password( $encrypted ) {
        if ( empty( $encrypted ) ) {
            return '';
        }
        // 如果不是加密格式，直接返回（兼容旧数据）
        if ( strpos( $encrypted, 'enc:' ) !== 0 ) {
            return $encrypted;
        }
        $encrypted = substr( $encrypted, 4 ); // 移除 'enc:' 前缀
        $decoded = base64_decode( $encrypted );
        if ( $decoded === false ) {
            return '';
        }
        $parts = explode( '|', $decoded, 2 );
        if ( count( $parts ) !== 2 || $parts[0] !== self::$salt ) {
            return '';
        }
        return $parts[1];
    }

    public function configure_smtp( $phpmailer ) {
        $smtp_host = developer_starter_get_option( 'smtp_host', '' );
        
        if ( empty( $smtp_host ) ) {
            return;
        }
        
        $smtp_port = developer_starter_get_option( 'smtp_port', '465' );
        $smtp_secure = developer_starter_get_option( 'smtp_secure', 'ssl' );
        $smtp_username = developer_starter_get_option( 'smtp_username', '' );
        $smtp_password_encrypted = developer_starter_get_option( 'smtp_password', '' );
        
        // 解密密码
        $smtp_password = self::decrypt_password( $smtp_password_encrypted );
        
        $phpmailer->isSMTP();
        $phpmailer->Host = $smtp_host;
        $phpmailer->Port = intval( $smtp_port );
        $phpmailer->SMTPSecure = $smtp_secure;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $smtp_username;
        $phpmailer->Password = $smtp_password;
        $phpmailer->CharSet = 'UTF-8';
    }

    public function set_from_email( $email ) {
        $smtp_username = developer_starter_get_option( 'smtp_username', '' );
        return ! empty( $smtp_username ) ? $smtp_username : $email;
    }

    public function set_from_name( $name ) {
        $sender_name = developer_starter_get_option( 'smtp_sender_name', '' );
        return ! empty( $sender_name ) ? $sender_name : $name;
    }
}
