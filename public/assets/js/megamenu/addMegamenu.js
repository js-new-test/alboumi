$(document).ready(function(){
    functionCall.showRelatedBlock();
}); 

var functionCall = {
    showRelatedBlock : function ()
    {
        $('#radioRelatedBlock').find("label").text('Page');
        appendHtml.dropdown(cms_pages);
    }
}

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

function showRelatedBlock() 
{
    var checkedValue = $('input[name="type"]:checked').val();
    if(checkedValue == 0)
    {
        $('#radioRelatedBlock').find("label").text('Page');
        appendHtml.dropdown(cms_pages);
    }
    if(checkedValue == 1)
    {
        $('#radioRelatedBlock').find("label").text('Category');
        appendHtml.dropdown(categories);
    }
}

/** add menu form validation */
$("#addMegamenuForm").validate({
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