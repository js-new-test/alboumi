$( document ).ready(function() {
    appendHtml.appendNonDefaultLanguage(nonDefaultLanguage);
    if(banner_image != null)
    {
        $("#selected_banner").show();
        var src1 = baseUrl + '/public/assets/images/cms/banner/' + banner_image;
        $("#selected_banner").attr("src", src1);
    }
    else
        $("#selected_banner").hide();
    
    if(mobile_banner_image != null)
    {
        $("#selected_mobile_banner").show();
        var src2 = baseUrl + '/public/assets/images/cms/mobile_banner/' + mobile_banner_image;
        $("#selected_mobile_banner").attr("src", src2);
    }
    else
        $("#selected_mobile_banner").hide();
    
});

$('body').on('change','#defaultLanguage', function() {
    langId =  $(this).val();
    if($(this).val() == defaultLanguageId)
    {
        $('.commonElement').removeClass('d-none');
    }
    else
    {
        $('.commonElement').addClass('d-none');
    }
    jQuery.ajax({
        type: "get",
        url: window.location.href + '?page=otherLang' + '&lang=' + $(this).val(),
        async: true,
        dataType: 'json',
        success: function (response)
        {
            console.log(response);
            if(response.status == true)
            {
                $('#title').val(response.cmsDetails.title);
                $('#seo_title').val(response.cmsDetails.seo_title);
                $('#seo_description').val(response.cmsDetails.seo_description);
                CKEDITOR.instances['description'].setData(response.cmsDetails.description)
                $('#seo_keyword').val(response.cmsDetails.seo_keyword);

                if(defaultLanguageId == langId)
                {
                    $("#selected_banner").attr("src", ''); 
                    $("#selected_mobile_banner").attr("src", '');

                    if(response.cmsDetails.cms_banner != null)
                    {
                        $("#selected_banner").show();
                        src1 = baseUrl + '/public/assets/images/cms/banner/' + response.cmsDetails.cms_banner;
                        $("#selected_banner").attr("src", src1);
                    }
                    else
                        $("#selected_banner").hide();
                    
                    if(response.cmsDetails.cms_mobile != null)
                    {
                        $("#selected_mobile_banner").show();
                        src2 = baseUrl + '/public/assets/images/cms/mobile_banner/' + response.cmsDetails.cms_mobile;
                        $("#selected_mobile_banner").attr("src", src2);
                    }
                    else
                        $("#selected_mobile_banner").hide();
                }
                else
                {       
                    $("#selected_pic").attr("src", ''); 
                    $("#selected_mobile_banner").attr("src", '');
             
                    if(response.cmsDetails.banner_image != null)
                    {
                        $("#selected_banner").show();
                        src3 = baseUrl + '/public/assets/images/cms/banner/' + response.cmsDetails.banner_image;
                        $("#selected_banner").attr("src", src3);
                    }
                    else
                        $("#selected_banner").hide();

                    if(response.cmsDetails.mobile_banner != null)
                    {
                        $("#selected_mobile_banner").show();
                        src4 = baseUrl + '/public/assets/images/cms/mobile_banner/' + response.cmsDetails.mobile_banner;
                        $("#selected_mobile_banner").attr("src", src4);
                    }
                    else
                        $("#selected_mobile_banner").hide();

                }
            }
        }
    });
})

var appendHtml = {
    appendNonDefaultLanguage : function (nonDefaultLanguage) {
        var defaultLanguage = document.getElementById('defaultLanguage');
        if(defaultLanguage != null)
        {
            $.each(nonDefaultLanguage, function (index,item) {
                var value = item['language_id'];
                var text = item['langEN'];
                var o = new Option(text, value);
                defaultLanguage.append(o);
            });
        }
    }
}

function generateSlug()
{
    var titleValue = $("#title").val();
    $("#slug").val(titleValue.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-').toLowerCase());
}


$("#updatecmsDetailsForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "title": {
            required: true,
        },
        "slug": {
            required: true,
        },
        "banner_image": {
            extension: "jpg|jpeg|png|"
        },
        "mobile_banner_image":{
                extension: "jpg|jpeg|png|"
        },
    },
    messages: {
        "title": {
            required: "Please enter title"
        },
        "slug": {
            required: "Please enter slug",
        },
        "banner_image" : {
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        "mobile_banner_image" : {
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
    },
    errorPlacement: function (error, element)
    {
        // if (element.attr("name") == "description")
        // {
        //     error.appendTo("#desc_error");
        // }
        // else
        // {
            error.insertAfter(element)
        // }
    },
    submitHandler: function(form)
    {
        form.submit();
    }
});


$('#updatePage').click(function() {
    // var totalcontentlength = CKEDITOR.instances['description'].getData().replace(/<[^>]*>/gi, '').length;
    // if( totalcontentlength > 0)
    // {
    //     $('#desc_error').css('display','none','!important');
    // }
    // else
    // {
    //     $("#desc_error").html('Please enter description');
    //     $("#desc_error").css('color', 'red');
    // }
    $("#updateCmsPageForm").valid();
});

// Show image dimensions for banner pic
function _showLoadedBannerDimensions(image)
{
  var file =image.files[0];
  img = new Image();
  var imgwidth = 0;
  var imgheight = 0;

  img.src = _URL.createObjectURL(file);
  img.onload = function() {
      imgwidth = this.width;
      imgheight = this.height;

      $("#width").text(imgwidth);
      $("#height").text(imgheight);
      $("#loaded_banner_width").val(imgwidth);
      $("#loaded_banner_height").val(imgheight);
  }
};

// Show image dimensions for banner pic
function _showLoadedMobileBannerDimensions(image)
  {
    var file = image.files[0];
    img = new Image();
    var imgwidth = 0;
    var imgheight = 0;

    img.src = _URL.createObjectURL(file);
    img.onload = function() {
        imgwidth = this.width;
        imgheight = this.height;

        $("#mobilebannerwidth").text(imgwidth);
        $("#mobilebannerheight").text(imgheight);
        $("#loaded_mobile_banner_width").val(imgwidth);
        $("#loaded_mobile_banner_height").val(imgheight);
    }
};
