<?php
	
	set_time_limit(0);
	ini_set('memory_limit', '200000000000M');
	$litmit = 700;
	$percen = 40;
	$arrs[0]['name'] = 'Sản phẩm 1';
	$arrs[0]['percen1'] = 30;
	$arrs[0]['price'] = 500;
	$arrs[0]['litmit'] = 600;
	$arrs[1]['name'] = 'Sản phẩm 2';
	$arrs[1]['percen1'] = 50;
	$arrs[1]['price'] = 620;
	$arrs[1]['litmit'] = 200;
	$arrs[2]['name'] = 'Sản phẩm 3';
	$arrs[2]['percen1'] = 60;
	$arrs[2]['price'] = 750;
	$arrs[2]['litmit'] = 100;
	$arrs[3]['name'] = 'Sản phẩm 4';
	$arrs[3]['percen1'] = 50;
	$arrs[3]['price'] = 125;
	$arrs[3]['litmit'] = 120;
	$arrs[4]['name'] = 'Sản phẩm 5';
	$arrs[4]['percen1'] = 20;
	$arrs[4]['price'] = 321;
	$arrs[4]['litmit'] = 500;
	$arrs[5]['name'] = 'Sản phẩm 6';
	$arrs[5]['percen1'] = 20;
	$arrs[5]['price'] = 321;
	$arrs[5]['litmit'] = 500;
	$arrs[6]['name'] = 'Sản phẩm 6';
	$arrs[6]['percen1'] = 20;
	$arrs[6]['price'] = 321;
	$arrs[6]['litmit'] = 500;
	$arrs[7]['name'] = 'Sản phẩm 6';
	$arrs[7]['percen1'] = 20;
	$arrs[7]['price'] = 321;
	$arrs[7]['litmit'] = 500;
	$arrs[8]['name'] = 'Sản phẩm 6';
	$arrs[8]['percen1'] = 20;
	$arrs[8]['price'] = 321;
	$arrs[8]['litmit'] = 500;
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
	
	$arr_temp = array();
	$arr_nextemp = array();
	$i = sizeof($arrs);
	dd($i);
	if($i==9){
		
	}
	for($i=0;$i<sizeof($arrs);$i++){
		$arr_temp[$i] = 1;
	}
	backtracking(1,$arr_temp,$arrs,$percen,$litmit);
	
	function backtracking($k,$arr_temp,$arrs,$percen,$litmit){
		$total = $totalpercen = 0;
		$check_plus = false;
		$arr_temp_check = array();
		for($i=0;$i<sizeof($arrs);$i++){
			$final = $litmit;
			$percen1 = $arrs[$i]['percen1'];
			$number = $arr_temp[$i];
			$total = $number + $total;
			$totalpercen = $totalpercen + ($number*$percen1);
			// check limit
			if(isset($arrs[$i]['litmit']) && $arrs[$i]['litmit'] > 0){
				$final = $arrs[$i]['litmit'];
			}
			$arr_temp_check[$i]['number'] = $number;
			$arr_temp_check[$i]['final'] = $final;
		}
		$result_percen = ($totalpercen/$total);
		if($result_percen == $percen){
			//dd($arr_temp);
		}else{
			//echo "k".$k."----"; echo $result_percen; echo "<br>";
		}
		$checkload = false;
		// add plus 1
		for($i=(sizeof($arr_temp_check)-1);$i>=0;$i--){
			if($arr_temp_check[$i]['number'] <= $arr_temp_check[$i]['final']){
				$arr_temp[$i] = $arr_temp[$i] + 1;
				$checkload = true;
				break;
			}
		}
		if($checkload){
			echo "k:".$k;dd($arr_temp,false);
			backtracking($k+1,$arr_temp,$arrs,$percen,$litmit);
		}
	}
	//dd(time(),false);
	
	
?>