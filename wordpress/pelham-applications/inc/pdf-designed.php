<?php
if(!defined('ABSPATH'))exit;
/**
 * Designed Pelham documents (1.6): the customer application, credit application, transport terms and
 * freight claim laid out as clean branded documents. The legal wording comes verbatim from the original
 * documents (documents/designed-text.json); the original paper layout is still available to the team.
 */
require_once PA_DIR.'/vendor/fpdf/fpdf.php';

class PA_Designed_PDF extends FPDF {
	public $title='';public $status='';public $draft=false;public $initials='';public $docStart=1;public $pageTitle='';public $pageInitials='';
	const GREEN=[0,125,66];const INK=[20,59,42];const MUTED=[98,114,105];const LINE=[208,222,213];const TINT=[243,248,245];
	function Header(){
		// The footer is drawn when the next page starts, so it uses what this page's header recorded.
		$this->pageTitle=$this->title;$this->pageInitials=$this->initials;
		$this->Image(PA_DIR.'/assets/pelham-logo.png',48,30,0,40);
		$this->SetTextColor(...self::INK);$this->SetFont('Helvetica','B',15);$this->SetXY(200,32);$this->Cell(364,18,pa_pdf_text($this->title),0,0,'R');
		$this->SetFont('Helvetica','',7.5);$this->SetTextColor(...self::MUTED);$this->SetXY(200,51);$this->Cell(364,10,pa_pdf_text('Pelham Services CA Corp  ·  249 East Redondo Beach Blvd, Gardena, CA 90248  ·  323-201-4995'),0,0,'R');
		$this->SetFont('Helvetica','B',7.5);$this->SetTextColor(...($this->draft?[176,40,40]:self::GREEN));$this->SetXY(200,62);$this->Cell(364,10,pa_pdf_text($this->status),0,0,'R');
		$this->SetDrawColor(...self::GREEN);$this->SetLineWidth(1.4);$this->Line(48,80,564,80);$this->SetLineWidth(.5);$this->SetY(96);
	}
	function Footer(){
		$this->SetDrawColor(...self::LINE);$this->Line(48,758,564,758);
		$this->SetFont('Helvetica','',7);$this->SetTextColor(...self::MUTED);
		$this->SetXY(48,763);$this->Cell(300,9,pa_pdf_text('Pelham Services CA Corp  ·  '.$this->pageTitle));
		if($this->pageInitials!==''){$this->SetXY(330,763);$this->Cell(150,9,pa_pdf_text('Initials: '.$this->pageInitials),0,0,'R');}
		$this->SetXY(484,763);$this->Cell(80,9,'Page '.($this->PageNo()-$this->docStart+1),0,0,'R');
	}
	/** Start a new page when fewer than $height points remain. */
	function need($height){if($this->GetY()+$height>742)$this->AddPage();}
	function section($title){
		$this->need(56);$this->Ln(8);$y=$this->GetY();
		$this->SetFont('Helvetica','B',8.5);$this->SetTextColor(...self::GREEN);$this->SetXY(48,$y);$this->Cell(516,12,pa_pdf_text(strtoupper($title)));
		$this->SetDrawColor(...self::LINE);$this->Line(48,$y+15,564,$y+15);$this->SetY($y+20);
	}
	function subheading($text){$this->need(40);$this->SetFont('Helvetica','B',9);$this->SetTextColor(...self::INK);$this->SetX(48);$this->Cell(516,14,pa_pdf_text($text),0,1);}
	/** Lines a value needs at a width. */
	function linesFor($text,$width){
		$count=0;foreach(explode("\n",$text) as $paragraph){$line='';$count++;foreach(preg_split('/\s+/',trim($paragraph)) as $word){$try=$line===''?$word:$line.' '.$word;if($this->GetStringWidth($try)>$width&&$line!==''){$count++;$line=$word;}else $line=$try;}}
		return max(1,$count);
	}
	/** One row of labelled values: [[label, value, share], ...]; the shares add up to 1. */
	function row($cells){
		$this->SetFont('Helvetica','',9.5);$heights=[];
		foreach($cells as [$label,$value,$share]){$w=516*$share-14;$heights[]=$this->linesFor(pa_pdf_text($value===''?'—':$value),$w)*12;}
		$h=max($heights)+24;$this->need($h);$y=$this->GetY();$x=48;
		foreach($cells as $i=>[$label,$value,$share]){$w=516*$share;
			$this->SetFillColor(...self::TINT);$this->Rect($x+1,$y,$w-2,$h-4,'F');
			$this->SetFont('Helvetica','B',6.5);$this->SetTextColor(...self::MUTED);$this->SetXY($x+7,$y+4);$this->Cell($w-14,9,pa_pdf_text(strtoupper($label)));
			$this->SetFont('Helvetica','',9.5);$this->SetTextColor(...($value===''?self::MUTED:self::INK));$this->SetXY($x+7,$y+14);$this->MultiCell($w-14,12,pa_pdf_text($value===''?'—':$value),0,'L');
			$x+=$w;}
		$this->SetY($y+$h);
	}
	function paragraph($text,$size=8.8,$box=false){
		$this->SetFont('Helvetica','',$size);$h=$this->linesFor(pa_pdf_text($text),$box?496:516)*($size+3.2)+($box?16:4);$this->need(min($h,200));
		$this->SetTextColor(...self::INK);
		if($box){$y=$this->GetY();$this->SetFillColor(...self::TINT);$this->Rect(48,$y,516,$h-4,'F');$this->SetFillColor(...self::GREEN);$this->Rect(48,$y,2.5,$h-4,'F');$this->SetXY(58,$y+6);$this->MultiCell(496,$size+3.2,pa_pdf_text($text),0,'J');$this->SetY($y+$h);}
		else{$this->SetX(48);$this->MultiCell(516,$size+3.2,pa_pdf_text($text),0,'J');$this->Ln(4);}
	}
	function numbered($items,$size=8.6,$start=1){
		foreach($items as $i=>$text){$this->SetFont('Helvetica','',$size);$lines=$this->linesFor(pa_pdf_text($text),494);$this->need($lines*($size+3)+6);$y=$this->GetY();
			$this->SetFont('Helvetica','B',$size);$this->SetTextColor(...self::GREEN);$this->SetXY(48,$y);$this->Cell(20,$size+3,($start+$i).'.');
			$this->SetFont('Helvetica','',$size);$this->SetTextColor(...self::INK);$this->SetXY(70,$y);$this->MultiCell(494,$size+3,pa_pdf_text($text),0,'J');$this->Ln(4);}
	}
	/** Checkbox line for an electronic acceptance. */
	function check($checked,$text,$x=48,$w=516){
		$this->SetFont('Helvetica','',8.6);$lines=$this->linesFor(pa_pdf_text($text),$w-20);$this->need($lines*11.6+6);$y=$this->GetY();
		$this->SetDrawColor(...self::GREEN);$this->Rect($x,$y+1.5,9,9);
		if($checked){$this->SetLineWidth(1.3);$this->Line($x+2,$y+6.3,$x+4.6,$y+8.8);$this->Line($x+4.6,$y+8.8,$x+8.2,$y+3.3);$this->SetLineWidth(.5);}
		$this->SetFont('Helvetica','',8.6);$this->SetTextColor(...self::INK);$this->SetXY($x+16,$y);$this->MultiCell($w-16,11.6,pa_pdf_text($text),0,'L');$this->Ln(3);
	}
	/** Signature with its printed details; $x and $w place it in a column. */
	function signature($x,$w,$y,$caption,$image,$details,&$temp){
		// The block is drawn at fixed positions: keep it whole on one page, without automatic breaks inside it.
		if($y+75+13*count($details)>748){$this->AddPage();$y=$this->GetY();}
		$this->SetAutoPageBreak(false);
		$this->SetDrawColor(...self::LINE);$this->SetFillColor(255,255,255);$this->Rect($x,$y,$w,58,'D');
		if($image){[$file,$iw,$ih,$type]=pa_pdf_signature_file($image,$temp);$scale=min(($w-24)/$iw,44/$ih);$this->Image($file,$x+12,$y+52-$ih*$scale,$iw*$scale,$ih*$scale,$type);}
		else{$this->SetFont('Helvetica','',8);$this->SetTextColor(...self::MUTED);$this->SetXY($x+12,$y+24);$this->Cell($w-24,10,'Not signed yet');}
		$this->SetDrawColor(...self::INK);$this->Line($x+10,$y+54,$x+$w-10,$y+54);
		$this->SetFont('Helvetica','B',6.5);$this->SetTextColor(...self::MUTED);$this->SetXY($x,$y+61);$this->Cell($w,9,pa_pdf_text(strtoupper($caption)));
		$line=$y+73;foreach($details as [$label,$value]){$this->SetFont('Helvetica','',7);$this->SetTextColor(...self::MUTED);$this->SetXY($x,$line);$this->Cell(62,11,pa_pdf_text($label));$this->SetFont('Helvetica','',9);$this->SetTextColor(...self::INK);$this->SetXY($x+62,$line);$this->Cell($w-62,11,pa_pdf_text($value===''?'—':$value));$line+=13;}
		$this->SetAutoPageBreak(true,60);
		return $line;
	}
}

