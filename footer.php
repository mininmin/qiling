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
                        <div style="color: rgba(255,255,255,0.7); line-height: 1.8;">
                            <?php echo wp_kses_post( developer_starter_get_option( 'company_brief', __( '专业的企业服务提供商，致力于为客户提供优质的产品与服务。', 'developer-starter' ) ) ); ?>
                        </div>
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
                        $working_hours = developer_starter_get_option( 'company_working_hours', '' );
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
                        <?php if ( $working_hours ) : ?>
                            <p style="color: rgba(255,255,255,0.7); margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 1.2em;">🕐</span><?php echo esc_html( $working_hours ); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="footer-widget-area">
                        <h3><?php echo esc_html( developer_starter_get_option( 'footer_follow_title', __( '关注我们', 'developer-starter' ) ) ); ?></h3>
                        <?php 
                        $wechat_qr = developer_starter_get_option( 'wechat_qrcode', '' );
                        $wechat_text = developer_starter_get_option( 'wechat_qr_text', '扫码关注公众号' );
                        $douyin_qr = developer_starter_get_option( 'douyin_qrcode', '' );
                        $douyin_text = developer_starter_get_option( 'douyin_qr_text', '扫码关注抖音' );
                        ?>
                        <div class="qrcode-grid" style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                            <?php if ( $wechat_qr ) : ?>
                                <div class="qrcode-item" style="text-align: center;">
                                    <img src="<?php echo esc_url( $wechat_qr ); ?>" alt="<?php esc_attr_e( '微信二维码', 'developer-starter' ); ?>" style="width: 110px; height: 110px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); object-fit: cover;" />
                                    <?php if ( $wechat_text ) : ?>
                                        <p style="color: rgba(255,255,255,0.6); font-size: 0.8rem; margin-top: 8px; max-width: 110px;"><?php echo esc_html( $wechat_text ); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ( $douyin_qr ) : ?>
                                <div class="qrcode-item" style="text-align: center;">
                                    <img src="<?php echo esc_url( $douyin_qr ); ?>" alt="<?php esc_attr_e( '抖音二维码', 'developer-starter' ); ?>" style="width: 110px; height: 110px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); object-fit: cover;" />
                                    <?php if ( $douyin_text ) : ?>
                                        <p style="color: rgba(255,255,255,0.6); font-size: 0.8rem; margin-top: 8px; max-width: 110px;"><?php echo esc_html( $douyin_text ); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        // 友情链接（仅首页显示）
        $friend_links_enable = developer_starter_get_option( 'friend_links_enable', '' );
        $friend_links = developer_starter_get_option( 'friend_links', array() );
        if ( $friend_links_enable && is_front_page() && ! empty( $friend_links ) && is_array( $friend_links ) ) :
        ?>
        <div class="footer-friend-links" style="<?php echo esc_attr( $bottom_bg_style ); ?> border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div class="container" style="padding: 20px 0;">
                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <span style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">友情链接：</span>
                    <?php foreach ( $friend_links as $link ) :
                        $text = isset( $link['text'] ) ? $link['text'] : '';
                        $url = isset( $link['url'] ) ? $link['url'] : '#';
                        if ( $text ) :
                    ?>
                        <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="external nofollow noopener noreferrer" style="color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.9rem; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'"><?php echo esc_html( $text ); ?></a>
                    <?php 
                        endif;
                    endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
// 页脚动画效果 - 使用外部JS文件
$effect_enabled = developer_starter_get_option( 'footer_effect_enable', '' );
if ( $effect_enabled ) :
    $effect_type = developer_starter_get_option( 'footer_effect_type', 'particles' );
?>
<script>
// 传递特效类型给外部JS
window.footerEffectType = '<?php echo esc_js( $effect_type ); ?>';
</script>
<?php endif; ?>

