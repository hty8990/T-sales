<?php
class Item_quantity extends CI_Model
{
    public function exists($item_id, $location_id)
    {
        $this->db->from('item_quantities');
        $this->db->where('item_id', $item_id);
        $this->db->where('location_id', $location_id);

        return ($this->db->get()->num_rows() == 1);
    }
    public function existscustumer($item_id,$customer,$price)
    {
        $this->db->from('price_custumer');
        $this->db->where('pk_custumer', $customer);
        $this->db->where('pk_item', $item_id);

        return ($this->db->get()->num_rows() == 1);
    }
    public function save($location_detail, $item_id, $location_id)
    {
        if(!$this->exists($item_id, $location_id))
        {
            return $this->db->insert('item_quantities', $location_detail);
        }

        $this->db->where('item_id', $item_id);
        $this->db->where('location_id', $location_id);

        return $this->db->update('item_quantities', $location_detail);
    }
    public function savepaket($quantities, $item_id)
    {
        $arrData = array(
                'quantities' => $quantities
        );

        $this->db->where('id', $item_id);

        return $this->db->update('t_items_packet', $arrData);
    }
    public function savecustumerprice($item_id,$customer,$price)
    {
        $arrData = array(
                'c_money' => $price,
                'pk_custumer' => $customer,
                'pk_item' => $item_id
        );
        if(!$this->existscustumer($item_id,$customer,$price))
        {
            return $this->db->insert('price_custumer', $arrData);
        }

       $this->db->where('pk_custumer', $customer);
        $this->db->where('pk_item', $item_id);

        return $this->db->update('price_custumer', $arrData);
    }
    /**
    public function get_item_quantity($item_id, $location_id)
    {     
        $this->db->from('item_quantities');
        $this->db->where('item_id', $item_id);
        $this->db->where('location_id', $location_id);
        $result = $this->db->get()->row();

        if(!isset($result))
        {
            return 0; 
        }
        return $result->quantity;   
    }
    **/
    public function get_item_quantity($item_id, $location_id)
    {     
        $tondauky =  $this->Giftcard->BC09_hanghoantonkho('kytruoc',$item_id, '2999-01-01');
        return $tondauky;   
    }
    public function get_item_quantityreturn($item_id, $location_id)
    {     
        $this->db->from('item_quantities');
        $this->db->where('item_id', $item_id);
        $this->db->where('location_id', $location_id);
        $result = $this->db->get()->row();

        if(!isset($result))
        {
            return 0; 
        }
        return $result->quantity_return;   
    }
	public function get_item_quantity_paket($item_id)
    {     
        $this->db->from('items_packet');
        $this->db->where('id', $item_id);
        $result = $this->db->get()->row();
        if(empty($result) == TRUE)
        {
            return 0;         
        }
        return $result->quantities; 
        
       
    }
    public function get_item_quantity_paket_from_weght($code)
    {     
        $this->db->from('items_packet');
        $this->db->where('id', $code);
        foreach($this->db->get()->result() as $row)
        {
            $suggestions[] = array('id' => $row->id, 'quantities' => $row->quantities);
        }
        return $suggestions; 
    }
    /*
    * Set to 0 all quantity in the given item
    */
    public function update_item_quantity_paket_from_weght($item_kit_id,$quantity)
    {
        $this->db->where('id', $item_kit_id);

        return $this->db->update('items_packet', array('quantities' => $quantity));
    }
	/*
	 * changes to quantity of an item according to the given amount.
	 * if $quantity_change is negative, it will be subtracted,
	 * if it is positive, it will be added to the current quantity
	 */
	public function change_quantity($item_id, $location_id, $quantity_change)
	{
		$quantity_old = $this->get_item_quantity($item_id, $location_id);
		$quantity_new = $quantity_old->quantity + intval($quantity_change);
		$location_detail = array('item_id' => $item_id, 'location_id' => $location_id, 'quantity' => $quantity_new);

		return $this->save($location_detail, $item_id, $location_id);
	}
	
	/*
	* Set to 0 all quantity in the given item
	*/
	public function reset_quantity($item_id)
	{
        $this->db->where('item_id', $item_id);

        return $this->db->update('item_quantities', array('quantity' => 0));
	}
	
	/*
	* Set to 0 all quantity in the given list of items
	*/
	public function reset_quantity_list($item_ids)
	{
        $this->db->where_in('item_id', $item_ids);

        return $this->db->update('item_quantities', array('quantity' => 0));
	}
}
?>