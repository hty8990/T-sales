<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>
	 $("#start_date").change(function() {
       table_support.refresh();
    });

    $(".trangthaihan").change(function() {
       table_support.refresh();
    });

	table_support.init({
		resource: '<?php echo site_url($controller_name);?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'id',
		 queryParams: function() {
            return $.extend(arguments[0], {
                start_date: $("#start_date").val() || '',
                stock_location: $("#stock_location").val(),
                trangthaihan: $('input[name=trangthaihan]:checked').val(),
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

<div id="title_bar" class="btn-toolbar">
<span style='font-weight: bold; font-size: 25px; margin-left: 10px;color: #217DBB;
    font-family: "Times New Roman",serif;'>Quản lý các đợt khuyến mại</span>
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view"); ?>'
			title='<?php echo $this->lang->line($controller_name. '_new'); ?>'>
		<span class="glyphicon glyphicon-tags">&nbsp</span><?php echo $this->lang->line($controller_name. '_new'); ?>
	</button>
</div>

<div id="toolbar">
	<div class="pull-left form-inline" role="toolbar">
		<button id="delete" class="btn btn-default btn-sm">
			<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete"); ?>
		</button>
		 <span>Ngày nhập</span>
             <?php echo form_input(array(
                'name'=>'start_date',
                'id'=>'start_date',
                'value'=>date("d/m/Y"),
                'class'=>'form-control input-sm')
                ); ?>
        <label>
        <input class="trangthaihan" type="radio" <?php echo $conhan; ?> name="trangthaihan" value="conhan"> Còn hạn
        </label>
        <span style="margin-left:10px;"></span>
        <label>
        <input class="trangthaihan" type="radio" <?php echo $quahan; ?> name="trangthaihan" value="quahan"> Tất cả
        </label>
	</div>
</div>

<div id="table_holder">
	<table id="table"></table>
</div>
 <script>
$( function() {
    _setDatepicker($('#start_date'));
})
</script>
 <style type="text/css">
    .ui-icon{
        text-indent: 0px !important;
        cursor: pointer;
    }
  </style>
<?php $this->load->view("partial/footer"); ?>