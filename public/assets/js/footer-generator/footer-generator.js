var i = 0;
$('#add_footer_gen_link_section').click(function(){
    i++;
    var output = '';        
    output += '<div class="row remove_current_div">';
        output += '<div class="col-md-3">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<label for="name" class="font-weight-bold">Name<span style="color:red">*</span></label>';
                output += '<input type="text" name="add_common_arr[]" id="footer_name_required'+i+'" data-inc-val="'+i+'" class="form-control footer_name_sec">';
                output += '<span id="footer_name_error'+i+'"></span>';
                output += '</div>';
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-3">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<label for="link" class="font-weight-bold">Link<span style="color:red">*</span></label>';
                output += '<input type="text" name="add_common_arr[]" id="footer_link_required'+i+'" data-inc-val="'+i+'" class="form-control footer_link_sec">';
                output += '<span id="footer_link_error'+i+'"></span>';
                output += '</div>';
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-3">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<label for="sort_order_section" class="font-weight-bold">Sort Order<span style="color:red">*</span></label>';
                output += '<input type="number" name="add_common_arr[]" id="footer_st_required'+i+'" data-inc-val="'+i+'" class="form-control footer_st_sec">';
                output += '<span id="footer_st_error'+i+'"></span>';
                output += '</div>';
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-3">';
            output += '<div class="form-group">';
                output += '<div style="margin-top: 38px;">';                        
                    output += '<button class="mb-2 mr-2 btn-icon btn-icon-only btn-square btn btn-primary delete_footer_links_section_no_cnf"><i class="fa fa-fw" aria-hidden="true" title="Copy to use close"></i></button>'
                output += '</div>';
            output += '</div>';            
        output += '</div>';            
    output += '</div>';        
    $('#dynamic_footer_gen_link_textbox').append(output);       
}) 

var k = 0;
$('#edit_footer_gen_link_section').click(function(){    
    var output = '';        
    output += '<div class="row remove_current_div">';
        output += '<div class="col-md-3">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<label for="name" class="font-weight-bold">Name<span style="color:red">*</span></label>';
                output += '<input type="text" name="add_common_arr[]" id="loaded_footer_name_required'+k+'" data-inc-val="'+k+'" class="form-control loaded_footer_name_sec">';
                output += '<span id="loaded_footer_name_error'+k+'"></span>';
                output += '</div>';
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-3">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<label for="link" class="font-weight-bold">Link<span style="color:red">*</span></label>';
                output += '<input type="text" name="add_common_arr[]" id="loaded_footer_link_required'+k+'" data-inc-val="'+k+'" class="form-control loaded_footer_link_sec">';
                output += '<span id="loaded_footer_link_error'+k+'"></span>';
                output += '</div>';
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-3">';
            output += '<div class="form-group">';
                output += '<div>';
                output += '<label for="sort_order_section" class="font-weight-bold">Sort Order<span style="color:red">*</span></label>';
                output += '<input type="number" name="add_common_arr[]" id="loaded_footer_st_required'+k+'" data-inc-val="'+k+'" class="form-control loaded_footer_st_sec">';
                output += '<span id="loaded_footer_st_error'+k+'"></span>';
                output += '</div>';
            output += '</div>';
        output += '</div>';
        output += '<div class="col-md-3">';
            output += '<div class="form-group">';
                output += '<div style="margin-top: 38px;">';                        
                    output += '<button class="mb-2 mr-2 btn-icon btn-icon-only btn-square btn btn-primary delete_footer_links_section_no_cnf"><i class="fa fa-fw" aria-hidden="true" title="Copy to use close"></i></button>'
                output += '</div>';
            output += '</div>';            
        output += '</div>';            
    output += '</div>';     
    k++;   
    $('#dynamic_footer_gen_link_textbox').append(output);       
})

$(document).on('click', '.delete_footer_links_section_no_cnf',function(){        
    $(this).closest(".remove_current_div").remove();
});

