<?php
/**
 * SEO Manager Class
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

namespace Developer_Starter\SEO;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_Manager {

    public function __construct() {
        add_action( 'wp_head', array( $this, 'output_meta_tags' ), 1 );
        add_action( 'wp_head', array( $this, 'output_schema' ), 5 );
        add_action( 'wp_head', array( $this, 'output_hreflang' ), 10 );
        add_filter( 'document_title_parts', array( $this, 'filter_title' ) );
    }

    public function output_meta_tags() {
        // Skip if other SEO plugin is active
        if ( $this->has_seo_plugin() ) {
            return;
        }

        $description = $this->get_description();
        $keywords    = $this->get_keywords();
        ?>
        <meta name="description" content="<?php echo esc_attr( $description ); ?>" />
        <?php if ( ! empty( $keywords ) ) : ?>
            <meta name="keywords" content="<?php echo esc_attr( $keywords ); ?>" />
        <?php endif; ?>
        
        <!-- Open Graph -->
        <meta property="og:title" content="<?php echo esc_attr( $this->get_title() ); ?>" />
        <meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
        <meta property="og:type" content="<?php echo is_singular() ? 'article' : 'website'; ?>" />
        <meta property="og:url" content="<?php echo esc_url( $this->get_current_url() ); ?>" />
        <?php if ( has_post_thumbnail() ) : ?>
            <meta property="og:image" content="<?php echo esc_url( get_the_post_thumbnail_url( null, 'large' ) ); ?>" />
        <?php endif; ?>
        
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <?php
    }

    public function output_schema() {
        if ( $this->has_seo_plugin() ) {
            return;
        }

        $options = get_option( 'developer_starter_options', array() );
        $schema  = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => isset( $options['company_name'] ) && $options['company_name'] ? $options['company_name'] : get_bloginfo( 'name' ),
            'url'      => home_url(),
        );

        if ( ! empty( $options['company_phone'] ) ) {
            $schema['telephone'] = $options['company_phone'];
        }
        if ( ! empty( $options['company_email'] ) ) {
            $schema['email'] = $options['company_email'];
        }
        if ( ! empty( $options['company_address'] ) ) {
            $schema['address'] = array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => $options['company_address'],
            );
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    }

    public function output_hreflang() {
        // Multi-language hreflang support
        if ( function_exists( 'pll_the_languages' ) ) {
            $languages = pll_the_languages( array( 'raw' => 1 ) );
            foreach ( $languages as $lang ) {
                echo '<link rel="alternate" hreflang="' . esc_attr( $lang['slug'] ) . '" href="' . esc_url( $lang['url'] ) . '" />' . "\n";
            }
        } elseif ( function_exists( 'icl_get_languages' ) ) {
            $languages = icl_get_languages( 'skip_missing=0' );
            foreach ( $languages as $lang ) {
                echo '<link rel="alternate" hreflang="' . esc_attr( $lang['language_code'] ) . '" href="' . esc_url( $lang['url'] ) . '" />' . "\n";
            }
        }
    }

    public function filter_title( $title ) {
        if ( is_singular() ) {
            $seo_title = get_post_meta( get_the_ID(), '_developer_starter_seo_title', true );
            if ( ! empty( $seo_title ) ) {
                $title['title'] = $seo_title;
            }
        }
        return $title;
    }

    private function get_title() {
        if ( is_singular() ) {
            $seo_title = get_post_meta( get_the_ID(), '_developer_starter_seo_title', true );
            return ! empty( $seo_title ) ? $seo_title : get_the_title();
        }
        return get_bloginfo( 'name' );
    }

    private function get_description() {
        if ( is_singular() ) {
            $seo_desc = get_post_meta( get_the_ID(), '_developer_starter_seo_description', true );
            if ( ! empty( $seo_desc ) ) return $seo_desc;
            return wp_trim_words( get_the_excerpt(), 30 );
        }
        return developer_starter_get_option( 'default_description', get_bloginfo( 'description' ) );
    }

    private function get_keywords() {
        if ( is_singular() ) {
            return get_post_meta( get_the_ID(), '_developer_starter_seo_keywords', true );
        }
        return developer_starter_get_option( 'default_keywords', '' );
    }

    private function get_current_url() {
        global $wp;
        return home_url( add_query_arg( array(), $wp->request ) );
    }

    private function has_seo_plugin() {
        return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' );
    }
}
