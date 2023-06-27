$( document ).ready(function() {
    init.handler();
    formValidations.formValidation();
    appendHtml.appendNonDefaultLanguage(nonDefaultLanguage);
    $('#status option[text="' + attr_group['status'] +'"]').prop("selected", true);
    $('#defaultLanguage option[value="' + attr_group['languageId'] +'"]').prop("selected", true);
});

var init = {
    handler : function() {
        $('body').on('change','#defaultLanguage', function() {
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
                   if(response.status == true)
                   {
                        $('#display_name').val(response.attr_group.display_name);
                        $('#name').val(response.attr_group.name);
                   }
                }
            });
        })
    }
};

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
        $("#updateAttriGroupForm").validate({
            ignore: [],  // ignore NOTHING
            rules: {
                    "display_name": {
                        required: true,
                    },
                    "name": {
                        required: true,
                    },
                    'sort_order': {
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
                    }
                },
            });        
        }
    }