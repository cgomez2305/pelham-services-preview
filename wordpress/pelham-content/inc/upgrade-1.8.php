<?php
if(!defined('ABSPATH'))exit;
// Older imports created services before compact display labels were available.
// Run independently of the layout migration, including already upgraded sites.
function pelham_backfill_service_labels(){
 if(!current_user_can('manage_options')||get_option('pelham_service_labels_release')==='1.8.1')return;
 $entries=json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true);
 if(!is_array($entries))return;
 foreach($entries as $entry){
  if($entry['type']!=='pelham_service'||empty($entry['service_label']))continue;
  $posts=get_posts(['post_type'=>'pelham_service','name'=>basename($entry['route']),'post_status'=>'any','numberposts'=>1]);
  if(!$posts)continue;
  $id=$posts[0]->ID;
  if(trim((string)get_post_meta($id,'pelham_service_label',true))==='')update_post_meta($id,'pelham_service_label',sanitize_text_field($entry['service_label']));
 }
 update_option('pelham_service_labels_release','1.8.1');
}
add_action('admin_init','pelham_backfill_service_labels',20);
function pelham_18_seed($content){return preg_replace_callback('/href="(\/[^"#]*)"/',fn($m)=>'href="'.esc_url(home_url($m[1])).'"',$content);}
function pelham_18_signature($content){
 $value=pelham_known_home_signature($content);static $aliases;
 if(!isset($aliases)){$aliases=[];foreach(json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true) as $e){$posts=get_posts(['post_type'=>$e['type'],'name'=>basename($e['route']),'numberposts'=>1]);if($posts){$path=wp_parse_url(get_permalink($posts[0]),PHP_URL_PATH);if($path&&$path!=='/'.$e['route'].'/')$aliases[$path]='/'.$e['route'].'/';}}}
 return strtr($value,$aliases);
}
function pelham_18_migrate($force=false){
 if(!current_user_can('manage_options'))return;
 $home=get_post((int)get_option('page_on_front'));if($home&&pelham_18_signature($home->post_content)===pelham_18_signature(file_get_contents(__DIR__.'/../data/home-1.7.html'))){wp_save_post_revision($home->ID);add_post_meta($home->ID,'pelham_before_1_8',$home->post_content);wp_update_post(['ID'=>$home->ID,'post_content'=>pelham_18_seed(file_get_contents(__DIR__.'/../data/home.html'))]);}
 $before=json_decode(file_get_contents(__DIR__.'/../data/pages-1.7.json'),true);$after=json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true);$pending=[];
 pelham_import_content();
 foreach($after as $entry){if(!in_array($entry['route'],['contact','register','services/dedicated-transport','services/freight-consolidation','services/transloading'],true))continue;
  $posts=get_posts(['post_type'=>$entry['type'],'name'=>basename($entry['route']),'post_status'=>'any','numberposts'=>1]);if(!$posts)continue;$post=$posts[0];
  if(pelham_18_signature($post->post_content)===pelham_18_signature($entry['content']))continue;
  $old=array_values(array_filter($before,fn($e)=>$e['route']===$entry['route']));
  $known=$old&&pelham_18_signature($post->post_content)===pelham_18_signature($old[0]['content']);
  if(!$known&&!$force){$pending[]=$post->ID;continue;}
  wp_save_post_revision($post->ID);add_post_meta($post->ID,'pelham_before_1_8',['content'=>$post->post_content,'title'=>$post->post_title,'status'=>$post->post_status,'time'=>current_time('mysql')]);
  wp_update_post(['ID'=>$post->ID,'post_content'=>pelham_18_seed($entry['content']),'post_title'=>$entry['title']]);
 }
 update_option('pelham_18_pending',$pending);update_option('pelham_layout_release','1.8.0');
}
add_action('admin_init',function(){if(current_user_can('manage_options')&&get_option('pelham_layout_release')!=='1.8.0')pelham_18_migrate();});
add_action('admin_notices',function(){if(!current_user_can('manage_options')||!get_option('pelham_18_pending'))return;echo '<div class="notice notice-info"><p>Pelham 1.8: custom service/contact/register content was preserved. Apply the restructured starter pages with backups, or adapt your existing copy in the editor.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_layout_18">';wp_nonce_field('pelham_layout_18');submit_button('Apply restructured pages (save backups)','secondary','submit',false);echo '</form></div>';});
add_action('admin_post_pelham_layout_18',function(){if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_layout_18');pelham_18_migrate(true);wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;});
