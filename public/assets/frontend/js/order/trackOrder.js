$(document).ready(function() {
    $('#prodDetailTable').hide();
})

$("#trackOrderForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "orderId": {
            required: true,
        },
        "email": {
            required: true,
        }
    },
    messages: {
        "orderId": {
            required: orderIdError
        },
        "email": {
            required: emailError
        }
    },
    errorPlacement: function (error, element) 
    {
        error.insertAfter(element);
        $('.input').css('margin-bottom','8px','!important')
    },
    submitHandler: function(form) 
    {
        var formData = $('#trackOrderForm').serialize();

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $(document).ajaxSend(function() {
            $("#ajax-loader").fadeIn(300);　
        });

        $.ajax({
            type: "post",
            url: baseUrl + '/trackOrder',
            data:{
                'orderId' : $('#orderId').val(),
                'email' : $('#email').val()
            },
            success: function (response) 
            {
                if(response.status == true)
                {
                    var dynamic = "";
                    for (var x = 0; x < response.productDetails.variants.length; x++)
                    {
                        dynamic += '<br>' + response.productDetails.variants[x].title + ' : ' + response.productDetails.variants[x].value ;
                    };
                    // $('#trackingInfoDiv').html('');
                    $('#trackingInfoLabel').html(trackingInfoLabel);
                    $('#orderIdLabel').html(orderIdLabel);
                    $('#orderID').html('#' + $('#orderId').val());
                    $("#prodDetailTable").find("tbody").empty();
                    $('#prodDetailTable').show();
                    $('#prodDetailTable').append('<tr><td>'+ response.productDetails.prodName + dynamic
                        + '</td><td>'+ response.productDetails.quantity +'</td><td>'+ response.productDetails.status +'</td></tr>')
                }
                else
                {
                    toastr.error(response.msg);
                }
            }
      });
    }
});