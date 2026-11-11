<?php
/**
 * Local SEO 1.22 (auditoría de producción del 11/11/2026, docs/SEO-LOCAL-AUDIT-PLAN-2026-11.md).
 *
 * 1. 301 de URL heredadas que seguían publicadas y duplicaban páginas nuevas, y de
 *    /contact-information/, la web que enlaza el Perfil de Empresa de Google de Gardena (404).
 * 2. Esas URL salen del sitemap; los archivos de categoría y etiqueta pasan a noindex
 *    y sus sitemaps se retiran (eran listados finos de artículos antiguos).
 * 3. Las páginas de ubicación muestran los niveles LTL que aplican desde/hacia esa sede
 *    y enlaces a los servicios, con los mismos plazos ya publicados en la oferta.
 */
if(!defined('ABSPATH'))exit;

/** Ruta antigua (sin barras) => ruta nueva. Solo URL comprobadas en producción. */
function pelham_local_seo_redirects(){
 return [
  'contact-information'=>'locations/gardena-ca',
  'contact-us'=>'contact',
  'team'=>'about',
  'forms'=>'register',
  'blog'=>'news',
  'news/hello-world'=>'news/how-pelham-elevates-logistics-in-ca-fl',
  'hello-world'=>'news/how-pelham-elevates-logistics-in-ca-fl',
  'news/technology-transforms-last-mile-delivery-across-the-nation'=>'news/navigating-challenges-in-us-logistics',
  'technology-transforms-last-mile-delivery-across-the-nation'=>'news/navigating-challenges-in-us-logistics',
  'news/rising-demand-for-sustainable-freight-solutions-in-the-u-s'=>'news/future-of-logistics-trends-pelham-is-embracing',
  'rising-demand-for-sustainable-freight-solutions-in-the-u-s'=>'news/future-of-logistics-trends-pelham-is-embracing',
 ];
}
/** Destino de la ruta pedida, o '' si no hay redirección. */
function pelham_local_seo_destination($request_path){
 $path=trim((string)$request_path,'/');
 $home=trim((string)wp_parse_url(home_url('/'),PHP_URL_PATH),'/');
 if($home!==''){if(!str_starts_with($path.'/',$home.'/'))return '';$path=trim(substr($path,strlen($home)),'/');}
 $map=pelham_local_seo_redirects();
 return isset($map[$path])?$map[$path]:'';
}
// Antes que WordPress resuelva la página antigua (que existe) y antes de las redirecciones 404 de 1.20.
add_action('template_redirect',function(){
 if(is_admin()||!in_array($_SERVER['REQUEST_METHOD']??'GET',['GET','HEAD'],true)||isset($_GET['preview']))return;
 $dest=pelham_local_seo_destination(wp_parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH));
 if($dest==='')return;
 // Conserva solo los UTM, para medir el tráfico que llega desde el Perfil de Empresa de Google.
 $utm=array_intersect_key(wp_unslash($_GET),array_flip(['utm_source','utm_medium','utm_campaign','utm_term','utm_content']));
 $utm=array_filter($utm,fn($v)=>is_string($v)&&strlen($v)<=100);
 wp_safe_redirect(add_query_arg(array_map('rawurlencode',$utm),home_url('/'.$dest.'/')),301,'Pelham local SEO 1.22');exit;
},0);

/** IDs de las entradas y páginas heredadas que redirigen: fuera del sitemap. */
function pelham_local_seo_redirected_ids(){
 $ids=[];
 foreach(array_keys(pelham_local_seo_redirects()) as $path){
  $slug=basename($path);
  foreach(get_posts(['post_type'=>['post','page'],'name'=>$slug,'post_status'=>'publish','numberposts'=>-1,'fields'=>'ids','pelham_all_languages'=>true]) as $id){
   $permalink=trim((string)wp_parse_url(get_permalink($id),PHP_URL_PATH),'/');
   if(pelham_local_seo_destination($permalink)!=='')$ids[]=(int)$id;
  }
 }
 return array_values(array_unique($ids));
}
add_filter('wp_sitemaps_posts_query_args',function($args,$type){
 if(!in_array($type,['post','page'],true))return $args;
 $args['post__not_in']=array_values(array_unique(array_merge($args['post__not_in']??[],pelham_local_seo_redirected_ids())));
 return $args;
},20,2);
add_filter('wp_sitemaps_add_provider',fn($provider,$name)=>$name==='taxonomies'?false:$provider,20,2);
add_filter('wp_robots',function($robots){
 if(is_category()||is_tag()){$robots['noindex']=true;$robots['follow']=true;unset($robots['index']);}
 return $robots;
},100);

