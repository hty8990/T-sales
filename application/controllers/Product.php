<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Product extends Secure_Controller
{
	public $result = array();
	public $arrLimit = array();
	public $datas;
	public $param;
	public $litmit;
	public $percen;

	public function __construct()
	{
		ini_set('MAX_EXECUTION_TIME', -1);
		parent::__construct('Product');
		$this->load->model('ProductModel');
	}
	
	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_product_manage_table_headers());

		$this->load->view('product/manage', $data);
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

		$customers = $this->ProductModel->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->ProductModel->get_found_rows($search);

		$data_rows = array();
		foreach($customers->result() as $person)
		{
			$data_rows[] = get_product_data_row($person, $this);
		}

		$data_rows = $this->xss_clean($data_rows);

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	public function get_row($row_id)
	{
		$arrinfor = $this->ProductModel->get_info($row_id);
		$data_row = $this->xss_clean(get_person_data_row($arrinfor, $this));

		echo json_encode($data_row);
	}
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest()
	{
		$suggestions = $this->xss_clean($this->ProductModel->get_search_suggestions($this->input->get('term'), TRUE));

		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->ProductModel->get_search_suggestions($this->input->post('term'), FALSE));

		echo json_encode($suggestions);
	}
	
	/*
	Loads the customer edit form
	*/
	public function view($id = -1)
	{
		$info = $this->ProductModel->get_info($id);
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		$data['product_info'] = $info;

		//$data['total'] = $this->xss_clean($this->Customer->get_totals($customer_id)->total);

		$this->load->view("product/form", $data);
	}
	
	/*
	Inserts/updates a customer
	*/
	public function save($id = -1)
	{
		$input_prices =  str_replace(",","",$this->input->post('price'));
		$input_prices =  str_replace(".","",$input_prices);
		$status = 0;
		if($this->input->post('is_status') == 1){
			$status = 1;
		}
		$product_data = array(
			'code' => $this->input->post('code'),
			'name' => $this->input->post('name'),
			'price' => $input_prices,
			'c_limit' => $this->input->post('limit'),
			'status' => $status,
			'percen1' => $this->input->post('percen1'),
			'percen2' => $this->input->post('percen2'),
			'percen3' => $this->input->post('percen3'),
			'percen4' => $this->input->post('percen4'),
			'description' => $this->input->post('description')
		);

		if($this->ProductModel->save($product_data, $id))
		{
			echo json_encode(array('success' => TRUE, 'message' => "Cập nhật thành công"));
		}
		else//failure
		{
			$person_data = $this->xss_clean($person_data);

			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_error_adding_updating').' '.
							$person_data['full_name'], 'id' => -1));
		}
	}
	
	/*
	This deletes customers from the customers table
	*/
	public function delete()
	{
		$customers_to_delete = $this->xss_clean($this->input->post('ids'));
		if(sizeof($customers_to_delete) == 1){
			$id = $customers_to_delete[0];
			if($this->ProductModel->delete($id))
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

	public function calculator()
	{
		$data['products'] = $this->ProductModel->getall_status();
		$this->load->view("product/calculator", $data);
	}

	public function process(){
		$datas = $this->ProductModel->getall_status();
		$i =0;
		foreach($datas as $data){
			$arrs[$i]['name'] = $data->name;
			$arrs[$i]['percen1'] = $data->percen1;
			$arrs[$i]['price'] = $data->price;
			$arrs[$i]['litmit'] = $data->c_limit;
			$i++;
		}
		// item
		$path = $_SERVER['DOCUMENT_ROOT']."//tool//data//item.txt";
		if(file_exists($path)){
			unlink($path);
		}
		file_put_contents($path, json_encode($arrs));
		// result
		$arrProcess['limit'] = $_GET['limit'];
		$arrProcess['percen1'] = $_GET['percen1'];
		$path = $_SERVER['DOCUMENT_ROOT']."//tool//data//process.txt";
		if(file_exists($path)){
			unlink($path);
		}
		file_put_contents($path, json_encode($arrProcess));
		$url = site_url()."../tool/";
		echo json_encode(array('success' => true, 'url' => $url));
	}
}
?>