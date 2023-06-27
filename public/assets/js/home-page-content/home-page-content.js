
var _URL = window.URL || window.webkitURL;
   
$('#image_1').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#width_1").text(imgwidth);
        $("#height_1").text(imgheight);   
        $("#loaded_image_width_1").val(imgwidth);
        $("#loaded_image_height_1").val(imgheight);                                    
    }    
}); 

$('#image_2').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#width_2").text(imgwidth);
        $("#height_2").text(imgheight);   
        $("#loaded_image_width_2").val(imgwidth);
        $("#loaded_image_height_2").val(imgheight);                                    
    }    
});

$('#mobile_image_1').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#mobile_width_1").text(imgwidth);
        $("#mobile_height_1").text(imgheight);   
        $("#loaded_mobile_image_width_1").val(imgwidth);
        $("#loaded_mobile_image_height_1").val(imgheight);                                    
    }    
});

$('#mobile_image_2').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#mobile_width_2").text(imgwidth);
        $("#mobile_height_2").text(imgheight);   
        $("#loaded_mobile_image_width_2").val(imgwidth);
        $("#loaded_mobile_image_height_2").val(imgheight);                                    
    }    
});

$('#image_1_update').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#width_1").text(imgwidth);
        $("#height_1").text(imgheight);   
        $("#loaded_image_width_1").val(imgwidth);
        $("#loaded_image_height_1").val(imgheight);                                    
    }    
}); 

$('#image_2_update').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#width_2").text(imgwidth);
        $("#height_2").text(imgheight);   
        $("#loaded_image_width_2").val(imgwidth);
        $("#loaded_image_height_2").val(imgheight);                                    
    }    
});

$('#mobile_image_1_update').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#mobile_width_1").text(imgwidth);
        $("#mobile_height_1").text(imgheight);   
        $("#loaded_mobile_image_width_1").val(imgwidth);
        $("#loaded_mobile_image_height_1").val(imgheight);                                    
    }    
});

$('#mobile_image_2_update').change(function () {
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;
        
        $("#mobile_width_2").text(imgwidth);
        $("#mobile_height_2").text(imgheight);   
        $("#loaded_mobile_image_width_2").val(imgwidth);
        $("#loaded_mobile_image_height_2").val(imgheight);                                    
    }    
});

