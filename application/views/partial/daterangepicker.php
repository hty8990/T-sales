<?php $this->lang->load("calendar"); $this->lang->load("date"); ?>

var start_date = "<?php echo date('Y-m-d') ?>";
var end_date   = "<?php echo date('Y-m-d') ?>";

$('#daterangepicker').daterangepicker({
	"ranges": {
		"<?php echo $this->lang->line("datepicker_today"); ?>": [
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d"),date("Y")));?>",
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>"
		],
		"<?php echo $this->lang->line("datepicker_yesterday"); ?>": [
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")-1,date("Y")));?>",
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d"),date("Y"))-1);?>"
		],
		"<?php echo $this->lang->line("datepicker_this_month"); ?>": [
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y")));?>",
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")+1,1,date("Y"))-1);?>"
		],
		"<?php echo $this->lang->line("datepicker_last_month"); ?>": [
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")-1,1,date("Y")));?>",
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y"))-1);?>"
		],
		"<?php echo $this->lang->line("datepicker_this_year"); ?>": [
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,1,1,date("Y")));?>",
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y")+1)-1);?>"
		],
		"Năm trước": [
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,1,1,date("Y")-1));?>",
			"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,12,31,date("Y")-1));?>"
		]
	},
	"locale": {
		"format": '<?php echo dateformat_momentjs($this->config->item("dateformat"))?>',
		"separator": " - ",
		"applyLabel": "<?php echo $this->lang->line("datepicker_apply"); ?>",
		"cancelLabel": "<?php echo $this->lang->line("datepicker_cancel"); ?>",
		"fromLabel": "<?php echo $this->lang->line("datepicker_from"); ?>",
		"toLabel": "<?php echo $this->lang->line("datepicker_to"); ?>",
		"customRangeLabel": "<?php echo $this->lang->line("datepicker_custom"); ?>",
		"daysOfWeek": [
			"CN",
	        "T2",
	        "T3",
	        "T4",
	        "T5",
	        "T6",
	        "T7"
		],
		"monthNames": [
			"Tháng 1",
	        "Tháng 2",
	        "Tháng 3",
	        "Tháng 4",
	        "Tháng 5",
	        "Tháng 6",
	        "Tháng 7",
	        "Tháng 8",
	        "Tháng 9",
	        "Tháng 10",
	        "Tháng 11",
	        "Tháng 12"
		],
		"firstDay": <?php echo $this->lang->line("datepicker_weekstart"); ?>
	},
	"alwaysShowCalendars": true,
	"startDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>",
	"endDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>",
	"minDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,01,01,2010));?>",
	"maxDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>"
}, function(start, end, label) {
	start_date = start.format('YYYY-MM-DD');
	end_date = end.format('YYYY-MM-DD');
});