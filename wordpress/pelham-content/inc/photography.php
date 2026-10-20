<?php
if (!defined('ABSPATH')) exit;
function pelham_photography_catalog(){static $catalog;if(!isset($catalog))$catalog=json_decode(file_get_contents(dirname(__DIR__).'/data/photography.json'),true);return $catalog;}
function pelham_photo_data($key){
 $catalog=pelham_photography_catalog();if(!isset($catalog[$key]))return [];
 $photo=$catalog[$key];
 if(function_exists('pelham_t'))$photo['alt']=pelham_t($photo['alt']);
 $photo['url']=plugins_url('assets/photography/'.$photo['files']['1600'],dirname(__DIR__).'/pelham-content.php');
 $ids=get_option('pelham_stock_media',[]);$id=absint($ids[$key]??0);
 // Use a Media copy only if it was imported from this exact catalog photo;
 // copies of earlier photography under the same key are ignored.
 if($id&&wp_get_attachment_url($id)&&get_post_meta($id,'pelham_photo_source',true)===$photo['source']){$photo['url']=wp_get_attachment_url($id);$photo['id']=$id;}
 return $photo;
}
function pelham_photo_markup($key,$class='',$eager=false,$post_id=0){
 static $seen=[];
 $owners=['pelham-services'=>'home','pelham-hero'=>'contact','pelham-green'=>'dedicated-transport','pelham-truck'=>'transloading','pelham-driver'=>'freight-consolidation'];
 if(isset($owners[$key])&&($owners[$key]==='home'?!pelham_is_home():pelham_route_slug(get_queried_object_id())!==$owners[$key]))return '';

 $known=get_option('pelham_stock_media',[]);
 if($post_id&&has_post_thumbnail($post_id)&&!in_array(get_post_thumbnail_id($post_id),$known,true)){
  $identity='attachment:'.get_post_thumbnail_id($post_id);if(isset($seen[$identity]))return ''; $seen[$identity]=true;
  return get_the_post_thumbnail($post_id,'large',['class'=>'freight-photo-image '.$class,'loading'=>$eager?'eager':'lazy','fetchpriority'=>$eager?'high':'auto']);
 }
 $photo=pelham_photo_data($key);if(!$photo)return '';if(isset($seen[$photo['sha256']]))return ''; $seen[$photo['sha256']]=true;
 if(!empty($photo['id']))return wp_get_attachment_image($photo['id'],'large',false,['class'=>'freight-photo-image '.$class,'loading'=>$eager?'eager':'lazy','fetchpriority'=>$eager?'high':'auto','style'=>'object-position:'.$photo['position']]);
 $srcset=[];foreach($photo['files'] as $width=>$file)$srcset[]=plugins_url('assets/photography/'.$file,dirname(__DIR__).'/pelham-content.php').' '.absint($width).'w';
 // Cover crops display a panoramic image at a larger effective width than
 // the narrow column. Account for that width when choosing a source.
 $sizes='(max-width: 600px) calc(100vw - 40px), (max-width: 1000px) 70vw, 800px';
 // The homepage panorama spans the full content width (up to 1200px).
 if($key==='pelham-services'&&pelham_is_home())$sizes='(max-width: 600px) calc(100vw - 40px), min(1200px, calc(100vw - 80px))';
 // Article openings span the full content width.
 if($eager&&is_singular('post'))$sizes='(max-width: 760px) calc(100vw - 40px), min(1200px, calc(100vw - 80px))';
 // News cards: one column on phones, two on the insights page, three on home.
 if(str_contains($class,'news-photo'))$sizes='(max-width: 760px) calc(100vw - 40px), (max-width: 1240px) 50vw, 600px';
 // Service covers are rendered eagerly as the page's opening photo.
 if($eager&&is_singular('pelham_service')){
  $ratio=$photo['width']/max(1,$photo['height']);
  $sizes='(max-width: 680px) '.(int)ceil(290*$ratio).'px, '.(int)ceil(600*$ratio).'px';
 }
 return '<img style="object-position:'.esc_attr($photo['position']).'" srcset="'.esc_attr(implode(', ',$srcset)).'" sizes="'.esc_attr($sizes).'" class="freight-photo-image '.esc_attr($class).'" src="'.esc_url($photo['url']).'" alt="'.esc_attr($photo['alt']).'" width="'.absint($photo['width']).'" height="'.absint($photo['height']).'" loading="'.($eager?'eager':'lazy').'" decoding="async" fetchpriority="'.($eager?'high':'auto').'">';
}
// The homepage panorama sits above the fold: lazy-loading it delays LCP.
add_shortcode('pelham_photo',function($attrs){$attrs=shortcode_atts(['key'=>'truck','caption'=>'','eager'=>''],$attrs);$eager=in_array(strtolower((string)$attrs['eager']),['1','true','yes'],true)||($attrs['key']==='pelham-services'&&pelham_is_home());return '<figure class="freight-photo">'.pelham_photo_markup($attrs['key'],'',$eager).($attrs['caption']?'<figcaption>'.esc_html($attrs['caption']).'</figcaption>':'').'</figure>';});
add_action('admin_menu',function(){add_submenu_page('pelham-company','Photography','Photography','upload_files','pelham-photography',function(){
 if(!current_user_can('upload_files'))return;
 echo '<div class="wrap"><h1>Pelham — website images</h1><p>These photographs come from the professional shoot at the Gardena terminal supplied by Pelham Services. They are bundled locally as optimized WebP files (640, 960 and 1600 px), without retouching or metadata. Set a featured image in any page, article or service to replace default photography. Imported images can be edited in Media.</p>';
 if(isset($_GET['photo_imported']))echo '<div class="notice notice-info"><p>Import attempted. Successfully downloaded images appear in Media. Photographs not imported continue using bundled local files.</p></div>';
 echo '<table class="widefat"><thead><tr><th>Subject</th><th>Source and license</th><th>Local media</th></tr></thead><tbody>';
 $ids=get_option('pelham_stock_media',[]);foreach(pelham_photography_catalog() as $key=>$photo){echo '<tr><td>'.esc_html($key).'</td><td><a href="'.esc_url($photo['source']).'" target="_blank" rel="noopener">Source</a> · <a href="'.esc_url($photo['license']).'" target="_blank" rel="noopener">License</a></td><td>'.(!empty($ids[$key])?esc_html((string)$ids[$key]):'Bundled locally').'</td></tr>';}
 echo '</tbody></table><p>Import the bundled photos into Media to manage them alongside your own uploads. No network connection is required; source details are recorded on each image.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_import_photos">';wp_nonce_field('pelham_import_photos');submit_button('Import original images into Media');echo '</form></div>';
 });});
