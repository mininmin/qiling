    </main><!-- #primary -->

    <?php
    // 页脚颜色设置
    $footer_widgets_bg = developer_starter_get_option( 'footer_widgets_bg', '#1e293b' );
    $footer_bottom_bg = developer_starter_get_option( 'footer_bottom_bg', '#0f172a' );
    $footer_text_color = developer_starter_get_option( 'footer_text_color', '#ffffff' );
    $effect_enabled = developer_starter_get_option( 'footer_effect_enable', '' );
    $effect_type = developer_starter_get_option( 'footer_effect_type', 'particles' );
    
    $widgets_bg_style = strpos( $footer_widgets_bg, 'gradient' ) !== false ? "background: {$footer_widgets_bg};" : "background-color: {$footer_widgets_bg};";
    $bottom_bg_style = strpos( $footer_bottom_bg, 'gradient' ) !== false ? "background: {$footer_bottom_bg};" : "background-color: {$footer_bottom_bg};";
    ?>

    <footer id="colophon" class="site-footer" style="color: <?php echo esc_attr( $footer_text_color ); ?>;">
        <div class="footer-widgets" style="<?php echo esc_attr( $widgets_bg_style ); ?> position: relative; overflow: hidden;">
            <?php if ( $effect_enabled ) : ?>
                <canvas id="footer-effect-canvas" style="position: absolute; inset: 0; pointer-events: none; z-index: 0;"></canvas>
            <?php endif; ?>
            <div class="container" style="position: relative; z-index: 1;">
                <div class="footer-widgets-grid">
                    <div class="footer-widget-area">
                        <h3><?php echo esc_html( developer_starter_get_option( 'footer_about_title', __( '关于我们', 'developer-starter' ) ) ); ?></h3>
                        <p style="color: rgba(255,255,255,0.7); line-height: 1.8;">
                            <?php echo esc_html( developer_starter_get_option( 'company_brief', __( '专业的企业服务提供商，致力于为客户提供优质的产品与服务。', 'developer-starter' ) ) ); ?>
                        </p>
                    </div>
                    
                    <div class="footer-widget-area">
                        <h3><?php echo esc_html( developer_starter_get_option( 'footer_links_title', __( '快速链接', 'developer-starter' ) ) ); ?></h3>
                        <?php
                        if ( has_nav_menu( 'footer' ) ) {
                            wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false ) );
                        } else {
                            $quick_links = developer_starter_get_option( 'footer_quick_links', array() );
                            if ( ! empty( $quick_links ) && is_array( $quick_links ) ) {
                                echo '<ul class="footer-links" style="list-style: none; padding: 0; margin: 0;">';
                                foreach ( $quick_links as $link ) {
                                    $text = isset( $link['text'] ) ? $link['text'] : '';
                                    $url = isset( $link['url'] ) ? $link['url'] : '#';
                                    if ( $text ) {
                                        echo '<li style="margin-bottom: 8px;"><a href="' . esc_url( $url ) . '" style="color: rgba(255,255,255,0.7); text-decoration: none;" target="_blank">' . esc_html( $text ) . '</a></li>';
                                    }
                                }
                                echo '</ul>';
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="footer-widget-area">
                        <h3><?php echo esc_html( developer_starter_get_option( 'footer_contact_title', __( '联系方式', 'developer-starter' ) ) ); ?></h3>
                        <?php 
                        $phone = developer_starter_get_option( 'company_phone', '' );
                        $email = developer_starter_get_option( 'company_email', '' );
                        $address = developer_starter_get_option( 'company_address', '' );
                        ?>
                        <?php if ( $phone ) : ?>
                            <p style="color: rgba(255,255,255,0.7); margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 1.2em;">📞</span><?php echo esc_html( $phone ); ?>
                            </p>
                        <?php endif; ?>
                        <?php if ( $email ) : ?>
                            <p style="color: rgba(255,255,255,0.7); margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 1.2em;">📧</span><?php echo esc_html( $email ); ?>
                            </p>
                        <?php endif; ?>
                        <?php if ( $address ) : ?>
                            <p style="color: rgba(255,255,255,0.7); margin-bottom: 10px; display: flex; align-items: flex-start; gap: 8px;">
                                <span style="font-size: 1.2em;">📍</span><?php echo esc_html( $address ); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="footer-widget-area">
                        <h3><?php echo esc_html( developer_starter_get_option( 'footer_follow_title', __( '关注我们', 'developer-starter' ) ) ); ?></h3>
                        <?php 
                        $wechat_qr = developer_starter_get_option( 'wechat_qrcode', '' );
                        $weibo_url = developer_starter_get_option( 'weibo_url', '' );
                        ?>
                        <div style="display: flex; gap: 15px; align-items: flex-start; flex-wrap: wrap;">
                            <?php if ( $wechat_qr ) : ?>
                                <div>
                                    <img src="<?php echo esc_url( $wechat_qr ); ?>" alt="<?php esc_attr_e( '微信二维码', 'developer-starter' ); ?>" style="max-width: 120px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);" />
                                    <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; margin-top: 10px;"><?php esc_html_e( '扫码关注公众号', 'developer-starter' ); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if ( $weibo_url ) : ?>
                                <a href="<?php echo esc_url( $weibo_url ); ?>" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: #E6162D; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.3s;" title="<?php esc_attr_e( '关注微博', 'developer-starter' ); ?>">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M10.098 20.323c-3.977.391-7.414-1.406-7.672-4.02-.259-2.609 2.759-5.047 6.74-5.441 3.979-.394 7.413 1.404 7.671 4.018.259 2.6-2.759 5.049-6.737 5.439l-.002.004zM9.05 17.219c-.384.616-1.208.884-1.829.602-.612-.279-.793-.991-.406-1.593.379-.595 1.176-.861 1.793-.601.622.263.82.972.442 1.592zm1.27-1.627c-.141.237-.449.353-.689.253-.236-.09-.313-.361-.177-.586.138-.227.436-.346.672-.24.239.09.315.36.18.573h.014zm.176-2.719c-1.893-.493-4.033.45-4.836 2.118-.818 1.683-.052 3.535 1.765 4.141 1.871.629 4.225-.271 5.102-1.994.869-1.721.123-3.759-2.035-4.265h.004zm8.834-1.677c-.745-.187-1.254-.31-1.754-1.127l.002-.001a5.521 5.521 0 00-1.552-1.96c-.689-.583-1.012-.576-1.012-.576l-.002.003s.248-.016.695.182c.447.197 1.019.574 1.491 1.13.476.558.795 1.108.945 1.439.151.329.153.403.081.541-.074.137-.149.182-.346.229-.199.047-.452.09-.548.14zm-.4-2.25c-.636-.203-1.073-.267-1.499-.973l.001-.001a4.724 4.724 0 00-1.329-1.677c-.587-.498-.86-.5-.86-.5l-.002.002s.21-.015.593.155a4.07 4.07 0 011.275.966c.407.477.679.948.808 1.23.128.281.13.346.068.462-.063.118-.128.155-.296.197-.169.04-.387.077-.468.119l.709.019zm-4.4-2.683c-1.327.365-2.09 1.447-1.702 2.424.387.975 1.72 1.409 2.983 1.007 1.282-.405 2.023-1.51 1.665-2.495-.356-.999-1.68-1.329-2.946-.936z"/></svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom" style="<?php echo esc_attr( $bottom_bg_style ); ?>">
            <div class="container">
                <div class="footer-bottom-content" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px 0;">
                    <div class="footer-copyright">
                        <?php 
                        $copyright = developer_starter_get_option( 'footer_copyright', '' );
                        echo $copyright ? wp_kses_post( $copyright ) : '&copy; ' . date('Y') . ' ' . esc_html( get_bloginfo( 'name' ) ) . '. ' . esc_html__( '版权所有', 'developer-starter' ) . '.';
                        ?>
                    </div>
                    
                    <div class="footer-filing" style="display: flex; gap: 20px; flex-wrap: wrap; align-items: center;">
                        <?php 
                        $icp = developer_starter_get_option( 'icp_number', '' );
                        $police = developer_starter_get_option( 'police_number', '' );
                        $police_icon = developer_starter_get_option( 'police_icon', '' );
                        
                        if ( $icp ) : ?>
                            <a href="https://beian.miit.gov.cn/" target="_blank" rel="external nofollow noopener noreferrer" style="color: rgba(255,255,255,0.6);"><?php echo esc_html( $icp ); ?></a>
                        <?php endif; ?>
                        <?php if ( $police ) : 
                            // 自动识别公安备案号中的数字
                            preg_match( '/(\d+)/', $police, $matches );
                            $police_record_code = ! empty( $matches[1] ) ? $matches[1] : '';
                            $police_url = $police_record_code ? 'http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . $police_record_code : '#';
                        ?>
                            <a href="<?php echo esc_url( $police_url ); ?>" target="_blank" rel="external nofollow noopener noreferrer" style="color: rgba(255,255,255,0.6); display: flex; align-items: center; gap: 5px;">
                                <?php if ( $police_icon ) : ?>
                                    <img src="<?php echo esc_url( $police_icon ); ?>" alt="" style="width: 16px; height: 16px;" />
                                <?php endif; ?>
                                <?php echo esc_html( $police ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</div><!-- #page -->

<?php 
// 浮动小工具
$float_enabled = developer_starter_get_option( 'float_widget_enable', '' );
if ( $float_enabled ) :
    $float_phone = developer_starter_get_option( 'float_phone', '' );
    $float_qq = developer_starter_get_option( 'float_qq', '' );
    $float_wechat = developer_starter_get_option( 'float_wechat_qrcode', '' );
    $float_custom = developer_starter_get_option( 'float_custom_items', array() );
?>
<div class="float-widget" style="position: fixed; right: 0; top: 50%; transform: translateY(-50%); z-index: 999; display: flex; flex-direction: column; border-radius: 8px 0 0 8px; overflow: hidden; box-shadow: -5px 0 30px rgba(0,0,0,0.1);">
    <?php if ( $float_phone ) : ?>
        <a href="tel:<?php echo esc_attr( $float_phone ); ?>" class="float-item" style="display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: #fff; text-decoration: none; transition: all 0.3s;" title="<?php esc_attr_e( '电话咨询', 'developer-starter' ); ?>">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.362 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
        </a>
    <?php endif; ?>
    
    <?php if ( $float_qq ) : ?>
        <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo esc_attr( $float_qq ); ?>&site=qq&menu=yes" target="_blank" class="float-item" style="display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: #12B7F5; color: #fff; text-decoration: none; transition: all 0.3s;" title="<?php esc_attr_e( 'QQ咨询', 'developer-starter' ); ?>">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm3 14h-6a1 1 0 010-2h6a1 1 0 010 2zm0-4H9a1 1 0 010-2h6a1 1 0 010 2z"/></svg>
        </a>
    <?php endif; ?>
    
    <?php if ( $float_wechat ) : ?>
        <div class="float-item float-wechat" style="position: relative; display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: #07C160; color: #fff; cursor: pointer; transition: all 0.3s;" title="<?php esc_attr_e( '微信咨询', 'developer-starter' ); ?>">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 01.213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 00.167-.054l1.903-1.114a.864.864 0 01.717-.098 10.16 10.16 0 002.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348zM5.785 5.991c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 01-1.162 1.178A1.17 1.17 0 014.623 7.17c0-.651.52-1.18 1.162-1.18zm5.813 0c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 01-1.162 1.178 1.17 1.17 0 01-1.162-1.178c0-.651.52-1.18 1.162-1.18z"/><path d="M23.918 14.667c0-3.193-3.068-5.791-6.858-5.791-3.789 0-6.858 2.598-6.858 5.791 0 3.194 3.069 5.792 6.858 5.792.746 0 1.466-.098 2.15-.28.202-.063.41-.035.596.075l1.473.87a.25.25 0 00.127.04.22.22 0 00.22-.225c0-.054-.022-.11-.037-.162l-.3-1.133a.45.45 0 01.163-.506c1.417-1.063 2.466-2.635 2.466-4.471zm-9.467-.794a.89.89 0 01-.886-.895c0-.494.396-.894.886-.894s.886.4.886.894a.89.89 0 01-.886.895zm5.218 0a.89.89 0 01-.886-.895c0-.494.396-.894.886-.894s.886.4.886.894a.89.89 0 01-.886.895z"/></svg>
            <div class="float-wechat-qr" style="display: none; position: absolute; right: 60px; top: 50%; transform: translateY(-50%); background: #fff; padding: 15px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <img src="<?php echo esc_url( $float_wechat ); ?>" alt="<?php esc_attr_e( '微信', 'developer-starter' ); ?>" style="width: 140px; display: block;" />
                <p style="text-align: center; margin: 10px 0 0; font-size: 12px; color: #666;"><?php esc_html_e( '扫码添加微信', 'developer-starter' ); ?></p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php 
    // 自定义浮动项目
    if ( ! empty( $float_custom ) && is_array( $float_custom ) ) :
        foreach ( $float_custom as $item ) :
            $title = isset( $item['title'] ) ? trim( $item['title'] ) : '';
            $url = isset( $item['url'] ) ? trim( $item['url'] ) : '';
            $icon_raw = isset( $item['icon'] ) ? trim( $item['icon'] ) : '🔗';
            $color = isset( $item['color'] ) ? $item['color'] : '#6366f1';
            // 只要有标题或有有效URL就显示
            if ( $title || ( $url && $url !== '#' && $url !== '' ) ) :
                // 解码HTML实体以便正确检测HTML标签
                $icon = html_entity_decode( $icon_raw, ENT_QUOTES, 'UTF-8' );
                // 判断图标输入格式
                // 1. 完整HTML标签格式: <i class="iconfont icon-xxx"></i>
                // 2. 类名格式: iconfont icon-xxx
                // 3. emoji或其他文本
                $is_html_tag = preg_match( '/<[^>]+>/', $icon );
                $is_iconfont_class = ! $is_html_tag && ( strpos( $icon, 'iconfont' ) !== false || strpos( $icon, 'icon-' ) !== false || strpos( $icon, 'fa-' ) !== false );
                $link_url = $url ? $url : '#';
    ?>
        <a href="<?php echo esc_url( $link_url ); ?>" target="_blank" class="float-item" style="display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: <?php echo esc_attr( $color ); ?>; color: #fff; text-decoration: none; font-size: 1.2em; transition: all 0.3s;" title="<?php echo esc_attr( $title ); ?>">
            <?php if ( $is_html_tag ) : ?>
                <?php echo wp_kses_post( $icon ); ?>
            <?php elseif ( $is_iconfont_class ) : ?>
                <i class="<?php echo esc_attr( $icon ); ?>"></i>
            <?php else : ?>
                <?php echo esc_html( $icon ); ?>
            <?php endif; ?>
        </a>
    <?php 
            endif;
        endforeach;
    endif; 
    ?>
    
    <a href="#" class="float-item float-top" style="display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: #475569; color: #fff; text-decoration: none; transition: all 0.3s;" title="<?php esc_attr_e( '返回顶部', 'developer-starter' ); ?>">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 15l-6-6-6 6"/></svg>
    </a>
</div>
<style>
.float-item:hover { opacity: 0.85; transform: scale(1.05); }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var wechatItem = document.querySelector('.float-wechat');
    if (wechatItem) {
        var qr = wechatItem.querySelector('.float-wechat-qr');
        wechatItem.addEventListener('mouseenter', function() { qr.style.display = 'block'; });
        wechatItem.addEventListener('mouseleave', function() { qr.style.display = 'none'; });
    }
    var topBtn = document.querySelector('.float-top');
    if (topBtn) {
        topBtn.addEventListener('click', function(e) { e.preventDefault(); window.scrollTo({top: 0, behavior: 'smooth'}); });
    }
});
</script>
<?php endif; ?>

<?php 
// 页脚动画效果
$effect_enabled = developer_starter_get_option( 'footer_effect_enable', '' );
if ( $effect_enabled ) :
    $effect_type = developer_starter_get_option( 'footer_effect_type', 'particles' );
?>
<script>
(function() {
    var canvas = document.getElementById('footer-effect-canvas');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var effectType = '<?php echo esc_js( $effect_type ); ?>';
    var particles = [];
    
    function resize() {
        canvas.width = canvas.parentElement.offsetWidth;
        canvas.height = canvas.parentElement.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);
    
    // 初始化粒子/对象
    function init() {
        particles = [];
        var count = effectType === 'stars' ? 80 : (effectType === 'particles' ? 50 : 30);
        for (var i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                size: Math.random() * 3 + 1,
                speedX: (Math.random() - 0.5) * 0.5,
                speedY: (Math.random() - 0.5) * 0.5,
                opacity: Math.random() * 0.5 + 0.2,
                phase: Math.random() * Math.PI * 2
            });
        }
    }
    init();
    
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        if (effectType === 'particles') {
            particles.forEach(function(p) {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255,255,255,' + p.opacity + ')';
                ctx.fill();
                p.x += p.speedX;
                p.y += p.speedY;
                if (p.x < 0) p.x = canvas.width;
                if (p.x > canvas.width) p.x = 0;
                if (p.y < 0) p.y = canvas.height;
                if (p.y > canvas.height) p.y = 0;
            });
        } else if (effectType === 'lines') {
            ctx.strokeStyle = 'rgba(255,255,255,0.1)';
            ctx.lineWidth = 1;
            particles.forEach(function(p, i) {
                particles.forEach(function(p2, j) {
                    if (i < j) {
                        var dx = p.x - p2.x, dy = p.y - p2.y;
                        var dist = Math.sqrt(dx*dx + dy*dy);
                        if (dist < 120) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.globalAlpha = 1 - dist/120;
                            ctx.stroke();
                            ctx.globalAlpha = 1;
                        }
                    }
                });
                ctx.beginPath();
                ctx.arc(p.x, p.y, 2, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255,255,255,0.5)';
                ctx.fill();
                p.x += p.speedX;
                p.y += p.speedY;
                if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
                if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;
            });
        } else if (effectType === 'waves') {
            var time = Date.now() * 0.001;
            for (var w = 0; w < 3; w++) {
                ctx.beginPath();
                ctx.moveTo(0, canvas.height);
                for (var x = 0; x <= canvas.width; x += 10) {
                    var y = canvas.height - 30 - w * 20 + Math.sin(x * 0.01 + time + w) * 15;
                    ctx.lineTo(x, y);
                }
                ctx.lineTo(canvas.width, canvas.height);
                ctx.closePath();
                ctx.fillStyle = 'rgba(255,255,255,' + (0.03 + w * 0.02) + ')';
                ctx.fill();
            }
        } else if (effectType === 'stars') {
            var time = Date.now() * 0.002;
            particles.forEach(function(p) {
                var twinkle = Math.sin(time + p.phase) * 0.5 + 0.5;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size * twinkle, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255,255,255,' + (p.opacity * twinkle) + ')';
                ctx.fill();
            });
        }
        
        requestAnimationFrame(draw);
    }
    draw();
})();
</script>
<?php endif; ?>

<?php wp_footer(); ?>

</body>
</html>
