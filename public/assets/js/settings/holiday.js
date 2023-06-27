$('#holiday_date').datepicker({
    "format": "yyyy-mm-dd",
    "autoclose": true,
    "orientation": "top",
});

$('#start_date').datepicker({
    "format": "yyyy-mm-dd",
    "autoclose": true,
    "orientation": "top",
    "endDate": "today"
});

$('#end_date').datepicker({
    "format": "yyyy-mm-dd",
    "autoclose": true,
    "orientation": "top",
    "endDate": "today"
});

$('#holiday_date_edit').datepicker({
    "format": "yyyy-mm-dd",
    "autoclose": true,
    "orientation": "top",
});

$("#holidayForm").validate({
    rules: {         
        holiday_date : {
            required: true,
        },
        name : {
            required: true,
        },                                                    
    },
    messages: {  
        holiday_date : {
            required: 'Date is required',
        },
        name : {
            required: 'Name is required',
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


$("#filterHolidayForm").validate({
    rules: {         
        start_date : {
            required: true,
        },
        end_date : {
            required: true,
            // greaterThan: "#start_date"
        },                                                    
    },
    messages: {  
        start_date : {
            required: 'Start date is required',
        },
        end_date : {
            required: 'End date is required',
            // greaterThan: "Start date should be less then to end date"
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


var table = $('#holiday_list').DataTable({
    processing: true,
    serverSide: true, 
    "initComplete": function (settings, json) {  
        $("#holiday_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
    },   
    ajax: {
        "url": window.location.href,
        "type": "GET"
    },
    columns: [        
        {data: 'date', name: 'date'},                                     
        {data: 'name', name: 'name'},    
        {data: 'holiday_created_at', name: 'holiday_created_at',
            render: function (data,type,row) {                    
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.holiday_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                output += '<a href="javascript:void(0);" class="holiday text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                return output;
            },                
        },            
    ],
    fnDrawCallback: function() {
    },
});

$(document).on('click','#filter_holiday', function(){
    $("#filterHolidayForm").valid();
    var token = $('input[name="_token"]').val();
    var start_date = $('#start_date').val();    
    var end_date = $('#end_date').val();    
    $('#holiday_list').dataTable().fnDestroy(); 
    table = $('#holiday_list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href + '/filter',
            method: "POST",
            data:{
                _token : token,
                start_date:start_date,
                end_date:end_date,
            },
        },
        columns: [        
            {data: 'date', name: 'date'},                                     
            {data: 'name', name: 'name'},    
            {data: 'holiday_created_at', name: 'holiday_created_at',
                render: function (data,type,row) {                    
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.holiday_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                    output += '<a href="javascript:void(0);" class="holiday text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },                
            },            
        ],
        fnDrawCallback: function() {
        },
    });               
})

$(document).on('click','.holiday', function(){
    var data_row = table.row($(this).closest('tr')).data();  
    var h_id = data_row.id;    
    var message = "Are you sure?";         
    $('#holidayDeleteModel').on('show.bs.modal', function(e){
        $('#h_id').val(h_id);            
        $('#message').text(message);
    });
    $('#holidayDeleteModel').modal('show'); 
})

$(document).on('click','#deleteStoreLocation', function(){
    var h_id = $('#h_id').val(); 
    $.ajax({
        url: window.location.href + '/delete',
        method: "POST",    
        data: {
            "_token": $('#token').val(),
            h_id: h_id,
        },            
        success: function(response)
        {                
            if(response.status == 'true')
            {
                $('#holidayDeleteModel').modal('hide')
                table.ajax.reload();                    
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#holidayDeleteModel').modal('hide')
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