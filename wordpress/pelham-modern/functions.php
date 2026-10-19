<?php
if (!defined('ABSPATH')) exit;
add_action('after_setup_theme', function () {
 add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('custom-logo');
 add_theme_support('responsive-embeds'); add_theme_support('editor-styles');
 add_editor_style('assets/site.css');
 register_nav_menus(['primary'=>'Primary navigation','footer'=>'Footer navigation']);
});
// Version assets by file modification time so every edited file busts browser
// and CDN caches, instead of hand-maintained version strings that drift.
function pelham_asset_version($file) {
 $path=get_template_directory().'/assets/'.$file;
 return file_exists($path) ? (string) filemtime($path) : wp_get_theme()->get('Version');
}
add_action('wp_enqueue_scripts', function () {
 $base=get_template_directory_uri().'/assets/';$previous=[];
 foreach(['site','experience','stories','refined','industrial','complete','restructure','usability','design-system'] as $name){
  wp_enqueue_style('pelham-'.$name,$base.$name.'.css',$previous,pelham_asset_version($name.'.css'));
  $previous=['pelham-'.$name];
 }
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
 $options=get_option('pelham_company', []); return $options[$key] ?? $fallback; }
function pelham_link($path) { return home_url('/'.trim($path,'/').'/'); }

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
 return $target !== '' && strpos($current, $target.'/') === 0 ? 'location' : '';
}
add_filter('nav_menu_link_attributes', function ($atts, $item, $args) {
 if (($args->theme_location ?? '') !== 'primary') return $atts;
 $current = pelham_navigation_current($item->url);
 if ($current) $atts['aria-current'] = $current;
 return $atts;
}, 10, 3);
