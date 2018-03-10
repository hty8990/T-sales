<?php
class Giftcard extends CI_Model
{
	/** ----------------------------------------------------------------
	 * ------------------ BC 01: CONG NO PHAI THU ----------------------------------
	 * ----------------------------------------------------------------
	**/
	public function BC01_congnophaithu($type,$customer_id,$search,$start_date,$end_date)
	{
		$no = 0;
		$this->db->select('SUM(order_money - pay_money) as tong_no');
		$this->db->from('sales');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		if($type == "kytruoc"){
			$this->db->where('sale_time <',$start_date);
		}else{
			$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$this->db->where('sales.type <>', 5);
		$this->db->where('sales.customer_id', $customer_id);
		$nodauky = $this->db->get()->result_array();
		if($nodauky && $nodauky[0]['tong_no'] !== 0){
			$no = $nodauky[0]['tong_no'];
		}
		$this->db->select('SUM(order_money - pay_money) as tong_no');
		$this->db->from('receivings');
		if($type == "kytruoc"){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$this->db->where('receivings.type', 5);
		$this->db->where('receivings.customer_id', $customer_id);
		$nodauky = $this->db->get()->result_array();
		if($nodauky && $nodauky[0]['tong_no'] !== 0){
			$no += $nodauky[0]['tong_no'];
		}
		return $no;
	}
	
	public function BC01_chitietcongnophaithu($type,$customer_id,$start_date,$end_date)
	{
		$this->db->select('*');
		$this->db->from('sales');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		$this->db->where('sales.type <>', 5);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		if($type == "kytruoc"){
			$this->db->where('sale_time <',$start_date);
		}else{
			$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$this->db->order_by('sale_time', 'desc');
		$array1 = $this->db->get()->result_array();

		$this->db->select('*,receiving_time as sale_time, 0 as sale_id, 55 type');
		$this->db->from('receivings');
		$this->db->join('t_people', 't_people.person_id = receivings.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		$this->db->where('receivings.type', 5);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('receivings.customer_id', $customer_id);
		}
		if($type == "kytruoc"){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$this->db->order_by('receiving_time', 'desc');
		$array2 =  $this->db->get()->result_array();
		$array = array_merge($array1, $array2);
		return $array;
	}

	public function chitietcongnothu_nodauky($customer_id,$start_date,$end_date)
	{
		$this->db->select('*');
		$this->db->from('sales');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		$this->db->where('sales.customer_id', $customer_id);
		$this->db->where('sales.sale_time <', $start_date);
		return $this->db->get()->result();
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 02: CONG NO PHAI TRA ----------------------------------
	 * ----------------------------------------------------------------
	**/

	public function BC02_giatrixuatkho($type,$suppliers_id,$start_date,$end_date)
	{
		$giatrihangxuatkho = 0;
		$giatrihangtralai = 0;
		// tong gia tri xuat kho
		$this->db->select('SUM((quantity + quantity_give - quantity_loan + quantity_loan_return) *input_prices * t_sales_items.unit_weigh) as thanhtien');
		$this->db->from('t_sales_items');
		$this->db->join('t_sales', 't_sales.sale_id = t_sales_items.sale_id');
		$this->db->join('t_items', 't_items.id = t_sales_items.item_id');
		$this->db->join('t_people', 't_people.person_id = t_sales.customer_id');
		$this->db->where('t_sales.type', 1);
		if($type == "kytruoc"){
			$this->db->where('sale_time <',$start_date);
		}else{
			$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$hangxuatkho = $this->db->get()->result();
		if($hangxuatkho[0]->thanhtien){
			$giatrihangxuatkho = $hangxuatkho[0]->thanhtien;
		}
		// tong hang tra lai nha cung cap
		$this->db->select('SUM(quantity*input_prices*t_items.unit_weigh) as thanhtien');
		$this->db->from('t_receivings_items');
		$this->db->join('t_receivings', 't_receivings_items.receiving_id = t_receivings.receiving_id');
		$this->db->join('t_items', 't_items.id = t_receivings_items.item_id');
		$this->db->where('t_receivings.type', 7);
		if($type == "kytruoc"){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$hangtralai = $this->db->get()->result();
		if($hangtralai[0]->thanhtien){
			$giatrihangtralai = $hangtralai[0]->thanhtien;
		}
		return $giatrihangxuatkho - $giatrihangtralai;
	}

	public function BC02_chitietxuatkho($type,$suppliers_id,$start_date,$end_date)
	{
		$giatrihangxuatkho = 0;
		$giatrihangtralai = 0;
		// tong gia tri xuat kho
		$this->db->select('(quantity + quantity_give - quantity_loan + quantity_loan_return) *input_prices * t_sales_items.unit_weigh as thanhtien, t_sales.sale_time, t_sales.sale_id,t_sales.type,t_items.name as full_name ');
		$this->db->from('t_sales_items');
		$this->db->join('t_sales', 't_sales.sale_id = t_sales_items.sale_id');
		$this->db->join('t_items', 't_items.id = t_sales_items.item_id');
		$this->db->join('t_people', 't_people.person_id = t_sales.customer_id');
		$this->db->where('t_sales.type', 1);
		if($type == "kytruoc"){
			$this->db->where('sale_time <',$start_date);
		}else{
			$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$this->db->order_by('t_sales.sale_time', 'desc');
		$hangxuatkho = $this->db->get()->result();
		// tong hang tra lai nha cung cap
		$this->db->select('SUM(quantity*input_prices*t_items.unit_weigh) as thanhtien');
		$this->db->from('t_receivings_items');
		$this->db->join('t_receivings', 't_receivings_items.receiving_id = t_receivings.receiving_id');
		$this->db->join('t_items', 't_items.id = t_receivings_items.item_id');
		$this->db->where('t_receivings.type', 7);
		if($type == "kytruoc"){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$hangtralai = $this->db->get()->result();
		if($hangtralai[0]->thanhtien){
			$giatrihangtralai = $hangtralai[0]->thanhtien;
		}
		$arr['hangxuatkhos'] = $hangxuatkho;
		$arr['giatrihangtralai'] = $giatrihangtralai;
		return $arr;
	}

	public function BC02_giatrinhapkho($type,$suppliers_id,$start_date,$end_date)
	{
		$this->db->select('SUM(quantity*t_receivings_items.input_prices) as thanhtien');
		$this->db->from('t_receivings');
		$this->db->join('t_receivings_items', 't_receivings_items.receiving_id = t_receivings.receiving_id');
		$this->db->where('t_receivings.type', 2);
		$this->db->join('t_items_prices', 't_items_prices.id = t_receivings_items.item_id');
		if(!empty($suppliers_id) && $suppliers_id > 0)
		{
			$this->db->where('t_receivings.supplier_id', $suppliers_id);
		}
		if($type == "kytruoc"){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$nhapkho = $this->db->get()->result();
		return $nhapkho[0]->thanhtien;
	}

	public function BC02_chitietnhapkho($type,$suppliers_id,$start_date,$end_date)
	{
		$this->db->select('order_money as thanhtien, t_receivings.type,t_receivings.receiving_id, receiving_time as sale_time,t_people.full_name ');
		$this->db->from('t_receivings');
		$this->db->join('t_people', 't_people.person_id = t_receivings.supplier_id');
		$this->db->where('t_receivings.type', 2);
		if(!empty($suppliers_id) && $suppliers_id > 0)
		{
			$this->db->where('t_receivings.supplier_id', $suppliers_id);
		}
		if($type == "kytruoc"){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$nhapkho = $this->db->get()->result();
		return $nhapkho;
	}

	public function BC02_sochinhacungcap($type,$suppliers_id,$start_date,$end_date)
	{
		// phieu chi - chi phi sang bao
		$this->db->select('SUM(pay_money) as sochi, SUM(cover_money) as chiphisangbao');
		$this->db->from('receivings');
		if(!empty($suppliers_id) && $suppliers_id > 0)
		{
			$this->db->where('supplier_id', $suppliers_id);
		}
		if($type == "kytruoc"){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$result = $this->db->get()->result();
		return $result[0]->sochi-$result[0]->chiphisangbao;
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 03: DOANH SO SAN LUONG ----------------------------------
	 * ----------------------------------------------------------------
	 **/

	public function BC03_doanhsosanluong($customer_id,$people_manager,$start_date,$end_date){
		$arrReturn = array();
		//echo "a"; exit;
		$this->load->helper('promotion');
		$this->db->select('sale_time,t_customers.code,t_people.full_name,sale_id,customer_id,order_money,pay_money,promotion,sanluong_soluong,sanluong_dongia,car_money');
		$this->db->from('sales');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		$this->db->where('sales.type', 1);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		if(!empty($people_manager) && $people_manager > 0)
		{
			$this->db->where('t_people.employees_id', $people_manager);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		$results = $this->db->get()->result_array();
		//print_r($results); exit;
		$i=0;
		$tong_kgdamdac=$tong_kghonhop=0;
		$tong_doanhsodamdac=$tong_doanhsohonhop=0;
		$tong_kgthuongsanluong=0;
		$tong_sotienthuongsanluong=0;
		$tong_tienvanchuyen = $tongtatca= 0;
		$tong=0;
		if(sizeof($results) > 0){
			foreach($results as $result){
				$sokg = 0;
				$tong_kg=0;
				$tongkgvanchuyen = 0;
				$tienkhuyenmaidamdac = $tienkhuyenmaihonhop =0;
				$temp_tienvanchuyendamdac = $temp_tienvanchuyenhonhop= 0;
				$temp_kgdamdac=$temp_kghonhop=0;
				$temp_doanhsodamdac=$temp_doanhsohonhop=0;
				$arrkhuyenmais = array();
				$arrTotal['kl']['thuc_an_dam_dac'] = 0;
				$arrTotal['kl']['thuc_an_hon_hop'] = 0;
				$arrTotal['money']['thuc_an_dam_dac'] = 0;
				$arrTotal['money']['thuc_an_hon_hop'] = 0;
				$arrTotal['kl']['thuc_an_dam_dac'] = 0;
				$arrTotal['kl']['thuc_an_hon_hop'] = 0;
				$arrTotal['money']['thuc_an_dam_dac'] = 0;
				$arrTotal['money']['thuc_an_hon_hop'] = 0;
				$this->db->select('item_id,category,quantity,quantity_give,unit_weigh,sale_price,input_prices,quantity_loan,quantity_loan_return');
				$this->db->from('sales_items');
				$this->db->where('sale_id', $result['sale_id']);
				$chitiets = $this->db->get()->result_array();
				foreach($chitiets as $chitiet){
					$giagoc = (int)$chitiet['input_prices'];
					$giaban = (int)$chitiet['sale_price'];
					$sokg = $chitiet['quantity']*$chitiet['unit_weigh'];
					$type = $chitiet['category'];
					$tong_kg = $tong_kg + (($chitiet['quantity'] + $chitiet['quantity_give'] - $chitiet['quantity_loan'] + $chitiet['quantity_loan_return'])*$chitiet['unit_weigh']);
					// San pham dam dac
					if($type == 'thuc_an_dam_dac'){
						$temp_kgdamdac = $temp_kgdamdac + $sokg;
						$temp_doanhsodamdac = $temp_doanhsodamdac + ($sokg * $giaban);
						$arrTotal['kl']['thuc_an_dam_dac'] = $arrTotal['kl']['thuc_an_dam_dac'] + $sokg;
						$arrTotal['money']['thuc_an_dam_dac'] = $arrTotal['money']['thuc_an_dam_dac'] + $sokg*$giaban;
					}else if($type == 'thuc_an_hon_hop'){
						$temp_kghonhop = $temp_kghonhop + $sokg;
						$temp_doanhsohonhop = $temp_doanhsohonhop + ($sokg * $giaban);
						$arrTotal['kl']['thuc_an_hon_hop'] = $arrTotal['kl']['thuc_an_hon_hop'] + $sokg;
						$arrTotal['money']['thuc_an_hon_hop'] = $arrTotal['money']['thuc_an_hon_hop'] + $sokg*$giaban;
					}
				}
				$tong_kgdamdac = $tong_kgdamdac + $temp_kgdamdac;
				$arrkhuyenmais = get_promotion_helper($result['promotion'],$arrTotal,$chitiets,$temp_kgdamdac,$temp_doanhsodamdac);
				foreach($arrkhuyenmais as $arrkhuyenmai){
					if($arrkhuyenmai['type'] == 'thuc_an_dam_dac'){
						$tienkhuyenmaidamdac = $tienkhuyenmaidamdac + $arrkhuyenmai['money'];
					}			
				}
				$tong_doanhsodamdac = $tong_doanhsodamdac + $temp_doanhsodamdac - $tienkhuyenmaidamdac;
				$tong_kghonhop = $tong_kghonhop + $temp_kghonhop;
				$arrkhuyenmais = get_promotion_helper($result['promotion'],$arrTotal,$chitiets,$temp_kghonhop,$temp_doanhsohonhop);
				foreach($arrkhuyenmais as $arrkhuyenmai){
					if($arrkhuyenmai['type'] == 'thuc_an_hon_hop'){
						$tienkhuyenmaihonhop = $tienkhuyenmaihonhop + $arrkhuyenmai['money'];
					}			
				}
				$tong_doanhsohonhop = $tong_doanhsohonhop  + $temp_doanhsohonhop - $tienkhuyenmaihonhop;
				// thuong san luong
				$tong_kgthuongsanluong = $tong_kgthuongsanluong + $result['sanluong_soluong'];
				$tong_tienvanchuyen = $tong_tienvanchuyen + ($tong_kg*$result['car_money']);
				$tong_sotienthuongsanluong = $tong_sotienthuongsanluong + ($result['sanluong_soluong']*$result['sanluong_dongia']);	
				// tinh khuyen mai ap dung cho tat ca
				$arrPromotions = json_decode($result['promotion']);
				$all_tienkhuyenmaidamdac = $all_tienkhuyenmaihonhop =0;
				foreach($arrPromotions as $arrPromotion){
					if($arrPromotion->type == 'all'){
						// kiem tra la khuyen mai Kg hay khuyen mai %
						if($arrPromotion->promotion_kg > 0){
							$all_tienkhuyenmaidamdac = $all_tienkhuyenmaidamdac + ($tong_kgdamdac * $arrPromotion->promotion_kg);
							$all_tienkhuyenmaihonhop = $all_tienkhuyenmaihonhop + ($tong_kghonhop * $arrPromotion->promotion_kg);
						}
							
					}
				}
				//echo $tong_kgdamdac+$tong_kghonhop; exit;
				$tong_doanhsohonhop = $tong_doanhsohonhop - $all_tienkhuyenmaihonhop;
				$tong_doanhsodamdac = $tong_doanhsodamdac - $all_tienkhuyenmaidamdac;	
			}
			// hang tra lai hon hop
			$this->db->select('items.category,SUM(quantity_return*t_sales_items.unit_weigh) as soluong_tralai, SUM(sale_price*quantity_return*t_sales_items.unit_weigh) as thanhtien');
			$this->db->from('sales');
			$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
			$this->db->join('items', 'items.id = sales_items.item_id');
			$this->db->join('t_people', 't_people.person_id = sales.customer_id');
			$this->db->where('sales.type', 2);
			$this->db->where('items.category', 'thuc_an_hon_hop');
			if(!empty($customer_id) && $customer_id > 0)
			{
				$this->db->where('sales.customer_id', $customer_id);
			}
			if(!empty($people_manager) && $people_manager > 0)
			{
				$this->db->where('t_people.employees_id', $people_manager);
			}
			$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
			$Honhoptralai = $this->db->get()->result_array();
			if($Honhoptralai[0]['soluong_tralai'] > 0){
				$tong_kghonhop = $tong_kghonhop - $Honhoptralai[0]['soluong_tralai'];
				$tong_doanhsohonhop = $tong_doanhsohonhop - $Honhoptralai[0]['thanhtien'];
			}
			// hang tra lai dam dac
			$this->db->select('SUM(quantity_return*t_sales_items.unit_weigh) as soluong_tralai, SUM(sale_price*quantity_return*t_sales_items.unit_weigh) as thanhtien');
			$this->db->from('sales');
			$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
			$this->db->join('items', 'items.id = sales_items.item_id');
			$this->db->join('t_people', 't_people.person_id = sales.customer_id');
			$this->db->where('sales.type', 2);
			$this->db->where('items.category', 'thuc_an_dam_dac');
			if(!empty($customer_id) && $customer_id > 0)
			{
				$this->db->where('sales.customer_id', $customer_id);
			}
			if(!empty($people_manager) && $people_manager > 0)
			{
				$this->db->where('t_people.employees_id', $people_manager);
			}
			$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
			$Damdactralai = $this->db->get()->result_array();
			//echo $tong_kgdamdac; exit;
			if($Damdactralai[0]['soluong_tralai'] > 0){
				$tong_kgdamdac = $tong_kgdamdac - $Damdactralai[0]['soluong_tralai'];
				$tong_doanhsodamdac = $tong_doanhsodamdac - $Damdactralai[0]['thanhtien'];
			}
			$tongtatca = $tong_doanhsohonhop + $tong_doanhsodamdac - $tong_sotienthuongsanluong + $tong_tienvanchuyen;
		}
		$arrReturn[0]['so_kg_dam_dac'] = $tong_kgdamdac." kg";
		$arrReturn[0]['doanh_so_dam_dac'] = to_currency($tong_doanhsodamdac);
		$arrReturn[0]['so_kg_hon_hop'] = $tong_kghonhop." kg";
		$arrReturn[0]['doanh_so_hon_hop'] = to_currency($tong_doanhsohonhop);
		$arrReturn[0]['kg_thuong_san_luong'] = $tong_kgthuongsanluong." kg";
		$arrReturn[0]['tien_thuong_san_luong'] = to_currency($tong_sotienthuongsanluong);
		$arrReturn[0]['tien_van_chuyen'] = to_currency($tong_tienvanchuyen);
		$arrReturn[0]['tong'] = to_currency($tongtatca);
		return $arrReturn;	
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 04: DOANH SO KHACH HANG ----------------------------------
	 * ----------------------------------------------------------------
	 **/
	public function BC04_doanhsokhachhang($customer_id, $people_manager,$category,$start_date,$end_date){
		$arrReturn = array();
		//echo "a"; exit;
		$this->load->helper('promotion');
		$this->db->select('sale_time,t_customers.code,t_people.full_name,sale_id,t_people.person_id,order_money,pay_money,promotion,sanluong_dongia,sanluong_soluong,car_money');
		$this->db->from('sales');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		$this->db->where('sales.type', 1);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		if(!empty($people_manager) && $people_manager > 0)
		{
			$this->db->where('t_people.employees_id', $people_manager);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		$this->db->where('type', 1);
		$results = $this->db->get()->result_array();
		//print_r($results); exit;
		$i=0;
		foreach($results as $result){
			$arrkhuyenmais = array();
			$tongsanluongtang = 0;
			$tienvanchuyen =0;
			$tongkg = 0;
			$tongkg_ban = $tongkg_tang = 0;
			$tonggiaban = 0;
			$giatridonhang = 0;
			$tongiavon = 0;
			$tienlai = 0;
			$laithucsu = 0;
			$tienkhuyenmai = 0;
			$arrTotal['kl']['thuc_an_dam_dac'] = 0;
			$arrTotal['kl']['thuc_an_hon_hop'] = 0;
			$arrTotal['money']['thuc_an_dam_dac'] = 0;
			$arrTotal['money']['thuc_an_hon_hop'] = 0;
			// tinh tong kg
			$this->db->select('item_id,category,quantity,quantity_give,unit_weigh,sale_price,input_prices');
			$this->db->from('sales_items');
			$this->db->where('sale_id', $result['sale_id']);
			if($category !== ''){
				$this->db->where('category', $category);
			}

			$chitiets = $this->db->get()->result_array();
			foreach($chitiets as $chitiet){
				$giagoc = (int)$chitiet['input_prices'];
				$giaban = (int)$chitiet['sale_price'];
				$sokg = $chitiet['quantity']*$chitiet['unit_weigh'];
				$tongkg = $tongkg + (($chitiet['quantity']+$chitiet['quantity_give'])*$chitiet['unit_weigh']);
				$tongkg_ban = $tongkg_ban+($chitiet['quantity']*$chitiet['unit_weigh']);
				$tongkg_tang = $tongkg_tang+($chitiet['quantity_give']*$chitiet['unit_weigh']);
				$tonggiaban = $tonggiaban + $giaban*$sokg;
				$tongiavon =  $tongiavon + $giagoc*$sokg;
				// lai theo kg
				$giatridonhang = $giatridonhang + ($sokg * $giaban);
				if($chitiet['category'] == 'thuc_an_dam_dac'){
					$arrTotal['kl']['thuc_an_dam_dac'] = $arrTotal['kl']['thuc_an_dam_dac'] + $sokg;
					$arrTotal['money']['thuc_an_dam_dac'] = $arrTotal['money']['thuc_an_dam_dac'] + $sokg*$giaban;
				}
				if($chitiet['category'] == 'thuc_an_hon_hop'){
					$arrTotal['kl']['thuc_an_hon_hop'] = $arrTotal['kl']['thuc_an_hon_hop'] + $sokg;
					$arrTotal['money']['thuc_an_hon_hop'] = $arrTotal['money']['thuc_an_hon_hop'] + $sokg*$giaban;
				}

			}
			$GLOBALS['thuc_an_hon_hop'] = '';
			$GLOBALS['thuc_an_dam_dac'] = '';
			$GLOBALS['tongkg_km_rieng'] = '';
			$GLOBALS['tong_tien_tru'] = '';
			$GLOBALS['donvicongthuctinh'] = '';
			$GLOBALS['itemkhuyenmai2'] = '';
			$arrkhuyenmais = get_promotion_helper($result['promotion'],$arrTotal,$chitiets,$tongkg,$giatridonhang);
			
			foreach($arrkhuyenmais as $arrkhuyenmai){
				
				$tienkhuyenmai = $tienkhuyenmai + $arrkhuyenmai['money'];
			}
			$tongsanluongtang = $result['sanluong_dongia'] * $result['sanluong_soluong'];
			$tienvanchuyen = $result['car_money'] * $tongkg;
			$laithucsu = ($tonggiaban + $tienvanchuyen ) - $tienkhuyenmai - $tongsanluongtang;
			if($tongkg > 0){
				$arrReturn[$i]['ngay_mua'] =  date("d-m-Y", strtotime($result['sale_time']));
				$arrReturn[$i]['code'] = $result['code'];
				$arrReturn[$i]['full_name'] = $result['full_name'];
				$arrReturn[$i]['tong_kg'] = $tongkg;
				$arrReturn[$i]['person_id'] = $result['person_id'];
				$arrReturn[$i]['gia_ban'] = $tonggiaban;
				$arrReturn[$i]['tongkg_ban'] = $tongkg_ban;
				$arrReturn[$i]['tongkg_tang'] = $tongkg_tang;
				$arrReturn[$i]['thuong_san_luong'] = $tongsanluongtang;
				$arrReturn[$i]['khuyen_mai'] = $tienkhuyenmai;
				$arrReturn[$i]['tien_lai'] = $laithucsu;
				$arrReturn[$i]['tien_lai_no_money'] = $laithucsu;
				$arrReturn[$i]['xem'] = anchor("sales/receipt/".$result['sale_id'], '<span class="glyphicon glyphicon-print"></span>',
			array('target' => '_blank'));
				$i++;
			}
			
		}
		return $arrReturn;	
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 05: KET QUA KINH DOANH ----------------------------------
	 * ----------------------------------------------------------------
	**/
	public function BC05_doanhthubanhang($customer_id,$start_date,$end_date){
		$arrReturn = array();
		//Doanh thu ban hang ban ra
		$this->db->select('SUM(order_money) as hangbanra');
		$this->db->from('sales');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		$this->db->where('type', 1);
		$hangbanra = $this->db->get()->result_array();
		$hangbanra = $hangbanra[0]['hangbanra'];
		//cuoc van chuyen
		$this->db->select('SUM((t_sales_items.quantity+t_sales_items.quantity_give-t_sales_items.quantity_loan+t_sales_items.quantity_loan_return)*t_sales_items.unit_weigh*t_sales.car_money) as cuocvanchuyen');
		$this->db->from('sales');
		$this->db->join('t_sales_items', 't_sales_items.sale_id = t_sales.sale_id');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		$this->db->where('type', 1);
		$hangtralai = $this->db->get()->result_array();
		$cuocvanchuyen = $hangtralai[0]['cuocvanchuyen'];
		$i=0;
		$doanhthubanhang =$hangbanra - $cuocvanchuyen;
		return $doanhthubanhang;
	}

	public function BC05_chiphikhac($customer_id,$start_date,$end_date){
		// chi phi khac trong khoang thoi gian
		$this->db->select('SUM(pay_money) as chiphikhac');
		$this->db->from('receivings');
		$this->db->where('DATE(receiving_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		$this->db->where('type',6);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('customer_id', $customer_id);
		}
		$results = $this->db->get()->result_array();
		$chiphikhac = $results[0]['chiphikhac'];
		return $chiphikhac;	
	}

	public function BC05_chiphibaobi($suppliers_id,$start_date,$end_date)
	{
		$this->db->select('t_items.packet_id,(t_sales_items.quantity+t_sales_items.quantity_give) as soluong');
		$this->db->from('t_sales_items');
		$this->db->join('t_sales', 't_sales_items.sale_id = t_sales.sale_id');
		$this->db->join('t_items', 't_items.id = t_sales_items.item_id');
		$this->db->join('t_customers', 't_customers.person_id = sales.customer_id');
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		$this->db->where('type', 1);
		$sales = $this->db->get()->result_array();
		$tongchiphibaobi = 0;
		foreach ($sales as $sale)
		{
			$arrResult = $this->Item_kit->get_packet_price_by_time($sale['packet_id'],$start_date);
			$tongchiphibaobi = $tongchiphibaobi + ($arrResult['input_prices'] * $sale['soluong']);
		}
		return $tongchiphibaobi;
	}
	public function BC05_giavonhanghoa($customer_id,$start_date,$end_date){
		$arrReturn = array();
		//gia von hang hoa
		//$this->db->select('SUM((t_sales_items.quantity+t_sales_items.quantity_give-t_sales_items.quantity_loan+t_sales_items.quantity_loan_return)*t_sales_items.unit_weigh*t_sales_items.input_prices) as giatridonhang');
		$this->db->select('SUM((t_sales_items.quantity+t_sales_items.quantity_give)*t_sales_items.unit_weigh*t_sales_items.input_prices) as giatridonhang');
		$this->db->from('sales');
		$this->db->join('t_sales_items', 't_sales_items.sale_id = t_sales.sale_id');
		$this->db->join('t_people', 't_people.person_id = sales.customer_id');
		$this->db->join('t_customers', 't_customers.person_id = t_people.person_id');
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		$this->db->where('type', 1);
		$doanhthubanhang = $this->db->get()->result_array();
		$this->db->select('SUM(t_sales_items.input_prices*quantity_return*t_sales_items.unit_weigh) as giatritralai');
		$this->db->from('sales');
		$this->db->join('t_sales_items', 't_sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'items.id = sales_items.item_id');
		$this->db->where('sales.type', 2);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		$hangtralai = $this->db->get()->result_array();
		$giavonhanghoa = $doanhthubanhang[0]['giatridonhang'] - $hangtralai[0]['giatritralai'];
		return $giavonhanghoa;
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 06: SO QUY TIEN MAT ----------------------------------
	 * ----------------------------------------------------------------
	 **/
	public function BC06_soquytienmat_thu($type,$payment_type, $khachhang_type,$start_date,$end_date){
		$this->db->select('SUM(pay_money) as money');
		$this->db->from('sales');
		$this->db->where('pay_money >', 0);
		$this->db->where('type <>', 2);
		// order by name of item
		if($type == 'kytruoc'){
			$this->db->where('sale_time <',$start_date);
		}else{
			$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		if($payment_type !== ""){
			$this->db->where('payment_type', $payment_type);
		}
		if($khachhang_type !== ""){
			if($khachhang_type !== ""){
				if($khachhang_type == 'nha_cung_cap'){
					$this->db->where('type', 5);
				}else if($khachhang_type == 'khac'){
					$this->db->where('type', 6);
				}else{
					$this->db->where('type <>', 5);
					$this->db->where('type <>', 6);
				}
			}
		}
		//echo $this->db->last_query();
		return $this->db->get()->result_array();
	}

	public function soquytienmat_thu($customer_id,$start_date,$end_date){
		$arrReturn = array();
		//echo "a"; exit;
		// tong thu
		$this->db->select('sales.payment_type,sales.sale_id as thu_chi_id,sales.sale_time as date_time,sales.pay_money as money, sales.comment,type, customer_id,supplier_id');
		$this->db->from('sales');
		$this->db->where('pay_money <>', 0);
		// order by name of item
		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		//echo $this->db->last_query();
		return $this->db->get();
	}

	public function BC06_soquytienmat_chi($type,$payment_type, $khachhang_type,$start_date,$end_date){
		$this->db->select('SUM(pay_money) as money');
		$this->db->from('receivings');
		$this->db->where('pay_money >', 0);
		$this->db->where('type <>', 7);
		if($type == 'kytruoc'){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		if($payment_type !== ""){
			$this->db->where('payment_type', $payment_type);
		}
		if($khachhang_type !== ""){
			if($khachhang_type == 'nha_cung_cap'){
				$this->db->where('type', 5);
			}else if($khachhang_type == 'khac'){
				$this->db->where('type', 6);
			}else{
				$this->db->where('type <>', 5);
				$this->db->where('type <>', 6);
			}
		}
		//echo $this->db->last_query();
		return $this->db->get()->result_array();
	}

	public function BC06_chitietsoquytienmatthutrongky($type,$khachhang_type,$start_date,$end_date){
		$this->db->select('*');
		$this->db->from('sales');
		$this->db->where('pay_money >', 0);
		$this->db->where('type <>', 2);
		// order by name of item
		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		if($type !== ""){
			$this->db->where('payment_type', $type);
		}
		if($khachhang_type !== ""){
			if($khachhang_type == 'nha_cung_cap'){
				$this->db->where('type', 5);
			}else if($khachhang_type == 'khac'){
				$this->db->where('type', 6);
			}else{
				$this->db->where('type <>', 5);
				$this->db->where('type <>', 6);
			}
		}
		$this->db->order_by('sale_time', 'desc');
		return $this->db->get()->result_array();
	}

	public function BC06_chitietsoquytienmatchitrongky($type,$khachhang_type,$start_date,$end_date){
		$this->db->select('*');
		$this->db->from('receivings');
		$this->db->where('pay_money >', 0);
		$this->db->where('type <>', 7);
		$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		if($type !== ""){
			$this->db->where('payment_type', $type);
		}
		if($khachhang_type !== ""){
			if($khachhang_type == 'nha_cung_cap'){
				$this->db->where('type', 5);
			}else if($khachhang_type == 'khac'){
				$this->db->where('type', 6);
			}else{
				$this->db->where('type <>', 5);
				$this->db->where('type <>', 6);
			}
		}
		$this->db->order_by('receiving_time', 'desc');
		//echo $this->db->last_query();
		return $this->db->get()->result_array();
	}

	/** ----------------------------------------------------------------
	 * ------------------ BC 07: HANG HOA NHAP KHO ----------------------------------
	 * ----------------------------------------------------------------
	 **/
	 public function BC07_hanghoanhapkhobaobi($suppliers_id,$search,$start_date,$end_date)
	 {
		 $this->db->select('*,SUM(quantity) as soluong, SUM(quantity*input_prices) as thanhtien');
		 $this->db->from('t_receivings_items');
		 $this->db->join('t_receivings', 't_receivings_items.receiving_id = t_receivings.receiving_id');
		 $this->db->join('t_items_packet', 't_items_packet.id = t_receivings_items.item_id');
		 $this->db->join('t_people', 't_people.person_id = t_receivings.supplier_id');
		 $this->db->where('t_receivings.type', 2);
		 $this->db->or_where('type', 10);
		 if(!empty($suppliers_id) && $suppliers_id > 0)
		 {
			 $this->db->where('t_receivings.supplier_id', $suppliers_id);
		 }
		 $this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
 
		 if(!empty($search) && $search !== '')
		 {
			 $this->db->group_start();
				 $this->db->or_like('t_items_packet.item_number', $search);
			 $this->db->group_end();
		 }
		 $this->db->group_by('t_items_packet.id ');
		 $this->db->order_by('receiving_time', 'desc');
		 return $this->db->get()->result();
	 }

	 public function BC07_hanghoanhapkhosanpham($suppliers_id,$search,$start_date,$end_date)
	 {
		 $this->db->select('*,SUM(quantity) as soluong, SUM(quantity*input_prices*t_items.unit_weigh) as thanhtien');
		 $this->db->from('t_receivings_items');
		 $this->db->join('t_receivings', 't_receivings_items.receiving_id = t_receivings.receiving_id');
		 $this->db->where('t_receivings.type', 1);
		 $this->db->join('t_items', 't_items.id = t_receivings_items.item_id');
		 if(!empty($suppliers_id) && $suppliers_id > 0)
		 {
			 $this->db->where('t_receivings.supplier_id', $suppliers_id);
		 }
		 $this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
 
		 if(!empty($search) && $search !== '')
		 {
			 $this->db->group_start();
				 $this->db->or_like('t_items.name', $search);
				 $this->db->or_like('t_items.item_number', $search);
			 $this->db->group_end();
		 }
		 $this->db->group_by('t_items.id ');
		 $this->db->order_by('receiving_time', 'desc');
		 return $this->db->get()->result();
	 }
	 public function BC07_hanghoanhapkhotaiche($suppliers_id,$search,$start_date,$end_date,$id)
	 {
		 $this->db->select('SUM(quantity) as soluong, SUM(quantity*input_prices*t_items.unit_weigh) as thanhtien');
		 $this->db->from('t_receivings_items');
		 $this->db->join('t_receivings', 't_receivings_items.receiving_id = t_receivings.receiving_id');
		 $this->db->where('t_receivings.type', 3);
		 $this->db->join('t_items', 't_items.id = t_receivings_items.item_id');
		 $this->db->where('t_items.id', $id);
		 if(!empty($suppliers_id) && $suppliers_id > 0)
		 {
			 $this->db->where('t_receivings.supplier_id', $suppliers_id);
		 }
		 $this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
 
		 if(!empty($search) && $search !== '')
		 {
			 $this->db->group_start();
				 $this->db->or_like('t_items.name', $search);
				 $this->db->or_like('t_items.item_number', $search);
			 $this->db->group_end();
		 }
		 return $this->db->get()->result();
	 }
 
	 public function BC07_hanghoanhapkhohuy($suppliers_id,$search,$start_date,$end_date,$id)
	 {
		 $this->db->select('SUM(quantity) as soluong, SUM(quantity*sale_price*t_items.unit_weigh) as thanhtien');
		 $this->db->from('t_sales_items');
		 $this->db->join('t_sales', 't_sales_items.sale_id = t_sales.sale_id');
		 $this->db->where('t_sales.type', 3);
		 $this->db->join('t_items', 't_items.id = t_sales_items.item_id');
		 $this->db->where('t_items.id', $id);
		 $this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
 
		 if(!empty($search) && $search !== '')
		 {
			 $this->db->group_start();
				 $this->db->or_like('t_items.name', $search);
				 $this->db->or_like('t_items.item_number', $search);
			 $this->db->group_end();
		 }
		 return $this->db->get()->result();
	 }
 
	 public function BC07_chitiethanghoanhapkho($item_id,$suppliers_id,$start_date,$end_date)
	 {
		 $this->db->select('quantity,input_prices,t_items.unit_weigh,full_name,receiving_time,type');
		 $this->db->from('t_receivings_items');
		 $this->db->join('t_receivings', 't_receivings_items.receiving_id = t_receivings.receiving_id');
		 $this->db->join('t_items', 't_items.id = t_receivings_items.item_id');
		 $this->db->join('t_people', 't_people.person_id = t_receivings.supplier_id');
		 $this->db->group_start();
		 $this->db->where('t_receivings.type', 1);
		 $this->db->or_where('t_receivings.type', 3);
		 $this->db->group_end();
		 $this->db->where('t_items.id', $item_id);
		 if(!empty($suppliers_id) && $suppliers_id > 0)
		 {
			 $this->db->where('t_receivings.supplier_id', $suppliers_id);
		 }
		 $this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		 $this->db->order_by('receiving_time', 'desc');
		 return $this->db->get()->result();
	 }
 
	 public function BC07_chitiethanghoanhapkhohuy($item_id,$suppliers_id,$start_date,$end_date)
	 {
		 $this->db->select('*');
		 $this->db->from('t_sales_items');
		 $this->db->join('t_sales', 't_sales_items.sale_id = t_sales.sale_id');
		 $this->db->where('t_sales.type', 3);
		 $this->db->join('t_items', 't_items.id = t_sales_items.item_id');
		 $this->db->join('t_people', 't_people.person_id = t_sales.customer_id');
		 $this->db->where('t_items.id', $item_id);
		 $this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
 
		 if(!empty($search) && $search !== '')
		 {
			 $this->db->group_start();
				 $this->db->or_like('t_items.name', $search);
				 $this->db->or_like('t_items.item_number', $search);
			 $this->db->group_end();
		 }
		 return $this->db->get()->result();
	 }

	public function BC07_chitiethanghoanhapkhobaobi($item_id,$suppliers_id,$start_date,$end_date)
	{
		$this->db->select('*');
		$this->db->from('t_receivings_items');
		$this->db->join('t_receivings', 't_receivings_items.receiving_id = t_receivings.receiving_id');
		$this->db->join('t_items_packet', 't_items_packet.id = t_receivings_items.item_id');
		$this->db->join('t_people', 't_people.person_id = t_receivings.supplier_id');
		$this->db->where('t_receivings.type', 2);
		$this->db->where('t_items_packet.id', $item_id);
		if(!empty($suppliers_id) && $suppliers_id > 0)
		{
			$this->db->where('t_receivings.supplier_id', $suppliers_id);
		}
		$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));

		if(!empty($search) && $search !== '')
		{
			$this->db->group_start();
				$this->db->or_like('t_items_packet.name', $search);
				$this->db->or_like('t_items_packet.item_number', $search);
			$this->db->group_end();
		}
		$this->db->order_by('receiving_time', 'desc');
		return $this->db->get()->result();
	}

	/** ----------------------------------------------------------------
	 * ------------------ BAO CAO 08: HANG HOA XUAT KHO ----------------------------------
	 * SUM(soluong_ban_t1 + soluong_tang_t1 + Số lượng_trả hàng nợ  – Số lượng_ gửi hang – Số lượng hàng khách trả lại
	**/
	
	public function BC08_hanghoanxuatkhosanpham($item_id,$customer_id,$start_date,$end_date)
	{
		$this->db->select('SUM((quantity + quantity_give - quantity_loan + quantity_loan_return) *input_prices * t_sales_items.unit_weigh) as thanhtien,SUM(quantity + quantity_give - quantity_loan + quantity_loan_return) as soluong,SUM((quantity + quantity_give - quantity_loan + quantity_loan_return)*t_sales_items.unit_weigh) as sokg');
		$this->db->from('t_sales_items');
		$this->db->join('t_sales', 't_sales.sale_id = t_sales_items.sale_id');
		$this->db->join('t_items', 't_items.id = t_sales_items.item_id');
		$this->db->join('t_people', 't_people.person_id = t_sales.customer_id');
		$this->db->where('t_sales.type', 1);
		$this->db->where('t_items.id', $item_id);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('t_sales.customer_id', $customer_id);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		return $this->db->get()->result();
	}

	public function BC08_hanghoatralai($item_id, $customer_id,$start_date,$end_date)
	{
		$this->db->select('SUM(quantity_return) as soluong_tralai, SUM(quantity_return*t_sales_items.unit_weigh) as sokg_tralai, SUM(sale_price*quantity_return*t_sales_items.unit_weigh) as thanhtien');
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'items.id = sales_items.item_id');
		$this->db->where('sales.type', 2);
		$this->db->where('items.id', $item_id);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		return $this->db->get()->result();
	}

	public function BC08_chitiethanghoaxuatkho($item_id,$customer_id,$start_date,$end_date)
	{
		$this->db->select('*');
		$this->db->from('t_sales_items');
		$this->db->join('t_sales', 't_sales.sale_id = t_sales_items.sale_id');
		$this->db->join('t_items', 't_items.id = t_sales_items.item_id');
		$this->db->join('t_people', 't_people.person_id = t_sales.customer_id');
		$this->db->where('t_sales.type', 1);
		$this->db->where('t_items.id', $item_id);
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('t_sales.customer_id', $customer_id);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		return $this->db->get()->result();
	}

	/** ----------------------------------------------------------------
	 * ------------------ BAO CAO 09 ----------------------------------
	**/

	// hang hoa nhap kho: so luong hang nhap - so luong hang huy - so luong hang tai che
	public function BC09_search_tonkho($items)
	{
		$this->db->select('items.*,items_packet.item_number as ma_bao_bi,suppliers.agency_name,item_quantities.quantity,item_quantities.quantity_return');
		$this->db->from('items');
		$this->db->join('suppliers', 'suppliers.person_id = items.supplier_id', 'left');
		$this->db->join('item_quantities', 'item_quantities.item_id = items.id', 'left');
		$this->db->join('items_packet', 'items_packet.id = items.packet_id', 'left');
		//$this->db->where('items.status', $filters['is_status']);
		if($items && $items !== '')
		{
			$this->db->where('items.id', $items);
		}
		//echo $this->db->last_query();
		return $this->db->get();
	}

	public function BC09_hanghoantonkho($type,$item_id,$start_date)
	{
		// tong hang nhap ky truoc
		$this->db->select('SUM(quantity) as nhapkytruoc');
		$this->db->from('receivings_items');
		$this->db->join('receivings', 'receivings_items.receiving_id = receivings.receiving_id');
		$this->db->where('receivings_items.item_id', $item_id);
		if($type == "kytruoc"){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('receiving_time >=',$start_date);
		}
		$this->db->group_start();
		$this->db->where('type', 1);
		$this->db->or_where('type', 0);
		$this->db->group_end();
		//$this->db->or_where('type', 0);
		$kytruoc = $this->db->get()->result_array();
		$soluongnhapkytruoc = $kytruoc[0]['nhapkytruoc'];
		// ban ky truoc
		$this->db->select('SUM(quantity) as bankytruoc');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->where('sales_items.item_id', $item_id);
		if($type == "kytruoc"){
			$this->db->where('sale_time <',$start_date);
		}else{
			$this->db->where('sale_time >=',$start_date);
		}
		
		$this->db->where('type', 1);
		$bankytruoc = $this->db->get()->result_array();
		$soluongbankytruoc = $bankytruoc[0]['bankytruoc'];
		$soluongtonkytruoc = $soluongnhapkytruoc - $soluongbankytruoc;
		return $soluongtonkytruoc;
	}

	public function BC09_hanghoantonkho_chuky($type,$item_id,$start_date,$end_date)
	{
		// tong hang nhap ky truoc
		$this->db->select('SUM(quantity) as nhapkytruoc');
		$this->db->from('receivings_items');
		$this->db->join('receivings', 'receivings_items.receiving_id = receivings.receiving_id');
		$this->db->where('receivings_items.item_id', $item_id);
		if($type == "kytruoc"){
			$this->db->where('receiving_time <',$start_date);
		}else{
			$this->db->where('DATE(receiving_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		$this->db->group_start();
		$this->db->where('type', 1);
		$this->db->or_where('type', 0);
		$this->db->group_end();
		//$this->db->or_where('type', 0);
		$kytruoc = $this->db->get()->result_array();
		$soluongnhapkytruoc = $kytruoc[0]['nhapkytruoc'];
		// ban ky truoc
		$this->db->select('SUM(quantity) as bankytruoc');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->where('sales_items.item_id', $item_id);
		if($type == "kytruoc"){
			$this->db->where('sale_time <',$start_date);
		}else{
			$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		}
		
		$this->db->where('type', 1);
		$bankytruoc = $this->db->get()->result_array();
		$soluongbankytruoc = $bankytruoc[0]['bankytruoc'];
		$soluongtonkytruoc = $soluongnhapkytruoc - $soluongbankytruoc;
		return $soluongtonkytruoc;
	}

	/** ----------------------------------------------------------------
	 * ------------------ BAO CAO 10 ----------------------------------
	**/
	public function BC10_hanghoanxuatkho($item_id,$start_date,$end_date)
	{
		$this->db->select('SUM((quantity + quantity_give - quantity_loan + quantity_loan_return) *input_prices * t_sales_items.unit_weigh) as thanhtien,SUM(quantity + quantity_give - quantity_loan + quantity_loan_return) as soluong,SUM((quantity + quantity_give - quantity_loan + quantity_loan_return)*t_sales_items.unit_weigh) as sokg');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'items.id = sales_items.item_id');
		$this->db->where('type', 1);
		$this->db->where('items.packet_id', $item_id);
		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		return $this->db->get()->result();
	}

	public function BC10_hanghoatralai($item_id,$start_date,$end_date)
	{
		$this->db->select('SUM(quantity) as soluong');
		$this->db->from('t_sales_items');
		$this->db->join('t_sales', 't_sales.sale_id = t_sales_items.sale_id');
		$this->db->join('t_items', 't_items.id = t_sales_items.item_id');
		$this->db->join('t_people', 't_people.person_id = t_sales.customer_id');
		$this->db->where('t_sales.type', 2);
		$this->db->where('t_items.packet_id', $item_id);
		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		return $this->db->get()->result();
	}

	public function BC10_chitiethanghoaxuatkho($item_id,$start_date,$end_date)
	{
		$this->db->select('*');
		$this->db->from('t_sales_items');
		$this->db->join('t_sales', 't_sales.sale_id = t_sales_items.sale_id');
		$this->db->join('t_items', 't_items.id = t_sales_items.item_id');
		$this->db->join('t_people', 't_people.person_id = t_sales.customer_id');
		$this->db->where('t_sales.type', 1);
		$this->db->where('t_items.packet_id', $item_id);
		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($start_date) . ' AND ' . $this->db->escape($end_date));
		return $this->db->get()->result();
	}

	public function hanghoantonkhodauky($item_id,$start_date)
	{
		// tong hang nhap ky truoc
		$this->db->select('SUM(quantity) as nhapkytruoc');
		$this->db->from('receivings_items');
		$this->db->join('receivings', 'receivings_items.receiving_id = receivings.receiving_id');
		$this->db->where('receivings_items.item_id', $item_id);
		$this->db->where('receiving_time <',$start_date);
		$this->db->group_start();
		$this->db->where('type', 1);
		$this->db->or_where('type', 0);
		$this->db->group_end();
		//$this->db->or_where('type', 0);
		$kytruoc = $this->db->get()->result_array();
		$soluongnhapkytruoc = $kytruoc[0]['nhapkytruoc'];
		// ban ky truoc
		$this->db->select('SUM(quantity) as bankytruoc');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->where('sales_items.item_id', $item_id);
		$this->db->where('sale_time <',$start_date);
		$this->db->where('type', 1);
		$bankytruoc = $this->db->get()->result_array();
		$soluongbankytruoc = $bankytruoc[0]['bankytruoc'];
		$soluongtonkytruoc = $soluongnhapkytruoc - $soluongbankytruoc;
		return $soluongtonkytruoc;
	}

	// Ham dung chung
	// tinh hang tra lai cua mot khach hang
	public function get_hangtralai_by_khachhang($customer_id, $people_manager,$start_date,$end_date,$category=''){
		// hang tra lai hon hop
		$this->db->select('SUM(quantity_return*t_sales_items.unit_weigh) as soluong_tralai, SUM(sale_price*quantity_return*t_sales_items.unit_weigh) as thanhtien');
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('t_people', 't_people.person_id = t_sales.customer_id');
		$this->db->join('items', 'items.id = sales_items.item_id');
		$this->db->where('sales.type', 2);
		if($category<> '')
		{
			$this->db->where('items.category', $category);
		}
		if(!empty($customer_id) && $customer_id > 0)
		{
			$this->db->where('sales.customer_id', $customer_id);
		}
		if(!empty($people_manager) && $people_manager > 0)
		{
			$this->db->where('t_people.employees_id', $people_manager);
		}
		$this->db->where('DATE(sale_time) BETWEEN ' .$this->db->escape($start_date). ' AND ' . $this->db->escape($end_date));
		return $this->db->get()->result_array();
	}
}
?>
