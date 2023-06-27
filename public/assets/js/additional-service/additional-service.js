var _URL = window.URL || window.webkitURL;    

$('#service_image').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#width").text(imgwidth);
        $("#height").text(imgheight);   
        $("#loaded_image_width").val(imgwidth);
        $("#loaded_image_height").val(imgheight);                                    
    }    
}); 

$('#service_act_deact').on('click', function(){
    if($(this).is(":checked"))
    {
        $('#act_deact_service_chk').val(1);
    }
    else
    {
        $('#act_deact_service_chk').val(0);
    }
})

var i = 0;
$('#add_requirements').click(function(){
    i++;
    var output = '';        
    output += '<div class="row remove_current_div">';
        output += '<div class="col-md-4">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<input type="text" name="requirement_labels[]" id="requirement_labels'+i+'" data-inc-val="'+i+'" placeholder="Requirements" class="form-control dynamic_requirement_field_validation">';
                output += '<span id="requirements_labels_error'+i+'"></span>';
                output += '</div>';
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-4">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<input type="text" name="requirements[]" id="requirements'+i+'" data-inc-val="'+i+'" placeholder="value" class="form-control dynamic_value_field_validation">';
                output += '<span id="requirements_error'+i+'"></span>';
                output += '</div>';
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-4">';
            output += '<div class="form-group">';
                output += '<div style="margin-top: 0px;">';                        
                    output += '<button class="mb-2 mr-2 btn-icon btn-icon-only btn-square btn btn-primary delete_requirement_no_confirm"><i class="fa fa-fw" aria-hidden="true" title="Copy to use close"></i></button>'
                output += '</div>';
            output += '</div>';            
        output += '</div>';            
    output += '</div>';        
    $('#dynamic_requirements_textbox').append(output);       
})   

var k = 0;
$('#edit_requirements').click(function(){         
    var output = '';        
    output += '<div class="row remove_current_div">';
        output += '<div class="col-md-4">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<input type="text" name="requirement_labels[]" id="loaded_requirement_labels'+k+'" data-inc-val="'+k+'" placeholder="Requirements" class="form-control loaded_requirement_field_validation">';
                output += '<span id="loaded_requirements_labels_error'+k+'"></span>';
                output += '</div>';
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-4">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<input type="text" name="requirements[]" id="loaded_requirements'+k+'" data-inc-val="'+k+'" placeholder="Value" class="form-control loaded_dynamic_field_validation">';                
                output += '<span id="loaded_requirements_error'+k+'"></span>';
                output += '</div>';                
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-4">';
            output += '<div class="form-group">';
                output += '<div style="margin-top: 0px;">';                        
                    output += '<button class="mb-2 mr-2 btn-icon btn-icon-only btn-square btn btn-primary delete_requirement_no_confirm"><i class="fa fa-fw" aria-hidden="true" title="Copy to use close"></i></button>'
                output += '</div>';
            output += '</div>';            
        output += '</div>';            
    output += '</div>';
    k++;        
    $('#dynamic_requirements_textbox').append(output);       
})

$(document).on('click', '.delete_requirement_no_confirm',function(){        
    $(this).closest(".remove_current_div").remove();
});


var element_obj;
$(document).on('click', '.delete_requirement',function(){
    element_obj = $(this);               
    var message = "Are you sure?";                 
    var serv_req_id = $(this).attr('data-id');
    var service_id = $('#service_id').val(); 
    $('#serviceRequirementDeleteModel').modal('show')
    .unbind().on('click','#deleteService_R', function(){                                   
        $.ajax({
            url:  window.location.href + "/../../requirement/delete",
            method: "POST",
            data: {
                "_token": $('#token').val(),
                serv_req_id: serv_req_id,
                service_id: service_id
            },
            success: function(response){                                
                if(response.status == 'true')
                {
                    $('#serviceRequirementDeleteModel').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);  
                }
                else
                {
                    $('#serviceRequirementDeleteModel').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);  
                }
            }
        })
        $(element_obj).closest(".remove_current_div").remove();          
    })                            
});

var sample_element_obj;
$(document).on('click', '#delete_sample_image', function(){
    sample_element_obj = $(this);
    var service_sample_id = $(this).attr('data-sample-id')    
    $('#serviceSamplesDeleteModel').on('show.bs.modal', function(e){
        $('#service_sample_id').val(service_sample_id);        
    });
    $('#serviceSamplesDeleteModel').modal('show');
})

$(document).on('click', '#closeServiceRModel', function(){
    $('#serviceRequirementDeleteModel').modal('hide')
})

$(document).on('click', '#deleteServiceSample', function(){
    var service_sample_id = $('#service_sample_id').val();        
    $.ajax({
        url: window.location.href + '/../../samples/delete',
        method: "POST",
        data:{
            "_token": $('#token').val(),
            service_sample_id: service_sample_id
        },
        success: function(response)
        {                    
            if(response.status == 'true')
            {
                $('#serviceSamplesDeleteModel').modal('hide') 
                sample_element_obj.closest(".remove_samples_section").remove();               
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);                
            }
            else
            {
                $('#serviceSamplesDeleteModel').modal('hide')                
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            setTimeout(function(){ 
                toastr.clear();
            }, 5000);
        }
    });
              
})