<?php
class Item extends CI_Model
{
	/*
	Determines if a given item_id is an item
	*/
	public function exists($item_id, $ignore_deleted = FALSE, $status = TRUE)
	{
		$this->db->from('items');
		$this->db->where('CAST(id AS CHAR) = ', $item_id);
		if($ignore_deleted == FALSE)
		{
			$this->db->where('status', $status);
		}

		return ($this->db->get()->num_rows() == 1);
	}
	public function exists_prices($item_id, $ignore_deleted = FALSE, $status = TRUE)
	{
		$this->db->from('items_prices');
		$this->db->where('CAST(id AS CHAR) = ', $item_id);
		if($ignore_deleted == FALSE)
		{
			$this->db->where('status', $status);
		}

		return ($this->db->get()->num_rows() == 1);
	}
	public function exists_prices_customer($item_id, $ignore_deleted = FALSE, $status = TRUE)
	{
		$this->db->from('t_items_prices_customer');
		$this->db->where('CAST(id AS CHAR) = ', $item_id);

		return ($this->db->get()->num_rows() == 1);
	}
	/*
	Determines if a given item_number exists
	*/
	public function item_number_exists($item_number, $item_id = '')
	{
		$this->db->from('items');
		$this->db->where('item_number', $item_number);
		if(!empty($item_id))
		{
			$this->db->where('id !=', $item_id);
		}

		return ($this->db->get()->num_rows() == 1);
	}

	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('items');
		$this->db->where('status', 1);

