<?php
/**
 * Template Name: 用户登录
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
$register_page_id = developer_starter_get_option( 'register_page_id', '' );
$forgot_page_id = developer_starter_get_option( 'forgot_password_page_id', '' );
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
                <h2 class="auth-title">用户登录</h2>
                <p class="auth-subtitle">欢迎回来，请登录您的账户</p>
            </div>
            
            <form id="login-form" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="username">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </label>
                    <input type="text" id="username" name="username" placeholder="用户名或邮箱" required autocomplete="username" />
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </label>
                    <input type="password" id="password" name="password" placeholder="密码" required autocomplete="current-password" />
                    <button type="button" class="toggle-password" tabindex="-1">
                        <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                
                <div class="form-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="true" />
                        <span>记住我</span>
                    </label>
                    <?php if ( $forgot_page_id ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $forgot_page_id ) ); ?>" class="forgot-link">忘记密码？</a>
                    <?php endif; ?>
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
                    <span class="btn-text">登 录</span>
                    <span class="btn-loading" style="display:none">
                        <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.416" stroke-dashoffset="10"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></circle></svg>
                    </span>
                </button>
                
                <?php wp_nonce_field( 'developer_starter_auth', 'auth_nonce' ); ?>
            </form>
            
            <?php if ( $register_page_id && get_option( 'users_can_register' ) ) : ?>
            <div class="auth-footer">
                <p>还没有账户？<a href="<?php echo esc_url( get_permalink( $register_page_id ) ); ?>">立即注册</a></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('login-form');
    var submitBtn = document.getElementById('submit-btn');
    var message = document.getElementById('form-message');
    
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
    
    // 滑动验证码
    var captcha = document.getElementById('slider-captcha');
    if (captcha) {
        initSliderCaptcha(captcha);
    }
    
    // 表单提交
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(form);
        formData.append('action', 'developer_starter_login');
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
