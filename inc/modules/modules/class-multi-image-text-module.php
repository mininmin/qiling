<?php
/**
 * Multi Image Text Module - 多图文模块
 *
 * 鼠标悬停切换图片的交互式图文展示模块
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Multi_Image_Text_Module extends Module_Base {

    public function __construct() {
        $this->category = 'general';
        $this->icon = 'dashicons-images-alt2';
        $this->description = '多图文悬停切换模块';
    }

    public function get_id() {
        return 'multi_image_text';
    }

    public function get_name() {
        return '多图文模块';
    }

    public function render( $data = array() ) {
        // 获取模块配置
        $title = isset( $data['multi_image_text_title'] ) && $data['multi_image_text_title'] !== '' 
            ? $data['multi_image_text_title'] : '';
        $subtitle = isset( $data['multi_image_text_subtitle'] ) ? $data['multi_image_text_subtitle'] : '';
        $layout = isset( $data['multi_image_text_layout'] ) ? $data['multi_image_text_layout'] : 'left';
        $bg_color = isset( $data['multi_image_text_bg_color'] ) && ! empty( $data['multi_image_text_bg_color'] ) 
            ? $data['multi_image_text_bg_color'] : '';
        $title_color = isset( $data['multi_image_text_title_color'] ) && ! empty( $data['multi_image_text_title_color'] ) 
            ? $data['multi_image_text_title_color'] : '';
        $subtitle_color = isset( $data['multi_image_text_subtitle_color'] ) && ! empty( $data['multi_image_text_subtitle_color'] ) 
            ? $data['multi_image_text_subtitle_color'] : '';
        $item_title_size = isset( $data['multi_image_text_item_title_size'] ) && ! empty( $data['multi_image_text_item_title_size'] ) 
            ? $data['multi_image_text_item_title_size'] : '1.25rem';
        $items = isset( $data['multi_image_text_items'] ) ? $data['multi_image_text_items'] : array();
        
        // 默认数据
        if ( empty( $items ) ) {
            $items = array(
                array(
                    'icon'  => '🚀',
                    'title' => '快速部署',
                    'desc'  => '采用自动化部署流程，5分钟即可完成系统上线，大幅降低运维成本和时间投入。',
                    'image' => '',
                    'link'  => '',
                ),
                array(
                    'icon'  => '🛡️',
                    'title' => '安全可靠',
                    'desc'  => '企业级安全架构，多层防护机制，数据加密存储，确保您的业务数据安全无虞。',
                    'image' => '',
                    'link'  => '',
                ),
                array(
                    'icon'  => '📊',
                    'title' => '数据分析',
                    'desc'  => '强大的数据分析引擎，实时监控业务指标，智能报表助力精准决策。',
                    'image' => '',
                    'link'  => '',
                ),
            );
        }
        
        // 生成唯一ID
        $module_id = 'mit-' . uniqid();
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false 
                ? "background: {$bg_color};" 
                : "background-color: {$bg_color};";
        }
        
        // 标题颜色样式
        $title_style = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $subtitle_style = ! empty( $subtitle_color ) ? "color: {$subtitle_color};" : '';
        ?>
        <section class="module module-multi-image-text section-padding" id="<?php echo esc_attr( $module_id ); ?>" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <?php if ( $title || $subtitle ) : ?>
                    <div class="section-header text-center" style="margin-bottom: 50px;">
                        <?php if ( $title ) : ?>
                            <h2 class="section-title" style="<?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( $title ); ?></h2>
                        <?php endif; ?>
                        <?php if ( $subtitle ) : ?>
                            <p class="section-subtitle" style="<?php echo esc_attr( $subtitle_style ); ?>"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="mit-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; <?php echo $layout === 'right' ? 'direction: rtl;' : ''; ?>">
                    <!-- 图片区域 -->
                    <div class="mit-image-area" style="<?php echo $layout === 'right' ? 'direction: ltr;' : ''; ?>">
                        <div class="mit-image-wrapper" style="position: relative; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);">
                            <?php foreach ( $items as $index => $item ) : 
                                $item_image = isset( $item['image'] ) && ! empty( $item['image'] ) ? $item['image'] : '';
                            ?>
                                <div class="mit-image <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" style="
                                    <?php echo $index !== 0 ? 'position: absolute; top: 0; left: 0; width: 100%;' : ''; ?>
                                    opacity: <?php echo $index === 0 ? '1' : '0'; ?>;
                                    transition: opacity 0.4s ease;
                                ">
                                    <?php if ( $item_image ) : ?>
                                        <img src="<?php echo esc_url( $item_image ); ?>" alt="" style="width: 100%; height: auto; display: block; aspect-ratio: 4/3; object-fit: cover;" />
                                    <?php else : ?>
                                        <div style="aspect-ratio: 4/3; background: linear-gradient(135deg, <?php echo $this->get_gradient_color( $index ); ?>); display: flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 4rem;"><?php echo isset( $item['icon'] ) ? esc_html( $item['icon'] ) : '📷'; ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- 文字列表区域 -->
                    <div class="mit-text-area" style="<?php echo $layout === 'right' ? 'direction: ltr;' : ''; ?>">
                        <?php foreach ( $items as $index => $item ) : 
                            $icon_raw = isset( $item['icon'] ) ? trim( $item['icon'] ) : '';
                            $item_title = isset( $item['title'] ) ? $item['title'] : '';
                            $item_desc = isset( $item['desc'] ) ? $item['desc'] : '';
                            $item_link = isset( $item['link'] ) ? $item['link'] : '';
                            $item_title_color = isset( $item['title_color'] ) && ! empty( $item['title_color'] ) ? $item['title_color'] : '';
                            $item_desc_color = isset( $item['desc_color'] ) && ! empty( $item['desc_color'] ) ? $item['desc_color'] : '';
                            
                            // 解码HTML实体
                            $icon = html_entity_decode( $icon_raw, ENT_QUOTES, 'UTF-8' );
                            
                            // 判断图标格式
                            $is_html_tag = ( strpos( $icon, '<' ) !== false && strpos( $icon, '>' ) !== false );
                            $is_iconfont_class = ! $is_html_tag && ( strpos( $icon, 'iconfont' ) !== false || strpos( $icon, 'icon-' ) !== false || strpos( $icon, 'fa-' ) !== false || strpos( $icon, 'fa ' ) !== false );
                            
                            // 标题颜色
                            $item_title_style = ! empty( $item_title_color ) ? "color: {$item_title_color};" : '';
                            $item_desc_style = ! empty( $item_desc_color ) ? "color: {$item_desc_color};" : 'color: var(--color-gray-600);';
                        ?>
                            <div class="mit-item <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" style="
                                padding: 24px 28px;
                                margin-bottom: 16px;
                                border-radius: 12px;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                background: <?php echo $index === 0 ? 'rgba(var(--color-primary-rgb, 37, 99, 235), 0.08)' : 'transparent'; ?>;
                                border-left: 4px solid <?php echo $index === 0 ? 'var(--color-primary)' : 'transparent'; ?>;
                            ">
                                <div style="display: flex; align-items: flex-start; gap: 16px;">
                                    <?php if ( $icon ) : ?>
                                        <div class="mit-icon" style="
                                            width: 48px;
                                            height: 48px;
                                            flex-shrink: 0;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-size: 1.5rem;
                                            background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%);
                                            color: #fff;
                                            border-radius: 12px;
                                        ">
                                            <?php if ( $is_html_tag ) : ?>
                                                <?php echo wp_kses_post( $icon ); ?>
                                            <?php elseif ( $is_iconfont_class ) : ?>
                                                <i class="<?php echo esc_attr( $icon ); ?>"></i>
                                            <?php else : ?>
                                                <?php echo esc_html( $icon ); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mit-content" style="flex: 1;">
                                        <?php if ( $item_title ) : ?>
                                            <h3 class="mit-title" style="
                                                font-size: <?php echo esc_attr( $item_title_size ); ?>;
                                                font-weight: 600;
                                                margin: 0 0 8px 0;
                                                <?php echo esc_attr( $item_title_style ); ?>
                                            ">
                                                <?php if ( $item_link && $item_link !== '#' ) : ?>
                                                    <a href="<?php echo esc_url( $item_link ); ?>" style="color: inherit; text-decoration: none;">
                                                        <?php echo esc_html( $item_title ); ?>
                                                    </a>
                                                <?php else : ?>
                                                    <?php echo esc_html( $item_title ); ?>
                                                <?php endif; ?>
                                            </h3>
                                        <?php endif; ?>
                                        
                                        <?php if ( $item_desc ) : ?>
                                            <p class="mit-desc" style="
                                                margin: 0;
                                                line-height: 1.7;
                                                font-size: 0.95rem;
                                                <?php echo esc_attr( $item_desc_style ); ?>
                                            "><?php echo esc_html( $item_desc ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        
        <style>
        #<?php echo esc_attr( $module_id ); ?> .mit-item:hover,
        #<?php echo esc_attr( $module_id ); ?> .mit-item.active {
            background: rgba(var(--color-primary-rgb, 37, 99, 235), 0.08) !important;
            border-left-color: var(--color-primary) !important;
        }
        
        #<?php echo esc_attr( $module_id ); ?> .mit-item:hover .mit-icon,
        #<?php echo esc_attr( $module_id ); ?> .mit-item.active .mit-icon {
            transform: scale(1.1);
        }
        
        #<?php echo esc_attr( $module_id ); ?> .mit-icon {
            transition: transform 0.3s ease;
        }
        
        @media (max-width: 991px) {
            #<?php echo esc_attr( $module_id ); ?> .mit-container {
                grid-template-columns: 1fr !important;
                direction: ltr !important;
                gap: 40px !important;
            }
            
            #<?php echo esc_attr( $module_id ); ?> .mit-image-area,
            #<?php echo esc_attr( $module_id ); ?> .mit-text-area {
                direction: ltr !important;
            }
        }
        </style>
        
        <script>
        (function() {
            var container = document.getElementById('<?php echo esc_js( $module_id ); ?>');
            if (!container) return;
            
            var items = container.querySelectorAll('.mit-item');
            var images = container.querySelectorAll('.mit-image');
            
            items.forEach(function(item) {
                item.addEventListener('mouseenter', function() {
                    var index = this.getAttribute('data-index');
                    
                    // 更新文字项状态
                    items.forEach(function(i) {
                        i.classList.remove('active');
                        i.style.background = 'transparent';
                        i.style.borderLeftColor = 'transparent';
                    });
                    this.classList.add('active');
                    
                    // 更新图片显示
                    images.forEach(function(img) {
                        if (img.getAttribute('data-index') === index) {
                            img.style.opacity = '1';
                            img.style.zIndex = '2';
                        } else {
                            img.style.opacity = '0';
                            img.style.zIndex = '1';
                        }
                    });
                });
            });
        })();
        </script>
        <?php
    }
    
    /**
     * 获取渐变色
     */
    private function get_gradient_color( $index ) {
        $gradients = array(
            '#667eea 0%, #764ba2 100%',
            '#f093fb 0%, #f5576c 100%',
            '#4facfe 0%, #00f2fe 100%',
            '#43e97b 0%, #38f9d7 100%',
            '#fa709a 0%, #fee140 100%',
            '#a8edea 0%, #fed6e3 100%',
        );
        return $gradients[ $index % count( $gradients ) ];
    }
}
