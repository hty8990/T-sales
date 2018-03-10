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
    $("#start_date").change(function() {
       table_support.refresh();
    });
    <?php $this->load->view('partial/bootstrap_tables_locale'); ?>

    table_support.init({
        employee_id: <?php echo $this->Employee->get_logged_in_employee_info()->person_id; ?>,
        resource: 'Items',
        headers: <?php echo $table_headers; ?>,
        pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
        uniqueId: 'items.id',
        queryParams: function() {
            return $.extend(arguments[0], {
                start_date: $("#start_date").val() || '',
                customer_id: $("#customer_id").val() || '',
                stock_location: $("#stock_location").val(),
                filters: $("#filters").val() || [""]
            });
        },
        onLoadSuccess: function(response) {
            $('a.rollover').imgPreview({
				imgCSS: { width: 200 },
				distanceFromCursor: { top:10, left:-210 }
			})
        }
    });
});
</script>

<div id="title_bar" class="btn-toolbar print_hide">
    <span style='font-weight: bold; font-size: 25px; margin-left: 10px;color: #217DBB;
    font-family: "Times New Roman",serif;'>Quản lý sản phẩm</span>
    <button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view"); ?>'
            title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
        <span class="glyphicon glyphicon-tag">&nbsp</span><?php echo $this->lang->line($controller_name. '_new'); ?>
    </button>
</div>

<div id="toolbar">
    <div class="pull-left form-inline" role="toolbar">
        <button id="delete" class="btn btn-default btn-sm print_hide">
            <span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete"); ?>
        </button>
             <span>Ngày nhập</span>
             <?php echo form_input(array(
                'name'=>'start_date',
                'id'=>'start_date',
                'value'=>date("d/m/Y"),
                'class'=>'form-control input-sm date_picker')
                ); ?>
            <span>Khách hàng</span>
            <?php echo form_input(array(
                    'name'=>'customer',
                    'style'=>'width:250px !important',
                    'id'=>'customer',
                    'class'=>'form-control input-sm')
                    ); ?>
            <input id="customer_id" name="customer_id" value= '' type="hidden">
    </div>
</div>

<div id="table_holder">
    <table id="table"></table>
</div>
 <script>
$( function() {
    $("#customer").autocomplete(
    {
        source: '<?php echo site_url("customers/suggest"); ?>',
        minChars:0,
        autoFocus: false,
        delay:10,
        select: function(e, ui) {
            e.preventDefault();
            $('#customer_id').val(ui.item.value);
            $("#customer").val(ui.item.label);
            table_support.refresh();
        }
    });
    _setDatepicker($('.date_picker'));
})
</script>
 <style type="text/css">
    .ui-icon{
        text-indent: 0px !important;
        cursor: pointer;
    }
  </style>
<?php $this->load->view("partial/footer"); ?>
