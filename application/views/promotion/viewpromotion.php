<div class="card">
    <div role="tabpanel" class="tab-pane active" id="da_chon">
        <table id="dachon_sales_table" class="table table-striped table-hover search_table">
                <thead>
                    <tr bgcolor="#CCC">
                        <th>Loại sản phẩm</th>
                        <th>Hình thức</th>
                        <th>%</th>
                        <th>Đồng/Kg</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $type = "";
                foreach($arrdachons as $arrdachon){
                ?>
                    <tr>
                        <td><?php echo $arrdachon['list_name'] ?></td>
                        <td><?php echo $arrdachon['name'] ?></td>
                        <td><?php echo $arrdachon['promotion_pecen'] ?></td>
                        <td><?php echo $arrdachon['promotion_kg'] ?></td>
                    </tr>
                <?php
                    }
                ?>
                </tbody>
            </table>
    </div>
</div>