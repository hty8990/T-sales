<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Items_price_customer extends Secure_Controller
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
		$items = $this->Item->search_prices_customer($item_id,$search, $filters, $limit, $offset, $sort, $order);

		//echo $items; exit;
		$total_rows = $this->Item->get_found_search_prices_customer_rows($item_id,$search, $filters);
		
		$data_rows = array();
		foreach($items->result() as $item)
		{
			//echo "<pre>"; print_r($item); echo "</pre>"; exit;
			//var_dump($item);
			$data_rows[] = $this->xss_clean(get_item_price_customer_data_row($item, $this));
		}
		
		//echo "<pre>";print_r($data_rows);echo "</pre>";exit;
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	public function view($price_id = -1)
	{
		$item_id = $this->item_lib->get_item_price();
		$item_info = $this->Item->get_info($item_id);
		$arrResult = $this->Customer->get_all();
		$peoples = array();
		foreach($arrResult->result() as $row)
		{
			$peoples[] = array('id' => $row->person_id
				,'name' => $row->full_name." - ".$row->code
			);
		}
		// lay thong tin gia
		$price_info = $this->Item->get_price_customer_info_items($price_id);
		//print_r($price_info); exit;
		$data['item_info'] = $item_info;
		$data['price_info'] = $price_info;
		$data['peoples'] = $peoples;
		$this->load->helper('listype');
		$this->load->view('items/from_price_item_customer', $data);
	}
	public function delete()
	{
		$items_to_delete = $this->input->post('ids');
		if($items_to_delete){
			if($this->Item->delete_price_customer_items($items_to_delete))
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
		$selectcustormer = $this->input->post('selectcustormer');
		$selectcustormers = explode(",", $selectcustormer);
		foreach($selectcustormers as $customer_id){
			if($customer_id > 0){
				$start_date = $this->input->post('start_date');
				$start_date = DateTime::createFromFormat('d/m/Y', $start_date)->format('Y-m-d H:i:s');
				$end_date = $this->input->post('end_date');
				$end_date = DateTime::createFromFormat('d/m/Y', $end_date)->format('Y-m-d H:i:s');
				$item_id = $this->item_lib->get_item_price();
				$sale_price =  str_replace(",","",$this->input->post('sale_price'));
				$sale_price =  str_replace(".","",$sale_price);
				//Save item data
				$item_data = array(
					'start_date' => $start_date,
					'end_date' => $end_date,
					'sale_price' => $sale_price,
					'customer_id' => $customer_id,
					'item_id' => $item_id,
					'description' => $this->input->post('description')
				);		
				if($this->Item->save_prices_customer($item_data, $price_id));
			}
		}
		echo json_encode(array('success' => TRUE, 'message' => 'Cập nhật thành công'));
	}
}
?>
