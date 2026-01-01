<?php
/**
 * Contact Module - 联系我们
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Contact_Module extends Module_Base {

    public function __construct() {
        $this->category = 'general';
        $this->icon = 'dashicons-email';
        $this->description = '联系我们模块';
    }

    public function get_id() {
        return 'contact';
    }

    public function get_name() {
        return '联系我们';
    }

    public function get_fields() {
        return array(
            array(
                'id' => 'contact_title',
                'label' => '标题',
                'type' => 'text',
                'default' => '联系我们',
            ),
            array(
                'id' => 'contact_subtitle',
                'label' => '副标题',
                'type' => 'text',
                'default' => '有任何问题？请随时与我们联系',
            ),
            array(
                'id' => 'contact_show_form',
                'label' => '显示留言表单',
                'type' => 'checkbox',
                'default' => '1',
            ),
            array(
                'id' => 'contact_image',
                'label' => '右侧图片',
                'type' => 'image',
                'description' => '不显示表单时显示此图片',
            ),
        );
    }

    public function render( $data = array() ) {
        $title = isset( $data['contact_title'] ) && $data['contact_title'] ? $data['contact_title'] : __( '联系我们', 'developer-starter' );
        $subtitle = isset( $data['contact_subtitle'] ) ? $data['contact_subtitle'] : __( '有任何问题？请随时与我们联系', 'developer-starter' );
        $show_form = isset( $data['contact_show_form'] ) ? $data['contact_show_form'] : '1';
        $right_image = isset( $data['contact_image'] ) ? $data['contact_image'] : '';
        
        $company_name = developer_starter_get_option( 'company_name', '' );
        $phone = developer_starter_get_option( 'company_phone', '' );
        $email = developer_starter_get_option( 'company_email', '' );
        $address = developer_starter_get_option( 'company_address', '' );
        $working_hours = developer_starter_get_option( 'company_working_hours', '' );
        ?>
        <section class="module module-contact section-padding" style="background: #f8fafc;">
            <div class="container">
                <div class="section-header text-center" style="margin-bottom: 50px;">
                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; max-width: 1000px; margin: 0 auto;">
                    
                    <!-- 左侧：联系信息 -->
                    <div class="contact-info" style="display: flex; flex-direction: column; gap: 25px;">
                        <?php if ( $company_name ) : ?>
                            <div style="display: flex; align-items: flex-start; gap: 16px;">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                                </div>
                                <div>
                                    <h4 style="margin: 0 0 4px; color: #94a3b8; font-size: 0.85rem; font-weight: 500;"><?php esc_html_e( '公司名称', 'developer-starter' ); ?></h4>
                                    <p style="margin: 0; color: #1e293b; font-weight: 600;"><?php echo esc_html( $company_name ); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $phone ) : ?>
                            <div style="display: flex; align-items: flex-start; gap: 16px;">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.362 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                                </div>
                                <div>
                                    <h4 style="margin: 0 0 4px; color: #94a3b8; font-size: 0.85rem; font-weight: 500;"><?php esc_html_e( '联系电话', 'developer-starter' ); ?></h4>
                                    <p style="margin: 0;"><a href="tel:<?php echo esc_attr( $phone ); ?>" style="color: #1e293b; font-weight: 600; text-decoration: none;"><?php echo esc_html( $phone ); ?></a></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $email ) : ?>
                            <div style="display: flex; align-items: flex-start; gap: 16px;">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </div>
                                <div>
                                    <h4 style="margin: 0 0 4px; color: #94a3b8; font-size: 0.85rem; font-weight: 500;"><?php esc_html_e( '电子邮箱', 'developer-starter' ); ?></h4>
                                    <p style="margin: 0;"><a href="mailto:<?php echo esc_attr( $email ); ?>" style="color: #1e293b; font-weight: 600; text-decoration: none;"><?php echo esc_html( $email ); ?></a></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $address ) : ?>
                            <div style="display: flex; align-items: flex-start; gap: 16px;">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <div>
                                    <h4 style="margin: 0 0 4px; color: #94a3b8; font-size: 0.85rem; font-weight: 500;"><?php esc_html_e( '公司地址', 'developer-starter' ); ?></h4>
                                    <p style="margin: 0; color: #1e293b; font-weight: 600; line-height: 1.5;"><?php echo esc_html( $address ); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $working_hours ) : ?>
                            <div style="display: flex; align-items: flex-start; gap: 16px;">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <div>
                                    <h4 style="margin: 0 0 4px; color: #94a3b8; font-size: 0.85rem; font-weight: 500;"><?php esc_html_e( '工作时间', 'developer-starter' ); ?></h4>
                                    <p style="margin: 0; color: #1e293b; font-weight: 600;"><?php echo esc_html( $working_hours ); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 右侧：表单或图片 -->
                    <div>
                        <?php if ( $show_form === '1' ) : ?>
                            <form class="contact-form" id="module-contact-form" style="background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">
                                <div style="margin-bottom: 15px;">
                                    <input type="text" name="name" placeholder="<?php esc_attr_e( '您的姓名 *', 'developer-starter' ); ?>" required style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; box-sizing: border-box;" />
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                    <input type="tel" name="phone" placeholder="<?php esc_attr_e( '联系电话 *', 'developer-starter' ); ?>" required style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; box-sizing: border-box;" />
                                    <input type="email" name="email" placeholder="<?php esc_attr_e( '电子邮箱', 'developer-starter' ); ?>" style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; box-sizing: border-box;" />
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <textarea name="message" rows="4" placeholder="<?php esc_attr_e( '您的留言内容... *', 'developer-starter' ); ?>" required style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; resize: vertical; box-sizing: border-box;"></textarea>
                                </div>
                                <input type="hidden" name="action" value="ds_submit_message" />
                                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'ds_message_nonce' ); ?>" />
                                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;"><?php esc_html_e( '提交留言', 'developer-starter' ); ?></button>
                            </form>
                        <?php elseif ( $right_image ) : ?>
                            <img src="<?php echo esc_url( $right_image ); ?>" alt="<?php esc_attr_e( '联系我们', 'developer-starter' ); ?>" style="width: 100%; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.1);" />
                        <?php else : ?>
                            <div style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); border-radius: 16px; padding: 60px 40px; text-align: center; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <div style="font-size: 4rem; margin-bottom: 20px;">💬</div>
                                <h3 style="color: #fff; font-weight: 600; margin: 0 0 10px;"><?php esc_html_e( '期待与您合作', 'developer-starter' ); ?></h3>
                                <p style="color: rgba(255,255,255,0.8); margin: 0;"><?php esc_html_e( '我们将竭诚为您服务', 'developer-starter' ); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
        </section>
        <style>
            @media (max-width: 768px) {
                .module-contact .container > div > div:first-child + div { margin-top: 0; }
                .module-contact .container > div { grid-template-columns: 1fr !important; }
            }
        </style>
        <?php
    }
}
