<?php
if(!defined('ABSPATH'))exit;
function pelham_social_links(){
 $opts=get_option('pelham_company',[]);$defaults=pelham_defaults();$html='<nav class="social-links" aria-label="Pelham social profiles">';
 foreach(['facebook'=>'Facebook','linkedin'=>'LinkedIn'] as $key=>$label){$url=$opts[$key]??$defaults[$key];if(!$url)continue;$html.='<a href="'.esc_url($url).'" target="_blank" rel="noopener">'.esc_html($label).'<span class="screen-reader-text"> (opens in a new tab)</span></a>';}
 return $html.'</nav>';
}
add_shortcode('pelham_social',function(){return pelham_social_links();});
function pelham_document_url($key){
 $files=['claim_form_url'=>'Claim-Form.pdf','customer_application_url'=>'Customer-Application.pdf','credit_application_url'=>'Credit-Application.pdf','terms_url'=>'Terms-and-Agreements-Pelham.pdf'];
 $o=get_option('pelham_company',[]);return !empty($o[$key])?$o[$key]:plugins_url('assets/documents/'.$files[$key],dirname(__DIR__).'/pelham-content.php');
}
add_shortcode('pelham_registration_documents',function(){
 $html='<div class="registration-documents"><ul>';
 foreach(['claim_form_url'=>'Claim form','customer_application_url'=>'Customer application','credit_application_url'=>'Credit application','terms_url'=>'Terms and agreements'] as $key=>$label){$html.='<li><a href="'.esc_url(pelham_document_url($key)).'" download>'.esc_html($label).'<span>PDF</span></a></li>';}
 return $html.'</ul></div>';
});
add_shortcode('pelham_terms_document',function(){return '<p><a class="button button-dark" href="'.esc_url(pelham_document_url('terms_url')).'">Read Pelham terms and agreements (PDF)</a></p>';});
add_shortcode('pelham_legal_contact',function(){return '<p>For questions about this notice or your information, <a href="'.esc_url(pelham_link('contact')).'">contact Pelham Services</a> at <a href="mailto:'.esc_attr(pelham_detail('email','info@pelhamservicesca.com')).'">'.esc_html(pelham_detail('email','info@pelhamservicesca.com')).'</a>.</p>';});
add_shortcode('pelham_registration_form',function(){ob_start();$state=sanitize_key($_GET['pelham_status']??''); ?>
<form class="pelham-form registration-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" aria-label="Customer registration" tabindex="-1">
<input type="hidden" name="action" value="pelham_registration"><?php wp_nonce_field('pelham_registration','pelham_nonce'); ?>
<?php if($state){$messages=['sent'=>'Your registration request was sent. The team will review it; this does not automatically create portal access.','invalid'=>'Check your name, email and consent. Attach only PDF files, up to 4 MB each and 16 MB total.','failed'=>'Your request could not be sent. Reattach your files to retry or contact the team. Files are not kept on the website.','limited'=>'Please wait a few minutes before trying again.'];?><p class="form-status" role="<?php echo $state==='sent'?'status':'alert'; ?>" tabindex="-1"><?php echo esc_html($messages[$state]??$messages['invalid']); ?></p><?php } ?>
<h2>Register your interest</h2><p class="registration-expectation">This is a request for team review, not an instant portal account. Already a customer? <a href="https://pelham.supply-vision.com/Portal/Login">Login to your account</a>.</p><p class="form-help">Fields marked * are required.</p><fieldset><legend>Your contact details</legend><div class="form-grid">
<label>First name *<input name="first_name" autocomplete="given-name" required maxlength="100"></label><label>Last name *<input name="last_name" autocomplete="family-name" required maxlength="100"></label><label>Email address *<input type="email" name="email" autocomplete="email" required maxlength="150"></label><label>Phone (optional)<input type="tel" name="phone" autocomplete="tel" maxlength="40"></label>
</div></fieldset><label>Message (optional)<textarea name="message" rows="4" maxlength="5000"></textarea></label>
<details class="optional-fields registration-uploads"><summary>Attach completed documents (optional)</summary><fieldset><legend>Completed documents</legend><p class="upload-note">PDF only. Up to 4 MB per file, 16 MB total. Downloads are on this page; uploads are optional.</p>
<?php foreach(['claim'=>'Claim form','customer'=>'Customer application','credit'=>'Credit application','terms'=>'Terms and agreement','attachment'=>'Additional attachment'] as $key=>$label){?><label><?php echo esc_html($label); ?> (optional)<input type="file" name="<?php echo esc_attr($key); ?>" accept=".pdf,application/pdf"></label><?php } ?></fieldset></details>
<label class="honey" aria-hidden="true">Leave empty<input name="website" tabindex="-1" autocomplete="off"></label><label class="consent"><input type="checkbox" name="consent" value="1" required>I agree to be contacted about this registration request.</label><p>Review our <a href="<?php echo esc_url(pelham_link('privacy-notice')); ?>">privacy policy</a>. Uploaded PDFs are sent to the team and removed from temporary website storage.</p><button class="button button-dark" type="submit">Send registration request</button>
</form>
<?php return pelham_form_errors(ob_get_clean(),'registration');});
function pelham_handle_registration(){
 wp_die('The legacy upload form is disabled. Please use the digital application or contact Pelham.','Registration unavailable',['response'=>410]);
}
add_action('admin_post_nopriv_pelham_registration','pelham_handle_registration');add_action('admin_post_pelham_registration','pelham_handle_registration');

