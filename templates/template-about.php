<?php
/**
 * Template Name: 关于我们
 *
 * @package Developer_Starter
 */

get_header();

// Get settings
$show_timeline = developer_starter_get_option( 'about_show_timeline', '' );
$show_team = developer_starter_get_option( 'about_show_team', '' );
$timeline_items = developer_starter_get_option( 'timeline_items', array() );
$team_members = developer_starter_get_option( 'team_members', array() );
?>

<div class="page-header" style="background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%); padding: 100px 0 60px;">
    <div class="container">
        <h1 class="page-title" style="color: #fff; text-align: center; font-size: 2.5rem; margin: 0;">
            <?php the_title(); ?>
        </h1>
    </div>
</div>

<div class="page-content section-padding">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
            
            <?php 
            $modules = get_post_meta( get_the_ID(), '_developer_starter_modules', true );
            if ( ! empty( $modules ) && is_array( $modules ) ) :
                developer_starter_render_page_modules(); 
            else :
            ?>
                <div class="entry-content" style="max-width: 900px; margin: 0 auto;">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
            
        <?php endwhile; ?>
    </div>
</div>

<?php
// Timeline Section
if ( $show_timeline && ! empty( $timeline_items ) && is_array( $timeline_items ) ) :
?>
<section class="about-timeline section-padding" style="background: #f8fafc;">
    <div class="container">
        <div class="section-header text-center" style="margin-bottom: 50px;">
            <h2 class="section-title" style="font-size: 2rem;">发展历程</h2>
        </div>
        
        <div class="timeline" style="max-width: 800px; margin: 0 auto; position: relative;">
            <div class="timeline-line" style="position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, var(--color-primary), #7c3aed);"></div>
            
            <?php foreach ( $timeline_items as $idx => $item ) : 
                $year = isset( $item['year'] ) ? $item['year'] : '';
                $title = isset( $item['title'] ) ? $item['title'] : '';
                $desc = isset( $item['desc'] ) ? $item['desc'] : '';
                $is_left = $idx % 2 === 0;
            ?>
                <div class="timeline-item" style="display: flex; align-items: center; margin-bottom: 40px; <?php echo $is_left ? '' : 'flex-direction: row-reverse;'; ?>">
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
</section>
<?php endif; ?>

<?php
// Team Section
if ( $show_team && ! empty( $team_members ) && is_array( $team_members ) ) :
?>
<section class="about-team section-padding">
    <div class="container">
        <div class="section-header text-center" style="margin-bottom: 50px;">
            <h2 class="section-title" style="font-size: 2rem;">团队成员</h2>
        </div>
        
        <div class="team-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
            <?php foreach ( $team_members as $member ) : 
                $name = isset( $member['name'] ) ? $member['name'] : '';
                $position = isset( $member['position'] ) ? $member['position'] : '';
                $avatar = isset( $member['avatar'] ) ? $member['avatar'] : '';
                $desc = isset( $member['desc'] ) ? $member['desc'] : '';
            ?>
                <div class="team-member" style="background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.1); text-align: center;">
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
</section>
<?php endif; ?>

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

<?php get_footer(); ?>
