<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('item_kits/save/'.$item_kit_info->id, array('id'=>'item_kit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="item_kit_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('item_kits_code'), 'item_number', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'item_number',
						'id'=>'item_number',
						'class'=>'form-control input-sm',
						'value'=>$item_kit_info->item_number)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('item_kits_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$item_kit_info->name)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_unit_weigh'), 'unit_weight', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('unit_weight', $quycach, $item_kit_info->unit_weight, array('class'=>'form-control')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('item_kits_quantities'), 'quantities', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'quantities',
						'id'=>'quantities',
						'class'=>'form-control input-sm',
						'value'=>intval($item_kit_info->quantities))
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('item_kits_description'), 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control input-sm',
						'value'=>$item_kit_info->description)
						);?>
			</div>
		</div>

	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">

//validation and submit handling
$(document).ready(function()
{
	$("#item").autocomplete({
		source: '<?php echo site_url("items/suggest"); ?>',
		minChars:0,
		autoFocus: false,
		delay:10,
		appendTo: ".modal-content",
		select: function(e, ui) {
			if ($("#item_kit_item_" + ui.item.value).length == 1)
			{
				$("#item_kit_item_" + ui.item.value).val(parseFloat( $("#item_kit_item_" + ui.item.value).val()) + 1);
			}
			else
			{
				$("#item_kit_items").append("<tr><td><a href='#' onclick='return delete_item_kit_row(this);'><span class='glyphicon glyphicon-trash'></span></a></td><td>" + ui.item.label + "</td><td><input class='quantity form-control input-sm' id='item_kit_item_" + ui.item.value + "' type='text' name=item_kit_item[" + ui.item.value + "] value='1'/></td></tr>");
			}
			$("#item").val("");
			return false;
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
			name:"required",
			item_number:"required",
			unit_price:"required"
		},
		messages:
		{
			name:"<?php echo $this->lang->line('items_name_required'); ?>",
			item_number:"<?php echo $this->lang->line('items_code_required'); ?>",
			unit_price:"<?php echo $this->lang->line('items_price_required'); ?>"
		}
	}, form_support.error));
});

function delete_item_kit_row(link)
{
	$(link).parent().parent().remove();
	return false;
}

</script>