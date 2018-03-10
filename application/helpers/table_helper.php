<?php
function get_receiving_item_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('sale_time' => "Ngày nhập hàng"),
		array('customer_name' => "Nhà cung cấp"),
		array('gia_tri_don_hang' => "Giá trị đơn hàng"),
		array('so_tien_thanh_toan' => "Số tiền đã thanh toán"),
		array('comment' => "Ghi chú"),
	);
	
	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}
function get_receiving_baobi_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('sale_time' => "Ngày nhập bao bì"),
		array('customer_name' => "Nhà cung cấp"),
		array('gia_tri_don_hang' => "Giá trị đơn hàng"),
		array('so_tien_thanh_toan' => "Số tiền đã thanh toán"),
		array('comment' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}
function get_sales_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('sale_time' => "Ngày bán hàng"),
		array('customer_name' => "Khách hàng"),
		array('gia_tri_don_hang' => "Giá trị đơn hàng"),
		array('so_tien_thanh_toan' => "Số tiền đã thanh toán"),
		array('hinh_thuc_thanh_toan' => "Hình thức thanh toán"),
		array('comment' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}
	$headers[] = array('viewpromotion' => '&nbsp', 'sortable' => FALSE);
	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}
function get_sales_return_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('sale_time' => "Ngày trả hàng"),
		array('customer_name' => "Khách hàng"),
		array('gia_tri_don_hang' => "Giá trị hàng trả lại"),
		array('so_tien_thanh_toan' => "Số tiền đã thanh toán"),
		array('hinh_thuc_thanh_toan' => "Hình thức thanh toán"),
		array('comment' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}
function get_sales_taiche_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('sale_time' => "Ngày Tái chế"),
		array('customer_name' => "Nhà cung cấp"),
		array('gia_tri_don_hang' => "Giá trị hàng tái chế"),
		array('so_tien_thanh_toan' => "Số tiền đã thanh toán"),
		array('hinh_thuc_thanh_toan' => "Hình thức thanh toán"),
		array('comment' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}
function get_sales_tra_ncc_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('sale_time' => "Ngày Trả hàng"),
		array('customer_name' => "Nhà cung cấp"),
		array('gia_tri_don_hang' => "Giá trị hàng trả lại"),
		array('so_tien_thanh_toan' => "Số tiền đã thanh toán"),
		array('hinh_thuc_thanh_toan' => "Hình thức thanh toán"),
		array('comment' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}
function get_sales_huy_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('sale_time' => "Ngày hủy hàng"),
		array('customer_name' => "Khách hàng"),
		array('gia_tri_don_hang' => "Giá trị hàng hủy"),
		array('so_tien_thanh_toan' => "Số tiền đã thanh toán"),
		array('hinh_thuc_thanh_toan' => "Hình thức thanh toán"),
		array('comment' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}
/*
 Gets the html data rows for the sales.
 */
function get_sale_data_last_row($sales, $controller)
{
	$CI =& get_instance();
	$table_data_rows = '';
	$gia_tri_don_hang = 0;
	$so_tien_thanh_toan = 0;
	$sum_change_due = 0;

	foreach($sales->result() as $key=>$sale)
	{
		$gia_tri_don_hang += $sale->order_money;
		$so_tien_thanh_toan += $sale->pay_money;
	}

	return array(
		'sale_id' => '-',
		'sale_time' => '<b>'.$CI->lang->line('sales_total').'</b>',
		'gia_tri_don_hang' => '<b>'. to_currency($gia_tri_don_hang).'</b>',
		'so_tien_thanh_toan' => '<b>'.to_currency($so_tien_thanh_toan).'</b>',
		'change_due' => '<b>'.to_currency($sum_change_due).'</b>'
	);
}

