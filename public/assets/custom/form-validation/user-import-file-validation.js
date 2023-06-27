$(document).ready(function() {
    
    $("#importUser").validate( {
        rules: {
            import_user_file: {
                required: true,               
            }     
        },
        messages: {
            import_user_file: {
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
    });

});

