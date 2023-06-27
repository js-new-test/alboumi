$(document).ready(function() {
    
    $("#importCustomer").validate( {
        rules: {
            import_customer_file: {
                required: true,               
            }     
        },
        messages: {
            import_customer_file: {
                required: "Please provide excel file for data import operation",            
            }
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

