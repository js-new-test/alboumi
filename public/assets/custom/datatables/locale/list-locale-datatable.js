$(document).ready(function() {
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "/" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
    console.log(baseUrl);
    var reintialize_table;
    var table = $('#locale_list').DataTable({
        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {  
            $("#locale_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        ajax: {
            "url": window.location.href,
            "type": "GET"
        },
        columns: [
            {data: 'code', name: 'code'},
            {data: 'title', name: 'title'},
            { data: 'zone', name: 'zone',
                render: function (data,type,row) {                    
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.lc_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                    output += '<a href="javascript:void(0);" class="locale_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },                
            },            
        ],
        fnDrawCallback: function() {
        },
    });
    
    $(document).on('click','#search_locale', function(e){
        e.preventDefault();
        var token = $('input[name="_token"]').val();
        var code = $('#code').val();
        var title = $('#title').val();
        $('#locale_list').dataTable().fnDestroy(); 
        reintialize_table = $('#locale_list').DataTable({
            processing: true,
            serverSide: true,
            "initComplete": function (settings, json) {  
                $("#locale_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            ajax: {
                url: window.location.href + '/filter',
                method: "POST",
                data:{
                    _token : token,
                    code:code,
                    title:title
                },
            },
            columns: [
                {data: 'code', name: 'code'},
                {data: 'title', name: 'title'},
                { data: 'zone', name: 'zone',
                    render: function (data,type,row) {                    
                        if(row.user_zone != null)
                        {
                            var z = row.user_zone;
                            return moment.utc(row.lc_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                        output += '<a href="javascript:void(0);" class="locale_delete"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                        return output;
                    },                
                },            
            ],
            fnDrawCallback: function() {                        
            },
        });               
    })
    
    $('#locale_list').on('click', 'tbody .locale_delete', function () {
        var data_row = table.row($(this).closest('tr')).data();  
        var locale_id = data_row.id;
        console.log(locale_id);  
        var message = "Are you sure?";         
        $('#localeDeleteModel').on('show.bs.modal', function(e){
            $('#locale_id').val(locale_id);            
            $('#message').text(message);
        });
        $('#localeDeleteModel').modal('show');              
    })
    
    $(document).on('click','#deleteLocale', function(){
        var locale_id = $('#locale_id').val(); 
        $.ajax({
            url: window.location.href + '/delete',
            method: "POST",    
            data: {
                "_token": $('#token').val(),
                locale_id: locale_id,
            },            
            success: function(response)
            {                
                if(response.status == 'true')
                {
                    $('#localeDeleteModel').modal('hide')
                    table.ajax.reload();                    
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#localeDeleteModel').modal('hide')
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

    $(document).on('click', '#export_locale_btn', function(){        
        $('#loadDefaultLangModel').modal('show');

        var selected_lang = $('#export_language :selected').val();
        $('#export_locale_code_link').attr('href', window.location.href + '/export-locale/' + selected_lang)        
    })

    $(document).on('change', '#export_language', function(){
        var selected_lang = $(this).val()
        $('#export_locale_code_link').attr('href', window.location.href + '/export-locale/' + selected_lang)        
    })
    
});
