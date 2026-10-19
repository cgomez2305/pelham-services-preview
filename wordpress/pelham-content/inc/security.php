<?php
if(!defined('ABSPATH'))exit;
add_filter('wp_headers',function($headers){
 $headers['X-Content-Type-Options']='nosniff';
 $headers['X-Frame-Options']='SAMEORIGIN';
 $headers['Referrer-Policy']='strict-origin-when-cross-origin';
 $headers['Permissions-Policy']='camera=(), microphone=(), geolocation=()';
 // Preserve WordPress inline scripts while enforcing framing, base and object restrictions.
 $headers['Content-Security-Policy']="base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'";
 $headers['Content-Security-Policy-Report-Only']="script-src 'self'; object-src 'none'; base-uri 'self'";
 return $headers;
});
function pelham_enquiry_input_errors($input){
 $limits=['name'=>150,'email'=>254,'company'=>250,'phone'=>40,'origin'=>250,'destination'=>250,'ltl_level'=>20,'service'=>250,'freight'=>250,'message'=>5000,'kind'=>12,'pelham_nonce'=>32,'website'=>250,'return_url'=>2000,'consent'=>10];$errors=[];
 foreach($limits as $key=>$limit){if(!isset($input[$key]))continue;$v=$input[$key];if(!is_string($v)||strlen($v)>$limit||preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/',$v))$errors[]=$key;}
 if(isset($input['kind'])&&(!is_string($input['kind'])||!in_array($input['kind'],['contact','quote'],true)))$errors[]='kind';
 return array_unique($errors);
}
// This lock is atomic across workers, unlike an ordinary transient increment.
function pelham_enquiry_allow(){
 $key='pelham_enquiry_lock_'.hash_hmac('sha256',$_SERVER['REMOTE_ADDR']??'',wp_salt());
 $until=(int)get_option($key);if($until&&$until<time())delete_option($key);
 if(!add_option($key,time()+120,'','no'))return false;
 wp_schedule_single_event(time()+130,'pelham_security_unlock',[$key]);return true;
}
add_action('pelham_security_unlock',function($key){if(str_starts_with($key,'pelham_enquiry_lock_')&&(int)get_option($key)<time())delete_option($key);});
