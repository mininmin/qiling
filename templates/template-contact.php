<?php
/**
 * Template Name: 联系我们
 *
 * @package Developer_Starter
 */

get_header();

$show_form = developer_starter_get_option( 'contact_show_form', '1' );
$show_info = developer_starter_get_option( 'contact_show_info', '1' );
$contact_image = developer_starter_get_option( 'contact_image', '' );

$company_name = developer_starter_get_option( 'company_name', '' );
$phone = developer_starter_get_option( 'company_phone', '' );
$email = developer_starter_get_option( 'company_email', '' );
$address = developer_starter_get_option( 'company_address', '' );
?>

<div class="page-header" style="background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%); padding: 100px 0 60px;">
    <div class="container">
        <h1 class="page-title" style="color: #fff; text-align: center; font-size: 2.5rem; margin: 0;">
            <?php the_title(); ?>
        </h1>
        <p style="text-align: center; color: rgba(255,255,255,0.8); margin-top: 15px; font-size: 1.1rem;">
            随时联系我们，我们将竭诚为您服务
        </p>
    </div>
</div>

<div class="page-content section-padding">
    <div class="container">
        
        <div class="contact-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; max-width: 1100px; margin: 0 auto;">
            
            <!-- 左侧：联系信息 -->
            <?php if ( $show_info ) : ?>
            <div class="contact-info-section">
                <h2 style="font-size: 1.75rem; margin-bottom: 30px; color: #1e293b;">联系方式</h2>
                
                <div class="contact-info-list" style="display: flex; flex-direction: column; gap: 25px;">
                    <?php if ( $company_name ) : ?>
                        <div class="contact-info-item" style="display: flex; align-items: flex-start; gap: 20px;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 5px; color: #64748b; font-size: 0.9rem; font-weight: 500;">公司名称</h4>
                                <p style="margin: 0; color: #1e293b; font-weight: 600;"><?php echo esc_html( $company_name ); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $phone ) : ?>
                        <div class="contact-info-item" style="display: flex; align-items: flex-start; gap: 20px;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.362 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 5px; color: #64748b; font-size: 0.9rem; font-weight: 500;">联系电话</h4>
                                <p style="margin: 0;"><a href="tel:<?php echo esc_attr( $phone ); ?>" style="color: #1e293b; font-weight: 600; text-decoration: none;"><?php echo esc_html( $phone ); ?></a></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $email ) : ?>
                        <div class="contact-info-item" style="display: flex; align-items: flex-start; gap: 20px;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 5px; color: #64748b; font-size: 0.9rem; font-weight: 500;">电子邮箱</h4>
                                <p style="margin: 0;"><a href="mailto:<?php echo esc_attr( $email ); ?>" style="color: #1e293b; font-weight: 600; text-decoration: none;"><?php echo esc_html( $email ); ?></a></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $address ) : ?>
                        <div class="contact-info-item" style="display: flex; align-items: flex-start; gap: 20px;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 5px; color: #64748b; font-size: 0.9rem; font-weight: 500;">公司地址</h4>
                                <p style="margin: 0; color: #1e293b; font-weight: 600; line-height: 1.6;"><?php echo esc_html( $address ); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 右侧：留言表单 或 图片 -->
            <div class="contact-form-section">
                <?php if ( $show_form ) : ?>
                    <h2 style="font-size: 1.75rem; margin-bottom: 30px; color: #1e293b;">在线留言</h2>
                    
                    <!-- 成功/失败提示弹窗 -->
                    <div id="form-toast" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; padding: 40px 60px; border-radius: 16px; text-align: center; box-shadow: 0 25px 80px rgba(0,0,0,0.25);">
                        <div id="toast-icon" style="font-size: 4rem; margin-bottom: 15px;"></div>
                        <div id="toast-text" style="font-size: 1.25rem; font-weight: 600;"></div>
                    </div>
                    <div id="form-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9998;"></div>
                    
                    <form id="ds-contact-form" class="contact-form" style="background: #f8fafc; padding: 30px; border-radius: 16px;">
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">姓名 <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="name" required style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s; box-sizing: border-box;" placeholder="请输入您的姓名" />
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">电话 <span style="color: #ef4444;">*</span></label>
                                <input type="tel" name="phone" style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s; box-sizing: border-box;" placeholder="请输入联系电话" />
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">邮箱</label>
                                <input type="email" name="email" style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s; box-sizing: border-box;" placeholder="请输入电子邮箱" />
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">留言内容 <span style="color: #ef4444;">*</span></label>
                            <textarea name="message" required rows="5" style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s; resize: vertical; box-sizing: border-box;" placeholder="请输入留言内容..."></textarea>
                        </div>
                        
                        <input type="hidden" name="action" value="ds_submit_message" />
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'ds_message_nonce' ); ?>" />
                        
                        <button type="submit" id="submit-btn" style="width: 100%; padding: 14px 30px; background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            提交留言
                        </button>
                    </form>
                    
                    <script>
                    document.getElementById('ds-contact-form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        var form = this;
                        var btn = document.getElementById('submit-btn');
                        var toast = document.getElementById('form-toast');
                        var overlay = document.getElementById('form-overlay');
                        var toastIcon = document.getElementById('toast-icon');
                        var toastText = document.getElementById('toast-text');
                        
                        btn.disabled = true;
                        btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:8px;"><svg width="20" height="20" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="32" stroke-linecap="round"/></svg>提交中...</span>';
                        
                        var formData = new FormData(form);
                        
                        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                            method: 'POST',
                            body: formData
                        })
                        .then(function(response) { return response.json(); })
                        .then(function(data) {
                            overlay.style.display = 'block';
                            toast.style.display = 'block';
                            
                            if (data.success) {
                                toast.style.background = '#dcfce7';
                                toastIcon.innerHTML = '✅';
                                toastText.innerHTML = '<span style="color:#166534;">' + data.data.message + '</span>';
                                form.reset();
                            } else {
                                toast.style.background = '#fee2e2';
                                toastIcon.innerHTML = '❌';
                                toastText.innerHTML = '<span style="color:#991b1b;">' + (data.data ? data.data.message : '提交失败') + '</span>';
                            }
                            
                            btn.disabled = false;
                            btn.textContent = '提交留言';
                            
                            setTimeout(function() {
                                toast.style.display = 'none';
                                overlay.style.display = 'none';
                            }, 2500);
                        })
                        .catch(function(err) {
                            console.error('Form error:', err);
                            overlay.style.display = 'block';
                            toast.style.display = 'block';
                            toast.style.background = '#fee2e2';
                            toastIcon.innerHTML = '❌';
                            toastText.innerHTML = '<span style="color:#991b1b;">网络错误，请稍后重试</span>';
                            btn.disabled = false;
                            btn.textContent = '提交留言';
                            
                            setTimeout(function() {
                                toast.style.display = 'none';
                                overlay.style.display = 'none';
                            }, 2500);
                        });
                    });
                    
                    document.getElementById('form-overlay').addEventListener('click', function() {
                        this.style.display = 'none';
                        document.getElementById('form-toast').style.display = 'none';
                    });
                    </script>
                    
                <?php elseif ( $contact_image ) : ?>
                    <!-- 显示自定义图片 -->
                    <img src="<?php echo esc_url( $contact_image ); ?>" alt="联系我们" style="width: 100%; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.1);" />
                    
                <?php else : ?>
                    <!-- 默认占位 -->
                    <div style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); border-radius: 16px; padding: 60px 40px; text-align: center;">
                        <div style="font-size: 4rem; margin-bottom: 20px;">📞</div>
                        <h3 style="color: #64748b; font-weight: 500; margin: 0 0 10px;">欢迎联系我们</h3>
                        <p style="color: #94a3b8; margin: 0;">我们期待与您的合作</p>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
        
    </div>
</div>

<style>
@keyframes spin { 100% { transform: rotate(360deg); } }
@media (max-width: 768px) {
    .contact-grid { grid-template-columns: 1fr !important; gap: 40px !important; }
    #form-toast { padding: 30px 40px !important; width: 85% !important; }
}
#ds-contact-form input:focus,
#ds-contact-form textarea:focus {
    border-color: var(--color-primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
#ds-contact-form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
}
</style>

<?php get_footer(); ?>
