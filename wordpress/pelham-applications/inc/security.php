<?php
if(!defined('ABSPATH'))exit;
// A scanner adapter must examine these exact bytes; no external service receives data by default.
function pa_scan_attachment($bytes,$mime){
 try{$result=apply_filters('pelham_scan_attachment',['status'=>'quarantined'],$bytes,$mime);}catch(Throwable $e){$result=['status'=>'quarantined'];}
 if(!is_array($result)||!in_array($result['status']??'', ['clean','infected'],true))return ['status'=>'quarantined','sha256'=>hash('sha256',$bytes)];
 return ['status'=>$result['status'],'sha256'=>hash('sha256',$bytes),'checked_at'=>gmdate('c')];
}
function pa_attachment_clean($file){
 $bytes=base64_decode($file['base64']??'',true);$scan=$file['scan']??[];
 return $bytes!==false&&($scan['status']??'')==='clean'&&hash_equals(hash('sha256',$bytes),(string)($scan['sha256']??''));
}
add_action('admin_post_pa_scan',function(){
 if(!current_user_can('manage_options'))wp_die('Administrator access required.','',['response'=>403]);
 $id=absint($_POST['record']??0);check_admin_referer('pa_scan_'.$id);
 if(get_post_type($id)!=='pelham_application')wp_die('Not found.','',['response'=>404]);
 if(!pa_lock($id))wp_die('Please retry.','',['response'=>409]);
 try{$p=pa_get($id);foreach($p['files'] as &$file)$file['scan']=pa_scan_attachment(base64_decode($file['base64']),$file['mime']);unset($file);pa_put($id,$p);pa_audit($id,'Attachment scan requested by administrator');}finally{pa_unlock($id);}
 wp_safe_redirect(admin_url('admin.php?page=pelham-applications&record='.$id));exit;
});
add_action('template_redirect',function(){if(is_page(['register','claims'])){nocache_headers();header('Cache-Control: no-store, private, max-age=0');header('Referrer-Policy: no-referrer');header('X-Robots-Tag: noindex');}},1);
