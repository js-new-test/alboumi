$( document ).ready(function() {
    init.handler();
    formValidations.generalValidation();
    appendHtml.appendNonDefaultLanguage(nonDefaultLanguage);
    var ident = 2;
    $('#status option[text="' + manufacturer['status'] +'"]').prop("selected", true);
    $('#defaultLanguage option[value="' + manufacturer['languageId'] +'"]').prop("selected", true);

    //show common element on load when selected default language
    if ($('#defaultLanguage').val() == defaultLanguageId) {
        $('.commonElement').removeClass('d-none');
    }
    
});

var init = {

    handler : function() {
        $('body').on('change','#defaultLanguage', function() {
            if($(this).val() == defaultLanguageId){
                $('.commonElement').removeClass('d-none');
            }else{
                $('.commonElement').addClass('d-none');
            }

            ajaxCall.getSelectedLanguageBrandData($(this).val(), $('#brandId').val());
        })
    }
};

var formValidations = {
    //general form validations
    generalValidation : function() {
        // Initialize form validation on the registration form.
        // It has the name attribute "registration"
        $("form[name='editManufacturer']").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                brandName: "required",
                status: "required",
                slug: "required",

            },
            // Specify validation error messages
            messages: {
                brandName: "Please Enter Brand Name",
                status: "Please Select Status",
                slug :"Please enter slug"

            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    }
}

var appendHtml = {
    appendNonDefaultLanguage : function (nonDefaultLanguage) {
        var defaultLanguage = document.getElementById('defaultLanguage');

        $.each(nonDefaultLanguage, function (index,item) {
            var value = item['id'];
            var text = item['language']['langEN'];
            var o = new Option(text, value);
            defaultLanguage.append(o);
        });
    }
}

var ajaxCall = {
    getSelectedLanguageBrandData :function(languageId, brandId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'get',
            url: baseUrl +'/admin/manufacturers/languageBrandData',
            data: {'languageId':languageId, 'brandId':brandId},
            beforeSend: function() {
                $('#loaderimage').css("display", "block");
                $('#loadingorverlay').css("display", "block");
            },
            success: function (response) {
                console.log(response);
                if (response != null) {
                    $('#brandName').val(response['name']);
                    // $('#description').text(response['description']);
                    CKEDITOR.instances['description'].setData(response['description'])
                }
                // if (typeof response['contact_us_reply'] === 'undefined') {
                //     $('#customerName').val(response['name']);
                //     $('#customerMessage').text(response['message']);
                //     $('#inquiryId').val(response['id']);
                // } else if (typeof response['contact_us_reply'] !== 'undefined') {
                //     $('#customerNameView').text(response['name']);
                //     $('#customerMessageVew').text(response['message']);
                //     $('#replyMessageView').text(response['contact_us_reply']['reply']);
                // }
            }
        });
    }
}
// Show image dimensions for service image
var _URL = window.URL || window.webkitURL;

function _showLoadedImageDimensions(image)
{
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    
    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#width").text(imgwidth);
        $("#height").text(imgheight);   
        $("#loaded_image_width").val(imgwidth);
        $("#loaded_image_height").val(imgheight);                             
    }    
};