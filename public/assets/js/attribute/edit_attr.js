$( document ).ready(function() {

    formValidations.formValidation();
    appendHtml.appendNonDefaultLanguage(nonDefaultLanguage);

    $('#imageBox').hide();
    $('#colorBox').hide();

    $('#status option[text="' + attr_group['status'] +'"]').prop("selected", true);
    $('#defaultLanguage option[value="' + attr_group['languageId'] +'"]').prop("selected", true);

    if($('#defaultLanguage option:selected').val() == defaultLanguageId)
    {
        $('a[href$="category_tree"]:first').show();
        
        // show inputs based on attribute group change and on load
        $('#attribute_group_id').on('change', function(){
            ajaxCall.showInputsForAttrType($(this).val());
        })

        if(attr_type == 'C')
        {
            $('#existingcolorpickerBox').show();
            $('#selected_attr_image').hide();
            $('#existingImage').hide();
            $('input[id*=color').addClass('colorpicker-default');
            $('.multi').show();
            $('.colorpicker-default').colorpicker();
            $('#existing_color').attr('required', 'true');
        }
        else if(attr_type == 'I')
        {
            $('#existingImage').hide();
            $('#imageBox').show();
            $('#selected_attr_image').show();
            $('#existingcolorpickerBox').hide();

            var imgText = '';
            imgText += '<small class="form-text text-muted">Image size should be 1000 X 1000 px.</small> <small class="form-text text-muted">width = <small id="width"></small></small> <small class="form-text text-muted">height = <small id="height"></small></small>'
            $('#img_text').append(imgText);
            var src1 = baseUrl + '/public/assets/images/attributes/' + image;
            $("#selected_attr_image").attr("src", src1);
        }
        else if(attr_type == 'D' || attr_type == 'R')
        {
            $('#existingcolorpickerBox').hide();
            $('#selected_attr_image').hide();
            $('#existingImage').hide();
            $('#colorBox').hide();
            $('#imageBox').hide();
        }
    }   
});

$('body').on('change','#defaultLanguage', function() {

    if($(this).val() == defaultLanguageId)
    {
        $('a[href$="category_tree"]:first').show();
        $('.commonElement').removeClass('d-none');
    }
    else
    {
        $('.commonElement').addClass('d-none');
        $('a[href$="category_tree"]:first').hide();
    }
    jQuery.ajax({
        type: "get",
        url: window.location.href + '?page=otherLang' + '&lang=' + $(this).val(),
        async: true,
        dataType: 'json',
        success: function (response) 
        {
            if(response.status == true)
            {
                $('#display_name').val(response.attribute.display_name);
                $('#name').val(response.attribute.name);
                $('#sort_order').val(response.attribute.sort_order);
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

var formValidations = {
    formValidation : function() {
        $("#attribute_update_form").validate({
            ignore: [], // ignore NOTHING
            rules: {
                "display_name": {
                    required: true,
                },
                "name": {
                    required: true,
                },
                'sort_order': {
                    required: true
                },
                'attribute_group_id': {
                    required: true
                }
            },
            messages: {
                "display_name": {
                    required: "Please enter display name"
                },
                "name": {
                    required: "Please enter name",
                },
                'sort_order': {
                    required: "Please enter sort order"
                },
                'attribute_group_id': {
                    required: 'Please select attribute group'
                }
            },
            submitHandler: function(form) 
            {
                var ext = $('#image').val().split('.').pop().toLowerCase();
                if(ext != '')
                {
                    if($.inArray(ext, ['png','jpg','jpeg']) == -1) 
                    {
                        $('#invalidExt_error').html('You can only upload png,jpg or jpeg image');
                        $('#invalidExt_error').addClass('error');
                        $('#invalidExt_error').css('display', 'block');
                    }
                    else
                    {
                        form.submit();
                    }
                }
                
                else
                {
                    form.submit();
                }
            }
        });
    }
}

var ajaxCall = {
    showInputsForAttrType:function(attributeGroupId){
        jQuery.ajax({
            type: "get",
            url: window.location.href + '/../../getAttributeType/' + attributeGroupId,
            async: true,
            dataType: 'json',
            success: function (response) 
            {
                if(response.status == true)
                {
                    if(response.attr_type.code == 'C')
                    {
                        $('input[id*=color').addClass('colorpicker-default');
                        $('.multi').show();
                        $('.colorpicker-default').colorpicker();
                        $('#color').attr('required', 'true');

                        $('#imageBox').hide();
                        if(attr_type == 'C')
                        {
                            $('#existingcolorpickerBox').show();
                        }
                        else
                        {
                            $('#colorBox').show();
                        }
                        $('#existingImage').hide();
                        $('#selected_attr_image').hide();
                        $('#image').attr('required', false);
                        $('#existing_image').attr('required',false);
                    }
                    else if(response.attr_type.code == 'I')
                    {
                        $('#image').attr('required', 'true');
                        $('#existingcolorpickerBox').hide();
                        $('#colorBox').hide();
                        $('#imageBox').show();
                        $('#existingImage').hide();
                    }
                    else if(response.attr_type.code == 'D' || response.attr_type.code == 'R')
                    {
                        $('#existingcolorpickerBox').hide();
                        $('#selected_attr_image').hide();
                        $('#existingImage').hide();
                        $('#colorBox').hide();
                        $('#imageBox').hide();
                    }
                }
            }
        });
    }
}
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

function setColorValue()
{
    $('#color').val($('#existing_color').val());
}