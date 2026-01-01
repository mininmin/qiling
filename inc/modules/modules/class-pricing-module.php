<?php
/**
 * Pricing Module - 价格方案
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Pricing_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-money-alt';
        $this->description = '展示价格方案套餐';
    }

    public function get_id() {
        return 'pricing';
    }

    public function get_name() {
        return '价格方案';
    }

    public function render( $data = array() ) {
        $title = isset( $data['pricing_title'] ) && $data['pricing_title'] !== '' ? $data['pricing_title'] : '价格方案';
        $subtitle = isset( $data['pricing_subtitle'] ) ? $data['pricing_subtitle'] : '选择适合您的方案，开启高效之旅';
        $bg_color = isset( $data['pricing_bg_color'] ) && ! empty( $data['pricing_bg_color'] ) ? $data['pricing_bg_color'] : '';
        $title_color = isset( $data['pricing_title_color'] ) && ! empty( $data['pricing_title_color'] ) ? $data['pricing_title_color'] : '';
        $subtitle_color = isset( $data['pricing_subtitle_color'] ) && ! empty( $data['pricing_subtitle_color'] ) ? $data['pricing_subtitle_color'] : '';
        $columns = isset( $data['pricing_columns'] ) && ! empty( $data['pricing_columns'] ) ? intval( $data['pricing_columns'] ) : 3;
        $items = isset( $data['pricing_items'] ) ? $data['pricing_items'] : array();
        
        // 默认示例数据
        if ( empty( $items ) ) {
            $items = array(
                array( 
                    'name' => '基础版',
                    'price' => '¥99',
                    'period' => '/月',
                    'desc' => '适合个人用户和小型项目',
                    'features' => "✓ 基础功能支持\n✓ 5GB 存储空间\n✓ 邮件支持\n✗ 高级分析\n✗ API 接口",
                    'btn_text' => '立即购买',
                    'btn_link' => '#',
                    'card_bg' => '#ffffff',
                    'featured' => '',
                    'featured_text' => '',
                    'featured_bg' => ''
                ),
                array( 
                    'name' => '专业版',
                    'price' => '¥299',
                    'period' => '/月',
                    'desc' => '适合成长型企业',
                    'features' => "✓ 全部基础功能\n✓ 50GB 存储空间\n✓ 优先技术支持\n✓ 高级数据分析\n✓ API 接口",
                    'btn_text' => '立即购买',
                    'btn_link' => '#',
                    'card_bg' => '#ffffff',
                    'featured' => '1',
                    'featured_text' => '推荐',
                    'featured_bg' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
                ),
                array( 
                    'name' => '企业版',
                    'price' => '¥999',
                    'period' => '/月',
                    'desc' => '适合大型企业定制需求',
                    'features' => "✓ 全部专业功能\n✓ 无限存储空间\n✓ 7×24专属客服\n✓ 定制化开发\n✓ 专属客户经理",
                    'btn_text' => '联系我们',
                    'btn_link' => '#',
                    'card_bg' => '#ffffff',
                    'featured' => '',
                    'featured_text' => '',
                    'featured_bg' => ''
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
        
        // 网格类
        $grid_class = 'grid-cols-' . $columns;
        ?>
        <section class="module module-pricing section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title" style="<?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle" style="<?php echo esc_attr( $subtitle_style ); ?>"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if ( ! empty( $items ) ) : ?>
                    <div class="pricing-grid <?php echo esc_attr( $grid_class ); ?>" style="align-items: stretch;">
                        <?php foreach ( $items as $item ) : 
                            $name = isset( $item['name'] ) ? $item['name'] : '';
                            $name_color = isset( $item['name_color'] ) && ! empty( $item['name_color'] ) ? $item['name_color'] : '';
                            $price = isset( $item['price'] ) ? $item['price'] : '';
                            $price_color = isset( $item['price_color'] ) && ! empty( $item['price_color'] ) ? $item['price_color'] : '';
                            $period = isset( $item['period'] ) ? $item['period'] : '';
                            $desc = isset( $item['desc'] ) ? $item['desc'] : '';
                            $desc_color = isset( $item['desc_color'] ) && ! empty( $item['desc_color'] ) ? $item['desc_color'] : '';
                            $features = isset( $item['features'] ) ? $item['features'] : '';
                            $btn_text = isset( $item['btn_text'] ) ? $item['btn_text'] : '立即购买';
                            $btn_link = isset( $item['btn_link'] ) ? $item['btn_link'] : '#';
                            $btn_bg = isset( $item['btn_bg'] ) && ! empty( $item['btn_bg'] ) ? $item['btn_bg'] : '';
                            $btn_text_color = isset( $item['btn_text_color'] ) && ! empty( $item['btn_text_color'] ) ? $item['btn_text_color'] : '';
                            $card_bg = isset( $item['card_bg'] ) && ! empty( $item['card_bg'] ) ? $item['card_bg'] : '#ffffff';
                            $is_featured = isset( $item['featured'] ) && $item['featured'];
                            $featured_text = isset( $item['featured_text'] ) && ! empty( $item['featured_text'] ) ? $item['featured_text'] : '推荐';
                            $featured_bg = isset( $item['featured_bg'] ) && ! empty( $item['featured_bg'] ) ? $item['featured_bg'] : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                            
                            // 卡片背景样式
                            $card_bg_style = strpos( $card_bg, 'gradient' ) !== false ? "background: {$card_bg};" : "background-color: {$card_bg};";
                            
                            // 特性列表转数组 - 支持多种换行符
                            $features = str_replace( array( "\r\n", "\r" ), "\n", $features );
                            $feature_list = array_filter( array_map( 'trim', explode( "\n", $features ) ) );
                        ?>
                            <div class="pricing-card <?php echo $is_featured ? 'pricing-featured' : ''; ?>" style="
                                <?php echo esc_attr( $card_bg_style ); ?>
                                border-radius: 16px;
                                padding: 40px 30px;
                                text-align: center;
                                box-shadow: 0 10px 40px rgba(0,0,0,0.08);
                                transition: transform 0.3s, box-shadow 0.3s;
                                position: relative;
                                display: flex;
                                flex-direction: column;
                                <?php echo $is_featured ? 'transform: scale(1.05); z-index: 2; box-shadow: 0 20px 60px rgba(0,0,0,0.15);' : ''; ?>
                            ">
                                <!-- 推荐标注 -->
                                <?php if ( $is_featured ) : ?>
                                    <div class="pricing-badge" style="
                                        position: absolute;
                                        top: -12px;
                                        left: 50%;
                                        transform: translateX(-50%);
                                        background: <?php echo esc_attr( $featured_bg ); ?>;
                                        color: #fff;
                                        padding: 6px 20px;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                        font-weight: 600;
                                        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                                    ">
                                        <?php echo esc_html( $featured_text ); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- 方案名称 -->
                                <?php 
                                $name_style = ! empty( $name_color ) ? "color: {$name_color};" : 'color: var(--color-dark);';
                                ?>
                                <h3 class="pricing-name" style="font-size: 1.5rem; font-weight: 600; margin-bottom: 10px; <?php echo esc_attr( $name_style ); ?>">
                                    <?php echo esc_html( $name ); ?>
                                </h3>
                                
                                <!-- 方案描述 -->
                                <?php if ( $desc ) : 
                                    $desc_style = ! empty( $desc_color ) ? "color: {$desc_color};" : 'color: var(--color-gray-600);';
                                ?>
                                    <p class="pricing-desc" style="<?php echo esc_attr( $desc_style ); ?> font-size: 0.9rem; margin-bottom: 20px;">
                                        <?php echo esc_html( $desc ); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <!-- 价格 -->
                                <?php 
                                // 价格颜色：支持渐变色或纯色
                                if ( ! empty( $price_color ) ) {
                                    if ( strpos( $price_color, 'gradient' ) !== false ) {
                                        $price_style = "background: {$price_color}; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;";
                                    } else {
                                        $price_style = "color: {$price_color};";
                                    }
                                } else {
                                    $price_style = "background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;";
                                }
                                ?>
                                <div class="pricing-price" style="margin-bottom: 25px;">
                                    <span style="font-size: 3rem; font-weight: 700; <?php echo esc_attr( $price_style ); ?>">
                                        <?php echo esc_html( $price ); ?>
                                    </span>
                                    <?php if ( $period ) : ?>
                                        <span style="color: var(--color-gray-500); font-size: 1rem;"><?php echo esc_html( $period ); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- 特性列表 -->
                                <?php if ( ! empty( $feature_list ) ) : ?>
                                    <ul class="pricing-features" style="list-style: none; padding: 0; margin: 0 0 30px; text-align: left; flex-grow: 1;">
                                        <?php foreach ( $feature_list as $feature ) : 
                                            $is_included = strpos( $feature, '✓' ) === 0 || strpos( $feature, '√' ) === 0;
                                            $is_excluded = strpos( $feature, '✗' ) === 0 || strpos( $feature, '×' ) === 0;
                                            $feature_text = ltrim( $feature, '✓✗√×  ' );
                                            $feature_color = $is_excluded ? 'var(--color-gray-400)' : 'var(--color-dark)';
                                            $icon_color = $is_included ? '#22c55e' : ( $is_excluded ? '#ef4444' : 'var(--color-primary)' );
                                        ?>
                                            <li style="padding: 10px 0; border-bottom: 1px solid var(--color-gray-100); display: flex; align-items: center; gap: 10px; color: <?php echo esc_attr( $feature_color ); ?>;">
                                                <?php if ( $is_included ) : ?>
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $icon_color ); ?>" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                <?php elseif ( $is_excluded ) : ?>
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $icon_color ); ?>" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                <?php else : ?>
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $icon_color ); ?>" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                <?php endif; ?>
                                                <span><?php echo esc_html( $feature_text ); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                
                                <!-- 按钮 -->
                                <?php if ( $btn_text && $btn_link ) : 
                                    // 按钮背景色：优先用自定义，否则推荐使用渐变色，普通使用灰色
                                    if ( ! empty( $btn_bg ) ) {
                                        $button_bg = strpos( $btn_bg, 'gradient' ) !== false ? $btn_bg : $btn_bg;
                                    } else {
                                        $button_bg = $is_featured ? 'linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%)' : 'var(--color-gray-100)';
                                    }
                                    // 按钮文字颜色
                                    if ( ! empty( $btn_text_color ) ) {
                                        $button_color = $btn_text_color;
                                    } else {
                                        $button_color = $is_featured ? '#fff' : 'var(--color-dark)';
                                    }
                                ?>
                                    <a href="<?php echo esc_url( $btn_link ); ?>" class="pricing-btn" style="
                                        display: inline-block;
                                        width: 100%;
                                        padding: 14px 30px;
                                        background: <?php echo esc_attr( $button_bg ); ?>;
                                        color: <?php echo esc_attr( $button_color ); ?>;
                                        border-radius: 10px;
                                        font-weight: 600;
                                        text-decoration: none;
                                        transition: all 0.3s;
                                    ">
                                        <?php echo esc_html( $btn_text ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <style>
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }
        .pricing-card.pricing-featured:hover {
            transform: scale(1.05) translateY(-10px);
        }
        .pricing-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        @media (max-width: 992px) {
            .pricing-card.pricing-featured {
                transform: none !important;
            }
            .pricing-grid {
                gap: 30px !important;
            }
        }
        </style>
        <?php
    }
}
