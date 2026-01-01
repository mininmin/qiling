<?php
/**
 * Clients Module - 合作客户（增强版）
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Clients_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-groups';
        $this->description = '合作客户Logo展示';
    }

    public function get_id() {
        return 'clients';
    }

    public function get_name() {
        return '合作客户';
    }

    public function render( $data = array() ) {
        $title = isset( $data['clients_title'] ) && $data['clients_title'] !== '' ? $data['clients_title'] : '合作客户';
        $subtitle = isset( $data['clients_subtitle'] ) ? $data['clients_subtitle'] : '';
        $bg_color = isset( $data['clients_bg_color'] ) && ! empty( $data['clients_bg_color'] ) ? $data['clients_bg_color'] : '';
        $title_color = isset( $data['clients_title_color'] ) && ! empty( $data['clients_title_color'] ) ? $data['clients_title_color'] : '';
        $columns = isset( $data['clients_columns'] ) && ! empty( $data['clients_columns'] ) ? intval( $data['clients_columns'] ) : 6;
        $logo_style = isset( $data['clients_logo_style'] ) ? $data['clients_logo_style'] : 'normal';
        $auto_scroll = isset( $data['clients_auto_scroll'] ) ? $data['clients_auto_scroll'] : '';
        $scroll_speed = isset( $data['clients_scroll_speed'] ) && ! empty( $data['clients_scroll_speed'] ) ? intval( $data['clients_scroll_speed'] ) : 30;
        $card_bg = isset( $data['clients_card_bg'] ) && ! empty( $data['clients_card_bg'] ) ? $data['clients_card_bg'] : '#ffffff';
        $logo_height = isset( $data['clients_logo_height'] ) && ! empty( $data['clients_logo_height'] ) ? $data['clients_logo_height'] : '50px';
        $show_name = isset( $data['clients_show_name'] ) ? $data['clients_show_name'] : '';
        $items = isset( $data['clients_items'] ) ? $data['clients_items'] : array();
        
        if ( empty( $items ) ) {
            $items = array(
                array( 'name' => '华为', 'logo' => '' ),
                array( 'name' => '阿里巴巴', 'logo' => '' ),
                array( 'name' => '腾讯', 'logo' => '' ),
                array( 'name' => '百度', 'logo' => '' ),
                array( 'name' => '京东', 'logo' => '' ),
                array( 'name' => '字节跳动', 'logo' => '' ),
            );
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        $title_style = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $unique_id = 'clients-' . uniqid();
        
        // Logo 样式
        $logo_filter = '';
        if ( $logo_style === 'grayscale' ) {
            $logo_filter = 'filter: grayscale(100%); transition: filter 0.3s;';
        }
        ?>
        <section class="module module-clients section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <?php if ( $title ) : ?>
                    <div class="section-header text-center">
                        <h2 class="section-title" style="<?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( $title ); ?></h2>
                        <?php if ( $subtitle ) : ?>
                            <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( ! empty( $items ) ) : ?>
                    <?php if ( $auto_scroll === '1' ) : ?>
                        <!-- 自动滚动样式 -->
                        <div id="<?php echo esc_attr( $unique_id ); ?>" class="clients-scroll-wrapper" style="overflow: hidden; position: relative;">
                            <div class="clients-scroll-track" style="display: flex; width: max-content; animation: clientsScroll <?php echo esc_attr( $scroll_speed ); ?>s linear infinite;">
                                <?php 
                                // 复制两次以实现无缝滚动
                                for ( $loop = 0; $loop < 2; $loop++ ) :
                                    foreach ( $items as $item ) : 
                                        $logo = isset( $item['logo'] ) ? $item['logo'] : '';
                                        $name = isset( $item['name'] ) ? $item['name'] : '';
                                ?>
                                    <div class="client-item" style="
                                        display: flex;
                                        flex-direction: column;
                                        align-items: center;
                                        justify-content: center;
                                        padding: 20px 30px;
                                        background: <?php echo esc_attr( $card_bg ); ?>;
                                        border-radius: 12px;
                                        min-height: 100px;
                                        min-width: 160px;
                                        margin: 0 15px;
                                        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
                                        transition: all 0.3s;
                                    ">
                                        <?php if ( $logo ) : ?>
                                            <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="max-width: 100%; height: <?php echo esc_attr( $logo_height ); ?>; object-fit: contain; <?php echo esc_attr( $logo_filter ); ?>" class="client-logo" />
                                        <?php else : ?>
                                            <span style="color: var(--color-gray-500); font-weight: 600; font-size: 1rem;"><?php echo esc_html( $name ); ?></span>
                                        <?php endif; ?>
                                        <?php if ( $show_name === '1' && $logo && $name ) : ?>
                                            <span style="color: var(--color-gray-500); font-size: 0.85rem; margin-top: 8px;"><?php echo esc_html( $name ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php 
                                    endforeach;
                                endfor;
                                ?>
                            </div>
                        </div>
                        
                        <style>
                        @keyframes clientsScroll {
                            0% { transform: translateX(0); }
                            100% { transform: translateX(-50%); }
                        }
                        #<?php echo esc_attr( $unique_id ); ?> .clients-scroll-track:hover {
                            animation-play-state: paused;
                        }
                        #<?php echo esc_attr( $unique_id ); ?> .client-item:hover {
                            transform: translateY(-5px);
                            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                        }
                        #<?php echo esc_attr( $unique_id ); ?> .client-logo:hover {
                            filter: grayscale(0%) !important;
                        }
                        </style>
                        
                    <?php else : ?>
                        <!-- 网格样式 -->
                        <div class="clients-grid" style="display: grid; grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr); gap: 20px; align-items: stretch;">
                            <?php foreach ( $items as $item ) : 
                                $logo = isset( $item['logo'] ) ? $item['logo'] : '';
                                $name = isset( $item['name'] ) ? $item['name'] : '';
                                $link = isset( $item['link'] ) ? $item['link'] : '';
                            ?>
                                <?php if ( $link ) : ?>
                                    <a href="<?php echo esc_url( $link ); ?>" target="_blank" class="client-item" style="
                                        display: flex;
                                        flex-direction: column;
                                        align-items: center;
                                        justify-content: center;
                                        padding: 25px 20px;
                                        background: <?php echo esc_attr( $card_bg ); ?>;
                                        border-radius: 12px;
                                        min-height: 100px;
                                        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
                                        transition: all 0.3s;
                                        text-decoration: none;
                                    ">
                                <?php else : ?>
                                    <div class="client-item" style="
                                        display: flex;
                                        flex-direction: column;
                                        align-items: center;
                                        justify-content: center;
                                        padding: 25px 20px;
                                        background: <?php echo esc_attr( $card_bg ); ?>;
                                        border-radius: 12px;
                                        min-height: 100px;
                                        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
                                        transition: all 0.3s;
                                    ">
                                <?php endif; ?>
                                    <?php if ( $logo ) : ?>
                                        <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="max-width: 100%; height: <?php echo esc_attr( $logo_height ); ?>; object-fit: contain; <?php echo esc_attr( $logo_filter ); ?>" class="client-logo" />
                                    <?php else : ?>
                                        <span style="color: var(--color-gray-500); font-weight: 600; font-size: 1rem;"><?php echo esc_html( $name ); ?></span>
                                    <?php endif; ?>
                                    <?php if ( $show_name === '1' && $logo && $name ) : ?>
                                        <span style="color: var(--color-gray-500); font-size: 0.85rem; margin-top: 10px;"><?php echo esc_html( $name ); ?></span>
                                    <?php endif; ?>
                                <?php if ( $link ) : ?>
                                    </a>
                                <?php else : ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <style>
                        .clients-grid .client-item:hover {
                            transform: translateY(-5px);
                            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                        }
                        .clients-grid .client-logo:hover {
                            filter: grayscale(0%) !important;
                        }
                        @media (max-width: 991px) {
                            .clients-grid {
                                grid-template-columns: repeat(4, 1fr) !important;
                            }
                        }
                        @media (max-width: 768px) {
                            .clients-grid {
                                grid-template-columns: repeat(3, 1fr) !important;
                            }
                        }
                        @media (max-width: 576px) {
                            .clients-grid {
                                grid-template-columns: repeat(2, 1fr) !important;
                            }
                        }
                        </style>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
