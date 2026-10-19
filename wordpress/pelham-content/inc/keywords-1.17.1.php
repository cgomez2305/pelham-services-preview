<?php
/** Keyword review 1.17.1: H1s for the measured search intents, only while the original title is unchanged. */
if (!defined('ABSPATH')) exit;
function pelham_keyword_retitles(){return [
 // "freight shipping from california to florida": the only measured commercial query for the lane.
 'freight-consolidation'=>['LTL shipping from California to Florida','LTL freight shipping from California to Florida'],
 // "ltl vs ftl", "ftl ltl meaning", "ftl and ltl meaning": 500 monthly searches each.
 'ltl-vs-ftl-california-florida-shipping'=>['LTL vs. FTL: which fits your California–Florida shipment?','LTL vs. FTL: meaning, differences and which fits your shipment'],
];}
add_filter('the_title',function($title,$id=0){
 if(!$id||is_admin()||!is_singular()||(int)$id!==get_queried_object_id())return $title;
 $post=get_post($id);$map=pelham_keyword_retitles()[$post->post_name??'']??null;
 return $map&&$post->post_title===$map[0]?$map[1]:$title;
},10,2);
