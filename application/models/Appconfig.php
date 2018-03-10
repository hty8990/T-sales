<?php
class Appconfig extends CI_Model 
{
	public function exists($key)
	{
		$this->db->from('app_config');	
		$this->db->where('app_config.key', $key);

		return ($this->db->get()->num_rows() == 1);
	}
	
	public function exists_sochi($key)
	{
		$this->db->from('receivings');	
		$this->db->where('receiving_id', $key);

		return ($this->db->get()->num_rows() == 1);
	}
	public function exists_sothu($key)
	{
		$this->db->from('sales');	
		$this->db->where('sale_id', $key);

		return ($this->db->get()->num_rows() == 1);
	}
	
	public function get_all()
	{
		$this->db->from('app_config');
		$this->db->order_by('key', 'asc');

		return $this->db->get();		
	}
	public function get($key)
	{
		$query = $this->db->get_where('app_config', array('key' => $key), 1);

		if($query->num_rows() == 1)
		{
			return $query->row()->value;
		}

		return '';
	}
	
	public function save($key, $value)
	{
		$config_data = array(
			'key'   => $key,
			'value' => $value
		);

		if(!$this->exists($key))
		{
			return $this->db->insert('app_config', $config_data);
		}

		$this->db->where('key', $key);

		return $this->db->update('app_config', $config_data);
	}
	
