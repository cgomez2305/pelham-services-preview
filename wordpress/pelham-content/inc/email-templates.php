<?php
if(!defined('ABSPATH'))exit;
/**
 * Branded HTML emails (1.22): one layout for the website's notifications, with a plain-text part for
 * clients that do not show HTML. Tables and inline styles only, so Outlook and Gmail render it the same.
 *
 * $mail = [
 *  'eyebrow'=>'WEBSITE · QUOTE REQUEST', 'title'=>'…', 'intro'=>'…', 'notice'=>'…',
 *  'sections'=>[['title'=>'Customer','rows'=>[['Name','…'],…]],…], 'message'=>['title'=>'Message','text'=>'…'],
 *  'button'=>['label'=>'…','url'=>'…'], 'footnote'=>'…', 'lang'=>'en'|'es',
 * ]
 * Empty rows and sections are left out.
 */
function pelham_email_logo_url(){
 return function_exists('pelham_logo_url')?pelham_logo_url('pelham-logo.png'):get_template_directory_uri().'/assets/pelham-logo.png';
}
function pelham_email_rows($rows){return array_values(array_filter($rows,fn($row)=>trim((string)($row[1]??''))!==''));}

function pelham_email_html($mail){
 $green='#007d42';$ink='#143b2a';$muted='#5f6f66';$line='#e3ebe6';$tint='#f2f7f4';
 $font="font-family:Arial,Helvetica,sans-serif;";
 $lang=($mail['lang']??'en')==='es'?'es':'en';
 $html='<!doctype html><html lang="'.$lang.'"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.esc_html($mail['title']??'Pelham Services').'</title></head>';
 $html.='<body style="margin:0;padding:0;background:#eef3f0;'.$font.'">';
 if(!empty($mail['preheader']))$html.='<div style="display:none;max-height:0;overflow:hidden;opacity:0;">'.esc_html($mail['preheader']).'</div>';
 $html.='<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#eef3f0;"><tr><td align="center" style="padding:28px 12px;">';
 $html.='<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border:1px solid '.$line.';border-radius:12px;overflow:hidden;">';
 // Header: logo and the green line of the website.
 $html.='<tr><td style="padding:22px 32px 18px;"><table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
 $html.='<td valign="middle"><img src="'.esc_url(pelham_email_logo_url()).'" width="96" alt="Pelham Services" style="display:block;width:96px;height:auto;border:0;'.$font.'font-size:18px;font-weight:bold;color:'.$ink.';"></td>';
 $html.='<td valign="middle" align="right" style="'.$font.'font-size:12px;color:'.$muted.';">pelhamservices.com</td></tr></table></td></tr>';
 $html.='<tr><td style="height:4px;line-height:4px;font-size:0;background:'.$green.';">&nbsp;</td></tr>';
 $html.='<tr><td style="padding:28px 32px 8px;">';
 if(!empty($mail['eyebrow']))$html.='<p style="margin:0 0 8px;'.$font.'font-size:12px;font-weight:bold;letter-spacing:1.5px;color:'.$green.';text-transform:uppercase;">'.esc_html($mail['eyebrow']).'</p>';
 $html.='<h1 style="margin:0 0 12px;'.$font.'font-size:24px;line-height:30px;font-weight:bold;color:'.$ink.';">'.esc_html($mail['title']??'').'</h1>';
 if(!empty($mail['intro']))$html.='<p style="margin:0 0 16px;'.$font.'font-size:15px;line-height:23px;color:#3d4d44;">'.esc_html($mail['intro']).'</p>';
 if(!empty($mail['notice']))$html.='<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:4px 0 18px;"><tr><td style="background:'.$tint.';border-left:4px solid '.$green.';padding:12px 16px;'.$font.'font-size:14px;line-height:21px;color:'.$ink.';">'.nl2br(esc_html($mail['notice'])).'</td></tr></table>';
 $html.='</td></tr>';
 foreach($mail['sections']??[] as $section){
  $rows=pelham_email_rows($section['rows']??[]);if(!$rows)continue;
  $html.='<tr><td style="padding:6px 32px 10px;"><p style="margin:10px 0 8px;'.$font.'font-size:12px;font-weight:bold;letter-spacing:1.2px;color:'.$green.';text-transform:uppercase;">'.esc_html($section['title']).'</p>';
  $html.='<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid '.$line.';">';
  foreach($rows as $row){[$label,$value]=$row;$link=$row[2]??'';
   $shown=esc_html($value);if(!empty($link))$shown='<a href="'.esc_url($link).'" style="color:'.$green.';text-decoration:underline;">'.$shown.'</a>';
   $html.='<tr><td width="38%" valign="top" style="padding:10px 12px 10px 0;border-bottom:1px solid '.$line.';'.$font.'font-size:13px;line-height:19px;color:'.$muted.';">'.esc_html($label).'</td><td valign="top" style="padding:10px 0;border-bottom:1px solid '.$line.';'.$font.'font-size:15px;line-height:21px;color:'.$ink.';">'.$shown.'</td></tr>';
  }
  $html.='</table></td></tr>';
 }
 if(!empty($mail['message']['text']))$html.='<tr><td style="padding:6px 32px 10px;"><p style="margin:10px 0 8px;'.$font.'font-size:12px;font-weight:bold;letter-spacing:1.2px;color:'.$green.';text-transform:uppercase;">'.esc_html($mail['message']['title']??'Message').'</p><table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background:'.$tint.';border-radius:8px;padding:14px 16px;'.$font.'font-size:15px;line-height:23px;color:'.$ink.';">'.nl2br(esc_html($mail['message']['text'])).'</td></tr></table></td></tr>';
 if(!empty($mail['button']['url']))$html.='<tr><td style="padding:18px 32px 6px;"><table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr><td bgcolor="#00a858" style="border-radius:8px;"><a href="'.esc_url($mail['button']['url']).'" style="display:inline-block;padding:13px 24px;'.$font.'font-size:15px;font-weight:bold;color:#ffffff;text-decoration:none;border-radius:8px;">'.esc_html($mail['button']['label']).' &rarr;</a></td></tr></table></td></tr>';
 if(!empty($mail['footnote']))$html.='<tr><td style="padding:14px 32px 0;'.$font.'font-size:13px;line-height:20px;color:'.$muted.';">'.esc_html($mail['footnote']).'</td></tr>';
 $html.='<tr><td style="padding:26px 32px 26px;"><table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid '.$line.';"><tr><td style="padding-top:16px;'.$font.'font-size:12px;line-height:19px;color:'.$muted.';">';
 $html.='<strong style="color:'.$ink.';">Pelham Services CA Corp</strong><br>249 East Redondo Beach Blvd, Gardena, CA 90248 &middot; <a href="tel:3232014995" style="color:'.$muted.';">323-201-4995</a><br><a href="'.esc_url(home_url('/')).'" style="color:'.$green.';">pelhamservices.com</a>';
 $html.='</td></tr></table></td></tr></table></td></tr></table></body></html>';
 return $html;
}

/** The same content as plain text, for the text part of the email. */
function pelham_email_text($mail){
 $out=[];if(!empty($mail['title']))$out[]=$mail['title'];if(!empty($mail['intro']))$out[]=$mail['intro'];if(!empty($mail['notice']))$out[]=$mail['notice'];
 foreach($mail['sections']??[] as $section){$rows=pelham_email_rows($section['rows']??[]);if(!$rows)continue;$lines=[strtoupper($section['title'])];foreach($rows as $row)$lines[]=$row[0].': '.$row[1];$out[]=implode("\n",$lines);}
 if(!empty($mail['message']['text']))$out[]=strtoupper($mail['message']['title']??'Message')."\n".$mail['message']['text'];
 if(!empty($mail['button']['url']))$out[]=$mail['button']['label'].': '.$mail['button']['url'];
 if(!empty($mail['footnote']))$out[]=$mail['footnote'];
 $out[]="Pelham Services CA Corp\n249 East Redondo Beach Blvd, Gardena, CA 90248 · 323-201-4995\n".home_url('/');
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
