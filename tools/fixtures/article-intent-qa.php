<?php
// Local-only fixture. Never distribute with the plugin.
add_action('wp_ajax_pelham_qa_reader_snapshot',function(){
 if(!current_user_can('manage_options'))wp_die('',403);
 $out=[];
 foreach(pelham_article_intent_entries() as $e){
  $ps=$e['route']==='home'?array_values(array_filter([get_post((int)get_option('page_on_front'))])):get_posts(['post_type'=>$e['type'],'name'=>basename($e['route']),'post_status'=>'any','numberposts'=>1]);
  if(!$ps){$out[$e['route']]=null;continue;}$p=$ps[0];
  $out[$e['route']]=['id'=>$p->ID,'permalink'=>get_permalink($p),'title'=>$p->post_title,'slug'=>$p->post_name,'date'=>$p->post_date,'modified'=>$p->post_modified,'status'=>$p->post_status,'author'=>$p->post_author,'thumbnail'=>get_post_meta($p->ID,'_thumbnail_id',true),'content'=>$p->post_content,'backup'=>get_post_meta($p->ID,'pelham_before_article_intent_1_15',true),'done'=>get_post_meta($p->ID,'pelham_article_intent_115_done',true),'meta'=>get_post_meta($p->ID,'pelham_seo_title',true)];
 }
 wp_send_json(['release'=>get_option('pelham_article_intent_release'),'rows'=>$out,'legacy_blog_count'=>count(pelham_blog_entries())]);
});
add_action('wp_ajax_pelham_qa_reader_repeat',function(){if(!current_user_can('manage_options'))wp_die('',403);$r=pelham_apply_article_intent_115();wp_send_json(['ok'=>!is_wp_error($r)]);});
