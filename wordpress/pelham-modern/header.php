<?php if (!defined('ABSPATH')) exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="site-header"><div class="shell header-inner">
<a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?> — home"><?php if (has_custom_logo()) { $id=get_theme_mod('custom_logo'); echo wp_get_attachment_image($id,'full',false,['class'=>'brand-logo']); } else { ?><img src="<?php echo esc_url(get_template_directory_uri().'/assets/pelham-logo.png'); ?>" alt="Pelham Services" width="156" height="96"><?php } ?></a>
<button class="menu-toggle" aria-expanded="false" aria-controls="site-navigation"><span class="menu-toggle-label">Menu</span><svg aria-hidden="true" viewBox="0 0 24 24" width="22" height="22"><path d="M4 8h16M4 16h16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></button>
<nav id="site-navigation" aria-label="Primary">
<div class="mobile-nav-heading"><span><?php echo esc_html__('Explore Pelham','pelham-modern'); ?></span><button class="menu-close" aria-label="<?php echo esc_attr__('Close menu','pelham-modern'); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" width="22" height="22"><path d="m6 6 12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></button></div>
<?php if (has_nav_menu('primary')) { wp_nav_menu(['theme_location'=>'primary','container'=>false,'menu_class'=>'nav-links']); } else { ?><ul class="nav-links"><?php foreach(['services'=>'Services','who-we-serve'=>'Who we serve','locations'=>'Our network','about'=>'About us','news'=>'Insights'] as $path=>$label) { ?><li><a href="<?php echo esc_url(pelham_link($path)); ?>"<?php $current=pelham_navigation_current(pelham_link($path)); if($current)echo ' aria-current="'.esc_attr($current).'"'; ?>><?php echo esc_html($label); ?></a></li><?php } ?></ul><?php } ?>
<div class="mobile-service-links" aria-label="<?php echo esc_attr__('Freight services','pelham-modern'); ?>"><?php foreach(['dedicated-transport'=>'Dedicated transport','freight-consolidation'=>'Freight consolidation','transloading'=>'Transloading'] as $path=>$label) { ?><a href="<?php echo esc_url(pelham_link('services/'.$path)); ?>"><?php echo esc_html($label); ?></a><?php } ?></div>
<div class="account-navigation"><a href="<?php echo esc_url(pelham_detail('portal','https://pelham.supply-vision.com/Portal/Login')); ?>">Login</a><a href="<?php echo esc_url(pelham_link('register')); ?>">Register</a></div>
<a class="button button-dark header-cta" href="<?php echo esc_url(pelham_link('quote')); ?>"><?php echo esc_html(pelham_detail('header_cta','Let’s move your freight')); ?> <svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18"><path d="M5 19 19 5M6 5h13v13" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
<div class="mobile-nav-contact"><a href="<?php echo esc_url(pelham_link('contact')); ?>"><?php echo esc_html__('Contact our team','pelham-modern'); ?></a><a href="<?php echo esc_url(pelham_detail('portal','https://pelham.supply-vision.com/Portal/Login')); ?>" target="_blank" rel="noopener"><?php echo esc_html__('Client portal','pelham-modern'); ?><span class="screen-reader-text"> (opens in a new tab)</span></a></div>
</nav>
</div></header>
<div class="menu-scrim" hidden aria-hidden="true"></div>
<main id="main-content">
