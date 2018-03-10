<?php

function congnothu_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('STT' => "TT"),
		array('ma_khach_hang' => "Mã khách hàng"),
		array('ten_dia_chi_kh' => "Tên Kh/địa chỉ"),
		array('no_dau_ky' => "Nợ đầu kỳ"),
		array('no_tk' => "Nợ trong kỳ"),
		array('no_ck' => "Tổng"),
	);
	

	return transform_headers($headers);
}

function congnotra_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		////array('STT' => "TT"),
		//array('ma_khach_hang' => "Tên nhà cung cấp"),
		array('no_dau_ky' => "Nợ đầu kỳ"),
		array('hang_xuat_kho' => "Giá trị hàng nhập/xuất kho"),
		array('so_chi_nha_cung_cap' => "Sổ chi nhà cung cấp"),
		array('tong_no' => "Tổng nợ"),
	);

	return transform_headers($headers);
}

function get_congnotra_data_row($sale,$i, $controller,$start_date,$end_date)
{
	//echo "<pre>"; print_r($sale); echo "</pre>"; exit;
	$CI =& get_instance();
	$controller_name = $CI->uri->segment(1);
	$row = array (
		'STT' => $i,
		'ma_khach_hang' => $sale->full_name,
		'ten_dia_chi_kh' => $sale->address,
		'no_dau_ky' => to_currency($sale->no_dau_ky),
		'no_tk' => to_currency($sale->no_trong_ky),
		'no_ck' => to_currency($sale->no_ck),
		'edit' => anchor($controller_name."/BC02_chitietcongnophaitra/$sale->supplier_id/$start_date/$end_date", '<span class="glyphicon glyphicon-info-sign icon-th"></span>',
			array('class'=>'modal-dlg', 'title'=>"Xem chi tiết công nợ của $sale->full_name"))
	);
	return $row;
}

function get_congnotra_data_last_row($tongnodauky, $tongnotrongky)
	{
		$CI =& get_instance();
		$table_data_rows = '';
		$tien_no_khach_hang = 0;
		$no_ck = $tongnodauky + $tongnotrongky;
		$sum_change_due = 0;

		return array(
			'sale_id' => '',
			'STT' => '',
			'ma_khach_hang' => '',
			'ten_dia_chi_kh' => '<b>'.$CI->lang->line('sales_total').'</b>',
			'no_dau_ky' => '<b>'. to_currency($tongnodauky).'</b>',
			'no_tk' => '<b>'. to_currency($tongnotrongky).'</b>',
			'no_ck' => '<b>'.to_currency($no_ck).'</b>'
		);	
	}

function hanghoanhapkho_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('ma_hang_hoa' => "Mã hàng hóa"),
		array('te_hang_hoa' => "Tên hàng hóa"),
		array('so_luong' => "Số lượng bao"),
		array('so_kg' => "Số lượng Kg"),
		array('gia_tri' => "Giá trị")
	);
	

	return transform_headers($headers);
}

function hanghoaxuatkhobaobi_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('ma_hang_hoa' => "Mã hàng hóa"),
		array('te_hang_hoa' => "Tên hàng hóa"),
		array('so_luong' => "Số lượng bao"),
		//array('gia_tri' => "Giá trị")
	);
	

	return transform_headers($headers);
}

function get_hanghoanhapkho_data_row($sale,$i, $controller,$customer_id=-1,$search='',$start_date,$end_date,$mode="")
{
	//echo "<pre>"; print_r($sale); echo "</pre>"; exit;
	$CI =& get_instance();
	$controller_name = $CI->uri->segment(1);
	$thanhtien = $sale->thanhtien;
	$soluong = $sale->soluong;
	if($customer_id > 0){
		$a=1;
	}else{
		$customer_id = -1;
	}
	$row = array (
		'ma_hang_hoa' => $sale->item_number,
		'te_hang_hoa' => $sale->name,
		'so_luong' =>  $soluong. ' bao',
		'gia_tri' => to_currency($thanhtien),
		'edit' => anchor($controller_name."/BC07_chitiethanghoanhapkho/$sale->id/$customer_id/$start_date/$end_date/$mode", '<span class="glyphicon glyphicon-info-sign icon-th"></span>',
			array('class'=>'modal-dlg', 'title'=>"Xem chi tiết sản phẩm $sale->name")
	)
	);
	return $row;
}

function get_hanghoanhapkho_data_last_row($sales, $controller)
{
	$CI =& get_instance();
	$table_data_rows = '';
	$tongtien = 0;
	$tongsoluong = 0;
	$sum_change_due = 0;
	$thanhtien=0;

	foreach($sales as $key=>$sale)
	{
		$tongtien += $sale->thanhtien;
		$tongsoluong += $sale->soluong;
	}

	return array(
		'ma_hang_hoa' => '',
		'te_hang_hoa' => '<b>Tổng</b>',
		'don_vi_tinh' => '',
		'so_luong' => $tongsoluong . ' Bao',
		'gia_tri' => '<b>'. to_currency($tongtien).'</b>'
	);
}

