<?php
if(!defined('ABSPATH'))exit;
function pelham_ltl_levels(){return ['expedited'=>['Expedited',3,'South Florida: Homestead to West Palm Beach'],'regular'=>['Regular',7,'All Florida destinations'],'economy'=>['Economy',10,'All Florida destinations · Lowest-cost LTL option']];}
add_action('wp_enqueue_scripts',function(){wp_enqueue_style('pelham-offer',plugins_url('../assets/offer.css',__FILE__),[],'1.21.6');wp_enqueue_script('pelham-offer',plugins_url('../assets/offer.js',__FILE__),[],'1.21.6',true);});
add_shortcode('pelham_ltl_levels',function(){ob_start();if(pelham_is_home())echo '<div class="shell pelham-level-shell">';?><section class="pelham-ltl-levels" aria-label="Compare LTL transit levels"><div class="section-heading"><h2>Your pallets. Your pace.</h2><p>Choose your service tier.</p></div><div class="pelham-level-grid"><?php foreach(pelham_ltl_levels() as $key=>$level){?><article class="pelham-level"><h3><?php echo esc_html($level[0]);?></h3><p class="pelham-level-days"><?php echo (int)$level[1];?><span> calendar days</span></p><p><?php echo esc_html($level[2]);?></p><a class="text-link" href="<?php echo esc_url(add_query_arg(['service'=>'freight-consolidation','ltl_level'=>$key],pelham_link('quote')));?>">Request <?php echo esc_html($level[0]);?> <span class="direction-arrow" aria-hidden="true"></span></a></article><?php }?></div><p class="pelham-transit-note">Estimated transit times from cargo pickup, counted in calendar days. Not guaranteed. Expedited serves only South Florida from Homestead to West Palm Beach; Regular and Economy serve all Florida destinations.</p></section><?php if(pelham_is_home())echo '</div>';return ob_get_clean();});
add_shortcode('pelham_domestic_network',function(){return '<div class="pelham-network"><p>LOS ANGELES <span aria-hidden="true">→</span> FLORIDA</p><p>Facilities in Gardena, Orlando and Miami. Domestic delivery to businesses and transport companies.</p><a class="text-link" href="'.esc_url(pelham_link('locations')).'">Explore our facilities</a></div>';});
function pelham_ltl_choice(){ob_start();?><fieldset class="pelham-ltl-choice"><legend>LTL transit preference (optional)</legend><label>Service level<select name="ltl_level" aria-describedby="ltl-coverage"><option value="">Help me choose</option><?php foreach(pelham_ltl_levels() as $key=>$v){?><option value="<?php echo esc_attr($key);?>"><?php echo esc_html($v[0].' — '.$v[1].' calendar days — '.$v[2]);?></option><?php }?></select></label><p id="ltl-coverage" aria-live="polite">For LTL only. Estimates start at pickup and are not guaranteed. Expedited: Homestead to West Palm Beach only. Regular and Economy: all Florida destinations.</p></fieldset><?php return ob_get_clean();}
function pelham_offer_entries(){
 $entries=json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true);$seo=json_decode(file_get_contents(__DIR__.'/../data/keyword-map.json'),true);
 $routes=['services','services/freight-consolidation','services/dedicated-transport','services/transloading','about','locations','quote','contact','locations/miami-fl','locations/gardena-ca'];
 $entries=array_values(array_filter($entries,fn($e)=>in_array($e['route'],$routes,true)));
 array_unshift($entries,['route'=>'home','type'=>'page','title'=>'Home','content'=>file_get_contents(__DIR__.'/../data/home.html'),'seo'=>$seo['home']]);return $entries;
}
function pelham_apply_offer(){
 if(!current_user_can('manage_options'))return new WP_Error('forbidden','Administrator access required.');
 foreach(pelham_offer_entries() as $e){
  $posts=$e['route']==='home'?array_filter([get_post((int)get_option('page_on_front'))]):get_posts(['post_type'=>$e['type'],'name'=>basename($e['route']),'post_status'=>'any','numberposts'=>1]);
  if(!$posts)return new WP_Error('missing','Missing page: '.$e['route'].'. Import missing starter content first.');
 }
 foreach(pelham_offer_entries() as $e){
  $post=$e['route']==='home'?get_post((int)get_option('page_on_front')):get_posts(['post_type'=>$e['type'],'name'=>basename($e['route']),'post_status'=>'any','numberposts'=>1])[0];
  $meta=[];foreach(['pelham_seo_title','pelham_seo_description','pelham_primary_keyword','pelham_service_label'] as $key)$meta[$key]=get_post_meta($post->ID,$key,true);
  $backup=['title'=>$post->post_title,'content'=>$post->post_content,'excerpt'=>$post->post_excerpt,'menu_order'=>$post->menu_order,'meta'=>$meta,'time'=>current_time('mysql')];
  if(!add_post_meta($post->ID,'pelham_before_offer_1_9',wp_slash($backup)))return new WP_Error('backup','Could not save backup. Update stopped.');
  wp_save_post_revision($post->ID);
  $result=wp_update_post(wp_slash(['ID'=>$post->ID,'post_title'=>$e['title'],'post_content'=>pelham_18_seed($e['content']),'post_excerpt'=>$e['excerpt']??$post->post_excerpt,'menu_order'=>$e['menu_order']??$post->menu_order]),true);
  if(is_wp_error($result))return $result;
  foreach(['seo_title'=>'pelham_seo_title','description'=>'pelham_seo_description','keyword'=>'pelham_primary_keyword'] as $from=>$to)if(isset($e['seo'][$from]))update_post_meta($post->ID,$to,wp_slash($e['seo'][$from]));
  if(isset($e['service_label']))update_post_meta($post->ID,'pelham_service_label',$e['service_label']);
 }
 $opts=get_option('pelham_company',[]);if(!add_option('pelham_company_before_offer_1_9',$opts,'','no')&&get_option('pelham_company_before_offer_1_9',null)===null)return new WP_Error('backup','Could not back up company settings.');
 foreach(['ui_quote_choice_freight_consolidation','ui_quote_choice_dedicated_transport','ui_quote_choice_transloading'] as $key)$opts[$key]=pelham_defaults()[$key];update_option('pelham_company',$opts);
 update_option('pelham_offer_release','1.10.0');return true;
}
add_action('admin_notices',function(){if(!current_user_can('manage_options')||get_option('pelham_offer_release')==='1.10.0'||get_current_screen()->id!=='toplevel_page_pelham-company')return;echo '<div class="notice notice-info"><h2>Pelham: confirmed LTL offer</h2><p>Update Home, Services, the three service pages, About, Locations and Quote, including titles, summaries and SEO settings. Existing copy is backed up per page; URLs, images, applications and company contact details are preserved. This replaces edited copy on these pages. Orlando is listed without an unconfirmed address.</p><form action="'.esc_url(admin_url('admin-post.php')).'" method="post"><input type="hidden" name="action" value="pelham_offer_19">';wp_nonce_field('pelham_offer_19');submit_button('Apply LTL content and SEO update (save backups)','primary','submit',false);echo '</form><p>3 / 7 / 10 calendar days from pickup, not guaranteed. Expedited: Homestead–West Palm Beach only.</p></div>';});
add_action('admin_post_pelham_offer_19',function(){if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_offer_19');if(get_option('pelham_offer_release')!=='1.10.0'){$result=pelham_apply_offer();if(is_wp_error($result))wp_die(esc_html($result->get_error_message()));}wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;});
