<?php
/**
 * 评论模板 - 美化版
 *
 * @package Developer_Starter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area" style="background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
    
    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title" style="font-size: 1.5rem; margin: 0 0 30px; padding-bottom: 20px; border-bottom: 2px solid var(--color-gray-100); display: flex; align-items: center; gap: 10px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <?php
            $comment_count = get_comments_number();
            printf(
                _n( '%s 条评论', '%s 条评论', $comment_count, 'developer-starter' ),
                number_format_i18n( $comment_count )
            );
            ?>
        </h2>

        <ol class="comment-list" style="list-style: none; margin: 0; padding: 0;">
            <?php
            wp_list_comments( array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 50,
                'callback'    => 'developer_starter_comment_callback',
            ) );
            ?>
        </ol>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
            <nav class="comment-navigation" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--color-gray-200); display: flex; justify-content: space-between;">
                <div class="nav-previous"><?php previous_comments_link( __( '← 较早的评论', 'developer-starter' ) ); ?></div>
                <div class="nav-next"><?php next_comments_link( __( '更新的评论 →', 'developer-starter' ) ); ?></div>
            </nav>
        <?php endif; ?>

        <?php if ( ! comments_open() ) : ?>
            <p class="no-comments" style="margin-top: 20px; padding: 20px; background: var(--color-gray-100); border-radius: 8px; text-align: center; color: var(--color-gray-600);">
                <?php esc_html_e( '评论已关闭。', 'developer-starter' ); ?>
            </p>
        <?php endif; ?>

    <?php endif; ?>

    <?php
    // 自定义评论表单
    $commenter = wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true' required" : '' );
    
    $fields = array(
        'author' => '<div class="comment-form-author" style="margin-bottom: 20px;">
            <label for="author" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-gray-600);">' . __( '昵称', 'developer-starter' ) . ( $req ? ' <span style="color: #ef4444;">*</span>' : '' ) . '</label>
            <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" style="width: 100%; padding: 12px 16px; border: 2px solid var(--color-gray-200); border-radius: 10px; font-size: 1rem; transition: border-color 0.3s;" placeholder="' . __( '您的昵称', 'developer-starter' ) . '"' . $aria_req . ' />
        </div>',
        'email'  => '<div class="comment-form-email" style="margin-bottom: 20px;">
            <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-gray-600);">' . __( '邮箱', 'developer-starter' ) . ( $req ? ' <span style="color: #ef4444;">*</span>' : '' ) . '</label>
            <input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" style="width: 100%; padding: 12px 16px; border: 2px solid var(--color-gray-200); border-radius: 10px; font-size: 1rem; transition: border-color 0.3s;" placeholder="' . __( '您的邮箱（不会公开）', 'developer-starter' ) . '"' . $aria_req . ' />
        </div>',
        'url'    => '<div class="comment-form-url" style="margin-bottom: 20px;">
            <label for="url" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-gray-600);">' . __( '网站', 'developer-starter' ) . '</label>
            <input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" style="width: 100%; padding: 12px 16px; border: 2px solid var(--color-gray-200); border-radius: 10px; font-size: 1rem; transition: border-color 0.3s;" placeholder="' . __( '您的网站（可选）', 'developer-starter' ) . '" />
        </div>',
    );

    comment_form( array(
        'fields'               => $fields,
        'title_reply'          => '<span style="display: flex; align-items: center; gap: 10px;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>' . __( '发表评论', 'developer-starter' ) . '</span>',
        'title_reply_to'       => __( '回复 %s', 'developer-starter' ),
        'cancel_reply_link'    => __( '取消回复', 'developer-starter' ),
        'label_submit'         => __( '提交评论', 'developer-starter' ),
        'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s" style="background: linear-gradient(135deg, var(--color-primary) 0%%, #7c3aed 100%%); color: #fff; border: none; padding: 14px 32px; font-size: 1rem; font-weight: 600; border-radius: 10px; cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;">%4$s</button>',
        'comment_field'        => '<div class="comment-form-comment" style="margin-bottom: 20px;">
            <label for="comment" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-gray-600);">' . __( '评论内容', 'developer-starter' ) . ' <span style="color: #ef4444;">*</span></label>
            <textarea id="comment" name="comment" rows="6" style="width: 100%; padding: 16px; border: 2px solid var(--color-gray-200); border-radius: 10px; font-size: 1rem; resize: vertical; transition: border-color 0.3s;" placeholder="' . __( '写下您的评论...', 'developer-starter' ) . '" required></textarea>
        </div>',
        'class_form'           => 'comment-form',
        'logged_in_as'         => '<p class="logged-in-as" style="margin-bottom: 20px; padding: 15px; background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(124, 58, 237, 0.05) 100%); border-radius: 10px;">' . sprintf(
            __( '已登录为 <a href="%1$s" style="color: var(--color-primary); font-weight: 600;">%2$s</a>。<a href="%3$s" style="color: var(--color-gray-500);">登出？</a>', 'developer-starter' ),
            get_edit_user_link(),
            wp_get_current_user()->display_name,
            wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) )
        ) . '</p>',
        'comment_notes_before' => '<p class="comment-notes" style="margin-bottom: 20px; color: var(--color-gray-500); font-size: 0.9rem;"><span id="email-notes">' . __( '您的邮箱地址不会被公开。', 'developer-starter' ) . '</span></p>',
        'class_submit'         => 'submit-btn',
    ) );
    ?>
    
</div>

<style>
.comment-form input:focus,
.comment-form textarea:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.comment-form .submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
}
.comment-respond {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 2px solid var(--color-gray-100);
}
.comment-reply-title {
    font-size: 1.25rem;
    margin: 0 0 25px;
    color: var(--color-dark);
}
.comment-reply-title small {
    font-size: 0.875rem;
    margin-left: 15px;
}
.comment-reply-title small a {
    color: var(--color-gray-500);
}
</style>

<?php
// 自定义评论回调函数
if ( ! function_exists( 'developer_starter_comment_callback' ) ) {
    function developer_starter_comment_callback( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        ?>
        <li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment-item' ); ?> style="margin-bottom: 25px;">
            <article class="comment-body" style="display: flex; gap: 20px; padding: 25px; background: var(--color-gray-100); border-radius: 12px;">
                <div class="comment-avatar" style="flex-shrink: 0;">
                    <?php echo get_avatar( $comment, 50, '', '', array( 'style' => 'border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.1);' ) ); ?>
                </div>
                <div class="comment-content" style="flex: 1; min-width: 0;">
                    <div class="comment-meta" style="display: flex; align-items: center; gap: 15px; margin-bottom: 12px; flex-wrap: wrap;">
                        <span class="comment-author" style="font-weight: 700; color: var(--color-dark);">
                            <?php echo get_comment_author_link(); ?>
                        </span>
                        <span class="comment-date" style="font-size: 0.85rem; color: var(--color-gray-500);">
                            <?php printf( __( '%1$s %2$s', 'developer-starter' ), get_comment_date(), get_comment_time() ); ?>
                        </span>
                        <?php if ( $comment->comment_approved == '0' ) : ?>
                            <span style="font-size: 0.8rem; padding: 2px 10px; background: #fef3c7; color: #d97706; border-radius: 20px;">
                                <?php esc_html_e( '待审核', 'developer-starter' ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="comment-text" style="color: var(--color-gray-600); line-height: 1.7;">
                        <?php comment_text(); ?>
                    </div>
                    <div class="comment-actions" style="margin-top: 15px;">
                        <?php
                        comment_reply_link( array_merge( $args, array(
                            'depth'     => $depth,
                            'max_depth' => $args['max_depth'],
                            'before'    => '<span style="font-size: 0.9rem;">',
                            'after'     => '</span>',
                        ) ) );
                        ?>
                    </div>
                </div>
            </article>
        <?php
    }
}
?>
