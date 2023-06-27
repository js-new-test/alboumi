$(document).on('click','#add_billing_address',function(){
    $('#addBillingAddressModel').modal('show');		
})

$(document).on('click','#save_billing_address', function(e){
    var full_name = $('#full_name').val()
    var address_1 = $('#address_1').val()
    var address_2 = $('#address_2').val()
    
    var state = $('#state').val()
    var city = $('#city').val()

    var mobile = $('#mobile').val()
    var pincode = $('#pincode').val() 
    var baseUrl = $('#baseUrl').val()
    var error = 0;
    var output = '';        
    if(full_name == '')
    {
        var full_name_label = $('#full_name_label').val();
        $('.error_full_name').html('<p style="color: red;font-size: 15px;">'+full_name_label+'</p>');
        error = 1;
    }
    else
    {
        $('.error_full_name').html('');
    }

    if(address_1 == '')
    {
        var address_1_label = $('#address_1_label').val();
        $('.error_address_1').html('<p style="color: red;font-size: 15px;">'+address_1_label+'</p>');
        error = 1;
    }
    else
    {
        $('.error_address_1').html('');
    }

    if(address_2 == '')
    {
        var address_2_label = $('#address_2_label').val();
        $('.error_address_2').html('<p style="color: red;font-size: 15px;">'+address_2_label+'</p>');
        error = 1;
    }
    else
    {
        $('.error_address_2').html('');
    }

    if(mobile == '')
    {
        var mobile_label = $('#mobile_label').val();
        $('.error_mobile').html('<p style="color: red;font-size: 15px;">'+mobile_label+'</p>');
        error = 1;
    }
    else if(!$.isNumeric(mobile))
    {
        var mobile_not_num_label = $('#mobile_not_num_label').val();
        $('.error_mobile').html('<p style="color: red;font-size: 15px;">'+mobile_not_num_label+'</p>');
        error = 1;
    }
    else if(mobile.length < 8){
        var mobile_num_must_8_degit = $('#mobile_num_must_8_degit').val();
        $('.error_mobile').html('<p style="color: red;font-size: 15px;">'+mobile_num_must_8_degit+'</p>');
        error = 1;
    }
    else
    {
        $('.error_mobile').html('');
    }

    if(state == '')
    {
        var state_label = $('#state_label').val();
        $('.error_state').html('<p style="color: red;font-size: 15px;">'+state_label+'</p>');
        error = 1;
    }
    else
    {
        $('.error_state').html('');
    }

    if(city == '')
    {
        var city_label = $('#city_label').val();
        $('.error_city').html('<p style="color: red;font-size: 15px;">'+city_label+'</p>');
        error = 1;
    }
    else
    {
        $('.error_city').html('');
    }

    if(pincode == '')
    {
        var pincode_label = $('#pincode_label').val();
        $('.error_pincode').html('<p style="color: red;font-size: 15px;">'+pincode_label+'</p>');
        error = 1;
    }
    else
    {
        $('.error_pincode').html('');
    }
   
    if(error == 1)
    {
        e.preventDefault();
    }
    else
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: baseUrl + '/customer/add-billing-address',
            method: 'POST',
            cache: false,
            datatype: 'html',
            data : {
                address_id: $('#address_id').val(),
                address_1: address_1,            
                address_2 : $('#address_2').val(),            
                address_type : $('#address_type').val(),            
                city : $('#city').val(),            
                country : $('#country').val(),
                customer_id : $('#customer_id').val(),
                full_name : full_name,            
                is_default : $('#is_default').is(':checked') ? 1 : 0,            
                mobile : mobile,
                pincode : pincode,            
                state : $('#state').val(),
            },
            success: function(response){                                   
                if(response.status == 'true')
                {
                    $('#addBillingAddressModel').modal('hide');
                    location.reload();
                    // toastr.clear();
                    // toastr.options.closeButton = true;
                    // toastr.options.timeOut = 0;
                    // toastr.success(response.msg);

                    // output += '<div class="row">'
                    //     output += '<div class="col-12 col-sm-8 col-md-7">';
                    //     output += '<p class="s2">'+response.address.fullname+'</p>';
                    //     output += '<span>'+response.address.address_1+', '+response.address.address_2+'</span>';
                    //     output += '<span>'+response.address.phone1+'</span>';
                    //     output += '<a class="edit-address" data-address_id="'+response.address.id+'"><img src="'+baseUrl+'/public/assets/frontend/img/Edit.png">Edit Address</a>';
                    //     output += '</div>';
                    //     output += '<div class="col-12 col-sm-4 col-md-5 right-576">';
                    //     output += '<button class="border-btn D-here" data-address_id="'+response.address.id+'">Deliver Here</button>';
                    //     output += '</div>';
                    // output += '</div>';                                                
                    // edit_address_obj.closest('.dynamic_address_list').html(output)                                            
                }
                else
                {
                    $('#addBillingAddressModel').modal('hide');
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.error(response.msg);
                }
            }
        })
    }
})

