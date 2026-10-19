<?php
/** About: the Pelham team, rendered from data/team.json without editing the saved page. */
if (!defined('ABSPATH')) exit;
function pelham_team_data(){static $data;if(!isset($data))$data=json_decode(file_get_contents(__DIR__.'/../data/team.json'),true);return $data;}
function pelham_team_initials($name){$parts=preg_split('/\s+/u',trim($name));return mb_strtoupper(mb_substr($parts[0],0,1).mb_substr(end($parts),0,1));}
// [pelham_team]
add_shortcode('pelham_team',function(){
 $data=pelham_team_data();ob_start();?><section class="team-band" aria-labelledby="meet-the-team"><div class="section-heading"><h2 id="meet-the-team"><?php echo esc_html($data['heading']); ?></h2><p><?php echo esc_html($data['intro']); ?> <a href="<?php echo esc_url(pelham_link('contact')); ?>">Contact the team</a></p></div><ul class="team-list"><?php foreach($data['members'] as $member){?><li><span class="team-initials" aria-hidden="true"><?php echo esc_html(pelham_team_initials($member['name'])); ?></span><h3><?php echo esc_html($member['name']); ?></h3><p class="team-role"><?php echo esc_html($member['role']); ?></p><p><?php echo esc_html($member['bio']); ?></p></li><?php } ?></ul></section><?php return ob_get_clean();
});
// Appended to the About page unless the editor already placed [pelham_team].
add_filter('the_content',function($content){
 if(!is_page(pelham_team_data()['page'])||!in_the_loop()||!is_main_query()||str_contains($content,'team-band'))return $content;
 return $content.do_shortcode('[pelham_team]');
},20);
