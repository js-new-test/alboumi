
$(document).ready(function(){
    appendHtml.languageDropdown(language);
});

// Append languages in dropdown for add in other lang
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


// add photographer validation
$("#addPhotographerForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "name": {
            required: true,
        },
        "location": {
            required: true,
        },
        'about': {
            required: true
        },
        'experience': {
            required: true
        },
        "profile_pic": {
            required: true,
            extension: "jpg|jpeg|png|"
        },
        "cover_photo" :{
            required: true,
            extension: "jpg|jpeg|png|"
        }
    },
    messages: {
        "name": {
            required: "Please enter name"
        },
        "location": {
            required: "Please enter location",
        },
        'about': {
            required: "Please write about you"
        },
        'experience': {
            required: 'Please enter experience',
        },
        "profile_pic": {
            required: "Please upload profile picture",
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        "cover_photo": {
            required: "Please upload cover photo",
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        }
    },
});