<?php 
// 隐私政策/Cookie提示条（GDPR）
$privacy_banner_enable = developer_starter_get_option( 'privacy_banner_enable', '' );
if ( $privacy_banner_enable ) :
    $privacy_text = developer_starter_get_option( 'privacy_banner_text', '本网站使用Cookie和类似技术来提升您的体验。继续使用本网站即表示您同意我们的隐私政策。' );
    $privacy_link_text = developer_starter_get_option( 'privacy_banner_link_text', '了解更多' );
    $privacy_link_url = developer_starter_get_option( 'privacy_banner_link_url', '' );
    $privacy_btn_text = developer_starter_get_option( 'privacy_banner_btn_text', '全部接受' );
    $privacy_decline_text = developer_starter_get_option( 'privacy_banner_decline_text', '' );
    $privacy_bg = developer_starter_get_option( 'privacy_banner_bg', '#1e293b' );
    $privacy_text_color = developer_starter_get_option( 'privacy_banner_text_color', '#ffffff' );
?>
<div id="privacy-banner" class="privacy-banner" style="display: none; position: fixed; bottom: 0; left: 0; right: 0; z-index: 9999; background: <?php echo esc_attr( $privacy_bg ); ?>; color: <?php echo esc_attr( $privacy_text_color ); ?>; padding: 15px 20px; box-shadow: 0 -4px 20px rgba(0,0,0,0.15);">
    <div class="container" style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
        <div class="privacy-banner-content" style="flex: 1; min-width: 300px;">
            <p style="margin: 0; font-size: 0.95rem; line-height: 1.6;">
                🍪 <?php echo esc_html( $privacy_text ); ?>
                <?php if ( $privacy_link_url ) : ?>
                    <a href="<?php echo esc_url( $privacy_link_url ); ?>" style="color: <?php echo esc_attr( $privacy_text_color ); ?>; text-decoration: underline; margin-left: 5px;" target="_blank"><?php echo esc_html( $privacy_link_text ); ?></a>
                <?php endif; ?>
            </p>
        </div>
        <div class="privacy-banner-actions" style="display: flex; gap: 10px; flex-shrink: 0;">
            <?php if ( $privacy_decline_text ) : ?>
                <button type="button" id="privacy-decline" style="padding: 10px 24px; background: transparent; color: <?php echo esc_attr( $privacy_text_color ); ?>; border: 2px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                    <?php echo esc_html( $privacy_decline_text ); ?>
                </button>
            <?php endif; ?>
            <button type="button" id="privacy-accept" style="padding: 10px 24px; background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: #fff; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                <?php echo esc_html( $privacy_btn_text ); ?>
            </button>
        </div>
    </div>
</div>
<style>
#privacy-banner button:hover {
    transform: translateY(-2px);
}
#privacy-banner #privacy-accept:hover {
    box-shadow: 0 5px 15px rgba(37, 99, 235, 0.4);
}
#privacy-banner #privacy-decline:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.5);
}
@media (max-width: 768px) {
    #privacy-banner .container {
        flex-direction: column;
        text-align: center;
    }
    #privacy-banner .privacy-banner-content {
        min-width: auto;
    }
    #privacy-banner .privacy-banner-actions {
        width: 100%;
        justify-content: center;
    }
}
</style>
<script>
(function() {
    var banner = document.getElementById('privacy-banner');
    var acceptBtn = document.getElementById('privacy-accept');
    var declineBtn = document.getElementById('privacy-decline');
    var storageKey = 'ds_privacy_consent';
    
    if (!banner) return;
    
    // 检查是否已做出选择
    var consent = localStorage.getItem(storageKey);
    if (!consent) {
        banner.style.display = 'block';
    }
    
    function hideBanner() {
        banner.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
        banner.style.transform = 'translateY(100%)';
        banner.style.opacity = '0';
        setTimeout(function() {
            banner.style.display = 'none';
        }, 300);
    }
    
    if (acceptBtn) {
        acceptBtn.addEventListener('click', function() {
            localStorage.setItem(storageKey, 'all');
            hideBanner();
        });
    }
    
    if (declineBtn) {
        declineBtn.addEventListener('click', function() {
            localStorage.setItem(storageKey, 'essential');
            hideBanner();
        });
    }
})();
</script>
<?php endif; ?>

<?php wp_footer(); ?>

</body>
</html>
