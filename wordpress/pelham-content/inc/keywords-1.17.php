<?php
/** Keyword review 1.17.1–1.17.2: each H1 carries its target keyword, only while the original title is unchanged. */
if (!defined('ABSPATH')) exit;
function pelham_keyword_retitles(){return [
 // Measured demand (Keyword Planner, Oct 2026).
 'freight-consolidation'=>['LTL shipping from California to Florida','LTL freight shipping from California to Florida'],
 'ltl-vs-ftl-california-florida-shipping'=>['LTL vs. FTL: which fits your California–Florida shipment?','LTL vs. FTL: meaning, differences and which fits your shipment'],
 // Intent targets without measured volume: the keyword-map keyword, worded naturally.
 'services'=>['LTL, FTL and freight handling services','Freight transportation services'],
 'who-we-serve'=>['Freight shipping for suppliers, distributors, manufacturers and forwarders','Trucking for suppliers, distributors, manufacturers and forwarders'],
 'container-transloading-documents-preparation'=>['Preparing a container-to-truck transfer with Pelham','Container transloading requirements: preparing a container-to-truck transfer'],
 'navigating-challenges-in-us-logistics'=>['Planning for freight delays and changes on a California–Florida shipment','Freight logistics challenges: planning for delays on a California–Florida shipment'],
 'how-pelham-elevates-logistics-in-ca-fl'=>['How Pelham plans a domestic freight shipment','Pelham’s freight shipping process: how a domestic shipment is planned'],
 'transloading-vs-cross-docking'=>['Cross-docking, transloading and palletizing: which do you need?','Transloading vs. cross-docking and palletizing: which do you need?'],
 'how-a-freight-fuel-surcharge-works'=>['Understanding fuel surcharges in a freight quote','Freight fuel surcharge explained: what it means in your quote'],
 'freight-delivery-to-forwarders-in-miami'=>['Delivering California freight to a forwarder in Miami','Freight delivery to forwarders in Miami from California'],
];}
add_filter('the_title',function($title,$id=0){
 if(!$id||is_admin()||!is_singular()||(int)$id!==get_queried_object_id())return $title;
 $post=get_post($id);$map=pelham_keyword_retitles()[$post->post_name??'']??null;
 return $map&&$post->post_title===$map[0]?$map[1]:$title;
},10,2);
// Home H1: add the lane to the brand line, only while the saved hero heading is the original.
add_filter('the_content',function($content){
 if(!is_front_page()||!in_the_loop()||!is_main_query())return $content;
 return str_replace('Pelham Services.<br>Your freight, <em>connected.</em></h1>','Pelham Services.<br>California to Florida freight, <em>connected.</em></h1>',$content);
},20);
