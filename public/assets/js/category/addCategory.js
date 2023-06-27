
$(document).ready(function(){
    appendHtml.languageDropdown(language);
    $('#qty_range_block').hide();
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

function showRange()
{
    $('#qty_range_block').show();
}
function hideRange()
{
    $('#qty_range_block').hide();
}

// add category validation
$("#addCategoryForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "title": {
            required: true,
        },
        "slug": {
            required: true,
        },
        "category_image": {
            required: true,
            accept: "jpg,jpeg,png"
        },
        "banner_image" :{
            required:false,
            accept: "jpg,jpeg,png"
        },
        "mobile_banner_image" :{
            required:false,
            accept: "jpg,jpeg,png"
        }
    },
    messages: {
        "title": {
            required: "Please enter title"
        },
        "slug": {
            required: "Please enter slug",
        },
        'category_image' :{
            required: "Please select image",
            accept: "Please upload file in these format only (png, jpg, jpeg)."
        },
        "banner_image" :{
            accept: "Please upload file in these format only (png, jpg, jpeg)."
        },
        "mobile_banner_image" :{
            accept: "Please upload file in these format only (png, jpg, jpeg)."
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
$(document).ready(function(){
  $('.photo_upload').change(function(){
    if($(this).val()==1){
      $('.is_multiple').removeClass('d-none');
    }
    else{
      $('.is_multiple').addClass('d-none');
    }
  })

});
