$(document).ready(function() {

    $("#adminProfile").validate( {
        rules: {
            firstname: "required",
            lastname: "required",            
            mobile: {
                required: true,
                number: true
            },            
            email: {
                required: true,
                email: true,
            },  
            // profile_photo: {
            //     required: true,
            // }                                                               
        },
        messages: {
            firstname: "Please enter first name",
            lastname: "Please enter last name",            
            mobile: {
                required: "Please enter phone number",
                number: "Phone number must be in digit"
            }, 
            email:{
                required: 'Please enter email address',
                email: 'Please enter a valid email address',
            },
            // profile_photo: {
            //     required: 'Please upload profile image',
            // }                       
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