function get_hanghoanxuatkho_data_row($sale,$i, $controller,$customer_id=-1,$search='',$start_date,$end_date,$category="")
{
	//echo "<pre>"; print_r($sale); echo "</pre>"; exit;
	$CI =& get_instance();
	$controller_name = $CI->uri->segment(1);
	$thanhtien = $sale->thanhtien;
	$soluong = $sale->soluong;
	if($customer_id > 0){
		$a=1;
	}else{
		$customer_id = -1;
	}
	
	return $row;
}

function get_hanghoaxuatkho_data_last_row($sales, $controller)
{
	$CI =& get_instance();
	$table_data_rows = '';
	$tongtien = 0;
	$tongsoluong = 0;
	$sum_change_due = 0;
	$thanhtien=0;

	foreach($sales as $key=>$sale)
	{
		$tongtien += $sale->thanhtien;
		$tongsoluong += $sale->soluong;
	}

	return array(
		'ma_hang_hoa' => '',
		'te_hang_hoa' => '<b>Tổng</b>',
		'don_vi_tinh' => '',
		'so_luong' => $tongsoluong . ' Bao',
		'gia_tri' => '<b>'. to_currency($tongtien).'</b>'
	);

}

	function hanghoatonkho_table_headers()
	{
		$CI =& get_instance();

		$headers = array(
			array('ma_hang_hoa' => "Mã hàng hóa"),
			array('ten_hang_hoa' => "Tên hàng hóa"),
			array('ton_ky_truoc' => "Tồng kỳ trước"),
			array('ton_trong_ky' => "Tồng trong kỳ"),
			array('ton_tong' => "Tổng kg Tồn")
		);
		

		return transform_headers($headers);
	}

	function doanhsosanluong_table_headers()
	{
		$CI =& get_instance();

		$headers = array(
			array('so_kg_dam_dac' => "Số kg đậm đặc"),
			array('doanh_so_dam_dac' => "Doanh số đậm đặc"),
			array('so_kg_hon_hop' => "Số kg hỗn hợp"),
			array('doanh_so_hon_hop' => "Doanh số hỗn hợp"),
			array('kg_thuong_san_luong' => "kg thuởng sản luợng"),
			array('tien_thuong_san_luong' => "Số tiền thuởng sản luợng"),
			array('tien_van_chuyen' => "Tiền vận chuyển"),
			array('tong' => "Tổng"),
		);
		
		return transform_headers(array_merge($headers));
	}

	function doanhsokhachhang_table_headers()
	{
		$CI =& get_instance();

		$headers = array(
			//array('ngay_mua' => "Ngày mua hàng"),
			array('code' => "Mã khách hàng"),
			array('full_name' => "Tên khách hàng"),
			array('tongkg_ban' => "Số kg bán"),
			array('tongkg_tang' => "Số kg khuyến mại"),
			//array('gia_ban' => "Giá bán"),
			array('khuyen_mai' => "Khuyến mại"),
			array('thuong_san_luong' => "Thuởng sảng luợng"),
			array('tien_lai' => "giá trị đơn hàng"),
			//array('xem' => "")
		);
		
		return transform_headers(array_merge($headers));
	}

	function doanhsokhachhang_data_last_row($sales)
{
	$CI =& get_instance();
	$table_data_rows = '';
	$tongkg = 0;
	$tongtien = 0;


	foreach($sales as $sale)
	{
		$tongkg += (int)$sale['tong_kg'];
		$tongtien += (int)$sale['tien_lai_no_money'];
	}

	return array(
		'ngay_mua' => '',
		'full_name' => '<b>Tổng</b>',
		'tong_kg' => $tongkg . " kg",
		'gia_ban' => '',
		'gia_von' => '',
		'khuyen_mai' => '',
		'tien_lai' => '<b>'. to_currency($tongtien).'</b>'
	);
}

function ketquakinhdoanh_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('doanh_so_ban_hang' => "Doanh thu BH"),
		array('gia_von_hang_hoa' => "Giá vốn hàng hóa"),
		array('lai_gop' => "Lãi gộp"),
		array('chi_phi_khac' => "Chi phí khác"),
		array('lai_thuan' => "Lãi thuần")
	);
	
	return transform_headers(array_merge($headers));
}

function soquytienmat_table_headers()
	{
		$CI =& get_instance();

		$headers = array(
			array('ton_quy_ky_truoc' => "Tồn quỹ kỳ trước"),
			array('so_thu_trongky' => "Số tiền thu trong kỳ"),
			//array('so_chi_kytruoc' => "Số tiền chi kỳ trước"),
			array('so_chi_trongky' => "Số tiền chi trong kỳ"),
			array('con_lai' => "Còn lại")
		);
		
		return transform_headers(array_merge($headers));
	}
?>