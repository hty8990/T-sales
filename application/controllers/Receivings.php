<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Receivings extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('receivings');

		$this->load->library('receiving_lib');
		$this->load->library('barcode_lib');
	}

	public function index()
	{
		$this->_reload();
	}
	public function manage()
	{
		$person_id = $this->session->userdata('person_id');
		//echo $person_id; exit;
		$mode = $this->receiving_lib->get_mode();
		if(!$this->Employee->has_grant('reports', $person_id))
		{
			redirect('no_access/sales/reports_sales');
		}
		else
		{
			if($mode == 'receive'){
				$data['table_headers_text'] = 'Lịch sử nhập sản phẩm';
				$data['table_headers'] = get_receiving_item_manage_table_headers();
				$this->load->view('receivings/manage', $data);
			}else{
				$data['table_headers_text'] = 'Lịch sử nhập bao bì';
				$data['table_headers'] = get_receiving_baobi_manage_table_headers();
				$this->load->view('receivings/manage', $data);
			}
		}
	}
	public function search()
	{
		$mode = $this->receiving_lib->get_mode();
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');
		$is_valid_receipt = FALSE;
		$filters = array(
						'start_date' => $this->input->get('start_date'),
						'end_date' => $this->input->get('end_date'),
						'is_valid_receipt' => $is_valid_receipt);
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);
		$filters = array_merge($filters, $filledup);
		if($mode == 'receive'){
			$type = 1;
		}else{
			$type = 2;
		}
		$sales = $this->Receiving->search_item($type,$search, $filters, $limit, $offset, $sort, $order);
			$total_rows = $this->Receiving->get_found_rows($type,$search, $filters, $limit, $offset, $sort, $order);
		$data_rows = array();
		foreach($sales->result() as $sale)
		{
			$data_rows[] = $this->xss_clean(get_recive_data_row($sale, $this));
		}

		if($total_rows > 0)
		{
			$data_rows[] = $this->xss_clean(get_sale_data_last_row($sales, $this));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	public function item_search()
	{
		$mode = $this->receiving_lib->get_mode();
		if($mode == 'receive'){
			$suggestions = $this->Item->get_search_suggestions($this->input->get('term'), array('search_custom' => FALSE, 'is_deleted' => TRUE), TRUE);
		}else{
			$suggestions = $this->Item_kit->get_search_recevie($this->input->get('term'));
		}
		
		//$suggestions = array_merge($suggestions, $this->Item_kit->get_search_suggestions($this->input->get('term'))); 

		$suggestions = $this->xss_clean($suggestions);

		echo json_encode($suggestions);
	}

	public function select_supplier()
	{
		$supplier_id = $this->input->post('supplier');
		if($this->Supplier->exists($supplier_id))
		{
			$this->receiving_lib->set_supplier($supplier_id);
		}

		$this->_reload();
	}

	public function change_mode()
	{
		$stock_destination = $this->input->post('stock_destination');
		$stock_source = $this->input->post('stock_source');

		if((!$stock_source || $stock_source == $this->receiving_lib->get_stock_source()) &&
			(!$stock_destination || $stock_destination == $this->receiving_lib->get_stock_destination()))
		{
			$this->receiving_lib->clear_reference();
			$mode = $this->input->post('mode');
			$this->receiving_lib->set_mode($mode);
		}
		elseif($this->Stock_location->is_allowed_location($stock_source, 'receivings'))
		{
			$this->receiving_lib->set_stock_source($stock_source);
			$this->receiving_lib->set_stock_destination($stock_destination);
		}
		$this->receiving_lib->clear_all();
		$this->_reload();
	}
	
	public function set_comment()
	{
		$this->receiving_lib->set_comment($this->input->post('comment'));
	}

	public function set_date_receiving()
	{
		$this->receiving_lib->set_date_receiving($this->input->post('date_receiving'));
	}

	public function set_print_after_sale()
	{
		$this->receiving_lib->set_print_after_sale($this->input->post('recv_print_after_sale'));
	}
	
	public function set_reference()
	{
		$this->receiving_lib->set_reference($this->input->post('recv_reference'));
	}
	
	public function add()
	{
		$data = array();

		$mode = $this->receiving_lib->get_mode();
		$item_id = $this->input->post('item');
		//echo $item_id_or_number_or_item_kit_or_receipt;
		$quantity = $mode == 'receive' ? 1 : -1;
		$item_location = 1;
		if($mode == 'return')
		{
			$this->receiving_lib->add_item_packet($item_id);
		}else{
			$this->receiving_lib->add_item($item_id, $quantity);
		}
		$data['selectinput'] = 'idcount'.$item_id.'1';
		$this->_reload($data);
	}

	public function edit_item($item_id)
	{
		$data = array();
		$mode = $this->receiving_lib->get_mode();
		$quantity= parse_decimals($this->input->post('quantity'));
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|callback_numeric');
		if($mode == 'return')
		{
			if($this->form_validation->run() != FALSE)
			{
				$this->receiving_lib->edit_item_kit($item_id, $quantity);
			}
			else
			{
				$data['error']=$this->lang->line('receivings_error_editing_item');
			}
		}else{
			//echo "<pre>"; print_r($this->input->post()); echo "</pre>";
			$this->form_validation->set_rules('price', 'lang:items_price', 'required|callback_numeric');
			$description = $this->input->post('description');
			$serialnumber = $this->input->post('serialnumber');
			$price = parse_decimals($this->input->post('price'));
			$quantitykg = parse_decimals($this->input->post('quantitykg'));
			$quantitytang = parse_decimals($this->input->post('quantitytang'));
			$quantitytangkg = parse_decimals($this->input->post('quantitytangkg'));
			$discount = parse_decimals($this->input->post('discount'));
			$item_location = $this->input->post('location');
			if($this->form_validation->run() != FALSE)
			{
				$this->receiving_lib->edit_item($item_id, $description, $serialnumber,$quantity, $quantitykg, $quantitytang,$quantitytangkg, $price);
			}
			else
			{
				$data['error']=$this->lang->line('receivings_error_editing_item');
			}
		}
		//echo "<pre>"; print_r($data); echo "</pre>";
		$this->_reload($data);
	}
	
	public function edit_receving($receiving_id)
	{
		// khoi tao session luu receiving_id
		$this->receiving_lib->clear_all();
		$this->receiving_lib->set_receiving_id($receiving_id);
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();
		//echo "<pre>"; print_r($receiving_info); echo "</pre>";
		$data['amount_tendered'] = to_currency_no_money($receiving_info['pay_money']);
		// lay don hang
		// ngay nhap don hang
		$date_receiving = date("d/m/Y", strtotime($receiving_info['receiving_time']));
		$this->receiving_lib->set_date_receiving($date_receiving);
		//sang bao
		$arrSangbao['sangbao_tieude'] = $receiving_info['cover_label'];
		$arrSangbao['sangbao_thanhtien'] = to_currency_no_money($receiving_info['cover_money']);
		$this->receiving_lib->set_sangbao($arrSangbao);
		// set mode
		if($receiving_info['type'] == 1){
			$this->receiving_lib->set_mode('receive');
			// san pham
			$arrcarts = $this->Receiving->get_info_itemp_receiving($receiving_id);
			foreach($arrcarts as $arrcart){
				$this->receiving_lib->add_item($arrcart['item_id'], (int)$arrcart['quantity'], $arrcart['input_prices']);
			}
		}else if($receiving_info['type'] == 2){
			$this->receiving_lib->set_mode('return');
			$arrcarts = $this->Receiving->get_info_itemp_baobi($receiving_id);
			foreach($arrcarts as $arrcart){
				$this->receiving_lib->add_item_packet($arrcart['item_id'], (int)$arrcart['quantity'], $arrcart['input_prices']);
			}
		}
		
		// nha cung cap
		$supplier_id = $receiving_info['supplier_id'];
		$this->receiving_lib->set_supplier($supplier_id);
		
		// ghi chu
		$this->receiving_lib->set_comment($receiving_info['comment']);
		$this->_reload($data);
	}

	public function delete_item($item_number)
	{
		$this->receiving_lib->delete_item($item_number);

		$this->_reload();
	}
	
	public function delete($receiving_id = -1, $update_inventory = TRUE) 
	{
		$mode = $this->receiving_lib->get_mode();
		$receiving_ids = $receiving_id == -1 ? $this->input->post('ids') : array($receiving_id);
		if($mode=='return'){
			if($this->Receiving->delete_packet_list($receiving_ids, $update_inventory))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('receivings_successfully_deleted'), 'ids' => $receiving_ids));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('receivings_cannot_be_deleted')));
			}
		}else{
			if($this->Receiving->delete_list($receiving_ids, $update_inventory))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('receivings_successfully_deleted'), 'ids' => $receiving_ids));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('receivings_cannot_be_deleted')));
			}
		}
	}

	public function remove_supplier()
	{
		$this->receiving_lib->clear_reference();
		$this->receiving_lib->remove_supplier();

		$this->_reload();
	}

	public function update_sangbao(){
		$chiphisangbao =  str_replace(",","",$this->input->post('sangbao_thanhtien'));
		$chiphisangbao =  str_replace(".","",$chiphisangbao);
		$arrSangbao['sangbao_tieude'] = $this->input->post('sangbao_tieude');
		$arrSangbao['sangbao_thanhtien'] = $this->input->post('sangbao_thanhtien');
		$this->receiving_lib->set_sangbao($arrSangbao);

		$this->_reload();
	}

	public function complete()
	{
		$data = array();
		$sotienthanhtoan =  str_replace(",","",$this->input->post('amount_tendered'));
		$sotienthanhtoan =  str_replace(".","",$sotienthanhtoan);
		$data['sotienthanhtoan'] = $sotienthanhtoan;
		$data['mode'] = $this->receiving_lib->get_mode();
		$data['cart'] = $this->receiving_lib->get_cart();
		$data['giatridonhang'] = $this->input->post('gia_tri_don_hang');
		$data['transaction_time'] = date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'));
		$data['comment'] = $this->receiving_lib->get_comment();
		// cap nhat ngay ban hang
		$datetime = $this->receiving_lib->get_date_receiving();
		$data['date_receiving'] = DateTime::createFromFormat('d/m/Y', $datetime)->format('Y-m-d H:i:s');
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$employee_info = $this->Employee->get_info($employee_id);
		$data['employee'] = $employee_info->full_name;
		$data['reference'] = '';
		$data['payment_type'] = 'Tiền mặt';
		$data['stock_location'] = 1;
		$supplier_info = '';
		$supplier_id = $this->receiving_lib->get_supplier();
		if($supplier_id != -1)
		{
			$supplier_info = $this->Supplier->get_info($supplier_id);
			$data['supplier'] = $supplier_info->full_name;
			$data['full_name'] = $supplier_info->full_name;
			$data['supplier_email'] = $supplier_info->email;
			$data['supplier_address'] = $supplier_info->address;
		}
		// neu da ton tai thi xoa cai cu them cai moi
		$receiving_id = $this->receiving_lib->get_receiving_id();
		if($receiving_id > 0){
			$this->delete($receiving_id);
			$this->receiving_lib->empty_receiving_id();
		}
		if($data['mode'] == 'receive'){
			$data['sang_bao'] = $this->receiving_lib->get_sangbao();
			//echo "<pre>"; print_r($data['sang_bao']); echo "</pre>"; exit;
			$data['receiving_id'] = $this->Receiving->save($data, $supplier_id, $employee_id);
		}else{
			// cap nhat gia tri cua bao bi
			$data['receiving_id'] = $this->Receiving->savePaket($data, $supplier_id, $employee_id);
		}
		$data = $this->xss_clean($data);
		if($data['receiving_id'] == -1)
		{
			$data['error_message'] = $this->lang->line('receivings_transaction_failed');
		}
		else
		{
			$data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['receiving_id']);				
		}
		$this->receiving_lib->clear_all();
		$data = $this->receipt($data['receiving_id']);
	}
	
	public function receipt($receiving_id)
	{
		$mode = $this->receiving_lib->get_mode();
		$data = $this->_load_receipt_data($receiving_id);
		if($mode == 'receive'){		
			$data['mode'] = $mode;
			$this->load->view("receivings/receipt", $data);
		}else{
			$data['mode'] = $mode;
			//echo "<pre>"; print_r($data); echo "</pre>"; exit;
			$this->load->view("receivings/receipt_baobi", $data);
		}
		$this->receiving_lib->clear_all();
	}
	private function _load_receipt_data($receiving_id)
	{

		$data = array();
		$this->receiving_lib->clear_all();
		// lay thong tin dot ban hang
		$data['sale_info'] = $this->Receiving->get_info($receiving_id)->row_array();
		$mode = $this->receiving_lib->get_mode();
		if($mode == 'receive'){	
			$data['cart'] = $this->Receiving->get_info_itemp_receiving($receiving_id);	
		}else{
			$data['cart'] = $this->Receiving->get_info_itemp_baobi($receiving_id);
		}
		// lay thong tin don hang
		//echo "<pre>"; print_r($data['sale_info']); echo "</pre>";
		$data['customer_info'] = $this->Employee->get_info_by_sales($data['sale_info']['employee_id']);
		$data = $this->xss_clean($data);
		$this->load->helper('listype');
		$data['arrListtype'] = get_listtype_two();
		$this->load->helper('namemoney');

		$numbermoney = (int)$data['sale_info']['pay_money'];
		$data['name_money'] = getstringmonney($numbermoney);
		$data['print_after_sale'] = $this->receiving_lib->is_print_after_sale();
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		//echo "<pre>"; print_r($data); echo "</pre>";     
		return $this->xss_clean($data);
	}
	private function _reload($data = array())
	{
		if(!isset($data['amount_tendered'])){
			$data['amount_tendered'] = 0;
		}
		$data['cart'] = $this->receiving_lib->get_cart();
		$data['modes'] = array('receive' => $this->lang->line('receivings_receiving'), 'return' => $this->lang->line('receivings_return'));
		$data['mode'] = $this->receiving_lib->get_mode();
		$data['stock_locations'] = $this->Stock_location->get_allowed_locations('receivings');
		//$data['total'] = $this->receiving_lib->get_total();
		$data['items_module_allowed'] = $this->Employee->has_grant('items', $this->Employee->get_logged_in_employee_info()->person_id);
		$data['comment'] = $this->receiving_lib->get_comment();
		$data['reference'] = $this->receiving_lib->get_reference();
		// ngay nhap hang
		$data['date_receiving'] = $this->receiving_lib->get_date_receiving();
		//$data['payment_options'] = $this->Receiving->get_payment_options();
		//echo "<pre>"; print_r($data['cart']); echo "</pre>";
		$supplier_id = $this->receiving_lib->get_supplier();
		$supplier_info = '';
		if($supplier_id != -1)
		{
			$supplier_info = $this->Supplier->get_info($supplier_id);
			$data['supplier'] = $supplier_info->full_name;
			$data['full_name'] = $supplier_info->full_name;
			$data['supplier_email'] = $supplier_info->email;
			$data['supplier_address'] = $supplier_info->address;
			if(!empty($supplier_info->zip) or !empty($supplier_info->city))
			{
				$data['supplier_location'] = $supplier_info->zip . ' ' . $supplier_info->city;				
			}
			else
			{
				$data['supplier_location'] = '';
			}
		}
		if(!isset($data['selectinput'])){
			$data['selectinput'] = 'item';
		}
		$data['print_after_sale'] = $this->receiving_lib->is_print_after_sale();
		$data['nexline'] = sizeof($data['cart']);
		//echo $data['nexline'];
		$data = $this->xss_clean($data);
		//echo "<pre>"; print_r($data); echo "</pre>";
		if($data['mode'] == 'receive'){
			// lay thong tin sang bao
			$data['sang_bao'] = $this->receiving_lib->get_sangbao();
			//echo "<pre>"; print_r($data['sang_bao']); echo "</pre>";
			$this->load->view("receivings/receiving", $data);
		}else{
			$this->load->view("receivings/receiving_baobi", $data);
		}
		
	}
	

	public function cancel_receiving()
	{
		$this->receiving_lib->clear_all();

		$this->_reload();
	}
}
?>
