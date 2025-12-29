<?php
/**
 * 模板标签函数
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 显示发布日期
 */
if ( ! function_exists( 'developer_starter_posted_on' ) ) {
    function developer_starter_posted_on() {
        $time = '<time class="entry-date" datetime="%1$s">%2$s</time>';
        echo sprintf( $time, esc_attr( get_the_date( DATE_W3C ) ), esc_html( get_the_date() ) );
    }
}

/**
 * 显示作者
 */
if ( ! function_exists( 'developer_starter_posted_by' ) ) {
    function developer_starter_posted_by() {
        echo '<span class="author vcard"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>';
    }
}

/**
 * 显示分类
 */
if ( ! function_exists( 'developer_starter_entry_categories' ) ) {
    function developer_starter_entry_categories() {
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            echo '<span class="cat-links">';
            foreach ( $categories as $cat ) {
                echo '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
            }
            echo '</span>';
        }
    }
}

/**
 * 显示文章底部
 */
if ( ! function_exists( 'developer_starter_entry_footer' ) ) {
    function developer_starter_entry_footer() {
        if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
            echo '<span class="comments-link">';
            comments_popup_link( __( '发表评论', 'developer-starter' ), __( '1条评论', 'developer-starter' ), __( '%条评论', 'developer-starter' ) );
            echo '</span>';
        }
        edit_post_link( __( '编辑', 'developer-starter' ), '<span class="edit-link">', '</span>' );
    }
}
