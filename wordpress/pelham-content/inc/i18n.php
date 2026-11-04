<?php
/**
 * Bilingual site: English at the root, Spanish under /es/.
 *
 * Each Spanish page, article, service and terminal is a normal WordPress post
 * (editable in the admin) with meta pelham_lang=es and pelham_translation
 * pointing to its English counterpart (and back). Lists, links, metadata and
 * interface strings follow the language of the current URL.
 */
if (!defined('ABSPATH')) exit;

function pelham_i18n_routes(){static $routes;if(!isset($routes))$routes=json_decode(file_get_contents(__DIR__.'/../data/i18n-routes.json'),true);return $routes;}
function pelham_i18n_dictionary(){static $dict;if(!isset($dict))$dict=json_decode(file_get_contents(__DIR__.'/../data/i18n-es.json'),true)?:[];return $dict;}

/** Language of the requested URL; usable before the main query runs. */
function pelham_request_lang(){
 $path=trim((string)wp_parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH),'/');
 $base=trim((string)wp_parse_url(home_url('/'),PHP_URL_PATH),'/');
 if($base!==''&&str_starts_with($path,$base))$path=trim(substr($path,strlen($base)),'/');
 return ($path==='es'||str_starts_with($path,'es/'))?'es':'en';
}
if(!function_exists('pelham_lang')){
function pelham_lang(){
 if(isset($GLOBALS['pelham_lang_override']))return $GLOBALS['pelham_lang_override'];
 // Form handlers run through admin-post.php and carry the page language.
 if(is_admin()||wp_doing_ajax())return sanitize_key(wp_unslash($_REQUEST['pelham_lang']??''))==='es'?'es':'en';
 return pelham_request_lang();
}}
function pelham_locale_tag(){return pelham_lang()==='es'?'es-US':get_bloginfo('language');}
function pelham_post_lang($post){$post=get_post($post);return $post&&get_post_meta($post->ID,'pelham_lang',true)==='es'?'es':'en';}
/** The published version of $post in $lang, or null. */
function pelham_translation($post,$lang){
 $post=get_post($post);if(!$post)return null;
 if(pelham_post_lang($post)===$lang)return $post;
 $other=get_post((int)get_post_meta($post->ID,'pelham_translation',true));
 return $other&&$other->post_status==='publish'&&pelham_post_lang($other)===$lang?$other:null;
}
/** English slug of a post, so route checks work in both languages. */
if(!function_exists('pelham_route_slug')){
function pelham_route_slug($post){
 $post=get_post($post);if(!$post)return '';
 if(pelham_post_lang($post)==='es'){$en=pelham_translation($post,'en');if($en)return (int)get_option('page_on_front')===$en->ID?'home':$en->post_name;}
 return $post->post_name;
}}
if(!function_exists('pelham_is_page')){
function pelham_is_page($slugs=''){
 if(!is_page())return false;
 if($slugs==='')return true;
 return in_array(pelham_route_slug(get_queried_object()),(array)$slugs,true)||is_page($slugs);
}}
if(!function_exists('pelham_is_home')){
function pelham_is_home(){
 if(is_front_page())return true;
 $home=(int)get_option('pelham_es_home');
 return $home&&is_page()&&(int)get_queried_object_id()===$home;
}}
if(!function_exists('pelham_home_link')){
function pelham_home_link(){
 if(pelham_lang()==='es'){$home=(int)get_option('pelham_es_home');if($home&&get_post_status($home)==='publish')return get_permalink($home);return home_url('/es/');}
 return home_url('/');
}}
/** Spanish URL for an English route ('quote', 'services/transloading'), or ''. */
function pelham_i18n_url($route){
 $route=trim($route,'/');if($route===''||$route==='home')return pelham_lang()==='es'?pelham_home_link():'';
 if(pelham_lang()!=='es')return '';
 $ids=get_option('pelham_i18n_ids',[]);
 if(!empty($ids[$route])&&get_post_status($ids[$route])==='publish')return get_permalink($ids[$route]);
 $routes=pelham_i18n_routes();return isset($routes[$route])?home_url('/'.$routes[$route].'/'):'';
}

