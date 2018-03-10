<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error_message))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error_message."</div>";
	exit;
}
?>

<?php $this->load->view('partial/print_receipt'); ?>

<div class="print_hide" id="control_buttons" style="text-align:right">
	<a style="text-align:right;" href="javascript:printdoc();"><div class="btn btn-info btn-sm", id="show_print_button"><?php echo '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'); ?></div></a>
	<?php if($mode == 'sale'){ ?>
	<?php echo anchor("sales/receiptexport/".$sale_id, '<span class="glyphicon glyphicon-indent-right">&nbsp</span>Phiếu xuất kho', array('class'=>'btn btn-info btn-sm', 'id'=>'show_sales_button')); ?>
	<?php } ?>
	<?php if(isset($customer_email) && !empty($customer_email)): ?>
		<a href="javascript:void(0);"><div class="btn btn-info btn-sm", id="show_email_button"><?php echo '<span class="glyphicon glyphicon-envelope">&nbsp</span>' . $this->lang->line('sales_send_receipt'); ?></div></a>
	<?php endif; ?>
	<?php echo anchor("sales", '<span class="glyphicon glyphicon-shopping-cart">&nbsp</span>' . $this->lang->line('sales_register'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_sales_button')); ?>
	<?php echo anchor("sales/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('sales_takings'), array('class'=>'btn btn-info btn-sm', 'id'=>'show_takings_button')); ?>
	
</div>

<script type="text/javascript">
$(document).ready(function()
{
	 $("#show_print_button").click(function()
    {
		window.print();
    });
})

</script>

<?php if($mode == 'sale'){ ?>
<?php $this->load->view("sales/" . $this->config->item('receipt_template')); ?>
<?php }elseif($mode == 'return'){ ?>
<?php $this->load->view("sales/receipt_return"); ?>
<?php }elseif($mode == 'taiche'){ ?>
<?php $this->load->view("sales/receipt_taiche"); ?>
<?php }elseif($mode == 'huy'){ ?>
<?php $this->load->view("sales/receipt_huy"); ?>
<?php }elseif($mode == 'tra_ncc'){ ?>
<?php $this->load->view("sales/receipt_tra_ncc"); ?>
<?php } ?>
<?php $this->load->view("partial/footer"); ?>