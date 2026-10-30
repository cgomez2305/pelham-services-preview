<?php
/**
 * Plugin Name: Pelham Applications
 * Description: Private digital account applications, credit, transport agreements and freight claims with drawn or uploaded signatures.
 * Version: 1.4.4
 * Requires PHP: 8.0
 * Requires at least: 6.4
 * License: GPL-2.0-or-later
 */
if (!defined('ABSPATH')) exit;
define('PA_DIR', __DIR__);
define('PA_VERSION', '1.4.4');
require_once PA_DIR.'/inc/core.php';
require_once PA_DIR.'/inc/validation.php';
require_once PA_DIR.'/inc/security.php';
require_once PA_DIR.'/inc/cloudmersive.php';
require_once PA_DIR.'/inc/api.php';
require_once PA_DIR.'/inc/forms.php';
require_once PA_DIR.'/inc/admin.php';
require_once PA_DIR.'/inc/pdf.php';
require_once PA_DIR.'/inc/pdf-signature.php';
require_once PA_DIR.'/inc/pdf-original.php';
require_once PA_DIR.'/inc/admin-documents.php';
require_once PA_DIR.'/inc/testing.php';
register_activation_hook(__FILE__, 'pa_activate');
register_deactivation_hook(__FILE__, function(){ wp_clear_scheduled_hook('pa_cleanup'); });
add_action('init', function(){
 register_post_type('pelham_application',['public'=>false,'publicly_queryable'=>false,'show_ui'=>false,'show_in_rest'=>false,'exclude_from_search'=>true,'rewrite'=>false,'supports'=>[]]);
});
function pa_activate(){
 get_role('administrator')?->add_cap('pelham_review_applications');
 add_role('pelham_sales','Pelham Sales',['read'=>true,'pelham_review_applications'=>true]);
 foreach(['register'=>'Apply for a Pelham account','claims'=>'Submit a freight claim'] as $slug=>$title){
  if(!get_page_by_path($slug))wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_name'=>$slug,'post_title'=>$title,'post_content'=>$slug==='claims'?'[pelham_claim_form]':'[pelham_digital_application]']);
 }
 if(!wp_next_scheduled('pa_cleanup'))wp_schedule_event(time()+3600,'daily','pa_cleanup');
}
// Replace the legacy registration at render time, preserving the page in the database.
add_filter('the_content',function($content){
 if(!is_admin()&&pa_is_page('register')&&in_the_loop()&&is_main_query())return pa_form('application');
 return $content;
},30);
add_filter('body_class',function($classes){if(pa_is_page(['register','claims']))$classes[]='pelham-digital-onboarding';return $classes;});
add_action('init',function(){
 add_shortcode('pelham_digital_application',fn()=>pa_form('application'));
 add_shortcode('pelham_claim_form',fn()=>pa_form('claim'));
 // The old theme fallback must not append a second form on /register/.
 add_shortcode('pelham_registration_form',fn()=>pa_is_page('register')?'':pa_form('application'));
 add_shortcode('pelham_registration_documents',fn()=>'<p>'.pa_t('Complete your <a href="'.esc_url(pa_link('register')).'">account application online</a>. For an existing shipment issue, <a href="'.esc_url(pa_link('claims')).'">submit a claim</a>.').'</p>');
},100);
// Disable the old public upload handler while digital onboarding is active.
add_action('admin_post_nopriv_pelham_registration','pa_legacy_redirect',-100);
add_action('admin_post_pelham_registration','pa_legacy_redirect',-100);
function pa_legacy_redirect(){wp_safe_redirect(home_url('/register/'));exit;}

add_filter('the_title',function($title,$id){if(!is_admin()&&pa_is_page('register')&&in_the_loop()&&is_main_query()&&(int)$id===(int)get_queried_object_id())return pa_t('Apply for a Pelham account');return $title;},20,2);

add_action('init',function(){add_shortcode('pelham_documents',fn()=>'<ul class="document-links"><li><a href="'.esc_url(pa_link('register')).'">'.esc_html(pa_t('Complete your account and credit application online')).' →</a></li><li><a href="'.esc_url(pa_link('claims')).'">'.esc_html(pa_t('Submit a freight claim online')).' →</a></li></ul>');},101);
add_filter('the_content',function($content){
 if(!is_admin()&&pa_is_page('cookie-policy')&&in_the_loop()&&is_main_query())$content.='<h2>'.esc_html(pa_t('Digital application session')).'</h2><p>'.esc_html(pa_t('After you verify your email, an essential HttpOnly session cookie keeps your application or claim private while you complete it. It expires after 24 hours or when you sign out. This cookie is not used for advertising or analytics.')).'</p>';
 return $content;
},40);
