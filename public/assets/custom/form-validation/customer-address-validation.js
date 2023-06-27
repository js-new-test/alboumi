$(document).ready(function() {

    $("#customerAddress").validate( {
        rules: {
            full_name: "required",
            address_1: "required",
            // address_2: "required",    
            country: "required",
            // states: "required",     
            // cities:"required",
            address_type:"required",      
            pincode: {
                required: true,
                // number: true
            },
            phone_1: {
                required: true,
                number: true
            },                                                          
        },
        messages: {
            full_name: "Please enter full name Ex. Firstname and Lastname",
            address_1: "Please fill address line 1", 
            // address_2: "Please enter flat no.",            
            country: "Please select country",
            // states: "Please enter block",     
            // cities: "Please enter road",   
            address_type: "Please select address type",       
            pincode: {
                required: "Please enter building",
                // number: "Pincode must be in digit"
            },
            phone_1: {
                required: "Please enter phone number",
                number: "Phone number must be in digit"
            },             
        },
        errorPlacement: function ( error, element ) {
            // Add the `invalid-feedback` class to the error element
            if ( element.prop( "type" ) === "checkbox" ) {
                error.insertAfter( element.next( "label" ) );
            } else {
                error.insertAfter( element );
            }
        },
    } );

});

