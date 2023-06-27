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
        $("form[name='editHowitWorks']").validate({
            // Specify validation rules
            rules: {
                title: "required",
                language: "required",
                sortOrder: "required",
                description: {
                    required: true,
                },
                status: "required",
                "image" :{
                    required:false,
                    accept: "jpg,jpeg,png"
                }
            },
            // Specify validation error messages
            messages: {
                title: "Please Enter Title",
                language: "Please Select Language",
                sortOrder: "Please Enter Sort Order",
                description: {
                    required: "Please Enter Description",
                },
                status: "Please Select Status",
                'image' :{
                    accept: "Please upload file in these format only (png, jpg, jpeg)."
                },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    }
}