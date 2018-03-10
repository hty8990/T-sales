<?php
class Receiving extends CI_Model
{
	public function get_info($receiving_id)
	{	
		$this->db->from('receivings');
		$this->db->join('people', 'people.person_id = receivings.supplier_id', 'LEFT');
		$this->db->join('suppliers', 'suppliers.person_id = receivings.supplier_id', 'LEFT');
		$this->db->where('receiving_id', $receiving_id);

		return $this->db->get();
	}
	public function get_info_baobi($receiving_id)
	{	
		$this->db->from('receivings_packet');
		$this->db->join('people', 'people.person_id = receivings_packet.supplier_id', 'LEFT');
		$this->db->join('suppliers', 'suppliers.person_id = receivings_packet.supplier_id', 'LEFT');
		$this->db->where('receiving_id', $receiving_id);

		return $this->db->get();
	}
	public function get_receiving_by_reference($reference)
	{
		$this->db->from('receivings');
		$this->db->where('reference', $reference);

		return $this->db->get();
	}

	public function exists($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);

		return ($this->db->get()->num_rows() == 1);
	}
	
	public function update($receiving_data, $receiving_id)
	{
		$this->db->where('receiving_id', $receiving_id);

		return $this->db->update('receivings', $receiving_data);
	}

	public function save($data, $supplier_id, $employee_id,$receiving_id = FALSE)
	{
		$sangbao_tieude = '';
		$sangbao_thanhtien = 0;
		if(isset($data['sang_bao']['sangbao_tieude'])){
			$sangbao_tieude = $data['sang_bao']['sangbao_tieude'];
		}
		if(isset($data['sang_bao']['sangbao_thanhtien'])){
			$sangbao_thanhtien =  str_replace(",","",$data['sang_bao']['sangbao_thanhtien']);
			$sangbao_thanhtien =  str_replace(".","",$sangbao_thanhtien);
		}
		$items = $data['cart'];
		if(count($items) == 0)
		{
			return -1;
		}
		$comment = $data['comment'];
		$reference = $data['reference'];
		$payment_type = $data['payment_type'];
		$sotienthanhtoan = $data['sotienthanhtoan'];
		$giatridonhang = $data['giatridonhang'];
		// luu thong tin mot don nhap hang
		$receivings_data = array(
			'receiving_time' => $data['date_receiving'],
			'supplier_id' => $this->Supplier->exists($supplier_id) ? $supplier_id : NULL,
			'employee_id' => $employee_id,
			'payment_type' => $payment_type,
			'comment' => $comment,
			'order_money' => $giatridonhang,
			'cover_label' => $sangbao_tieude,
			'cover_money' => $sangbao_thanhtien,
			'pay_money' => $sotienthanhtoan,
			'type' => 1,
		);
		$this->db->trans_start();
		//echo "<pre>";print_r($receivings_data);echo"</pre>";
		$this->db->insert('receivings', $receivings_data);
		$receiving_id = $this->db->insert_id();
		// luu san pham
		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);
			// lay gia 
			$receivings_items_data = array(
				'receiving_id' => $receiving_id,
				'item_id' => $item['item_id'],
				'line' => $item['line'],
				'quantity' => $item['quantity'],
				'unit_weigh' => $item['unit_weigh'],
				'input_prices' => $item['price'],
				'item_location' => $item['item_location']
			);
			$this->db->insert('receivings_items', $receivings_items_data);
			//Cap nhat so luong
			$item_quantity = $this->Item_quantity->get_item_quantity($item['item_id'], $item['item_location']);
            $this->Item_quantity->save(array('quantity' => $item_quantity + $item['quantity'], 'item_id' => $item['item_id'],
                                              'location_id' => $item['item_location']), $item['item_id'], $item['item_location']);
		}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}

		return $receiving_id;
	}
	public function savePaket($data, $supplier_id, $employee_id)
	{
		$items = $data['cart'];
		if(count($items) == 0)
		{
			return -1;
		}
		$comment = $data['comment'];
		$giatridonhang = $data['giatridonhang'];
		$sotienthanhtoan = $data['sotienthanhtoan'];
		$receivings_data = array(
			'receiving_time' => $data['date_receiving'],
			'supplier_id' => $this->Supplier->exists($supplier_id) ? $supplier_id : NULL,
			'employee_id' => $employee_id,
			'payment_type' => '',
			'comment' => $comment,
			'order_money' => $giatridonhang,
			'pay_money' => $sotienthanhtoan,
			'type' => 2,
		);
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('receivings', $receivings_data);
		$receiving_id = $this->db->insert_id();
		foreach($items as $line=>$item)
		{

			$receivings_items_data = array(
				'receiving_id' => $receiving_id,
				'item_id' => $item['item_kit_id'],
				'line' => $item['line'],
				'quantity' => $item['quantity'],
				'unit_weigh' => $item['unit_weigh'],
				'input_prices' => $item['price'],
				'item_location' => 1
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
	public function delete_list($receiving_ids, $update_inventory = TRUE)
	{
		$success = TRUE;
		// start a transaction to assure data integrity
		$this->db->trans_start();
		if(sizeof($receiving_ids) == 1){
			foreach($receiving_ids as $receiving_id)
			{
				$success &= $this->delete($receiving_id, $update_inventory);
			}
			// execute transaction
			$this->db->trans_complete();

			$success &= $this->db->trans_status();
		}else{
			$success = false;
		}
		return $success;
	}
	public function delete_packet_list($receiving_ids, $update_inventory = TRUE)
	{
		$success = TRUE;

		// start a transaction to assure data integrity
		$this->db->trans_start();
		if(sizeof($receiving_ids) == 1){
			foreach($receiving_ids as $receiving_id)
			{
				$success &= $this->delete_packet($receiving_id, $update_inventory);
			}

			// execute transaction
			$this->db->trans_complete();

			$success &= $this->db->trans_status();
		}else{
			$success = false;
		}
		return $success;
	}
	public function delete_packet($receiving_id, $update_inventory = TRUE)
	{
		$this->db->trans_start();
		if($update_inventory)
		{
			$items = $this->get_receiving_items($receiving_id)->result_array();
			//echo "<pre>"; print_r($items); echo "</pre>"; exit;
			if($items){
				$this->db->delete('receivings_items', array('receiving_id' => $receiving_id));
			}
		}
		// delete sale itself
		$this->db->delete('receivings', array('receiving_id' => $receiving_id));
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	public function delete($receiving_id, $update_inventory = TRUE)
	{
		// start a transaction to assure data integrity
		$this->db->trans_start();

		if($update_inventory)
		{
			// Xoa hang hoa cu
			$items = $this->get_receiving_items($receiving_id)->result_array();
			if($items){
				$this->db->delete('receivings_items', array('receiving_id' => $receiving_id));
			}
		}
		// xoa don hang do di
		$this->db->delete('receivings', array('receiving_id' => $receiving_id));
		// execute transaction
		$this->db->trans_complete();
	
		return $this->db->trans_status();
	}

	public function get_receiving_items($receiving_id)
	{
		$this->db->from('receivings_items');
		$this->db->where('receiving_id', $receiving_id);

		return $this->db->get();
	}
	public function get_info_itemp_receiving($receiving_id)
	{
		$this->db->from('receivings_items');
		$this->db->join('items', 'items.id = receivings_items.item_id');
		$this->db->where('receivings_items.receiving_id', $receiving_id);

		return $this->db->get()->result_array();
	}
	public function get_info_itemp_baobi($receiving_id)
	{
		$this->db->from('receivings_items');
		$this->db->join('items_packet', 'items_packet.id = receivings_items.item_id');
		$this->db->where('receivings_items.receiving_id', $receiving_id);

		return $this->db->get()->result_array();
	}
	public function get_supplier($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id', $receiving_id);

		return $this->Supplier->get_info($this->db->get()->row()->supplier_id);
	}

	public function get_payment_options()
	{
		return array(
			$this->lang->line('sales_cash') => $this->lang->line('sales_cash'),
			$this->lang->line('sales_check') => $this->lang->line('sales_check'),
			$this->lang->line('sales_debit') => $this->lang->line('sales_debit'),
			$this->lang->line('sales_credit') => $this->lang->line('sales_credit')
		);
	}
	
	public function search_item($type,$search, $filters, $rows = 0, $limit_from = 0, $sort = 'receiving_time', $order = 'desc')
	{
		$this->db->select('receiving_id
			,pay_money,order_money,comment, receiving_time, agency_name, people.full_name');
		$this->db->from('receivings');
		$this->db->join('suppliers', 'receivings.supplier_id = suppliers.person_id','left');
		$this->db->join('people', 'people.person_id = suppliers.person_id','left');
		$this->db->where('date(receiving_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		if($type == 1){
			$this->db->where('type', 1);
			$this->db->or_where('type', 0);
		}elseif($type == 7){
			$this->db->where('type', 7);
		}else{
			$this->db->where('type', 2);
			$this->db->or_where('type', 10);
		}
		if(!empty($search))
		{
			if($filters['is_valid_receipt'] != FALSE)
			{
				$pieces = explode(' ', $search);
				$this->db->where('receiving_id', $pieces[1]);
			}
			else
			{
				$this->db->group_start();
					$this->db->like('customer_last_name', $search);
					$this->db->or_like('customer_first_name', $search);
					$this->db->or_like('customer_name', $search);
					$this->db->or_like('customer_company_name', $search);
				$this->db->group_end();
			}
		}
		$this->db->group_by('receiving_id');
		$this->db->order_by('receiving_time', 'desc');

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}
	public function search_baobi($search, $filters, $rows = 0, $limit_from = 0, $sort = 'receiving_date', $order = 'desc')
	{
		$this->db->select('receiving_id
			,so_tien_thanh_toan,gia_tri_don_hang, 	comment, receiving_time, 
						 agency_name, people.full_name');
		$this->db->from('receivings_packet');
		$this->db->join('suppliers', 'receivings_packet.supplier_id = suppliers.person_id','left');
		$this->db->join('people', 'people.person_id = suppliers.person_id','left');
		$this->db->where('date(receiving_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));

		if(!empty($search))
		{
			if($filters['is_valid_receipt'] != FALSE)
			{
				$pieces = explode(' ', $search);
				$this->db->where('receiving_id', $pieces[1]);
			}
			else
			{
				$this->db->group_start();
					$this->db->like('customer_last_name', $search);
					$this->db->or_like('customer_first_name', $search);
					$this->db->or_like('customer_name', $search);
					$this->db->or_like('customer_company_name', $search);
				$this->db->group_end();
			}
		}

		if($filters['location_id'] != 'all')
		{
			$this->db->where('item_location', $filters['location_id']);
		}

		if($filters['sale_type'] == 'sales')
        {
            $this->db->where('receiving_quantity > 0');
        }
        elseif($filters['sale_type'] == 'returns')
        {
            $this->db->where('receiving_quantity < 0');
        }

		if($filters['only_invoices'] != FALSE)
		{
			$this->db->where('invoice_number IS NOT NULL');
		}


		$this->db->group_by('receiving_id');
		$this->db->order_by('receiving_time', 'desc');

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}
	public function get_found_rows($type,$search, $filters, $limit, $offset, $sort, $order)
	{
		return $this->search_item($type,$search, $filters, $limit, $offset, $sort, $order)->num_rows();
	}
}
?>