/** Designed PDF of a request (one document or all of them), from a submitted revision or the draft. */
function pa_designed_pdf($id,$revision,$document='all',$mask=false){
	$p=$revision?pa_get($id,'_pa_revision_'.$revision):pa_get($id);if(!$p)throw new RuntimeException('Revision not found.');
	$docs=pa_document_keys($p);if($document!=='all'){if(!in_array($document,$docs,true))throw new RuntimeException('Document unavailable.');$docs=[$document];}
	$text=json_decode(file_get_contents(PA_DIR.'/documents/designed-text.json'),true);$d=$p['data'];$temp=[];
	$counter=$revision?pa_get($id,'_pa_countersign_'.$revision):[];$internal=pa_get($id,'_pa_internal');
	$date=fn($iso)=>$iso?gmdate('m/d/Y',strtotime($iso)):'';
	$v=function($key)use($d,$mask){$value=trim((string)($d[$key]??''));if($mask&&in_array($key,['tax_id','bank_account'],true)&&$value!=='')return '•••• '.substr($value,-4);return $value;};
	$place=fn($prefix)=>trim(implode(', ',array_filter([$v($prefix.'city'),trim($v($prefix.'state').' '.$v($prefix.'zip'))])));
	$money=fn($key)=>is_numeric($d[$key]??null)?'$'.number_format((float)$d[$key],2):$v($key);
	$sig=fn($slot)=>$p['signatures'][$slot]??null;
	$titles=['customer'=>'Customer Application','credit'=>'Credit Application','terms'=>'Terms and Conditions of Contract','claim'=>'Standard Claim for Loss/Damage'];
	$pdf=new PA_Designed_PDF('P','pt','Letter');$pdf->SetMargins(48,96,48);$pdf->SetAutoPageBreak(true,60);$pdf->SetTitle(pa_reference($id).' — '.($revision?'revision '.$revision:'DRAFT'));
	$pdf->draft=!$revision;$pdf->status=pa_reference($id).'  ·  '.($revision?'Submitted · revision '.$revision.' · '.$date($p['submitted_at']??''):'DRAFT — NOT SUBMITTED · preview '.gmdate('m/d/Y'));
	$checks=function($keys)use($pdf,$d,$p){$schema=[];foreach(pa_schema($p['kind']) as $f)$schema[$f['key']]=$f;foreach($keys as $key)if(isset($schema[$key])&&($d[$key]??'')!=='')$pdf->check($d[$key]==='1',$schema[$key]['label']);};
	// Audit trail of the electronic signatures on a document.
	$record=function($doc)use($pdf,$p,$date){
		$lines=[];foreach(pa_signatures_needed($p) as $slot=>$spec){if($spec['doc']!==$doc)continue;$s=$p['signatures'][$slot]??null;$lines[]=$spec['label'].': '.($s?$s['name'].' · '.gmdate('m/d/Y H:i',strtotime($s['signed_at'])).' UTC · '.$s['method'].' · data SHA-256 '.substr($s['data_hash'],0,16).'…':'not signed');}
		$lines[]='Source document SHA-256: '.substr($p['documents'][$doc]['sha256']??'',0,32).'…';
		$pdf->need(30+count($lines)*10);$pdf->Ln(10);$pdf->SetFont('Helvetica','B',6.5);$pdf->SetTextColor(...PA_Designed_PDF::MUTED);$pdf->SetX(48);$pdf->Cell(516,9,'ELECTRONIC SIGNATURE RECORD',0,1);
		$pdf->SetFont('Helvetica','',6.8);foreach($lines as $line){$pdf->SetX(48);$pdf->MultiCell(516,9,pa_pdf_text($line));}
	};
	try{foreach($docs as $doc){
		$pdf->title=$titles[$doc];$pdf->initials=$doc==='terms'?$v('terms_initials_1'):'';$pdf->AddPage();$pdf->docStart=$pdf->PageNo();
		if($doc==='customer'){
			$pdf->section('Business contact information');
			$pdf->row([['Contact person',$v('contact_name'),.5],['Title',$v('contact_title'),.5]]);
			$pdf->row([['Company name',$v('company'),1]]);
			$pdf->row([['Phone',$v('phone'),.3],['Fax',$v('fax'),.3],['Email',$v('email'),.4]]);
			$pdf->row([['Registered company address',$v('address'),.6],['City, state, ZIP',$place(''),.4]]);
			$pdf->row([['Date business commenced',$date($v('commenced')),.4],['Business structure',$v('entity')==='Other'?'Other: '.$v('entity_other'):$v('entity'),.6]]);
			$pdf->section('Billing information');
			$pdf->row([['Billing address',$v('billing_address'),.6],['City, state, ZIP',$place('billing_'),.4]]);
			$pdf->row([['Preferred method of billing',$v('billing_method'),.5],['Preferred payment method',$v('payment_method'),.5]]);
			$pdf->row([['Telephone',$v('billing_phone'),.3],['Fax',$v('billing_fax'),.3],['Email',$v('billing_email'),.4]]);
			$pdf->row([['Accounts payable contact',$v('ap_contact'),.5],['Phone',$v('ap_phone'),.5]]);
			$pdf->Ln(4);$pdf->paragraph($text['customer']['note'],8.2,true);
			$pdf->section('Business / trade references');
			foreach([1,2] as $i){$r='ref'.$i.'_';$pdf->subheading('Reference '.$i);
				$pdf->row([['Company name',$v($r.'company'),.5],['Contact name',$v($r.'contact'),.5]]);
				$pdf->row([['Address',$v($r.'address'),.6],['City, state, ZIP',$place($r),.4]]);
				$pdf->row([['Phone',$v($r.'phone'),.3],['Fax',$v($r.'fax'),.3],['Email',$v($r.'email'),.4]]);
				$pdf->row([['Type of account',$v($r.'account_type'),1]]);}
			$pdf->section('Agreement');$pdf->numbered($text['customer']['agreement']);
			$checks(['accept_terms','consent_signature']);
			$pdf->section('Signature');$pdf->need(120);$y=$pdf->GetY();
			$end=$pdf->signature(48,300,$y,'Authorized signature',$sig('customer')['image']??null,[['Name',$v('customer_signer')],['Title',$v('customer_title')],['Date',$date($sig('customer')['signed_at']??'')]],$temp);
			// Office box: the review decision belongs to Pelham, not to the customer's signature.
			$approved=($internal['credit_status']??'')==='approved';
			$pdf->SetFillColor(...PA_Designed_PDF::TINT);$pdf->Rect(364,$y,200,96,'F');$pdf->SetFont('Helvetica','B',6.5);$pdf->SetTextColor(...PA_Designed_PDF::MUTED);$pdf->SetXY(374,$y+8);$pdf->Cell(180,9,'FOR PELHAM USE ONLY');
			$line=$y+24;foreach([['Date',$approved?$date($internal['reviewed_at']??''):''],['Credit limit',$approved?(string)($internal['credit_limit']??''):''],['Approved by',$approved?(get_userdata($internal['reviewed_by']??0)->display_name??''):'']] as [$label,$value]){$pdf->SetFont('Helvetica','',7.5);$pdf->SetTextColor(...PA_Designed_PDF::MUTED);$pdf->SetXY(374,$line);$pdf->Cell(70,12,$label);$pdf->SetFont('Helvetica','',9);$pdf->SetTextColor(...PA_Designed_PDF::INK);$pdf->SetXY(444,$line);$pdf->Cell(112,12,pa_pdf_text($value===''?'—':$value));$pdf->SetDrawColor(...PA_Designed_PDF::LINE);$pdf->Line(444,$line+13,556,$line+13);$line+=22;}
			$pdf->SetY(max($end,$y+100));$record('customer');
		}
		if($doc==='credit'){
			$pdf->section('Contact information');
			$pdf->row([['Date',$date($sig('credit')['signed_at']??'')?:gmdate('m/d/Y'),.25],['Contact person',$v('contact_name'),.4],['Phone number',$v('phone'),.35]]);
			$pdf->row([['Email address',$v('email'),.6],['Fax number',$v('fax'),.4]]);
			$pdf->section('Company information');
			$pdf->row([['Company',$v('company'),.6],[$v('tax_id_type')?:'FEIN or SS#',$v('tax_id'),.4]]);
			$pdf->row([['Address',$v('address'),.6],['City, state, ZIP',$place(''),.4]]);
			$pdf->row([['Years in business',$v('years_business'),.4],['Annual gross sales',$money('annual_sales'),.6]]);
			$pdf->row([['Owner / controller',$v('owner'),.6],['Phone number',$v('owner_phone'),.4]]);
			$pdf->section('Bank');
			$pdf->row([['Bank name',$v('bank_name'),.6],['Account number',$v('bank_account'),.4]]);
			$pdf->row([['Phone number',$v('bank_phone'),.4],['Address',$v('bank_address'),.6]]);
			$pdf->row([['City, state, ZIP',$place('bank_'),1]]);
			$pdf->section('Trade references');
			foreach([1,2,3] as $i){$r='ref'.$i.'_';$pdf->subheading('Reference '.$i);
				$pdf->row([['Company',$v($r.'company'),.6],['Phone number',$v($r.'phone'),.4]]);
				$pdf->row([['Contact name',$v($r.'contact'),.4],['Address',trim($v($r.'address').($place($r)?', '.$place($r):'')),.6]]);}
			$pdf->section('Credit terms');$pdf->paragraph($text['credit']['request']);$pdf->paragraph($text['credit']['terms']);
			$checks(['accept_credit','consent_signature']);
			$pdf->section('Applicant signature');$pdf->need(120);$y=$pdf->GetY();
			$end=$pdf->signature(48,300,$y,'Owner / officer signature',$sig('credit')['image']??null,[['Company',$v('company')],['Name',$v('credit_signer')],['Title',$v('credit_title')],['Date',$date($sig('credit')['signed_at']??'')]],$temp);$pdf->SetY($end+4);
			$pdf->need(260);// The guaranty and its signature stay on one page.
			$pdf->section('Personal guaranty');
			$pdf->paragraph('I, '.($v('guarantor')?:'________________').', '.$text['credit']['guaranty_after_name']);$pdf->paragraph($text['credit']['guaranty_continuing']);
			$checks(['accept_guarantee']);
			$pdf->need(110);$y=$pdf->GetY()+4;$end=$pdf->signature(48,300,$y,'Guarantor signature',$sig('guarantee')['image']??null,[['Printed name',$v('guarantor')],['Dated',$date($sig('guarantee')['signed_at']??'')]],$temp);$pdf->SetY($end);
			$record('credit');
		}
		if($doc==='terms'){
			$pdf->SetFont('Helvetica','',8.6);$pdf->SetTextColor(...PA_Designed_PDF::MUTED);$pdf->SetX(48);$pdf->MultiCell(516,11,pa_pdf_text('These Terms and Conditions of Contract apply to every shipment tendered to Pelham Services CA Corp. Each page is initialed by the shipper.'));$pdf->Ln(6);
			$pdf->numbered($text['terms']['clauses'],8.3);
			$pdf->section('Acknowledgement');$pdf->paragraph($text['terms']['acknowledgement'],9);
			$checks(['accept_terms','consent_signature']);
			if($v('terms_initials_1')!==''){$pdf->SetFont('Helvetica','',8.6);$pdf->SetTextColor(...PA_Designed_PDF::INK);$pdf->SetX(48);$pdf->MultiCell(516,11.6,pa_pdf_text('Initials on every page of these Terms and Conditions: '.$v('terms_initials_1')));$pdf->Ln(4);}
			$pdf->need(130);$y=$pdf->GetY()+6;
			$a=$pdf->signature(48,248,$y,"Shipper's signature",$sig('terms')['image']??null,[['Name',$v('terms_signer')],['Title',$v('terms_title')],['Date',$date($sig('terms')['signed_at']??'')]],$temp);
			$b=$pdf->signature(316,248,$y,'Pelham Services representative',$counter['signature']??null,[['Name',$counter['representative']??''],['Title',$counter['representative_title']??''],['Date',$date($counter['signed_at']??'')]],$temp);
			$pdf->SetY(max($a,$b));$record('terms');
		}
		if($doc==='claim'){
			$pdf->paragraph($text['claim']['intro'],9);
			$pdf->section('Shipment');
			$pdf->row([['BOL #',$v('bol'),.34],['Date of shipment',$date($v('shipment_date')),.33],['Payment method',$v('payment_method'),.33]]);
			$pdf->section('Shipper and consignee');
			$pdf->row([['Shipper',$v('shipper_name'),.5],['Consignee',$v('consignee_name'),.5]]);
			$pdf->row([['Shipper address',$v('shipper_address'),.5],['Consignee address',$v('consignee_address'),.5]]);
			$pdf->row([['Contact',$v('shipper_contact'),.25],['Phone',$v('shipper_phone'),.25],['Contact',$v('consignee_contact'),.25],['Phone',$v('consignee_phone'),.25]]);
			$pdf->row([['Email',$v('shipper_email'),.32],['Fax',$v('shipper_fax'),.18],['Email',$v('consignee_email'),.32],['Fax',$v('consignee_fax'),.18]]);
			$pdf->section('Goods and claim');
			$pdf->row([['Commodity',$v('commodity'),.5],['Quantity',$v('quantity'),.2],['Weight',$v('weight'),.3]]);
			$pdf->row([['Type of claim',$v('incident'),.5],['Claim amount',$money('claim_amount'),.5]]);
			$pdf->row([['If lost, amount of pieces',$v('lost_pieces'),.5],['If damaged, extent of damage',$v('damage'),.5]]);
			// Signature on the left, the supporting documents beside it.
			$pdf->need(190);$pdf->section('Claimant and supporting documents');
			$checks(['consent_signature','consent_contact']);
			$pdf->need(130);$y=$pdf->GetY()+6;
			$pdf->SetFont('Helvetica','B',6.5);$pdf->SetTextColor(...PA_Designed_PDF::MUTED);$pdf->SetXY(364,$y);$pdf->Cell(200,9,'SUPPORTING DOCUMENTS');$pdf->SetY($y+13);
			foreach(['invoice','repairs','photos'] as $i=>$slot){$file=$p['files'][$slot]??null;$pdf->check((bool)$file,$text['claim']['attachments'][$i].($file?' Attached: '.$file['name']:''),364,200);}
			$docsEnd=$pdf->GetY();
			$end=$pdf->signature(48,300,$y,'Signature of claimant',$sig('claim')['image']??null,[['Name',$v('claim_signer')],['Phone',$v('phone')],['Email',$v('email')],['Date',$date($sig('claim')['signed_at']??'')]],$temp);$pdf->SetY(max($end,$docsEnd));
			$record('claim');
		}
	}
	return $pdf->Output('S');}finally{foreach($temp as $file)@unlink($file);}
}
