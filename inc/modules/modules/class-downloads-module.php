<?php
/**
 * Downloads Module - 下载中心
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Downloads_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-download';
        $this->description = '资料下载中心';
    }

    public function get_id() {
        return 'downloads';
    }

    public function get_name() {
        return '下载中心';
    }

    public function get_fields() {
        return array(
            array(
                'id' => 'downloads_title',
                'label' => '标题',
                'type' => 'text',
                'default' => '资料下载',
            ),
            array(
                'id' => 'downloads_subtitle',
                'label' => '副标题',
                'type' => 'text',
                'default' => '下载我们的产品资料和技术文档',
            ),
            array(
                'id' => 'downloads_columns',
                'label' => '每行列数',
                'type' => 'select',
                'options' => array(
                    '1' => '1列',
                    '2' => '2列',
                    '3' => '3列',
                ),
                'default' => '1',
            ),
            array(
                'id' => 'downloads_items',
                'label' => '下载项目',
                'type' => 'repeater',
                'description' => '添加下载文件，链接可填写外部URL。文件格式、日期、说明为可选项，填写后会显示',
                'fields' => array(
                    array( 'id' => 'title', 'label' => '文件名称', 'type' => 'text' ),
                    array( 'id' => 'size', 'label' => '文件大小', 'type' => 'text' ),
                    array( 'id' => 'file', 'label' => '文件链接(可填外部URL)', 'type' => 'text' ),
                    array( 'id' => 'icon', 'label' => '图标(emoji或留空)', 'type' => 'text' ),
                    array( 'id' => 'format', 'label' => '文件格式(可选，如PDF、DOC等)', 'type' => 'text' ),
                    array( 'id' => 'date', 'label' => '文件日期(可选，如2024-01-01)', 'type' => 'text' ),
                    array( 'id' => 'description', 'label' => '文件说明(可选)', 'type' => 'textarea' ),
                ),
            ),
        );
    }

    public function render( $data = array() ) {
        $title = isset( $data['downloads_title'] ) && $data['downloads_title'] !== '' ? $data['downloads_title'] : '资料下载';
        $subtitle = isset( $data['downloads_subtitle'] ) ? $data['downloads_subtitle'] : '';
        $columns = isset( $data['downloads_columns'] ) ? intval( $data['downloads_columns'] ) : 1;
        $items = isset( $data['downloads_items'] ) ? $data['downloads_items'] : array();
        
        if ( empty( $items ) ) {
            $items = array(
                array( 'title' => '产品手册', 'file' => '', 'size' => '2.5MB', 'icon' => '📄' ),
                array( 'title' => '技术白皮书', 'file' => '', 'size' => '1.2MB', 'icon' => '📋' ),
                array( 'title' => '用户指南', 'file' => '', 'size' => '3.8MB', 'icon' => '📘' ),
            );
        }
        
        $grid_style = $columns > 1 ? "display: grid; grid-template-columns: repeat({$columns}, 1fr); gap: 20px;" : "";
        ?>
        <section class="module module-downloads section-padding">
            <div class="container">
                <div class="section-header text-center" style="margin-bottom: 40px;">
                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="downloads-list" style="max-width: 1000px; margin: 0 auto; <?php echo $grid_style; ?>">
                    <?php foreach ( $items as $item ) : 
                        $item_title = isset( $item['title'] ) ? $item['title'] : '';
                        $file = isset( $item['file'] ) ? trim( $item['file'] ) : '';
                        $size = isset( $item['size'] ) ? $item['size'] : '';
                        $icon = isset( $item['icon'] ) && $item['icon'] ? $item['icon'] : '📄';
                        $format = isset( $item['format'] ) ? trim( $item['format'] ) : '';
                        $date = isset( $item['date'] ) ? trim( $item['date'] ) : '';
                        $description = isset( $item['description'] ) ? trim( $item['description'] ) : '';
                    ?>
                        <div class="download-item" style="display: flex; align-items: flex-start; justify-content: space-between; padding: 20px 24px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: <?php echo $columns > 1 ? '0' : '15px'; ?>; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                            <div style="display: flex; align-items: flex-start; gap: 15px; flex: 1;">
                                <span style="font-size: 2rem; line-height: 1;"><?php echo esc_html( $icon ); ?></span>
                                <div style="flex: 1;">
                                    <strong style="font-size: 1.05rem; color: #1e293b;"><?php echo esc_html( $item_title ); ?></strong>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 5px;">
                                        <?php if ( $format ) : ?>
                                            <span style="display: inline-block; padding: 2px 8px; background: #e0e7ff; color: #3730a3; font-size: 0.75rem; border-radius: 4px; font-weight: 500;"><?php echo esc_html( $format ); ?></span>
                                        <?php endif; ?>
                                        <?php if ( $size ) : ?>
                                            <span style="color: #94a3b8; font-size: 0.85rem;"><?php echo esc_html( $size ); ?></span>
                                        <?php endif; ?>
                                        <?php if ( $date ) : ?>
                                            <span style="color: #94a3b8; font-size: 0.85rem;">📅 <?php echo esc_html( $date ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ( $description ) : ?>
                                        <p style="margin: 8px 0 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;"><?php echo esc_html( $description ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ( $file ) : ?>
                                <a href="<?php echo esc_url( $file ); ?>" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); border: none; padding: 10px 20px; border-radius: 8px; color: #fff; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; flex-shrink: 0; margin-left: 15px;" target="_blank" download>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    下载
                                </a>
                            <?php else : ?>
                                <span style="padding: 10px 20px; background: #f1f5f9; border-radius: 8px; color: #94a3b8; font-size: 0.9rem; flex-shrink: 0; margin-left: 15px;">暂无文件</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <style>
            .download-item:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important; }
        </style>
        <?php
    }
}
