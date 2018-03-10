<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error_message))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error_message."</div>";
	exit;
}
?>

<?php $this->load->view('partial/print_receipt', array('print_after_sale', $print_after_sale, 'selected_printer'=>'receipt_printer')); ?>

<div class="print_hide" id="control_buttons" style="text-align:right">
	<a href="javascript:printdoc();"><div class="btn btn-info btn-sm", id="show_print_button"><?php echo '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'); ?></div></a>
	<?php echo anchor("receivings", '<span class="glyphicon glyphicon-save">&nbsp</span>' . $this->lang->line('receivings_register'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_sales_button')); ?>
		<?php echo anchor("receivings/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('recive_takings'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_takings_button')); ?>
</div>

<link rel="stylesheet" type="text/css" href="css/print.css">
<div id="receipt_wrapper" style="width: 80%; margin-left:140px !important;">
	<table  style="margin-left:15%; border: none">
		<colgroup>
			<col width="20%"><col width="80%">
		</colgroup>
		<tr>
			<td class="xl71" rowspan="4"><img height="115" width="178" id="image" src="<?php echo base_url('images/logo_larg.png'); ?>" alt="company_logo" /></td>
			<td class="xl71" style="text-align: center;"><span class="print_header1"><?php echo mb_strtoupper($this->config->item('company'),'utf8'); ?></span></td>
		</tr>
		<tr>
			<td style="text-align: center;"><span class="print_header1"><?php echo $this->config->item('address'); ?></span></td>
		</tr>
		<tr>
			<td style="text-align: center;"><span class="print_header1"><?php echo $this->config->item('phone'); ?></span></td>
		</tr>
		<tr>
			<td style="text-align: center;"><span class="print_header1"><?php echo $this->config->item('fax'); ?></span></td>
		</tr>
	</table>
	<div id="receipt_header" style="margin-top: 15px;">
		<div id="company_name"><span span class="print_header2">PHIẾU NHẬP BAO BÌ</span></div>
		<div id="company_name"><span class="print_header2">KIÊM HÓA ĐƠN THANH TOÁN</span></div>
		<?php
		 $arrdate = explode(" ", $sale_info['receiving_time']);
		 $arrdate1 =  explode("-", $arrdate[0]);
		?>
		<div id="company_phone"><span class="print_italic">Ngày <?php echo $arrdate1[2] ?>  tháng  <?php echo $arrdate1[1] ?>  năm <?php echo $arrdate1[0] ?></span></div>
	</div>
	<div>
		<table style="margin-left:25%; border: none" cellpadding="10">
			<colgroup>
				<col width="32%"><col width="31%">
			</colgroup>
			<tr>
				<td><span class="print_normal">Họ tên nhà cung cấp: </span></td>
				<td><span class="print_normal"><b><?php echo $sale_info['full_name']; ?></b></span></td>
			</tr>
			<tr>
				<td><span class="print_normal">Địa chỉ: </span></td>
				<td><span class="print_normal"><?php echo $sale_info['address']; ?></span></td>
			</tr>
			<tr>
				<td><span class="print_normal">Điện thoại liên lạc: </span></td>
				<td><span class="print_normal"><?php echo $sale_info['phone_number']; ?></span></td>
			</tr>
			<tr>
				<td><span class="print_normal">Lý do: </span></td>
				<td><span class="print_normal"><?php echo $sale_info['comment']; ?></span></td>
			</tr>
		</table>
	</div>

	<table cellpadding="10">
		<colgroup>
			<col width="3%"><col width="8%"><col width="30%"><col width="5%">
			<col width="5%"><col width="15%"><col width="15%">
		</colgroup>
		<thead>
			<tr>
				<th class="print_header_table">STT</th>
				<th class="print_header_table">Mã SP</th>
				<th class="print_header_table">Tên Bao bì</th>
				<th class="print_header_table">Loại</th>
				<th class="print_header_table">Số lượng</th>
				<th class="print_header_table" style=""><span style="font-size: 18px;">Đơn giá</th>
				<th class="print_header_table">Thành tiền</th>
			</tr>
		</thead>

		<tbody id="cart_contents">
			<?php
			$giatridonhang = 0;
			$tongbao = 0;
			$tongkg = 0;
			$i = 0;
			foreach(array_reverse($cart, true) as $line=>$item)
			{
			//echo "<br>"	;echo "<pre>";print_r($item);echo "<pre>"; exit;
			// hang hoa ban
			if($item['quantity'] !== 0 ){		
			//
			?>
				<tr>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $i+1;?></td>
					<td class="print_normal_table"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo $item['name']; ?>
					</td>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $item['unit_weight']; ?></td>
					<td class="print_normal_table" style="padding: 4px;"><?php echo (int)$item['quantity'];?></td>
					<td class="print_normal_table" style="padding: 4px;"><?php echo to_currency_no_money($item['input_prices']);?></td>
					<?php
					// tinh tong tien
					$total = $item['quantity']*$item['input_prices'];
					$giatridonhang  = $giatridonhang  + $total;
					?>
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($total); ?></td>
				</tr>
			<?php $i++; }} ?>
			<tr>
					<td class="print_header_table" colspan="6">Tổng giá trị đơn hàng</td>
					<td class="print_header_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($sale_info['order_money']); ?></td>
				</tr>
				<tr>
					<td class="print_header_table" colspan="6">Số tiền đã thanh toán</td>
					<td class="print_header_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($sale_info['pay_money']); ?></td>
				</tr>
				<tr>
					<td class="print_header_table" colspan="2">Bằng chữ</td>
					<td class="print_header_table" colspan="5"><?php echo $name_money; ?></td>
				</tr>
		</tbody>
	</table>	
</div>
<br>
<div class="footer_detail_print" style="margin-left: 150px !important">
	<table style="border: none;">
			<tr style="border: none;">
				<th style="	width: 300px; border: none;" class="print_header_table">Kế toán</th>
				<th style="width: 300px; border: none;"  class="print_header_table">Người nhận hàng</th>
				<th style="width: 300px; border: none;"  class="print_header_table">Thủ kho</th>
			</tr>
			<tr>
				<td style="width: 300px; border: none;" class="print_italic">(Ký, họ tên)</td>
				<td style="width: 300px; border: none;" class="print_italic">(Ký,họ tên)</td>
				<td style="width: 300px; border: none;" class="print_italic">(Ký, họ tên)</td>
			</tr>
	</table>
</div>		

<script type="text/javascript">
$(document).ready(function()
{
	// mau in
	 $("#show_print_button").click(function()
    {
		window.print();
    });
})

</script>

<?php $this->load->view("partial/footer"); ?>
