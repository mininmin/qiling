<?php
/**
 * 表单前端渲染和处理类
 *
 * @package Developer_Starter
 * @since 1.0.2
 */

namespace Developer_Starter\Forms;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Form_Handler {

    private $manager;

    public function __construct() {
        $this->manager = Form_Manager::get_instance();
        
        // 注册短代码
        add_shortcode( 'developer_form', array( $this, 'shortcode' ) );
        
        // AJAX 处理
        add_action( 'wp_ajax_developer_submit_form', array( $this, 'handle_submit' ) );
        add_action( 'wp_ajax_nopriv_developer_submit_form', array( $this, 'handle_submit' ) );
        
        // 加载前端资源
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * 加载前端资源
     */
    public function enqueue_assets() {
        // 检测是否需要加载表单资源
        global $post;
        $needs_form = false;
        
        // 检查内容是否包含表单短代码
        if ( $post && strpos( $post->post_content, '[developer_form' ) !== false ) {
            $needs_form = true;
        }
        
        // 联系页面模板自带表单
        if ( is_page_template( 'templates/template-contact.php' ) ) {
            $needs_form = true;
        }
        
        if ( ! $needs_form ) {
            return;
        }
        
        wp_enqueue_style(
            'developer-starter-forms',
            DEVELOPER_STARTER_ASSETS . '/css/forms.css',
            array(),
            DEVELOPER_STARTER_VERSION
        );
        
        wp_enqueue_script(
            'developer-starter-forms',
            DEVELOPER_STARTER_ASSETS . '/js/forms.js',
            array( 'jquery' ),
            DEVELOPER_STARTER_VERSION,
            true
        );
        
        wp_localize_script( 'developer-starter-forms', 'developerForms', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'developer_form_submit' ),
        ) );
    }

    /**
     * 短代码处理
     */
    public function shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
            'title' => '',
        ), $atts );
        
        $form_id = absint( $atts['id'] );
        if ( ! $form_id ) {
            return '<p class="form-error">请指定表单 ID</p>';
        }
        
        return $this->render_form( $form_id, $atts['title'] );
    }

    /**
     * 渲染表单
     */
    public function render_form( $form_id, $custom_title = '' ) {
        $form = $this->manager->get_form( $form_id );
        
        if ( ! $form || $form->status !== 'active' ) {
            return '<p class="form-error">表单不存在或已禁用</p>';
        }
        
        $fields = json_decode( $form->fields, true );
        if ( empty( $fields ) ) {
            return '<p class="form-error">表单没有配置字段</p>';
        }
        
        $title = ! empty( $custom_title ) ? $custom_title : $form->title;
        
        ob_start();
        ?>
        <div class="developer-form-wrap" id="developer-form-<?php echo $form_id; ?>">
            <?php if ( $title ) : ?>
                <h3 class="form-title"><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>
            
            <form class="developer-form" data-form-id="<?php echo $form_id; ?>">
                <input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />
                
                <div class="form-fields">
                    <?php foreach ( $fields as $field ) : ?>
                        <?php $this->render_field( $field ); ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="form-submit">
                    <button type="submit" class="btn-submit">
                        <span class="btn-text"><?php echo esc_html( $form->submit_button ?: '提交' ); ?></span>
                        <span class="btn-loading" style="display:none;">提交中...</span>
                    </button>
                </div>
                
                <div class="form-message" style="display:none;"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * 渲染单个字段
     */
    private function render_field( $field ) {
        $type = $field['type'] ?? 'text';
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? '';
        $placeholder = $field['placeholder'] ?? '';
        $required = ! empty( $field['required'] );
        $width = $field['width'] ?? '100';
        $options = $field['options'] ?? array();
        $rows = $field['rows'] ?? 4;
        
        $field_class = 'form-field field-' . $type . ' field-width-' . $width;
        $input_id = 'field-' . $name . '-' . wp_rand( 1000, 9999 );
        ?>
        <div class="<?php echo esc_attr( $field_class ); ?>">
            <?php if ( $label ) : ?>
                <label for="<?php echo esc_attr( $input_id ); ?>">
                    <?php echo esc_html( $label ); ?>
                    <?php if ( $required ) : ?>
                        <span class="required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>
            
            <?php
            switch ( $type ) {
                case 'text':
                case 'email':
                case 'tel':
                case 'date':
                    ?>
                    <input 
                        type="<?php echo esc_attr( $type ); ?>" 
                        id="<?php echo esc_attr( $input_id ); ?>"
                        name="<?php echo esc_attr( $name ); ?>" 
                        placeholder="<?php echo esc_attr( $placeholder ); ?>"
                        <?php echo $required ? 'required' : ''; ?>
                    />
                    <?php
                    break;
                    
                case 'textarea':
                    ?>
                    <textarea 
                        id="<?php echo esc_attr( $input_id ); ?>"
                        name="<?php echo esc_attr( $name ); ?>" 
                        placeholder="<?php echo esc_attr( $placeholder ); ?>"
                        rows="<?php echo esc_attr( $rows ); ?>"
                        <?php echo $required ? 'required' : ''; ?>
                    ></textarea>
                    <?php
                    break;
                    
                case 'select':
                    ?>
                    <select 
                        id="<?php echo esc_attr( $input_id ); ?>"
                        name="<?php echo esc_attr( $name ); ?>"
                        <?php echo $required ? 'required' : ''; ?>
                    >
                        <option value=""><?php echo $placeholder ?: '请选择'; ?></option>
                        <?php foreach ( $options as $option ) : ?>
                            <option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php
                    break;
                    
                case 'radio':
                    ?>
                    <div class="radio-group">
                        <?php foreach ( $options as $i => $option ) : ?>
                            <label class="radio-label">
                                <input 
                                    type="radio" 
                                    name="<?php echo esc_attr( $name ); ?>" 
                                    value="<?php echo esc_attr( $option ); ?>"
                                    <?php echo $required && $i === 0 ? 'required' : ''; ?>
                                />
                                <span><?php echo esc_html( $option ); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    break;
                    
                case 'checkbox':
                    ?>
                    <div class="checkbox-group">
                        <?php foreach ( $options as $option ) : ?>
                            <label class="checkbox-label">
                                <input 
                                    type="checkbox" 
                                    name="<?php echo esc_attr( $name ); ?>[]" 
                                    value="<?php echo esc_attr( $option ); ?>"
                                />
                                <span><?php echo esc_html( $option ); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    break;
            }
            ?>
        </div>
        <?php
    }

    /**
     * 处理表单提交
     */
    public function handle_submit() {
        // 验证 nonce
        if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'developer_form_submit' ) ) {
            wp_send_json_error( array( 'message' => '安全验证失败，请刷新页面重试' ) );
        }
        
        $form_id = absint( $_POST['form_id'] ?? 0 );
        $form = $this->manager->get_form( $form_id );
        
        if ( ! $form || $form->status !== 'active' ) {
            wp_send_json_error( array( 'message' => '表单不存在或已禁用' ) );
        }
        
        // 检查提交频率限制
        if ( ! $this->manager->check_rate_limit( $form_id ) ) {
            wp_send_json_error( array( 'message' => '提交过于频繁，请稍后再试' ) );
        }
        
        // 获取字段配置
        $fields = json_decode( $form->fields, true );
        if ( ! is_array( $fields ) ) {
            wp_send_json_error( array( 'message' => '表单配置错误' ) );
        }
        
        // 收集和验证数据
        $entry_data = array();
        $errors = array();
        
        foreach ( $fields as $field ) {
            $name = $field['name'] ?? '';
            $required = ! empty( $field['required'] );
            $type = $field['type'] ?? 'text';
            
            $value = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';
            
            // 清理数据
            if ( is_array( $value ) ) {
                $value = array_map( 'sanitize_text_field', $value );
            } else {
                if ( $type === 'email' ) {
                    $value = sanitize_email( $value );
                } elseif ( $type === 'textarea' ) {
                    $value = sanitize_textarea_field( $value );
                } else {
                    $value = sanitize_text_field( $value );
                }
            }
            
            // 验证必填
            if ( $required ) {
                $is_empty = is_array( $value ) ? empty( $value ) : trim( $value ) === '';
                if ( $is_empty ) {
                    $errors[] = ( $field['label'] ?? $name ) . ' 为必填项';
                }
            }
            
            // 验证邮箱格式
            if ( $type === 'email' && ! empty( $value ) && ! is_email( $value ) ) {
                $errors[] = '请输入有效的邮箱地址';
            }
            
            $entry_data[ $name ] = $value;
        }
        
        if ( ! empty( $errors ) ) {
            wp_send_json_error( array( 'message' => implode( '<br>', $errors ) ) );
        }
        
        // 保存数据
        $entry_id = $this->manager->save_entry( $form_id, $entry_data );
        
        if ( ! $entry_id ) {
            wp_send_json_error( array( 'message' => '保存失败，请稍后重试' ) );
        }
        
        // 记录提交（用于频率限制）
        $this->manager->record_submission( $form_id );
        
        // 发送通知邮件
        $this->manager->send_notification( $form, $entry_data );
        
        wp_send_json_success( array(
            'message' => $form->success_message ?: '提交成功！',
        ) );
    }
}

/**
 * 模板函数：渲染表单
 */
if ( ! function_exists( 'developer_starter_render_form' ) ) {
    function developer_starter_render_form( $form_id, $title = '' ) {
        $handler = new Form_Handler();
        echo $handler->render_form( $form_id, $title );
    }
}