add_action('admin_post_pelham_import_photos',function(){
 if(!current_user_can('upload_files'))wp_die('Forbidden',403);check_admin_referer('pelham_import_photos');
 require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
 $ids=get_option('pelham_stock_media',[]);
 foreach(pelham_photography_catalog() as $key=>$photo){
  if(!empty($ids[$key])&&wp_get_attachment_url($ids[$key])&&get_post_meta($ids[$key],'pelham_photo_source',true)===$photo['source'])continue;
  // Copy only reviewed files from the bundled manifest; never fetch a public URL.
  $source=dirname(__DIR__).'/assets/photography/'.basename($photo['files']['1600']);
  $tmp=wp_tempnam($source);if(!$tmp||!copy($source,$tmp))continue;
  $id=media_handle_sideload(['name'=>'pelham-photo-'.$key.'.webp','tmp_name'=>$tmp],0,$photo['alt']);
  if(is_wp_error($id)){wp_delete_file($tmp);continue;}
  $ids[$key]=$id;update_post_meta($id,'_wp_attachment_image_alt',$photo['alt']);update_post_meta($id,'pelham_photo_source',$photo['source']);update_post_meta($id,'pelham_photo_license',$photo['license']);update_post_meta($id,'pelham_photo_author',$photo['author']??$photo['bank']);
 }
 update_option('pelham_stock_media',$ids);wp_safe_redirect(admin_url('admin.php?page=pelham-photography&photo_imported=1'));exit;
});