$(document).on('click', '#place_order_btn', function(){
    if(!$("#same_as_deli_addr").is(":checked"))
    {    
        if($('#billing_add_not_found').length)
        {
            var msg = $('#billing_add_not_found').val();
            toastr.options.closeButton = true;
            toastr.options.timeOut = 0;
            toastr.error(msg);
            setTimeout(function(){                       
                toastr.clear();
            }, 5000);
            return false;
        }
    }        
    var wait_for_while_msg = $('#wait_for_while_msg').val();
    var same_as_ship_addr = $('#same_as_ship_addr').is('ckecked') ? 1 : 0 ;
    $.ajax({
        url: baseUrl + '/customer/place-order',
        method: 'GET',
        async: false,
        beforeSend: function() {
            toastr.options.closeButton = true;
            toastr.options.timeOut = 0;
            toastr.info(wait_for_while_msg);
            setTimeout(function(){                       
                toastr.clear();
            }, 2000);
            $("#ajax-loader").fadeIn();
        },
        success: function(response_data){              
            if(typeof response_data.arrayNotAvailableProducts != 'undefined' || typeof response_data.arrayLessAvailableProducts != 'undefined')
            {
                var not_avbl = [];
                var less_avbl = [];
                $.each(response_data.arrayNotAvailableProducts, function(index, value){
                    not_avbl.push({"not_avbl_prod":value.product_name})
                })                
                $.each(response_data.arrayLessAvailableProducts, function(index, value){                                                            
                    less_avbl.push({"avbl_prod":value.product_name, "avbl_qty":value.qty})
                })
                var merge_prod = $.merge( $.merge( [], not_avbl ), less_avbl ); 
                
                var output = '';
                output += 'Sorry, following items are not available or having less quantity:';
                output += '<ul>';
                $.each(merge_prod, function(index, value){                                        
                    if(typeof value.not_avbl_prod != 'undefined')
                    {
                        output += '<li>';
                        output += value.not_avbl_prod + ' (Out Of Stock)';
                        output += '</li>';
                    }                                        
                    if(typeof value.avbl_prod != 'undefined' && typeof value.avbl_qty != 'undefined')
                    {
                        output += '<li>';
                        output += value.avbl_prod + ' (Qty : ' + value.avbl_qty + ' Available)';
                        output += '</li>';
                    }                                        
                })                
                output += '</ul>';
                   
                setTimeout(function(){   
                    $("#ajax-loader").fadeOut();                    
                }, 2000);
                setTimeout(function(){                       
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.options.extendedTimeOut = 0;
                    toastr.error(output);
                }, 2000);                                                    
            }
            else
            {
                var response = jQuery.parseJSON(response_data);
                if(response.msg == "Success")
                {
                    if(response.error == 0)
                    {                           
                        localStorage.setItem('total', response.grandTotal);
                        localStorage.setItem('session_id', response.session_id);     
                        localStorage.setItem('merchant_order_id', response.merchant_order_id);
                        localStorage.setItem('merchant_id', response.merchant_id);
                        // window.location.href = 'https://ap-gateway.mastercard.com/checkout/entry/' + response.session.id;
                        window.location.href = baseUrl + '/customer/create-payment'
                    }
                    else
                    {                                              
                        toastr.options.closeButton = true;
                        toastr.options.timeOut = 0;
                        toastr.error(response.msg);
                        setTimeout(function(){                       
                            toastr.clear();
                        }, 5000);
                        setTimeout(function(){   
                            $("#ajax-loader").fadeOut();                    
                        }, 5000);
                    }
                }
                else
                {
                    location.href = response[0].url;
                }  
            }                                
        }
    })    
})

