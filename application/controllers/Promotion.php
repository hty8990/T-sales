<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Promotion extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('Promotion');
		$this->load->library('item_lib');
	}
	
	/*
	Add the total cost and retail price to a passed items kit retrieving the data from each singular item part of the kit
	*/
	private function _add_totals_to_item_kit($item_kit)
	{
		$item_kit->total_cost_price = 0;
		$item_kit->total_unit_price = 0;
		
		foreach($this->Item_kit_items->get_info($item_kit->item_kit_id) as $item_kit_item)
		{
			$item_info = $this->Item->get_info($item_kit_item['item_id']);
			foreach(get_object_vars($item_info) as $property => $value)
			{
				$item_info->$property = $this->xss_clean($value);
			}
			
			$item_kit->total_cost_price += $item_info->cost_price * $item_kit_item['quantity'];
			$item_kit->total_unit_price += $item_info->unit_price * $item_kit_item['quantity'];
		}

		return $item_kit;
	}
	
	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_promotion_manage_table_headers());
		$data['conhan'] = 'checked="checked"';
		$data['quahan'] = '';
		$this->load->view('promotion/manage', $data);
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
		$order  = $this->input->get('order');
		$status  = $this->input->get('trangthaihan');
		$start_date = $this->input->get('start_date');
		$giarieng = false;
		if($search == 'hh'){
			$search = 'thuc_an_hon_hop';
		}else if($search == 'dd'){
			$search = 'thuc_an_dam_dac';
		}else if($search == 'k'){
			$search = 'khac';
		}else if($search == 'tk'){
			$search = 'triet_khau';
		}else if($search == 'km'){
			$search = 'khuyen_mai';
		}else if($search == 'tc'){
			$search = 'all';
		}else if($search == 'gr'){
			$search = 'gia_rieng';
		}
		$item_kits = $this->Item_kit->search_promotion($search, $sort, $limit);
		$total_rows = sizeof($item_kits);
		$data_rows = array();
		$this->load->helper('listype');
		$arrListtype = get_listtype_two();
		$start_date = DateTime::createFromFormat('d/m/Y',$start_date)->format('Y-m-d H:i:s');
		foreach($item_kits->result() as $item_kit)
		{
			if($status == 'conhan'){
				$arrResult = $this->Customer->get_promotion_by_midle_date_customer($item_kit->id,$start_date);
				if($arrResult){
				//if($arrResult['end_date'] >= $start_date){
				$item_kit->promotion_kg = $arrResult['promotion_kg'];
				$item_kit->promotion_pecen = $arrResult['promotion_pecen'];
				$item_kit->start_date = $arrResult['start_date'];
				$item_kit->end_date = $arrResult['end_date'];
				$data_rows[] = get_promotion_data_row($item_kit, $this, $arrListtype );	
				}
			}else{
				$arrResult = $this->Customer->get_promotion_by_midle_date_customer($item_kit->id,$start_date);
			//	print_r($arrResult);// exit;
				$item_kit->promotion_kg = $arrResult['promotion_kg'];
				$item_kit->promotion_pecen = $arrResult['promotion_pecen'];
				$item_kit->start_date = $arrResult['start_date'];
				$item_kit->end_date = $arrResult['end_date'];
				$data_rows[] = get_promotion_data_row($item_kit, $this, $arrListtype );	
			}
			//}		
		}
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Item_kit->get_search_suggestions($this->input->post('term')));

		echo json_encode($suggestions);
	}

	public function get_row($row_id)
	{
		// calculate the total cost and retail price of the Kit so it can be added to the table refresh
		$item_kit =$this->Item_kit->get_info($row_id);
		
		echo json_encode(get_item_kit_data_row($item_kit, $this));
	}
	
	public function view($id = -1)
	{
		$info = $this->Item_kit->get_info_Promotion($id);
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		$data['Promotion']  = $info;
		$this->load->helper('listype');
		$data['arrListtype'] = get_listtype_two();
		$data['arrtype_promotion'] = get_type_promotion();
		//echo "<pre>"; print_r($data); echo "</pre>";
		$this->load->view("promotion/form", $data);
	}
	
	public function save($id = -1)
	{
		$checkall = 0;
		if($this->input->post('check_promotion')){
			$checkall = 1;
		}
		$promotion_data = array(
			'promotion_type' => $this->input->post('promotion_type'),
			'promotion_name' => $this->input->post('promotion_name'),
			'description' => $this->input->post('description'),
			'sort' => $this->input->post('sort'),
			'type' => $this->input->post('type'),
			'check_all' => $checkall
		);
		if($promotion_data['promotion_type'] == 'khac'){
			$promotion_data['item_promotion'] = trim($this->input->post('item_promotion'),',');
			$promotion_data['item_id_promotion'] = trim($this->input->post('item_id_promotion'),',');
		}
		$result = $this->Item_kit->save_promotion($promotion_data, $id);
		if($result)
		{
			$checkid = $id;
			if($id == -1){
				$checkid = $result;
			}
			// cap nhat so thu tu
			$this->Item_kit->update_sort_promotion($this->input->post('sort'), $checkid);
			$success = TRUE;
			//New item kit

			echo json_encode(array('success' => $success,
								'message' => "Cập nhật thành công"));
		}
		else//failure
		{
			$item_kit_data = $this->xss_clean($item_kit_data);

			echo json_encode(array('success' => FALSE, 
								'message' => "Cập nhật thất bại"));
		}
	}
	public function savequantities($item_kit_id = -1)
	{
		$item_kit_data = array(
			'name' => $this->input->post('name'),
			'item_number' => $this->input->post('item_number'),
			'unit_price' => $this->input->post('unit_price'),
			'unit_weight' => $this->input->post('unit_weight'),
			'description' => $this->input->post('description')
		);
		//var_dump($item_kit_data); exit;
		if($this->Item_kit->save($item_kit_data, $item_kit_id))
		{
			$success = TRUE;
			//New item kit

			echo json_encode(array('success' => $success,
								'message' => $this->lang->line('item_kits_successful_adding').' '.$item_kit_data['name'], 'id' => $item_kit_id));
		}
		else//failure
		{
			$item_kit_data = $this->xss_clean($item_kit_data);

			echo json_encode(array('success' => FALSE, 
								'message' => $this->lang->line('item_kits_error_adding_updating').' '.$item_kit_data['name'], 'id' => -1));
		}
	}
	public function delete()
	{
		$item_kits_to_delete = $this->xss_clean($this->input->post('ids'));
		if($this->Item_kit->delete_promotion_list($item_kits_to_delete))
		{
			echo json_encode(array('success' => TRUE,
								'message' => 'Cập nhật thành công'));
		}
		else
		{
			echo json_encode(array('success' => FALSE,
								'message' => $this->lang->line('item_kits_cannot_be_deleted')));
		}
	}
	
	public function generate_barcodes($item_kit_ids)
	{
		$this->load->library('barcode_lib');
		$result = array();

		$item_kit_ids = explode(':', $item_kit_ids);
		foreach($item_kit_ids as $item_kid_id)
		{		
			// calculate the total cost and retail price of the Kit so it can be added to the barcode text at the bottom
			$item_kit = $this->_add_totals_to_item_kit($this->Item_kit->get_info($item_kid_id));
			
			$item_kid_id = 'KIT '. urldecode($item_kid_id);

			$result[] = array('name' => $item_kit->name, 'item_id' => $item_kid_id, 'item_number' => $item_kid_id,
							'cost_price' => $item_kit->total_cost_price, 'unit_price' => $item_kit->total_unit_price);
		}

		$data['items'] = $result;
        $barcode_config = $this->barcode_lib->get_barcode_config();
		// in case the selected barcode type is not Code39 or Code128 we set by default Code128
		// the rationale for this is that EAN codes cannot have strings as seed, so 'KIT ' is not allowed
		if($barcode_config['barcode_type'] != 'Code39' && $barcode_config['barcode_type'] != 'Code128')
		{
			$barcode_config['barcode_type'] = 'Code128';
		}
		$data['barcode_config'] = $barcode_config;

		// display barcodes
		$this->load->view("barcodes/barcode_sheet", $data);
	}
	// ----------------------------------------------------------------------------
	// --------------------- Quan ly gia ban --------------------------------------
	// ----------------------------------------------------------------------------
	public function manageprice($id = -1)
	{	
		$promotion= $this->Item_kit->get_info_Promotion($id);

		//print_r($promotion); exit;
		$data['promotion'] = $promotion;
		$this->item_lib->set_item_promotion($id);
		$data['table_headers'] = chi_tiet_khuyen_mai_headers();
		$this->load->view('promotion/manageprice', $data);
	}

	public function suspended($custumer_id)
	{	
		$this->load->library('sale_lib');
		$start_date = $this->sale_lib->get_date_sale();
		$arrCustumer = $this->Customer->get_info($custumer_id);
		//var_dump($arrCustumer->c_promotion);
		// lay tat ca cac chuong trinh cua mot don khuyen mai arrPromotion
		$arrPromotions = $this->Item_kit->search_promotion('')->result_array();
		$data['custumer_id'] = $custumer_id;
		$data['c_promotion'] = $arrCustumer->c_promotion;
		$this->load->helper('listype');
		$data['arrListtype'] = get_listtype_two();
		$start_date = DateTime::createFromFormat('d/m/Y',$start_date)->format('Y-m-d H:i:s');
		$i=$j=0;		
		$arrdachon = array();
		$arrchuachon = array();
		foreach($arrPromotions as $arrPromotion)
		{
			$arrResult = $this->Customer->get_promotion_by_time_customer($arrPromotion['id'],$start_date);
			if($arrResult && $start_date <= $arrResult['end_date']){
				if (strpos(','.$data['c_promotion'].',', ','.$arrPromotion['id'].',') !== false) {
					$arrdachon[$i]['id'] = $arrPromotion['id'];
					$arrdachon[$i]['name'] = $arrPromotion['promotion_name'];
					$arrdachon[$i]['promotion_type'] = $arrPromotion['promotion_type'];
					$arrdachon[$i]['type'] = $arrPromotion['type'];
					$arrdachon[$i]['item_id_promotion'] = $arrPromotion['item_id_promotion'];
					$arrdachon[$i]['description'] = $arrPromotion['description'];
					$arrdachon[$i]['promotion_pecen'] = $arrResult['promotion_pecen'];
					$arrdachon[$i]['promotion_kg'] = $arrResult['promotion_kg'];
					$arrdachon[$i]['start_date'] = $arrResult['start_date'];
					$arrdachon[$i]['end_date'] = $arrResult['end_date'];
					$i++;
				}else{
					$arrchuachon[$j]['id'] = $arrPromotion['id'];
					$arrchuachon[$j]['name'] = $arrPromotion['promotion_name'];
					$arrchuachon[$j]['promotion_type'] = $arrPromotion['promotion_type'];
					$arrchuachon[$j]['type'] = $arrPromotion['type'];
					$arrchuachon[$j]['item_id_promotion'] = $arrPromotion['item_id_promotion'];
					$arrchuachon[$j]['description'] = $arrPromotion['description'];
					$arrchuachon[$j]['promotion_pecen'] = $arrResult['promotion_pecen'];
					$arrchuachon[$j]['promotion_kg'] = $arrResult['promotion_kg'];
					$arrchuachon[$j]['start_date'] = $arrResult['start_date'];
					$arrchuachon[$j]['end_date'] = $arrResult['end_date'];
					$j++;
				}
			}
		}
		$data['arrdachons'] = $arrdachon;
		$data['arrchuachons'] = $arrchuachon;
		$this->load->view('promotion/suspended', $data);
	}
}
?>