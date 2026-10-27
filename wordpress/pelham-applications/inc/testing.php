<?php
if(!defined('ABSPATH'))exit;
// Test requests are isolated synthetic records (TEST- reference, no emails, never a customer's draft).
// Administrators can always create them; a client can create them through a secret, expiring testing link.

/** Client testing link: ['hash','token'(encrypted),'expires']. */
function pa_test_link(){$o=get_option('pa_test_link',[]);return is_array($o)?$o:[];}
function pa_test_link_active(){$o=pa_test_link();return !empty($o['hash'])&&(int)($o['expires']??0)>time();}
function pa_test_token_valid($token){$o=pa_test_link();return is_string($token)&&preg_match('/^[a-f0-9]{48}$/',$token)&&pa_test_link_active()&&hash_equals($o['hash'],pa_secret_hash('test-link|'.$token));}
/** The valid testing token of this request, or ''. */
function pa_test_request_token(){$token=sanitize_key(wp_unslash($_REQUEST['pa_test']??''));return pa_test_token_valid($token)?$token:'';}
/** Plain testing token, for the administrator who shares the link. */
function pa_test_link_token(){$o=pa_test_link();if(!pa_test_link_active()||empty($o['token']))return '';try{return (string)(pa_decrypt($o['token'])['t']??'');}catch(Throwable $e){return '';}}

function pa_test_controls(){
 $admin=current_user_can('manage_options');$token=$admin?'':pa_test_request_token();
 if(!$admin&&!$token)return '';
 $title=$admin?'Administrator testing · no SMTP needed':'Test mode';
 $text=$admin?'Create a separate test request. Use fictitious data: no emails are sent for these records. This does not verify email delivery.':'Create a test request without verifying an email address. Use fictitious data: test requests are marked TEST, no emails are sent and the team can delete them.';
 $return=esc_url_raw(home_url(wp_unslash($_SERVER['REQUEST_URI']??'/')));
 $html='<section class="pa-test-panel"><strong>'.esc_html($title).'</strong><p>'.esc_html($text).'</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pa_test_start"><input type="hidden" name="return" value="'.esc_attr($return).'">';
 $html.=$admin?wp_nonce_field('pa_test_start','_wpnonce',true,false):'<input type="hidden" name="pa_test" value="'.esc_attr($token).'">';
 foreach(pa_kinds() as $kind=>$label)$html.='<button type="submit" name="kind" value="'.esc_attr($kind).'">'.esc_html(['customer'=>'Test Customer Application','credit'=>'Test Credit Application','terms'=>'Test Terms and Agreements','claim'=>'Test Claim Form'][$kind]).'</button> ';
 $html.='</form></section>';
 return $html;
}
function pa_test_start(){
 $admin=current_user_can('manage_options');$token=$admin?'':pa_test_request_token();
 if($admin)check_admin_referer('pa_test_start');
 elseif(!$token)wp_die(esc_html(pa_t('This testing link has expired or is not valid. Ask the Pelham team for a new link.')),'',['response'=>403]);
 if(!$admin&&!pa_rate('test-ip:'.($_SERVER['REMOTE_ADDR']??''),20))wp_die(esc_html(pa_t('Please wait before requesting another link.')),'',['response'=>429]);
 $kind=sanitize_key($_POST['kind']??'');if(!isset(pa_kinds()[$kind]))$kind='customer';
 // Open the matching page (application or claim) in the language the test started from, keeping the testing link.
 $from=(string)wp_parse_url(esc_url_raw(wp_unslash($_POST['return']??'')),PHP_URL_PATH);$route=$kind==='claim'?'claims':'register';
 $routes=function_exists('pelham_i18n_routes')?pelham_i18n_routes():[];
 $return=str_contains($from,'/es/')&&!empty($routes[$route])?home_url('/'.$routes[$route].'/'):home_url('/'.$route.'/');
 if($kind!=='claim')$return=add_query_arg('form',$kind,$return);
 if($token)$return=add_query_arg('pa_test',$token,$return);
 try{
  pa_encrypt(['check'=>true]);
  $email='test-'.bin2hex(random_bytes(6)).'@example.invalid';
  $id=wp_insert_post(['post_type'=>'pelham_application','post_status'=>'private','post_title'=>'TEST — private request'],true);
  if(is_wp_error($id)||!$id)throw new RuntimeException('Unable to create test');
  update_post_meta($id,'_pa_test',1);if(!$admin)update_post_meta($id,'_pa_test_client',1);
  if(function_exists('pelham_lang')&&str_contains((string)wp_parse_url($return,PHP_URL_PATH),'/es/'))update_post_meta($id,'_pa_lang','es');
  $p=['test'=>true,'kind'=>$kind,'email'=>$email,'data'=>pa_clean_data($kind,['email'=>$email],$email),'documents'=>pa_documents(),'signatures'=>[],'files'=>[],'created_at'=>gmdate('c')];
  pa_put($id,$p);update_post_meta($id,'_pa_kind',$kind);update_post_meta($id,'_pa_status','draft');
  $resume=bin2hex(random_bytes(32));update_post_meta($id,'_pa_resume_hash',pa_secret_hash($resume));update_post_meta($id,'_pa_resume_expires',time()+30*MINUTE_IN_SECONDS);
  wp_safe_redirect($return.'#application-token='.$id.'.'.$resume);exit;
 }catch(Throwable $e){wp_die('Unable to create the test. Check encryption and database configuration.','',['response'=>503]);}
}
add_action('admin_post_pa_test_start','pa_test_start');
add_action('admin_post_nopriv_pa_test_start','pa_test_start');

