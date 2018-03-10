<?php
	
	function getresult($arrs){
		$i=0;
		// get litmit
		foreach($arrs as $arr){
			$arrLimit[$i] = $GLOBALS['limit'];
			if(isset($arr['litmit']) && $arr['litmit'] > 0){
				$arrLimit[$i] = $arr['litmit'];
			}
			$i++;
		}
		$percen1 = $arrs[0]['percen1'];
		for($a=1;$a<=$arrLimit[0];$a++){
			for($b=1;$b<=$arrLimit[1];$b++){
				for($c=1;$c<=$arrLimit[2];$c++){
					for($d=1;$d<=$arrLimit[3];$d++){
						for($e=1;$e<=$arrLimit[4];$e++){
							for($f=1;$f<=$arrLimit[5];$f++){
								for($g=1;$g<=$arrLimit[6];$g++){
									for($h=1;$h<=$arrLimit[7];$h++){
										for($i=1;$i<=$arrLimit[8];$i++){
											$total = $a+$b+$c+$d+$e+$f+$g+$h+$i;
											$totalpercen = 
											$a*$arrs[0]['percen1']
											+ $b*$arrs[1]['percen1']
											+ $c*$arrs[2]['percen1']
											+ $d*$arrs[3]['percen1']
											+ $e*$arrs[4]['percen1']
											+ $f*$arrs[5]['percen1']
											+ $g*$arrs[6]['percen1']
											+ $h*$arrs[7]['percen1']
											+ $i*$arrs[8]['percen1']
											;
											$result_percen = ($totalpercen/$total);
											if($result_percen == $GLOBALS['percen']){
												dd($result_percen);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		
	};
	
?>