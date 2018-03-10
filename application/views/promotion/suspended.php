
<?php 
echo form_open('sales/savekm/'.$custumer_id, array('id'=>'customer_form', 'class'=>'form-horizontal')); ?>
<?php echo form_hidden('listpromotion', $c_promotion); ?>
<div class="card">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#da_chon" aria-controls="da_chon" role="tab" data-toggle="tab">Đã chọn</a></li>
        <li role="presentation"><a href="#chua_chon" aria-controls="chua_chon" role="tab" data-toggle="tab">Chưa chọn</a></li>
        <li><input style="margin-bottom: 3px; margin-top: 5px;" class="form-control input-sm" id="search_promotion" type="text" onkeyup="myFunction()" placeholder="Tìm kiếm.."></li>
    </ul>
    <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="da_chon">
        <table id="dachon_sales_table" class="table table-striped table-hover search_table">
                <thead>
                    <tr bgcolor="#CCC">
                        <th>Loại sản phẩm</th>
                        <th>#</th>
                        <th>Hình thức</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>%</th>
                        <th>Đồng/Kg</th>
                        <th>ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $type = "";
                foreach($arrdachons as $arrdachon){
                ?>
                    <tr>
                        <?php
                        if($type !== $arrdachon['promotion_type']){
                            $type = $arrdachon['promotion_type'];
                            //print_r($arrListtype); echo "<br>";
                            foreach($arrListtype as $key1 => $values){
                                if($key1 == $arrdachon['promotion_type']){
                                    //echo $key['promotion_type']; echo "</br>";
                                    $name = $values;
                                    break;
                                }
                            }
                            ?>
                            <td><span><?php echo $name; ?></span></td>
                            <?php
                        }else{
                            echo "<td><span style='display:none'>".$name."</span></td>";
                        }
                        $checked = "checked";
                        ?>
                        <td><input <?php echo $checked; ?>  type="checkbox" name="btSelectItemkm" values="<?php echo $arrdachon['id']; ?>"></td>
                        <td><label for="btSelectItemkm"><?php echo $arrdachon['name'];  ?></label></td>
                        <td><?php echo date("d/m/Y", strtotime($arrdachon['start_date'])) ?></td>
                        <td><?php echo date("d/m/Y", strtotime($arrdachon['end_date'])) ?></td>
                        <td><?php echo $arrdachon['promotion_pecen'] ?></td>
                        <td><?php echo $arrdachon['promotion_kg'] ?></td>
                        <td><?php echo $arrdachon['description'] ?></td>
                    </tr>
                <?php
                    }
                ?>
                </tbody>
            </table>
    </div>
    <div role="tabpanel" class="tab-pane" id="chua_chon">
            <table id="chuachon_sales_table" class="table table-striped table-hover search_table">
            	<thead>
            		<tr bgcolor="#CCC">
            			<th>Loại sản phẩm</th>
            			<th>#</th>
            			<th>Hình thức</th>
            			<th>Ngày bắt đầu</th>
            			<th>Ngày kết thúc</th>
            			<th>%</th>
            			<th>Đồng/Kg</th>
            			<th>ghi chú</th>
            		</tr>
            	</thead>
            	<tbody>
            	<?php
            	$type = "";
        		foreach($arrchuachons as $arrchuachon){
            	?>
            		<tr>
            			<?php
            			if($type !== $arrchuachon['promotion_type']){
                            $type = $arrchuachon['promotion_type'];
                            //print_r($arrListtype); echo "<br>";
                            foreach($arrListtype as $key1 => $values){
                                if($key1 == $arrchuachon['promotion_type']){
                                    //echo $key['promotion_type']; echo "</br>";
                                    $name = $values;
                                    break;
                                }
                            }
                            ?>
                            <td><span><?php echo $name; ?></span></td>
                            <?php
                        }else{
                            echo "<td><span style='display:none'>".$name."</span></td>";
                        }
            			$checked = "";
            			?>
            			<td><input <?php echo $checked; ?>  type="checkbox" name="btSelectItemkm" values="<?php echo $arrchuachon['id']; ?>"></td>
            			<td><label for="btSelectItemkm"><?php echo $arrchuachon['name'];  ?></label></td>
            			<td><?php echo date("d/m/Y", strtotime($arrchuachon['start_date'])) ?></td>
            			<td><?php echo date("d/m/Y", strtotime($arrchuachon['end_date'])) ?></td>
            			<td><?php echo $arrchuachon['promotion_pecen'] ?></td>
            			<td><?php echo $arrchuachon['promotion_kg'] ?></td>
            			<td><?php echo $arrchuachon['description'] ?></td>
            		</tr>
            	<?php
            		}
            	?>
            	</tbody>
            </table>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">

//validation and submit handling
$(document).ready(function()
{
	$("#submit_custumer").click(function() {
		var listpromotion = '';
		$('#dachon_sales_table input:checked').each(function() {
			listpromotion = listpromotion + ',' + $(this).attr('values');
		});
        $('#chuachon_sales_table input:checked').each(function() {
            listpromotion = listpromotion + ',' + $(this).attr('values');
        });
		$("input[name=listpromotion]").val(listpromotion);
		 $("#customer_form").submit();
	});


})

function myFunction() {
  // Declare variables 
  var input, filter, table, tr, td, i, hinhthuc, loaisanpham, chuoitimkiem;
  input = $("#search_promotion").val();
  $('#dachon_sales_table > tbody  > tr').each(function() {
    hinhthuc = $(this).find("td").eq(2).find("label").html();
    loaisanpham = $(this).find("td").eq(0).find("span").html();
    ghichu = $(this).find("td").eq(7).html();
    chuoitimkiem = hinhthuc + ' ' + loaisanpham + ' ' + ghichu;
    if (chuoitimkiem.toUpperCase().indexOf(input.toUpperCase()) >= 0){
        $(this).show();
    }else{
        $(this).hide();
    }
  });
  $('#chuachon_sales_table > tbody  > tr').each(function() {
    hinhthuc = $(this).find("td").eq(2).find("label").html();
    loaisanpham = $(this).find("td").eq(0).find("span").html();
    ghichu = $(this).find("td").eq(7).html();
    chuoitimkiem = hinhthuc + ' ' + loaisanpham + ' ' + ghichu;
    if (chuoitimkiem.toUpperCase().indexOf(input.toUpperCase()) >= 0){
        $(this).show();
    }else{
        $(this).hide();
    }
  });
  // Loop through all table rows, and hide those who don't match the search query
}
</script>