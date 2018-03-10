<div class="card">
    <div role="tabpanel" class="tab-pane active" id="da_chon">
        <table id="dachon_sales_table" class="table table-striped table-hover search_table">
                <thead>
                    <tr bgcolor="#CCC">
                        <th>Ngày nhập hàng</th>
                        <th>Khách hàng</th>
                        <th>Số lượng</th>
                        <th>Giá gốc</th>
                        <th>Thành tiền</th>
                        <th>Hình thức</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $type = "";
                $tongsoluong = 0;
                $tongtien = 0;
                foreach($datas as $data){
                    $type = "Nhập hàng";
                    $tongsoluong =  $tongsoluong + $data->quantity;
                    $giatri =  $data->quantity * $data->input_prices;
                    $tongtien = $tongtien + $giatri;
                ?>
                    <tr>
                        <td><?php echo date("d-m-Y H:i:s", strtotime($data->receiving_time)) ?></td>
                        <td><?php echo $data->full_name ?></td>
                        <td><?php echo $data->quantity. " bao" ?></td>
                        <td><?php echo to_currency($data->input_prices) ?> /1kg</td>
                        <td><?php echo to_currency($giatri) ?></td>
                        <td><?php echo $type ?></td>
                    </tr>
                <?php
                    }
                ?>
                 <tr>
                <td colspan="2"> Tổng</td>
                <td><?php echo $tongsoluong . " bao" ; ?></td>
                <td></td>
                <td><?php echo to_currency($tongtien) ?></td>
                <td></td>
                </tr>
                </tbody>
            </table>
    </div>

</div>