<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>
<?php 
if(isset($packet_price_infor->start_date) && $packet_price_infor->start_date !== ''){
$start_date = date("d/m/Y", strtotime($packet_price_infor->start_date));
}else{
$start_date = '';
}
$inputprice = (int)$packet_price_infor->input_prices;
?>
<?php echo form_open('Item_kits_price/save/'.$packet_price_infor->id, array('id'=>'item_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="inv_item_basic_info">
		<div class="form-group form-group-sm">
		<?php echo form_label('Tên gói', 'item_number', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'item_number',
						'id'=>'item_number',
						'class'=>'form-control input-sm',
						'value'=>$packet_infor->name,
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
			<?php echo form_label('Giá nhập', 'input_prices', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'input_prices',
						'id'=>'input_prices',
						'value'=>to_currency_no_money($inputprice),
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
						'value'=>$packet_price_infor->description,
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
			gia_goc:"required"
		},
		messages: 
		{
			
			start_date:
			{
				required:"<?php echo "Ngày tháng không được để trống"; ?>"
			},
			input_prices:
			{
				required:"<?php echo "Giá bán không được để trống"; ?>",
				number:"<?php echo $this->lang->line('items_unit_price_number'); ?>"
			},
			gia_goc:
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