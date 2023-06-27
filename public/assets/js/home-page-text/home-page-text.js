$("#homePageTextForm").validate( {
    rules: { 
        language: {
            required: true,
        },
        content_1 : {
            required: true,
        },           
        content_2: {
            required: true,
        }                                                               
    },
    messages: { 
        language: {
            required: "Language field is required",
        }, 
        content_1 : {
            required: 'Content 1 field is required',
        },          
        content_2: {
            required: 'Content 2 field is required',
        }                       
    },
    errorPlacement: function ( error, element ) {        
        if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.next( "label" ) );
        } else {
            error.insertAfter( element );
        }
    },
    
} );

var table = $('#home_page_text_list').DataTable({
    processing: true,
    serverSide: true,
    // "scrollX": true,
    "initComplete": function (settings, json) {  
        $("#home_page_text_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
    },
    order: [[ 4, "desc" ]],
    ajax: {
        "url": window.location.href,
        "type": "GET"
    },
    columns: [
        {data: 'langName', name: 'langName'},
        {data: 'content_1', name: 'content_1'},
        {data: 'content_2', name: 'content_2'},
        { data: 'zone', name: 'zone',
            render: function (data,type,row) {                    
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.hpt_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                output += '<a href="javascript:void(0);" class="hpt_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                return output;
            },                
        },            
    ],
    fnDrawCallback: function() {
    },
});

$(document).on('click','.hpt_delete', function(){
    var data_row = table.row($(this).closest('tr')).data();  
    var hpt_id = data_row.id;    
    var message = "Are you sure?";         
    $('#HPTDeleteModel').on('show.bs.modal', function(e){
        $('#hpt_id').val(hpt_id);            
        $('#message').text(message);
    });
    $('#HPTDeleteModel').modal('show'); 
})

$(document).on('click','#deleteHPT', function(){
    var hpt_id = $('#hpt_id').val(); 
    $.ajax({
        url: window.location.href + '/delete',
        method: "POST",    
        data: {
            "_token": $('#token').val(),
            hpt_id: hpt_id,
        },            
        success: function(response)
        {                
            if(response.status == 'true')
            {
                $('#HPTDeleteModel').modal('hide')
                table.ajax.reload();                    
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#HPTDeleteModel').modal('hide')
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


