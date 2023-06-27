
$(document).ready(function(){

    var lang_id = " ";
    ajaxCall.getAttributes(lang_id);

    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterLangDiv").slideToggle('slow');
    });

    /** Filter attributes */
    $('body').on('click', '#filter_group', function () {
        var lang_id = $('#filter_group').val();
        ajaxCall.getAttributes(lang_id)
    });

    $('#colorBox').hide();
    $('#imageBox').hide();

    /** Delete Attribute for multi lang */
    $('body').on('click', '.attr_delete', function () {
        var attrId = $(this).attr('data');
        $('#attrDeleteModel').on('show.bs.modal', function(e){
            ajaxCall.loadDataTableForDeleteAttr(attrId);
        });
        $('#attrDeleteModel').modal('show');
    });

    $('body').on('click', '.deleteAttrLanguage',function () {
        var attrDetailId = $(this).attr('data');
        $('#attrDetailId').val(attrDetailId);
        $('#attrLangDeleteModel').modal('show');

        $('body').on('click', '#confirmDelete', function () {
            ajaxCall.deleteAttribute($('#attrDetailId').val());
        });
    })

    /** Delete Attribute if only single lang exists */
    $('body').on('click', '.attr_default_delete', function () {
        $('#attrDetailId').val($(this).attr('data'));
        
        $('body').on('click', '#confirmDelete', function () {
            ajaxCall.deleteAttribute($('#attrDetailId').val())
        })
	});

    // show inputs based on attribute group change
    $('#attribute_group_id').on('change', function() {
        ajaxCall.showInputsForAttrType($(this).val());
    })

    /** Active inactive Attribute */
    $('body').on('click', '.toggle-is-active-switch', function () {
        $('#attrActiveInactiveModel').val($(this).attr('data'));
        $('#is_active').val($(this).attr('active'));
	});

	$('body').on('click', '#confirmActiveInactive', function () {
		ajaxCall.activeInactiveAttribute($('#attrActiveInactiveModel').val(),$('#is_active').val())
    })
}); 

var ajaxCall = {
    getAttributes : function (lang_id) {
        $('#attribute_listing').DataTable().destroy();
        $('#attribute_listing').DataTable({

        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {  
            $("#attribute_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
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
            "data":'display_name'
        },
        {
            "target": 2,
            "data":'group_name'
        },
        {
            "target": 3,
            "data":'name'
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
                    output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#attrActiveInactiveModel" data="'+row['id']+'" active="0">'
                    +'<div class="handle"></div>'
                    +'</button>'
                }
                else
                {
                    output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#attrActiveInactiveModel" data="'+row['id']+'" active="1">'
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
                    output += '<a class="text-danger"><i class="fa fa-trash attr_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#attrDeleteModel"></i></a>'
                }
                if(otherLanguages.length == 0)
                {
                    output += '<a class="text-danger"><i class="fa fa-trash attr_default_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#attrLangDeleteModel"></i></a>'
                }
                if(otherLanguages.length != 0)
                {
                    output += "<a href='"+ window.location.href +"/addAttribute?page=anotherLanguage&attributeId="+ row.id +"'> <button class='btn btn-primary ml-3'><i class='fa fa-plus'></i> Add In Another Language</button></a>";
                }
                return output;
            },
        }]
    })

    },

    loadDataTableForDeleteAttr: function (attrId) {
        $('#tblDeleteAttr').DataTable().destroy();
        $('#tblDeleteAttr').DataTable({
            processing: true,
            serverSide: true,
            "initComplete": function (settings, json) {  
                $("#tblDeleteAttr").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            "ajax": {
                'type': 'get',
                'url': window.location.href + '/languageWiseAttr',
                'data': {attrId:attrId}
            },
            columns: [{
                "target": 0,
                "data":'id'
            },{
                "target": 1,
                "data":'attrName'
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
                    return "<i style='color:red;' class='fas fa-trash btn deleteAttrLanguage' data="+row['id']+"></i></a>";
                },
            }]
        });
    },

    deleteAttribute: function (id) {
        $.ajax({
            type: "get",
            url:'attribute/deleteAttribute',
            data:{'attrDetailId':id},
            success: function (response) {
                if (response['success'] == true) {
                    $('#attrLangDeleteModel').modal('hide');
                    $('#attrDeleteModel').modal('hide');
                    $('#attribute_listing').DataTable().ajax.reload();
                    toastr.success(response.message);
                }
            }
        });
    },

    showInputsForAttrType:function(attributeGroupId){
        jQuery.ajax({
            type: "get",
            url: 'getAttributeType/' + attributeGroupId,
            async: true,
            dataType: 'json',
            success: function (response) 
            {
                if(response.status == true)
                {
                    if(response.attr_type.code == 'C')
                    {
                        $('input[id*=color').addClass('colorpicker-default');
                        $('.multi').show();
                        $('.colorpicker-default').colorpicker();
                        $('#imageBox').hide();
                        $('#colorBox').show();
                        $('#color').attr('required', 'true');

                    }
                    else if(response.attr_type.code == 'I')
                    {
                        $('#colorBox').hide();
                        $('#imageBox').show();
                        $('#image').attr('required', 'true');
                    }
                    else if(response.attr_type.code == 'D' || response.attr_type.code == 'R')
                    {
                        $('#existingcolorpickerBox').hide();
                        $('#selected_attr_image').hide();
                        $('#existingImage').hide();
                        $('#colorBox').hide();
                        $('#imageBox').hide();
                    }
                }
            }
        });
    },

    activeInactiveAttribute : function (attributeId,is_active) {      
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: baseUrl + '/admin/attribute/activeInactiveAttribute',
            method: "POST",
            data:{
                "is_active":is_active,
                "attributeId":attributeId
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#attrActiveInactiveModel').modal('hide')
                    $('#attribute_listing').DataTable().ajax.reload();
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
}
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
