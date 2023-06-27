$(document).ready(function() {

    $("#forgotPassForm").validate( {
        rules: {
            forgot_email: {
                required: true,
                email: true,
            },             
        },
        messages: {
            forgot_email:{
                required: 'Please enter email address',
                email: 'Please enter a valid email address',
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

