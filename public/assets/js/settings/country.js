$(document).ready(function(){

    ajaxCall.getCountries();
    
    /** Delete FAQ */
    $('body').on('click', '.country_delete', function () {
		$('#countryIdForDelete').val($(this).attr('data'));
	});

	$('body').on('click', '#confirmDelete', function () {
		ajaxCall.deleteCurrency($('#countryIdForDelete').val())
    })

    /** Active inactive FAQ */
    $('body').on('click', '.toggle-is-active-switch', function () {
        $('#countryIdForActiveInactive').val($(this).attr('data'));
        $('#is_active').val($(this).attr('active'));
	});

	$('body').on('click', '#confirmActiveInactive', function () {
		ajaxCall.activeInactiveCountry($('#countryIdForActiveInactive').val(),$('#is_active').val())
    })
    
}); 

    var ajaxCall = {
        getCountries : function () {
            $('#country_listing').DataTable().destroy();
            $('#country_listing').DataTable({

            processing: true,
            serverSide: true,
            "initComplete": function (settings, json) {  
                $("#country_listing").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            "ajax": {
                'type': 'get',
                'url': window.location.href + '/list',
            },
            columnDefs: [
                {
                    "targets": [0,3,4],
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
                "data":'name'
            },
            {
                "target": 3,
                "render": function (data, type, row, meta)
                {                                              
                    var output = "";             
                    if(row.is_active == 1)
                    {                                                             
                        output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#countryActiveInactiveModel" data="'+row['id']+'" active="0">'
                        +'<div class="handle"></div>'
                        +'</button>' 
                    }
                    else
                    {                         
                        output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#countryActiveInactiveModel" data="'+row['id']+'" active="1">'
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
                    output += '<a href="'+ window.location.href +'/editCountry/'+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                    output += '<a class="text-danger"><i class="fa fa-trash country_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#countryDeleteModel"></i></a>'
                    return output;
                },
            }
            ]
        })

        },

        deleteCurrency : function (country_id) {
            var origin = window.location.href;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: origin + '/' + country_id + '/deleteCountry',
                method: "POST",    
                data: {
                    country_id: country_id,
                },            
                success: function(response)
                {
                    if(response.status == 'true')
                    {
                        $('#countryDeleteModel').modal('hide')
                        $('#country_listing').DataTable().ajax.reload();
                        toastr.clear();
                        toastr.options.closeButton = true;
                        toastr.options.timeOut = 0;
                        toastr.success(response.msg);
                    }
                    else
                    {
                        $('#countryDeleteModel').modal('hide')                  
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
        },

        activeInactiveCountry : function (country_id,is_active) {
            var origin = window.location.href;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: origin + '/activeDeactiveCountry',
                method: "POST",
                data:{
                    "is_active":is_active,
                    "country_id":country_id                  
                },
                success: function(response)
                {
                    if(response.status == 'true')
                    {                    
                        $('#countryActiveInactiveModel').modal('hide')
                        $('#country_listing').DataTable().ajax.reload();
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
        }
    }

 
/** add  country form validation */
$("#addCountryForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "name": {
            required: true,
        }
    },
    messages: {
        "name": {
            required: "Please enter country name"
        }
    },
    errorPlacement: function (error, element) 
    {
        error.insertAfter(element);
    },
    submitHandler: function(form) 
    {
        form.submit();
    }
});

/** edit country form validation */
$("#updateCountryForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "name": {
            required: true,
        }
    },
    messages: {
        "name": {
            required: "Please enter country name"
        }
    },
    errorPlacement: function (error, element) 
    {
        error.insertAfter(element);
    },
    submitHandler: function(form) 
    {
        form.submit();
    }
});
