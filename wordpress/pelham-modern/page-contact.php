<?php get_header();while(have_posts()):the_post(); ?>
<article <?php post_class('contact-page-v3 shell'); ?>>
<?php if(!str_contains(get_the_content(),'contact-v3')){ ?><h1><?php the_title(); ?></h1><?php } ?>
<div class="entry-content"><?php the_content(); ?></div>
</article>
<?php endwhile;get_footer(); ?>
