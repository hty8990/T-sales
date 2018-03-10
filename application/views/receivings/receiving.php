<?php $this->load->view("partial/header"); ?>
<?php
$tongtien = 0;
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
	<!-- Top register controls -->

	<?php echo form_open($controller_name."/change_mode", array('id'=>'mode_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group" style="padding: 2px;">
			<ul>
			<li class="pull-left first_li">
					<label class="control-label">&nbsp;&nbsp;&nbsp;&nbsp;Ngày nhập hàng: </label>
				</li>
			<li class="pull-left">
			<input class="form-control input-sm ui-autocomplete-input" name="date_receiving" value=<?php
			echo $date_receiving; ?> size="15"  type="text" id="date_receiving">
			</li>
			</ul>
			<ul>
				<li class="pull-left first_li">
					<label class="control-label">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->lang->line('receivings_mode'); ?></label>
				</li>
				<li class="pull-left">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo form_dropdown('mode', $modes, $mode, array('onchange'=>"$('#mode_form').submit();", 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
				</li>
			</ul>
		</div>
	<?php echo form_close(); ?>

	<?php echo form_open($controller_name."/add", array('id'=>'add_item_form', 'class'=>'form-horizontal panel panel-default')); ?>
		<div class="panel-body form-group">
			<ul>
				<li class="pull-left first_li">
					<label for="item", class='control-label'>
						<?php echo $this->lang->line('receivings_find_or_scan_item'); ?>	
					</label>
				</li>
				<li class="pull-left">
					<?php echo form_input(array('name'=>'item', 'id'=>'item', 'class'=>'form-control input-sm', 'size'=>'50', 'tabindex'=>'1')); ?>
				</li>
				<li class="pull-right">
				<?php echo anchor("receivings/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('recive_takings'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_takings_button')); ?>
				</li>
			</ul>
		</div>
	<?php echo form_close(); ?>	
<!-- Receiving Items List -->
<table class="sales_table_100" id="register">
		<colgroup>
			<col width="3%"><col width="8%"><col width="29%"><col width="5%"><col width="5%">
			<col width="5%"><col width="10%"><col width="15%">	
		</colgroup>
		<thead>
			<tr>
				<th  rowspan="2" style="border-right: 1px solid white;"><?php echo $this->lang->line('common_delete'); ?></th>
				<th rowspan="2" style="border-right: 1px solid white;"><?php echo $this->lang->line('sales_item_number'); ?></th>
				<th rowspan="2" style="border-right: 1px solid white;"><?php echo $this->lang->line('sales_item_name'); ?></th>
				<th rowspan="2" style="border-right: 1px solid white;">Loại</th>
				<th colspan="2" style="border-right: 0.5px solid white;border-bottom : 1px solid white">Số lượng nhập</th>
				<th rowspan="2" style="border-right: 1px solid white;">Giá bán</th>
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
				$tabindex = 0;
				$tongkg = 0;
				$tongbao =0;
				//echo "<pre>"; print_r($cart); echo "</pre>";
				foreach(array_reverse($cart, true) as $line=>$item)
				{
			?>
					<?php echo form_open($controller_name."/edit_item/$line", array('class'=>'form-horizontal', 'id'=>'cart_'.$line)); ?>
							<tr>
							<td><?php echo anchor($controller_name."/delete_item/$line", '<span class="glyphicon glyphicon-trash"></span>');?></td>
							<td><?php echo $item['item_number']; ?></td>
							<td style="text-align: left;">
								<?php echo $item['name']; ?><br /> <?php echo '[' . to_quantity_decimals($item['in_stock']) . ' bao trong ' . $item['stock_name'] . ']'; ?>
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
								$price = $item['price'];
								if($item['category'] == 'sang_bao'){
									echo form_input(array('id' => ''.$id.'2'.'','name'=>'price', 'class'=>'form-control input-sm', 'value'=>to_currency_no_money($price), 'tabindex'=>++$tabindex));
								}else{
									$tongkg = $tongkg + $quantitykg;
									$tongbao = $tongbao + $item['quantity'];
									echo to_currency($price);
									echo form_hidden('price', $price);
								}
								//$i++;
								?>
								<?php echo form_hidden('line', $i); ?>
							</td>
							<?php
							// tinh tong tien
							//$item['quantitykg'] = 1;
							$total = $price*$quantitykg;
							$tongtien = $tongtien  + $total;
							
							?>
							<td><?php echo to_currency($total) ?></td>
						</tr>
					<?php echo form_close(); ?>
			<?php
				$i++;
				}
				if($tongkg >0){
					?>
					<tr style="margin-top: 2px;border-top: 0.5px solid; 	border-top-color: #3498db; margin-bottom: 5px; padding: 1px; text-align: center;
vertical-align: middle;"><td colspan="3">Tổng</td>
					<!-- Tinh so luong -->
					<td></td>
					<td><?php echo $tongbao ?> bao</td>
					<td><?php echo $tongkg ?> kg</td><td></td><td><?php echo to_currency($tongtien); ?></td></tr>
					<?php }
			}
			?>
		</tbody>
	</table>
	<div style="margin-top: 5px; margin-bottom: 2px;"></div>
	<?php if(count($cart) > 0){ ?>
	<table class="sales_table_100 border-line-top" id="sangbao">
		<colgroup>
			<col width="65%"><col width="10%"><col width="25%">
		</colgroup>
		<tbody>
			<tr>
				<?php echo form_open($controller_name."/update_sangbao", array('class'=>'form-horizontal', 'id'=>'form_sangbao')); ?>
				<td><?php echo form_input(array('id' => 'sangbao_tieude','style'=>'width:90%;','name'=>'sangbao_tieude','size'=>'2', 'class'=>'form-control input-sm','placeholder'=>'Ghi chú sang bao', 'value'=>$sang_bao['sangbao_tieude'], 'tabindex'=>++$tabindex));?></td>
				<td ><?php echo "Thành tiền: "; ?></td>
				<td><?php echo form_input(array('id' => 'sangbao_thanhtien','style'=>' margin-right: 9px;','placeholder'=>'Đơn giá','name'=>'sangbao_thanhtien','size'=>'2', 'class'=>'form-control input-sm', 'value'=>$sang_bao['sangbao_thanhtien'], 'tabindex'=>++$tabindex));?></td>
				<?php echo form_close(); ?>
			</tr>
		</tbody>
	</table>
	<?php 
	$chiphisangbao =  str_replace(",","",$sang_bao['sangbao_thanhtien']);
    $chiphisangbao =  str_replace(".","",$chiphisangbao);
    if($chiphisangbao !== 0){
    	 $tongtien = $tongtien + $chiphisangbao;
    }
   
	} ?>
</div>

<!-- Overall Receiving -->

<div id="overall_sale" class="panel panel-default">
	<div class="panel-body">
		<?php
		if(isset($supplier))
		{
		?>
			<table class="sales_table_100">
				<tr>
					<th style='width: 55%;'><?php echo $this->lang->line("receivings_supplier"); ?></th>
					<th style="width: 45%; text-align: right;"><?php echo $supplier; ?></th>
				</tr>
				<?php
				if(!empty($supplier_address))
				{
				?>
					<tr>
						<th style='width: 55%;'><?php echo $this->lang->line("receivings_supplier_address"); ?></th>
						<th style="width: 45%; text-align: right;"><?php echo $supplier_address; ?></th>
					</tr>
				<?php
				}
				?>
			</table>
			<?php echo anchor($controller_name."/remove_supplier", '<span class="glyphicon glyphicon-remove">&nbsp</span>' . $this->lang->line('receivings_modify'),
								array('class'=>'btn btn-danger btn-sm', 'id'=>'remove_supplier_button', 'title'=>$this->lang->line('common_remove').' '.$this->lang->line('suppliers_supplier'))); ?>
		<?php
		}
		else
		{
		?>
			<?php echo form_open($controller_name."/select_supplier", array('id'=>'select_supplier_form', 'class'=>'form-horizontal')); ?>
				<div class="form-group" id="select_customer">
					<label id="supplier_label" for="supplier" class="control-label" style="margin-bottom: 1em; margin-top: -1em;"><?php echo $this->lang->line('receivings_select_supplier'); ?></label>
					<?php echo form_input(array('name'=>'supplier', 'id'=>'supplier', 'class'=>'form-control input-sm', 'value'=>$this->lang->line('receivings_start_typing_supplier_name'))); ?>

					<button id='new_supplier_button' class='btn btn-info btn-sm modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url("suppliers/view"); ?>'
							title='<?php echo $this->lang->line('receivings_new_supplier'); ?>'>
						<span class="glyphicon glyphicon-user">&nbsp</span><?php echo $this->lang->line('receivings_new_supplier'); ?>
					</button>

				</div>
			<?php echo form_close(); ?>
		<?php
		}
		?>
		<!-- tongg tien --> 
		<table class="sales_table_100" id="sale_totals">
			<tr>
				<th style="width: 55%;"><?php echo $this->lang->line('sales_total'); ?></th>
					<th style="width: 45%; text-align: right;"><?php echo to_currency($tongtien); ?></th>
			</tr>
		</table>

		<?php
		if(count($cart) > 0 && isset($supplier))
		{
		?>
			<div id="finish_sale">
					<?php echo form_open($controller_name."/complete", array('id'=>'finish_receiving_form', 'class'=>'form-horizontal')); ?>
						<input id="gia_tri_don_hang" name="gia_tri_don_hang" value= '<?php echo $tongtien; ?>' type="hidden">
						<div class="form-group form-group-sm">
							<label id="comment_label" for="comment"><?php echo $this->lang->line('common_comments'); ?></label>
							<?php echo form_textarea(array('name'=>'comment', 'id'=>'comment', 'class'=>'form-control input-sm', 'value'=>$comment, 'rows'=>'1'));?>

							<table class="sales_table_100" id="payment_details">
								<tr>
									<td><?php echo $this->lang->line('sales_amount_tendered'); ?></td>
									<td>
										<?php echo form_input(array('name'=>'amount_tendered','id'=>'amount_tendered', 'value'=>$amount_tendered, 'class'=>'form-control input-sm', 'size'=>'5')); ?>
									</td>
								</tr>
							</table>

							<div class='btn btn-sm btn-danger pull-left' id='cancel_receiving_button'><span class="glyphicon glyphicon-remove">&nbsp</span><?php echo $this->lang->line('receivings_cancel_receiving') ?></div>
							
							<div class='btn btn-sm btn-success pull-right' id='finish_receiving_button'><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('receivings_complete_receiving') ?></div>
						</div>
					<?php echo form_close(); ?>
			</div>
		<?php
		}
		?>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function()
{
	$('#sangbao_tieude').keypress(function (e) {
		if (e.which == 13) {
			$('#form_sangbao').submit();
			return false;
		}
	});

	$('#sangbao_thanhtien').keypress(function (e) {
		if (e.which == 13) {
			$('#form_sangbao').submit();
			return false;
		}
	});
	$("#sangbao_thanhtien").keyup(function(e){
		if($(this).val() > 0){
			$(this).val(formatmonney($(this).val()));
		}
    });
	$("#amount_tendered").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });
    $("#item").autocomplete(
    {
		source: '<?php echo site_url($controller_name."/item_search"); ?>',
    	minChars:0,
       	delay:10,
       	autoFocus: false,
		select:	function (a, ui) {
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

	$('#date_receiving').change(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_date_receiving");?>', {date_receiving: $('#date_receiving').val()});
	});

	$('#item').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});


	$('#item,#supplier').click(function()
    {
    	$(this).attr('value','');
    });

    $("#supplier").autocomplete(
    {
		source: '<?php echo site_url("suppliers/suggest"); ?>',
    	minChars:0,
    	delay:10,
		select: function (a, ui) {
			$(this).val(ui.item.value);
			$("#select_supplier_form").submit();
		}
    });

	dialog_support.init("a.modal-dlg, button.modal-dlg");

	$('#supplier').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('receivings_start_typing_supplier_name'); ?>");
    });

    $("#finish_receiving_button").click(function()
    {
   		$('#finish_receiving_form').submit();
    });

    $("#cancel_receiving_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("receivings_confirm_cancel_receiving"); ?>'))
    	{
			$('#finish_receiving_form').attr('action', '<?php echo site_url($controller_name."/cancel_receiving"); ?>');
    		$('#finish_receiving_form').submit();
    	}
    });

	$("#cart_contents input").keypress(function(event)
	{
		if (event.which == 13)
		{
			$(this).parents("tr").prevAll("form:first").submit();
		}
	});

	table_support.handle_submit = function(resource, response, stay_open)
	{
		if(response.success)
		{
			if (resource.match(/suppliers$/))
			{
				$("#supplier").attr("value",response.id);
				$("#select_supplier_form").submit();
			}
			else
			{
				$("#item").attr("value",response.id);
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
</script>
  <script>
  $( function() {
  	_setDatepicker($('#date_receiving'));
  	})
  </script>
  <style type="text/css">
    .ui-icon{
        text-indent: 0px !important;
        cursor: pointer;
    }
  </style>
<?php $this->load->view("partial/footer"); ?>