$("#homePageContentForm").validate( {
    rules: {         
        language : {
            required: true,
        },
        title : {
            required: true,
        },           
        description: {
            required: true,
        },
        link: {
            required: true,
        },
        link_2: {
            required: true,
        },
        image_text_1:{
            required: true,
        },
        image_1:{
            required: true,
            extension: "jpg|jpeg|png"
        },
        image_text_2:{
            required: true,
        },
        image_2:{
            required: true,
            extension: "jpg|jpeg|png"
        },
        mobile_image_1:{
            required: true,
            extension: "jpg|jpeg|png"
        },
        mobile_image_2:{
            required: true,
            extension: "jpg|jpeg|png"
        },
        image_1_update:{
            extension: "jpg|jpeg|png"
        },
        image_2_update:{
            extension: "jpg|jpeg|png"
        },
        mobile_image_1_update:{
            extension: "jpg|jpeg|png"
        },
        mobile_image_2_update:{
            extension: "jpg|jpeg|png"
        }                                                               
    },
    messages: {  
        language : {
            required: 'Language is required',
        },
        title : {
            required: 'Title is required',
        },
        description : {
            required: 'Description is required',
        },
        link : {
            required: 'Link 1 is required',
        },
        link_2 : {
            required: 'Link 2 is required',
        },
        image_text_1 : {
            required: 'Image Text 1 is required',
        },
        image_1 : {
            required: 'Please upload image 1',
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        image_text_2 : {
            required: 'Image Text 2 is required',
        },
        image_2 : {
            required: 'Please upload image 2',
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        mobile_image_1 : {
            required: 'Please upload image 1',
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        mobile_image_2 : {
            required: 'Please upload image 2',
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        image_1_update:{
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        image_2_update:{
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        mobile_image_1_update:{
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        mobile_image_2_update:{
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        }                                            
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

var table = $('#home_page_content_list').DataTable({
    processing: true,
    serverSide: true,
    // "scrollX": true,
    "initComplete": function (settings, json) {  
        $("#home_page_content_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
    },
    order: [[ 4, "desc" ]],
    ajax: {
        "url": window.location.href,
        "type": "GET"
    },
    columns: [
        {data: 'langName', name: 'langName'},
        {data: 'title', name: 'title'},
        {data: 'description', name: 'description'},
        {data: 'link', name: 'link'},
        {data: 'image_text_1', name: 'image_text_1'},
        {data: 'image_1', name: 'image_1',
            render: function(data,type,row){
                return '<img height="80" width="80" src="'+window.location.href +'/../../public/assets/images/home-page-content/desktop/'+row.image_1+'">';
            },
        },
        {data: 'image_text_2', name: 'image_text_2'},
        {data: 'image_2', name: 'image_2',
            render: function(data,type,row){
                return '<img height="80" width="80" src="'+window.location.href +'/../../public/assets/images/home-page-content/desktop/'+row.image_2+'">';
            },
        },        
        { data: 'zone', name: 'zone',
            render: function (data,type,row) {                    
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.hpc_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                output += '<a href="javascript:void(0);" class="hpc_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                return output;
            },                
        },            
    ],
    fnDrawCallback: function() {
    },
});

$(document).on('click','#filter_HPC', function(){
    var token = $('input[name="_token"]').val();
    var filter_HPC_lang = $('#filter_HPC_lang').val();    
    $('#home_page_content_list').dataTable().fnDestroy(); 
    table = $('#home_page_content_list').DataTable({
        processing: true,
        serverSide: true,
        // "scrollX": true,
        "initComplete": function (settings, json) {  
            $("#home_page_content_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        ajax: {
            url: window.location.href + '/filter',
            method: "POST",
            data:{
                _token : token,
                filter_HPC_lang:filter_HPC_lang
            },
        },
        columns: [
            {data: 'langName', name: 'langName'},
            {data: 'title', name: 'title'},
            {data: 'description', name: 'description'},
            {data: 'link', name: 'link'},
            {data: 'image_text_1', name: 'image_text_1'},
            {data: 'image_1', name: 'image_1',
                render: function(data,type,row){
                    return '<img height="80" width="80" src="'+window.location.href +'/../../public/assets/images/home-page-content/desktop/'+row.image_1+'">';
                },
            },
            {data: 'image_text_2', name: 'image_text_2'},
            {data: 'image_2', name: 'image_2',
                render: function(data,type,row){
                    return '<img height="80" width="80" src="'+window.location.href +'/../../public/assets/images/home-page-content/desktop/'+row.image_2+'">';
                },
            },            
            { data: 'zone', name: 'zone',
                render: function (data,type,row) {                    
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.hpc_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
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
                    output += '<a href="javascript:void(0);" class="hpc_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },                
            },            
        ],
        fnDrawCallback: function() {
        },
    });               
})

$(document).on('click','.hpc_delete', function(){
    var data_row = table.row($(this).closest('tr')).data();  
    var hpc_id = data_row.id;    
    var message = "Are you sure?";         
    $('#HPCDeleteModel').on('show.bs.modal', function(e){
        $('#hpc_id').val(hpc_id);            
        $('#message').text(message);
    });
    $('#HPCDeleteModel').modal('show'); 
})

$(document).on('click','#deleteHPC', function(){
    var hpc_id = $('#hpc_id').val(); 
    $.ajax({
        url: window.location.href + '/delete',
        method: "POST",    
        data: {
            "_token": $('#token').val(),
            hpc_id: hpc_id,
        },            
        success: function(response)
        {                
            if(response.status == 'true')
            {
                $('#HPCDeleteModel').modal('hide')
                table.ajax.reload();                    
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#HPCDeleteModel').modal('hide')
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


