<div class="card">
    <div role="tabpanel" class="tab-pane active" id="da_chon">
        <table id="dachon_sales_table" class="table table-striped table-hover search_table">
                <thead>
                    <tr bgcolor="#CCC">
                        <th>Ngày ban hàng</th>
                        <th>Tên</th>
                        <th>Giá trị hàng nhập/xuất kho</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $tongtrigiahoadon = $tongsotienthu = 0;
                        //echo "<pre>"; print_r($data); echo "</pre>"; exit;
                        foreach($datas as $data){
                            $tongtrigiahoadon = $tongtrigiahoadon+ $data->thanhtien;
                        ?>
                            <tr>
                                <td><?php echo date("d-m-Y H:i:s", strtotime($data->sale_time)) ?></td>
                                <td><?php echo $data->full_name ?></td>
                                <td><?php echo to_currency($data->thanhtien) ?></td>
                                 <?php if($check == 'vietrung'){ ?>
                                <td><?php echo get_link_by_type($data->type,$data->sale_id); ?>
                                 <?php }else{
                                 ?>
                                 <td><?php echo get_link_by_type_receiving($data->type,$data->receiving_id); ?>
                                  <?php } ?>
                                </td>
                            </tr>
                        <?php    
                        }
                        if($giatrihangtralai){
                            $tongtrigiahoadon = $tongtrigiahoadon - $giatrihangtralai;
                        }
                    ?>
                    <tr>
                        <td colspan="2"><b>Tổng: </b></td>
                        <td><b><?php echo to_currency($tongtrigiahoadon); ?></b></td>
                        <td></td>
                    </tr>
                    <?php if($check == 'vietrung'){ ?>
                    <tr>
                        <td colspan="2"><b>Hàng trả lại: </b></td>
                        <td><b><?php echo to_currency($giatrihangtralai); ?></b></td>
                        <td></td>
                    </tr>
                   <?php } ?>
                    <tr>
                        <td colspan="2"><b>Giá trị phải trả: </b></td>
                        <td><b><?php echo to_currency($tongtrigiahoadon); ?></b></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
    </div>
</div>