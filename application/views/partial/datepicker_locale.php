<?php $this->lang->load('calendar'); $this->lang->load('date'); ?>

$.fn.datetimepicker.dates['<?php echo $this->config->item("language"); ?>'] = {
    days: [
		"Chủ nhật",
        "Thứ hai",
        "Thứ ba",
        "Thứ tư",
        "Thứ năm",
        "Thứ sáu",
        "Thứ bẩy",
		],
        daysShort: [
		"CN",
        "T2",
        "T3",
        "T4",
        "T5",
        "T6",
        "T7"
		],
        daysMin: [
		"CN",
        "T2",
        "T3",
        "T4",
        "T5",
        "T6",
        "T7"
		],
        months: [
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
        monthsShort: [
		"T1",
        "T2",
        "T3",
        "T4",
        "T5",
        "T6",
        "T7",
        "T8",
        "T9",
        "T10",
        "T11",
        "T12"
		],
    today: "<?php echo $this->lang->line("datepicker_today"); ?>",
    <?php
        if( strpos($this->config->item('timeformat'), 'a') !== false )
        {
    ?>
    meridiem: ["am", "pm"],
    <?php
        }
        elseif( strpos($this->config->item('timeformat'), 'A') !== false )
        {
    ?>
    meridiem: ["AM", "PM"],
    <?php
        }
        else
        {
    ?>
    meridiem: [],
    <?php
        }
    ?>
    weekStart: <?php echo $this->lang->line("datepicker_weekstart"); ?>
};