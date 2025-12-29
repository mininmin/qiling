<?php
/**
 * Module Base Class
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Module_Base {

    protected $id;
    protected $name;
    protected $description = '';
    protected $icon = 'dashicons-layout';
    protected $category = 'general';
    protected $fields = array();

    abstract public function get_id();
    abstract public function get_name();
    abstract public function render( $data = array() );

    public function get_description() {
        return $this->description;
    }

    public function get_icon() {
        return $this->icon;
    }

    public function get_category() {
        return $this->category;
    }

    public function get_fields() {
        return $this->fields;
    }

    public function get_default_data() {
        $defaults = array();
        foreach ( $this->fields as $field ) {
            $defaults[ $field['id'] ] = isset( $field['default'] ) ? $field['default'] : '';
        }
        return $defaults;
    }

    protected function get_template_part( $template, $data = array() ) {
        $template_path = DEVELOPER_STARTER_DIR . '/template-parts/modules/' . $template . '.php';
        if ( file_exists( $template_path ) ) {
            extract( $data );
            include $template_path;
        }
    }

    protected function add_field( $id, $label, $type = 'text', $default = '', $options = array() ) {
        $this->fields[] = array(
            'id'      => $id,
            'label'   => $label,
            'type'    => $type,
            'default' => $default,
            'options' => $options,
        );
    }
}
