
var appendHtml = {
    custGroupsDropdown : function (custGroups) {
        var custGroupIds = document.getElementById('custGroupIds');
        $.each(custGroups, function (index,item) {
            var text = item['group_name'];
            var value = item['id'];
            var o = new Option(text, value);
            custGroupIds.append(o);
        })
    }
}

// click on assign group btn
$('body').on('click', '.custGroupAssignBtn', function () {
    $('#custGroupModel').on('show.bs.modal', function(e){
        appendHtml.custGroupsDropdown(custGroups);
    });
    $('#custGroupModel').modal('show');
});

// click on remove group btn
$('body').on('click', '.custGroupRemoveBtn', function () {
    $('#removeCustGroupModel').on('show.bs.modal', function(e){
        appendHtml.custGroupsDropdown(custGroups);
    });
    $('#removeCustGroupModel').modal('show');
});

$(document).ready(function() {
    var origin = window.location.href;

    var table = $('#customer_list').DataTable({
        processing: true,
        serverSide: true,
        'scrollX': true,
        ajax: {
            "url": origin,
            "type": "GET"
        },
        'columnDefs': [{
            'targets': 0,
            'searchable':false,
            'orderable':false,
            'className': 'dt-body-center',
            'render': function (data, type,row){
                return '<input type="checkbox" name="id[]" value="'
                   + $('<div/>').text(row.id).html() + '">';
                // return '<div class="custom-checkbox custom-control"><input type="checkbox" class="custom-control-input" name="id'+row.id+'[]" value="'
                // + $('<div/>').text(row.id).html() + '"><label class="custom-control-label" for="id'+row.id+'[]"></label>'
            }
         }],
         'order': [1, 'asc'],
        columns: [
            {data:'selectBox' , name:"selectBox"},
            {data: 'customer_unique_id', name: 'customer_unique_id'},
            {data: 'first_name', name: 'first_name'},
            {data: 'last_name', name: 'last_name'},
            {
                render: function (data, type, row) 
                {
                    var output = "";
                    if(row.group_name == null)
                        output += 'Not Assigned';
                    else
                        output += row.group_name;
                    return output;
                },
                // data: 'group_name', name: 'group_name'
            },
            {data: 'email', name: 'email'},
            {data: 'mobile', name: 'mobile'},
            {data: 'ip_address', name: 'ip_address'},
            {data: 'customer_created_at', name: 'customer_created_at',
                render: function (data,type,row) {
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.customer_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }

                }
            },
            {data: 'is_active', name: 'is_active',
                render: function (data)
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
            },
            {data: 'is_verify', name: 'is_verify',
                render: function (data)
                {
                    var output = "";
                    if(data == 1)
                    {
                        output += '<div class="mb-2 mr-2 badge badge-focus">Yes</div>';
                    }
                    else
                    {
                        output += '<div class="mb-2 mr-2 badge badge-focus">No</div>';
                    }
                    return output;
                },
            },

            {data: 'id', name: 'action', // orderable: true, // searchable: true
                render: function(data, type, row)
                {
                    var output = "";
                    output += '<a href="'+origin+'/../edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                    output += '<a href="javascript:void(0);" class="customer_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },
            },
        ],
        fnDrawCallback: function() {
            // $('.toggle-is-active').bootstrapToggle();
            $('.toggle-is-approve').bootstrapToggle();
            $('.toggle-is-deleted').bootstrapToggle();
        },
    });

    // Assign customer group to customer
    // Select all checkbox at once
    $('#selectAllCust').on('click', function(){
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    // if any checkbox is unchecked
    $('#customer_list tbody').on('change', 'input[type="checkbox"]', function(){
        if(!this.checked)
        {
           var el = $('#selectAllCust').get(0);
           if(el && el.checked && ('indeterminate' in el))
           {
              el.indeterminate = true;
           }
        }
    });

    // assign cust group
    var checkedValues = [];
    $('#custGroupModel').on('click', '#confirmAssign', function () {
        table.$('input[type="checkbox"]').each(function()
        {
            if(this.checked)
            {
                checkedValues.push(this.value)
            }
        });

        $.ajax({
            url: baseUrl + '/admin/customer/assignCustGroup',
            method: "POST",
            data:{
                "_token": $('#token').val(),
                'customerIds' : checkedValues,
                'groupId' : $('#custGroupIds').find(":selected").val()
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#custGroupModel').modal('hide')
                    table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#custGroupModel').modal('hide')
                    table.ajax.reload();
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

    // remove customer group
    var checkedValuesToRemove = [];
    $('#removeCustGroupModel').on('click', '#confirmRemove', function () {
        table.$('input[type="checkbox"]').each(function()
        {
            if(this.checked)
            {
                checkedValuesToRemove.push(this.value)
            }
        });

        $.ajax({
            url: baseUrl + '/admin/customer/removeCustGroup',
            method: "POST",
            data:{
                "_token": $('#token').val(),
                'customerIds' : checkedValuesToRemove,
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#removeCustGroupModel').modal('hide')
                    table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#removeCustGroupModel').modal('hide')
                    table.ajax.reload();
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


    $('#customer_list').on('click', 'tbody .customer_delete', function () {
        var data_row = table.row($(this).closest('tr')).data();
        var is_deleted = data_row.is_deleted;
        var customer_id = data_row.id;
        var message = "Are you sure?";
        $('#deleteCustomerModel').on('show.bs.modal', function(e){
            $('#customer_id').val(customer_id);
            $('#is_deleted').val(is_deleted);
            $('#delete_message').text(message);
        });
        $('#deleteCustomerModel').modal('show');
    })

    $(document).on('click', '#customerDelete', function(){
        var customer_id = $('#customer_id').val();
        var is_deleted = $('#is_deleted').val();
        $.ajax({
            url: origin + '/../' + customer_id + '/delete',
            method: "GET",
            data:{is_deleted: is_deleted},
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#deleteCustomerModel').modal('hide')
                    table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success("Customer deleted successfully!");
                    // window.location.href = '/admin/user/list';
                }
                else
                {
                    $('#deleteCustomerModel').modal('hide')
                    // table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success("Something went wrong!");
                }
            }
        });
    })

    $('#customer_list').on('click', 'tbody .toggle-is-active-switch', function () {
        var is_active = ($(this).attr('aria-pressed') === 'true') ? 0 : 1;
        var data_row = table.row($(this).closest('tr')).data();
        var customer_id = data_row.id;
        var message = ($(this).attr('aria-pressed') === 'true') ? "Are you sure ?": "Are you sure ?";
        if($(this).attr('aria-pressed') == 'false')
        {
            $(this).addClass('active');
        }
        if($(this).attr('aria-pressed') == 'true')
        {
            $(this).removeClass('active');
        }
        $('#customerIsActiveModel').on('show.bs.modal', function(e){
            $('#customer_id').val(customer_id);
            $('#is_active').val(is_active);
            $('#message').text(message);
        });
        $('#customerIsActiveModel').modal('show');
    });

    $(document).on('click','#customerIsActive', function(){
        var customer_id = $('#customer_id').val();
        var is_active = $('#is_active').val();
        $.ajax({
            url: origin + '/../activate-deactivate',
            method: "POST",
            data:{
                "_token": $('#token').val(),
                "is_active":is_active,
                "customer_id":customer_id
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#customerIsActiveModel').modal('hide')
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
});