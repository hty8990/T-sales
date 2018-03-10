<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Item_lib
{
	private $CI;

  	public function __construct()
	{
		$this->CI =& get_instance();
	}

	public function get_item_location()
	{
		if(!$this->CI->session->userdata('item_location'))
		{
			$location_id = $this->CI->Stock_location->get_default_location_id();
			$this->set_item_location($location_id);
		}

		return $this->CI->session->userdata('item_location');
	}

	public function set_item_location($location)
	{
		$this->CI->session->set_userdata('item_location',$location);
	}

	public function clear_item_location()
	{
		$this->CI->session->unset_userdata('item_location');
	}	

	public function set_item_price($item)
	{
		$this->CI->session->set_userdata('item_price',$item);
	}
	public function set_item_kit_infor($item)
	{
		$this->CI->session->set_userdata('item_kit_infor',$item);
	}
	public function get_item_kit_infor()
	{
		return $this->CI->session->userdata('item_kit_infor');
	}
	public function set_item_promotion($item)
	{
		$this->CI->session->set_userdata('item_promotion',$item);
	}
	public function get_item_promotion()
	{
		return $this->CI->session->userdata('item_promotion');
	}
	public function get_item_price()
	{

		return $this->CI->session->userdata('item_price');
	}

	public function clear_item_price()
	{
		$this->CI->session->unset_userdata('item_price');
	}
	public function set_type_thuchi($type)
	{
		$this->CI->session->set_userdata('type_thuchi',$type);
	}
	public function get_type_thuchi()
	{

		return $this->CI->session->userdata('type_thuchi');
	}
	public function clear_type_thuchi()
	{
		$this->CI->session->unset_userdata('type_thuchi');
	}
}

?>