$("#footerGeneratorForm").validate( {
    ignore: [], // ignore NOTHING
    rules: {
        footer_group: "required",
        footer_sort_order: "required",              
    },
    messages: {
        footer_group: "Group is required",
        footer_sort_order: "Sort Order is required",                            
    },
    errorPlacement: function ( error, element ) {

        error.insertAfter( element );
    },
});

$('#footerGeneratorForm').on('submit', function(event) {                   
        
    $('.footer_name_sec').each(function() { 
        var inc = $(this).attr('data-inc-val');              
        if(inc)
        {
            i = inc;
        }
        else
        {
            i = 0;
        }               
        if($('#footer_name_required'+i+'').val() == '')
        {
            $('#footer_name_error'+i+'').html('<p style="color: red;">Name is required</p>');
            event.preventDefault();
        }
        else
        {
            $('#footer_name_error'+i).html('');
        }        
    });      
    
    $('.footer_link_sec').each(function() {  
        var inc = $(this).attr('data-inc-val');              
        if(inc)
        {
            i = inc;
        }
        else
        {
            i = 0;
        }
        if($('#footer_link_required'+i+'').val() == '')
        {
            $('#footer_link_error'+i+'').html('<p style="color: red;">Link is required</p>');
            event.preventDefault();
        }
        else
        {
            $('#footer_link_error'+i).html('');
        }        
    }); 

    $('.footer_st_sec').each(function() {
        var inc = $(this).attr('data-inc-val');              
        if(inc)
        {
            i = inc;
        }
        else
        {
            i = 0;
        }                        
        if($('#footer_st_required'+i+'').val() == '')
        {
            $('#footer_st_error'+i+'').html('<p style="color: red;">Sort Order is required</p>');
            event.preventDefault();
        }
        else
        {
            $('#footer_st_error'+i).html('');
        }
    });    
    
    $('.loaded_footer_name_sec').each(function() {   
        var inc = $(this).attr('data-inc-val');              
        if(inc)
        {
            i = inc;
        }
        else
        {
            i = 0;
        }             
        if($('#loaded_footer_name_required'+i+'').val() == '')
        {
            $('#loaded_footer_name_error'+i+'').html('<p style="color: red;">Name is required</p>');
            event.preventDefault();
        }
        else
        {
            $('#loaded_footer_name_error'+i).html('');
        }        
    });      
    
    $('.loaded_footer_link_sec').each(function() {      
        var i = $(this).attr('data-inc-val');          
        if($('#loaded_footer_link_required'+i+'').val() == '')
        {
            $('#loaded_footer_link_error'+i+'').html('<p style="color: red;">Link is required</p>');
            event.preventDefault();
        }
        else
        {
            $('#loaded_footer_link_error'+i).html('');
        }        
    }); 

    $('.loaded_footer_st_sec').each(function() {   
        var i = $(this).attr('data-inc-val');                     
        if($('#loaded_footer_st_required'+i+'').val() == '')
        {
            $('#loaded_footer_st_error'+i+'').html('<p style="color: red;">Sort Order is required</p>');
            event.preventDefault();
        }
        else
        {
            $('#loaded_footer_st_error'+i).html('');
        }
    });    
})

$('#footerGeneratorForm').validate();
// $('.footer_name_error').rules('add', {
//     required: true,    
//     messages: {
//         required:  "Name is required",        
//     }
// });
// $('.footer_link_error').rules('add', {
//     required: true,    
//     messages: {
//         required:  "Link is required",        
//     }
// });
// $('.footer_st_error').rules('add', {
//     required: true,    
//     messages: {
//         required:  "Sort code is required",        
//     }
// });

