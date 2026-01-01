<?php
/**
 * Accordion Module - 手风琴折叠
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Accordion_Module extends Module_Base {

    public function __construct() {
        $this->category = 'general';
        $this->icon = 'dashicons-list-view';
        $this->description = '可折叠的手风琴内容';
    }

    public function get_id() {
        return 'accordion';
    }

    public function get_name() {
        return '手风琴';
    }

    public function render( $data = array() ) {
        $title = isset( $data['accordion_title'] ) ? $data['accordion_title'] : '';
        $subtitle = isset( $data['accordion_subtitle'] ) ? $data['accordion_subtitle'] : '';
        $bg_color = isset( $data['accordion_bg_color'] ) && ! empty( $data['accordion_bg_color'] ) ? $data['accordion_bg_color'] : '';
        $title_color = isset( $data['accordion_title_color'] ) && ! empty( $data['accordion_title_color'] ) ? $data['accordion_title_color'] : '';
        $style = isset( $data['accordion_style'] ) ? $data['accordion_style'] : 'default';
        $allow_multiple = isset( $data['accordion_multiple'] ) ? $data['accordion_multiple'] : '';
        $first_open = isset( $data['accordion_first_open'] ) ? $data['accordion_first_open'] : '1';
        $items = isset( $data['accordion_items'] ) ? $data['accordion_items'] : array();
        
        // 默认示例数据
        if ( empty( $items ) ) {
            $items = array(
                array( 'title' => '产品质量如何保证？', 'content' => '我们拥有完善的质量管理体系，通过ISO9001认证。每件产品都经过严格的质检流程，确保出厂产品100%合格。如有任何质量问题，我们提供无条件退换货服务。', 'icon' => '🛡️' ),
                array( 'title' => '配送范围和时效？', 'content' => '我们支持全国配送，一二线城市1-3天送达，其他地区3-7天送达。部分地区支持当日达服务，下单时可查看具体配送时效。', 'icon' => '🚚' ),
                array( 'title' => '售后服务政策？', 'content' => '我们提供7x24小时在线客服支持，产品享有1年质保期。质保期内非人为损坏可免费维修或更换。质保期外提供有偿维修服务。', 'icon' => '💬' ),
            );
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        $title_style_attr = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $accordion_id = 'accordion-' . uniqid();
        ?>
        <section class="module module-accordion section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <?php if ( $title ) : ?>
                    <div class="section-header text-center">
                        <h2 class="section-title" style="<?php echo esc_attr( $title_style_attr ); ?>"><?php echo esc_html( $title ); ?></h2>
                        <?php if ( $subtitle ) : ?>
                            <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( ! empty( $items ) ) : ?>
                    <div id="<?php echo esc_attr( $accordion_id ); ?>" class="accordion-wrapper accordion-style-<?php echo esc_attr( $style ); ?>" data-multiple="<?php echo esc_attr( $allow_multiple ); ?>" style="max-width: 900px; margin: 0 auto;">
                        <?php foreach ( $items as $index => $item ) : 
                            $item_title = isset( $item['title'] ) ? $item['title'] : '';
                            $content = isset( $item['content'] ) ? $item['content'] : '';
                            $icon = isset( $item['icon'] ) ? $item['icon'] : '';
                            $is_open = ( $first_open === '1' && $index === 0 );
                        ?>
                            <div class="accordion-item <?php echo $is_open ? 'active' : ''; ?>" style="
                                margin-bottom: 12px;
                                border-radius: <?php echo $style === 'minimal' ? '0' : '12px'; ?>;
                                overflow: hidden;
                                <?php if ( $style === 'default' ) : ?>
                                    background: #fff;
                                    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
                                <?php elseif ( $style === 'bordered' ) : ?>
                                    background: #fff;
                                    border: 2px solid #e2e8f0;
                                <?php elseif ( $style === 'minimal' ) : ?>
                                    background: transparent;
                                    border-bottom: 1px solid #e2e8f0;
                                <?php endif; ?>
                            ">
                                <div class="accordion-header" style="
                                    display: flex;
                                    align-items: center;
                                    padding: <?php echo $style === 'minimal' ? '20px 0' : '20px 25px'; ?>;
                                    cursor: pointer;
                                    user-select: none;
                                    transition: all 0.3s;
                                ">
                                    <?php if ( $icon ) : ?>
                                        <span style="font-size: 1.3rem; margin-right: 15px;"><?php echo esc_html( $icon ); ?></span>
                                    <?php endif; ?>
                                    <span style="flex: 1; font-weight: 600; font-size: 1.05rem; color: #1e293b;"><?php echo esc_html( $item_title ); ?></span>
                                    <span class="accordion-icon" style="
                                        width: 28px;
                                        height: 28px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        border-radius: 50%;
                                        background: <?php echo $is_open ? 'var(--color-primary)' : '#f1f5f9'; ?>;
                                        color: <?php echo $is_open ? '#fff' : '#64748b'; ?>;
                                        transition: all 0.3s;
                                        font-size: 1.2rem;
                                    "><?php echo $is_open ? '−' : '+'; ?></span>
                                </div>
                                <div class="accordion-content" style="
                                    padding: 0 <?php echo $style === 'minimal' ? '0' : '25px'; ?>;
                                    max-height: <?php echo $is_open ? '500px' : '0'; ?>;
                                    overflow: hidden;
                                    transition: max-height 0.3s ease, padding 0.3s ease;
                                ">
                                    <div style="padding-bottom: 20px; color: #64748b; line-height: 1.8;">
                                        <?php echo wp_kses_post( $content ); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <style>
        #<?php echo esc_attr( $accordion_id ); ?> .accordion-header:hover {
            background: rgba(0,0,0,0.02);
        }
        #<?php echo esc_attr( $accordion_id ); ?> .accordion-item.active .accordion-content {
            max-height: 500px !important;
            padding-bottom: 5px;
        }
        #<?php echo esc_attr( $accordion_id ); ?> .accordion-item.active .accordion-icon {
            background: var(--color-primary) !important;
            color: #fff !important;
        }
        </style>
        
        <script>
        (function() {
            var id = '<?php echo esc_js( $accordion_id ); ?>';
            var wrapper = document.getElementById(id);
            if (!wrapper) return;
            
            var allowMultiple = wrapper.getAttribute('data-multiple') === '1';
            var headers = wrapper.querySelectorAll('.accordion-header');
            
            headers.forEach(function(header) {
                header.addEventListener('click', function() {
                    var item = this.closest('.accordion-item');
                    var isActive = item.classList.contains('active');
                    var icon = this.querySelector('.accordion-icon');
                    var content = item.querySelector('.accordion-content');
                    
                    if (!allowMultiple) {
                        // 关闭其他
                        wrapper.querySelectorAll('.accordion-item.active').forEach(function(activeItem) {
                            if (activeItem !== item) {
                                activeItem.classList.remove('active');
                                activeItem.querySelector('.accordion-icon').textContent = '+';
                                activeItem.querySelector('.accordion-content').style.maxHeight = '0';
                            }
                        });
                    }
                    
                    if (isActive) {
                        item.classList.remove('active');
                        icon.textContent = '+';
                        content.style.maxHeight = '0';
                    } else {
                        item.classList.add('active');
                        icon.textContent = '−';
                        content.style.maxHeight = content.scrollHeight + 'px';
                    }
                });
            });
        })();
        </script>
        <?php
    }
}
