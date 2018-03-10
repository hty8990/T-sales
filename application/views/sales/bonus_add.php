<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>
<?php echo form_open('sales/bonus_save', array('id'=>'item_kit_form', 'class'=>'form-horizontal')); ?>
	<input id="selectsupplier" name="selectsupplier" value= '<?php echo $thu_chi[0]['supplier_id']; ?>' type="hidden">
	<input id="selectcustomer" name="selectcustomer" value= '<?php echo $thu_chi[0]['customer_id']; ?>' type="hidden">
	<fieldset id="item_kit_basic_info">
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3">Từ ngày: </label>
			<div class="col-xs-8">
			<?php echo form_input(array(
				'name'=>'from_date',
				'id'=>'from_date',
				'value'=>'',
				'class'=>'form-control input-sm')
				); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3">Đến ngày: </label>
			<div class="col-xs-8">
			<?php echo form_input(array(
				'name'=>'to_date',
				'id'=>'to_date',
				'value'=>'',
				'class'=>'form-control input-sm')
				); ?>
			</div>
		</div>
		<div class="form-group form-group-sm" id="select_khachhang">
			<?php echo form_label('Khách hàng', 'customer', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
			<?php echo form_input(array(
					'name'=>'customer',
					'id'=>'customer',
					'value'=>'',
					'class'=>'form-control input-sm')
					); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Số Kg', 'money', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-5'>
				<?php echo form_input(array(
						'name'=>'money',
						'id'=>'money',
						'class'=>'form-control input-sm',
						'value'=>"")
						);?>
			</div>
			<button type="button" class="btn btn-primary">Primary</button>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Tiêu đề', 'Label', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'label',
						'id'=>'label',
						'class'=>'form-control input-sm',
						'value'=>"")
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Đơn giá', 'money', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'money',
						'id'=>'money',
						'class'=>'form-control input-sm',
						'value'=>"")
						);?>
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
			e.preventDefault();
			$('#selectcustomer').val(ui.item.value);
			$('#customer').val(ui.item.label);
		}
    });

	$('#item_kit_form').validate($.extend({
		submitHandler:function(form)
		{
		$(form).ajaxSubmit({
			success:function(response)
			{
				dialog_support.hide();
				table_support.handle_submit('<?php echo site_url('item_kits'); ?>', response);
			},
			dataType:'json'
		});

		},
		rules:
		{
			from_date:"required",
			to_date:"required",
			unit_price:"required",
			selectcustormer:"required"
		},
		messages:
		{
			from_date:"<?php echo 'Từ ngày không được để trống'; ?>",
			to_date:"<?php echo 'Đến ngày không được để trống'; ?>",
			unit_price:"<?php echo $this->lang->line('items_price_required'); ?>",
			selectcustormer: "<?php echo "Khách hàng không được để trống"; ?>"
		}
	}, form_support.error));
});

function delete_item_kit_row(link)
{
	$(link).parent().parent().remove();
	return false;
}

</script>
 <script>
$( function() {
    _setDatepicker($('#to_date'));
    _setDatepicker($('#from_date'));
})
</script>
  <style type="text/css">
  	.ui-icon{
  		text-indent: 0px !important;
  	}
  </style>