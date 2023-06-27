
$(document).ready(function(){
    appendHtml.languageDropdown(language);
});

var appendHtml = {
    languageDropdown : function (language) {
        var defaultLanguage = document.getElementById('defaultLanguage');

        $.each(language, function (index,item) {
            var text = item['languageName'];
            var value = item['globalLanguageId'];
            var o = new Option(text, value);
            defaultLanguage.append(o);
        })
    }
}

function generateSlug()
{
    var titleValue = $("#title").val();
    $("#slug").val(titleValue.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-').toLowerCase());
}

$("#addCmsPageForm").validate({
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

$('#addPage').click(function() {
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
    $("#addCmsPageForm").valid();
});

// Show image dimensions for banner pic
function _showLoadedBannerDimensions(image)
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
