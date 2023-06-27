
$(document).ready(function(){

    var lang_id = " ";
    ajaxCall.getCmsPages(lang_id);

    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterLangDiv").slideToggle('slow');
    });

    /** Filter CMS Pages */
    $('body').on('click', '#filter_cms_page', function () {
        var lang_id = $('#filter_cms_page').val();
        ajaxCall.getCmsPages(lang_id)
    });

     /** Active inactive Page */
    $('body').on('click', '.toggle-is-active-switch', function () {
        $('#pageIdForActiveInactive').val($(this).attr('data'));
        $('#is_active').val($(this).attr('active'));
	});

	$('body').on('click', '#confirmActiveInactive', function () {
		ajaxCall.activeInactivePage($('#pageIdForActiveInactive').val(),$('#is_active').val())
    })

    /** Delete Page for multi lang */
    $('body').on('click', '.page_delete', function () {
        var pageId = $(this).attr('data');
        $('#pageDeleteModel').on('show.bs.modal', function(e){
            ajaxCall.loadDataTableForDeletePage(pageId);
        });
        $('#pageDeleteModel').modal('show');
    });

    $('body').on('click', '.deletePageLanguage',function () {
        var pageDetailId = $(this).attr('data');
        $('#pageDetailId').val(pageDetailId);
        $('#pageLangDeleteModel').modal('show');

        $('body').on('click', '#confirmDelete', function () {
            ajaxCall.deletePage($('#pageDetailId').val());
        });
    })

    /** Delete Page if only single lang exists */
    $('body').on('click', '.page_default_delete', function () {
        $('#pageDetailId').val($(this).attr('data'));

        $('body').on('click', '#confirmDelete', function () {
            ajaxCall.deletePage($('#pageDetailId').val())
        })
	});
});

var ajaxCall = {
    getCmsPages : function (lang_id) {
        $('#cms_listing').DataTable().destroy();
        $('#cms_listing').DataTable({

        processing: true,
        serverSide: true,
        // "scrollX": true,
        "initComplete": function (settings, json) {  
            $("#cms_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        "ajax": {
            'type': 'get',
            'url': window.location.href + '/list',
            'data': {lang_id:lang_id}
        },
        'columnDefs': [{
            "targets": [0,4,5,6],
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
            "data":'title'
        },
        {
            "target": 3,
            "data":'slug'
        },
        {
            "target": 4,
            "render": function (data, type, row, meta)
            {
                var output = "";
                if(row.status == 1)
                {
                    output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#pageActiveInactiveModel" data="'+row['id']+'" active="0">'
                    +'<div class="handle"></div>'
                    +'</button>'
                }
                else
                {
                    output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#pageActiveInactiveModel" data="'+row['id']+'" active="1">'
                    +'<div class="handle"></div>'
                    +'</button>'
                }
                return output;
            },
        },
        { data: 'zone', name: 'zone',
            render: function (data,type,row) {
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.ag_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                    output += '<a class="text-danger"><i class="fa fa-trash page_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#pageDeleteModel"></i></a>'
                }
                if(otherLanguages.length == 0)
                {
                    output += '<a class="text-danger"><i class="fa fa-trash page_default_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#pageLangDeleteModel"></i></a>'
                }
                if(otherLanguages.length != 0)
                {
                    output += "<a href='"+ window.location.href +"/addPage?page=anotherLanguage&pageId="+ row.id +"'> <button class='btn btn-primary ml-3'><i class='fa fa-plus'></i> Add In Another Language</button></a>";
                }
                return output;
            },
        }]
    })

    },

    activeInactivePage : function (pageId,is_active) {
        var origin = window.location.href;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: origin + '/activeDeactivePage',
            method: "POST",
            data:{
                "is_active":is_active,
                "pageId":pageId
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#pageActiveInactiveModel').modal('hide')
                    $('#cms_listing').DataTable().ajax.reload();
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

    loadDataTableForDeletePage: function (pageId) {

        $('#tblDeletePage').DataTable().destroy();
        $('#tblDeletePage').DataTable({
            processing: true,
            serverSide: true,
            // "scrollX": true,
            "initComplete": function (settings, json) {  
                $("#tblDeletePage").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            "ajax": {
                'type': 'get',
                'url': window.location.href + '/languageWisePage',
                'data': {pageId:pageId}
            },
            columns: [{
                "target": 0,
                "data":'id'
            },{
                "target": 1,
                "data":'pageName'
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
                    return "<i style='color:red;' class='fas fa-trash btn deletePageLanguage' data="+row['id']+"></i></a>";
                },
            }]
        });
    },

    deletePage: function (id) {
        $.ajax({
            type: "get",
            url:'cmsPages/deletePage',
            data:{'pageDetailId':id},
            success: function (response) {
                if (response['success'] == true) {
                    $('#pageLangDeleteModel').modal('hide');
                    $('#pageDeleteModel').modal('hide');
                    $('#cms_listing').DataTable().ajax.reload();
                    toastr.success(response.message);
                }
            }
        });
    },

}
// Functio for show dimention by Nivedita (11-01-2021)
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

        $("#width").text(imgwidth);
        $("#height").text(imgheight);
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
// Functio end for show dimention by Nivedita (11-01-2021)
