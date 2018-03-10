<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('items/save/'.$item_info->id, array('id'=>'item_form', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>
	<fieldset id="item_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_item_number'), 'item_number', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<div class="input-group">
					
					<?php echo form_input(array(
							'name'=>'item_number',
							'id'=>'item_number',
							'class'=>'form-control input-sm',
							//'disabled'=>'',
							'value'=>$item_info->item_number)
							);?>
							<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-barcode"></span></span>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_unit_weigh'), 'unit_weigh', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('unit_weigh', $quycach, $item_info->unit_weigh, array('class'=>'form-control')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$item_info->name)
						);?>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_category'), 'category', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
					<?php 
					echo form_dropdown('category',$arr_listype,$selected_cartegory, array('class' => 'form-control input-sm')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_supplier'), 'supplier', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('supplier_id', $suppliers, $selected_supplier, array('class'=>'form-control')); ?>
			</div>
		</div>
		<?php echo form_hidden('items_kit', $item_info->packet_id); ?>
		<div class="form-group form-group-sm">
			<?php echo form_label('Bao bì', 'check_bao_bi', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'check_bao_bi',
						'id'=>'check_bao_bi',
						'value'=>1,
						'checked'=>($check_packet) ? 1 : 0)
						);?>

			</div><span>Nếu chọn sẽ thêm một mã bao bì tương ứng</span>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_is_status'), 'is_status', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'is_status',
						'id'=>'is_status',
						'value'=>1,
						'checked'=>($item_info->status) ? 1 : 0)
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
						'value'=>$item_info->description)
						);?>
			</div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
	//validation and submit handling
	$(document).ready(function()
	{

		$('#item_form').validate($.extend({
			submitHandler: function(form, event) {
				$(form).ajaxSubmit({
					success:function(response)
					{
						dialog_support.hide();
						table_support.handle_submit('<?php echo site_url('items'); ?>', response);
					},
					dataType:'json'
				});
			},

			rules:
			{
				name:"required",
				unit_weigh:"required",
				item_number:"required",
				category:"required",
				item_kit_paket:"required",
				item_number:
				{
					required: true,
					remote:
					{
						url: "<?php echo site_url($controller_name . '/check_item_number')?>",
						type: "post",
						data: $.extend(csrf_form_base(),
						{
							"item_id" : "<?php echo $item_info->id; ?>",
							"item_number" : function()
							{
								return $("#item_number").val();
							},
						})
					}
				},
				cost_price:
				{
					required: true,
					remote: "<?php echo site_url($controller_name . '/check_numeric')?>"
				},
				unit_weigh:
				{
					required: true,
					remote: "<?php echo site_url($controller_name . '/check_numeric')?>"
				},
				unit_price:
				{
					required:true,
					remote: "<?php echo site_url($controller_name . '/check_numeric')?>"
				}
			},

			messages:
			{
				name:"<?php echo $this->lang->line('items_name_required'); ?>",
				item_number: "<?php echo $this->lang->line('items_item_number_duplicate'); ?>",
				category:"<?php echo $this->lang->line('items_category_required'); ?>",
				cost_price:
				{
					required:"<?php echo $this->lang->line('items_cost_price_required'); ?>",
					number:"<?php echo $this->lang->line('items_cost_price_number'); ?>"
				},
				unit_price:
				{
					required:"<?php echo $this->lang->line('items_unit_price_required'); ?>",
					number:"<?php echo $this->lang->line('items_unit_price_number'); ?>"
				},
				unit_weigh:
				{
					required:"<?php echo $this->lang->line('items_unit_price_required'); ?>",
					number:"<?php echo $this->lang->line('items_unit_price_number'); ?>"
				}
			}
		}, form_support.error));
	});
</script>