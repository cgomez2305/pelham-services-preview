<?php
if(!defined('ABSPATH'))exit;
// Optional external processor: deliberately disabled until approved and configured by the site owner.
function pa_cloudmersive_configured(){return defined('PELHAM_CLOUDMERSIVE_APPROVED')&&PELHAM_CLOUDMERSIVE_APPROVED===true&&defined('PELHAM_CLOUDMERSIVE_API_KEY')&&is_string(PELHAM_CLOUDMERSIVE_API_KEY)&&strlen(PELHAM_CLOUDMERSIVE_API_KEY)>0;}
function pa_cloudmersive_request($bytes,$mime,$key){
 $pending=['status'=>'quarantined'];$extensions=['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
 if(!is_string($bytes)||!isset($extensions[$mime])||strlen($bytes)>2*MB_IN_BYTES||$bytes===''||!is_string($key)||$key===''||preg_match('/[\r\n]/',$key))return $pending;
 $boundary='Pelham'.bin2hex(random_bytes(24));
 // Use a neutral filename; transmit no company name, email or original filename.
 $body='--'.$boundary."\r\nContent-Disposition: form-data; name=\"inputFile\"; filename=\"attachment.".$extensions[$mime]."\"\r\nContent-Type: ".$mime."\r\n\r\n".$bytes."\r\n--".$boundary."--\r\n";
 $response=wp_safe_remote_post('https://api.cloudmersive.com/virus/scan/file/advanced',[
  'timeout'=>8,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>65536,
  'headers'=>['Apikey'=>$key,'Accept'=>'application/json','Content-Type'=>'multipart/form-data; boundary='.$boundary,'allowExecutables'=>'false','allowInvalidFiles'=>'false','allowScripts'=>'false','allowPasswordProtectedFiles'=>'false','allowMacros'=>'false','allowXmlExternalEntities'=>'false','allowInsecureDeserialization'=>'false','allowUnsafeArchives'=>'false','allowOleEmbeddedObject'=>'false','allowUnwantedAction'=>'false','restrictFileTypes'=>'.pdf,.jpg,.jpeg,.png,.webp'],
  'body'=>$body,'data_format'=>'body']);
 if(is_wp_error($response)||wp_remote_retrieve_response_code($response)!==200)return $pending;
 $data=json_decode(wp_remote_retrieve_body($response),true);
 if(!is_array($data)||!array_key_exists('CleanResult',$data)||!is_bool($data['CleanResult']))return $pending;
 if($data['CleanResult']===false)return ['status'=>'infected'];
 // A contradictory or incomplete response never releases a file.
 if(!isset($data['FoundViruses'])||!is_array($data['FoundViruses'])||count($data['FoundViruses'])!==0)return $pending;
 foreach(['ContainsExecutable','ContainsInvalidFile','ContainsScript','ContainsPasswordProtectedFile','ContainsRestrictedFileFormat','ContainsMacros','ContainsXmlExternalEntities','ContainsInsecureDeserialization','ContainsUnsafeArchive','ContainsOleEmbeddedObject','ContainsUnwantedAction'] as $flag)if(isset($data[$flag])&&$data[$flag]!==false)return $pending;
 return ['status'=>'clean'];
}
if(pa_cloudmersive_configured())add_filter('pelham_scan_attachment',function($result,$bytes,$mime){return pa_cloudmersive_request($bytes,$mime,PELHAM_CLOUDMERSIVE_API_KEY);},10,3);
