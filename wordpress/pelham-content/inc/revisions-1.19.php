<?php
/**
 * Client revisions 1.19 (October 2026): home, services, who we serve, about,
 * the LTL page, two articles and the Orlando facility, in English and Spanish.
 * Explicit, backed-up update from Pelham Company; never runs on a public request.
 */
if (!defined('ABSPATH')) exit;
const PELHAM_REVISIONS_RELEASE='1.19.0';
function pelham_revisions_routes(){return ['home','services','who-we-serve','about','services/freight-consolidation','news/ltl-vs-ftl-california-florida-shipping','news/pallet-shipping-cost-california-to-florida','locations/orlando-fl'];}
/** New English content and title for a route, from the plugin's seed files. */
function pelham_revisions_english($route){
 if($route==='home')return ['content'=>file_get_contents(__DIR__.'/../data/home.html')];
 if($route==='who-we-serve')return ['content'=>file_get_contents(__DIR__.'/../data/who-we-serve.html')];
 foreach(json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true) as $entry)if($entry['route']===$route)return ['content'=>$entry['content'],'title'=>$route==='locations/orlando-fl'?$entry['title']:null];
 return null;
}
function pelham_revisions_spanish($route){
 foreach(json_decode(file_get_contents(__DIR__.'/../data/pages-es.json'),true) as $entry)if($entry['en_route']===$route)return ['content'=>$entry['content'],'title'=>in_array($route,['locations/orlando-fl','services','who-we-serve'],true)?$entry['title']:null];
 return null;
}
function pelham_apply_revisions_119(){
 if(!current_user_can('manage_options'))return new WP_Error('forbidden','Administrator access required.');
 $updated=0;
 foreach(pelham_revisions_routes() as $route){
  $english=pelham_i18n_english_post($route);if(!$english)continue;
  $versions=[[$english,pelham_revisions_english($route)]];
  $spanish=pelham_translation($english,'es');if($spanish)$versions[]=[$spanish,pelham_revisions_spanish($route)];
  foreach($versions as [$post,$data]){
   if(!$data||get_post_meta($post->ID,'pelham_revisions_119_done',true))continue;
   $backup=['title'=>$post->post_title,'content'=>$post->post_content,'phone'=>get_post_meta($post->ID,'pelham_phone',true),'time'=>current_time('mysql')];
   if(!add_post_meta($post->ID,'pelham_before_revisions_1_19',wp_slash($backup)))return new WP_Error('backup','Could not save a backup of '.$post->post_title.'. Nothing else was changed.');
   wp_save_post_revision($post->ID);
   $fields=['ID'=>$post->ID,'post_content'=>pelham_18_seed($data['content'])];if(!empty($data['title']))$fields['post_title']=$data['title'];
   $result=wp_update_post(wp_slash($fields),true);if(is_wp_error($result))return $result;
   if($route==='locations/orlando-fl'){
    update_post_meta($post->ID,'pelham_phone','323-201-4995');
    // Imported SEO fields override the keyword map; refresh them only if they still carry the old name.
    $seo=json_decode(file_get_contents(__DIR__.'/../data/keyword-map.json'),true)['orlando-fl'];
    foreach(['pelham_seo_title'=>'seo_title','pelham_seo_description'=>'description'] as $meta=>$field)if(str_contains((string)get_post_meta($post->ID,$meta,true),'Land Stand'))update_post_meta($post->ID,$meta,wp_slash($seo[$field]));
   }
   update_post_meta($post->ID,'pelham_revisions_119_done',PELHAM_REVISIONS_RELEASE);$updated++;
  }
 }
 update_option('pelham_revisions_release',PELHAM_REVISIONS_RELEASE,false);
 return $updated;
}
add_action('admin_post_pelham_apply_revisions_119',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_apply_revisions_119');
 $result=pelham_apply_revisions_119();
 set_transient('pelham_i18n_notice',is_wp_error($result)?$result->get_error_message():sprintf('Client revisions applied to %d pages in English and Spanish. Previous text is saved in each page (pelham_before_revisions_1_19) and in its revisions.',$result),300);
 wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;
});
add_action('admin_notices',function(){
 if(!current_user_can('manage_options')||get_option('pelham_revisions_release')===PELHAM_REVISIONS_RELEASE)return;
 // Sites imported with this version already have the new text.
 $about=get_page_by_path('about');if($about&&str_contains($about->post_content,'Service boundary:')){update_option('pelham_revisions_release',PELHAM_REVISIONS_RELEASE,false);return;}
 echo '<div class="notice notice-warning"><p><strong>Pelham:</strong> the client’s October revisions (home, services, who we serve, about, LTL, two articles and Orlando, in English and Spanish) are ready. Applying them replaces those pages’ text and keeps a backup of the current text.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_apply_revisions_119">';
 wp_nonce_field('pelham_apply_revisions_119');submit_button('Apply client revisions (save backups)','primary','submit',false);echo '</form></div>';
});
