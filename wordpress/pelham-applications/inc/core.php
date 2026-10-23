<?php
if(!defined('ABSPATH'))exit;
function pa_schema($kind){static $schema; $schema??=json_decode(file_get_contents(PA_DIR.'/inc/schema.json'),true);return $schema[$kind]??[];}
/** Interface text in the visitor's language (Pelham Content's dictionary). */
function pa_t($text){return function_exists('pelham_t')?pelham_t($text):$text;}
/** Page check that also matches the Spanish version of a page. */
function pa_is_page($slugs){return function_exists('pelham_is_page')?pelham_is_page($slugs):is_page($slugs);}
function pa_link($route){return function_exists('pelham_link')?pelham_link($route):home_url('/'.trim($route,'/').'/');}
/** Language a request was started in; emails to the applicant follow it. */
function pa_request_lang($id){return get_post_meta($id,'_pa_lang',true)==='es'?'es':'en';}
function pa_t_for($text,$lang){
 if($lang!=='es'||!function_exists('pelham_t'))return $text;
 $previous=$GLOBALS['pelham_lang_override']??null;$GLOBALS['pelham_lang_override']='es';$out=pelham_t($text);
 if($previous===null)unset($GLOBALS['pelham_lang_override']);else $GLOBALS['pelham_lang_override']=$previous;return $out;
}
/** Public page of a request type in a language (Spanish registration page when it exists). */
function pa_page_url($kind,$lang){
 $route=$kind==='claim'?'claims':'register';
 if($lang==='es'){$ids=get_option('pelham_i18n_ids',[]);if(!empty($ids[$route])&&get_post_status($ids[$route])==='publish')return get_permalink($ids[$route]);}
 return home_url($kind==='claim'?'/claims/':'/register/');
}
function pa_error($message,$status=400,$fields=[]){return new WP_Error('pelham_application',$message,['status'=>$status,'fields'=>$fields]);}
function pa_key(){return hash('sha256',defined('PELHAM_APPLICATIONS_KEY')?PELHAM_APPLICATIONS_KEY:wp_salt('auth'),true);}
function pa_encrypt($value){
 if(!function_exists('openssl_encrypt'))throw new RuntimeException('OpenSSL is required for private application storage.');
 $iv=random_bytes(12);$tag='';$cipher=openssl_encrypt(wp_json_encode($value), 'aes-256-gcm',pa_key(),OPENSSL_RAW_DATA,$iv,$tag,'pelham-applications',16);
 if($cipher===false)throw new RuntimeException('Private storage unavailable.');return base64_encode($iv.$tag.$cipher);
}
function pa_decrypt($value){
 $raw=base64_decode((string)$value,true);if(!$raw||strlen($raw)<29)return [];
 $json=openssl_decrypt(substr($raw,28),'aes-256-gcm',pa_key(),OPENSSL_RAW_DATA,substr($raw,0,12),substr($raw,12,16),'pelham-applications');
 if($json===false)throw new RuntimeException('Cannot unlock this record. Check the application encryption key.');return json_decode($json,true)?:[];
}
function pa_get($id,$key='_pa_payload'){return pa_decrypt(get_post_meta($id,$key,true));}
function pa_put($id,$payload,$key='_pa_payload'){update_post_meta($id,$key,wp_slash(pa_encrypt($payload)));}
function pa_hash($value){return hash('sha256',wp_json_encode($value));}
function pa_secret_hash($value){return hash_hmac('sha256',$value,wp_salt('secure_auth'));}
function pa_reference($id){return (get_post_meta($id,'_pa_test',true)?'TEST-':'PEL-').str_pad((string)$id,6,'0',STR_PAD_LEFT);}
function pa_can_review(){return current_user_can('pelham_review_applications');}
function pa_documents(){
 $manifest=json_decode(file_get_contents(PA_DIR.'/documents/manifest.json'),true);$out=[];
 foreach(['customer'=>'Customer-Application','credit'=>'Credit-Application','terms'=>'Terms-and-Agreements-Pelham','claim'=>'Claim-Form'] as $key=>$name)$out[$key]=['title'=>str_replace('-',' ',$name),'sha256'=>$manifest[$name]['sha256'],'text'=>file_get_contents(PA_DIR.'/documents/'.$name.'.txt')];
 return $out;
}
function pa_signatures_needed($p){
 if($p['kind']==='claim')return ['claim'=>['name'=>'claim_signer','title'=>null,'doc'=>'claim','label'=>'Freight claim']];
 $s=['customer'=>['name'=>'customer_signer','title'=>'customer_title','doc'=>'customer','label'=>'Business account application'],'terms'=>['name'=>'terms_signer','title'=>'terms_title','doc'=>'terms','label'=>'Transport terms and conditions']];
 if(($p['data']['request_credit']??'')==='Yes')$s+=['credit'=>['name'=>'credit_signer','title'=>'credit_title','doc'=>'credit','label'=>'Credit application'],'guarantee'=>['name'=>'guarantor','title'=>null,'doc'=>'credit','label'=>'Personal guaranty']];return $s;
}
function pa_clean_data($kind,$input,$email){
 $data=[];foreach(pa_schema($kind) as $f){$v=$input[$f['key']]??'';if(!is_scalar($v))$v='';$v=$f['type']==='checkbox'?(!empty($v)?'1':''):sanitize_textarea_field((string)$v);$data[$f['key']]=substr($v,0,$f['type']==='textarea'?5000:250);}
 $data['email']=$email;
 if($kind==='application'){
  if(!empty($data['billing_same']))foreach(['address','city','state','zip'] as $key)$data['billing_'.$key]=$data[$key];
  if(($data['request_credit']??'')!=='Yes')foreach(pa_schema($kind) as $f)if(($f['condition']??'')==='credit')$data[$f['key']]='';
 }
 return $data;
}
function pa_validate($p){
 $errors=[];$data=$p['data'];foreach(pa_schema($p['kind']) as $f){
  if(($f['condition']??'')==='credit'&&($data['request_credit']??'')!=='Yes')continue;
  $v=$data[$f['key']]??'';
  if($f['required']&&trim((string)$v)===''){$errors[$f['key']]=$f['label'].' is required.';continue;}
  if($v==='')continue;
  $format=pa_field_format_error($f,$v,!empty($p['test']));if($format)$errors[$f['key']]=$format;
  if($f['type']==='email'&&!is_email($v))$errors[$f['key']]='Enter a valid email address.';
  if($f['type']==='select'&&!in_array($v,$f['options'],true))$errors[$f['key']]='Choose an available option.';
  if($f['type']==='number'&&(!is_numeric($v)||!is_finite((float)$v)||(float)$v<0))$errors[$f['key']]='Enter a valid non-negative number.';
  if($f['type']==='date'){ $d=DateTime::createFromFormat('!Y-m-d',$v);if(!$d||$d->format('Y-m-d')!==$v||$v>gmdate('Y-m-d'))$errors[$f['key']]='Enter a valid date that is not in the future.'; }
 }
 if($p['kind']==='application'){
  if(($data['entity']??'')==='Other'&&empty($data['entity_other']))$errors['entity_other']='Describe the business structure.';
  if(($data['request_credit']??'')==='Yes')foreach([1,2] as $i)if(empty($data['ref'.$i.'_contact']))$errors['ref'.$i.'_contact']='A reference contact is required for credit.';
 }else{
  if(str_contains(strtolower($data['incident']??''),'loss')&&empty($data['lost_pieces']))$errors['lost_pieces']='Describe the lost pieces.';
  if(str_contains(strtolower($data['incident']??''),'damage')&&empty($data['damage']))$errors['damage']='Describe the extent of damage.';
  if(empty($p['files']['invoice']))$errors['invoice']='Attach the cost invoice supporting the claim value.';
  if((float)($data['claim_amount']??0)<=0)$errors['claim_amount']='Enter the claim amount.';
 }
 return $errors;
}
function pa_image($data){
 if(!is_string($data)||strlen($data)>1500000||!preg_match('#^data:image/(png|jpeg|webp);base64,([A-Za-z0-9+/=]+)$#D',$data,$m))return false;
 $bytes=base64_decode($m[2],true);$info=$bytes?@getimagesizefromstring($bytes):false;
 if(!$info||!in_array($info['mime'],['image/jpeg','image/png'],true)||strlen($bytes)>1000000||$info[0]>4096||$info[1]>4096||$info[0]<10||$info[1]<5)return false;
 return ['base64'=>base64_encode($bytes),'mime'=>$info['mime'],'sha256'=>hash('sha256',$bytes)];
}
function pa_lock($id){$key='pa_lock_'.$id;if(add_option($key,time()+40,'','no'))return true;$until=(int)get_option($key);if($until<time()){delete_option($key);return add_option($key,time()+40,'','no');}return false;}
function pa_unlock($id){delete_option('pa_lock_'.$id);}
function pa_rate($bucket,$limit=8){$key='pa_rate_'.pa_secret_hash($bucket);if(!pa_lock($key))return false;try{$n=(int)get_transient($key);if($n>=$limit)return false;set_transient($key,$n+1,15*MINUTE_IN_SECONDS);return true;}finally{pa_unlock($key);}}
function pa_sales_email(){ $o=get_option('pa_settings',[]);$legacy=get_option('pelham_company',[]);return sanitize_email($o['sales_email']??($legacy['recipient']??get_option('admin_email'))); }
function pa_job_option($id,$key){return 'pa_mail_'.(int)$id.'_'.sanitize_key($key);}
function pa_jobs($id){$out=[];foreach(get_post_meta($id,'_pa_job_keys',true)?:[] as $key){$value=get_option(pa_job_option($id,$key));if($value)$out[$key]=pa_decrypt($value);}return $out;}
function pa_queue($id,$key,$to,$subject,$body){
 if(get_post_meta($id,'_pa_test',true))return;
 $job=['to'=>$to,'subject'=>$subject,'body'=>$body,'attempts'=>0,'status'=>'pending','updated'=>gmdate('c')];
 if(!add_option(pa_job_option($id,$key),pa_encrypt($job),'','no'))return;
 $keys=get_post_meta($id,'_pa_job_keys',true)?:[];$keys[]=$key;update_post_meta($id,'_pa_job_keys',$keys);
 wp_schedule_single_event(time()+1,'pa_send_mail',[$id,$key]);
}
add_action('pa_send_mail',function($id,$key){
 if(!get_post($id)||!pa_lock('mail_'.$id.'_'.$key))return;
 try{$raw=get_option(pa_job_option($id,$key));$job=$raw?pa_decrypt($raw):null;if(!$job||$job['status']==='accepted')return;
  try{$sent=wp_mail($job['to'],$job['subject'],$job['body']);}catch(Throwable $e){$sent=false;}
  $job['attempts']++;$job['status']=$sent?'accepted':($job['attempts']>=5?'failed':'pending');$job['updated']=gmdate('c');update_option(pa_job_option($id,$key),pa_encrypt($job),false);
  if(!$sent&&$job['attempts']<5)wp_schedule_single_event(time()+min(3600,60*(2**$job['attempts'])),'pa_send_mail',[$id,$key]);
 }finally{pa_unlock('mail_'.$id.'_'.$key);}
},10,2);
add_action('before_delete_post',function($id){if(get_post_type($id)!=='pelham_application')return;foreach(get_post_meta($id,'_pa_job_keys',true)?:[] as $key){delete_option(pa_job_option($id,$key));wp_clear_scheduled_hook('pa_send_mail',[$id,$key]);}});
add_action('pa_cleanup',function(){
 $days=max(7,min(365,(int)(get_option('pa_settings',[])['draft_days']??30)));
 $ids=get_posts(['post_type'=>'pelham_application','post_status'=>'private','fields'=>'ids','numberposts'=>100,'date_query'=>[['column'=>'post_modified_gmt','before'=>gmdate('Y-m-d H:i:s',time()-$days*DAY_IN_SECONDS)]],'meta_query'=>[['key'=>'_pa_status','value'=>'draft']]]);
 foreach($ids as $id)wp_delete_post($id,true);
});
function pa_audit($id,$event){$entries=get_post_meta($id,'_pa_audit',true)?:[];$entries[]=['at'=>gmdate('c'),'user'=>get_current_user_id(),'event'=>$event];update_post_meta($id,'_pa_audit',array_slice($entries,-200));}

function pa_fingerprint($payload){$files=[];foreach($payload['files']??[] as $slot=>$file)$files[$slot]=$file['sha256'];return pa_hash(['data'=>$payload['data'],'files'=>$files,'documents'=>$payload['documents']]);}
