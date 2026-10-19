<?php get_header();while(have_posts()):the_post();$data=function_exists('pelham_keyword_data')?pelham_keyword_data(get_post()):[]; ?>
<article <?php post_class('service-story service-v3'); ?>>
<header class="shell service-opening-v3">
 <div class="service-cover-copy"><?php pelham_breadcrumbs(); ?><h1><?php the_title(); ?></h1><p class="service-cover-lead"><?php echo esc_html(get_the_excerpt()); ?></p><a class="button button-dark" href="<?php echo esc_url(add_query_arg('service',get_post_field('post_name'),pelham_link('quote'))); ?>">Discuss your shipment <span class="direction-arrow" aria-hidden="true"></span></a><a class="service-cover-secondary" href="#overview">See how this service works</a></div>
 <figure class="freight-photo service-cover-photo"><?php if(function_exists('pelham_photo_markup'))echo pelham_photo_markup($data['photo']??'none','',true,get_the_ID()); ?></figure>
</header>
<nav class="shell service-section-nav" aria-label="On this service page"><a href="#overview">Overview</a><a href="#process">How it works</a><a href="#prepare">Plan your shipment</a><a href="#questions">Questions</a></nav>
<div class="shell service-body-v3"><div class="entry-content service-content-v3"><?php the_content(); ?></div></div>
<?php if(shortcode_exists('pelham_audiences'))echo do_shortcode('[pelham_audiences variant="service" service="'.esc_attr(get_post_field('post_name')).'"]'); ?>
<section class="shell service-related service-related-v3"><div class="section-heading"><h2>Find the right fit<br>for your freight.</h2><p>Compare the other ways we can help you move.</p></div><?php echo do_shortcode('[pelham_services]'); ?></section>
</article>
<?php endwhile;get_footer(); ?>
