<?php
class ProductModel extends CI_Model
{	
	/*
	Determines if a given person_id is a customer
	*/
	public function exists($item)
	{
		$this->db->from('product_item');	
		$this->db->where('product_item.id', $item);
		
		return ($this->db->get()->num_rows() == 1);
	}

	public function search($search, $rows = 0, $limit_from = 0, $sort = 'last_name', $order = 'asc')
	{
		$this->db->from('product_item');
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('description', $search);
		$this->db->group_end();
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();	
	}

	public function get_found_rows($search)
	{
		$this->db->from('product_item');
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('description', $search);
		$this->db->group_end();

		return $this->db->get()->num_rows();
	}

	public function get_info($id)
	{
		$this->db->from('product_item');
		$this->db->where('product_item.id', $id);
		$query = $this->db->get();
		
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT a customer
			$item_obj = new stdClass();
			
			//Get all the fields from customer table
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('product_item') as $field)
			{
				$item_obj->$field = '';
			}
			
			return $item_obj;
		}
	}

	public function save(&$product_data, $id = FALSE)
	{		
		if(!$id || !$this->exists($id))
		{
			if($this->db->insert('product_item', $product_data))
			{
				$product_data['id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}
		
		$this->db->where('id', $id);
		//var_dump($person_data); exit;
		return $this->db->update('product_item', $product_data);
	}

	public function delete($id)
	{
		$this->db->delete('product_item', array('id' => $id));
		return true;
	 }
	 
	 public function getall(){
		$this->db->from('product_item');
		return $this->db->get()->result();	
	 }

	 public function getall_status(){
		$this->db->from('product_item');
		$this->db->where('status', 1);
		return $this->db->get()->result();	
	 }
}
?>
