<?php
if (!defined('ABSPATH')) exit;
add_action('after_setup_theme', function () {
 add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('custom-logo');
 add_theme_support('responsive-embeds'); add_theme_support('editor-styles');
 add_editor_style('assets/theme.css');
 register_nav_menus(['primary'=>'Primary navigation','footer'=>'Footer navigation','primary_es'=>'Primary navigation (Spanish)','footer_es'=>'Footer navigation (Spanish)']);
});
// Version assets by file modification time so every edited file busts browser
// and CDN caches, instead of hand-maintained version strings that drift.
/** Theme logo URL with a content hash, so browsers and the host cache load a replaced logo at once. */
function pelham_logo_url($file) {
 $path=get_template_directory().'/assets/'.$file;
 return add_query_arg('ver', file_exists($path) ? substr(md5_file($path),0,8) : wp_get_theme()->get('Version'), get_template_directory_uri().'/assets/'.$file);
}
function pelham_asset_version($file) {
 $path=get_template_directory().'/assets/'.$file;
 return file_exists($path) ? (string) filemtime($path) : wp_get_theme()->get('Version');
}
add_action('wp_enqueue_scripts', function () {
 $base=get_template_directory_uri().'/assets/';
 // One stylesheet (the former nine layers, concatenated in cascade order).
 wp_enqueue_style('pelham-theme',$base.'theme.css',[],pelham_asset_version('theme.css'));
 wp_enqueue_script('pelham-site',$base.'site.js',[],pelham_asset_version('site.js'),true);
 wp_enqueue_script('pelham-editorial',$base.'editorial.js',['pelham-site'],pelham_asset_version('editorial.js'),true);
 wp_enqueue_script('pelham-motion-engine',$base.'vendor/motion-mini-14.1.0.min.js',[],'14.1.0',true);
 wp_enqueue_script('pelham-motion',$base.'motion.js',['pelham-site','pelham-motion-engine'],pelham_asset_version('motion.js'),true);
});
function pelham_detail($key, $fallback='') {
 if(preg_match('/^(ca|fl)_(name|address|phone)$/',$key,$m)){
  $posts=get_posts(['post_type'=>'pelham_terminal','name'=>$m[1]==='ca'?'gardena-ca':'miami-fl','numberposts'=>1]);
  if($posts){return $m[2]==='name'?$posts[0]->post_title:get_post_meta($posts[0]->ID,'pelham_'.$m[2],true);}
 }
 $options=get_option('pelham_company', []); return pelham_t($options[$key] ?? $fallback); }
