<?php
/**
 * Client article revisions 1.20 (October 2026): the eleven freight guides and
 * the FTL page, in English and Spanish. URLs, titles, headings and anchors are
 * unchanged. 1.20.1 adds the client's answers (status milestones, 53' trailers,
 * one or two drivers, contract rates, liftgate and residential delivery).
 * Explicit, backed-up update from Pelham Company; never runs on a public request.
 */
if (!defined('ABSPATH')) exit;
const PELHAM_ARTICLES_RELEASE='1.20.1';
function pelham_articles_120_routes(){return ['news/ltl-vs-ftl-california-florida-shipping','news/pallet-shipping-cost-california-to-florida','news/what-to-include-in-a-freight-quote','news/transloading-vs-cross-docking','news/container-transloading-documents-preparation','news/freight-delivery-to-forwarders-in-miami','news/how-a-freight-fuel-surcharge-works','news/planning-recurring-california-florida-freight','news/navigating-challenges-in-us-logistics','news/how-pelham-elevates-logistics-in-ca-fl','news/future-of-logistics-trends-pelham-is-embracing','services/dedicated-transport'];}
/** New article content for a route, from the plugin's seed files ('en' or 'es'). */
function pelham_articles_120_content($route,$lang){
 static $data=[];
 if(!isset($data[$lang]))$data[$lang]=json_decode(file_get_contents(__DIR__.'/../data/'.($lang==='es'?'pages-es.json':'pages.json')),true);
 foreach($data[$lang] as $entry)if(($lang==='es'?$entry['en_route']:$entry['route'])===$route)return $entry['content'];
 return null;
}
function pelham_apply_articles_120(){
 if(!current_user_can('manage_options'))return new WP_Error('forbidden','Administrator access required.');
 $updated=0;
 foreach(pelham_articles_120_routes() as $route){
  $english=pelham_i18n_english_post($route);if(!$english)continue;
  $versions=[[$english,'en']];
  $spanish=pelham_translation($english,'es');if($spanish)$versions[]=[$spanish,'es'];
  foreach($versions as [$post,$lang]){
   $content=pelham_articles_120_content($route,$lang);
   // Sites that applied 1.20.0 receive the 1.20.1 text once, with a new backup.
   if(!$content||get_post_meta($post->ID,'pelham_articles_120_done',true)===PELHAM_ARTICLES_RELEASE)continue;
   $backup=['content'=>$post->post_content,'time'=>current_time('mysql')];
   if(!add_post_meta($post->ID,'pelham_before_articles_1_20',wp_slash($backup)))return new WP_Error('backup','Could not save a backup of '.$post->post_title.'. Nothing else was changed.');
   wp_save_post_revision($post->ID);
   $result=wp_update_post(wp_slash(['ID'=>$post->ID,'post_content'=>pelham_18_seed($content)]),true);if(is_wp_error($result))return $result;
   update_post_meta($post->ID,'pelham_articles_120_done',PELHAM_ARTICLES_RELEASE);$updated++;
  }
 }
 update_option('pelham_articles_release',PELHAM_ARTICLES_RELEASE,false);
 return $updated;
}
add_action('admin_post_pelham_apply_articles_120',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_apply_articles_120');
 $result=pelham_apply_articles_120();
 set_transient('pelham_i18n_notice',is_wp_error($result)?$result->get_error_message():sprintf('Article revisions applied to %d articles and pages in English and Spanish. Previous text is saved in each article (pelham_before_articles_1_20) and in its revisions.',$result),300);
 wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;
});
add_action('admin_notices',function(){
 if(!current_user_can('manage_options')||get_option('pelham_articles_release')===PELHAM_ARTICLES_RELEASE)return;
 // Sites imported with this version already have the new articles.
 $technology=pelham_i18n_english_post('news/future-of-logistics-trends-pelham-is-embracing');$ftl=pelham_i18n_english_post('services/dedicated-transport');
 if(!$technology||!$ftl||(str_contains($technology->post_content,'Arrival at the destination warehouse')&&str_contains($ftl->post_content,'two-driver team'))){update_option('pelham_articles_release',PELHAM_ARTICLES_RELEASE,false);return;}
 echo '<div class="notice notice-warning"><p><strong>Pelham:</strong> the client’s revisions to the 11 freight guides and the full truckload page (in English and Spanish) are ready. Addresses, titles and headings stay the same. Applying them replaces those articles’ text and keeps a backup of the current text.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_apply_articles_120">';
 wp_nonce_field('pelham_apply_articles_120');submit_button('Apply article revisions (save backups)','primary','submit',false);echo '</form></div>';
});
