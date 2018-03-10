<?php
	
	function total_4($arrs,$arrLimit,$result){
		for($a=1;$a<=$arrLimit[0];$a++){
			for($b=1;$b<=$arrLimit[1];$b++){
				for($c=1;$c<=$arrLimit[2];$c++){
					for($d=1;$d<=$arrLimit[3];$d++){
						$values[0] = $a; $values[1] = $b; $values[2] = $c; $values[3] = $d;
						// tinh ra ket qua %
						$total_result = get_percen_money($values,$arrs,4);
						if($total_result['percen1'] == $GLOBALS['percen1']){
							if($total_result['money'] < $result['min_money']){
								$result['min_money'] = $total_result['money'];
								$result['data'] = $values;
							}
						}
					}
				}
			}
		}
		return $result;
	}
	
	
?>