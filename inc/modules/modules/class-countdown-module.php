<?php
/**
 * Countdown Module - 产品上线倒计时
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Countdown_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-clock';
        $this->description = '产品上线倒计时展示';
    }

    public function get_id() {
        return 'countdown';
    }

    public function get_name() {
        return '产品倒计时';
    }

    public function render( $data = array() ) {
        $title = isset( $data['countdown_title'] ) && $data['countdown_title'] !== '' ? $data['countdown_title'] : '新品即将上线';
        $subtitle = isset( $data['countdown_subtitle'] ) ? $data['countdown_subtitle'] : '敬请期待';
        $description = isset( $data['countdown_desc'] ) ? $data['countdown_desc'] : '我们正在精心打造一款革命性的产品，即将与您见面！';
        $bg_color = isset( $data['countdown_bg_color'] ) && ! empty( $data['countdown_bg_color'] ) ? $data['countdown_bg_color'] : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        $title_color = isset( $data['countdown_title_color'] ) && ! empty( $data['countdown_title_color'] ) ? $data['countdown_title_color'] : '#ffffff';
        $subtitle_color = isset( $data['countdown_subtitle_color'] ) && ! empty( $data['countdown_subtitle_color'] ) ? $data['countdown_subtitle_color'] : 'rgba(255,255,255,0.8)';
        $desc_color = isset( $data['countdown_desc_color'] ) && ! empty( $data['countdown_desc_color'] ) ? $data['countdown_desc_color'] : 'rgba(255,255,255,0.7)';
        $product_image = isset( $data['countdown_image'] ) ? $data['countdown_image'] : '';
        $target_date = isset( $data['countdown_date'] ) ? $data['countdown_date'] : '';
        $countdown_days = isset( $data['countdown_days'] ) && ! empty( $data['countdown_days'] ) ? intval( $data['countdown_days'] ) : 0;
        $timer_bg = isset( $data['countdown_timer_bg'] ) && ! empty( $data['countdown_timer_bg'] ) ? $data['countdown_timer_bg'] : 'rgba(255,255,255,0.15)';
        $timer_color = isset( $data['countdown_timer_color'] ) && ! empty( $data['countdown_timer_color'] ) ? $data['countdown_timer_color'] : '#ffffff';
        $btn_text = isset( $data['countdown_btn_text'] ) ? $data['countdown_btn_text'] : '立即预约';
        $btn_link = isset( $data['countdown_btn_link'] ) ? $data['countdown_btn_link'] : '#';
        $btn_bg = isset( $data['countdown_btn_bg'] ) && ! empty( $data['countdown_btn_bg'] ) ? $data['countdown_btn_bg'] : '#ffffff';
        $btn_text_color = isset( $data['countdown_btn_text_color'] ) && ! empty( $data['countdown_btn_text_color'] ) ? $data['countdown_btn_text_color'] : '#667eea';
        
        // 计算目标时间戳
        if ( ! empty( $target_date ) ) {
            $target_timestamp = strtotime( $target_date );
        } elseif ( $countdown_days > 0 ) {
            $target_timestamp = time() + ( $countdown_days * 24 * 60 * 60 );
        } else {
            $target_timestamp = time() + ( 7 * 24 * 60 * 60 ); // 默认7天
        }
        
        // 背景样式
        $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        
        // 生成唯一ID
        $unique_id = 'countdown-' . uniqid();
        ?>
        <section class="module module-countdown section-padding" style="<?php echo esc_attr( $bg_style ); ?> position: relative; overflow: hidden;">
            <!-- 背景装饰 -->
            <div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none;">
                <div style="position: absolute; top: -50%; right: -20%; width: 60%; height: 200%; background: rgba(255,255,255,0.05); border-radius: 50%; transform: rotate(-15deg);"></div>
                <div style="position: absolute; bottom: -30%; left: -10%; width: 40%; height: 150%; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
            </div>
            
            <div class="container" style="position: relative; z-index: 1;">
                <div class="countdown-wrapper" style="display: flex; align-items: center; gap: 60px; flex-wrap: wrap;">
                    
                    <!-- 左侧产品图片 -->
                    <?php if ( $product_image ) : ?>
                    <div class="countdown-image" style="flex: 1; min-width: 300px; max-width: 500px;">
                        <div style="
                            position: relative;
                            border-radius: 20px;
                            overflow: hidden;
                            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
                            transform: perspective(1000px) rotateY(-5deg);
                            transition: transform 0.5s;
                        ">
                            <img src="<?php echo esc_url( $product_image ); ?>" alt="<?php echo esc_attr( $title ); ?>" style="width: 100%; display: block;" />
                            <!-- 闪光效果 -->
                            <div style="
                                position: absolute;
                                inset: 0;
                                background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, transparent 50%, transparent 100%);
                                pointer-events: none;
                            "></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- 右侧倒计时内容 -->
                    <div class="countdown-content" style="flex: 1; min-width: 300px; text-align: <?php echo $product_image ? 'left' : 'center'; ?>;">
                        <!-- 标签 -->
                        <div style="
                            display: inline-block;
                            background: rgba(255,255,255,0.2);
                            padding: 8px 20px;
                            border-radius: 30px;
                            margin-bottom: 20px;
                            backdrop-filter: blur(10px);
                        ">
                            <span style="color: <?php echo esc_attr( $subtitle_color ); ?>; font-size: 0.9rem; font-weight: 500;">
                                🎉 <?php echo esc_html( $subtitle ); ?>
                            </span>
                        </div>
                        
                        <!-- 标题 -->
                        <h2 style="
                            color: <?php echo esc_attr( $title_color ); ?>;
                            font-size: 2.8rem;
                            font-weight: 700;
                            margin: 0 0 15px 0;
                            line-height: 1.2;
                        "><?php echo esc_html( $title ); ?></h2>
                        
                        <!-- 描述 -->
                        <?php if ( $description ) : ?>
                        <p style="
                            color: <?php echo esc_attr( $desc_color ); ?>;
                            font-size: 1.1rem;
                            line-height: 1.7;
                            margin: 0 0 35px 0;
                            max-width: 500px;
                        "><?php echo esc_html( $description ); ?></p>
                        <?php endif; ?>
                        
                        <!-- 倒计时 -->
                        <div id="<?php echo esc_attr( $unique_id ); ?>" class="countdown-timer" style="
                            display: flex;
                            gap: 15px;
                            margin-bottom: 35px;
                            flex-wrap: wrap;
                            justify-content: <?php echo $product_image ? 'flex-start' : 'center'; ?>;
                        ">
                            <div class="countdown-item" style="
                                background: <?php echo esc_attr( $timer_bg ); ?>;
                                border-radius: 16px;
                                padding: 20px 25px;
                                text-align: center;
                                min-width: 90px;
                                backdrop-filter: blur(10px);
                                border: 1px solid rgba(255,255,255,0.1);
                            ">
                                <div class="countdown-value" data-type="days" style="
                                    color: <?php echo esc_attr( $timer_color ); ?>;
                                    font-size: 2.5rem;
                                    font-weight: 700;
                                    line-height: 1;
                                ">00</div>
                                <div style="color: <?php echo esc_attr( $timer_color ); ?>; opacity: 0.7; font-size: 0.85rem; margin-top: 8px;">天</div>
                            </div>
                            <div class="countdown-item" style="
                                background: <?php echo esc_attr( $timer_bg ); ?>;
                                border-radius: 16px;
                                padding: 20px 25px;
                                text-align: center;
                                min-width: 90px;
                                backdrop-filter: blur(10px);
                                border: 1px solid rgba(255,255,255,0.1);
                            ">
                                <div class="countdown-value" data-type="hours" style="
                                    color: <?php echo esc_attr( $timer_color ); ?>;
                                    font-size: 2.5rem;
                                    font-weight: 700;
                                    line-height: 1;
                                ">00</div>
                                <div style="color: <?php echo esc_attr( $timer_color ); ?>; opacity: 0.7; font-size: 0.85rem; margin-top: 8px;">时</div>
                            </div>
                            <div class="countdown-item" style="
                                background: <?php echo esc_attr( $timer_bg ); ?>;
                                border-radius: 16px;
                                padding: 20px 25px;
                                text-align: center;
                                min-width: 90px;
                                backdrop-filter: blur(10px);
                                border: 1px solid rgba(255,255,255,0.1);
                            ">
                                <div class="countdown-value" data-type="minutes" style="
                                    color: <?php echo esc_attr( $timer_color ); ?>;
                                    font-size: 2.5rem;
                                    font-weight: 700;
                                    line-height: 1;
                                ">00</div>
                                <div style="color: <?php echo esc_attr( $timer_color ); ?>; opacity: 0.7; font-size: 0.85rem; margin-top: 8px;">分</div>
                            </div>
                            <div class="countdown-item" style="
                                background: <?php echo esc_attr( $timer_bg ); ?>;
                                border-radius: 16px;
                                padding: 20px 25px;
                                text-align: center;
                                min-width: 90px;
                                backdrop-filter: blur(10px);
                                border: 1px solid rgba(255,255,255,0.1);
                            ">
                                <div class="countdown-value" data-type="seconds" style="
                                    color: <?php echo esc_attr( $timer_color ); ?>;
                                    font-size: 2.5rem;
                                    font-weight: 700;
                                    line-height: 1;
                                ">00</div>
                                <div style="color: <?php echo esc_attr( $timer_color ); ?>; opacity: 0.7; font-size: 0.85rem; margin-top: 8px;">秒</div>
                            </div>
                        </div>
                        
                        <!-- 按钮 -->
                        <?php if ( $btn_text && $btn_link ) : ?>
                        <a href="<?php echo esc_url( $btn_link ); ?>" class="countdown-btn" style="
                            display: inline-flex;
                            align-items: center;
                            gap: 10px;
                            padding: 16px 40px;
                            background: <?php echo esc_attr( $btn_bg ); ?>;
                            color: <?php echo esc_attr( $btn_text_color ); ?>;
                            border-radius: 50px;
                            font-size: 1.1rem;
                            font-weight: 600;
                            text-decoration: none;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                            transition: all 0.3s;
                        ">
                            <?php echo esc_html( $btn_text ); ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        
        <style>
        .countdown-image:hover > div {
            transform: perspective(1000px) rotateY(0deg) scale(1.02) !important;
        }
        .countdown-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3) !important;
        }
        .countdown-item {
            transition: transform 0.3s;
        }
        .countdown-item:hover {
            transform: translateY(-5px);
        }
        @media (max-width: 992px) {
            .countdown-wrapper {
                flex-direction: column !important;
                text-align: center !important;
            }
            .countdown-image {
                max-width: 100% !important;
            }
            .countdown-image > div {
                transform: none !important;
            }
            .countdown-content {
                text-align: center !important;
            }
            .countdown-timer {
                justify-content: center !important;
            }
        }
        @media (max-width: 576px) {
            .countdown-item {
                min-width: 70px !important;
                padding: 15px !important;
            }
            .countdown-value {
                font-size: 1.8rem !important;
            }
        }
        </style>
        
        <script>
        (function() {
            var targetTime = <?php echo intval( $target_timestamp * 1000 ); ?>;
            var container = document.getElementById('<?php echo esc_js( $unique_id ); ?>');
            if (!container) return;
            
            function updateCountdown() {
                var now = new Date().getTime();
                var distance = targetTime - now;
                
                if (distance < 0) {
                    distance = 0;
                }
                
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                var daysEl = container.querySelector('[data-type="days"]');
                var hoursEl = container.querySelector('[data-type="hours"]');
                var minutesEl = container.querySelector('[data-type="minutes"]');
                var secondsEl = container.querySelector('[data-type="seconds"]');
                
                if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
                if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
                if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
                if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        })();
        </script>
        <?php
    }
}