// Without the Pelham Content plugin the site is English-only.
if (!function_exists('pelham_t')) { function pelham_t($text) { return $text; } }
if (!function_exists('pelham_lang')) { function pelham_lang() { return 'en'; } }
if (!function_exists('pelham_is_page')) { function pelham_is_page($slugs='') { return is_page($slugs); } }
if (!function_exists('pelham_is_home')) { function pelham_is_home() { return is_front_page(); } }
if (!function_exists('pelham_home_link')) { function pelham_home_link() { return home_url('/'); } }
if (!function_exists('pelham_date')) { function pelham_date($format='',$post=null,$modified=false) { return $modified?get_the_modified_date($format,$post):get_the_date($format,$post); } }
if (!function_exists('pelham_route_slug')) { function pelham_route_slug($post) { return get_post_field('post_name',$post); } }
/** Link to an English route, or to its Spanish page when the visitor is on the Spanish site. */
function pelham_link($path) {
 $path=trim($path,'/');
 if (function_exists('pelham_i18n_url') && ($url=pelham_i18n_url($path))) return $url;
 return home_url('/'.$path.'/');
}
/** Menu location for the current language, falling back to the English one only on the English site. */
function pelham_menu_location($location) {
 if (pelham_lang()==='es') return has_nav_menu($location.'_es') ? $location.'_es' : '';
 return has_nav_menu($location) ? $location : '';
}
// Breadcrumb trail shared by the visible navigation and the BreadcrumbList schema.
function pelham_breadcrumb_trail($post=null) {
 if (pelham_is_home() || is_404()) return [];
 $trail=[[pelham_t('Home'),pelham_home_link()]];
 $parents=['post'=>['news','Insights'],'pelham_service'=>['services','Services'],'pelham_terminal'=>['locations','Our network']];
 if (is_archive() || is_home()) { $trail[]=[pelham_t('Insights'),pelham_link('news')]; if (is_archive()) $trail[]=[wp_strip_all_tags(get_the_archive_title()),'']; return $trail; }
 $post=get_post($post); if (!$post) return $trail;
 if (isset($parents[$post->post_type])) $trail[]=[pelham_t($parents[$post->post_type][1]),pelham_link($parents[$post->post_type][0])];
 // The Spanish home page is the parent of Spanish pages; it is already the first crumb.
 foreach (array_reverse(get_post_ancestors($post)) as $ancestor) if ((int)$ancestor!==(int)get_option('pelham_es_home')) $trail[]=[get_the_title($ancestor),get_permalink($ancestor)];
 // Main pages use the short label people see in the navigation, not their long H1.
 $labels=['services'=>'Services','who-we-serve'=>'Who we serve','locations'=>'Our network','about'=>'About us','news'=>'Insights','contact'=>'Contact','quote'=>'Request a quote','register'=>'Register'];
 $route=pelham_route_slug($post);
 $trail[]=[$post->post_type==='page'&&isset($labels[$route])?pelham_t($labels[$route]):wp_strip_all_tags(get_the_title($post)),get_permalink($post)];
 return $trail;
}
function pelham_breadcrumbs() {
 $trail=pelham_breadcrumb_trail(); if (count($trail)<2) return;
 $last=count($trail)-1;
 echo '<nav class="story-breadcrumb" aria-label="'.esc_attr(pelham_t('Breadcrumb')).'"><ol>';
 foreach ($trail as $i=>[$name,$url]) echo $i===$last ? '<li><span aria-current="page" title="'.esc_attr($name).'">'.esc_html($name).'</span></li>' : '<li><a href="'.esc_url($url).'">'.esc_html($name).'</a></li>';
 echo '</ol></nav>';
}

require_once get_template_directory().'/inc/seo.php';

add_action('admin_notices',function(){if(current_user_can('activate_plugins')&&!shortcode_exists('pelham_registration_form'))echo '<div class="notice notice-error"><p>Pelham needs the Pelham Content plugin for services, forms and registration. Install and activate the matching plugin ZIP included with this theme.</p></div>';});

// Prepare only editorial openings before first paint; expire safely if scripts fail.
add_action('wp_head',function(){
 echo '<style id="pelham-entrance-boot">'.file_get_contents(__DIR__.'/assets/motion-bootstrap.css').'</style>';
 echo '<script id="pelham-entrance-init">'.file_get_contents(__DIR__.'/assets/motion-bootstrap.js').'</script>';
 foreach(['site.js?ver='.pelham_asset_version('site.js'),'vendor/motion-mini-14.1.0.min.js?ver=14.1.0','motion.js?ver='.pelham_asset_version('motion.js')] as $file)echo '<link rel="preload" as="script" href="'.esc_url(get_template_directory_uri().'/assets/'.$file).'">';
},1);

// Mark the active section for flat WordPress pages as well as nested menus.
function pelham_navigation_current($url) {
 if (wp_parse_url($url, PHP_URL_HOST) !== wp_parse_url(home_url('/'), PHP_URL_HOST)) return '';
 $current = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
 $target = trim((string) wp_parse_url($url, PHP_URL_PATH), '/');
 if ($current === $target) return 'page';
 // The home page (English or Spanish) is only current on itself, not on every page below it.
 if ($target === trim((string) wp_parse_url(pelham_home_link(), PHP_URL_PATH), '/')) return '';
 return $target !== '' && strpos($current, $target.'/') === 0 ? 'location' : '';
}
add_filter('nav_menu_link_attributes', function ($atts, $item, $args) {
 if (!in_array($args->theme_location ?? '', ['primary','primary_es'], true)) return $atts;
 $current = pelham_navigation_current($item->url);
 if ($current) $atts['aria-current'] = $current;
 return $atts;
}, 10, 3);
