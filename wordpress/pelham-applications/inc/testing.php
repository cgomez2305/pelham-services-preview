<?php
if(!defined('ABSPATH'))exit;
// An authenticated administrator can create an isolated synthetic request, never open a customer's draft.
function pa_test_controls(){
 if(!current_user_can('manage_options'))return '';
 $html='<section class="pa-test-panel"><strong>Administrator testing · no SMTP needed</strong><p>Create a separate test request. Use fictitious data: no emails are sent for these records. This does not verify email delivery.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pa_test_start">'.wp_nonce_field('pa_test_start','_wpnonce',true,false).'<button type="submit" name="kind" value="application">Test account application</button> <button type="submit" name="kind" value="claim">Test freight claim</button></form></section>';
 return $html;
}
add_action('admin_post_pa_test_start',function(){
 if(!current_user_can('manage_options'))wp_die('Administrator access required.', '', ['response'=>403]);
 check_admin_referer('pa_test_start');
 $kind=($_POST['kind']??'')==='claim'?'claim':'application';
 try{
  pa_encrypt(['check'=>true]);
  $email='test-'.bin2hex(random_bytes(6)).'@example.invalid';
  $id=wp_insert_post(['post_type'=>'pelham_application','post_status'=>'private','post_title'=>'TEST — private request'],true);
  if(is_wp_error($id)||!$id)throw new RuntimeException('Unable to create test');
  update_post_meta($id,'_pa_test',1);
  $p=['test'=>true,'kind'=>$kind,'email'=>$email,'data'=>pa_clean_data($kind,['email'=>$email],$email),'documents'=>pa_documents(),'signatures'=>[],'files'=>[],'created_at'=>gmdate('c')];
  pa_put($id,$p);update_post_meta($id,'_pa_kind',$kind);update_post_meta($id,'_pa_status','draft');
  $token=bin2hex(random_bytes(32));update_post_meta($id,'_pa_resume_hash',pa_secret_hash($token));update_post_meta($id,'_pa_resume_expires',time()+30*MINUTE_IN_SECONDS);
  wp_safe_redirect(home_url($kind==='claim'?'/claims/':'/register/').'#application-token='.$id.'.'.$token);exit;
 }catch(Throwable $e){wp_die('Unable to create the test. Check encryption and database configuration.','',['response'=>503]);}
});
