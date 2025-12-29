<?php
/**
 * The template for displaying all single posts
 *
 * @package Developer_Starter
 */

get_header();
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
            
            <div style="color: rgba(255,255,255,0.6); margin-top: 20px; font-size: 0.9rem;">
                <span><?php echo get_the_date(); ?></span>
                <span style="margin: 0 10px;">·</span>
                <span><?php echo get_the_author(); ?></span>
            </div>
        </div>
    </div>
</div>

<article class="single-post section-padding">
    <div class="container">
        <div class="post-content" style="max-width: 800px; margin: 0 auto;">
            <?php 
            // 判断是否显示封面图
            // 如果封面图来自文章第一张图片，则不显示（避免重复）
            $show_thumbnail = false;
            if ( has_post_thumbnail() ) {
                $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                $first_image = developer_starter_get_first_image( get_the_ID() );
                
                // 如果封面图URL与文章第一张图片URL不同，则显示
                if ( $first_image ) {
                    // 比较URL（去除协议和尺寸后缀）
                    $thumb_clean = preg_replace( '/-\d+x\d+\./', '.', basename( $thumbnail_url ) );
                    $first_clean = preg_replace( '/-\d+x\d+\./', '.', basename( $first_image ) );
                    if ( $thumb_clean !== $first_clean ) {
                        $show_thumbnail = true;
                    }
                } else {
                    // 文章没有图片，显示封面图
                    $show_thumbnail = true;
                }
            }
            
            if ( $show_thumbnail ) : ?>
                <div class="post-thumbnail" style="margin-bottom: 40px; border-radius: 12px; overflow: hidden;">
                    <?php the_post_thumbnail( 'large' ); ?>
                </div>
            <?php endif; ?>
            
            <div class="entry-content" style="font-size: 1.125rem; line-height: 1.8;">
                <?php
                while ( have_posts() ) :
                    the_post();
                    the_content();
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
        </div>
        
        <!-- Comments -->
        <?php if ( comments_open() || get_comments_number() ) : ?>
            <div style="max-width: 800px; margin: 60px auto 0;">
                <?php comments_template(); ?>
            </div>
        <?php endif; ?>
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

<?php get_footer(); ?>
