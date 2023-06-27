$(document).ready(function(){

    var lang_id = " ";
    ajaxCall.getFaqs(lang_id);
    
    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterLangDiv").slideToggle('slow');
    });
    
    /** Filter FAQ */
    $('body').on('click', '#filter_faq', function () {
        var lang_id = $('#filter_faq').val();
        ajaxCall.getFaqs(lang_id)
    });

    /** Delete FAQ */
    $('body').on('click', '.faq_delete', function () {
		$('#faqIdForDelete').val($(this).attr('data'));
	});

	$('body').on('click', '#confirmDelete', function () {
		ajaxCall.deleteFaq($('#faqIdForDelete').val())
    })

    /** Active inactive FAQ */
    $('body').on('click', '.toggle-is-active-switch', function () {
        $('#faqIdForActiveInactive').val($(this).attr('data'));
        $('#is_active').val($(this).attr('active'));
	});

	$('body').on('click', '#confirmActiveInactive', function () {
		ajaxCall.activeInactiveFaq($('#faqIdForActiveInactive').val(),$('#is_active').val())
    })
}); 

    var ajaxCall = {
        getFaqs : function (lang_id) {
            $('#faq_listing').DataTable().destroy();
            $('#faq_listing').DataTable({

            processing: true,
            serverSide: true,
            "ajax": {
                'type': 'get',
                'url': window.location.href + '/list',
                'data': {lang_id:lang_id}
            },
            columnDefs: [
                {
                    "targets": [0,4,5,6],
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
                "data":'question'
            },
            {
                "target": 3,
                "data":'answer'
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
                    if(row.is_active == 1)
                    {                                                             
                        output += '<button type="button" class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#faqActiveInactiveModel" data="'+row['id']+'" active="0">'
                        +'<div class="handle"></div>'
                        +'</button>' 
                    }
                    else
                    {                         
                        output += '<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="modal" aria-pressed="true" autocomplete="off" data-target="#faqActiveInactiveModel" data="'+row['id']+'" active="1">'
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
                    output += '<a href="'+ window.location.href +'/editFaq/'+ row.id +'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                    output += '<a class="text-danger"><i class="fa fa-trash faq_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#faqDeleteModel"></i></a>'
                    return output;
                },
            }
            ]
        })

        },

        deleteFaq : function (faq_id) {
            var origin = window.location.href;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: origin + '/' + faq_id + '/deleteFaq',
                method: "POST",    
                data: {
                    faq_id: faq_id,
                },            
                success: function(response)
                {
                    if(response.status == 'true')
                    {
                        $('#faqDeleteModel').modal('hide')
                        $('#faq_listing').DataTable().ajax.reload();
                        toastr.clear();
                        toastr.options.closeButton = true;
                        toastr.options.timeOut = 0;
                        toastr.success(response.msg);
                    }
                    else
                    {
                        $('#faqDeleteModel').modal('hide')                  
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

        activeInactiveFaq : function (faq_id,is_active) {
            var origin = window.location.href;
            console.log(faq_id,is_active);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: origin + '/activeDeactivefaq',
                method: "POST",
                data:{
                    "is_active":is_active,
                    "faq_id":faq_id                  
                },
                success: function(response)
                {
                    if(response.status == 'true')
                    {                    
                        $('#faqActiveInactiveModel').modal('hide')
                        $('#faq_listing').DataTable().ajax.reload();
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

 
/** add  faq form validation */
$("#addFaqForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "language_id": {
            required: true,
        },
        "question": {
            required: true,
        }
    },
    messages: {
        "language_id": {
            required: "Please select language"
        },
        "question": {
            required: "Please write question",
        }
    },
    errorPlacement: function (error, element) 
    {
        if (element.attr("name") == "language_id") 
        {
            error.appendTo("#lang_error");
        }
        else 
        {
            error.insertAfter(element)
        }
    },
    submitHandler: function(form) 
    {
        form.submit();
    }
});

$('#addFaq').click(function() {
    var totalcontentlength = CKEDITOR.instances['answer'].getData().replace(/<[^>]*>/gi, '').length;
    if( totalcontentlength > 0) 
    {
        $('#ck_error').css('display','none','!important'); 
    }
    else
    {
        $("#ck_error").html('Please write answer');
        $("#ck_error").css('color', 'red');
    }
    $("#addFaqForm").valid();
});

$('#updateFaq').click(function() {
    var totalcontentlength = CKEDITOR.instances['answer'].getData().replace(/<[^>]*>/gi, '').length;
    if( totalcontentlength > 0) 
    {
        $('#ck_error').css('display','none','!important'); 
    }
    else
    {
        $("#ck_error").html('Please write answer');
        $("#ck_error").css('color', 'red');
    }
    $("#editFaqForm").valid();
});

/** edit faq form validation */
$("#editFaqForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "language_id": {
            required: true,
        },
        "question": {
            required: true,
        }
    },
    messages: {
        "language_id": {
            required: "Please select language"
        },
        "question": {
            required: "Please write question",
        }
    },
    errorPlacement: function (error, element) 
    {
        if (element.attr("name") == "answer") 
        {
            error.appendTo("#ck_error");
        }
        else 
        {
            error.insertAfter(element)
        }
    },
    submitHandler: function(form) 
    {
        form.submit();
    }
});
