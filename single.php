<?php
/**
 * 文章详情页模板（增强版）
 * 
 * 支持：正文样式自定义、TOC目录、作者信息卡片、版权信息、阅读统计等
 *
 * @package Developer_Starter
 */

get_header();

// 获取主题选项
$options = get_option( 'developer_starter_options', array() );

// 检查侧边栏是否显示
$hide_sidebar = ! empty( $options['hide_post_sidebar'] ) && $options['hide_post_sidebar'] === '1';
$has_sidebar = ! $hide_sidebar && is_active_sidebar( 'sidebar-post' );

// 获取 TOC 设置
$toc_enable = ! empty( $options['toc_enable'] );
$toc_position = isset( $options['toc_position'] ) ? $options['toc_position'] : 'sidebar';

// 获取统计设置
$post_views_enable = ! empty( $options['post_views_enable'] );
$reading_time_enable = ! empty( $options['reading_time_enable'] );

// 获取正文样式
$content_style_vars = Developer_Starter\Core\Post_Enhancer::get_content_style_vars();
$style_string = '';
foreach ( $content_style_vars as $var => $value ) {
    $style_string .= $var . ':' . $value . ';';
}

// 获取浏览量和阅读时长
$post_views = Developer_Starter\Core\Post_Enhancer::get_post_views();
$reading_time = Developer_Starter\Core\Post_Enhancer::get_reading_time();

// 生成 TOC
$toc_data = array( 'toc' => '', 'content' => '' );
if ( $toc_enable && have_posts() ) {
    global $post;
    $toc_data = Developer_Starter\Core\Post_Enhancer::generate_toc( $post->post_content );
}
?>

