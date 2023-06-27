var _URL = window.URL || window.webkitURL;
   
$('#banner_image').change(function () {
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
        $("#loaded_banner_image_width").val(imgwidth);
        $("#loaded_banner_image_height").val(imgheight);                                    
    }    
}); 

$("#hotItWorksBnnr").validate( {
    rules: { 
        language: {
            required: true,
        },
        banner_image : {
            required: true,
            extension: "jpg|jpeg|png"
        },     
        edit_banner_image: {
            extension: "jpg|jpeg|png"
        }                                                                          
    },
    messages: { 
        language: {
            required: "Language field is required",
        },     
        banner_image:{                    
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        edit_banner_image: {
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        }                       
    },
    errorPlacement: function ( error, element ) {        
        if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.next( "label" ) );
        } else {
            error.insertAfter( element );
        }
    },    
} );

var table = $('#how_it_works_bnr_list').DataTable({
    processing: true,
    serverSide: true,
    // order: [[ 4, "desc" ]],
    ajax: {
        "url": window.location.href,
        "type": "GET"
    },
    columns: [
        {data: 'image', name: 'image', 
            render: function(data,type,row){
                return '<img height="80" width="80" src="'+window.location.href +'/../../public/assets/images/how-it-works-banner/'+row.image+'">';
            },
        },          
        { data: 'hiwb_created_at', name: 'hiwb_created_at',
            render: function (data,type,row) {                    
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.hiwb_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                }
                else
                {
                    return "-----"
                }
                
            }
        },                        
        {data: 'id', name: 'action', // orderable: true, // searchable: true
            render: function(data, type, row)
            {                    
                var output = "";
                output += '<a href="'+window.location.href+'/edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                output += '<a href="javascript:void(0);" class="hitwb_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                return output;
            },                
        },            
    ],
    fnDrawCallback: function() {
    },
});

$(document).on('click','.hitwb_delete', function(){
    var data_row = table.row($(this).closest('tr')).data();  
    var hitwb_id = data_row.id;    
    var message = "Are you sure?";         
    $('#HITWBDeleteModel').on('show.bs.modal', function(e){
        $('#hitwb_id').val(hitwb_id);            
        $('#message').text(message);
    });
    $('#HITWBDeleteModel').modal('show'); 
})

$(document).on('click','#deleteHITWB', function(){
    var hitwb_id = $('#hitwb_id').val(); 
    $.ajax({
        url: window.location.href + '/delete',
        method: "POST",    
        data: {
            "_token": $('#token').val(),
            hitwb_id: hitwb_id,
        },            
        success: function(response)
        {                
            if(response.status == 'true')
            {
                $('#HITWBDeleteModel').modal('hide')
                table.ajax.reload();                    
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#HITWBDeleteModel').modal('hide')
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