	public function batch_save($data)
	{
		$success = TRUE;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		foreach($data as $key=>$value)
		{
			$success &= $this->save($key, $value);
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}
	public function deletesothuchi($key)
	{
		return $this->db->delete('app_config', array('key' => $key)); 
	}		
	public function delete($key)
	{
		return $this->db->delete('app_config', array('key' => $key)); 
	}
	
	public function delete_all()
	{
		return $this->db->empty_table('app_config'); 
	}
	public function search_sothu($search, $filters,$persion_type='', $rows = 0, $limit_from = 0, $sort = 'sale_time', $order = 'DESC')
	{
		$this->db->select('sales.payment_type,sales.sale_id as thu_chi_id,sales.sale_time as date_time,sales.pay_money as money, sales.comment,type, customer_id,supplier_id');
		$this->db->from('sales');
		if($persion_type == 'khach_hang'){
			$this->db->join('people', 'sales.customer_id = people.person_id');
		}else if($persion_type == 'nha_cung_cap'){
			$this->db->join('people', 'sales.supplier_id = people.person_id');
		}else if($persion_type == 'khac'){
			$this->db->where('type', 6);
		}
		//$this->db->join('people', 'sales.customer_id = people.person_id', 'right');
		//$this->db->join('people', 'sales.supplier_id = people.person_id', 'left');
		$this->db->where('pay_money <>', 0);
		// order by name of item
		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		//$this->db->group_start();	
		if($search <> '' and ($persion_type == 'khach_hang' or $persion_type == 'nha_cung_cap')){
			$this->db->like('people.full_name', $search);
		}	
			//$this->db->like('people.full_name', $search);
		//$this->db->group_end();
		$this->db->order_by('sale_time', 'DESC');
		

		if($rows > 0) 
		{	
			$this->db->limit($rows, $limit_from);
		}
		//echo $this->db->last_query();
		return $this->db->get();
	}
	public function total_sothu($search, $filters,$persion_type='')
	{
		$this->db->select('SUM(pay_money) as tongcuoi');
		$this->db->from('sales');
		if($persion_type == 'khach_hang'){
			$this->db->join('people', 'sales.customer_id = people.person_id');
		}else if($persion_type == 'nha_cung_cap'){
			$this->db->join('people', 'sales.supplier_id = people.person_id');
		}else if($persion_type == 'khac'){
			$this->db->where('type', 6);
		}
		//$this->db->join('people', 'sales.customer_id = people.person_id', 'right');
		//$this->db->join('people', 'sales.supplier_id = people.person_id', 'left');
		$this->db->where('pay_money <>', 0);
		// order by name of item
		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		//$this->db->group_start();	
		if($search <> '' and ($persion_type == 'khach_hang' or $persion_type == 'nha_cung_cap')){
			$this->db->like('people.full_name', $search);
		}
		$tongsothu = $this->db->get()->result_array();
		return $tongsothu[0]['tongcuoi'];
	}

	public function total_sochi($search, $filters,$persion_type)
	{
		$this->db->select('SUM(pay_money) as tongcuoi');
		$this->db->from('receivings');
		if($persion_type == 'khach_hang'){
			$this->db->join('people', 'receivings.customer_id = people.person_id');
		}else if($persion_type == 'nha_cung_cap'){
			$this->db->join('people', 'receivings.supplier_id = people.person_id');
		}else if($persion_type == 'khac'){
			$this->db->where('type', 6);
		}
		$this->db->where('pay_money <>', 0);
		// order by name of item
		$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		if($search <> '' and ($persion_type == 'khach_hang' or $persion_type == 'nha_cung_cap')){
			$this->db->like('people.full_name', $search);
		}	
		$tongsochi = $this->db->get()->result_array();
		return $tongsochi[0]['tongcuoi'];
	}
	public function get_found_rows_sothu($search,$filters,$persion_type)
	{
		return $this->search_sothu($search,$filters,$persion_type)->num_rows();
	}
	public function get_found_rows_sochi($search,$filters,$persion_type)
	{
		return $this->search_sochi($search,$filters,$persion_type)->num_rows();
	}
		/*
	Inserts or updates a item
	*/
	public function save_sothu(&$sales_data, $sale_id = FALSE)
	{
		if(!$sale_id || !$this->exists_sothu($sale_id, TRUE))
		{
			if($this->db->insert('sales', $sales_data))
			{
				$sales_data['sale_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}
		
		$this->db->where('sale_id', $sale_id);

		return $this->db->update('sales', $sales_data);
	}
	public function save_sochi(&$receivings_data, $receiving_id = FALSE)
	{
		if(!$receiving_id || !$this->exists_sochi($receiving_id, TRUE))
		{
			if($this->db->insert('receivings', $receivings_data))
			{
				$receiving_data['receiving_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}
		
		$this->db->where('receiving_id', $receiving_id);

		return $this->db->update('receivings', $receivings_data);
	}
	public function search_sochi($search, $filters,$persion_type, $rows = 0, $limit_from = 0, $sort = 'receiving_time', $order = 'asc')
	{
		$this->db->select('receivings.payment_type,receivings.receiving_id as thu_chi_id,receivings.receiving_time as date_time,receivings.pay_money as money, receivings.comment,type, customer_id,supplier_id');
		$this->db->from('receivings');
		if($persion_type == 'khach_hang'){
			$this->db->join('people', 'receivings.customer_id = people.person_id');
		}else if($persion_type == 'nha_cung_cap'){
			$this->db->join('people', 'receivings.supplier_id = people.person_id');
		}else if($persion_type == 'khac'){
			$this->db->where('type', 6);
		}
		$this->db->where('pay_money <>', 0);
		// order by name of item
		$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		if($search <> '' and ($persion_type == 'khach_hang' or $persion_type == 'nha_cung_cap')){
			$this->db->like('people.full_name', $search);
		}	
		if($rows > 0) 
		{	
			$this->db->limit($rows, $limit_from);
		}
		$this->db->order_by('receiving_time', 'DESC');
		//echo $this->db->last_query();
		return $this->db->get();
	}
	public function get_found_rows($search, $filters)
	{
		return $this->search($search, $filters)->num_rows();
	}
	public function get_info($item_id,$type)
	{
		if($type == 'sothu'){
			$this->db->select('sales.payment_type,sales.sale_time,sales.sale_id as thu_chi_id,pay_money as money,comment,type, customer_id,supplier_id');
			$this->db->from('sales');
			$this->db->where('sale_id', $item_id);
			return $this->db->get()->result_array();
			
		}else{
			$this->db->select('receivings.payment_type,receivings.receiving_time,receivings.receiving_id as thu_chi_id,pay_money as money,comment,type, customer_id,supplier_id');
			$this->db->from('receivings');
			$this->db->where('receiving_id', $item_id);
			return $this->db->get()->result_array();
		}
	}
}
?>