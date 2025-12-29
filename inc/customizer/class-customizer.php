<?php
/**
 * Customizer Class
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

namespace Developer_Starter\Customizer;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Customizer {

    public function __construct() {
        add_action( 'customize_register', array( $this, 'register' ) );
    }

    public function register( $wp_customize ) {
        // Colors Section
        $wp_customize->add_section( 'developer_starter_colors', array(
            'title'    => __( '主题颜色', 'developer-starter' ),
            'priority' => 30,
        ) );

        $wp_customize->add_setting( 'developer_starter_primary_color', array(
            'default'           => '#2563eb',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'developer_starter_primary_color', array(
            'label'   => __( '主色调', 'developer-starter' ),
            'section' => 'developer_starter_colors',
        ) ) );

        // Header Section
        $wp_customize->add_section( 'developer_starter_header', array(
            'title'    => __( '头部设置', 'developer-starter' ),
            'priority' => 40,
        ) );

        $wp_customize->add_setting( 'developer_starter_header_sticky', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );
        $wp_customize->add_control( 'developer_starter_header_sticky', array(
            'type'    => 'checkbox',
            'label'   => __( '启用固定头部', 'developer-starter' ),
            'section' => 'developer_starter_header',
        ) );

        // Footer Section
        $wp_customize->add_section( 'developer_starter_footer', array(
            'title'    => __( '页脚设置', 'developer-starter' ),
            'priority' => 50,
        ) );

        $wp_customize->add_setting( 'developer_starter_copyright', array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        ) );
        $wp_customize->add_control( 'developer_starter_copyright', array(
            'type'    => 'textarea',
            'label'   => __( '版权信息', 'developer-starter' ),
            'section' => 'developer_starter_footer',
        ) );
    }
}

new Customizer();
