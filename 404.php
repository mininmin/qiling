<?php
/**
 * 404页面模板
 *
 * @package Developer_Starter
 */

get_header();
?>

<div class="error-404-page" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 60px 0;">
    <div class="container text-center">
        <div class="error-code" style="font-size: 8rem; font-weight: 900; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1;">
            404
        </div>
        
        <h1 style="font-size: 2rem; margin: 30px 0 15px;"><?php esc_html_e( '页面未找到', 'developer-starter' ); ?></h1>
        
        <p style="color: var(--color-gray-600); max-width: 500px; margin: 0 auto 40px;">
            <?php esc_html_e( '抱歉，您访问的页面不存在或已被移除。请检查网址是否正确，或返回首页继续浏览。', 'developer-starter' ); ?>
        </p>
        
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo home_url( '/' ); ?>" class="btn btn-primary"><?php esc_html_e( '返回首页', 'developer-starter' ); ?></a>
            <button onclick="history.back()" class="btn btn-outline"><?php esc_html_e( '返回上页', 'developer-starter' ); ?></button>
        </div>
        
        <div style="margin-top: 60px;">
            <p style="color: var(--color-gray-500); margin-bottom: 20px;"><?php esc_html_e( '也许您可以试试搜索：', 'developer-starter' ); ?></p>
            <form role="search" method="get" action="<?php echo home_url( '/' ); ?>" style="max-width: 400px; margin: 0 auto;">
                <div style="display: flex; gap: 10px;">
                    <input type="search" name="s" placeholder="<?php esc_attr_e( '输入关键词搜索...', 'developer-starter' ); ?>" 
                           style="flex: 1; padding: 12px 16px; border: 1px solid var(--color-gray-300); border-radius: 8px; font-size: 1rem;" />
                    <button type="submit" class="btn btn-primary"><?php esc_html_e( '搜索', 'developer-starter' ); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php get_footer(); ?>
