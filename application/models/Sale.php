<?php
class Sale extends CI_Model
{
	public function get_info_by_customer($customer_id)
	{
		$this->db->select(' * ');
		$this->db->from('sales');

		$this->db->where('customer_id', $customer_id);

		return $this->db->get();
	}
	public function get_info($sale_id)
	{
		$this->db->select(' * ');
		$this->db->from('sales');

		$this->db->where('sale_id', $sale_id);

		return $this->db->get();
	}
	public function get_info_return($sale_id)
	{
		$this->db->select(' * ');
		$this->db->from('sales_suspended');

		$this->db->where('sale_id', $sale_id);

		return $this->db->get();
	}
	public function get_info_itemp_sales($sale_id)
	{
		$this->db->from('sales_items');
		$this->db->join('items', 'items.id = sales_items.item_id');
		$this->db->where('sales_items.sale_id', $sale_id);
		$this->db->order_by('line', 'desc');

		return $this->db->get()->result_array();
	}
	public function get_info_itemp_sales_return($sale_id)
	{
		$this->db->from('sales_items');
		$this->db->join('items', 'items.id = sales_items.item_id');
		$this->db->where('sales_items.sale_id', $sale_id);
		return $this->db->get()->result_array();
	}
	/*
	 Get number of rows for the takings (sales/manage) view
	*/
	public function get_found_rows($type,$search, $filters)
	{
		return $this->search($type,$search, $filters)->num_rows();
	}

	/*
	 Get the sales data for the takings (sales/manage) view
	*/
	public function search($type,$search, $filters, $rows = 0, $limit_from = 0, $sort = 'sale_time', $order = 'desc')
	{
		$this->db->select('*, 0 as height_color');
		$this->db->from('sales');
		$this->db->join('people', 'people.person_id = sales.customer_id','left');
		$this->db->join('customers', 'people.person_id = customers.person_id','left');
		if($type ==1){
			$this->db->group_start();
				$this->db->where('sales.type', $type);
				//$this->db->or_where('sales.type', 5);
			$this->db->group_end();
		}else{
			$this->db->where('sales.type', $type);
		}
		$this->db->where('date(sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));

		if(!empty($search))
		{
			$this->db->group_start();
				$this->db->like('t_people.full_name', $search);
				$this->db->or_like('customers.code', $search);
			$this->db->group_end();
		}

		$this->db->group_by('sale_id');
		$this->db->order_by('sale_time', 'desc');

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}

	/*
	 Get the payment summary for the takings (sales/manage) view
	*/
	public function get_payments_summary($search, $filters)
	{
		// get payment summary
		$this->db->select('payment_type, count(*) AS count, SUM(payment_amount) AS payment_amount');
		$this->db->from('sales');
		$this->db->join('sales_payments', 'sales_payments.sale_id = sales.sale_id');
		$this->db->join('people', 'people.person_id = sales.customer_id', 'left');

		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));

		if(!empty($search))
		{
			if($filters['is_valid_receipt'] != FALSE)
			{
				$pieces = explode(' ',$search);
				$this->db->where('sales.sale_id', $pieces[1]);
			}
			else
			{
				$this->db->group_start();
					$this->db->like('full_name', $search);
				$this->db->group_end();
			}
		}

		if($filters['sale_type'] == 'sales')
		{
			$this->db->where('payment_amount > 0');
		}
		elseif($filters['sale_type'] == 'returns')
		{
			$this->db->where('payment_amount < 0');
		}

		if($filters['only_invoices'] != FALSE)
		{
			$this->db->where('invoice_number IS NOT NULL');
		}
		
		if($filters['only_cash'] != FALSE)
		{
			$this->db->like('payment_type', $this->lang->line('sales_cash'), 'after');
		}

		$this->db->group_by('payment_type');

		$payments = $this->db->get()->result_array();

		// consider Gift Card as only one type of payment and do not show "Gift Card: 1, Gift Card: 2, etc." in the total
		$gift_card_count = 0;
		$gift_card_amount = 0;
		foreach($payments as $key=>$payment)
		{
			if( strstr($payment['payment_type'], $this->lang->line('sales_giftcard')) != FALSE )
			{
				$gift_card_count  += $payment['count'];
				$gift_card_amount += $payment['payment_amount'];

				// remove the "Gift Card: 1", "Gift Card: 2", etc. payment string
				unset($payments[$key]);
			}
		}

