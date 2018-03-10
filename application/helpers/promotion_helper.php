<?php

function get_promotion_helper($customer_stringpromotion,$arrTotal,$cart,$totalkg_sale,$giatridonhang)
{
	$return = array();
	$arrPromotions = json_decode($customer_stringpromotion);
	//echo "<pre>"; print_r($arrPromotions); echo "</pre>"; exit;
	$GLOBALS['arrTotal']= $arrTotal;
	$GLOBALS['giatridonhang'] = $giatridonhang;
	$GLOBALS['tongkg_km_rieng'] = '';
	$GLOBALS['tong_tien_tru'] = array();
	if(!empty($arrPromotions) && sizeof($arrPromotions) > 0 && $cart) {
		$i=0;
		foreach($arrPromotions as $arrPromotion) {
			if($arrPromotion && $arrPromotion->list_name){
				$soluong = 0;
				// du lieu dau ra
				$return[$i]['type'] = $arrPromotion->type;
				$return[$i]['list_name'] = $arrPromotion->list_name;
				$return[$i]['name'] = $arrPromotion->name;
				$return[$i]['not_check_all'] = $arrPromotion->not_check_all;
				$return[$i]['promotion_kg'] = $arrPromotion->promotion_kg;
				$return[$i]['congthuctinh'] = '';
				$return[$i]['promotion_pecen'] = $arrPromotion->promotion_pecen;
				$return[$i]['promotion_type'] = $arrPromotion->promotion_type;
				// kiem tra xem khuyen mai kg hay %
				if($arrPromotion->promotion_pecen > 0){
					$result = countpercen($arrPromotion,$cart,$totalkg_sale);
				}else{
					$result = countkg($arrPromotion,$cart,$totalkg_sale);
				}
				$return[$i]['soluong'] = $result['soluong'];
				$return[$i]['dongia'] = $result['dongia'];
				$return[$i]['money'] = $result['money'];
				$return[$i]['congthuctinh'] = $result['congthuctinh'];
				$i++;
			}
		}
	}
	//echo "<pre>"; print_r($return); echo "</pre>"; exit;
	return $return;
}

