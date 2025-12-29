<?php
/**
 * Features Module - 企业优势
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Features_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-awards';
        $this->description = '展示企业核心优势';
    }

    public function get_id() {
        return 'features';
    }

    public function get_name() {
        return '企业优势';
    }

    public function render( $data = array() ) {
        $title = isset( $data['features_title'] ) && $data['features_title'] !== '' ? $data['features_title'] : '为什么选择我们';
        $subtitle = isset( $data['features_subtitle'] ) ? $data['features_subtitle'] : '我们的核心竞争优势';
        $items = isset( $data['features_items'] ) ? $data['features_items'] : array();
        
        if ( empty( $items ) ) {
            $items = array(
                array( 'icon' => '+', 'title' => '专业团队', 'desc' => '拥有10年行业经验的专业团队' ),
                array( 'icon' => '+', 'title' => '优质服务', 'desc' => '7x24小时全天候服务支持' ),
                array( 'icon' => '+', 'title' => '价格透明', 'desc' => '无隐形消费，明码标价' ),
                array( 'icon' => '+', 'title' => '品质保障', 'desc' => 'ISO9001质量管理体系认证' ),
            );
        }
        ?>
        <section class="module module-features section-padding bg-light">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if ( ! empty( $items ) ) : ?>
                    <div class="features-grid grid-cols-4">
                <?php foreach ( $items as $item ) : 
                            $icon_raw = isset( $item['icon'] ) ? trim( $item['icon'] ) : '+';
                            $item_title = isset( $item['title'] ) ? $item['title'] : '';
                            $desc = isset( $item['desc'] ) ? $item['desc'] : '';
                            
                            // 解码HTML实体以便正确检测HTML标签
                            $icon = html_entity_decode( $icon_raw, ENT_QUOTES, 'UTF-8' );
                            
                            // 判断图标格式 - 检测是否包含HTML标签（如 <i>, <span>, <svg>）
                            $is_html_tag = ( strpos( $icon, '<' ) !== false && strpos( $icon, '>' ) !== false );
                            // 检测是否是纯class名称（如 iconfont icon-weibo）
                            $is_iconfont_class = ! $is_html_tag && ( strpos( $icon, 'iconfont' ) !== false || strpos( $icon, 'icon-' ) !== false || strpos( $icon, 'fa-' ) !== false || strpos( $icon, 'fa ' ) !== false );
                        ?>
                            <div class="feature-item" style="display: flex; align-items: flex-start; gap: 15px; padding: 20px; background: #fff; border-radius: 8px;">
                                <div class="feature-icon" style="width: 48px; height: 48px; background: var(--color-primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.25rem;">
                                    <?php if ( $is_html_tag ) : ?>
                                        <?php echo wp_kses_post( $icon ); ?>
                                    <?php elseif ( $is_iconfont_class ) : ?>
                                        <i class="<?php echo esc_attr( $icon ); ?>"></i>
                                    <?php else : ?>
                                        <?php echo esc_html( $icon ); ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 style="margin: 0 0 8px; font-size: 1rem; font-weight: 600;"><?php echo esc_html( $item_title ); ?></h3>
                                    <p style="margin: 0; color: var(--color-gray-600); font-size: 0.9rem;"><?php echo esc_html( $desc ); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
