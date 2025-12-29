<?php
/**
 * 模块管理器类
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Module_Manager {

    private static $instance = null;
    private $modules = array();

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_default_modules' ), 5 );
    }

    public function register_default_modules() {
        $this->register_module( new Modules\Banner_Module() );
        $this->register_module( new Modules\Services_Module() );
        $this->register_module( new Modules\Features_Module() );
        $this->register_module( new Modules\Clients_Module() );
        $this->register_module( new Modules\Stats_Module() );
        $this->register_module( new Modules\CTA_Module() );
        $this->register_module( new Modules\Image_Text_Module() );
        $this->register_module( new Modules\Columns_Module() );
        $this->register_module( new Modules\Timeline_Module() );
        $this->register_module( new Modules\FAQ_Module() );
        $this->register_module( new Modules\Contact_Module() );
        $this->register_module( new Modules\News_Module() );
        $this->register_module( new Modules\Products_Module() );
        $this->register_module( new Modules\Cases_Module() );
        $this->register_module( new Modules\Downloads_Module() );
    }

    public function register_module( Module_Base $module ) {
        $this->modules[ $module->get_id() ] = $module;
    }

    public function get_module( $id ) {
        return isset( $this->modules[ $id ] ) ? $this->modules[ $id ] : null;
    }

    public function get_all_modules() {
        return $this->modules;
    }

    public function render_module( $module_id, $data = array() ) {
        $module = $this->get_module( $module_id );
        if ( $module ) {
            $data = wp_parse_args( $data, $module->get_default_data() );
            $module->render( $data );
        }
    }

    public function render_page_modules( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $modules = get_post_meta( $post_id, '_developer_starter_modules', true );
        if ( empty( $modules ) || ! is_array( $modules ) ) {
            return;
        }

        foreach ( $modules as $module_data ) {
            $module_id = isset( $module_data['type'] ) ? $module_data['type'] : '';
            $data = isset( $module_data['data'] ) ? $module_data['data'] : array();
            $this->render_module( $module_id, $data );
        }
    }
}
