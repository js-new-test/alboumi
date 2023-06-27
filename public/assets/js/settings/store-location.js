$("#storeLocationForm").validate({
    rules: {         
        language : {
            required: true,
        },
        title : {
            required: true,
        },  
        address_1 : {
            required: true,
        },          
        phone : {
            required: true,
        },                                          
    },
    messages: {  
        language : {
            required: 'Language is required',
        },
        title : {
            required: 'Title is required',
        },
        address_1 : {
            required: 'Address 1 is required',
        },
        phone : {
            required: 'Phone number is required',
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
});

var table = $('#store_loc_list').DataTable({
    processing: true,
    serverSide: true,    
    "initComplete": function (settings, json) {  
        $("#store_loc_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
    },
    ajax: {
        "url": window.location.href,
        "type": "GET"
    },
    columns: [        
        {data: 'title', name: 'title'},                                     
        {data: 'address_1', name: 'address_1'},
        {data: 'phone', name: 'phone'},
        {data: 'sl_created_at', name: 'sl_created_at',
            render: function (data,type,row) {                    
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.sl_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                output += '<a href="javascript:void(0);" class="store_location text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                return output;
            },                
        },            
    ],
    fnDrawCallback: function() {
    },
});

$(document).on('click','#filter_SL', function(){
    var token = $('input[name="_token"]').val();
    var filter_SL_lang = $('#filter_SL_lang').val();    
    $('#store_loc_list').dataTable().fnDestroy(); 
    table = $('#store_loc_list').DataTable({
        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {  
            $("#store_loc_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        ajax: {
            url: window.location.href + '/filter',
            method: "POST",
            data:{
                _token : token,
                filter_SL_lang:filter_SL_lang
            },
        },
        columns: [        
            {data: 'title', name: 'title'},                                     
            {data: 'address_1', name: 'address_1'},
            {data: 'phone', name: 'phone'},
            {data: 'sl_created_at', name: 'sl_created_at',
                render: function (data,type,row) {                    
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.sl_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                    output += '<a href="javascript:void(0);" class="store_location text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },                
            },            
        ],
        fnDrawCallback: function() {
        },
    });               
})

$(document).on('click','.store_location', function(){
    var data_row = table.row($(this).closest('tr')).data();  
    var sl_id = data_row.id;    
    var message = "Are you sure?";         
    $('#storeLocationDeleteModel').on('show.bs.modal', function(e){
        $('#sl_id').val(sl_id);            
        $('#message').text(message);
    });
    $('#storeLocationDeleteModel').modal('show'); 
})

$(document).on('click','#deleteStoreLocation', function(){
    var sl_id = $('#sl_id').val(); 
    $.ajax({
        url: window.location.href + '/delete',
        method: "POST",    
        data: {
            "_token": $('#token').val(),
            sl_id: sl_id,
        },            
        success: function(response)
        {                
            if(response.status == 'true')
            {
                $('#storeLocationDeleteModel').modal('hide')
                table.ajax.reload();                    
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#storeLocationDeleteModel').modal('hide')
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