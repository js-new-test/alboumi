$(document).ready(function(){

    var lang_id = " ";
    ajaxCall.getCollections(lang_id);

    // Show image which was selected
    if(page_name == 'edit')
    {
        var src1 = baseUrl + '/public/assets/images/collections/' + collection_image;
        $("#selected_pic").attr("src", src1);
    }
    
     /** Filter collection */
    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterLangDiv").slideToggle('slow');
    });    

     $('body').on('click', '#filter_collection_btn', function () {
        lang_id = $('#filter_collection').val();
        ajaxCall.getCollections(lang_id)
    });

      /** Active inactive collection */
    $('body').on('click', '.toggle-is-active-switch', function () {
        $('#collectionIdForActiveInactive').val($(this).attr('data'));
        $('#is_active').val($(this).attr('active'));
	});

	$('body').on('click', '#confirmActiveInactive', function () {
		ajaxCall.activeInactiveCollection($('#collectionIdForActiveInactive').val(),$('#is_active').val())
    })
    
    /** Delete collection */
    $('body').on('click', '.collection_delete', function () {
		$('#collectionIdForDelete').val($(this).attr('data'));
	});

	$('body').on('click', '#confirmDelete', function () {
		ajaxCall.deleteCollection($('#collectionIdForDelete').val())
    })

});

var ajaxCall = {
    getCollections : function (lang_id) {
        $('#collection_listing').DataTable().destroy();
        $('#collection_listing').DataTable({

        processing: true,
        serverSide: true,
        // "scrollX": true,
        "initComplete": function (settings, json) {  
            $("#collection_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        "ajax": {
            'type': 'get',
            'url': window.location.href + '/list',
            'data': {lang_id:lang_id}
        },
        columnDefs: [
            {
                "targets": [0,1,3,4,5],
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
            "data":'collection_title'
        },
        { 
            "target": 4,
            render: function (data, type, row) 
                {
                    return "<img src=\"" + baseUrl + '/public/assets/images/collections/' + row.collection_image + "\" height=\"80\" width=\"80\"/>";
                },
        },
        {
            "target": 4,
            "render": function (data, type, row, meta)
            {                                              
                var output = "";             
                if(row.status == 1)
                {                                                             
                    output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#collectionActiveInactiveModel" data="'+row['id']+'" active="0">'
                    +'<div class="handle"></div>'
                    +'</button>' 
                }
                else
                {                         
                    output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#collectionActiveInactiveModel" data="'+row['id']+'" active="1">'
                    +'<div class="handle"></div>'
                    +'</button>'
                }                       
                return output;
            },
        },
        {
            "target": -1,
            "bSortable": false,
            "order":false,
            "render": function ( data, type, row ) 
            {
                var output = "";
                output += '<a href="'+ window.location.href +'/editCollection/'+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                output += '<a class="text-danger"><i class="fa fa-trash collection_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#collectionDeleteModel"></i></a>'
                return output;
            },
        }
        ]
    })

    },

    activeInactiveCollection : function (collection_id,is_active) {
        var origin = window.location.href;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: origin + '/activeDeactiveCollection',
            method: "POST",
            data:{
                "is_active":is_active,
                "collection_id":collection_id                  
            },
            success: function(response)
            {
                if(response.status == 'true')
                {                    
                    $('#collectionActiveInactiveModel').modal('hide')
                    $('#collection_listing').DataTable().ajax.reload();
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
    },

    deleteCollection : function (collection_id) {
        var origin = window.location.href;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: origin + '/' + collection_id + '/deleteCollection',
            method: "POST",    
            data: {
                collection_id: collection_id,
            },            
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#collectionDeleteModel').modal('hide')
                    $('#collection_listing').DataTable().ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#faqDeleteModel').modal('hide')                  
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
    }
}


/** add collection form validation */
$("#addCollectionForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "language_id": {
            required: true,
        },
        "collection_title": {
            required: true,
        },
        "collection_image": {
            required: true,
            extension: "jpg|jpeg|png"
        },
        "collection_link": {
            required: true,
        },
        "sort_order": {
            required: true,
        }
    },
    messages: {
        "language_id": {
            required: "Please select language"
        },
        "collection_title": {
            required: "Please write title",
        },
        "collection_image":{
            required: "Please select image",
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        "collection_link" :{
            required: "Please provide link"
        },
        "sort_order" :{
            required: "Please enter sort order"
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

// Show image dimensions for collection image
var _URL = window.URL || window.webkitURL;

function _showLoadedImageDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#width").text(imgwidth);
        $("#height").text(imgheight);   
        $("#image_width").val(imgwidth);
        $("#image_height").val(imgheight);                                    
    }    
};

/** update services form validation */
$("#updateCollectionForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "collection_title": {
            required: true,
        },
        "collection_link": {
            required: true,
        },
        "sort_order": {
            required: true,
        },
        "collection_image" :{
            required:false,
            accept: "jpg,jpeg,png"
        },
    },
    messages: {
        "collection_title": {
            required: "Please write title",
        },
        "collection_link" :{
            required: "Please provide link"
        },
        "sort_order" :{
            required: "Please enter sort order"
        },
        'collection_image' :{
            accept: "Please upload file in these format only (png, jpg, jpeg)."
        },
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