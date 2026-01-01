<?php
/**
 * 评论模板 - 完善版
 * 
 * 处理：
 * - WordPress后台讨论设置（需登录才能评论等）
 * - 主题设置（完全禁用评论、蜜罐陷阱、用户名隐私）
 * - 密码保护文章
 * - 评论已关闭状态
 *
 * @package Developer_Starter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ========================================
// 前置检查
// ========================================

// 密码保护的文章不显示评论
if ( post_password_required() ) {
    return;
}

// 主题设置：完全禁用评论
$theme_disable_comments = developer_starter_get_option( 'disable_comments', '' );
if ( $theme_disable_comments ) {
    return;
}

// 获取WordPress讨论设置
$require_login = get_option( 'comment_registration' ); // 需要登录才能评论
$is_logged_in = is_user_logged_in();
$can_post_comment = $is_logged_in || ! $require_login;

// 主题蜜罐设置
$honeypot_enabled = developer_starter_get_option( 'comment_honeypot', '' );

// ========================================
// 评论区渲染
// ========================================
?>

<section id="comments" class="comments-section">
    
    <?php if ( have_comments() ) : ?>
        <div class="comments-header">
            <div class="comments-title-wrap">
                <span class="comments-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </span>
                <div>
                    <h2 class="comments-title">读者评论</h2>
                    <span class="comments-count"><?php echo number_format_i18n( get_comments_number() ); ?> 条</span>
                </div>
            </div>
        </div>

        <div class="comments-list-wrap">
            <ol class="comment-list">
                <?php
                wp_list_comments( array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 48,
                    'callback'    => 'developer_starter_comment_callback',
                ) );
                ?>
            </ol>
        </div>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
            <nav class="comment-pagination">
                <div class="nav-prev"><?php previous_comments_link( '← 较早的评论' ); ?></div>
                <div class="nav-next"><?php next_comments_link( '更新的评论 →' ); ?></div>
            </nav>
        <?php endif; ?>

    <?php else : ?>
        <?php if ( comments_open() ) : ?>
            <p class="no-comments-hint">暂无评论，快来抢沙发吧！</p>
        <?php endif; ?>
    <?php endif; ?>

    <?php // 评论已关闭提示 ?>
    <?php if ( ! comments_open() && have_comments() ) : ?>
        <p class="comments-closed-notice">评论已关闭</p>
    <?php endif; ?>

    <?php // 评论表单区域 ?>
    <?php if ( comments_open() ) : ?>
        
        <?php if ( ! $can_post_comment ) : ?>
            <!-- 需要登录才能评论 -->
            <div class="comment-login-required" id="respond">
                <div class="login-required-icon">🔒</div>
                <p class="login-required-text">请先<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">登录</a>后发表评论</p>
                <?php 
                // 如果启用了自定义登录页
                $custom_login_page = developer_starter_get_option( 'login_page_id', '' );
                if ( $custom_login_page ) :
                ?>
                    <a href="<?php echo esc_url( get_permalink( $custom_login_page ) ); ?>" class="btn-login">立即登录</a>
                <?php else : ?>
                    <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="btn-login">立即登录</a>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <!-- 评论表单 -->
            <?php
            $commenter = wp_get_current_commenter();
            $req = get_option( 'require_name_email' );
            $aria_req = ( $req ? " aria-required='true' required" : '' );
            ?>
            
            <div class="comment-form-section" id="respond">
                <div class="comment-form-header">
                    <h3 class="form-title" id="reply-title">
                        <?php echo $is_logged_in ? '发表评论' : '参与讨论'; ?>
                        <small><?php cancel_comment_reply_link( '取消回复' ); ?></small>
                    </h3>
                </div>
                
                <?php if ( $is_logged_in ) : ?>
                    <div class="logged-user-info">
                        <?php echo get_avatar( get_current_user_id(), 36 ); ?>
                        <span class="user-name"><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
                        <a href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>" class="logout-link">登出</a>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" method="post" class="comment-form" id="commentform">
                    <?php comment_id_fields(); ?>
                    
                    <?php // 蜜罐字段 - 隐藏的输入框，机器人会填写 ?>
                    <?php if ( $honeypot_enabled ) : ?>
                        <div style="display:none !important;">
                            <input type="text" name="website_url_hp" value="" autocomplete="off" tabindex="-1" />
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( ! $is_logged_in ) : ?>
                    <div class="form-row">
                        <div class="form-field">
                            <input type="text" name="author" id="author" value="<?php echo esc_attr( $commenter['comment_author'] ); ?>" placeholder="昵称<?php echo $req ? ' *' : ''; ?>"<?php echo $aria_req; ?> />
                        </div>
                        <div class="form-field">
                            <input type="email" name="email" id="email" value="<?php echo esc_attr( $commenter['comment_author_email'] ); ?>" placeholder="邮箱<?php echo $req ? ' *' : ''; ?>"<?php echo $aria_req; ?> />
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-field">
                        <textarea name="comment" id="comment" rows="3" placeholder="写下你的评论..." required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            发表评论
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
    
</section>

<style>
/* ========================================
   评论区样式 - 精简紧凑版
   ======================================== */

.comments-section {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid rgba(0,0,0,0.06);
    margin-top: 32px;
}

[data-theme="dark"] .comments-section {
    background: #1e293b;
    border-color: rgba(255,255,255,0.1);
}

/* 评论头部 */
.comments-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
}

[data-theme="dark"] .comments-header {
    border-color: rgba(255,255,255,0.1);
}

.comments-title-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.comments-icon {
    color: var(--color-primary, #2563eb);
}

.comments-title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
}

[data-theme="dark"] .comments-title {
    color: #f1f5f9;
}

