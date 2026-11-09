<?php
if(!defined('ABSPATH'))exit;
/**
 * Branded HTML emails: one layout for the website's notifications, with a plain-text part for clients
 * that do not show HTML. Tables and inline styles, so Outlook and Gmail render it the same; a small
 * media query stacks the label/value rows on phones (Gmail app, Apple Mail, Outlook mobile).
 *
 * $mail = [
 *  'eyebrow'=>'small line above the title (date, type)', 'title'=>'…', 'intro'=>'…', 'notice'=>'…',
 *  'sections'=>[['title'=>'Contact','rows'=>[['Name','…'],['Email','…','mailto:…'],…]],…],
 *  'message'=>['title'=>'Message','text'=>'…'], 'button'=>['label'=>'…','url'=>'…'], 'footnote'=>'…', 'lang'=>'en'|'es',
 * ]
 * Empty rows and sections are left out.
 */
function pelham_email_logo_url(){
 return function_exists('pelham_logo_url')?pelham_logo_url('pelham-logo.png'):get_template_directory_uri().'/assets/pelham-logo.png';
}
function pelham_email_rows($rows){return array_values(array_filter($rows,fn($row)=>trim((string)($row[1]??''))!==''));}

function pelham_email_html($mail){
 $green='#007d42';$button='#00a858';$ink='#17322a';$text='#3b4a43';$muted='#6b7a72';$line='#e4ebe7';$tint='#f3f7f5';$page='#eef3f0';
 $font='font-family:Helvetica,Arial,sans-serif;';
 $lang=($mail['lang']??'en')==='es'?'es':'en';
 $html='<!doctype html><html lang="'.$lang.'"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="x-apple-disable-message-reformatting"><meta name="format-detection" content="telephone=no,address=no,email=no,date=no">';
 $html.='<title>'.esc_html($mail['title']??'Pelham Services').'</title><style>';
 $html.='a[x-apple-data-detectors]{color:inherit!important;text-decoration:none!important}';
 $html.='@media only screen and (max-width:520px){.pm-pad{padding-left:20px!important;padding-right:20px!important}.pm-outer{padding:0!important}.pm-card{border-radius:0!important;border-left:0!important;border-right:0!important}.pm-h1{font-size:21px!important;line-height:27px!important}.pm-row td{display:block!important;width:100%!important;box-sizing:border-box}.pm-row .pm-l{padding:12px 0 2px!important;border-bottom:0!important}.pm-row .pm-v{padding:0 0 12px!important}.pm-btn{width:100%!important}}';
 $html.='</style></head>';
 $html.='<body style="margin:0;padding:0;background:'.$page.';-webkit-text-size-adjust:100%;">';
 if(!empty($mail['preheader']))$html.='<div style="display:none;max-height:0;overflow:hidden;opacity:0;mso-hide:all;">'.esc_html($mail['preheader']).'&#8199;&#847;&#8199;&#847;&#8199;&#847;</div>';
 $html.='<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:'.$page.';"><tr><td class="pm-outer" align="center" style="padding:32px 12px;">';
 $html.='<table class="pm-card" role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border:1px solid '.$line.';border-radius:10px;">';
 // Header: the logo on white, then the green line of the website.
 $html.='<tr><td class="pm-pad" style="padding:24px 36px 20px;"><img src="'.esc_url(pelham_email_logo_url()).'" width="104" alt="Pelham Services" style="display:block;width:104px;height:auto;border:0;'.$font.'font-size:18px;font-weight:bold;color:'.$ink.';"></td></tr>';
 $html.='<tr><td style="height:3px;line-height:3px;font-size:0;background:'.$green.';">&nbsp;</td></tr>';
 $html.='<tr><td class="pm-pad" style="padding:30px 36px 6px;">';
 if(!empty($mail['eyebrow']))$html.='<p style="margin:0 0 6px;'.$font.'font-size:13px;line-height:18px;color:'.$muted.';">'.esc_html($mail['eyebrow']).'</p>';
 $html.='<h1 class="pm-h1" style="margin:0 0 10px;'.$font.'font-size:23px;line-height:29px;font-weight:bold;color:'.$ink.';">'.esc_html($mail['title']??'').'</h1>';
 if(!empty($mail['intro']))$html.='<p style="margin:0 0 18px;'.$font.'font-size:15px;line-height:23px;color:'.$text.';">'.esc_html($mail['intro']).'</p>';
 if(!empty($mail['notice']))$html.='<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px;"><tr><td style="background:'.$tint.';border-left:3px solid '.$green.';padding:12px 16px;'.$font.'font-size:14px;line-height:21px;color:'.$ink.';">'.nl2br(esc_html($mail['notice'])).'</td></tr></table>';
 $html.='</td></tr>';
 foreach($mail['sections']??[] as $section){
  $rows=pelham_email_rows($section['rows']??[]);if(!$rows)continue;
  $html.='<tr><td class="pm-pad" style="padding:10px 36px 8px;"><p style="margin:0 0 8px;'.$font.'font-size:15px;line-height:20px;font-weight:bold;color:'.$ink.';">'.esc_html($section['title']).'</p>';
  $html.='<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid '.$line.';">';
  foreach($rows as $row){[$label,$value]=$row;$link=$row[2]??'';
   $shown=esc_html($value);if(!empty($link))$shown='<a href="'.esc_url($link).'" style="color:'.$green.';text-decoration:none;">'.$shown.'</a>';
   $html.='<tr class="pm-row"><td class="pm-l" width="36%" valign="top" style="padding:11px 14px 11px 0;border-bottom:1px solid '.$line.';'.$font.'font-size:13px;line-height:20px;color:'.$muted.';">'.esc_html($label).'</td><td class="pm-v" valign="top" style="padding:11px 0;border-bottom:1px solid '.$line.';'.$font.'font-size:15px;line-height:21px;color:'.$ink.';">'.$shown.'</td></tr>';
  }
  $html.='</table></td></tr>';
 }
 if(!empty($mail['message']['text']))$html.='<tr><td class="pm-pad" style="padding:10px 36px 8px;"><p style="margin:0 0 8px;'.$font.'font-size:15px;line-height:20px;font-weight:bold;color:'.$ink.';">'.esc_html($mail['message']['title']??'Message').'</p><table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background:'.$tint.';border-radius:6px;padding:14px 16px;'.$font.'font-size:15px;line-height:23px;color:'.$ink.';">'.nl2br(esc_html($mail['message']['text'])).'</td></tr></table></td></tr>';
 // Button: the padding sits on the cell, so clients that drop padding on links (Outlook, translated views) keep its shape.
 if(!empty($mail['button']['url']))$html.='<tr><td class="pm-pad" style="padding:20px 36px 4px;"><table class="pm-btn" role="presentation" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" bgcolor="'.$button.'" style="background:'.$button.';border-radius:6px;padding:13px 26px;"><a href="'.esc_url($mail['button']['url']).'" style="'.$font.'font-size:15px;line-height:20px;font-weight:bold;color:#ffffff;text-decoration:none;"><span style="color:#ffffff;">'.esc_html($mail['button']['label']).'</span></a></td></tr></table></td></tr>';
 if(!empty($mail['footnote']))$html.='<tr><td class="pm-pad" style="padding:16px 36px 0;'.$font.'font-size:13px;line-height:20px;color:'.$muted.';">'.esc_html($mail['footnote']).'</td></tr>';
 // Footer: the address and phone are our own quiet links, so Gmail does not turn them into blue ones.
 $map='https://www.google.com/maps/search/?api=1&query='.rawurlencode('Pelham Services, 249 E Redondo Beach Blvd, Gardena, CA 90248');
 $quiet='color:'.$muted.';text-decoration:none;';
 $html.='<tr><td class="pm-pad" style="padding:28px 36px 28px;"><table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid '.$line.';"><tr><td style="padding-top:18px;'.$font.'font-size:12px;line-height:19px;color:'.$muted.';">';
 $html.='<span style="color:'.$ink.';font-weight:bold;">Pelham Services CA Corp</span><br><a href="'.esc_url($map).'" style="'.$quiet.'">249 East Redondo Beach Blvd, Gardena, CA 90248</a><br><a href="tel:+13232014995" style="'.$quiet.'">323-201-4995</a> &nbsp;&middot;&nbsp; <a href="'.esc_url(home_url('/')).'" style="color:'.$green.';text-decoration:none;">pelhamservices.com</a>';
 $html.='</td></tr></table></td></tr></table></td></tr></table></body></html>';
 return $html;
}

