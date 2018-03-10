<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error_message))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error_message."</div>";
	exit;
}
?>

<?php $this->load->view('partial/print_receipt'); ?>

<div class="print_hide" id="control_buttons" style="text-align:right">
	<a style="text-align:right;" href="javascript:printdoc();"><div class="btn btn-info btn-sm", id="show_print_button"><?php echo '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'); ?></div></a>
	<?php if($mode == 'sale'){ ?>
	<?php echo anchor("sales/receipt/".$sale_id, '<span class="glyphicon glyphicon-indent-right">&nbsp</span>Hoá đơn thanh toán', array('class'=>'btn btn-info btn-sm', 'id'=>'show_sales_button')); ?>
	<?php } ?>
	<?php if(isset($customer_email) && !empty($customer_email)): ?>
		<a href="javascript:void(0);"><div class="btn btn-info btn-sm", id="show_email_button"><?php echo '<span class="glyphicon glyphicon-envelope">&nbsp</span>' . $this->lang->line('sales_send_receipt'); ?></div></a>
	<?php endif; ?>
	<?php echo anchor("sales", '<span class="glyphicon glyphicon-shopping-cart">&nbsp</span>' . $this->lang->line('sales_register'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_sales_button')); ?>
	<?php echo anchor("sales/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('sales_takings'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_takings_button')); ?>
	
</div>

<script type="text/javascript">
$(document).ready(function()
{
	 $("#show_print_button").click(function()
    {
		window.print();
    });
})

</script>

<link rel="stylesheet" type="text/css" href="css/print.css">
<div id="receipt_wrapper" style="width: 85%;margin-left: 120px;">
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
	<div id="receipt_header" style="margin-top: 15px; border:none">
		<div id="company_name"><span class="print_header2">PHIẾU XUẤT KHO</span></div>
		<?php
		 $arrdate = explode(" ", $sale_info['sale_time']);
		 $arrdate1 =  explode("-", $arrdate[0]);
		?>
		<div id="company_phone"><span class="print_italic">Ngày <?php echo $arrdate1[2] ?>  tháng  <?php echo $arrdate1[1] ?>  năm <?php echo $arrdate1[0] ?></span></div>
	</div>
	<div>
		<table style="margin-left:25%; border:none" cellpadding="10">
			<colgroup>
				<col width="32%"><col width="31%">
			</colgroup>
			<tr>
				<td><span class="print_normal">Mã khách hàng: </span></td>
				<td><span class="print_normal"><?php echo $customer_info[0]['code']; ?></span></td>
			</tr>
			<tr>
				<td><span class="print_normal">Họ tên người mua hàng: </span></td>
				<td><span class="print_normal"><b><?php echo $customer_info[0]['full_name']; ?></b></span></td>
			</tr>
			<tr>
				<td><span class="print_normal">Địa chỉ: </span></td>
				<td><span class="print_normal"><?php echo $customer_info[0]['address']; ?></span></td>
			</tr>
			<tr>
				<td><span class="print_normal">Điện thoại liên lạc: </span></td>
				<td><span class="print_normal"><?php echo $customer_info[0]['phone_number']; ?></span></td>
			</tr>
			<tr>
				<td><span class="print_normal">Lý do: </span></td>
				<td><span class="print_normal"><?php echo $sale_info['comment']; ?></span></td>
			</tr>
		</table>
	</div>

	<table cellpadding="10">
		<colgroup>
			<col width="3%"><col width="12%"><col width="30%"><col width="5%"><col width="5%">
			<col width="5%"><col width="10%"><col width="15%">
		</colgroup>
		<thead>
			<tr>
				<th  rowspan="2" class="print_header_table">STT</th>
				<th colspan="2" class="print_header_table">Tên sản phẩm</th>
				<th rowspan="2" class="print_header_table">Loại</th>
				<th colspan="2" class="print_header_table">Số lượng</th>
			</tr>
			<tr>
				<th class="print_header_table">Mã SP</th>
				<th class="print_header_table">Tên Sản phẩm</th>
				<th class="print_header_table">Bao</th>
				<th class="print_header_table">Kg</th>
			</tr>
		</thead>

		<tbody id="cart_contents">
			<?php
			$giatridonhang = 0;
			$totalbao = 0;
			$totalkg = 0;
			$tongkgtang = 0;
			$i = 0;
			$totalkg_sale = 0;
			$cuocvanchuyen = 0;
			// tinh so tien hang hoa ban
			foreach(array_reverse($cart, true) as $line=>$item)
			{
			$tonghanghoabanra = $item['quantity']+$item['quantity_return'];
			// hang hoa ban
			if($tonghanghoabanra > 0 ){
				$quantitykg = $item['unit_weigh'] * $tonghanghoabanra;
				$totalbao = $totalbao + $tonghanghoabanra;
				$totalkg = $totalkg + $quantitykg;
			?>
				<tr>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $i+1;?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo $item['name']; ?>
					</td>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $item['unit_weigh']; ?></td>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $tonghanghoabanra;?></td>	
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo $quantitykg;?></td>
				</tr>
			<?php $i++; 
				}
			}
			// hang hoa tang
			foreach(array_reverse($cart, true) as $line=>$item)
			{
			if($item['quantity_give'] > 0 ){	
				$totalbao = $totalbao + $item['quantity_give'];
				$quantitytangkg = $item['quantity_give'] * $item['unit_weigh'];
				$totalkg = $totalkg + $quantitytangkg;
			//echo "<br>"	;echo "<pre>";print_r($item);echo "<pre>"; exit;
			?>
				<tr>
					<td class="print_normal_table"><?php echo $i+1;?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo "Khuyến mại"; ?>
					</td>
					<td class="print_normal_table"><?php echo $item['unit_weigh']; ?></td>
					
					<?php $id= 'idcount'.$i ?>
					<td class="print_normal_table"><?php echo $item['quantity_give'];?></td>	
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo $quantitytangkg;?></td>
				</tr>
			<?php }
			} 
			?>
			<?php
			foreach(array_reverse($cart, true) as $line=>$item)
				{
				if($item['quantity_loan'] > 0){
					$totalbao = $totalbao - $item['quantity_loan'];
					$totalkg = $totalkg - $item['quantity_loan'] * $item['unit_weigh'];
					?>
				<tr>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $i+1;?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo "Gửi hàng"; ?>
					</td>
					<td class="print_normal_table"><?php echo $item['unit_weigh']; ?></td>
					<td class="print_normal_table"><?php echo $item['quantity_loan'];?></td>	
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo $item['quantity_loan'] * $item['unit_weigh'];?></td>
				</tr>
				<?php 
				}
				}
			?>
			<?php
			foreach(array_reverse($cart, true) as $line=>$item)
				{
				if($item['quantity_loan_return'] > 0){
					$totalbao = $totalbao + $item['quantity_loan_return'];
					$totalkg = $totalkg + $item['quantity_loan_return'] * $item['unit_weigh'];
				?>
				<tr>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $i+1;?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo "Trả hàng"; ?>
					</td>
					<td class="print_normal_table"><?php echo $item['unit_weigh']; ?></td>
					<td class="print_normal_table"><?php echo $item['quantity_loan_return'];?></td>	
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo $item['quantity_loan_return'] * $item['unit_weigh'];?></td>
				</tr>
				<?php
				}
				}
			?>
			<tr>
				<td colspan="4" class="print_header_table">
					<span>Tổng</span>
				</td>
				<td class="print_header_table">
					<span><?php echo $totalbao; ?></span>
				</td>
				<td class="print_header_table" style="text-align: right; padding: 4px;">
					<span><?php echo $totalkg; ?></span>
				</td>
			</tr>
			<?php if($sale_info['customer_debt']){ ?>	
		</tbody>
	</table>	
	<?php
	}
?>
</div>
<br>
<div class="footer_detail_print" style="margin-left: 150px !important">
	<table style="border: none;">
			<tr style="border: none;">
				<th style="	width: 300px; border: none;" class="print_header_table">Kế toán</th>
				<th style="width: 350px; border: none;"  class="print_header_table">Người nhận hàng</th>
				<th style="width: 300px; border: none;"  class="print_header_table">Thủ kho</th>
			</tr>
			<tr>
				<td style="width: 300px; border: none;" class="print_italic">(Ký, họ tên)</td>
				<td style="width: 350px; border: none;" class="print_italic">(Ký,họ tên)</td>
				<td style="width: 300px; border: none;" class="print_italic">(Ký, họ tên)</td>
			</tr>
	</table>
</div>	
<?php //echo $sale_id; ?>
<script type="text/javascript">
	//$('#name_km').attr('rowspan', <?php echo $checkrowspan; ?>);
</script>

<?php $this->load->view("partial/footer"); ?>