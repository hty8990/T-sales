<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Sales extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('sales');

		$this->load->library('sale_lib');
				$this->CI =& get_instance();
	}

	public function index()
	{
		$this->_reload();
	}
	
	public function manage()
	{
		$person_id = $this->session->userdata('person_id');
		//echo $person_id; exit;
		$mode = $this->sale_lib->get_mode();
		if(!$this->Employee->has_grant('reports', $person_id))
		{
			redirect('no_access/sales/reports_sales');
		}
		else
		{
			if($mode == 'sale'){
				$data['table_headers_text'] = 'Lịch sử bán hàng';
				$data['table_headers'] = get_sales_manage_table_headers();
				$this->load->view('sales/manage', $data);
			}elseif($mode == 'return'){
				$data['table_headers_text'] = 'Lịch sử trả hàng';
				$data['table_headers'] = get_sales_return_manage_table_headers();
				$this->load->view('sales/manage', $data);
			}elseif($mode == 'taiche'){
				$data['table_headers_text'] = 'Lịch sử Tái chế';
				$data['table_headers'] = get_sales_taiche_manage_table_headers();
				$this->load->view('sales/manage', $data);
			}elseif($mode == 'huy'){
				$data['table_headers_text'] = 'Lịch sử Hàng hủy';
				$data['table_headers'] = get_sales_huy_manage_table_headers();
				$this->load->view('sales/manage', $data);
			}elseif($mode == 'tra_ncc'){
				$data['table_headers_text'] = 'Lịch sử Trả nhà cung cấp';
				$data['table_headers'] = get_sales_tra_ncc_manage_table_headers();
				$this->load->view('sales/manage', $data);
			}
		}
	}
	
	public function get_row($row_id)
	{
		$this->Sale->create_temp_table();

		$sale_info = $this->Sale->get_info($row_id)->row();
		$data_row = $this->xss_clean(get_sale_data_row($sale_info, $this));

		echo json_encode($data_row);
	}

	public function search()
	{
		$mode = $this->sale_lib->get_mode();
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');


		$filters = array('sale_type' => 'all',
						'location_id' => 'all',
						'start_date' => $this->input->get('start_date'),
						'end_date' => $this->input->get('end_date'));

		// check if any filter is set in the multiselect dropdown
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);
		$filters = array_merge($filters, $filledup);
		$mode = $this->sale_lib->get_mode();
		$check_recevied = false;
		if($mode == 'sale'){
			$type=1;
		}elseif($mode == 'return'){
			$type=2;
		}elseif($mode == 'taiche'){
			$type=3;
			$check_recevied =true;
		}elseif($mode == 'huy'){
			$type=3;
		}elseif($mode == 'tra_ncc'){
			$type=7;
			$check_recevied =true;
		}
		if($check_recevied){
			$sales = $this->Receiving->search_item($type,$search, $filters, $limit, $offset, $sort, $order);
			$total_rows = $this->Receiving->get_found_rows($type,$search, $filters, $limit, $offset, $sort, $order);
			$data_rows = array();
			foreach($sales->result() as $sale)
			{
				if($type == 7){
					$data_rows[] = $this->xss_clean(get_recive_tra_ncc_data_row($sale, $this));
				}else{
					$data_rows[] = $this->xss_clean(get_recive_taiche_data_row($sale, $this));
				}
			}

			if($total_rows > 0)
			{
				$data_rows[] = $this->xss_clean(get_sale_data_last_row($sales, $this));
			}

			echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
		}else{
			$sales = $this->Sale->search($type,$search, $filters, $limit, $offset, $sort, $order);
			$total_rows = $this->Sale->get_found_rows($type,$search, $filters);
			//$payments = $this->Sale->get_payments_summary($search, $filters);
			//$payment_summary = $this->xss_clean(get_sales_manage_payments_summary($payments, $sales, $this));
			$payment_summary = '';
			$data_rows = array();
			//echo "<pre>"; print_r($sales->result()); echo "</pre>"; exit;
			if($type == 1){
				$arrCheckOtherPromotion = array();
				$dataResult = $sales->result();
				$j=0;
				if(isset($search) && $search !== ""){
					for($i=count($dataResult)-1;$i>=0;$i--){
						// kiem tra neu i + 1 <> i thi boi mau
						if($i >= 1){
							if($dataResult[$i]->promotion !== $dataResult[$i-1]->promotion){
								$dataResult[$i-1]->height_color = true;
							}
						}
					}
					foreach($dataResult as $sale)
					{
						$data_rows[] = $this->xss_clean(get_sale_data_row($sale, $this,$type));
					}	
				}else{
					foreach($sales->result() as $sale)
					{
						$data_rows[] = $this->xss_clean(get_sale_data_row($sale, $this,$type));
					}
				}
			}else{
				foreach($sales->result() as $sale)
				{
					$data_rows[] = $this->xss_clean(get_sale_data_row($sale, $this,$type));
				}
			}
			

			if($total_rows > 0)
			{
				$data_rows[] = $this->xss_clean(get_sale_data_last_row($sales, $this,$type));
			}
			echo json_encode(array('total' => $total_rows, 'rows' => $data_rows, 'payment_summary' => $payment_summary));	
		}
	}

	public function item_search()
	{
		$suggestions = array();
		$receipt = $search = $this->input->get('term') != '' ? $this->input->get('term') : NULL;

		$suggestions = array_merge($suggestions, $this->Item->get_search_suggestions($search, array('search_custom' => FALSE, 'is_deleted' => TRUE), TRUE));
		$suggestions = $this->xss_clean($suggestions);

		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$search = $this->input->post('term') != '' ? $this->input->post('term') : NULL;
		
		$suggestions = $this->xss_clean($this->Sale->get_search_suggestions($search));
		
		echo json_encode($suggestions);
	}

	public function select_customer()
	{
		$customer_id = $this->input->post('customer');
		if($this->Customer->exists($customer_id))
		{
			$this->sale_lib->set_customer($customer_id);
			//lay thong tin chuong trinh khuyen mai
		}
		$this->_reload();
	}

	public function change_mode()
	{
		$stock_location = $this->input->post('stock_location');
		if (!$stock_location || $stock_location == $this->sale_lib->get_sale_location())
		{
			$mode = $this->input->post('mode');
			$this->sale_lib->set_mode($mode);
		} 
		elseif($this->Stock_location->is_allowed_location($stock_location, 'sales'))
		{
			$this->sale_lib->set_sale_location($stock_location);
		}
		$this->sale_lib->empty_cart();
		$this->sale_lib->clear_all();
		$this->_reload();
	}
	
	public function set_comment() 
	{
		$this->sale_lib->set_comment($this->input->post('comment'));
	}
	public function set_date_sale() 
	{
		$this->sale_lib->set_date_sale($this->input->post('date_sale'));
	}
	public function set_invoice_number()
	{
		$this->sale_lib->set_invoice_number($this->input->post('sales_invoice_number'));
	}
	
	public function set_invoice_number_enabled()
	{
		$this->sale_lib->set_invoice_number_enabled($this->input->post('sales_invoice_number_enabled'));
	}
	
	public function set_print_after_sale()
	{
		$this->sale_lib->set_print_after_sale($this->input->post('sales_print_after_sale'));
	}
	
	public function set_email_receipt()
	{
		$this->sale_lib->set_email_receipt($this->input->post('email_receipt'));
	}

	// Multiple Payments
	public function add_payment()
	{
		$data = array();
		$this->form_validation->set_rules('amount_tendered', 'lang:sales_amount_tendered', 'trim|required|callback_numeric');

		$payment_type = $this->input->post('payment_type');

		if($this->form_validation->run() == FALSE)
		{
			if($payment_type == $this->lang->line('sales_giftcard'))
			{
				$data['error'] = $this->lang->line('sales_must_enter_numeric_giftcard');
			}
			else
			{
				$data['error'] = $this->lang->line('sales_must_enter_numeric');
			}
		}
		else
		{
			$amount_tendered = $this->input->post('amount_tendered');
			$data['check_success'] = 1;
			$this->sale_lib->add_payment($payment_type, $amount_tendered);
		}

		$this->_reload($data);
	}

	// Multiple Payments
	public function delete_payment($payment_id)
	{
		$this->sale_lib->unset_payment();

		$this->_reload();
	}

	public function add()
	{
		$data = array();
		$mode = $this->sale_lib->get_mode();
		$item_id_or_number_or_item_kit_or_receipt = $this->input->post('item');
		$quantity = 1;		
		$item_location = $this->sale_lib->get_sale_location();
		$discount = 0;
		$customer_id = $this->sale_lib->get_customer();
		if(!$this->sale_lib->add_item($item_id_or_number_or_item_kit_or_receipt, $quantity))
		{
			$data['error'] = $this->lang->line('sales_unable_to_add_item');
		}
		$data['selectinput'] = 'idcount'.$item_id_or_number_or_item_kit_or_receipt.'1';
		$data['warning'] = $this->sale_lib->out_of_stock($item_id_or_number_or_item_kit_or_receipt, $item_location);

		$this->_reload($data);
	}

	public function edit_item($item_id)
	{
		$data = array();
		// kiem tra neu la tra lai thi cho phep sua gia
		$mode = $this->sale_lib->get_mode();
		//echo "<pre>"; print_r($this->input->post()); echo "</pre>";
		$this->form_validation->set_rules('price', 'lang:items_price', 'required|callback_numeric');
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|callback_numeric');
		$description = $this->input->post('description');
		$serialnumber = $this->input->post('serialnumber');
		$price = parse_decimals($this->input->post('price'));
		$quantity = parse_decimals($this->input->post('quantity'));
		$quantitytang = parse_decimals($this->input->post('quantitytang'));
		$hanggui = parse_decimals($this->input->post('hanggui'));
		$hangtra = parse_decimals($this->input->post('hangtra'));
		$item_location = $this->input->post('location'); 
		if($this->form_validation->run() != FALSE)
		{
			//echo "a".$price;
			$this->sale_lib->edit_item($item_id, $description, $serialnumber, $quantity, $quantitytang, $price,$hanggui,$hangtra);
		}
		else
		{
			$data['error'] = $this->lang->line('sales_error_editing_item');
		}
		$data['selectinput'] = 'item';
		//echo $data['selecttext'];
		$data['warning'] = $this->sale_lib->out_of_stock($this->sale_lib->get_item_id($item_id), $item_location);

		$this->_reload($data);
	}
	public function update_tranfer()
	{
		$data = array();
		$vanchuyen_dg = $this->input->post('vanchuyen_dg');
		$vanchuyen_dg =  str_replace(".","",$vanchuyen_dg);
		$vanchuyen_dg =  str_replace(",","",$vanchuyen_dg);
		$this->sale_lib->set_tranfer($vanchuyen_dg);
		$data['selectinput'] = 'amount_tendered';
		$this->_reload($data);
	}
	public function update_thuongsanluong(){
		$sanluong_dongia = $this->input->post('sanluong_dongia');
		$sanluong_dongia =  str_replace(".","",$sanluong_dongia);
		$sanluong_dongia =  str_replace(",","",$sanluong_dongia);
		$arrSanluong['sanluong_tieude'] = $this->input->post('sanluong_tieude');
		$arrSanluong['sanluong_soluong'] = $this->input->post('sanluong_soluong');
		$arrSanluong['sanluong_dongia'] = $sanluong_dongia;
		$this->sale_lib->set_thuongsanluong($arrSanluong);
		$this->_reload();

	}
	public function update_taiche(){
		$taiche_dongia = $this->input->post('taiche_dongia');
		$taiche_dongia =  str_replace(".","",$taiche_dongia);
		$taiche_dongia =  str_replace(",","",$taiche_dongia);
		$arrTaiche['taiche_soluong'] = $this->input->post('taiche_soluong');
		$arrTaiche['taiche_dongia'] = $taiche_dongia;
		$this->sale_lib->set_taiche($arrTaiche);
		$this->_reload();
	}
	public function set_uncheck_sanluong(){
		$this->sale_lib->clear_thuongsanluong();
		$this->_reload();
	}
	public function set_checked_return()
	{
		//$this->sale_lib->clear_checked_return();
		$arrays = $this->sale_lib->get_checked_return();
		$totalarray = sizeof($arrays);
		$arraytemp = array();
		$i=0;
		if($totalarray > 0){
			$checkupdate = true;
			foreach($arrays as $key => $value){
				if($value['item_id'] == $this->input->post('id_item')){
					$checkupdate = false;
				}
			}
			foreach($arrays as $key => $value){
				if($value['item_id'] == $this->input->post('id_item')){
					$arrays[$key]['checkreturn'] = $this->input->post('checked_return');
				}else{
					if($checkupdate){
						$arraytemp[$totalarray]['item_id'] = $this->input->post('id_item');
						$arraytemp[$totalarray]['checkreturn'] = $this->input->post('checked_return');
					}
				}
				$i++;
			}
		}else{
			$arrays[0]['item_id'] = $this->input->post('id_item');
			$arrays[0]['checkreturn'] = $this->input->post('checked_return');
		}
		if(sizeof($arraytemp) > 0){
			$arrays = array_merge($arrays,$arraytemp);
		}
		$this->sale_lib->set_checked_return($arrays);
	}
	public function delete_item($item_number)
	{
		$this->sale_lib->delete_item($item_number);

		$this->_reload();
	}

	public function remove_customer()
	{
		$this->sale_lib->clear_giftcard_remainder();
		$this->sale_lib->clear_invoice_number();
		$this->sale_lib->remove_customer();

		$this->_reload();
	}
	// cap nhat thanh toan
	public function complete()
	{
		$data = array();
		// gia tri don hang
		$data['order_money'] = $this->input->post('giatridonhang');
		//if($data['order_money'] > 0){
			// Thong tin san pham
			$data['cart'] = $this->sale_lib->get_cart();
			//echo "<pre>"; print_r($data['cart']); echo "</pre>"; exit;
			$data['customer_id'] = $this->sale_lib->get_customer();
			// cap nhat ngay ban hang
			$datetime = $this->sale_lib->get_date_sale();
			$data['date_sale'] = DateTime::createFromFormat('d/m/Y', $datetime)->format('Y-m-d H:i:s');
			// chuong trinh khuyen mai
			$data['get_stringpromotion'] = $this->sale_lib->get_stringpromotion();
			// Lay tien cuoc van chuyen
			$data['get_tranfer'] = $this->sale_lib->get_tranfer();
			// Tien no cua khach hang
			$data['customer_debt'] = $this->sale_lib->get_customer_debt();
			 $this->load->helper('listype');
			$data['arrListtype'] = get_listtype_two();
			$nocu = 0;
			$data['receipt_title'] = $this->lang->line('sales_receipt');
			$data['transaction_date'] = date($this->config->item('dateformat'));
			$data['show_stock_locations'] = $this->Stock_location->show_locations('sales');
			$data['comments'] = $this->sale_lib->get_comment();
			$data['payments'] = $this->sale_lib->get_payments();
			$data['employee_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
			// neu la tra lai
			$mode = $this->sale_lib->get_mode();
			//echo "<pre>"; print_r($data); echo "</pre>"; exit;
			$sale_id_old = $this->sale_lib->get_sale_id();
			// neu la sua don hang thi xoa hang cu, them hang moi
			if($sale_id_old > 0){
				// neu la hang tai che thi tru di vao nha cung cap
				if($mode=='taiche'){
					$this->Receiving->delete($sale_id_old,false);
					$this->sale_lib->remove_sale_id();
				}else{
					$this->delete($sale_id_old,false,false);
					$this->sale_lib->remove_sale_id();
				}
			}
			if($mode == 'sale'){

			//**********************************************************************
			// -------------- Ban hang -------------------------------
			//**********************************************************************
				$arrThuongsanluong = $this->sale_lib->get_thuongsanluong();
				if(isset($arrThuongsanluong)){
					$data['thuong_san_luong']['tieude'] = $arrThuongsanluong['sanluong_tieude'];
					$data['thuong_san_luong']['soluong'] = $arrThuongsanluong['sanluong_soluong'];
					$data['thuong_san_luong']['dongia'] = $arrThuongsanluong['sanluong_dongia'];
				}else{
					$data['thuong_san_luong']['tieude'] = '';
					$data['thuong_san_luong']['soluong'] = '';
					$data['thuong_san_luong']['dongia'] = '';
				}
				$sale_id = $this->Sale->save($data);
				if($sale_id){
					$dataRecive = $this->_load_sale_data($sale_id);
					$dataRecive['sale_id'] = $sale_id;
					$this->load->helper('promotion');
					$this->load->view('sales/receipt', $dataRecive);
				}else{
					$data['error'] = 'Lỗi cập nhật';
					$this->_reload($data);
				}
				
			}else{
				if($mode == 'return'){
			//**********************************************************************
			// -------------- Tra lai -------------------------------
			//**********************************************************************
					$sale_id = $this->Sale->saveReturn($data);
					$dataRecive = $this->_load_sale_data($sale_id);
					$dataRecive['mode'] = $mode;
					$this->load->view('sales/receipt', $dataRecive);
				}else if($mode== 'taiche'){
					$employee_id_sale = $this->sale_lib->get_supplier();
					$data['taiche'] = $this->sale_lib->get_taiche();
					$sale_id = $this->Sale->savetaiche($data);
					$this->sale_lib->clear_reference();
					$this->sale_lib->remove_supplier();
					// In mau phieu tai che
					$dataRecive = $this->_load_sale_taiche_data($sale_id);
					//echo "<pre>"; print_r($data); echo "</pre>"; exit;
					$dataRecive['mode'] = $mode;
					$this->load->view('sales/receipt', $dataRecive);
				}else if($mode == 'huy'){
					$sale_id = $this->Sale->savehuy($data);
					$dataRecive = $this->_load_sale_huy_data($sale_id);
					$dataRecive['mode'] = $mode;
					//echo "<pre>"; print_r($data); echo "</pre>"; exit;
					$this->load->view('sales/receipt', $dataRecive);
				}else if($mode== 'tra_ncc'){
					$employee_id_sale = $this->sale_lib->get_supplier();
					$sale_id = $this->Sale->savetra_ncc($data);
					$this->sale_lib->clear_reference();
					$this->sale_lib->remove_supplier();
					// In mau phieu tai che
					$dataRecive = $this->_load_sale_taiche_data($sale_id);
					//echo "<pre>"; print_r($data); echo "</pre>"; exit;
					$dataRecive['mode'] = $mode;
					$this->load->view('sales/receipt', $dataRecive);
				}
				// In mau phieu ban hang
			}
			$this->sale_lib->clear_all();
		//}else{
			//$this->_reload();
		////}
		
	}
	private function _load_customer_data($customer_id, &$data)
	{	
		$customer_info = '';
		if($customer_id != -1)
		{
			$start_date = DateTime::createFromFormat('d/m/Y',$this->sale_lib->get_date_sale())->format('Y-m-d H:i:s');
			$customer_info = $this->Customer->get_info($customer_id);
			if(isset($customer_info->company_name))
			{
				$data['customer'] = $customer_info->company_name;
			}
			else
			{
				$data['customer'] = $customer_info->full_name;
			}
			$data['customer_id'] = $customer_id;
			$data['full_name'] = $customer_info->full_name;
			$data['customer_email'] = $customer_info->email;
			$data['customer_address'] = $customer_info->address;
			$arrjsonKm = array();
			$sale_id_old = $this->sale_lib->get_sale_id();
			if($sale_id_old > 0){
				$checkedit = TRUE;
			}else{
				$checkedit = false;
			}
			if($checkedit){
				$a=1;
			}else{
				$arrjsonKm = array();
			// lay thong tin chi tiet mot dot khuyen mai
			if($customer_info->c_promotion !==''){
				$c_promotion = $customer_info->c_promotion;
				$arrpromotions = explode(',', $c_promotion);
				//echo "<pre>"; print_r($arrpromotions); echo "</pre>"; exit;
				$stringkhuyenmai = '';
				if($arrpromotions){
					$i =0;
					foreach($arrpromotions as $idpromotion){
						$listypename = '';
						$info = $this->Item_kit->get_info_Promotion($idpromotion);
						$datapromotion = $this->Customer->get_promotion_by_midle_date_customer($idpromotion,$start_date);
						foreach($data['arrListtype'] as $key => $values){
							if($key == $info->promotion_type){
								//echo $value['promotion_type']; echo "</br>";
								$listypename = $values;
								break;
							}
						}
						if($datapromotion){
							$arrjsonKm[$i] = array(
								'type' => $info->promotion_type,
								'promotion_type' => $info->type,
								'not_check_all' => $info->check_all,
								'list_name' => $listypename,
								'name' => $info->promotion_name,
								'item_id_promotion' => $info->item_id_promotion,
								'promotion_pecen' => $datapromotion['promotion_pecen'],
								'promotion_kg' => $datapromotion['promotion_kg']
							);
							$i++;
						}
					}
				}	
			}
			$this->sale_lib->set_stringpromotion(json_encode($arrjsonKm));
			// tong tien ben thu
			$customer_debt = $this->Customer->get_debt_customer($customer_id,$start_date);
			// Tong tien no mac dinh ben chi
			$customer_debt_chi = $this->Customer->get_debt_customer_chi($customer_id,$start_date);
			$tongno = $customer_debt - $customer_debt_chi;
			$this->sale_lib->set_customer_debt($tongno);
			}
		}
		return $customer_info;
	}

	private function _load_sale_data($sale_id)
	{

		$data = array();
		$this->sale_lib->clear_all();
		// lay thong tin dot ban hang
		$data['sale_info'] = $this->Sale->get_info($sale_id)->row_array();
		// lay thong tin don hang
		$data['cart'] = $this->Sale->get_info_itemp_sales($sale_id);
		$data['mode'] = $this->sale_lib->get_mode();
		//echo "<pre>";print_r($data['sale_info']);echo "</pre>";
		$data['customer_info'] = $this->Customer->get_info_by_sales($data['sale_info']['customer_id']);
		// lay chuong trinh khuyen mai
		$data = $this->xss_clean($data);
		$this->load->helper('listype');
		$data['arrListtype'] = get_listtype_two();
		$this->load->helper('namemoney');
		$numbermoney = (int)(($data['sale_info']['order_money'] + $data['sale_info']['customer_debt']) - $data['sale_info']['pay_money']);
		$data['name_money'] = getstringmonney($numbermoney);
		return $this->xss_clean($data);
	}
	private function _load_sale_huy_data($sale_id)
	{
		$data = array();
		$this->sale_lib->clear_all();
		// lay thong tin dot ban hang
		$data['sale_info'] = $this->Sale->get_info($sale_id)->row_array();
		// lay thong tin don hang
		$data['cart'] = $this->Sale->get_info_itemp_sales($sale_id);
		$data['mode'] = $this->sale_lib->get_mode();
		//echo "<pre>";print_r($customer_info);echo "</pre>";
		$data['customer_info'] = $this->Customer->get_info_by_sales($data['sale_info']['customer_id']);
		// lay chuong trinh khuyen mai
		$data = $this->xss_clean($data);
		$this->load->helper('listype');
		$data['arrListtype'] = get_listtype_two();
		$this->load->helper('namemoney');
		$numbermoney = (int)$data['sale_info']['pay_money'];
		$data['name_money'] = getstringmonney($numbermoney);
		return $this->xss_clean($data);
	}
	private function _load_sale_taiche_data($receiving_id)
	{
		$data['mode'] = $this->sale_lib->get_mode();
		$data = array();
		$this->sale_lib->clear_all();
		// lay thong tin dot ban hang
		$data['sale_info'] = $this->Receiving->get_info($receiving_id)->row_array();
		$data['cart'] = $this->Receiving->get_info_itemp_receiving($receiving_id);
		// lay thong tin don hang
		$data['customer_info'] = $this->Employee->get_info_by_sales($data['sale_info']['employee_id']);
		$data = $this->xss_clean($data);
		$this->load->helper('listype');
		$data['arrListtype'] = get_listtype_two();
		$this->load->helper('namemoney');

		$numbermoney = (int)$data['sale_info']['pay_money'];
		$data['name_money'] = getstringmonney($numbermoney);
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		//echo "<pre>"; print_r($data); echo "</pre>";     
		return $this->xss_clean($data);
	}
	private function _load_sale_tra_ncc_data($receiving_id)
	{
		$data['mode'] = $this->sale_lib->get_mode();
		$data = array();
		$this->sale_lib->clear_all();
		// lay thong tin dot ban hang
		$data['sale_info'] = $this->Receiving->get_info($receiving_id)->row_array();
		$data['cart'] = $this->Receiving->get_info_itemp_receiving($receiving_id);
		// lay thong tin don hang
		$data['customer_info'] = $this->Employee->get_info_by_sales($data['sale_info']['employee_id']);
		$data = $this->xss_clean($data);
		$this->load->helper('listype');
		$data['arrListtype'] = get_listtype_two();
		$this->load->helper('namemoney');

		$numbermoney = (int)$data['sale_info']['pay_money'];
		$data['name_money'] = getstringmonney($numbermoney);
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		//echo "<pre>"; print_r($data); echo "</pre>";     
		return $this->xss_clean($data);
	}
	private function _reload($data = array())
	{		
		if(!isset($data['hinhthucban'])){
			$data['hinhthucban'] = 'Tiền mặt';
		}
		if(!isset($data['amount_tendered'])){
			$data['amount_tendered'] = 0;
		}
		$data['cart'] = $this->sale_lib->get_cart();
		//echo "<pre>"; print_r($data['cart']); echo "</pre>";
		$data['modes'] = array('sale' => $this->lang->line('sales_sale'), 'return' => $this->lang->line('sales_return'),'taiche' => $this->lang->line('sales_taiche'),'huy' => $this->lang->line('sales_huy'), 'tra_ncc' => 'Trả lại NCC');
		$data['mode'] = $this->sale_lib->get_mode();
		// lay thong tin cuoc van chuyen
		$data['vanchuyen_dg'] = $this->sale_lib->get_tranfer();
		$data['comment'] = $this->sale_lib->get_comment();
		// hinh thuc thanh toan
		// ngay ban hang
		$data['date_sale'] = $this->sale_lib->get_date_sale();
		// kiem tra xem co duoc hoan thanh hay khong
		if(isset($data['check_success'])){
			$data['payments_cover_total'] = 1;
		}else{
			$data['payments_cover_total'] = 0;
		}
		$data['payment_options'] = $this->xss_clean($this->Sale->get_payment_options(FALSE));
		$data['payments'] = $this->sale_lib->get_payments();
		$this->load->helper('listype');
		$data['arrListtype'] = get_listtype_two();
		if(!isset($data['selectinput'])){
			$data['selectinput'] = 'item';
		}
		$customer_info = $this->_load_customer_data($this->sale_lib->get_customer(), $data);
		if($data['mode'] == 'sale'){
			// thuong san luong
			$data['thuong_san_luong'] = $this->sale_lib->get_thuongsanluong();
			// Thong tin khach hang
			$data['customer_stringpromotion'] = $this->sale_lib->get_stringpromotion();
			$data['customer_debt'] = $this->sale_lib->get_customer_debt();
			// kiem tra co check vao o tra lai hay ko
			$arrchecked_returns = $this->sale_lib->get_checked_return();
			if(sizeof($arrchecked_returns) > 0){
				foreach($arrchecked_returns as $arrchecked_return){
					foreach($data['cart'] as $key => $value){
						if($value['item_id'] == $arrchecked_return['item_id']){
							$data['cart'][$key]['checkreturn'] = $arrchecked_return['checkreturn'];
						}
					}
				}
			}
			$this->load->helper('promotion');
			//get_checked_return();
			//echo "<pre>"; print_r($data); echo "</pre>";// exit;
			$this->load->view("sales/sales_register", $data);
		}else if($data['mode'] == 'huy'){
			$this->load->view("sales/sales_huy", $data);
		}else if($data['mode'] == 'taiche'){
			$supplier_id = $this->sale_lib->get_supplier();
			$data['taiche'] = $this->sale_lib->get_taiche();
			//print_r($data['taiche']); //exit;
			$supplier_info = '';
			if($supplier_id != -1)
			{
				$supplier_info = $this->Supplier->get_info($supplier_id);
				$data['supplier'] = $supplier_info->agency_name;
				$data['full_name'] = $supplier_info->full_name;
				$data['supplier_email'] = $supplier_info->email;
				$data['supplier_address'] = $supplier_info->address;
			}
			$this->load->view("sales/sales_taiche", $data);
		}else if($data['mode'] == 'return'){
			$this->load->view("sales/sales_return", $data);
		}else if($data['mode'] == 'tra_ncc'){
			$supplier_id = $this->sale_lib->get_supplier();
			//$data['taiche'] = $this->sale_lib->get_taiche();
			//print_r($data['taiche']); //exit;
			$supplier_info = '';
			if($supplier_id != -1)
			{
				$supplier_info = $this->Supplier->get_info($supplier_id);
				$data['supplier'] = $supplier_info->agency_name;
				$data['full_name'] = $supplier_info->full_name;
				$data['supplier_email'] = $supplier_info->email;
				$data['supplier_address'] = $supplier_info->address;
			}
			$this->load->view("sales/sales_tra_ncc", $data);
		}
		//echo "<pre>"; print_r($data); echo "</pre>";// exit;
	}
	public function remove_supplier()
	{
		$this->sale_lib->clear_reference();
		$this->sale_lib->remove_supplier();

		$this->_reload();
	}
	public function select_supplier()
	{
		$supplier_id = $this->input->post('supplier');
		if($this->Supplier->exists($supplier_id))
		{
			$this->sale_lib->set_supplier($supplier_id);
		}

		$this->_reload();
	}
	public function receipt($sale_id)
	{
		$mode = $this->sale_lib->get_mode();
		if($mode == 'sale'){
			$data = $this->_load_sale_data($sale_id);
		}elseif($mode == 'return'){
			$data = $this->_load_sale_data($sale_id);
		}elseif($mode == 'taiche'){
			$data = $this->_load_sale_taiche_data($sale_id);
		}elseif($mode == 'huy'){
			$data = $this->_load_sale_huy_data($sale_id);
		}elseif($mode == 'tra_ncc'){
			$data = $this->_load_sale_tra_ncc_data($sale_id);
		}
		$data['mode'] = $mode;
		$data['sale_id'] = $sale_id;
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		$this->load->helper('promotion');
		$this->load->view('sales/receipt', $data);
		$this->sale_lib->clear_all();
	}
	public function receiptexport($sale_id)
	{
		$mode = $this->sale_lib->get_mode();
		if($mode == 'sale'){
			$data = $this->_load_sale_data($sale_id);
		}elseif($mode == 'return'){
			$data = $this->_load_sale_data($sale_id);
		}elseif($mode == 'taiche'){
			$data = $this->_load_sale_taiche_data($sale_id);
		}elseif($mode == 'huy'){
			$data = $this->_load_sale_huy_data($sale_id);
		}
		$data['mode'] = $mode;
		$data['sale_id'] = $sale_id;
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		$this->load->view('sales/receiptexport', $data);
		$this->sale_lib->clear_all();
	}
	public function invoice($sale_id)
	{
		$data = $this->_load_sale_data($sale_id);

		$this->load->view('sales/invoice', $data);

		$this->sale_lib->clear_all();
	}

	public function viewpromotion($sale_id){
		$arrdachon = array();
		$arrchuachon = array();
		$sale_info = $this->xss_clean($this->Sale->get_info($sale_id)->row_array());
		$promotion = $sale_info['promotion'];
		$arrPromotion = json_decode($promotion,true);
		//echo "<pre>"; print_r($arrPromotion); echo "</pre>"; exit;
		$data['arrdachons'] = $arrPromotion;
		$this->load->view('promotion/viewpromotion', $data);
	}

	public function edit($sale_id)
	{
		$data = array();

		$data['employees'] = array();
		foreach($this->Employee->get_all()->result() as $employee)
		{
			foreach(get_object_vars($employee) as $property => $value)
			{
				$employee->$property = $this->xss_clean($value);
			}
			
			$data['employees'][$employee->person_id] = $employee->full_name;
		}

		$this->Sale->create_temp_table();

		$sale_info = $this->xss_clean($this->Sale->get_info($sale_id)->row_array());	
		$data['selected_customer_name'] = $sale_info['customer_name'];
		$data['selected_customer_id'] = $sale_info['customer_id'];
		$data['sale_info'] = $sale_info;

		$data['payments'] = array();
		foreach($this->Sale->get_sale_payments($sale_id)->result() as $payment)
		{
			foreach(get_object_vars($payment) as $property => $value)
			{
				$payment->$property = $this->xss_clean($value);
			}
			
			$data['payments'][] = $payment;
		}
		
		// don't allow gift card to be a payment option in a sale transaction edit because it's a complex change
		$data['payment_options'] = $this->xss_clean($this->Sale->get_payment_options(FALSE));
		
		$this->load->view('sales/form', $data);
	}

	public function delete($sale_id = -1, $update_inventory = TRUE,$checkshow = true)
	{
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$sale_ids = $sale_id == -1 ? $this->input->post('ids') : array($sale_id);
		$mode = $this->sale_lib->get_mode();
		if(sizeof($sale_ids) == 1){
			if($mode=='taiche' || $mode =='tra_ncc'){
				foreach($sale_ids as $sale_id)
				{
					$this->Receiving->delete($sale_id);
					if($checkshow){
						echo json_encode(array('success' => TRUE, 'message' => "Xoa thanh cong"));
					}
				}
			}else{
				if($this->Sale->delete_list($sale_ids, $employee_id, $update_inventory))
				{
					if($checkshow){
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('sales_successfully_deleted')));
					}
				}
				else
				{
					if($checkshow){
					echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('sales_unsuccessfully_deleted')));
					}
				}
			}
		}else{
			if($checkshow){
			echo json_encode(array('success' => FALSE, 'message' => "Chỉ được chọn 1 để xóa"));
			}
		}
	}

	public function save($sale_id = -1)
	{
		$newdate = $this->input->post('date');

		$date_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $newdate);

		$sale_data = array(
			'sale_time' => $date_formatter->format('Y-m-d H:i:s'),
			'customer_id' => $this->input->post('customer_id') != '' ? $this->input->post('customer_id') : NULL,
			'employee_id' => $this->input->post('employee_id'),
			'comment' => $this->input->post('comment'),
			'invoice_number' => $this->input->post('invoice_number') != '' ? $this->input->post('invoice_number') : NULL
		);

		// go through all the payment type input from the form, make sure the form matches the name and iterator number
		$payments = array();
		$number_of_payments = $this->input->post('number_of_payments');
		for ($i = 0; $i < $number_of_payments; ++$i)
		{
			$payment_amount = $this->input->post('payment_amount_' . $i);
			$payment_type = $this->input->post('payment_type_' . $i);
			// remove any 0 payment if by mistake any was introduced at sale time
			if($payment_amount != 0)
			{
				// search for any payment of the same type that was already added, if that's the case add up the new payment amount
				$key = FALSE;
				if(!empty($payments))
				{
					// search in the multi array the key of the entry containing the current payment_type
					// NOTE: in PHP5.5 the array_map could be replaced by an array_column
					$key = array_search($payment_type, array_map(function($v){return $v['payment_type'];}, $payments));
				}

				// if no previous payment is found add a new one
				if($key === FALSE)
				{
					$payments[] = array('payment_type' => $payment_type, 'payment_amount' => $payment_amount);
				}
				else
				{
					// add up the new payment amount to an existing payment type
					$payments[$key]['payment_amount'] += $payment_amount;
				}
			}
		}

		if($this->Sale->update($sale_id, $sale_data, $payments))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('sales_successfully_updated'), 'id' => $sale_id));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('sales_unsuccessfully_updated'), 'id' => $sale_id));
		}
	}

	public function cancel()
	{
		$this->sale_lib->clear_all();

		$this->_reload();
	}
		public function saveprovice($customer_id = -1)
		{
		   $this->_reload();
		}
	public function suspend()
	{	
		$cart = $this->sale_lib->get_cart();
		$payments = $this->sale_lib->get_payments();
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$customer_id = $this->sale_lib->get_customer();
		$customer_info = $this->Customer->get_info($customer_id);
		$invoice_number = $this->_is_custom_invoice_number($customer_info) ? $this->sale_lib->get_invoice_number() : NULL;
		$comment = $this->sale_lib->get_comment();

		//SAVE sale to database 
		$data = array();
		if($this->Sale_suspended->save($cart, $customer_id, $employee_id, $comment, $invoice_number, $payments) == '-1')
		{
			$data['error'] = $this->lang->line('sales_unsuccessfully_suspended_sale');
		}
		else
		{
			$data['success'] = $this->lang->line('sales_successfully_suspended_sale');
		}

		$this->sale_lib->clear_all();

		$this->_reload($data);
	}
	
	
	
	public function savekm($customer_id = -1)
	{
		if($customer_id !== -1){
			$listpromotion = $this->input->post('listpromotion');
			$listpromotion = trim($listpromotion,",");
			if($this->Sale->updatetkmp($customer_id,$listpromotion))
			{
				$this->_reload();
			}
		}		
	}

	public function edit_salse($sale_id)
	{
		// khoi tao session luu receiving_id
		$this->sale_lib->clear_all();
		$this->sale_lib->set_sale_id($sale_id);
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		//echo "<pre>";print_r($sale_info);echo "</pre>"; exit;
		$arrSanluong['sanluong_tieude'] = $sale_info['sanluong_tieude'];
		$arrSanluong['sanluong_soluong'] = $sale_info['sanluong_soluong'];
		$arrSanluong['sanluong_dongia'] = $sale_info['sanluong_dongia'];
		$this->sale_lib->set_thuongsanluong($arrSanluong);
		// lay don hang
		// ngay nhap don hang
		$date_sale = date("d/m/Y", strtotime($sale_info['sale_time']));
		$this->sale_lib->set_date_sale($date_sale);
		$data=array();
		$data['hinhthucban'] = $sale_info['payment_type'];
		$data['amount_tendered'] = to_currency_no_money($sale_info['pay_money']);
		// set mode
		if($sale_info['type'] == 1 || $sale_info['type'] == 5){

			// cap nhat cuoc van chuyen
			$this->sale_lib->set_tranfer($sale_info['car_money']);
			$this->sale_lib->set_mode('sale');
			// san pham
			$arrcarts = $this->Sale->get_info_itemp_sales($sale_id);
			//echo "<pre>";print_r($arrcarts);echo "</pre>";exit;
			foreach($arrcarts as $arrcart){
				$quantity = (int)$arrcart['quantity'];
				$this->sale_lib->add_item($arrcart['item_id'],$quantity ,(int)$arrcart['quantity_give'], (int)$arrcart['sale_price'],(int)$arrcart['quantity_return'],(int)$arrcart['quantity_loan'],(int)$arrcart['quantity_loan_return']);
			}
			//$this->sale_lib->add_payment($sale_info['payment_type'], 0);
			$this->sale_lib->set_stringpromotion($sale_info['promotion']);
			$this->sale_lib->set_customer_debt($sale_info['customer_debt']);
		}else if($sale_info['type'] == 2){
			$this->sale_lib->set_tranfer($sale_info['car_money']);
			$this->sale_lib->set_mode('return');
			$arrcarts = $this->Sale->get_info_itemp_sales($sale_id);
			foreach($arrcarts as $arrcart){
				$this->sale_lib->add_item($arrcart['item_id'], (int)$arrcart['quantity_return'],(int)$arrcart['quantity_give'], (int)$arrcart['sale_price']);
			}
		}
		// nha cung cap
		$customer_id = $sale_info['customer_id'];
		$this->sale_lib->set_customer($customer_id);
		// ghi chu
		$this->sale_lib->set_comment($sale_info['comment']);
		$this->_reload($data);
	}
	public function edit_salse_tc($receiving_id)
	{
		// khoi tao session luu receiving_id
		$this->sale_lib->clear_all();
		$this->sale_lib->set_sale_id($receiving_id);
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();
		//echo "<pre>"; print_r($receiving_info); echo "</pre>";
		$taiche['taiche_soluong'] = $receiving_info['add_quantity'];
		$taiche['taiche_dongia'] = $receiving_info['add_money'];
		$this->sale_lib->set_taiche($taiche);
		// ngay nhap don hang
		$date_receiving = date("d/m/Y", strtotime($receiving_info['receiving_time']));
		$this->sale_lib->set_date_sale($date_receiving);
		$this->sale_lib->set_mode('taiche');
		// san pham
		$arrcarts = $this->Receiving->get_info_itemp_receiving($receiving_id);
		foreach($arrcarts as $arrcart){
			$this->sale_lib->add_item($arrcart['item_id'], (int)$arrcart['quantity'],0, $arrcart['input_prices']);
		}
		// nha cung cap
		$supplier_id = $receiving_info['supplier_id'];
		$this->sale_lib->set_supplier($supplier_id);
		// ghi chu
		$this->sale_lib->set_comment($receiving_info['comment']);
		$this->_reload();
	}

	public function edit_salse_tncc($receiving_id)
	{
		// khoi tao session luu receiving_id
		$this->sale_lib->clear_all();
		$this->sale_lib->set_sale_id($receiving_id);
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();
		// ngay nhap don hang
		$date_receiving = date("d/m/Y", strtotime($receiving_info['receiving_time']));
		$this->sale_lib->set_date_sale($date_receiving);
		$this->sale_lib->set_mode('tra_ncc');
		// san pham
		$arrcarts = $this->Receiving->get_info_itemp_receiving($receiving_id);
		foreach($arrcarts as $arrcart){
			$this->sale_lib->add_item($arrcart['item_id'], (int)$arrcart['quantity'],0, $arrcart['input_prices']);
		}
		// nha cung cap
		$supplier_id = $receiving_info['supplier_id'];
		$this->sale_lib->set_supplier($supplier_id);
		// ghi chu
		$this->sale_lib->set_comment($receiving_info['comment']);
		$this->_reload();
	}

	// Thuong san luong
	public function bonus()
	{
		$person_id = $this->session->userdata('person_id');
		//echo $person_id; exit;
		$mode = $this->sale_lib->get_mode();
		$data['table_headers_text'] = 'Thưởng sản lượng';
		$data['table_headers'] = get_sales_manage_table_headers();
		$this->load->view('sales/bonus', $data);
	}

	public function add_bonus()
	{
		$data = array();
		$this->load->view('sales/bonus_add', $data);
	}

}
?>
