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
	<div id="receipt_header" style="margin-top: 15px;">
		<div id="company_name"><span span class="print_header2">PHIẾU TRẢ LẠI</span></div>
		<div id="company_name"><span class="print_header2">KIÊM HÓA ĐƠN THANH TOÁN</span></div>
		<?php
		 $arrdate = explode(" ", $sale_info['sale_time']);
		 $arrdate1 =  explode("-", $arrdate[0]);
		?>
		<div id="company_phone"><span class="print_italic">Ngày <?php echo $arrdate1[2] ?>  tháng  <?php echo $arrdate1[1] ?>  năm <?php echo $arrdate1[0] ?></span></div>
	</div>
	<div>
		<table style="margin-left:25%; border: none"" cellpadding="10">
			<colgroup>
				<col width="32%"><col width="31%">
			</colgroup>
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
	<table style="border: none" cellpadding="10">
		<colgroup>
			<col width="3%"><col width="8%"><col width="30%"><col width="5%"><col width="5%">
			<col width="5%"><col width="15%"><col width="15%">
		</colgroup>
		<thead>
			<tr>
				<th  rowspan="2" class="print_header_table">STT</th>
				<th colspan="2" class="print_header_table">Tên sản phẩm</th>
				<th rowspan="2" class="print_header_table">Loại</th>
				<th colspan="2" class="print_header_table">Số lượng</th>
				<th rowspan="2"  class="print_header_table" style=""><span style="font-size: 18px;">Đơn giá</span> <br> <span style="font-size: 14px;">(VNĐ/KG)</span></th>
				<th rowspan="2" class="print_header_table">Thành tiền</th>
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
			$tongbao = 0;
			$tongkg = 0;
			$i = 0;
			foreach(array_reverse($cart, true) as $line=>$item)
			{
			// hang hoa ban
			if($item['quantity_return'] > 0 ){		
			//echo "<br>"	;echo "<pre>";print_r($item);echo "<pre>"; exit;
			?>
				<tr>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $i+1;?></td>
					<td class="print_normal_table"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo $item['name']; ?>
					</td>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $item['unit_weigh']; ?></td>
					
					<?php $id= 'idcount'.$i ?>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $item['quantity_return'];?></td>	
					<td class="print_normal_table" style="padding: 4px;"><?php echo $item['quantity_return']*$item['unit_weigh'];?></td>
					<td class="print_normal_table" style="text-align: right; padding: 4px;">
						<?php
						// tinh tong kg cho mot loai
						$price = $item['sale_price'];
						$tongbao = $tongbao + $item['quantity_return'];
						$tongkg = $tongkg + $item['quantity_return']*$item['unit_weigh'];
						?>
						<?php echo to_currency_no_money($price); ?>
					</td>
					<?php
					// tinh tong tien
					$total = $price*$item['quantity_return']*$item['unit_weigh'];
					?>
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($total); ?></td>
				</tr>
			<?php $i++; }} ?>
			<?php
			// San pham
			foreach(array_reverse($cart, true) as $line=>$item)
			{
			if($item['quantity'] > 0 ){	
						$tongbao = $tongbao + $item['quantity'];
						$tongkg = $tongkg + $item['quantity']*$item['unit_weigh'];	
			//echo "<br>"	;echo "<pre>";print_r($item);echo "<pre>"; exit;
			?>
				<tr>
					<td class="print_normal_table"><?php echo $i+1;?></td>
					<td class="print_normal_table"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left;">
						<?php echo "Khuyến mại"; ?>
					</td>
					<td class="print_normal_table"><?php echo $item['unit_weigh']; ?></td>
					
					<?php $id= 'idcount'.$i ?>
					<td class="print_normal_table"><?php echo $item['so_luong_bao_tang'];?></td>	
					<td class="print_normal_table"><?php echo $item['discount_tangcho'];?></td>
					<td class="print_normal_table">
					</td>
					<td class="print_normal_table"></td>
				</tr>
			<?php }} ?>
				<!-- ************************************* Cuoc van chuyen **********************************-->
			<?php if($sale_info['car_money'] !== '' && ($sale_info['car_money']>0 || $sale_info['car_money']<0)){ 
				$cuocvanchuyen = ($tongkg)*$sale_info['car_money'];
				?>
			<tr>
				<td colspan="4" class="print_header_table">
					<span >Cước vận chuyển</span>
				</td>
				<td colspan="2" class="print_header_table">
					<span><?php echo $tongkg; ?></span>
				</td>
				<td class="print_header_table" style="text-align: right; padding: 4px;">
					<span><?php echo to_currency_no_money($sale_info['car_money']); ?></span>
				</td class="print_header_table">
				<td class="print_header_table" style="text-align: right; padding: 4px;">
					<span><?php echo to_currency_no_money($cuocvanchuyen); ?></span>
				</td>
			</tr>
			<?php } ?>
				<tr>
					<td class="print_header_table" colspan="7">Tổng giá trị đơn hàng</td>
					<td class="print_header_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($sale_info['pay_money']); ?></td>
				</tr>
				<tr>
					<td class="print_header_table" colspan="7">Số tiền đã thanh toán</td>
					<td class="print_header_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($sale_info['order_money']); ?></td>
				</tr>
				<tr>
					<td class="print_header_table" colspan="2">Bằng chữ</td>
					<td class="print_header_table" colspan="6"><?php echo $name_money; ?></td>
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
<?php //echo $sale_id; ?>
<script type="text/javascript">
	//$('#name_km').attr('rowspan', <?php echo $checkrowspan; ?>);
</script>