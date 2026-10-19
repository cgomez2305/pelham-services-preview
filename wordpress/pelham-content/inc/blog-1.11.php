<?php
if(!defined('ABSPATH'))exit;
function pelham_blog_entries(){return array_values(array_filter(json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true),fn($e)=>$e['type']==='post' && !in_array($e['route'],json_decode(file_get_contents(__DIR__.'/../data/keyword-rollout-1.12.json'),true)['new'],true)));}
function pelham_update_blog(){
 if(!current_user_can('manage_options'))return new WP_Error('forbidden','Administrator access required.');$targets=[];
 foreach(pelham_blog_entries() as $e){$posts=get_posts(['post_type'=>'post','name'=>basename($e['route']),'post_status'=>'any','numberposts'=>1]);if(!$posts)return new WP_Error('missing','Article missing: '.$e['route'].'. Restore or import missing starter content before updating.');$targets[]=[$posts[0],$e];}
 foreach($targets as [$p,$e]){
  $meta=[];foreach(['pelham_seo_title','pelham_seo_description','pelham_primary_keyword'] as $key)$meta[$key]=get_post_meta($p->ID,$key,true);
  $backup=['title'=>$p->post_title,'content'=>$p->post_content,'excerpt'=>$p->post_excerpt,'meta'=>$meta,'date'=>$p->post_date,'status'=>$p->post_status,'time'=>current_time('mysql')];
  if(!add_post_meta($p->ID,'pelham_before_blog_1_11',wp_slash($backup)))return new WP_Error('backup','Could not save article backup. Update stopped.');
  wp_save_post_revision($p->ID);$result=wp_update_post(wp_slash(['ID'=>$p->ID,'post_title'=>$e['title'],'post_excerpt'=>$e['excerpt'],'post_content'=>pelham_18_seed($e['content'])]),true);if(is_wp_error($result))return $result;
  foreach(['seo_title'=>'pelham_seo_title','description'=>'pelham_seo_description','keyword'=>'pelham_primary_keyword'] as $from=>$to)if(isset($e['seo'][$from]))update_post_meta($p->ID,$to,wp_slash($e['seo'][$from]));
 }
 update_option('pelham_blog_release','1.11.0');return true;
}
add_action('admin_notices',function(){if(!current_user_can('manage_options')||get_current_screen()->id!=='toplevel_page_pelham-company'||get_option('pelham_blog_release')==='1.11.0')return;
 echo '<div class="notice notice-info"><h2>Pelham: refresh nine existing blog articles</h2><p>Replace the nine starter articles with practical, verified-service copy and updated SEO summaries. URLs, publication dates, status, featured images and comments remain unchanged. Existing text and SEO fields are backed up per article. Custom edits within these articles will be replaced.</p><details><summary>Articles included</summary><ul>';foreach(pelham_blog_entries() as $e)echo '<li>'.esc_html($e['title']).'</li>';echo '</ul></details><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_blog_111">';wp_nonce_field('pelham_blog_111');submit_button('Update 9 blog articles (save backups)','secondary','submit',false);echo '</form></div>';
});
add_action('admin_post_pelham_blog_111',function(){if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_blog_111');if(get_option('pelham_blog_release')!=='1.11.0'){$r=pelham_update_blog();if(is_wp_error($r))wp_die(esc_html($r->get_error_message()));}wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;});
