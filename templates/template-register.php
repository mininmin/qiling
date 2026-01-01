<?php
/**
 * Template Name: 用户注册
 * 
 * @package Developer_Starter
 */

// 已登录用户跳转
if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

// 检查是否开放注册
if ( ! get_option( 'users_can_register' ) ) {
    wp_redirect( home_url() );
    exit;
}

get_header();

$captcha_enable = developer_starter_get_option( 'auth_captcha_enable', '' );
$login_page_id = developer_starter_get_option( 'login_page_id', '' );
$password_strength = developer_starter_get_option( 'password_strength', 'medium' );
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
                <h2 class="auth-title">创建账户</h2>
                <p class="auth-subtitle">加入我们，开启精彩之旅</p>
            </div>
            
            <form id="register-form" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="username">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </label>
                    <input type="text" id="username" name="username" placeholder="用户名（至少3个字符）" required autocomplete="username" />
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </label>
                    <input type="email" id="email" name="email" placeholder="邮箱地址" required autocomplete="email" />
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </label>
                    <input type="password" id="password" name="password" placeholder="密码" required autocomplete="new-password" />
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
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="确认密码" required autocomplete="new-password" />
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
                
                <?php 
                $agreement_enable = developer_starter_get_option( 'register_agreement_enable', '' );
                if ( $agreement_enable ) :
                    $agreement_text = developer_starter_get_option( 'register_agreement_text', '我已阅读并同意' );
                    $agreement_link_text = developer_starter_get_option( 'register_agreement_link_text', '《用户服务协议》' );
                    $agreement_url = developer_starter_get_option( 'register_agreement_url', '' );
                ?>
                <div class="form-group agreement-group">
                    <label class="agreement-label">
                        <input type="checkbox" name="agreement" id="agreement" value="1" />
                        <span class="checkmark"></span>
                        <span class="agreement-text">
                            <?php echo esc_html( $agreement_text ); ?>
                            <?php if ( $agreement_url ) : ?>
                                <a href="<?php echo esc_url( $agreement_url ); ?>" target="_blank"><?php echo esc_html( $agreement_link_text ); ?></a>
                            <?php else : ?>
                                <?php echo esc_html( $agreement_link_text ); ?>
                            <?php endif; ?>
                        </span>
                    </label>
                </div>
                <?php endif; ?>
                
                <div class="form-message" id="form-message"></div>
                
                <button type="submit" class="auth-submit" id="submit-btn">
                    <span class="btn-text">立即注册</span>
                    <span class="btn-loading" style="display:none">
                        <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.416" stroke-dashoffset="10"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></circle></svg>
                    </span>
                </button>
                
                <?php wp_nonce_field( 'developer_starter_auth', 'auth_nonce' ); ?>
            </form>
            
            <?php if ( $login_page_id ) : ?>
            <div class="auth-footer">
                <p>已有账户？<a href="<?php echo esc_url( get_permalink( $login_page_id ) ); ?>">立即登录</a></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('register-form');
    var submitBtn = document.getElementById('submit-btn');
    var message = document.getElementById('form-message');
    var passwordInput = document.getElementById('password');
    var strengthContainer = document.getElementById('password-strength');
    var requiredStrength = '<?php echo esc_js( $password_strength ); ?>';
    
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
    
    // 表单提交
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(form);
        formData.append('action', 'developer_starter_register');
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
                setTimeout(function() {
                    window.location.href = data.data.redirect;
                }, 1000);
            } else {
                message.className = 'form-message error';
                message.textContent = data.data.message;
                submitBtn.disabled = false;
                submitBtn.querySelector('.btn-text').style.display = 'inline';
                submitBtn.querySelector('.btn-loading').style.display = 'none';
            }
        })
        .catch(function() {
            message.className = 'form-message error';
            message.textContent = '网络错误，请稍后再试';
            submitBtn.disabled = false;
            submitBtn.querySelector('.btn-text').style.display = 'inline';
            submitBtn.querySelector('.btn-loading').style.display = 'none';
        });
    });
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
