<?php
/** Explicit, backed-up editorial rollout. Never runs on a public request. */
if (!defined('ABSPATH')) exit;
function pelham_seo_audit_entries() {
 $plan=json_decode(file_get_contents(__DIR__.'/../data/seo-audit-1.14.json'),true);
 $entries=json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true);
 $seo=json_decode(file_get_contents(__DIR__.'/../data/keyword-map.json'),true);
 array_unshift($entries,['route'=>'home','type'=>'page','title'=>'Home','content'=>file_get_contents(__DIR__.'/../data/home.html'),'excerpt'=>$seo['home']['description'],'seo'=>$seo['home']]);
 return array_values(array_filter($entries,fn($e)=>in_array($e['route'],$plan['existing'],true)));
}
function pelham_apply_seo_audit_114() {
 if (!current_user_can('manage_options')) return new WP_Error('forbidden','Administrator access required.');
 if (get_option('pelham_seo_audit_release')==='1.14.0') return true;
 $plan=json_decode(file_get_contents(__DIR__.'/../data/seo-audit-1.14.json'),true);
 $targets=[];
 // Preflight every target before changing any content. Never silently replace an unrelated slug.
 foreach (pelham_seo_audit_entries() as $e) {
  $posts=$e['route']==='home'?array_values(array_filter([get_post((int)get_option('page_on_front'))])):get_posts(['post_type'=>$e['type'],'name'=>basename($e['route']),'post_status'=>'any','numberposts'=>1]);
  $p=$posts?$posts[0]:null;$new=in_array($e['route'],$plan['create_if_missing'],true);
  if (!$p && !$new) return new WP_Error('missing','Missing content: '.$e['route'].'. Import missing starter content first.');
  if ($p && $new && !get_post_meta($p->ID,'pelham_keywords_112_done',true) && !get_post_meta($p->ID,'pelham_seo_audit_114_done',true) && pelham_18_signature($p->post_content)!==pelham_18_signature(pelham_18_seed($e['content']))) return new WP_Error('conflict','An existing article uses '.$e['route'].'. Its content was preserved. Review the conflicting slug before applying this update.');
  $targets[]=[$e,$p];
 }
 $category=term_exists('pelham-insights','category');
 if (!$category) $category=wp_insert_term('Pelham Insights','category',['slug'=>'pelham-insights']);
 if (is_wp_error($category)) return $category;
 foreach ($targets as [$e,$p]) {
  if ($p && get_post_meta($p->ID,'pelham_seo_audit_114_done',true)) continue;
  $fields=['post_title'=>$e['title'],'post_content'=>pelham_18_seed($e['content']),'post_excerpt'=>$e['excerpt']];
  if ($p) {
   $meta=[];foreach(['pelham_seo_title','pelham_seo_description','pelham_primary_keyword'] as $key)$meta[$key]=get_post_meta($p->ID,$key,true);
   if (!metadata_exists('post',$p->ID,'pelham_before_seo_audit_1_14')) {
    $backup=['title'=>$p->post_title,'content'=>$p->post_content,'excerpt'=>$p->post_excerpt,'meta'=>$meta,'date'=>$p->post_date,'status'=>$p->post_status,'time'=>current_time('mysql')];
    if (!add_post_meta($p->ID,'pelham_before_seo_audit_1_14',wp_slash($backup),true)) return new WP_Error('backup','Could not save backup. Update stopped.');
   }
   wp_save_post_revision($p->ID);
   $id=wp_update_post(wp_slash(array_merge($fields,['ID'=>$p->ID])),true);
  } else {
   $id=wp_insert_post(wp_slash(array_merge($fields,['post_type'=>'post','post_name'=>basename($e['route']),'post_status'=>'publish','post_date'=>current_time('mysql')])),true);
   if (!is_wp_error($id)) { $terms=wp_set_object_terms($id,(int)$category['term_id'],'category');if(is_wp_error($terms))return $terms; }
  }
  if (is_wp_error($id)) return $id;
  foreach(['seo_title'=>'pelham_seo_title','description'=>'pelham_seo_description','keyword'=>'pelham_primary_keyword'] as $from=>$to) update_post_meta($id,$to,wp_slash($e['seo'][$from]));
  update_post_meta($id,'pelham_seo_audit_114_done',1);
 }
 update_option('pelham_seo_audit_release','1.14.0');return true;
}
add_action('admin_notices',function(){
 if(!current_user_can('manage_options')||get_current_screen()->id!=='toplevel_page_pelham-company')return;
 if(get_option('pelham_seo_audit_release')==='1.14.0'){echo '<div class="notice notice-success"><p>SEO intent and headings audit 1.14 applied. Install Pelham Modern 1.18 for the corrected service H1 rendering.</p></div>';return;}
 echo '<div class="notice notice-info"><h2>Pelham: SEO intent and headings audit</h2><p>Updates 13 existing pages, including the homepage, service titles, quote checklist and recurring-freight guide. Saves previous text and SEO fields; custom text on these pages will be replaced. URLs, publication dates, status and media remain unchanged. Complete any pending keyword coverage update first. Install theme 1.18 to render the service titles as H1 headings.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_seo_audit_114">';wp_nonce_field('pelham_seo_audit_114');submit_button('Apply SEO intent and headings (save backups)','secondary','submit',false);echo '</form></div>';
});
add_action('admin_post_pelham_seo_audit_114',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);
 check_admin_referer('pelham_seo_audit_114');
 $result=pelham_apply_seo_audit_114();if(is_wp_error($result))wp_die(esc_html($result->get_error_message()));
 wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;
});
