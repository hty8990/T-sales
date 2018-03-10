<div class="card">
    <div role="tabpanel" class="tab-pane active" id="da_chon">
        <table id="dachon_sales_table" class="table table-striped table-hover search_table">
                <thead>
                    <tr bgcolor="#CCC">
                        <th>Ngày mua hàng</th>
                        <th>Giá bán</th>
                        <th>Tổng kg</th>
                        <th>Khuyến mại</th>
                        <th>Thuởng sảng luợng</th>
                        <th>giá trị đơn hàng</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $type = "";
                $tong_kg = $khuyen_mai = $thuong_san_luong = $tien_lai = 0;
                foreach($datas as $data){
                    $tong_kg += $data['tong_kg'];
                    $khuyen_mai += $data['khuyen_mai'];
                    $thuong_san_luong += $data['thuong_san_luong'];
                    $tien_lai += $data['tien_lai'];
                    //echo "<pre>"; print_r($data); echo "</pre>"; exit;
                ?>
                    <tr>
                        <td><?php echo $data['ngay_mua']; ?></td>
                        <td><?php echo to_currency($data['gia_ban']) ?></td>
                        <td><?php echo $data['tong_kg']?> kg</td>
                        <td><?php echo to_currency($data['khuyen_mai']) ?></td>
                        <td><?php echo to_currency($data['thuong_san_luong']) ?></td>
                        <td><?php echo to_currency($data['tien_lai']) ?></td>
                        <td><?php echo $data['xem'] ?></td>
                    </tr>
                <?php
                    }
                ?>
                <?php
                    if($returns){
                ?>
                <tr>
                    <td colspan="2"> Trả lại</td>
                    <td><?php echo $returns[0]['soluong_tralai'] . " kg" ; ?></td>
                    <td></td><td></td>
                    <td><?php echo to_currency($returns[0]['thanhtien']) ?></td>
                </tr>
                <?php
                    $tong_kg = $tong_kg - $returns[0]['soluong_tralai'];
                    $tien_lai = $tien_lai - $returns[0]['thanhtien'];
                    }
                ?>
                 <tr>
                <td colspan="2"> Tổng</td>
                <td><?php echo $tong_kg . " kg" ; ?></td>
                <td><?php echo to_currency($khuyen_mai) ?></td>
                <td><?php echo to_currency($thuong_san_luong) ?></td>
                <td><?php echo to_currency($tien_lai) ?></td>
                <td></td>
                </tr>
                </tbody>
            </table>
    </div>

</div>