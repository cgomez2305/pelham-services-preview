<?php
/**
 * Plugin Name: Pelham Content
 * Description: Editable services, terminals, company information, starter content and enquiry forms for Pelham Services.
 * Version: 1.21.2
 * Requires at least: 6.6
 * Requires PHP: 8.0
 * License: GPL-2.0-or-later
 * Text Domain: pelham-content
 */
if (!defined('ABSPATH')) exit;
require_once __DIR__.'/inc/security.php';
require_once __DIR__.'/inc/i18n.php';
function pelham_register_content() {
 foreach(['pelham_service'=>['Services','Service','services'],'pelham_terminal'=>['Terminals','Terminal','locations']] as $type=>$labels) {
  register_post_type($type,['labels'=>['name'=>$labels[0],'singular_name'=>$labels[1]],'public'=>true,'show_in_rest'=>true,'has_archive'=>false,'rewrite'=>['slug'=>$labels[2],'with_front'=>false],'supports'=>['title','editor','excerpt','thumbnail','revisions','page-attributes','custom-fields'],'menu_icon'=>$type==='pelham_service'?'dashicons-car':'dashicons-location-alt']);
 }
 foreach(['address'=>'string','phone'=>'string','hours'=>'string'] as $key=>$type) {
  register_post_meta('pelham_terminal','pelham_'.$key,['type'=>$type,'single'=>true,'show_in_rest'=>true,'sanitize_callback'=>'sanitize_textarea_field','auth_callback'=>function(){return current_user_can('edit_posts');}]);
 }
}
add_action('init','pelham_register_content');
function pelham_defaults() { return ['ui_quote_choice_help'=>'Describe your shipment; we’ll help you choose.','ui_quote_choice_dedicated_transport'=>'Exclusive trailer capacity for a single shipment or recurring work.','ui_quote_choice_freight_consolidation'=>'Our specialty: pallets from Los Angeles to Florida.','ui_quote_choice_transloading'=>'Cross-docking, transloading and palletizing for domestic trucking.','ui_quote_origin_example'=>'e.g. Gardena, CA or 90248','ui_quote_destination_example'=>'e.g. Miami, FL or 33166','ui_quote_route_help'=>'Enter a city and state or ZIP code for pickup and delivery. Exact street addresses can follow when you discuss the shipment.','ui_quote_guide_general'=>'Tell us what you are shipping, the approximate quantity or weight, and your preferred dates. If you are unsure which service fits, describe the move and our team can help.','ui_quote_equipment_summary'=>'Add equipment or handling details (optional)','ui_quote_question_general'=>'What needs to move, and when?','ui_quote_guide_dedicated_transport'=>'Include your load size, preferred pickup date and whether this is a one-time or recurring shipment. For recurring loads, tell us the expected frequency.','ui_quote_guide_freight_consolidation'=>'Include the number of pallets or pieces, dimensions, total weight and preferred dates. Mention any special handling needs so the team can discuss whether shared transport fits.','ui_quote_guide_transloading'=>'Describe the incoming container or trailer, cargo type and quantity, and the outbound equipment or destination. Include the expected arrival date if known.','ui_quote_question_dedicated_transport'=>'What load and schedule should we plan for?','ui_quote_question_freight_consolidation'=>'What pieces or pallets are you shipping?','ui_quote_question_transloading'=>'What is arriving, and how should it move onward?','email'=>'info@pelhamservicesca.com','recipient'=>get_option('admin_email'),'portal'=>'https://pelham.supply-vision.com/Portal/Login','facebook'=>'https://www.facebook.com/profile.php?id=61556624532644','linkedin'=>'https://www.linkedin.com/company/108392574','tagline'=>'Real people. Clear communication. Freight that moves forward.','footer_heading'=>'Your next move starts here.','fuel_rate'=>'','fuel_basis'=>'','fuel_updated'=>'','claim_form_url'=>'','customer_application_url'=>'','credit_application_url'=>'','terms_url'=>'','header_cta'=>'Let’s move your freight','footer_cta'=>'Request a quote','footer_eyebrow'=>'KEEP MOVING FORWARD','footer_explore'=>'Explore','scene_heading'=>'THE PELHAM CONNECTION','scene_caption'=>'Two coasts. One connected team.','scene_ca'=>'GARDENA','scene_fl'=>'MIAMI','scene_ca_detail'=>'California terminal','scene_fl_detail'=>'Florida terminal','map_lane_caption'=>'OUR CORE COAST-TO-COAST LANE','ui_service_cta'=>'Explore service','ui_news_cta'=>'Read the story','ui_terminal_eyebrow'=>'OUR TERMINALS','ui_form_help'=>'Fields marked * are required.','ui_form_optional'=>'optional','ui_form_your_details'=>'Your details','ui_form_shipment_details'=>'Your shipment','ui_contact_jump'=>'Send a message','ui_form_name'=>'Your name','ui_form_email'=>'Email address','ui_form_company'=>'Company','ui_form_phone'=>'Phone','ui_form_origin'=>'Origin','ui_form_destination'=>'Destination','ui_form_service'=>'Service','ui_form_choose'=>'Help me choose','ui_form_freight'=>'Freight / equipment','ui_form_message'=>'How can we help?','ui_form_consent'=>'I agree to be contacted about this enquiry.','ui_form_privacy'=>'Your details are used to respond to this request.','ui_form_privacy_link'=>'Privacy policy','ui_form_contact_submit'=>'Send message','ui_form_quote_submit'=>'Send quote request','ui_form_sent'=>'Thank you. Your message has been sent to our team.','ui_form_invalid'=>'Please complete all required fields with a valid email address.','ui_form_failed'=>'We could not send your message. Please contact us by phone or email.','ui_form_limited'=>'Please wait a few minutes before sending another message.','ui_map_cta'=>'Open directions','ui_documents_cta'=>'Contact our team for onboarding documents']; }
/** Original SVG line icons: 24px grid, 1.7px stroke, text labels remain accessible. */
function pelham_icon($name){
 $paths=[
 'truck'=>'<path d="M3 5h11v12H3zM14 9h4l3 4v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
 'boxes'=>'<path d="m3 7 4-2 4 2-4 2-4-2v6l4 2 4-2V7M7 9v6m6-8 4-2 4 2-4 2-4-2v6l4 2 4-2V7m-4 2v6M8 17l4-2 4 2v4l-4 2-4-2v-4m0 0 4 2 4-2m-4 2v4"/>',
 'transfer'=>'<path d="M3 7h17m-4-4 4 4-4 4M21 17H4m4-4-4 4 4 4"/>',
 'pin'=>'<path d="M20 10c0 6-8 11-8 11S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
 'flag'=>'<path d="M5 21V3m0 1c5-3 9 3 14 0v9c-5 3-9-3-14 0"/>',
 'mail'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/>',
 'phone'=>'<path d="m8 3 2 5-3 2c2 4 3 5 7 7l2-3 5 2v3c0 2-2 3-4 2C9 19 5 15 3 7c-1-2 0-4 2-4h3Z"/>',
 'route'=>'<circle cx="5" cy="5" r="2"/><circle cx="19" cy="19" r="2"/><path d="M7 5h9a4 4 0 0 1 0 8H8a3 3 0 0 0 0 6h9"/>',
 'user'=>'<circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/>'
 ];
 if(!isset($paths[$name]))return '';
 return '<svg class="pelham-icon" aria-hidden="true" focusable="false" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">'.$paths[$name].'</svg>';
}
function pelham_ui($key,$fallback){$o=get_option('pelham_company',[]);return apply_filters('pelham_ui_text',$o['ui_'.$key]??$fallback);}
register_activation_hook(__FILE__,function(){pelham_register_content(); pelham_i18n_rewrite_rules(); add_option('pelham_company',pelham_defaults()); pelham_import_content(); flush_rewrite_rules();});
register_deactivation_hook(__FILE__,'flush_rewrite_rules');
add_action('admin_init',function(){register_setting('pelham_company','pelham_company',['type'=>'array','sanitize_callback'=>function($input){
 $old=get_option('pelham_company',pelham_defaults()); foreach(pelham_defaults() as $key=>$value) { if(!isset($input[$key]))continue; $raw=wp_unslash($input[$key]); $old[$key]=in_array($key,['email','recipient'],true)?sanitize_email($raw):(in_array($key,['portal','facebook','linkedin'],true)||str_ends_with($key,'_url')?esc_url_raw($raw):sanitize_textarea_field($raw)); } return $old;
}]);});
add_action('admin_menu',function(){add_menu_page('Pelham Company','Pelham Company','manage_options','pelham-company','pelham_settings_page','dashicons-admin-site-alt3',26);});
function pelham_settings_page(){ if(!current_user_can('manage_options'))return; $opts=get_option('pelham_company',pelham_defaults()); ?>
<div class="wrap"><h1>Pelham — company information</h1><p>Pages: edit homepage sections and all page text in the WordPress editor. Services and Terminals: edit content, summaries and featured images. Appearance → Menus: edit navigation. These settings update shared company information.</p><?php if(isset($_GET['imported'])) echo '<div class="notice notice-success"><p>Starter content imported. Existing pages were preserved.</p></div>'; ?>
<form method="post" action="options.php"><?php settings_fields('pelham_company'); ?><table class="form-table"><?php foreach(pelham_defaults() as $key=>$default){ ?><tr><th><label for="pelham-<?php echo esc_attr($key); ?>"><?php echo esc_html(ucwords(str_replace('_',' ',$key))); ?></label></th><td><textarea class="large-text" rows="2" id="pelham-<?php echo esc_attr($key); ?>" name="pelham_company[<?php echo esc_attr($key); ?>]"><?php echo esc_textarea($opts[$key]??$default); ?></textarea></td></tr><?php } ?></table><p>Recipient is the email address for enquiries. Configure your host’s SMTP delivery before launch. Leave fuel fields empty until operations has verified them.</p><?php submit_button(); ?></form>
<hr><h2>Import the starter website</h2><p>Creates missing pages, services, terminals and articles. Existing content is never overwritten. It sets a homepage only if your site does not already have one, and adds a primary menu only when no primary menu is assigned. Back up an existing site before importing.</p><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="pelham_import"><?php wp_nonce_field('pelham_import'); submit_button('Import missing starter content','secondary'); ?></form><?php do_action('pelham_company_tools'); ?></div>
<?php }
add_action('admin_post_pelham_import',function(){if(!current_user_can('manage_options'))wp_die('Forbidden',403); check_admin_referer('pelham_import'); pelham_import_content(); wp_safe_redirect(admin_url('admin.php?page=pelham-company&imported=1'));exit;});
function pelham_import_content(){
 // Set the /news/ structure first: internal links below are resolved to real permalinks.
 pelham_news_permalinks();
 $home=file_get_contents(__DIR__.'/data/home.html');
 $entries=json_decode(file_get_contents(__DIR__.'/data/pages.json'),true);
 array_unshift($entries,['route'=>'home','title'=>'Home','type'=>'page','content'=>$home,'seo'=>json_decode(file_get_contents(__DIR__.'/data/keyword-map.json'),true)['home']]);
 $news_category=term_exists('pelham-insights','category');if(!$news_category)$news_category=wp_insert_term('Pelham Insights','category',['slug'=>'pelham-insights']);
 $ids=[]; $created=[];
 foreach($entries as $entry){
  $slug=basename($entry['route']); $found=get_posts(['post_type'=>$entry['type'],'name'=>$slug,'numberposts'=>1,'post_status'=>'any']);
  if($found){$ids[$entry['route']]=$found[0]->ID;continue;}
  $content=preg_replace_callback('/(href|src)="(\/[^"#]*)"/',function($m){return $m[1].'="'.esc_url(home_url($m[2])).'"';},$entry['content']);
  $excerpt=$entry['excerpt']??wp_trim_words(wp_strip_all_tags($content),32);
  if($entry['type']==='pelham_service'){
   $summaries=['transloading'=>'Cross-docking, transloading and palletizing for domestic trucking.','dedicated-transport'=>'Exclusive truck capacity from Los Angeles to businesses in Florida.','freight-consolidation'=>'Smaller shipments. Smarter loads. Share the journey and make more of every mile.'];
   $excerpt=$entry['excerpt']??($summaries[$slug]??$excerpt);
  }
  $id=wp_insert_post(['post_type'=>$entry['type'],'post_name'=>$slug,'post_title'=>$entry['title'],'post_content'=>$content,'post_excerpt'=>$excerpt,'post_status'=>'publish','post_date'=>$entry['date']??current_time('mysql'),'menu_order'=>$entry['menu_order']??count($ids)],true);
  if(is_wp_error($id))continue;
  if(!empty($entry['seo'])){update_post_meta($id,'pelham_seo_title',$entry['seo']['seo_title']);update_post_meta($id,'pelham_seo_description',$entry['seo']['description']);update_post_meta($id,'pelham_primary_keyword',$entry['seo']['keyword']);}
  if(!empty($entry['service_label']))update_post_meta($id,'pelham_service_label',$entry['service_label']);
  $ids[$entry['route']]=$id; $created[$id]=$entry['content'];
  if($entry['type']==='post'&&!is_wp_error($news_category))wp_set_object_terms($id,(int)$news_category['term_id'],'category');
  if($entry['type']==='pelham_service'){
   $titles=['transloading'=>'Transloading','dedicated-transport'=>'Dedicated transport','freight-consolidation'=>'Freight consolidation']; if(empty($entry['seo']))wp_update_post(['ID'=>$id,'post_title'=>$titles[$slug]??$entry['title']]);
  }
  if($entry['type']==='pelham_terminal'){
   $locations=json_decode(file_get_contents(__DIR__.'/data/locations-confirmed.json'),true);if(isset($locations[$slug])){update_post_meta($id,'pelham_address',$locations[$slug]['address']);if($locations[$slug]['phone']!==null)update_post_meta($id,'pelham_phone',$locations[$slug]['phone']);}
  }
 }
 // Resolve all imported links to WordPress permalinks, including sites with custom blog URLs.
 foreach($created as $id=>$original){
  $content=preg_replace_callback('/(href|src)="(\/[^"#]*)"/',function($m)use($ids){
   $route=trim($m[2],'/');
   if(isset($ids[$route]))$url=get_permalink($ids[$route]);
   elseif(str_starts_with($route,'assets/'))$url=plugins_url('data/legacy/'.substr($route,7),__FILE__);
   else $url=home_url($m[2]);
   return $m[1].'="'.esc_url($url).'"';
  },$original);
  wp_update_post(['ID'=>$id,'post_content'=>$content]);
 }
 if(!get_option('page_on_front')&&!empty($ids['home'])){update_option('show_on_front','page');update_option('page_on_front',$ids['home']);}
 $locs=get_theme_mod('nav_menu_locations',[]);
 if(empty($locs['primary'])){
  $menu=wp_create_nav_menu('Pelham navigation');
  if(!is_wp_error($menu)){foreach(['home'=>'Home','services'=>'Services','locations'=>'Our network','about'=>'About us','news'=>'Insights'] as $route=>$label){if(isset($ids[$route]))wp_update_nav_menu_item($menu,0,['menu-item-title'=>$label,'menu-item-object-id'=>$ids[$route],'menu-item-object'=>'page','menu-item-type'=>'post_type','menu-item-status'=>'publish']);} $locs['primary']=$menu;set_theme_mod('nav_menu_locations',$locs);}
 }
 // Copy the original media to a plugin-owned directory; never replace existing media.
 $upload=wp_upload_dir(); $target=$upload['basedir'].'/pelham'; wp_mkdir_p($target);
 foreach(glob(__DIR__.'/data/brand/*.png')?:[] as $file){$dest=$target.'/'.basename($file);if(!file_exists($dest))copy($file,$dest);}
 pelham_i18n_rewrite_rules(); flush_rewrite_rules(); return $ids;
}
add_action('add_meta_boxes',function(){add_meta_box('pelham-terminal','Terminal details',function($post){wp_nonce_field('pelham_terminal','pelham_terminal_nonce');foreach(['address','phone','hours'] as $key){echo '<p><label>'.esc_html(ucfirst($key)).'<br><textarea style="width:100%" name="pelham_'.$key.'">'.esc_textarea(get_post_meta($post->ID,'pelham_'.$key,true)).'</textarea></label></p>'; }},'pelham_terminal');});
add_action('save_post_pelham_terminal',function($id){if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)return;if(!current_user_can('edit_post',$id)||!isset($_POST['pelham_terminal_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pelham_terminal_nonce'])),'pelham_terminal'))return;foreach(['address','phone','hours'] as $key){if(isset($_POST['pelham_'.$key]))update_post_meta($id,'pelham_'.$key,sanitize_textarea_field(wp_unslash($_POST['pelham_'.$key])));}});
add_shortcode('pelham_network_map',function(){ $o=get_option('pelham_company',[]);return str_replace('OUR CORE COAST-TO-COAST LANE',esc_html($o['map_lane_caption']??'OUR CORE COAST-TO-COAST LANE'),file_get_contents(__DIR__.'/data/network.html'));});
function pelham_card_art($i){
 $type=$i%3;
 if($type===0)return '<div class="mini-warehouse"><div class="warehouse-roof"></div><div class="warehouse-door"></div><i class="box box-a"></i><i class="box box-b"></i><i class="box box-c"></i></div>';
 if($type===1)return '<div class="mini-highway"><div class="highway-line"></div><div class="mini-truck"><i></i><b></b></div></div>';
 return '<div class="mini-cargo"><i></i><i></i><i></i><i></i><i></i><i></i></div>';
}
/** Extra card content per service (pallet range, service points), data/service-cards.json. */
function pelham_service_card($route){static $cards;if(!isset($cards))$cards=json_decode(file_get_contents(__DIR__.'/data/service-cards.json'),true)?:[];return $cards[$route]??[];}
add_shortcode('pelham_services',function(){ $posts=get_posts(['post_type'=>'pelham_service','numberposts'=>-1,'post__not_in'=>is_singular('pelham_service')?[get_the_ID()]:[],'orderby'=>'menu_order','order'=>'ASC']); ob_start(); if(pelham_is_page('services')){ ?><h2 class="screen-reader-text"><?php echo esc_html(pelham_t('Freight services')); ?></h2><?php } ?><div class="service-grid"><?php foreach($posts as $i=>$post){ ?><article class="service-card"><a href="<?php echo esc_url(get_permalink($post)); ?>"><div class="card-art" aria-hidden="true"><?php echo ''; ?><span class="card-index"><?php echo esc_html(sprintf('%02d',$i+1)); ?></span></div><div class="card-body"><h3><?php echo pelham_icon(['dedicated-transport'=>'truck','freight-consolidation'=>'boxes','transloading'=>'transfer'][pelham_route_slug($post)]??'truck'); echo esc_html(get_post_meta($post->ID,'pelham_service_label',true)?:$post->post_title); ?></h3><?php $card=pelham_service_card(pelham_route_slug($post)); if(!empty($card['detail'])){ ?><p class="card-detail"><?php echo esc_html(pelham_t($card['detail'])); ?></p><?php } if(!empty($card['points'])){ ?><ul class="card-points"><?php foreach($card['points'] as [$term,$text]){ ?><li><strong><?php echo esc_html(pelham_t($term)); ?>:</strong> <?php echo esc_html(pelham_t($text)); ?></li><?php } ?></ul><?php } else { ?><p><?php echo esc_html($post->post_excerpt); ?></p><?php } ?><span class="card-link"><?php echo esc_html(pelham_ui('service_cta','Explore service')); ?> <span aria-hidden="true"><span class="direction-arrow" aria-hidden="true"></span></span></span></div></a></article><?php } ?></div><?php return ob_get_clean();});
add_shortcode('pelham_terminals',function(){ob_start();?><div class="service-grid terminal-grid"><?php foreach(get_posts(['post_type'=>'pelham_terminal','numberposts'=>-1,'orderby'=>'title','order'=>'ASC']) as $post){?><article class="terminal-card"><p class="eyebrow"><?php echo esc_html(pelham_ui('terminal_eyebrow','OUR TERMINALS')); ?></p><h2><a href="<?php echo esc_url(get_permalink($post)); ?>"><?php echo esc_html($post->post_title); ?></a></h2><p><?php echo nl2br(esc_html(get_post_meta($post->ID,'pelham_address',true))); ?></p><p><?php echo esc_html(get_post_meta($post->ID,'pelham_hours',true)); ?></p><?php if(get_post_meta($post->ID,'pelham_phone',true)){ ?><a href="tel:<?php echo esc_attr(preg_replace('/[^+0-9]/','',get_post_meta($post->ID,'pelham_phone',true))); ?>"><?php echo esc_html(get_post_meta($post->ID,'pelham_phone',true)); ?></a><?php } ?></article><?php } ?></div><?php return ob_get_clean();});
add_shortcode('pelham_news',function($attrs=[]){
 $attrs=shortcode_atts(['count'=>pelham_is_page('news')?12:3],$attrs);$count=max(1,min(24,absint($attrs['count'])));
 $posts=get_posts(['post_type'=>'post','numberposts'=>-1,'category_name'=>term_exists('pelham-insights','category')?'pelham-insights':'']);
 // Same order in both languages: newest first, ties broken by the English post, so /es/ shows the same articles as the English page.
 $english_id=function($post){$english=pelham_translation($post,'en');return $english?$english->ID:$post->ID;};
 usort($posts,function($a,$b)use($english_id){return strcmp($b->post_date,$a->post_date)?:$english_id($b)-$english_id($a);});
 $posts=array_slice($posts,0,$count);
 if(pelham_is_page('news')){usort($posts,function($a,$b){return (pelham_route_slug($b)==='what-to-include-in-a-freight-quote')-(pelham_route_slug($a)==='what-to-include-in-a-freight-quote');});}
 ob_start();if(pelham_is_page('news')){ ?><div class="news-topics" aria-label="<?php echo esc_attr(pelham_t('Filter freight insights')); ?>" hidden><?php foreach(['all'=>'All insights','planning'=>'Freight planning','transloading'=>'Transloading','consolidation'=>'Consolidation'] as $key=>$label){ ?><button type="button" data-topic="<?php echo esc_attr($key); ?>" aria-pressed="<?php echo $key==='all'?'true':'false'; ?>"><?php echo esc_html(pelham_t($label)); ?></button><?php } ?></div><p class="news-count" role="status" hidden></p><h2 class="screen-reader-text"><?php echo esc_html(pelham_t('All freight insights')); ?></h2><?php } ?>
 <div class="news-grid"><?php foreach($posts as $i=>$post){$route=pelham_route_slug($post);$topic=str_contains($route,'transload')?'transloading':((str_contains($route,'consolidat')||str_contains($route,'ltl'))?'consolidation':'planning');?><article class="news-card" data-topic="<?php echo esc_attr($topic); ?>"><a href="<?php echo esc_url(get_permalink($post)); ?>"><div class="news-art news-art-<?php echo esc_attr($i); ?>" aria-hidden="true"><?php echo pelham_photo_markup(pelham_keyword_data($post)['photo']??'none','news-photo',false,$post->ID); ?></div><p class="news-date"><time datetime="<?php echo esc_attr(get_the_date('c',$post)); ?>"><?php echo esc_html(pelham_date('M j, Y',$post)); ?></time></p><h3><?php echo esc_html($post->post_title); ?></h3><p class="news-summary"><?php echo esc_html(wp_trim_words(function_exists('pelham_seo_description')?pelham_seo_description($post):wp_strip_all_tags(strip_shortcodes($post->post_excerpt?:$post->post_content)),28)); ?></p><span class="text-link"><?php echo esc_html(pelham_ui('news_cta','Read the story')); ?> <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24"><path d="M5 19 19 5M6 5h13v13" fill="none" stroke="currentColor" stroke-width="1.7"/></svg></span></a></article><?php } ?></div><?php return ob_get_clean();});

add_shortcode('pelham_quick_calls',function(){ob_start();?><nav class="quick-terminal-calls" aria-label="<?php echo esc_attr(pelham_t('Call a freight terminal')); ?>"><?php foreach(get_posts(['post_type'=>'pelham_terminal','numberposts'=>-1,'orderby'=>'title','order'=>'ASC']) as $terminal){$phone=get_post_meta($terminal->ID,'pelham_phone',true);if(!$phone)continue;?><a href="tel:<?php echo esc_attr(preg_replace('/[^+0-9]/','',$phone)); ?>"><span><?php echo esc_html(pelham_t('Call')); ?> <?php echo esc_html($terminal->post_title); ?></span><span><?php echo esc_html($phone); ?></span></a><?php } ?></nav><?php return ob_get_clean();});

add_shortcode('pelham_contact_directory',function(){ob_start();?><div class="contact-directory"><?php foreach(get_posts(['post_type'=>'pelham_terminal','numberposts'=>-1,'orderby'=>'title','order'=>'ASC']) as $terminal){$address=get_post_meta($terminal->ID,'pelham_address',true);$phone=get_post_meta($terminal->ID,'pelham_phone',true);if(!$phone)continue;?><article><h3><a href="<?php echo esc_url(get_permalink($terminal)); ?>"><?php echo esc_html($terminal->post_title); ?></a></h3><p><?php echo nl2br(esc_html($address)); ?></p><p><?php echo esc_html(get_post_meta($terminal->ID,'pelham_hours',true)); ?></p><div class="terminal-actions"><?php if($phone){ ?><a href="tel:<?php echo esc_attr(preg_replace('/[^+0-9]/','',$phone)); ?>"><?php echo pelham_icon('phone'); echo esc_html($phone); ?></a><?php } ?><a href="<?php echo esc_url('https://www.google.com/maps/search/?api=1&query='.rawurlencode($address)); ?>" target="_blank" rel="noopener"><?php echo pelham_icon('route'); echo esc_html(pelham_ui('map_cta','Open directions')); ?></a></div></article><?php }$options=get_option('pelham_company',[]);if(!empty($options['email'])){?><a href="mailto:<?php echo esc_attr($options['email']); ?>"><?php echo esc_html($options['email']); ?></a><?php } ?></div><?php return ob_get_clean();});

add_shortcode('pelham_fuel',function(){ $o=get_option('pelham_company',[]);if(empty($o['fuel_rate']))return '';return '<div class="shell fuel-panel"><h3>'.esc_html(pelham_t('Current fuel surcharge')).'</h3><p>'.esc_html($o['fuel_rate']).'</p><p>'.esc_html(pelham_t($o['fuel_basis']??'')).'</p><p>'.esc_html(pelham_t('Updated:')).' '.esc_html($o['fuel_updated']??'').'</p></div>';});
add_shortcode('pelham_contact_jump',function(){return '<a class="text-link contact-form-jump" href="#pelham-enquiry">'.esc_html(pelham_ui('contact_jump','Send a message')).'</a>';});
// Add a shortcut without changing the page's saved blocks or any existing edits.
add_filter('the_content',function($content){
 // Suppress the exact legacy development caption; keep all real editorial captions.
 $content=preg_replace('/<figcaption\b[^>]*>\s*\[FEATURED IMAGE PENDING[^\]]*\]\s*<\/figcaption>/is','',$content);
 if(pelham_is_page('contact')&&str_contains($content,'[pelham_form')&&!str_contains($content,'[pelham_contact_jump]')){
  $content=preg_replace('/\[pelham_terminals\]/','[pelham_contact_jump][pelham_terminals]',$content,1);
 }
 return $content;
},9);
add_shortcode('pelham_form',function($attrs){
 $attrs=shortcode_atts(['kind'=>'contact'],$attrs);
 $kind=$attrs['kind']==='quote'?'quote':'contact';
 $state=isset($_GET['pelham_status'])?sanitize_key($_GET['pelham_status']):'';
 $field=function($key,$fallback,$required=false,$type='text',$autocomplete='',$maxlength=150){
  ?><label><?php if(in_array($key,['origin','destination'],true))echo pelham_icon($key==='origin'?'pin':'flag'); echo esc_html(pelham_ui('form_'.$key,$fallback)); ?> <span class="field-requirement"><?php if($required){?><span aria-hidden="true">*</span><?php }else echo '('.esc_html(pelham_ui('form_optional','optional')).')'; ?></span><input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($key); ?>" <?php if($required)echo 'required'; ?> <?php if($autocomplete)echo 'autocomplete="'.esc_attr($autocomplete).'"'; ?> maxlength="<?php echo esc_attr($maxlength); ?>" <?php if(in_array($key,['origin','destination'],true))echo 'aria-describedby="quote-route-help" placeholder="'.esc_attr(pelham_ui('quote_'.$key.'_example',$key==='origin'?'e.g. Gardena, CA or 90248':'e.g. Miami, FL or 33166')).'"'; ?>></label><?php
 };
 ob_start(); ?>
<form id="pelham-enquiry" class="pelham-form" tabindex="-1" aria-label="<?php echo esc_attr(pelham_t($kind==='quote'?'Freight quote request':'Contact enquiry')); ?>" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
 <input type="hidden" name="action" value="pelham_enquiry"><input type="hidden" name="pelham_lang" value="<?php echo esc_attr(pelham_lang()); ?>"><input type="hidden" name="kind" value="<?php echo esc_attr($kind); ?>"><input type="hidden" name="return_url" value="<?php echo esc_url(get_permalink()); ?>"><?php wp_nonce_field('pelham_enquiry','pelham_nonce'); ?>
 <?php if($state){$messages=['sent'=>pelham_ui('form_sent','Thank you. Your message has been sent to our team.'),'invalid'=>pelham_ui('form_invalid','Please complete all required fields with a valid email address.'),'failed'=>pelham_ui('form_failed','We could not send your message. Please contact us by phone or email.'),'limited'=>pelham_ui('form_limited','Please wait a few minutes before sending another message.')]; ?><p class="form-status" role="<?php echo $state==='sent'?'status':'alert'; ?>" tabindex="-1"><?php echo esc_html($messages[$state]??$messages['failed']); ?></p><?php } ?>
 <p class="form-help"><?php echo esc_html(pelham_ui('form_help','Fields marked * are required.')); ?></p>
 <?php if($kind==='contact'){ ?> <fieldset><legend><?php echo pelham_icon('user'); echo esc_html(pelham_ui('form_your_details','Your details')); ?></legend><div class="form-grid">
 <?php $field('name','Your name',true,'text','name',100);$field('email','Email address',true,'email','email',150); ?>
 </div><?php if(in_array($kind,['contact','quote'],true)){ ?><details class="optional-fields"><summary><?php echo esc_html(pelham_t('Add company or phone (optional)')); ?></summary><div class="form-grid"><?php $field('company','Company',false,'text','organization',150);$field('phone','Phone',false,'tel','tel',40); ?></div></details><?php } ?></fieldset>
<?php } ?>
 <?php if($kind==='quote'){ ?>
 <fieldset class="quote-shipment"><legend><?php echo pelham_icon('truck'); echo esc_html(pelham_ui('form_shipment_details','Your shipment')); ?></legend>
 <label><?php echo esc_html(pelham_ui('form_service','Service')); ?> <span class="field-requirement">(<?php echo esc_html(pelham_ui('form_optional','optional')); ?>)</span><select name="service"><option data-service="" data-description="<?php echo esc_attr(pelham_ui('quote_choice_help','Describe your shipment; we’ll help you choose.')); ?>"><?php echo esc_html(pelham_ui('form_choose','Help me choose')); ?></option><?php foreach(get_posts(['post_type'=>'pelham_service','numberposts'=>-1,'orderby'=>'menu_order','order'=>'ASC']) as $post){$route=pelham_route_slug($post);echo '<option data-service="'.esc_attr($route).'" data-description="'.esc_attr(pelham_ui('quote_choice_'.str_replace('-','_',$route),['dedicated-transport'=>'Exclusive trailer capacity for a single shipment or recurring work.','freight-consolidation'=>'Our specialty: pallets from Los Angeles to Florida.','transloading'=>'Cross-docking, transloading and palletizing for domestic trucking.'][$route]??get_the_excerpt($post))).'"'.selected(sanitize_key($_GET['service']??''),$route,false).'>'.esc_html(get_post_meta($post->ID,'pelham_service_label',true)?:$post->post_title).'</option>';} ?></select></label>
 <?php echo pelham_ltl_choice(); ?>
 <p class="quote-route-help" id="quote-route-help"><?php echo esc_html(pelham_t('Enter the 5-digit ZIP code for pickup and delivery. Exact street addresses can follow when you discuss the shipment.')); ?></p>
 <div class="form-grid quote-route"><?php foreach(['origin'=>['pin','Origin ZIP code','e.g. 90248'],'destination'=>['flag','Destination ZIP code','e.g. 33166']] as $key=>[$icon,$label,$example]){ ?><label><?php echo pelham_icon($icon).esc_html(pelham_t($label)); ?> <span class="field-requirement"><span aria-hidden="true">*</span></span><input type="text" name="<?php echo esc_attr($key); ?>" required inputmode="numeric" autocomplete="postal-code" maxlength="10" pattern="[0-9]{5}(-[0-9]{4})?" aria-describedby="quote-route-help" placeholder="<?php echo esc_attr(pelham_t($example)); ?>"></label><?php } ?></div>
 <?php // Shipment details for LTL (with cubic feet) and FTL (total weight); offer.js shows the set that matches the service. ?>
 <div class="quote-details" data-quote-details><p class="quote-details-title"><?php echo esc_html(pelham_t('Shipment details')); ?></p><div class="form-grid">
 <?php foreach(['pieces'=>['Pieces','number','min="1" step="1" inputmode="numeric"',''],'weight'=>['Weight (lb)','number','min="1" step="any" inputmode="decimal"',''],'cubic_feet'=>['Cubic feet','number','min="0.1" step="any" inputmode="decimal"','ltl'],'commodity'=>['Commodity','text','maxlength="150" placeholder="'.esc_attr(pelham_t('e.g. wearing apparel, auto parts')).'"',''],'value'=>['Value (USD)','number','min="0" step="any" inputmode="decimal"','']] as $key=>[$label,$type,$attributes,$only]){ ?><label<?php if($only)echo ' data-ltl-only'; ?>><span<?php if($key==='weight')echo ' data-label-ltl="'.esc_attr(pelham_t('Weight (lb)')).'" data-label-ftl="'.esc_attr(pelham_t('Total weight (lb)')).'"'; ?>><?php echo esc_html(pelham_t($label)); ?></span> <span class="field-requirement"><span aria-hidden="true">*</span></span><input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($key); ?>" <?php echo $attributes; ?>></label><?php } ?>
 </div></div>
 <div class="quote-guidance" id="quote-guidance" aria-live="polite" aria-atomic="true">
 <?php echo esc_html(pelham_ui('quote_guide_general','Tell us what you are shipping, the approximate quantity or weight, and your preferred dates. If you are unsure which service fits, describe the move and our team can help.')); ?>
 </div>
 <?php foreach(['dedicated-transport'=>'Complete the shipment details. In the message, add the preferred pickup date and whether this is a one-time or recurring load.','freight-consolidation'=>'Complete the shipment details. In the message, add the preferred pickup date and any special handling needs.','transloading'=>'Describe the incoming container or trailer, cargo type and quantity, and the outbound equipment or destination. Include the expected arrival date if known.'] as $slug=>$guide){ $structured=$slug!=='transloading'; ?><template data-quote-guide="<?php echo esc_attr($slug); ?>" data-question="<?php echo esc_attr($structured?pelham_t('Anything else we should know? (optional)'):pelham_ui('quote_question_transloading','What is arriving, and how should it move onward?')); ?>"><?php echo esc_html($structured?pelham_t($guide):pelham_ui('quote_guide_transloading',$guide)); ?></template><?php } ?>
 <details class="optional-fields quote-equipment"><summary><?php echo esc_html(pelham_ui('quote_equipment_summary','Add equipment or handling details (optional)')); ?></summary><?php $field('freight','Freight / equipment',false,'text','',200); ?></details>
 </fieldset><?php } ?>
 <label><span<?php if($kind==='quote')echo ' id="quote-message-label"'; ?>><?php echo esc_html($kind==='quote'?pelham_ui('quote_question_general','What needs to move, and when?'):pelham_ui('form_message','How can we help?')); ?></span> <span class="field-requirement" aria-hidden="true">*</span><textarea name="message" <?php if($kind==='quote')echo 'aria-describedby="quote-guidance"'; ?> required rows="5" maxlength="5000"></textarea></label>
 <?php if($kind==='quote'){ ?> <fieldset><legend><?php echo pelham_icon('user'); echo esc_html(pelham_ui('form_your_details','Your details')); ?></legend><div class="form-grid">
 <?php $field('name','Your name',true,'text','name',100);$field('email','Email address',true,'email','email',150); ?>
 </div><?php $requested_business=sanitize_key(wp_unslash($_GET['business']??''));$business_options=function_exists('pelham_audience_data')?pelham_audience_data()['business_options']:[];if($business_options){ ?><label><?php echo esc_html(pelham_ui('form_business','Your business')); ?> <span class="field-requirement">(<?php echo esc_html(pelham_ui('form_optional','optional')); ?>)</span><select name="business"><option value=""><?php echo esc_html(pelham_ui('form_business_choose','Select one')); ?></option><?php foreach($business_options as $value=>$label)echo '<option value="'.esc_attr($value).'"'.selected($requested_business,$value,false).'>'.esc_html(pelham_t($label)).'</option>'; ?></select></label><?php } ?><?php if(in_array($kind,['contact','quote'],true)){ ?><details class="optional-fields"><summary><?php echo esc_html(pelham_t('Add company or phone (optional)')); ?></summary><div class="form-grid"><?php $field('company','Company',false,'text','organization',150);$field('phone','Phone',false,'tel','tel',40); ?></div></details><?php } ?></fieldset>
<?php } ?> <label class="honey" aria-hidden="true"><?php echo esc_html(pelham_t('Leave empty')); ?> <input name="website" tabindex="-1" autocomplete="off"></label>
 <label class="consent"><input type="checkbox" name="consent" value="1" required> <?php echo esc_html(pelham_ui('form_consent','I agree to be contacted about this enquiry.')); ?></label>
 <p><?php echo esc_html(pelham_ui('form_privacy','Your details are used to respond to this request.')); ?> <?php if(get_privacy_policy_url()||get_page_by_path('privacy-notice')){ ?><a href="<?php echo esc_url(pelham_lang()==='es'?pelham_link('privacy-notice'):(get_privacy_policy_url()?:home_url('/privacy-notice/'))); ?>"><?php echo esc_html(pelham_ui('form_privacy_link','Privacy policy')); ?></a><?php } ?></p>
 <button class="button button-dark" type="submit"><?php echo esc_html($kind==='quote'?pelham_ui('form_quote_submit','Send quote request'):pelham_ui('form_contact_submit','Send message')); ?> <svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18"><path d="M5 19 19 5M6 5h13v13" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
</form>
<?php return pelham_form_errors(ob_get_clean(),$kind);});
function pelham_handle_enquiry(){
 $contact=pelham_link('contact');
 if((int)($_SERVER['CONTENT_LENGTH']??0)>20000||pelham_enquiry_input_errors(wp_unslash($_POST))) {wp_safe_redirect(add_query_arg('pelham_status','invalid',$contact));exit;}
 if(!pelham_enquiry_allow()){wp_safe_redirect(add_query_arg('pelham_status','limited',$contact));exit;}
 $return=isset($_POST['return_url'])?esc_url_raw(wp_unslash($_POST['return_url'])):$contact;$return=wp_validate_redirect($return,$contact);
 $finish=function($status,$errors=[])use($return){wp_safe_redirect(add_query_arg(['pelham_status'=>$status,'pelham_errors'=>implode(',',$errors)],$return).'#main-content');exit;};
 if(!isset($_POST['pelham_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pelham_nonce'])),'pelham_enquiry'))$finish('invalid',['session']);
 if(!empty($_POST['website']))$finish('invalid');
 $fields=[];foreach(['name','email','company','phone','business','origin','destination','service','ltl_level','freight','pieces','weight','cubic_feet','commodity','value','message','kind'] as $key)$fields[$key]=sanitize_textarea_field(wp_unslash($_POST[$key]??''));
 $service=$fields['kind']==='quote'?pelham_quote_service_slug($fields['service']):'';$structured=in_array($service,['freight-consolidation','dedicated-transport'],true);
 if(!$structured)foreach(['pieces','weight','cubic_feet','commodity','value'] as $key)$fields[$key]='';
 if($service!=='freight-consolidation')$fields['cubic_feet']='';
 $errors=[];if($fields['ltl_level']!==''){ $ltl=get_posts(['post_type'=>'pelham_service','name'=>'freight-consolidation','numberposts'=>1]);$labels=[];foreach(array_filter([$ltl[0]??null,$ltl?pelham_translation($ltl[0],'es'):null]) as $version){$labels[]=$version->post_title;$labels[]=get_post_meta($version->ID,'pelham_service_label',true);}$labels=array_values(array_filter($labels));if($fields['kind']!=='quote'||!in_array($fields['service'],$labels,true)||!isset(pelham_ltl_levels()[$fields['ltl_level']]))$errors[]='service';else{$level=pelham_ltl_levels()[$fields['ltl_level']];$fields['ltl_level']=implode(' | ',[$level[0],$level[1].' calendar days from pickup (estimated, not guaranteed)',$level[2]]);}}$business_options=function_exists('pelham_audience_data')?pelham_audience_data()['business_options']:[];$fields['business']=$business_options[$fields['business']]??'';if(!$fields['name'])$errors[]='name';if(!is_email($fields['email']))$errors[]='email';if((!$structured&&!$fields['message'])||strlen($fields['message'])>5000)$errors[]='message';if(empty($_POST['consent']))$errors[]='consent';if($fields['kind']==='quote')foreach(['origin','destination'] as $key)if(!preg_match('/^\d{5}(-\d{4})?$/',$fields[$key]))$errors[]=$key;
 if($structured){foreach(['pieces'=>1,'weight'=>0.01,'value'=>0]+($service==='freight-consolidation'?['cubic_feet'=>0.01]:[]) as $key=>$min)if(!is_numeric($fields[$key])||(float)$fields[$key]<$min||(float)$fields[$key]>99999999)$errors[]=$key;if(trim($fields['commodity'])==='')$errors[]='commodity';if(is_numeric($fields['pieces'])&&floor((float)$fields['pieces'])!=(float)$fields['pieces'])$errors[]='pieces';}
 if($errors)$finish('invalid',array_unique($errors));
 $ip=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';$rate='pelham_rate_'.hash_hmac('sha256',$ip,wp_salt());if(get_transient($rate))$finish('limited');set_transient($rate,1,2*MINUTE_IN_SECONDS);
 $opts=get_option('pelham_company',pelham_defaults());$to=sanitize_email($opts['recipient']??get_option('admin_email'));
 $names=['origin'=>'Origin ZIP','destination'=>'Destination ZIP','ltl_level'=>'LTL level','pieces'=>'Pieces','weight'=>$service==='dedicated-transport'?'Total weight (lb)':'Weight (lb)','cubic_feet'=>'Cubic feet','commodity'=>'Commodity','value'=>'Value (USD)'];
 $body='New website enquiry'."\n\n";foreach($fields as $key=>$value){if($value===''&&in_array($key,['ltl_level','pieces','weight','cubic_feet','commodity','value'],true))continue;$body.=($names[$key]??ucfirst($key)).': '.$value."\n";}
 $body.='Language: '.(pelham_lang()==='es'?'Spanish (reply in Spanish)':'English')."\n";
 $sent=wp_mail($to,'Pelham website — '.($fields['kind']==='quote'?'Quote request':'Contact enquiry'),$body,['Reply-To: '.sanitize_email($fields['email'])]);$finish($sent?'sent':'failed');
}
/** English slug of the quoted service from the submitted option label (English or Spanish). */
function pelham_quote_service_slug($label){
 if($label==='')return '';
 foreach(get_posts(['post_type'=>'pelham_service','numberposts'=>-1,'pelham_all_languages'=>true]) as $post)if(in_array($label,array_filter([$post->post_title,get_post_meta($post->ID,'pelham_service_label',true)]),true))return pelham_route_slug($post);
 return '';
}
add_action('admin_post_nopriv_pelham_enquiry','pelham_handle_enquiry');add_action('admin_post_pelham_enquiry','pelham_handle_enquiry');

add_shortcode('pelham_service_illustration',function(){return '';});
add_shortcode('pelham_terminal_details',function(){
 $id=get_the_ID();$address=get_post_meta($id,'pelham_address',true);$phone=get_post_meta($id,'pelham_phone',true);$hours=get_post_meta($id,'pelham_hours',true);
 $html='<div class="terminal-detail"><p>'.nl2br(esc_html($address)).'</p>';
 if($hours)$html.='<p>'.esc_html($hours).'</p>';
 $html.='<div class="terminal-detail-actions">';if($phone)$html.='<a href="tel:'.esc_attr(preg_replace('/[^+0-9]/','',$phone)).'">'.esc_html($phone).'</a>';
 if($address)$html.='<a class="text-link" href="'.esc_url('https://www.google.com/maps/search/?api=1&query='.rawurlencode($address)).'" target="_blank" rel="noopener">'.esc_html(pelham_ui('map_cta','Open directions')).'<span class="screen-reader-text"> (opens in a new tab)</span></a>';
 return $html.'</div></div>';
});

add_shortcode('pelham_maps',function(){ob_start();?><div class="terminal-maps"><?php foreach(get_posts(['post_type'=>'pelham_terminal','numberposts'=>-1]) as $post){$address=get_post_meta($post->ID,'pelham_address',true);?><div><h3><?php echo esc_html($post->post_title); ?></h3><div class="terminal-map-poster" aria-hidden="true"><div class="map-street street-one"></div><div class="map-street street-two"></div><div class="map-street street-three"></div><span class="map-pin"></span></div><p><?php echo nl2br(esc_html($address)); ?></p><a class="text-link" href="<?php echo esc_url('https://www.google.com/maps/search/?api=1&query='.rawurlencode($address)); ?>" target="_blank" rel="noopener"><?php echo esc_html(pelham_ui('map_cta','Open directions')); ?> <span class="direction-arrow" aria-hidden="true"></span></a></div><?php } ?></div><?php return ob_get_clean();});
add_shortcode('pelham_documents',function(){ $options=get_option('pelham_company',[]);$html='<ul class="document-links">';$count=0;foreach(['claim_form_url'=>'Claim form','customer_application_url'=>'Customer application','credit_application_url'=>'Credit application','terms_url'=>'Terms and agreements'] as $key=>$label){if(!empty($options[$key])){$count++;$html.='<li><a href="'.esc_url($options[$key]).'">'.esc_html($label).' <span class="direction-arrow" aria-hidden="true"></span></a></li>';}}$html.='</ul>';return $count?$html:'<p><a class="text-link" href="mailto:'.esc_attr($options['email']??'').'">'.esc_html(pelham_ui('documents_cta','Contact our team for onboarding documents')).' <span class="direction-arrow" aria-hidden="true"></span></a></p>'; });

add_shortcode('pelham_logistics_scene',function(){ $o=get_option('pelham_company',[]);$defaults=pelham_defaults();$labels=['THE PELHAM CONNECTION'=>'scene_heading','Two coasts. One connected team.'=>'scene_caption','GARDENA'=>'scene_ca','MIAMI'=>'scene_fl','California terminal'=>'scene_ca_detail','Florida terminal'=>'scene_fl_detail'];$replace=[];foreach($labels as $text=>$key)$replace[$text]=esc_html($o[$key]??$defaults[$key]);$scene=file_get_contents(__DIR__.'/data/scene.html');$scene=str_replace('<div class="scene-grid"></div>','<div class="scene-photo freight-photo">'.pelham_photo_markup('truck','',true,get_the_ID()).'</div><div class="scene-grid"></div>',$scene);return strtr($scene,$replace);});

// Keyword implementation is editable; native post titles remain the page H1.
add_action('init',function(){foreach(['page','post','pelham_service','pelham_terminal'] as $type){foreach(['pelham_seo_title','pelham_seo_description','pelham_primary_keyword','pelham_service_label'] as $key){register_post_meta($type,$key,['type'=>'string','single'=>true,'show_in_rest'=>true,'sanitize_callback'=>'sanitize_text_field','auth_callback'=>function(){return current_user_can('edit_posts');}]);}}});
add_action('add_meta_boxes',function(){foreach(['page','post','pelham_service','pelham_terminal'] as $type){add_meta_box('pelham-seo','Pelham — search and page settings',function($post){wp_nonce_field('pelham_seo','pelham_seo_nonce');foreach(['pelham_seo_title'=>'Search/social title','pelham_seo_description'=>'Search/social description','pelham_primary_keyword'=>'Primary keyword (planning reference)','pelham_service_label'=>'Compact service heading and card label'] as $key=>$label){if($key==='pelham_service_label'&&$post->post_type!=='pelham_service')continue;echo '<p><label for="'.esc_attr($key).'">'.esc_html($label).'</label><textarea id="'.esc_attr($key).'" name="'.esc_attr($key).'" style="width:100%" rows="2">'.esc_textarea(get_post_meta($post->ID,$key,true)).'</textarea></p>';}echo '<p>Services use the compact label for their visible heading and cards when provided. Other pages use the native post title. SEO metadata is optional and a dedicated SEO plugin takes precedence.</p>';},$type,'normal');}});
add_action('save_post',function($id){if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)return;if(!current_user_can('edit_post',$id)||!isset($_POST['pelham_seo_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pelham_seo_nonce'])),'pelham_seo'))return;foreach(['pelham_seo_title','pelham_seo_description','pelham_primary_keyword','pelham_service_label'] as $key){if(isset($_POST[$key]))update_post_meta($id,$key,sanitize_text_field(wp_unslash($_POST[$key])));}});
function pelham_keyword_data($post){
 static $map,$es;if(!isset($map)){$map=json_decode(file_get_contents(__DIR__.'/data/keyword-map.json'),true);$es=json_decode(file_get_contents(__DIR__.'/data/keyword-map-es.json'),true)?:[];}
 // Spanish posts use their own Spanish slugs and metadata.
 if(!empty($post->ID)&&pelham_post_lang($post)==='es')return $es[$post->post_name]??[];
 return $map[$post->post_name]??[];
}
require_once __DIR__.'/inc/photography.php';

