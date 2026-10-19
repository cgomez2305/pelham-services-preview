<?php
/** Who we serve: suppliers, distributors, manufacturers and freight forwarders. */
if (!defined('ABSPATH')) exit;
function pelham_audience_data(){static $data;if(!isset($data))$data=json_decode(file_get_contents(__DIR__.'/../data/audiences.json'),true);return $data;}
function pelham_audience_link($segment=''){return pelham_link(pelham_audience_data()['page']).($segment?'#'.$segment:'');}
function pelham_audience_rows($items){
 $segments=pelham_audience_data()['segments'];ob_start();?><ul class="audience-rows"><?php foreach($items as $key=>$note){if(!isset($segments[$key]))continue;?><li><a href="<?php echo esc_url(pelham_audience_link($key)); ?>"><span class="audience-name"><?php echo esc_html($segments[$key]['label']); ?></span><span class="audience-note"><?php echo esc_html($note); ?></span><span class="direction-arrow" aria-hidden="true"></span></a></li><?php } ?></ul><?php return ob_get_clean();
}
// [pelham_audiences variant="home"] or [pelham_audiences variant="service" service="transloading"]
add_shortcode('pelham_audiences',function($attrs){
 $attrs=shortcode_atts(['variant'=>'home','service'=>''],$attrs);$data=pelham_audience_data();
 if($attrs['variant']==='service'){
  $config=$data['services'][$attrs['service']]??null;if(!$config)return '';
  return '<section class="shell audience-band audience-band-service" aria-labelledby="audience-'.esc_attr($attrs['service']).'"><div class="section-heading"><h2 id="audience-'.esc_attr($attrs['service']).'">'.esc_html($config['heading']).'</h2><p><a href="'.esc_url(pelham_audience_link()).'">How we work with each business</a></p></div>'.pelham_audience_rows($config['items']).'</section>';
 }
 $items=array_map(fn($s)=>$s['home'],$data['segments']);
 return '<section class="shell section audience-band" aria-labelledby="who-we-ship-for"><div class="section-heading"><h2 id="who-we-ship-for">Who we<br>ship <em>for.</em></h2><p>From California suppliers and factories to Florida distributors and forwarders. <a href="'.esc_url(pelham_audience_link()).'">How we work with your business</a></p></div>'.pelham_audience_rows($items).'</section>';
});
// Homepage: shown after the services rows without editing the saved page. If
// the layout was customized (no services/network sections), nothing is added.
add_filter('the_content',function($content){
 if(!is_front_page()||!in_the_loop()||!is_main_query()||str_contains($content,'audience-band'))return $content;
 $anchor='<div class="wp-block-group shell section network-grid industrial-network';
 $at=strpos($content,$anchor);if($at===false)return $content;
 return substr($content,0,$at).do_shortcode('[pelham_audiences variant="home"]').substr($content,$at);
},20);
// Create the Who we serve page once, only if the slug is free. Never overwrites.
add_action('admin_init',function(){
 if(!current_user_can('manage_options')||get_option('pelham_audiences_page'))return;
 $slug=pelham_audience_data()['page'];
 if($existing=get_page_by_path($slug)){update_option('pelham_audiences_page',$existing->ID,false);return;}
 $seo=function_exists('pelham_keyword_data')?pelham_keyword_data((object)['post_name'=>$slug]):[];
 $id=wp_insert_post(wp_slash(['post_type'=>'page','post_status'=>'publish','post_name'=>$slug,'post_title'=>$seo['heading']??'Who we serve','post_excerpt'=>$seo['description']??'','post_content'=>pelham_home_seed_content('who-we-serve.html')]),true);
 if(is_wp_error($id))return;
 update_option('pelham_audiences_page',$id,false);
 // Add it to the assigned primary menu right after Services; other items keep their order.
 $menu=get_nav_menu_locations()['primary']??0;if(!$menu)return;
 $items=wp_get_nav_menu_items($menu)?:[];$position=count($items)+1;
 foreach($items as $item){if((int)$item->object_id===(int)$id)return;if(untrailingslashit(wp_parse_url($item->url,PHP_URL_PATH))==='/services'){$position=$item->menu_order+1;}}
 foreach($items as $item){if($item->menu_order>=$position)wp_update_post(['ID'=>$item->ID,'menu_order'=>$item->menu_order+1]);}
 wp_update_nav_menu_item($menu,0,['menu-item-object-id'=>$id,'menu-item-object'=>'page','menu-item-type'=>'post_type','menu-item-title'=>'Who we serve','menu-item-status'=>'publish','menu-item-position'=>$position]);
});