/** Interface text: Spanish translation of an English string when the page is Spanish. */
function pelham_i18n_key($text){
 $text=html_entity_decode((string)$text,ENT_QUOTES|ENT_HTML5,'UTF-8');
 $text=strtr($text,['’'=>"'",'‘'=>"'",'“'=>'"','”'=>'"','…'=>'...',"\u{00a0}"=>' ']);
 return trim(preg_replace('/\s+/u',' ',$text));
}
// The theme declares English-only fallbacks for these when the plugin is
// inactive; activating the plugin must not redeclare them in that request.
/**
 * Spanish for an English interface string. Plain text in, plain text out.
 * Strings with inline HTML (<br>, <em>, links) are matched with numbered
 * placeholders, and the original tags are put back around the translation.
 */
if(!function_exists('pelham_t')){
function pelham_t($text){
 if(!is_string($text)||$text===''||pelham_lang()!=='es')return $text;
 $dict=pelham_i18n_dictionary();
 if(!str_contains($text,'<')){$key=pelham_i18n_key($text);return $dict[$key]??$text;}
 $parts=preg_split('/(<[^>]+>)/',$text,-1,PREG_SPLIT_DELIM_CAPTURE);$tags=[];$key='';
 foreach($parts as $i=>$part){if($i%2){$tags[]=$part;$key.='{'.count($tags).'}';}else $key.=$part;}
 $key=pelham_i18n_key($key);if(!isset($dict[$key]))return $text;
 $out='';
 foreach(preg_split('/(\{\d+\})/',$dict[$key],-1,PREG_SPLIT_DELIM_CAPTURE) as $piece){
  $out.=preg_match('/^\{(\d+)\}$/',$piece,$m)?($tags[(int)$m[1]-1]??''):htmlspecialchars($piece,ENT_NOQUOTES,'UTF-8',false);
 }
 preg_match('/^\s*/',$text,$lead);preg_match('/\s*$/',$text,$trail);
 return $lead[0].trim($out).$trail[0];
}}
/** Dates: "October 7, 2026" in English, "7 de octubre de 2026" in Spanish. */
if(!function_exists('pelham_date')){
function pelham_date($format='',$post=null,$modified=false){
 $post=get_post($post);$time=$modified?get_post_modified_time('U',true,$post):get_post_time('U',true,$post);
 if(pelham_lang()!=='es')return $modified?get_the_modified_date($format,$post):get_the_date($format,$post);
 $months=['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
 $month=$months[(int)wp_date('n',$time)-1];
 if($format==='M j, Y')return wp_date('j',$time).' '.mb_substr($month,0,3).' '.wp_date('Y',$time);
 return wp_date('j',$time).' de '.$month.' de '.wp_date('Y',$time);
}}

// URLs: /es/blog/<slug>/, /es/servicios/<slug>/, /es/ubicaciones/<slug>/. Spanish
// pages are children of the Spanish home page (slug "es"), so /es/<slug>/ is native.
function pelham_i18n_rewrite_rules(){
 add_rewrite_rule('^es/blog/([^/]+)/?$','index.php?name=$matches[1]','top');
 add_rewrite_rule('^es/servicios/([^/]+)/?$','index.php?pelham_service=$matches[1]','top');
 add_rewrite_rule('^es/ubicaciones/([^/]+)/?$','index.php?pelham_terminal=$matches[1]','top');
}
add_action('init','pelham_i18n_rewrite_rules',1);
// Self-repair: any flush that ran before these rules were registered (plugin
// activation, another plugin) leaves /es/ articles, services and facilities
// without routes. Detect that once per request and rebuild the rules.
add_action('init',function(){
 $rules=get_option('rewrite_rules');
 if(is_array($rules)&&$rules&&!isset($rules['^es/blog/([^/]+)/?$']))flush_rewrite_rules(false);
},99);
add_filter('post_link',function($url,$post){return $post->post_name&&pelham_post_lang($post)==='es'?home_url('/es/blog/'.$post->post_name.'/'):$url;},10,2);
add_filter('post_type_link',function($url,$post){
 $bases=['pelham_service'=>'servicios','pelham_terminal'=>'ubicaciones'];
 return isset($bases[$post->post_type])&&$post->post_name&&pelham_post_lang($post)==='es'?home_url('/es/'.$bases[$post->post_type].'/'.$post->post_name.'/'):$url;
},10,2);

// Lists (news, services, terminals, footer) show only the current language.
add_action('pre_get_posts',function($query){
 if((is_admin()&&!wp_doing_ajax())||$query->get('pelham_all_languages'))return;
 foreach(['p','name','pagename','page_id','attachment'] as $var)if($query->get($var))return;
 if($query->get('post__in')||($query->is_main_query()&&$query->is_singular()))return;
 $types=array_filter((array)$query->get('post_type'));if(!$types)$types=['post'];
 if(!array_intersect($types,['post','pelham_service','pelham_terminal','any']))return;
 $meta=(array)$query->get('meta_query');
 $meta[]=pelham_lang()==='es'?['key'=>'pelham_lang','value'=>'es']:['key'=>'pelham_lang','compare'=>'NOT EXISTS'];
 $query->set('meta_query',$meta);
});
// One URL per language: an article, service or facility requested under the
// other language's base (/news/<spanish-slug>/, /es/blog/<english-slug>/) is
// permanently redirected to its own address.
add_action('template_redirect',function(){
 if(!is_singular(['post','pelham_service','pelham_terminal'])||is_preview())return;
 $expected=(string)wp_parse_url(get_permalink(),PHP_URL_PATH);$actual=(string)wp_parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH);
 if($expected===''||trailingslashit($actual)===trailingslashit($expected))return;
 $query=(string)wp_parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_QUERY);
 wp_safe_redirect(get_permalink().($query!==''?'?'.$query:''),301,'Pelham language');exit;
},1);
add_filter('wp_sitemaps_posts_query_args',function($args){$args['pelham_all_languages']=true;return $args;},5);

