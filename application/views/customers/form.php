<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('customers/save/'.$person_info->person_id, array('id'=>'customer_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="customer_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label("Mã KH", 'customers_code', array('class' => 'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'customers_code',
						'class'=>'form-control input-sm',
						'value'=>$person_info->code)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Nhân viên quản lý', 'people_manager', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('people_manager',$allpeople, $person_info->employees_id , array('id'=>'people_manager', 'class'=>'form-control')); ?>
			</div>
		</div>
		<?php $this->load->view("people/form_basic_info"); ?>	
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">

//validation and submit handling
$(document).ready(function()
{
	$('#customer_form').validate($.extend({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
				success:function(response)
				{
					dialog_support.hide();
					table_support.handle_submit('<?php echo site_url($controller_name); ?>', response);
				},
				dataType:'json'
			});
		},
		rules:
		{
			customers_code: "required",
			full_name: "required",
    		email: "email",
    		account_number:
			{
				remote:
				{
					url: "<?php echo site_url($controller_name . '/check_account_number')?>",
					type: "post",
					data: $.extend(csrf_form_base(),
					{
						"person_id" : "<?php echo $person_info->person_id; ?>",
						"account_number" : function()
						{
							return $("#account_number").val();
						}
					})
				}
			}
   		},
		messages: 
		{
			customers_code: "<?php echo "Mã khách hàng không được để trống"; ?>",
     		full_name: "<?php echo $this->lang->line('common_full_name_required'); ?>",
     		email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>",
			account_number: "<?php echo $this->lang->line('customers_account_number_duplicate'); ?>"
		}
	}, form_support.error));
});
</script>