/** Niveles LTL que aplican a una sede (los de la oferta publicada; no se inventan plazos). */
function pelham_local_seo_terminal_levels($route){
 if(!function_exists('pelham_ltl_levels'))return [];
 $levels=pelham_ltl_levels();
 if($route==='orlando-fl')unset($levels['expedited']); // Expedited: solo Homestead–West Palm Beach.
 return $levels;
}
function pelham_local_seo_terminal_section($post){
 $route=pelham_route_slug($post);
 $copy=[
  'gardena-ca'=>['Shipping from Los Angeles to Florida','Pelham Services Gardena is the California origin of our Los Angeles to Florida lane. LTL transit times are counted in calendar days from pickup:','Expedited covers South Florida only, from Homestead to West Palm Beach. Regular and Economy cover all Florida destinations.'],
  'miami-fl'=>['Freight from Los Angeles to Miami','Shipments picked up in the Los Angeles area reach Miami with these LTL options, counted in calendar days from pickup:','Miami is inside the Expedited area (Homestead to West Palm Beach).'],
  'orlando-fl'=>['Freight from Los Angeles to Orlando','Shipments picked up in the Los Angeles area reach Orlando with these LTL options, counted in calendar days from pickup:','Expedited is not available to Orlando: it covers South Florida only, from Homestead to West Palm Beach.'],
 ][$route]??null;
 if(!$copy)return '';
 $levels=pelham_local_seo_terminal_levels($route);if(!$levels)return '';
 ob_start();?>
<section class="pelham-ltl-levels pelham-terminal-lane" aria-labelledby="terminal-lane-title">
 <div class="section-heading"><h2 id="terminal-lane-title"><?php echo esc_html(pelham_t($copy[0]));?></h2><p><?php echo esc_html(pelham_t($copy[1]));?></p></div>
 <div class="pelham-level-grid"><?php foreach($levels as $key=>$level){?><article class="pelham-level"><h3><?php echo esc_html(pelham_t($level[0]));?></h3><p class="pelham-level-days"><?php echo (int)$level[1];?><span> <?php echo esc_html(pelham_t('calendar days'));?></span></p><a class="text-link" href="<?php echo esc_url(add_query_arg(['service'=>'freight-consolidation','ltl_level'=>$key],pelham_link('quote')));?>"><?php echo esc_html(pelham_t('Request '.$level[0]));?> <span class="direction-arrow" aria-hidden="true"></span></a></article><?php }?></div>
 <p class="pelham-transit-note"><?php echo esc_html(pelham_t('Estimated, not guaranteed.').' '.pelham_t($copy[2]));?></p>
 <p class="pelham-terminal-services"><?php echo esc_html(pelham_t('Services:'));?> <a href="<?php echo esc_url(pelham_link('services/freight-consolidation'));?>"><?php echo esc_html(pelham_t('Less Than Truckload (LTL)'));?></a> · <a href="<?php echo esc_url(pelham_link('services/dedicated-transport'));?>"><?php echo esc_html(pelham_t('Full Truckload (FTL)'));?></a> · <a href="<?php echo esc_url(pelham_link('services/transloading'));?>"><?php echo esc_html(pelham_t('Cross-Docking & Palletizing'));?></a></p>
</section>
<?php return ob_get_clean();
}
add_filter('the_content',function($content){
 if(!is_singular('pelham_terminal')||!in_the_loop()||!is_main_query()||str_contains($content,'pelham-terminal-lane'))return $content;
 return $content.pelham_local_seo_terminal_section(get_post());
},30);
