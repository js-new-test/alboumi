$(document).ready(function(){
    appendHtml.languageDropdown(language);
    var defaultLanguage = document.getElementById('defaultLanguage');
    if(defaultLanguage == null)
        appendHtml.categoryDropdown(defaultLanguage);
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
    },
    categoryDropdown : function(language) {
        $.ajax({
            type: "get",
            url:'../getcategories/' + language,
            success: function (response) {
                $.each(response, function (index,item) {
                    var text = item['category'];
                    var value = item['id'];
                    var o = new Option(text, value);
                    $('#category_ids').append(o);
                })
            }
        });
    }
}

/* Add attribute Group - Validations*/

$("#addAttriGroupForm").validate({
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