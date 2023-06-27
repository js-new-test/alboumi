$(document).ready(function(){
    ajaxCalls.getEventEnquiryPhotos();
    $('#uploadImagesBtn').hide();
});

var ajaxCalls = {
    getEventEnquiryPhotos : function (filterData) {
        $('#eventEnqPhotosListing').DataTable().destroy();
        table = $('#eventEnqPhotosListing').DataTable({
            processing: true,
            serverSide: true,
            'scrollX': true,
            "ajax": {
                'type': 'get',
                'url': baseUrl + '/admin/eventEnq/photos/getPhotosList?enqId=' + $('#enqId').val(),
                'data': filterData
            },
            columnDefs: [
                {
                    "targets": [0,1,2,3],
                    "className": "text-center",
                },
                {
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center',

                    'render': function (data, type,row){
                    // return '<div class="custom-checkbox custom-control"><input type="checkbox" class="custom-control-input" name="id'+row.id+'[]" value="'
                    // + $('<div/>').text(row.id).html() + '"><label class="custom-control-label" for="id'+row.id+'[]"></label>'
                    return '<input type="checkbox" name="id[]" value="'
                        + $('<div/>').text(row.id).html() + '">';
                    }
                },
                {
                    "targets": [1],
                    "visible": false
                }
            ],
            'order': [1, 'asc'],
            columns: [
            {
                "target": 0,
                "data":'selectBox'
            },
            {
                "target": 1,
                "data":'id'
            },
            {
                "target":3,
                "data":'image',
                "render": function (data, type, full, meta)
                {
                    return "<img src=\"" + data + "\" height=\"100\" width=\"100\"/>";
                },
            },
            {
                "target": -1,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row )
                {
                    var output = "";
                    var disable = (row.photographer_id == 0) ? "" : "disabled";
                    output += '<a href="javascript:void(0);" class="photoDelete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },
            }
            ]
        })
    },
}

if (window.File && window.FileList && window.FileReader)
{
    $("#upload_imgs").on("change", function(e) {
        var files = e.target.files,
        filesLength = files.length;
        if(filesLength > 0)
        {
            for (var i = 0; i < filesLength; i++)
            {
                var f = files[i]
                var fileReader = new FileReader();
                fileReader.onload = (function(e) {
                    var file = e.target;
                    $("<span class=\"pip\">" +
                    "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                    "<br/><span class=\"remove\"><i class='fa fa-trash'></span>" +
                    "</span>").insertAfter("#upload_imgs");
                    $(".remove").click(function(){
                    $(this).parent(".pip").remove();
                    });

                });
                fileReader.readAsDataURL(f);
            }
            $('#uploadImagesBtn').show();
        }
    });
}


// Select all checkbox at once
$('#selectAllPhotos').on('click', function(){
    var rows = table.rows({ 'search': 'applied' }).nodes();
    $('input[type="checkbox"]', rows).prop('checked', this.checked);
});

// if any checkbox is unchecked
$('#eventEnqPhotosListing tbody').on('change', 'input[type="checkbox"]', function(){
    console.log('checkbox');

    if(!this.checked)
    {
        console.log(this);
       var el = $('#selectAllPhotos').get(0);
       if(el && el.checked && ('indeterminate' in el))
       {
          el.indeterminate = true;
       }
    }
});

// delete single photo
var checkedValues = [];
const url = window.location.href;
eventEnqId = url.split("/").pop();

$('#eventEnqPhotosListing').on('click', 'tbody .photoDelete', function () {
    var data_row = table.row($(this).closest('tr')).data();
    checkedValues.push(data_row.id);

    var message = "Are you sure ?";
    $('#photoDeleteModel').on('show.bs.modal', function(e){
        $('#photo_id').val(checkedValues);
        $('#message_delete').text(message);
    });
    $('#photoDeleteModel').modal('show');
})

$(document).on('click','#deletePhoto', function(){
    table.$('input[type="checkbox"]').each(function()
    {
        if(this.checked)
        {
            checkedValues.push(this.value)
        }
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: baseUrl + '/admin/eventEnq/photos/deletePhoto',
        method: "POST",
        data: {
            photo_id: checkedValues,
            eventEnqId : eventEnqId
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#photoDeleteModel').modal('hide')
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#photoDeleteModel').modal('hide')
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

// delete multiple photos
$('body').on('click', '.deleteMultiplePhotos', function () {
    $('#photoDeleteModel').modal('show');
});

