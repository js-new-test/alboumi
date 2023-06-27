$(document).ready(function() {
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "/" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
    console.log(baseUrl);
    var reintialize_table;
    var table = $('#banner_list').DataTable({
        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {  
            $("#banner_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        order: [[ 3, "desc" ]],
        ajax: {
            "url": window.location.href,
            "type": "GET"
        },
        columns: [
            {data: 'banner', name: 'banner'},
            { data: 'bnr_created_at', name: 'bnr_created_at',
                render: function (data,type,row) {                    
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.bnr_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }
                    
                }
            },
            {data: 'status', name: 'status',
                render: function (data, type, row, meta)
                {                                          
                    var output = "";             
                    if(data == 1)
                    {                                                             
                        output += '<div class="row">'
                        +'<div class="col-sm-5">'
                        +'<button type="button" disabled class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="button" aria-pressed="true" autocomplete="off">'
                        +'<div class="handle"></div>'
                        +'</button>'
                        +'</div>'                    
                    }
                    else
                    {                         
                        output += '<div class="row">'
                        +'<div class="col-sm-5">'
                        +'<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="button" aria-pressed="false" autocomplete="off">'
                        +'<div class="handle"></div>'
                        +'</button>'
                        +'</div>'                        
                    }                       
                    return output;
                },
            },                                    
            {data: 'id', name: 'action', // orderable: true, // searchable: true
                render: function(data, type, row)
                {                    
                    var output = "";
                    output += '<a href="'+window.location.href+'/edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                    if(row.status == 0)
                    {
                        output += '<a href="javascript:void(0);" class="banner_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    }
                    return output;
                },                
            },            
        ],
        fnDrawCallback: function() {
        },
    });   
    
    $(document).on('click','#filter_banner', function(){
        var token = $('input[name="_token"]').val();
        var filter_banner_lang = $('#filter_banner_lang').val();
        $('#banner_list').dataTable().fnDestroy(); 
        table = $('#banner_list').DataTable({
            processing: true,
            serverSide: true,
            "initComplete": function (settings, json) {  
                $("#banner_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            ajax: {
                url: window.location.href + '/filter-banner',
                method: "POST",
                data:{
                    _token : token,
                    filter_banner_lang:filter_banner_lang
                },
            },
            columns: [
                {data: 'banner', name: 'banner'},
                { data: 'bnr_created_at', name: 'bnr_created_at',
                    render: function (data,type,row) {                    
                        if(row.user_zone != null)
                        {
                            var z = row.user_zone;
                            return moment.utc(row.bnr_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                        }
                        else
                        {
                            return "-----"
                        }
                        
                    }
                },
                {data: 'status', name: 'status',
                    render: function (data, type, row, meta)
                    {                                          
                        var output = "";             
                        if(data == 1)
                        {                                                             
                            output += '<div class="row">'
                            +'<div class="col-sm-5">'
                            +'<button type="button" disabled class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="button" aria-pressed="true" autocomplete="off">'
                            +'<div class="handle"></div>'
                            +'</button>'
                            +'</div>'                    
                        }
                        else
                        {                         
                            output += '<div class="row">'
                            +'<div class="col-sm-5">'
                            +'<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="button" aria-pressed="false" autocomplete="off">'
                            +'<div class="handle"></div>'
                            +'</button>'
                            +'</div>'                        
                        }                       
                        return output;
                    },
                },                                    
                {data: 'id', name: 'action', // orderable: true, // searchable: true
                    render: function(data, type, row)
                    {                    
                        var output = "";
                        output += '<a href="'+window.location.href+'/edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                        if(row.status == 0)
                        {
                            output += '<a href="javascript:void(0);" class="banner_delete text-dangere"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                        }
                        return output;
                    },                
                },            
            ],
            fnDrawCallback: function() {
            },
        });               
    })
    
    $('#banner_list').on('click', 'tbody .banner_delete', function () {
        var data_row = table.row($(this).closest('tr')).data();  
        var banner_id = data_row.id;    
        var message = "Are you sure?";         
        $('#bannerDeleteModel').on('show.bs.modal', function(e){
            $('#banner_id').val(banner_id);            
            $('#message').text(message);
        });
        $('#bannerDeleteModel').modal('show');              
    })
    
    $(document).on('click','#deleteBanner', function(){
        var banner_id = $('#banner_id').val(); 
        $.ajax({
            url: window.location.href + '/delete',
            method: "POST",    
            data: {
                "_token": $('#token').val(),
                banner_id: banner_id,
            },            
            success: function(response)
            {                
                if(response.status == 'true')
                {
                    $('#bannerDeleteModel').modal('hide')
                    table.ajax.reload();                    
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#bannerDeleteModel').modal('hide')
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

    $('#banner_list').on('click', 'tbody .toggle-is-active-switch', function () { 
        if($(this).attr('aria-pressed') == 'false')
        {
            $(this).addClass('active');
        }
        if($(this).attr('aria-pressed') == 'true')
        {
            $(this).removeClass('active');
        }  
        var data_row = table.row($(this).closest('tr')).data();                              
        var status = data_row.status;
        var banner_id = data_row.id;                
        var message = "Are you sure?";                                       
        $('#bannerActDeactModel').on('show.bs.modal', function(e){
            $('#banner_id').val(banner_id);
            $('#status').val(status);
            $('#status_message').text(message);
        });
        $('#bannerActDeactModel').modal('show');
    })

    $(document).on('click','#bannerActDeact', function(){
        var banner_id = $('#banner_id').val();
        var status = $('#status').val();
        $.ajax({
            url: window.location.href + '/activate-deactivate',
            method: "POST",    
            data: {
                "_token": $('#token').val(),
                banner_id: banner_id,
                status: status,
            },            
            success: function(response)
            {                           
                if(response.status == 'true')
                {
                    $('#bannerActDeactModel').modal('hide')
                    table.ajax.reload();                    
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#bannerActDeactModel').modal('hide')
                    // table.ajax.reload();                    
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
});