// The Spanish home page uses the homepage template and body classes.
add_filter('template_include',function($template){
 if(!is_front_page()&&pelham_is_home()){$home=locate_template('front-page.php');if($home)return $home;}
 return $template;
});
add_filter('body_class',function($classes){if(!is_front_page()&&pelham_is_home())$classes[]='home';$classes[]='lang-'.pelham_lang();return $classes;});
add_filter('language_attributes',function($output){return pelham_lang()==='es'?preg_replace('/lang="[^"]*"/','lang="es-US"',$output):$output;});

/** Alternate URLs of the current page: ['en'=>url,'es'=>url] when both exist. */
function pelham_alternates(){
 if(is_404()||is_search())return [];
 if(pelham_is_home())return ['en'=>home_url('/'),'es'=>pelham_i18n_url_for_lang('es')];
 if(!is_singular())return [];
 $post=get_queried_object();$en=pelham_translation($post,'en');$es=pelham_translation($post,'es');
 return $en&&$es?['en'=>get_permalink($en),'es'=>get_permalink($es)]:[];
}
function pelham_i18n_url_for_lang($lang){
 $home=(int)get_option('pelham_es_home');
 if($lang==='es')return $home&&get_post_status($home)==='publish'?get_permalink($home):'';
 return home_url('/');
}
add_action('wp_head',function(){
 $alt=array_filter(pelham_alternates());if(count($alt)<2)return;
 echo '<link rel="alternate" hreflang="en-US" href="'.esc_url($alt['en']).'">'."\n";
 echo '<link rel="alternate" hreflang="es-US" href="'.esc_url($alt['es']).'">'."\n";
 echo '<link rel="alternate" hreflang="x-default" href="'.esc_url($alt['en']).'">'."\n";
},2);
/** Header link to the same page in the other language (or the other home page). */
function pelham_language_switch(){
 $other=pelham_lang()==='es'?'en':'es';
 $alt=pelham_alternates();$url=$alt[$other]??'';
 if(!$url)$url=pelham_i18n_url_for_lang($other);
 if(!$url)return '';
 $labels=['es'=>['ES','Español','Ver esta página en español'],'en'=>['EN','English','View this page in English']];
 return '<a class="language-switch" href="'.esc_url($url).'" hreflang="'.($other==='es'?'es-US':'en-US').'" lang="'.$other.'" aria-label="'.esc_attr($labels[$other][2]).'"><span aria-hidden="true">'.$labels[$other][0].'</span><span class="language-switch-name">'.$labels[$other][1].'</span></a>';
}

