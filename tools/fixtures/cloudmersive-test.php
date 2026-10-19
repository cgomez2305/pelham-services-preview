<?php
// Local-only QA. No requests leave WordPress; never distribute in the plugin.
add_action('wp_ajax_pa_cloudmersive_qa',function(){
 if(!current_user_can('manage_options'))wp_send_json_error('Denied',403);
 $clean=['response'=>['code'=>200],'body'=>'{"CleanResult":true,"FoundViruses":[]}'];
 $cases=[[$clean,'clean'],[['response'=>['code'=>200],'body'=>'{"CleanResult":false}'],'infected'],[['response'=>['code'=>200],'body'=>'{"CleanResult":"true","FoundViruses":[]}'],'quarantined'],[['response'=>['code'=>200],'body'=>'{"CleanResult":true}'],'quarantined'],[['response'=>['code'=>200],'body'=>'{"CleanResult":true,"FoundViruses":[],"ContainsScript":true}'],'quarantined'],[['response'=>['code'=>429],'body'=>'{}'],'quarantined'],[['response'=>['code'=>302],'body'=>'{}'],'quarantined'],[new WP_Error('timeout','Simulated timeout'),'quarantined'],[['response'=>['code'=>200],'body'=>'not-json'],'quarantined']];
 $passed=0;$transport=true;$bytes="%PDF-QA\0binary-data";
 foreach($cases as [$response,$expected]){
  $mock=function($pre,$args,$url)use($response,$bytes,&$transport){$transport=$transport&&$url==='https://api.cloudmersive.com/virus/scan/file/advanced'&&$args['sslverify']===true&&$args['redirection']===0&&$args['timeout']===8&&str_contains($args['body'],$bytes)&&$args['headers']['Apikey']==='LOCAL-FAKE-KEY'&&$args['limit_response_size']===65536;return $response;};
  add_filter('pre_http_request',$mock,10,3);try{$result=pa_cloudmersive_request($bytes,'application/pdf','LOCAL-FAKE-KEY');if($result['status']===$expected)$passed++;}finally{remove_filter('pre_http_request',$mock,10);}
 }
 wp_send_json(['passed'=>$passed,'total'=>count($cases),'transport'=>$transport,'disabled_by_default'=>!pa_cloudmersive_configured(),'default_quarantine'=>pa_scan_attachment($bytes,'application/pdf')['status']==='quarantined']);
});
