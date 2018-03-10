<?php
class Customer extends Person
{	
	/*
	Determines if a given person_id is a customer
	*/
	public function exists($person_id)
	{
		$this->db->from('customers');	
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.person_id', $person_id);
		
		return ($this->db->get()->num_rows() == 1);
	}

	public function countBrithday(){
		//
		$countdate = $this->config->item('number_brithday');
		$SQL = "select count(*) as tongso FROM  t_people WHERE  DATE_ADD(birthday, 
				INTERVAL YEAR(CURDATE())-YEAR(birthday)
				+ IF(DAYOFYEAR(CURDATE()) >= DAYOFYEAR(STR_TO_DATE( CONCAT(YEAR(CURDATE()), '-', MONTH(birthday), '-', DAY(birthday) ), '%Y-%m-%d' )),0,0)
				YEAR)  
				BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ".$countdate." DAY);";
		$query = $this->db->query($SQL);
		$arrCustomer = $query->result_array();
		return $arrCustomer;
	}

	public function countBrithdayByid($id){
		//
		$countdate = $this->config->item('number_brithday');
		$SQL = "select DAY(CURDATE()) as day, DAY(birthday) as birthday FROM  t_people WHERE  person_id = ".$id." and  DATE_ADD(birthday, 
				INTERVAL YEAR(CURDATE())-YEAR(birthday)
				+ IF(DAYOFYEAR(CURDATE()) >= DAYOFYEAR(STR_TO_DATE( CONCAT(YEAR(CURDATE()), '-', MONTH(birthday), '-', DAY(birthday) ), '%Y-%m-%d' )),0,0)
				YEAR)  
				BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ".$countdate." DAY);";
		$query = $this->db->query($SQL);
		$arrCustomer = $query->result_array();
		return $arrCustomer;
	}


	/*
	Checks if account number exists
	*/
	public function account_number_exists($account_number, $person_id = '')
	{
		$this->db->from('customers');
		$this->db->where('account_number', $account_number);

		if(!empty($person_id))
		{
			$this->db->where('person_id !=', $person_id);
		}

		return ($this->db->get()->num_rows() == 1);
	}	
	/*
	Checks if account number exists
	*/
	public function getall_promotion($person_id = '')
	{
		$this->db->from('promotion');
		$this->db->order_by('promotion_sttt', 'asc');
		$arrResult = $this->db->get();

		foreach($arrResult->result() as $row)
		{
			$suggestions[] = array('promotion_type' => $row->promotion_type
				,'promotion_code' => $row->promotion_code
				,'promotion_name' => $row->promotion_name
				,'promotion_pecen' => $row->promotion_pecen
				,'promotion_kg' => $row->promotion_kg
				,'promotion_sttt' => $row->promotion_sttt
				,'item_promotion' => $row->item_promotion
			);
		}
		return $suggestions;
	}
        // lay mat hang voi gia
        public function getcustumer_prince($person_id)
	{
		$suggestions = "";
        if($person_id !== ""){
                $this->db->from('price_custumer');
                $this->db->where('pk_custumer', $person_id);
		$arrResult = $this->db->get();
		foreach($arrResult->result() as $row)
		{
			$suggestions[] = array('pk_item' => $row->pk_item
				,'pk_custumer' => $row->pk_custumer
				,'c_money' => $row->c_money
			);
		}
		return $suggestions;
            }else{
                return "";
            }
		
	}
	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('customers');
		return $this->db->count_all_results();
	}
	
	/*
	Returns all the customers
	*/
	public function get_all($rows = 0, $limit_from = 0)
	{
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		//$this->db->order_by('last_name', 'asc');

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();		
	}
	public function get_all_customer($id=-1)
	{
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		//$this->db->order_by('last_name', 'asc');

		if($id > 0)
		{
			$this->db->where('customers.person_id', $id);
		}

		return $this->db->get()->result();		
	}
	public function get_info_by_sales($customer_id)
	{
		$this->db->from('customers');
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.person_id', $customer_id);

		return $this->db->get()->result_array();
	}
	public function get_info_by_taiche($customer_id)
	{
		$this->db->from('suppliers');
		$this->db->join('people', 'people.person_id = suppliers.person_id');
		$this->db->where('suppliers.person_id', $customer_id);

		return $this->db->get()->result_array();
	}
	/*
	Gets information about a particular customer
	*/
	public function get_info($customer_id)
	{
		$this->db->from('customers');
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.person_id', $customer_id);
		$query = $this->db->get();
		
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT a customer
			$person_obj = parent::get_info(-1);
			
			//Get all the fields from customer table
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('customers') as $field)
			{
				$person_obj->$field = '';
			}
			
			return $person_obj;
		}
	}
	
	/*
	Gets total about a particular customer
	*/
	public function get_totals($customer_id)
	{
		$this->db->select('SUM(pay_money) AS total');
		$this->db->from('sales');
		//$this->db->join('sales_payments', 'sales.sale_id = sales_payments.sale_id');
		$this->db->where('sales.customer_id', $customer_id);

		return $this->db->get()->row();
	}
	/*
	Gets total about a particular customer
	*/
	public function get_debt_customer($customer_id,$start_date)
	{
		$this->db->select('SUM(order_money - pay_money) AS debt');
		$this->db->from('sales');
		//$this->db->join('sales_payments', 'sales.sale_id = sales_payments.sale_id');
		$this->db->where('sales.customer_id', $customer_id);
		//if($start_date!==''){
		$this->db->where('sales.sale_time <', $start_date);
		//}
		
		$arrResult = $this->db->get()->row();

		if(isset($arrResult->debt) && $arrResult->debt !== ''){
			return $arrResult->debt;
		}
		return 0;
	}
	public function get_debt_customer_chi($customer_id,$start_date)
	{
		$this->db->select('SUM(pay_money) AS debt');
		$this->db->from('receivings');
		//$this->db->join('sales_payments', 'sales.sale_id = sales_payments.sale_id');
		$this->db->where('receivings.customer_id', $customer_id);
		$this->db->where('receivings.receiving_time <', $start_date);
		$arrResult = $this->db->get()->row();

		if(isset($arrResult->debt) && $arrResult->debt !== ''){
			return $arrResult->debt;
		}
		return 0;
	}
	
	/*
	Gets information about multiple customers
	*/
	public function get_multiple_info($customer_ids)
	{
		$this->db->from('customers');
		$this->db->join('people', 'people.person_id = customers.person_id');		
		$this->db->where_in('customers.person_id', $customer_ids);
		$this->db->order_by('last_name', 'asc');

		return $this->db->get();
	}
	
	/*
	Inserts or updates a customer
	*/
	public function save_customer(&$person_data, &$customer_data, $customer_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		if(parent::save($person_data, $customer_id))
		{
			if(!$customer_id || !$this->exists($customer_id))
			{
				$customer_data['person_id'] = $person_data['person_id'];
				$success = $this->db->insert('customers', $customer_data);
			}
			else
			{
				$this->db->where('person_id', $customer_id);
				$success = $this->db->update('customers', $customer_data);
			}
		}
		
		$this->db->trans_complete();
		
		$success &= $this->db->trans_status();

		return $success;
	}
	
	/*
	Deletes one customer
	*/
	public function updatenocu($customer_id,$nomoi)
	{
		$this->db->where('person_id', $customer_id);

		return $this->db->update('customers', array('tien_no' => $nomoi));
	}
	/*
	Deletes a list of customers
	*/
	public function delete($customer_id)
	{
		$success = false;
		// kiem tra neu khach hang da mua hang roi thi khong cho xoa
		$arrResult = $this->Sale->get_info_by_customer($customer_id)->result_array();
		if(sizeof($arrResult) == 0){
			$this->db->delete('customers', array('person_id' => $customer_id));
			$success =  $this->db->delete('people', array('person_id' => $customer_id)); 
		}
		
		return $success;
 	}
 	
 	/*
	Get search suggestions to find customers
	*/
	public function get_search_suggestions($search, $unique = TRUE, $limit = 25)
	{
		$suggestions = array();
		
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		$this->db->group_start();		
			$this->db->like('full_name', $search);
			$this->db->or_like('code', $search);
		$this->db->group_end();
		$this->db->order_by('full_name', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' =>$row->code .' - '. $row->full_name);
		}

		if(!$unique)
		{
			$this->db->from('customers');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->like('email', $search);
			$this->db->order_by('email', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->email);
			}

			$this->db->from('customers');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->like('phone_number', $search);
			$this->db->order_by('phone_number', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->phone_number);
			}

			$this->db->from('customers');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->like('account_number', $search);
			$this->db->order_by('account_number', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->account_number);
			}
		}
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

 	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('full_name', $search);
			$this->db->or_like('email', $search);
			$this->db->or_like('phone_number', $search);
			$this->db->or_like('account_number', $search);
		$this->db->group_end();

		return $this->db->get()->num_rows();
	}
	
	/*
	Performs a search on customers
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'last_name', $order = 'asc')
	{
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('full_name', $search);
			$this->db->or_like('email', $search);
			$this->db->or_like('phone_number', $search);
			$this->db->or_like('account_number', $search);
		$this->db->group_end();
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();	
	}
	// lay chuong trinh khuyen mai theo khoang thoi gian
	// lay gia hien tai theo khoang thoi gian
	public function get_promotion_by_time_customer($promotion_id,$start_date)
	{
		$promotion_kg = '';
		$promotion_pecen = '';
		$end_date = '';
		$SQL = "select * from t_promotion_detail WHERE `promotion_id` = ".$promotion_id." and DATEDIFF('".$start_date."',`start_date`) >= 0 order by DATEDIFF('".$start_date."',`start_date`) LIMIT 1 ";
		$query = $this->db->query($SQL);
		$arrResult = $query->result_array();
		if($arrResult){
			$promotion_kg = $arrResult[0]['promotion_kg'];
			$promotion_pecen = $arrResult[0]['promotion_percent'];
			$start_date = $arrResult[0]['start_date'];
			$end_date = $arrResult[0]['end_date'];
			return array(
				'promotion_kg' => $promotion_kg,
				'promotion_pecen' => $promotion_pecen,
				'start_date' => $start_date,
				'end_date' => $end_date
			);
		}
	}
	// lay chuong trinh khuyen mai theo khoang thoi gian
	// lay gia hien tai theo khoang thoi gian
	public function get_promotion_by_midle_date_customer($promotion_id,$start_date)
	{
		$promotion_kg = '';
		$promotion_pecen = '';
		$SQL = "select * from t_promotion_detail WHERE `promotion_id` = ".$promotion_id." and DATEDIFF('".$start_date."',`start_date`) >= 0 and DATEDIFF('".$start_date."',`end_date`) <= 0 order by DATEDIFF('".$start_date."',`start_date`) LIMIT 1 ";
		$query = $this->db->query($SQL);
		$arrResult = $query->result_array();
		if($arrResult){
			$promotion_kg = $arrResult[0]['promotion_kg'];
			$promotion_pecen = $arrResult[0]['promotion_percent'];
			$start_date = $arrResult[0]['start_date'];
			$end_date = $arrResult[0]['end_date'];
			return array(
				'promotion_kg' => $promotion_kg,
				'promotion_pecen' => $promotion_pecen,
				'start_date' => $start_date,
				'end_date' => $end_date
			);
		}
	}
}
?>
