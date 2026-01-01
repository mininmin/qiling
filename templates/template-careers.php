<?php
/**
 * Template Name: 招聘/职业机会
 * Template Post Type: page
 *
 * 招聘页面模板 - 用于展示公司招聘信息、职位列表和在线申请
 *
 * @package Developer_Starter
 */

get_header();

// 获取招聘设置
$careers_options = Developer_Starter\Core\Careers_Manager::get_option();
$hero_title = $careers_options['hero_title'] ?? the_title( '', '', false );
$hero_subtitle = $careers_options['hero_subtitle'] ?? '';
$stat_1_number = $careers_options['stat_1_number'] ?? '50+';
$stat_1_label = $careers_options['stat_1_label'] ?? '团队成员';
$stat_2_number = $careers_options['stat_2_number'] ?? '10+';
$stat_2_label = $careers_options['stat_2_label'] ?? '开放职位';
$stat_3_number = $careers_options['stat_3_number'] ?? '5个';
$stat_3_label = $careers_options['stat_3_label'] ?? '办公城市';
$benefits = $careers_options['benefits'] ?? array();
$enable_application = $careers_options['enable_application'] ?? '1';
$hero_bg_color = $careers_options['hero_bg_color'] ?? '';

// HR联系方式 - 优先使用招聘设置，否则使用主题设置
$hr_phone = ! empty( $careers_options['hr_phone'] ) ? $careers_options['hr_phone'] : developer_starter_get_option( 'company_phone', '' );
$hr_email = ! empty( $careers_options['hr_email'] ) ? $careers_options['hr_email'] : developer_starter_get_option( 'company_email', '' );

// 获取公司信息
$company_name = developer_starter_get_option( 'company_name', '' );
$address = developer_starter_get_option( 'company_address', '' );

// 获取职位列表
$positions = Developer_Starter\Core\Careers_Manager::get_positions();

// 福利图标映射
$benefit_icons = array(
    'money' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>',
    'shield' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
    'book' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>',
    'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
    'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
    'trending' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
    'heart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>',
    'star' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
);

// 福利背景色
$benefit_colors = array(
    'money' => 'linear-gradient(135deg, var(--color-primary), #7c3aed)',
    'shield' => 'linear-gradient(135deg, #10b981, #059669)',
    'book' => 'linear-gradient(135deg, #f59e0b, #d97706)',
    'calendar' => 'linear-gradient(135deg, #ec4899, #be185d)',
    'users' => 'linear-gradient(135deg, #8b5cf6, #6d28d9)',
    'trending' => 'linear-gradient(135deg, #06b6d4, #0891b2)',
    'heart' => 'linear-gradient(135deg, #ef4444, #dc2626)',
    'star' => 'linear-gradient(135deg, #eab308, #ca8a04)',
);

// 职位类型和分类映射
$job_types = array( 'fulltime' => '全职', 'parttime' => '兼职', 'intern' => '实习' );
$categories = array( 'tech' => '技术研发', 'product' => '产品设计', 'market' => '市场运营', 'admin' => '职能管理' );
?>

<!-- Hero Banner -->
<?php 
$hero_style = '';
if ( ! empty( $hero_bg_color ) ) {
    $hero_style = 'background: ' . esc_attr( $hero_bg_color ) . ';';
}
?>
<div class="careers-hero"<?php echo $hero_style ? ' style="' . $hero_style . '"' : ''; ?>>
    <div class="careers-hero-bg"></div>
    <div class="careers-hero-particles"></div>
    <div class="container">
        <div class="careers-hero-content">
            <span class="careers-badge">🚀 加入我们</span>
            <h1 class="careers-hero-title"><?php echo esc_html( $hero_title ); ?></h1>
            <?php if ( $hero_subtitle ) : ?>
                <p class="careers-hero-subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
            <?php endif; ?>
            <div class="careers-hero-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html( $stat_1_number ); ?></span>
                    <span class="stat-label"><?php echo esc_html( $stat_1_label ); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html( $stat_2_number ); ?></span>
                    <span class="stat-label"><?php echo esc_html( $stat_2_label ); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html( $stat_3_number ); ?></span>
                    <span class="stat-label"><?php echo esc_html( $stat_3_label ); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 公司福利 -->
