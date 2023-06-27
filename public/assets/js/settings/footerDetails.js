$(document).ready(function(){
    $("#footerTab li>a:first").addClass("active").show(); //Activate first tab on load
    $(".tab_content:first").addClass("active").show();
});

// Set tab active on click
$('#footerTab li>a').click(function(e) {
    $($('#footerTab li>a').parent()).addClass("active").not(this.parentNode).removeClass("active");
    e.preventDefault();
});

/** add footer details form validation */
$("#addFooterDetailsForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "contact_number[]": {
            required: true,
        },
        "whatsapp_number[]": {
            required: true,
        }
    },
    messages: {
        "contact_number[]": {
            minlength: 8
        },
        "whatsapp_number[]": {
            minlength: 8
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter(element)
    },
    submitHandler: function(form)
    {
        form.submit();
    }
});

/* shipping cost form validation */
$("#shippingCostForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "min_order_amt": {
            required: true,
        },
        "shipping_cost": {
            required: true,
        },
        "min_qty": {
            required: true,
        },
        "delivery_days": {
            required: true,
        },
        "delivery_days_exceed_min_qty": {
            required: true,
        }
    },
    messages: {
        "min_order_amt": {
            required: "Minimum order amount is required"
        },
        "shipping_cost": {
            required: "Shipping cost is required"
        },
        "min_qty": {
            required: "Min qty is required"
        },
        "delivery_days": {
            required: "Delivery days is required"
        },
        "delivery_days_exceed_min_qty": {
            required: "Exceeding Min Qty, Delivery Days is required"
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter(element)
    },
    submitHandler: function(form)
    {
        form.submit();
    }
});

$(".footer_about").each(function(){
    $(this).rules("add", {
        required:true,
        messages:{required:'Please write contents here'}
    });
});

$(".contact_email").each(function(){
    $(this).rules("add", {
        required:true,
        messages:{required:'Please enter contact email'}
    });
});

$(".contact_number").each(function(){
    $(this).rules("add", {
        required:true,
        messages:{required:'Please enter contact number'}
    });
});

/** update footer details form validation */
$("#updateFooterDetailsForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "contact_number[]": {
            required: true,
        },
        "whatsapp_number[]": {
            required: true,
        }
    },
    messages: {
        "contact_number[]": {
            minlength: 8
        },
        "whatsapp_number[]": {
            minlength: 8
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter(element)
    },
    submitHandler: function(form)
    {
        form.submit();
    }
});

$(".update_footer_about").each(function(){
    $(this).rules("add", {
        required:true,
        messages:{required:'Please write contents here'}
    });
});

$(".update_contact_email").each(function(){
    $(this).rules("add", {
        required:true,
        messages:{required:'Please enter contact email'}
    });
});

$(".update_contact_number").each(function(){
    $(this).rules("add", {
        required:true,
        messages:{required:'Please enter contact number'}
    });
});

$("#aramexShipping").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "contact_name": {
            required: true,
        },
        "company_name": {
            required: true,
        },
        "line_1": {
            required: true,
        },
        "line_2": {
            required: true,
        },
        "city": {
            required: true,
        },
        "country_code": {
            required: true,
        },
        "phone_ext": {
            required: true,
        },
        "phone_number": {
            required: true,
        },
        "email": {
            required: true,
            email: true
        },
    },
    messages: {
        "contact_name": {
            required: "Please enter contact name",
        },
        "company_name": {
            required: "Pleae enter company name",
        },
        "line_1": {
            required: "Please enter line 1",
        },
        "line_2": {
            required: "Please enter line 2",
        },
        "city": {
            required: "Please enter city",
        },
        "country_code": {
            required: "Please enter country code",
        },
        "phone_ext": {
            required: "Please enter phone extension",
        },
        "phone_number": {
            required: "Please enter phone number",
        },
        "email": {
            required: "Please enter email",
            email: "Please enter a valid email address"
        },
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter(element)
    },
    submitHandler: function(form)
    {
        form.submit();
    }
});

var curHomePageCompObj;
$(document).on('click', '.benner-component-is-active-switch', function () {
    curHomePageCompObj = $(this);
    var home_page_act_deact = ($(this).attr('aria-pressed') === 'true') ? 1 : 0;
    var home_page_com_id = $(this).attr('data-id');
    if($(this).attr('aria-pressed') == 'false')
    {
        $(this).addClass('active');
    }
    if($(this).attr('aria-pressed') == 'true')
    {
        $(this).removeClass('active');
    }
    $('#homePageComponentModel').on('show.bs.modal', function(e){
        $('#home_page_com_id').val(home_page_com_id);
        $('#home_page_act_deact').val(home_page_act_deact);
    });
    $('#homePageComponentModel').modal('show');
});

