<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('Promotion/save/'.$Promotion->id, array('id'=>'promotion_kit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="item_kit_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label('Loại', 'type', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('type', $arrtype_promotion, $Promotion->type, array('class'=>'form-control', 'id'=>'type')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Áp dụng cho', 'promotion_type', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('promotion_type', $arrListtype, $Promotion->promotion_type, array('class'=>'form-control', 'id'=>'promotion_type')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label("Hình thức khuyến mại", 'promotion_name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'promotion_name',
						'id'=>'promotion_name',
						'class'=>'form-control input-sm',
						'value'=>$Promotion->promotion_name)
						);?>
			</div>
		</div>
		
		<div class="form-group form-group-sm" id="tim_kiem_sp">
			<?php echo form_label('Tìm sản phẩm', 'item_promotion', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<input id="item" class="form-control input-sm ui-autocomplete-input" name="item" value="" size="40" tabindex="1" autocomplete="off" type="text">
			</div>
		</div>
		<div class="form-group form-group-sm" id="sp_ap_dung">
			<?php echo form_label('Sản phẩm áp dụng', 'item_promotion', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'item_promotion',
						'id'=>'item_promotion',
						'class'=>'form-control input-sm',
						'value'=>$Promotion->item_promotion)
						);?>
				<input id="item_id_promotion" name="item_id_promotion" value= '<?php echo $Promotion->item_id_promotion;  ?>' type="hidden">
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Mô tả', 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control input-sm',
						'value'=>$Promotion->description)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('STT', 'sort', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'sort',
						'id'=>'sort',
						'class'=>'form-control input-sm',
						'value'=>$Promotion->sort)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('', 'check_promotion', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'check_promotion',
						'id'=>'check_promotion',
						'value'=>1,
						'checked'=>($Promotion->check_all) ? 1 : 0)
						);?>

			</div><span>(Nếu chọn thì sẽ không tính sang các khuyến mại khác)</span>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">

//validation and submit handling
$(document).ready(function()
{
	if($('#promotion_type').val() == 'khac'){
		$("#tim_kiem_sp").show();
		$("#sp_ap_dung").show();
	}else{
		$("#tim_kiem_sp").hide();
		$("#sp_ap_dung").hide();
	}
	$('#promotion_type').change(function() 
	{
		if($('#promotion_type').val() == 'khac'){
			$("#tim_kiem_sp").show();
			$("#sp_ap_dung").show();
		}else{
			$("#tim_kiem_sp").hide();
			$("#sp_ap_dung").hide();
		}
	});
	$("#item").autocomplete(
	{
		source: '<?php echo site_url("sales/item_search"); ?>',
    	minChars: 0,
    	autoFocus: false,
       	delay: 10,
       	appendTo: ".modal-content",
		select: function (e, ui) {
			e.preventDefault();
			$("#item").val('');
			var stringtext = $('#item_promotion').val();
			if(stringtext!==''){
				stringtext = stringtext + ','  + ui.item.item_number ;
			}else{
				stringtext =  ui.item.item_number ;
				$('#item_id_promotion').val('');
			}
			var stringid = $('#item_id_promotion').val();
			if(stringid!==''){
				stringid = stringid + ','  + ui.item.value ;
			}else{
				stringid =  ui.item.value ;
			}
			$('#item_promotion').val(stringtext);
			$('#item_id_promotion').val(stringid);
		}
    });
	$('#promotion_kit_form').validate($.extend({
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
			promotion_code:"required",
			promotion_name:"required",
			promotion_type:"required"
		},
		messages:
		{
			promotion_code:"<?php echo "Chưa nhập mã khuyến mại"; ?>",
			promotion_name:"<?php echo "Chưa nhập tên khuyến mại"; ?>",
			promotion_type:"<?php echo "Chưa nhập loại sản phẩm khuyến mại"; ?>"
		}
	}, form_support.error));
});

function delete_item_kit_row(link)
{
	$(link).parent().parent().remove();
	return false;
}

</script>