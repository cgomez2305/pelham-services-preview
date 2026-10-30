<?php get_header(); while(have_posts()):the_post(); ?>
<article <?php post_class('page-story news-hub'); ?>>
<header class="page-masthead shell"><?php pelham_breadcrumbs(); ?><h1><?php the_title(); ?></h1></header>
<div class="shell article-page"><div class="entry-content legacy-content"><?php the_content(); ?></div></div>
</article>
<?php endwhile;get_footer(); ?>
