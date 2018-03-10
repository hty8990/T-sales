<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Items extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('items');

		$this->load->library('item_lib');
	}
	
	public function index()
	{
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
		$customer_id = $this->input->get('customer_id');
		$start_date = $this->input->get('start_date');
		$this->item_lib->set_item_location($this->input->get('stock_location'));
		$filters = array('start_date' => $start_date,
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
		$items = $this->Item->search($search, $filters, $limit, $offset, $sort, $order);

		//echo $items; exit;
		$total_rows = $this->Item->get_found_rows($search, $filters);
		
		$data_rows = array();
		$start_date = DateTime::createFromFormat('d/m/Y',$start_date)->format('Y-m-d H:i:s');
		foreach($items->result() as $item)
		{
			// tinh gia hien tai dua vao khoang thoi gian
			
			$arrResult = $this->Item->get_prices_by_time_customer($item->id,$start_date,$customer_id);
			$item->sale_price = $arrResult['sale_price'];
			$item->input_prices = $arrResult['input_prices'];
			$item->tondauky = $this->Giftcard->BC09_hanghoantonkho('kytruoc',$item->id, $start_date);
			$data_rows[] = get_item_data_row($item, $this);
		
		}
		//echo "<pre>";print_r($data_rows);echo "</pre>";exit;
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	public function pic_thumb($pic_id)
	{
		$this->load->helper('file');
		$this->load->library('image_lib');
		$base_path = "uploads/item_pics/" . $pic_id ;
		$images = glob ($base_path. "*");
		if(sizeof($images) > 0)
		{
			$image_path = $images[0];
			$ext = pathinfo($image_path, PATHINFO_EXTENSION);
			$thumb_path = $base_path . $this->image_lib->thumb_marker . '.' . $ext;
			if(sizeof($images) < 2)
			{
				$config['image_library'] = 'gd2';
				$config['source_image']  = $image_path;
				$config['maintain_ratio'] = TRUE;
				$config['create_thumb'] = TRUE;
				$config['width'] = 52;
				$config['height'] = 32;
 				$this->image_lib->initialize($config);
 				$image = $this->image_lib->resize();
				$thumb_path = $this->image_lib->full_dst_path;
			}
			$this->output->set_content_type(get_mime_by_extension($thumb_path));
			$this->output->set_output(file_get_contents($thumb_path));
		}
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Item->get_search_suggestions($this->input->post_get('term'),
			array('search_custom' => $this->input->post('search_custom'), 'is_deleted' => $this->input->post('is_deleted') != NULL), FALSE));

		echo json_encode($suggestions);
	}

	public function suggest()
	{
		$suggestions = $this->xss_clean($this->Item->get_search_suggestions($this->input->post_get('term'),
			array('search_custom' => FALSE, 'is_deleted' => FALSE), TRUE));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_category()
	{
		$suggestions = $this->xss_clean($this->Item->get_category_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	/*
	 Gives search suggestions based on what is being searched for
	*/
	public function suggest_location()
	{
		$suggestions = $this->xss_clean($this->Item->get_location_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}
	
	/*
	 Gives search suggestions based on what is being searched for
	*/
	public function suggest_custom()
	{
		$suggestions = $this->xss_clean($this->Item->get_custom_suggestions($this->input->post('term'), $this->input->post('field_no')));

		echo json_encode($suggestions);
	}

	public function get_row($item_ids)
	{
		$this->search();
	}

	public function view($item_id = -1)
	{

		//$data['item_tax_info'] = $this->xss_clean($this->Item_taxes->get_info($item_id));
		//$data['default_tax_1_rate'] = '';
		//$data['default_tax_2_rate'] = '';
		$this->load->helper('listype');
		$arr_listype = get_listtype_three();
		$data['arr_listype'] = $arr_listype;
		$item_info = $this->Item->get_info($item_id);
		
		foreach(get_object_vars($item_info) as $property => $value)
		{
			$item_info->$property = $this->xss_clean($value);
		}
		$data['check_packet'] = 0;
		if($item_info->packet_id > 0){
			$data['check_packet'] = 1;
		}
		$data['item_info'] = $item_info;
		//var_dump($item_info); exit;
		$data['selected_cartegory'] = $item_info->category;
		$suppliers = array('' => $this->lang->line('items_none'));
		$arrsuppliers = $this->Supplier->get_all()->result_array();
		//print_r($arrsuppliers); exit;
		foreach($arrsuppliers as $row)
		{
			$suppliers[$this->xss_clean($row['person_id'])] = $this->xss_clean($row['agency_name']);
		}
		$Item_kit = array('' => ' -- Chọn bao bì --');
		// lay thong tin bao bi
		foreach($this->Item_kit->get_all()->result_array() as $row)
		{
			$Item_kit[$this->xss_clean($row['id'])] = $this->xss_clean($row['name']);
		}
		$data['Item_kit'] = $Item_kit;
		$data['suppliers'] = $suppliers;
		$data['selected_supplier'] = $item_info->supplier_id;

		$data['logo_exists'] = $item_info->pic_id != '';
		$images = glob("uploads/item_pics/" . $item_info->pic_id . ".*");
		$data['image_path'] = sizeof($images) > 0 ? base_url($images[0]) : '';
		// lay du lieu bao bi
		$data['quycach'] = get_quycach();
		$this->load->view('items/form', $data);
	}
    
	public function change_quantities($item_id = -1)
	{
		$this->load->helper('listype');
		$data = array();
		$data['start_date'] = date('d/m/Y');
		$item_info = $this->Item->get_info($item_id);
		foreach(get_object_vars($item_info) as $property => $value)
		{
			$item_info->$property = $this->xss_clean($value);
		}
		$data['ton_dau_ky'] = $this->Giftcard->hanghoantonkhodauky($item_id,date('Y-m-d'));
		$data['item_info'] = $item_info;
		$data['quycach'] = get_quycach();
		$this->load->view('items/change_quantities', $data);
	}

	public function inventory($item_id = -1)
	{
		$item_info = $this->Item->get_info($item_id);
		foreach(get_object_vars($item_info) as $property => $value)
		{
			$item_info->$property = $this->xss_clean($value);
		}
		$data['item_info'] = $item_info;

        $data['stock_locations'] = array();
        $stock_locations = $this->Stock_location->get_undeleted_all()->result_array();
        foreach($stock_locations as $location)
        {
			$location = $this->xss_clean($location);
			$quantity = $this->xss_clean($this->Item_quantity->get_item_quantity($item_id, $location['location_id'])->quantity);
		
            $data['stock_locations'][$location['location_id']] = $location['location_name'];
            $data['item_quantities'][$location['location_id']] = $quantity;
        }
		$this->load->view('items/form_inventory', $data);
	}
	
	public function count_details($item_id = -1)
	{
		$item_info = $this->Item->get_info($item_id);
		$data['item_info'] = $item_info;
		
		$arr_customer_infor = $this->Item->get_price_info_customer($item_info->item_number);
		$data['arr_customer_infor'] = $arr_customer_infor->result_array();
		//echo "<pre>";print_r($arr_customer_infor->result_array()); echo "</pre>";exit;
		//$quantity = 0;
        // lay theo gia khach hang
		$this->load->view('items/form_count_details', $data);
	}

	public function save_quantities($item_id = -1){
		$start_date = $this->input->post('input_date');
		$start_date = DateTime::createFromFormat('d/m/Y', $start_date)->format('Y-m-d H:i:s');
		$quantity = $this->input->post('new_quantitie');
		$coments = $this->input->post('description');
		if($this->Item->save_quantities($quantity, $start_date, $coments, $item_id))
		{
			$message = 'Cập nhật thành công';
            echo json_encode(array('success' => TRUE, 'message' => $message));
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => "Cập nhật lỗi", 'id' => -1));
		}
	}

	public function save($item_id = -1)
	{
		$upload_success = $this->_handle_image_upload();
		$upload_data = $this->upload->data();
		$idbaobi = 0;
		if($this->input->post('check_bao_bi')){
			$idbaobi = $this->input->post('items_kit');
			$arrbaobi = $this->Item_kit->get_info($idbaobi);
			
			if(!$arrbaobi->id){
				$arrbaobi = $this->Item_kit->get_info_bycode($this->input->post('item_number'));		
				if(!$arrbaobi->id){
					$item_kit_data = array(
						'name' => 'Bao bì '.$this->input->post('name'),
						'item_number' => $this->input->post('item_number'),
						'unit_weight' => $this->input->post('unit_weigh') == '' ? NULL : $this->input->post('unit_weigh'),
						'description' => $this->input->post('description')
					);
					$this->db->insert('items_packet', $item_kit_data);
					$idbaobi = $this->db->insert_id();
				}else{
					$idbaobi = $arrbaobi->id;
				}
			}else{
				$idbaobi = $arrbaobi->id;
			}
		}
		// cap nhat bao bi neu check
		//Save item data
		$item_data = array(
			'name' => $this->input->post('name'),
			'packet_id' => $idbaobi,
			'category' => $this->input->post('category'),
			'supplier_id' => $this->input->post('supplier_id') == '' ? NULL : $this->input->post('supplier_id'),
			'item_number' => $this->input->post('item_number') == '' ? NULL : $this->input->post('item_number'),
			'description' => $this->input->post('description'),
			'unit_weigh' => $this->input->post('unit_weigh') == '' ? NULL : $this->input->post('unit_weigh'),
			'status' => $this->input->post('is_status') != NULL
		);
		//var_dump($item_data); exit;
		if(!empty($upload_data['orig_name']))
		{
			// XSS file image sanity check
			if($this->xss_clean($upload_data['raw_name'], TRUE) === TRUE)
			{
				$item_data['pic_id'] = $upload_data['raw_name'];
			}
		}
		//var_dump($item_data); exit;
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);
		if($this->Item->save($item_data, $item_id))
		{
			$success = TRUE;
			$new_item = FALSE;
			//New item
			if($success && $upload_success)
            {
            	$message = 'Cap nhat thanh cong';

            	echo json_encode(array('success' => TRUE, 'message' => $message));
            }
            else
            {
            	$message = $this->xss_clean($upload_success ? $this->lang->line('items_error_adding_updating') . ' ' . $item_data['name'] : strip_tags($this->upload->display_errors())); 

            	echo json_encode(array('success' => FALSE, 'message' => $message, 'id' => $item_id));
            }
		}
		else//failure
		{
			$message = $this->xss_clean($this->lang->line('items_error_adding_updating') . ' ' . $item_data['name']);
			
			echo json_encode(array('success' => FALSE, 'message' => $message, 'id' => -1));
		}
	}
	
	public function check_item_number()
	{
		$exists = $this->Item->item_number_exists($this->input->post('item_number'), $this->input->post('item_id'));
		echo !$exists ? 'true' : 'false';
	}
	
	private function _handle_image_upload()
	{
		$this->load->helper('directory');

		$map = directory_map('./uploads/item_pics/', 1);

		// load upload library
		$config = array('upload_path' => './uploads/item_pics/',
			'allowed_types' => 'gif|jpg|png',
			'max_size' => '11024',
			'max_width' => '640',
			'max_height' => '480',
			'file_name' => sizeof($map) + 1
		);
		$this->load->library('upload', $config);
		$this->upload->do_upload('item_image');           
		
		return strlen($this->upload->display_errors()) == 0 || !strcmp($this->upload->display_errors(), '<p>'.$this->lang->line('upload_no_file_selected').'</p>');
	}

	public function remove_logo($item_id)
	{
		$item_data = array('pic_id' => NULL);
		$result = $this->Item->save($item_data, $item_id);

		echo json_encode(array('success' => $result));
	}

	public function save_inventory($item_id = -1)
	{	
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);
                $item_id = $this->input->post('item_number');
                $customer = $this->input->post('customer');
                $price = parse_decimals($this->input->post('price'));
		if($this->Item_quantity->savecustumerprice($item_id,$customer,$price))
		{
			$message = "Cập nhật thành công";
			
			echo json_encode(array('success' => TRUE, 'message' => $message, 'id' => $item_id));
		}
		else//failure
		{
			$message = "Cập nhật thất bại";
			
			echo json_encode(array('success' => FALSE, 'message' => $message, 'id' => -1));
		}
	}

	public function delete()
	{
		$items_to_delete = $this->input->post('ids');
		if(sizeof($items_to_delete) == 1){
			if($this->Item->delete_list($items_to_delete))
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
	// ----------------------------------------------------------------------------
	// --------------------- Quan ly gia ban --------------------------------------
	// ----------------------------------------------------------------------------
	public function manageprice($item_id = -1)
	{	
		$item_info = $this->Item->get_info($item_id);

		$data['item_info'] = $item_info;
		$this->item_lib->set_item_price($item_id);
		$data['table_headers'] = quan_ly_gia_theo_san_pham_headers();
		$this->load->view('items/manageprice', $data);
	}
	// ----------------------------------------------------------------------------
	// --------------------- Quan ly gia ban theo khach hang --------------------------------------
	// ----------------------------------------------------------------------------
	public function managepeople($item_id = -1)
	{	
		$item_info = $this->Item->get_info($item_id);

		$data['item_info'] = $item_info;
		$this->item_lib->set_item_price($item_id);
		$data['table_headers'] = quan_ly_gia_theo_khach_hang_headers();
		$this->load->view('items/managepricepeple', $data);
	}
}
?>
