<?php get_header();while(have_posts()):the_post();
$data=function_exists('pelham_keyword_data')?pelham_keyword_data(get_post()):[];
$content=apply_filters('the_content',get_the_content());$toc=[];
$content=preg_replace_callback('/<h2([^>]*)>(.*?)<\/h2>/s',function($m)use(&$toc){$label=wp_strip_all_tags($m[2]);if(!$label)return $m[0];$id='reading-'.(count($toc)+1);$toc[]=[$id,$label];return '<h2'.$m[1].' id="'.$id.'">'.$m[2].'</h2>';},$content);
?>
<article <?php post_class('page-story reading-story'); ?>>
<header class="page-masthead shell"><div><nav class="story-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(pelham_link('news')); ?>">All freight insights</a></nav><h1><?php the_title(); ?></h1><p class="article-date">Published <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time><?php if(get_the_modified_date('Y-m-d')!==get_the_date('Y-m-d')){ ?> · Updated <time datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>"><?php echo esc_html(get_the_modified_date()); ?></time><?php } ?></p><p class="article-date">Published by <a href="<?php echo esc_url(pelham_link('about')); ?>"><?php echo esc_html(get_bloginfo('name')); ?></a></p></div><?php if((($data['photo']??'none')!=='none'||has_post_thumbnail())){ ?><figure class="freight-photo page-masthead-photo"><?php if(function_exists('pelham_photo_markup'))echo pelham_photo_markup($data['photo']??'truck','',true,get_the_ID()); ?></figure><?php } ?></header>
<div class="shell reading-layout"><nav class="reading-toc" aria-label="In this article"><p>In this article</p><?php foreach($toc as [$id,$label]){ ?><a href="#<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></a><?php } ?></nav><div class="entry-content legacy-content"><?php echo $content; ?></div></div>
</article>
<?php endwhile;get_footer(); ?>
