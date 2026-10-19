<?php get_header(); ?>
<?php if(have_posts()):while(have_posts()):the_post();$data=function_exists('pelham_keyword_data')?pelham_keyword_data(get_post()):[];?>
<article <?php post_class('page-story'); ?>>
<header class="page-masthead shell"><div><nav class="story-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><?php if(get_post_type()==='post'){?><span aria-hidden="true">/</span><a href="<?php echo esc_url(pelham_link('news')); ?>">Freight insights</a><?php } ?></nav><h1><?php the_title(); ?></h1><?php if(get_post_type()==='post'){ ?><p class="article-date"><?php echo esc_html(get_the_date()); ?></p><?php } ?></div>
<?php if(!is_page(['services','news','contact','quote','register','privacy-notice','cookie-policy','terms-and-conditions'])&&(($data['photo']??'none')!=='none'||has_post_thumbnail())&&function_exists('pelham_photo_markup')){?><figure class="freight-photo page-masthead-photo"><?php echo pelham_photo_markup($data['photo']??'truck','',true,get_the_ID()); ?><div class="photo-fallback" aria-hidden="true"><span>CA</span><i></i><span>FL</span></div><?php if(get_post_type()==='pelham_terminal'){ ?><figcaption>Image from the original Pelham website</figcaption><?php } ?></figure><?php } ?></header>
<div class="shell article-page"><div class="entry-content legacy-content"><?php the_content(); ?></div></div>
</article>
<?php endwhile;the_posts_pagination();else: ?><div class="shell article-page"><h1>Nothing here yet.</h1><p><a href="<?php echo esc_url(home_url('/')); ?>">Return home</a></p></div><?php endif; ?>
<?php get_footer(); ?>
