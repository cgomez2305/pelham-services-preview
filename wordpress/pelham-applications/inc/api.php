<?php
if(!defined('ABSPATH'))exit;
function pa_origin_ok(){
 $origin=get_http_origin();if(!$origin)return true;
 $site=wp_parse_url(home_url());$expected=$site['scheme'].'://'.$site['host'].(isset($site['port'])?':'.$site['port']:'');return strtolower($origin)===strtolower($expected);
}
function pa_session(){
 $cookie=$_COOKIE['pelham_application']??'';if(!is_string($cookie)||!preg_match('/^(\d+)\.([a-f0-9]{64})$/D',$cookie,$m))return 0;
 $id=(int)$m[1];if(get_post_type($id)!=='pelham_application'||(int)get_post_meta($id,'_pa_session_expires',true)<time()||!hash_equals((string)get_post_meta($id,'_pa_session',true),pa_secret_hash($m[2])))return 0;return $id;
}
function pa_csrf(){return pa_secret_hash('csrf:'.($_COOKIE['pelham_application']??''));}
function pa_public_payload($id,$p){
 $signs=[];foreach($p['signatures']??[] as $key=>$s)$signs[$key]=['name'=>$s['name'],'method'=>$s['method'],'signed_at'=>$s['signed_at'],'image'=>'data:'.$s['image']['mime'].';base64,'.$s['image']['base64']];
 $files=[];foreach($p['files']??[] as $key=>$f)$files[$key]=['name'=>$f['name'],'size'=>$f['size'],'scan_status'=>pa_attachment_clean($f)?'clean':(($f['scan']['status']??'')==='infected'?'blocked':'pending')];
 return ['test'=>!empty($p['test']),'reference'=>pa_reference($id),'kind'=>$p['kind'],'status'=>get_post_meta($id,'_pa_status',true),'data'=>$p['data'],'data_hash'=>pa_fingerprint($p),'signatures'=>$signs,'files'=>$files,'documents'=>$p['documents'],'csrf'=>pa_csrf(),'correction'=>$p['correction']??'','saved_at'=>$p['saved_at']??null];
}
add_action('rest_api_init',function(){
 register_rest_route('pelham-applications/v1','/(?P<operation>start|exchange|session|save|sign|file|submit|logout)',[
  'methods'=>['GET','POST'],'permission_callback'=>'__return_true','callback'=>'pa_api']);
});
function pa_api(WP_REST_Request $r){
 if(!pa_origin_ok())return pa_error('This request must come from the Pelham website.',403);
 $op=$r['operation'];if(($op==='session'&&$r->get_method()!=='GET')||($op!=='session'&&$r->get_method()!=='POST'))return pa_error('Method not allowed.',405);
 if(strlen($r->get_body())>5000000)return pa_error('This request is too large.',413);
 $body=$r->get_json_params();if(!is_array($body))$body=[];
 foreach(['email','kind','website','token','slot','method','image','data_hash','name','file'] as $key)if(isset($body[$key])&&!is_string($body[$key]))return pa_error('Invalid request field.',400);
 $locked=0;
 try{
  if($op==='start'){
   if(!empty($body['website']))return pa_error('Unable to start this request.');
   $raw_email=$body['email']??'';if(!is_string($raw_email)||!pa_email_valid(trim($raw_email)))return pa_error('Enter a valid email address with a public domain.',400,['email'=>'Check your email address.']);$email=trim($raw_email);$kind=($body['kind']??'')==='claim'?'claim':'application';if(!is_email($email))return pa_error('Enter a valid email address.',400,['email'=>'Enter a valid email address.']);
   if(!pa_rate('start-ip:'.($_SERVER['REMOTE_ADDR']??''),12)||!pa_rate('start-email:'.strtolower($email),4))return pa_error('Please wait before requesting another link.',429);
   // Fail before creating records if encryption is not available.
   pa_encrypt(['check'=>true]);
   $ids=get_posts(['post_type'=>'pelham_application','post_status'=>'private','fields'=>'ids','numberposts'=>1,'meta_query'=>[['key'=>'_pa_email_hash','value'=>pa_secret_hash(strtolower($email))],['key'=>'_pa_kind','value'=>$kind],['key'=>'_pa_status','value'=>['draft','needs_information'],'compare'=>'IN']]]);
   $id=$ids[0]??wp_insert_post(['post_type'=>'pelham_application','post_status'=>'private','post_title'=>'Private Pelham request'],true);if(is_wp_error($id)||!$id)return pa_error('Unable to create the draft. Please try again.',503);
   if(!pa_lock($id))return pa_error('Please retry in a moment.',409);$locked=$id;
   if(!$ids){$p=['kind'=>$kind,'email'=>$email,'data'=>pa_clean_data($kind,['email'=>$email],$email),'documents'=>pa_documents(),'signatures'=>[],'files'=>[],'created_at'=>gmdate('c')];pa_put($id,$p);update_post_meta($id,'_pa_email_hash',pa_secret_hash(strtolower($email)));update_post_meta($id,'_pa_kind',$kind);update_post_meta($id,'_pa_status','draft');wp_update_post(['ID'=>$id,'post_title'=>pa_reference($id)]);}
   $token=bin2hex(random_bytes(32));update_post_meta($id,'_pa_resume_hash',pa_secret_hash($token));update_post_meta($id,'_pa_resume_expires',time()+30*MINUTE_IN_SECONDS);
   $url=home_url($kind==='claim'?'/claims/':'/register/').'#application-token='.$id.'.'.$token;
   pa_queue($id,'verify-'.bin2hex(random_bytes(6)),$email,'Pelham — continue your '.($kind==='claim'?'claim':'application'),"Use this private link to verify your email and continue. It expires in 30 minutes and can be used once.\n\n".$url."\n\nIf you did not request this link, you can ignore it.");
   return ['message'=>'Check your email for a private link to continue. If delivery is delayed, you can request another link.'];
  }
  if($op==='exchange'){
   if(!pa_rate('exchange:'.($_SERVER['REMOTE_ADDR']??''),30))return pa_error('Please try again later.',429);
   if(!preg_match('/^(\d+)\.([a-f0-9]{64})$/D',(string)($body['token']??''),$m))return pa_error('This link is invalid or expired.',403);$id=(int)$m[1];
   if(!pa_lock($id))return pa_error('Please try again.',409);$locked=$id;
   if(get_post_type($id)!=='pelham_application'||(int)get_post_meta($id,'_pa_resume_expires',true)<time()||!hash_equals((string)get_post_meta($id,'_pa_resume_hash',true),pa_secret_hash($m[2])))return pa_error('This link is invalid or expired. Request a new link.',403);
   delete_post_meta($id,'_pa_resume_hash');delete_post_meta($id,'_pa_resume_expires');
   $secret=bin2hex(random_bytes(32));$cookie=$id.'.'.$secret;update_post_meta($id,'_pa_session',pa_secret_hash($secret));update_post_meta($id,'_pa_session_expires',time()+DAY_IN_SECONDS);
   setcookie('pelham_application',$cookie,['expires'=>time()+DAY_IN_SECONDS,'path'=>'/','secure'=>is_ssl(),'httponly'=>true,'samesite'=>'Strict']);$_COOKIE['pelham_application']=$cookie;
   $p=pa_get($id);return pa_public_payload($id,$p);
  }
  $id=pa_session();if($id&&$op!=='session'&&!pa_rate('session-write:'.$id,600))return pa_error('Too many updates. Please wait before trying again.',429);if(!$id)return pa_error('Verify your email to open or resume your request.',401);
  if($op!=='session'&&!hash_equals(pa_csrf(),(string)$r->get_header('X-Pelham-CSRF')))return pa_error('Your session needs to be refreshed.',403);
  if($op==='logout'){delete_post_meta($id,'_pa_session');setcookie('pelham_application','',['expires'=>time()-3600,'path'=>'/','secure'=>is_ssl(),'httponly'=>true,'samesite'=>'Strict']);return ['ok'=>true];}
  if($op==='session')return pa_public_payload($id,pa_get($id));
  if(!pa_lock($id))return pa_error('A save is already in progress. Try again.',409);$locked=$id;$p=pa_get($id);$status=get_post_meta($id,'_pa_status',true);
  if(!in_array($status,['draft','needs_information'],true))return $op==='submit'?pa_public_payload($id,$p):pa_error('This request has already been submitted and is locked for review.',409);
  if($op==='save'){
   $input_errors=pa_input_errors($p['kind'],$body['data']??null);if($input_errors)return pa_error('Check the highlighted fields.',422,$input_errors);
   $data=pa_clean_data($p['kind'],is_array($body['data']??null)?$body['data']:[],$p['email']);
   if(pa_hash($data)!==pa_hash($p['data'])){$p['signatures']=[];$p['data']=$data;}
  }elseif($op==='sign'){
   if(!hash_equals(pa_fingerprint($p),(string)($body['data_hash']??'')))return pa_error('Your information changed. Save it and sign again.',409);
   $errors=pa_validate($p);if($errors)return pa_error('Complete the required information before signing.',422,$errors);
   $slot=sanitize_key($body['slot']??'');$needed=pa_signatures_needed($p);if(!isset($needed[$slot]))return pa_error('Unknown document.');
   $image=pa_image($body['image']??'');if(!$image||!in_array($body['method']??'',['draw','upload','reuse'],true)||empty($body['confirm']))return pa_error('Draw or upload a valid signature and confirm your intention to sign.');
   $spec=$needed[$slot];$p['signatures'][$slot]=['name'=>$p['data'][$spec['name']],'title'=>$spec['title']?$p['data'][$spec['title']]:'','method'=>$body['method'],'image'=>$image,'signed_at'=>gmdate('c'),'data_hash'=>pa_fingerprint($p),'document_sha256'=>$p['documents'][$spec['doc']]['sha256']];
  }elseif($op==='file'){
   if($p['kind']!=='claim')return pa_error('Attachments are only requested for a claim.');
   $slot=sanitize_key($body['slot']??'');if(!in_array($slot,['invoice','repairs','photos'],true))return pa_error('Unknown attachment.');
   if(!empty($body['remove']))unset($p['files'][$slot]);else{
    $raw=$body['file']??'';if(!is_string($raw)||!preg_match('#^data:(application/pdf|image/jpeg|image/png|image/webp);base64,([A-Za-z0-9+/=]+)$#D',$raw,$m))return pa_error('Use PDF, JPG, PNG or WebP.');
    $bytes=base64_decode($m[2],true);if(!$bytes||strlen($bytes)>2*MB_IN_BYTES)return pa_error('Keep each attachment under 2 MB.');
    if($m[1]==='application/pdf'){if(substr($bytes,0,5)!=='%PDF-')return pa_error('Invalid PDF.');}else{$info=@getimagesizefromstring($bytes);if(!$info||$info['mime']!==$m[1]||$info[0]>12000||$info[1]>12000||$info[0]*$info[1]>24000000)return pa_error('Invalid image.');}
    $scan=pa_scan_attachment($bytes,$m[1]);if($scan['status']==='infected')return pa_error('This file was rejected by the security scanner.',422);
    $p['files'][$slot]=['scan'=>$scan,'name'=>sanitize_file_name($body['name']??'attachment'),'mime'=>$m[1],'size'=>strlen($bytes),'base64'=>base64_encode($bytes),'sha256'=>hash('sha256',$bytes)];
   }$p['signatures']=[];
  }elseif($op==='submit'){
   $errors=pa_validate($p);foreach(pa_signatures_needed($p) as $slot=>$spec)if(empty($p['signatures'][$slot])||!hash_equals(pa_fingerprint($p),$p['signatures'][$slot]['data_hash']))$errors['signature_'.$slot]='Sign the '.$spec['label'].'.';
   if($errors)return pa_error('Please complete the highlighted items before sending.',422,$errors);
   $rev=(int)get_post_meta($id,'_pa_revision',true)+1;$p['submitted_at']=gmdate('c');$p['revision']=$rev;$p['correction']='';
   pa_put($id,$p);pa_put($id,$p,'_pa_revision_'.$rev);update_post_meta($id,'_pa_revision',$rev);update_post_meta($id,'_pa_status','received');
   $company=$p['data']['company']??$p['data']['contact_name'];wp_update_post(['ID'=>$id,'post_title'=>pa_reference($id).' — '.$company]);
   pa_audit($id,'Customer submitted revision '.$rev);
   $summary=pa_reference($id)."\n".$company."\nContact: ".$p['data']['contact_name']."\nEmail: ".$p['email']."\nPhone: ".$p['data']['phone']."\nCredit requested: ".($p['data']['request_credit']??'Not applicable')."\n\nReview the private record (sign-in required):\n".admin_url('admin.php?page=pelham-applications&record='.$id);
   pa_queue($id,'sales-'.$rev,pa_sales_email(),'Pelham — new '.$p['kind'].' '.pa_reference($id),$summary);
   pa_queue($id,'receipt-'.$rev,$p['email'],'Pelham — request received '.pa_reference($id),"We received your ".$p['kind'].". Reference: ".pa_reference($id).". Our team will review the information and contact you. This does not automatically approve credit, a claim, or customer portal access.");
  }
  $p['saved_at']=gmdate('c');pa_put($id,$p);wp_update_post(['ID'=>$id,'post_title'=>pa_reference($id).' — '.($p['data']['company']??$p['data']['contact_name']??''),'post_modified'=>current_time('mysql'),'post_modified_gmt'=>current_time('mysql',true)]);
  return pa_public_payload($id,$p);
 }catch(Throwable $e){return pa_error('We could not complete this operation. Your previously saved information is retained. Please retry or contact Pelham.',503);}
 finally{if($locked)pa_unlock($locked);}
}
// Keep personalized API responses out of shared caches.
add_filter('rest_post_dispatch',function($response,$server,$request){if(str_starts_with($request->get_route(),'/pelham-applications/')){$response=rest_ensure_response($response);$response->header('Cache-Control','no-store, private, max-age=0');$response->header('X-Robots-Tag','noindex');$response->header('Referrer-Policy','no-referrer');}return $response;},10,3);
