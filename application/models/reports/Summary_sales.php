<?php
require_once("Report.php");
class Summary_sales extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		return array($this->lang->line('reports_date'), $this->lang->line('reports_quantity'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_tax'), $this->lang->line('reports_cost'), $this->lang->line('reports_profit'));
	}
	
	public function getData(array $inputs)
	{		
		$this->db->select('sale_date, SUM(quantity_purchased) AS quantity_purchased, SUM(subtotal) AS subtotal, SUM(total) AS total, SUM(tax) AS tax, SUM(cost) AS cost, SUM(profit) AS profit');
		$this->db->from('sales_items_temp');
		$this->db->where("sale_date BETWEEN " . $this->db->escape($inputs['start_date']) . " AND " . $this->db->escape($inputs['end_date']));

		if ($inputs['location_id'] != 'all')
		{
			$this->db->where('item_location', $inputs['location_id']);
		}

		if ($inputs['sale_type'] == 'sales')
        {
            $this->db->where('quantity_purchased > 0');
        }
        elseif ($inputs['sale_type'] == 'returns')
        {
            $this->db->where('quantity_purchased < 0');
        }    
        
		$this->db->group_by('sale_date');
		$this->db->order_by('sale_date');

		return $this->db->get()->result_array();
	}
	
	public function kqtheosoluong($customer_id,$type,$start_date,$end_date)
	{
		$this->db->select('sale_id,sales.customer_id, SUM(order_money - pay_money) as no_trong_ky,t_people.full_name,t_people.address,t_customers.code');
		$this->db->from('sales');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
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
	

}
?>