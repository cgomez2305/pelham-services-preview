<?php
if(!defined('ABSPATH'))exit;
function pelham_contact_seed(){foreach(json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true) as $entry){if($entry['route']==='contact')return $entry['content'];}return '';}
add_action('admin_init',function(){
 if(!current_user_can('manage_options'))return;
 $pages=get_posts(['post_type'=>'page','name'=>'contact','numberposts'=>1]);if(!$pages)return;$post=$pages[0];if(str_contains($post->post_content,'contact-v2'))return;
 $previous=json_decode(file_get_contents(__DIR__.'/../data/pages-before-1.7.json'),true);
 foreach($previous as $entry){if($entry['route']==='contact'&&pelham_known_home_signature($post->post_content)===pelham_known_home_signature($entry['content'])){wp_save_post_revision($post->ID);update_post_meta($post->ID,'pelham_contact_before_1_7',$post->post_content);wp_update_post(['ID'=>$post->ID,'post_content'=>pelham_contact_seed()]);break;}}
});
add_action('admin_notices',function(){
 if(!current_user_can('manage_options')||get_current_screen()->id!=='toplevel_page_pelham-company')return;
 $pages=get_posts(['post_type'=>'page','name'=>'contact','numberposts'=>1]);if(!$pages||str_contains($pages[0]->post_content,'contact-v2'))return;
 echo '<div class="notice notice-info"><p>Your custom contact copy was preserved. You can apply the new contact composition with a backup.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_upgrade_contact">';wp_nonce_field('pelham_upgrade_contact');submit_button('Apply refreshed contact page (backup current copy)','secondary','submit',false);echo '</form></div>';
});
add_action('admin_post_pelham_upgrade_contact',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_upgrade_contact');$pages=get_posts(['post_type'=>'page','name'=>'contact','numberposts'=>1]);
 if($pages){wp_save_post_revision($pages[0]->ID);update_post_meta($pages[0]->ID,'pelham_contact_before_1_7',$pages[0]->post_content);wp_update_post(['ID'=>$pages[0]->ID,'post_content'=>pelham_contact_seed()]);}
 wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;
});
add_action('admin_init',function(){
 if(!current_user_can('manage_options')||get_option('pelham_content_release')==='1.7.0')return;
 pelham_import_content();
 $privacy=get_posts(['post_type'=>'page','name'=>'privacy-notice','numberposts'=>1]);
 if($privacy&&!get_option('wp_page_for_privacy_policy'))update_option('wp_page_for_privacy_policy',$privacy[0]->ID);
 update_option('pelham_content_release','1.7.0');
});