		return $this->db->count_all_results();
	}

	/*
	Get number of rows
	*/
	public function get_found_rows($search, $filters)
	{
		return $this->search($search, $filters)->num_rows();
	}

	/*
	Perform a search on items
	*/
	public function search($search, $filters, $rows = 0, $limit_from = 0, $sort = 'items.item_number', $order = 'asc')
	{
		$this->db->select('items.*,items_packet.item_number as ma_bao_bi,suppliers.agency_name,item_quantities.quantity,item_quantities.quantity_return');
		$this->db->from('items');
		$this->db->join('suppliers', 'suppliers.person_id = items.supplier_id', 'left');
		$this->db->join('item_quantities', 'item_quantities.item_id = items.id', 'left');
		$this->db->join('items_packet', 'items_packet.id = items.packet_id', 'left');
		//$this->db->where('items.status', $filters['is_status']);
		if($search && $search !== '')
		{
			$this->db->like('items.item_number', $search);
			$this->db->or_like('items.name', $search);
		}
		// avoid duplicated entries with same name because of inventory reporting multiple changes on the same item in the same date range
		$this->db->group_by('items.id');
		
		// order by name of item
		$this->db->order_by($sort,$order);

		if($rows > 0) 
		{	
			$this->db->limit($rows, $limit_from);
		}
		//echo $this->db->last_query();
		return $this->db->get();
	}
	
	/*
	Returns all the items
	*/
	public function get_all($stock_location_id = -1, $rows = 0, $limit_from = 0)
	{
		$this->db->from('items');
		// order by name of item
		$this->db->order_by('items.name', 'asc');
		return $this->db->get()->result();
	}

	public function get_all_item($search,$category)
	{
		$this->db->from('items');
		// order by name of item
		if($category !==''){
			$this->db->where('items.category', $category);
		}
		if(!empty($search) && $search !== '')
		{
			$this->db->group_start();
				$this->db->or_like('items.name', $search);
				$this->db->or_like('items.item_number', $search);
			$this->db->group_end();
		}
		$this->db->order_by('items.name', 'asc');
		return $this->db->get()->result();
	}

	/*
	Gets information about a particular item
	*/
	public function get_info($item_id)
	{
		$this->db->select('items.*');
		$this->db->select('suppliers.agency_name as company_name');
		$this->db->from('items');
		$this->db->join('suppliers', 'suppliers.person_id = items.supplier_id', 'left');
		$this->db->where('id', $item_id);

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_id is NOT an item
			$item_obj = new stdClass();

			//Get all the fields from items table
			foreach($this->db->list_fields('items') as $field)
			{
				$item_obj->$field = '';
			}

			return $item_obj;
		}
	}
	// lay gia khach hang
	public function get_price_info_customer($item_id)
	{
		$this->db->select('*');
		$this->db->from('price_custumer');
		$this->db->join('people', 'people.person_id = price_custumer.pk_custumer');
		$this->db->where('pk_item', $item_id);

		return $this->db->get();
	}
	// lay gia san pham
	public function get_price_info_items($item_id)
	{
		$this->db->select('items_prices.*');
		$this->db->from('items_prices');
		$this->db->join('items', 'items.id = items_prices.item_id');
		$this->db->where('items_prices.id', $item_id);

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_id is NOT an item
			$item_obj = new stdClass();

			//Get all the fields from items table
			foreach($this->db->list_fields('items_prices') as $field)
			{
				$item_obj->$field = '';
			}

			return $item_obj;
		}
	}

	public function get_price_info_first_items($item_id){
		$this->db->select('sale_price');
		$this->db->from('items_prices');
		$this->db->join('items', 'items.id = items_prices.item_id');
		$this->db->where('items.id', $item_id);
		$this->db->limit(1);
		$this->db->order_by('start_date', 'desc');

		return $this->db->get()->result_array();
	}

	// lay gia san pham
	public function get_price_customer_info_items($item_id)
	{
		$this->db->select('items_prices_customer.*,people.full_name');
		$this->db->from('items_prices_customer');
		$this->db->join('items', 'items.id = items_prices_customer.item_id', 'left');
		$this->db->join('customers', 'customers.person_id = items_prices_customer.customer_id', 'left');
		$this->db->join('people', 'people.person_id = items_prices_customer.customer_id', 'left');
		$this->db->where('items_prices_customer.id', $item_id);

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_id is NOT an item
			$item_obj = new stdClass();

			//Get all the fields from items table
			foreach($this->db->list_fields('items_prices_customer') as $field)
			{
				$item_obj->$field = '';
			}
			$item_obj->full_name = '';
			return $item_obj;
		}
	}
	/*
	Get an item id given an item number
	*/
	public function get_item_id($item_number)
	{
		$this->db->from('items');
		$this->db->join('suppliers', 'suppliers.person_id = items.supplier_id', 'left');
		$this->db->where('item_number', $item_number);
		$this->db->where('items.status', 1);
        
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row()->item_id;
		}

		return FALSE;
	}

	/*
	Gets information about multiple items
	*/
	public function get_multiple_info($item_ids, $location_id)
	{
		$this->db->from('items');
		$this->db->join('suppliers', 'suppliers.person_id = items.supplier_id', 'left');
		$this->db->join('item_quantities', 'item_quantities.item_id = items.id', 'left');
		$this->db->where('location_id', $location_id);
		$this->db->where_in('items.id', $item_ids);

		return $this->db->get();
	}

	/*
	Inserts or updates a item
	*/
	public function save(&$item_data, $item_id = FALSE)
	{
		if(!$item_id || !$this->exists($item_id, TRUE))
		{
			if($this->db->insert('items', $item_data))
			{
				$item_data['item_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}
		
		$this->db->where('id', $item_id);

		return $this->db->update('items', $item_data);
	}

	public function save_quantities($quantity,$start_date, $coments, $item_id = FALSE)
	{
		$receivings_data = array(
			'receiving_time' => $start_date,
			'customer_id'	 => 0,
			'employee_id'	 => 1,
			'comment'		 => $coments,
			'payment_type' 	 => '',
			'order_money'	 => 0,
			'pay_money'	 => 0,
			'type' => 0,
		);
		$this->db->trans_start();
		$this->db->insert('receivings', $receivings_data);
		$receivings_id = $this->db->insert_id();
		$receivings_items_data = array(
			'receiving_id' => $receivings_id,
			'item_id' => $item_id,
			'line' => 0,
			'quantity' => $quantity,
			'unit_weigh' => 0,
			'input_prices' => 0,
			'item_location' => 1
		);
		$this->db->insert('receivings_items', $receivings_items_data);
		$this->db->trans_complete();
		return true;
	}

	/*
	Updates multiple items at once
	*/
	public function update_multiple($item_data, $item_ids)
	{
		$this->db->where_in('id', explode(':', $item_ids));

		return $this->db->update('items', $item_data);
	}

	/*
	Deletes one item
	*/
	public function delete($item_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		// set to 0 quantities
		$this->Item_quantity->reset_quantity($item_id);
		$this->db->where('id', $item_id);
		$success = $this->db->update('items', array('status'=>0));
		
		$this->db->trans_complete();
		
		$success &= $this->db->trans_status();

		return $success;
	}
	

	/*
	Deletes a list of items
	*/
	public function delete_list($item_ids)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$success = false;
		foreach($item_ids as $item_id)
		{
			$this->db->delete('item_quantities', array('item_id' => $item_id)); 
			$success = $this->db->delete('items', array('id' => $item_id)); 
		}
		// set to 0 quantities

		return $success;
 	}

 	public function delete_price_items($item_ids)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$success = false;
		foreach($item_ids as $item_id)
		{
			$success = $this->db->delete('items_prices', array('id' => $item_id)); 
		}
		// set to 0 quantities

		return $success;
 	}
 	public function delete_price_customer_items($item_ids)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$success = false;
		foreach($item_ids as $item_id)
		{
			$success = $this->db->delete('items_prices_customer', array('id' => $item_id)); 
		}
		// set to 0 quantities

		return $success;
 	}
	public function get_search_suggestions($search, $filters = array('is_deleted' => TRUE, 'search_custom' => FALSE), $unique = FALSE, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('id, name, item_number');
		$this->db->from('items');
		$this->db->where('status', $filters['is_deleted']);
		$this->db->like('name', $search);
		$this->db->or_like('item_number', $search);
		$this->db->order_by('item_number', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->id, 'label' => $row->item_number.' - '.$row->name,'item_number' => $row->item_number);
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}

		return $suggestions;
	}

	public function get_category_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('category');
		$this->db->from('items');
		$this->db->like('category', $search);
		$this->db->where('status', 1);
		$this->db->order_by('category', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->category);
		}

		return $suggestions;
	}
	
	public function get_location_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('location');
		$this->db->from('items');
		$this->db->like('location', $search);
		$this->db->where('status', 1);
		$this->db->order_by('location', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->location);
		}
	
		return $suggestions;
	}

	public function get_custom_suggestions($search, $field_no)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('custom'.$field_no);
		$this->db->from('items');
		$this->db->like('custom'.$field_no, $search);
		$this->db->where('status', 1);
		$this->db->order_by('custom'.$field_no, 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$row_array = (array) $row;
			$suggestions[] = array('label' => $row_array['custom'.$field_no]);
		}
	
		return $suggestions;
	}

	public function get_categories()
	{
		$this->db->select('category');
		$this->db->from('items');
		$this->db->where('status', 1);
		$this->db->distinct();
		$this->db->order_by('category', 'asc');

		return $this->db->get();
	}
	
	public function hanghoanhapkhobaobi_found_rows($customer_id,$search,$start_date,$end_date)
	{
		return $this->hanghoanhapkhobaobi($customer_id,$search,$start_date,$end_date)->num_rows();
	}
	
	public function chiphikhac($customer_id,$type,$start_date,$end_date)
	{
		$this->db->select('SUM(pay_money) as chiphikhac');
		$this->db->from('receivings');
		$this->db->where('DATE(receiving_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		$this->db->where('receivings.type', 4);
		if(!empty($search) && $search !== '')
		{
			$this->db->group_start();
				$this->db->like('customer_last_name', $search);
				$this->db->or_like('customer_first_name', $search);
				$this->db->or_like('customer_name', $search);
				$this->db->or_like('customer_company_name', $search);
			$this->db->group_end();
		}

		$this->db->order_by('receiving_time', 'desc');
		return $this->db->get();
	}
	public function doanhthubanhang($customer_id,$type,$start_date,$end_date)
	{
		$this->db->select('SUM(order_money) as doanhthubanhang');
		$this->db->from('sales');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_sales_items', 't_sales_items.sale_id = sales.sale_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		$this->db->where('sales.type', 1);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));

		if(!empty($search) && $search !== '')
		{
			$this->db->group_start();
				$this->db->like('customer_last_name', $search);
				$this->db->or_like('customer_first_name', $search);
				$this->db->or_like('customer_name', $search);
				$this->db->or_like('customer_company_name', $search);
			$this->db->group_end();
		}

		$this->db->group_by('t_people.person_id ');
		$this->db->order_by('sale_time', 'desc');
		return $this->db->get();
	}
	public function giavonhanghoa($customer_id,$type,$start_date,$end_date)
	{
		$this->db->select('SUM(t_salaes_items.input_prices) as giavonhanghoa');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id','LEFT');
		$this->db->where('sales.type', 1);
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		
		if(!empty($search) && $search !== '')
		{
			$this->db->group_start();
				$this->db->like('customer_last_name', $search);
				$this->db->or_like('customer_first_name', $search);
				$this->db->or_like('customer_name', $search);
				$this->db->or_like('customer_company_name', $search);
			$this->db->group_end();
		}
		$this->db->order_by('sale_time', 'desc');
		return $this->db->get();
	}
	public function hanghoanhapkhosanpham_found_rows($customer_id,$search,$start_date,$end_date)
	{
		return $this->hanghoanhapkhosanpham($customer_id,$search,$start_date,$end_date)->num_rows();
	}

	public function baobitonkhodauky($item_id,$start_date)
	{
		// tong hang nhap ky truoc
		$this->db->select('SUM(quantity) as nhapkytruoc');
		$this->db->from('receivings_items');
		$this->db->join('receivings', 'receivings_items.receiving_id = receivings.receiving_id');
		$this->db->where('receivings_items.item_id', $item_id);
		$this->db->where('receiving_time <=',$start_date);
		$this->db->group_start();
		$this->db->where('type', 2);
		$this->db->or_where('type', 10);
		$this->db->group_end();
		//$this->db->or_where('type', 0);
		$kytruoc = $this->db->get()->result_array();
		$soluongnhapkytruoc = $kytruoc[0]['nhapkytruoc'];
		// ban ky truoc
		$this->db->select('SUM(quantity + quantity_give - quantity_loan + quantity_loan_return) as bankytruoc');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'items.id = sales_items.item_id');
		$this->db->where('items.packet_id', $item_id);
		$this->db->where('sale_time <=',$start_date);
		$this->db->where('type', 1);
		$bankytruoc = $this->db->get()->result_array();
		$soluongbankytruoc = $bankytruoc[0]['bankytruoc'];
		// tra lai ky truoc
		$this->db->select('SUM(quantity) as tralaikytruoc');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'items.id = sales_items.item_id');
		$this->db->where('items.packet_id', $item_id);
		$this->db->where('sale_time <=',$start_date);
		$this->db->where('type', 2);
		$tralaikytruoc = $this->db->get()->result_array();
		$soluongtralaikytruoc = $tralaikytruoc[0]['tralaikytruoc'];
		//echo $soluongbankytruoc; exit;
		$soluongtonkytruoc = $soluongnhapkytruoc - ($soluongbankytruoc - $soluongtralaikytruoc);
		return $soluongtonkytruoc;
	}
	
	public function hanghoanxuatkhosanpham_found_rows($customer_id,$search,$start_date,$end_date,$category)
	{
		return $this->hanghoanxuatkhosanpham($customer_id,$search,$start_date,$end_date,$category)->num_rows();
	}
	
	public function search_prices($item_id,$search, $filters, $rows = 0, $limit_from = 0, $sort = 'start_date', $order = 'asc')
	{
		$this->db->select('items_prices.*');
		$this->db->from('items_prices');
		$this->db->join('items', 'items.id = items_prices.item_id', 'left');
		$this->db->where('items_prices.item_id', $item_id);
		$this->db->where('DATE(start_date) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		//$this->db->where('items.status', $filters['is_status']);
		if($search && $search !== '')
		{
			$this->db->like('items_prices.description', $search);
		}
		if($sort){
			$this->db->order_by($sort,$order);
		}else{
			$this->db->order_by('start_date','desc');
		}		
		// order by name of item
		

		if($rows > 0) 
		{	
			$this->db->limit($rows, $limit_from);
		}
		//echo $this->db->last_query();
		return $this->db->get();
	}
	
	public function get_found_search_prices_rows($item_id,$search, $filters)
	{
		return $this->search_prices($item_id,$search, $filters)->num_rows();
	}

	public function save_prices(&$item_data, $item_id = FALSE)
	{
		if(!$item_id || !$this->exists_prices($item_id, TRUE))
		{
			if($this->db->insert('items_prices', $item_data))
			{
				$item_data['item_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}
		
		$this->db->where('id', $item_id);

		return $this->db->update('items_prices', $item_data);
	}
	public function save_prices_customer(&$item_data, $item_id = FALSE)
	{
		if(!$item_id || !$this->exists_prices_customer($item_id, TRUE))
		{
			if($this->db->insert('items_prices_customer', $item_data))
			{
				$item_data['item_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}
		
		$this->db->where('id', $item_id);

		return $this->db->update('items_prices_customer', $item_data);
	}
	public function search_prices_customer($item_id,$search, $filters, $rows = 0, $limit_from = 0, $sort = 'items_prices_customer.start_date', $order = 'desc')
	{
		$this->db->select('items_prices_customer.*,people.full_name');
		$this->db->from('items_prices_customer');
		$this->db->join('items', 'items.id = items_prices_customer.item_id', 'left');
		$this->db->join('customers', 'customers.person_id = items_prices_customer.customer_id', 'left');
		$this->db->join('people', 'people.person_id = items_prices_customer.customer_id', 'left');
		$this->db->where('items_prices_customer.item_id', $item_id);
		$this->db->where('DATE(start_date) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		//$this->db->where('items.status', $filters['is_status']);
		if($search && $search !== '')
		{
			$this->db->like('items_prices_customer.description', $search);
		}
		// avoid duplicated entries with same name because of inventory reporting multiple changes on the same item in the same date range
		$this->db->group_by('items_prices_customer.id');
		
		// order by name of item
		$this->db->order_by($sort,$order);

		if($rows > 0) 
		{	
			$this->db->limit($rows, $limit_from);
		}
		//echo $this->db->last_query();
		return $this->db->get();
	}

	public function get_found_search_prices_customer_rows($item_id,$search, $filters)
	{
		return $this->search_prices_customer($item_id,$search, $filters)->num_rows();
	}
	// lay gia hien tai theo khoang thoi gian
	public function get_prices_by_time_customer($item_id,$start_date,$customer_id='')
	{
		$sale_price = '';
		$input_prices = '';
		if($customer_id !== '' and $customer_id > 0){
			// gia theo khach hang
			$SQL = "select sale_price from t_items_prices_customer WHERE `item_id` = ".$item_id." and `customer_id` = ".$customer_id." and DATEDIFF('".$start_date."',`start_date`) >= 0 and DATEDIFF('".$start_date."',`end_date`) <= 0 order by DATEDIFF('".$start_date."',`start_date`) LIMIT 1 ";
			$query = $this->db->query($SQL);
			$arrCustomer = $query->result_array();
			if($arrCustomer){
				$sale_price = $arrCustomer[0]['sale_price'];
			}
		}
		$SQL = "select * from t_items_prices WHERE `item_id` = ".$item_id." and DATEDIFF('".$start_date."',`start_date`) >= 0 order by DATEDIFF('".$start_date."',`start_date`) LIMIT 1 ";
		$query = $this->db->query($SQL);
		$arrResult = $query->result_array();
		if($arrResult){
			if($sale_price == ''){
				$sale_price = $arrResult[0]['sale_price'];
			}
			$input_prices = $arrResult[0]['input_prices'];
		}
		return array(
				'sale_price' => $sale_price,
				'input_prices' => $input_prices,
			);
	}
}
?>