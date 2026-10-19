<?php
/** Explicit, backed-up editorial rollout. Never runs on a public request. */
if (!defined('ABSPATH')) exit;
function pelham_keyword_rollout_entries() {
 $plan=json_decode(file_get_contents(__DIR__.'/../data/keyword-rollout-1.12.json'),true);
 $entries=json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true);
 return array_values(array_filter($entries,fn($e)=>in_array($e['route'],array_merge($plan['existing'],$plan['new']),true)));
}
function pelham_apply_keywords_112() {
 if (!current_user_can('manage_options')) return new WP_Error('forbidden','Administrator access required.');
 if (get_option('pelham_keyword_release')==='1.12.0') return true;
 $plan=json_decode(file_get_contents(__DIR__.'/../data/keyword-rollout-1.12.json'),true);
 $targets=[];
 // Preflight every target before changing any content. Never silently replace an unrelated slug.
 foreach (pelham_keyword_rollout_entries() as $e) {
  $posts=get_posts(['post_type'=>$e['type'],'name'=>basename($e['route']),'post_status'=>'any','numberposts'=>1]);
  $p=$posts?$posts[0]:null;$new=in_array($e['route'],$plan['new'],true);
  if (!$p && !$new) return new WP_Error('missing','Missing content: '.$e['route'].'. Import missing starter content first.');
  if ($p && $new && !get_post_meta($p->ID,'pelham_keywords_112_done',true) && pelham_18_signature($p->post_content)!==pelham_18_signature(pelham_18_seed($e['content']))) return new WP_Error('conflict','An existing article uses '.$e['route'].'. Its content was preserved. Review the conflicting slug before applying this update.');
  $targets[]=[$e,$p];
 }
 $category=term_exists('pelham-insights','category');
 if (!$category) $category=wp_insert_term('Pelham Insights','category',['slug'=>'pelham-insights']);
 if (is_wp_error($category)) return $category;
 foreach ($targets as [$e,$p]) {
  if ($p && get_post_meta($p->ID,'pelham_keywords_112_done',true)) continue;
  $fields=['post_title'=>$e['title'],'post_content'=>pelham_18_seed($e['content']),'post_excerpt'=>$e['excerpt']];
  if ($p) {
   $meta=[];foreach(['pelham_seo_title','pelham_seo_description','pelham_primary_keyword'] as $key)$meta[$key]=get_post_meta($p->ID,$key,true);
   if (!metadata_exists('post',$p->ID,'pelham_before_keywords_1_12')) {
    $backup=['title'=>$p->post_title,'content'=>$p->post_content,'excerpt'=>$p->post_excerpt,'meta'=>$meta,'date'=>$p->post_date,'status'=>$p->post_status,'time'=>current_time('mysql')];
    if (!add_post_meta($p->ID,'pelham_before_keywords_1_12',wp_slash($backup),true)) return new WP_Error('backup','Could not save backup. Update stopped.');
   }
   wp_save_post_revision($p->ID);
   $id=wp_update_post(wp_slash(array_merge($fields,['ID'=>$p->ID])),true);
  } else {
   $id=wp_insert_post(wp_slash(array_merge($fields,['post_type'=>'post','post_name'=>basename($e['route']),'post_status'=>'publish','post_date'=>current_time('mysql')])),true);
   if (!is_wp_error($id)) { $terms=wp_set_object_terms($id,(int)$category['term_id'],'category');if(is_wp_error($terms))return $terms; }
  }
  if (is_wp_error($id)) return $id;
  foreach(['seo_title'=>'pelham_seo_title','description'=>'pelham_seo_description','keyword'=>'pelham_primary_keyword'] as $from=>$to) update_post_meta($id,$to,wp_slash($e['seo'][$from]));
  update_post_meta($id,'pelham_keywords_112_done',1);
 }
 update_option('pelham_keyword_release','1.12.0');return true;
}
add_action('admin_notices',function(){
 if (!current_user_can('manage_options') || get_current_screen()->id!=='toplevel_page_pelham-company') return;
 if(get_option('pelham_keyword_release')==='1.12.0') {echo '<div class="notice notice-success"><p>Keyword content update 1.12 applied: four existing pages refreshed and two guides available. No indexing or ranking result is implied.</p></div>';return;}
 echo '<div class="notice notice-info"><h2>Pelham: apply keyword research content</h2><p>Updates LTL, FTL, the LTL vs. FTL article and the quote checklist, saving their previous text and SEO fields. Custom text on these four pages will be replaced. Existing URLs, publication dates, status, authors and featured images remain unchanged. Publishes two new guides using the current date: pallet shipping costs and domestic delivery to Miami freight forwarders.</p><p>Apply any pending nine-article refresh first, then this update. Staging remains excluded from search indexing.</p><details><summary>Included content</summary><ul>';
 foreach(pelham_keyword_rollout_entries() as $e) echo '<li>'.esc_html($e['title']).'</li>';
 echo '</ul></details><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_keywords_112">';wp_nonce_field('pelham_keywords_112');submit_button('Apply keyword content (save backups)','secondary','submit',false);echo '</form></div>';
});
add_action('admin_post_pelham_keywords_112',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);
 check_admin_referer('pelham_keywords_112');
 $result=pelham_apply_keywords_112();if(is_wp_error($result))wp_die(esc_html($result->get_error_message()));
 wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;
});
// Resolve editorial news links against the site's actual permalink configuration.
// Do not change existing post URLs or force a global permalink setting.
add_filter('the_content',function($content){
 $home=rtrim(home_url(),'/');
 return preg_replace_callback('~href="(?:'.preg_quote($home,'~').')?/news/([a-z0-9-]+)/?(\?[^"#]*)?(#[^"]*)?"~',function($m){
  $posts=get_posts(['post_type'=>'post','name'=>$m[1],'post_status'=>'publish','numberposts'=>1]);
  if(!$posts)return $m[0];
  $url=get_permalink($posts[0]);
  if(!empty($m[2])){$query=[];parse_str(html_entity_decode(ltrim($m[2],'?'),ENT_QUOTES,'UTF-8'),$query);$url=add_query_arg($query,$url);}
  return 'href="'.esc_url($url.($m[3]??'')).'"';
 },$content);
},20);
