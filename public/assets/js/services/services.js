$(document).ready(function(){

    var lang_id = " ";
    ajaxCall.getServices(lang_id);

    // Show image which was selected
    if(page_name == 'edit')
    {
        var src1 = baseUrl + '/public/assets/images/services/' + service_image;
        $("#selected_pic").attr("src", src1);
    }

    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterLangDiv").slideToggle('slow');
    });    

     /** Filter service */
     $('body').on('click', '#filter_service', function () {
        lang_id = $('#filter_service').val();
        ajaxCall.getServices(lang_id)
    });

      /** Active inactive service */
    $('body').on('click', '.toggle-is-active-switch', function () {
        $('#serviceIdForActiveInactive').val($(this).attr('data'));
        $('#is_active').val($(this).attr('active'));
	});

	$('body').on('click', '#confirmActiveInactive', function () {
		ajaxCall.activeInactiveService($('#serviceIdForActiveInactive').val(),$('#is_active').val())
    })
    
    /** Delete service */
    $('body').on('click', '.service_delete', function () {
		$('#serviceIdForDelete').val($(this).attr('data'));
	});

	$('body').on('click', '#confirmDelete', function () {
		ajaxCall.deleteService($('#serviceIdForDelete').val())
    })

});

var ajaxCall = {
    getServices : function (lang_id) {
        $('#services_listing').DataTable().destroy();
        $('#services_listing').DataTable({

        processing: true,
        serverSide: true,
        // "scrollX": true,
        "initComplete": function (settings, json) {  
            $("#services_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
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
            "data":'service_name'
        },
        { 
            "target": 4,
            render: function (data, type, row) 
                {
                    return "<img src=\"" + baseUrl + '/public/assets/images/services/' + row.service_image + "\" height=\"80\" width=\"80\"/>";
                },
        },
        {
            "target": 4,
            "render": function (data, type, row, meta)
            {                                              
                var output = "";             
                if(row.status == 1)
                {                                                             
                    output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#serviceActiveInactiveModel" data="'+row['id']+'" active="0">'
                    +'<div class="handle"></div>'
                    +'</button>' 
                }
                else
                {                         
                    output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#serviceActiveInactiveModel" data="'+row['id']+'" active="1">'
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
                output += '<a href="'+ window.location.href +'/editService/'+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                output += '<a class="text-danger"><i class="fa fa-trash service_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#serviceDeleteModel"></i></a>'
                return output;
            },
        }
        ]
    })

    },

    activeInactiveService : function (service_id,is_active) {
        var origin = window.location.href;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: origin + '/activeDeactiveService',
            method: "POST",
            data:{
                "is_active":is_active,
                "service_id":service_id                  
            },
            success: function(response)
            {
                if(response.status == 'true')
                {                    
                    $('#serviceActiveInactiveModel').modal('hide')
                    $('#services_listing').DataTable().ajax.reload();
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

    deleteService : function (service_id) {
        var origin = window.location.href;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: origin + '/' + service_id + '/deleteService',
            method: "POST",    
            data: {
                service_id: service_id,
            },            
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#serviceDeleteModel').modal('hide')
                    $('#services_listing').DataTable().ajax.reload();
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
    },
}


/** add services form validation */
$("#addServiceForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "language_id": {
            required: true,
        },
        "service_name": {
            required: true,
        },
        "service_image": {
            required: true,
            extension: "jpg|jpeg|png"
        },
        "short_desc": {
            required: true,
        },
        "link": {
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
        "service_name": {
            required: "Please write service name",
        },
        "service_image":{
            required: "Please select service image",
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        "short_desc" :{
            required: "Please write short description",
        },
        "link" :{
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

// Show image dimensions for service image
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
$("#updateServiceForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "service_name": {
            required: true,
        },
        "short_desc": {
            required: true,
        },
        "link": {
            required: true,
        },
        "sort_order": {
            required: true,
        },
        "service_image" :{
            required:false,
            accept: "jpg,jpeg,png"
        },
    },
    messages: {
        "service_name": {
            required: "Please write service name",
        },
        "short_desc" :{
            required: "Please write short description",
        },
        "link" :{
            required: "Please provide link"
        },
        "sort_order" :{
            required: "Please enter sort order"
        },
        'service_image' :{
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