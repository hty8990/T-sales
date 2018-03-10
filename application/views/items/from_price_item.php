<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>
<?php 
if(isset($price_info->start_date) && $price_info->start_date !== ''){
$start_date = date("d/m/Y", strtotime($price_info->start_date));
}else{
$start_date = '';
}
if(isset($price_info->sale_price) && $price_info->sale_price !== ''){
$sale_price = (int)$price_info->sale_price;
}else{
$sale_price = '';
}

if(isset($price_info->input_prices) && $price_info->input_prices !== ''){
$input_prices = to_currency_no_money((int)$price_info->input_prices);
}else{
$input_prices = '';
}

?>
<?php echo form_open('items_price/save/'.$price_info->id, array('id'=>'item_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="inv_item_basic_info">
		<div class="form-group form-group-sm">
		<?php echo form_label('Sản phẩm', 'item_number', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'item_number',
						'id'=>'item_number',
						'class'=>'form-control input-sm',
						'value'=>$item_info->item_number .'-' .$item_info->name,
						'disabled'=>'')
						); ?>
			</div>
		</div>	
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3">Ngày bắt đầu: </label>
			<div class="col-xs-8">
			<?php echo form_input(array(
				'name'=>'start_date',
				'id'=>'start_date',
				'value'=>$start_date,
				'class'=>'form-control input-sm')
				); ?>
			</div>
		</div>		

		<div class="form-group form-group-sm">
			<?php echo form_label('Giá gốc', 'input_prices', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'input_prices',
						'id'=>'input_prices',
						'value'=>$input_prices,
						'class'=>'form-control input-sm')
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Giá bán', 'sale_price', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'sale_price',
						'id'=>'sale_price',
						'value'=>to_currency_no_money($sale_price),
						'class'=>'form-control input-sm')
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Ghi chú', 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'value'=>$price_info->description,
						'class'=>'form-control input-sm')
						); ?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
//validation and submit handling 
$(document).ready(function()
{	
	$("#input_prices").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });
	
	$("#sale_price").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });

	$("#customer").autocomplete(
    {
		source: '<?php echo site_url("customers/suggest"); ?>',
    	minChars:0,
		autoFocus: false,
		delay:10,
		appendTo: ".modal-content",
		select: function(e, ui) {
			$('#name_customer').val(ui.item.label);
		}
    });
	$('#item_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				dialog_support.hide();
				table_support.handle_submit('<?php echo site_url('items'); ?>', response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
			start_date:"required",
			input_prices:"required",
			sale_price:"required"
		},
		messages: 
		{
			
			date_sale:
			{
				required:"<?php echo "Ngày tháng không được để trống"; ?>"
			},
			input_prices:
			{
				required:"<?php echo "Giá bán không được để trống"; ?>",
				number:"<?php echo $this->lang->line('items_unit_price_number'); ?>"
			},
			sale_price:
			{
				required:"<?php echo "Giá gốc không được để trống"; ?>",
				number:"<?php echo $this->lang->line('items_unit_price_number'); ?>"
			}
		}
	});
});
function delete_item_kit_row(link)
{
	alert(link);
}
</script>
  <script>
$( function() {
    _setDatepicker($('#start_date'));
})
</script>
  <style type="text/css">
  	.ui-icon{
  		text-indent: 0px !important;
  	}
  </style>