// Company settings and shortcode labels pass through the dictionary too.
add_filter('pelham_ui_text','pelham_t');

require_once __DIR__.'/i18n-content.php';

// Strings used by the theme and plugin scripts, for Spanish pages only.
add_action('wp_head',function(){
 if(pelham_lang()!=='es')return;
 $strings=['Primary','Menu','Close','article','articles','Site navigation','Reduced motion enabled','Resume animations','Pause animations','Your device is set to reduced motion.','Sending…','Sending your request. Please wait.','Your details have been restored in this tab. Review them and confirm consent before trying again.','Expedited: estimated 3 calendar days from pickup. South Florida only, from Homestead to West Palm Beach. Not guaranteed. Confirm your delivery address with our team.','Regular: estimated 7 calendar days from pickup. All Florida destinations. Not guaranteed.','Economy: estimated 10 calendar days from pickup. All Florida destinations. Our lowest-cost LTL option. Not guaranteed.'];
 $map=[];foreach($strings as $text)$map[$text]=pelham_t($text);
 echo '<script id="pelham-i18n">window.pelhamI18n='.wp_json_encode($map,JSON_HEX_TAG|JSON_UNESCAPED_UNICODE).';</script>'."\n";
},1);

/**
 * Translate an HTML fragment segment by segment (same rules as
 * tools/i18n/segment.py): each run of text and inline tags inside a block
 * is looked up with its tags as {n} placeholders; alt, aria-label, title,
 * placeholder and data-description/question attributes are looked up as text.
 */
