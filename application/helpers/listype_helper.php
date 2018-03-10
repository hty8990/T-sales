<?php

function get_listtype_two()
{
	$arrTypeItem = array(
		'all' => 'Tất cả',
		'thuc_an_dam_dac' => 'Thức ăn đậm đặc',
		'thuc_an_hon_hop' => 'Thức ăn hỗn hợp',
		'khac' => 'Khác',
	);
	return $arrTypeItem;
}

function loai_thu_chi()
{
	$arrTypeItem = array(
		'so_thu' => 'Sổ thu',
		'so_chi' => 'Sổ chi'
	);
	return $arrTypeItem;
}

function loai_khach_hang()
{
	$arrTypeItem = array(
		'khach_hang' => 'Khách hàng',
		'nha_cung_cap' => 'Nhà cung cấp',
		'khac' => 'Khác'
	);
	return $arrTypeItem;
}

function get_listtype_three()
{
	$arrTypeItem = array(
		'' => ' -- Chọn loại thức ăn --',
		'thuc_an_dam_dac' => 'Thức ăn đậm đặc',
		'thuc_an_hon_hop' => 'Thức ăn hỗn hợp',
		'sang_bao'	=> 'Sang bao',
		'khac' => 'Khác',
	);
	return $arrTypeItem;
}

function get_quycach()
{
	$arrTypeItem = array(
		'' => ' -- Chọn quy cách --',
		'1' => '1 Kg',
		'2' => '2 Kg',
		'5' => '5 Kg',
		'10' => '10 Kg',
		'15' => '15 Kg',
		'20' => '20 Kg',
		'25' => '25 Kg',
		'40' => '40 Kg',
	);
	return $arrTypeItem;
}

function get_type_promotion()
{
	$arrTypeItem = array(
		'khuyen_mai' => 'Khuyến mãi',
		'triet_khau' => 'Triết khấu'
	);
	return $arrTypeItem;
}
?>
