<?php
/**
 * Branches Module - 门店/分支机构
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Branches_Module extends Module_Base {

    public function __construct() {
        $this->category = 'general';
        $this->icon = 'dashicons-location';
        $this->description = '展示门店/分支机构信息';
    }

    public function get_id() {
        return 'branches';
    }

    public function get_name() {
        return '门店机构';
    }

    public function get_fields() {
        return array(
            array( 'id' => 'branches_title', 'label' => '标题', 'type' => 'text', 'default' => '全国分支机构' ),
            array( 'id' => 'branches_subtitle', 'label' => '副标题', 'type' => 'text', 'default' => '覆盖全国主要城市，为您提供本地化服务' ),
            array( 'id' => 'branches_bg_color', 'label' => '背景颜色', 'type' => 'text', 'description' => '支持渐变色' ),
            array( 'id' => 'branches_title_color', 'label' => '标题颜色', 'type' => 'color' ),
            array( 'id' => 'branches_columns', 'label' => '每行列数', 'type' => 'select', 'options' => array( '2' => '2列', '3' => '3列', '4' => '4列' ), 'default' => '3' ),
            array(
                'id' => 'branches_list',
                'label' => '分支机构列表',
                'type' => 'repeater',
                'description' => '添加各地分支机构信息',
                'fields' => array(
                    array( 'id' => 'name', 'label' => '机构名称', 'type' => 'text' ),
                    array( 'id' => 'address', 'label' => '地址', 'type' => 'textarea' ),
                    array( 'id' => 'phone', 'label' => '电话', 'type' => 'text' ),
                    array( 'id' => 'email', 'label' => '邮箱', 'type' => 'text' ),
                    array( 'id' => 'hours', 'label' => '营业时间', 'type' => 'text' ),
                    array( 'id' => 'image', 'label' => '图片(可选)', 'type' => 'text' ),
                    array( 'id' => 'map_url', 'label' => '地图链接(可选)', 'type' => 'text' ),
                ),
            ),
        );
    }

    public function render( $data = array() ) {
        $title = isset( $data['branches_title'] ) && $data['branches_title'] !== '' ? $data['branches_title'] : '全国分支机构';
        $subtitle = isset( $data['branches_subtitle'] ) ? $data['branches_subtitle'] : '覆盖全国主要城市，为您提供本地化服务';
        $bg_color = isset( $data['branches_bg_color'] ) && ! empty( $data['branches_bg_color'] ) ? $data['branches_bg_color'] : '';
        $title_color = isset( $data['branches_title_color'] ) && ! empty( $data['branches_title_color'] ) ? $data['branches_title_color'] : '';
        $columns = isset( $data['branches_columns'] ) && ! empty( $data['branches_columns'] ) ? intval( $data['branches_columns'] ) : 3;
        $branches = isset( $data['branches_list'] ) ? $data['branches_list'] : array();
        
        // 默认示例数据
        if ( empty( $branches ) ) {
            $branches = array(
                array( 
                    'name' => '北京总部', 
                    'address' => '北京市朝阳区建国路88号SOHO现代城A座', 
                    'phone' => '010-88888888',
                    'email' => 'beijing@example.com',
                    'hours' => '周一至周五 9:00-18:00',
                ),
                array( 
                    'name' => '上海分公司', 
                    'address' => '上海市浦东新区陆家嘴环路1000号恒生银行大厦', 
                    'phone' => '021-88888888',
                    'email' => 'shanghai@example.com',
                    'hours' => '周一至周五 9:00-18:00',
                ),
                array( 
                    'name' => '深圳分公司', 
                    'address' => '深圳市南山区科技园南区高新南七道', 
                    'phone' => '0755-88888888',
                    'email' => 'shenzhen@example.com',
                    'hours' => '周一至周五 9:00-18:00',
                ),
            );
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        $title_style = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $grid_class = 'grid-cols-' . $columns;
        ?>
        <section class="module module-branches section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title" style="<?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if ( ! empty( $branches ) ) : ?>
                    <div class="branches-grid <?php echo esc_attr( $grid_class ); ?>" style="align-items: stretch;">
                        <?php foreach ( $branches as $branch ) : 
                            $name = isset( $branch['name'] ) ? $branch['name'] : '';
                            $address = isset( $branch['address'] ) ? $branch['address'] : '';
                            $phone = isset( $branch['phone'] ) ? $branch['phone'] : '';
                            $email = isset( $branch['email'] ) ? $branch['email'] : '';
                            $hours = isset( $branch['hours'] ) ? $branch['hours'] : '';
                            $image = isset( $branch['image'] ) ? $branch['image'] : '';
                            $map_url = isset( $branch['map_url'] ) ? $branch['map_url'] : '';
                        ?>
                            <div class="branch-card" style="
                                background: #fff;
                                border-radius: 16px;
                                overflow: hidden;
                                box-shadow: 0 10px 40px rgba(0,0,0,0.08);
                                transition: transform 0.3s, box-shadow 0.3s;
                                display: flex;
                                flex-direction: column;
                            ">
                                <!-- 顶部图片或渐变条 -->
                                <?php if ( $image ) : ?>
                                    <div style="height: 160px; overflow: hidden;">
                                        <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                                    </div>
                                <?php else : ?>
                                    <div style="height: 8px; background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);"></div>
                                <?php endif; ?>
                                
                                <!-- 内容区 -->
                                <div style="padding: 25px; flex: 1; display: flex; flex-direction: column;">
                                    <!-- 名称 -->
                                    <h3 style="margin: 0 0 15px; font-size: 1.25rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                                        <span style="font-size: 1.3em;">📍</span>
                                        <?php echo esc_html( $name ); ?>
                                    </h3>
                                    
                                    <!-- 信息列表 -->
                                    <div style="flex: 1; display: flex; flex-direction: column; gap: 12px; color: #64748b; font-size: 0.9rem;">
                                        <?php if ( $address ) : ?>
                                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                <span><?php echo esc_html( $address ); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ( $phone ) : ?>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.362 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                                                <a href="tel:<?php echo esc_attr( $phone ); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html( $phone ); ?></a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ( $email ) : ?>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                                <a href="mailto:<?php echo esc_attr( $email ); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html( $email ); ?></a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ( $hours ) : ?>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span><?php echo esc_html( $hours ); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- 地图链接 -->
                                    <?php if ( $map_url ) : ?>
                                        <a href="<?php echo esc_url( $map_url ); ?>" target="_blank" style="
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 6px;
                                            margin-top: 20px;
                                            padding: 10px 20px;
                                            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
                                            color: #fff;
                                            border-radius: 8px;
                                            text-decoration: none;
                                            font-size: 0.9rem;
                                            font-weight: 500;
                                            transition: all 0.3s;
                                        ">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                                            查看地图
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <style>
        .branch-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.12);
        }
        .branch-card a:hover {
            color: var(--color-primary) !important;
        }
        .branch-card a[href^="mailto"]:hover,
        .branch-card a[href^="tel"]:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .branches-grid {
                gap: 20px !important;
            }
        }
        </style>
        <?php
    }
}