require_once __DIR__.'/inc/upgrade-home.php';

require_once __DIR__.'/inc/customer-access.php';
require_once __DIR__.'/inc/upgrade-pages.php';

add_shortcode('pelham_direct_contact',function(){return '<div class="direct-contact"><a href="mailto:'.esc_attr(pelham_detail('email','info@pelhamservicesca.com')).'">'.pelham_icon('mail').esc_html(pelham_detail('email','info@pelhamservicesca.com')).'</a><a href="#terminals">'.pelham_icon('phone').'Call a terminal <span class="direction-arrow" aria-hidden="true"></span></a></div>';});
require_once __DIR__.'/inc/upgrade-1.8.php';
require_once __DIR__.'/inc/upgrade-quote.php';

require_once __DIR__.'/inc/offer-1.9.php';

require_once __DIR__.'/inc/seo-migration.php';

require_once __DIR__.'/inc/blog-1.11.php';

require_once __DIR__.'/inc/locations-1.11.php';

require_once __DIR__.'/inc/keywords-1.12.php';

require_once __DIR__.'/inc/keywords-1.13.php';

require_once __DIR__.'/inc/seo-audit-1.14.php';

require_once __DIR__.'/inc/article-intent-1.15.php';

require_once __DIR__.'/inc/audiences-1.17.php';
require_once __DIR__.'/inc/team-1.17.1.php';
require_once __DIR__.'/inc/keywords-1.17.php';
require_once __DIR__.'/inc/revisions-1.19.php';
require_once __DIR__.'/inc/revisions-1.20.php';
require_once __DIR__.'/inc/revisions-1.21.php';
require_once __DIR__.'/inc/permalinks-1.20.php';
