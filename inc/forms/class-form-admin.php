<?php
/**
 * 表单后台管理类
 *
 * @package Developer_Starter
 * @since 1.0.2
 */

namespace Developer_Starter\Forms;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Form_Admin {

    private $manager;

    public function __construct() {
        $this->manager = Form_Manager::get_instance();
        
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_developer_save_form', array( $this, 'ajax_save_form' ) );
        add_action( 'wp_ajax_developer_delete_form', array( $this, 'ajax_delete_form' ) );
        add_action( 'wp_ajax_developer_delete_entry', array( $this, 'ajax_delete_entry' ) );
        add_action( 'wp_ajax_developer_export_entries', array( $this, 'ajax_export_entries' ) );
    }

    /**
     * 添加菜单
     */
    public function add_menu() {
        $unread = $this->manager->get_unread_count();
        $badge = $unread > 0 ? ' <span class="awaiting-mod">' . $unread . '</span>' : '';
        
        add_submenu_page(
            'developer-starter-settings',
            '表单管理',
            '表单管理' . $badge,
            'manage_options',
            'developer-starter-forms',
            array( $this, 'render_page' )
        );
    }

    /**
     * 加载资源
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'developer-starter-forms' ) === false ) {
            return;
        }
        
        wp_enqueue_style(
            'developer-starter-admin-forms',
            DEVELOPER_STARTER_ASSETS . '/css/admin-forms.css',
            array(),
            DEVELOPER_STARTER_VERSION
        );
        
        wp_enqueue_script(
            'developer-starter-admin-forms',
            DEVELOPER_STARTER_ASSETS . '/js/admin-forms.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            DEVELOPER_STARTER_VERSION,
            true
        );
        
        wp_localize_script( 'developer-starter-admin-forms', 'developerFormsData', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'developer_forms_nonce' ),
        ) );
    }

    /**
     * 渲染页面
     */
    public function render_page() {
        $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
        $form_id = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;
        
        echo '<div class="wrap developer-forms-wrap">';
        
        switch ( $action ) {
            case 'new':
            case 'edit':
                $this->render_form_editor( $form_id );
                break;
            case 'entries':
                $this->render_entries( $form_id );
                break;
            default:
                $this->render_form_list();
        }
        
        echo '</div>';
    }

