<?php get_header();while(have_posts()):the_post(); ?>
<article <?php post_class('register-page-v3 shell'); ?>><header><h1><?php the_title(); ?></h1></header><div class="entry-content"><?php the_content(); if(!has_shortcode(get_the_content(),'pelham_registration_form')&&shortcode_exists('pelham_registration_form'))echo do_shortcode('[pelham_registration_form]'); ?></div></article>
<?php endwhile;get_footer(); ?>
