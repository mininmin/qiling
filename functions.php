<?php
/**
 * Qi Ling 主题函数和定义
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

// 防止直接访问
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 主题常量
 */
define( 'DEVELOPER_STARTER_VERSION', '1.0.0' );
define( 'DEVELOPER_STARTER_DIR', get_template_directory() );
define( 'DEVELOPER_STARTER_URI', get_template_directory_uri() );
define( 'DEVELOPER_STARTER_INC', DEVELOPER_STARTER_DIR . '/inc' );
define( 'DEVELOPER_STARTER_ASSETS', DEVELOPER_STARTER_URI . '/assets' );

/**
 * 根据主题设置切换前台显示语言
 * 必须在加载翻译文件之前执行
 */
function developer_starter_switch_locale( $locale ) {
    // 只在前台切换语言，后台保持WordPress设置
    if ( is_admin() ) {
        return $locale;
    }
    
    // 获取主题语言设置，默认为 zh_CN
    $options = get_option( 'developer_starter_options', array() );
    $theme_language = isset( $options['theme_language'] ) && ! empty( $options['theme_language'] ) 
        ? $options['theme_language'] 
        : 'zh_CN';
    
    return $theme_language;
}
// 优先级设为1，确保在其他操作之前执行
add_filter( 'locale', 'developer_starter_switch_locale', 1 );

/**
 * 加载主题翻译文件
 * 使用init钩子确保locale已经正确切换
 */
function developer_starter_load_textdomain() {
    // 先卸载可能已加载的（错误locale的）翻译
    unload_textdomain( 'developer-starter' );
    
    // 重新加载正确locale的翻译
    $locale = get_locale();
    $mo_file = DEVELOPER_STARTER_DIR . '/languages/developer-starter-' . $locale . '.mo';
    
    if ( file_exists( $mo_file ) ) {
        load_textdomain( 'developer-starter', $mo_file );
    }
}
add_action( 'init', 'developer_starter_load_textdomain', 1 );

/**
 * 核心类
 */
require_once DEVELOPER_STARTER_INC . '/core/class-theme-setup.php';
require_once DEVELOPER_STARTER_INC . '/core/class-assets.php';
require_once DEVELOPER_STARTER_INC . '/core/class-helpers.php';
require_once DEVELOPER_STARTER_INC . '/core/class-message-manager.php';
require_once DEVELOPER_STARTER_INC . '/core/class-smtp-manager.php';

/**
 * 后台管理类
 */
require_once DEVELOPER_STARTER_INC . '/admin/class-admin-settings.php';
require_once DEVELOPER_STARTER_INC . '/admin/class-meta-boxes.php';

/**
 * 模块系统
 */
require_once DEVELOPER_STARTER_INC . '/modules/class-module-base.php';
require_once DEVELOPER_STARTER_INC . '/modules/class-module-manager.php';

/**
 * 加载各个模块
 */
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-banner-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-services-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-features-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-clients-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-stats-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-cta-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-image-text-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-columns-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-timeline-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-faq-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-contact-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-news-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-products-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-cases-module.php';
require_once DEVELOPER_STARTER_INC . '/modules/modules/class-downloads-module.php';

/**
 * 中国特性功能
 */
require_once DEVELOPER_STARTER_INC . '/china/class-china-features.php';

/**
 * SEO功能
 */
require_once DEVELOPER_STARTER_INC . '/seo/class-seo-manager.php';

/**
 * 小工具
 */
require_once DEVELOPER_STARTER_INC . '/widgets/class-widget-contact.php';
require_once DEVELOPER_STARTER_INC . '/widgets/class-widget-social.php';

/**
 * 首页创建器
 */
require_once DEVELOPER_STARTER_INC . '/core/class-homepage-creator.php';

/**
 * 初始化主题
 */
function developer_starter_init() {
    // 初始化核心类
    new Developer_Starter\Core\Theme_Setup();
    new Developer_Starter\Core\Assets();
    new Developer_Starter\Core\Message_Manager();
    new Developer_Starter\Core\SMTP_Manager();
    
    // 初始化后台管理
    if ( is_admin() ) {
        new Developer_Starter\Admin\Admin_Settings();
        new Developer_Starter\Admin\Meta_Boxes();
    }
    
    // 初始化模块管理器
    Developer_Starter\Modules\Module_Manager::get_instance();
    
    // 初始化中国特性功能
    new Developer_Starter\China\China_Features();
    
    // 初始化SEO
    new Developer_Starter\SEO\SEO_Manager();
    
    // 初始化首页创建器
    new Developer_Starter\Core\Homepage_Creator();
}
add_action( 'after_setup_theme', 'developer_starter_init', 5 );

/**
 * 主题模板标签函数
 */
require_once DEVELOPER_STARTER_INC . '/template-tags.php';

/**
 * 自定义器扩展
 */
require_once DEVELOPER_STARTER_INC . '/customizer/class-customizer.php';
