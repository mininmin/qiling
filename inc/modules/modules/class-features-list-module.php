<?php
/**
 * 功能清单列表模块
 *
 * Tab标签切换的功能清单展示模块
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Features_List_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-list-view';
        $this->description = 'Tab标签切换的功能清单，展示产品功能特性';
    }

    public function get_id() {
        return 'features_list';
    }

    public function get_name() {
        return '功能清单列表';
    }

    public function render( $data = array() ) {
        $title = isset( $data['title'] ) ? $data['title'] : '产品功能';
        $subtitle = isset( $data['subtitle'] ) ? $data['subtitle'] : '';
        $bg_color = isset( $data['bg_color'] ) ? $data['bg_color'] : '';
        $text_color = isset( $data['text_color'] ) ? $data['text_color'] : '';
        $columns = isset( $data['columns'] ) ? $data['columns'] : '3';
        $tabs = isset( $data['tabs'] ) ? $data['tabs'] : array();
        
        if ( empty( $tabs ) ) {
            return;
        }
        
        // 解析功能清单数据
        $parsed_tabs = array();
        foreach ( $tabs as $tab ) {
            $parsed_tab = $tab;
            if ( isset( $tab['features'] ) && is_string( $tab['features'] ) ) {
                $features_text = $tab['features'];
                $lines = explode( "\n", $features_text );
                $parsed_tab['features'] = array();
                foreach ( $lines as $line ) {
                    $line = trim( $line );
                    if ( empty( $line ) ) continue;
                    $parts = explode( '|', $line );
                    if ( count( $parts ) >= 3 ) {
                        $parsed_tab['features'][] = array(
                            'icon' => trim( $parts[0] ),
                            'title' => trim( $parts[1] ),
                            'desc' => trim( $parts[2] ),
                        );
                    }
                }
            }
            $parsed_tabs[] = $parsed_tab;
        }
        $tabs = $parsed_tabs;
        
        $unique_id = 'features-list-' . uniqid();
        $style = '';
        if ( ! empty( $bg_color ) ) {
            $style .= "background: {$bg_color};";
        }
        if ( ! empty( $text_color ) ) {
            $style .= "color: {$text_color};";
        }
        ?>
        
        <section class="module module-features-list section-padding" id="<?php echo esc_attr( $unique_id ); ?>" <?php echo $style ? 'style="' . esc_attr( $style ) . '"' : ''; ?>>
            <div class="container">
                <?php if ( $title || $subtitle ) : ?>
                    <div class="section-header text-center" data-aos="fade-up">
                        <?php if ( $title ) : ?>
                            <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                        <?php endif; ?>
                        <?php if ( $subtitle ) : ?>
                            <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="features-list-wrapper" data-aos="fade-up" data-aos-delay="100">
                    <div class="features-tabs">
                        <?php foreach ( $tabs as $index => $tab ) : ?>
                            <button 
                                class="features-tab-btn<?php echo $index === 0 ? ' active' : ''; ?>" 
                                data-tab="<?php echo esc_attr( $tab['tab_id'] ); ?>"
                                data-target="<?php echo esc_attr( $unique_id ); ?>">
                                <?php if ( ! empty( $tab['tab_icon'] ) ) : 
                                    $icon = $tab['tab_icon'];
                                    // 如果包含HTML标签，直接输出
                                    if ( strpos( $icon, '<' ) !== false ) {
                                        echo '<span class="tab-icon">' . wp_kses_post( $icon ) . '</span>';
                                    }
                                    // 如果包含空格或iconfont等关键词，视为类名
                                    elseif ( strpos( $icon, ' ' ) !== false || strpos( $icon, 'icon-' ) !== false || strpos( $icon, 'iconfont' ) !== false ) {
                                        echo '<span class="tab-icon"><i class="' . esc_attr( $icon ) . '"></i></span>';
                                    }
                                    // 否则当作emoji或文本
                                    else {
                                        echo '<span class="tab-icon">' . esc_html( $icon ) . '</span>';
                                    }
                                endif; ?>
                                <span class="tab-text"><?php echo esc_html( $tab['tab_title'] ); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="features-tabs-content">
                        <?php foreach ( $tabs as $index => $tab ) : ?>
                            <div 
                                class="features-tab-pane<?php echo $index === 0 ? ' active' : ''; ?>" 
                                data-tab-content="<?php echo esc_attr( $tab['tab_id'] ); ?>">
                                
                                <?php if ( ! empty( $tab['features'] ) ) : ?>
                                    <div class="features-grid features-grid-<?php echo esc_attr( $columns ); ?>">
                                        <?php foreach ( $tab['features'] as $feature ) : ?>
                                            <div class="feature-card">
                                                <?php if ( ! empty( $feature['icon'] ) ) : 
                                                    $icon = $feature['icon'];
                                                    ?>
                                                    <div class="feature-icon">
                                                        <?php
                                                        // 如果包含HTML标签，直接输出
                                                        if ( strpos( $icon, '<' ) !== false ) {
                                                            echo wp_kses_post( $icon );
                                                        }
                                                        // 如果包含空格或iconfont等关键词，视为类名
                                                        elseif ( strpos( $icon, ' ' ) !== false || strpos( $icon, 'icon-' ) !== false || strpos( $icon, 'iconfont' ) !== false ) {
                                                            echo '<i class="' . esc_attr( $icon ) . '"></i>';
                                                        }
                                                        // 否则当作emoji或文本
                                                        else {
                                                            echo esc_html( $icon );
                                                        }
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if ( ! empty( $feature['title'] ) ) : ?>
                                                    <h3 class="feature-title"><?php echo esc_html( $feature['title'] ); ?></h3>
                                                <?php endif; ?>
                                                
                                                <?php if ( ! empty( $feature['desc'] ) ) : ?>
                                                    <p class="feature-desc"><?php echo esc_html( $feature['desc'] ); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        
        <?php
        $this->enqueue_tab_script();
    }
    
    private function enqueue_tab_script() {
        static $script_added = false;
        if ( $script_added ) return;
        $script_added = true;
        ?>
        <script>
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                const tabButtons = document.querySelectorAll('.features-tab-btn');
                tabButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-target');
                        const tabId = this.getAttribute('data-tab');
                        const container = document.getElementById(targetId);
                        if (!container) return;
                        container.querySelectorAll('.features-tab-btn').forEach(function(btn) {
                            btn.classList.remove('active');
                        });
                        container.querySelectorAll('.features-tab-pane').forEach(function(pane) {
                            pane.classList.remove('active');
                        });
                        this.classList.add('active');
                        const targetPane = container.querySelector('[data-tab-content="' + tabId + '"]');
                        if (targetPane) {
                            targetPane.classList.add('active');
                        }
                    });
                });
            });
        })();
        </script>
        <?php
    }
}