    /**
     * 渲染表单列表
     */
    private function render_form_list() {
        $forms = $this->manager->get_forms();
        ?>
        <h1 class="wp-heading-inline">表单管理</h1>
        <a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms&action=new' ); ?>" class="page-title-action">新建表单</a>
        <hr class="wp-header-end">
        
        <div class="forms-list-wrap">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>表单标题</th>
                        <th width="120">别名</th>
                        <th width="80">字段数</th>
                        <th width="100">提交数</th>
                        <th width="80">状态</th>
                        <th width="180">创建时间</th>
                        <th width="180">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $forms ) ) : ?>
                        <tr><td colspan="8">暂无表单，<a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms&action=new' ); ?>">创建第一个表单</a></td></tr>
                    <?php else : ?>
                        <?php foreach ( $forms as $form ) : 
                            $fields = json_decode( $form->fields, true );
                            $field_count = is_array( $fields ) ? count( $fields ) : 0;
                            $entry_count = $this->manager->get_entries_count( $form->id );
                            $unread = $this->manager->get_unread_count( $form->id );
                        ?>
                        <tr>
                            <td><?php echo $form->id; ?></td>
                            <td>
                                <strong><a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms&action=edit&form_id=' . $form->id ); ?>"><?php echo esc_html( $form->title ); ?></a></strong>
                                <div class="row-actions">
                                    <span><a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms&action=edit&form_id=' . $form->id ); ?>">编辑</a></span> | 
                                    <span><a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms&action=entries&form_id=' . $form->id ); ?>">查看数据</a></span> | 
                                    <span class="delete"><a href="#" class="delete-form" data-id="<?php echo $form->id; ?>">删除</a></span>
                                </div>
                            </td>
                            <td><code>[developer_form id="<?php echo $form->id; ?>"]</code></td>
                            <td><?php echo $field_count; ?> 个</td>
                            <td>
                                <?php echo $entry_count; ?>
                                <?php if ( $unread > 0 ) : ?>
                                    <span class="unread-badge"><?php echo $unread; ?> 未读</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-<?php echo $form->status; ?>"><?php echo $form->status === 'active' ? '启用' : '禁用'; ?></span>
                            </td>
                            <td><?php echo $form->created_at; ?></td>
                            <td>
                                <a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms&action=edit&form_id=' . $form->id ); ?>" class="button button-small">编辑</a>
                                <a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms&action=entries&form_id=' . $form->id ); ?>" class="button button-small">数据</a>
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
     * 渲染表单编辑器
     */
    private function render_form_editor( $form_id ) {
        $form = $form_id ? $this->manager->get_form( $form_id ) : null;
        $is_new = ! $form;
        
        $title = $form ? $form->title : '';
        $slug = $form ? $form->slug : '';
        $fields = $form ? json_decode( $form->fields, true ) : array();
        $notify_emails = $form ? $form->notify_emails : '';
        $submit_button = $form ? $form->submit_button : '提交';
        $success_message = $form ? $form->success_message : '提交成功！';
        $limit_per_ip = $form ? $form->limit_per_ip : 5;
        $limit_interval = $form ? $form->limit_interval : 60;
        $status = $form ? $form->status : 'active';
        ?>
        <h1 class="wp-heading-inline"><?php echo $is_new ? '新建表单' : '编辑表单'; ?></h1>
        <a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms' ); ?>" class="page-title-action">返回列表</a>
        <hr class="wp-header-end">
        
        <form id="form-editor" class="form-editor" data-form-id="<?php echo $form_id; ?>">
            <div class="form-editor-main">
                <div class="form-section">
                    <h3>基本信息</h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="form-title">表单标题 <span class="required">*</span></label></th>
                            <td><input type="text" id="form-title" name="title" value="<?php echo esc_attr( $title ); ?>" class="regular-text" required /></td>
                        </tr>
                        <tr>
                            <th><label for="form-slug">表单别名</label></th>
                            <td>
                                <input type="text" id="form-slug" name="slug" value="<?php echo esc_attr( $slug ); ?>" class="regular-text" />
                                <p class="description">用于短代码调用，如不填写将自动生成</p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="form-section">
                    <h3>表单字段</h3>
                    <p class="description">拖拽字段进行排序，点击字段进行编辑</p>
                    
                    <div class="fields-builder">
                        <div class="fields-list" id="fields-list">
                            <?php if ( ! empty( $fields ) ) : ?>
                                <?php foreach ( $fields as $index => $field ) : ?>
                                    <?php $this->render_field_item( $field, $index ); ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="add-field-buttons">
                            <button type="button" class="button add-field" data-type="text">+ 文本框</button>
                            <button type="button" class="button add-field" data-type="email">+ 邮箱</button>
                            <button type="button" class="button add-field" data-type="tel">+ 电话</button>
                            <button type="button" class="button add-field" data-type="textarea">+ 多行文本</button>
                            <button type="button" class="button add-field" data-type="select">+ 下拉选择</button>
                            <button type="button" class="button add-field" data-type="radio">+ 单选</button>
                            <button type="button" class="button add-field" data-type="checkbox">+ 多选</button>
                            <button type="button" class="button add-field" data-type="date">+ 日期</button>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>表单设置</h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="submit-button">提交按钮文字</label></th>
                            <td><input type="text" id="submit-button" name="submit_button" value="<?php echo esc_attr( $submit_button ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="success-message">成功提示信息</label></th>
                            <td><textarea id="success-message" name="success_message" rows="2" class="large-text"><?php echo esc_textarea( $success_message ); ?></textarea></td>
                        </tr>
                    </table>
                </div>
                
                <div class="form-section">
                    <h3>通知设置</h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="notify-emails">通知邮箱</label></th>
                            <td>
                                <input type="text" id="notify-emails" name="notify_emails" value="<?php echo esc_attr( $notify_emails ); ?>" class="large-text" placeholder="多个邮箱用逗号分隔" />
                                <p class="description">留空则使用管理员邮箱 (<?php echo get_option( 'admin_email' ); ?>)</p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="form-section">
                    <h3>防刷设置</h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="limit-per-ip">IP 提交限制</label></th>
                            <td>
                                <input type="number" id="limit-per-ip" name="limit_per_ip" value="<?php echo esc_attr( $limit_per_ip ); ?>" min="1" max="100" class="small-text" /> 次
                            </td>
                        </tr>
                        <tr>
                            <th><label for="limit-interval">限制时间窗口</label></th>
                            <td>
                                <input type="number" id="limit-interval" name="limit_interval" value="<?php echo esc_attr( $limit_interval ); ?>" min="1" max="1440" class="small-text" /> 分钟
                                <p class="description">在指定时间内，同一 IP 最多提交指定次数</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="form-editor-sidebar">
                <div class="sidebar-box">
                    <h4>发布</h4>
                    <div class="sidebar-content">
                        <p>
                            <label>状态：</label>
                            <select name="status" id="form-status">
                                <option value="active" <?php selected( $status, 'active' ); ?>>启用</option>
                                <option value="inactive" <?php selected( $status, 'inactive' ); ?>>禁用</option>
                            </select>
                        </p>
                        <button type="submit" class="button button-primary button-large" id="save-form">保存表单</button>
                    </div>
                </div>
                
                <div class="sidebar-box">
                    <h4>调用方式</h4>
                    <div class="sidebar-content">
                        <p><strong>短代码：</strong></p>
                        <code id="shortcode-preview">[developer_form id="<?php echo $form_id ?: 'ID'; ?>"]</code>
                        <p style="margin-top: 15px;"><strong>PHP 函数：</strong></p>
                        <code>&lt;?php developer_starter_render_form( <?php echo $form_id ?: 'ID'; ?> ); ?&gt;</code>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- 字段编辑模板 -->
        <script type="text/html" id="field-template">
            <div class="field-item" data-index="{{index}}">
                <div class="field-header">
                    <span class="field-drag">☰</span>
                    <span class="field-type-badge">{{type_label}}</span>
                    <span class="field-label">{{label}}</span>
                    <span class="field-required">{{required_badge}}</span>
                    <span class="field-actions">
                        <button type="button" class="edit-field" title="编辑">✏️</button>
                        <button type="button" class="delete-field" title="删除">🗑️</button>
                    </span>
                </div>
                <div class="field-editor" style="display: none;">
                    <table class="field-settings">
                        <tr>
                            <td width="80"><label>字段名</label></td>
                            <td><input type="text" class="field-name" value="{{name}}" /></td>
                        </tr>
                        <tr>
                            <td><label>标签</label></td>
                            <td><input type="text" class="field-label-input" value="{{label}}" /></td>
                        </tr>
                        <tr>
                            <td><label>占位符</label></td>
                            <td><input type="text" class="field-placeholder" value="{{placeholder}}" /></td>
                        </tr>
                        <tr class="options-row" style="{{options_display}}">
                            <td><label>选项</label></td>
                            <td><textarea class="field-options" rows="3" placeholder="每行一个选项">{{options}}</textarea></td>
                        </tr>
                        <tr>
                            <td><label>宽度</label></td>
                            <td>
                                <select class="field-width">
                                    <option value="100" {{width_100}}>100%</option>
                                    <option value="50" {{width_50}}>50%</option>
                                    <option value="33" {{width_33}}>33%</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>必填</label></td>
                            <td><input type="checkbox" class="field-required-input" {{required_checked}} /></td>
                        </tr>
                    </table>
                    <input type="hidden" class="field-type" value="{{type}}" />
                </div>
            </div>
        </script>
        <?php
    }

    /**
     * 渲染单个字段项
     */
    private function render_field_item( $field, $index ) {
        $type_labels = array(
            'text' => '文本',
            'email' => '邮箱',
            'tel' => '电话',
            'textarea' => '多行',
            'select' => '下拉',
            'radio' => '单选',
            'checkbox' => '多选',
            'date' => '日期',
        );
        
        $type = $field['type'] ?? 'text';
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? '';
        $placeholder = $field['placeholder'] ?? '';
        $required = ! empty( $field['required'] );
        $width = $field['width'] ?? '100';
        $options = isset( $field['options'] ) && is_array( $field['options'] ) ? implode( "\n", $field['options'] ) : '';
        $has_options = in_array( $type, array( 'select', 'radio', 'checkbox' ) );
        ?>
        <div class="field-item" data-index="<?php echo $index; ?>">
            <div class="field-header">
                <span class="field-drag">☰</span>
                <span class="field-type-badge"><?php echo $type_labels[ $type ] ?? $type; ?></span>
                <span class="field-label"><?php echo esc_html( $label ?: '未命名字段' ); ?></span>
                <?php if ( $required ) : ?>
                    <span class="field-required-star">*</span>
                <?php endif; ?>
                <span class="field-actions">
                    <button type="button" class="edit-field" title="编辑">✏️</button>
                    <button type="button" class="delete-field" title="删除">🗑️</button>
                </span>
            </div>
            <div class="field-editor" style="display: none;">
                <table class="field-settings">
                    <tr>
                        <td width="80"><label>字段名</label></td>
                        <td><input type="text" class="field-name" value="<?php echo esc_attr( $name ); ?>" /></td>
                    </tr>
                    <tr>
                        <td><label>标签</label></td>
                        <td><input type="text" class="field-label-input" value="<?php echo esc_attr( $label ); ?>" /></td>
                    </tr>
                    <tr>
                        <td><label>占位符</label></td>
                        <td><input type="text" class="field-placeholder" value="<?php echo esc_attr( $placeholder ); ?>" /></td>
                    </tr>
                    <tr class="options-row" style="<?php echo $has_options ? '' : 'display:none'; ?>">
                        <td><label>选项</label></td>
                        <td><textarea class="field-options" rows="3" placeholder="每行一个选项"><?php echo esc_textarea( $options ); ?></textarea></td>
                    </tr>
                    <tr>
                        <td><label>宽度</label></td>
                        <td>
                            <select class="field-width">
                                <option value="100" <?php selected( $width, '100' ); ?>>100%</option>
                                <option value="50" <?php selected( $width, '50' ); ?>>50%</option>
                                <option value="33" <?php selected( $width, '33' ); ?>>33%</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>必填</label></td>
                        <td><input type="checkbox" class="field-required-input" <?php checked( $required ); ?> /></td>
                    </tr>
                </table>
                <input type="hidden" class="field-type" value="<?php echo esc_attr( $type ); ?>" />
            </div>
        </div>
        <?php
    }

    /**
     * 渲染提交数据列表
     */
    private function render_entries( $form_id ) {
        $form = $this->manager->get_form( $form_id );
        if ( ! $form ) {
            echo '<div class="notice notice-error"><p>表单不存在</p></div>';
            return;
        }
        
        $page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
        $entries = $this->manager->get_entries( $form_id, array( 'page' => $page ) );
        $total = $this->manager->get_entries_count( $form_id );
        $fields = json_decode( $form->fields, true );
        ?>
        <h1 class="wp-heading-inline"><?php echo esc_html( $form->title ); ?> - 提交数据</h1>
        <a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms' ); ?>" class="page-title-action">返回列表</a>
        <a href="<?php echo admin_url( 'admin.php?page=developer-starter-forms&action=edit&form_id=' . $form_id ); ?>" class="page-title-action">编辑表单</a>
        <button type="button" class="page-title-action export-entries" data-form-id="<?php echo $form_id; ?>">导出 CSV</button>
        <hr class="wp-header-end">
        
        <p>共 <?php echo $total; ?> 条数据</p>
        
        <table class="wp-list-table widefat fixed striped entries-table">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <?php foreach ( array_slice( $fields, 0, 4 ) as $field ) : ?>
                        <th><?php echo esc_html( $field['label'] ?? $field['name'] ); ?></th>
                    <?php endforeach; ?>
                    <th width="130">提交时间</th>
                    <th width="100">状态</th>
                    <th width="80">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $entries ) ) : ?>
                    <tr><td colspan="<?php echo min( count( $fields ), 4 ) + 4; ?>">暂无数据</td></tr>
                <?php else : ?>
                    <?php foreach ( $entries as $entry ) : 
                        $data = json_decode( $entry->data, true );
                    ?>
                    <tr class="<?php echo $entry->is_read ? '' : 'unread'; ?>">
                        <td><?php echo $entry->id; ?></td>
                        <?php foreach ( array_slice( $fields, 0, 4 ) as $field ) : 
                            $value = $data[ $field['name'] ] ?? '';
                            if ( is_array( $value ) ) $value = implode( ', ', $value );
                        ?>
                            <td><?php echo esc_html( mb_substr( $value, 0, 50 ) ); ?></td>
                        <?php endforeach; ?>
                        <td><?php echo $entry->created_at; ?></td>
                        <td><?php echo $entry->is_read ? '已读' : '<span class="unread-badge">未读</span>'; ?></td>
                        <td>
                            <a href="#" class="view-entry" data-id="<?php echo $entry->id; ?>" data-content="<?php echo esc_attr( $entry->data ); ?>">查看</a> | 
                            <a href="#" class="delete-entry" data-id="<?php echo $entry->id; ?>">删除</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- 详情弹窗 -->
        <div id="entry-modal" class="entry-modal" style="display:none;">
            <div class="entry-modal-content">
                <div class="entry-modal-header">
                    <h3>提交详情</h3>
                    <button type="button" class="entry-modal-close">&times;</button>
                </div>
                <div class="entry-modal-body" id="entry-detail"></div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX 保存表单
     */
    public function ajax_save_form() {
        check_ajax_referer( 'developer_forms_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '权限不足' ) );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
        
        $data = array(
            'id' => $form_id,
            'title' => sanitize_text_field( $_POST['title'] ?? '' ),
            'slug' => sanitize_title( $_POST['slug'] ?? '' ),
            'fields' => $_POST['fields'] ?? '[]',
            'notify_emails' => sanitize_text_field( $_POST['notify_emails'] ?? '' ),
            'submit_button' => sanitize_text_field( $_POST['submit_button'] ?? '提交' ),
            'success_message' => wp_kses_post( $_POST['success_message'] ?? '' ),
            'limit_per_ip' => absint( $_POST['limit_per_ip'] ?? 5 ),
            'limit_interval' => absint( $_POST['limit_interval'] ?? 60 ),
            'status' => sanitize_text_field( $_POST['status'] ?? 'active' ),
        );
        
        // 自动生成别名
        if ( empty( $data['slug'] ) ) {
            $data['slug'] = sanitize_title( $data['title'] ) . '-' . time();
        }
        
        $id = $this->manager->save_form( $data );
        
        if ( $id ) {
            wp_send_json_success( array( 
                'message' => '保存成功',
                'form_id' => $id,
                'redirect' => admin_url( 'admin.php?page=developer-starter-forms&action=edit&form_id=' . $id ),
            ) );
        } else {
            wp_send_json_error( array( 'message' => '保存失败' ) );
        }
    }

    /**
     * AJAX 删除表单
     */
    public function ajax_delete_form() {
        check_ajax_referer( 'developer_forms_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '权限不足' ) );
        }
        
        $form_id = absint( $_POST['form_id'] ?? 0 );
        
        if ( $this->manager->delete_form( $form_id ) ) {
            wp_send_json_success( array( 'message' => '删除成功' ) );
        } else {
            wp_send_json_error( array( 'message' => '删除失败' ) );
        }
    }

    /**
     * AJAX 删除提交
     */
    public function ajax_delete_entry() {
        check_ajax_referer( 'developer_forms_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '权限不足' ) );
        }
        
        $entry_id = absint( $_POST['entry_id'] ?? 0 );
        
        if ( $this->manager->delete_entry( $entry_id ) ) {
            wp_send_json_success( array( 'message' => '删除成功' ) );
        } else {
            wp_send_json_error( array( 'message' => '删除失败' ) );
        }
    }

    /**
     * AJAX 导出数据
     */
    public function ajax_export_entries() {
        check_ajax_referer( 'developer_forms_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( '权限不足' );
        }
        
        $form_id = absint( $_GET['form_id'] ?? 0 );
        $form = $this->manager->get_form( $form_id );
        
        if ( ! $form ) {
            wp_die( '表单不存在' );
        }
        
        $entries = $this->manager->get_entries( $form_id, array( 'per_page' => 9999 ) );
        $fields = json_decode( $form->fields, true );
        
        // CSV 头
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=' . $form->slug . '-' . date( 'Y-m-d' ) . '.csv' );
        
        $output = fopen( 'php://output', 'w' );
        
        // BOM for Excel
        fprintf( $output, chr(0xEF) . chr(0xBB) . chr(0xBF) );
        
        // 表头
        $headers = array( 'ID', '提交时间', 'IP地址' );
        foreach ( $fields as $field ) {
            $headers[] = $field['label'] ?? $field['name'];
        }
        fputcsv( $output, $headers );
        
        // 数据
        foreach ( $entries as $entry ) {
            $data = json_decode( $entry->data, true );
            $row = array( $entry->id, $entry->created_at, $entry->ip_address );
            
            foreach ( $fields as $field ) {
                $value = $data[ $field['name'] ] ?? '';
                if ( is_array( $value ) ) {
                    $value = implode( ', ', $value );
                }
                $row[] = $value;
            }
            
            fputcsv( $output, $row );
        }
        
        fclose( $output );
        exit;
    }
}
