var _URL = window.URL || window.webkitURL;

$( document ).ready(function() {
    init.handler();
    formValidations.generalValidation();
});

var init = {
    handler : function () {
        $('#image').change(function () {
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
        $("form[name='addHowitWorks']").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                title: "required",
                language: "required",
                "image" :{
                    required:true,
                    accept: "jpg,jpeg,png"
                },
                description: {
                    required: true,
                },
                sortOrder: "required",
                status: "required",
                image: {
                    required: true,
                    extension: "jpg|jpeg|png"
                },
            },
            // Specify validation error messages
            messages: {
                title: "Please Enter Title",
                language: "Please Select Language",
                'image' :{
                    accept: "Please upload file in these format only (png, jpg, jpeg)."
                },
                sortOrder: "Please Enter Sort Order",
                description: {
                    required: "Please Enter Description",
                },
                status: "Please Select Status",
                image:{
                    required: "Please Select Image",
                    extension: "Please upload file in these format only (png, jpg, jpeg)."
                },
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
