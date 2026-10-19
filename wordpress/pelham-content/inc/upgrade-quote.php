<?php
if(!defined('ABSPATH'))exit;
function pelham_quote_upgrade_data(){
 $entries=json_decode(file_get_contents(__DIR__.'/../data/pages.json'),true);
 foreach((array)$entries as $entry){if(($entry['route']??'')==='quote')return $entry;}
 return null;
}
add_action('admin_notices',function(){
 if(!current_user_can('manage_options')||get_current_screen()->id!=='toplevel_page_pelham-company')return;
 $entry=pelham_quote_upgrade_data();$post=get_page_by_path('quote');
 if(!$entry||!$post)return;
 if(pelham_18_signature($post->post_content)===pelham_18_signature($entry['content']))return;
 echo '<div class="notice notice-info"><p>The quote page still uses different content from the current starter layout. Apply the current quote page with a backup of your existing copy. The page URL, title and SEO settings are preserved.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_upgrade_quote">';
 wp_nonce_field('pelham_upgrade_quote');submit_button('Update quote page (save backup)','secondary','submit',false);echo '</form></div>';
});
add_action('admin_post_pelham_upgrade_quote',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);
 check_admin_referer('pelham_upgrade_quote');
 $entry=pelham_quote_upgrade_data();$post=get_page_by_path('quote');
 if(!$entry||!$post)wp_die('Quote page or starter content not found.');
 if(pelham_18_signature($post->post_content)!==pelham_18_signature($entry['content'])){
  wp_save_post_revision($post->ID);
  $backup=add_post_meta($post->ID,'pelham_before_quote_1_8_2',wp_slash(['content'=>$post->post_content,'title'=>$post->post_title,'status'=>$post->post_status,'time'=>current_time('mysql')]));
  if(!$backup)wp_die('The backup could not be saved. No content was replaced.');
  $result=wp_update_post(wp_slash(['ID'=>$post->ID,'post_content'=>pelham_18_seed($entry['content'])]),true);
  if(is_wp_error($result))wp_die(esc_html($result->get_error_message()));
 }
 wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;
});
