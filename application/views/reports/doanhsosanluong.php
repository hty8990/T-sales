<?php $this->load->view("partial/header"); ?>

<div id="page_title"><span style="font-weight: bold; font-size: 25px; margin-left: 10px;color: #217DBB; font-family: "Times New Roman",serif;">Báo cáo doanh số theo sản lượng</span></div>

<?php
if(isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}
?>
<input style="display: none" id="idcustomer" value="">
<input style="display: none" id="type" name="type" value="doanhsosanluong">
<?php echo form_open('#', array('id'=>'item_form', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>
	<div class="form-group form-group-sm" style="margin-left: 5%;">
		<?php echo form_label($this->lang->line('reports_date_range'), 'report_date_range_label', array('class'=>'control-label col-xs-3')); ?>
		<div class="col-xs-4">
				<?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
		</div>

	</div>
	<div class="form-group form-group-sm" style="margin-left: 5%;">
		<?php echo form_label('Chọn khách hàng', 'customer', array('class'=>' control-label col-xs-3')); ?>
			<div class='col-xs-4'>
			<?php echo form_input(array(
					'name'=>'customer',
					'id'=>'customer',
					'value'=>'',
					'class'=>'form-control input-sm')
					); ?>
			</div>
		
	</div>
	<div class="form-group form-group-sm" style="margin-left: 5%;">
			<?php echo form_label('Theo nhân viên quản lý', 'people_manager', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<?php echo form_dropdown('people_manager',$allpeople, "", array('id'=>'people_manager', 'class'=>'form-control')); ?>
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
	$("#category").change(function() {
	  table_support.refresh();
	});
	$("#people_manager").change(function() {
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
			}
		},
		queryParams: function() {
			return $.extend(arguments[0], {
				start_date: start_date,
				end_date: end_date,
				supplier: $("#idcustomer").val(),
				category: $("#category").val(),
				people_manager: $("#people_manager").val(),
				type: $("#type").val(),
				filters: $("#filters").val() || [""]
			});
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

   $('#item,#customer').click(function()
    {
    	$(this).attr('value','');
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
			$('#idcustomer').val(ui.item.value);
			$('#customer').val(ui.item.label);
			table_support.refresh();
		}
    });


	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});
	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});
</script>
<?php $this->load->view("partial/footer"); ?>