.comments-count {
    font-size: 0.8rem;
    color: #64748b;
    margin-left: 8px;
}

/* 评论列表 */
.comment-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.comment-item {
    margin-bottom: 16px;
}

.comment-body {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    transition: background 0.2s;
}

[data-theme="dark"] .comment-body {
    background: rgba(255,255,255,0.05);
}

.comment-body:hover {
    background: #f1f5f9;
}

[data-theme="dark"] .comment-body:hover {
    background: rgba(255,255,255,0.08);
}

.comment-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 10px;
}

.comment-content {
    flex: 1;
    min-width: 0;
}

.comment-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.comment-author {
    font-weight: 600;
    font-size: 0.9rem;
    color: #1e293b;
}

[data-theme="dark"] .comment-author {
    color: #f1f5f9;
}

.comment-date {
    font-size: 0.75rem;
    color: #94a3b8;
}

.comment-awaiting {
    font-size: 0.7rem;
    padding: 2px 8px;
    background: #fef3c7;
    color: #b45309;
    border-radius: 10px;
}

.comment-text {
    color: #475569;
    font-size: 0.9rem;
    line-height: 1.6;
}

.comment-text p {
    margin: 0;
}

[data-theme="dark"] .comment-text {
    color: #cbd5e1;
}

.comment-actions {
    margin-top: 10px;
}

.comment-reply-link {
    font-size: 0.8rem;
    color: #64748b;
    text-decoration: none;
    padding: 4px 12px;
    background: rgba(0,0,0,0.04);
    border-radius: 20px;
    transition: all 0.2s;
}

.comment-reply-link:hover {
    background: var(--color-primary, #2563eb);
    color: #fff;
}

/* 子评论 */
.comment-list .children {
    list-style: none;
    margin: 12px 0 0 24px;
    padding-left: 16px;
    border-left: 2px solid rgba(0,0,0,0.06);
}

[data-theme="dark"] .comment-list .children {
    border-color: rgba(255,255,255,0.1);
}

/* 分页 */
.comment-pagination {
    display: flex;
    justify-content: space-between;
    padding-top: 16px;
    margin-top: 16px;
    border-top: 1px solid rgba(0,0,0,0.06);
}

.comment-pagination a {
    color: var(--color-primary, #2563eb);
    text-decoration: none;
    font-size: 0.9rem;
}

/* 无评论提示 - 精简版 */
.no-comments-hint {
    text-align: center;
    color: #94a3b8;
    font-size: 0.9rem;
    padding: 16px 0;
    margin: 0;
}

/* 评论已关闭 */
.comments-closed-notice {
    text-align: center;
    color: #94a3b8;
    font-size: 0.85rem;
    padding: 12px;
    background: rgba(0,0,0,0.02);
    border-radius: 8px;
    margin: 16px 0 0;
}

/* 需要登录提示 */
.comment-login-required {
    text-align: center;
    padding: 32px 20px;
    border-top: 1px solid rgba(0,0,0,0.06);
    margin-top: 20px;
}

.login-required-icon {
    font-size: 2rem;
    margin-bottom: 12px;
}

.login-required-text {
    color: #64748b;
    margin: 0 0 16px;
}

.login-required-text a {
    color: var(--color-primary, #2563eb);
    text-decoration: none;
    font-weight: 500;
}

.btn-login {
    display: inline-block;
    padding: 10px 24px;
    background: var(--color-primary, #2563eb);
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
}

/* ========================================
   评论表单
   ======================================== */

.comment-form-section {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid rgba(0,0,0,0.06);
}

[data-theme="dark"] .comment-form-section {
    border-color: rgba(255,255,255,0.1);
}

.form-title {
    margin: 0 0 16px;
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 12px;
}

[data-theme="dark"] .form-title {
    color: #f1f5f9;
}

.form-title small a {
    font-size: 0.8rem;
    font-weight: 500;
    color: #ef4444;
    text-decoration: none;
    padding: 4px 10px;
    background: rgba(239,68,68,0.1);
    border-radius: 20px;
}

.form-title small a:hover {
    background: #ef4444;
    color: #fff;
}

/* 已登录用户 */
.logged-user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
}

[data-theme="dark"] .logged-user-info {
    background: rgba(255,255,255,0.05);
}

.logged-user-info img {
    border-radius: 8px;
}

.logged-user-info .user-name {
    font-weight: 500;
    color: #1e293b;
    flex: 1;
}

[data-theme="dark"] .logged-user-info .user-name {
    color: #f1f5f9;
}

.logout-link {
    font-size: 0.8rem;
    color: #94a3b8;
    text-decoration: none;
}

.logout-link:hover {
    color: #ef4444;
}

/* 表单字段 */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 12px;
}

@media (max-width: 480px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.form-field {
    margin-bottom: 12px;
}

.form-field input,
.form-field textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 8px;
    font-size: 0.9rem;
    color: #1e293b;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
}

[data-theme="dark"] .form-field input,
[data-theme="dark"] .form-field textarea {
    background: rgba(0,0,0,0.2);
    border-color: rgba(255,255,255,0.1);
    color: #f1f5f9;
}

.form-field input:focus,
.form-field textarea:focus {
    outline: none;
    border-color: var(--color-primary, #2563eb);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}

.form-field textarea {
    resize: vertical;
    min-height: 80px;
}

.form-actions {
    margin-top: 4px;
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--color-primary, #2563eb);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
}

/* 响应式 */
@media (max-width: 640px) {
    .comments-section {
        padding: 20px 16px;
    }
    
    .comment-body {
        padding: 12px;
    }
    
    .comment-list .children {
        margin-left: 12px;
        padding-left: 12px;
    }
}
</style>
