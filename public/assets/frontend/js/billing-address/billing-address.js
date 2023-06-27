$('#country').on('change', function(){
    var country = $(this).val();        
    var baseUrl = $('#baseUrl').val();      
    $('#edit_states_dropdown').empty();
    $('#edit_cities_dropdown').empty();    
    $.ajax({
        url: baseUrl + '/customer/billing-address/states/' + country,
        method: "GET",
        success: function(response) {
            if(response)
            {                    
                var output = '';                    
                // output += '<optgroup label="Select States">';                                                                    
                $.each(response, function(i, value){                        
                    output += '<option value="'+value.id+'">'+value.name+'</option>';                                
                })                    
                // output += '</optgroup>';
                // $('#states').html(output);   
                $('#states_dropdown').html('<select name="states" id="states" class="select">'+output+'</select>')             
                $('#cities_dropdown').html('<select name="cities" id="cities" class="select"></select>')
            }
            
        }
    })
})

$(document).on('change', '#states', function(){    
    var cities = $(this).val();      
    var baseUrl = $('#baseUrl').val();       
    $.ajax({
        url: baseUrl + '/customer/billing-address/cities/' + cities,
        method: "GET",
        success: function(response) {            
            if(response)
            {                    
                var output = '';                    
                // output += '<optgroup label="Select Cities">';
                $.each(response, function(i, value){                        
                    output += '<option value="'+value.id+'">'+value.name+'</option>';                                
                })                    
                // output += '</optgroup>';
                // $('#cities').html(output);
                $('#cities_dropdown').html('<select name="cities" id="cities" class="select">'+output+'</select>')             
            }
            
        }
    })
})

$(document).on('change', '.change_is_default', function() {
    var address_id = $(this).attr('data-id');
    $('#changeDefaultAddressModel').on('show.bs.modal', function(e){
        $('#address_id').val(address_id);        
    });
    $('#changeDefaultAddressModel').modal('show');
});

$(document).on('click', '#change_defalut_address', function(){
    var address_id = $('#address_id').val();
    var is_default = 1;
    var baseUrl = $('#baseUrl').val();
    $.ajax({
        url: baseUrl + '/customer/change-default-billing-address',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            address_id: address_id,
            is_default: is_default
        },
        success: function(response){
            if(response == 'true')
            {
                location.reload();
            }
        }
    })
})


$(document).on('click', '.delete-address', function() {
    var address_id = $(this).attr('data-id');
    $('#deleteAddressModel').on('show.bs.modal', function(e){
        $('#address_id').val(address_id);        
    });
    $('#deleteAddressModel').modal('show');
});

$(document).on('click', '#delete_address', function(){
    var address_id = $('#address_id').val();    
    var baseUrl = $('#baseUrl').val();
    $.ajax({
        url: baseUrl + '/customer/delete-billing-address',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            address_id: address_id,        
        },
        success: function(response){
            if(response == 'true')
            {
                location.reload();
            }
        }
    })
})



$(document).on('click', '.edit-address', function() {        
    var address_id = $(this).attr('data-id');    
    var baseUrl = $('#baseUrl').val();
    $.ajax({
        url: baseUrl + '/customer/get-ajax-billing-address',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            address_id: address_id,        
        },
        success: function(response){  
            $('#states_dropdown').empty();
            $('#cities_dropdown').empty();
            $('#edit_address_id').val(response.customer_address.id);
            $('#full_name').val(response.customer_address.fullname);   
            $('#address_1').val(response.customer_address.address_1);   
            $('#address_2').val(response.customer_address.address_2);
            $('#mobile').val(response.customer_address.phone1);
            $('#pincode').val(response.customer_address.pincode);            
            $('#country').val(response.country.id);            
            $('#state').val(response.customer_address.state);
            $('#city').val(response.customer_address.city);                                           
            $('#address_type').val(response.customer_address.address_type).change();
            $('#is_default').val(response.customer_address.is_default);
        }
    })
});

$("#frontMyBillingAddressForm").validate({
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

