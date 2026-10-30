<?php
if(!defined('ABSPATH'))exit;
function pa_document_keys($p){if($p['kind']!=='application')return [$p['kind']];return array_merge(['customer'],($p['data']['request_credit']??'')==='Yes'?['credit']:[],['terms']);}
function pa_document_fields($p,$doc){return array_values(array_filter(pa_schema($p['kind']),fn($f)=>$f['doc']===$doc||($p['kind']==='application'&&$doc==='credit'&&($f['group']==='company'||str_starts_with($f['key'],'ref')))));}
function pa_original_pdf($id,$revision,$document='all'){
 require_once PA_DIR.'/vendor/fpdf/fpdf.php';require_once PA_DIR.'/vendor/fpdi/src/autoload.php';
 $p=$revision?pa_get($id,'_pa_revision_'.$revision):pa_get($id);if(!$p)throw new RuntimeException('Revision not found.');
 $docs=pa_document_keys($p);if($document!=='all'){if(!in_array($document,$docs,true))throw new RuntimeException('Document unavailable.');$docs=[$document];}
 $map=json_decode(file_get_contents(PA_DIR.'/inc/pdf-map.json'),true);$manifest=json_decode(file_get_contents(PA_DIR.'/documents/templates/manifest.json'),true);
 $pdf=new class('P','pt','Letter') extends \setasign\Fpdi\Fpdi {public function originalTemplate($tpl){$this->_out('q 0 g 0 G 0 Tc 0 Tw');$this->useTemplate($tpl,0,0,612,792);$this->_out('Q');}};$pdf->SetAutoPageBreak(false);$pdf->SetMargins(36,36,36);$pdf->SetTitle(pa_reference($id).' — '.($revision?'revision '.$revision:'DRAFT'));$temp=[];
 $counter=$revision?pa_get($id,'_pa_countersign_'.$revision):[];$internal=pa_get($id,'_pa_internal');$d=$p['data'];$extra=[];
 if(!empty($d['shipment_date'])&&preg_match('/^\d{4}-\d{2}-\d{2}$/D',$d['shipment_date']))$d['shipment_date']=gmdate('m/d/Y',strtotime($d['shipment_date']));
 foreach(['customer','credit','terms','guarantee'] as $slot)$d['@'.$slot.'_date']=empty($p['signatures'][$slot]['signed_at'])?'':gmdate('m/d/Y',strtotime($p['signatures'][$slot]['signed_at']));
 $d['@representative']=$counter['representative']??'';$d['@representative_title']=$counter['representative_title']??'';$d['@representative_date']=empty($counter['signed_at'])?'':gmdate('m/d/Y',strtotime($counter['signed_at']));
 // Office decisions belong to the review, not to the immutable customer signature.
 $d['@review_date']=($internal['credit_status']??'')==='approved'?gmdate('m/d/Y',strtotime($internal['reviewed_at'])):'';$d['@credit_limit']=($internal['credit_status']??'')==='approved'?($internal['credit_limit']??''):'';$d['@approved_by']=$d['@review_date']?(get_userdata($internal['reviewed_by']??0)->display_name??''):'';
 $write=function($key,$x,$y,$w,$h,$size=9,$baseline=null,$ruledLines=null)use($pdf,$d,&$extra){
  $value=(string)($d[$key]??'');if($value==='')return;
  $text=pa_pdf_text($value);$pdf->SetFont('Helvetica','',$size);$pdf->SetTextColor(15,25,20);
  if($ruledLines!==null){
   $words=preg_split('/\s+/',trim($text));$lines=[];$index=0;$line='';$overflow=false;
   foreach($words as $word){$candidate=$line===''?$word:$line.' '.$word;$width=$ruledLines[$index][1];if($pdf->GetStringWidth($candidate)<=$width){$line=$candidate;continue;}if($line===''){$overflow=true;break;}$lines[]=$line;$index++;if(!isset($ruledLines[$index])||$pdf->GetStringWidth($word)>$ruledLines[$index][1]){$overflow=true;break;}$line=$word;}
   if(!$overflow&&$line!=='')$lines[]=$line;
   if($overflow){$lines=['See digital supplement for complete details.'];$extra[$key]=$value;}
   foreach($lines as $i=>$line)$pdf->Text($x,$ruledLines[$i][0],$line);return;
  }
  if($baseline!==null){$text=preg_replace('/\s+/',' ',$text);$minimum=$size>=11?10:8;while($pdf->GetStringWidth($text)>$w&&$size>$minimum){$size-=.25;$pdf->SetFont('Helvetica','',$size);}if($pdf->GetStringWidth($text)>$w){$text='[Annex]';$extra[$key]=$value;}$pdf->Text($x,$baseline,$text);return;}
  while($pdf->GetStringWidth($text)>$w&&$size>8&&$h<=12){$size-=.25;$pdf->SetFont('Helvetica','',$size);}if($h<=12&&$pdf->GetStringWidth($text)>$w){$text='[Annex]';$pdf->SetFont('Helvetica','',8);$extra[$key]=$value;}
  $pdf->SetXY($x,$y);if($h>12)$pdf->MultiCell($w,12,$text);else $pdf->Cell($w,$h,$text,0,0);
 };
 $signature=function($s,$x,$y,$w,$h)use($pdf,&$temp){if(empty($s['image'])&&empty($s['mime']))return;$image=$s['image']??$s;[$file,$iw,$ih,$type]=pa_pdf_signature_file($image,$temp);$scale=min($w/$iw,$h/$ih);$height=$ih*$scale;$pdf->Image($file,$x,$y+$h-$height,$iw*$scale,$height,$type);};

 try{foreach($docs as $doc){$spec=$map[$doc];$source=PA_DIR.'/documents/templates/'.$spec['file'];$hash=$manifest[$spec['file']];if(!hash_equals($hash['source_sha256'],$p['documents'][$doc]['sha256'])||!hash_equals($hash['template_sha256'],hash_file('sha256',$source)))throw new RuntimeException('Source version mismatch.');$pages=$pdf->setSourceFile($source);
 for($page=1;$page<=$pages;$page++){$tpl=$pdf->importPage($page);$pdf->AddPage();$pdf->originalTemplate($tpl);foreach($spec['pages'][$page-1] as $field)$write(...$field);
 if($doc==='customer'){$entity=$d['entity']??'';$positions=['Sole proprietorship'=>[180,235,22],'Partnership'=>[279,235,20],'Corporation'=>[405,235,23],'Other'=>[470,235,57]];if(isset($positions[$entity])){$box=$positions[$entity];$pdf->SetFont('Helvetica','B',8);$pdf->SetXY($box[0],$box[1]);$pdf->Cell($box[2],10,'X');}$signature($p['signatures']['customer']??[],80,672,220,23);}
 if($doc==='credit'&&$page===2){$signature($p['signatures']['credit']??[],218,264,195,9);$signature($p['signatures']['guarantee']??[],92,493,178,29);}
 if($doc==='terms'&&$page===4){$signature($p['signatures']['terms']??[],365,494,199,27);$signature($counter['signature']??[],40,494,195,27);}
 if($doc==='claim'&&$page===1){$pdf->SetFont('Helvetica','B',12);$pdf->Text(($d['payment_method']??'')==='Collect'?320:236,292,'X');}
 if($doc==='claim'&&$page===2)$signature($p['signatures']['claim']??[],320,190,169,42);
 $pdf->SetFont('Helvetica','',7);$pdf->SetTextColor($revision?80:170,$revision?80:20,$revision?80:20);$pdf->SetXY(36,768);$pdf->Cell(540,10,pa_pdf_text(pa_reference($id).' | '.($revision?'Submitted revision '.$revision:'DRAFT — NOT SUBMITTED').' | '.$doc.' '.$page.'/'.$pages));
 }
 // Preserve every full value, including fields that the historical paper template has no slot for.
 $pdf->AddPage();$pdf->SetAutoPageBreak(true,36);$pdf->SetTextColor(20,48,35);$pdf->SetFont('Helvetica','B',15);$pdf->MultiCell(540,20,pa_pdf_text('Digital supplement — '.$p['documents'][$doc]['title']));$pdf->SetFont('Helvetica','',9);$pdf->MultiCell(540,13,pa_pdf_text(pa_reference($id).' | '.($revision?'Submitted revision '.$revision:'DRAFT — NOT SUBMITTED').' | Full values, signatures and source integrity.'));$pdf->Ln(8);
 foreach(pa_document_fields($p,$doc) as $f){$v=$p['data'][$f['key']]??'';if($v==='')continue;$pdf->SetFont('Helvetica','B',9);$pdf->MultiCell(540,13,pa_pdf_text($f['label']));$pdf->SetFont('Helvetica','',9);$pdf->MultiCell(540,13,pa_pdf_text($f['type']==='checkbox'?'Confirmed':$v));}
 foreach(pa_signatures_needed($p) as $slot=>$spec){if($spec['doc']!==$doc)continue;$s=$p['signatures'][$slot]??null;$pdf->Ln(8);$pdf->MultiCell(540,13,pa_pdf_text($spec['label'].': '.($s?($s['name'].' | '.$s['signed_at'].' | '.$s['method']):'Not signed')));if($s)$pdf->MultiCell(540,13,'Signed data SHA-256: '.$s['data_hash']);}
 if($doc==='terms'){$pdf->MultiCell(540,13,pa_pdf_text('Pelham countersignature: '.($counter?($counter['representative'].' | '.$counter['signed_at']):'Pending')));if(isset($p['data']['terms_initials_5']))$pdf->MultiCell(540,13,pa_pdf_text('Legacy extra initials field (not an original page): '.$p['data']['terms_initials_5']));}
 if($doc==='claim')foreach($p['files']??[] as $slot=>$file)$pdf->MultiCell(540,13,pa_pdf_text('Evidence '.$slot.': '.$file['name'].' | SHA-256: '.$file['sha256']));
 if($doc==='customer')$pdf->MultiCell(540,13,pa_pdf_text('Office review at export: '.($internal['credit_status']??'pending').' | Credit limit: '.($d['@credit_limit']?:'not approved').' | '.$d['@approved_by']));
 $pdf->MultiCell(540,13,'Original source SHA-256: '.$p['documents'][$doc]['sha256']);$pdf->SetAutoPageBreak(false);
 }
 return $pdf->Output('S');}finally{foreach($temp as $file)@unlink($file);}
}
