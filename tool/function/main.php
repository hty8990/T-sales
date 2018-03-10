<?php
	
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

	function set_limit($arrs){
		$i=0;
		foreach($arrs as $arr){
			$arrLimit[$i] = $GLOBALS['limit'];
			if(isset($arr['litmit']) && $arr['litmit'] > 0){
				$arrLimit[$i] = $arr['litmit'];
			}
			$i++;
		}
		return $arrLimit;
	}

	function set_arrvalue($datas,$values,$size){
		for($i=0;$i<$size;$i++){
			$arrvalue[$i]['name'] = $datas[$i]['name'];
			$arrvalue[$i]['number'] = $values[$i];
		}
		return $arrvalue;
	}

	function get_result($result,$arrvalue,$param,$datas){
		
		if($total_result['percen1'] == $param['percen1']){
			if($total_result['money'] < $result['min_money']){
				$check_return = true;
				$result['data'] = $arrvalue;
				$result['min_money'] = $total_result['money'];
				$result['success'] = true;
			}
		}
		return $result;
	}

	function get_percen_money($values,$datas,$size){
		$total = $totalpercen1 = $totalmoney =0;
		for($i=0;$i<$size-1;$i++){
			$total = $total + $values[$i];
			$totalpercen1 = $totalpercen1 +  $values[$i]*$datas[$i]['percen1'];
			$totalmoney = $totalmoney + $values[$i]*$datas[$i]['price'];
		}
		$result['percen1'] = round(($totalpercen1/$total), 2);
		$result['money'] = $totalmoney;
		return $result;
	}
	
	
?>