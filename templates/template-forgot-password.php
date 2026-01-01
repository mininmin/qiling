<?php
/**
 * Template Name: 找回密码
 * 
 * @package Developer_Starter
 */

// 已登录用户跳转
if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

get_header();

$captcha_enable = developer_starter_get_option( 'auth_captcha_enable', '' );
$login_page_id = developer_starter_get_option( 'login_page_id', '' );
$password_strength = developer_starter_get_option( 'password_strength', 'medium' );

// 检查是否是重置密码模式
$is_reset_mode = isset( $_GET['action'] ) && $_GET['action'] === 'reset';
$reset_key = isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : '';
$reset_login = isset( $_GET['login'] ) ? sanitize_user( $_GET['login'] ) : '';
?>

<div class="auth-page">
    <div class="auth-bg">
        <div class="auth-particles"></div>
    </div>
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <?php 
                    $logo = developer_starter_get_option( 'site_logo', '' );
                    if ( $logo ) : ?>
                        <img src="<?php echo esc_url( $logo ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
                    <?php else : ?>
                        <h1><?php bloginfo( 'name' ); ?></h1>
                    <?php endif; ?>
                </div>
                
                <?php if ( $is_reset_mode ) : ?>
                    <h2 class="auth-title">设置新密码</h2>
                    <p class="auth-subtitle">请输入您的新密码</p>
                <?php else : ?>
                    <h2 class="auth-title">找回密码</h2>
                    <p class="auth-subtitle">输入您的注册邮箱，我们将发送重置链接</p>
                <?php endif; ?>
            </div>
            
            <?php if ( $is_reset_mode ) : ?>
            <!-- 重置密码表单 -->
            <form id="reset-form" class="auth-form" novalidate>
                <input type="hidden" name="key" value="<?php echo esc_attr( $reset_key ); ?>" />
                <input type="hidden" name="login" value="<?php echo esc_attr( $reset_login ); ?>" />
                
                <div class="form-group">
                    <label for="password">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </label>
                    <input type="password" id="password" name="password" placeholder="新密码" required autocomplete="new-password" />
                    <button type="button" class="toggle-password" tabindex="-1">
                        <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                
                <div class="password-strength" id="password-strength">
                    <div class="strength-bar"><span></span></div>
                    <div class="strength-text"></div>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="确认新密码" required autocomplete="new-password" />
                </div>
                
                <div class="form-message" id="form-message"></div>
                
                <button type="submit" class="auth-submit" id="submit-btn">
                    <span class="btn-text">重置密码</span>
                    <span class="btn-loading" style="display:none">
                        <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.416" stroke-dashoffset="10"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></circle></svg>
                    </span>
                </button>
                
                <?php wp_nonce_field( 'developer_starter_auth', 'auth_nonce' ); ?>
            </form>
            
            <?php else : ?>
            <!-- 找回密码表单 -->
            <form id="forgot-form" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="email">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </label>
                    <input type="email" id="email" name="email" placeholder="请输入注册邮箱" required autocomplete="email" />
                </div>
                
                <?php if ( $captcha_enable ) : ?>
                <div class="form-group captcha-group">
                    <div class="slider-captcha" id="slider-captcha">
                        <div class="captcha-track">
                            <div class="captcha-slider">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                            </div>
                            <div class="captcha-progress"></div>
                            <span class="captcha-text">向右滑动完成验证</span>
                        </div>
                    </div>
                    <input type="hidden" name="captcha_verified" id="captcha_verified" value="false" />
                </div>
                <?php endif; ?>
                
                <div class="form-message" id="form-message"></div>
                
                <button type="submit" class="auth-submit" id="submit-btn">
                    <span class="btn-text">发送重置链接</span>
                    <span class="btn-loading" style="display:none">
                        <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.416" stroke-dashoffset="10"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></circle></svg>
                    </span>
                </button>
                
                <?php wp_nonce_field( 'developer_starter_auth', 'auth_nonce' ); ?>
            </form>
            <?php endif; ?>
            
            <?php if ( $login_page_id ) : ?>
            <div class="auth-footer">
                <p><a href="<?php echo esc_url( get_permalink( $login_page_id ) ); ?>">← 返回登录</a></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var isResetMode = <?php echo $is_reset_mode ? 'true' : 'false'; ?>;
    
    // 密码显示切换
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.parentElement.querySelector('input');
            var eyeOpen = this.querySelector('.eye-open');
            var eyeClosed = this.querySelector('.eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        });
    });
    
    // 密码强度检测
    var passwordInput = document.getElementById('password');
    var strengthContainer = document.getElementById('password-strength');
    if (passwordInput && strengthContainer) {
        passwordInput.addEventListener('input', function() {
            var password = this.value;
            var strength = checkPasswordStrength(password);
            var bar = strengthContainer.querySelector('.strength-bar span');
            var text = strengthContainer.querySelector('.strength-text');
            
            bar.className = '';
            if (password.length === 0) {
                bar.style.width = '0';
                text.textContent = '';
            } else if (strength === 'weak') {
                bar.style.width = '33%';
                bar.className = 'weak';
                text.textContent = '弱';
                text.className = 'strength-text weak';
            } else if (strength === 'medium') {
                bar.style.width = '66%';
                bar.className = 'medium';
                text.textContent = '中';
                text.className = 'strength-text medium';
            } else {
                bar.style.width = '100%';
                bar.className = 'strong';
                text.textContent = '强';
                text.className = 'strength-text strong';
            }
        });
    }
    
    function checkPasswordStrength(password) {
        var score = 0;
        if (password.length >= 6) score++;
        if (password.length >= 8) score++;
        if (password.length >= 10) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score++;
        
        if (score <= 2) return 'weak';
        if (score <= 4) return 'medium';
        return 'strong';
    }
    
    // 滑动验证码
    var captcha = document.getElementById('slider-captcha');
    if (captcha) {
        initSliderCaptcha(captcha);
    }
    
    if (isResetMode) {
        // 重置密码表单
        var form = document.getElementById('reset-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(form, 'developer_starter_reset_password');
        });
    } else {
        // 找回密码表单
        var form = document.getElementById('forgot-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(form, 'developer_starter_forgot_password');
        });
    }
    
    function submitForm(form, action) {
        var submitBtn = document.getElementById('submit-btn');
        var message = document.getElementById('form-message');
        
        var formData = new FormData(form);
        formData.append('action', action);
        formData.append('nonce', document.querySelector('[name="auth_nonce"]').value);
        
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-text').style.display = 'none';
        submitBtn.querySelector('.btn-loading').style.display = 'inline-flex';
        
        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                message.className = 'form-message success';
                message.textContent = data.data.message;
                if (data.data.redirect) {
                    setTimeout(function() {
                        window.location.href = data.data.redirect;
                    }, 2000);
                }
            } else {
                message.className = 'form-message error';
                message.textContent = data.data.message;
            }
            submitBtn.disabled = false;
            submitBtn.querySelector('.btn-text').style.display = 'inline';
            submitBtn.querySelector('.btn-loading').style.display = 'none';
        })
        .catch(function() {
            message.className = 'form-message error';
            message.textContent = '网络错误，请稍后再试';
            submitBtn.disabled = false;
            submitBtn.querySelector('.btn-text').style.display = 'inline';
            submitBtn.querySelector('.btn-loading').style.display = 'none';
        });
    }
});

