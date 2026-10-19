<?php
if(!defined('ABSPATH'))exit;
// These are format checks, not carrier, mailbox or postal-deliverability verification.
function pa_phone_valid($value){
 if(!preg_match('/^\+?[0-9().\s-]+(?:\s*(?:ext\.?|x|#)\s*\d{1,6})?$/iD',$value))return false;
 $base=preg_replace('/\s*(?:ext\.?|x|#)\s*\d{1,6}$/i','',$value);$digits=preg_replace('/\D/','',$base);
 if(strlen($digits)===11&&$digits[0]==='1')$digits=substr($digits,1);
 return (bool)preg_match('/^[2-9][0-9]{2}[2-9][0-9]{6}$/D',$digits)&&substr($digits,1,2)!=='11'&&substr($digits,4,2)!=='11'&&!preg_match('/^(\d)\1{9}$/D',$digits);
}
function pa_email_valid($email,$test=false){
 if(!is_string($email)||strlen($email)>254||$email!==trim($email)||!is_email($email))return false;
 $domain=strtolower(substr(strrchr($email,'@'),1));
 if($test&&$domain==='example.invalid')return true;
 return str_contains($domain,'.')&&!preg_match('/(?:^|\.)(?:invalid|test|localhost|example)$/D',$domain)&&!in_array($domain,['example.com','example.org','example.net'],true);
}
function pa_field_format_error($f,$value,$test=false){
 if($value==='')return '';
 if($f['type']==='tel'&&!pa_phone_valid($value))return 'Enter a 10-digit US/NANP phone number, optionally +1 and an extension (e.g. 323-201-4995 ext 12).';
 if($f['type']==='email'&&!pa_email_valid($value,$test))return 'Enter a valid email address with a public domain, such as name@company.com.';
 if(preg_match('/(^|_)zip$/D',$f['key'])&&(!preg_match('/^\d{5}(?:-\d{4})?$/D',$value)||substr($value,0,5)==='00000'))return 'Enter a five-digit US ZIP code or ZIP+4 (e.g. 90248 or 90248-1234).';
 return '';
}
function pa_input_errors($kind,$input){
 if(!is_array($input))return ['data'=>'Invalid form data.'];
 $errors=[];foreach(pa_schema($kind) as $f){$key=$f['key'];if(!array_key_exists($key,$input))continue;$v=$input[$key];
 if(!is_scalar($v)||strlen((string)$v)>($f['type']==='textarea'?5000:250)||preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/',(string)$v))$errors[$key]='This field has an invalid value or exceeds the allowed length.';
 }
 return $errors;
}
