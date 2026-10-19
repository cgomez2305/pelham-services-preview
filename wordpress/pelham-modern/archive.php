<?php get_header(); ?>
<section class="archive-hub news-hub shell">
 <header class="page-masthead"><?php pelham_breadcrumbs(); ?><h1><?php echo esc_html(wp_strip_all_tags(get_the_archive_title())); ?></h1></header>
 <?php if(have_posts()): ?><div class="news-grid">
 <?php while(have_posts()):the_post(); ?><article class="news-card"><a href="<?php the_permalink(); ?>"><p class="eyebrow"><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('M j, Y')); ?></time></p><h2><?php the_title(); ?></h2><p><?php echo esc_html(pelham_seo_description(get_post())); ?></p><span class="text-link">Read the story <span class="direction-arrow" aria-hidden="true"></span></span></a></article><?php endwhile; ?>
 </div><?php the_posts_pagination();else: ?><p>No articles are available here yet.</p><?php endif; ?>
</section>
<?php get_footer(); ?>
