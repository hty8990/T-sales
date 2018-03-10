<div class="card">
    <div role="tabpanel" class="tab-pane active" id="da_chon">
        <table id="dachon_sales_table" class="table table-striped table-hover search_table">
                <thead>
                    <tr bgcolor="#CCC">
                        <th>Thời gian</th>
                        <th>Tên khách hàng</th>
                        <th>Số tiền</th>
                        <th>Hình thức</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $tongtien = 0;
                        foreach($datas as $data){
                              //echo "<pre>"; print_r($data); echo "</pre>"; exit;
                            if($data['type'] == 5){
                                // lay nha cung cap
                                $users = $this->Supplier->get_info($data['supplier_id']);
                                $full_name = $users->full_name;
                            }else if($data['type'] == 6){
                                $full_name = 'Khác';
                            }else{
                                $users = $this->Customer->get_info($data['customer_id']);
                                $full_name = $users->full_name;
                            }
                            //echo "<pre>"; print_r($data); echo "</pre>"; exit;
                            $hoadon = $data['order_money'];
                            $sotien = $data['pay_money'];
                            $tongtien = $tongtien + $sotien;
                            if(($tongtien)!= 0){
                        ?>
                            <tr>
                                <td><?php echo date("d-m-Y H:i:s", strtotime($data['sale_time'])) ?></td>
                                <td><?php echo $full_name; ?></td>
                                <td><?php echo to_currency($sotien) ?></td>
                                <td><?php echo get_type_sale($data['type']); ?></td>
                                <td><?php echo get_link_by_type($data['type'],$data['sale_id']); ?>
                                </td>
                            </tr>
                        <?php    
                            }
                        }
                    ?>
                    <tr>
                        <td colspan="2"><b>Tổng: </b></td>
                        <td><b><?php echo to_currency($tongtien); ?></b></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
    </div>
</div>