		if( $gift_card_count > 0 )
		{
			$payments[] = array('payment_type' => $this->lang->line('sales_giftcard'), 'count' => $gift_card_count, 'payment_amount' => $gift_card_amount);
		}

		return $payments;
	}

	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('sales');

		return $this->db->count_all_results();
	}

	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		if(!$this->sale_lib->is_valid_receipt($search))
		{
			$this->db->distinct();
			$this->db->select('full_name');
			$this->db->from('sales');
			$this->db->join('people', 'people.person_id = sales.customer_id');
			$this->db->like('full_name', $search);
			$this->db->or_like('company_name', $search);
			$this->db->order_by('full_name', 'asc');

			foreach($this->db->get()->result_array() as $result)
			{
				$suggestions[] = array('label' => $result['full_name']);
			}
		}
		else
		{
			$suggestions[] = array('label' => $search);
		}

		return $suggestions;
	}

	/*
	Gets total of invoice rows
	*/
	public function get_invoice_count()
	{
		$this->db->from('sales');
		$this->db->where('invoice_number IS NOT NULL');

		return $this->db->count_all_results();
	}

	public function get_sale_by_invoice_number($invoice_number)
	{
		$this->db->from('sales');
		$this->db->where('invoice_number', $invoice_number);

		return $this->db->get();
	}

	public function get_invoice_number_for_year($year = '', $start_from = 0) 
	{
		$year = $year == '' ? date('Y') : $year;
		$this->db->select('COUNT( 1 ) AS invoice_number_year');
		$this->db->from('sales');
		$this->db->where('DATE_FORMAT(sale_time, "%Y" ) = ', $year);
		$this->db->where('invoice_number IS NOT NULL');
		$result = $this->db->get()->row_array();

		return ($start_from + $result['invoice_number_year']);
	}

	public function exists($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id', $sale_id);

		return ($this->db->get()->num_rows()==1);
	}

	public function update($sale_id, $sale_data, $payments)
	{
		$this->db->where('sale_id', $sale_id);
		$success = $this->db->update('sales', $sale_data);

		// touch payment only if update sale is successful and there is a payments object otherwise the result would be to delete all the payments associated to the sale
		if($success && !empty($payments))
		{
			//Run these queries as a transaction, we want to make sure we do all or nothing
			$this->db->trans_start();
			
			// first delete all payments
			$this->db->delete('sales_payments', array('sale_id' => $sale_id));

			// add new payments
			foreach($payments as $payment)
			{
				$sales_payments_data = array(
					'sale_id' => $sale_id,
					'payment_type' => $payment['payment_type'],
					'payment_amount' => $payment['payment_amount']
				);

				$success = $this->db->insert('sales_payments', $sales_payments_data);
			}
			
			$this->db->trans_complete();
			
			$success &= $this->db->trans_status();
		}
		
		return $success;
	}
	public function updatetkmp($customer_id,$listpromotion)
	{
		if($listpromotion !== ""){
			$this->db->where('person_id', $customer_id);
			$this->db->update('customers', array('c_promotion'=>$listpromotion));
			return true;
		}else{
			$this->db->where('person_id', $customer_id);
			$this->db->update('customers', array('c_promotion'=>''));
			return true;
		}
	}
	// cap nhat mot don hang
	public function save($data)
	{
		$items = $data['cart'];
		//echo "<pre>"; print_r($items); echo "</pre>"; exit;
		if(count($items) == 0)
		{
			return -1;
		}
		$payments = $data['payments'];
		$type =1;
		if($payments['payment_type'] == 'Trả thưởng'){
			//$type = 5;
		}
		$thuong_san_luong = $data['thuong_san_luong'];
		$customer = $this->Customer->get_info($data['customer_id']);
		// thong tin ban hang
		$sales_data = array(
			'sale_time'		 => $data['date_sale'],
			'customer_id'	 => $data['customer_id'],
			'employee_id'	 => $data['employee_id'],
			'comment'		 => $data['comments'],
			'payment_type' 	 => $payments['payment_type'],
			'order_money'	 => $data['order_money'],
			'pay_money' 	 => $payments['payment_amount'],
			'car_money'      => $data['get_tranfer'],
			'promotion' 	 => $data['get_stringpromotion'],
			'customer_debt' => $data['customer_debt'],
			'sanluong_tieude' => $thuong_san_luong['tieude'],
			'sanluong_soluong' => $thuong_san_luong['soluong'],
			'sanluong_dongia' => $thuong_san_luong['dongia'],
			'type' => $type,
			
		);
		// Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('sales', $sales_data);
		$sale_id = $this->db->insert_id();
		// Cap nhat lai cach tinh so luong cua mot dot san pham
		//echo "<pre>"; print_r($items); echo "</pre>"; exit;
		foreach($items as $line=>$item)
		{
			$checkreturn = false;
			$quantity_return = 0;
			$quantity = $item['quantity'];
			$quantitytang = $item['quantitytang'];
			// Update so luong
			$quantity_instock = $this->Item_quantity->get_item_quantity($item['item_id'], $item['item_location']);
			$quantityreturn_instock = $this->Item_quantity->get_item_quantityreturn($item['item_id'], $item['item_location']);
			$total_quantity_sale =  $quantity + $quantitytang;
			// kiem tra co check vao o tra lai hay ko
			$arrchecked_returns = $this->sale_lib->get_checked_return();
			if(sizeof($arrchecked_returns) > 0){
				foreach($arrchecked_returns as $arrchecked_return){
					if($item['item_id'] == $arrchecked_return['item_id']){
						$checkreturn = $arrchecked_return['checkreturn'];
					}
				}
			}
			if($checkreturn==='true'){
				// neu tong so luong ban lon hon tong so luong con tra lai
				if($total_quantity_sale > $quantityreturn_instock ){
					$quantity_return = $quantityreturn_instock;
					$soluongconlai = $quantity_instock - ($total_quantity_sale - $quantityreturn_instock);
					$this->Item_quantity->save(array('quantity'		=> $soluongconlai,
												  'quantity_return'		=> 0,
	                                              'item_id'		=> $item['item_id'],
	                                              'location_id'	=> $item['item_location']
	                                              ), 
												$item['item_id']
												, $item['item_location']
											);
				}else{
					$quantity_return = $total_quantity_sale;
					$quantitytang = 0;
					$soluongconlai = $quantityreturn_instock - $total_quantity_sale;
					$this->Item_quantity->save(array('quantity_return'		=> $soluongconlai,
	                                              'item_id'		=> $item['item_id'],
	                                              'location_id'	=> $item['item_location']
	                                              ), 
												$item['item_id']
												, $item['item_location']
											);
				}
			}else{
			}
			$sales_items_data = array(
				'sale_id'			=> $sale_id,
				'item_id'			=> $item['item_id'],
				'description'		=> character_limiter($item['description'], 30),
				'line'				=> $item['line'],
				'quantity'			=> $quantity,
				'quantity_return'	=> $quantity_return,
				'quantity_give'		=> $quantitytang,
				'quantity_loan'		=> $item['hanggui'],
				'quantity_loan_return'	=> $item['hangtra'],
				'unit_weigh'		=> $item['unit_weigh'],
				'sale_price'		=> $item['price'],
				'input_prices'		=> $item['gia_goc'],
				'category'		=> $item['category'],
				'item_location'	=> $item['item_location']
			);
			$this->db->insert('sales_items', $sales_items_data);
			if($item['quantity'] < 0)
			{
				$this->Item->undelete($item['item_id']);
			}
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}	
		return $sale_id;
	}
	// cap nhat gia
	public function updatePrice($item_id,$item_data){
		$this->db->where('item_id', $item_id);
		$success = $this->db->update('items', $item_data);
		return $success;
	}
	// cap nhat mot don hang tra lai
	public function saveReturn($data)
	{
		$items = $data['cart'];
		if(count($items) == 0)
		{
			return -1;
		}
		//echo "<pre>"; print_r($items); echo "</pre>"; exit;
		$payments = $data['payments'];
		$customer = $this->Customer->get_info($data['customer_id']);
		// thong tin ban hang
		$sales_data = array(
			'sale_time'		 => $data['date_sale'],
			'customer_id'	 => $data['customer_id'],
			'employee_id'	 => $data['employee_id'],
			'comment'		 => $data['comments'],
			'payment_type' 	 => $payments['payment_type'],
			'order_money'	 => $payments['payment_amount'],
			'pay_money' 	 => $data['order_money'],
			'car_money'      => $data['get_tranfer'],
			'promotion' 	 => '',
			'customer_debt' => $data['customer_debt'],
			'type' => 2,
			
		);
		// Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		$this->db->insert('sales', $sales_data);
		$sale_id = $this->db->insert_id();
		// Cap nhat lich su mot dot mua ban hang
		foreach($items as $line=>$item)
		{
			// gia khach hang $giakhachhang
			$price = $item['price'];
			$item['quantitykg'] = $item['quantity'] * $item['unit_weigh'];
			//echo $price; exit;
			$sales_items_data = array(
				'sale_id'			=> $sale_id,
				'item_id'			=> $item['item_id'],
				'description'		=> character_limiter($item['description'], 30),
				'line'				=> $item['line'],
				'quantity'			=> 0,
				'quantity_return'	=> $item['quantity'],
				'quantity_give'		=> 0,
				'unit_weigh'		=> $item['unit_weigh'],
				'sale_price'		=> $item['price'],
				'item_location'		=> 1
			);
			//print_r($sales_items_data); exit;
			$this->db->insert('sales_items', $sales_items_data);
			// Update so luong
			$item_quantityreturn = $this->Item_quantity->get_item_quantityreturn($item['item_id'], $item['item_location']);
			$soluongconlai =$item_quantityreturn + $item['quantity'];
			$this->Item_quantity->save(array('quantity_return'	=> $soluongconlai,
                                              'item_id'		=> $item['item_id'],
                                              'location_id'	=> $item['item_location']
                                              ), 
											$item['item_id']
											, $item['item_location']
										);
			
			// if an items was deleted but later returned it's restored with this rule
			if($item['quantity'] < 0)
			{
				$this->Item->undelete($item['item_id']);
			}
		}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		
		return $sale_id;
	}
	// cap nhat mot lan tai che
	public function savetaiche($data)
	{
		$taiche_soluong = 0;
		$taiche_dongia = 0;
		if(isset($data['taiche']['taiche_soluong'])){
			$taiche_soluong = $data['taiche']['taiche_soluong'];
		}
		if(isset($data['taiche']['taiche_dongia'])){
			$taiche_dongia = $data['taiche']['taiche_dongia'];
		}
		$items = $data['cart'];
		if(count($items) == 0)
		{
			return -1;
		}
		$supplier_id = $this->sale_lib->get_supplier();
		$employee_id = $data['employee_id'];
		$payments = $data['payments'];
		// luu thong tin mot don nhap hang
		$receivings_data = array(
			'receiving_time' => $data['date_sale'],
			'supplier_id' => $this->Supplier->exists($supplier_id) ? $supplier_id : NULL,
			'employee_id' => $employee_id,
			'payment_type' => $payments['payment_type'],
			'comment' => $data['comments'],
			'order_money' => $data['order_money'],
			'add_quantity' => $taiche_soluong,
			'add_money' => $taiche_dongia,
			'pay_money' => $payments['payment_amount'],
			'type' => 3,
		);
		$this->db->trans_start();
		//echo "<pre>";print_r($receivings_data);echo"</pre>";
		$this->db->insert('receivings', $receivings_data);
		// Run these queries as a transaction, we want to make sure we do all or nothing
		$receiving_id = $this->db->insert_id();
		// tinh so tien no cua nha cung cap
		// luu san pham
		foreach($items as $line=>$item)
		{
			// lay gia 
			$receivings_items_data = array(
				'receiving_id' => $receiving_id,
				'item_id' => $item['item_id'],
				'line' => $item['line'],
				'quantity' => $item['quantity'],
				'unit_weigh' => $item['unit_weigh'],
				'input_prices' => $item['gia_goc'],
				'item_location' => $item['item_location']
			);
			$this->db->insert('receivings_items', $receivings_items_data);
			//Cap nhat so luong
			$item_quantity = $this->Item_quantity->get_item_quantity($item['item_id'], $item['item_location']);
		}

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		
		return $receiving_id;
	}
	// cap nhat tra lai nha cung cap
	public function savetra_ncc($data)
	{
		$items = $data['cart'];
		if(count($items) == 0)
		{
			return -1;
		}
		$supplier_id = $this->sale_lib->get_supplier();
		$employee_id = $data['employee_id'];
		$payments = $data['payments'];
		// luu thong tin mot don nhap hang
		$receivings_data = array(
			'receiving_time' => $data['date_sale'],
			'supplier_id' => $this->Supplier->exists($supplier_id) ? $supplier_id : NULL,
			'employee_id' => $employee_id,
			'payment_type' => $payments['payment_type'],
			'comment' => $data['comments'],
			'order_money' => $data['order_money'],
			'pay_money' => $payments['payment_amount'],
			'type' => 7,
		);
		$this->db->trans_start();
		//echo "<pre>";print_r($receivings_data);echo"</pre>";
		$this->db->insert('receivings', $receivings_data);
		// Run these queries as a transaction, we want to make sure we do all or nothing
		$receiving_id = $this->db->insert_id();
		// tinh so tien no cua nha cung cap
		// luu san pham
		foreach($items as $line=>$item)
		{
			// lay gia 
			$receivings_items_data = array(
				'receiving_id' => $receiving_id,
				'item_id' => $item['item_id'],
				'line' => $item['line'],
				'quantity' => $item['quantity'],
				'unit_weigh' => $item['unit_weigh'],
				'input_prices' => $item['gia_goc'],
				'item_location' => $item['item_location']
			);
			$this->db->insert('receivings_items', $receivings_items_data);
		}

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		
		return $receiving_id;
	}
	// cap nhat mot don hang
	public function savehuy($data)
	{
		$items = $data['cart'];
		if(count($items) == 0)
		{
			return -1;
		}
		//echo "<pre>"; print_r($items); echo "</pre>"; exit;
		$payments = $data['payments'];
		$customer = $this->Customer->get_info($data['customer_id']);
		// thong tin ban hang
		$sales_data = array(
			'sale_time'		 => $data['date_sale'],
			'customer_id'	 => $data['customer_id'],
			'employee_id'	 => $data['employee_id'],
			'comment'		 => $data['comments'],
			'payment_type' 	 => $payments['payment_type'],
			'order_money'	 => $payments['payment_amount'],
			'pay_money' 	 => $data['order_money'],
			'car_money'      => $data['get_tranfer'],
			'promotion' 	 => '',
			'customer_debt' => $data['customer_debt'],
			'type' => 3,
			
		);
		// Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		$this->db->insert('sales', $sales_data);
		$sale_id = $this->db->insert_id();
		// Cap nhat lich su mot dot mua ban hang
		foreach($items as $line=>$item)
		{
			// gia khach hang $giakhachhang
			$price = $item['gia_goc'];
			$item['quantitykg'] = $item['quantity'] * $item['unit_weigh'];
			//echo $price; exit;
			$sales_items_data = array(
				'sale_id'			=> $sale_id,
				'item_id'			=> $item['item_id'],
				'description'		=> character_limiter($item['description'], 30),
				'line'				=> $item['line'],
				'quantity'			=> $item['quantity'],
				'quantity_return'	=> 0,
				'quantity_give'		=> 0,
				'unit_weigh'		=> $item['unit_weigh'],
				'sale_price'		=> $item['gia_goc'],
				'item_location'		=> 1
			);
			//print_r($sales_items_data); exit;
			$this->db->insert('sales_items', $sales_items_data);
			//Cap nhat so luong
			$item_quantity = $this->Item_quantity->get_item_quantity($item['item_id'], $item['item_location']);
            $this->Item_quantity->save(array('quantity' => $item_quantity - $item['quantity'], 'item_id' => $item['item_id'],
                                              'location_id' => $item['item_location']), $item['item_id'], $item['item_location']);	
			// if an items was deleted but later returned it's restored with this rule
			if($item['quantity'] < 0)
			{
				$this->Item->undelete($item['item_id']);
			}
		}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		
		return $sale_id;
	}
	public function delete_list($sale_ids, $employee_id, $update_inventory = TRUE) 
	{
		$mode = $this->sale_lib->get_mode();
		$result = TRUE;
		foreach($sale_ids as $sale_id)
		{
			if($mode == 'sale'){
				$result &= $this->delete($sale_id, $employee_id, $update_inventory);
			}elseif($mode == 'return'){
				$result &= $this->delete_return($sale_id, $employee_id, $update_inventory);
			}elseif($mode == 'taiche'){
				$result &= $this->delete_taiche($sale_id, $employee_id, $update_inventory);
			}elseif($mode == 'huy'){
				$result &= $this->delete_huy($sale_id, $employee_id, $update_inventory);
			}
			
		}

		return $result;
	}

	public function delete($sale_id, $employee_id, $update_inventory = TRUE) 
	{
		// start a transaction to assure data integrity
		$this->db->trans_start();
		// xoa tat ca bang ban hang
		$this->db->delete('sales_items', array('sale_id' => $sale_id));
		// delete sale itself
		$this->db->delete('sales', array('sale_id' => $sale_id));
		// execute transaction
		$this->db->trans_complete();
	
		return $this->db->trans_status();
	}
	public function delete_return($sale_id, $employee_id, $update_inventory = TRUE) 
	{
		// start a transaction to assure data integrity
		$this->db->trans_start();
		// xoa tat ca bang ban hang
		$this->db->delete('sales_items', array('sale_id' => $sale_id));
		// delete sale itself
		$this->db->delete('sales', array('sale_id' => $sale_id));
		// delete sale itself
		
		// execute transaction
		$this->db->trans_complete();
	
		return $this->db->trans_status();
	}
	public function delete_huy($sale_id, $employee_id, $update_inventory = FALSE) 
	{
		// start a transaction to assure data integrity
		$this->db->trans_start();
		$this->db->delete('sales_items', array('sale_id' => $sale_id));
		// xoa tat ca bang ban hang
		$this->db->delete('sales', array('sale_id' => $sale_id));
		// delete sale itself
		
		// execute transaction
		$this->db->trans_complete();
	
		return $this->db->trans_status();
	}
	public function delete_taiche($sale_id, $employee_id, $update_inventory = TRUE) 
	{
		// start a transaction to assure data integrity
		$this->db->trans_start();
		$this->db->delete('sales_suspended_items', array('sale_id' => $sale_id));
		// xoa tat ca bang ban hang
		$this->db->delete('sales_suspended', array('sale_id' => $sale_id));
		// delete sale itself
		
		// execute transaction
		$this->db->trans_complete();
	
		return $this->db->trans_status();
	}
	public function get_sale_items($sale_id)
	{
		$this->db->from('sales_items');
		$this->db->where('sale_id', $sale_id);

		return $this->db->get();
	}

	public function get_sale_payments($sale_id)
	{
		$this->db->from('sales_payments');
		$this->db->where('sale_id', $sale_id);

		return $this->db->get();
	}

	public function get_payment_options($giftcard = TRUE)
	{
		$payments = array();
		
		$payments[$this->lang->line('sales_cash')] = $this->lang->line('sales_cash');
		$payments[$this->lang->line('sales_credit')] = $this->lang->line('sales_credit');
		$payments['Trả thưởng'] = 'Trả thưởng';
		return $payments;
	}

	public function get_customer($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id', $sale_id);

		return $this->Customer->get_info($this->db->get()->row()->customer_id);
	}

	public function invoice_number_exists($invoice_number, $sale_id = '')
	{
		$this->db->from('sales');
		$this->db->where('invoice_number', $invoice_number);
		if(!empty($sale_id))
		{
			$this->db->where('sale_id !=', $sale_id);
		}
		
		return ($this->db->get()->num_rows() == 1);
	}

	public function get_giftcard_value($giftcardNumber)
	{
		if(!$this->Giftcard->exists($this->Giftcard->get_giftcard_id($giftcardNumber)))
		{
			return 0;
		}
		
		$this->db->from('giftcards');
		$this->db->where('giftcard_number', $giftcardNumber);

		return $this->db->get()->row()->value;
	}
}
?>