<?php if ( ! empty( $benefits ) ) : ?>
<section class="careers-benefits section-padding">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">为什么选择我们？</h2>
            <p class="section-subtitle">我们提供具有竞争力的薪酬福利和广阔的发展空间</p>
        </div>
        
        <div class="benefits-grid">
            <?php foreach ( $benefits as $idx => $benefit ) : 
                $icon_key = $benefit['icon'] ?? 'star';
                $icon_svg = $benefit_icons[ $icon_key ] ?? $benefit_icons['star'];
                $icon_bg = $benefit_colors[ $icon_key ] ?? $benefit_colors['star'];
            ?>
                <div class="benefit-card">
                    <div class="benefit-icon" style="background: <?php echo $icon_bg; ?>;">
                        <?php echo $icon_svg; ?>
                    </div>
                    <h3 class="benefit-title"><?php echo esc_html( $benefit['title'] ?? '' ); ?></h3>
                    <p class="benefit-desc"><?php echo esc_html( $benefit['desc'] ?? '' ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 招聘职位 -->
<section class="careers-positions section-padding" style="background: #f8fafc;">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">开放职位</h2>
            <p class="section-subtitle">寻找你的理想职位，开启精彩职业旅程</p>
        </div>
        
        <div class="positions-filter">
            <button class="filter-btn active" data-filter="all">全部职位</button>
            <?php foreach ( $categories as $cat_key => $cat_label ) : ?>
                <button class="filter-btn" data-filter="<?php echo $cat_key; ?>"><?php echo $cat_label; ?></button>
            <?php endforeach; ?>
        </div>
        
        <div class="positions-list">
            <?php if ( empty( $positions ) ) : ?>
                <div class="no-positions" style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px;">
                    <p style="color: #64748b; font-size: 1.1rem;">暂无开放职位，请稍后再来查看</p>
                </div>
            <?php else : ?>
                <?php foreach ( $positions as $pos ) : ?>
                    <div class="position-card" data-category="<?php echo esc_attr( $pos->category ); ?>">
                        <div class="position-header">
                            <div class="position-info">
                                <h3 class="position-title"><?php echo esc_html( $pos->title ); ?></h3>
                                <div class="position-meta">
                                    <span class="meta-item">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <?php echo esc_html( $pos->location ); ?>
                                    </span>
                                    <span class="meta-item">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                                        <?php echo esc_html( $pos->department ); ?>
                                    </span>
                                    <span class="meta-item">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                        <?php echo esc_html( $job_types[ $pos->job_type ] ?? '全职' ); ?>
                                    </span>
                                </div>
                            </div>
                            <?php if ( $pos->salary ) : ?>
                                <div class="position-salary"><?php echo esc_html( $pos->salary ); ?></div>
                            <?php endif; ?>
                            <button class="position-toggle">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                        </div>
                        <div class="position-details">
                            <?php if ( $pos->description ) : ?>
                                <div class="detail-section">
                                    <h4>职位描述</h4>
                                    <ul>
                                        <?php foreach ( explode( "\n", $pos->description ) as $line ) : ?>
                                            <?php if ( trim( $line ) ) : ?>
                                                <li><?php echo esc_html( trim( $line ) ); ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if ( $pos->requirements ) : ?>
                                <div class="detail-section">
                                    <h4>任职要求</h4>
                                    <ul>
                                        <?php foreach ( explode( "\n", $pos->requirements ) as $line ) : ?>
                                            <?php if ( trim( $line ) ) : ?>
                                                <li><?php echo esc_html( trim( $line ) ); ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if ( $enable_application ) : ?>
                                <a href="#apply-form" class="btn btn-primary" data-position-id="<?php echo esc_attr( $pos->id ); ?>" data-position-title="<?php echo esc_attr( $pos->title ); ?>">立即申请</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 在线申请表单 -->
<?php if ( $enable_application ) : ?>
<section id="apply-form" class="careers-apply section-padding">
    <div class="container">
        <div class="apply-wrapper">
            <div class="apply-info">
                <h2>投递简历</h2>
                <p>填写以下信息，我们会尽快与您联系</p>
                
                <div class="apply-tips">
                    <h4>投递须知</h4>
                    <ul>
                        <li>请确保联系方式真实有效</li>
                        <li>简历投递后3-5个工作日内回复</li>
                        <li>面试通过后签订正式劳动合同</li>
                    </ul>
                </div>
                
                <?php if ( $hr_phone || $hr_email ) : ?>
                <div class="hr-contact">
                    <h4>HR联系方式</h4>
                    <?php if ( $hr_phone ) : ?>
                        <p><strong>电话：</strong><?php echo esc_html( $hr_phone ); ?></p>
                    <?php endif; ?>
                    <?php if ( $hr_email ) : ?>
                        <p><strong>邮箱：</strong><?php echo esc_html( $hr_email ); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="apply-form-container">
                <!-- 成功/失败提示弹窗 -->
                <div id="apply-toast" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; padding: 40px 60px; border-radius: 16px; text-align: center; box-shadow: 0 25px 80px rgba(0,0,0,0.25);">
                    <div id="apply-toast-icon" style="font-size: 4rem; margin-bottom: 15px;"></div>
                    <div id="apply-toast-text" style="font-size: 1.25rem; font-weight: 600;"></div>
                </div>
                <div id="apply-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9998;"></div>
                
                <form id="careers-apply-form" class="apply-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>姓名 <span class="required">*</span></label>
                            <input type="text" name="name" required placeholder="请输入您的姓名" />
                        </div>
                        <div class="form-group">
                            <label>电话 <span class="required">*</span></label>
                            <input type="tel" name="phone" required placeholder="请输入联系电话" />
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>邮箱 <span class="required">*</span></label>
                            <input type="email" name="email" required placeholder="请输入电子邮箱" />
                        </div>
                        <div class="form-group">
                            <label>应聘职位 <span class="required">*</span></label>
                            <select name="position_title" id="position-select" required>
                                <option value="">请选择职位</option>
                                <?php foreach ( $positions as $pos ) : ?>
                                    <option value="<?php echo esc_attr( $pos->title ); ?>" data-id="<?php echo esc_attr( $pos->id ); ?>">
                                        <?php echo esc_html( $pos->title ); ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="其他职位">其他职位</option>
                            </select>
                            <input type="hidden" name="position_id" id="position-id" value="0" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>自我介绍</label>
                        <textarea name="message" rows="5" placeholder="请简要介绍您的教育背景、工作经验和核心技能..."></textarea>
                    </div>
                    
                    <input type="hidden" name="action" value="ds_submit_careers_application" />
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'ds_careers_application_nonce' ); ?>" />
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-submit">
                        <span class="btn-text">提交申请</span>
                        <span class="btn-loading" style="display: none;">
                            <svg width="20" height="20" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="32" stroke-linecap="round"/>
                            </svg>
                            提交中...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 公司地址 -->
