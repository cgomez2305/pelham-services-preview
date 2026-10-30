<?php
/**
 * Client web revisions 1.21 (October 28, 2026): Home in the main menu, the quote
 * page without the quick-call bar and with ZIP codes, and the Gardena facility
 * named "Pelham Services Gardena" (without "/ Los Angeles") across the network.
 * Only the changed sentences are replaced, in English and Spanish; URLs, titles
 * and keyword headings stay the same. Explicit, backed-up update from Pelham
 * Company; never runs on a public request.
 */
if (!defined('ABSPATH')) exit;
const PELHAM_REVISIONS_121='1.21.0';
function pelham_revisions_121_routes(){return ['quote','about','locations','locations/gardena-ca','services/freight-consolidation'];}
/** Old sentence => new sentence, English and Spanish. */
function pelham_revisions_121_pairs(){return [
 'Our operated facilities are in Gardena / Los Angeles,'=>'Our operated facilities are in Gardena,',
 'Our Los Angeles facility is in <a'=>'Our California facility is in <a',
 'Pelham operates facilities in Los Angeles (Gardena), Orlando and Miami'=>'Pelham operates facilities in Gardena (California), Orlando and Miami',
 'Pelham operates facilities in Los Angeles, Orlando and Miami.'=>'Pelham operates facilities in Gardena (California), Orlando and Miami.',
 'Pelham’s Los Angeles facility is in Gardena, California.'=>'Pelham’s California facility is in Gardena.',
 'Pelham Services Los Angeles is located in Gardena at'=>'Pelham Services Gardena is located at',
 'provide the pickup and receiving city/state/ZIP.'=>'provide the pickup and receiving ZIP codes.',
 'Nuestras instalaciones propias están en Gardena / Los Angeles,'=>'Nuestras instalaciones propias están en Gardena,',
 'Nuestra instalación de Los Angeles está en'=>'Nuestra instalación de California está en',
 'Pelham opera instalaciones en Los Angeles (Gardena), Orlando y Miami'=>'Pelham opera instalaciones en Gardena (California), Orlando y Miami',
 'Pelham opera instalaciones en Los Angeles, Orlando y Miami.'=>'Pelham opera instalaciones en Gardena (California), Orlando y Miami.',
 'La instalación de Pelham en Los Angeles está en Gardena, California.'=>'La instalación de Pelham en California está en Gardena.',
 'Pelham Services Los Angeles está ubicada en Gardena, en'=>'Pelham Services Gardena está ubicada en',
 'indique la ciudad/estado/ZIP de recogida y de recepción'=>'indique los códigos postales (ZIP) de recogida y de recepción',
];}
function pelham_revisions_121_text($content){
 $content=strtr($content,pelham_revisions_121_pairs());
 return preg_replace('/<!-- wp:shortcode -->\s*\[pelham_quick_calls\]\s*<!-- \/wp:shortcode -->\s*/','',$content);
}
function pelham_revisions_121_title($title){return in_array($title,['Pelham Services Gardena / Los Angeles','Pelham Services Los Angeles'],true)?'Pelham Services Gardena':$title;}
function pelham_revisions_121_meta($text){return strtr($text,['Gardena, Los Angeles:'=>'Gardena, California:','in Los Angeles, Orlando and Miami'=>'in Gardena, Orlando and Miami','en Los Angeles, Orlando y Miami'=>'en Gardena, Orlando y Miami']);}
function pelham_revisions_121_posts(){
 $posts=[];
 foreach(pelham_revisions_121_routes() as $route){
  $english=pelham_i18n_english_post($route);if(!$english)continue;
  $posts[]=$english;$spanish=pelham_translation($english,'es');if($spanish)$posts[]=$spanish;
 }
 return $posts;
}
/** Primary menu without a link to the home page, or 0. */
function pelham_revisions_121_menu(){
 $menu=get_nav_menu_locations()['primary']??0;if(!$menu)return 0;
 $home=untrailingslashit(home_url('/'));$front=(int)get_option('page_on_front');
 foreach(wp_get_nav_menu_items($menu)?:[] as $item)if(($front&&(int)$item->object_id===$front)||untrailingslashit($item->url)===$home)return 0;
 return $menu;
}
function pelham_revisions_121_pending(){
 if(pelham_revisions_121_menu())return true;
 foreach(pelham_revisions_121_posts() as $post)if(pelham_revisions_121_text($post->post_content)!==$post->post_content||pelham_revisions_121_title($post->post_title)!==$post->post_title)return true;
 return false;
}
function pelham_apply_revisions_121(){
 if(!current_user_can('manage_options'))return new WP_Error('forbidden','Administrator access required.');
 $updated=0;
 foreach(pelham_revisions_121_posts() as $post){
  $content=pelham_revisions_121_text($post->post_content);$title=pelham_revisions_121_title($post->post_title);
  if($content!==$post->post_content||$title!==$post->post_title){
   $backup=['title'=>$post->post_title,'content'=>$post->post_content,'time'=>current_time('mysql')];
   if(!add_post_meta($post->ID,'pelham_before_revisions_1_21',wp_slash($backup)))return new WP_Error('backup','Could not save a backup of '.$post->post_title.'. Nothing else was changed.');
   wp_save_post_revision($post->ID);
   $result=wp_update_post(wp_slash(['ID'=>$post->ID,'post_title'=>$title,'post_content'=>$content]),true);if(is_wp_error($result))return $result;
   $updated++;
  }
  $description=(string)get_post_meta($post->ID,'pelham_seo_description',true);
  if($description!==''&&pelham_revisions_121_meta($description)!==$description)update_post_meta($post->ID,'pelham_seo_description',pelham_revisions_121_meta($description));
 }
 // Home first in the English menu; the Spanish navigation adds it on its own.
 if($menu=pelham_revisions_121_menu()){
  foreach(wp_get_nav_menu_items($menu)?:[] as $item)wp_update_post(['ID'=>$item->ID,'menu_order'=>$item->menu_order+1]);
  $front=(int)get_option('page_on_front');
  $item=$front?['menu-item-object-id'=>$front,'menu-item-object'=>'page','menu-item-type'=>'post_type']:['menu-item-type'=>'custom','menu-item-url'=>home_url('/')];
  wp_update_nav_menu_item($menu,0,$item+['menu-item-title'=>'Home','menu-item-status'=>'publish','menu-item-position'=>1]);
 }
 update_option('pelham_revisions_121',PELHAM_REVISIONS_121,false);
 return $updated;
}
add_action('admin_post_pelham_apply_revisions_121',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_apply_revisions_121');
 $result=pelham_apply_revisions_121();
 set_transient('pelham_i18n_notice',is_wp_error($result)?$result->get_error_message():sprintf('Web revisions applied: %d pages updated in English and Spanish, and Home added to the menu. Previous text is saved in each page (pelham_before_revisions_1_21) and in its revisions.',$result),300);
 wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;
});
add_action('admin_notices',function(){
 if(!current_user_can('manage_options')||get_option('pelham_revisions_121')===PELHAM_REVISIONS_121)return;
 // New installs already have this content.
 if(!pelham_revisions_121_pending()){update_option('pelham_revisions_121',PELHAM_REVISIONS_121,false);return;}
 echo '<div class="notice notice-warning"><p><strong>Pelham:</strong> the client’s web revisions of October 28 are ready: Home in the main menu, the quote page without the call bar and with ZIP codes, and the Gardena facility named “Pelham Services Gardena”, in English and Spanish. Addresses and keyword headings stay the same.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_apply_revisions_121">';
 wp_nonce_field('pelham_apply_revisions_121');submit_button('Apply web revisions (save backups)','primary','submit',false);echo '</form></div>';
});