function pelham_translate_html($html){
 if(pelham_lang()!=='es'||!is_string($html)||$html===''||!pelham_i18n_dictionary())return $html;
 $inline=array_flip(['a','abbr','b','br','code','em','i','small','span','strong','sub','sup','time','u','mark','q','s','cite','data','kbd','var','wbr','img']);
 $skip=array_flip(['script','style','svg','textarea','noscript','title','head']);
 $void=array_flip(['area','base','br','col','embed','hr','img','input','link','meta','source','track','wbr']);
 preg_match_all('/<!--.*?-->|<![^>]*>|<[^>]+>|[^<]+/s',$html,$m,PREG_OFFSET_CAPTURE);$toks=$m[0];
 $name=function($t){return preg_match('/^<\/?\s*([a-zA-Z][\w-]*)/',$t,$x)?strtolower($x[1]):null;};
 $is_open=function($t)use($name,$void){$n=$name($t);return $n&&$t[0]==='<'&&$t[1]!=='/'&&$t[1]!=='!'&&!isset($void[$n])&&!str_ends_with($t,'/>');};
 // Inline elements that contain blocks (a card link) behave as blocks.
 $blockish=[];$stack=[];
 foreach($toks as $i=>[$t]){if($t[0]!=='<'||($t[1]??'')==='!')continue;$n=$name($t);if(!$n)continue;
  if($t[1]==='/'){while($stack){[$j,$o]=array_pop($stack);if($o===$n)break;}}
  elseif($is_open($t)){if(!isset($inline[$n])||isset($skip[$n]))foreach($stack as [$j,$o])if(isset($inline[$o]))$blockish[$j]=true;$stack[]=[$i,$n];}}
 $closing=[];$stack=[];
 foreach($toks as $i=>[$t]){if($t[0]!=='<'||($t[1]??'')==='!')continue;$n=$name($t);
  if($t[1]==='/'){for($k=count($stack)-1;$k>=0;$k--)if($stack[$k][1]===$n){if(isset($blockish[$stack[$k][0]]))$closing[$i]=true;array_splice($stack,$k);break;}}
  elseif($is_open($t))$stack[]=[$i,$n];}
 $dict=pelham_i18n_dictionary();$edits=[];$run=[];$depth=0;$skipping=0;
 $wraps=function($r)use($is_open){$d=0;$last=count($r)-1;foreach($r as $k=>[$t]){if($t[0]==='<'&&($is_open($t)||$t[1]==='/')){$d+=$t[1]==='/'?-1:1;if($d===0&&$k<$last)return false;}}return true;};
 $flush=function()use(&$run,&$edits,$dict,$name,$is_open,$wraps,$html){
  $r=$run;$run=[];
  while($r&&(trim($r[0][0])===''||str_starts_with($r[0][0],'</')))array_shift($r);
  while($r&&(trim(end($r)[0])===''||$is_open(end($r)[0])))array_pop($r);
  while(count($r)>2&&$is_open($r[0][0])&&str_starts_with(end($r)[0],'</')&&$name($r[0][0])===$name(end($r)[0])&&$wraps($r)){array_shift($r);array_pop($r);while($r&&trim($r[0][0])==='')array_shift($r);while($r&&trim(end($r)[0])==='')array_pop($r);}
  if(!$r)return;
  $start=$r[0][1];$end=end($r)[1]+strlen(end($r)[0]);$tags=[];$key='';
  foreach($r as [$t])if($t[0]==='<'){$tags[]=$t;$key.='{'.count($tags).'}';}else $key.=$t;
  $key=pelham_i18n_key($key);if(!isset($dict[$key])||!preg_match('/[A-Za-z]{2}/',strip_tags(str_replace(['{','}'],'',$key))))return;
  $out='';foreach(preg_split('/(\{\d+\})/',$dict[$key],-1,PREG_SPLIT_DELIM_CAPTURE) as $piece)$out.=preg_match('/^\{(\d+)\}$/',$piece,$x)?($tags[(int)$x[1]-1]??''):htmlspecialchars($piece,ENT_NOQUOTES,'UTF-8',false);
  $src=substr($html,$start,$end-$start);preg_match('/^\s*/',$src,$lead);preg_match('/\s*$/',$src,$trail);
  $edits[]=[$start,$end,$lead[0].trim($out).$trail[0]];
 };
 foreach($toks as $i=>[$t,$pos]){
  $tag=$t[0]==='<';$n=$tag&&($t[1]??'')!=='!'?$name($t):null;
  if($n&&isset($skip[$n])){if($t[1]==='/')$skipping=max(0,$skipping-1);elseif(!str_ends_with($t,'/>'))$skipping++;$flush();continue;}
  if($skipping)continue;
  $is_inline=!$tag||($n&&isset($inline[$n])&&!isset($blockish[$i])&&!isset($closing[$i]));
  if($tag&&($t[1]??'')==='!')$is_inline=false;
  if($is_inline){
   if($tag&&$is_open($t)&&$run&&$depth===0){$tail=array_slice($run,-2);$closed=false;foreach($tail as [$x])if(str_starts_with($x,'</'))$closed=true;$lastt=end($run)[0];if($closed&&($lastt[0]==='<'||trim($lastt)===''))$flush();}
   $run[]=[$t,$pos];
   if($tag&&($is_open($t)||$t[1]==='/')){$depth+=$t[1]==='/'?-1:1;$depth=max(0,$depth);}
  }else{$depth=0;$flush();}
 }
 $flush();
 // Attributes.
 if(preg_match_all('/<(?!\/|!|script|style)[a-zA-Z][^>]*>/',$html,$tm,PREG_OFFSET_CAPTURE))foreach($tm[0] as [$tagsrc,$tpos]){
  if(preg_match_all('/\s(alt|aria-label|title|placeholder|data-description|data-question)="([^"]*)"/',$tagsrc,$am,PREG_OFFSET_CAPTURE))foreach($am[2] as [$value,$vpos]){
   $key=pelham_i18n_key($value);if(isset($dict[$key]))$edits[]=[$tpos+$vpos,$tpos+$vpos+strlen($value),esc_attr($dict[$key])];
  }
 }
 usort($edits,fn($a,$b)=>$a[0]<=>$b[0]);$out='';$at=0;
 foreach($edits as [$s,$e,$text]){if($s<$at)continue;$out.=substr($html,$at,$s-$at).$text;$at=$e;}
 return $out.substr($html,$at);
}
// Spanish pages: Pelham shortcodes render their interface text in Spanish.
add_filter('do_shortcode_tag',function($output,$tag){return str_starts_with($tag,'pelham_')?pelham_translate_html($output):$output;},20,2);
