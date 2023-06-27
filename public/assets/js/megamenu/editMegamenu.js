$(document).ready(function()
{
    showRelatedBlock(type);
    console.log(small_image);
    if(small_image != null)
    {
        console.log('small');
        var src1 = baseUrl + '/public/assets/images/megamenu/small/' + small_image;
        $("#selected_small_pic").attr("src", src1);
    }
    else
    {
        var src1 = baseUrl + '/public/assets/images/no_image.png'
        $("#selected_small_pic").attr("src", src1);
    }

    if(big_image != null)
    {
        var src1 = baseUrl + '/public/assets/images/megamenu/big/' + big_image;
        $("#selected_big_pic").attr("src", src1);
    }
    else
    {
        var src1 = baseUrl + '/public/assets/images/no_image.png'
        $("#selected_big_pic").attr("src", src1);
    }

    if(icon_image != null)
    {
        var src1 = baseUrl + '/public/assets/images/megamenu/icon/' + icon_image;
        $("#selected_icon_image").attr("src", src1);
    }
    else
    {
        var src1 = baseUrl + '/public/assets/images/no_image.png'
        $("#selected_icon_image").attr("src", src1);
    }

    $('#selectDropdownOnRadio option[value="' + selected_dropdown +'"]').prop("selected", true);

}); 

var appendHtml = {
    dropdown : function (dropdownValue) {
        var selectDropdownOnRadio = document.getElementById('selectDropdownOnRadio');
        $('#selectDropdownOnRadio'). empty(); 
        $.each(dropdownValue, function (index,item) {
            var text = item['title'];
            var value = item['id'];
            var o = new Option(text, value);
            selectDropdownOnRadio.append(o);
        })
    }
}

function showRelatedBlock(type) 
{
    if(type == 0)
    {
        $('#radioRelatedBlock').find("label").text('Page');
        appendHtml.dropdown(cms_pages);
    }
    if(type == 1)
    {
        $('#radioRelatedBlock').find("label").text('Category');
        appendHtml.dropdown(categories);
        $('#selectDropdownOnRadio option[value="' + selected_dropdown +'"]').prop("selected", true);
    }
}

/** edit megamenu form validation */
$("#updateMegamenuForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "sort_order": {
            required: true,
        },
        "small_image" :{
            required:false,
            accept: "jpg,jpeg,png"
        },
        "big_image" :{
            required:false,
            accept: "jpg,jpeg,png"
        },
        "icon_image" :{
            required:false,
            accept: "jpg,jpeg,png"
        }
    },
    messages: {
        "sort_order": {
            required: "Please enter sort order"
        },
        'small_image' :{
            accept: "Please upload file in these format only (png, jpg, jpeg)."
        },
        'big_image' :{
            accept: "Please upload file in these format only (png, jpg, jpeg)."
        },
        'icon_image' :{
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
