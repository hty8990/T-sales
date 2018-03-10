<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Items_price extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('items');

		$this->load->library('item_lib');
	}
	public function index()
	{
		print_r(phpinfo()); exit;
		$data['table_headers'] = $this->xss_clean(get_items_manage_table_headers());
		

		$this->load->view('items/manage', $data);
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

		$this->item_lib->set_item_location($this->input->get('stock_location'));
		$filters = array('start_date' => $this->input->get('start_date'),
						'end_date' => $this->input->get('end_date'),
						'stock_location_id' => $this->item_lib->get_item_location(),
						'empty_upc' => FALSE,
						'low_inventory' => FALSE, 
						'is_serialized' => FALSE,
						'no_description' => FALSE,
						'search_custom' => FALSE,
						'is_status' => TRUE);
		
		// check if any filter is set in the multiselect dropdown
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);

		$filters = array_merge($filters, $filledup);
		//var_dump($filters);
		$item_id = $this->item_lib->get_item_price();
		$items = $this->Item->search_prices($item_id,$search, $filters, $limit, $offset, $sort, $order);

		//echo $items; exit;
		$total_rows = $this->Item->get_found_search_prices_rows($item_id,$search, $filters);
		
		$data_rows = array();
		foreach($items->result() as $item)
		{
			//echo "<pre>"; print_r($item); echo "</pre>"; exit;
			//var_dump($item);
			$data_rows[] = $this->xss_clean(get_item_price_data_row($item, $this));
		}
		
		//echo "<pre>";print_r($data_rows);echo "</pre>";exit;
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	public function view($price_id = -1)
	{
		$item_id = $this->item_lib->get_item_price();
		$item_info = $this->Item->get_info($item_id);
		//print_r($item_info); exit;		
		if($price_id > 0){
			$price_info = $this->Item->get_price_info_items($price_id);
		}else{
			$price_info = $this->Item->get_price_info_items($price_id);
			$pricess = $this->Item->get_price_info_first_items($item_id);
			$price_info->sale_price = $pricess[0]['sale_price'];
		}
		
		$data['item_info'] = $item_info;
		$data['price_info'] = $price_info;
		$this->load->helper('listype');
		$this->load->view('items/from_price_item', $data);
	}
	public function delete()
	{
		$items_to_delete = $this->input->post('ids');
		if(sizeof($items_to_delete) == 1){
			if($this->Item->delete_price_items($items_to_delete))
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
		$item_id = $this->item_lib->get_item_price();
		$sale_price =  str_replace(",","",$this->input->post('sale_price'));
		$sale_price =  str_replace(".","",$sale_price);
		$input_prices =  str_replace(",","",$this->input->post('input_prices'));
		$input_prices =  str_replace(".","",$input_prices);
		//Save item data
		$item_data = array(
			'start_date' => $start_date,
			'sale_price' => $sale_price,
			'input_prices' => $input_prices,
			'item_id' => $item_id,
			'description' => $this->input->post('description')
		);		
		if($this->Item->save_prices($item_data, $price_id))
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
