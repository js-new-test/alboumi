$( document ).ready(function() {

    var src1 = baseUrl + '/public/assets/images/home-page-photographer/bigimage/' + big_image;
    $("#selected_bigimage").attr("src", src1);
    var src2 = baseUrl + '/public/assets/images/home-page-photographer/smallimage/' + small_image;
    $("#selected_smallimage").attr("src", src2);
});
var _URL = window.URL || window.webkitURL;

$('#p_image').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#width").text(imgwidth);
        $("#height").text(imgheight);
        $("#loaded_image_width").val(imgwidth);
        $("#loaded_image_height").val(imgheight);
    }
});

$('#p_image_update').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#width").text(imgwidth);
        $("#height").text(imgheight);
        $("#loaded_image_width").val(imgwidth);
        $("#loaded_image_height").val(imgheight);
    }
});

$("#homePagePhotographerForm").validate({
    rules: {
        // language : {
        //     required: true,
        // },
        p_id : {
            required: true,
        },
        p_status:{
            required: true,
        },
        p_sort_order: {
            required: true,
        },
         big_image:{
             required: true,
             extension: "jpg|jpeg|png|"
         },
         small_image:{
             required: true,
             extension: "jpg|jpeg|png|"
         },
        // link:{
        //     required: true,
        // }
    },
    messages: {
        // language : {
        //     required: 'Language is required',
        // },
        p_id : {
            required: 'Name is required',
        },
        p_status : {
            required: 'Status is required',
        },
        p_sort_order : {
            required: 'Sort Order is required',
        },
        big_image : {
            required: 'Please upload big image',
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        small_image : {
            required: 'Please upload small image',
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        // link:{
        //     required: 'Link is required',
        // }
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
$("#homePagePhotographerupdateForm").validate({
    rules: {
        // language : {
        //     required: true,
        // },
        p_id : {
            required: true,
        },
        p_status:{
            required: true,
        },
        p_sort_order: {
            required: true,
        },
        big_image:{
            extension: "jpg|jpeg|png|"
        },
        small_image:{
            extension: "jpg|jpeg|png|"
        },
        // link:{
        //     required: true,
        // }
    },
    messages: {
        // language : {
        //     required: 'Language is required',
        // },
        p_id : {
            required: 'Name is required',
        },
        p_status : {
            required: 'Status is required',
        },
        p_sort_order : {
            required: 'Sort Order is required',
        },
        big_image : {
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        small_image : {
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        // link:{
        //     required: 'Link is required',
        // }
    },
    errorPlacement: function ( error, element ) {
        // Add the `invalid-feedback` class to the error element
        if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.next( "label" ) );
        } else {
            error.insertAfter( element );
        }
    },

});var table = $('#home_page_photographer_list').DataTable({
    processing: true,
    serverSide: true,
    "scrollX": true,
    // order: [[ 6, "desc" ]],
    ajax: {
        "url": window.location.href,
        "type": "GET"
    },
    columns: [
        // {data: 'langName', name: 'langName'},
        {data: 'name', name: 'name'},
        {data: 'image', name: 'image',
            render: function(data,type,row){
                return '<img height="80" width="80" src="'+window.location.href +'/../../public/assets/images/photographers/'+row.profile_pic+'">';
            },
        },
        // {data: 'link', name: 'link'},
        {data: 'sort_order', name: 'sort_order'},
        {data: 'hpp_created_at', name: 'hpp_created_at',
            render: function (data,type,row) {
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.hpp_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                }
                else
                {
                    return "-----"
                }

            }
        },
        {data: 'status', name: 'status',
            render: function (data, type, row)
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
        {data: 'id', name: 'action', // orderable: true, // searchable: true
            render: function(data, type, row)
            {
                var output = "";
                output += '<a href="'+window.location.href+'/edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                output += '<a href="javascript:void(0);" class="hpp_photographer text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                return output;
            },
        },
    ],
    fnDrawCallback: function() {
    },
});

$(document).on('click','#filter_HPP', function(){
    var token = $('input[name="_token"]').val();
    var filter_HPP_lang = $('#filter_HPP_lang').val();
    $('#home_page_photographer_list').dataTable().fnDestroy();
    table = $('#home_page_photographer_list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href + '/filter',
            method: "POST",
            data:{
                _token : token,
                filter_HPP_lang:filter_HPP_lang
            },
        },
        columns: [
            // {data: 'langName', name: 'langName'},
            {data: 'name', name: 'name'},
            {data: 'image', name: 'image',
                render: function(data,type,row){
                    return '<img height="80" width="80" src="'+window.location.href +'/../../public/assets/images/photographers/'+row.profile_pic+'">';
                },
            },
            // {data: 'link', name: 'link'},
            {data: 'sort_order', name: 'sort_order'},
            {data: 'hpp_created_at', name: 'hpp_created_at',
                render: function (data,type,row) {
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.hpp_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }

                }
            },
            {data: 'status', name: 'status',
                render: function (data, type, row)
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
            {data: 'id', name: 'action', // orderable: true, // searchable: true
                render: function(data, type, row)
                {
                    var output = "";
                    output += '<a href="'+window.location.href+'/edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                    output += '<a href="javascript:void(0);" class="hpp_photographer text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },
            },
        ],
        fnDrawCallback: function() {
        },
    });
})

$(document).on('click','.hpp_photographer', function(){
    var data_row = table.row($(this).closest('tr')).data();
    var hpp_id = data_row.id;
    var message = "Are you sure?";
    $('#HPPDeleteModel').on('show.bs.modal', function(e){
        $('#hpp_id').val(hpp_id);
        $('#message').text(message);
    });
    $('#HPPDeleteModel').modal('show');
})

$(document).on('click','#deleteHPP', function(){
    var hpp_id = $('#hpp_id').val();
    $.ajax({
        url: window.location.href + '/delete',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            hpp_id: hpp_id,
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#HPPDeleteModel').modal('hide')
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#HPPDeleteModel').modal('hide')
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

$('#home_page_photographer_list').on('click', 'tbody .toggle-is-active-switch', function () {
    var is_active = ($(this).attr('aria-pressed') === 'true') ? 0 : 1;
    var data_row = table.row($(this).closest('tr')).data();
    var hpp_id = data_row.id;
    if($(this).attr('aria-pressed') == 'false')
    {
        $(this).addClass('active');
    }
    if($(this).attr('aria-pressed') == 'true')
    {
        $(this).removeClass('active');
    }
    $('#HPPActInactModel').on('show.bs.modal', function(e){
        $('#hpp_id').val(hpp_id);
        $('#is_active').val(is_active);
    });
    $('#HPPActInactModel').modal('show');
});

$(document).on('click', '#ActInactHPP', function(){
    var hpp_id = $('#hpp_id').val();
    var is_active = $('#is_active').val();
    $.ajax({
        url: window.location.href + '/act-inact',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            hpp_id: hpp_id,
            is_active: is_active,
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#HPPActInactModel').modal('hide')
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#HPPActInactModel').modal('hide')
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
});
// Function for show dimention by Nivedita (13-01-2021)
// Show image dimensions for banner image
var _URL = window.URL || window.webkitURL;
function _showLoadedBigImgDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#widthbigimg").text(imgwidth);
        $("#heightbigimg").text(imgheight);
        $("#loaded_bigimage_width").val(imgwidth);
        $("#loaded_bigimage_height").val(imgheight);
    }
};
// Show image dimensions for mobile image
var _URLMob = window.URL || window.webkitURL;
function _showLoadedSmallImgDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#widthsmallimg").text(imgwidth);
        $("#heightsmallimg").text(imgheight);
        $("#loaded_smallimage_width").val(imgwidth);
        $("#loaded_smallimage_height").val(imgheight);
    }
};
// Function end for show dimention by Nivedita (13-01-2021)