<div class="page-header" style="background: var(--color-gray-900); padding: 100px 0 60px;">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <?php 
            $categories = get_the_category();
            if ( ! empty( $categories ) ) : ?>
                <div style="margin-bottom: 15px;">
                    <a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>" 
                       style="color: var(--color-primary-light); text-decoration: none;">
                        <?php echo esc_html( $categories[0]->name ); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <h1 class="page-title" style="color: #fff; font-size: 2rem; margin: 0; line-height: 1.3;">
                <?php the_title(); ?>
            </h1>
            
            <div class="post-meta-stats" style="color: rgba(255,255,255,0.6); margin-top: 20px; font-size: 0.9rem; justify-content: center;">
                <span class="meta-stat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <?php echo get_the_date(); ?>
                </span>
                <span class="meta-stat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <?php echo get_the_author(); ?>
                </span>
                
                <?php if ( $post_views_enable ) : ?>
                <span class="meta-stat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    <?php echo number_format( $post_views ); ?> 阅读
                </span>
                <?php endif; ?>
                
                <?php if ( $reading_time_enable ) : ?>
                <span class="meta-stat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <?php printf( __( '%d 分钟阅读', 'developer-starter' ), $reading_time ); ?>
                </span>
                <?php endif; ?>
                
                <?php if ( comments_open() || get_comments_number() ) : ?>
                <span class="meta-stat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <?php echo get_comments_number(); ?> 评论
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<article class="single-post section-padding" style="<?php echo esc_attr( $style_string ); ?>">
    <div class="container">
        <div class="post-layout <?php echo $has_sidebar ? 'has-sidebar' : 'no-sidebar'; ?> <?php echo $toc_enable && $toc_position === 'sidebar' && $toc_data['toc'] ? 'has-toc-sidebar' : ''; ?>">
            
            <?php // TOC 在正文开头位置 ?>
            <?php if ( $toc_enable && $toc_position === 'before_content' && $toc_data['toc'] ) : ?>
                <div class="toc-before-content">
                    <?php echo $toc_data['toc']; ?>
                </div>
            <?php endif; ?>
            
            <div class="post-main-content">
                <?php 
                // 判断是否显示封面图
                $show_thumbnail = false;
                if ( has_post_thumbnail() ) {
                    $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                    $first_image = developer_starter_get_first_image( get_the_ID() );
                    
                    if ( $first_image ) {
                        $thumb_clean = preg_replace( '/-\d+x\d+\./', '.', basename( $thumbnail_url ) );
                        $first_clean = preg_replace( '/-\d+x\d+\./', '.', basename( $first_image ) );
                        if ( $thumb_clean !== $first_clean ) {
                            $show_thumbnail = true;
                        }
                    } else {
                        $show_thumbnail = true;
                    }
                }
                
                if ( $show_thumbnail ) : ?>
                    <div class="post-thumbnail" style="margin-bottom: 40px; border-radius: 12px; overflow: hidden;">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                <?php endif; ?>
                
                <div class="entry-content">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        // 如果 TOC 启用，使用处理后的内容
                        if ( $toc_enable && $toc_data['content'] ) {
                            echo apply_filters( 'the_content', $toc_data['content'] );
                        } else {
                            the_content();
                        }
                    endwhile;
                    ?>
                </div>
                
                <!-- Tags -->
                <?php
                $tags = get_the_tags();
                if ( $tags ) :
                ?>
                    <div class="post-tags" style="margin-top: 50px; padding-top: 30px; border-top: 1px solid var(--color-gray-200);">
                        <strong>标签：</strong>
                        <?php foreach ( $tags as $tag ) : ?>
                            <a href="<?php echo get_tag_link( $tag->term_id ); ?>" 
                               style="display: inline-block; padding: 4px 12px; background: var(--color-gray-100); border-radius: 20px; margin: 4px; font-size: 0.875rem; color: var(--color-gray-600);">
                                <?php echo esc_html( $tag->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Copyright -->
                <?php echo Developer_Starter\Core\Post_Enhancer::render_copyright(); ?>
                
                <!-- Author Box -->
                <?php echo Developer_Starter\Core\Post_Enhancer::render_author_box(); ?>
                
                <!-- Post Navigation -->
                <nav class="post-navigation" style="margin-top: 50px; padding-top: 30px; border-top: 1px solid var(--color-gray-200); display: flex; justify-content: space-between;">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>
                    
                    <div style="flex: 1; padding-right: 20px;">
                        <?php if ( $prev_post ) : ?>
                            <span style="color: var(--color-gray-500); font-size: 0.875rem;">← 上一篇</span>
                            <a href="<?php echo get_permalink( $prev_post->ID ); ?>" style="display: block; margin-top: 5px; font-weight: 500;">
                                <?php echo esc_html( $prev_post->post_title ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div style="flex: 1; text-align: right; padding-left: 20px;">
                        <?php if ( $next_post ) : ?>
                            <span style="color: var(--color-gray-500); font-size: 0.875rem;">下一篇 →</span>
                            <a href="<?php echo get_permalink( $next_post->ID ); ?>" style="display: block; margin-top: 5px; font-weight: 500;">
                                <?php echo esc_html( $next_post->post_title ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </nav>
                
                <!-- Comments -->
                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <div style="margin-top: 60px;">
                        <?php comments_template(); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php // 侧边栏区域（包含TOC或小工具） ?>
            <?php if ( $has_sidebar || ( $toc_enable && $toc_position === 'sidebar' && $toc_data['toc'] ) ) : ?>
                <div class="post-sidebar toc-sidebar">
                    <?php // TOC 在侧边栏位置 ?>
                    <?php if ( $toc_enable && $toc_position === 'sidebar' && $toc_data['toc'] ) : ?>
                        <?php echo $toc_data['toc']; ?>
                    <?php endif; ?>
                    
                    <?php if ( $has_sidebar ) : ?>
                        <?php get_sidebar(); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>

<!-- Related Posts -->
<?php
$related_args = array(
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post__not_in'   => array( get_the_ID() ),
    'orderby'        => 'rand',
);

if ( ! empty( $categories ) ) {
    $related_args['cat'] = $categories[0]->term_id;
}

$related = new WP_Query( $related_args );

if ( $related->have_posts() ) :
?>
<section class="related-posts section-padding bg-light">
    <div class="container">
        <h2 class="section-title text-center" style="margin-bottom: 40px;">相关文章</h2>
        <div class="news-grid grid-cols-3">
            <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <article class="news-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="news-thumb">
                            <?php the_post_thumbnail( 'medium_large' ); ?>
                        </a>
                    <?php endif; ?>
                    <div class="news-content">
                        <span class="news-date"><?php echo get_the_date(); ?></span>
                        <h3 class="news-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
/* TOC 侧边栏布局调整 */
.post-layout.has-toc-sidebar {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 40px;
}

.post-layout.has-toc-sidebar .post-sidebar {
    position: relative;
}

@media (max-width: 1024px) {
    .post-layout.has-toc-sidebar {
        grid-template-columns: 1fr;
    }
    
    .post-layout.has-toc-sidebar .post-sidebar {
        order: -1;
    }
}
</style>

<?php get_footer(); ?>
