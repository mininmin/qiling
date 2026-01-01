<?php
/**
 * Process Module - 合作流程
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Process_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-randomize';
        $this->description = '展示合作流程步骤';
    }

    public function get_id() {
        return 'process';
    }

    public function get_name() {
        return '合作流程';
    }

    public function render( $data = array() ) {
        $title = isset( $data['process_title'] ) && $data['process_title'] !== '' ? $data['process_title'] : '合作流程';
        $subtitle = isset( $data['process_subtitle'] ) ? $data['process_subtitle'] : '简单四步，开启合作之旅';
        $bg_color = isset( $data['process_bg_color'] ) && ! empty( $data['process_bg_color'] ) ? $data['process_bg_color'] : '';
        $title_color = isset( $data['process_title_color'] ) && ! empty( $data['process_title_color'] ) ? $data['process_title_color'] : '';
        $subtitle_color = isset( $data['process_subtitle_color'] ) && ! empty( $data['process_subtitle_color'] ) ? $data['process_subtitle_color'] : '';
        $items = isset( $data['process_items'] ) ? $data['process_items'] : array();
        
        // 默认示例数据
        if ( empty( $items ) ) {
            $items = array(
                array( 
                    'icon' => '01', 
                    'title' => '需求沟通', 
                    'desc' => '深入了解您的业务需求和目标',
                    'icon_bg' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
                ),
                array( 
                    'icon' => '02', 
                    'title' => '方案设计', 
                    'desc' => '根据需求制定专属解决方案',
                    'icon_bg' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
                ),
                array( 
                    'icon' => '03', 
                    'title' => '开发实施', 
                    'desc' => '专业团队高效执行项目开发',
                    'icon_bg' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
                ),
                array( 
                    'icon' => '04', 
                    'title' => '交付上线', 
                    'desc' => '严格测试后交付，持续技术支持',
                    'icon_bg' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'
                ),
            );
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        // 标题颜色样式
        $title_style = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $subtitle_style = ! empty( $subtitle_color ) ? "color: {$subtitle_color};" : '';
        ?>
        <section class="module module-process section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title" style="<?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle" style="<?php echo esc_attr( $subtitle_style ); ?>"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if ( ! empty( $items ) ) : ?>
                    <div class="process-grid" style="display: flex; justify-content: center; align-items: flex-start; flex-wrap: wrap; gap: 20px; position: relative;">
                        <?php 
                        $total = count( $items );
                        foreach ( $items as $index => $item ) : 
                            $icon_raw = isset( $item['icon'] ) ? trim( $item['icon'] ) : sprintf( '%02d', $index + 1 );
                            $item_title = isset( $item['title'] ) ? $item['title'] : '';
                            $item_title_color = isset( $item['title_color'] ) && ! empty( $item['title_color'] ) ? $item['title_color'] : '';
                            $desc = isset( $item['desc'] ) ? $item['desc'] : '';
                            $icon_bg = isset( $item['icon_bg'] ) && ! empty( $item['icon_bg'] ) ? $item['icon_bg'] : 'linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%)';
                            
                            // 解码HTML实体
                            $icon = html_entity_decode( $icon_raw, ENT_QUOTES, 'UTF-8' );
                            
                            // 判断图标格式
                            $is_html_tag = ( strpos( $icon, '<' ) !== false && strpos( $icon, '>' ) !== false );
                            $is_iconfont_class = ! $is_html_tag && ( strpos( $icon, 'iconfont' ) !== false || strpos( $icon, 'icon-' ) !== false || strpos( $icon, 'fa-' ) !== false );
                            
                            $is_last = ( $index === $total - 1 );
                        ?>
                            <div class="process-item" style="flex: 1; min-width: 200px; max-width: 280px; text-align: center; position: relative;">
                                <!-- 图标 -->
                                <div class="process-icon" style="
                                    width: 90px; 
                                    height: 90px; 
                                    margin: 0 auto 20px; 
                                    background: <?php echo esc_attr( $icon_bg ); ?>; 
                                    border-radius: 50%; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center; 
                                    font-size: 2rem; 
                                    font-weight: 700; 
                                    color: #fff;
                                    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                                    transition: transform 0.3s, box-shadow 0.3s;
                                    position: relative;
                                    z-index: 2;
                                ">
                                    <?php if ( $is_html_tag ) : ?>
                                        <?php echo wp_kses_post( $icon ); ?>
                                    <?php elseif ( $is_iconfont_class ) : ?>
                                        <i class="<?php echo esc_attr( $icon ); ?>"></i>
                                    <?php else : ?>
                                        <?php echo esc_html( $icon ); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- 箭头连接线（非最后一个） -->
                                <?php if ( ! $is_last ) : ?>
                                    <div class="process-arrow" style="
                                        position: absolute;
                                        top: 45px;
                                        right: -30px;
                                        width: 40px;
                                        height: 2px;
                                        background: linear-gradient(90deg, var(--color-primary), transparent);
                                        z-index: 1;
                                    ">
                                        <div style="
                                            position: absolute;
                                            right: 0;
                                            top: -4px;
                                            border: solid var(--color-primary);
                                            border-width: 0 2px 2px 0;
                                            padding: 4px;
                                            transform: rotate(-45deg);
                                        "></div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- 标题 -->
                                <?php 
                                $item_title_style = ! empty( $item_title_color ) ? "color: {$item_title_color};" : 'color: var(--color-dark);';
                                ?>
                                <h3 class="process-title" style="font-size: 1.25rem; font-weight: 600; margin-bottom: 10px; <?php echo esc_attr( $item_title_style ); ?>">
                                    <?php echo esc_html( $item_title ); ?>
                                </h3>
                                
                                <!-- 描述 -->
                                <p class="process-desc" style="color: var(--color-gray-600); font-size: 0.9rem; line-height: 1.6; margin: 0;">
                                    <?php echo esc_html( $desc ); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <style>
        .process-item:hover .process-icon {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        @media (max-width: 768px) {
            .process-grid {
                flex-direction: column !important;
                align-items: center !important;
            }
            .process-item {
                max-width: 100% !important;
            }
            .process-arrow {
                display: none !important;
            }
        }
        </style>
        <?php
    }
}
