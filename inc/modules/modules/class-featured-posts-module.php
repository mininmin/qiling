<?php
/**
 * Featured Posts Module - 博客置顶推荐模块
 *
 * 支持轮播滚动文章展示，用于博客顶部运营引导
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Featured_Posts_Module extends Module_Base {

    public function __construct() {
        $this->category = 'content';
        $this->icon = 'dashicons-star-filled';
        $this->description = __( '博客置顶推荐，支持轮播和文章列表展示', 'developer-starter' );
    }

    public function get_id() {
        return 'featured_posts';
    }

    public function get_name() {
        return __( '博客置顶推荐', 'developer-starter' );
    }

    public function render( $data = array() ) {
        // 基础配置
        $title = isset( $data['fp_title'] ) ? $data['fp_title'] : '';
        $bg_color = isset( $data['fp_bg_color'] ) ? $data['fp_bg_color'] : '';
        
        // 布局配置
        $layout = isset( $data['fp_layout'] ) ? $data['fp_layout'] : 'full'; // full | dual
        $slider_ratio = isset( $data['fp_slider_ratio'] ) ? $data['fp_slider_ratio'] : '65'; // 轮播区域占比
        
        // 轮播配置
        $autoplay = isset( $data['fp_autoplay'] ) && $data['fp_autoplay'] === 'yes';
        $interval = isset( $data['fp_interval'] ) && $data['fp_interval'] !== '' ? intval( $data['fp_interval'] ) : 5000;
        $effect = isset( $data['fp_effect'] ) ? $data['fp_effect'] : 'slide'; // slide | fade
        $show_arrows = isset( $data['fp_show_arrows'] ) && $data['fp_show_arrows'] === 'yes';
        $show_dots = isset( $data['fp_show_dots'] ) && $data['fp_show_dots'] === 'yes';
        $slider_height = isset( $data['fp_slider_height'] ) && $data['fp_slider_height'] !== '' ? $data['fp_slider_height'] : '400px';
        
        // 数据来源 - 轮播
        $slider_source = isset( $data['fp_slider_source'] ) ? $data['fp_slider_source'] : 'latest';
        $slider_ids = isset( $data['fp_slider_ids'] ) ? $data['fp_slider_ids'] : '';
        $slider_category = isset( $data['fp_slider_category'] ) ? $data['fp_slider_category'] : '';
        $slider_count = isset( $data['fp_slider_count'] ) && $data['fp_slider_count'] !== '' ? intval( $data['fp_slider_count'] ) : 5;
        
        // 数据来源 - 右侧列表
        $list_source = isset( $data['fp_list_source'] ) ? $data['fp_list_source'] : 'latest';
        $list_ids = isset( $data['fp_list_ids'] ) ? $data['fp_list_ids'] : '';
        $list_category = isset( $data['fp_list_category'] ) ? $data['fp_list_category'] : '';
        $list_count = isset( $data['fp_list_count'] ) && $data['fp_list_count'] !== '' ? intval( $data['fp_list_count'] ) : 4;
        
        // 角标配置
        $badge_type = isset( $data['fp_badge_type'] ) ? $data['fp_badge_type'] : 'none'; // none | recommend | hot | featured | top | custom
        $badge_text = isset( $data['fp_badge_text'] ) ? $data['fp_badge_text'] : '';
        $badge_position = isset( $data['fp_badge_position'] ) ? $data['fp_badge_position'] : 'left'; // left | right
        $badge_color = isset( $data['fp_badge_color'] ) ? $data['fp_badge_color'] : '';
        
        // 显示控制
        $show_category = isset( $data['fp_show_category'] ) && $data['fp_show_category'] === 'yes';
        $show_author = isset( $data['fp_show_author'] ) && $data['fp_show_author'] === 'yes';
        $show_date = isset( $data['fp_show_date'] ) && $data['fp_show_date'] === 'yes';
        $show_excerpt = isset( $data['fp_show_excerpt'] ) && $data['fp_show_excerpt'] === 'yes';
        
        // 获取轮播文章
        $slider_posts = $this->get_posts( $slider_source, $slider_ids, $slider_category, $slider_count );
        
        // 获取列表文章（双栏布局时）
        $list_posts = array();
        if ( $layout === 'dual' ) {
            $list_posts = $this->get_posts( $list_source, $list_ids, $list_category, $list_count );
        }
        
        $module_id = 'featured-posts-' . uniqid();
        
        // 背景样式
        $section_style = '';
        if ( ! empty( $bg_color ) ) {
            if ( strpos( $bg_color, 'gradient' ) !== false ) {
                $section_style = 'background: ' . $bg_color . ';';
            } else {
                $section_style = 'background-color: ' . $bg_color . ';';
            }
        }
        
        // 角标文字
        $badge_labels = array(
            'recommend' => __( '推荐', 'developer-starter' ),
            'hot' => __( '热门', 'developer-starter' ),
            'featured' => __( '精选', 'developer-starter' ),
            'top' => __( '置顶', 'developer-starter' ),
            'custom' => $badge_text,
        );
        $badge_label = isset( $badge_labels[ $badge_type ] ) ? $badge_labels[ $badge_type ] : '';
        
        ?>
        <section class="module module-featured-posts section-padding" id="<?php echo esc_attr( $module_id ); ?>" <?php echo $section_style ? 'style="' . esc_attr( $section_style ) . '"' : ''; ?>>
            <div class="container">
                <?php if ( $title ) : ?>
                    <div class="section-header" style="margin-bottom: 24px;">
                        <h2 class="section-title" style="font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo esc_html( $title ); ?></h2>
                    </div>
                <?php endif; ?>
                
                <div class="fp-wrapper fp-layout-<?php echo esc_attr( $layout ); ?>">
                    <!-- 轮播区域 -->
                    <div class="fp-slider-wrapper" style="<?php echo $layout === 'dual' ? 'width: ' . esc_attr( $slider_ratio ) . '%;' : ''; ?>">
                        <?php if ( ! empty( $slider_posts ) ) : ?>
                        <div class="fp-slider" data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>" data-interval="<?php echo esc_attr( $interval ); ?>" data-effect="<?php echo esc_attr( $effect ); ?>" style="height: <?php echo esc_attr( $slider_height ); ?>;">
                            <?php foreach ( $slider_posts as $index => $post ) : 
                                $image = $this->get_post_image( $post->ID );
                                $categories = get_the_category( $post->ID );
                                $cat = ! empty( $categories ) ? $categories[0] : null;
                            ?>
                                <div class="fp-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                    <a href="<?php echo get_permalink( $post->ID ); ?>" class="fp-slide-link">
                                        <div class="fp-slide-image" style="background-image: url('<?php echo esc_url( $image ); ?>');"></div>
                                        <div class="fp-slide-overlay"></div>
                                        <div class="fp-slide-content">
                                            <?php if ( $badge_type !== 'none' && $badge_label ) : ?>
                                                <span class="fp-badge fp-badge-<?php echo esc_attr( $badge_position ); ?>" <?php echo $badge_color ? 'style="background:' . esc_attr( $badge_color ) . ';"' : ''; ?>>
                                                    <?php echo esc_html( $badge_label ); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <div class="fp-slide-meta">
                                                <?php if ( $show_category && $cat ) : ?>
                                                    <span class="fp-category"><?php echo esc_html( $cat->name ); ?></span>
                                                <?php endif; ?>
                                                <?php if ( $show_date ) : ?>
                                                    <span class="fp-date"><?php echo get_the_date( '', $post->ID ); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <h3 class="fp-slide-title"><?php echo esc_html( $post->post_title ); ?></h3>
                                            
                                            <?php if ( $show_excerpt ) : ?>
                                                <p class="fp-slide-excerpt"><?php echo wp_trim_words( get_the_excerpt( $post->ID ), 20 ); ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if ( $show_author ) : ?>
                                                <div class="fp-author">
                                                    <img src="<?php echo esc_url( get_avatar_url( $post->post_author, array( 'size' => 32 ) ) ); ?>" alt="" class="fp-author-avatar">
                                                    <span class="fp-author-name"><?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if ( $show_arrows && count( $slider_posts ) > 1 ) : ?>
                                <button class="fp-arrow fp-arrow-prev" aria-label="<?php _e( '上一张', 'developer-starter' ); ?>">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                </button>
                                <button class="fp-arrow fp-arrow-next" aria-label="<?php _e( '下一张', 'developer-starter' ); ?>">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </button>
                            <?php endif; ?>
                            
                            <?php if ( $show_dots && count( $slider_posts ) > 1 ) : ?>
                                <div class="fp-dots">
                                    <?php for ( $i = 0; $i < count( $slider_posts ); $i++ ) : ?>
                                        <button class="fp-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></button>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php else : ?>
                        <!-- 空轮播提示 -->
                        <div class="fp-empty" style="height: <?php echo esc_attr( $slider_height ); ?>; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; color: #fff;">
                            <div style="text-align: center;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.8; margin-bottom: 12px;"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                                <p style="margin: 0; font-size: 14px; opacity: 0.9;"><?php _e( '暂无轮播文章，请在后台配置数据来源', 'developer-starter' ); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ( $layout === 'dual' && ! empty( $list_posts ) ) : ?>
                    <!-- 右侧文章列表 -->
                    <div class="fp-list-wrapper" style="width: <?php echo 100 - intval( $slider_ratio ); ?>%;">
                        <div class="fp-list">
                            <?php foreach ( $list_posts as $index => $post ) : 
                                $image = $this->get_post_image( $post->ID );
                                $categories = get_the_category( $post->ID );
                                $cat = ! empty( $categories ) ? $categories[0] : null;
                            ?>
                                <a href="<?php echo get_permalink( $post->ID ); ?>" class="fp-list-item <?php echo $index === 0 ? 'fp-list-item-featured' : ''; ?>">
                                    <div class="fp-list-image" style="background-image: url('<?php echo esc_url( $image ); ?>');"></div>
                                    <div class="fp-list-overlay"></div>
                                    <div class="fp-list-content">
                                        <?php if ( $show_category && $cat && $index === 0 ) : ?>
                                            <span class="fp-list-category"><?php echo esc_html( $cat->name ); ?></span>
                                        <?php endif; ?>
                                        <h4 class="fp-list-title"><?php echo esc_html( $post->post_title ); ?></h4>
                                        <?php if ( $show_date && $index === 0 ) : ?>
                                            <span class="fp-list-date"><?php echo get_the_date( '', $post->ID ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <style>
            #<?php echo esc_attr( $module_id ); ?> .fp-wrapper {
                display: flex;
                gap: 16px;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-wrapper.fp-layout-full .fp-slider-wrapper {
                width: 100%;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slider {
                position: relative;
                border-radius: 16px;
                overflow: hidden;
                min-height: 200px;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0;
                transition: opacity 0.5s ease, transform 0.5s ease;
                z-index: 0;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide.active {
                position: relative;
                opacity: 1;
                z-index: 1;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide-link {
                display: block;
                position: relative;
                height: 100%;
                text-decoration: none;
                color: #fff;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide-image {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-size: cover;
                background-position: center;
                transition: transform 0.5s ease;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide:hover .fp-slide-image {
                transform: scale(1.05);
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.1) 100%);
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide-content {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 30px;
                z-index: 2;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-badge {
                display: inline-block;
                padding: 6px 14px;
                background: linear-gradient(135deg, #ff6b6b, #ee5a24);
                color: #fff;
                font-size: 0.75rem;
                font-weight: 600;
                border-radius: 20px;
                margin-bottom: 12px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide-meta {
                display: flex;
                gap: 12px;
                margin-bottom: 10px;
                font-size: 0.85rem;
                opacity: 0.9;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-category {
                background: rgba(255,255,255,0.2);
                padding: 4px 12px;
                border-radius: 15px;
                backdrop-filter: blur(10px);
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide-title {
                font-size: 1.6rem;
                font-weight: 700;
                line-height: 1.3;
                margin: 0 0 10px;
                text-shadow: 0 2px 8px rgba(0,0,0,0.3);
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-slide-excerpt {
                font-size: 0.95rem;
                opacity: 0.85;
                margin: 0 0 15px;
                line-height: 1.5;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-author {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-author-avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                border: 2px solid rgba(255,255,255,0.3);
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-author-name {
                font-size: 0.9rem;
                opacity: 0.9;
            }
            /* 箭头控制 */
            #<?php echo esc_attr( $module_id ); ?> .fp-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 48px;
                height: 48px;
                background: rgba(255,255,255,0.15);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 50%;
                color: #fff;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                z-index: 10;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-arrow:hover {
                background: rgba(255,255,255,0.3);
                transform: translateY(-50%) scale(1.1);
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-arrow-prev { left: 20px; }
            #<?php echo esc_attr( $module_id ); ?> .fp-arrow-next { right: 20px; }
            /* 导航点 */
            #<?php echo esc_attr( $module_id ); ?> .fp-dots {
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 8px;
                z-index: 10;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: rgba(255,255,255,0.4);
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-dot.active,
            #<?php echo esc_attr( $module_id ); ?> .fp-dot:hover {
                background: #fff;
                transform: scale(1.2);
            }
            /* 右侧列表 */
            #<?php echo esc_attr( $module_id ); ?> .fp-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                height: <?php echo esc_attr( $slider_height ); ?>;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-item {
                position: relative;
                flex: 1;
                border-radius: 12px;
                overflow: hidden;
                text-decoration: none;
                color: #fff;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-item-featured {
                flex: 2;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-image {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-size: cover;
                background-position: center;
                transition: transform 0.4s ease;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-item:hover .fp-list-image {
                transform: scale(1.08);
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.2) 100%);
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-content {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 16px;
                z-index: 2;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-category {
                display: inline-block;
                padding: 3px 10px;
                background: var(--color-primary, #2563eb);
                font-size: 0.7rem;
                font-weight: 600;
                border-radius: 12px;
                margin-bottom: 8px;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-title {
                font-size: 0.95rem;
                font-weight: 600;
                margin: 0;
                line-height: 1.4;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-item-featured .fp-list-title {
                font-size: 1.1rem;
            }
            #<?php echo esc_attr( $module_id ); ?> .fp-list-date {
                font-size: 0.75rem;
                opacity: 0.7;
                margin-top: 6px;
                display: block;
            }
            /* 响应式 */
            @media (max-width: 992px) {
                #<?php echo esc_attr( $module_id ); ?> .fp-wrapper {
                    flex-direction: column;
                }
                #<?php echo esc_attr( $module_id ); ?> .fp-wrapper.fp-layout-dual .fp-slider-wrapper,
                #<?php echo esc_attr( $module_id ); ?> .fp-list-wrapper {
                    width: 100% !important;
                }
                #<?php echo esc_attr( $module_id ); ?> .fp-list {
                    flex-direction: row;
                    flex-wrap: wrap;
                    height: auto;
                }
                #<?php echo esc_attr( $module_id ); ?> .fp-list-item {
                    flex: 1 1 calc(50% - 6px);
                    min-height: 150px;
                }
                #<?php echo esc_attr( $module_id ); ?> .fp-list-item-featured {
                    flex: 1 1 100%;
                    min-height: 180px;
                }
            }
            @media (max-width: 576px) {
                #<?php echo esc_attr( $module_id ); ?> .fp-slide-title {
                    font-size: 1.2rem;
                }
                #<?php echo esc_attr( $module_id ); ?> .fp-slide-content {
                    padding: 20px;
                }
                #<?php echo esc_attr( $module_id ); ?> .fp-arrow {
                    width: 36px;
                    height: 36px;
                }
                #<?php echo esc_attr( $module_id ); ?> .fp-arrow svg {
                    width: 18px;
                    height: 18px;
                }
                #<?php echo esc_attr( $module_id ); ?> .fp-list-item {
                    flex: 1 1 100%;
                }
            }
            </style>
            
            <script>
            (function(){
                var module = document.getElementById('<?php echo esc_js( $module_id ); ?>');
                if (!module) return;
                
                var slider = module.querySelector('.fp-slider');
                if (!slider) return;
                
                var slides = slider.querySelectorAll('.fp-slide');
                var dots = slider.querySelectorAll('.fp-dot');
                var prevBtn = slider.querySelector('.fp-arrow-prev');
                var nextBtn = slider.querySelector('.fp-arrow-next');
                
                if (slides.length <= 1) return;
                
                var currentIndex = 0;
                var autoplayEnabled = slider.dataset.autoplay === 'true';
                var interval = parseInt(slider.dataset.interval) || 5000;
                var autoplayTimer = null;
                
                function showSlide(index) {
                    if (index < 0) index = slides.length - 1;
                    if (index >= slides.length) index = 0;
                    
                    slides.forEach(function(slide, i) {
                        slide.classList.toggle('active', i === index);
                    });
                    
                    dots.forEach(function(dot, i) {
                        dot.classList.toggle('active', i === index);
                    });
                    
                    currentIndex = index;
                }
                
                function nextSlide() {
                    showSlide(currentIndex + 1);
                }
                
                function prevSlide() {
                    showSlide(currentIndex - 1);
                }
                
                function startAutoplay() {
                    if (autoplayEnabled && !autoplayTimer) {
                        autoplayTimer = setInterval(nextSlide, interval);
                    }
                }
                
                function stopAutoplay() {
                    if (autoplayTimer) {
                        clearInterval(autoplayTimer);
                        autoplayTimer = null;
                    }
                }
                
                // 绑定事件
                if (prevBtn) {
                    prevBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        stopAutoplay();
                        prevSlide();
                        startAutoplay();
                    });
                }
                
                if (nextBtn) {
                    nextBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        stopAutoplay();
                        nextSlide();
                        startAutoplay();
                    });
                }
                
                dots.forEach(function(dot) {
                    dot.addEventListener('click', function(e) {
                        e.preventDefault();
                        stopAutoplay();
                        showSlide(parseInt(this.dataset.index));
                        startAutoplay();
                    });
                });
                
                // 鼠标悬停暂停
                slider.addEventListener('mouseenter', stopAutoplay);
                slider.addEventListener('mouseleave', startAutoplay);
                
                // 触摸滑动支持
                var touchStartX = 0;
                var touchEndX = 0;
                
                slider.addEventListener('touchstart', function(e) {
                    touchStartX = e.changedTouches[0].screenX;
                    stopAutoplay();
                }, { passive: true });
                
                slider.addEventListener('touchend', function(e) {
                    touchEndX = e.changedTouches[0].screenX;
                    var diff = touchStartX - touchEndX;
                    if (Math.abs(diff) > 50) {
                        if (diff > 0) {
                            nextSlide();
                        } else {
                            prevSlide();
                        }
                    }
                    startAutoplay();
                }, { passive: true });
                
                // 启动自动播放
                startAutoplay();
            })();
            </script>
        </section>
        <?php
    }
    
    /**
     * 获取文章列表
     */
    private function get_posts( $source, $ids = '', $category = '', $count = 5 ) {
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => $count,
            'post_status'    => 'publish',
        );
        
        switch ( $source ) {
            case 'manual':
                if ( ! empty( $ids ) ) {
                    $post_ids = array_map( 'intval', array_filter( explode( ',', $ids ) ) );
                    if ( ! empty( $post_ids ) ) {
                        $args['post__in'] = $post_ids;
                        $args['orderby'] = 'post__in';
                    } else {
                        // 如果没有有效的ID，回退到最新文章
                        $args['orderby'] = 'date';
                        $args['order'] = 'DESC';
                    }
                } else {
                    // 如果没有提供ID，回退到最新文章
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
                }
                break;
                
            case 'random':
                $args['orderby'] = 'rand';
                break;
                
            case 'popular':
                $args['meta_key'] = 'post_views_count';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
                
            case 'comment':
                $args['orderby'] = 'comment_count';
                $args['order'] = 'DESC';
                break;
                
            case 'category':
                if ( ! empty( $category ) ) {
                    $cat_ids = array_map( 'intval', array_filter( explode( ',', $category ) ) );
                    if ( ! empty( $cat_ids ) ) {
                        $args['category__in'] = $cat_ids;
                    }
                }
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
                
            case 'latest':
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
        }
        
        $query = new \WP_Query( $args );
        return $query->posts;
    }
    
    /**
     * 获取文章图片
     */
    private function get_post_image( $post_id ) {
        // 优先使用特色图片
        $image = get_the_post_thumbnail_url( $post_id, 'large' );
        
        // 如果没有特色图片，尝试获取文章中的第一张图片
        if ( ! $image ) {
            $post = get_post( $post_id );
            if ( $post && preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/', $post->post_content, $matches ) ) {
                $image = $matches[1];
            }
        }
        
        // 如果还是没有图片，使用占位图
        if ( ! $image ) {
            $image = 'data:image/svg+xml,' . rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 400" fill="#e2e8f0"><rect width="800" height="400"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#94a3b8" font-family="sans-serif" font-size="24">No Image</text></svg>' );
        }
        
        return $image;
    }
}
