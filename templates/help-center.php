<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'sp-help-center-page' ); ?>>
<?php wp_body_open(); ?>
<?php if ( is_page( 'support' ) ) : ?>
    <main id="main-content"><?php echo do_shortcode( '[support_search]' ); ?></main>
<?php else : ?>
    <header class="sp-support-shell"><div class="sp-help-header"><a href="<?php echo esc_url( home_url( '/support/' ) ); ?>"><strong>CampusLoop</strong><span> / Help Center</span></a></div></header>
    <main id="main-content">
    <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
    </main>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