/** Administrator panel: create, show or disable the client testing link, and delete test requests. */
function pa_test_link_panel(){
 if(!current_user_can('manage_options'))return '';
 $tests=get_posts(['post_type'=>'pelham_application','post_status'=>'private','numberposts'=>-1,'fields'=>'ids','meta_key'=>'_pa_test','meta_value'=>1]);
 $html='<details class="pa-admin-panel pa-settings"'.(pa_test_link_active()?' open':'').'><summary>Client testing link</summary><p>Share these private links so a client can try the four forms (Customer Application, Credit Application, Terms and Agreements and Claim Form) without an email check. Test requests are marked TEST and send no emails. The link expires automatically.</p>';
 $token=pa_test_link_token();
 if($token){
  $links=['English · Customer, Credit and Terms forms'=>add_query_arg('pa_test',$token,home_url('/register/')),'English · Claim Form'=>add_query_arg('pa_test',$token,home_url('/claims/'))];
  if(function_exists('pelham_i18n_routes')){$routes=pelham_i18n_routes();foreach(['register'=>'Spanish · Customer, Credit and Terms forms','claims'=>'Spanish · Claim Form'] as $route=>$label)if(!empty($routes[$route]))$links[$label]=add_query_arg('pa_test',$token,home_url('/'.$routes[$route].'/'));}
  $html.='<ul>';foreach($links as $label=>$url)$html.='<li>'.esc_html($label).': <input type="text" readonly class="large-text code" value="'.esc_attr($url).'" onfocus="this.select()"></li>';$html.='</ul>';
  $html.='<p>Active until <strong>'.esc_html(wp_date(get_option('date_format').' '.get_option('time_format'),(int)pa_test_link()['expires'])).'</strong>.</p>';
 }
 $html.='<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pa_test_link">'.wp_nonce_field('pa_test_link','_wpnonce',true,false);
 $html.=$token?'<button class="button" name="op" value="disable">Disable link</button> <button class="button" name="op" value="create">Replace link (14 days)</button>':'<button class="button button-primary" name="op" value="create">Create link (14 days)</button>';
 if($tests)$html.=' <button class="button" name="op" value="purge" onclick="return confirm(\'Delete all test requests?\')">Delete '.count($tests).' test request'.(count($tests)===1?'':'s').'</button>';
 return $html.'</form></details>';
}
add_action('admin_post_pa_test_link',function(){
 if(!current_user_can('manage_options'))wp_die('Not authorized.',403);check_admin_referer('pa_test_link');
 $op=sanitize_key($_POST['op']??'');
 if($op==='create'){$token=bin2hex(random_bytes(24));update_option('pa_test_link',['hash'=>pa_secret_hash('test-link|'.$token),'token'=>pa_encrypt(['t'=>$token]),'expires'=>time()+14*DAY_IN_SECONDS],false);}
 elseif($op==='disable')delete_option('pa_test_link');
 elseif($op==='purge')foreach(get_posts(['post_type'=>'pelham_application','post_status'=>'private','numberposts'=>-1,'fields'=>'ids','meta_key'=>'_pa_test','meta_value'=>1]) as $id)wp_delete_post($id,true);
 wp_safe_redirect(admin_url('admin.php?page=pelham-applications&saved=1'));exit;
});
// Pages opened with a testing link are private to the client: never index or cache them.
add_action('template_redirect',function(){if(!empty($_GET['pa_test'])){nocache_headers();header('X-Robots-Tag: noindex, nofollow',true);if(!defined('DONOTCACHEPAGE'))define('DONOTCACHEPAGE',true);}});
