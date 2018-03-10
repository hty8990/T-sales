<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('items/save_inventory/'.$item_info->item_id, array('id'=>'item_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="inv_item_basic_info">
		<?php echo form_hidden('item_number', $item_info->item_number); ?>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_item_number'), 'name', array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-8">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-barcode"></span></span>
					<?php echo form_input(array(
							'name'=>'item_number',
							'id'=>'item_number',
							'class'=>'form-control input-sm',
							'disabled'=>'',
							'value'=>$item_info->item_number)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_name'), 'name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'disabled'=>'',
						'value'=>$item_info->name)
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Tìm mã khách hàng', 'customer', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
			<?php echo form_input(array(
					'name'=>'customer',
					'id'=>'customer',
					'class'=>'form-control input-sm')
					); ?>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<?php echo form_label('Tên khách hàng', 'name_customer', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
			<?php echo form_input(array(
					'name'=>'name_customer',
					'id'=>'name_customer',
					'disabled'=>'',
					'class'=>'form-control input-sm')
					); ?>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<?php echo form_label('Giá bán', 'price', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'price',
						'id'=>'price',
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
			newquantity:
			{
				required:true,
				number:true
			}
   		},
		messages: 
		{
			
			newquantity:
			{
				required:"<?php echo $this->lang->line('items_quantity_required'); ?>",
				number:"<?php echo $this->lang->line('items_quantity_number'); ?>"
			}
		}
	});
});
function delete_item_kit_row(link)
{
	alert(link);
}
</script>