$(document).on('keyup paste', '#cart_master_message', function(){
    var msg = $(this).val();
    var cart_master_id = $('#cart_master_id').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: baseUrl + '/customer/store-order-message',
        method: 'POST',
        data: {message : msg, cart_master_id: cart_master_id},
        success: function(response){

        }
    })
})

var edit_billing_address_obj; 
$(document).on('click','#change_billing_address',function(){
    edit_billing_address_obj = $(this);
    var bill_address_id = $(this).attr('data-bill_address_id');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: baseUrl + '/customer/edit-billing-address',
        method: 'POST',
        data: {
            bill_address_id:bill_address_id,				
        },
        success: function(response){
            if(response.status == 'true')
            {
                $('#editBillAddressModel').on('show.bs.modal', function(e){
                    $('#bill_address_id').val(response.customer_address.id);        
                    $('#e_full_name').val(response.customer_address.fullname);        
                    $('#e_address_1').val(response.customer_address.address_1);        
                    $('#e_address_2').val(response.customer_address.address_2);        
                    $('#e_country').val(response.customer_address.country);
                    $('#e_state').val(response.customer_address.state);        
                    $('#e_city').val(response.customer_address.city);        
                    $('#e_mobile').val(response.customer_address.phone1);
                    $('#e_pincode').val(response.customer_address.pincode); 
                    $("#e_address_type").val(response.customer_address.address_type);
                    var is_default = response.customer_address.is_default == 1 ? true : false;                         						                        
                    $('#e_is_default').prop('checked', is_default);
                    $('#e_is_default').attr("disabled", is_default);             
                    
                });
                $('#editBillAddressModel').modal('show');
            }				
        }
    })		
})

