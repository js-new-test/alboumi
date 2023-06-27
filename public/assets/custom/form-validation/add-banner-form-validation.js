$(document).ready(function() {

    $("#addBanner").validate( {
        rules: {            
            banner_image: {
                required: true,
            }                                                               
        },
        messages: {            
            banner_image: {
                required: 'Please upload banner image',
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

