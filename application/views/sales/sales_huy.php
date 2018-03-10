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
<div id="register_wrapper">

<!-- Top register controls -->
	<?php $tabindex = 0; 
	$tienkhuyenmai = 0;
	$giatridonhang = 0;
	$cuocvanchuyen = 0;
	$totalkg = 0;
	?>
	<?php echo form_open($controller_name."/change_mode", array('id'=>'mode_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group" style="padding: 2px;">
			
			<ul>
			<li class="pull-left first_li">
					<label class="control-label">&nbsp;&nbsp;&nbsp;&nbsp;Ngày bán hàng: </label>
				</li>
			<li class="pull-left">
			<input class="form-control input-sm ui-autocomplete-input" name="date_sale" value=<?php
			echo $date_sale; ?> size="15"  type="text" id="date_sale">
			</li>
			</ul>
			<ul>
				<li class="pull-left first_li">
					<label class="control-label">&nbsp;&nbsp;&nbsp;&nbsp;Hình thức bán</label>
				</li>
				<li class="pull-left">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo form_dropdown('mode', $modes, $mode, array('onchange'=>"$('#mode_form').submit();", 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
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
			<col width="3%"><col width="8%"><col width="29%"><col width="5%"><col width="5%">
			<col width="5%">
		</colgroup>
		<thead>
			<tr>
				<th  rowspan="2" style="border-right: 1px solid white;"><?php echo $this->lang->line('common_delete'); ?></th>
				<th rowspan="2" style="border-right: 1px solid white;"><?php echo $this->lang->line('sales_item_number'); ?></th>
				<th rowspan="2" style="border-right: 1px solid white;"><?php echo $this->lang->line('sales_item_name'); ?></th>
				<th rowspan="2" style="border-right: 1px solid white;">Loại</th>
				<th colspan="2" style="border-right: 0.5px solid white;border-bottom : 1px solid white">Số lượng hủy, rách, hỏng</th>
				<th rowspan="2" style="border-right: 1px solid white;">Giá gốc</th>
				<th rowspan="2" style="">Thành tiền</th>
			</tr>
			<tr>
				<th style="width: 8%; border-right: 1px solid white;">Bao, túi</th>
				<th style="width: 8%; border-right: 1px solid white;">Kg</th>
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
			//echo "<br>"	;echo "<pre>";print_r($cart);echo "<pre>";
			?>
					<?php echo form_open($controller_name."/edit_item/$line", array('class'=>'form-horizontal', 'id'=>'cart_'.$line)); ?>
						<tr>
							<td><?php echo anchor($controller_name."/delete_item/$line", '<span class="glyphicon glyphicon-trash"></span>');?></td>
							<td><?php echo $item['item_number']; ?></td>
							<td style="text-align: left;">
								<?php echo $item['name']; ?><br /> <?php echo '[' . to_quantity_decimals($item['in_stock']) . ' kg trong ' . $item['stock_name'] . ']'; ?>
								<?php echo form_hidden('location', $item['item_location']); ?>
							</td>
							<td><?php echo $item['unit_weigh']; ?></td>
							
							<?php $id= 'idcount'.$item['item_id'] ?>
							<td><?php echo form_input(array('id' => ''.$id.'1'.'','onchange'=>'count_weigh(\''.$item['unit_weigh'].'\',\''.$id.'\')','name'=>'quantity', 'class'=>'form-control input-sm', 'value'=>$item['quantity'], 'tabindex'=>++$tabindex));?></td>	
							<?php $quantitykg = $item['unit_weigh'] * $item['quantity']; ?>	
							<td><?php echo form_input(array('id' => ''.$id.'2'.'','name'=>'quantitykg', 'class'=>'form-control input-sm', 'value'=>$quantitykg, 'tabindex'=>++$tabindex, 'disabled'=>''));?></td>
							<td>
								<?php
								// tinh tong kg cho mot loai
								$price = $item['gia_goc'];
								// tinh gia tien
								?>
								<?php echo to_currency($price); ?>
								<?php echo form_hidden('price', $price); ?>
							</td>
							<?php
							// tinh tong tien
							$total = $price*$quantitykg;
							$giatridonhang  = $giatridonhang  + $total;
							?>
							<td><?php echo to_currency($total) ?></td>
						</tr>
					<?php echo form_close(); ?>
			<?php
					$totalkg = $totalkg + $quantitykg;
					$i++;
				}
			}
			?>
		</tbody>
	</table>
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
					<th style='width: 15%;'><?php echo $this->lang->line("sales_customer") . " :"; ?></th>
					<th style="width: 85%; text-align: right;"><?php echo $customer; ?></th>
				</tr>
				<?php
				if(!empty($customer_email))
				{
				?>
					<tr>
						<th style='width: 15%;'><?php echo $this->lang->line("sales_customer_email")  . " :"; ?></th>
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
						<th style='width: 15%;'><?php echo $this->lang->line("sales_customer_address")  . " :"; ?></th>
						<th style="width: 85%; text-align: right;"><?php echo $customer_address; ?></th>
					</tr>
				<?php
				}
				?>
			</table>
			<button class='btn btn-info btn-sm modal-dlg' data-btn-submit_custumer='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/suspended/$person_id"); ?>'
					title='<?php echo $this->lang->line('sales_suspended_sales'); ?>'>
				<span class="glyphicon glyphicon-align-justify">&nbsp</span><?php echo $this->lang->line('sales_suspended_sales'); ?>
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
		?>
			<table class="sales_table_100" id="sale_totals">
				
				<tr>
					<th style='width: 55%;'>Tổng giá trị đơn hàng</th>
					<th style="width: 45%; text-align: right;"><?php echo to_currency($giatridonhang); ?></th>
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
										<?php echo form_input(array('name'=>'amount_tendered', 'id'=>'amount_tendered', 'class'=>'form-control input-sm', 'value'=>to_currency_no_money($giatridonhang), 'size'=>'5', 'tabindex'=>++$tabindex)); ?>
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
					<?php echo form_hidden('giatridonhang', $giatridonhang); ?>
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
     var clear_fields = function()
    {
        if ($(this).val().match("<?php echo $this->lang->line('sales_start_typing_item_name') . '|' . $this->lang->line('sales_start_typing_customer_name'); ?>"))
        {
            $(this).val('');
        }
    };
	$('#customer').blur(function()
    {
    	$(this).val("<?php echo $this->lang->line('sales_start_typing_customer_name'); ?>");
    });
	$('#item, #customer').click(clear_fields).dblclick(function(event)
	{
		$(this).autocomplete("search");
	});
	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});
	$("#sales_print_after_sale").change(function()
	{
		$.post('<?php echo site_url($controller_name."/set_print_after_sale");?>', {sales_print_after_sale: $(this).is(":checked")});
	});
	
	$('#email_receipt').change(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
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
    $('#date_sale').change(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_date_sale");?>', {date_sale: $('#date_sale').val()});
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
  	var $date_sale = $('#date_sale');
$date_sale.datepicker({
  dateFormat: "dd/mm/yy"
});
})
  </script>
<?php $this->load->view("partial/footer"); ?>
