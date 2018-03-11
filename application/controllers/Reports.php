<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once ('Secure_Controller.php');

class Reports extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('reports');

		$method_name = $this->uri->segment(2);
		$exploder = explode('_', $method_name);

		/**if(sizeof($exploder) > 1)
		{
			preg_match('/(?:inventory)|([^_.]*)(?:_graph|_row)?$/', $method_name, $matches);
			preg_match('/^(.*?)([sy])?$/', array_pop($matches), $matches);
			$submodule_id = $matches[1] . ((count($matches) > 2) ? $matches[2] : 's');

			$this->track_page('reports/' . $submodule_id, 'reports_' . $submodule_id);

			// check access to report submodule
			if(!$this->Employee->has_grant('reports_' . $submodule_id, $this->Employee->get_logged_in_employee_info()->person_id))
			{
				redirect('no_access/reports/reports_' . $submodule_id);
			}
		}*/
		$this->load->helper('report');
	}

	//Initial report listing screen
	public function index()
	{
		$data['grants'] = $this->xss_clean($this->Employee->get_employee_grants($this->session->userdata('person_id')));
		$checkadmin = false;
		foreach($data['grants'] as $grant){
			if($grant['permission_id'] == 'employees'){
				$checkadmin = true;
			}
		}
		$data['checkadmin'] =$checkadmin;
		$this->load->view('reports/listing', $data);

	}

	//Input for reports that require only a date range. (see routes.php to see that all graphical summary reports route here)
	public function detailed_sales($type)
	{
		$checkadmin = false;
		$grants = $this->xss_clean($this->Employee->get_employee_grants($this->session->userdata('person_id')));
		foreach($grants as $grant){
			if($grant['permission_id'] == 'employees'){
				$checkadmin = true;
			}
		}
		$data = array();
		$stock_locations = $data = $this->xss_clean($this->Stock_location->get_allowed_locations('sales'));
		$stock_locations['all'] = $this->lang->line('reports_all');
		$data['stock_locations'] = array_reverse($stock_locations, TRUE);
		$data['mode'] = 'sale';
		$data['type'] = $type;
		// BC01
		if ($type == 'congnothu') {
			$data['table_headers'] = congnothu_table_headers();
			$this->load->view('reports/congnothu', $data);
		}
		// BC02
		elseif ($type == 'congnotra') {
			$data['table_headers'] = congnotra_table_headers();
			$this->load->view('reports/congnotra', $data);
		}
		// BC03
		elseif ($type == 'doanhsosanluong') {
			// lay thong tin nguoi dung
			$allPeoples = $this->Employee->getalls();
			$arrPeople = array();
			$arrPeople[''] = "-- Chọn nhân viên--";
			foreach($allPeoples as $allPeople){
				$arrPeople[$allPeople['person_id']] = $allPeople['full_name'];
			}
			$data['allpeople'] = $arrPeople;
			$data['table_headers'] = doanhsosanluong_table_headers();
			$this->load->helper('listype');
			$arr_listype = get_listtype_three();
			$data['arr_listype'] = $arr_listype;
			$this->load->view('reports/doanhsosanluong', $data);
		}
		// BC04
		elseif ($type == 'doanhsokhachhang') {
			// lay thong tin nguoi dung
			$allPeoples = $this->Employee->getalls();
			$arrPeople = array();
			$arrPeople[''] = "-- Chọn nhân viên--";
			foreach($allPeoples as $allPeople){
				$arrPeople[$allPeople['person_id']] = $allPeople['full_name'];
			}
			$data['allpeople'] = $arrPeople;
			$data['table_headers'] = doanhsokhachhang_table_headers();
			$this->load->helper('listype');
			$arr_listype = get_listtype_three();
			$data['arr_listype'] = $arr_listype;
			$this->load->view('reports/doanhsokhachhang', $data);
		}
		// BC05
		elseif ($type == 'ketquakinhdoanh') {
			$data['table_headers'] = ketquakinhdoanh_table_headers();
			$this->load->helper('listype');
			$arr_listype = get_listtype_three();
			$data['arr_listype'] = $arr_listype;
			if($checkadmin){
				$this->load->view('reports/ketquakinhdoanh', $data);
			}else{
				echo "ban khong co quyen truy cap";
			}
			
		}
		// BC06
		elseif ($type == 'soquytienmat') {
			$data['table_headers'] = soquytienmat_table_headers();
			$this->load->helper('listype');
			$arr_listype = get_listtype_three();
			$data['arr_listype'] = $arr_listype;
			$data['payment_options'] = $this->xss_clean($this->Sale->get_payment_options(FALSE));
			$data['arrKhachhang'] = $arrTypeItem = array(
				'' => '-- Tất cả --',
				'khach_hang' => 'Khách hàng',
				'nha_cung_cap' => 'Nhà cung cấp',
				'khac' => 'Khác'
			);
			$this->load->view('reports/soquytienmat', $data);
		}
		// BC07
		elseif ($type == 'hanghoanhapkho') {
			$data['modes'] = array('receive' => $this->lang->line('receivings_receiving'), 'return' => $this->lang->line('receivings_return'));
			$data['table_headers'] = hanghoanhapkho_table_headers();
			$this->load->view('reports/hanghoanhapkho', $data);
		}
		// BC08
		elseif ($type == 'hanghoaxuatkho') {
			$data['table_headers'] = hanghoanhapkho_table_headers();
			$this->load->helper('listype');
			$arr_listype = get_listtype_three();
			$data['arr_listype'] = $arr_listype;
			$this->load->view('reports/hanghoaxuatkho', $data);
		}
		// BC09
		elseif ($type == 'hanghoatonkho') {
			$data['table_headers'] = hanghoatonkho_table_headers();
			$this->load->view('reports/hanghoatonkho', $data);
		}
		elseif ($type == 'thekho') {
			$data['table_headers'] = hanghoanhapkho_table_headers();
			$this->load->view('reports/thekho', $data);
		}elseif($type == 'hoanghoaxuatkhobaobi'){
			$data['table_headers'] = hanghoaxuatkhobaobi_table_headers();
			$this->load->view('reports/hanghoaxuatkhobaobi', $data);
		}
	}

	public function search()
	{

		$type = $this->input->get('type');
		$search = $this->input->get('search');
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		// BC01
		if ($type == 'congnothu') {
			$customer_id = $this->input->get('customer_id');
			$this->BC01_congnothu($customer_id, $search, $start_date, $end_date);
		}
		// BC02
		elseif ($type == 'congnotra') {
			$supplier = $this->input->get('supplier');
			if ($supplier > 0) {
				if ($supplier == 39) {
					$this->BC02_congnotraviettrung($supplier, $search, $start_date, $end_date);
				}
				else {
					$this->BC02_congnotra($supplier, $search, $start_date, $end_date);
				}
			}
		}
		// BC03
		elseif ($type == 'doanhsosanluong') {
			$customer_id = $this->input->get('supplier');
			$people_manager = $this->input->get('people_manager');
			$this->BC03_doanhsosanluong($customer_id, $people_manager, $start_date, $end_date);
		}
		// BC04
		elseif ($type == 'doanhsokhachhang') {
			$customer_id = $this->input->get('customer_id');
			$category = $this->input->get('category');
			$people_manager = $this->input->get('people_manager');
			$this->BC04_doanhsokhachhang($customer_id, $people_manager, $category, $start_date, $end_date);
		}
		// BC05
		elseif ($type == 'ketquakinhdoanh') {
			$customer_id = $this->input->get('supplier');
			$this->BC05_ketquakinhdoanh($customer_id, $start_date, $end_date);
		}
		// BC06
		elseif ($type == 'soquytienmat') {
			$payment_type = $this->input->get('payment_type');
			$khachhang_type = $this->input->get('khachhang_type');
			$this->BC06_soquytienmat($payment_type, $khachhang_type, $start_date, $end_date);
		}
		// BC07
		elseif ($type == 'hanghoanhapkho') {
			$mode = $this->input->get('mode');
			$customer_id = $this->input->get('customer_id');
			$this->BC07_hanghoanhapkhosanpham($mode, $customer_id, $search, $start_date, $end_date);
		}
		// BC08
		elseif ($type == 'hanghoaxuatkho') {
			$customer_id = $this->input->get('customer_id');
			$category = $this->input->get('category');
			$this->BC08_hanghoaxuatkho($customer_id, $search, $start_date, $end_date, $category);
		}
		// BC09
		elseif ($type == 'hanghoatonkho') {
			$customer_id = $this->input->get('customer_id');
			$this->BC09_hanghoatonkho($customer_id, $search, $start_date,$end_date);
		}
		// BC10
		elseif ($type == 'thekho') {
			$this->thekho($customer_id, $search, $start_date, $end_date);
		}elseif($type == 'hanghoaxuatkhobaobi'){
			$this->BC10_hanghoaxuatkhobaobi($search, $start_date, $end_date);
		}
	}
	/** ----------------------------------------------------------------
	 * ------------------ BC 01: CONG NO PHAI THU ----------------------------------
	 * ----------------------------------------------------------------
	 **/
	private function BC01_congnothu($customer_id, $search, $start_date, $end_date)
	{
		$customers = $this->Customer->get_all_customer($customer_id);
		//echo "<pre>"; print_r($customers); exit;
		$total_rows = 0;
		$payment_summary = '';
		$data_rows = array();
		$CI =& get_instance();
		$controller_name = $CI->uri->segment(1);
		$i = 0;
		$tongdauky = $tongtrongky = 0;
		foreach ($customers as $customer)
		{
			if($customer){
				// tinh no ky truoc
				$nodauky = $this->Giftcard->BC01_congnophaithu("kytruoc",$customer->person_id, $search, $start_date, $end_date);
				$notrongky = $this->Giftcard->BC01_congnophaithu("trongky",$customer->person_id, $search, $start_date, $end_date);
				if($nodauky !== 0 || $notrongky !== 0){
					$tongdauky = $tongdauky + $nodauky;
					$tongtrongky = $tongtrongky + $notrongky;
					//echo "<pre>"; print_r($sale); echo "</pre>"; exit;
					$data_rows[$i] = array (
						'STT' => $i,
						'ma_khach_hang' => $customer->code,
						'ten_dia_chi_kh' => $customer->full_name ." (".$customer->address.")" ,
						'no_dau_ky' => to_currency($nodauky),
						'no_tk' => to_currency($notrongky),
						'no_ck' => to_currency($nodauky + $notrongky),
						'edit' => anchor($controller_name."/BC01_chitietcongnophaithu/$customer->person_id/$start_date/$end_date", '<span class="glyphicon glyphicon-info-sign icon-th"></span>',
							array('class'=>'modal-dlg', 'title'=>"Xem chi tiết công nợ của $customer->full_name"))
						);
					$i++;
				}
			}
		}
		$total_rows = sizeof($data_rows);
		if ($total_rows > 0){
			$data_rows[$i] = array (
			'STT' => '',
			'ma_khach_hang' => '',
			'ten_dia_chi_kh' => 'Tổng',
			'no_dau_ky' => to_currency($tongdauky),
			'no_tk' => to_currency($tongtrongky),
			'no_ck' => to_currency($tongdauky + $tongtrongky),
			'edit' => ''
			);
		}
		//print_r($data_rows); exit;
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function BC01_chitietcongnophaithu($customer_id = -1, $start_date, $end_date)
	{
		$data['nokytruoc'] = $this->Giftcard->BC01_congnophaithu("kytruoc",$customer_id, "", $start_date, $end_date);
		$data['datas'] = $this->Giftcard->BC01_chitietcongnophaithu('trongky',$customer_id, $start_date, $end_date);
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		$this->load->view("reports/chitietcongnophaithu", $data);
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 02: CONG NO PHAI TRA ----------------------------------
	 * ----------------------------------------------------------------
	 **/

	private function BC02_congnotra($suppliers_id, $search, $start_date, $end_date)
	{
		$CI =& get_instance();
		$controller_name = $CI->uri->segment(1);

		// Cong no ky truoc
		$tongnhapkhokytruoc = $this->Giftcard->BC02_giatrinhapkho("kytruoc", $suppliers_id, $start_date, $end_date);
		$sochikytruoc = $this->Giftcard->BC02_sochinhacungcap("kytruoc", $suppliers_id, $start_date, $end_date);
		$congnotrakytruoc = $tongnhapkhokytruoc - $sochikytruoc;
		// Cong no tra trong ky
		$tongnhapkhotrongky = $this->Giftcard->BC02_giatrinhapkho("trongky", $suppliers_id, $start_date, $end_date);
		$sochitrongky = $this->Giftcard->BC02_sochinhacungcap("trongky", $suppliers_id, $start_date, $end_date);
		// Hien thi
		$result[0]['no_dau_ky'] = to_currency($congnotrakytruoc);
		$result[0]['hang_xuat_kho'] = to_currency($tongnhapkhotrongky);
		$result[0]['so_chi_nha_cung_cap'] = to_currency($sochitrongky);
		$result[0]['tong_no'] = to_currency($congnotrakytruoc + $tongnhapkhotrongky - $sochitrongky);
		$result[0]['edit'] = anchor($controller_name."/BC02_chitietcongnophaitra/$suppliers_id/$start_date/$end_date", '<span class="glyphicon glyphicon-info-sign icon-th"></span>',
							array('class'=>'modal-dlg', 'title'=>"Xem chi tiết"));
		//print_r($data_rows); exit;
		echo json_encode(array('total' => 1, 'rows' => $result));
	}
	private function BC02_congnotraviettrung($suppliers_id, $search, $start_date, $end_date)
	{
		$CI =& get_instance();
		$controller_name = $CI->uri->segment(1);
		// Cong no ky truoc
		$tongxuatkhokytruoc = $this->Giftcard->BC02_giatrixuatkho("kytruoc", $suppliers_id, $start_date, $end_date);
		$sochikytruoc = $this->Giftcard->BC02_sochinhacungcap("kytruoc", $suppliers_id, $start_date, $end_date);
		$congnotrakytruoc = $tongxuatkhokytruoc - $sochikytruoc;
		// Cong no tra trong ky
		$tongxuatkhotrongky = $this->Giftcard->BC02_giatrixuatkho("trongky", $suppliers_id, $start_date, $end_date);
		$sochitrongky = $this->Giftcard->BC02_sochinhacungcap("trongky", $suppliers_id, $start_date, $end_date);
		// Hien thi
		$result[0]['no_dau_ky'] = to_currency($congnotrakytruoc);
		$result[0]['hang_xuat_kho'] = to_currency($tongxuatkhotrongky);
		$result[0]['so_chi_nha_cung_cap'] = to_currency($sochitrongky);
		$result[0]['tong_no'] = to_currency($congnotrakytruoc + $tongxuatkhotrongky - $sochitrongky);
		$result[0]['edit'] = anchor($controller_name."/BC02_chitietcongnophaitra/$suppliers_id/$start_date/$end_date", '<span class="glyphicon glyphicon-info-sign icon-th"></span>',
							array('class'=>'modal-dlg', 'title'=>"Xem chi tiết hàng xuất kho"));
		//print_r($data_rows); exit;
		echo json_encode(array('total' => 1, 'rows' => $result));
	}

	public function BC02_chitietcongnophaitra($supplier_id = -1, $start_date, $end_date)
	{
		if($supplier_id  == 39){
			$datas = $this->Giftcard->BC02_chitietxuatkho('trongky',$supplier_id, $start_date, $end_date);
			$data['datas'] = $datas['hangxuatkhos'];
			$data['giatrihangtralai'] = $datas['giatrihangtralai'];
			$data['check'] = 'vietrung';
		}else{
			$datas = $this->Giftcard->BC02_chitietnhapkho('trongky',$supplier_id, $start_date, $end_date);
			$data['giatrihangtralai'] = false;
			$data['check'] = 'all';
			$data['datas'] = $datas;
		}
		$this->load->view("reports/chitietcongnophaitra", $data);
	}


	/** ----------------------------------------------------------------
	 * ------------------ BC 03: DOANH SO SAN LUONG ----------------------------------
	 * ----------------------------------------------------------------
	 **/

	private function BC03_doanhsosanluong($customer_id, $people_manager, $start_date, $end_date)
	{
		// gia von hang hoa
		$arrReturn= array();
		$tienvanchuyen = 0;
		$so_kg_dam_dac = $doanh_so_dam_dac = $so_kg_hon_hop = $doanh_so_hon_hop = $kg_thuong_san_luong = $tien_thuong_san_luong = $tien_van_chuyen =0;
		$results = $this->Giftcard->BC03_doanhsosanluong($customer_id, $people_manager, $start_date, $end_date);
		// Boc tach hon hop
		$honhops = $this->Giftcard->BC04_doanhsokhachhang($customer_id,$people_manager,'thuc_an_hon_hop',$start_date, $end_date);
		$honhops = $this->Giftcard->BC03_tinhtong($honhops,$customer_id, $people_manager,'thuc_an_hon_hop', $start_date, $end_date);
		$so_kg_hon_hop = $honhops['tong_kg'];
		$doanh_so_hon_hop = $honhops['doanhso'];
		$tienvanchuyen = $tienvanchuyen + $honhops['tien_van_chuyen'];
		// Boc tach dam dac
		$damdacs = $this->Giftcard->BC04_doanhsokhachhang($customer_id,$people_manager,'thuc_an_dam_dac',$start_date, $end_date);
		$damdacs = $this->Giftcard->BC03_tinhtong($damdacs,$customer_id, $people_manager,'thuc_an_dam_dac', $start_date, $end_date);
		$so_kg_dam_dac = $damdacs['tong_kg'];
		$doanh_so_dam_dac = $damdacs['doanhso'];
		$tienvanchuyen = $tienvanchuyen + $damdacs['tien_van_chuyen'];
		// Tinh ket qua
		$arrReturn[0]['so_kg_dam_dac'] = to_currency($so_kg_dam_dac)." kg";
		$arrReturn[0]['doanh_so_dam_dac'] = to_currency($doanh_so_dam_dac);
		$arrReturn[0]['so_kg_hon_hop'] = to_currency($so_kg_hon_hop)." kg";
		$arrReturn[0]['doanh_so_hon_hop'] = to_currency($doanh_so_hon_hop);
		$arrReturn[0]['kg_thuong_san_luong'] = to_currency($results[0]['kg_thuong_san_luong'])." kg";
		$arrReturn[0]['tien_thuong_san_luong'] = to_currency($results[0]['tien_thuong_san_luong']);
		$arrReturn[0]['tien_van_chuyen'] = to_currency($tienvanchuyen);
		$tongtatca = $doanh_so_dam_dac + $doanh_so_hon_hop - $results[0]['kg_thuong_san_luong'] + $tienvanchuyen;
		$arrReturn[0]['tong'] = to_currency($tongtatca);
		echo json_encode(array('total' => 1, 'rows' => $arrReturn));
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 04: DOANH SO KHACH HANG ----------------------------------
	 * ----------------------------------------------------------------
	 **/
	private function BC04_doanhsokhachhang($customer_id, $people_manager, $category, $start_date, $end_date)
	{
		$CI = &get_instance();
		$controller_name = $CI->uri->segment(1);
		// gia von hang hoa
		$data_rows = $this->Giftcard->BC04_doanhsokhachhang($customer_id, $people_manager, $category, $start_date, $end_date);
		$returns = $this->Giftcard->BC04_doanhsokhachhang_tinhtong($data_rows, $customer_id, $people_manager, $category, $start_date, $end_date);
		//$data_rows[] = $this->xss_clean(doanhsokhachhang_data_last_row($data_rows));
		echo json_encode(array('total' => count($data_rows), 'rows' => $returns));
	}

	public function BC04_chitietdoanhsokhachhang($customer_id = -1, $category = '', $start_date, $end_date, $people_manager = -1)
	{
		if ($category == 'all') {
			$category = '';
		}
		$data['datas'] = $this->Giftcard->BC04_doanhsokhachhang($customer_id, $people_manager, $category, $start_date, $end_date);
		$data['returns'] = $this->Giftcard->get_hangtralai_by_khachhang($customer_id, $people_manager, $start_date, $end_date, $category);
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		$this->load->view("reports/chitietdoanhsokhachhang", $data);
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 05: KET QUA KINH DOANH ----------------------------------
	 * ----------------------------------------------------------------
	 **/

	private function BC05_ketquakinhdoanh($customer_id, $start_date, $end_date)
	{
		// doanh thu ban hang
		$doanhthubanhang = $this->Giftcard->BC05_doanhthubanhang($customer_id, $start_date, $end_date);
		$chiphikhac = $this->Giftcard->BC05_chiphikhac($customer_id, $start_date, $end_date);
		// gia von hang hoa
		$giavonhanghoa = $this->Giftcard->BC05_giavonhanghoa($customer_id, $start_date, $end_date);
		//print_r($data_rows); exit;
		$laigop = $doanhthubanhang - $giavonhanghoa;
		$total_rows = 1;
		$laithuan = $laigop - $chiphikhac;
		$data_rows[0]['doanh_so_ban_hang'] = to_currency($doanhthubanhang);
		$data_rows[0]['gia_von_hang_hoa'] = to_currency($giavonhanghoa);
		$data_rows[0]['lai_gop'] = to_currency($laigop);
		$data_rows[0]['chi_phi_khac'] = to_currency($chiphikhac);
		$data_rows[0]['lai_thuan'] = to_currency($laithuan);
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 06: SO QUY TIEN MAT ----------------------------------
	 * ----------------------------------------------------------------
	 **/
	private function BC06_soquytienmat($payment_type, $khachhang_type , $start_date, $end_date)
	{
		$CI = &get_instance();
		$controller_name = $CI->uri->segment(1);
		// gia von hang hoa
		$start_date = str_replace('/', '-', $start_date);
		$date = date("Y-m-d", strtotime($start_date));
		$thukytruoc = $this->Giftcard->BC06_soquytienmat_thu('kytruoc',$payment_type ,$khachhang_type , $date, $end_date);
		$chikytruoc = $this->Giftcard->BC06_soquytienmat_chi('kytruoc',$payment_type, $khachhang_type, $date, $end_date);
		$thutrongky = $this->Giftcard->BC06_soquytienmat_thu('trongky',$payment_type, $khachhang_type, $start_date, $end_date);
		$chitrongky = $this->Giftcard->BC06_soquytienmat_chi('trongky',$payment_type, $khachhang_type, $start_date, $end_date);
		$tienthukytruoc = $thukytruoc[0]['money'];
		$tienthutrongky = $thutrongky[0]['money'];
		$tienchikytruoc = $chikytruoc[0]['money'];
		$tienchitrongky = $chitrongky[0]['money'];
		if ($payment_type == 'Tiền mặt') {
			$temptype = 1;
		}
		else if ($payment_type == 'Chuyển khoản') {
			$temptype = 2;
		}
		else {
			$temptype = 3;
		}
		$tonquykytruoc = $tienthukytruoc - $tienchikytruoc;
		$data_rows[0]['ton_quy_ky_truoc'] = to_currency($tonquykytruoc);
		if ($tienthutrongky !== 0) {
			$data_rows[0]['so_thu_trongky'] = to_currency($tienthutrongky) . " " . anchor(
				$controller_name . "/BC06_chitietsoquytienmattrongky/$temptype/$khachhang_type/$start_date/$end_date",
				'<span class="glyphicon glyphicon-info-sign icon-th"></span>',
				array('class' => 'modal-dlg', 'title' => "Xem chi tiết số tiền thu trong kỳ")
			);
		}
		else {
			$data_rows[0]['so_thu_trongky'] = to_currency($tienthutrongky);
		}
		if ($tienchitrongky !== 0) {
			$data_rows[0]['so_chi_trongky'] = to_currency($tienchitrongky) . " " . anchor(
				$controller_name . "/BC06_chitietsoquytienmatchitrongky/$temptype/$khachhang_type/$start_date/$end_date",
				'<span class="glyphicon glyphicon-info-sign icon-th"></span>',
				array('class' => 'modal-dlg', 'title' => "Xem chi tiết số tiền chi trong kỳ")
			);
		}
		else {
			$data_rows[0]['so_chi_trongky'] = to_currency($tienchitrongky);
		}
		//$data_rows[0]['so_chi_kytruoc'] = to_currency($tienchikytruoc);

		$tienconlai = $tonquykytruoc + $tienthutrongky - $tienchitrongky;
		$data_rows[0]['con_lai'] = to_currency($tienconlai);
		echo json_encode(array('total' => count($data_rows), 'rows' => $data_rows));
	}

	public function BC06_chitietsoquytienmattrongky($payment_type,$khachhang_type, $start_date, $end_date)
	{
		if ($payment_type == 1) {
			$temptype = 'Tiền mặt';
		}
		else if ($payment_type == 2) {
			$temptype = 'Chuyển khoản';
		}
		else {
			$temptype = 'Trả thưởng';
		}
		$data['datas'] = $this->Giftcard->BC06_chitietsoquytienmatthutrongky($temptype,$khachhang_type, $start_date, $end_date);
		$this->load->view("reports/chitietsoquytienmatthutrongky", $data);
	}

	public function BC06_chitietsoquytienmatchitrongky($payment_type,$khachhang_type, $start_date, $end_date)
	{
		if ($payment_type == 1) {
			$temptype = 'Tiền mặt';
		}
		else if ($payment_type == 2) {
			$temptype = 'Chuyển khoản';
		}
		else {
			$temptype = 'Trả thưởng';
		}
		$data['datas'] = $this->Giftcard->BC06_chitietsoquytienmatchitrongky($temptype,$khachhang_type, $start_date, $end_date);
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		$this->load->view("reports/chitietsoquytienmatchitrongky", $data);
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 07: HANG HOA NHAP KHO ----------------------------------
	 * ----------------------------------------------------------------
	 **/
	private function BC07_hanghoanhapkhosanpham($mode, $suppliers_id, $search, $start_date, $end_date)
	{
		if ($mode == 'return') {
			$sales = $this->Giftcard->BC07_hanghoanhapkhobaobi($suppliers_id, $search, $start_date, $end_date);
		}
		else {
			$sales = $this->Giftcard->BC07_hanghoanhapkhosanpham($suppliers_id, $search, $start_date, $end_date);
		}
		$total_rows = sizeof($sales);
			//echo "<pre>"; print_r($sales); echo "</pre>"; exit;

		$payment_summary = '';
		$data_rows = array();
		$i = 1;
		foreach ($sales as $sale)
			{
			if ($mode == 'receive') {
					// so luong hang tai che
				$hangtaiches = $this->Giftcard->BC07_hanghoanhapkhotaiche($suppliers_id, $search, $start_date, $end_date, $sale->id);
				if ($hangtaiches[0]->soluong > 0) {
					$sale->soluong = $sale->soluong - $hangtaiches[0]->soluong;
					$sale->thanhtien = $sale->thanhtien - $hangtaiches[0]->thanhtien;
				}
				// so luong hang huy
				$hanghuys = $this->Giftcard->BC07_hanghoanhapkhohuy($suppliers_id, $search, $start_date, $end_date, $sale->id);
				if ($hanghuys[0]->soluong > 0) {
					$sale->soluong = $sale->soluong - $hanghuys[0]->soluong;
					$sale->thanhtien = $sale->thanhtien - $hanghuys[0]->thanhtien;
				}
			}
			$data_rows[] = (get_hanghoanhapkho_data_row($sale, $i, $this, $suppliers_id, $search, $start_date, $end_date, $mode));
			$i++;
		}

		if ($total_rows > 0)
			{
			$data_rows[] = $this->xss_clean(get_hanghoanhapkho_data_last_row($sales, $this));
		}
			//print_r($data_rows); exit;
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows, 'payment_summary' => $payment_summary));
	}

	public function BC07_chitiethanghoanhapkho($item_id = -1, $customer_id = -1, $start_date, $end_date, $mode)
	{
		$hanghuys = false;
		if ($mode == 'return') {
			$data['datas'] = $this->Giftcard->BC07_chitiethanghoanhapkhobaobi($item_id, $customer_id, $start_date, $end_date);
		}
		else {
			$data['datas'] = $this->Giftcard->BC07_chitiethanghoanhapkho($item_id, $customer_id, $start_date, $end_date);
			$hanghuys = $this->Giftcard->BC07_chitiethanghoanhapkhohuy($item_id, $customer_id, $start_date, $end_date);
		}
			//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		$i = sizeof($data['datas']);
		if ($hanghuys) {
				//echo "<pre>"; print_r($hanghuys); echo "</pre>"; exit;
			foreach ($hanghuys as $hanghuy) {
				$data['datas'][$i]->quantity = $hanghuy->quantity;
				$data['datas'][$i]->input_prices = $hanghuy->sale_price;
				$data['datas'][$i]->unit_weigh = $hanghuy->unit_weigh;
				$data['datas'][$i]->full_name = $hanghuy->full_name;
				$data['datas'][$i]->receiving_time = $hanghuy->sale_time;
				$data['datas'][$i]->type = 33;
			}
		}
		if ($mode == 'return') {
			$this->load->view("reports/chitiethanghoanhapkhobaobi", $data);
		}
		else {
			$this->load->view("reports/chitiethanghoanhapkho", $data);
		}	
			//echo "<pre>"; print_r($data['datas']); echo "</pre>"; exit;


	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 08: HANG HOA XUAT KHO ----------------------------------
	 * ----------------------------------------------------------------
	 **/

	private function BC08_hanghoaxuatkho($customer_id, $search, $start_date, $end_date, $category)
	{
		$items = $this->Item->get_all_item($search, $category);
		$CI = &get_instance();
		$controller_name = $CI->uri->segment(1);
		$i = 0;
		$tongsoluong = 0;
		$tongkg =0;
		$data_rows = array();
		$total_rows = 0;
		$tongtien = 0;
		if ($customer_id == '') {
			$customer_id = -1;
		}
		foreach ($items as $item)
			{
			//print_r($item); exit;
			$thanhtien = 0;
			$soluong = 0;
			$sokg = 0;
			$hangxuatkho = $this->Giftcard->BC08_hanghoanxuatkhosanpham($item->id, $customer_id, $start_date, $end_date);
			if ($hangxuatkho[0]->thanhtien) {
				$thanhtien = $hangxuatkho[0]->thanhtien;
				$soluong = $hangxuatkho[0]->soluong;
				$sokg = $hangxuatkho[0]->sokg;
			}
			$hangtralais = $this->Giftcard->BC08_hanghoatralai($item->id, $customer_id , $start_date, $end_date);
			if (isset($hangtralais[0]) && $hangtralais[0]->thanhtien) {
				$thanhtien = $thanhtien - $hangtralais[0]->thanhtien;
				$soluong = $soluong - $hangtralais[0]->soluong_tralai;
				$sokg = $sokg - $hangtralais[0]->sokg_tralai;
			}
			
			if ($soluong !== 0) {
				$tongsoluong = $tongsoluong + $soluong;
				$tongtien = $tongtien + $thanhtien;
				$tongkg =$tongkg+$sokg;
				$data_rows[$i] = array(
					'ma_hang_hoa' => $item->item_number,
					'te_hang_hoa' => $item->name,
					'so_luong' => $soluong . ' bao',
					'so_kg' => $sokg . ' Kg',
					'gia_tri' => to_currency($thanhtien),
					'edit' => anchor(
						$controller_name . "/BC08_chitiethanghoaxuatkho/$item->id/$customer_id/$start_date/$end_date/$category",
						'<span class="glyphicon glyphicon-info-sign icon-th"></span>',
						array('class' => 'modal-dlg', 'title' => "Xem chi tiết sản phẩm   $item->name")
					)
				);
				
				$i++;
			}
		}
		//print_r($data_rows); exit;
		if(isset($data_rows)){
			$total_rows = sizeof($data_rows);
			if (sizeof($data_rows) > 0)
				{
				$data_rows[$i] = array(
					'ma_hang_hoa' => '',
					'te_hang_hoa' => 'Tổng',
					'so_luong' => $tongsoluong . ' bao',
					'so_kg' => $tongkg . ' Kg',
					'gia_tri' => to_currency($tongtien),
					'edit' => ''
				);
			}
		}
		
		//print_r($data_rows); exit;
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function BC08_chitiethanghoaxuatkho($item_id = -1, $customer_id = -1, $start_date, $end_date)
	{
			// hang hoa xuat kho
		$data['datas'] = $this->Giftcard->BC08_chitiethanghoaxuatkho($item_id, $customer_id, $start_date, $end_date);
			// chi tiet hang tra lai nha cung cap
		$data['hangtralai'] = $this->Giftcard->BC08_hanghoatralai($item_id, $customer_id , $start_date, $end_date);
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		$this->load->view("reports/chitiethanghoaxuatkho", $data);
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 09: HANG HOA TON KHO ----------------------------------
	 * ----------------------------------------------------------------
	 **/

	private function BC09_hanghoatonkho($item_id, $search, $start_date,$end_date)
	{
		// lay tat cac san pham
		$arrItems = $this->Giftcard->BC09_search_tonkho($item_id)->result_array();
		$start_date = str_replace('/', '-', $start_date);
		$start_date = date("Y-m-d", strtotime($start_date));
		$result = array();
		$i = 0;
		$CI = &get_instance();
		$controller_name = $CI->uri->segment(1);
		foreach ($arrItems as $arrItem) {
			// hang hoa ton kho trong ky
			$tondauky = $this->Giftcard->BC09_hanghoantonkho_chuky('kytruoc',$arrItem['id'], $start_date,$end_date);
			$tontrongky = $this->Giftcard->BC09_hanghoantonkho_chuky('trongky',$arrItem['id'], $start_date,$end_date);
			if ($tondauky + $tontrongky > 0) {
				$name = $arrItem['name'];
				$id = $arrItem['id'];
				$result[$i]['ma_hang_hoa'] = $arrItem['item_number'];
				$result[$i]['ten_hang_hoa'] = $name;
				$result[$i]['ton_ky_truoc'] = $tondauky;
				$result[$i]['ton_trong_ky'] = $tontrongky;
				$result[$i]['ton_tong'] = $tondauky + $tontrongky;
				//$result[$i]['edit'] = anchor($controller_name."/chitiethangtonkho/$id/$start_date", '<span class="glyphicon glyphicon-info-sign icon-th"></span>',
				///array('class'=>'modal-dlg'
					//,'title'=>"Xem chi tiết tồn kho sản phẩm $name")
				//);
				$i++;
			}
		}		
		// tinh ton dau ky
		$total_rows = count($result);
		//print_r($data_rows); exit;
		echo json_encode(array('total' => $total_rows, 'rows' => $result));
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 10: HANG HOA XUAT KHO BAO BI ----------------------------------
	 * ----------------------------------------------------------------
	 **/

	private function BC10_hanghoaxuatkhobaobi($search, $start_date, $end_date)
	{
		$items = $this->Item_kit->get_all_item_kit($search);
		$CI = &get_instance();
		$controller_name = $CI->uri->segment(1);
		$i = 0;
		$tongsoluong = 0;
		$tongkg =0;
		$tongtien = 0;
		foreach ($items as $item)
			{
			//echo "<pre>"; print_r($item); echo "</pre>"; exit;
			$thanhtien = 0;
			$soluong = 0;
			$sokg = 0;
			$hangxuatkho = $this->Giftcard->BC10_hanghoanxuatkho($item->id, $start_date, $end_date);
			if ($hangxuatkho[0]->soluong) {
				$soluong = $hangxuatkho[0]->soluong;
			}
			$hangtralais = $this->Giftcard->BC10_hanghoatralai($item->id, $start_date, $end_date);
			if ($hangtralais[0]->soluong) {
				$soluong = $soluong - $hangtralais[0]->soluong;
			}
			
			if ($soluong !== 0) {
				$tongsoluong = $tongsoluong + $soluong;
				$tongtien = $tongtien + $thanhtien;
				$tongkg =$tongkg+$sokg;
				$data_rows[$i] = array(
					'ma_hang_hoa' => $item->item_number,
					'te_hang_hoa' => $item->name,
					'so_luong' => $soluong . ' bao',
					//'so_kg' => $sokg . ' Kg',
					//'gia_tri' => to_currency($thanhtien),
					'edit' => anchor(
						$controller_name . "/BC10_chitiethanghoaxuatkhobaobi/$item->id/$start_date/$end_date",
						'<span class="glyphicon glyphicon-info-sign icon-th"></span>',
						array('class' => 'modal-dlg', 'title' => "Xem chi tiết $item->name")
					)
				);
				$i++;
			}
		}
		//print_r($data_rows); exit;
		$total_rows = sizeof($data_rows);
		if (sizeof($data_rows) > 0)
			{
			$data_rows[$i] = array(
				'ma_hang_hoa' => '',
				'te_hang_hoa' => 'Tổng',
				'so_luong' => $tongsoluong . ' bao',
				'so_kg' => $tongkg . ' Kg',
				'gia_tri' => to_currency($tongtien),
				'edit' => ''
			);
		}
		//print_r($data_rows); exit;
		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function BC10_chitiethanghoaxuatkhobaobi($item_id = -1, $start_date, $end_date)
	{
			// hang hoa xuat kho
		$data['datas'] = $this->Giftcard->BC10_chitiethanghoaxuatkho($item_id, $start_date, $end_date);
			// chi tiet hang tra lai nha cung cap
		$data['hangtralai'] = $this->Giftcard->BC10_hanghoatralai($item_id, $start_date, $end_date);
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		$this->load->view("reports/chitiethanghoaxuatkho", $data);
	}
}
?>