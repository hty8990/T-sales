$( function() {
    _setDatepicker($('#date_sale'));
    $("#amount_tendered").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });
    $("#vanchuyen_dg").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });
    $("#sanluong_dongia").keyup(function(e){
        $(this).val(formatmonney($(this).val()));
    });
})

$(document).ready(function()
{
    if($('#check_thuongsanluong').is(":checked")) {
         $("#thuongsanluong").show();
    }
    // thuong san luong
    $('#check_thuongsanluong').change(function() {
        if($(this).is(":checked")) {
            $("#thuongsanluong").show();
        }else{
            $("#thuongsanluong").hide();
            $("#sanluong_soluong").val('');
            $("#sanluong_dongia").val('');
            $("#sanluong_tieude").val('Thuởng sản lượng tháng ');
            $('#set_uncheck_sanluong').submit();
        }
          
    });
    var typetrathuong = $("#hinhthucban").val();
    $('#payment_types').val(typetrathuong);
    $('.SelectItemreturn').change(function() 
    {
        var id_item = $(this).attr('data-index');
        var checked_return = false;
        if($(this).is(':checked')){
            checked_return = true;
        }
        $.post('<?php echo site_url($controller_name."/set_checked_return");?>', {checked_return: checked_return, id_item: id_item});
    });
    $("#item").autocomplete(
    {
        source: '<?php echo site_url($controller_name."/item_search"); ?>',
        minChars: 0,
        autoFocus: false,
        delay: 10,
        select: function (a, ui) {
            $(this).val(ui.item.value);
            $("#add_item_form").submit();
        }
    });
    var idselect = $('#selecttext').val();
    $('#'+idselect).select();

    $('#item').keypress(function (e) {
        if (e.which == 13) {
            $('#add_item_form').submit();
                return false;
        }
    });

    $('#item').blur(function()
    {
        $(this).val("<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

    var clear_fields = function()
    {
        if ($(this).val().match("<?php echo $this->lang->line('sales_start_typing_item_name') . '|' . $this->lang->line('sales_start_typing_customer_name'); ?>"))
        {
            $(this).val('');
        }
    };

    $("#customer").autocomplete(
    {
        source: '<?php echo site_url("customers/suggest"); ?>',
        minChars: 0,
        delay: 10,
        select: function (a, ui) {
            $(this).val(ui.item.value);
            $("#select_customer_form").submit();
        }
    });

    $('#item, #customer').click(clear_fields).dblclick(function(event)
    {
        $(this).autocomplete("search");
    });

    $('#customer').blur(function()
    {
        $(this).val("<?php echo $this->lang->line('sales_start_typing_customer_name'); ?>");
    });

    $('#comment').keyup(function() 
    {
        $.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
    });

    $('#date_sale').change(function() 
    {
        $.post('<?php echo site_url($controller_name."/set_date_sale");?>', {date_sale: $('#date_sale').val()});
    });
    
    $("#finish_sale_button").click(function()
    {
        $('#buttons_form').attr('action', '<?php echo site_url($controller_name."/complete"); ?>');
        $('#buttons_form').submit();
    });

    $("#suspend_sale_button").click(function()
    {   
        $('#buttons_form').attr('action', '<?php echo site_url($controller_name."/suspend"); ?>');
        $('#buttons_form').submit();
    });

    $("#cancel_sale_button").click(function()
    {
        if (confirm('<?php echo $this->lang->line("sales_confirm_cancel_sale"); ?>'))
        {
            $('#buttons_form').attr('action', '<?php echo site_url($controller_name."/cancel"); ?>');
            $('#buttons_form').submit();
        }
    });

    $("#add_payment_button").click(function()
    {
        $('#add_payment_form').submit();
    });


    $("#cart_contents input").keypress(function(event)
    {
        if (event.which == 13)
        {
            $(this).parents("tr").prevAll("form:first").submit();
        }
    });

    $("#sanluong_soluong").keypress(function(event)
    {
        if( event.which == 13 )
        {
            if(check_sanluong()){
                $('#form_sanluong').submit();
            }else{
                alert('Tiêu đề, số lượng,đơn giá không được để trống');
            }
            
        }
    });

    $("#sanluong_tieude").keypress(function(event)
    {
        if( event.which == 13 )
        {
            if(check_sanluong()){
                $('#form_sanluong').submit();
            }else{
                alert('Tiêu đề, số lượng,đơn giá không được để trống');
            }
            
        }
    });

    $("#sanluong_dongia").keypress(function(event)
    {
        if( event.which == 13 )
        {
            if(check_sanluong()){
                $('#form_sanluong').submit();
            }else{
                alert('Tiêu đề, số lượng,đơn giá không được để trống');
            }
            
        }
    });

    function check_sanluong(){
        if($("#sanluong_tieude").val() == '' || $("#sanluong_dongia").val() == '' || $("#sanluong_dongia").val() == ''){
            return false;
        }else{
            return true;
        }
    }

    $("#amount_tendered").keypress(function(event)
    {
        if( event.which == 13 )
        {
            $('#add_payment_form').submit();
        }
    });
    
    $("#finish_sale_button").keypress(function(event)
    {
        if ( event.which == 13 )
        {
            $('#finish_sale_form').submit();
        }
    });

    dialog_support.init("a.modal-dlg, button.modal-dlg");

    table_support.handle_submit = function(resource, response, stay_open)
    {
        if(response.success) {
            if (resource.match(/customers$/))
            {
                $("#customer").val(response.id);
                $("#select_customer_form").submit();
            }
            else
            {
                $("#item_location").val(1);
                $("#item").val(response.id);
                if (stay_open)
                {
                    $("#add_item_form").ajaxSubmit();
                }
                else
                {
                    $("#add_item_form").submit();
                }
            }
        }
    }
});

function count_weigh(qckg,id){
    var id1 = id+'1';
    var id2 = id+'2';
    var id3 = id+'3';
    var x = $('#'+id1).val();
    var total = x * qckg;
    if(total > 0){
        $('#'+id2).val(total);
    }
}
function count_weigh_km(qckg,id){
    var id1 = id+'1';
    var id2 = id+'2';
    var id3 = id+'3';
    var id4 = id+'4';
    var x = $('#'+id3).val();
    var total = x * qckg;
    if(total > 0){
        $('#'+id4).val(total);
    }
}