<?php $this->load->view("partial/header"); ?>

<?php
if(isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}
?>

<div class="row">

	<div class="col-md-6">
		<div class="panel panel-primary">
		  	<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-list-alt">&nbsp</span>Báo cáo công nợ</h3>
		  	</div>
			<div class="list-group">
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/congnothu');?>">01 - Công nợ phải thu</a>
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/congnotra');?>">02 - Công nợ phải trả</a>
			 </div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel panel-primary">
		  	<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-list-alt">&nbsp</span>Báo cáo doanh số</h3>
		  	</div>
			<div class="list-group">
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/doanhsosanluong');?>">03 - Doanh số theo sản lượng</a>
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/doanhsokhachhang');?>">04 - Doanh số theo khách hàng</a>
			 </div>
		</div>
	</div>
</div>

<div class="row">
	

	<div class="col-md-6">
		<div class="panel panel-primary">
		  	<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-list-alt">&nbsp</span>Kết quả kinh doanh</h3>
		  	</div>
			<div class="list-group">
				<?php if($checkadmin) {?>
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/ketquakinhdoanh');?>">05 - Kết quả kinh doanh</a>
				<?php }	 ?>
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/soquytienmat');?>">06 - Sổ quỹ tiền mặt</a>
			 </div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel panel-primary">
		  	<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-list">&nbsp</span><?php echo $this->lang->line('reports_summary_reports'); ?></h3>
		  	</div>
			<div class="list-group">
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/hanghoanhapkho');?>">07 - Hàng hóa nhập kho</a>
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/hanghoaxuatkho');?>">08 - Hàng hóa xuất kho</a>
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/hanghoatonkho');?>">09 - Hàng hóa tồn kho</a>
				<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/hoanghoaxuatkhobaobi');?>">10 - Hàng hóa xuất kho bao bì</a>
				<!--<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/hoanghoatonkhobaobi');?>">11 - Hàng hóa tồn kho bao bì</a>-->
			 </div>
		</div>
	</div>
</div>

<?php $this->load->view("partial/footer"); ?>