function pelham_form_errors($html,$scope){
 $messages=['session'=>'This form expired. Review your details and submit again.','name'=>'Enter your name.','first_name'=>'Enter your first name, using up to 100 characters.','last_name'=>'Enter your last name, using up to 100 characters.','email'=>'Enter a valid email address.','message'=>'Enter your message using up to 5,000 characters.','origin'=>'Enter the pickup location.','destination'=>'Enter the delivery location.','consent'=>'Confirm that the team may contact you about this request.','files_total'=>'Keep the combined PDF attachments under 16 MB.'];
 foreach(['claim'=>'Claim form','customer'=>'Customer application','credit'=>'Credit application','terms'=>'Terms and agreement','attachment'=>'Additional attachment'] as $key=>$label)$messages[$key]=$label.': choose a PDF up to 4 MB and attach it again.';
 $keys=array_values(array_intersect(explode(',',sanitize_text_field(wp_unslash($_GET['pelham_errors']??''))),array_keys($messages)));if(!$keys)return $html;
 $items='';foreach($keys as $key){$id=$scope.'-'.$key;$text=esc_html($messages[$key]);$items.='<li id="'.esc_attr($id.'-error').'">'.(in_array($key,['session','files_total'],true)?$text:'<a href="#'.esc_attr($id).'">'.$text.'</a>').'</li>';}
 $html=preg_replace('/(<p class="form-status"[^>]*>).*?(<\/p>)/s','$1Please correct the following before sending.$2<ul class="form-errors">'.$items.'</ul>',$html,1);
 $html=preg_replace_callback('/<(input|textarea|select)\b([^>]*\bname="([a-z_]+)"[^>]*)>/',function($m)use($keys,$scope){if(!in_array($m[3],$keys,true))return $m[0];$id=$scope.'-'.$m[3];$attributes=$m[2];$described=$id.'-error';if(preg_match('/\saria-describedby="([^"]*)"/',$attributes,$existing)){$described=$existing[1].' '.$described;$attributes=preg_replace('/\saria-describedby="[^"]*"/','',$attributes);}return '<'.$m[1].$attributes.' id="'.esc_attr($id).'" aria-invalid="true" aria-describedby="'.esc_attr($described).'">';},$html);
 if(array_intersect($keys,['claim','customer','credit','terms','attachment','files_total']))$html=str_replace('<details class="optional-fields registration-uploads">','<details class="optional-fields registration-uploads" open>',$html);
 return $html;
}
