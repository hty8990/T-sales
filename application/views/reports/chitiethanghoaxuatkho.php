<div class="card">
    <div role="tabpanel" class="tab-pane active" id="da_chon">
        <table id="dachon_sales_table" class="table table-striped table-hover search_table">
                <thead>
                    <tr bgcolor="#CCC">
                        <th>Ngày xuất hàng</th>
                        <th>Khách hàng</th>
                        <th>Số lượng</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $type = "";
                $tongsoluong = 0;
                $tongtien = 0;
                foreach($datas as $data){
                    $soluong = $data->quantity + $data->quantity_give - $data->quantity_loan + $data->quantity_loan_return;
                    $tongsoluong = $tongsoluong +  $soluong;
                    $giatri =  $soluong * $data->input_prices * $data->unit_weigh;
                    $tongtien = $tongtien + $giatri;
                    if($data->type == 1){
                        $type = "Bán hàng";
                    }else{
                        $type = "Khác";
                    }
                    $titiled = "Bán: ".$data->quantity;

                    if($data->quantity_give > 0){
                        $titiled .= " + Tặng: ".$data->quantity_give;
                    }

                    if($data->quantity_loan > 0){
                        $titiled .= " - Hàng gửi: ".$data->quantity_loan;
                    }
                    if($data->quantity_loan_return > 0){
                        $titiled .= " - Trả hàng gửi: ".$data->quantity_loan_return;
                    }

                ?>
                    <tr title="<?php echo $titiled; ?>" >
                        <td><?php echo date("d-m-Y H:i:s", strtotime($data->sale_time)) ?></td>
                        <td><?php echo $data->full_name ?></td>
                        <td><?php echo $soluong. " bao" ?></td>
                        <td><?php echo anchor("sales/receipt/".$data->sale_id, '<span class="glyphicon glyphicon-print"></span>',
            array('target' => '_blank')); ?></td>
                    </tr>
                <?php
                    }
                ?>
                <tr>
                <td colspan="2"> Tổng xuất hàng</td>
                <td><?php echo $tongsoluong . " bao" ; ?></td>
                <td></td>
                <td></td>
                <td></td>
                </tr>
                <tr>
                <td colspan="2"> Tổng trả lại</td>
                <td><?php echo $hangtralai[0]->soluong_tralai ?></td>
                <td></td>
                <td></td>
                <td></td>
                </tr>
                <td colspan="2"> Tổng</td>
                <td><?php echo $tongsoluong - $hangtralai[0]->soluong_tralai ?> bao</td>
                <td></td>
                <td></td>
                <td></td>
                </tbody>
            </table>
    </div>

</div>