function initSliderCaptcha(container) {
    var slider = container.querySelector('.captcha-slider');
    var progress = container.querySelector('.captcha-progress');
    var text = container.querySelector('.captcha-text');
    var track = container.querySelector('.captcha-track');
    var verified = document.getElementById('captcha_verified');
    var isDragging = false;
    var startX = 0;
    var sliderWidth = slider.offsetWidth;
    var trackWidth = track.offsetWidth - sliderWidth;
    
    function handleStart(e) {
        if (verified.value === 'true') return;
        isDragging = true;
        startX = (e.touches ? e.touches[0].clientX : e.clientX) - slider.offsetLeft;
        slider.style.transition = 'none';
        progress.style.transition = 'none';
    }
    
    function handleMove(e) {
        if (!isDragging) return;
        e.preventDefault();
        var x = (e.touches ? e.touches[0].clientX : e.clientX) - startX;
        x = Math.max(0, Math.min(x, trackWidth));
        slider.style.left = x + 'px';
        progress.style.width = (x + sliderWidth) + 'px';
    }
    
    function handleEnd() {
        if (!isDragging) return;
        isDragging = false;
        slider.style.transition = 'left 0.3s';
        progress.style.transition = 'width 0.3s';
        
        var x = parseInt(slider.style.left) || 0;
        if (x >= trackWidth - 5) {
            verified.value = 'true';
            container.classList.add('verified');
            text.textContent = '验证成功';
            slider.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
        } else {
            slider.style.left = '0';
            progress.style.width = sliderWidth + 'px';
        }
    }
    
    slider.addEventListener('mousedown', handleStart);
    document.addEventListener('mousemove', handleMove);
    document.addEventListener('mouseup', handleEnd);
    slider.addEventListener('touchstart', handleStart);
    document.addEventListener('touchmove', handleMove, { passive: false });
    document.addEventListener('touchend', handleEnd);
}
</script>

<?php get_footer(); ?>
