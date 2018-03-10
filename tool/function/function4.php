<?php
	
	function getresult($arrs){
		$i=0;
		echo time();
		// get litmit
		foreach($arrs as $arr){
			$arrLimit[$i] = $GLOBALS['limit'];
			if(isset($arr['litmit']) && $arr['litmit'] > 0){
				$arrLimit[$i] = $arr['litmit'];
			}
			$i++;
		}
		$z = 0;
		$percen1 = $arrs[0]['percen1'];
		$check_money = false;
		for($a=1;$a<=$arrLimit[0];$a++){
			for($b=1;$b<=$arrLimit[1];$b++){
				for($c=1;$c<=$arrLimit[2];$c++){
					for($d=1;$d<=$arrLimit[3];$d++){
						$total = $a+$b+$c+$d;
						$totalpercen = 
						$a*$arrs[0]['percen1']
						+ $b*$arrs[1]['percen1']
						+ $c*$arrs[2]['percen1']
						+ $d*$arrs[3]['percen1']
						;
						$result_percen = ($totalpercen/$total);
						if($result_percen == $GLOBALS['percen']){
							// thanh tien
							$total_money = $a*$arrs[0]['price']
							+ $b*$arrs[1]['price']
							+ $c*$arrs[2]['price']
							+ $d*$arrs[3]['price'];
							$arr_result['total'] = $total_money;
							$arr_result[0]['name'] = $arrs[0]['name'];
							$arr_result[0]['percen1'] = $arrs[0]['percen1'];
							$arr_result[0]['price'] = $arrs[0]['price'];
							$arr_result[0]['soluong'] = $a;
							$arr_result[1]['name'] = $arrs[1]['name'];
							$arr_result[1]['percen1'] = $arrs[1]['percen1'];
							$arr_result[1]['price'] = $arrs[1]['price'];
							$arr_result[1]['soluong'] = $b;
							$arr_result[2]['name'] = $arrs[2]['name'];
							$arr_result[2]['percen1'] = $arrs[2]['percen1'];
							$arr_result[2]['price'] = $arrs[2]['price'];
							$arr_result[2]['soluong'] = $c;
							$arr_result[3]['name'] = $arrs[3]['name'];
							$arr_result[3]['percen1'] = $arrs[3]['percen1'];
							$arr_result[3]['price'] = $arrs[3]['price'];
							$arr_result[3]['soluong'] = $d;
							if($check_money ){
								if($total_money < $arr_result['total']){
									$result = $arr_result;
								}
							}else{
								$result = $arr_result;
							}							
							$check_money = true;
							$z++;
						}
					}
				}
			}
		}
		echo time();
		dd($result);
	};
	
?>