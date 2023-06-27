
/** add  template form validation */
$("#addEmailTemplateForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "code": {
            required: true,
        },
        "title": {
            required: true,
        },
        "variables": {
            required: true,
        }
    },
    messages: {
        "code": {
            required: "Please enter code"
        },
        "title": {
            required: "Please enter title"
        },
        "variables": {
            required: "Please enter variable names",
        }
    },
    errorPlacement: function (error, element) 
    {
        error.insertAfter(element)
    },
    submitHandler: function(form) 
    {      
        form.submit();
    }
});
 
$(".ckeditor").each(function(){
    $(this).rules("add", { 
        required:true,
        messages:{required:'Please write template body'}
    });
});

$(".mail_subject").each(function(){
    $(this).rules("add", { 
        required:true,
        messages:{required:'Please write subject'}
    });
});

/** templates listing */
$(document).ready(function(){
    var origin   = window.location.href;
    var table = $('#email_template_listing').DataTable({
        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {  
            $("#email_template_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        ajax: origin,
        'columnDefs': [
            {
                "targets": [0,5,6],
                "className": "text-center",
            },
            {
                "targets": [1],
                "visible": false
            }
        ],
        columns: [
            {data: 'rownum', name: 'rownum'},
            {data: 'id', name: 'id'},
            {data: 'code', name: 'code'},
            {data: 'title', name: 'title'},
            {data: 'variables', name: 'variables'},
            {data: 'is_active', name: 'is_active',
                render: function (data, type, full, meta)
                {                                              
                    var output = "";             
                    if(full.is_active == 1)
                    {                                                             
                        output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="button" aria-pressed="true" autocomplete="off">'
                        +'<div class="handle"></div>'
                        +'</button>' 
                    }
                    else
                    {                         
                        output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="button" aria-pressed="false" autocomplete="off">'
                        +'<div class="handle"></div>'
                        +'</button>'
                    }                       
                    return output;
                },
            },
            {data: 'id', name: 'action', // orderable: true, // searchable: true
                render: function(data, type, row)
                {
                    var output = "";
                    output += '<a href="'+ origin +'/../editTemplate/'+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a> &nbsp; &nbsp;'
                    output += '<a class="template_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },
            },
        ]
    }); 

    /** delete template */
    $('#email_template_listing').on('click', 'tbody .template_delete', function () {
        var data_row = table.row($(this).closest('tr')).data();  
        var template_id = data_row.id;  
        var message = "Are you sure ?";   
        console.log(message);       
        $('#templateDeleteModel').on('show.bs.modal', function(e){
            $('#template_id').val(template_id);
            $('#message_delete').text(message);
        });
        $('#templateDeleteModel').modal('show');              
    })

    $(document).on('click','#deleteTemplate', function(){
        var template_id = $('#template_id').val(); 
        $.ajax({
            url: origin + '/../' + template_id + '/deleteTemplate',
            method: "POST",    
            data: {
                "_token": $('#token').val(),
                template_id: template_id,
            },            
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#templateDeleteModel').modal('hide')
                    table.ajax.reload();                    
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#templateDeleteModel').modal('hide')                  
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

    /** toggle active switch and show confirmation */
    $('#email_template_listing').on('click', 'tbody .toggle-is-active-switch', function () {            
        var is_active = ($(this).attr('aria-pressed') === 'true') ? 0 : 1;         
        var data_row = table.row($(this).closest('tr')).data();     
        if(data_row == undefined)
        {
            var data_row = reintialize_table.row($(this).closest('tr')).data();  
        }                   
        var template_id = data_row.id; 
        var message = ($(this).attr('aria-pressed') === 'true') ? "Are you sure ?" : "Are you sure ?";        
        if($(this).attr('aria-pressed') == false)
        {
            $(this).addClass('active');
        }
        if($(this).attr('aria-pressed') == true)
        {
            $(this).removeClass('active');
        }                        
        $('#templateIsActiveModel').on('show.bs.modal', function(e){
            $('#template_id').val(template_id);
            $('#is_active').val(is_active);
            $('#message').text(message);
        });
        $('#templateIsActiveModel').modal('show');                                         
    });    

    
    /** Activate or deactivate template */
    $(document).on('click','#templateIsActive', function(){ 
        var template_id = $('#template_id').val();
        var is_active = $('#is_active').val();                          
        $.ajax({
            url: origin + '/../activeDeactiveTemplate',
            method: "POST",
            data:{
                "_token": $('#token').val(),
                "is_active":is_active,
                "template_id":template_id                  
            },
            success: function(response)
            {
                if(response.status == 'true')
                {                    
                    $('#templateIsActiveModel').modal('hide')
                    table.ajax.reload();                    
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);                    
                }   
                setTimeout(function(){ 
                    toastr.clear();
                }, 5000);                           
            }
        })
    });  
   
    $("#myTab li>a:first").addClass("active").show(); //Activate first tab on load
    $(".tab_content:first").addClass("active").show();
})

// Set tab active on click
$('#myTab li>a').click(function(e) {
    $($('#myTab li>a').parent()).addClass("active").not(this.parentNode).removeClass("active");   
    e.preventDefault();
});
