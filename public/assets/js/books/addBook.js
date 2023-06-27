var _URL = window.URL || window.webkitURL;

$( document ).ready(function() {

    init.handler();
    formValidations.generalValidation();
    // appendHtml.languageDropdown(language);
});

var init = {
    handler : function () {
        $('#bookImage').change(function () {
            var file = $(this)[0].files[0];
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
        });
    }
}


var formValidations = {
    //general form validations
    generalValidation : function() {
        // Initialize form validation on the registration form.
        // It has the name attribute "registration"
        $("form[name='addBook']").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                title: "required",
                language: "required",                
                link: "required",
                price: "required",
                sortOrder: "required",
                status: "required",
                description:"required",
                bookImage: {
                     required:true,
                    extension: "jpg|jpeg|png|"
                },

            },
            // Specify validation error messages
            messages: {
                title: "Please Enter Title",
                language: "Please Select Language",
                'bookImage' :{
                    required: "Please select image",
                    accept: "Please upload file in these format only (png, jpg, jpeg)."
                },
                link: "Please Enter Link",
                price: "Please Enter Price",
                sortOrder: "Please Enter Sort Order",
                status: "Please Select Status",
                bookImage : {
                    extension: "Please upload file in these format only (png, jpg, jpeg)."
                },
                description: "Please Enter Description",
              },

            submitHandler: function(form) {
                form.submit();
            }
        });
    }
}

// var appendHtml = {
//     languageDropdown : function (language) {
//         var defaultLanguage = document.getElementById('defaultLanguage');
//         $.each(language, function (index,item) {
//             var text = item['languageName'];
//             var value = item['globalLanguageId'];
//             var o = new Option(text, value);
//             defaultLanguage.append(o);
//         })
//     }
// }
