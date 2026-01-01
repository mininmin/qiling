<?php
/**
 * Video Module - 视频展示
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Video_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-video-alt3';
        $this->description = '展示视频内容';
    }

    public function get_id() {
        return 'video';
    }

    public function get_name() {
        return '视频展示';
    }

    public function render( $data = array() ) {
        $title = isset( $data['video_title'] ) && $data['video_title'] !== '' ? $data['video_title'] : '视频展示';
        $subtitle = isset( $data['video_subtitle'] ) ? $data['video_subtitle'] : '';
        $bg_color = isset( $data['video_bg_color'] ) && ! empty( $data['video_bg_color'] ) ? $data['video_bg_color'] : '';
        $title_color = isset( $data['video_title_color'] ) && ! empty( $data['video_title_color'] ) ? $data['video_title_color'] : '';
        $subtitle_color = isset( $data['video_subtitle_color'] ) && ! empty( $data['video_subtitle_color'] ) ? $data['video_subtitle_color'] : '';
        $video_url = isset( $data['video_url'] ) ? trim( $data['video_url'] ) : '';
        $video_width = isset( $data['video_width'] ) && ! empty( $data['video_width'] ) ? $data['video_width'] : '100%';
        $video_height = isset( $data['video_height'] ) && ! empty( $data['video_height'] ) ? $data['video_height'] : '500px';
        $video_poster = isset( $data['video_poster'] ) ? $data['video_poster'] : '';
        
        if ( empty( $video_url ) ) {
            return;
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        // 标题颜色样式
        $title_style = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $subtitle_style = ! empty( $subtitle_color ) ? "color: {$subtitle_color};" : '';
        
        // 检测视频类型
        $is_bilibili = $this->is_bilibili_url( $video_url );
        $bvid = '';
        if ( $is_bilibili ) {
            $bvid = $this->extract_bvid( $video_url );
        }
        ?>
        <section class="module module-video section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <?php if ( $title ) : ?>
                <div class="section-header text-center">
                    <h2 class="section-title" style="<?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle" style="<?php echo esc_attr( $subtitle_style ); ?>"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="video-container" style="
                    max-width: <?php echo esc_attr( $video_width ); ?>; 
                    margin: 0 auto;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
                ">
                    <?php if ( $is_bilibili && $bvid ) : ?>
                        <!-- B站视频嵌入 -->
                        <div class="bilibili-player" style="
                            position: relative;
                            width: 100%;
                            height: <?php echo esc_attr( $video_height ); ?>;
                            background: #000;
                        ">
                            <iframe 
                                src="//player.bilibili.com/player.html?bvid=<?php echo esc_attr( $bvid ); ?>&page=1&high_quality=1&danmaku=0&autoplay=0"
                                style="width: 100%; height: 100%; border: none;"
                                allowfullscreen="true"
                                loading="lazy"
                            ></iframe>
                        </div>
                    <?php else : ?>
                        <!-- 普通视频播放器 -->
                        <video 
                            controls 
                            preload="metadata"
                            style="width: 100%; height: <?php echo esc_attr( $video_height ); ?>; background: #000; display: block;"
                            <?php if ( $video_poster ) : ?>poster="<?php echo esc_url( $video_poster ); ?>"<?php endif; ?>
                        >
                            <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
                            您的浏览器不支持视频播放
                        </video>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
        <style>
        .video-container:hover {
            box-shadow: 0 15px 50px rgba(0,0,0,0.2);
        }
        .video-container video:focus {
            outline: none;
        }
        @media (max-width: 768px) {
            .video-container {
                border-radius: 8px !important;
            }
        }
        </style>
        <?php
    }
    
    /**
     * 检测是否为B站链接
     */
    private function is_bilibili_url( $url ) {
        return strpos( $url, 'bilibili.com' ) !== false || strpos( $url, 'b23.tv' ) !== false;
    }
    
    /**
     * 从B站链接提取BV号
     */
    private function extract_bvid( $url ) {
        // 匹配 BV 号格式
        if ( preg_match( '/BV([a-zA-Z0-9]+)/', $url, $matches ) ) {
            return 'BV' . $matches[1];
        }
        return '';
    }
}
