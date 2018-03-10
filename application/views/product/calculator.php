<style>

.span-result{
	font-weight: bold;
	color:red;
}
	.glyphicon-refresh-animate {
    -animation: spin .7s infinite linear;
    -webkit-animation: spin2 .7s infinite linear;
}

@-webkit-keyframes spin2 {
    from { -webkit-transform: rotate(0deg);}
    to { -webkit-transform: rotate(360deg);}
}

@keyframes spin {
    from { transform: scale(1) rotate(0deg);}
    to { transform: scale(1) rotate(360deg);}
}
</style>
<div class="card">
	<div role="tabpanel" class="tab-pane active" id="da_chon">
		<div class="form-group form-group-sm" style="margin-bottom: 39px;">
			<label for="price" class="control-label col-xs-2">Giới hạn SL</label>
			<div class="col-xs-4">
				<input type="text" name="limit" value="700" id="limit" class="form-control input-sm">
			</div>
			<div>
				<button id="limit_load" class="btn btn-danger btn-sm pull-right" >Tính kết quả tối ưu</button>
			</div>
		</div>
		<div class="form-group form-group-sm" style="margin-bottom: 39px;">
			<label for="price" class="control-label col-xs-2">Tỷ lệ % 1</label>
			<div class="col-xs-4">
				<input type="text" name="percen_1" value="" id="percen_1" class="form-control input-sm">
			</div>
			<label for="price" class="control-label col-xs-2">Tỷ lệ % 2</label>
			<div class="col-xs-4">
				<input type="text" name="percen_2" value="" id="percen_2" class="form-control input-sm">
			</div>
		</div>
		<br>
		<div class="form-group form-group-sm" style="margin-bottom: 39px;">
			<label for="price" class="control-label col-xs-2">Tỷ lệ % 3</label>
			<div class="col-xs-4">
				<input type="text" name="percen_3" value="" id="percen_3" class="form-control input-sm">
			</div>
			<label for="price" class="control-label col-xs-2">Tỷ lệ % 4</label>
			<div class="col-xs-4">
				<input type="text" name="percen_4" value="" id="percen_4" class="form-control input-sm">
			</div>
		</div>
		<div id="result-percen">
			
		</div>
		<br>
		<table id="dachon_sales_table" class="table table-striped table-hover search_table">
			<thead>
                <tr bgcolor="#CCC">
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Thành tiền</th>
                    <th>Giới hạn SL</th>
                    <th>Tỷ lệ % 1</th>
                    <th>Tỷ lệ % 2</th>
                    <th>Tỷ lệ % 3</th>
                    <th>Tỷ lệ % 4</th>
                </tr>
            </thead>
            <tbody>
            	<?php
                $type = "";
                foreach($products as $product){
                ?>
                <tr>
                	<td><?php echo $product->code ?></td>
                    <td><?php echo $product->name  ?></td>
                    <td><?php echo to_currency($product->price) ?></td>
                    <td><?php echo $product->c_limit ?></td>
                    <td><?php echo $product->percen1 ?></td>
                    <td><?php echo $product->percen2 ?></td>
                    <td><?php echo $product->percen3 ?></td>
                    <td><?php echo $product->percen4 ?></td>
                </tr>
                <?php } ?>
            </tbody>
		</table>
	</div>
</div>
<script>
	$('#limit_load').on('click', function() {
	   var limit = $("#limit").val();
	   var percen1 = $("#percen_1").val();
	   if(limit > 0 && percen1 > 0){
	   		/**jQuery('.modal-dlg').modal('show').on('hide.bs.modal', function (e) {
			  e.preventDefault();
			})
		   $(this).addClass( "glyphicon-refresh-animate" );**/
	   		$.ajax({
		        url : '<?php echo site_url($controller_name."/process");?>',
		        type : "get",
		        dataType:"json",
		        data : { 
		             limit : limit,
		             percen1: percen1,
		             percen2: $("#percen_2").val(),
		             percen3: $("#percen_3").val(),
		             percen4: $("#percen_4").val(),

		        },
		        success : function (arrResult){
		        	if(arrResult['success']){
		        		 window.open(arrResult['url'],'_blank');
		        	}
		        }
		    });
	   }else{
	   		alert('chưa chọn đủ thông tin!');
	   }
	});

</script>