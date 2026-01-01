<?php
/**
 * Template Name: 关于我们
 *
 * @package Developer_Starter
 */

// 加载关于我们页面专用样式
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'developer-starter-about',
        DEVELOPER_STARTER_ASSETS . '/css/about.css',
        array( 'developer-starter-main' ),
        developer_starter_get_assets_version()
    );
}, 20 );

get_header();

// Get settings
$show_timeline = developer_starter_get_option( 'about_show_timeline', '' );
$show_team = developer_starter_get_option( 'about_show_team', '' );
$show_certificates = developer_starter_get_option( 'about_show_certificates', '' );
$show_environment = developer_starter_get_option( 'about_show_environment', '' );
$show_culture = developer_starter_get_option( 'about_show_culture', '' );

$timeline_items = developer_starter_get_option( 'timeline_items', array() );
$team_members = developer_starter_get_option( 'team_members', array() );
$certificates = developer_starter_get_option( 'about_certificates', array() );
$environment = developer_starter_get_option( 'about_environment', array() );
$culture = developer_starter_get_option( 'about_culture', array() );

// 构建Tab数据
$tabs = array();
$tabs['intro'] = '公司简介';
if ( $show_timeline && ! empty( $timeline_items ) ) $tabs['timeline'] = '发展历程';
if ( $show_team && ! empty( $team_members ) ) $tabs['team'] = '团队成员';
if ( $show_certificates && ! empty( $certificates ) ) $tabs['certificates'] = '资质荣誉';
if ( $show_environment && ! empty( $environment ) ) $tabs['environment'] = '公司环境';
if ( $show_culture && ! empty( $culture ) ) $tabs['culture'] = '企业文化';

$has_tabs = count( $tabs ) > 1;
?>

<div class="page-header" style="background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%); padding: 100px 0 60px;">
    <div class="container">
        <h1 class="page-title" style="color: #fff; text-align: center; font-size: 2.5rem; margin: 0;" data-aos="fade-up">
            <?php the_title(); ?>
        </h1>
        <p style="text-align: center; color: rgba(255,255,255,0.8); margin-top: 15px; font-size: 1.1rem;" data-aos="fade-up" data-aos-delay="100">
            了解我们的故事与文化
        </p>
    </div>
</div>

