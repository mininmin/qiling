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

    public function __construct() {
        add_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
        add_filter( 'wp_mail_from', array( $this, 'set_from_email' ) );
        add_filter( 'wp_mail_from_name', array( $this, 'set_from_name' ) );
    }

    public function configure_smtp( $phpmailer ) {
        $smtp_host = developer_starter_get_option( 'smtp_host', '' );
        
        if ( empty( $smtp_host ) ) {
            return;
        }
        
        $smtp_port = developer_starter_get_option( 'smtp_port', '465' );
        $smtp_secure = developer_starter_get_option( 'smtp_secure', 'ssl' );
        $smtp_username = developer_starter_get_option( 'smtp_username', '' );
        $smtp_password = developer_starter_get_option( 'smtp_password', '' );
        
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
