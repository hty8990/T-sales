<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sale_lib
{
	private $CI;

  	public function __construct()
	{
		$this->CI =& get_instance();
	}
	public function get_supplier()
	{
		if(!$this->CI->session->userdata('sale_supplier'))
		{
			$this->set_supplier(-1);
		}

		return $this->CI->session->userdata('sale_supplier');
	}
	public function clear_reference()
	{
		$this->CI->session->unset_userdata('recv_reference');
	}
	public function set_sale_id($sale_id)
	{
		$this->CI->session->set_userdata('set_sale_id', $sale_id);
	}
	public function get_sale_id()
	{
		return $this->CI->session->userdata('set_sale_id');
	}
	public function remove_sale_id()
	{
		$this->CI->session->unset_userdata('set_sale_id');
	}
	public function set_supplier($supplier_id)
	{
		$this->CI->session->set_userdata('sale_supplier', $supplier_id);
	}

	public function remove_supplier()
	{
		$this->CI->session->unset_userdata('sale_supplier');
	}
	public function get_cart()
	{
		if(!$this->CI->session->userdata('sales_cart'))
		{
			$this->set_cart(array());
		}

		return $this->CI->session->userdata('sales_cart');
	}

	public function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('sales_cart', $cart_data);
	}

	public function empty_cart()
	{
		$this->CI->session->unset_userdata('sales_cart');
	}
	
	public function get_comment() 
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$comment = $this->CI->session->userdata('sales_comment');

    	return empty($comment) ? '' : $comment;
	}

	public function set_comment($comment) 
	{
		$this->CI->session->set_userdata('sales_comment', $comment);
	}
	public function get_date_sale() 
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$sales_date = $this->CI->session->userdata('sales_date');

    	return empty($sales_date) ? date("d/m/Y") : $sales_date;
	}
	public function clear_date_sale() 	
	{
		$this->CI->session->unset_userdata('sales_date');
	}
	
	public function set_checked_return($checked_return) 
	{
		$this->CI->session->set_userdata('checked_return', $checked_return);
	}
	public function get_checked_return()
	{
		return $this->CI->session->userdata('checked_return');
	}
	public function clear_checked_return() 	
	{
		$this->CI->session->unset_userdata('checked_return');
	}	

	public function set_date_sale($date_sale) 
	{
		$this->CI->session->set_userdata('sales_date', $date_sale);
	}
	public function clear_comment() 	
	{
		$this->CI->session->unset_userdata('sales_comment');
	}
	
	public function get_invoice_number()
	{
		return $this->CI->session->userdata('sales_invoice_number');
	}
	
	public function set_invoice_number($invoice_number, $keep_custom = FALSE)
	{
		$current_invoice_number = $this->CI->session->userdata('sales_invoice_number');
		if(!$keep_custom || empty($current_invoice_number))
		{
			$this->CI->session->set_userdata('sales_invoice_number', $invoice_number);
		}
	}
	
	public function clear_invoice_number()
	{
		$this->CI->session->unset_userdata('sales_invoice_number');
	}
	
	public function is_invoice_number_enabled() 
	{
		return ($this->CI->session->userdata('sales_invoice_number_enabled') == 'true' ||
				$this->CI->session->userdata('sales_invoice_number_enabled') == '1') &&
				$this->CI->config->item('invoice_enable') == TRUE;
	}
	
	public function set_invoice_number_enabled($invoice_number_enabled)
	{
		return $this->CI->session->set_userdata('sales_invoice_number_enabled', $invoice_number_enabled);
	}
	
	public function is_print_after_sale() 
	{
		return ($this->CI->session->userdata('sales_print_after_sale') == 'true' ||
				$this->CI->session->userdata('sales_print_after_sale') == '1');
	}
	
	public function set_print_after_sale($print_after_sale)
	{
		return $this->CI->session->set_userdata('sales_print_after_sale', $print_after_sale);
	}
	
	public function get_email_receipt() 
	{
		return $this->CI->session->userdata('sales_email_receipt');
	}

	public function set_email_receipt($email_receipt) 
	{
		$this->CI->session->set_userdata('sales_email_receipt', $email_receipt);
	}

	public function clear_email_receipt() 	
	{
		$this->CI->session->unset_userdata('sales_email_receipt');
	}

	// Multiple Payments
	public function get_payments()
	{
		if(!$this->CI->session->userdata('sales_payments'))
		{
			$this->set_payments(array());
		}

		return $this->CI->session->userdata('sales_payments');
	}

	// Multiple Payments
	public function set_payments($payments_data)
	{
		$this->CI->session->set_userdata('sales_payments', $payments_data);
	}
	public function unset_payment() 	
	{
		$this->CI->session->unset_userdata('sales_payments');
	}
	// Multiple Payments
	public function add_payment($payment_id, $payment_amount)
	{
		$payment = array('payment_type' => $payment_id, 'payment_amount' => $payment_amount);
		$this->set_payments($payment);
	}

	// Multiple Payments
	public function edit_payment($payment_id, $payment_amount)
	{
		$payments = $this->get_payments();
		if(isset($payments[$payment_id]))
		{
			$payments[$payment_id]['payment_type'] = $payment_id;
			$payments[$payment_id]['payment_amount'] = $payment_amount;
			$this->set_payments($payments);

			return TRUE;
		}

		return FALSE;
	}

	// Multiple Payments
	public function delete_payment($payment_id)
	{
		$payments = $this->get_payments();
		unset($payments[urldecode($payment_id)]);
		$this->set_payments($payments);
	}

	// Multiple Payments
	public function empty_payments()
	{
		$this->CI->session->unset_userdata('sales_payments');
	}

	// Multiple Payments
	public function get_payments_total()
	{
		$subtotal = 0;
		foreach($this->get_payments() as $payments)
		{
		    $subtotal = bcadd($payments['payment_amount'], $subtotal);
		}

		return $subtotal;
	}

	// Multiple Payments
	public function get_amount_due()
	{
		$payment_total = $this->get_payments_total();
		$sales_total = $this->get_total();
		$amount_due = bcsub($sales_total, $payment_total);
		$precision = $this->CI->config->item('currency_decimals');
		$rounded_due = bccomp(round($amount_due, $precision, PHP_ROUND_HALF_EVEN), 0, $precision);
		// take care of rounding error introduced by round tripping payment amount to the browser
 		return  $rounded_due == 0 ? 0 : $amount_due;
	}

	public function get_customer()
	{
		if(!$this->CI->session->userdata('sales_customer'))
		{
			$this->set_customer(-1);
		}

		return $this->CI->session->userdata('sales_customer');
	}
    public function get_pricecustumer($customer_info)
	{
		if($this->CI->session->userdata('sales_customer') !== -1)
		{
			$person_id = $customer_info->person_id;
			//echo $listpromotion;
			$arrpromotion = $this->CI->Customer->getcustumer_prince($person_id);
			return $arrpromotion;
		}else{
			return "";
		}		
	}
	
	public function get_promotion($customer_info)
	{
		if($this->CI->session->userdata('sales_customer') !== -1)
		{
			$suggestions = "";
			$listpromotion = $customer_info->c_promotion;
			//echo $listpromotion;
			$arrpromotion = $this->CI->Customer->getall_promotion();
			foreach($arrpromotion as $value){
				if (strpos(','.$listpromotion.',', ','.$value['promotion_code'].',') !== false) {
					$suggestions[] = array('promotion_type' => $value['promotion_type']
						,'promotion_code' => $value['promotion_code']
						,'promotion_name' => $value['promotion_name']
						,'promotion_pecen' => $value['promotion_pecen']
						,'promotion_kg' => $value['promotion_kg']
						,'promotion_sttt' => $value['promotion_sttt']
						,'item_promotion' => $value['item_promotion']
					);
				  // echo $value['promotion_code']; echo "<br>";
				}
				
			}
			//echo "<pre>";var_dump($suggestions);echo "</pre>";
			return $suggestions;
		}else{
			return "";
		}		
	}
	public function get_promotion_bysales($customer_info)
	{
		$suggestions = "";
			$listpromotion = $customer_info->c_promotion;
			//echo $listpromotion;
			$arrpromotion = $this->CI->Customer->getall_promotion();
			foreach($arrpromotion as $value){
				if (strpos(','.$listpromotion.',', ','.$value['promotion_code'].',') !== false) {
					$suggestions[] = array('promotion_type' => $value['promotion_type']
						,'promotion_code' => $value['promotion_code']
						,'promotion_name' => $value['promotion_name']
						,'promotion_pecen' => $value['promotion_pecen']
						,'promotion_kg' => $value['promotion_kg']
						,'promotion_sttt' => $value['promotion_sttt']
					);
				  // echo $value['promotion_code']; echo "<br>";
				}
				
			}
			//echo "<pre>";var_dump($suggestions);echo "</pre>";
			return $suggestions;		
	}
	public function set_customer($customer_id)
	{
		$this->CI->session->set_userdata('sales_customer', $customer_id);
	}
	public function set_stringpromotion($stringpromotion)
	{
		$this->CI->session->set_userdata('stringpromotion', $stringpromotion);
	}
	public function set_customer_debt($customer_debt)
	{
		$this->CI->session->set_userdata('customer_debt', $customer_debt);
	}
	public function get_customer_debt()
	{
		return $this->CI->session->userdata('customer_debt');
	}
	public function remove_customer_debt()
	{
		$this->CI->session->unset_userdata('customer_debt');
	}
	public function get_stringpromotion()
	{
		return $this->CI->session->userdata('stringpromotion');
	}
	public function remove_stringpromotion()
	{
		$this->CI->session->unset_userdata('stringpromotion');
	}
	public function remove_customer()
	{
		$this->CI->session->unset_userdata('sales_customer');
	}

	public function get_mode()
	{
		if(!$this->CI->session->userdata('sales_mode'))
		{
			$this->set_mode('sale');
		}

		return $this->CI->session->userdata('sales_mode');
	}

	public function set_mode($mode)
	{
		$this->CI->session->set_userdata('sales_mode', $mode);
	}

	public function clear_mode()
	{
		$this->CI->session->unset_userdata('sales_mode');
	}
	public function set_taiche($taiche)
	{
		$this->CI->session->set_userdata('sales_taiche', $taiche);
	}
	public function get_taiche()
	{
		return $this->CI->session->userdata('sales_taiche');
	}
	public function clear_taiche()
	{
		$this->CI->session->unset_userdata('sales_taiche');
	}

	public function set_thuongsanluong($thuongsanluong)
	{
		$this->CI->session->set_userdata('sales_thuongsanluong', $thuongsanluong);
	}
	public function get_thuongsanluong()
	{
		return $this->CI->session->userdata('sales_thuongsanluong');
	}
	public function clear_thuongsanluong()
	{
		$this->CI->session->unset_userdata('sales_thuongsanluong');
	}
	public function set_tranfer($tranfer)
	{
		$this->CI->session->set_userdata('sales_tranfer', $tranfer);
	}
	public function get_tranfer()
	{
		if(!$this->CI->session->userdata('sales_tranfer'))
		{
			$this->set_tranfer(0);
		}

		return $this->CI->session->userdata('sales_tranfer');
	}
	public function clear_tranfer()
	{
		$this->CI->session->unset_userdata('sales_tranfer');
	}
    public function get_sale_location()
    {
        if(!$this->CI->session->userdata('sales_location'))
        {
			$this->set_sale_location($this->CI->Stock_location->get_default_location_id());
        }

        return $this->CI->session->userdata('sales_location');
    }

    public function set_sale_location($location)
    {
        $this->CI->session->set_userdata('sales_location', $location);
    }
    
    public function clear_sale_location()
    {
    	$this->CI->session->unset_userdata('sales_location');
    }
    
    public function set_giftcard_remainder($value)
    {
    	$this->CI->session->set_userdata('sales_giftcard_remainder', $value);
    }
    
    public function get_giftcard_remainder()
    {
    	return $this->CI->session->userdata('sales_giftcard_remainder');
    }
    
    public function clear_giftcard_remainder()
    {
    	$this->CI->session->unset_userdata('sales_giftcard_remainder');
    }
    
	public function add_item($item_id, $quantity = 1, $quantitytang = 0, $price = NULL,$quantitytralai=0,$hanggui = 0, $hangtra = 0)
	{
		//make sure item exists	     
		if($this->validate_item($item_id) == FALSE)
        {
            return FALSE;
        }
		// Serialization and Description

		//Get all items in the cart so far...
		$items = $this->get_cart();

        //We need to loop through all items in the cart.
        //If the item is already there, get it's key($updatekey).
        //We also need to get the next key that we are going to use in case we need to add the
        //item to the cart. Since items can be deleted, we can't use a count. we use the highest key + 1.
        $maxkey = 0;                       //Highest key so far
        $itemalreadyinsale = FALSE;        //We did not find the item yet.
		$insertkey = 0;                    //Key to use for new entry.
		$updatekey = 0;                    //Key to use to update(quantity)
		$description = '';
        $item_info = $this->CI->Item->get_info($item_id, 1);
        if($item_info->unit_weigh > 0){
            $quantitykg = $item_info->unit_weigh;
        }
        //var_dump($item_info); //exit;
		foreach($items as $item)
		{
            //Also, we have stored the key in the element itself so we can compare.
			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if($item['item_id'] == $item_id)
			{
				$itemalreadyinsale = TRUE;
				$updatekey = $item['line'];
				$quantity = $item['quantity'] + 1;
			}
		}
		$insertkey = $maxkey+1;
		$start_date = DateTime::createFromFormat('d/m/Y',$this->get_date_sale())->format('Y-m-d H:i:s');
		$customer_id = $this->get_customer();
		// tinh gia hien tai dua vao khoang thoi gian
		$arrResultprices = $this->CI->Item->get_prices_by_time_customer($item_id,$start_date,$customer_id);
		//print_r($arrResultprices); exit;
		//array/cart records are identified by $insertkey and item_id is just another field.
		$price = $price != NULL ? $price : $arrResultprices['sale_price'];
		//Item already exists and is not serialized, add to quantity
		if($quantitytralai > 0){
			$conlaitrongkho =	$this->CI->Item_quantity->get_item_quantity($item_id, 1);
			$conlaitralai =	$this->CI->Item_quantity->get_item_quantityreturn($item_id, 1);
			$conlaitralai = $conlaitralai + $quantitytralai;
			if($quantitytralai < $quantity + $quantitytang){
				$conlaitrongkho = $conlaitrongkho + (($quantity + $quantitytang) - $conlaitralai);
			}
			$checkreturn = true;
		}else{
			$conlaitrongkho =	$this->CI->Item_quantity->get_item_quantity($item_id, 1);
			$conlaitralai =	$this->CI->Item_quantity->get_item_quantityreturn($item_id, 1);
			$checkreturn = false;
		}
		if(!$itemalreadyinsale)
		{
            $item = array($insertkey => array(
                    'item_id' => $item_id,
                    'item_location' => 1,
                    'stock_name' => $this->CI->Stock_location->get_location_name(1),
                    'line' => $insertkey,
                    'name' => $item_info->name,
                    'packet_id' => $item_info->packet_id,
                    'category' => $item_info->category,
                    'unit_weigh' => $item_info->unit_weigh,
                    'item_number' => $item_info->item_number,
                    'description' => $description!=NULL ? $description: $item_info->description,
                    'quantity' => $quantity,
                    'quantitytang' => $quantitytang,
                    'hanggui' => $hanggui,
                    'hangtra' => $hangtra,
                    'in_stock' => $conlaitrongkho,
                    'in_stock_return' => $conlaitralai,
                    'price' => $price,
                    'gia_goc' => $arrResultprices['input_prices'],
                    'checkreturn' => $checkreturn
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
	
	public function out_of_stock($item_id, $item_location)
	{
		//make sure item exists
		if($this->validate_item($item_id) == FALSE)
        {
            return FALSE;
        }

		$item_info = $this->CI->Item->get_info($item_id);
		//$item = $this->CI->Item->get_info($item_id);
		$item_quantity = $this->CI->Item_quantity->get_item_quantity($item_id,$item_location);
		$quantity_added = $this->get_quantity_already_added($item_id,$item_location);

		if($item_quantity - $quantity_added < 0)
		{
			return $this->CI->lang->line('sales_quantity_less_than_zero');
		}
		//elseif($item_quantity - $quantity_added < $item_info->reorder_level)
		//{
		//	return $this->CI->lang->line('sales_quantity_less_than_reorder_level');
		//}

		return FALSE;
	}
	
	public function get_quantity_already_added($item_id, $item_location)
	{
		$items = $this->get_cart();
		$quanity_already_added = 0;
		foreach($items as $item)
		{
			if($item['item_id'] == $item_id && $item['item_location'] == $item_location)
			{
				$quanity_already_added+=$item['quantity'];
			}
		}
		
		return $quanity_already_added;
	}
	
	public function get_item_id($line_to_get)
	{
		$items = $this->get_cart();

		foreach($items as $line=>$item)
		{
			if($line == $line_to_get)
			{
				return $item['item_id'];
			}
		}
		
		return -1;
	}

	public function edit_item($line, $description, $serialnumber, $quantity, $quantitytang, $price,$hanggui,$hangtra)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))	
		{
			$quantitykg =  $items[$line]['unit_weigh']*$quantity;
			$line = &$items[$line];
			$line['description'] = $description;
			$line['serialnumber'] = $serialnumber;
			$line['quantity'] = $quantity;
            $line['quantitytang'] = $quantitytang;
			$line['price'] = $price;
			$line['hanggui'] = $hanggui;
			$line['hangtra'] = $hangtra;
			$line['total'] = $this->get_item_total($quantitykg, $price, 0);
			$line['discounted_total'] = $this->get_item_total($quantitykg, $price, TRUE);
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

	public function is_valid_receipt(&$receipt_sale_id)
	{
		//POS #
		$pieces = explode(' ', $receipt_sale_id);

		if(count($pieces) == 2 && strtolower($pieces[0]) == 'pos')
		{
			return $this->CI->Sale->exists($pieces[1]);
		}
		elseif($this->CI->config->item('invoice_enable') == TRUE)
		{
			$sale_info = $this->CI->Sale->get_sale_by_invoice_number($receipt_sale_id);
			if($sale_info->num_rows() > 0)
			{
				$receipt_sale_id = 'POS ' . $sale_info->row()->sale_id;

				return TRUE;
			}
		}

		return FALSE;
	}
	
	public function is_valid_item_kit($item_kit_id)
	{
		//KIT #
		$pieces = explode(' ', $item_kit_id);

		if(count($pieces) == 2)
		{
			return $this->CI->Item_kit->exists($pieces[1]);
		}

		return FALSE;
	}

	public function return_entire_sale($receipt_sale_id)
	{
		//POS #
		$pieces = explode(' ', $receipt_sale_id);
		$sale_id = $pieces[1];

		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id, -$row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber);
		}
		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);
	}
	
	public function add_item_kit($external_item_kit_id, $item_location)
	{
		//KIT #
		$pieces = explode(' ', $external_item_kit_id);
		$item_kit_id = $pieces[1];
		
		foreach($this->CI->Item_kit_items->get_info($item_kit_id) as $item_kit_item)
		{
			$this->add_item($item_kit_item['item_id'], $item_kit_item['quantity'], $item_location);
		}
	}

	public function copy_entire_sale($sale_id)
	{
		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id, $row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber);
		}
		foreach($this->CI->Sale->get_sale_payments($sale_id)->result() as $row)
		{
			$this->add_payment($row->payment_type, $row->payment_amount);
		}
		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);
	}
	

	public function clear_all()
	{
		$this->clear_checked_return();
		$this->clear_thuongsanluong();
		$this->remove_sale_id();
		$this->set_invoice_number_enabled(FALSE);
		$this->remove_stringpromotion();
		$this->remove_customer_debt();
		$this->empty_cart();
		$this->clear_comment();
		$this->clear_tranfer();
		$this->clear_date_sale();
		$this->clear_email_receipt();
		$this->clear_invoice_number();
		$this->clear_giftcard_remainder();
		$this->empty_payments();
		$this->remove_customer();
		$this->clear_taiche();
	}
	
	public function is_customer_taxable()
	{
		$customer_id = $this->get_customer();
		$customer = $this->CI->Customer->get_info($customer_id);
		
		//Do not charge sales tax if we have a customer that is not taxable
		return $customer->taxable or $customer_id == -1;
	}

	public function get_taxes()
	{
		$taxes = array();

		//Do not charge sales tax if we have a customer that is not taxable
		if($this->is_customer_taxable())
		{
			foreach($this->get_cart() as $line=>$item)
			{
				$tax_info = $this->CI->Item_taxes->get_info($item['item_id']);

				foreach($tax_info as $tax)
				{
					$name = to_tax_decimals($tax['percent']) . '% ' . $tax['name'];
					$tax_amount = $this->get_item_tax($item['quantity'], $item['price'], $item['discount'], $tax['percent']);

					if(!isset($taxes[$name]))
					{
						$taxes[$name] = 0;
					}

					$taxes[$name] = bcadd($taxes[$name], $tax_amount);
				}
			}
		}

		return $taxes;
	}
	
	public function get_discount()
	{
		$discount = 0;
		foreach($this->get_cart() as $line=>$item)
		{
			if($item['discount'] > 0)
			{
				$item_discount = $this->get_item_discount($item['quantity'], $item['price'], $item['discount']);
				$discount = bcadd($discount, $item_discount);
			}
		}

		return $discount;
	}

	public function get_subtotal($include_discount=FALSE, $exclude_tax=FALSE)
	{
		$subtotal = $this->calculate_subtotal($include_discount, $exclude_tax);		
		return $subtotal;
	}
	
	public function get_item_total_tax_exclusive($item_id, $quantity, $price, $discount_percentage, $include_discount = FALSE) 
	{
		$tax_info = $this->CI->Item_taxes->get_info($item_id);
		$item_price = $this->get_item_total($quantity, $price, $discount_percentage, $include_discount);
		// only additive tax here
		foreach($tax_info as $tax)
		{
			$tax_percentage = $tax['percent'];
			$item_price = bcsub($item_price, $this->get_item_tax($quantity, $price, $discount_percentage, $tax_percentage));
		}
		
		return $item_price;
	}
	
	public function get_item_total($quantity, $price, $discount_percentage, $include_discount = FALSE)  
	{
		$total = bcmul($quantity, $price);
		if($include_discount)
		{
			$discount_amount = $this->get_item_discount($quantity, $price, $discount_percentage);

			return bcsub($total, $discount_amount);
		}

		return $total;
	}
	
	public function get_item_discount($quantity, $price, $discount_percentage)
	{
		$total = bcmul($quantity, $price);
		$discount_fraction = bcdiv($discount_percentage, 100);

		return bcmul($total, $discount_fraction);
	}
	
	public function get_item_tax($quantity, $price, $discount_percentage, $tax_percentage) 
	{
		$price = $this->get_item_total($quantity, $price, $discount_percentage, TRUE);
		if($this->CI->config->config['tax_included'])
		{
			$tax_fraction = bcadd(100, $tax_percentage);
			$tax_fraction = bcdiv($tax_fraction, 100);
			$price_tax_excl = bcdiv($price, $tax_fraction);

			return bcsub($price, $price_tax_excl);
		}
		$tax_fraction = bcdiv($tax_percentage, 100);

		return bcmul($price, $tax_fraction);
	}

	public function calculate_subtotal($include_discount = FALSE, $exclude_tax = FALSE) 
	{
		$subtotal = 0;
		foreach($this->get_cart() as $item)
		{
			if($exclude_tax && $this->CI->config->config['tax_included'])
			{
				$subtotal = bcadd($subtotal, $this->get_item_total_tax_exclusive($item['item_id'], $item['quantity'], $item['price'], $item['discount'], $include_discount));
			}
			else 
			{
				$subtotal = bcadd($subtotal, $this->get_item_total($item['quantity'], $item['price'], $item['discount'], $include_discount));
			}
		}

		return $subtotal;
	}

	public function get_total()
	{
		$total = $this->calculate_subtotal(TRUE);		
		if(!$this->CI->config->config['tax_included'])
		{
			foreach($this->get_taxes() as $tax)
			{
				$total = bcadd($total, $tax);
			}
		}

		return $total;
	}
    
    public function validate_item(&$item_id)
    {
        //make sure item exists
        if(!$this->CI->Item->exists($item_id))
        {
            //try to get item id given an item_number
            $mode = $this->get_mode();
            $item_id = $this->CI->Item->get_item_id($item_id);

            if(!$item_id)
			{
				return FALSE;
			}
        }

        return TRUE;
    }
}

?>