$(document).on('click','#save_edit_bill_customer_address', function(e){
    var cart_master_id = $('#cart_master_id').val();
    var full_name = $('#e_full_name').val()
    var address_1 = $('#e_address_1').val()
    var address_2 = $('#e_address_2').val()
    var mobile = $('#e_mobile').val()

    var country = $('#e_country').val()
    var state = $('#e_state').val()
    var city = $('#e_city').val()

    var pincode = $('#e_pincode').val() 
    var baseUrl = $('#baseUrl').val()
    var error = 0;
    var output = '';        

    if(country == '')
    {
        var country_label = $('#e_country_label').val();
        $('.e_error_country').html('<p style="color: red;font-size: 15px;">'+country_label+'</p>');
        error = 1;
    }
    else
    {
        $('.e_error_country').html('');
    }

    if(full_name == '')
    {
        var full_name_label = $('#e_full_name_label').val();
        $('.e_error_full_name').html('<p style="color: red;font-size: 15px;">'+full_name_label+'</p>');
        error = 1;
    }
    else
    {
        $('.e_error_full_name').html('');
    }

    if(address_1 == '')
    {
        var address_1_label = $('#e_address_1_label').val();
        $('.e_error_address_1').html('<p style="color: red;font-size: 15px;">'+address_1_label+'</p>');
        error = 1;
    }
    else
    {
        $('.e_error_address_1').html('');
    }

    if(address_2 == '')
    {
        var address_2_label = $('#e_address_2_label').val();
        $('.e_error_address_2').html('<p style="color: red;font-size: 15px;">'+address_2_label+'</p>');
        error = 1;
    }
    else
    {
        $('.e_error_address_2').html('');
    }

    if(mobile == '')
    {
        var mobile_label = $('#e_mobile_label').val();
        $('.e_error_mobile').html('<p style="color: red;font-size: 15px;">'+mobile_label+'</p>');
        error = 1;
    }
    else if(!$.isNumeric(mobile))
    {
        var mobile_not_num_label = $('#e_mobile_not_num_label').val();
        $('.e_error_mobile').html('<p style="color: red;font-size: 15px;">'+mobile_not_num_label+'</p>');
        error = 1;
    }        
    else if(mobile.length < 8){
        var mobile_num_must_8_degit = $('#e_mobile_num_must_8_degit').val();
        $('.e_error_mobile').html('<p style="color: red;font-size: 15px;">'+mobile_num_must_8_degit+'</p>');
        error = 1;
    }
    else
    {
        $('.e_error_mobile').html('');
    }

    if(state == '')
    {
        var state_label = $('#e_state_label').val();
        $('.e_error_state').html('<p style="color: red;font-size: 15px;">'+state_label+'</p>');
        error = 1;
    }
    else
    {
        $('.e_error_state').html('');
    }

    if(city == '')
    {
        var city_label = $('#e_city_label').val();
        $('.e_error_city').html('<p style="color: red;font-size: 15px;">'+city_label+'</p>');
        error = 1;
    }
    else
    {
        $('.e_error_city').html('');
    }

    if(pincode == '')
    {
        var pincode_label = $('#e_pincode_label').val();
        $('.e_error_pincode').html('<p style="color: red;font-size: 15px;">'+pincode_label+'</p>');
        error = 1;
    }
    else
    {
        $('.e_error_pincode').html('');
    }
   
    if(error == 1)
    {
        e.preventDefault();
    }
    else
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: baseUrl + '/customer/update-billing-address',
            method: 'POST',
            cache: false,
            datatype: 'html',
            data : {
                address_id: $('#bill_address_id').val(),
                address_1: address_1,            
                address_2 : $('#e_address_2').val(),            
                address_type : $('#e_address_type').val(),            
                city : $('#e_city').val(),            
                country : $('#e_country').val(),
                customer_id : $('#customer_id').val(),
                full_name : full_name,            
                is_default : $('#e_is_default').is(':checked') ? 1 : 0,            
                mobile : mobile,
                pincode : pincode,            
                state : $('#e_state').val(),
                cart_master_id: cart_master_id
            },
            beforeSend: function() {
                $('#editBillAddressModel').modal('hide');
                $("#ajax-loader").fadeIn();
            },
            success: function(response){                   
                if(response.status == 'true')
                {
                    // $('#editBillAddressModel').modal('hide');
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                    setTimeout(function() {                             
                        toastr.clear();            
                    }, 2000);
                    setTimeout(function() {                             
                        $("#ajax-loader").fadeOut();
                    }, 2000);
                    // location.reload();   
                    // output += '<div class="col-12 col-sm-12 col-md-6">';
                    output += '<input type="hidden" name="billing_add_id" id="billing_add_id" value="'+response.address.id+'">';
                    output += '<h6>'+response.BILLINGADDRESS+'</h6>';
                    output += '<p class="s2">'+response.address.fullname+'</p>';
                    output += '<span>'+response.address.address_1+', '+response.address.address_2+'</span>';
                    output += '<span>'+response.address.city+', '+response.address.state+'</span>';
                    output += '<span>'+response.country+'</span>';
                    output += '<span>'+response.address.phone1+'</span>';
                    output += '<a href="javascript:void(0)" style="position: absolute;" data-bill_address_id="'+response.address.id+'" id="change_billing_address">'+response.CHANGE+'</a>'
                    if(response.checkouttype == 1)
                    {
                        if(response.same_as_ship_addr == 1)
                        {
                            output += '<span style="margin-left: 70px;font-weight: bold;color: #212121;" for="same_as_ship_addr">Same as Shipping Address <input style="margin-left: 8px;" type="checkbox" checked name="same_as_ship_addr" id="same_as_ship_addr" class="form-check-input"></span>';                        
                        }
                        else
                        {
                            output += '<span style="margin-left: 70px;font-weight: bold;color: #212121;" for="same_as_ship_addr">Same as Shipping Address <input style="margin-left: 8px;" type="checkbox" name="same_as_ship_addr" id="same_as_ship_addr" class="form-check-input"></span>';                        
                        }
                        
                    }                        
                    // output += '</div>';                                                                  
                    edit_billing_address_obj.closest('#dynamic_bill_address_list').html(output)                                                             
                }
                else
                {
                    $('#editBillAddressModel').modal('hide');                        
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.error(response.msg);
                    setTimeout(function() {                             
                        toastr.clear();            
                    }, 2000);
                }
            }
        })
    }
})