var table = $('#footer_generator_list').DataTable({
    processing: true,
    serverSide: true,
    // "scrollX": true,
    "initComplete": function (settings, json) {  
        $("#footer_generator_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
    },
    ajax: {
        "url": window.location.href,
        "type": "GET"
    },
    columns: [
        {data: 'footer_group', name: 'footer_group'},
        {data: 'sort_order', name: 'sort_order'},
        { data: 'zone', name: 'zone',
            render: function (data,type,row) {                    
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.fg_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                }
                else
                {
                    return "-----"
                }
                
            }
        },                        
        {data: 'id', name: 'action', // orderable: true, // searchable: true
            render: function(data, type, row)
            {                    
                var output = "";
                output += '<a href="'+window.location.href+'/edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                output += '<a href="javascript:void(0);" class="footer_gen_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                return output;
            },                
        },            
    ],
    fnDrawCallback: function() {
    },
});

$(document).on('click','#filter_footer_generator', function(){
    var token = $('input[name="_token"]').val();
    var filter_footer_gen_lang = $('#filter_footer_gen_lang').val();
    $('#footer_generator_list').dataTable().fnDestroy(); 
    table = $('#footer_generator_list').DataTable({
        processing: true,
        serverSide: true,
        // "scrollX": true,
        "initComplete": function (settings, json) {  
            $("#footer_generator_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        ajax: {
            url: window.location.href + '/filter-footer-generator',
            method: "POST",
            data:{
                _token : token,
                filter_footer_gen_lang:filter_footer_gen_lang
            },
        },
        columns: [
            {data: 'footer_group', name: 'footer_group'},
            {data: 'sort_order', name: 'sort_order'},
            { data: 'zone', name: 'zone',
                render: function (data,type,row) {                    
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.fg_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }
                    
                }
            },                        
            {data: 'id', name: 'action', // orderable: true, // searchable: true
                render: function(data, type, row)
                {                    
                    var output = "";
                    output += '<a href="'+window.location.href+'/edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                    output += '<a href="javascript:void(0);" class="footer_gen_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },                
            },            
        ],
        fnDrawCallback: function() {
        },
    });               
})

var element_obj;
$(document).on('click', '.delete_footer_links_section',function(){
    element_obj = $(this);                                   
    var data_f_link_s_id = $(this).attr('data-f-link-s-id');
    var token = $('#_token').val();           
    $('#footerGeneratorDeleteModel').modal('show')
    .unbind().on('click','#deleteFooterLinksSec', function(){                                   
        $.ajax({
            url: window.location.href + '/../../delete',
            method: "POST",
            data: {
                "_token": token,
                data_f_link_s_id: data_f_link_s_id,                
            },
            success: function(response){                                
                if(response.status == 'true')
                {
                    $('#footerGeneratorDeleteModel').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);  
                }
                else
                {
                    $('#footerGeneratorDeleteModel').modal('hide')
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

$('#closeFooterGenModel').click(function(){
    $('#footerGeneratorDeleteModel').modal('hide')
})

$(document).on('click','.footer_gen_delete', function(){
    var data_row = table.row($(this).closest('tr')).data();  
    var footer_gen_id = data_row.id;    
    var message = "Are you sure?";         
    $('#footerGenDeleteModel').on('show.bs.modal', function(e){
        $('#footer_gen_id').val(footer_gen_id);            
        $('#message').text(message);
    });
    $('#footerGenDeleteModel').modal('show'); 
})

$(document).on('click','#deleteFooterGen', function(){
    var footer_gen_id = $('#footer_gen_id').val(); 
    $.ajax({
        url: window.location.href + '/parent/delete',
        method: "POST",    
        data: {
            "_token": $('#token').val(),
            footer_gen_id: footer_gen_id,
        },            
        success: function(response)
        {                
            if(response.status == 'true')
            {
                $('#footerGenDeleteModel').modal('hide')
                table.ajax.reload();                    
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#footerGenDeleteModel').modal('hide')
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(response.msg);
            } 
            setTimeout(function(){ 
                toastr.clear();
            }, 5000);                                 
        }
    });  
})