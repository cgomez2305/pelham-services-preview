<?php get_header(); ?>
<?php if(have_posts()):while(have_posts()):the_post();$data=function_exists('pelham_keyword_data')?pelham_keyword_data(get_post()):[];?>
<article <?php post_class('page-story'); ?>>
<header class="page-masthead shell"><div><?php pelham_breadcrumbs(); ?><h1><?php the_title(); ?></h1><?php if(get_post_type()==='post'){ ?><p class="article-date"><?php echo esc_html(pelham_date()); ?></p><?php } ?></div>
<?php if(!pelham_is_page(['services','news','contact','quote','register','privacy-notice','cookie-policy','terms-and-conditions'])&&(($data['photo']??'none')!=='none'||has_post_thumbnail())&&function_exists('pelham_photo_markup')){?><figure class="freight-photo page-masthead-photo"><?php echo pelham_photo_markup($data['photo']??'truck','',true,get_the_ID()); ?><div class="photo-fallback" aria-hidden="true"><span>CA</span><i></i><span>FL</span></div></figure><?php } ?></header>
<div class="shell article-page"><div class="entry-content legacy-content"><?php the_content(); ?></div></div>
</article>
<?php endwhile;the_posts_pagination();else: ?><div class="shell article-page"><h1><?php echo esc_html(pelham_t('Nothing here yet.')); ?></h1><p><a href="<?php echo esc_url(pelham_home_link()); ?>"><?php echo esc_html(pelham_t('Return home')); ?></a></p></div><?php endif; ?>
<?php get_footer(); ?>