$(document).on('click','#actDeactHomePageComponent', function(){
    var home_page_com_id = $('#home_page_com_id').val();
    var home_page_act_deact = $('#home_page_act_deact').val();
    var baseUrl = $('#baseUrl').val();
    $.ajax({
        url: baseUrl + '/admin/home-page-comp-act-deact',
        method: "POST",
        data:{
            "_token": $('#token').val(),
            "home_page_com_id":home_page_com_id,
            "home_page_act_deact":home_page_act_deact
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#homePageComponentModel').modal('hide')
                if(response.switch_status == "true")
                {
                    curHomePageCompObj.addClass('active').attr('aria-pressed', true);
                }
                if(response.switch_status == "false")
                {
                    curHomePageCompObj.removeClass('active').attr('aria-pressed', false);
                }
                // table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
                // location.reload();
            }
            setTimeout(function(){
                toastr.clear();
            }, 5000);
        }
    })
});

var counter = 0;
$(document).on('click', '.home-page-mobile-app-is-active-switch', function () {     
    var event_id = $(this).closest('tr').find('select').val();
    var output = '';
    var name = $(this).attr('data-name');
    var id = $(this).attr('data-id');
    var home_page_mobile_app_image_height = $('#home_page_mobile_app_image_height').val();
    var home_page_mobile_app_image_width = $('#home_page_mobile_app_image_width').val();
    if($(this).attr('aria-pressed') == 'true')
    {
        output += '<div class="form-group prevent_default_submit">';
        output += '<label for="home_page_mobile_app_image"><strong>'+name+'<span style="color:red">*</span></strong></label>'
        output += '<div class=""><input name="home_page_mobile_app_image['+id+']" type="file" id="home_page_mobile_app_image'+counter+'" data-inc-val="'+counter+'" class="form-control-file dynamic_home_page_mobile_app_image validation_image_class">';
        output += '<small class="form-text text-muted">Image size should be '+home_page_mobile_app_image_width+' X '+home_page_mobile_app_image_height+' px.</small>';
        output += '<small class="form-text text-muted">width = <small class="mobile_width"></small></small>';
        output += '<small class="form-text text-muted">height = <small class="mobile_height"></small></small>'
        output += '<span id="home_page_mobile_app_image_error'+counter+'"></span>';
        output += '</div>';
        output += '</div>';
        output += '<input type="hidden" name="hpma_id[]" value="'+id+'">';
        output += '<input type="hidden" name="event_id[]" value="'+event_id+'">';
        $(this).closest('tr').find('.dynamic_image_field').html(output);
    }
    if($(this).attr('aria-pressed') == 'false')
    {
        $(this).closest('tr').find('.dynamic_image_field').empty();
    }
    counter++;
});

var _URL = window.URL || window.webkitURL;
$('#tblHomePageMobileApp').on('change','.dynamic_home_page_mobile_app_image', function () {
    var $this = $(this);
    var file = $(this)[0].files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
    imgwidth = this.width;
    imgheight = this.height;

    $this.closest("tr").find('.mobile_width').text(imgwidth);
    $this.closest("tr").find('.mobile_height').text(imgheight);
    }
});

$.validator.addMethod(
    "multiemail",
    function (value, element) {
        var email = value.split(/[;,]+/); // split element by , and ;
        valid = true;
        for (var i in email) {
            value = email[i];
            valid = valid && jQuery.validator.methods.email.call(this, $.trim(value), element);
        }
        return valid;
    },
    $.validator.messages.multiemail
);

$("#adminMultipleEmails").validate({
    // debug: true,
    ignore: [], // ignore NOTHING
    rules: {
        admin_emails: {
            required: true,
            multiemail: true
        }
    },
    messages: {
        admin_emails: {
            required: "Please enter email address.",
            multiemail: "You must enter a valid email, or comma separate multiple"
        }
    },
    errorPlacement: function (error, element){
            error.insertAfter(element)
    },
    submitHandler: function(form){
        form.submit();
    }
});

/*$('#homePageMobileAppSection').on('submit', function(event) {
    if ($('div.prevent_default_submit').length > 0) {
        $('.validation_image_class').each(function() {
            var inc = $(this).attr('data-inc-val');
            if(inc)
            {
                i = inc;
            }
            else
            {
                i = 0;
            }
            if($('#home_page_mobile_app_image'+i+'').val() == '')
            {
                $('#home_page_mobile_app_image_error'+i+'').html('<p style="color: red;">Please upload image</p>');
                event.preventDefault();
            }
            else
            {
                $('#home_page_mobile_app_image_error'+i).html('');
            }
        });
    }
    else
    {
        event.preventDefault();
    }
})

$('#homePageMobileAppSection').validate();*/
