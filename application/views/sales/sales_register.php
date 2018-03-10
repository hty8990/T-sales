<?php $this->load->view("partial/header"); 
?>

<?php
if (isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}

if (!empty($warning))
{
	echo "<div class='alert alert-dismissible alert-warning'>".$warning."</div>";
}

if (isset($success))
{
	echo "<div class='alert alert-dismissible alert-success'>".$success."</div>";
}
?>
<input id="selecttext" name="selecttext" value= '<?php echo $selectinput; ?>' type="hidden">
<input id="hinhthucban" name="hinhthucban" value= '<?php echo $hinhthucban; ?>' type="hidden">
<div id="register_wrapper">
<!-- Top register controls -->
	<?php
	
	$tabindex = 0; 
	$tienkhuyenmai = 0;
	$giatridonhang = 0;
	$cuocvanchuyen = 0;
	$totalkg = 0;
	$totalkg_sale = 0;
	$totalbaogui =0;
	$totalbaotra = 0;
	$tongbao=0;
	$tongbaotang=0;
	$arrTotal =array();
	?>
	<?php echo form_open($controller_name."/change_mode", array('id'=>'mode_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group" style="padding: 2px;">
			<ul>
			<li class="pull-left first_li">
					<label style="margin-left:10px;" class="control-label">Ngày bán hàng: </label>
				</li>
			<li class="pull-left">
			<input class="form-control input-sm ui-autocomplete-input" name="date_sale" value=<?php
			echo $date_sale; ?> size="15"  type="text" id="date_sale">
			</li>
			</ul>
			<ul>
				<li class="pull-left first_li">
					<label style="margin-left:30px;" class="control-label">Hình thức bán</label>
				</li>
				<li class="pull-left">
					&nbsp;&nbsp;<?php echo form_dropdown('mode', $modes, $mode, array('onchange'=>"$('#mode_form').submit();", 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
				</li>
			</ul>
		</div>
	<?php echo form_close(); ?>
	
		<!-- 
***************************************************************************************************
************************************* Cac nut *************************************
***************************************************************************************************
	-->
	<?php echo form_open($controller_name."/add", array('id'=>'add_item_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group" >
			<ul>
				<li class="pull-left first_li">
					<label for="item" class='control-label'><?php echo $this->lang->line('sales_find_or_scan_item_or_receipt'); ?></label>
				</li>
				<li class="pull-left">
					<?php echo form_input(array('name'=>'item', 'id'=>'item', 'class'=>'form-control input-sm', 'size'=>'40', 'tabindex'=>++$tabindex)); ?>
					<span class="ui-helper-hidden-accessible" role="status"></span>
				</li>
				<li class="pull-right">
					<?php echo anchor("sales/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('sales_takings'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_takings_button')); ?>
				</li>
			</ul>
		</div>
	<?php echo form_close(); ?>

	<!-- 
***************************************************************************************************
************************************* San pham *************************************
***************************************************************************************************
	-->
	<table class="sales_table_100" id="register">
		<colgroup>
			<col width="5%"><col width="8%"><col width="30%"><col width="5%"><col width="4%">
			<col width="4%"><col width="4%"><col width="4%"><col width="8%"><col width="19%">
		</colgroup>
		<thead>
			<tr>
				<th rowspan="2">#</th>
				<th rowspan="2"><?php echo $this->lang->line('sales_item_number'); ?></th>
				<th rowspan="2"><?php echo $this->lang->line('sales_item_name'); ?></th>
				<th rowspan="2" >Loại</th>
				<th class='bd-bottom' colspan="2">Số lượng bán</th>
				<th class='bd-bottom' colspan="2">Số lượng tặng</th>
				<th rowspan="2">Giá bán</th>
				<th rowspan="2">Thành tiền</th>
				
			</tr>
			<tr>
				<th style="width: 8%;">Bao, túi</th>
				<th style="width: 8%;">Kg</th>
				<th style="width: 8%;">Bao, túi</th>
				<th style="width: 8%;">Kg</th>
			</tr>
		</thead>

		<tbody id="cart_contents">
			<?php
			if(count($cart) == 0)
			{
			?>
				<tr>
					<td colspan='10'>
						<div class='alert alert-dismissible alert-info'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div>
					</td>
				</tr>
			<?php
			}
			else
			{	
				$i = 0;		
				foreach(array_reverse($cart, true) as $line=>$item)
				{
				// kiem tra la co hang tra lai hay khong
				if($item['in_stock_return'] > 0){
					$strcheck = '';
					if($item['checkreturn'] == 'true'){
						$strcheck = 'checked="checked"';
					}else{
						$strcheck = '';
					}
					$StrrInStock =  '<input '.$strcheck.' title="Nếu chọn khối lượng bán sẽ được trừ vào hàng trả lại" data-index="'.$item['item_id'].'" class="SelectItemreturn" name="btSelectItem" type="checkbox">  [' . to_quantity_decimals($item['in_stock']) . ' bao trong đó có '.to_quantity_decimals($item['in_stock_return']).' bao trả lại ] ';
				}else{
					$StrrInStock =  '[' . to_quantity_decimals($item['in_stock']) . ' bao trong ' . $item['stock_name'] . ']';
				}
				if($item['hanggui'] > 0 || $item['hangtra'] > 0){
					$totalbaogui = $totalbaogui + ($item['hanggui'] * $item['unit_weigh']);
					$totalbaotra = $totalbaotra + ($item['hangtra'] * $item['unit_weigh']);
					$stylehanggui = '';
					$spanadd = '<span class="glyphicon glyphicon-minus"></span>';
				}else{
					$stylehanggui = 'style="display:none;"';
					$spanadd = '<span class="glyphicon glyphicon-plus"></span>';
				} 
			//echo "<br>"	;echo "<pre>";print_r($item);echo "<pre>";
			?>
					<?php echo form_open($controller_name."/edit_item/$line", array('class'=>'form-horizontal', 'id'=>'cart_'.$line)); ?>
						<tr>
							<td><?php echo anchor($controller_name."/delete_item/$line", '<span class="glyphicon glyphicon-trash"></span>');?> | <a class="hang_gui_tra" id-item = <?php echo $item['item_id']; ?> style="cursor: pointer; "><?php echo $spanadd ?></a></td>
							<td><?php echo $item['item_number']; ?></td>
							<td style="text-align: left;">
								<?php echo $item['name']; ?><br /> <?php echo $StrrInStock?>
								<?php echo form_hidden('location', $item['item_location']); ?>
							</td>
							<td><?php echo $item['unit_weigh']; ?></td>
							
							<?php $id= 'idcount'.$item['item_id'] ?>
							<td><?php echo form_input(array('id' => ''.$id.'1'.'','onchange'=>'count_weigh(\''.$item['unit_weigh'].'\',\''.$id.'\')','name'=>'quantity', 'class'=>'form-control input-sm', 'value'=>$item['quantity'], 'tabindex'=>++$tabindex));?></td>
							<?php $quantitykg = $item['unit_weigh'] * $item['quantity']; ?>	
							<td><?php echo form_input(array('id' => ''.$id.'2'.'','name'=>'quantitykg', 'class'=>'form-control input-sm', 'value'=>$quantitykg, 'tabindex'=>++$tabindex, 'disabled'=>''));?></td>
							<td><?php echo form_input(array('id' => ''.$id.'3'.'','onchange'=>'count_weigh_km(\''.$item['unit_weigh'].'\',\''.$id.'\')','name'=>'quantitytang', 'class'=>'form-control input-sm', 'value'=>$item['quantitytang'], 'tabindex'=>++$tabindex));?></td>
							<?php $quantitytangkg = $item['unit_weigh'] * $item['quantitytang']; ?>
							<td><?php echo form_input(array('id' => ''.$id.'4'.'','name'=>'quantitytangkg', 'class'=>'form-control input-sm', 'value'=>$quantitytangkg, 'tabindex'=>++$tabindex, 'disabled'=>''));?></td>
							<td>
								<?php
								// tinh tong kg cho mot loai
								$price = $item['price'];
								?>
								<?php echo to_currency($price); ?>
								<?php echo form_hidden('price', $price); ?>
							</td>
							<?php
							// tinh tong tien
							$total = $price*$quantitykg;
							$tongbao = $tongbao +$item['quantity']+$item['quantitytang'];
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
							?>
							<td><?php echo to_currency($total) ?></td>
						</tr>
						<tr <?php echo $stylehanggui ?> class="show_hang_tra_lai" id= hang_tra_lai_<?php echo $item['item_id'] ?> >
							<td colspan="3"></td>
							<td>Gửi</td>
							<td><input type="text" value=<?php echo $item['hanggui']; ?> name="hanggui" class="form-control input-sm"></td>
							<td><input type="text"  value=<?php echo $item['hanggui'] * $item['unit_weigh']; ?>  class="form-control input-sm" disabled=""></td>
							<td><input type="text" name="hangtra" value=<?php echo $item['hangtra']; ?> class="form-control input-sm"></td>
							<td><input type="text"  value=<?php echo $item['hangtra'] * $item['unit_weigh']; ?>  class="form-control input-sm" disabled=""></td>
							<td>Trả</td>
							<td colspan="2"></td>
						</tr>
					<?php echo form_close(); ?>
			<?php
					$totalkg = $totalkg + $quantitykg + $quantitytangkg;
					$totalkg_sale = $totalkg_sale + $quantitykg;
					$i++;
				}
				if($totalkg >0){
					?>
					<tr class="border-line-top"><td colspan="3">Tổng</td>
					<!-- Tinh so luong -->
					<td></td>
					<td colspan="2"><?php echo $tongbao ?> bao</td>
					<td colspan="2"><?php echo $totalkg_sale ?> kg</td><td></td><td><?php echo to_currency($giatridonhang); ?></td></tr>
					<?php }
			}
			?>
		</tbody>
	</table>
<!-- *************************** Cuoc van chuyen **************************************-->
<?php 
	if(isset($customer) && $cart){
		$vanchuyen = 0;
		if(isset($vanchuyen_dg)){
			$vanchuyen = $vanchuyen_dg;
			$kgvanchuyen = $totalkg - $totalbaogui + $totalbaotra;
			$cuocvanchuyen = $kgvanchuyen*$vanchuyen;
			$giatridonhang = $giatridonhang + $cuocvanchuyen;
		}
?>

<div style="margin-top: 5px; margin-bottom: 2px;"></div>
<table class="sales_table_100 border-line-top" id="khuyenmai">
	<colgroup>
		<col width="40%"><col width="15%"><col width="10%"><col width="12%"><col width="50%">
	</colgroup>
	<tbody>
		<tr>
			<td style="font-weight: bold; font-size: 15px; text-align: center;">Cước vận chuyển:</td>
			<td><?php echo "Số lượng: ".$kgvanchuyen." Kg" ?></td>
			<td style="text-align: right;">Đơn giá: </td>
			<?php echo form_open($controller_name."/update_tranfer", array('class'=>'form-horizontal', 'id'=>'cart_'.$line)); ?>
			<td><?php echo form_input(array('id' => 'vanchuyen_dg','style'=>'width:90%; margin-left: 9px;','name'=>'vanchuyen_dg','size'=>'2', 'class'=>'form-control input-sm', 'value'=>to_currency_no_money((int)$vanchuyen), 'tabindex'=>++$tabindex));?></td>
			<?php echo form_close(); ?>
			<td ><?php echo "Thành tiền: ".to_currency($cuocvanchuyen); ?></td>
		</tr>
	</tbody>
</table>

<?php
	}
?>
<!-- *************************** Thuong san luong**************************************-->
<?php 
	$giatridonhangtrietkhau = $giatridonhang;
	if(isset($customer) && $cart){
		if(isset($thuong_san_luong['sanluong_tieude']) && $thuong_san_luong['sanluong_tieude'] !== ''){
			$tieudosanluong = $thuong_san_luong['sanluong_tieude'];
		}else{
			$tieudosanluong = 'Thuởng sản lượng tháng ';
		}
		if(isset($thuong_san_luong['sanluong_soluong']) && $thuong_san_luong['sanluong_soluong'] !== ''){
			$sanluong_soluong = $thuong_san_luong['sanluong_soluong'];
		}else{
			$sanluong_soluong = '';
		}
		if(isset($thuong_san_luong['sanluong_dongia']) && $thuong_san_luong['sanluong_dongia'] !== ''){
			$sanluong_dongia = to_currency_no_money($thuong_san_luong['sanluong_dongia']);
		}else{
			$sanluong_dongia = '';
		}
		if($sanluong_soluong > 0 && $sanluong_soluong > 0){
			$checked = 'checked';
			$sanluong_thanhtien = $sanluong_soluong * $thuong_san_luong['sanluong_dongia'];
			$giatridonhang = $giatridonhang - $sanluong_thanhtien;

		}else{
			$checked = '';
			$sanluong_thanhtien = '';
		}
?>
<div class="col-xs-12">
<input <?php echo $checked; ?> type="checkbox" name="check_thuongsanluong" id="check_thuongsanluong">
<label for="check_thuongsanluong">Thuởng sản lượng</label>
</div>
<?php echo form_open($controller_name."/set_uncheck_sanluong", array('id'=>'set_uncheck_sanluong')); ?>
<?php echo form_close(); ?>
<table class="sales_table_100" id="thuongsanluong" style="display:none;margin-top: 2px; margin-bottom: 5px; padding: 1px; text-align: center;
vertical-align: middle;">
	<colgroup>
		<col width="40%"><col width="8%"><col width="2%"><col width="5%"><col width="15%"><col width="2%"><col width="20%">
	</colgroup>
	<tbody>
		<tr>
			<?php echo form_open($controller_name."/update_thuongsanluong", array('class'=>'form-horizontal', 'id'=>'form_sanluong')); ?>
			<td><?php echo form_input(array('id' => 'sanluong_tieude','style'=>'width:90%;','name'=>'sanluong_tieude','size'=>'2', 'class'=>'form-control input-sm','placeholder'=>'Tiêu đề sản lượng', 'value'=>$tieudosanluong, 'tabindex'=>++$tabindex));?></td>
			<td><?php echo form_input(array('id' => 'sanluong_soluong','style'=>'','placeholder'=>'Số lượng','name'=>'sanluong_soluong','size'=>'2', 'class'=>'form-control input-sm', 'value'=>$sanluong_soluong, 'tabindex'=>++$tabindex));?></td>
			<td>kg</td><td></td>
			<td><?php echo form_input(array('id' => 'sanluong_dongia','style'=>' margin-right: 9px;','placeholder'=>'Đơn giá','name'=>'sanluong_dongia','size'=>'2', 'class'=>'form-control input-sm', 'value'=>$sanluong_dongia, 'tabindex'=>++$tabindex));?></td><td>Đ</td>
			<?php echo form_close(); ?>
			<td ><?php echo "Thành tiền: ".to_currency($sanluong_thanhtien); ?></td>
		</tr>
	</tbody>
</table>

<?php
	}
?>
	<!-- 
***************************************************************************************************
************************************* Chuong trinh khuyen mai *************************************
***************************************************************************************************
	-->
	<?php
	$arrPromotions = get_promotion_helper($customer_stringpromotion,$arrTotal,$cart,$totalkg_sale,$giatridonhangtrietkhau);
	//echo "<pre>"; print_r($arrPromotions); echo "</pre>"; exit;
	 if(sizeof($arrPromotions) > 0 && $cart) {
	 	$typecheck = '';
	 	?>
		<table class="table table-hover table-striped" id="promotion">
			<thead>
				<tr bgcolor="#BBBBBB">
					<th>Loại sản phẩm</th>
					<th>Hình thức</th>
					<th>Đơn vị tính</th>
					<th>Đơn giá</th>
					<th>Thành tiền</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach($arrPromotions as $arrPromotion){
				if(isset($arrPromotion['soluong']) && $arrPromotion['soluong'] >0){
					$congthuctinh = $arrPromotion['congthuctinh'];
					$tienkhuyenmai = $tienkhuyenmai + $arrPromotion['money'];
					if($typecheck !== $arrPromotion['type']){
						$typecheck = $arrPromotion['type'];
					?>
						<tr><td><?php echo $arrPromotion['list_name']; ?></td>
					<?php
					}else{
						echo "<tr><td></td>";
					}

					?>
					<?php if($arrPromotion['not_check_all'] == 0){ ?>
						<?php if($arrPromotion['promotion_type'] == 'triet_khau'){ ?>
							<td><?php echo $arrPromotion['name']?></td>
						<?php }else{ ?>
							<td style="color:#3498DB"><?php echo $arrPromotion['name'] ?></td>
						<?php } ?>
					<?php }else{ ?>
					<td style="color:red"><?php echo $arrPromotion['name'] ?></td>
					<?php } ?>
					<!-- Tinh so luong -->
					<td title="<?php echo $congthuctinh ?>"><?php echo $arrPromotion['soluong'] ?></td>
					<td><?php echo $arrPromotion['dongia'] ?></td>
					<td ><?php echo to_currency($arrPromotion['money']) ?></td></tr>
					<?php 
				} 
			} ?>
		</tbody></table>
		<?php
		}
	?>
</div>
<!-- Overall Sale -->
<!-- **************** Thong tin khach hang *************************************-->
<div id="overall_sale" class="panel panel-default">
	<div class="panel-body">
		<?php
		if(isset($customer))
		{
		?>
			<table class="sales_table_100">
				<tr>
					<th style='width: 15%;'><?php echo $this->lang->line("sales_customer"); ?></th>
					<th style="width: 85%; text-align: right;"><?php echo $customer; ?></th>
				</tr>
				<?php
				if(!empty($customer_email))
				{
				?>
					<tr>
						<th style='width: 15%;'><?php echo $this->lang->line("sales_customer_email"); ?></th>
						<th style="width: 85%; text-align: right;"><?php echo $customer_email; ?></th>
					</tr>
				<?php
				}
				?>
				<?php
				if(!empty($customer_address))
				{
				?>
					<tr>
						<th style='width: 15%;'><?php echo $this->lang->line("sales_customer_address"); ?></th>
						<th style="width: 85%; text-align: right;"><?php echo $customer_address; ?></th>
					</tr>
				<?php
				}
				?>
			</table>
			<button class='btn btn-info btn-sm modal-dlg' data-btn-submit_custumer='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url("promotion/suspended/$customer_id"); ?>'
					title='<?php echo $this->lang->line('sales_suspended_sales'); ?>'>
				<span class="glyphicon glyphicon-sound-5-1">&nbsp</span><?php echo $this->lang->line('sales_suspended_sales'); ?>
			</button>
			<?php echo anchor($controller_name."/remove_customer", '<span class="glyphicon glyphicon-remove">&nbsp</span>' . $this->lang->line('common_remove').' '.$this->lang->line('customers_customer'),
								array('class'=>'btn btn-danger btn-sm', 'id'=>'remove_customer_button', 'title'=>$this->lang->line('common_remove').' '.$this->lang->line('customers_customer'))); ?>
					
		<?php
		}
		else
		{
		?>
			<?php echo form_open($controller_name."/select_customer", array('id'=>'select_customer_form', 'class'=>'form-horizontal')); ?>
				<div class="form-group" id="select_customer">
					<label id="customer_label" for="customer" class="control-label" style="margin-bottom: 1em; margin-top: -1em;"><?php echo $this->lang->line('sales_select_customer'); ?></label>
					<?php echo form_input(array('name'=>'customer', 'id'=>'customer', 'class'=>'form-control input-sm', 'value'=>$this->lang->line('sales_start_typing_customer_name')));?>
	
					<button class='btn btn-info btn-sm modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url("customers/view"); ?>'
							title='<?php echo $this->lang->line($controller_name. '_new_customer'); ?>'>
						<span class="glyphicon glyphicon-user">&nbsp</span><?php echo $this->lang->line($controller_name. '_new_customer'); ?>
					</button>

				</div>
			<?php echo form_close(); ?>
		<?php
		}
		?>
<!-- 
***************************************************************************************************
************************************* Tong tien gia tri hang hoa *********************************
***************************************************************************************************
-->
		<?php
		// Only show this part if there are Items already in the sale.
		if(count($cart) > 0)
		{
		$tongtien = ($giatridonhang - $tienkhuyenmai) + $customer_debt;
		?>
			<table class="sales_table_100" id="sale_totals">
				

				<tr>
					<th style='width: 55%;'>Tổng giá trị đơn hàng</th>
					<th style="width: 45%; text-align: right;"><?php echo to_currency($giatridonhang - $tienkhuyenmai); ?></th>
				</tr>
				<tr>
					<th style="width: 55%;">Nợ cũ</th>
					<th style="width: 45%; text-align: right;"><?php echo to_currency($customer_debt); ?></th>
				</tr>
			</table>
			<table class="sales_table_100" id="payment_totals">
				<tr>
					<th style="width: 55%;">Tổng công</th>
					<th style="width: 45%; text-align: right;"><?php echo to_currency($tongtien); ?></th>
				</tr>
			</table>

			<div id="payment_details">
					<?php
					// Show Complete sale button instead of Add Payment if there is no amount due left
					if(!$payments_cover_total)
					{
					?>
						<?php echo form_open($controller_name."/add_payment", array('id'=>'add_payment_form', 'class'=>'form-horizontal')); ?>
							<table class="sales_table_100">
								<tr>
									<td><?php echo $this->lang->line('sales_payment');?></td>
									<td>
										<?php echo form_dropdown('payment_type', $payment_options, array(), array('id'=>'payment_types', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'auto')); ?>
									</td>
								</tr>
								<tr>
									<td><span id="amount_tendered_label"><?php echo $this->lang->line('sales_amount_tendered'); ?></span></td>
									<td>
										<?php echo form_input(array('name'=>'amount_tendered', 'id'=>'amount_tendered', 'class'=>'form-control input-sm', 'value'=>$amount_tendered, 'size'=>'5', 'tabindex'=>++$tabindex)); ?>
									</td>
								</tr>
							</table>
						<?php echo form_close(); ?>
					<?php
					}
					?>
				<?php
				// Only show this part if there is at least one payment entered.
				if(count($payments) > 0)
				{
				?>
					<table class="sales_table_100" id="register">
						<thead>
							<tr>
								<th style="width: 10%;"><?php echo $this->lang->line('common_delete'); ?></th>
								<th style="width: 60%;"><?php echo $this->lang->line('sales_payment_type'); ?></th>
								<th style="width: 20%;"><?php echo $this->lang->line('sales_payment_amount'); ?></th>
							</tr>
						</thead>
			
						<tbody id="payment_contents">
							<?php
							if($payments)
							{
								$payment_id = $payments['payment_type'];
							?>
								<tr>
									<td><?php echo anchor($controller_name."/delete_payment/$payment_id", '<span class="glyphicon glyphicon-trash"></span>'); ?></td>
									<td><?php echo $payments['payment_type']; ?></td>
									<td style="text-align: right;"><?php echo to_currency( $payments['payment_amount'] ); ?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				<?php
				}
				?>
			</div>
			<?php
				// Only show this part if the payment cover the total
				if($payments_cover_total)
				{
				?>
					<div class="container-fluid">
						<div class="no-gutter row">
							<div class="form-group form-group-sm">
								<div class="col-xs-12">
									<?php echo form_label($this->lang->line('common_comments'), 'comments', array('class'=>'control-label', 'id'=>'comment_label', 'for'=>'comment')); ?>
									<?php echo form_textarea(array('name'=>'comment', 'id'=>'comment', 'class'=>'form-control input-sm', 'value'=>$comment, 'rows'=>'2')); ?>
								</div>
							</div>
						</div>
						<div class="row">
					</div>
				<?php
				}
				?>
<!-- 
***************************************************************************************************
************************************* Cac nut cap nhat du lieu *********************************
***************************************************************************************************
-->
			<?php echo form_open($controller_name."/cancel", array('id'=>'buttons_form')); ?>
				<div class="form-group" id="buttons_sale">

					<div class='btn btn-sm btn-danger pull-left' id='cancel_sale_button'><span class="glyphicon glyphicon-remove">&nbsp</span><?php echo $this->lang->line('sales_cancel_sale'); ?></div>
					<?php
					// Show Complete sale button instead of Add Payment if there is no amount due left
					if($payments_cover_total)
					{
					?>
					<?php echo form_hidden('giatridonhang', ($giatridonhang - $tienkhuyenmai)); ?>
					<div class='btn btn-sm btn-success pull-right' id='finish_sale_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('sales_complete_sale'); ?></div>
					<?php
					}else{
						if(isset($customer)){
					?>
					<div class='btn btn-sm btn-success pull-right' id='add_payment_button' tabindex='<?php echo ++$tabindex; ?>'><span class="glyphicon glyphicon-credit-card">&nbsp</span><?php echo $this->lang->line('sales_add_payment'); ?></div>
					
					<?php }} ?>
				</div>
			<?php echo form_close(); ?>
		<?php
		}
		?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function()
{
	// hang gui tra
	
	$('.hang_gui_tra').click(function() 
	{
		// lay id
		var id = $(this).attr('id-item');
		// show hang tra lai
		if ( $("#hang_tra_lai_"+id).css('display') == 'none' ){
		    // element is hidden
		    $("#hang_tra_lai_"+id).show();
		   $(this).find('span').removeClass(' glyphicon-plus').addClass(' glyphicon-minus');
		}else{
			$("#hang_tra_lai_"+id).hide();
			$(this).find('span').removeClass('glyphicon-minus').addClass(' glyphicon-plus');
		}
	});	
	if($('#check_thuongsanluong').is(":checked")) {
		 $("#thuongsanluong").show();
	}
	// thuong san luong
	$('#check_thuongsanluong').change(function() {
        if($(this).is(":checked")) {
            $("#thuongsanluong").show();
        }else{
        	$("#thuongsanluong").hide();
        	$("#sanluong_soluong").val('');
        	$("#sanluong_dongia").val('');
        	$("#sanluong_tieude").val('Thuởng sản lượng tháng ');
        	$('#set_uncheck_sanluong').submit();
        }
          
    });
	var typetrathuong = $("#hinhthucban").val();
	$('#payment_types').val(typetrathuong);
	$('.SelectItemreturn').change(function() 
	{
		var id_item = $(this).attr('data-index');
		var checked_return = false;
		if($(this).is(':checked')){
			checked_return = true;
		}
		$.post('<?php echo site_url($controller_name."/set_checked_return");?>', {checked_return: checked_return, id_item: id_item});
	});
    $("#item").autocomplete(
	{
		source: '<?php echo site_url($controller_name."/item_search"); ?>',
    	minChars: 0,
    	autoFocus: false,
       	delay: 10,
		select: function (a, ui) {
			$(this).val(ui.item.value);
			$("#add_item_form").submit();
		}
    });
    var idselect = $('#selecttext').val();
	$('#'+idselect).select();

	$('#item').keypress(function (e) {
		if (e.which == 13) {
			$('#add_item_form').submit();
			 	return false;
		}
	});

    $('#item').blur(function()
    {
        $(this).val("<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

    var clear_fields = function()
    {
        if ($(this).val().match("<?php echo $this->lang->line('sales_start_typing_item_name') . '|' . $this->lang->line('sales_start_typing_customer_name'); ?>"))
        {
            $(this).val('');
        }
    };

    $("#customer").autocomplete(
    {
		source: '<?php echo site_url("customers/suggest"); ?>',
    	minChars: 0,
    	delay: 10,
		select: function (a, ui) {
			$(this).val(ui.item.value);
			$("#select_customer_form").submit();
		}
    });

	$('#item, #customer').click(clear_fields).dblclick(function(event)
	{
		$(this).autocomplete("search");
	});

	$('#customer').blur(function()
    {
    	$(this).val("<?php echo $this->lang->line('sales_start_typing_customer_name'); ?>");
    });

	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});

	$('#date_sale').change(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_date_sale");?>', {date_sale: $('#date_sale').val()});
	});
	
    $("#finish_sale_button").click(function()
    {
		$('#buttons_form').attr('action', '<?php echo site_url($controller_name."/complete"); ?>');
		$('#buttons_form').submit();
    });

	$("#suspend_sale_button").click(function()
	{ 	
		$('#buttons_form').attr('action', '<?php echo site_url($controller_name."/suspend"); ?>');
		$('#buttons_form').submit();
	});

    $("#cancel_sale_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("sales_confirm_cancel_sale"); ?>'))
    	{
			$('#buttons_form').attr('action', '<?php echo site_url($controller_name."/cancel"); ?>');
    		$('#buttons_form').submit();
    	}
    });

	$("#add_payment_button").click(function()
	{
		$('#add_payment_form').submit();
    });


	$("#cart_contents input").keypress(function(event)
	{
		if (event.which == 13)
		{
			$(this).parents("tr").prevAll("form:first").submit();
		}
	});

	$("#sanluong_soluong").keypress(function(event)
	{
		if( event.which == 13 )
		{
			if(check_sanluong()){
				$('#form_sanluong').submit();
			}else{
				alert('Tiêu đề, số lượng,đơn giá không được để trống');
			}
			
		}
	});

	$("#sanluong_tieude").keypress(function(event)
	{
		if( event.which == 13 )
		{
			if(check_sanluong()){
				$('#form_sanluong').submit();
			}else{
				alert('Tiêu đề, số lượng,đơn giá không được để trống');
			}
			
		}
	});

	$("#sanluong_dongia").keypress(function(event)
	{
		if( event.which == 13 )
		{
			if(check_sanluong()){
				$('#form_sanluong').submit();
			}else{
				alert('Tiêu đề, số lượng,đơn giá không được để trống');
			}
			
		}
	});

	function check_sanluong(){
		if($("#sanluong_tieude").val() == '' || $("#sanluong_dongia").val() == '' || $("#sanluong_dongia").val() == ''){
			return false;
		}else{
			return true;
		}
	}

	$("#amount_tendered").keypress(function(event)
	{
		if( event.which == 13 )
		{
			$('#add_payment_form').submit();
		}
	});
	
    $("#finish_sale_button").keypress(function(event)
	{
		if ( event.which == 13 )
		{
			$('#finish_sale_form').submit();
		}
	});

	dialog_support.init("a.modal-dlg, button.modal-dlg");

	table_support.handle_submit = function(resource, response, stay_open)
	{
		if(response.success) {
			if (resource.match(/customers$/))
			{
				$("#customer").val(response.id);
				$("#select_customer_form").submit();
			}
			else
			{
				$("#item_location").val(1);
				$("#item").val(response.id);
				if (stay_open)
				{
					$("#add_item_form").ajaxSubmit();
				}
				else
				{
					$("#add_item_form").submit();
				}
			}
		}
	}
});

function count_weigh(qckg,id){
	var id1 = id+'1';
	var id2 = id+'2';
	var id3 = id+'3';
	var x = $('#'+id1).val();
	var total = x * qckg;
	if(total > 0){
		$('#'+id2).val(total);
	}
}
function count_weigh_km(qckg,id){
	var id1 = id+'1';
	var id2 = id+'2';
	var id3 = id+'3';
	var id4 = id+'4';
	var x = $('#'+id3).val();
	var total = x * qckg;
	if(total > 0){
		$('#'+id4).val(total);
	}
}
</script>
<script>
$( function() {
  	_setDatepicker($('#date_sale'));
  	$("#amount_tendered").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });
    $("#vanchuyen_dg").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });
    $("#sanluong_dongia").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });
})
</script>
<?php $this->load->view("partial/footer"); ?>