/** The same content as plain text, for the text part of the email. */
function pelham_email_text($mail){
 $out=[];foreach(['eyebrow','title','intro','notice'] as $key)if(!empty($mail[$key]))$out[]=$mail[$key];
 foreach($mail['sections']??[] as $section){$rows=pelham_email_rows($section['rows']??[]);if(!$rows)continue;$lines=[$section['title']];foreach($rows as $row)$lines[]='- '.$row[0].': '.$row[1];$out[]=implode("\n",$lines);}
 if(!empty($mail['message']['text']))$out[]=($mail['message']['title']??'Message')."\n".$mail['message']['text'];
 if(!empty($mail['button']['url']))$out[]=$mail['button']['label'].': '.$mail['button']['url'];
 if(!empty($mail['footnote']))$out[]=$mail['footnote'];
 $out[]="--\nPelham Services CA Corp\n249 East Redondo Beach Blvd, Gardena, CA 90248\n323-201-4995 · ".home_url('/');
 return implode("\n\n",$out);
}

/** Send a branded email (HTML with a plain-text part). */
function pelham_mail($to,$subject,$mail,$headers=[]){
 $text=pelham_email_text($mail);
 $alt=function($phpmailer)use($text){$phpmailer->AltBody=$text;};
 add_action('phpmailer_init',$alt);
 try{return wp_mail($to,$subject,pelham_email_html($mail),array_merge(['Content-Type: text/html; charset=UTF-8'],$headers));}
 finally{remove_action('phpmailer_init',$alt);}
}
