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

$("#attribute_create_form").validate({
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
        var defaultLanguage = document.getElementById('defaultLanguage');
        if(defaultLanguage == null)
        {
            var ext = $('#image').val().split('.').pop().toLowerCase();
            if(ext != '')
            {
                if($.inArray(ext, ['png','jpg','jpeg']) == -1) 
                {
                    $('#image-error').html('You can only upload png,jpg or jpeg image');
                    $('#image-error').addClass('error');
                    $('#image-error').css('display', 'block');
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
        else
        {
            form.submit();
        }
        
    }
});
