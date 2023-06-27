// Written by Pallavi (22 Jan 2021)
$(document).ready(function(){
    if($('#flagLoggedIn').val() == 1)
    {
        console.log(localStorage);
        if (localStorage.length == 0)
        {
            $('#enqForm').hide();
        }
        else
        {
            if (localStorage.getItem("full_name") != '')
            {
                $('#enqForm').show();
                $('#full_name').val(localStorage.getItem("full_name"));
            }
            if (localStorage.getItem("email") != '')
            {
                $('#enqForm').show();
                $('#email').val(localStorage.getItem("email"));
            }
            if (localStorage.getItem("event_date") != '')
            {
                $('#enqForm').show();
                $('#event_date').val(localStorage.getItem("event_date"));
            }
            if (localStorage.getItem("event_time") != '')
            {
                $('#enqForm').show();
                $('#event_time').val(localStorage.getItem("event_time"));
            }
            if (localStorage.getItem("photographer_count") != '')
            {
                $('#enqForm').show();
                $('#photographer_count').val(localStorage.getItem("photographer_count"));
            }
            if (localStorage.getItem("videographer_count") != '')
            {
                $('#enqForm').show();
                $('#videographer_count').val(localStorage.getItem("videographer_count"));
            }
            if (localStorage.getItem("photographer_gender") != '')
            {
                $('#enqForm').show();
                $('#photographer_gender').val(localStorage.getItem("photographer_gender"));
            }

            if (localStorage.getItem("videographer_gender") != '')
            {
                $('#enqForm').show();
                $('#videographer_gender').val(localStorage.getItem("videographer_gender"));
            }

            if (localStorage.getItem("package_id") != '')
            {
                $('#package_id').val(localStorage.getItem("package_id"));
                $('#pkgName').val(localStorage.getItem("package_name"));
                $('.pkgName').html(localStorage.getItem("package_name"));

                $('#pkgWithFeatureTable').find('th').each(function (i, el) {
        
                    if(localStorage.getItem("package_id") == $(this)[0].id)
                    {
                        $($(this).closest('th')).addClass('selected');
                    }
                });
                $('#pkgWithFeatureTable').find('tr td').each(function (i, el) {
                    if(localStorage.getItem("package_id") == $(this)[0].id)
                    {
                        $($(this).closest('td')).addClass('selected');
                    }
                });

                $('.select-package-border').each(function (i, el) {
                    // console.log(el)
                    if(localStorage.getItem("package_id") == el.id)
                    {
                        $($(this)).css('border-color','#062D7A');
                    }
                });
            }

            var addServices = localStorage.getItem("additional_pkg_ids");
            if(addServices != null)
            {
                var results = addServices.split(',');
                for (var r = 0, len=results.length; r < len; r++ )
                {
                    $('li[data-name="'+results[r]+'"]').addClass('checked');
                    $('.addToEnq_' + results[r]).addClass('d-none');
                    $('.removeEnq_' + results[r]).removeClass('d-none');
                }
            }
        }

    }
    if($('#flagLoggedIn').val() == 0)
    {
        $('#enqForm').hide();
    }
});

$('.removeEnq').click(function(){
    var id = $(this).data('btn');
    $('li[data-name="'+id+'"]').removeClass('checked');
    $('#removeBtn_' + id).addClass('d-none');
    $('#addBtn_' + id).removeClass('d-none');
})

$('.addToEnq').click(function(){
    var id = $(this).data('btn');
    $('li[data-name="'+id+'"]').addClass('checked');
    $('#removeBtn_' + id).removeClass('d-none');
    $('#addBtn_' + id).addClass('d-none');
})

$('#event_date').datepicker({
    "format": "yyyy-mm-dd",
    startDate: new Date(new Date().getTime() + 24 * 60 * 60 * 1000),
    orientation: "bottom auto"

});

function showEnqForm(pkgId,pkgName)
{
    $('#enqForm').show();
    if (localStorage.getItem("videographer_gender") == '')
    {
        $("#videographer_gender").prop("selectedIndex", 0).val();
    }
    if (localStorage.getItem("photographer_gender") == '')
    {
        $("#photographer_gender").prop("selectedIndex", 0).val();
    }
    $('html,body').animate({
        scrollTop: $("#enqForm").offset().top},
    'slow');

    $('.pkgName').html(pkgName);
    $('#pkgName').val(pkgName);
    $('#package_id').val(pkgId);

    $('.addToEnq').on('click',function(){
        $(this).closest('li').parent().closest('li').addClass('checked');
        var btnData = $(this).attr('data');
        $('.addToEnq_' + btnData).addClass('d-none');
        $('.removeEnq_' + btnData).removeClass('d-none');
    })

    $('.removeEnq').on('click',function(){
        $(this).closest('li').parent().closest('li').removeClass('checked');
        var btnData = $(this).attr('data');
        $('.removeEnq_' + btnData).addClass('d-none');
        $('.addToEnq_' + btnData).removeClass('d-none');
    })
}

$("#submitEnqBtn").on('click',function(){
    if($('#flagLoggedIn').val() == 0)
    {
        var photographer_gender = $('#photographer_gender').find(":selected").text();
        var videographer_gender = $('#videographer_gender').find(":selected").text();

        localStorage.setItem('full_name',$('#full_name').val());
        localStorage.setItem('email',$('#email').val());
        localStorage.setItem('event_date',$('#event_date').val());
        localStorage.setItem('event_time',$('#event_time').val());
        localStorage.setItem('photographer_count',$('#photographer_count').val());
        localStorage.setItem('videographer_count',$('#videographer_count').val());
        localStorage.setItem('photographer_gender',photographer_gender);
        localStorage.setItem('videographer_gender',videographer_gender);
        localStorage.setItem('package_id',$('#package_id').val());
        localStorage.setItem('package_name',$('#pkgName').val());
        var li = [];

        $('#addServList li.checked').each(function() {
          li.push(this.id);
        });
        localStorage.setItem('additional_pkg_ids',li);

        window.location = baseUrl + '/login?flagLogin=1&eventId='+ eventId
    }
    if($('#flagLoggedIn').val() == 1)
    {
        $('#submitEventEnqForm').valid();
    }

})
$("#submitEventEnqForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "full_name": {
            required: true,
        },
        "email": {
            required: true,
        },
        "event_date": {
          required: true,
       },
        "event_time": {
            required: true,
        }
    },
    messages: {
        "full_name": {
            required: fullNameError
        },
        "email": {
            required: emailError,
        },
        "event_date": {
          required: eventDateError
        },
        "event_time": {
            required: eventTimeError,
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter( element );
    },
    submitHandler: function(form)
    {
        var formData = $('#submitEventEnqForm').serialize();
        var li = [];

        $('#addServList li.checked').each(function() {
          li.push(this.id);
        });

        $.ajaxSetup({
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
          type: "post",
          url: baseUrl + '/customer/events/submitEventEnq',
          data:formData + "&additional_pkg_ids=" + li,
          success: function (response)
          {
            if(response.status == true)
            {
                window.location = baseUrl + '/customer/events/enqSubmitSuccess';
                localStorage.clear();

            }
            else
            {
                toastr.error(response.msg);
            }
          }
      });
    }
});

