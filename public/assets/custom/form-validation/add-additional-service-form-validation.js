$(document).ready(function() {

    $("#addAdditionalService").validate( {
        rules: { 
            addit_service_name : {
                required: true,
            },           
            service_image: {
                required: true,
                accept: "jpg,jpeg,png"
            },
            service_description: {
                required: true,
            },
            price: {
                required: true,
            },
            'samples[]': {
                required: true,
                accept: "jpg,jpeg,png"
            }        
        },
        messages: {  
            addit_service_name : {
                required: 'Name is required',
            },          
            service_image: {
                required: 'Please upload image',
                accept: "Please upload file in these format only (png, jpg, jpeg)."
            },
            service_description: {
                required: 'Description is required',
            },
            price: {
                required: 'Price is required'
            },
            'samples[]': {
                required: 'Please upload samples',
                accept: "Please upload file in these format only (png, jpg, jpeg)."
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

    $('#addAdditionalService').on('submit', function(event) {                       
        
        $('.dynamic_value_field_validation').each(function() { 
            var inc = $(this).attr('data-inc-val');              
            if(inc)
            {
                i = inc;
            }
            else
            {
                i = 0;
            }             
            if($('#requirements'+i+'').val() == '')
            {
                $('#requirements_error'+i+'').html('<p style="color: red;">Value field is required</p>');
                event.preventDefault();
            }
            else
            {
                $('#requirements_error'+i).html('');
            }
        });

        $('.dynamic_requirement_field_validation').each(function() { 
            var inc = $(this).attr('data-inc-val');              
            if(inc)
            {
                i = inc;
            }
            else
            {
                i = 0;
            }             
            if($('#requirement_labels'+i+'').val() == '')
            {
                $('#requirements_labels_error'+i+'').html('<p style="color: red;">Requirement field is required</p>');
                event.preventDefault();
            }
            else
            {
                $('#requirements_labels_error'+i).html('');
            }
        });
        
        $('.loaded_dynamic_field_validation').each(function() {  
            var inc = $(this).attr('data-inc-val');              
            if(inc)
            {
                i = inc;
            }
            else
            {
                i = 0;
            }                        
            if($('#loaded_requirements'+i+'').val() == '')
            {
                $('#loaded_requirements_error'+i+'').html('<p style="color: red;">Value field is required</p>');
                event.preventDefault();
            }
            else
            {
                $('#loaded_requirements_error'+i).html('');
            }
        });

        $('.loaded_requirement_field_validation').each(function() {  
            var inc = $(this).attr('data-inc-val');              
            if(inc)
            {
                i = inc;
            }
            else
            {
                i = 0;
            }                        
            if($('#loaded_requirement_labels'+i+'').val() == '')
            {
                $('#loaded_requirements_labels_error'+i+'').html('<p style="color: red;">Requirements field is required</p>');
                event.preventDefault();
            }
            else
            {
                $('#loaded_requirements_labels_error'+i).html('');
            }
        });        
    })
    
    $('#addAdditionalService').validate();

});

