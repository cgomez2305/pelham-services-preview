<?php
if (!defined('ABSPATH')) exit;

// Defer to a dedicated SEO plugin when one owns the page's metadata.
function pelham_theme_seo_enabled() {
 return apply_filters('pelham_theme_seo_enabled', !defined('WPSEO_VERSION') && !defined('RANK_MATH_VERSION') && !defined('AIOSEO_VERSION') && !defined('SEOPRESS_VERSION'));
}
add_filter('pre_get_document_title',function($title){
 if(!is_singular()||!pelham_theme_seo_enabled())return $title;
 $post=get_queried_object();$custom=get_post_meta($post->ID,'pelham_seo_title',true);
 if($custom)return wp_strip_all_tags($custom);
 $data=function_exists('pelham_keyword_data')?pelham_keyword_data($post):[];
 return $data['seo_title']??$title;
},20);
function pelham_seo_text($text) {
 $text = preg_replace('/\[[^\]]*\]/', ' ', strip_shortcodes($text));
 return trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($text)));
}
function pelham_seo_description($post) {
 $custom = pelham_seo_text(get_post_meta($post->ID, 'pelham_seo_description', true));
 if ($custom) return $custom;
 $data = function_exists('pelham_keyword_data') ? pelham_keyword_data($post) : [];
 if (!empty($data['description'])) return pelham_seo_text($data['description']);
 $defaults = [
  'home'=>'LTL pallet shipping from Los Angeles to Florida. Compare Expedited, Regular and Economy, plus full truckload and freight handling services.',
  'services'=>'Explore domestic LTL and FTL trucking from Los Angeles to Florida, cross-docking and palletizing.',
  'locations'=>'Explore Pelham facilities in Los Angeles, Orlando and Miami. Confirm receiving arrangements with the team.',
  'news'=>'Read Pelham Services insights on freight transportation, logistics, consolidation and transloading between California and Florida.',
  'contact'=>'Contact Pelham Services for freight transportation support. Reach our Gardena and Miami terminals or send a message to our team.',
  'quote'=>'Request an LTL or FTL quote from Los Angeles to Florida. Share your pallet count, dimensions, weight, pickup and business delivery details.'
 ];
 $excerpt = pelham_seo_text($post->post_excerpt);
 // Imported shortcode-only or boilerplate excerpts need a useful public summary.
 if (isset($defaults[$post->post_name]) && (!$excerpt || str_contains($post->post_excerpt, '[pelham_') || is_front_page())) return $defaults[$post->post_name];
 $text = $excerpt ?: pelham_seo_text($post->post_content);
 return $text ? wp_html_excerpt($text, 160, '…') : 'Explore '.get_the_title($post).' at '.get_bloginfo('name').'.';
}
function pelham_postal_address($raw) {
 $lines = preg_split('/\r?\n/', trim($raw));
 if (count($lines) > 1 && preg_match('/^(.+?),\s*([A-Z]{2})\s+(\d{5}(?:-\d{4})?)$/', trim(end($lines)), $match)) {
  return ['@type'=>'PostalAddress', 'streetAddress'=>implode(', ', array_slice($lines,0,-1)), 'addressLocality'=>$match[1], 'addressRegion'=>$match[2], 'postalCode'=>$match[3], 'addressCountry'=>'US'];
 }
 return $raw;
}
/** A facility identity is shared by directory, company and facility pages. */
function pelham_terminal_entity($post, $organization_id) {
 $address=trim((string)get_post_meta($post->ID,'pelham_address',true));
 if(!$address)return null;
 $url=get_permalink($post);
 $entity=['@type'=>'LocalBusiness','@id'=>$url.'#terminal','name'=>get_the_title($post),'url'=>$url,'address'=>pelham_postal_address($address),'parentOrganization'=>['@id'=>$organization_id]];
 $phone=trim((string)get_post_meta($post->ID,'pelham_phone',true));if($phone)$entity['telephone']=$phone;
 return $entity;
}
// Archive routes are discoverable through WordPress sitemaps too. They need
// their own metadata; singular rel_canonical() does not cover these routes.
add_action('wp_head',function(){
 if(!is_archive()||!pelham_theme_seo_enabled())return;
 $url=remove_query_arg(['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid'],get_pagenum_link(max(1,(int)get_query_var('paged')),false));
 $title=wp_get_document_title();
 $description='Browse '.wp_strip_all_tags(get_the_archive_title()).' articles from '.get_bloginfo('name').': freight planning, transport and company updates.';
 if(get_query_var('paged')>1)$description.=' Page '.(int)get_query_var('paged').'.';
 echo '<link rel="canonical" href="'.esc_url($url).'">'."\n";
 echo '<meta name="description" content="'.esc_attr($description).'">'."\n";
 $logo_id=get_theme_mod('custom_logo');
 $image=($logo_id?wp_get_attachment_image_url($logo_id,'full'):false)?:get_template_directory_uri().'/assets/pelham-logo.png';
 foreach(['og:type'=>'website','og:title'=>$title,'og:description'=>$description,'og:url'=>$url,'og:site_name'=>get_bloginfo('name'),'og:locale'=>str_replace('-','_',get_bloginfo('language')),'og:image'=>$image,'og:image:alt'=>get_bloginfo('name')] as $key=>$value)echo '<meta property="'.esc_attr($key).'" content="'.esc_attr($value).'">'."\n";
 foreach(['twitter:card'=>'summary','twitter:title'=>$title,'twitter:description'=>$description,'twitter:image'=>$image] as $key=>$value)echo '<meta name="'.esc_attr($key).'" content="'.esc_attr($value).'">'."\n";
 echo '<script type="application/ld+json">'.wp_json_encode(['@context'=>'https://schema.org','@type'=>'CollectionPage','@id'=>$url.'#webpage','url'=>$url,'name'=>$title,'description'=>$description,'inLanguage'=>get_bloginfo('language')],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script>'."\n";
},20);
add_action('wp_head',function(){
 if (!is_singular() || !pelham_theme_seo_enabled()) return;
 $post = get_queried_object();
 $url = get_permalink($post);
 $title = wp_get_document_title();
 $description = pelham_seo_description($post);
 $logo_id = get_theme_mod('custom_logo');
 $logo = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : get_template_directory_uri().'/assets/pelham-logo.png';
 $photo_data=function_exists('pelham_keyword_data')?pelham_keyword_data($post):[];
 $stock=function_exists('pelham_photo_data')?pelham_photo_data($photo_data['photo']??'truck'):[];
 $image = get_the_post_thumbnail_url($post, 'large') ?: ($stock['url']??$logo);
 $image_alt = has_post_thumbnail($post) ? get_post_meta(get_post_thumbnail_id($post),'_wp_attachment_image_alt',true) : ($stock['alt']??get_bloginfo('name'));
 echo '<meta name="description" content="'.esc_attr($description).'">'."\n";
 $meta = ['og:type'=>get_post_type($post)==='post'?'article':'website', 'og:title'=>$title, 'og:description'=>$description, 'og:url'=>$url, 'og:site_name'=>get_bloginfo('name'), 'og:locale'=>str_replace('-', '_', get_bloginfo('language')), 'og:image'=>$image, 'og:image:alt'=>$image_alt];
 foreach ($meta as $property=>$value) echo '<meta property="'.esc_attr($property).'" content="'.esc_attr($value).'">'."\n";
 foreach (['twitter:card'=>'summary_large_image','twitter:title'=>$title,'twitter:description'=>$description,'twitter:image'=>$image,'twitter:image:alt'=>$image_alt] as $name=>$value) echo '<meta name="'.esc_attr($name).'" content="'.esc_attr($value).'">'."\n";
 $org = ['@type'=>'Organization','@id'=>home_url('/').'#organization','name'=>get_bloginfo('name'),'url'=>home_url('/'),'logo'=>$logo];
 if (pelham_detail('email')) $org['email'] = pelham_detail('email');
 $defaults = function_exists('pelham_defaults') ? pelham_defaults() : [];
 $social = array_values(array_filter([pelham_detail('facebook',$defaults['facebook']??''),pelham_detail('linkedin',$defaults['linkedin']??'')],fn($url)=>!empty($url)&&in_array(wp_parse_url($url,PHP_URL_SCHEME),['https','http'],true)&&wp_parse_url($url,PHP_URL_HOST)));
 if ($social) $org['sameAs'] = $social;
 $website = ['@type'=>'WebSite','@id'=>home_url('/').'#website','url'=>home_url('/'),'name'=>get_bloginfo('name'),'publisher'=>['@id'=>$org['@id']],'inLanguage'=>get_bloginfo('language')];
 $page = ['@type'=>'WebPage','@id'=>$url.'#webpage','url'=>$url,'name'=>$title,'description'=>$description,'publisher'=>['@id'=>$org['@id']]];
 $page['isPartOf'] = ['@id'=>$website['@id']];
 $page['inLanguage'] = get_bloginfo('language');
 $page['breadcrumb'] = ['@id'=>$url.'#breadcrumb'];
 $breadcrumbs = [['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>home_url('/')]];
 $type = get_post_type($post);
 if (!is_front_page()) {
  $parent = $type==='pelham_service' ? ['services','Services'] : ($type==='pelham_terminal' ? ['locations','Our network'] : ($type==='post' ? ['news','Insights'] : null));
  if ($parent) $breadcrumbs[] = ['@type'=>'ListItem','position'=>2,'name'=>$parent[1],'item'=>pelham_link($parent[0])];
  $breadcrumbs[] = ['@type'=>'ListItem','position'=>count($breadcrumbs)+1,'name'=>get_the_title($post),'item'=>$url];
 }
 $graph = [$org,$website,$page,['@type'=>'BreadcrumbList','@id'=>$url.'#breadcrumb','itemListElement'=>$breadcrumbs]];
 if ($type==='pelham_service') {
  $service=['@type'=>'Service','@id'=>$url.'#service','name'=>get_the_title($post),'description'=>$description,'url'=>$url,'provider'=>['@id'=>$org['@id']]];
  if($post->post_name==='freight-consolidation'&&function_exists('pelham_ltl_levels')){
   $service['serviceType']='Domestic less-than-truckload pallet shipping from Los Angeles to Florida';
   $service['areaServed']=[['@type'=>'City','name'=>'Los Angeles'],['@type'=>'State','name'=>'Florida']];$offers=[];
   foreach(pelham_ltl_levels() as $key=>$level)$offers[]=['@type'=>'Offer','url'=>add_query_arg(['service'=>'freight-consolidation','ltl_level'=>$key],pelham_link('quote')),'itemOffered'=>['@type'=>'Service','name'=>'LTL '.$level[0],'serviceType'=>'Domestic LTL trucking from Los Angeles','areaServed'=>$key==='expedited'?['@type'=>'Place','name'=>'South Florida: Homestead to West Palm Beach']:['@type'=>'State','name'=>'Florida'],'description'=>$level[1].' calendar days from pickup in Los Angeles, estimated and not guaranteed. '.$level[2]]];
   $service['hasOfferCatalog']=['@type'=>'OfferCatalog','name'=>'Pelham LTL transit levels','itemListElement'=>$offers];
  }
  $graph[2]['mainEntity']=['@id'=>$service['@id']];
  $graph[]=$service;
 }
 if ($type==='pelham_terminal') {
  $local=pelham_terminal_entity($post,$org['@id']);
  if($local){$graph[]=$local;$graph[2]['mainEntity']=['@id'=>$local['@id']];}
 }
 // Match entities already shown in the public facility directory and footer.
 if(is_front_page()||is_page(['about','locations'])){
  $branches=[];
  foreach(get_posts(['post_type'=>'pelham_terminal','post_status'=>'publish','numberposts'=>-1]) as $terminal){
   $local=pelham_terminal_entity($terminal,$org['@id']);
   if($local){$branches[]=['@id'=>$local['@id']];$graph[]=$local;}
  }
  if($branches)$graph[0]['subOrganization']=$branches;
  if(is_front_page()||is_page('about'))$graph[2]['mainEntity']=['@id'=>$org['@id']];
 }
 if ($type==='post') {
  $article = ['@type'=>'Article','@id'=>$url.'#article','headline'=>get_the_title($post),'description'=>$description,'datePublished'=>get_the_date(DATE_W3C,$post),'dateModified'=>get_the_modified_date(DATE_W3C,$post),'publisher'=>['@id'=>$org['@id']],'mainEntityOfPage'=>['@id'=>$page['@id']]];
  $article['inLanguage']=get_bloginfo('language');
  $graph[2]['mainEntity']=['@id'=>$article['@id']];
  if (has_post_thumbnail($post)) $article['image']=$image;
  $graph[]=$article;
 }
 echo '<script type="application/ld+json">'.wp_json_encode(['@context'=>'https://schema.org','@graph'=>$graph],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script>'."\n";
},20);
