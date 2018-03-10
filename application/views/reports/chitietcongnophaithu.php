<div class="card">
    <div role="tabpanel" class="tab-pane active" id="da_chon">
        <table id="dachon_sales_table" class="table table-striped table-hover search_table">
                <thead>
                    <tr bgcolor="#CCC">
                        <th>Ngày bán hàng</th>
                        <th>Trị giá hóa đơn</th>
                        <th>Số tiền trả</th>
                        <th>Tiền nợ</th>
                        <th>Hình thức</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $tongnotk = 0;
                        $tongnokt =0;
                        $tongno = 0;
                        $tongtrigiahoadon = $tongsotientra = 0;
                        foreach($datas as $data){
                            $hoadon = $data['order_money'];
                            $sotientra = $data['pay_money'];
                            $sotienno = $hoadon-$sotientra;
                            $tongtrigiahoadon = $tongtrigiahoadon + $hoadon;
                            $tongsotientra = $tongsotientra + $sotientra;
                            $tongnotk = $tongnotk + $sotienno;
                            if(($hoadon)!= 0 || $sotientra != 0){
                        ?>
                            <tr>
                                <td><?php echo date("d-m-Y H:i:s", strtotime($data['sale_time'])) ?></td>
                                <td><?php echo to_currency($hoadon) ?></td>
                                <td><?php echo to_currency($sotientra) ?></td>
                                <td><?php echo to_currency($sotienno) ?></td>
                                <td><?php echo get_type_sale($data['type']); ?></td>
                                <td><?php echo get_link_by_type($data['type'],$data['sale_id']); ?>
                                </td>
                            </tr>
                        <?php    
                            }
                        }
                    ?>
                    <tr>
                        <td><b>Tổng: </b></td>
                        <td><b><?php echo to_currency($tongtrigiahoadon); ?></b></td>
                        <td><b><?php echo to_currency($tongsotientra); ?></b></td>
                        <td><b><?php echo to_currency($tongnotk); ?></b></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Kỳ trước: <?php echo to_currency($nokytruoc); ?></b></td>
                        <td><b>Trong kỳ:</b></td>
                        <td><b><?php echo to_currency($tongnotk); ?></b></td>
                        <td colspan="2"><b>Tổng: <?php echo to_currency($nokytruoc+$tongnotk); ?></b></td>
                    </tr>
                </tbody>
            </table>
    </div>
</div>