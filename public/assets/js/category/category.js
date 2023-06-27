$(document).ready(function(){

    var lang_id = $('#langId').val();
    $('#filter_category').val(lang_id);
    ajaxCall.getCategories(lang_id);

    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterLangDiv").slideToggle('slow');
    });

    /** Filter Category */
    $('body').on('click', '#filter_cat', function () {
        lang_id = $('#filter_category').val();
        $('#langId').val(lang_id);
        ajaxCall.getCategories(lang_id)
    });

     /** Active inactive Category */
    $('body').on('click', '.toggle-is-active-switch', function () {
        $('#categoryIdForActiveInactive').val($(this).attr('data'));
        $('#is_active').val($(this).attr('active'));
	});

	$('body').on('click', '#confirmActiveInactive', function () {
		ajaxCall.activeInactiveCategory($('#categoryIdForActiveInactive').val(),$('#is_active').val())
    })

    /** Delete Category for multi lang */
    $('body').on('click', '.category_delete', function () {
        var categoryId = $(this).attr('data');
        $('#categoryDeleteModel').on('show.bs.modal', function(e){
            ajaxCall.loadDataTableForDeleteCategory(categoryId);
        });
        $('#categoryDeleteModel').modal('show');
    });

    $('body').on('click', '.deleteCategoryLanguage',function () {
        var categoryDetailId = $(this).attr('data');
        $('#categoryDetailId').val(categoryDetailId);
        $('#categoryLangDeleteModel').modal('show');
    })

    /** Delete Category if only single lang exists */
    $('body').on('click', '.category_default_delete', function () {
        $('#categoryDetailId').val($(this).attr('data'));
    });

    $('body').on('click', '#confirmDelete', function () {
        ajaxCall.deleteCategory($('#categoryDetailId').val())
    })
});

