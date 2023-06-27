$( document ).ready(function() {
    init.handler();
    var lang_id = " ";
	ajaxCalls.loadDataTable(lang_id);
    
});

var init = {
    handler: function () {
        $('body').on('click', '#brandExport', function() {
            ajaxCalls.exportBrand();
        });

        $('body').on('click', '.deleteBrand', function () {
            var brandId = $(this).attr('data');
            $('#brandDeleteModel').on('show.bs.modal', function(e){
                ajaxCalls.loadDataTableForDeleteBrand(brandId);
            });
            $('#brandDeleteModel').modal('show');
        });

        $('body').on('click', '.deleteBrandLanguage',function () {
            var brandDetailId = $(this).attr('data');
            $('#brandDetailId').val(brandDetailId);
            $('#brandLanguageDeleteModel').modal('show');
        })

        $('body').on('click', '#confirmDelete', function () {
            ajaxCalls.deleteBrand($('#brandDetailId').val());
        });

        /** Delete Attribute if only single lang exists */
        $('body').on('click', '.deleteDefaultBrand', function () {
            $('#brandDetailId').val($(this).attr('data'));
        });

        // $('body').on('click', '#confirmDelete', function () {
        //     ajaxCalls.deleteBrand($('#brandDetailId').val())
        // })

        /** toggle active switch and show confirmation */
        $('body').on('click', 'tbody .toggleIsActive', function () {
            var isActive = ($(this).attr('aria-pressed') === 'true') ? 0 : 1;
            var brandId = $(this).attr('data');
            
            $('#confirmationModel').on('show.bs.modal', function(e){
                $('#brandId').val(brandId);
                $('#brandStatus').val(isActive);
            });
            $('#confirmationModel').modal('show');
        });

        $('body').on('click', '#confirmStatus', function () {
            var brandId = $('#brandId').val();
            var brandStatus = $('#brandStatus').val();
            ajaxCalls.activeInactiveBrand(brandId, brandStatus);
        });

        $('body').on('click', '#divFilterToggle', function () {
            $("#FilterLangDiv").slideToggle('slow');
        });
    
        /** Filter CMS Pages */
        $('body').on('click', '#filter_brands', function () {
            var lang_id = $('#filter_brands').val();
            ajaxCalls.loadDataTable(lang_id)
        });
        
    }
}

var ajaxCalls = {
	loadDataTable: function (lang_id) {
		$('#tableManufacturers').DataTable().destroy();
		$('#tableManufacturers').DataTable({
            processing: true,
            serverSide: true,
            "initComplete": function (settings, json) {  
                $("#tableManufacturers").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            "ajax": {
                "url" : '../admin/manufacturers/list',
                'data': {lang_id:lang_id}
            },
            columns: [{
            	"target": 0,
                "visible": false,
            	"data":'id'
            },{
            	"target": 1,
            	"data":'brandName'
            },{
            	"target": 1,
            	"data":'slug'
            },{
                "target": 2,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) {
                    return "<img src='" + baseUrl + "/public/assets/images/brands/"+row['imageName']+"' class='rounded' style='height:50px; width:50px;'>";
                }
            },{
                "target": 3,
                render: function (data, type, row)
                {
                    var output = '<button type="button" class="btn btn-sm btn-toggle toggleIsActive" data-toggle="button" data-target="confirmationModel" aria-pressed="false" autocomplete="off" data='+row['id']+'>'
                        +'<div class="handle"></div>'
                        +'</button>';
                    if(row.status == 'Active') {
                        output = '<button type="button" class="btn btn-sm btn-toggle active toggleIsActive" data-toggle="button" data-target="confirmationModel" aria-pressed="true" autocomplete="off" data='+row['id']+'>'
                        +'<div class="handle"></div>'
                        +'</button>'
                    }
                    return output;
                },
            }
            /*{
            	"target": 1,
            	"data":'status'
            }*/,
            { data: 'mfg_created_at', name: 'mfg_created_at',
                render: function (data,type,row) {
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.mfg_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }
                    
                }
            },
            /*{
                "target": 3,
                "data":'status'
            },*/{
                "target": -1,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
                    var output = '';
                    output += "<a href='"+ window.location.href +"/edit/"+ row.id + "'><i class='fas fa-edit'></i></a> &nbsp &nbsp"
                    if(otherLanguages.length == 0)
                    {
                        output += '<a class="text-danger"><i class="fa fa-trash deleteDefaultBrand" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#brandLanguageDeleteModel"></i></a>'
                    }
                    if(otherLanguages.length != 0)
                    {
                        output += '<a class="text-danger"><i class="fa fa-trash deleteBrand" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#brandDeleteModel"></i></a>'

                        output += "<a href='"+ window.location.href +"/add?page=anotherLanguage&brandId="+ row.id +"'> <button class='btn btn-primary ml-3'><i class='fa fa-plus'></i> Add Another Language</button></a>";
                    }
                    return output;

                },
            }]
        });
	},

    loadDataTableForDeleteBrand: function (brandId) {
        $('#tblDeleteBrand').DataTable().destroy();
        $('#tblDeleteBrand').DataTable({
            processing: true,
            serverSide: true,
            "initComplete": function (settings, json) {  
                $("#tblDeleteBrand").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            "ajax": {
                'type': 'get',
                'url': baseUrl+'/admin/manufacturers/languageWiseBrand',
                'data': {brandId:brandId}
            },
            columns: [{
                "target": 0,
                "data":'id'
            },{
                "target": 1,
                "data":'brandName'
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
                    return "<i style='color:red;' class='fas fa-trash btn deleteBrandLanguage' data="+row['id']+"></i></a>";
                },
            }]
        });
    },

    deleteBrand: function (id) {
        $.ajax({
                type: "get",
                url:'manufacturers/deleteBrand',
                data:{'brandDetailId':id},
                success: function (response) {
                    if (response['success'] == true) {
                        $('#brandLanguageDeleteModel').modal('hide');
                        $('#brandDeleteModel').modal('hide');
                        toastr.success(response.message);
                        ajaxCalls.loadDataTable($('#filter_brands').val());
                    }
                }
            });
    },

    exportBrand: function () {
        $.ajax({
                type: "get",
                url:'manufacturers/export',
                success: function (response) {
                    if (response === "brands.csv") {
                        window.location.href = '../brands.csv';
                    }
                }
            });
    },

    activeInactiveBrand : function (brandId, brandStatus) {
        $.ajax({
                type: "get",
                url:baseUrl + '/admin/manufacturers/updateStatus',
                data:{'brandId':brandId, 'brandStatus':brandStatus},
                success: function (result) {
                    if (result['success'] == true) {
                        toastr.success(result.message);
                        $("#confirmationModel").modal('hide');
                        ajaxCalls.loadDataTable($('#filter_brands').val())
                    }
                }
            });
    }
}