var set_same_as_ship_add;
$(document).on('change', '#same_as_ship_addr', function() {
    var cart_master_id = $('#cart_master_id').val();
    if(this.checked) {
        $('#show_hide_change_button').hide();
        set_same_as_ship_add = 1;
        sameAsShipAddr(set_same_as_ship_add, cart_master_id)
    }
    else
    {
        $('#show_hide_change_button').show();
        set_same_as_ship_add = 0;
        sameAsShipAddr(set_same_as_ship_add, cart_master_id)
    }
});

function sameAsShipAddr(is_checked, cart_master_id)
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: baseUrl + '/customer/set-same-as-ship-address',
        method: 'POST',
        data: {
            is_checked: is_checked,
            cart_master_id: cart_master_id
        },
        beforeSend: function(){
            $("#ajax-loader").fadeIn();
        },
        success: function (response){                        
            if(response.status == 'true')
            {
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
                setTimeout(function() {                             
                    toastr.clear();            
                }, 2000);
                setTimeout(function() {                             
                    $("#ajax-loader").fadeOut();
                }, 2000);
            }
            else
            {
                $("#ajax-loader").fadeOut();
            }
        }
    })
}

if($('#same_as_ship_addr').is(':checked')) {
    $('#show_hide_change_button').hide();    
}
else
{
    $('#show_hide_change_button').show();    
}

if($('#same_as_deli_addr').is(':checked')) {
    $('#show_hide_change_button').hide();
    $("#add_billing_address").hide();    
}
else
{
    $('#show_hide_change_button').show();
    $("#add_billing_address").show();    
}

var set_same_as_deli_add;
$(document).on('change', '#same_as_deli_addr', function() {
    var cart_master_id = $('#cart_master_id').val();
    if(this.checked) {
        $('#show_hide_change_button').hide();
        $("#add_billing_address").hide();
        set_same_as_deli_add = 1;
        sameAsDeliAddr(set_same_as_deli_add, cart_master_id)
    }
    else
    {
        $('#show_hide_change_button').show();
        $("#add_billing_address").show();
        set_same_as_deli_add = 0;
        sameAsDeliAddr(set_same_as_deli_add, cart_master_id)
    }
});

function sameAsDeliAddr(is_checked, cart_master_id)
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: baseUrl + '/customer/set-same-as-deli-address',
        method: 'POST',
        data: {
            is_checked: is_checked,
            cart_master_id: cart_master_id
        },
        beforeSend: function(){
            $("#ajax-loader").fadeIn();
        },
        success: function (response){                        
            if(response.status == 'true')
            {
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
                setTimeout(function() {                             
                    toastr.clear();            
                }, 2000);
                setTimeout(function() {                             
                    $("#ajax-loader").fadeOut();
                }, 2000);
            }
            else
            {
                $("#ajax-loader").fadeOut();
            }
        }
    })
}