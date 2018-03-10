<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Config extends Secure_Controller 
{
	public function __construct()
	{

		parent::__construct('config');
		$this->load->library('item_lib');
		//$this->load->library('barcode_lib');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_so_thu_chi_manage_table_headers());
		$this->load->helper('listype');
		$data['arrKhachhang'] = loai_khach_hang();
		$data['arrThuchi'] = loai_thu_chi();
		$typethuchi = $this->item_lib->get_type_thuchi();
		if(isset($typethuchi) && $typethuchi !== ''){
			$data['thuchi'] = $this->item_lib->get_type_thuchi();
		}else{
			$this->item_lib->set_type_thuchi('sothu');
			$data['thuchi'] = $this->item_lib->get_type_thuchi();
		}
		if($data['thuchi'] == 'sothu'){
			$data['checkedthu'] = 'checked="checked"';
			$data['checkedchi'] = '';
			$data['table_headers_text'] = 'Quản lý sổ thu';
		}else{
			$data['checkedthu'] = '';
			$data['checkedchi'] = 'checked="checked"';
			$data['table_headers_text'] = 'Quản lý sổ chi';
		}
		$this->load->view('configs/manage', $data);
	}
	public function change_mode()
	{
		$data['table_headers'] = $this->xss_clean(get_so_thu_chi_manage_table_headers());
		$this->load->helper('listype');
		$data['arrThuchi'] = loai_thu_chi();
		$typethuchi = $this->input->post('sothuchi');
		$this->item_lib->set_type_thuchi($typethuchi);
		$data['thuchi'] = $this->item_lib->get_type_thuchi();
		if($data['thuchi'] == 'sothu'){
			$data['checkedthu'] = 'checked="checked"';
			$data['checkedchi'] = '';
			$data['table_headers_text'] = 'Quản lý sổ thu';
		}else{
			$data['checkedthu'] = '';
			$data['checkedchi'] = 'checked="checked"';
			$data['table_headers_text'] = 'Quản lý sổ chi';
		}
		$this->load->view('configs/manage', $data);
	}
		/*
	Returns Item kits table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = 'DESC';
		$persion_type  = $this->input->get('persion_type');
		$sothuchi  = $this->input->get('sothuchi');
		$filters['start_date'] = $this->input->get('start_date');
		$filters['end_date'] = $this->input->get('end_date');
		$filters['search'] = $this->input->get('search');
		if($sothuchi=='sothu'){
			$item_kits = $this->Appconfig->search_sothu($search,$filters,$persion_type, $limit, $offset, $sort, $order)->result_array();
			$tongcuoi = $this->Appconfig->total_sothu($search,$filters,$persion_type);
			$total_rows = $this->Appconfig->get_found_rows_sothu($search,$filters,$persion_type);
		}else{
			$item_kits = $this->Appconfig->search_sochi($search,$filters,$persion_type, $limit, $offset, $sort, $order)->result_array();
			$tongcuoi = $this->Appconfig->total_sochi($search,$filters,$persion_type);
			$total_rows = $this->Appconfig->get_found_rows_sochi($search, $filters,$persion_type);
		}
		
		//var_dump($item_kits); exit;
		$data_rows = array();
		$this->load->helper('listype');
		$arrThuchi = loai_thu_chi();
		$j=0;
		$totalmoney = 0;
		foreach($item_kits as $item_kit)
		{
			$totalmoney = $totalmoney + $item_kit['money'];
			// calculate the total cost and retail price of the Kit so it can be printed out in the manage table
			//$item_kit = $this->_add_totals_to_item_kit($item_kit);
			if($sothuchi=='sothu'){
				if($item_kit['type'] == 5){
					// lay nha cung cap
					$users = $this->Supplier->get_info($item_kit['supplier_id']);
					$item_kit['full_name'] = $users->agency_name."-".$users->full_name;
				}else if($item_kit['type'] == 6){
					$item_kit['full_name'] = 'Khác';
				}else{
					$users = $this->Customer->get_info($item_kit['customer_id']);
					$item_kit['full_name'] = $users->full_name;
				}
				
			$data_rows[] = $this->xss_clean(get_so_thu_data_row($item_kit,$arrThuchi, $this));
			}else{
				if($item_kit['type'] == 5){
					$users = $this->Customer->get_info($item_kit['customer_id']);
					$item_kit['full_name'] = $users->full_name;
				}else if($item_kit['type'] == 6){
					$item_kit['full_name'] = 'Khác';
				}else{
					// lay nha cung cap
					$users = $this->Supplier->get_info($item_kit['supplier_id']);
					$item_kit['full_name'] = $users->agency_name."-".$users->full_name;
				}
				$data_rows[] = $this->xss_clean(get_so_chi_data_row($item_kit,$arrThuchi, $this));
			}
			$j++;
		}

		$data_rows = $this->xss_clean($data_rows);
		$data_rows[$j]['date_time'] = '';
		$data_rows[$j]['customer_id'] = '<b>Tổng</b>';
		$data_rows[$j]['money'] = '<b>'.to_currency($totalmoney).'</b>';
		$data_rows[$j]['type'] = '';
		$data_rows[$j]['payment_type'] = '';
		$data_rows[$j]['comment'] = '';
		$data_rows[$j+1]['date_time'] = '';
		$data_rows[$j+1]['customer_id'] = '<b>Tổng cuối</b>';
		$data_rows[$j+1]['money'] = '<b>'.to_currency($tongcuoi).'</b>';
		$data_rows[$j+1]['type'] = '';
		$data_rows[$j+1]['payment_type'] = '';
		$data_rows[$j+1]['comment'] = '';
		// Tong cuoi

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	public function view($item_kit_id = -1)
	{
		$typethuchi = $this->item_lib->get_type_thuchi();
		$info = $this->Appconfig->get_info($item_kit_id,$typethuchi);
		$data['payment_options'] = $this->xss_clean($this->Sale->get_payment_options(FALSE));
		$this->load->helper('listype');
		$data['arrThuchi'] = loai_thu_chi();
		$data['arrKhachhang'] = loai_khach_hang();
		if(sizeof($info) == 0){
			$info[0]['thu_chi_id'] = -1;
			$info[0]['money'] = '';
			$info[0]['comment'] = '';
			$info[0]['customer_id'] = '';
			$info[0]['object'] = 'khach_hang';
			$info[0]['supplier_id'] = '';
			$info[0]['full_name'] = '';
			$info[0]['sale_time'] = '';
			$info[0]['payment_type'] = '';
		}else{
			if($info[0]['type'] == 5){
			// lay nha cung cap
				$users = $this->Supplier->get_info($info[0]['supplier_id']);
				$info[0]['full_name'] = $users->agency_name."-".$users->full_name;
				$info[0]['object'] = 'nha_cung_cap';
			}else if($info[0]['type'] == 6){
				$item_kit['full_name'] = 'Khác';
				$info[0]['object'] = 'khac';
				$info[0]['full_name'] = '';
			}else{
				$users = $this->Customer->get_info($info[0]['customer_id']);
				$info[0]['full_name'] = $users->full_name;
				$info[0]['object'] = 'khach_hang';
			}
		}
		$data['thu_chi']  = $info;
		$data['objectchon'] = 'khach_hang';
		$this->load->view("configs/form", $data);
	}
	public function viewsochi($item_kit_id = -1)
	{
		$typethuchi = $this->item_lib->get_type_thuchi();
		$info = $this->Appconfig->get_info($item_kit_id,$typethuchi);
		$data['payment_options'] = $this->xss_clean($this->Sale->get_payment_options(FALSE));
		$this->load->helper('listype');
		$data['arrThuchi'] = loai_thu_chi();
		$data['arrKhachhang'] = loai_khach_hang();
		if(sizeof($info) == 0){
			$info[0]['thu_chi_id'] = -1;
			$info[0]['money'] = '';
			$info[0]['comment'] = '';
			$info[0]['customer_id'] = '';
			$info[0]['object'] = 'nha_cung_cap';
			$info[0]['full_name'] = '';
			$info[0]['receiving_time'] = '';
			$info[0]['payment_type'] = '';
		}else{
			if($info[0]['type'] == 5){
			// lay nha cung cap
				$users = $this->Customer->get_info($info[0]['customer_id']);
				$info[0]['full_name'] = $users->full_name;
				$info[0]['object'] = 'khach_hang';
			}else if($info[0]['type'] == 6){
				$item_kit['full_name'] = 'Khác';
				$info[0]['object'] = 'khac';
				$info[0]['full_name'] = '';
			}else{
				$users = $this->Supplier->get_info($info[0]['supplier_id']);
				$info[0]['full_name'] = $users->agency_name."-".$users->full_name;
				$info[0]['object'] = 'nha_cung_cap';	
			}
		}
		$data['thu_chi']  = $info;
		//echo "<pre>"; print_r($data); echo "</pre>";
		$this->load->view("configs/sochi", $data);
	}
	public function save($id = -1)
	{
		$object = $this->input->post('object');
		$this->capnhatsothu($id,$object);
		
	}
	public function savesochi($id = -1)
	{
		$object = $this->input->post('object');
		$this->capnhatsochi($id,$object);
	}
	public function capnhatsothu($sale_id = -1,$object){
		if($object == 'khach_hang'){
			$customer_id = $this->input->post('selectcustomer');
			$supplier_id = 0;
			$type =4;
		}else if($object == 'nha_cung_cap'){
			$supplier_id = $this->input->post('selectsupplier');
			$customer_id =0;
			$type =5;
		}else{
			$customer_id =0;
			$supplier_id = 0;
			$type =6;
		}
		
		$datetime = $this->input->post('start_date');
		$customer = $this->input->post('customer');
		$money =  str_replace(",","",$this->input->post('money'));
		$money =  str_replace(".","",$money);
		$description = $this->input->post('description');
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		// them moi mot luot so thu chi
		$sales_data = array(
			'sale_time'		 => DateTime::createFromFormat('d/m/Y', $datetime)->format('Y-m-d H:i:s'),
			'customer_id'	 => $customer_id,
			'supplier_id'	 => $supplier_id,
			'employee_id'	 => $employee_id,
			'comment'		 => $description,
			'payment_type' 	 => $this->input->post('payment_type'),
			'order_money'	 => 0,
			'pay_money' 	 => $money,
			'car_money'      => 0,
			'promotion' 	 => 0,
			'customer_debt' => 0,
			'type' => $type,
			
		);
		//echo "<pre>"; print_r($sales_data); echo "</pre>"; exit;
		if($this->Appconfig->save_sothu($sales_data, $sale_id))
		{
			echo json_encode(array('success' => TRUE, 'message' => 'Cập nhật thành công'));
		}else{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('sales_unsuccessfully_updated')));
		}
	}
	public function capnhatsochi($receiving_id = -1,$object){
		if($object == 'nha_cung_cap'){
			$supplier_id = $this->input->post('selectsupplier');
			$customer_id = 0;
			$type =4;
		}else if($object == 'khach_hang'){
			$customer_id = $this->input->post('selectcustomer');
			$supplier_id =0;
			$type =5;
		}else{
			$customer_id =0;
			$supplier_id = 0;
			$type =6;
		}
		$datetime = $this->input->post('start_date');
		$customer = $this->input->post('customer');
		$money =  str_replace(",","",$this->input->post('money'));
		$money =  str_replace(".","",$money);
		$description = $this->input->post('description');
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;	
		// them moi mot luot so thu chi
		$receivings_data = array(
			'receiving_time' => DateTime::createFromFormat('d/m/Y', $datetime)->format('Y-m-d H:i:s'),
			'supplier_id' => $supplier_id,
			'customer_id'	 => $customer_id,
			'employee_id' => $employee_id,
			'payment_type' 	 => $this->input->post('payment_type'),
			'comment' => $description,
			'order_money' => 0,
			'pay_money' => $money,
			'type' => $type,
		);
		//echo "<pre>"; print_r($receivings_data); echo "</pre>"; exit;
		if($this->Appconfig->save_sochi($receivings_data, $receiving_id))
		{
			echo json_encode(array('success' => TRUE, 'message' => 'Cập nhật thành công'));
		}else{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('sales_unsuccessfully_updated')));
		}
	}
	public function delete($sale_id = -1) 
	{
		$sale_ids = $sale_id == -1 ? $this->input->post('ids') : array($sale_id);
		if(sizeof($sale_ids) == 1){
			// kiem tra hinh thuc la thu hay chi
			$type = $this->item_lib->get_type_thuchi();
			if($type == 'sothu'){
				// xoa dot cap nhat
				$this->db->delete('sales_items', array('sale_id' => $sale_ids[0]));
				$this->db->delete('sales', array('sale_id' => $sale_ids[0]));
			}else{
				$this->db->delete('receivings_items', array('receiving_id' => $sale_ids[0]));
				$this->db->delete('receivings', array('receiving_id' => $sale_ids[0]));
			}
			echo json_encode(array('success' => TRUE, 'message' => 'Xóa thành công'));
		}else{
			echo json_encode(array('success' => FALSE, 'message' => "Chỉ được chọn 1 để xóa"));
		}
	}
}
?>
