<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Persons.php");

class Employees extends Persons
{
	public function __construct()
	{
		parent::__construct('employees');
	}
	
	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_employ_manage_table_headers());

		$this->load->view('employees/manage', $data);
	}
	public function config()
	{
		//echo "<pre>"; var_dump($data['promotion']); echo "</pre>";
		//$data['support_barcode'] = $this->barcode_lib->get_list_barcodes();
		$data['logo_exists'] = $this->Appconfig->get('company_logo') != '';
		$this->load->helper('listype');
        $data['arrListtype'] = get_listtype_two();
		
		$data = $this->xss_clean($data);
		// load all the license statements, they are already XSS cleaned in the private function
		//$data['licenses'] = $this->_licenses();
		//$data['themes'] = $this->_themes();
		$this->load->view("employees/config", $data);
	}
	/*
	Returns employee table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$employees = $this->Employee->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Employee->get_found_rows($search);
		$data_rows = array();
		foreach($employees->result() as $person)
		{
			//print_r($person); exit;
			$data_rows[] = get_employ_data_row($person, $this);
		}

		$data_rows = $this->xss_clean($data_rows);

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Employee->get_search_suggestions($this->input->post('term')));

		echo json_encode($suggestions);
	}
	
	/*
	Loads the employee edit form
	*/
	public function view($employee_id = -1)
	{
		$person_info = $this->Employee->get_info($employee_id);
		foreach(get_object_vars($person_info) as $property => $value)
		{
			$person_info->$property = $this->xss_clean($value);
		}
		$data['person_info'] = $person_info;

		$modules = array();
		foreach($this->Module->get_all_modules()->result() as $module)
		{
			$module->module_id = $this->xss_clean($module->module_id);
			$module->grant = $this->xss_clean($this->Employee->has_grant($module->module_id, $person_info->person_id));
			
			$modules[] = $module;
		}
		$data['all_modules'] = $modules;

		$permissions = array();
		foreach($this->Module->get_all_subpermissions()->result() as $permission)
		{
			$permission->module_id = $this->xss_clean($permission->module_id);
			$permission->permission_id = $this->xss_clean($permission->permission_id);
			$permission->grant = $this->xss_clean($this->Employee->has_grant($permission->permission_id, $person_info->person_id));
			
			$permissions[] = $permission;
		}
		$data['all_subpermissions'] = $permissions;
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		    $remote  = $_SERVER['REMOTE_ADDR'];

		    if(filter_var($client, FILTER_VALIDATE_IP))
		    {
		        $ip = $client;
		    }
		    elseif(filter_var($forward, FILTER_VALIDATE_IP))
		    {
		        $ip = $forward;
		    }
		    else
		    {
		        $ip = $remote;
		    }
		$data['ip'] = $ip;
		$this->load->view("employees/form", $data);
	}
	
	/*
	Inserts/updates an employee
	*/
	public function save($employee_id = -1)
	{
		$person_data = array(
			'full_name' => $this->input->post('full_name'),
			'gender' => $this->input->post('gender'),
			'email' => $this->input->post('email'),
			'phone_number' => $this->input->post('phone_number'),
			'address' => $this->input->post('address'),
			'comments' => $this->input->post('comments'),
			'ip' => $this->input->post('ip')
		);
		$grants_data = $this->input->post('grants') != NULL ? $this->input->post('grants') : array();
		
		//Password has been changed OR first time password set
		if($this->input->post('password') != '')
		{
			$employee_data = array(
				'username' => $this->input->post('username'),
				'password' => md5($this->input->post('password'))
			);
		}
		else //Password not changed
		{
			$employee_data = array('username' => $this->input->post('username'));
		}
		
		if($this->Employee->save_employee($person_data, $employee_data, $grants_data, $employee_id))
		{
			$person_data = $this->xss_clean($person_data);
			$employee_data = $this->xss_clean($employee_data);

			//New employee
			if($employee_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('employees_successful_adding').' '.
								$person_data['full_name'], 'id' => $employee_data['person_id']));
			}
			else //Existing employee
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('employees_successful_updating').' '.
								$person_data['full_name'], 'id' => $employee_id));
			}
		}
		else//failure
		{
			$person_data = $this->xss_clean($person_data);

			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('employees_error_adding_updating').' '.
							$person_data['full_name'], 'id' => -1));
		}
	}
	
	/*
	This deletes employees from the employees table
	*/
	public function delete()
	{
		$employees_to_delete = $this->xss_clean($this->input->post('ids'));

		if($this->Employee->delete_list($employees_to_delete))
		{
			echo json_encode(array('success' => TRUE,'message' => $this->lang->line('employees_successful_deleted').' '.
							count($employees_to_delete).' '.$this->lang->line('employees_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE,'message' => $this->lang->line('employees_cannot_be_deleted')));
		}
	}

	public function save_info()
	{
		$batch_save_data = array(
			'company' => $this->input->post('company'),
			'address' => $this->input->post('address'),
			'phone' => $this->input->post('phone'),
			'email' => $this->input->post('email'),
			'fax' => $this->input->post('fax'),
			'website' => $this->input->post('website'),	
			'return_policy' => $this->input->post('return_policy')
		);
			
		$result = $this->Appconfig->batch_save($batch_save_data);
		echo json_encode(array('success' => true, 'message' => 'Cập nhật thành công'));
	}
}
?>