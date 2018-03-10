<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>
<?php 
if(isset($thu_chi[0]['sale_time']) && $thu_chi[0]['sale_time'] !== ''){
$start_date = date("d/m/Y", strtotime($thu_chi[0]['sale_time']));
}else{
$start_date = date("d/m/Y");
}
if($thu_chi[0]['money'] !== ''){
	$money = (int)$thu_chi[0]['money'];
	$money =to_currency_no_money($money);
}else{
	$money = '';
}

?>
<?php echo form_open('config/save/'.$thu_chi[0]['thu_chi_id'], array('id'=>'item_kit_form', 'class'=>'form-horizontal')); ?>
	<input id="selectsupplier" name="selectsupplier" value= '<?php echo $thu_chi[0]['supplier_id']; ?>' type="hidden">
	<input id="selectcustomer" name="selectcustomer" value= '<?php echo $thu_chi[0]['customer_id']; ?>' type="hidden">
	<fieldset id="item_kit_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label('Loại sổ', 'unit_weight', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'type',
						'id'=>'type',
						'class'=>'form-control input-sm',
						'disabled'=>'',
						'value'=>'Sổ thu')
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Hình thức', 'sales_payment', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('payment_type', $payment_options,$thu_chi[0]['payment_type'] , array('id'=>'payment_type', 'class'=>'form-control')); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Đối tượng', 'object', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('object', $arrKhachhang,$thu_chi[0]['object'], array('class'=>'form-control','id'=>'object')); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3">Ngày cập nhật: </label>
			<div class="col-xs-8">
			<?php echo form_input(array(
				'name'=>'start_date',
				'id'=>'start_date',
				'value'=>$start_date,
				'class'=>'form-control input-sm')
				); ?>
			</div>
		</div>
		<div class="form-group form-group-sm" id="select_nhacungcap">
			<?php echo form_label('Chọn nhà cung cấp', 'suppliers', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
			<?php echo form_input(array(
					'name'=>'suppliers',
					'id'=>'suppliers',
					'value'=>$thu_chi[0]['full_name'],
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
					'value'=>$thu_chi[0]['full_name'],
					'class'=>'form-control input-sm')
					); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Số tiền', 'money', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'money',
						'id'=>'money',
						'class'=>'form-control input-sm',
						'value'=>$money)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Mô tả', 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control input-sm',
						'value'=>$thu_chi[0]['comment'])
						);?>
			</div>
		</div>

	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
function show_hide_object(type){
	if(type=='khach_hang'){
		$("#select_nhacungcap").hide();
		$("#select_khachhang").show();
		$("#suppliers").val('');
	}else if(type=='nha_cung_cap'){
		$("#select_nhacungcap").show();
		$("#select_khachhang").hide();
		$("#customer").val('');
	}else{
		$("#select_khachhang").hide();
		$("#select_nhacungcap").hide();
		$("#customer").val('');
		$("#suppliers").val('');
	}
}
//validation and submit handling
$(document).ready(function()
{
	show_hide_object($("#object").val());
	$('#object').change(function() 
	{
		show_hide_object($(this).val());
	});

	$("#money").keyup(function(e){
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
			e.preventDefault();
			$('#selectcustomer').val(ui.item.value);
			$('#customer').val(ui.item.label);
		}
    });

	$("#suppliers").autocomplete(
    {
		source: '<?php echo site_url("suppliers/suggest"); ?>',
    	minChars:0,
		autoFocus: false,
		delay:10,
		appendTo: ".modal-content",
		select: function(e, ui) {
			e.preventDefault();
			$('#selectsupplier').val(ui.item.value);
			$('#suppliers').val(ui.item.label);
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
			customer:"required",
			money:"required",
			unit_price:"required",
			selectcustormer:"required"
		},
		messages:
		{
			customer:"<?php echo 'Mã khách hàng không được để trống'; ?>",
			money:"<?php echo 'Số tiền không được để trống'; ?>",
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
    _setDatepicker($('#start_date'));
})
</script>
  <style type="text/css">
  	.ui-icon{
  		text-indent: 0px !important;
  	}
  </style>