<?php if ( $address || $company_name ) : ?>
<section class="careers-location" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div class="container">
        <div class="location-content">
            <div class="location-text">
                <h3>工作地点</h3>
                <?php if ( $company_name ) : ?>
                    <p class="company-name"><?php echo esc_html( $company_name ); ?></p>
                <?php endif; ?>
                <?php if ( $address ) : ?>
                    <p class="company-address"><?php echo esc_html( $address ); ?></p>
                <?php endif; ?>
            </div>
            <div class="location-cta">
                <p>期待与你相见！</p>
                <?php if ( $enable_application ) : ?>
                    <a href="#apply-form" class="btn btn-light btn-lg">立即加入</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
/* ===== Careers Hero ===== */
.careers-hero {
    position: relative;
    background: linear-gradient(135deg, #2563eb 0%, #0891b2 50%, #10b981 100%);
    padding: 140px 0 100px;
    overflow: hidden;
}

.careers-hero-bg {
    position: absolute;
    inset: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="3" fill="rgba(255,255,255,0.08)"/><circle cx="40" cy="80" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="90" r="1" fill="rgba(255,255,255,0.15)"/></svg>');
    animation: float 20s linear infinite;
}

.careers-hero-particles {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 30% 70%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 70% 30%, rgba(255,255,255,0.08) 0%, transparent 40%);
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(1deg); }
}

.careers-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    color: #fff;
}

.careers-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    padding: 8px 20px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 20px;
    animation: fadeInUp 0.6s ease;
}

.careers-hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin: 0 0 20px;
    text-shadow: 0 4px 20px rgba(0,0,0,0.2);
    animation: fadeInUp 0.6s ease 0.1s both;
}

.careers-hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto 40px;
    animation: fadeInUp 0.6s ease 0.2s both;
}

