$(document).ready(function() {
    var origin   = window.location.href;
    var reintialize_table;
    var table = $('#user_list').DataTable({
        processing: true,
        serverSide: true,
        "scrollX": true,
        ajax: {
            "url": origin,
            "type": "GET"
        },
        columns: [
            {data: 'firstname', name: 'firstname'},
            {data: 'lastname', name: 'lastname'},
            {data: 'email', name: 'email'},
            {data: 'role_title', name: 'role_title'},
            {data: 'user_created_at', name: 'user_created_at',
                render: function (data,type,row) {                   
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.user_created_at).utcOffset(z.replace('.', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }
                    
                }
            },
            {data: 'is_active', name: 'is_active',
                render: function (data, type, full, meta)
                {
                    var output = "";
                    if(data == 1)
                    {
                        output += '<div class="row">'
                        +'<div class="col-sm-5">'
                        +'<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="button" aria-pressed="true" autocomplete="off">'
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
                // className: 'text-center',
            },
            // {data: 'is_deleted', name: 'is_deleted',
            //     render: function (data)
            //     {                           
            //         var output = "";             
            //         if(data == 1)
            //         {                        
            //             output += '<div class="mb-2 mr-2 badge badge-focus">Yes</div>';                    
            //         }
            //         else
            //         {                        
            //             output += '<div class="mb-2 mr-2 badge badge-focus">No</div>';                    
            //         }                    
            //         return output;
            //     },
            //     // className: 'text-center',
            // },
            {data: 'id', name: 'action', // orderable: true, // searchable: true
                render: function(data, type, row)
                {
                    // console.log(row.id);
                    var output = "";
                    output += '<a href="'+origin+'/../edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                    output += '<a href="javascript:void(0);" class="user_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },
                // className: 'text-center',
            },            
        ],
        fnDrawCallback: function() {
            $('.toggle-is-deleted').bootstrapToggle();
        },
    }); 
    
    $(document).on('click','#search_role', function(){
        var token = $('input[name="_token"]').val();
        var filter_role = $('#filter_role').val();
        $('#user_list').dataTable().fnDestroy(); 
        reintialize_table = $('#user_list').DataTable({
            processing: true,
            serverSide: true,
            "scrollX": true,
            ajax: {
                url: origin + '/../search_role',
                method: "POST",
                data:{
                    _token : token,
                    filter_role:filter_role
                },
            },
            columns: [
                {data: 'firstname', name: 'firstname'},
                {data: 'lastname', name: 'lastname'},
                {data: 'email', name: 'email'},
                {data: 'role_title', name: 'role_title'},
                {data: 'r_created_at', name: 'r_created_at',
                    render: function (data,type,row) {                   
                        if(row.user_zone != null)
                        {                            
                            var z = row.user_zone;
                            return moment.utc(row.r_created_at).utcOffset(z.replace('.', "")).format('YYYY-MM-DD HH:mm:ss')
                        }
                        else
                        {
                            return "-----"
                        }
                        
                    }
                },
                {data: 'is_active', name: 'is_active',
                    render: function (data, type, full, meta)
                    {
                        var output = "";
                        if(data == 1)
                        {
                            output += '<div class="row">'
                            +'<div class="col-sm-5">'
                            +'<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="button" aria-pressed="true" autocomplete="off">'
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
                    // className: 'text-center',
                },                
                {data: 'id', name: 'action', // orderable: true, // searchable: true
                    render: function(data, type, row)
                    {
                        // console.log(row.id);
                        var output = "";
                        output += '<a href="'+origin+'/../edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                        output += '<a href="" class="user_delete"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                        return output;
                    },
                    // className: 'text-center',
                },            
            ],
            fnDrawCallback: function() {
                $('.toggle-is-deleted').bootstrapToggle();
            },
        });               
    })
    
    $('#user_list').on('click', 'tbody .user_delete', function () {
        var data_row = table.row($(this).closest('tr')).data();
        var is_deleted = data_row.is_deleted;
        var user_id = data_row.id;
        var message = "Are you sure?";
        $('#deleteUserModel').on('show.bs.modal', function(e){
            $('#user_id').val(user_id);
            $('#is_deleted').val(is_deleted);
            $('#delete_message').text(message);
        });
        $('#deleteUserModel').modal('show');
    })

    $(document).on('click', '#userDelete', function(){
        var user_id = $('#user_id').val();
        var is_deleted = $('#is_deleted').val(); 
        $.ajax({
            url: origin + '/../' + user_id + '/delete',
            method: "GET",
            data:{is_deleted: is_deleted},
            success: function(response)
            {                    
                if(response.status == 'true')
                {
                    $('#deleteUserModel').modal('hide')
                    table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success("User deleted successfully!");
                    // window.location.href = '/admin/user/list';
                }
                else
                {
                    $('#deleteUserModel').modal('hide')
                    // table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success("Something went wrong!");
                }
                setTimeout(function(){ 
                    toastr.clear();
                }, 5000);
            }
        });
    })

    // $('#user_list').on('click', 'tbody .user_delete', function () {
    //     var data_row = table.row($(this).closest('tr')).data(); 
    //     if(data_row == undefined)
    //     {
    //         var data_row = reintialize_table.row($(this).closest('tr')).data();  
    //     }         
    //     var is_deleted = data_row.is_deleted;
    //     var user_id = data_row.id;
    //     if(confirm("Are you sure you want to delete this user ?"))
    //     {
    //         // alert('delete');
    //         $.ajax({
    //             url: origin + '/../' + user_id + '/delete',
    //             method: "GET",
    //             data:{is_deleted: is_deleted},
    //             success: function(response)
    //             {  
    //                 // console.log(response);
    //                 if(response.status == 'true')
    //                 {
    //                     window.location.href = './list';
    //                 }                    
    //             }
    //         });
    //     }
    // })    

    $('#user_list').on('click', 'tbody .toggle-is-active-switch', function () {
        // alert($(this).attr('aria-pressed'));
        var is_active = ($(this).attr('aria-pressed') === 'true') ? 0 : 1;         
        var data_row = table.row($(this).closest('tr')).data();
        if(data_row == undefined)
        {
            var data_row = reintialize_table.row($(this).closest('tr')).data();  
        }                             
        var user_id = data_row.id; 
        var message = ($(this).attr('aria-pressed') === 'true') ? "Are you sure ?": "Are you sure ?";        
        if($(this).attr('aria-pressed') == 'false')
        {
            $(this).addClass('active');
        }
        if($(this).attr('aria-pressed') == 'true')
        {
            $(this).removeClass('active');
        }
        $('#userIsActiveModel').on('show.bs.modal', function(e){
            $('#user_id').val(user_id);
            $('#is_active').val(is_active);
            $('#message').text(message);
        });
        $('#userIsActiveModel').modal('show');
    });    
    
    $(document).on('click','#userIsActive', function(){ 
        var user_id = $('#user_id').val();
        var is_active = $('#is_active').val();
        $.ajax({
            url: origin + '/../activate-deactivate',
            method: "POST",
            data:{
                "_token": $('#token').val(),
                "is_active":is_active,
                "user_id":user_id
            },
            success: function(response)
            {
                if(response.status == 'true')
                {                    
                    // $('#userIsActiveModel').modal('hide')
                    // table.ajax.reload();                    
                    // toastr.clear();
                    // toastr.options.closeButton = true;
                    // toastr.options.timeOut = 0;
                    // toastr.success(response.msg);
                    location.reload();                     
                }   
                setTimeout(function(){ 
                    toastr.clear();
                }, 5000);
            }
        })
    });  
    
});
