<?php
if(!defined('ABSPATH'))exit;
function pelham_home_seed_content($file){
 return preg_replace_callback('/href="(\/[^"#]*)"/',function($m){return 'href="'.esc_url(home_url($m[1])).'"';},file_get_contents(__DIR__.'/../data/'.$file));
}
function pelham_known_home_signature($content){
 $content=preg_replace('/https?:\/\/[^\/"\s]+(?=\/)/','',$content);
 return preg_replace('/\s+/u',' ',trim(serialize_blocks(parse_blocks($content))));
}
add_action('admin_init',function(){
 if(!current_user_can('manage_options'))return;
 $home=get_post((int)get_option('page_on_front'));if(!$home||str_contains($home->post_content,'pelham-services'))return;
 $old=pelham_home_seed_content('home-1.5.html');
 if(pelham_known_home_signature($home->post_content)!==pelham_known_home_signature($old)&&pelham_known_home_signature($home->post_content)!==pelham_known_home_signature(pelham_home_seed_content('home-1.6.html')))return;
 // Upgrade only an unchanged shipped starter. Custom WordPress copy is never replaced.
 wp_save_post_revision($home->ID);
 update_post_meta($home->ID,'pelham_home_before_1_6',$home->post_content);
 wp_update_post(['ID'=>$home->ID,'post_content'=>pelham_home_seed_content('home.html')]);
});
add_action('admin_notices',function(){
 if(!current_user_can('manage_options'))return;
 $home=get_post((int)get_option('page_on_front'));if(!$home||str_contains($home->post_content,'pelham-services'))return;
 if(get_current_screen()->id!=='toplevel_page_pelham-company')return;
 echo '<div class="notice notice-info"><p>Your customized homepage was preserved. The new industrial layout can be imported with a backup, or copied selectively from the starter blocks.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_upgrade_home">';wp_nonce_field('pelham_upgrade_home');submit_button('Apply industrial homepage (backup current copy)','secondary','submit',false);echo '</form><p>Other pages and company settings stay editable.</p></div>';
});
add_action('admin_post_pelham_upgrade_home',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_upgrade_home');
 $home=get_post((int)get_option('page_on_front'));if($home){wp_save_post_revision($home->ID);update_post_meta($home->ID,'pelham_home_before_1_6',$home->post_content);wp_update_post(['ID'=>$home->ID,'post_content'=>pelham_home_seed_content('home.html')]);}
 wp_safe_redirect(admin_url('admin.php?page=pelham-company'));exit;
});