function get_sale_data_row($sale, $controller)
{
	$CI =& get_instance();
	$controller_name = $CI->uri->segment(1);
	if($sale->height_color == 1){
		$name = "<a>".$sale->full_name."</a>";
	}else{
		$name = $sale->full_name;
	}
	$row = array (
		'sale_id' => $sale->sale_id,
		'sale_time' => date( $CI->config->item('dateformat') . ' ' . $CI->config->item('timeformat'), strtotime($sale->sale_time) ),
		'customer_name' => $name,
		'gia_tri_don_hang' => to_currency($sale->order_money),
		'so_tien_thanh_toan' => to_currency($sale->pay_money),
		'comment' => $sale->comment,
		'hinh_thuc_thanh_toan' => $sale->payment_type,
		'edit' => anchor($controller_name."/edit_salse/$sale->sale_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('title'=>'Sửa'))
	);

	$row['viewpromotion'] = anchor($controller_name."/viewpromotion/$sale->sale_id", '<span class="glyphicon glyphicon-eye-open"></span>',
		array('class' => 'modal-dlg','title'=>"Xem chuong trinh khuyen mai")
	);

	$row['receipt'] = anchor($controller_name."/receipt/$sale->sale_id", '<span class="glyphicon glyphicon-print"></span>',
		array('title'=>$CI->lang->line('sales_show_receipt'),'target' => '_blank')
	);
	return $row;
}
function get_recive_data_row($sale, $controller)
{
	$CI =& get_instance();
	$controller_name = $CI->uri->segment(1);
	//echo "<pre>"; print_r($sale); echo "</pre>"; exit;
	$row = array (
		'sale_id' => $sale->receiving_id,
		'sale_time' =>date("d/m/Y", strtotime($sale->receiving_time)),
		'customer_name' => $sale->full_name,
		'gia_tri_don_hang' => to_currency($sale->order_money),
		'so_tien_thanh_toan' => to_currency($sale->pay_money),
		'comment' => $sale->comment,
		'edit' => anchor($controller_name."/edit_receving/$sale->receiving_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('title'=>'Sửa'))
	);


	$row['receipt'] = anchor($controller_name."/receipt/$sale->receiving_id", '<span class="glyphicon glyphicon-print"></span>',
		array('title'=>$CI->lang->line('sales_show_receipt'))
	);

	return $row;
}

function get_recive_tra_ncc_data_row($sale, $controller)
{
	$CI =& get_instance();
	//print_r($sale); exit;
	$controller_name = $CI->uri->segment(1);
	$row = array (
		'sale_id' => $sale->receiving_id,
		'sale_time' =>date("d/m/Y", strtotime($sale->receiving_time)),
		'customer_name' => $sale->full_name,
		'gia_tri_don_hang' => to_currency($sale->order_money),
		'so_tien_thanh_toan' => to_currency($sale->pay_money),
		'comment' => $sale->comment,
		'edit' => anchor($controller_name."/edit_salse_tncc/$sale->receiving_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('title'=>'Sửa'))
	);


	$row['receipt'] = anchor($controller_name."/receipt/$sale->receiving_id", '<span class="glyphicon glyphicon-print"></span>',
		array('title'=>$CI->lang->line('sales_show_receipt'))
	);

	return $row;
}
function get_recive_taiche_data_row($sale, $controller)
{
	$CI =& get_instance();
	//print_r($sale); exit;
	$controller_name = $CI->uri->segment(1);
	$row = array (
		'sale_id' => $sale->receiving_id,
		'sale_time' =>date("d/m/Y", strtotime($sale->receiving_time)),
		'customer_name' => $sale->full_name,
		'gia_tri_don_hang' => to_currency($sale->order_money),
		'so_tien_thanh_toan' => to_currency($sale->pay_money),
		'comment' => $sale->comment,
		'edit' => anchor($controller_name."/edit_salse_tc/$sale->receiving_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('title'=>'Sửa'))
	);


	$row['receipt'] = anchor($controller_name."/receipt/$sale->receiving_id", '<span class="glyphicon glyphicon-print"></span>',
		array('title'=>$CI->lang->line('sales_show_receipt'))
	);

	return $row;
}
/*
Get the sales payments summary
*/
function get_sales_manage_payments_summary($payments, $sales, $controller)
{
	$CI =& get_instance();
	$table = '<div id="report_summary">';

	foreach($payments as $key=>$payment)
	{
		$amount = $payment['payment_amount'];

		// WARNING: the strong assumption here is that if a change is due it was a cash transaction always
		// therefore we remove from the total cash amount any change due
		if( $payment['payment_type'] == $CI->lang->line('sales_cash') )
		{
			foreach($sales->result_array() as $key=>$sale)
			{
				$amount -= $sale['change_due'];
			}
		}
		$table .= '<div class="summary_row">' . $payment['payment_type'] . ': ' . to_currency( $amount ) . '</div>';
	}
	$table .= '</div>';

	return $table;
}

function transform_headers_readonly($array)
{
	$result = array();
	foreach($array as $key => $value)
	{
		$result[] = array('field' => $key, 'title' => $value, 'sortable' => $value != '', 'switchable' => !preg_match('(^$|&nbsp)', $value));
	}

	return json_encode($result);
}

function transform_headers($array)
{
	$result = array();
	$array = array_merge(array(array('checkbox' => 'select', 'sortable' => FALSE)),
		$array, array(array('edit' => '')));
	foreach($array as $element)
	{
		$result[] = array('field' => key($element),
			'title' => current($element),
			'switchable' => isset($element['switchable']) ?
				$element['switchable'] : !preg_match('(^$|&nbsp)', current($element)),
			'sortable' => isset($element['sortable']) ?
				$element['sortable'] : current($element) != '',
			'checkbox' => isset($element['checkbox']) ?
				$element['checkbox'] : FALSE,
			'class' => isset($element['checkbox']) || preg_match('(^$|&nbsp)', current($element)) ?
				'print_hide' : '');
	}
	return json_encode($result);
}

function get_people_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('people.code' => 'Mã khách hàng'),
		array('full_name' => $CI->lang->line('common_full_name')),
		array('address' => $CI->lang->line('common_address')),
		array('birthday' => 'Ngày sinh'),
		array('phone_number' => $CI->lang->line('common_phone_number')),
		array('people_manager' => 'Nhân viên quản lý'),
		array('ghi_chu' => 'Ghi chú')
	);

	if($CI->Employee->has_grant('messages', $CI->session->userdata('person_id')))
	{
		$headers[] = array('messages' => '', 'sortable' => FALSE);
	}
	
	return transform_headers($headers);
}

function get_product_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('code' => 'Mã sản phẩm'),
		array('name' => 'Tên sản phẩm'),
		array('price' => 'Thành tiền'),
		array('limit' => 'Giới hạn SL'),
		array('percen1' => 'Tỷ lệ % 1'),
		array('percen2' => 'Tỷ lệ % 2'),
		array('percen3' => 'Tỷ lệ % 3'),
		array('percen4' => 'Tỷ lệ % 4'),
		array('status' => 'Trạng thái'),
	);

	if($CI->Employee->has_grant('messages', $CI->session->userdata('person_id')))
	{
		$headers[] = array('messages' => '', 'sortable' => FALSE);
	}
	
	return transform_headers($headers);
}
function get_employ_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('username' => 'Tên đăng nhập'),
		array('full_name' => $CI->lang->line('common_full_name')),
		array('address' => $CI->lang->line('common_address')),
		array('phone_number' => $CI->lang->line('common_phone_number'))
	);

	if($CI->Employee->has_grant('messages', $CI->session->userdata('person_id')))
	{
		$headers[] = array('messages' => '', 'sortable' => FALSE);
	}
	
	return transform_headers($headers);
}
function get_person_data_row($person, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	if(isset($person->birthday) && $person->birthday !== ''){
		$birthday = date("d/m/Y", strtotime($person->birthday));
	}else{
		$birthday = '';
	}
	$counts = $CI->Customer->countBrithdayByid($person->person_id);
	$checkbrithday = true;
	if($counts){
		$subdate = $counts[0]['birthday'] - $counts[0]['day'] ;
		if($subdate == 0){
			$stringngay = 'Hôm nay';
		}else{
			$stringngay = $subdate.' ngày';
		}
		$birthday = $birthday.' <i class="glyphicon glyphicon-globe" aria-hidden="true"></i> '.$stringngay;
	}
	//echo "<pre>"; print_r($person); echo "</pre>"; exit;
	return array (
		'people.person_id' => $person->person_id,
		'people.code' => $person->code,
		'full_name' => $person->full_name,
		'ghi_chu' => $person->comments,
		'birthday' => $birthday,
		'address' => $person->address,
		'people_manager' => $person->people_manager,
		'phone_number' => $person->phone_number,
		'edit' => anchor($controller_name."/view/$person->person_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
	));
}
function get_product_data_row($data, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$status = 'Không hoạt động';
	if($data->status == 1){
		$status = 'Hoạt động';	
	}
	//echo "<pre>"; print_r($person); echo "</pre>"; exit;
	return array (
		'id' => $data->id,
		'code' => $data->code,
		'name' => $data->name,
		'limit' => $data->c_limit,
		'price' => to_currency($data->price),
		'percen1' => $data->percen1,
		'percen2' => $data->percen2,
		'percen3' => $data->percen3,
		'percen4' => $data->percen4,
		'status' => $status,
		'edit' => anchor($controller_name."/view/$data->id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
	));
}
function get_employ_data_row($person, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	return array (
		'username' => $person->username,
		'full_name' => $person->full_name,
		'address' => $person->address,
		'phone_number' => $person->phone_number,
		'messages' => empty($person->phone_number) ? '' : anchor("Messages/view/$person->person_id", '<span class="glyphicon glyphicon-phone"></span>', 
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('messages_sms_send'))),
		'edit' => anchor($controller_name."/view/$person->person_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
	));
}
function get_suppliers_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('agency_name' => $CI->lang->line('suppliers_agency_name')),
		array('full_name' => $CI->lang->line('common_full_name')),
		array('address' => $CI->lang->line('common_address')),
		array('email' => $CI->lang->line('common_email')),
		array('phone_number' => $CI->lang->line('common_phone_number'))
	);

	if($CI->Employee->has_grant('messages', $CI->session->userdata('person_id')))
	{
		$headers[] = array('messages' => '');
	}

	return transform_headers($headers);
}