<div class="page-content section-padding">
    <div class="container">
        
        <?php if ( $has_tabs ) : ?>
        <!-- Tab 导航 -->
        <div class="about-tabs-wrapper" data-aos="fade-up">
            <div class="about-tabs">
                <?php $first = true; foreach ( $tabs as $key => $label ) : ?>
                    <button class="about-tab-btn <?php echo $first ? 'active' : ''; ?>" data-tab="<?php echo $key; ?>">
                        <?php echo esc_html( $label ); ?>
                    </button>
                <?php $first = false; endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Tab 内容区域 -->
        <div class="about-tab-panels">
            
            <!-- 公司简介 -->
            <div class="about-tab-content <?php echo isset( $tabs['intro'] ) ? 'active' : ''; ?>" data-panel="intro">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php 
                    $modules = get_post_meta( get_the_ID(), '_developer_starter_modules', true );
                    if ( ! empty( $modules ) && is_array( $modules ) ) :
                        developer_starter_render_page_modules(); 
                    else :
                    ?>
                        <div class="about-intro" data-aos="fade-up">
                            <div class="entry-content">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
            
            <?php if ( isset( $tabs['timeline'] ) ) : ?>
            <!-- 发展历程 -->
            <div class="about-tab-content" data-panel="timeline">
                <div class="timeline" style="max-width: 800px; margin: 0 auto; position: relative;" data-aos="fade-up">
                    <div class="timeline-line" style="position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, var(--color-primary), #7c3aed);"></div>
                    
                    <?php foreach ( $timeline_items as $idx => $item ) : 
                        $year = isset( $item['year'] ) ? $item['year'] : '';
                        $title = isset( $item['title'] ) ? $item['title'] : '';
                        $desc = isset( $item['desc'] ) ? $item['desc'] : '';
                        $is_left = $idx % 2 === 0;
                    ?>
                        <div class="timeline-item" style="display: flex; align-items: center; margin-bottom: 40px; <?php echo $is_left ? '' : 'flex-direction: row-reverse;'; ?>" data-aos="fade-<?php echo $is_left ? 'right' : 'left'; ?>" data-aos-delay="<?php echo $idx * 100; ?>">
                            <div class="timeline-content" style="flex: 1; padding: 30px; background: #fff; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); <?php echo $is_left ? 'margin-right: 50px; text-align: right;' : 'margin-left: 50px;'; ?>">
                                <span style="display: inline-block; background: linear-gradient(135deg, var(--color-primary), #7c3aed); color: #fff; padding: 5px 15px; border-radius: 20px; font-weight: 600; margin-bottom: 10px;"><?php echo esc_html( $year ); ?></span>
                                <h3 style="font-size: 1.25rem; margin-bottom: 10px;"><?php echo esc_html( $title ); ?></h3>
                                <p style="color: #64748b; margin: 0;"><?php echo esc_html( $desc ); ?></p>
                            </div>
                            <div class="timeline-dot" style="width: 20px; height: 20px; background: var(--color-primary); border-radius: 50%; border: 4px solid #fff; box-shadow: 0 0 0 4px var(--color-primary); position: relative; z-index: 1;"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ( isset( $tabs['team'] ) ) : ?>
            <!-- 团队成员 -->
            <div class="about-tab-content" data-panel="team">
                <div class="team-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
                    <?php foreach ( $team_members as $idx => $member ) : 
                        $name = isset( $member['name'] ) ? $member['name'] : '';
                        $position = isset( $member['position'] ) ? $member['position'] : '';
                        $avatar = isset( $member['avatar'] ) ? $member['avatar'] : '';
                        $desc = isset( $member['desc'] ) ? $member['desc'] : '';
                    ?>
                        <div class="team-member" style="background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.1); text-align: center;" data-aos="fade-up" data-aos-delay="<?php echo $idx * 100; ?>">
                            <div class="member-avatar" style="padding: 30px 30px 0;">
                                <?php if ( $avatar ) : ?>
                                    <img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--color-primary);" />
                                <?php else : ?>
                                    <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--color-primary), #7c3aed); margin: 0 auto; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2.5rem; font-weight: 600;">
                                        <?php echo mb_substr( $name, 0, 1 ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="member-info" style="padding: 20px 30px 30px;">
                                <h3 style="font-size: 1.25rem; margin-bottom: 5px;"><?php echo esc_html( $name ); ?></h3>
                                <p style="color: var(--color-primary); font-weight: 500; margin-bottom: 15px;"><?php echo esc_html( $position ); ?></p>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.6;"><?php echo esc_html( $desc ); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ( isset( $tabs['certificates'] ) ) : ?>
            <!-- 资质荣誉 -->
            <div class="about-tab-content" data-panel="certificates">
                <div class="about-gallery">
                    <?php foreach ( $certificates as $idx => $cert ) : 
                        $image = isset( $cert['image'] ) ? $cert['image'] : '';
                        $title = isset( $cert['title'] ) ? $cert['title'] : '';
                    ?>
                        <div class="about-gallery-item" data-aos="fade-up" data-aos-delay="<?php echo $idx * 50; ?>" onclick="openAboutLightbox('<?php echo esc_url( $image ); ?>', '<?php echo esc_attr( $title ); ?>')">
                            <?php if ( $image ) : ?>
                                <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
                            <?php else : ?>
                                <div class="about-gallery-placeholder">📜</div>
                            <?php endif; ?>
                            <?php if ( $title ) : ?>
                                <div class="gallery-title"><?php echo esc_html( $title ); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ( isset( $tabs['environment'] ) ) : ?>
            <!-- 公司环境 -->
            <div class="about-tab-content" data-panel="environment">
                <div class="about-gallery">
                    <?php foreach ( $environment as $idx => $env ) : 
                        $image = isset( $env['image'] ) ? $env['image'] : '';
                        $title = isset( $env['title'] ) ? $env['title'] : '';
                    ?>
                        <div class="about-gallery-item" data-aos="fade-up" data-aos-delay="<?php echo $idx * 50; ?>" onclick="openAboutLightbox('<?php echo esc_url( $image ); ?>', '<?php echo esc_attr( $title ); ?>')">
                            <?php if ( $image ) : ?>
                                <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
                            <?php else : ?>
                                <div class="about-gallery-placeholder">🏢</div>
                            <?php endif; ?>
                            <?php if ( $title ) : ?>
                                <div class="gallery-title"><?php echo esc_html( $title ); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ( isset( $tabs['culture'] ) ) : ?>
            <!-- 企业文化 -->
            <div class="about-tab-content" data-panel="culture">
                <div class="about-culture-grid">
                    <?php foreach ( $culture as $idx => $item ) : 
                        $icon_raw = isset( $item['icon'] ) ? trim( $item['icon'] ) : '💡';
                        $title = isset( $item['title'] ) ? $item['title'] : '';
                        $desc = isset( $item['desc'] ) ? $item['desc'] : '';
                        
                        $icon = html_entity_decode( $icon_raw, ENT_QUOTES, 'UTF-8' );
                        $is_html_tag = ( strpos( $icon, '<' ) !== false && strpos( $icon, '>' ) !== false );
                        $is_iconfont_class = ! $is_html_tag && ( strpos( $icon, 'iconfont' ) !== false || strpos( $icon, 'icon-' ) !== false || strpos( $icon, 'fa-' ) !== false );
                    ?>
                        <div class="about-culture-card" data-aos="fade-up" data-aos-delay="<?php echo $idx * 100; ?>">
                            <div class="culture-icon">
                                <?php if ( $is_html_tag ) : ?>
                                    <?php echo wp_kses_post( $icon ); ?>
                                <?php elseif ( $is_iconfont_class ) : ?>
                                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                                <?php else : ?>
                                    <?php echo esc_html( $icon ); ?>
                                <?php endif; ?>
                            </div>
                            <h3 class="culture-title"><?php echo esc_html( $title ); ?></h3>
                            <p class="culture-desc"><?php echo nl2br( esc_html( $desc ) ); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php
// Contact Info Section
$company_name = developer_starter_get_option( 'company_name', '' );
$phone = developer_starter_get_option( 'company_phone', '' );
$email = developer_starter_get_option( 'company_email', '' );
$address = developer_starter_get_option( 'company_address', '' );

if ( $company_name || $phone || $email || $address ) :
?>
<section class="about-info section-padding" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
    <div class="container">
        <div class="section-header text-center" style="margin-bottom: 40px;">
            <h2 class="section-title" style="color: #fff; font-size: 2rem;">联系我们</h2>
        </div>
        <div style="max-width: 600px; margin: 0 auto; text-align: center;">
            <?php if ( $company_name ) : ?>
                <p style="font-size: 1.25rem; margin-bottom: 20px;"><?php echo esc_html( $company_name ); ?></p>
            <?php endif; ?>
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; opacity: 0.9;">
                <?php if ( $phone ) : ?>
                    <div>电话：<?php echo esc_html( $phone ); ?></div>
                <?php endif; ?>
                <?php if ( $email ) : ?>
                    <div>邮箱：<?php echo esc_html( $email ); ?></div>
                <?php endif; ?>
            </div>
            <?php if ( $address ) : ?>
                <p style="margin-top: 20px; opacity: 0.8;">地址：<?php echo esc_html( $address ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 灯箱 -->
<div class="about-lightbox" id="aboutLightbox" onclick="closeAboutLightbox()">
    <button class="lightbox-close" onclick="closeAboutLightbox()">&times;</button>
    <img src="" alt="" id="lightboxImage" onclick="event.stopPropagation()" />
    <div class="lightbox-title" id="lightboxTitle"></div>
</div>

<script>
(function() {
    // Tab 切换
    var tabs = document.querySelectorAll('.about-tab-btn');
    var panels = document.querySelectorAll('.about-tab-content');
    
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var target = this.getAttribute('data-tab');
            
            // 更新Tab状态
            tabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            
            // 更新内容面板
            panels.forEach(function(p) {
                if (p.getAttribute('data-panel') === target) {
                    p.classList.add('active');
                } else {
                    p.classList.remove('active');
                }
            });
            
            // 重新触发AOS动画
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
        });
    });
})();

// 灯箱功能
function openAboutLightbox(src, title) {
    if (!src) return;
    document.getElementById('lightboxImage').src = src;
    document.getElementById('lightboxTitle').textContent = title || '';
    document.getElementById('aboutLightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAboutLightbox() {
    document.getElementById('aboutLightbox').classList.remove('active');
    document.body.style.overflow = '';
}

// ESC 键关闭灯箱
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAboutLightbox();
});
</script>

<?php get_footer(); ?>
