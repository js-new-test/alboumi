$(document).ready(function(){
    ajaxCall.getCustGroups();

    /** Delete service */
    $('body').on('click', '.group_delete', function () {
		$('#groupIdForDelete').val($(this).attr('data'));
	});

	$('body').on('click', '#confirmDelete', function () {
		ajaxCall.deleteGroup($('#groupIdForDelete').val())
    })

});

var ajaxCall = {
    getCustGroups : function () {
        $('#custGroupsListing').DataTable().destroy();
        $('#custGroupsListing').DataTable({

        processing: true,
        serverSide: true,
        "scrollX": true,
        "ajax": {
            'type': 'get',
            'url': window.location.href + '/list',
        },
        columnDefs: [
            {
                "targets": [0,1,3],
                "className": "text-center",
            },
            {
                "targets": [1],
                "visible": false
            },
        ],
        columns: [
        {
            "target": 0,
            "data":'rownum'
        },
        {
            "target": 1,
            "data":'id'
        },
        {
            "target": 2,
            "data":'group_name'
        },
        {
            "target": -1,
            "bSortable": false,
            "order":false,
            "render": function ( data, type, row )
            {
                var output = "";
                output += '<a href="'+ window.location.href +'/editGroup/'+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                output += '<a class="text-danger"><i class="fa fa-trash group_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#groupDeleteModel"></i></a>'
                return output;
            },
        }
        ]
    })

    },

    deleteGroup : function (group_id) {
        var origin = window.location.href;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: origin + '/' + group_id + '/deleteGroup',
            method: "POST",
            data: {
                group_id: group_id,
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#groupDeleteModel').modal('hide')
                    $('#custGroupsListing').DataTable().ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#groupDeleteModel').modal('hide')
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
    },
}


/** add group form validation */
$("#addGroupForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "group_name": {
            required: true,
        }
    },
    messages: {
        "group_name": {
            required: "Please write group name"
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

/** update group form validation */
$("#updateGroupForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "group_name": {
            required: true,
        }
    },
    messages: {
        "group_name": {
            required: "Please write group name"
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