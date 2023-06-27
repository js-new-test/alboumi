if($('.credit_card_method').is(':checked'))
{
    $('#debit_opt_btn').hide();
}

$('input[type="radio"]').click(function() {
    if($(this).attr('class') == 'credit_card_method') {
         $('#debit_opt_btn').hide();           
    }
    else {
         $('#debit_opt_btn').show();   
    }
    if($(this).attr('class') == 'debit_card_method') {
         $('#credit_opt_btn').hide();           
    }
    else {
         $('#credit_opt_btn').show();   
    }
});

$(document).on('click','#payment-continue-btn', function(){
    var cart_method = $('input[name="pm"]:checked').val();
    var cart_master_id = $('#cart_master_id').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: baseUrl + '/customer/save-payment-method',
        method: 'POST',
        data: {
            cart_method: cart_method,
            cart_master_id: cart_master_id
        },
        beforeSend: function(){
            $('#payment-continue-btn').attr("disabled", "disabled");
        },
        success: function(response){
            if(response.status == 'true')
            {
                window.location.href = baseUrl + '/customer/review-order'
                // toastr.clear();
                // toastr.options.closeButton = true;
                // toastr.options.timeOut = 0;
                // toastr.success(response.msg);
            }
            else
            {
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(response.msg);
            }
        }
    })
})