.careers-hero-stats {
    display: flex;
    justify-content: center;
    gap: 60px;
    animation: fadeInUp 0.6s ease 0.3s both;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ===== Benefits Section ===== */
.benefits-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.benefit-card {
    background: #fff;
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.06);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.benefit-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.12);
}

.benefit-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--color-primary), #7c3aed);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: #fff;
}

.benefit-icon svg {
    width: 32px;
    height: 32px;
}

.benefit-title {
    font-size: 1.25rem;
    margin: 0 0 10px;
    color: #1e293b;
}

.benefit-desc {
    color: #64748b;
    font-size: 0.95rem;
    margin: 0;
    line-height: 1.6;
}

/* ===== Positions Section ===== */
.positions-filter {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 10px 24px;
    border: 2px solid #e2e8f0;
    background: #fff;
    border-radius: 30px;
    font-size: 0.95rem;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: #fff;
}

.positions-list {
    max-width: 900px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.position-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    transition: all 0.3s;
}

.position-card:hover {
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.position-header {
    display: flex;
    align-items: center;
    padding: 25px 30px;
    gap: 20px;
    cursor: pointer;
}

.position-info {
    flex: 1;
}

.position-title {
    font-size: 1.25rem;
    margin: 0 0 10px;
    color: #1e293b;
}

.position-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #64748b;
    font-size: 0.9rem;
}

.meta-item svg {
    opacity: 0.7;
}

.position-salary {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1.1rem;
}

.position-toggle {
    width: 40px;
    height: 40px;
    border: none;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    color: #64748b;
}

.position-toggle:hover {
    background: var(--color-primary);
    color: #fff;
}

.position-card.expanded .position-toggle {
    transform: rotate(180deg);
}

.position-details {
    display: none;
    padding: 0 30px 30px;
    border-top: 1px solid #f1f5f9;
    animation: slideDown 0.3s ease;
}

.position-card.expanded .position-details {
    display: block;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.detail-section {
    margin-bottom: 25px;
}

.detail-section h4 {
    font-size: 1rem;
    color: #1e293b;
    margin: 20px 0 15px;
}

.detail-section ul {
    margin: 0;
    padding-left: 20px;
    color: #64748b;
}

.detail-section li {
    margin-bottom: 8px;
    line-height: 1.6;
}

/* ===== Apply Section ===== */
.careers-apply {
    background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
}

.apply-wrapper {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 60px;
    max-width: 1000px;
    margin: 0 auto;
}

.apply-info h2 {
    font-size: 2rem;
    margin: 0 0 10px;
    color: #1e293b;
}

.apply-info > p {
    color: #64748b;
    margin: 0 0 30px;
}

.apply-tips {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
}

.apply-tips h4 {
    margin: 0 0 15px;
    color: var(--color-primary);
    font-size: 1rem;
}

.apply-tips ul {
    margin: 0;
    padding-left: 20px;
    color: #1e40af;
}

.apply-tips li {
    margin-bottom: 8px;
}

.hr-contact {
    background: #f8fafc;
    border-radius: 16px;
    padding: 25px;
}

.hr-contact h4 {
    margin: 0 0 15px;
    color: #1e293b;
    font-size: 1rem;
}

.hr-contact p {
    margin: 0 0 8px;
    color: #64748b;
}

.apply-form-container {
    background: #fff;
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.08);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #334155;
}

.form-group .required {
    color: #ef4444;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s;
    font-family: inherit;
    box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--color-primary);
    outline: none;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.btn-submit {
    width: 100%;
    padding: 16px 32px;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
}

/* ===== Location Section ===== */
.careers-location {
    padding: 60px 0;
    color: #fff;
}

.location-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
}

.location-text h3 {
    font-size: 1.5rem;
    margin: 0 0 15px;
}

.company-name {
    font-size: 1.25rem;
    margin: 0 0 8px;
    opacity: 0.9;
}

.company-address {
    opacity: 0.7;
    margin: 0;
}

.location-cta {
    text-align: center;
}

.location-cta p {
    margin: 0 0 15px;
    font-size: 1.1rem;
    opacity: 0.9;
}

