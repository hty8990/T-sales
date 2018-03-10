<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Persons.php");

class Suppliers extends Persons
{
	public function __construct()
	{
		parent::__construct('suppliers');
	}
	
	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_suppliers_manage_table_headers());

		$this->load->view('suppliers/manage', $data);
	}
	
	/*
	Gets one row for a supplier manage table. This is called using AJAX to update one row.
	*/
	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_supplier_data_row($this->Supplier->get_info($row_id), $this));

		echo json_encode($data_row);
	}
	
	/*
	Returns Supplier table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$suppliers = $this->Supplier->search($search, $limit, $offset, $sort, $order);
		//print_r($suppliers); die();
		$total_rows = $this->Supplier->get_found_rows($search);

		$data_rows = array();
		foreach($suppliers->result() as $supplier)
		{
			$data_rows[] = get_supplier_data_row($supplier, $this);
		}

		$data_rows = $this->xss_clean($data_rows);

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest()
	{
		$suggestions = $this->xss_clean($this->Supplier->get_search_suggestions($this->input->get('term'), TRUE));

		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Supplier->get_search_suggestions($this->input->post('term'), FALSE));

		echo json_encode($suggestions);
	}
	
	/*
	Loads the supplier edit form
	*/
	public function view($supplier_id = -1)
	{
		$info = $this->Supplier->get_info($supplier_id);
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		$data['person_info'] = $info;

		$this->load->view("suppliers/form", $data);
	}
	
	/*
	Inserts/updates a supplier
	*/
	public function save($supplier_id = -1)
	{
		$person_data = array(
			'full_name' => $this->input->post('full_name'),
			'gender' => $this->input->post('gender'),
			'email' => $this->input->post('email'),
			'phone_number' => $this->input->post('phone_number'),
			'address' => $this->input->post('address'),
			'comments' => $this->input->post('comments')
		);
		$supplier_data = array(
			'agency_name' => $this->input->post('agency_name'),
			'account_number' => $this->input->post('account_number') == '' ? NULL : $this->input->post('account_number')
		);

		if($this->Supplier->save_supplier($person_data, $supplier_data, $supplier_id))
		{
			$supplier_data = $this->xss_clean($supplier_data);

			//New supplier
			if($supplier_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('suppliers_successful_adding').' '.
								$supplier_data['agency_name'], 'id' => $supplier_data['person_id']));
			}
			else //Existing supplier
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('suppliers_successful_updating'), 'id' => $supplier_id));
			}
		}
		else//failure
		{
			$supplier_data = $this->xss_clean($supplier_data);

			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('suppliers_error_adding_updating'), 'id' => -1));
		}
	}
	
	/*
	This deletes suppliers from the suppliers table
	*/
	public function delete()
	{
		$suppliers_to_delete = $this->xss_clean($this->input->post('ids'));
		if(sizeof($suppliers_to_delete) == 1){
			$supplier_id = $suppliers_to_delete[0];
			if($this->Supplier->delete($supplier_id))
			{
				echo json_encode(array('success' => TRUE, 'message' => "Xóa thành công"));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => "Không thể xóa nhà cung cấp khi khách đã nhập sản phẩm"));
			}
		}
		else{
			echo json_encode(array('success' => FALSE, 'message' => "Chỉ được chọn 1 để xóa"));
		}
	}
	
}
?>