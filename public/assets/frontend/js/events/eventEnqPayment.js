$("#continueBtn").click(function(){
    var enqId = $('#eventEnquiryId').val();
    var payment_type = $('input[name="pm"]:checked').val();
    // console.log(enqId);

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    $(document).ajaxSend(function() {
        $("#ajax-loader").fadeIn(10);　
        // $('#pay_modal').hide();
    });

    $.ajax({
        method: "post",
        url: baseUrl +'/eventEnq/paymentOfEventEnq',
        data:{
            'enqId' : enqId,
            'payment_type' : payment_type
        },
        success: function (response_data)
        {
            var response = jQuery.parseJSON(response_data);
            console.log(response);
            if(response.msg == "Success")
            {
                if(response.error == 0)
                {
                    setTimeout(function(){
                        $("#ajax-loader").fadeOut(300);
                    },500);

                    window.location.href = baseUrl + '/eventEnq/createEventEnqOrderPayment'
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
});