var ajaxCall = {
    getCategories : function (lang_id) {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        var catId = urlParams.get('catId')

        if(catId == null)
        {
            url = baseUrl + '/admin/categories/list'
        }
        else
        {
            url = baseUrl + '/admin/categories/list?catId='+ catId
        }

        $('#category_listing').DataTable().destroy();
        $('#category_listing').DataTable({

        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {  
            $("#category_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        "ajax": {
            'type': 'get',
            'url': url,
            'data': {lang_id:lang_id}
        },
        'columnDefs': [{
            "targets": [0,3,5,6,7],
            "className": "text-center",
        },
           {
               "targets": [1],
               "visible": false
           }
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
            "render": function ( data, type, row )
            {
                var output = "";
                var lang_id = $('#langId').val();
                if(catId != null)
                {
                    if(row.flag_product == 1)
                        output += row.title
                    else
                        output += '<a href="'+ baseUrl + '/admin/categories' + '?catId='+ row.id + '&langId=' + lang_id + '">'+ row.title +'</a>&nbsp;&nbsp;'
                }
                else
                {
                    if(row.flag_product == 1)
                        output += row.title
                    else
                        output += '<a href="'+ window.location.href +'?catId='+ row.id + '&langId=' + lang_id + '">'+ row.title +'</a>&nbsp;&nbsp;'
                }

                return output;
            },
        },
        {
            "target": 4,
            render: function (data, type, row)
                {
                    return "<img src=\"" + baseUrl + '/public/assets/images/categories/' + row.category_image + "\" height=\"80\" width=\"80\"/>";
                },
        },
        {
            "target": 4,
            "data":'slug'
        },
        {
            "target": 4,
            "data":'sort_order'
        },
        {
            "target": 5,
            "render": function (data, type, row, meta)
            {
                var output = "";
                if(row.status == 1)
                {
                    output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#categoryActiveInactiveModel" data="'+row['id']+'" active="0">'
                    +'<div class="handle"></div>'
                    +'</button>'
                }
                else
                {
                    output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#categoryActiveInactiveModel" data="'+row['id']+'" active="1">'
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
                if(catId != null)
                    output += '<a href="'+ baseUrl +'/admin/categories/edit/'+ row.id +'?catId='+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                else
                    output += '<a href="'+ window.location.href +'/edit/'+ row.id +'?catId='+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'

                if(otherLanguages.length != 0)
                {
                    output += '<a class="text-danger"><i class="fa fa-trash category_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#categoryDeleteModel"></i></a>'
                }
                if(otherLanguages.length == 0)
                {
                    output += '<a class="text-danger"><i class="fa fa-trash category_default_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#categoryLangDeleteModel"></i></a>'
                }
                if(otherLanguages.length != 0)
                {
                    if(catId != null)
                        output += "<a href='"+ baseUrl + '/admin/categories' +"/addCategory?page=anotherLanguage&catId="+ row.id +"'> <button class='btn btn-primary ml-3'><i class='fa fa-plus'></i> Add In Another Language</button></a>";
                    else
                        output += "<a href='"+ window.location.href +"/addCategory?page=anotherLanguage&catId="+ row.id +"'> <button class='btn btn-primary ml-3'><i class='fa fa-plus'></i> Add In Another Language</button></a>";
                }
                return output;
            },
        }]
    })

    },

    activeInactiveCategory : function (catId,is_active) {

        if(catId == null)
        {
            url = baseUrl + '/admin/categories/activeDeactiveCategory'
        }
        else
        {
            url = baseUrl + '/admin/categories/activeDeactiveCategory?catId='+ catId
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: url,
            method: "POST",
            data:{
                "is_active":is_active,
                "catId":catId
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#categoryActiveInactiveModel').modal('hide')
                    $('#category_listing').DataTable().ajax.reload();
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

    loadDataTableForDeleteCategory: function (catId) {
        if(catId == null)
        {
            url = baseUrl + '/admin/categories/languageWiseCategory'
        }
        else
        {
            url = baseUrl + '/admin/categories/languageWiseCategory?catId='+ catId
        }

        $('#tblDeleteCategory').DataTable().destroy();
        $('#tblDeleteCategory').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
                'type': 'get',
                'url': url,
                'data': {catId:catId}
            },
            columns: [{
                "target": 0,
                "data":'id'
            },{
                "target": 1,
                "data":'catName'
            },{
                "target": 2,
                "render": function ( data, type, row ) {
                    var language = row['languageName'];
                    if (row['isDefault'] == 1) {
                        language = row['languageName'] +" (Default)";
                    }
                    return language;
                },
            },{
                "target": -1,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) {
                    return "<i style='color:red;' class='fas fa-trash btn deleteCategoryLanguage' data="+row['id']+"></i></a>";
                },
            }]
        });
    },

    deleteCategory: function (id) {
        $.ajax({
            type: "get",
            url:'categories/deleteCategory',
            data:{'categoryDetailId':id},
            success: function (response) {
                if (response['success'] == true) {
                    $('#categoryLangDeleteModel').modal('hide');
                    $('#categoryDeleteModel').modal('hide');
                    $('#category_listing').DataTable().ajax.reload();
                    toastr.success(response.message);
                }
            }
        });
    },
}

// Generate slug based on title entered
function generateSlug()
{
    var titleValue = $("#title").val();
    $("#slug").val(titleValue.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-').toLowerCase());
}

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
        $("#loaded_image_width").val(imgwidth);
        $("#loaded_image_height").val(imgheight);
    }
};
// Show image dimensions for banner image
var _URL = window.URL || window.webkitURL;
function _showLoadedBannerDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#bannerwidth").text(imgwidth);
        $("#bannerheight").text(imgheight);
        $("#loaded_banner_width").val(imgwidth);
        $("#loaded_banner_height").val(imgheight);
    }
};
// Show image dimensions for mobile image
var _URLMob = window.URL || window.webkitURL;
function _showLoadedMobileBannerDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#mobilebannerwidth").text(imgwidth);
        $("#mobilebannerheight").text(imgheight);
        $("#loaded_mobile_banner_width").val(imgwidth);
        $("#loaded_mobile_banner_height").val(imgheight);
    }
};
