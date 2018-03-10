<?php
class Promotion extends CI_Model
{
	/*
	Determines if a given item_id is an item kit
	*/
	public function exists($code)
	{
		$this->db->from('promotion');
		$this->db->where('promotion_code', $code);

		return ($this->db->get()->num_rows() == 1);
	}

	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('promotion');

		return $this->db->count_all_results();
	}
	
	/*
	Gets information about a particular item kit
	*/
	public function get_info($code)
	{
		$this->db->from('promotion');
		$this->db->where('promotion_code', $code);
		
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

	/*
	Gets information about multiple item kits
	*/
	public function get_multiple_info($code)
	{
		$this->db->from('promotion');
		$this->db->where_in('promotion_code', $code);
		$this->db->order_by('promotion_type', 'asc');

		return $this->db->get();
	}

	/*
	Inserts or updates an item kit
	*/
	public function save(&$item_kit_data, $code = FALSE)
	{
		if($code == -1)
		{

			if($this->db->insert('promotion', $code))
			{
				$item_kit_data['promotion_code'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('promotion_code', $code);

		return $this->db->update('promotion', $item_kit_data);

	}

	/*
	Deletes one item kit
	*/
	public function delete($code)
	{
		return $this->db->delete('promotion', array('promotion_code' => $code)); 	
	}

	/*
	Deletes a list of item kits
	*/
	public function delete_list($item_kit_ids)
	{
		$this->db->where_in('promotion_code', $item_kit_ids);

		return $this->db->delete('promotion');		
 	}

	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->from('promotion');

		$this->db->like('name', $search);
		$this->db->order_by('name', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => 'KIT ' . $row->item_kit_id, 'label' => $row->name);
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
			$suggestions[] = array('value' => $row->item_kit_id, 'label' => $row->item_number.' - '.$row->name);
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
	public function search($search, $rows=0)
	{
		echo "b"; exit;
		$this->db->from('promotion');
		$this->db->like('promotion_name', $search);

		$this->db->order_by('promotion_name', 'asc');

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();	
	}
	
	public function get_found_rows($search)
	{
		$this->db->from('items_packet');
		$this->db->like('name', $search);
		$this->db->or_like('description', $search);

		//KIT #
		if(stripos($search, 'KIT ') !== FALSE)
		{
			$this->db->or_like('item_kit_id', str_ireplace('KIT ', '', $search));
		}

		return $this->db->get()->num_rows();
	}
}
?>