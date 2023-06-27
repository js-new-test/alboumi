$( document ).ready(function() {

    formValidations.generalValidation();
    appendHtml.languageDropdown(language);
});


var formValidations = {
    //general form validations
    generalValidation : function() {
        // Initialize form validation on the registration form.
        // It has the name attribute "registration"
        $("form[name='addManufacturer']").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                brandName: "required",
                slug: "required",
                status: "required",
            },
            // Specify validation error messages
            messages: {
                brandName: "Please Enter Brand Name",
                status: "Please Select Status",
                slug :"Please enter slug"
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    }
}

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

// Generate slug based on brand name entered
function generateSlug()
{
    var nameValue = $("#brandName").val();
    $("#slug").val(nameValue.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-').toLowerCase());
}

// Show image dimensions for service image
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