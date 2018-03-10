<?php $this->load->view("partial/header"); ?>

<div id="page_title"><span style="font-weight: bold; font-size: 25px; margin-left: 10px;color: #217DBB; font-family: "Times New Roman",serif;">Báo cáo hàng hóa nhập kho</span></div>

<?php
if(isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}
?>
<input style="display: none" id="idcustomer" value="">
<input style="display: none" id="type" value="hanghoanhapkho">
<input style="display: none" id="mode" value="receive">
<?php echo form_open('#', array('id'=>'item_form', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>
	<div class="form-group form-group-sm" style="margin-left: 5%;">
		<?php echo form_label($this->lang->line('reports_date_range'), 'report_date_range_label', array('class'=>'control-label col-xs-3')); ?>
		<div class="col-xs-4">
				<?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
		</div>

	</div>
	<div class="form-group form-group-sm" style="margin-left: 5%;">
		<label class="control-label col-xs-3"><?php echo $this->lang->line('receivings_mode'); ?></label>
		<div class="col-xs-4">
				<?php echo form_dropdown('mode', $modes, $mode, array('onchange'=>"changemode()", 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
		</div>
		
	</div>
	<div class="form-group form-group-sm" style="margin-left: 5%;">
		<?php echo form_label("Tìm mã nhà cung cấp", 'report_date_range_label', array('class'=>'control-label col-xs-3')); ?>
		<div class="col-xs-4">
				<?php echo form_input(array('name'=>'supplier', 'id'=>'supplier', 'class'=>'form-control input-sm', 'value'=>$this->lang->line('receivings_start_typing_supplier_name'))); ?>
		</div>
		
	</div>
	<table id="table"></table>
</div>

<div id="payment_summary">
</div>
<script type="text/javascript">
$(document).ready(function()
{

	$("#button_print_doc").click(function()
    {
		window.print();
    });
	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e) {
		table_support.refresh();
	});
	$("#customer").change(function() {
       table_support.refresh();
    });
	// load the preset datarange picker
	<?php $this->load->view('partial/daterangepicker'); ?>

	$('#daterangepicker').data('daterangepicker').setStartDate("<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),01,date("Y")));?>");
	// update the hidden inputs with the selected dates before submitting the search data
    var start_date = "<?php echo date('Y-m-d', mktime(0,0,0,date("m"),01,date("Y")));?>";
    $("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
        table_support.refresh();
    });

	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name);?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'sale_id',
		onLoadSuccess: function(response) {
			if($("#table tbody tr").length > 1) {
				$("#payment_summary").html(response.payment_summary);
				$("#table tbody tr:last td:first").html("");
				$(".pagination-detail").hide();
				$(".pagination").hide();
				
			}
		},
		queryParams: function() {
			return $.extend(arguments[0], {
				start_date: start_date,
				end_date: end_date,
				customer_id: $("#idcustomer").val(),
				type: $("#type").val(),
				mode: $("#mode").val(),
				filters: $("#filters").val() || [""]
			});
		},
		columns: {
			'invoice': {
				align: 'center'
			}
		}
	});
});
var clear_fields = function()
    {
        if ($(this).val().match("<?php echo $this->lang->line('sales_start_typing_item_name') . '|' . $this->lang->line('sales_start_typing_customer_name'); ?>"))
        {
            $(this).val('');
        }
    };

   $('#item,#supplier').click(function()
    {
    	$(this).attr('value','');
    });

    $("#supplier").autocomplete(
    {
		source: '<?php echo site_url("suppliers/suggest"); ?>',
    	minChars:0,
    	delay:10,
		select: function(e, ui) {
            e.preventDefault();
            $('#idcustomer').val(ui.item.value);
            $("#supplier").val(ui.item.label);
            table_support.refresh();
        }
    });

	dialog_support.init("a.modal-dlg, button.modal-dlg");

	$('#supplier').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('receivings_start_typing_supplier_name'); ?>");
    });

	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});
	function changemode(){
		$("#mode").val($("select[name='mode']").val());
		table_support.refresh();
	}
</script>
<?php $this->load->view("partial/footer"); ?>