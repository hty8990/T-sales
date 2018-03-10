<?php echo form_open('employees/save_info/', array('id' => 'info_config_form', 'class' => 'form-horizontal')); ?>
	<div id="config_wrapper">
		<fieldset id="config_info">
			<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
			<ul id="info_error_message_box" class="error_message_box"></ul>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('config_company'), 'company', array('class' => 'control-label col-xs-2 required')); ?>
				<div class="col-xs-8">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
						<?php echo form_input(array(
							'name' => 'company',
							'id' => 'company',
							'class' => 'form-control input-sm required',
							'value'=>$this->config->item('company'))); ?>
					</div>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('config_address'), 'address', array('class' => 'control-label col-xs-2 required')); ?>
				<div class='col-xs-8'>
					<?php echo form_textarea(array(
						'name' => 'address',
						'id' => 'address',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('address'))); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('config_website'), 'website', array('class' => 'control-label col-xs-2')); ?>
				<div class="col-xs-8">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-globe"></span></span>
						<?php echo form_input(array(
							'name' => 'website',
							'id' => 'website',
							'class' => 'form-control input-sm',
							'value'=>$this->config->item('website'))); ?>
					</div>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('common_email'), 'email', array('class' => 'control-label col-xs-2')); ?>
				<div class="col-xs-8">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-envelope"></span></span>
						<?php echo form_input(array(
							'name' => 'email',
							'id' => 'email',
							'type' => 'email',
							'class' => 'form-control input-sm',
							'value'=>$this->config->item('email'))); ?>
					</div>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('config_phone'), 'phone', array('class' => 'control-label col-xs-2 required')); ?>
				<div class="col-xs-8">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-phone-alt"></span></span>
						<?php echo form_input(array(
							'name' => 'phone',
							'id' => 'phone',
							'class' => 'form-control input-sm required',
							'value'=>$this->config->item('phone'))); ?>
					</div>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('config_fax'), 'fax', array('class' => 'control-label col-xs-2')); ?>
				<div class="col-xs-8">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-phone-alt"></span></span>
						<?php echo form_input(array(
							'name' => 'fax',
							'id' => 'fax',
							'class' => 'form-control input-sm',
							'value'=>$this->config->item('fax'))); ?>
					</div>
				</div>
			</div>

			<!--<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('config_backup_database'), 'config_backup_database', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-8'>
					<div id="backup_db" class="btn btn-default btn-sm">
						<span style="top:22%;"><?php echo $this->lang->line('config_backup_button'); ?></span>
					</div>
				</div>
			</div>-->

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('common_return_policy'), 'return_policy', array('class' => 'control-label col-xs-2 required')); ?>
				<div class='col-xs-8'>
					<?php echo form_textarea(array(
						'name' => 'return_policy',
						'id' => 'return_policy',
						'class' => 'form-control input-sm required',
						'value'=>$this->config->item('return_policy'))); ?>
				</div>
			</div>
		</fieldset>
	</div>
<?php echo form_close(); ?>

<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	$("#backup_db").click(function() {
		window.location='<?php echo site_url('config/backup_db') ?>';
	});

	$('#info_config_form').validate($.extend({
		submitHandler:function(form)
		{
		$(form).ajaxSubmit({
			success:function(response)
			{
				dialog_support.hide();
				table_support.handle_submit('<?php echo site_url('item_kits'); ?>', response);
			},
			dataType:'json'
		});

		},
		rules:
		{
			company: "required",
			address: "required",
			phone: "required",
    		email: "email",
    		return_policy: "required" 		
   		},

		messages: 
		{
			company: "Ten cong ty khong duoc de trong",
			address: "Dia chi khong duoc de trong",
			phone: "So dien thoai khong duoc de trong",
			email: "Email khong duoc de trong",
			return_policy: "Thong tin khong duoc de trong"
		}
	}, form_support.error));

});
</script>