function get_supplier_data_row($supplier, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));

	return array (
		'people.person_id' => $supplier->person_id,
		'agency_name' => $supplier->agency_name,
		'full_name' => $supplier->full_name,
		'address' => $supplier->address,
		'email' => empty($supplier->email) ? '' : mailto($supplier->email, $supplier->email),
		'phone_number' => $supplier->phone_number,
		'messages' => empty($supplier->phone_number) ? '' : anchor("Messages/view/$supplier->person_id", '<span class="glyphicon glyphicon-phone"></span>', 
			array('class'=>"modal-dlg", 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('messages_sms_send'))),
		'edit' => anchor($controller_name."/view/$supplier->person_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>"modal-dlg", 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update')))
		);
}

function get_items_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		//array('items.item_id' => $CI->lang->line('common_id')),
		array('item_number' => 'Mã SP'),
		array('name' => $CI->lang->line('items_name')),
		array('ma_bao_bi' => 'Mã BB'),
		array('unit_weigh' => 'QC'),
		array('category' => $CI->lang->line('items_category')),
		array('input_prices' => $CI->lang->line('items_input_prices')),
		array('sale_price' => $CI->lang->line('items_sale_price')),
		//array('quantity' => $CI->lang->line('items_quantity')),
		//array('tax_percents' => $CI->lang->line('items_tax_percents'), 'sortable' => FALSE),
		array('quantity' => 'Số lượng'),
		array('item_status' => 'Trạng thái'),
		array('inventory' => ''),
		array('stock' => '')
	);

	return transform_headers($headers);
}

