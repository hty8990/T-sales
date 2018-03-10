<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('item_kits/save_quantities/'.$item_info->id, array('id'=>'itemkit_form_quantitie', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>
	<fieldset id="inv_item_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label('Mã bao bì', 'item_number', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<div class="input-group">
					
					<?php echo form_input(array(
							'name'=>'item_number',
							'id'=>'item_number',
							'class'=>'form-control input-sm',
							'disabled'=>'',
							'value'=>$item_info->item_number)
							);?>
							<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-barcode"></span></span>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Tên bao bì', 'name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'disabled'=>'',
						'class'=>'form-control input-sm',
						'value'=>$item_info->name)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3">Ngày nhập: </label>
			<div class="col-xs-8">
			<?php echo form_input(array(
				'name'=>'input_date',
				'id'=>'input_date',
				'value'=>$start_date,
				'class'=>'form-control input-sm date_picker')
				); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Thêm số lượng', 'new_quantitie', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-3'>
				<div class="input-group">
				<?php echo form_input(array(
						'name'=>'new_quantitie',
						'id'=>'new_quantitie',
						'class'=>'form-control input-sm',
						'value'=>'')
						);?>
						<span class="input-group-addon input-sm">Túi</span>
				</div>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_description'), 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control input-sm',
						'value'=>'')
						);?>
			</div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
	//validation and submit handling
	$(document).ready(function()
	{
		_setDatepicker($('.date_picker'));
		$('#itemkit_form_quantitie').validate($.extend({
			submitHandler: function(form, event) {
				$(form).ajaxSubmit({
					success:function(response)
					{
						dialog_support.hide();
						window.location.href = '<?php echo site_url('item_kits'); ?>';
						 //table_support.refresh();
					},
					dataType:'json'
				});
			},

			rules:
			{
				new_quantitie:"required",
				input_date:"required"
			},

			messages:
			{
				new_quantitie:"Số lượng không được để trống!",
				input_date:"Ngày không được để trống"
			}
		}, form_support.error));
	});
</script>