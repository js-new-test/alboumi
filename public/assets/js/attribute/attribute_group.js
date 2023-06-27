
$(document).ready(function(){

    var lang_id = " ";
    ajaxCall.getAttributeGroups(lang_id);

    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterLangDiv").slideToggle('slow');
    });

    /** Filter attribute groups */
    $('body').on('click', '#filter_attr_group', function () {
        var lang_id = $('#filter_attr_group').val();
        console.log(lang_id);
        ajaxCall.getAttributeGroups(lang_id)
    });

    /** Delete Attribute group for multi lang */
    $('body').on('click', '.attr_group_delete', function () {
        var attrGroupId = $(this).attr('data');
        $('#attrGroupDeleteModel').on('show.bs.modal', function(e){
            ajaxCall.loadDataTableForDeleteAttrGroup(attrGroupId);
        });
        $('#attrGroupDeleteModel').modal('show');
    });

    $('body').on('click', '.deleteAttrGroupLanguage',function () {
        var attrGroupDetailId = $(this).attr('data');
        $('#attrGroupDetailId').val(attrGroupDetailId);
        $('#attrGroupLangDeleteModel').modal('show');

        $('body').on('click', '#confirmDelete', function () {
            ajaxCall.deleteAttributeGroup($('#attrGroupDetailId').val());
        });
    })

    /** Delete Attribute group if only single lang exists */
    $('body').on('click', '.attr_group_default_delete', function () {
        $('#attrGroupDetailId').val($(this).attr('data'));

        $('body').on('click', '#confirmDelete', function () {
            ajaxCall.deleteAttributeGroup($('#attrGroupDetailId').val())
        })
    });

     /** Active inactive Attribute group */
     $('body').on('click', '.toggle-is-active-switch', function () {
        $('#attrGroupActiveInactiveModel').val($(this).attr('data'));
        $('#is_active').val($(this).attr('active'));
	});

	$('body').on('click', '#confirmActiveInactive', function () {
		ajaxCall.activeInactiveAttrGroup($('#attrGroupActiveInactiveModel').val(),$('#is_active').val())
    })

}); 

var ajaxCall = {
    getAttributeGroups : function (lang_id) {
        $('#attribute_group_listing').DataTable().destroy();
        $('#attribute_group_listing').DataTable({

        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {  
            $("#attribute_group_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
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
                    output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#attrGroupActiveInactiveModel" data="'+row['id']+'" active="0">'
                    +'<div class="handle"></div>'
                    +'</button>'
                }
                else
                {
                    output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#attrGroupActiveInactiveModel" data="'+row['id']+'" active="1">'
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
                    output += '<a class="text-danger"><i class="fa fa-trash attr_group_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#attrGroupDeleteModel"></i></a>'
                }
                if(otherLanguages.length == 0)
                {
                    output += '<a class="text-danger"><i class="fa fa-trash attr_group_default_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#attrGroupLangDeleteModel"></i></a>'
                }
                if(otherLanguages.length != 0)
                {
                    output += "<a href='"+ window.location.href +"/addAttributeGroup?page=anotherLanguage&groupId="+ row.id +"'> <button class='btn btn-primary ml-3'><i class='fa fa-plus'></i> Add In Another Language</button></a>";
                }
                return output;
            },
        }]
    })

    },

    loadDataTableForDeleteAttrGroup: function (attrGroupId) {
        $('#tblDeleteAttrGroup').DataTable().destroy();
        $('#tblDeleteAttrGroup').DataTable({
            processing: true,
            serverSide: true,
            "initComplete": function (settings, json) {  
                $("#tblDeleteAttrGroup").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            "ajax": {
                'type': 'get',
                'url': window.location.href + '/languageWiseAttrGroup',
                'data': {attrGroupId:attrGroupId}
            },
            columns: [{
                "target": 0,
                "data":'id'
            },{
                "target": 1,
                "data":'attrGroupName'
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
                    return "<i style='color:red;' class='fas fa-trash btn deleteAttrGroupLanguage' data="+row['id']+"></i></a>";
                },
            }]
        });
    },

    deleteAttributeGroup: function (id) {
        $.ajax({
            type: "get",
            url:'attributeGroup/deleteAttributeGroup',
            data:{'attrGroupDetailId':id},
            success: function (response) {
                if (response['success'] == true) {
                    $('#attrGroupLangDeleteModel').modal('hide');
                    $('#attrGroupDeleteModel').modal('hide');
                    $('#attribute_group_listing').DataTable().ajax.reload();
                    toastr.success(response.message);
                }
            }
        });
    },
    activeInactiveAttrGroup : function (attrGroupId,is_active) {      
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: baseUrl + '/admin/attributeGroup/activeInactiveAttrGroup',
            method: "POST",
            data:{
                "is_active":is_active,
                "attrGroupId":attrGroupId
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#attrGroupActiveInactiveModel').modal('hide')
                    $('#attribute_group_listing').DataTable().ajax.reload();
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
