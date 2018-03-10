<?php
if(isset($person_info->birthday) && $person_info->birthday !== ''){
	$birthday = date("d/m/Y", strtotime($person_info->birthday));
}else{
	$birthday = '';
}
$check = false;
?>

<div class="form-group form-group-sm">	
	<?php echo form_label($this->lang->line('common_full_name'), 'full_name', array('class'=>'required control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<?php echo form_input(array(
				'name'=>'full_name',
				'id'=>'full_name',
				'class'=>'form-control input-sm',
				'value'=>$person_info->full_name)
				);?>
	</div>
</div>

<div class="form-group form-group-sm">	
	<?php echo form_label('Số CMTND', 'identity_card', array('class'=>'control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<?php echo form_input(array(
				'name'=>'identity_card',
				'id'=>'identity_card',
				'class'=>'form-control input-sm',
				'value'=>$person_info->identity_card)
				);?>
	</div>
</div>

<div class="form-group form-group-sm">	
	<?php echo form_label('Ngày sinh', 'birthday', array('class'=>'control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<?php echo form_input(array(
				'name'=>'birthday',
				'id'=>'birthday',
				'class'=>'form-control input-sm',
				'value'=>$birthday)
				);?>
	</div>
</div>

<div class="form-group form-group-sm">	
	<?php echo form_label($this->lang->line('common_address'), 'address', array('class'=>'control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<?php echo form_input(array(
				'name'=>'address',
				'id'=>'address',
				'class'=>'form-control input-sm',
				'value'=>$person_info->address)
				);?>
	</div>
</div>

<div class="form-group form-group-sm">	
	<?php echo form_label($this->lang->line('common_gender'), 'gender', !empty($basic_version) ? array('class'=>'required control-label col-xs-3') : array('class'=>'control-label col-xs-3')); ?>
	<div class="col-xs-4">
		<label class="radio-inline">
			<?php echo form_radio(array(
					'name'=>'gender',
					'type'=>'radio',
					'id'=>'gender',
					'value'=>1,
					'checked'=>$person_info->gender === '1')
					); ?> <?php echo $this->lang->line('common_gender_male'); ?>
		</label>
		<label class="radio-inline">
			<?php echo form_radio(array(
					'name'=>'gender',
					'type'=>'radio',
					'id'=>'gender',
					'value'=>0,
					'checked'=>$person_info->gender === '0')
					); ?> <?php echo $this->lang->line('common_gender_female'); ?>
		</label>

	</div>
</div>

<div class="form-group form-group-sm">	
	<?php echo form_label($this->lang->line('common_phone_number'), 'phone_number', array('class'=>'control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<div class="input-group">
			<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-phone-alt"></span></span>
			<?php echo form_input(array(
					'name'=>'phone_number',
					'id'=>'phone_number',
					'class'=>'form-control input-sm',
					'value'=>$person_info->phone_number)
					);?>
		</div>
	</div>
</div>

<div class="form-group form-group-sm">	
	<?php echo form_label($this->lang->line('common_email'), 'email', array('class'=>'control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<div class="input-group">
			<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-envelope"></span></span>
			<?php echo form_input(array(
					'name'=>'email',
					'id'=>'email',
					'class'=>'form-control input-sm',
					'value'=>$person_info->email)
					);?>
		</div>
	</div>
</div>


<div class="form-group form-group-sm">	
	<?php echo form_label($this->lang->line('common_comments'), 'comments', array('class'=>'control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<?php echo form_textarea(array(
				'name'=>'comments',
				'id'=>'comments',
				'class'=>'form-control input-sm',
				'value'=>$person_info->comments)
				);?>
	</div>
</div>

<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	nominatim.init({
		language : '<?php echo $this->config->item('language');?>',
	});
	 _setDatepicker($('#birthday'));
});
</script>
  <style type="text/css">
  	.ui-icon{
  		text-indent: 0px !important;
  	}
  </style>