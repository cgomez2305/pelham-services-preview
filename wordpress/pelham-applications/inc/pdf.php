<?php
if(!defined('ABSPATH'))exit;
function pa_pdf_text($text){$text=str_replace(["\r","\f"],["","\n\n"],(string)$text);return function_exists('iconv')?iconv('UTF-8','windows-1252//TRANSLIT',$text):preg_replace('/[^\x09\x0A\x20-\x7E]/','?',$text);}
function pa_pdf($id,$revision){return pa_original_pdf($id,$revision);}
add_action('admin_post_pa_download',function(){
 if(!pa_can_review())wp_die('Not authorized.',403);$id=absint($_GET['record']??0);check_admin_referer('pa_download_'.$id);if(get_post_type($id)!=='pelham_application')wp_die('Not found.',404);
 try{if(($_GET['format']??'')==='pdf'){$revision=absint($_GET['revision']??0);$document=sanitize_key($_GET['document']??'all');$bytes=pa_original_pdf($id,$revision,$document);$name=pa_reference($id).'-'.$document.'-'.($revision?'revision-'.$revision:'DRAFT').'.pdf';$mime='application/pdf';}else{$p=pa_get($id);$file=$p['files'][sanitize_key($_GET['slot']??'')]??null;if(!$file)wp_die('Not found.',404);if(!pa_attachment_clean($file))wp_die('This attachment is quarantined until a configured security scanner approves it.','',['response'=>403]);$bytes=base64_decode($file['base64']);$name=sanitize_file_name($file['name']);$mime=$file['mime'];}
  nocache_headers();header('Content-Type: '.$mime);header('X-Content-Type-Options: nosniff');header('Content-Disposition: attachment; filename="'.str_replace('"','',$name).'"');header('Content-Length: '.strlen($bytes));pa_audit($id,'Private download: '.$name);echo $bytes;exit;
 }catch(Throwable $e){wp_die('The document could not be generated. Please contact the site administrator.',500);}
});
