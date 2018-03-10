<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e)
	{
        table_support.refresh();
    });
	// load the preset datarange picker
    $(".sothuchi").change(function() {
       $('#mode_form').submit();
    });
    // load the preset datarange picker
     $("#persion_type").change(function() {
        table_support.refresh();
    });


	// load the preset datarange picker
	<?php $this->load->view('partial/daterangepicker'); ?>
    // set the beginning of time as starting date
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
		uniqueId: 'thu_chi_id',
		queryParams: function() {
            return $.extend(arguments[0], {
                start_date: start_date,
                end_date: end_date,
                sothuchi: $('input[name=sothuchi]:checked').val(),
                persion_type: $('#persion_type').val(),
                stock_location: $("#stock_location").val(),
                filters: $("#filters").val() || [""]
            });
        },
	});

});

</script>

<div id="title_bar" class="btn-toolbar">
<span style='font-weight: bold; font-size: 25px; margin-left: 10px;color: #217DBB;
    font-family: "Times New Roman",serif;'><?php echo $table_headers_text; ?></span>
<?php if($thuchi == 'sochi'){ ?>
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/viewsochi"); ?>'
			title='<?php echo $this->lang->line($controller_name. '_new'); ?>'>
		<span class="glyphicon glyphicon-tags">&nbsp</span><?php echo 'Thêm sổ chi'; ?>
	</button>
<?php }  ?>
<?php if($thuchi == 'sothu'){ ?>
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view"); ?>'
			title='<?php echo $this->lang->line($controller_name. '_new'); ?>'>
		<span class="glyphicon glyphicon-tags">&nbsp</span><?php echo 'Thêm sổ thu'; ?>
	</button>
<?php } ?>
</div>

<div id="toolbar">
	<div class="pull-left form-inline" role="toolbar">
		<button id="delete" class="btn btn-default btn-sm print_hide">
			<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
		</button>
		 <?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
		 <?php echo form_open($controller_name."/change_mode", array('id'=>'mode_form','style'=>'display:inline')); ?>
		<label>
		<input class="sothuchi" type="radio" <?php echo $checkedthu; ?> name="sothuchi" value="sothu"> Sổ thu
		</label>
		<span style="margin-left:10px;"></span>
		<label>
		<input class="sothuchi" type="radio" <?php echo $checkedchi; ?> name="sothuchi" value="sochi"> Sổ chi
		</label>
		<span style="margin-left:10px;">
		<select name="persion_type" id="persion_type" class="form-control input-sm">
		<option value="">-- Chọn đối tượng --</option>
		<option value="khach_hang">Khách hàng</option>
		<option value="nha_cung_cap">Nhà cung cấp</option>
		<option value="khac">Khác</option>
		</select>
		</span>
	</div>
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<?php $this->load->view("partial/footer"); ?>