function get_item_data_row($item, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));

	$image = '';
	if (!empty($item->pic_id))
	{
		$images = glob("uploads/item_pics/" . $item->pic_id . ".*");
		if (sizeof($images) > 0)
		{
			$image .= '<a class="rollover" href="'. base_url($images[0]) .'"><img src="'.site_url('items/pic_thumb/'.$item->pic_id).'"></a>';
		}
	}
	$ci =& get_instance();
	$ci->load->helper('listype');
	$arrListype = get_listtype_three();
	$listype = "";
	if($arrListype){
		foreach($arrListype as $key => $value){
			if($key == $item->category){
				$listype = $value;
			}
		}
	}
	if($item->status){
		$status = 'Hoạt động';
	}else{
		$status = 'Không hoạt động';
	}
	$quantityurl = site_url($controller_name."/change_quantities/$item->id");
	$linkeditquantity = '<a href="'.$quantityurl.'" class="modal-dlg" data-btn-submit="Cập nhật" title="Cập nhật số lượng của sản phẩm"><span class="glyphicon glyphicon-modal-window"></span></a>';
	return array (
		'items.id' => $item->id,
		'item_number' => $item->item_number,
		'name' => $item->name,
		'ma_bao_bi' => $item->ma_bao_bi,
		'unit_weigh' => $item->unit_weigh." Kg",
		'category' => $listype,
		'sale_price' => to_currency($item->sale_price),
		'input_prices' => to_currency($item->input_prices),
		'quantity' => $item->tondauky." bao ".$linkeditquantity,
		'item_status' => $status,
		'inventory' => anchor("items/manageprice/$item->id", '<span class="glyphicon glyphicon-eur"></span>', array('class'=>'pull-right', 'title' => 'Giá theo sản phẩm')),
		'stock' => anchor("items/managepeople/$item->id", '<span class=" 	glyphicon glyphicon-user"></span>',
			array('class'=>'pull-right', 'title' => 'Giá theo khách hàng')
		),
		'edit' => anchor($controller_name."/view/$item->id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class' => 'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title' => $CI->lang->line($controller_name.'_update'))
	));
}