/* ===== Responsive ===== */
@media (max-width: 992px) {
    .benefits-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .apply-wrapper {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .careers-hero-stats {
        gap: 40px;
    }
}

@media (max-width: 768px) {
    .careers-hero {
        padding: 100px 0 60px;
    }
    
    .careers-hero-title {
        font-size: 2rem;
    }
    
    .careers-hero-subtitle {
        font-size: 1rem;
    }
    
    .careers-hero-stats {
        flex-wrap: wrap;
        gap: 30px;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .benefits-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .position-header {
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .position-salary {
        order: -1;
    }
    
    .location-content {
        flex-direction: column;
        text-align: center;
    }
    
    .apply-form-container {
        padding: 25px;
    }
    
    #apply-toast {
        padding: 30px 40px !important;
        width: 85% !important;
    }
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 职位筛选
    var filterBtns = document.querySelectorAll('.filter-btn');
    var positionCards = document.querySelectorAll('.position-card');
    
    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var filter = this.getAttribute('data-filter');
            
            filterBtns.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            
            positionCards.forEach(function(card) {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // 职位展开/收起
    var positionHeaders = document.querySelectorAll('.position-header');
    positionHeaders.forEach(function(header) {
        header.addEventListener('click', function() {
            var card = this.closest('.position-card');
            card.classList.toggle('expanded');
        });
    });
    
    // 职位选择同步
    var positionSelect = document.getElementById('position-select');
    var positionIdInput = document.getElementById('position-id');
    if (positionSelect && positionIdInput) {
        positionSelect.addEventListener('change', function() {
            var selected = this.options[this.selectedIndex];
            positionIdInput.value = selected.getAttribute('data-id') || 0;
        });
    }
    
    // 点击申请按钮时选中对应职位
    document.querySelectorAll('.position-details .btn-primary').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            var positionTitle = this.getAttribute('data-position-title');
            var positionId = this.getAttribute('data-position-id');
            if (positionSelect && positionTitle) {
                for (var i = 0; i < positionSelect.options.length; i++) {
                    if (positionSelect.options[i].value === positionTitle) {
                        positionSelect.selectedIndex = i;
                        if (positionIdInput) {
                            positionIdInput.value = positionId || 0;
                        }
                        break;
                    }
                }
            }
        });
    });
    
    // 表单提交
    var applyForm = document.getElementById('careers-apply-form');
    if (applyForm) {
        applyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var form = this;
            var btnText = form.querySelector('.btn-text');
            var btnLoading = form.querySelector('.btn-loading');
            var submitBtn = form.querySelector('.btn-submit');
            var toast = document.getElementById('apply-toast');
            var overlay = document.getElementById('apply-overlay');
            var toastIcon = document.getElementById('apply-toast-icon');
            var toastText = document.getElementById('apply-toast-text');
            
            // 显示加载状态
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-flex';
            
            var formData = new FormData(form);
            
            fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                overlay.style.display = 'block';
                toast.style.display = 'block';
                
                if (data.success) {
                    toast.style.background = '#dcfce7';
                    toastIcon.innerHTML = '✅';
                    toastText.innerHTML = '<span style="color:#166534;">' + data.data.message + '</span>';
                    form.reset();
                } else {
                    toast.style.background = '#fee2e2';
                    toastIcon.innerHTML = '❌';
                    toastText.innerHTML = '<span style="color:#991b1b;">' + (data.data ? data.data.message : '提交失败，请稍后重试') + '</span>';
                }
                
                submitBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
                
                setTimeout(function() {
                    toast.style.display = 'none';
                    overlay.style.display = 'none';
                }, 2500);
            })
            .catch(function(err) {
                console.error('Form error:', err);
                overlay.style.display = 'block';
                toast.style.display = 'block';
                toast.style.background = '#fee2e2';
                toastIcon.innerHTML = '❌';
                toastText.innerHTML = '<span style="color:#991b1b;">网络错误，请稍后重试</span>';
                
                submitBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
                
                setTimeout(function() {
                    toast.style.display = 'none';
                    overlay.style.display = 'none';
                }, 2500);
            });
        });
    }
    
    // 点击遮罩关闭弹窗
    var applyOverlay = document.getElementById('apply-overlay');
    if (applyOverlay) {
        applyOverlay.addEventListener('click', function() {
            this.style.display = 'none';
            document.getElementById('apply-toast').style.display = 'none';
        });
    }
});
</script>

<?php get_footer(); ?>
