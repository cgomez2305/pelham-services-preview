<?php
/**
 * Creates the Spanish version of the site from data/pages-es.json.
 * Runs once automatically for an administrator, and again from
 * Pelham Company → "Create missing Spanish pages". Existing Spanish
 * content is never overwritten; English content is never changed.
 */
if (!defined('ABSPATH')) exit;

/** Existing English post for an English route ('home', 'about', 'news/slug'). */
function pelham_i18n_english_post($route){
 if($route==='home'){$id=(int)get_option('page_on_front');return $id?get_post($id):null;}
 $types=['news/'=>'post','services/'=>'pelham_service','locations/'=>'pelham_terminal'];
 foreach($types as $prefix=>$type)if(str_starts_with($route,$prefix)){$found=get_posts(['post_type'=>$type,'name'=>basename($route),'numberposts'=>1,'post_status'=>'any','pelham_all_languages'=>true]);return $found[0]??null;}
 $page=get_page_by_path($route);return $page&&pelham_post_lang($page)==='en'?$page:null;
}
function pelham_i18n_link_pair($en_id,$es_id){
 update_post_meta($es_id,'pelham_lang','es');
 update_post_meta($es_id,'pelham_translation',$en_id);
 update_post_meta($en_id,'pelham_translation',$es_id);
}
function pelham_create_spanish_site(){
 if(!current_user_can('manage_options'))return new WP_Error('forbidden','Administrator access required.');
 $entries=json_decode(file_get_contents(__DIR__.'/../data/pages-es.json'),true);
 if(!is_array($entries))return new WP_Error('data','Spanish content file is missing or invalid.');
 $ids=get_option('pelham_i18n_ids',[]);$created=[];
 $category=term_exists('pelham-insights','category');
 foreach($entries as $entry){
  $en_route=$entry['en_route'];
  if(!empty($ids[$en_route])&&get_post($ids[$en_route]))continue;
  if(!empty($entry['requires_shortcode'])&&!shortcode_exists($entry['requires_shortcode']))continue;
  $english=pelham_i18n_english_post($en_route);
  // Reuse a Spanish post already linked to this English post (re-runs, restores).
  if($english&&($linked=pelham_translation($english,'es'))){$ids[$en_route]=$linked->ID;continue;}
  $parent=0;
  if($entry['type']==='page'&&$en_route!=='home'){$parent=(int)($ids['home']??0);if(!$parent)return new WP_Error('order','The Spanish home page must be created first.');}
  $slug=basename($entry['route']);
  $clash=$entry['type']==='page'?get_page_by_path($entry['route']):get_posts(['post_type'=>$entry['type'],'name'=>$slug,'numberposts'=>1,'post_status'=>'any','pelham_all_languages'=>true]);
  if($clash){$clash=is_array($clash)?$clash[0]:$clash;if(pelham_post_lang($clash)==='es'){$ids[$en_route]=$clash->ID;continue;}return new WP_Error('conflict','The address /'.$entry['route'].'/ is already used by other content. Nothing was overwritten.');}
  $id=wp_insert_post(wp_slash(['post_type'=>$entry['type'],'post_name'=>$slug,'post_parent'=>$parent,'post_title'=>$entry['title'],'post_content'=>$entry['content'],'post_excerpt'=>$entry['excerpt']??'','post_status'=>'publish','post_date'=>$english?$english->post_date:current_time('mysql'),'menu_order'=>$english?$english->menu_order:0,'comment_status'=>'closed']),true);
  if(is_wp_error($id))return $id;
  update_post_meta($id,'pelham_lang','es');
  if($english){
   pelham_i18n_link_pair($english->ID,$id);
   $thumb=get_post_thumbnail_id($english);if($thumb)set_post_thumbnail($id,$thumb);
   if($entry['type']==='pelham_terminal')foreach(['pelham_address','pelham_phone','pelham_hours'] as $key){$value=get_post_meta($english->ID,$key,true);if($value!=='')update_post_meta($id,$key,wp_slash($value));}
   $template=get_post_meta($english->ID,'_wp_page_template',true);if($template)update_post_meta($id,'_wp_page_template',$template);
  }
  if(!empty($entry['service_label']))update_post_meta($id,'pelham_service_label',wp_slash($entry['service_label']));
  if($entry['type']==='post'&&$category)wp_set_object_terms($id,(int)$category['term_id'],'category');
  if($en_route==='home')update_option('pelham_es_home',$id,false);
  $ids[$en_route]=$id;$created[$id]=$entry['content'];
  update_option('pelham_i18n_ids',$ids,false);
 }
 // Point internal /es/… links to the real permalinks.
 $routes=array_flip(pelham_i18n_routes());
 foreach($created as $id=>$content){
  $content=preg_replace_callback('/href="(\/es\/[^"#?]*)([^"]*)"/',function($m)use($routes,$ids){
   $route=$routes[trim($m[1],'/')]??null;$url=$route&&!empty($ids[$route])?get_permalink($ids[$route]):home_url($m[1]);
   return 'href="'.esc_url($url).$m[2].'"';
  },$content);
  wp_update_post(wp_slash(['ID'=>$id,'post_content'=>$content]));
 }
 // Link Spanish items whose English page appeared later (e.g. Who we serve).
 foreach($ids as $en_route=>$es_id){
  if(get_post_meta($es_id,'pelham_translation',true))continue;
  $english=pelham_i18n_english_post($en_route);if($english&&!pelham_translation($english,'es'))pelham_i18n_link_pair($english->ID,$es_id);
 }
 update_option('pelham_i18n_ids',$ids,false);
 update_option('pelham_i18n_release','1.18.0',false);
 flush_rewrite_rules(false);
 return count($created);
}
// First administrator visit after installing: build the Spanish site once,
// after the other setup steps (priority 10) have created their English pages.
add_action('admin_init',function(){
 if(!current_user_can('manage_options')||!get_option('page_on_front'))return;
 // Also runs again when Pelham Applications is installed later (Spanish claims page).
 $ids=get_option('pelham_i18n_ids',[]);
 if(get_option('pelham_i18n_release')&&!(shortcode_exists('pelham_claim_form')&&empty($ids['claims'])))return;
 $result=pelham_create_spanish_site();
 set_transient('pelham_i18n_notice',is_wp_error($result)?$result->get_error_message():sprintf('Spanish site created: %d pages, articles, services and facilities under /es/.',$result),300);
},20);
add_action('admin_notices',function(){$notice=get_transient('pelham_i18n_notice');if(!$notice)return;delete_transient('pelham_i18n_notice');echo '<div class="notice notice-info is-dismissible"><p>'.esc_html($notice).'</p></div>';});
add_action('admin_post_pelham_create_spanish',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_create_spanish');
 $result=pelham_create_spanish_site();
 set_transient('pelham_i18n_notice',is_wp_error($result)?$result->get_error_message():sprintf('Spanish site checked: %d new items created.',$result),300);
 wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;
});
add_action('pelham_company_tools',function(){?><hr><h2>Spanish version (/es/)</h2><p>Spanish pages, articles, services and facilities are normal WordPress content marked as Spanish and linked to their English page. Edit them like any page. This button only creates the ones that are missing; it never overwrites existing content.</p><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="pelham_create_spanish"><?php wp_nonce_field('pelham_create_spanish');submit_button('Create missing Spanish pages','secondary'); ?></form><?php });

// Editors see the language and the linked translation of every item.
foreach(['page','post','pelham_service','pelham_terminal'] as $pelham_type){
 add_filter('manage_'.$pelham_type.'_posts_columns',function($columns){$columns['pelham_lang']='Language';return $columns;});
 add_action('manage_'.$pelham_type.'_posts_custom_column',function($column,$id){if($column!=='pelham_lang')return;$lang=pelham_post_lang($id);$other=(int)get_post_meta($id,'pelham_translation',true);echo $lang==='es'?'Español':'English';if($other&&get_post($other))echo ' · <a href="'.esc_url(get_edit_post_link($other)).'">'.($lang==='es'?'English':'Español').'</a>';},10,2);
}
