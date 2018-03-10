<?php echo form_open('items', array('id'=>'item_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="count_item_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_item_number'), 'name', array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-8">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-barcode"></span></span>
					<?php echo form_input(array(
							'name'=>'item_number',
							'id'=>'item_number',
							'class'=>'form-control input-sm',
							'disabled'=>'',
							'value'=>$item_info->item_number)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('items_name'), 'name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'disabled'=>'',
						'value'=>$item_info->name)
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Giá bán', 'name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'disabled'=>'',
						'value'=>to_currency($item_info->cost_price))
						); ?>
			</div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<table id="items_count_details" class="table table-striped table-hover">
	<thead>
		<tr>
			<th width="20%">Tên khách hàng</th>
			<th width="20%">Địa chỉ</th>
			<th width="20%">Giá theo khách hàng</th>
		</tr>
	</thead>
	<tbody>
		<?php
		/*
		 * the tbody content of the table will be filled in by the javascript (see bottom of page)
		*/

		foreach($arr_customer_infor as $row)
		{
			?>
			<tr>
			<td><?php echo $row['full_name'] ?></td>
			<td><?php echo $row['address'] ?></td>
			<td><?php echo to_currency($row['c_money']) ?></td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
