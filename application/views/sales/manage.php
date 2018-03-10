<?php $this->load->view("partial/header"); ?>

<?php $this->load->view('partial/print_receipt', array('print_after_sale'=>false, 'selected_printer'=>'takings_printer')); ?>

<style>
.color-red {
	color:red;
}
</style>

<div id="title_bar" class="print_hide btn-toolbar">
	<span style='font-weight: bold; font-size: 30px; margin-left: 10px;color: #217DBB;
    font-family: "Times New Roman",serif;'><?php echo $table_headers_text; ?></span>
	<button id="button_print_doc" class='btn btn-info btn-sm pull-right'>
		<span class="glyphicon glyphicon-print">&nbsp</span>In danh sách
	</button>
	<?php echo anchor("sales", '<span class="glyphicon glyphicon-shopping-cart">&nbsp</span>' . $this->lang->line('sales_register'), array('class'=>'btn btn-info btn-sm pull-right', 'id'=>'show_sales_button')); ?>
</div>

<div id="toolbar">
	<div class="pull-left form-inline" role="toolbar" style="width: 120%;">
		<button id="delete" class="btn btn-default btn-sm print_hide">
			<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
		</button>
		
		<?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control 
		input-sm'
		, 'style'=>'width: 70%;'
		, 'id'=>'daterangepicker')); ?>
	</div>
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<div id="payment_summary">
</div>
<script type="text/javascript">
$(document).ready(function()
{
	$("span.color-red").parents('li').css("property", "value");

	$("#button_print_doc").click(function()
    {
		window.print();
    });
	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e) {
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
</script>
<?php $this->load->view("partial/footer"); ?>
