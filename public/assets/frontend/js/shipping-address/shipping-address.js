$(document).ready(function(){    
	var cart_master_id = $('#cart_master_id').val();
	if($('.Delivery').is(':checked'))
	{
		$('#store_pickup_section').hide();
	}

	$('input[type="radio"]').click(function() {
       if($(this).attr('class') == 'Delivery') {
            $('#store_pickup_section').hide();           
       }
       else {
            $('#store_pickup_section').show();   
       }
	   if($(this).attr('class') == 'StorePickup') {
            $('#delivery_section').hide();           
       }
       else {
            $('#delivery_section').show();   
       }
    });

    if($('.dynamic_delivery_radio').is(':checked'))
	{
		$('#dynamic_store_pickup_section').hide();
	}

	$('input[type="radio"]').click(function() {
       if($(this).attr('class') == 'dynamic_delivery_radio') {
            $('.save-continue').attr('type', 'submit');
            $('#dynamic_store_pickup_section').hide();           
       }
       else {
            $('#dynamic_store_pickup_section').show();   
       }
	   if($(this).attr('class') == 'dynamic_store_pickup_radio') {
            $('.save-continue').attr('type', 'button');
            $('#dynamic_delivery_section').hide();           
       }
       else {
            $('#dynamic_delivery_section').show();   
       }
    });

	$(document).on('click', ".D-here", function(){
		$('#add_new_address').attr('onsubmit','return false;');
		var a = document.getElementsByClassName('address-ck');        
		for(var i = 0; i < a.length; i++){
			a[i].checked = false
		}
		$(this).closest(".address-box").find(".address-ck").prop('checked', true);
		var address_id = (typeof $(this).attr('data-address_id') !== 'undefined') ? $(this).attr('data-address_id') : '0';
		var s_location_id = (typeof $(this).attr('data-s_location_id') !== 'undefined') ? $(this).attr('data-s_location_id') : '0';
		if($('.Delivery').is(':checked'))
		{
			var checkout_type = '1';
		}

		if($('.StorePickup').is(':checked'))
		{
			var checkout_type = '2';
		}
		
        if($('.dynamic_store_pickup_radio').is(':checked'))
		{
			var checkout_type = '2';
		}

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url: baseUrl + '/customer/save-delivery-type',
			method: 'POST',
			data: {
				address_id:address_id,
				s_location_id: s_location_id,
				checkout_type: checkout_type,
				cart_master_id: cart_master_id
			},
			success: function(response){
				if(response.status == 'true')
				{
					toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
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

    var edit_address_obj; 
	$(document).on('click','.edit-address',function(){
        edit_address_obj = $(this);
		var address_id = $(this).attr('data-address_id');
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url: baseUrl + '/customer/edit-shippind-address',
			method: 'POST',
			data: {
				address_id:address_id,				
			},
			success: function(response){
				if(response.status == 'true')
				{
					$('#editAddressModel').on('show.bs.modal', function(e){
                        $('#address_id').val(response.customer_address.id);        
						$('#full_name').val(response.customer_address.fullname);        
						$('#address_1').val(response.customer_address.address_1);        
						$('#address_2').val(response.customer_address.address_2);        
                        $('#country').val(response.customer_address.country);
						$('#state').val(response.customer_address.state);        
						$('#city').val(response.customer_address.city);        
						$('#mobile').val(response.customer_address.phone1);
						$('#pincode').val(response.customer_address.pincode); 
                        $("#address_type").val(response.customer_address.address_type);
						var is_default = response.customer_address.is_default == 1 ? true : false;                         						                        
                        $('#is_default').prop('checked', is_default);
                        $('#is_default').attr("disabled", is_default);             
						
					});
					$('#editAddressModel').modal('show');
				}				
			}
		})		
	})
    
    $(document).on('click','#save_edit_customer_address', function(e){
        var full_name = $('#full_name').val()
        var address_1 = $('#address_1').val()
        var address_2 = $('#address_2').val()
        var mobile = $('#mobile').val()

        var country = $('#country').val()
        var state = $('#state').val()
        var city = $('#city').val()

        var pincode = $('#pincode').val() 
        var baseUrl = $('#baseUrl').val()
        var error = 0;
        var output = '';        

        if(country == '')
        {
            var country_label = $('#country_label').val();
            $('.error_country').html('<p style="color: red;font-size: 15px;">'+country_label+'</p>');
            error = 1;
        }
        else
        {
            $('.error_country').html('');
        }

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
                url: baseUrl + '/customer/update-shippind-address',
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
                        $('#editAddressModel').modal('hide');
                        toastr.options.closeButton = true;
                        toastr.options.timeOut = 0;
                        toastr.success(response.msg);
                        setTimeout(function() {                             
                            toastr.clear();            
                        }, 2000);

                        output += '<div class="row">'
                            output += '<div class="col-12 col-sm-8 col-md-7">';
                            output += '<p class="s2">'+response.address.fullname+'</p>';
                            output += '<span>'+response.address.address_1+', '+response.address.address_2+'</span>';
                            output += '<span>'+response.address.city+', '+response.address.state+'</span>';
                            output += '<span>'+response.country+'</span>';
                            output += '<span>'+response.address.phone1+'</span>';
                            output += '<a class="edit-address" data-address_id="'+response.address.id+'"><img src="'+baseUrl+'/public/assets/frontend/img/Edit.png">Edit Address</a>';
                            output += '</div>';
                            output += '<div class="col-12 col-sm-4 col-md-5 right-576">';
                            output += '<button class="border-btn D-here" data-address_id="'+response.address.id+'">Deliver Here</button>';
                            output += '</div>';
                        output += '</div>';                                                
                        edit_address_obj.closest('.dynamic_address_list').html(output)                                            
                    }
                    else
                    {
                        $('#editAddressModel').modal('hide');                        
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

    $('#add_new_shi_address_btn').on('click', function(){        
        // var cart_id = $(this).attr('data-cart-id');
        $('#addNewAddressModel').on('show.bs.modal', function(e){
            // $('#cart_master_id').val(cart_id);        
        });
        $('#addNewAddressModel').modal('show');
    })

    $(document).on('click','#save_new_customer_address', function(e){
        var full_name = $('#n_full_name').val()
        var address_1 = $('#n_address_1').val()
        var address_2 = $('#n_address_2').val()
        var mobile = $('#n_mobile').val()

        var state = $('#n_state').val()
        var city = $('#n_city').val()

        var pincode = $('#n_pincode').val() 
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
                url: baseUrl + '/customer/add-new-shippind-address',
                method: 'POST',
                // cache: false,
                // datatype: 'html',
                data : {
                    // address_id: $('#address_id').val(),
                    address_1: address_1,            
                    address_2 : address_2,            
                    address_type : $('#n_address_type').val(),            
                    city : city,            
                    country : $('#n_country').val(),
                    customer_id : $('#customer_id').val(),
                    full_name : full_name,            
                    is_default : $('#n_is_default').is(':checked') ? 1 : 0,            
                    mobile : mobile,
                    pincode : pincode,            
                    state : state,
                },
                success: function(response){                                       
                    if(response.status == 'true')
                    {
                        location.reload();
                    }                    
                }
            })
        }
    })

    $(document).on('click','.address-continue', function(){
        var delivery_msg = $('#Delivery_msg').attr('data-delivary-error-msg');
        var store_msg = $('#StorePickup_msg').attr('data-store-error-msg');   
        var checkedNum = $('input[name=address-ck]:checked').length;        
        if (!checkedNum) {
            if($('.Delivery').prop('checked') == true)
            {
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(delivery_msg);
            }

            if($('.StorePickup').prop('checked') == true)
            {                
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(store_msg);
            }
            
            setTimeout(function() {             
                toastr.clear();            
            }, 3000);
        }
        else
        {
            window.location.href = baseUrl + '/customer/payment-method'
        }                 
    })

    $(document).on('click','.save-continue', function(){        
        var store_msg = $('#StorePickup_msg').attr('data-store-error-msg');   
        var checkedNum = $('input[name=address-ck]:checked').length;        
        if (!checkedNum) {            
            if($('.dynamic_store_pickup_radio').prop('checked') == true)
            {                
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(store_msg);
            }
            
            setTimeout(function() {             
                toastr.clear();            
            }, 3000);
        }
        else
        {
            window.location.href = baseUrl + '/customer/payment-method'
        }                 
    })

    var queries = [];
    $.each(document.location.search.substr(1).split('&'),function(c,q){
      var i = q.split('=');
      queries[i[0].toString()] = i[1].toString();
    });
        
    if(queries != undefined)
    {
        if(queries.checkouttype == 2)
        {
            $('.Delivery').prop('checked', false);
            $('#delivery_section').hide(); 
            $('.StorePickup').prop('checked', true);
            $('#store_pickup_section').show();

            $('.dynamic_delivery_radio').prop('checked', false);
            $('#dynamic_delivery_section').hide(); 
            $('.dynamic_store_pickup_radio').prop('checked', true);
            $('#dynamic_store_pickup_section').show();
            $('.save-continue').attr('type', 'button');
        }
    }    
})

$("#add_new_address").validate({
    ignore: [], // ignore NOTHING
    rules: {    
        full_name : {
            required : true
        },               
        address_1 : {
            required : true
        },
        address_2 : {
            required : true
        },
        mobile : {
            required : true,
            minlength: 8,
            number: true
        },
        address_type : {
            required : true
        },
        country : {
            required : true
        },
        state : {
            required : true
        },
        city : {
            required : true
        },
        pincode : {
            required : true
        },               
    },
    messages: { 
        full_name : {
            required : firstNameReq
        },               
        address_1 : {
            required : address1Req
        },
        address_2 : {
            required : address2Req
        },
        mobile : {
            required : mobileReq,
            minlength: mobileMustBe,
            number: mobileNum
        },
        address_type : {
            required : addressTypeReq
        },
        country : {
            required : countryReq
        },
        state : {
            required : stateReq
        },
        city : {
            required : cityReq
        },
        pincode : {
            required : pinCodeReq
        },                    
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter( element );
    }    
});