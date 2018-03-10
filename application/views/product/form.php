<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php
if(isset($product_info->price) && $product_info->price !== ''){
$input_prices = to_currency_no_money((int)$product_info->price);
}else{
$input_prices = '';
}
?>

<?php echo form_open('product/save/'.$product_info->id, array('id'=>'product_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="customer_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_item_number'), 'item_number', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<div class="input-group">
					
					<?php echo form_input(array(
							'name'=>'code',
							'id'=>'code',
							'class'=>'form-control input-sm',
							//'disabled'=>'',
							'value'=>$product_info->code)
							);?>
							<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-barcode"></span></span>
				</div>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$product_info->name)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Đơn giá', 'price', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'price',
						'id'=>'price',
						'class'=>'form-control input-sm',
						'value'=>$input_prices)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Giới hạn số lượng', 'limit', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'limit',
						'id'=>'limit',
						'class'=>'form-control input-sm',
						'value'=>$product_info->c_limit)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Tỷ lệ % 1', 'percen1', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'percen1',
						'id'=>'percen1',
						'class'=>'form-control input-sm',
						'value'=>$product_info->percen1)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Tỷ lệ % 2', 'percen2', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'percen2',
						'id'=>'percen2',
						'class'=>'form-control input-sm',
						'value'=>$product_info->percen2)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Tỷ lệ % 3', 'percen3', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'percen3',
						'id'=>'percen3',
						'class'=>'form-control input-sm',
						'value'=>$product_info->percen3)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Tỷ lệ % 4', 'percen4', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'percen4',
						'id'=>'percen4',
						'class'=>'form-control input-sm',
						'value'=>$product_info->percen4)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_is_status'), 'is_status', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'is_status',
						'id'=>'is_status',
						'value'=>1,
						'checked'=>($product_info->status) ? 1 : 0)
						);?>

			</div><span><?php echo $this->lang->line('items_is_status_hd') ?></span>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_description'), 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control input-sm',
						'value'=>$product_info->description)
						);?>
			</div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">

//validation and submit handling
$(document).ready(function()
{
	$("#price").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });
	$('#product_form').validate($.extend({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
				success:function(response)
				{
					dialog_support.hide();
					table_support.handle_submit('<?php echo site_url($controller_name); ?>', response);
				},
				dataType:'json'
			});
		},
		rules:
		{
			name: "required",
			code: "required"
   		},
		messages: 
		{
			name: "<?php echo "Tên sản phẩm không được để trống"; ?>",
			code: "<?php echo "Mã sản phẩm không được để trống"; ?>"
		}
	}, form_support.error));
});
</script>