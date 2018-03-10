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
		<div id="company_name"><span span class="print_header2">PHIẾU XUẤT KHO</span></div>
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
			$totalbao = 0;
			$totalkg = 0;
			$tongkgtang = 0;
			$i = 0;
			$checktang = false;
			$checkgui = false;
			$checktra = false;
			$totalkg_sale = 0;
			$cuocvanchuyen = 0;
			$totalkgvanchuyen = 0;
			// tinh so tien hang hoa ban
			foreach(array_reverse($cart, true) as $line=>$item)
			{
			if($item['quantity_give'] > 0 ){
				$checktang = true;
			}
			if($item['quantity_loan'] > 0){
				$checkgui = true;
			}
			if($item['quantity_loan_return'] > 0){
				$checktra = true;
			}
			$tonghanghoabanra = $item['quantity']+$item['quantity_return'];
			// hang hoa ban
			if($tonghanghoabanra > 0 ){	
			//echo "<br>"	;echo "<pre>";print_r($item);echo "<pre>"; exit;
			?>
				<tr>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $i+1;?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo $item['name']; ?>
					</td>
					<td class="print_normal_table" style="padding: 4px;"><?php echo $item['unit_weigh']; ?></td>
					<?php $quantitykg = $item['unit_weigh'] * $tonghanghoabanra; ?>	
					<td class="print_normal_table" style="padding: 4px;"><?php echo $tonghanghoabanra;?></td>	
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo $quantitykg;?></td>
					<td class="print_normal_table" style="text-align: right; padding: 4px;">
						<?php
						// tinh tong kg cho mot loai
						$price = $item['sale_price'];
						?>
						<?php echo to_currency_no_money($price); ?>
					</td>
					<?php
					// tinh tong tien
					$total = $price*$quantitykg;
					$giatridonhang  = $giatridonhang  + $total;
					foreach($arrListtype as $key => $value){
						if (strpos($item['category'],$key) !== false) {
							if(isset($arrTotal['kl'][$key])){
									$arrTotal['kl'][$key] = $arrTotal['kl'][$key] + $quantitykg;	
							}else{
								$arrTotal['kl'][$key] = $quantitykg;
							}
							if(isset($arrTotal['money'][$key])){
									$arrTotal['money'][$key] = $arrTotal['money'][$key] + $total;	
							}else{
								$arrTotal['money'][$key] = $total;
							}
						}
					}
					$totalbao = $totalbao + $tonghanghoabanra;
					$totalkg = $totalkg + $quantitykg;
					$totalkg_sale = $totalkg_sale + $quantitykg;
					?>
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($total); ?></td>
				</tr>
			<?php $i++; 
				}
			}
			$j = $i;
			$tongkgkhuyenmai =0;
			if($checktang)
			{
				// hang hoa tang
				foreach(array_reverse($cart, true) as $line=>$item)
				{
				if($item['quantity_give'] > 0){
					//$totalbaovanchuyen = $totalbao + $item['quantity_give'];
				$quantitytangkg = $item['quantity_give'] * $item['unit_weigh'];
				$tongkgkhuyenmai = $tongkgkhuyenmai + $quantitytangkg;
				//echo "<br>"	;echo "<pre>";print_r($item);echo "<pre>"; exit;
				?>
				<tr>
					<td class="print_normal_table"><?php echo $j+1;?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo "Khuyến mại"; ?>
					</td>
					<td class="print_normal_table"><?php echo $item['unit_weigh']; ?></td>
					
					<?php $id= 'idcount'.$i ?>
					<td class="print_normal_table"><?php echo $item['quantity_give'];?></td>	
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo $quantitytangkg;?></td>
					<td class="print_normal_table">
					</td>
					<td class="print_normal_table"></td>
				</tr>
				<?php 
				$j++;
				} 
				}
			}
			$k = $j;
			$tongkggui =0;
			if($checkgui){
				// hang hoa tang
				foreach(array_reverse($cart, true) as $line=>$item)
				{
				if($item['quantity_loan'] > 0){
					$tongkggui = $tongkggui + $item['quantity_loan'] * $item['unit_weigh'];
					?>
				<tr>
					<td class="print_normal_table"><?php echo $k+1;?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo "Gửi hàng"; ?>
					</td>
					<td class="print_normal_table"><?php echo $item['unit_weigh']; ?></td>
					<td class="print_normal_table"><?php echo $item['quantity_loan'];?></td>	
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo $item['quantity_loan'] * $item['unit_weigh'];?></td>
					<td class="print_normal_table">
					</td>
					<td class="print_normal_table"></td>
				</tr>
				<?php 
					$k++;
				}
				}
				
			}
			$f = $k;
			$tongkgtra = 0;
			if($checktra){
				// hang hoa tra
				foreach(array_reverse($cart, true) as $line=>$item)
				{
				if($item['quantity_loan_return'] > 0){
					$tongkgtra = $tongkgtra + $item['quantity_loan_return'] * $item['unit_weigh'];
				?>
				<tr>
					<td class="print_normal_table"><?php echo $f+1;?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;"><?php echo $item['item_number']; ?></td>
					<td class="print_normal_table" style="text-align: left; padding: 4px;">
						<?php echo "Trả hàng"; ?>
					</td>
					<td class="print_normal_table"><?php echo $item['unit_weigh']; ?></td>
					<td class="print_normal_table"><?php echo $item['quantity_loan_return'];?></td>	
					<td class="print_normal_table" style="text-align: right; padding: 4px;"><?php echo $item['quantity_loan_return'] * $item['unit_weigh'];?></td>
					<td class="print_normal_table">
					</td>
					<td class="print_normal_table"></td>
				</tr>
				<?php 
					$f++;
				}
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
				<td class="print_header_table" style="padding: 4px;"></td>
				<td class="print_header_table" style="text-align: right; padding: 4px;">
					<span><?php echo to_currency_no_money($giatridonhang); ?></span>
				</td>
			</tr>
			<?php if($sale_info['customer_debt']){ ?>
			<!-- ************************************* No cu*************************************-->
			<tr>
				<td colspan="7" class="print_header_table">
					<span >Nợ cũ</span>
				</td>
				<td class="print_header_table" style="text-align: right; padding: 4px;"> 
					<span><?php echo to_currency_no_money($sale_info['customer_debt']); ?></span>
				</td>
			</tr>
			<?php } ?>
			<!-- ************************************* Cuoc van chuyen **********************************-->
			<?php if($sale_info['car_money'] !== '' && ($sale_info['car_money']>0 || $sale_info['car_money']<0)){ 
				$cuocvanchuyen = ($totalkg + $tongkgkhuyenmai - $tongkggui + $tongkgtra)*$sale_info['car_money'];
				?>
			<tr>
				<td colspan="4" class="print_header_table">
					<span >Cước vận chuyển</span>
				</td>
				<td colspan="2" class="print_header_table">
					<span><?php echo $totalkg + $tongkgkhuyenmai - $tongkggui + $tongkgtra; ?></span>
				</td>
				<td class="print_header_table" style="text-align: right; padding: 4px;">
					<span><?php echo to_currency_no_money($sale_info['car_money']); ?></span>
				</td class="print_header_table">
				<td class="print_header_table" style="text-align: right; padding: 4px;">
					<span><?php echo to_currency_no_money($cuocvanchuyen); ?></span>
				</td>
			</tr>
			<?php } ?>
			<!-- ************************************* Thuong sang luong**********************************-->
			<?php if($sale_info['sanluong_soluong'] <> 0 && $sale_info['sanluong_dongia'] <> 0){ 
				$tongtiensanluong = $sale_info['sanluong_soluong']*$sale_info['sanluong_dongia'];
				?>
			<tr>
				<td colspan="4" class="print_header_table">
					<span><?php echo $sale_info['sanluong_tieude']; ?></span>
				</td>
				<td colspan="2" class="print_header_table">
					<span><?php echo $sale_info['sanluong_soluong']; ?></span>
				</td>
				<td class="print_header_table" style="text-align: right; padding: 4px;">
					<span><?php echo to_currency_no_money($sale_info['sanluong_dongia']); ?></span>
				</td>
				<td class="print_header_table" style="text-align: right; padding: 4px;">
					<span><?php echo to_currency_no_money($tongtiensanluong); ?></span>
				</td>
			</tr>
			<?php } ?>
		<!-- 
***************************************************************************************************
************************************* Chuong trinh khuyen mai *************************************
***************************************************************************************************
	-->
	<?php 
	if($sale_info['promotion'] && $cart) {
		$arrPromotions = get_promotion_helper($sale_info['promotion'],$arrTotal,$cart,$totalkg_sale,$giatridonhang);
		//echo "<pre>"; print_r($arrPromotions); echo "</pre>"; exit;
			//echo $sale_info['chuong_trinh_khuyen_mai']; exit;
		$typecheck = '';
			foreach($arrPromotions as $arrPromotion) {				
				if(isset($arrPromotion['soluong']) && $arrPromotion['soluong'] >0){
					
					if($typecheck !== $arrPromotion['type']){
						$typecheck = $arrPromotion['type'];
						?>
						<tr><td style="	border-bottom-style: none" colspan="2" class="print_header_table" id="name_km" ><span><?php echo $arrPromotion['list_name']; ?></span></td>
						<?php
					}else{
						?>
						<tr><td colspan="2" style="border-left: 1px solid windowtext"></td>
						<?php
					}
					?>
					<!-- kiem tra xem la khuyen mai kg hay % -->
					
					<td colspan="2" class="print_header_table" style="text-align: left; padding: 4px;"><span><?php echo $arrPromotion['name']?></span></td>
					<!-- Tinh so luong -->
					<td colspan="2" class="print_header_table"><span><?php echo $arrPromotion['soluong'] ?></span></td>
					<td class="print_header_table" style="text-align: right; padding: 4px;"><span><?php echo $arrPromotion['dongia']; ?></span></td>
					<td class="print_header_table" style="text-align: right; padding: 4px;"><span><?php echo  to_currency_no_money($arrPromotion['money']); ?></span></td>
				</tr>
				<?php
				} 
			} ?>
				<tr>
					<td class="print_header_table" colspan="7">Tổng giá trị đơn hàng</td>
					<td class="print_header_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($sale_info['order_money']); ?></td>
				</tr>
				<?php if($sale_info['pay_money'] > 0){
				?>
				<tr>
					<td class="print_header_table" colspan="7">Số tiền thanh toán</td>
					<td class="print_header_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($sale_info['pay_money']); ?></td>
				</tr>
				<?php	
					} ?>
				<tr>
					<td class="print_header_table" colspan="7">Tổng cộng</td>
					<?php $tongcong = ($sale_info['order_money'] + $sale_info['customer_debt']) - $sale_info['pay_money']; ?>
					<td class="print_header_table" style="text-align: right; padding: 4px;"><?php echo to_currency_no_money($tongcong); ?></td>
				</tr>

				<tr>
					<td class="print_header_table" colspan="2">Bằng chữ</td>
					<td class="print_header_table" colspan="6"><?php echo $name_money; ?></td>
				</tr>
				
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