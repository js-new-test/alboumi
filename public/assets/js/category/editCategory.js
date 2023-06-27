$( document ).ready(function() {
    formValidations.formValidation();
    appendHtml.appendNonDefaultLanguage(nonDefaultLanguage);

    if(category_image != null)
    {
        $("#selected_pic").show();
        var src1 = baseUrl + '/public/assets/images/categories/' + category_image;
        $("#selected_pic").attr("src", src1);
    }
    else
        $("#selected_pic").hide();

    if(banner_image != null)
    {
        $("#selected_banner").show();
        var src2 = baseUrl + '/public/assets/images/categories/banner/' + banner_image;
        $("#selected_banner").attr("src", src2);
    }
    else
        $("#selected_banner").hide();

    if(mobile_banner_image != null)
    {
        $("#selected_mobile_banner").show();
        var src3 = baseUrl + '/public/assets/images/categories/mobile_banner/' + mobile_banner_image;
        $("#selected_mobile_banner").attr("src", src3);
    }
    else
        $("#selected_mobile_banner").hide();

    if(qty_matrix == 0)
        $('#qty_range_block').hide();
    else
        $('#qty_range_block').show();

});

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
        url: baseUrl + '/admin/categories/edit/' + categoryId + '?page=otherLang' + '&lang=' + $(this).val(),
        dataType: 'json',
        success: function (response)
        {
            if(response.status == true)
            {
                $('#title').val(response.categoryDetails.title);
                CKEDITOR.instances['description'].setData(response.categoryDetails.description)
                $('#meta_title').val(response.categoryDetails.meta_title);
                $('#meta_keywords').val(response.categoryDetails.meta_keywords);
                $('#meta_description').val(response.categoryDetails.meta_description);
                if($('#defaultLanguage').val() == defaultLanguageId)
                    $("#selected_pic").show();
                else
                    $("#selected_pic").hide();

                if(defaultLanguageId == langId)
                {
                    if(response.categoryDetails.category_image != null)
                    {
                        $("#selected_pic").show();
                        src1 = baseUrl + '/public/assets/images/categories/' + response.categoryDetails.category_image;
                        $("#selected_pic").attr("src", src1);
                    }
                    else
                        $("#selected_pic").hide();

                    if(response.categoryDetails.cat_banner != null)
                    {
                        $("#selected_banner").show();
                        src2 = baseUrl + '/public/assets/images/categories/banner/' + response.categoryDetails.cat_banner;
                        $("#selected_banner").attr("src", src2);
                    }
                    else
                        $("#selected_banner").hide();

                    if(response.categoryDetails.cat_mob_banner != null)
                    {
                        $("#selected_mobile_banner").show();
                        src4 = baseUrl + '/public/assets/images/categories/banner/' + response.categoryDetails.cat_mob_banner;
                        $("#selected_mobile_banner").attr("src", src4);
                    }
                    else
                        $("#selected_mobile_banner").hide();
                }
                else
                {
                    if(response.categoryDetails.banner_image != null)
                    {
                        $("#selected_banner").show();
                        src2 = baseUrl + '/public/assets/images/categories/banner/' + response.categoryDetails.banner_image;
                        $("#selected_banner").attr("src", src2);
                    }
                    else
                        $("#selected_banner").hide();

                    if(response.categoryDetails.mobile_banner != null)
                    {
                        $("#selected_mobile_banner").show();
                        src3 = baseUrl + '/public/assets/images/categories/mobile_banner/' + response.categoryDetails.mobile_banner;
                        $("#selected_mobile_banner").attr("src", src3);
                    }
                    else
                        $("#selected_mobile_banner").hide();
                }
            }
        }
    });
})


var formValidations = {
    formValidation : function() {
        $("#updateCategoryForm").validate({
            ignore: [], // ignore NOTHING
            rules: {
                "title": {
                    required: true,
                },
                "slug": {
                    required: true,
                },
                "category_image": {
                    accept: "jpg,jpeg,png"
                },
                "banner_image" :{
                    accept: "jpg,jpeg,png"
                },
                "mobile_banner_image" :{
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
