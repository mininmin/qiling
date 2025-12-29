<?php
/**
 * Services Module - 服务展示
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Services_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-grid-view';
        $this->description = '服务项目展示';
    }

    public function get_id() {
        return 'services';
    }

    public function get_name() {
        return '服务展示';
    }

    public function render( $data = array() ) {
        $title = isset( $data['services_title'] ) && $data['services_title'] !== '' ? $data['services_title'] : '我们的服务';
        $subtitle = isset( $data['services_subtitle'] ) ? $data['services_subtitle'] : '为企业提供全方位的专业服务';
        $items = isset( $data['services_items'] ) ? $data['services_items'] : array();
        
        if ( empty( $items ) ) {
            $items = array(
                array( 'icon' => '01', 'title' => '产品研发', 'desc' => '提供专业的产品研发服务，从需求分析到产品上线全流程支持', 'link' => '#' ),
                array( 'icon' => '02', 'title' => '解决方案', 'desc' => '针对不同行业提供定制化解决方案，满足企业个性化需求', 'link' => '#' ),
                array( 'icon' => '03', 'title' => '技术支持', 'desc' => '7x24小时技术支持服务，快速响应解决技术问题', 'link' => '#' ),
                array( 'icon' => '04', 'title' => '数据分析', 'desc' => '专业数据分析团队，助力企业数据驱动决策', 'link' => '#' ),
            );
        }
        ?>
        <section class="module module-services section-padding">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if ( ! empty( $items ) ) : ?>
                    <div class="services-grid grid-cols-4">
                        <?php foreach ( $items as $item ) : 
                            $icon_raw = isset( $item['icon'] ) ? trim( $item['icon'] ) : '01';
                            $item_title = isset( $item['title'] ) ? $item['title'] : '';
                            $desc = isset( $item['desc'] ) ? $item['desc'] : '';
                            $link = isset( $item['link'] ) ? $item['link'] : '';
                            
                            // 解码HTML实体以便正确检测HTML标签
                            $icon = html_entity_decode( $icon_raw, ENT_QUOTES, 'UTF-8' );
                            
                            // 判断图标格式 - 检测是否包含HTML标签（如 <i>, <span>, <svg>）
                            $is_html_tag = ( strpos( $icon, '<' ) !== false && strpos( $icon, '>' ) !== false );
                            // 检测是否是纯class名称（如 iconfont icon-weibo）
                            $is_iconfont_class = ! $is_html_tag && ( strpos( $icon, 'iconfont' ) !== false || strpos( $icon, 'icon-' ) !== false || strpos( $icon, 'fa-' ) !== false || strpos( $icon, 'fa ' ) !== false );
                        ?>
                            <div class="service-card" style="text-align: center; padding: 30px 20px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s, box-shadow 0.3s;">
                                <div class="service-icon" style="font-size: 2.5rem; font-weight: 700; color: var(--color-primary); margin-bottom: 15px;">
                                    <?php if ( $is_html_tag ) : ?>
                                        <?php echo wp_kses_post( $icon ); ?>
                                    <?php elseif ( $is_iconfont_class ) : ?>
                                        <i class="<?php echo esc_attr( $icon ); ?>"></i>
                                    <?php else : ?>
                                        <?php echo esc_html( $icon ); ?>
                                    <?php endif; ?>
                                </div>
                                <h3 class="service-title" style="font-size: 1.125rem; margin-bottom: 10px;">
                                    <?php if ( $link ) : ?>
                                        <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $item_title ); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html( $item_title ); ?>
                                    <?php endif; ?>
                                </h3>
                                <p class="service-desc" style="color: var(--color-gray-600); font-size: 0.9rem; line-height: 1.6;"><?php echo esc_html( $desc ); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
