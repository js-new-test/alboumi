
$(document).ready(function(){

    var lang_id = " ";
    ajaxCall.getPhotographers(lang_id);

    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterLangDiv").slideToggle('slow');
    });

    /** Filter Photographers */
    $('body').on('click', '#filter_photographers', function () {
        var lang_id = $('#filter_photographers').val();
        ajaxCall.getPhotographers(lang_id)
    });

    /** Active inactive Page */
    $('body').on('click', '.toggle-is-active-switch', function () {
        $('#photographerIdForActiveInactive').val($(this).attr('data'));
        $('#is_active').val($(this).attr('active'));
    });

    $('body').on('click', '#confirmActiveInactive', function () {
        ajaxCall.activeInactivePhotographer($('#photographerIdForActiveInactive').val(),$('#is_active').val())
    })

    /** Delete Photographer for multi lang */
    $('body').on('click', '.photographer_delete', function () {
        var photographerId = $(this).attr('data');
        $('#photographerDeleteModel').on('show.bs.modal', function(e){
            ajaxCall.loadDataTableForDeletePhotographer(photographerId);
        });
        $('#photographerDeleteModel').modal('show');
    });

    $('body').on('click', '.deletePhotographerLanguage',function () {
        var photographerDetailId = $(this).attr('data');
        $('#photographerDetailId').val(photographerDetailId);
        $('#photographerLangDeleteModel').modal('show');
        $('body').on('click', '#confirmDelete', function () {
            ajaxCall.deletePhotographer($('#photographerDetailId').val());
        });
    })

    /** Delete Photographer if only single lang exists */
    $('body').on('click', '.photographer_default_delete', function () {
        $('#photographerDetailId').val($(this).attr('data'));
        $('body').on('click', '#confirmDelete', function () {
            ajaxCall.deletePhotographer($('#photographerDetailId').val())
        })
	});
});

var ajaxCall = {
    getPhotographers : function (lang_id) {
        $('#photographers_listing').DataTable().destroy();
        $('#photographers_listing').DataTable({

        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {  
            $("#photographers_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        "ajax": {
            'type': 'get',
            'url': window.location.href + '/list',
            'data': {lang_id:lang_id}
        },
        'columnDefs': [{
            "targets": [0,4,5,6,7],
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
            "data":'name'
        },
        {
            "target": 3,
            "data":'experience'
        },

        {
            "target": 4,
            render: function (data, type, row)
                {
                    return "<img src=\"" + baseUrl + '/public/assets/images/photographers/' + row.profile_pic + "\" height=\"100\" width=\"100\"/>";
                },
        },
        {
            "target": 5,
            render: function (data, type, row)
            {
                var output = "";
                if(row.status == 1)
                {
                    output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#photographerActiveInactiveModel" data="'+row['id']+'" active="0">'
                    +'<div class="handle"></div>'
                    +'</button>'
                }
                else
                {
                    output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#photographerActiveInactiveModel" data="'+row['id']+'" active="1">'
                    +'<div class="handle"></div>'
                    +'</button>'
                }
                return output;
            },
        },
        {
            "target" : 6,
            render: function (data,type,row) {
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.p_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                }
                else
                {
                    return "-----"
                }
            }
        },

        {
            "target": -1,
            "bSortable": false,
            "order":false,
            "render": function ( data, type, row )
            {
                var output = "";
                output += '<a href="'+ window.location.href +'/edit/'+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                if(otherLanguages.length != 0)
                {
                    output += '<a class="text-danger"><i class="fa fa-trash photographer_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#photographerDeleteModel"></i></a>'
                }
                if(otherLanguages.length == 0)
                {
                    output += '<a class="text-danger"><i class="fa fa-trash photographer_default_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#photographerLangDeleteModel"></i></a>'
                }
                if(otherLanguages.length != 0)
                {
                    output += "<a href='"+ window.location.href +"/addPhotographer?page=anotherLanguage&photoId="+ row.id +"'> <button class='btn btn-primary ml-3'><i class='fa fa-plus'></i> Add In Another Language</button></a>";
                }
                return output;
            },
        }]
    })

    },

    activeInactivePhotographer : function (photographerId,is_active) {
        var origin = window.location.href;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: origin + '/activeDeactivePhotographer',
            method: "POST",
            data:{
                "is_active":is_active,
                "photographerId":photographerId
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#photographerActiveInactiveModel').modal('hide')
                    $('#photographers_listing').DataTable().ajax.reload();
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

    loadDataTableForDeletePhotographer: function (photographerId) {

        $('#tblDeletePhotographer').DataTable().destroy();
        $('#tblDeletePhotographer').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
                'type': 'get',
                'url': window.location.href + '/languageWisePhotographer',
                'data': {photographerId:photographerId}
            },
            columns: [{
                "target": 0,
                "data":'id'
            },{
                "target": 1,
                "data":'name'
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
                    return "<i style='color:red;' class='fas fa-trash btn deletePhotographerLanguage' data="+row['id']+"></i></a>";
                },
            }]
        });
    },    
    deletePhotographer: function (id) {
        console.log(id);
        $.ajax({
            type: "get",
            url:'photgraphers/deletePhotographer',
            data:{'photographerDetailId':id},
            success: function (response) {
                if (response['success'] == true) {
                    $('#photographerLangDeleteModel').modal('hide');
                    $('#photographerDeleteModel').modal('hide');
                    $('#photographers_listing').DataTable().ajax.reload();
                    toastr.success(response.message);
                }
            }
        });
    },
};


// Show image dimensions for profile pic
var _URL = window.URL || window.webkitURL;

function _showProfilePicDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#profile_width").text(imgwidth);
        $("#profile_height").text(imgheight);
        $("#profile_image_width").val(imgwidth);
        $("#profile_image_height").val(imgheight);
    }
};

// Show image dimensions for cover pic

function _showCoverPicDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#cover_width").text(imgwidth);
        $("#cover_height").text(imgheight);
        $("#cover_image_width").val(imgwidth);
        $("#cover_image_height").val(imgheight);
    }
};
