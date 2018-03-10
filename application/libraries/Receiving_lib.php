<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Receiving_lib
{
	private $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}
	public function set_sangbao($sang_bao)
	{
		$this->CI->session->set_userdata('sang_bao', $sang_bao);
	}
	public function get_sangbao()
	{
		return $this->CI->session->userdata('sang_bao');
	}
	public function empty_sangbao()
	{
		$this->CI->session->unset_userdata('sang_bao');
	}


	public function set_receiving_id($receiving_id)
	{
		$this->CI->session->set_userdata('receiving_id', $receiving_id);
	}
	public function get_receiving_id()
	{
		return $this->CI->session->userdata('receiving_id');
	}
	public function empty_receiving_id()
	{
		$this->CI->session->unset_userdata('receiving_id');
	}
	public function get_cart()
	{
		if(!$this->CI->session->userdata('recv_cart'))
		{
			$this->set_cart(array());
		}

		return $this->CI->session->userdata('recv_cart');
	}

	public function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('recv_cart', $cart_data);
	}

	public function empty_cart()
	{
		$this->CI->session->unset_userdata('recv_cart');
	}

	public function get_supplier()
	{
		if(!$this->CI->session->userdata('recv_supplier'))
		{
			$this->set_supplier(-1);
		}

		return $this->CI->session->userdata('recv_supplier');
	}

	public function set_supplier($supplier_id)
	{
		$this->CI->session->set_userdata('recv_supplier', $supplier_id);
	}

	public function remove_supplier()
	{
		$this->CI->session->unset_userdata('recv_supplier');
	}

	public function get_mode()
	{
		if(!$this->CI->session->userdata('recv_mode'))
		{
			$this->set_mode('receive');
		}

		return $this->CI->session->userdata('recv_mode');
	}

	public function set_mode($mode)
	{
		$this->CI->session->set_userdata('recv_mode', $mode);
	}
	
	public function clear_mode()
	{
		$this->CI->session->unset_userdata('recv_mode');
	}

	public function get_stock_source()
	{
		if(!$this->CI->session->userdata('recv_stock_source'))
		{
			$this->set_stock_source($this->CI->Stock_location->get_default_location_id());
		}

		return $this->CI->session->userdata('recv_stock_source');
	}
	
	public function get_comment()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$comment = $this->CI->session->userdata('recv_comment');

		return empty($comment) ? '' : $comment;
	}
	
	public function set_comment($comment)
	{
		$this->CI->session->set_userdata('recv_comment', $comment);
	}
	
	public function clear_comment()
	{
		$this->CI->session->unset_userdata('recv_comment');
	}
    
    public function get_date_receiving() 
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$receiving_date = $this->CI->session->userdata('receiving_date');

    	return empty($receiving_date) ? date("d/m/Y") : $receiving_date;
	}
	public function clear_date_receiving() 	
	{
		$this->CI->session->unset_userdata('receiving_date');
	}
	public function set_date_receiving($receiving_date) 
	{
		$this->CI->session->set_userdata('receiving_date', $receiving_date);
	}
	public function get_reference()
	{
		return $this->CI->session->userdata('recv_reference');
	}
	
	public function set_reference($reference)
	{
		$this->CI->session->set_userdata('recv_reference', $reference);
	}
	
	public function clear_reference()
	{
		$this->CI->session->unset_userdata('recv_reference');
	}
	
	public function is_print_after_sale()
	{
		return $this->CI->session->userdata('recv_print_after_sale') == 'true' ||
				$this->CI->session->userdata('recv_print_after_sale') == '1';
	}
	
	public function set_print_after_sale($print_after_sale)
	{
		return $this->CI->session->set_userdata('recv_print_after_sale', $print_after_sale);
	}
	
	public function set_stock_source($stock_source)
	{
		$this->CI->session->set_userdata('recv_stock_source', $stock_source);
	}
	
	public function clear_stock_source()
	{
		$this->CI->session->unset_userdata('recv_stock_source');
	}
	
	public function get_stock_destination()
	{
		if(!$this->CI->session->userdata('recv_stock_destination'))
		{
			$this->set_stock_destination($this->CI->Stock_location->get_default_location_id());
		}

		return $this->CI->session->userdata('recv_stock_destination');
	}

	public function set_stock_destination($stock_destination)
	{
		$this->CI->session->set_userdata('recv_stock_destination', $stock_destination);
	}
	
	public function clear_stock_destination()
	{
		$this->CI->session->unset_userdata('recv_stock_destination');
	}

	public function add_item($item_id, $quantity = 1, $price = NULL)
	{
		 $item_location = 1;
		$nexline = $item_id;
		if(!$this->CI->Item->exists($item_id))
		{
			$item_id = $this->CI->Item->get_item_id($item_id);
			if(!$item_id)
			{
				return FALSE;
			}
		}

		$items = $this->get_cart();
         $quantitytang = 0;
         $quantitykg = 0;
         $quantitytangkg = 0;
        $maxkey = 0;                       //Highest key so far
        $itemalreadyinsale = FALSE;        //We did not find the item yet.
		$insertkey = 0;                    //Key to use for new entry.
		$updatekey = 0;                    //Key to use to update(quantity)
        $item_info = $this->CI->Item->get_info($item_id,  $item_location);
        //echo "<pre>"; print_r($item_info); echo "</pre>";
        if($item_info->unit_weigh > 0){
            $quantitykg = $item_info->unit_weigh;
        }
        foreach($items as $item)
		{
            //We primed the loop so maxkey is 0 the first time.
            //Also, we have stored the key in the element itself so we can compare.
			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if($item['item_id'] == $item_id && $item['item_location'] == $item_location)
			{
				$itemalreadyinsale = TRUE;
				$updatekey = $item['line'];
               // if(!$item_info->is_serialized)
               // {
                //    $quantity = bcadd($quantity, $items[$updatekey]['quantity']);
               // }
			}
		}
		$insertkey = $maxkey+1;
		$start_date = DateTime::createFromFormat('d/m/Y',$this->get_date_receiving())->format('Y-m-d H:i:s');
		// Lay gia nhap vao theo khoang thoi gian
		$arrResultprices = $this->CI->Item->get_prices_by_time_customer($item_info->id,$start_date);
		//array/cart records are identified by $insertkey and item_id is just another field.
		$price = $price != NULL ? $price : $arrResultprices['input_prices'];
		if(!$itemalreadyinsale)
		{
			$item = array($insertkey => array(
	                'item_id' => $item_id,
	                'nexline' => $nexline,
	                'item_location' => $item_location,
	                'line' => $insertkey,
	                'name' => $item_info->name,
	                'unit_weigh' => $item_info->unit_weigh,
	                'category' => $item_info->category,
	                'item_number' => $item_info->item_number,
	                'quantity' => $quantity,
	                'quantitykg' => $quantitykg,
	                'in_stock' => $this->CI->Item_quantity->get_item_quantity($item_id, $item_location),
	                'stock_name' => $this->CI->Stock_location->get_location_name($item_location),
	                'price' => $price
	            )
	        );
			//add to existing array
			$items += $item;
		}
        else
        {
            $line = &$items[$updatekey];
            $line['quantity'] = $quantity;
        }	
		$this->set_cart($items);
		return TRUE;
	}

	public function edit_item($line, $description, $serialnumber,$quantity, $quantitykg, $quantitytang,$quantitytangkg, $price)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			$start_date = DateTime::createFromFormat('d/m/Y',$this->get_date_receiving())->format('Y-m-d H:i:s');
			// Lay gia nhap vao theo khoang thoi gian
			$arrResultprices = $this->CI->Item->get_prices_by_time_customer($items[$line]['item_id'],$start_date);
			//array/cart records are identified by $insertkey and item_id is just another field.
			$line = &$items[$line];
			$line['description'] = $description;
			$line['serialnumber'] = $serialnumber;
			$line['quantity'] = $quantity;
			$line['price'] = $price;
			//$line['total'] = $this->get_item_total($quantity, $price, $discount); 
			$this->set_cart($items);
		}

		return FALSE;
	}
	public function edit_item_kit($line, $quantity)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			$line = &$items[$line];
			$line['quantity'] = $quantity;
			//$line['total'] = $this->get_item_total($quantity, $price, $discount); 
			$this->set_cart($items);
		}

		return FALSE;
	}
	public function delete_item($line)
	{
		$items = $this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}

	public function is_valid_receipt($receipt_receiving_id)
	{
		//RECV #
		$pieces = explode(' ', $receipt_receiving_id);

		if(count($pieces) == 2 && preg_match('/(RECV|KIT)/', $pieces[1]))
		{
			return $this->CI->Receiving->exists($pieces[1]);
		}
		else 
		{
			return $this->CI->Receiving->get_receiving_by_reference($receipt_receiving_id)->num_rows() > 0;
		}

		return FALSE;
	}

	public function is_valid_item_kit($item_kit_id)
	{
		//KIT #
		$pieces = explode(' ',$item_kit_id);

		if(count($pieces) == 2)
		{
			return $this->CI->Item_kit->exists($pieces[1]);
		}

		return FALSE;
	}

	public function add_item_packet($item_id, $quantity = 1, $price = NULL)
	{
		$nexline = $item_id;
		//make sure item exists in database.
		if(!$this->CI->Item_kit->exists($item_id))
		{
			return FALSE;
		}
		$items = $this->get_cart();
        $maxkey = 0;                       //Highest key so far
        $itemalreadyinsale = FALSE;        //We did not find the item yet.
		$insertkey = 0;                    //Key to use for new entry.
		$updatekey = 0;                    //Key to use to update(quantity)
        $item_info = $this->CI->Item_kit->get_info($item_id);
		foreach($items as $item)
		{
			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}
			if($item['item_kit_id'] == $item_id)
			{
				$itemalreadyinsale = TRUE;
				$updatekey = $item['line'];
			}
		}
		$insertkey = $maxkey+1;
		$start_date = DateTime::createFromFormat('d/m/Y',$this->get_date_receiving())->format('Y-m-d H:i:s');
		//array/cart records are identified by $insertkey and item_id is just another field.
		$arrResultprice = $this->CI->Item_kit->get_packet_price_by_time($item_id,$start_date);
		$price = $price != NULL ? $price : $arrResultprice['input_prices'];
		//Item already exists and is not serialized, add to quantity
		if(!$itemalreadyinsale)
		{
             $item = array($insertkey => array(
	                'item_kit_id' => $item_id,
	                'nexline' => $nexline,
	                'line' => $insertkey,
	                'name' => $item_info->name,
	                'unit_weigh' => $item_info->unit_weight,
	                'item_number' => $item_info->item_number,
	                'description' => $item_info->description,
	                //'is_serialized' => $item_info->is_serialized,
	                'quantity' => $quantity,
	                'quantity_in_stock' => $this->CI->Item->baobitonkhodauky($item_id,$start_date),
	                'price' => $price
	            )
	        );
			//add to existing array
			$items += $item;
		}
        else
        {
            $line = &$items[$updatekey];
            $line['quantity'] = $quantity;
            $line['total'] =to_currency($total);
        }
		$this->set_cart($items);
		return TRUE;
	}

	public function add_item_kit($external_item_kit_id, $item_location)
	{
		//KIT #
		$pieces = explode(' ',$external_item_kit_id);
		$item_kit_id = $pieces[1];
		
		foreach($this->CI->Item_kit_items->get_info($item_kit_id) as $item_kit_item)
		{
			$this->add_item($item_kit_item['item_id'],$item_kit_item['quantity'], $item_location);
		}
	}

	public function copy_entire_receiving($receiving_id)
	{
		$this->empty_cart();
		$this->remove_supplier();

		foreach($this->CI->Receiving->get_receiving_items($receiving_id)->result() as $row)
		{
			$this->add_item($row->item_id, $row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber, $row->receiving_quantity);
		}
		$this->set_supplier($this->CI->Receiving->get_supplier($receiving_id)->person_id);
		//$this->set_reference($this->CI->Receiving->get_info($receiving_id)->row()->reference);
	}

	public function clear_all()
	{
		$this->empty_receiving_id(); 
		$this->empty_cart();
		$this->remove_supplier();
		$this->clear_comment();
		$this->clear_reference();
		$this->clear_date_receiving();
		$this->empty_sangbao();
	}

	public function get_item_total($quantity, $price)
	{
		$total = bcmul($quantity, $price);
		//$discount_fraction = bcdiv($discount_percentage, 100);
		//$discount_amount = bcmul($total, $discount_fraction);

		return $total;
	}

	public function get_total()
	{
		$total = 0;
		foreach($this->get_cart() as $item)
		{
			$total = bcadd($total, $this->get_item_total($item['quantity'], $item['price']));
		}
		
		return $total;
	}
}

?>
