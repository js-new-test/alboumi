/** Ajax - datatable for package listing */

$(document).ready(function () {
    var origin = window.location.href;
    var table = $('#package_listing').DataTable({
        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {  
            $("#package_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        ajax: {
            "url": origin,
            "type": "GET"
        },
        'columnDefs': [{
                "targets": [0,1,6,7],
                "className": "text-center",
            },
               {
                   "targets": [1],
                   "visible": false
               }
        ],
        columns: [{
                data: 'rownum',
                name: 'rownum'
            },
            {
                data: 'id',
                name: 'id'
            },
            {
                data: 'event_name',
                name: 'event_name'
            },
            {
                data: 'package_name',
                name: 'package_name'
            },
            {
                data: 'price',
                name: 'price'
            },
            // {
            //     data: 'discounted_price',
            //     name: 'discounted_price'
            // },
            { data: 'zone', name: 'zone',
                render: function (data,type,row) {                    
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.p_created_at).utcOffset(z.replace('.', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }
                    
                }
            },
            // {
            //     data: 'other_details',
            //     name: 'other_details'
            // },
            {
                data: 'is_active',
                name: 'is_active',
                render: function (data, type, full, meta) {
                    var output = "";
                    if (full.is_active == 1) {
                        output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="button" aria-pressed="true" autocomplete="off">' +
                            '<div class="handle"></div>' +
                            '</button>'
                    } else {
                        output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="button" aria-pressed="false" autocomplete="off">' +
                            '<div class="handle"></div>' +
                            '</button>'
                    }
                    return output;
                },
            },
            {
                data: 'id',
                name: 'action', // orderable: true, // searchable: true
                render: function (data, type, row) {
                    var output = "";
                    output += '<a href="' + origin + '/../editPackage/' + row.id + '"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp; &nbsp;'
                    output += '<a class="pkg_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },
            },
        ]
    });

    $(document).on('click','#filter_package', function(){
        var token = $('input[name="_token"]').val();
        var filter_package_lang = $('#filter_package_lang').val();
        $('#package_listing').dataTable().fnDestroy(); 
        table = $('#package_listing').DataTable({
            processing: true,
            serverSide: true,
            "initComplete": function (settings, json) {  
                $("#package_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            ajax: {
                url: window.location.href + '/../list/filter-package',
                method: "POST",
                data:{
                    _token : token,
                    filter_package_lang:filter_package_lang
                },
            },
            'columnDefs': [{
                    "targets": [0,1,6,7],
                    "className": "text-center",
                },
                {
                    "targets": [1],
                    "visible": false
                }
            ],
            columns: [{
                data: 'rownum',
                name: 'rownum'
            },
            {
                data: 'id',
                name: 'id'
            },
            {
                data: 'event_name',
                name: 'event_name'
            },
            {
                data: 'package_name',
                name: 'package_name'
            },
            {
                data: 'price',
                name: 'price'
            },
            // {
            //     data: 'discounted_price',
            //     name: 'discounted_price'
            // },
            { data: 'zone', name: 'zone',
                render: function (data,type,row) {                    
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.p_created_at).utcOffset(z.replace('.', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }
                    
                }
            },
            // {
            //     data: 'other_details',
            //     name: 'other_details'
            // },
            {
                data: 'is_active',
                name: 'is_active',
                render: function (data, type, full, meta) {
                    var output = "";
                    if (full.is_active == 1) {
                        output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="button" aria-pressed="true" autocomplete="off">' +
                            '<div class="handle"></div>' +
                            '</button>'
                    } else {
                        output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="button" aria-pressed="false" autocomplete="off">' +
                            '<div class="handle"></div>' +
                            '</button>'
                    }
                    return output;
                },
            },
            {
                data: 'id',
                name: 'action', // orderable: true, // searchable: true
                render: function (data, type, row) {
                    var output = "";
                    output += '<a href="' + origin + '/../editPackage/' + row.id + '"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp; &nbsp;'
                    output += '<a class="pkg_delete text-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                    return output;
                },
            },
        ]
        });               
    })


    /** Delete package */
    $('#package_listing').on('click', 'tbody .pkg_delete', function () {
        var data_row = table.row($(this).closest('tr')).data();  
        var package_id = data_row.id;  
        var message = "Are you sure ?";   
        console.log(message);       
        $('#packageDeleteModel').on('show.bs.modal', function(e){
            $('#package_id').val(package_id);
            $('#message_delete').text(message);
        });
        $('#packageDeleteModel').modal('show');              
    })

    $(document).on('click','#deletePackage', function(){
        var package_id = $('#package_id').val(); 
        $.ajax({
            url: origin + '/../' + package_id + '/deletePackage',
            method: "POST",    
            data: {
                "_token": $('#token').val(),
                package_id: package_id,
            },            
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#packageDeleteModel').modal('hide')
                    table.ajax.reload();                    
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#packageDeleteModel').modal('hide')                  
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


    /** toggle active switch and show confirmation */
    $('#package_listing').on('click', 'tbody .toggle-is-active-switch', function () {            
        var is_active = ($(this).attr('aria-pressed') === 'true') ? 0 : 1;         
        var data_row = table.row($(this).closest('tr')).data();                             
        var package_id = data_row.id; 
        var message = ($(this).attr('aria-pressed') === 'true') ? "Are you sure ?" : "Are you sure ?";
        if($(this).attr('aria-pressed') == 'false')
        {
            $(this).addClass('active');
        }
        if($(this).attr('aria-pressed') == 'true')
        {
            $(this).removeClass('active');
        }                        
        $('#packageIsActiveModel').on('show.bs.modal', function(e){
            $('#package_id').val(package_id);
            $('#is_active').val(is_active);
            $('#message').text(message);
        });
        $('#packageIsActiveModel').modal('show');                                             
    });    

    /** Activate or deactivate package */
    $(document).on('click','#packageIsActive', function(){ 
        var package_id = $('#package_id').val();
        var is_active = $('#is_active').val();                          
        $.ajax({
            url: origin + '/../activeDeactivePackage',
            method: "POST",
            data:{
                "_token": $('#token').val(),
                "is_active":is_active,
                "package_id":package_id                  
            },
            success: function(response)
            {
                if(response.status == 'true')
                {                    
                    $('#packageIsActiveModel').modal('hide')
                    table.ajax.reload();                    
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
    });  


    /** Export packages */
    $('body').on('click', '#exportPackages', function() {
        $.ajax({
            type: "get",
            url: origin + '/../exportPackages',
            success: function (response) 
            {
                if (response === "packages.csv") 
                {
                    window.location.href = '../../packages.csv';
                }
            }
        });
    });
    
});

/** Add package form validation */
$("#addPackageForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        // "event_id": {
        //     required: true,
        // },
        "package_name": {
            required: true
        },
        'price': {
            required: true
        },  
        "sort_order" : {
            required: true
        }      
    },
    messages: {
        // "event_id": {
        //     required: "Please select event name"
        // },
        "package_name": {
            required: "Please enter package name"
        },
        'price': {
            required: "Please enter price"
        },   
        "sort_order" : {
            required: "Please enter sort order"
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
    // errorPlacement: function (error, element) 
    // {
    //     error.appendTo(element.parent().parent().parent());
    //     $(element.parent().parent()).css('margin-bottom', '0');
    // },
    // submitHandler: function (form) 
    // {
    //     // validate_count();
    //     form.submit();

    // }
    
});

$('#addPackageForm').on('submit', function(event) {   
    if($('#event_id').val() == null)
    {
        $('#event_name_error').html('<p style="color: red;">Please select event name</p>');
        event.preventDefault();
    }       
    else
    {
        $('#event_name_error').html('');
    }        
         
    var i = 0;
    $('.dynamic_feature_value').each(function() { 
        i++;                
        if($('#dynamic_feature_value_id'+i+'').val() == '')
        {            
            // $("table").has('label[id=dynamic_feature_value_id'+i+'-error]').remove();
            // $('#dynamic_feature_value_error'+i+'').html('<p style="color: red;">This field is required</p>');
            event.preventDefault();
        }
        else
        {
            $('#dynamic_feature_value_error'+i).html('');
        }
    });
              
})

$('#addPackageForm').validate();

// $('#add_pkg_btn').click(function() {
//     $("#addPackageForm").valid();
// });


/** Edit package - show hidden div if relevant option is selected */

/** Update package form validation */
$("#updatePackageForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        // "event_id": {
        //     required: true,
        // },
        "package_name": {
            required: true
        },
        'price': {
            required: true,
            number:true
        },
        "sort_order" : {
            required: true
        }
    },
    messages: {
        // "event_id": {
        //     required: "Please select event name"
        // },
        "package_name": {
            required: "Please enter package name"
        },
        'price': {
            required: "Please enter price"
        },
        "sort_order" : {
            required: "Please enter sort order"
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

$('#updatePackageForm').on('submit', function(event) {                
    var i = 0;
    $('.dynamic_feature_value').each(function() { 
        i++;                
        if($('#dynamic_feature_value_id'+i+'').val() == '')
        {            
            // $("table").has('label[id=dynamic_feature_value_id'+i+'-error]').remove();
            // $('#dynamic_feature_value_error'+i+'').html('<p style="color: red;">This field is required</p>');
            event.preventDefault();
        }
        else
        {
            $('#dynamic_feature_value_error'+i).html('');
        }
    });
              
})

$('#updatePackageForm').validate();

// $('#edit_pkg_btn').click(function() {
//     $("#updatePackageForm").valid();
// });
