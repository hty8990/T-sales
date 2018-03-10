<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Item_kits_price extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('items');

		$this->load->library('item_lib');
	}
	
	/*
	Returns Items table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort = $this->input->get('sort');
		$order = $this->input->get('order');

		$filters = array('start_date' => $this->input->get('start_date'),
						'end_date' => $this->input->get('end_date'),
						'empty_upc' => FALSE,
						'low_inventory' => FALSE, 
						'is_serialized' => FALSE,
						'no_description' => FALSE,
						'search_custom' => FALSE,
						'is_status' => TRUE);
		
		// check if any filter is set in the multiselect dropdown
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);

		$filters = array_merge($filters, $filledup);
		$id = $this->item_lib->get_item_kit_infor();
		$items = $this->Item_kit->get_all_Packet_prices($id,$search, $filters, $limit, $offset, $sort, $order);

		//echo $items; exit;
		$total_rows = $this->Item_kit->get_found_Packet_prices_rows($id,$search, $filters);
		
		$data_rows = array();
		foreach($items->result() as $item)
		{
			//echo "<pre>"; print_r($item); echo "</pre>"; exit;
			//var_dump($item);
			$data_rows[] = $this->xss_clean(get_packet_price_detail_data_row($item, $this));
		}
		
		//echo "<pre>";print_r($data_rows);echo "</pre>";exit;
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	public function view($item_packet_id = -1)
	{
		$packet_price_infor = $this->Item_kit->get_info_Packet_price($item_packet_id);
		//print_r($packet_price_infor);
		$id = $this->item_lib->get_item_kit_infor();
		$packet_infor= $this->Item_kit->get_info($id);
		// lay thong tin gia
		$data['packet_infor'] = $packet_infor;
		$data['packet_price_infor'] = $packet_price_infor;

		$this->load->helper('listype');
		$this->load->view('item_kits/form_prices', $data);
	}
	public function delete()
	{
		$items_to_delete = $this->input->post('ids');
		if(sizeof($items_to_delete) == 1){
			if($this->Item_kit->delete_packet_price_detail($items_to_delete))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('sales_successfully_deleted')));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('sales_unsuccessfully_deleted')));
			}
		}else{
			echo json_encode(array('success' => FALSE, 'message' => "Chỉ được chọn 1 để xóa"));
		}
	}
	public function save($price_id = -1)
	{
		$start_date = $this->input->post('start_date');
		$start_date = DateTime::createFromFormat('d/m/Y', $start_date)->format('Y-m-d H:i:s');
		$packet_id = $this->item_lib->get_item_kit_infor();
		$input_prices =  str_replace(",","",$this->input->post('input_prices'));
		$input_prices =  str_replace(".","",$input_prices);
		//Save item data
		$item_data = array(
			'start_date' => $start_date,
			'input_prices' => $input_prices,
			'packet_id' => $packet_id,
			'description' => $this->input->post('description')
		);		
		if($this->Item_kit->save_packet_prices($item_data, $price_id))
		{
			$success = TRUE;
			$new_item = FALSE;
			//New item
			echo json_encode(array('success' => TRUE, 'message' => 'Cập nhật thành công'));
		}
		else//failure
		{
			$message = $this->xss_clean($this->lang->line('items_error_adding_updating') . ' ' . $item_data['name']);
			
			echo json_encode(array('success' => FALSE, 'message' => $message, 'id' => -1));
		}
	}
}
?>