function get_giftcards_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('giftcard_id' => $CI->lang->line('common_id')),
		array('last_name' => $CI->lang->line('common_last_name')),
		array('full_name' => $CI->lang->line('common_full_name')),
		array('giftcard_number' => $CI->lang->line('giftcards_giftcard_number')),
		array('value' => $CI->lang->line('giftcards_card_value'))
	);

	return transform_headers($headers);
}

function get_giftcard_data_row($giftcard, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));

	return array (
		'giftcard_id' => $giftcard->giftcard_id,
		'full_name' => $giftcard->full_name,
		'giftcard_number' => $giftcard->giftcard_number,
		'value' => to_currency($giftcard->value),
		'edit' => anchor($controller_name."/view/$giftcard->giftcard_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}

function get_so_thu_chi_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('date_time' => 'Ngày nhập'),
		array('customer_id' => 'Tên khách hàng, nhà cung cấp'),
		array('money' => 'Số tiền'),
		array('type' => 'Loai'),
		array('payment_type' => 'Hình thức thanh toán'),
		array('comment' => 'Ghi chú')
	);

	return transform_headers($headers);
}

function get_item_kits_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('item_number' => $CI->lang->line('item_kits_code')),
		array('name' => $CI->lang->line('item_kits_name')),
		array('name_item' => 'Mã sản phẩm'),
		array('unit_weight' => 'Quy cách'),
		array('input_prices' => $CI->lang->line('item_kits_price'), 'sortable' => FALSE),
		array('quantities' => 'Số lượng'),
		array('gia_tri' => 'Giá trị'),
		array('packet_price_manager' => '')	
	);

	return transform_headers($headers);
}
function get_promotion_manage_table_headers()
{
	$CI =& get_instance();
	$headers = array(
		array('sort' => "STT"),
		array('start_date' => 'Ngày bắt đầu'),
		array('end_date' => 'Ngày kết thúc'),
		array('promotion_type' => 'Loại sản phẩm'),
		array('promotion_name' => 'Hình thức khuyến mãi'),
		array('promotion_pecen' => '%'),
		array('promotion_kg' => "Kg"),
		array('description' => "Ghi chú"),
		array('inventory' => '')
	);

	return transform_headers($headers);
}
function get_item_kit_data_row($item_kit, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$input_price = '';
	$giatri = $item_kit->tondauky * $item_kit->input_prices;
	$quantityurl = site_url($controller_name."/change_quantities/$item_kit->id");
	$linkeditquantity = '<a href="'.$quantityurl.'" class="modal-dlg" data-btn-submit="Cập nhật" title="Cập nhật số lượng của sản phẩm"><span class="glyphicon glyphicon-modal-window"></span></a>';
	return array (
		'id' => $item_kit->id,
		'item_number' => $item_kit->item_number,
		'name' => $item_kit->name,
		'name_item' => $item_kit->ma_san_pham,
		'unit_weight' => $item_kit->unit_weight.' Kg',
		'gia_tri' => to_currency($giatri),
		'quantities' => $item_kit->tondauky.' túi '.$linkeditquantity,
		'input_prices' => to_currency($item_kit->input_prices),
		'packet_price_manager' => anchor($controller_name."/manageprice/$item_kit->id", '<span class="glyphicon glyphicon-eur"></span>', array('class'=>'pull-right', 'title' => 'Cập nhật giá cho bao bì')),
		'edit' => anchor($controller_name."/view/$item_kit->id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}
function get_so_thu_data_row($item_kit,$arrThuchi, $controller)
{
	$CI =& get_instance();
	$controller_name = $CI->uri->segment(1);
	$id = $item_kit['thu_chi_id'];
	if($item_kit['type'] == 1){
		$type = 'Nhập hang';
	}else{
		$type = 'Nhập ở sổ thu';
	}
	$row = array (
		'thu_chi_id' => $item_kit['thu_chi_id'],
		'date_time' => date("d/m/Y", strtotime($item_kit['date_time'])),
		'customer_id' => $item_kit['full_name'],
		'money' => to_currency($item_kit['money']),
		'comment' => $item_kit['comment'],
		'payment_type' => $item_kit['payment_type'],
		'type' => $type,
		'edit' => anchor($controller_name."/view/$id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>"modal-dlg", 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>'Cap nhat'))
		);
	return $row;
}
function get_so_chi_data_row($item_kit,$arrThuchi, $controller)
{
	$CI =& get_instance();
	$controller_name = $CI->uri->segment(1);
	$id = $item_kit['thu_chi_id'];
	if($item_kit['type'] == 1){
		$type = 'Nhập kho';
	}else{
		$type = 'Nhập ở sổ chi';
	}
	$row = array (
		'thu_chi_id' => $item_kit['thu_chi_id'],
		'date_time' => date("d/m/Y", strtotime($item_kit['date_time'])),
		'customer_id' => $item_kit['full_name'],
		'money' => to_currency($item_kit['money']),
		'comment' => $item_kit['comment'],
		'payment_type' => $item_kit['payment_type'],
		'type' => $type,
		'edit' => anchor($controller_name."/viewsochi/$id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>"modal-dlg", 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>'Cap nhat'))
		);
	return $row;
}
function get_promotion_data_row($item_kit, $controller, $arrListtype )
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$promotion_pecen = '';
	if($item_kit->promotion_pecen > 0){
		$promotion_pecen = $item_kit->promotion_pecen;
	}
	$promotion_kg = '';
	if($item_kit->promotion_kg > 0){
		$promotion_kg = to_currency_kg($item_kit->promotion_kg);
	}
	return array (
		'id' => $item_kit->id,
		'sort' => $item_kit->sort,
		'start_date' => date("d/m/Y", strtotime($item_kit->start_date)),
		'end_date' => date("d/m/Y", strtotime($item_kit->end_date)),
		'promotion_type' => $arrListtype[$item_kit->promotion_type],
		'promotion_name' => $item_kit->promotion_name,
		'promotion_pecen' => $promotion_pecen,
		'promotion_kg' => $promotion_kg,
		'description' => $item_kit->description,
		'inventory' => anchor($controller_name."/manageprice/$item_kit->id", '<span class="glyphicon glyphicon-eur"></span>', array('class'=>'pull-right', 'title' => 'Cập nhật đợt khuyến mãi')),
		'edit' => anchor($controller_name."/view/$item_kit->id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		)
		);
}

function get_congnothu_data_last_row($sales, $controller)
{
	$CI =& get_instance();
	$table_data_rows = '';
	$no_trong_ky = 0;
	$no_ck = 0;
	$no_dau_ky = 0;
	$sum_change_due = 0;

	foreach($sales->result() as $key=>$sale)
	{
		$no_trong_ky += $sale->no_trong_ky;
		$no_dau_ky += $sale->no_dau_ky;
		$no_ck += $sale->no_ck;
	}

	return array(
		'sale_id' => '',
		'STT' => '',
		'ma_khach_hang' => '',
		'ten_dia_chi_kh' => '<b>'.$CI->lang->line('sales_total').'</b>',
		'no_dau_ky' => '<b>'. to_currency($no_dau_ky).'</b>',
		'no_tk' => '<b>'. to_currency($no_trong_ky).'</b>',
		'no_ck' => '<b>'.to_currency($no_ck).'</b>',
	);
}

function quan_ly_gia_theo_san_pham_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('start_date' => "Ngày bắt đầu"),
		array('input_prices' => "Giá gốc"),
		array('sale_price' => "Giá bán"),
		array('description' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}

function chi_tiet_khuyen_mai_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('start_date' => "Ngày bắt đầu"),
		array('end_date' => "Ngày kết thúc"),
		array('promotion_percent' => "Khuyến mại %"),
		array('promotion_kg' => "Khuyến mại Kg"),
		array('description' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}

function chi_tiet_gia_bao_bi_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('start_date' => "Ngày bắt đầu"),
		array('input_prices' => "Giá nhập vào"),
		array('description' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}

function quan_ly_gia_theo_khach_hang_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('start_date' => "Ngày bắt đầu"),
		array('end_date' => "Ngày kết thúc"),
		array('khach_hang' => "Khách hàng"),
		array('sale_price' => "Giá bán"),
		array('description' => "Ghi chú"),
	);
	
	if($CI->config->item('invoice_enable') == TRUE)
	{
		//$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		//$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	return transform_headers(array_merge($headers, array(array('receipt' => '&nbsp', 'sortable' => FALSE))));
}

function get_item_price_data_row($item, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));

	return array (
		'id' => $item->id,
		'start_date' => date("d/m/Y", strtotime($item->start_date)),
		'sale_price' => to_currency($item->sale_price),
		'input_prices' => to_currency($item->input_prices),
		'description' => $item->description,
		'edit' => anchor($controller_name."/view/$item->id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class' => 'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title' => $CI->lang->line($controller_name.'_update'))
		)
		);
}

function get_promotion_detail_data_row($item, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$promotion_percent = '';
	if($item->promotion_percent > 0){
		$promotion_percent = $item->promotion_percent;
	}
	$promotion_kg = '';
	if($item->promotion_kg > 0){
		$promotion_kg = to_currency_kg($item->promotion_kg);
	}
	return array (
		'id' => $item->id,
		'start_date' => date("d/m/Y", strtotime($item->start_date)),
		'end_date' => date("d/m/Y", strtotime($item->end_date)),
		'promotion_kg' => $promotion_kg,
		'promotion_percent' => $item->promotion_percent,
		'description' => $item->description,
		'edit' => anchor($controller_name."/view/$item->id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class' => 'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title' => $CI->lang->line($controller_name.'_update'))
		)
		);
}

function get_packet_price_detail_data_row($item, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$input_prices = '';
	if($item->input_prices > 0){
		$input_prices = $item->input_prices;
	}
	return array (
		'id' => $item->id,
		'start_date' => date("d/m/Y", strtotime($item->start_date)),
		'input_prices' => to_currency($input_prices),
		'description' => $item->description,
		'edit' => anchor($controller_name."/view/$item->id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class' => 'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title' => $CI->lang->line($controller_name.'_update'))
		)
		);
}

function get_item_price_customer_data_row($item, $controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));

	return array (
		'id' => $item->id,
		'start_date' => date("d/m/Y", strtotime($item->start_date)),
		'end_date' => date("d/m/Y", strtotime($item->end_date)),
		'sale_price' => to_currency($item->sale_price),
		'khach_hang' => $item->full_name,
		'description' => $item->description,
		'edit' => anchor($controller_name."/view/$item->id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class' => 'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title' => $CI->lang->line($controller_name.'_update'))
		)
		);
}
?>
