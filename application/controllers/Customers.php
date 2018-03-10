<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Customers extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('customers');
	}
	
	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_people_manage_table_headers());

		$this->load->view('customers/manage', $data);
	}
	
	/*
	Returns customer table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$customers = $this->Customer->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Customer->get_found_rows($search);

		$data_rows = array();
		foreach($customers->result() as $person)
		{
			// lay ten nguoi quan ly neu co
			if($person->employees_id > 0){
				$infors = $this->Person->get_info($person->employees_id);
				$person->people_manager = $infors->full_name;
			}else{
				$person->people_manager = '';
			}
			$data_rows[] = get_person_data_row($person, $this);
		}

		$data_rows = $this->xss_clean($data_rows);

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	public function get_row($row_id)
	{
		$arrinfor = $this->Customer->get_info($row_id);
		if($arrinfor->employees_id > 0){
			$infors = $this->Person->get_info($arrinfor->employees_id);
			$arrinfor->people_manager = $infors->full_name;
		}else{
			$arrinfor->people_manager = '';
		}
		$data_row = $this->xss_clean(get_person_data_row($arrinfor, $this));

		echo json_encode($data_row);
	}
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest()
	{
		$suggestions = $this->xss_clean($this->Customer->get_search_suggestions($this->input->get('term'), TRUE));

		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Customer->get_search_suggestions($this->input->post('term'), FALSE));

		echo json_encode($suggestions);
	}
	
	/*
	Loads the customer edit form
	*/
	public function view($customer_id = -1)
	{
		// lay thong tin nguoi dung
		$allPeoples = $this->Employee->getalls();
		$arrPeople = array();
		$arrPeople[''] = "-- Chọn nhân viên--";
		foreach($allPeoples as $allPeople){
			$arrPeople[$allPeople['person_id']] = $allPeople['full_name'];
		}
		//print_r($arrPeople); exit;
		$info = $this->Customer->get_info($customer_id);
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		$data['person_info'] = $info;
		$data['allpeople'] = $arrPeople;
		//$data['total'] = $this->xss_clean($this->Customer->get_totals($customer_id)->total);

		$this->load->view("customers/form", $data);
	}
	
	/*
	Inserts/updates a customer
	*/
	public function save($customer_id = -1)
	{
		if($this->input->post('birthday') !== ''){
			$birthday = DateTime::createFromFormat('d/m/Y', $this->input->post('birthday'))->format('Y-m-d H:i:s');
		}else{
			$birthday = NULL;
		}
		$person_data = array(
			'full_name' => $this->input->post('full_name'),
			'gender' => $this->input->post('gender'),
			'email' => $this->input->post('email'),
			'phone_number' => $this->input->post('phone_number'),
			'address' => $this->input->post('address'),
			'identity_card' => $this->input->post('identity_card'),
			'employees_id' => $this->input->post('people_manager'),
			'birthday' => $birthday,
			'comments' => $this->input->post('comments')
		);
		$customer_data = array(
			'account_number' => $this->input->post('account_number') == '' ? NULL : $this->input->post('account_number'),
			'company_name' => $this->input->post('company_name') == '' ? NULL : $this->input->post('company_name'),
			'code' => $this->input->post('customers_code')
		);

		if($this->Customer->save_customer($person_data, $customer_data, $customer_id))
		{
			$person_data = $this->xss_clean($person_data);
			$customer_data = $this->xss_clean($customer_data);
			
			//New customer
			if($customer_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_successful_adding').' '.
								$person_data['full_name'], 'id' => $customer_data['person_id']));
			}
			else //Existing customer
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_successful_updating').' '.
								$person_data['full_name'], 'id' => $customer_id));
			}
		}
		else//failure
		{
			$person_data = $this->xss_clean($person_data);

			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_error_adding_updating').' '.
							$person_data['full_name'], 'id' => -1));
		}
	}
	
	public function check_account_number()
	{
		$exists = $this->Customer->account_number_exists($this->input->post('account_number'), $this->input->post('person_id'));

		echo !$exists ? 'true' : 'false';
	}
	
	/*
	This deletes customers from the customers table
	*/
	public function delete()
	{
		$customers_to_delete = $this->xss_clean($this->input->post('ids'));
		if(sizeof($customers_to_delete) == 1){
			$customer_id = $customers_to_delete[0];
			if($this->Customer->delete($customer_id))
			{
				echo json_encode(array('success' => TRUE, 'message' => "Xóa thành công"));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => "Không thể xóa khách hàng khi khách đã mua sản phẩm"));
			}
		}
		else{
			echo json_encode(array('success' => FALSE, 'message' => "Chỉ được chọn 1 để xóa"));
		}
	}
}
?>