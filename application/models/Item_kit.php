<?php
class Item_kit extends CI_Model
{
	/*
	Determines if a given item_id is an item kit
	*/
	public function exists($item_kit_id)
	{
		$this->db->from('items_packet');
		$this->db->where('id', $item_kit_id);

		return ($this->db->get()->num_rows() == 1);
	}

	public function exists_promotion_detail($item_kit_id)
	{
		$this->db->from('promotion_detail');
		$this->db->where('id', $item_kit_id);

		return ($this->db->get()->num_rows() == 1);
	}
	public function exists_price_packet_detail($item_kit_id)
	{
		$this->db->from('items_packet_prices');
		$this->db->where('id', $item_kit_id);

		return ($this->db->get()->num_rows() == 1);
	}
	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('items_packet');

		return $this->db->count_all_results();
	}
	
	/*
	Gets information about a particular item kit
	*/
	public function get_info($item_kit_id)
	{
		$this->db->from('items_packet');
		$this->db->where('id', $item_kit_id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_kit_id is NOT an item kit
			$item_obj = new stdClass();

			//Get all the fields from items table
			foreach($this->db->list_fields('items_packet') as $field)
			{
				$item_obj->$field = '';
			}

			return $item_obj;
		}
	}
	public function get_info_bycode($code)
	{
		$this->db->from('items_packet');
		$this->db->where('item_number', $code);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_kit_id is NOT an item kit
			$item_obj = new stdClass();

			//Get all the fields from items table
			foreach($this->db->list_fields('items_packet') as $field)
			{
				$item_obj->$field = '';
			}

			return $item_obj;
		}
	}
	public function get_info_Promotion($id)
	{
		$this->db->from('promotion');
		$this->db->where('id', $id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_kit_id is NOT an item kit
			$item_obj = new stdClass();

			//Get all the fields from items table
			foreach($this->db->list_fields('promotion') as $field)
			{
				$item_obj->$field = '';
			}

			return $item_obj;
		}
	}
	public function get_info_Packet_price($id)
	{
		$this->db->from('items_packet_prices');
		$this->db->where('id', $id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_kit_id is NOT an item kit
			$item_obj = new stdClass();

			//Get all the fields from items table
			foreach($this->db->list_fields('items_packet_prices') as $field)
			{
				$item_obj->$field = '';
			}

			return $item_obj;
		}
	}

	public function get_info_Promotion_detail($id)
	{
		$this->db->from('promotion_detail');
		$this->db->where('id', $id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_kit_id is NOT an item kit
			$item_obj = new stdClass();

			//Get all the fields from items table
			foreach($this->db->list_fields('promotion_detail') as $field)
			{
				$item_obj->$field = '';
			}

			return $item_obj;
		}
	}
	public function delete_pomotion_detail($item_ids)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$success = false;
		foreach($item_ids as $item_id)
		{
			$success = $this->db->delete('promotion_detail', array('id' => $item_id)); 
		}
		// set to 0 quantities

		return $success;
 	}
 	public function delete_packet_price_detail($item_ids)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$success = false;
		foreach($item_ids as $item_id)
		{
			$success = $this->db->delete('items_packet_prices', array('id' => $item_id)); 
		}
		// set to 0 quantities

		return $success;
 	}
 	public function save_packet_prices(&$item_data, $item_id = FALSE)
	{
		if(!$item_id || !$this->exists_price_packet_detail($item_id, TRUE))
		{
			if($this->db->insert('items_packet_prices', $item_data))
			{
				$item_data['item_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}
		
		$this->db->where('id', $item_id);

		return $this->db->update('items_packet_prices', $item_data);
	}
	public function save_promotion_detial(&$item_data, $item_id = FALSE)
	{
		if(!$item_id || !$this->exists_promotion_detail($item_id, TRUE))
		{
			if($this->db->insert('promotion_detail', $item_data))
			{
				$item_data['item_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}
		
		$this->db->where('id', $item_id);

		return $this->db->update('promotion_detail', $item_data);
	}
	public function get_all_Packet_prices($id,$search, $filters, $rows = 0, $limit_from = 0, $sort = 'items_packet_prices.start_date', $order = 'asc')
	{
		$this->db->select('items_packet_prices.*');
		$this->db->from('items_packet_prices');
		$this->db->join('items_packet', 'items_packet.id = items_packet_prices.packet_id', 'left');
		$this->db->where('items_packet_prices.packet_id', $id);
		$this->db->where('DATE(start_date) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		//$this->db->where('items.status', $filters['is_status']);
		if($search && $search !== '')
		{
			$this->db->like('items_packet_prices.description', $search);
		}		
		// order by name of item
		$this->db->order_by($sort,$order);

		if($rows > 0) 
		{	
			$this->db->limit($rows, $limit_from);
		}
		//echo $this->db->last_query();
		return $this->db->get();
	}
	public function get_found_Packet_prices_rows($item_id,$search, $filters)
	{
		return $this->get_all_Packet_prices($item_id,$search, $filters)->num_rows();
	}
	public function get_all_Promotion_detail($id,$search, $filters, $rows = 0, $limit_from = 0, $sort = 'promotion_detail.start_date', $order = 'asc')
	{
		$this->db->select('promotion_detail.*');
		$this->db->from('promotion_detail');
		$this->db->join('promotion', 'promotion.id = promotion_detail.promotion_id', 'left');
		$this->db->where('promotion_detail.promotion_id', $id);
		$this->db->where('DATE(start_date) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		//$this->db->where('items.status', $filters['is_status']);
		if($search && $search !== '')
		{
			$this->db->like('promotion_detail.description', $search);
		}		
		// order by name of item
		$this->db->order_by($sort,$order);

		if($rows > 0) 
		{	
			$this->db->limit($rows, $limit_from);
		}
		//echo $this->db->last_query();
		return $this->db->get();
	}
	public function get_found_Promotion_detail_rows($item_id,$search, $filters)
	{
		return $this->get_all_Promotion_detail($item_id,$search, $filters)->num_rows();
	}
	/*
	Gets information about multiple item kits
	*/
	public function get_multiple_info($item_kit_ids)
	{
		$this->db->from('items_packet');
		$this->db->where_in('id', $item_kit_ids);
		$this->db->order_by('name', 'asc');

		return $this->db->get();
	}

	/*
	Inserts or updates an item kit
	*/
	public function save(&$item_kit_data, $item_kit_id = FALSE)
	{
		if($item_kit_id == -1)
		{

			if($this->db->insert('items_packet', $item_kit_data))
			{
				$item_kit_data['item_kit_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('id', $item_kit_id);

		return $this->db->update('items_packet', $item_kit_data);

	}
	public function save_promotion(&$promotion_data, $id = FALSE)
	{
		if($id == -1)
		{

			if($this->db->insert('promotion', $promotion_data))
			{
				//$promotion_data['promotion_code'] = $this->db->insert_id();

				return $this->db->insert_id();
			}

			return FALSE;
		}

		$this->db->where('id', $id);

		return $this->db->update('promotion', $promotion_data);

	}
	public function update_sort_promotion($order, $id)
	{
		// lay tat ca ban gi khac de cap nhat
		$this->db->from('promotion');
		//$this->db->where('id <>', $id);
		$this->db->where('sort >=', $order);
		$this->db->where('promotion_type', 'khac');
		$this->db->order_by('sort', 'asc');
		$i =1;
		foreach($this->db->get()->result() as $row)
		{
			$this->db->where('id', $row->id);
			$item_data = array(
				'sort' => $order+$i
			);
			$this->db->update('promotion', $item_data);
			$i++;
		}
		// cap nhat cac ban ghi cua loai tat ca
		$this->db->from('promotion');
		//$this->db->where('id <>', $id);
		$this->db->where('sort >=', $order);
		$this->db->where('promotion_type', 'all');
		$this->db->order_by('sort', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$this->db->where('id', $row->id);
			$item_data = array(
				'sort' => $order+$i
			);
			$this->db->update('promotion', $item_data);
			$i++;
		}
		// cap nhat cac ban ghi cua loai thuc_an_dam_dac
		$this->db->from('promotion');
		//$this->db->where('id <>', $id);
		$this->db->where('sort >=', $order);
		$this->db->where('promotion_type', 'thuc_an_dam_dac');
		$this->db->order_by('sort', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$this->db->where('id', $row->id);
			$item_data = array(
				'sort' => $order+$i
			);
			$this->db->update('promotion', $item_data);
			$i++;
		}
		// cap nhat cac ban ghi cua loai thuc_an_hon_hop
		$this->db->from('promotion');
		//$this->db->where('id <>', $id);
		$this->db->where('sort >=', $order);
		$this->db->where('promotion_type', 'thuc_an_hon_hop');
		$this->db->order_by('sort', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$this->db->where('id', $row->id);
			$item_data = array(
				'sort' => $order+$i
			);
			$this->db->update('promotion', $item_data);
			$i++;
		}
	}
	/*
	Deletes one item kit
	*/
	public function delete($item_kit_id)
	{
		return $this->db->delete('items_packet', array('id' => $id)); 	
	}

	/*
	Deletes a list of item kits
	*/
	public function delete_list($item_kit_ids)
	{
		$this->db->where_in('packet_id', $item_kit_ids);
		$this->db->delete('items_packet_prices');
		$this->db->where_in('id', $item_kit_ids);
		return $this->db->delete('items_packet');		
 	}
 	public function delete_promotion_list($item_kit_ids)
	{
		//echo $item_kit_ids; exit;
		// xoa thang con
		$this->db->where_in('promotion_id', $item_kit_ids);
		$this->db->delete('promotion_detail');	
		$this->db->where_in('id', $item_kit_ids);
		return $this->db->delete('promotion');		
 	}
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->from('items_packet');

		//KIT #
		if(stripos($search, 'KIT ') !== FALSE)
		{
			$this->db->like('id', str_ireplace('KIT ', '', $search));
			$this->db->order_by('item_kit_id', 'asc');

			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => 'KIT '. $row->item_kit_id, 'label' => 'KIT ' . $row->item_kit_id);
			}
		}
		else
		{
			$this->db->like('name', $search);
			$this->db->order_by('name', 'asc');

			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => 'KIT ' . $row->id, 'label' => $row->name);
			}
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}
	public function get_search_recevie($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->from('items_packet');
		$this->db->like('item_number', $search);
		$this->db->or_like('name', $search);
		$this->db->order_by('item_number', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->id, 'label' => $row->item_number.' - '.$row->name);
		}
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}
	public function get_all_suggestions()
	{
		$suggestions = array();

		$this->db->from('t_quy_cach');
		$this->db->order_by('key', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->key);
		}
		return $suggestions;
	}
	/*
	Perform a search on items
	*/
	public function search($search,$filters, $rows=0, $limit_from=2, $sort='items_packet.name', $order='asc')
	{
		$this->db->select('items_packet.id,items_packet.name,
		items_packet.item_number,	
		items.item_number as ma_san_pham,
		items_packet.unit_weight,
		items_packet.description,
		items_packet.quantities');
		$this->db->from('items_packet');
		$this->db->join('items', 'items.packet_id = items_packet.id', 'left');
		$this->db->like('items_packet.name', $search);
		$this->db->or_like('items_packet.description', $search);
		$this->db->or_like('items_packet.item_number', $search);
		$this->db->order_by($sort, $order);
		$this->db->group_by('items_packet.id');
		
		// order by name of item
		$this->db->order_by('items_packet.item_number');
		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();	
	}
	public function get_all($limit_from = 0, $rows = 0)
	{
		$this->db->from('items_packet');			
		$this->db->order_by('unit_weight', 'asc');
		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();		
	}

	public function get_all_item_kit($search = "")
	{
		$this->db->from('items_packet');			
		$this->db->order_by('unit_weight', 'asc');
		if(!empty($search) && $search !== '')
		{
			$this->db->group_start();
				$this->db->or_like('items_packet.name', $search);
				$this->db->or_like('items_packet.item_number', $search);
			$this->db->group_end();
		}

		return $this->db->get()->result();		
	}

	public function search_promotion($search, $rows=0, $limit_from=0, $sort='sort', $order='asc')
	{
		$this->db->from('promotion');
		if($search == 'gia_rieng'){
			$this->db->like('check_all', 1);
		}else{
			if(!empty($search))
			{
				$this->db->group_start();
					$this->db->like('promotion_name', $search);
					$this->db->or_like('description', $search);
					$this->db->or_like('type', $search);
					$this->db->or_like('promotion_type', $search);
					$this->db->or_like('item_promotion', $search);
				$this->db->group_end();
			}
		}
		
		
		$this->db->order_by($sort, $order);


		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();	
	}
	public function get_found_rows($search,$filters)
	{
		return $this->search($search, $filters)->num_rows();
	}

	// lay gia hien tai theo khoang thoi gian
	public function get_packet_price_by_time($packet_id,$start_date)
	{
		$input_prices = '';
		$SQL = "select * from t_items_packet_prices WHERE `packet_id` = ".$packet_id." and DATEDIFF('".$start_date."',`start_date`) >= 0 order by DATEDIFF('".$start_date."',`start_date`) LIMIT 1 ";
		$query = $this->db->query($SQL);
		$arrResult = $query->result_array();
		if($arrResult){
			$input_prices = $arrResult[0]['input_prices'];
		}
		return array(
				'input_prices' => $input_prices
			);
	}

	public function save_quantities_packet($quantity,$start_date, $coments, $item_id = FALSE)
	{
		$receivings_data = array(
			'receiving_time' => $start_date,
			'customer_id'	 => 0,
			'employee_id'	 => 1,
			'comment'		 => $coments,
			'payment_type' 	 => '',
			'order_money'	 => 0,
			'pay_money'	 => 0,
			'type' => 10,
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
}
?>