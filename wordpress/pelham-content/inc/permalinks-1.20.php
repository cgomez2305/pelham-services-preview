<?php
/**
 * Articles live under /news/ (the URLs used by internal links, the sitemap,
 * the legacy redirects and the Spanish routes). WordPress installs default
 * to /%postname%/, which put articles at /slug/ and sent every internal
 * article link through a 301. WordPress itself redirects /slug/ to the new
 * address, so older links keep working.
 */
if (!defined('ABSPATH')) exit;
const PELHAM_NEWS_PERMALINKS='/news/%postname%/';
/** Set /news/%postname%/ when the site uses no structure or the plain default; returns true if it changed. */
function pelham_news_permalinks(){
 $current=(string)get_option('permalink_structure');
 if(!in_array($current,['','/%postname%/'],true))return false;
 global $wp_rewrite;$wp_rewrite->set_permalink_structure(PELHAM_NEWS_PERMALINKS);
 pelham_i18n_rewrite_rules();flush_rewrite_rules(false);
 return true;
}
/** Rewrite stored links to /article-name/ as /news/article-name/ (sites imported under /%postname%/); returns pages changed. */
function pelham_news_fix_links(){
 $slugs=get_posts(['post_type'=>'post','post_status'=>'publish','numberposts'=>-1,'fields'=>'ids','pelham_all_languages'=>true]);
 $slugs=array_filter(array_map(fn($id)=>get_post_field('post_name',$id),$slugs));if(!$slugs)return 0;
 $home=untrailingslashit(home_url());$changed=0;
 $pattern='#href="('.preg_quote($home,'#').')?/('.implode('|',array_map(fn($s)=>preg_quote($s,'#'),$slugs)).')/#';
 foreach(get_posts(['post_type'=>['page','post','pelham_service','pelham_terminal'],'post_status'=>'any','numberposts'=>-1,'pelham_all_languages'=>true]) as $post){
  $content=preg_replace($pattern,'href="'.$home.'/news/$2/',$post->post_content,-1,$count);
  if(!$count)continue;
  wp_save_post_revision($post->ID);wp_update_post(wp_slash(['ID'=>$post->ID,'post_content'=>$content]));$changed++;
 }
 return $changed;
}
/** The 1.8 layout check compared links under the old structure; drop pages that now match the starter content. */
function pelham_news_recheck_18(){
 $pending=(array)get_option('pelham_18_pending',[]);if(!$pending||!function_exists('pelham_18_signature'))return;
 $seed=[];foreach(json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true) as $entry)$seed[basename($entry['route'])]=pelham_18_signature($entry['content']);
 update_option('pelham_18_pending',array_values(array_filter($pending,function($id)use($seed){$post=get_post($id);return $post&&(!isset($seed[$post->post_name])||pelham_18_signature($post->post_content)!==$seed[$post->post_name]);})));
}
// Existing sites: apply once on the first administrator visit, before the content migrations compare links.
add_action('admin_init',function(){
 if(!current_user_can('manage_options')||get_option('pelham_permalinks_release')==='1.20.5')return;
 $before=(string)get_option('permalink_structure');
 if(pelham_news_permalinks()){$fixed=pelham_news_fix_links();pelham_news_recheck_18();set_transient('pelham_permalinks_notice','Article addresses now use /news/article-name/, as the internal links do. Previous addresses ('.($before?:'plain').') redirect automatically. Internal article links updated in '.$fixed.' pages.',300);}
 update_option('pelham_permalinks_release','1.20.5',false);
},1);
add_action('admin_notices',function(){
 if(!current_user_can('manage_options'))return;
 if($notice=get_transient('pelham_permalinks_notice')){delete_transient('pelham_permalinks_notice');echo '<div class="notice notice-success is-dismissible"><p><strong>Pelham:</strong> '.esc_html($notice).'</p></div>';return;}
 $current=(string)get_option('permalink_structure');
 if($current!==PELHAM_NEWS_PERMALINKS&&get_current_screen()&&in_array(get_current_screen()->id,['dashboard','toplevel_page_pelham-company','options-permalink'],true))echo '<div class="notice notice-warning"><p><strong>Pelham:</strong> the permalink structure is <code>'.esc_html($current).'</code>. Articles and their internal links expect <code>/news/%postname%/</code> (Settings → Permalinks → Custom structure).</p></div>';
});
// Old /article-name/ addresses (sites that used /%postname%/) redirect to /news/article-name/.
add_action('template_redirect',function(){
 if(!is_404()||!in_array($_SERVER['REQUEST_METHOD']??'GET',['GET','HEAD'],true))return;
 $path=trim((string)wp_parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH),'/');
 $home=trim((string)wp_parse_url(home_url('/'),PHP_URL_PATH),'/');
 if($home!==''){if(!str_starts_with($path,$home.'/'))return;$path=substr($path,strlen($home)+1);}
 if($path===''||str_contains($path,'/')||!preg_match('/^[a-z0-9-]+$/',$path))return;
 $found=get_posts(['post_type'=>'post','name'=>$path,'post_status'=>'publish','numberposts'=>1,'pelham_all_languages'=>true]);
 if($found){wp_safe_redirect(get_permalink($found[0]),301);exit;}
},1);
