<?php
	set_time_limit(0);
	ini_set('memory_limit', '200000000000M');
	$stringjson = file_get_contents("data/item.txt");
	$arrs = json_decode($stringjson,true);
	//
	$jsonprocess =  file_get_contents("data/process.txt");
	$arrprocess = json_decode($jsonprocess,true);
	$GLOBALS['limit'] = $arrprocess['limit'];
	$GLOBALS['percen']= $arrprocess['percen1'];
	function dd($arr,$exit=true){
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		if($exit){
			exit;
		}else{
			echo "<br>";
		}
	};
	$total = sizeof($arrs);
	include 'function/function'.$total.'.php';
	getresult($arrs);
?>