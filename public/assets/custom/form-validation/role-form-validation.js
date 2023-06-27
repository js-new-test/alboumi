$(document).ready(function() {

    $("#roleForm").validate( {
        rules: {
            role_title: "required",
            role_type: "required",            
        },
        messages: {
            role_title: "Role Title is required (Max length is 255 character)",
            role_type: "Role Type is required",            
        },
        errorPlacement: function ( error, element ) {
            // Add the `invalid-feedback` class to the error element
            if ( element.prop( "type" ) === "checkbox" ) {
                error.insertAfter( element.next( "label" ) );
            } else {
                error.insertAfter( element );
            }
        }
    } );

});

