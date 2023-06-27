$(document).ready(function(){

    ajaxCall.getMegamenu();

    /** Delete megamenu */
    $('body').on('click', '.menu_delete', function () {
		$('#menuIdForDelete').val($(this).attr('data'));
	});

	$('body').on('click', '#confirmDelete', function () {
		ajaxCall.deleteMenu($('#menuIdForDelete').val())
    })
}); 

    var ajaxCall = {
        getMegamenu : function (lang_id) {
            $('#megamenu_listing').DataTable().destroy();
            $('#megamenu_listing').DataTable({

            processing: true,
            serverSide: true,
            "scrollX": true,
            "ajax": {
                'type': 'get',
                'url': window.location.href + '/list',
            },
            "order": [[ 5, "asc" ]],
            columnDefs: [
                {
                    "targets": [0,1,4,5,6],
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
                render: function (data, type, row) 
                {
                    if(row.type == 0)
                        return "CMS"
                    else
                        return "Category"
                }
            },
            {
                "target": 3,
                "data":"name"
            },
            { 
                "target": 4,
                render: function (data, type, row) 
                {
                    if(row.small_image != null)
                    {
                        return "<img src=\"" + baseUrl + '/public/assets/images/megamenu/small/' + row.small_image + "\" height=\"80\" width=\"80\"/>";
    
                    }
                    else
                    {
                        return "<img src=\"" + baseUrl + '/public/assets/images/no_image.png' + "\" height=\"80\" width=\"80\"/>";
                    }
                },
            },
            {
                "target": 5,
                "data":'sort_order'
            },
            {
                "target": -1,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) 
                {
                    var output = "";
                    output += '<a href="'+ window.location.href +'/editMenu/'+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                    output += '<a class="text-danger"><i class="fa fa-trash menu_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#menuDeleteModel"></i></a>'
                    return output;
                },
            }
            ]
        })

        },

        deleteMenu : function (menu_id) {
            var origin = window.location.href;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: origin + '/' + menu_id + '/deleteMenu',
                method: "POST",    
                data: {
                    menu_id: menu_id,
                },            
                success: function(response)
                {
                    if(response.status == 'true')
                    {
                        $('#menuDeleteModel').modal('hide')
                        $('#megamenu_listing').DataTable().ajax.reload();
                        toastr.clear();
                        toastr.options.closeButton = true;
                        toastr.options.timeOut = 0;
                        toastr.success(response.msg);
                    }
                    else
                    {
                        $('#menuDeleteModel').modal('hide')                  
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

// Show image dimensions for service image
var _URL = window.URL || window.webkitURL;

function _showSmallImageDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#small_width").text(imgwidth);
        $("#small_height").text(imgheight);   
        $("#small_image_width").val(imgwidth);
        $("#small_image_height").val(imgheight);                             
    }    
};

function _showBigImageDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#big_width").text(imgwidth);
        $("#big_height").text(imgheight);   
        $("#big_image_width").val(imgwidth);
        $("#big_image_height").val(imgheight);                             
    }    
};

function _showIconImageDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#icon_width").text(imgwidth);
        $("#icon_height").text(imgheight);   
        $("#icon_image_width").val(imgwidth);
        $("#icon_image_height").val(imgheight);                             
    }    
};