<?php
if(!defined('ABSPATH'))exit;
function pelham_is_production_host(){return in_array(strtolower((string)wp_parse_url(home_url('/'),PHP_URL_HOST)),['pelhamservices.com','www.pelhamservices.com'],true);}
function pelham_private_seo_page(){return pelham_is_page(['register','login','claim','claims','freight-claim','application'])||isset($_GET['pa_token'])||isset($_GET['pelham_status']);}
add_filter('wp_robots',function($robots){if(!pelham_is_production_host()||pelham_private_seo_page()||is_author()||is_date()||is_search()||is_404()){$robots['noindex']=true;unset($robots['index']);}return $robots;},99);
add_action('send_headers',function(){if(!pelham_is_production_host())header('X-Robots-Tag: noindex, nofollow',true);});
add_filter('wp_sitemaps_enabled',fn($enabled)=>$enabled&&pelham_is_production_host());
add_filter('wp_sitemaps_add_provider',fn($provider,$name)=>$name==='users'?false:$provider,10,2);
add_filter('wp_sitemaps_posts_query_args',function($args,$type){if($type!=='page')return $args;$ids=[];foreach(['register','login','claim','claims','freight-claim','application'] as $slug){$p=get_page_by_path($slug);if($p){$ids[]=$p->ID;$es=pelham_translation($p,'es');if($es)$ids[]=$es->ID;}}$args['post__not_in']=array_unique(array_merge($args['post__not_in']??[],$ids));return $args;},10,2);
function pelham_legacy_destination($query,$path){
 if(!in_array($path,['/','/index.php'],true))return '';
 $maps=['page_id'=>['12'=>'/','30'=>'/services/','151'=>'/about/','13'=>'/news/','10'=>'/register/','74'=>'/contact/'],'p'=>['98'=>'/news/future-of-logistics-trends-pelham-is-embracing/','95'=>'/news/navigating-challenges-in-us-logistics/','1'=>'/news/how-pelham-elevates-logistics-in-ca-fl/'],'cat'=>['1'=>'/news/','4'=>'/news/']];
 $keys=array_intersect(array_keys($maps),array_keys($query));if(count($keys)!==1)return '';
 foreach($maps as $key=>$map){if(!isset($query[$key])||!is_string($query[$key])||!isset($map[$query[$key]]))continue;$dest=$map[$query[$key]];if(isset($query['paged'])){if($key!=='cat'||!is_scalar($query['paged'])||!ctype_digit((string)$query['paged'])||(int)$query['paged']<1)return '';if((int)$query['paged']>1)return '';}return $dest;}return '';
}
add_action('template_redirect',function(){
 if(!pelham_is_production_host()||!get_option('pelham_legacy_redirects')||is_admin()||!in_array($_SERVER['REQUEST_METHOD']??'GET',['GET','HEAD'],true)||isset($_GET['preview']))return;
 $path=wp_parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH);$dest=pelham_legacy_destination(wp_unslash($_GET),$path);
 if($dest){wp_safe_redirect(home_url($dest),301,'Pelham legacy URL migration');exit;}
},1);
add_action('admin_menu',function(){add_submenu_page('pelham-company','SEO & migration','SEO & migration','manage_options','pelham-seo','pelham_seo_admin');});
function pelham_seo_admin(){if(!current_user_can('manage_options'))return;?><div class="wrap"><h1>Pelham SEO & migration</h1><p>Production domain: pelhamservices.com. Other hosts are marked noindex and their WordPress sitemap is disabled.</p><p><strong>Current host:</strong> <?php echo esc_html(pelham_is_production_host()?'Production':'Staging / local — noindex enforced');?>. <strong>WordPress search visibility:</strong> <?php echo get_option('blog_public')?'Search engines allowed by WordPress':'Discouraged — change Settings → Reading only at production launch';?>.</p><p>Native sitemap: <a href="<?php echo esc_url(home_url('/wp-sitemap.xml'));?>">wp-sitemap.xml</a>. Private application pages and author listings are excluded. An external SEO plugin may manage its own sitemap and must be checked separately.</p><?php pelham_seo_readiness(); ?><h2>Verified legacy redirects</h2><p>Enable only after replacing the old site on the production domain. These redirects take priority over old numeric IDs, which may collide with IDs in the new database. They affect only known root/index.php legacy URLs, never POST requests. Unknown IDs are not redirected to the homepage.</p><form action="<?php echo esc_url(admin_url('admin-post.php'));?>" method="post"><input type="hidden" name="action" value="pelham_seo_settings"><?php wp_nonce_field('pelham_seo_settings');?><label><input type="checkbox" name="legacy" value="1" <?php checked(get_option('pelham_legacy_redirects'));?>> Enable verified legacy 301 redirects on pelhamservices.com</label><p><label for="pelham-gsc">Search Console HTML verification token (optional)</label><br><input class="regular-text" id="pelham-gsc" name="verification" value="<?php echo esc_attr(get_option('pelham_gsc_verification',''));?>"></p><p>Paste only the verification token from Google, not HTML. No Google account is created or connected automatically.</p><?php submit_button('Save SEO settings');?></form><h2>Measurement</h2><p>No third-party analytics is loaded by this release. Configure Search Console and choose an analytics/consent setup before collecting visitor metrics. Real enquiries still go through the existing sales email flow. A successful WordPress mail handoff does not prove inbox delivery.</p></div><?php }
add_action('admin_post_pelham_seo_settings',function(){if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_seo_settings');$raw=wp_unslash($_POST['verification']??'');if(!is_string($raw)||strlen($raw)>200||($raw!==''&&!preg_match('/^[A-Za-z0-9_-]+$/D',$raw)))wp_die('Invalid verification token.');update_option('pelham_gsc_verification',$raw);update_option('pelham_legacy_redirects',isset($_POST['legacy'])&&$_POST['legacy']==='1');wp_safe_redirect(admin_url('admin.php?page=pelham-seo'));exit;});
add_action('wp_head',function(){$token=get_option('pelham_gsc_verification','');if(pelham_is_production_host()&&is_front_page()&&$token)echo '<meta name="google-site-verification" content="'.esc_attr($token).'">'."\n";},2);
// Update only the shipped obsolete description; preserve custom copy and application content.
add_action('admin_init',function(){
 if(!current_user_can('manage_options'))return;$post=get_page_by_path('register');if(!$post)return;
 $old='Download Pelham registration forms and submit customer onboarding documents.';$new='Apply for a Pelham account online. Complete your company details, review agreements and draw or upload your signature for review by our team.';
 $current=get_post_meta($post->ID,'pelham_seo_description',true);
 if($current===$old){if(!add_post_meta($post->ID,'pelham_seo_description_before_1_10_1',$current,true)&&get_post_meta($post->ID,'pelham_seo_description_before_1_10_1',true)!==$old)return;update_post_meta($post->ID,'pelham_seo_description',$new);clean_post_cache($post->ID);}
});
function pelham_seo_readiness(){
 echo '<h2>Indexing readiness</h2><p>Staging must remain noindex. Do not allow indexing until the new site is on pelhamservices.com. Registration and claims remain noindex after launch.</p>';
 if(file_exists(ABSPATH.'robots.txt'))echo '<div class="notice notice-warning inline"><p>A physical robots.txt exists. The hosting server may serve it before WordPress. Review its Googlebot and wildcard rules at launch; this plugin does not overwrite server files.</p></div>';
 echo '<p>Hosting/CDN rules may override WordPress even without a physical file. Use the public check below after clearing caches. A blocked Googlebot must be resolved in hosting settings before production indexing.</p>';
 $report=get_transient('pelham_seo_public_check');if(is_array($report)){echo '<p><strong>Last public robots.txt check:</strong> '.esc_html($report['time']).'</p><pre style="white-space:pre-wrap">'.esc_html($report['summary']).'</pre>';}
 echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="pelham_check_robots">';wp_nonce_field('pelham_check_robots');submit_button('Check public robots.txt','secondary','submit',false);echo '</form>';
}
function pelham_robots_root_blocks($body){
 $agents=[];$has_rules=false;$blocked=[];
 foreach(preg_split('/\r?\n/',$body) as $line){$line=trim(explode('#',$line,2)[0]);if(!str_contains($line,':'))continue;[$key,$value]=array_map('trim',explode(':',$line,2));$key=strtolower($key);
  if($key==='user-agent'){if($has_rules){$agents=[];$has_rules=false;}$agents[]=strtolower($value);}
  elseif(in_array($key,['disallow','allow'],true)){$has_rules=true;if($key==='disallow'&&$value==='/')foreach($agents as $agent)if(in_array($agent,['*','googlebot'],true))$blocked[]=$agent;}
 }return array_values(array_unique($blocked));
}
add_action('admin_post_pelham_check_robots',function(){
 if(!current_user_can('manage_options'))wp_die('Forbidden',403);check_admin_referer('pelham_check_robots');
 $response=wp_safe_remote_get(home_url('/robots.txt'),['timeout'=>10,'redirection'=>2,'limit_response_size'=>65536]);
 if(is_wp_error($response))$summary='Could not verify the public response: '.$response->get_error_message();
 else{$code=wp_remote_retrieve_response_code($response);$body=wp_remote_retrieve_body($response);$blocks=pelham_robots_root_blocks($body);$summary='HTTP '.$code.'. ';if($code!==200)$summary.='Review the public robots.txt response before launch.';elseif($blocks)$summary.='Root Disallow rules detected for: '.implode(', ',$blocks).'. '.(pelham_is_production_host()?'Review hosting/CDN and robots.txt before allowing production indexing.':'Staging protection: keep it while testing; review again after migration.');else $summary.='No root Disallow rule detected for Googlebot or *. This check does not validate all robots directives or prove Google indexing.';}
 set_transient('pelham_seo_public_check',['time'=>current_time('mysql'),'summary'=>'URL: '.home_url('/robots.txt')."\n".$summary],DAY_IN_SECONDS);wp_safe_redirect(admin_url('admin.php?page=pelham-seo'));exit;
});
