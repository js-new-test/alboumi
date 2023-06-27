$('#payNdownloadBtn').click(function(){
    var checkedImagesIds = $("#selectedImagesToBuy").val();
    currentCount = $('#checkedImagesCount').val();

    if($("#selectedImagesToBuy").val() == '')
    {
        checkedImagesIds = $("#checkedImagesIds").val();
    }
    if(checkedImagesIds == '')
    {
        toastr.error("Please select at least one photo");
        $('#pay_modal').modal('hide');
    }
    else if(currentCount == 0)
    {
        toastr.error("Please select at least one photo");
        $('#pay_modal').modal('hide');
    }
    else
        $('#pay_modal').modal('show');
})

$("#continueBtn").click(function(){
    var enqId = $('#enqId').val();
    var custId = document.getElementById('custId').value;
    var payment_type = $('input[name="payment_type"]:checked').val();
    var checkedImagesIds = $("#selectedImagesToBuy").val();
    var selectedImagesPrice = $('#selectedImagePrice').val();
    console.log(checkedImagesIds);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Access-Control-Allow-Origin': '*'
        }
    });

    $(document).ajaxSend(function() {
        $("#ajax-loader").fadeIn(300);　
        $('#pay_modal').hide();
    });

    // if(payment_type == 1)
    // {
        $.ajax({
            method: "post",
            url: baseUrl +'/customer/buyEventPhotos',
            data:{
                'enqId' : enqId,
                'custId' : custId,
                'checkedImagesIds' : checkedImagesIds,
                'selectedImagesPrice' : selectedImagesPrice,
                'payment_type' : payment_type
            },
            success: function (response_data)
            {
                console.log(response_data);
                var response = jQuery.parseJSON(response_data);
                console.log(response);
                if(response.msg == "Success")
                {
                    if(response.error == 0)
                    {
                        setTimeout(function(){
                            $("#ajax-loader").fadeOut(300);
                        },500);

                        localStorage.setItem('total', response.grandTotal);
                        localStorage.setItem('session_id', response.session_id);
                        localStorage.setItem('event_merchant_order_id', response.event_merchant_order_id);
                        localStorage.setItem('merchant_id', response.merchant_id);
                        window.location.href = baseUrl + '/customer/eventorders/createEventOrderPayment'
                    }
                    else
                    {
                        setTimeout(function(){
                            $("#ajax-loader").fadeOut(300);
                        },500);

                        toastr.options.closeButton = true;
                        toastr.options.timeOut = 0;
                        toastr.error(response.explanation);
                        setTimeout(function(){
                            toastr.clear();
                        }, 2000);
                    }
                }
                else
                {
                    location.href = response[0].url;
                }
            }
        });
    // }
    // if(payment_type == 2)
    // {
    //     window.location.href = "../../buyEventPhotos"
    // }
});