function countkg($arrPromotion,$cart,$totalkg_sale){
	$checkall = false;
	$type = $arrPromotion->type;
	$item_id_promotion = $arrPromotion->item_id_promotion;
	$promotion_pecen = $arrPromotion->promotion_pecen;
	$promotion_kg = $arrPromotion->promotion_kg;
	$kgtotalitem = $soluong = $money = $dongia = 0;
	$congthuctinh = '';
	$GLOBALS['donvicongthuctinh'] = '';
	$donvicongthuctinh = '';
	if($type == 'khac'){
		$arr_items_promotions = explode(',',$item_id_promotion);
		foreach($arr_items_promotions as $arr_items_promotion){
			//$kgtotalitem = 0;
			$soluongtru = 0;
			if($arr_items_promotion){
				foreach(array_reverse($cart, true) as $line=>$item)
				{
					//echo "<pre>";print_r($item);echo "</pre>";
					if($arr_items_promotion == $item['item_id']){
						$type = $item['category'];
						congthuc_tinhtheo_sanphankhac($item['quantity'],$item['unit_weigh']);					
						$soluongtru = $item['quantity'] * $item['unit_weigh'];
						// neu chon la khong ap dung san pham vao tinh tong khuyen mai
						if($arrPromotion->not_check_all == 1){
							tinh_tong_kg_khuyen_mai_rieng($type,$soluongtru);
							if(!isset($item['price'])){
								$price = $item['sale_price'];
							}else{
								$price = $item['price'];
							}
							// tach tong co bao nhieu kg hon hop va dam dac
							totaltypeprivateitem($type,$soluongtru);
						}		
					}
				}
			}
		}
	}else if($type == 'all'){
		// B1: tinh tong kg can khuyen mai
		foreach(array_reverse($cart, true) as $line=>$item){
			if($GLOBALS['donvicongthuctinh']){
				$GLOBALS['donvicongthuctinh'] .= '+'. $item['unit_weigh']*$item['quantity'];
			}else{
				$GLOBALS['donvicongthuctinh'] = $item['unit_weigh']*$item['quantity'];	
			}			
		}
		// B2: neu la phan khuyen mai thi tru them cac san pham khac khong tinh khuyen mai
		if($arrPromotion->promotion_type == 'khuyen_mai'){
			if($GLOBALS['tongkg_km_rieng'] !== ''){
				$GLOBALS['donvicongthuctinh'] = $GLOBALS['donvicongthuctinh'] ."-(". $GLOBALS['tongkg_km_rieng'].")";
			}			
		}
		// Tac rieng de tru vao phan tram san pham hon hop va dam dac
		tach_tien_honhop_damdac($promotion_kg,$arrPromotion->promotion_type);
	}else{
		if(isset($GLOBALS['arrTotal']['kl'][$type])){
			foreach(array_reverse($cart, true) as $line=>$item){
				if($item['category'] == $type){
					if($GLOBALS['donvicongthuctinh']){
					$GLOBALS['donvicongthuctinh'] .= '+'. $item['unit_weigh']*$item['quantity'];
					}else{
						$GLOBALS['donvicongthuctinh'] = $item['unit_weigh']*$item['quantity'];	
					}
				}				
				
			}
		}
		// neu chon chi tinh cho no thi phai tru di
		if($arrPromotion->not_check_all == 1){
			tinh_tong_kg_khuyen_mai_rieng($type,$GLOBALS['arrTotal']['kl'][$type]);
		}
		// B2: neu la phan khuyen mai thi tru them cac san pham khac khong tinh khuyen mai
		if($arrPromotion->promotion_type == 'khuyen_mai'){
			if(isset($GLOBALS[$type]['tongkg_km_rieng']) && $GLOBALS[$type]['tongkg_km_rieng'] !== ''){
				$GLOBALS['donvicongthuctinh'] = $GLOBALS['donvicongthuctinh'] ."-(". $GLOBALS[$type]['tongkg_km_rieng'].")";
			}			
		}
	}
	if($GLOBALS['donvicongthuctinh'] !== ''){
		eval( '$sokgkhuyenmai = (' . $GLOBALS['donvicongthuctinh'] . ');' );	
		$soluong = $sokgkhuyenmai . ' Kg';
		$money = $sokgkhuyenmai * $promotion_kg;
		$congthuctinh = $GLOBALS['donvicongthuctinh']. " (kg)";
		$dongia =  to_currency($promotion_kg) . "/kg";
		// Luu cac so tien can phai tru de phuc vu cho tinh khuyen mai phan tram
		luu_so_tien_phai_tru($type,$money);
	}
	return array(
		'soluong' => $soluong,
		'dongia' => $dongia,
		'money' => $money,
		'congthuctinh' => $congthuctinh
	);
}
// Tinh phan tram
function countpercen($arrPromotion,$cart,$totalkg_sale){
	$GLOBALS['donvicongthuctinh'] = '';
	$checkall = false;
	//echo "<pre>"; print_r($arrTotal); echo "</pre>"; exit;
	$type = $arrPromotion->type;
	$item_id_promotion = $arrPromotion->item_id_promotion;
	$promotion_pecen = $arrPromotion->promotion_pecen;
	$promotion_kg = $arrPromotion->promotion_kg;
	$kgtotalitem = 0;
	$moneythucte = 0;
	$congthuctinh = '';
	$soluong = $dongia = $money = 0;
	if($type == 'khac'){
		$arr_items_promotions = explode(',',$item_id_promotion);
		$moneykhac =0;
		foreach($arr_items_promotions as $arr_items_promotion){
			if($arr_items_promotion){
				foreach(array_reverse($cart, true) as $line=>$item)
				{
					if($arr_items_promotion == $item['item_id']){
						$type = $item['category'];
						if(!isset($item['price'])){
							$price = $item['sale_price'];
						}else{
							$price = $item['price'];
						}
						$GLOBALS['donvicongthuctinh'] = $price * $item['quantity'] * $item['unit_weigh'];
					}
				}
			}
		}
	}else if($type == 'all'){
		$GLOBALS['donvicongthuctinh'] = $GLOBALS['giatridonhang'];
		// tru di so tien voi thuc an hon hop va thuc an dam dac		
		if(isset($GLOBALS['arrTotal']['money']['thuc_an_hon_hop'])){
			$tien = ($GLOBALS['arrTotal']['money']['thuc_an_hon_hop'] * $promotion_pecen)/100;
			luu_so_tien_phai_tru('thuc_an_hon_hop',$tien);
		}	
		if(isset($GLOBALS['arrTotal']['money']['thuc_an_dam_dac'])){
			$tien = ($GLOBALS['arrTotal']['money']['thuc_an_dam_dac'] * $promotion_pecen)/100;
			luu_so_tien_phai_tru('thuc_an_dam_dac',$tien);
		}
		//
	}else{
		if(isset($GLOBALS['tong_tien_tru'][$type])){
			if(isset($GLOBALS['arrTotal']['money'][$type])){
				$GLOBALS['donvicongthuctinh'] = $GLOBALS['arrTotal']['money'][$type]."-".$GLOBALS['tong_tien_tru'][$type];
			}
		}else{
			if(isset($GLOBALS['arrTotal']['money'][$type])){
				$GLOBALS['donvicongthuctinh'] = $GLOBALS['arrTotal']['money'][$type];
			}			
		}
		
	}
	if($GLOBALS['donvicongthuctinh'] !== ''){
		$congthuctinh  = $GLOBALS['donvicongthuctinh'];
		$congthuctinh = str_replace("--","-",$GLOBALS['donvicongthuctinh'] );
		//echo $GLOBALS['donvicongthuctinh']."*****"; "</br>";
		eval( '$moneythucte = (' . $congthuctinh . ');' );
		$soluong = to_currency($moneythucte);
		$dongia =  $promotion_pecen . "%";
		$money = ($moneythucte * $promotion_pecen)/100;
		$congthuctinh = $GLOBALS['donvicongthuctinh']. " (Đ)";
		// Luu cac so tien can phai tru de phuc vu cho tinh khuyen mai phan tram
		luu_so_tien_phai_tru($type,round($money));
	}
	return array(
		'soluong' => $soluong,
		'dongia' => $dongia,
		'money' => $money,
		'congthuctinh' => $congthuctinh
	);
}
function luu_so_tien_phai_tru($type,$money){
	if(isset($GLOBALS['tong_tien_tru'][$type])){
		if($money !== ''){
			$GLOBALS['tong_tien_tru'][$type] .= "-".$money;	
		}
		//
	}else{
		$GLOBALS['tong_tien_tru'][$type] = $money;
	}
	//echo $GLOBALS['tong_tien_tru'][$type]; exit;
}
function tach_tien_honhop_damdac($promotion_kg,$type){
	if(isset($GLOBALS['arrTotal']['kl']['thuc_an_hon_hop'])){
			$tongkghonhop = $GLOBALS['arrTotal']['kl']['thuc_an_hon_hop'];
			if(isset($GLOBALS['itemkhuyenmai2']['thuc_an_hon_hop']) && $type == 'khuyen_mai'){
				$tongkghonhop = $tongkghonhop - $GLOBALS['itemkhuyenmai2']['thuc_an_hon_hop'];
			}
			//voi san pham hon hop
			luu_so_tien_phai_tru('thuc_an_hon_hop',$tongkghonhop * $promotion_kg);
		}
		if(isset($GLOBALS['arrTotal']['kl']['thuc_an_dam_dac'])){
			$tongkgdamdac = $GLOBALS['arrTotal']['kl']['thuc_an_dam_dac'];
			if(isset($GLOBALS['itemkhuyenmai2']['thuc_an_dam_dac']) && $type == 'khuyen_mai'){
				$tongkgdamdac = $tongkgdamdac - $GLOBALS['itemkhuyenmai2']['thuc_an_dam_dac'];
			} 
			luu_so_tien_phai_tru('thuc_an_dam_dac',$tongkgdamdac * $promotion_kg);
		}
}
// Cong thuc tinh theo tung san pham (voi chuong trinh khuyen mai la khac)
function congthuc_tinhtheo_sanphankhac($quantity,$unit_weigh){
	// hien thi cong thuc tinh
	if($GLOBALS['donvicongthuctinh'] !== ''){
		$GLOBALS['donvicongthuctinh'] .= "+".$quantity * $unit_weigh;
	}else{
		$GLOBALS['donvicongthuctinh'] = $quantity * $unit_weigh;
	}	
};

function totaltypeprivateitem($type,$kgtotalitem){
	if(isset($GLOBALS['itemkhuyenmai2'][$type])){
		$GLOBALS['itemkhuyenmai2'][$type] = $GLOBALS['itemkhuyenmai2'][$type] + $kgtotalitem;
	}else{
		$GLOBALS['itemkhuyenmai2'][$type] = $kgtotalitem;
	}
	//echo "<pre>"; print_r($GLOBALS['itemkhuyenmai2']); echo '</pre>'; //exit;
	
}

// So luong bi tru do san pham chi tinh gia rieng
function tinh_tong_kg_khuyen_mai_rieng($type,$soluongtru){
	if($GLOBALS['tongkg_km_rieng'] !== ''){
		$GLOBALS['tongkg_km_rieng'] .= "+".$soluongtru;
		$GLOBALS[$type]['tongkg_km_rieng'] .= "+".$soluongtru;
	}else{
		$GLOBALS['tongkg_km_rieng'] = $soluongtru;
		$GLOBALS[$type]['tongkg_km_rieng'] = $soluongtru;
	}	
}
?>
