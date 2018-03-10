<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>
<?php 
if(isset($promotion_detail->start_date) && $promotion_detail->start_date !== ''){
$start_date = date("d/m/Y", strtotime($promotion_detail->start_date));
}else{
$start_date = '';
}
if(isset($promotion_detail->end_date) && $promotion_detail->end_date !== ''){
$end_date = date("d/m/Y", strtotime($promotion_detail->end_date));
}else{
$end_date = '';
}
?>
<?php echo form_open('Promotion_detail/save/'.$promotion_detail->id, array('id'=>'item_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="inv_item_basic_info">
		<div class="form-group form-group-sm">
		<?php echo form_label('Tên chương trình khuyến mại', 'item_number', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'item_number',
						'id'=>'item_number',
						'class'=>'form-control input-sm',
						'value'=>$promotion->promotion_name,
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
			<label class="required control-label col-xs-3">Ngày kết thúc: </label>
			<div class="col-xs-8">
			<?php echo form_input(array(
				'name'=>'end_date',
				'id'=>'end_date',
				'value'=>$end_date,
				'class'=>'form-control input-sm')
				); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Khuyến mại theo Kg', 'promotion_kg', array('class'=>' control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'promotion_kg',
						'id'=>'promotion_kg',
						'value'=>$promotion_detail->promotion_kg,
						'class'=>'form-control input-sm')
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Khuyến mại theo %', 'promotion_percent', array('class'=>' control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'promotion_percent',
						'id'=>'promotion_percent',
						'value'=>$promotion_detail->promotion_percent,
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
						'value'=>$promotion_detail->description,
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
			start_date:"required",
			end_date:"required",
			gia_ban:"required",
			gia_goc:"required"
		},
		messages: 
		{
			
			start_date:
			{
				required:"<?php echo "Ngày tháng không được để trống"; ?>"
			},
			end_date:
			{
				required:"<?php echo "Ngày tháng không được để trống"; ?>"
			},
			gia_ban:
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
	$("#end_date").change(function() {
		var datediff = daydiff(parseDate($('#end_date').val()), parseDate($('#start_date').val()));
		if(datediff > 0){
			alert('Ngày kết thúc không nhỏ hơn ngày bắt đầu!');
			$('#end_date').val('');
		}
       
    });
    
function parseDate(str) {
    var mdy = str.split('/');
    return new Date(mdy[2], mdy[0]-1, mdy[1]);
}

function daydiff(first, second) {
    return Math.round((second-first)/(1000*60*60*24));
}

_setDatepicker($('#start_date'));
 _setDatepicker($('#end_date'));
})
</script>
  <style type="text/css">
  	.ui-icon{
  		text-indent: 0px !important;
  	}
  </style>