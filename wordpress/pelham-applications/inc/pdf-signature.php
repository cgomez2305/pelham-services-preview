<?php
if(!defined('ABSPATH'))exit;
/** Create an export-only crop. The stored signature and its signed hash are never changed. */
function pa_pdf_signature_file($image,&$temp){
 $bytes=base64_decode($image['base64'],true);$original=tempnam(sys_get_temp_dir(),'pa-sign-original-');$temp[]=$original;file_put_contents($original,$bytes);$info=getimagesize($original);
 $result=[$original,$info[0],$info[1],$image['mime']==='image/png'?'PNG':'JPG'];
 if($info[0]*$info[1]>4000000)return $result;
 if(!function_exists('imagecreatefromstring')||!function_exists('imagecrop'))return $result;
 $source=@imagecreatefromstring($bytes);if(!$source)return $result;
 $probe=null;$crop=null;
 try{
  $width=imagesx($source);$height=imagesy($source);$ratio=min(1,1024/max($width,$height));$pw=max(1,(int)ceil($width*$ratio));$ph=max(1,(int)ceil($height*$ratio));
  $probe=imagecreatetruecolor($pw,$ph);imagefill($probe,0,0,imagecolorallocate($probe,255,255,255));imagecopyresampled($probe,$source,0,0,0,0,$pw,$ph,$width,$height);
  $left=$pw;$right=-1;$top=$ph;$bottom=-1;
  for($y=0;$y<$ph;$y++)for($x=0;$x<$pw;$x++){$rgb=imagecolorat($probe,$x,$y);if(min(($rgb>>16)&255,($rgb>>8)&255,$rgb&255)<245){$left=min($left,$x);$right=max($right,$x);$top=min($top,$y);$bottom=max($bottom,$y);}}
  if($right<$left)return $result;
  $padding=max(2,(int)ceil(3/$ratio));$x=max(0,(int)floor($left/$ratio)-$padding);$y=max(0,(int)floor($top/$ratio)-$padding);$w=min($width-$x,(int)ceil(($right+1)/$ratio)+$padding-$x);$h=min($height-$y,(int)ceil(($bottom+1)/$ratio)+$padding-$y);
  $crop=imagecrop($source,['x'=>$x,'y'=>$y,'width'=>$w,'height'=>$h]);if(!$crop)return $result;
  $file=tempnam(sys_get_temp_dir(),'pa-sign-crop-');$temp[]=$file;if(!imagepng($crop,$file))return $result;
  return [$file,$w,$h,'PNG'];
 }finally{if($crop)imagedestroy($crop);if($probe)imagedestroy($probe);imagedestroy($source);}
}
