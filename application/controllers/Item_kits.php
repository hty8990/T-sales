<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Item_kits extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('item_kits');
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
		$data['table_headers'] = $this->xss_clean(get_item_kits_manage_table_headers());

		$this->load->view('item_kits/manage', $data);
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
		$start_date = $this->input->get('start_date');
		$filters = '';
		$item_kits = $this->Item_kit->search($search,$filters, $limit, $offset, $sort, $order);
		$total_rows = $this->Item_kit->get_found_rows($search, $filters);
		$start_date = DateTime::createFromFormat('d/m/Y',$start_date)->format('Y-m-d H:i:s');
		//var_dump($item_kits); exit;
		$data_rows = array();
		foreach($item_kits->result() as $item_kit)
		{
			//print_r($item_kit); exit;
			$arrResult = $this->Item_kit->get_packet_price_by_time($item_kit->id,$start_date);
			//$item_kit = $this->_add_totals_to_item_kit($item_kit);
			$item_kit->input_prices = $arrResult['input_prices'];
			$item_kit->tondauky = $this->Item->baobitonkhodauky($item_kit->id,$start_date);
			$data_rows[] = $this->xss_clean(get_item_kit_data_row($item_kit, $this));
		}

		$data_rows = $this->xss_clean($data_rows);

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
	
	public function view($item_kit_id = -1)
	{
		$info = $this->Item_kit->get_info($item_kit_id);
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		$data['item_kit_info']  = $info;
		
		$items = array();
		foreach($this->Item_kit_items->get_info($item_kit_id) as $item_kit_item)
		{
			
			$items[] = $item_kit_item;
		}
		$this->load->helper('listype');
		$data['quycach'] = get_quycach();
		$data['item_kit_items'] = $items;
		//echo "<pre>"; print_r($data); echo "</pre>";
		$this->load->view("item_kits/form", $data);
	}
	
	public function save($item_kit_id = -1)
	{
		$item_kit_data = array(
			'name' => $this->input->post('name'),
			'item_number' => $this->input->post('item_number'),
			'item_number' => $this->input->post('item_number'),
			'quantities' => $this->input->post('quantities'),
			'description' => $this->input->post('description')
		);
		//var_dump($item_kit_data); exit;
		if($this->Item_kit->save($item_kit_data, $item_kit_id))
		{
			$success = TRUE;
			//New item kit

			echo json_encode(array('success' => $success,
								'message' => $this->lang->line('item_kits_successful_adding')));
		}
		else//failure
		{
			$item_kit_data = $this->xss_clean($item_kit_data);

			echo json_encode(array('success' => FALSE, 
								'message' => $this->lang->line('item_kits_error_adding_updating')));
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

		if($this->Item_kit->delete_list($item_kits_to_delete))
		{
			echo json_encode(array('success' => TRUE,
								'message' => $this->lang->line('item_kits_successful_deleted')));
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
		$Item_kit_infor= $this->Item_kit->get_info($id);
		$this->load->library('item_lib');
		$data['Item_kit_infor'] = $Item_kit_infor;
		$this->item_lib->set_item_kit_infor($id);
		$data['table_headers'] = chi_tiet_gia_bao_bi_headers();
		$this->load->view('item_kits/manageprice', $data);
	}

	public function change_quantities($item_id = -1)
	{
		$this->load->helper('listype');
		$data = array();
		$data['start_date'] = date('d/m/Y');
		$item_info = $this->Item_kit->get_info($item_id);
		//echo "<pre>"; print_r($item_info) ;echo "</pre>"; //exit;
		foreach(get_object_vars($item_info) as $property => $value)
		{
			$item_info->$property = $this->xss_clean($value);
		}
		$data['ton_dau_ky'] = $this->Item->baobitonkhodauky($item_id,date('Y-m-d'));
		$data['item_info'] = $item_info;
		$data['quycach'] = get_quycach();
		$this->load->view('item_kits/change_quantities', $data);
	}

	public function save_quantities($item_id = -1){
		$start_date = $this->input->post('input_date');
		$start_date = DateTime::createFromFormat('d/m/Y', $start_date)->format('Y-m-d H:i:s');
		$quantity = $this->input->post('new_quantitie');
		$coments = $this->input->post('description');
		if($this->Item_kit->save_quantities_packet($quantity, $start_date, $coments, $item_id))
		{
			$message = 'Cập nhật thành công';
            echo json_encode(array('success' => TRUE, 'message' => $message));
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => "Cập nhật lỗi", 'id' => -1));
		}
	}
}
?>