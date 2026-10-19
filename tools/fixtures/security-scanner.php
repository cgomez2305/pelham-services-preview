<?php
// LOCAL TEST DOUBLE ONLY. Not an antivirus. Never distribute.
add_filter('pelham_scan_attachment',function($result,$bytes){
 if(str_contains($bytes,'%PA_SCAN_TEST_CLEAN'))return ['status'=>'clean'];
 if(str_contains($bytes,'%PA_SCAN_TEST_INFECTED'))return ['status'=>'infected'];
 if(str_contains($bytes,'%PA_SCAN_TEST_ERROR'))throw new RuntimeException('Simulated scanner failure');
 return $result;
},10,2);
