// show image in popup
$(document).on("click", ".openImgInGallery", function () {
    var imgsrc = $(this).data('id');
    $('#galleryImage').attr('src',imgsrc);
});

// show image in print popup
$(document).on("click", ".printImg", function () {
    var printimgsrc = $(this).data('id');
    $('#printImage').attr('src',printimgsrc);
});

// show selected images and price
var selectedImages = '';
var selectedImagesArray = [];
var totalPrice = parseFloat($('#selectedImagePrice').val());

$(".imgCheckbox").click(function() {
    selectedImages = JSON.parse('[' + $('#selectedImagesToBuy').val() + ']');
    selectedImagesArray = selectedImages;

    if(this.checked)
    {
        var currentCount = $('#checkedImagesCount').val();

        $('#checkedImagesCount').val(parseInt(currentCount) + 1);
        currentCount = $('#checkedImagesCount').val();
        
        price = $('#checkedImagesCount').val() * pricePerPhoto;

        $('#selectedImagePrice').val(price)

        totalPrice = parseFloat($('#selectedImagePrice').val());
        
        var iNum = parseInt(this.value);
        selectedImagesArray.push(iNum);

        $('#selectedPrice').html($('#checkedImagesCount').val() + ' ' + selectedLabel + ' (' + currencyCode + ' ' + totalPrice.toFixed(3) + ')')
    }
    else
    {
        var currentCount = $('#checkedImagesCount').val();
        $('#checkedImagesCount').val(parseInt(currentCount) - 1);
        currentCount = $('#checkedImagesCount').val();

        price =  $('#checkedImagesCount').val() * pricePerPhoto;
        $('#selectedImagePrice').val(price)

        totalPrice = parseFloat($('#selectedImagePrice').val());

        var removeItem = this.value;

        selectedImagesArray = jQuery.grep(selectedImagesArray, function(value) {
        return value != removeItem;
        });
        
        $('#selectedPrice').html($('#checkedImagesCount').val() + ' ' + selectedLabel + ' (' + currencyCode + ' ' + totalPrice.toFixed(3) + ')')
    }
    $('#selectedImagePrice').val(price);

    if($('#checkedImagesCount').val() == 0)
        selectedImagesArray = '';
    else
    {
        var uniquePhotoIds = [];
        $.each(selectedImagesArray, function(i, el){
            
            if($.inArray(el, uniquePhotoIds) === -1) uniquePhotoIds.push(el);
        });
        
        $('#selectedImagesToBuy').val(uniquePhotoIds);

    }

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
        type: "POST",
        url: baseUrl + '/customer/setSelectedImages',
        data: {
            'selectedImages' : uniquePhotoIds,
            'totalPrice' : $('#selectedImagePrice').val()
        },
        success: function(response)
        {
            if(response.status == true)
            {
            }
        }
    });
});


if($('#checkedImagesCount').val() > 0)
{
    $('#selectedPrice').html($('#checkedImagesCount').val() + ' ' + selectedLabel + ' (' + currencyCode + ' ' + totalPrice.toFixed(3) + ')')
}

// numberpicker
(function ($) {
    $.fn.numberPicker = function() {
      var dis = 'disabled';
      return this.each(function() {
        var picker = $(this),
            p = picker.find('button:last-child'),
            m = picker.find('button:first-child'),
            input = picker.find('input'),
            min = parseInt(input.attr('min'), 10),
            max = parseInt(input.attr('max'), 10),
            inputFunc = function(picker) {
              var i = parseInt(input.val(), 10);
              if ( (i <= min) || (!i) ) {
                input.val(min);
                p.prop(dis, false);
                m.prop(dis, true);
              } else if (i >= max) {
                input.val(max);
                p.prop(dis, true);
                m.prop(dis, false);
              } else {
                p.prop(dis, false);
                m.prop(dis, false);
              }
            },
            changeFunc = function(picker, qty) {
              var q = parseInt(qty, 10),
                  i = parseInt(input.val(), 10);

              if ((i < max && (q > 0)) || (i > min && !(q > 0))) {
                input.val(i + q);
                inputFunc(picker);
              }
            };
        m.on('click', function(){changeFunc(picker,-1);});
        p.on('click', function(){changeFunc(picker,1);});
        input.on('change', function(){inputFunc(picker);});
        inputFunc(picker); //init
      });
    };
  }(jQuery));

$(document).ready(function(){
    $('.plusminus').numberPicker();
})



// set selected product name
$('#selectedProdName').html($('#productNames').find(":selected").text());
var selected = $('#productNames').find(':selected');
var optionId = selected.data('option');
$('#selectedOptionId').val(optionId)

$('#productNames').on('change', function(){
    $('#selectedProdName').html($('#productNames').find(":selected").text());
    selected = $('#productNames').find(':selected');
    optionId = selected.data('option');
    $('#selectedOptionId').val(optionId);

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
        method: "post",
        url: baseUrl +'/customer/getSelectedProdPrice',
        data:{
            'option_id' : optionId,
        },
        success: function (response)
        {
            if(response.status == true)
            {
                $('#prodPrice').html(response.prodDetails.price);
                if(response.prodDetails.discountedPrice != '' && response.prodDetails.discountedPrice != null)
                    $('#prodPrice').html('<strike>' + response.prodDetails.price.replace(/,/g, '') + '</strike><span class="text-danger prodPrice" data-price='+ response.prodDetails.discountedPrice.replace(/,/g, '') +'> ' + response.prodDetails.discountedPrice.replace(/,/g, '') + '</span>');
                else if(response.prodDetails.group_price != '' && response.prodDetails.group_price != null)
                    $('#prodPrice').html('<strike>' + response.prodDetails.price.replace(/,/g, '') + '</strike><span class="text-danger prodPrice" data-price='+ response.prodDetails.group_price.replace(/,/g, '') +'> ' + response.prodDetails.group_price.replace(/,/g, '') + '</span>');
                else
                    $('#prodPrice').html(response.prodDetails.price.replace(/,/g, ''));
            }
        },
        error: function(xhr, status, error) {
        },
    });
})

$(".addToCart").click(function(){
    var option_id = $('#selectedOptionId').val();
    var qty = document.getElementById('productQty').value;
    var product_id = $("#productNames option:selected").val();
    var image = $('#printImage').attr('src');
    var shoppingmsg = '';
    var formsg = 0;
    var ladyoperator = 0;

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
        method: "post",
        url: baseUrl +'/add-to-cart',
        data:{
            'option_id' : option_id,
            'qty' : qty,
            'product_id' : product_id,
            'image' : image,
            'shoppingmsg' : shoppingmsg,
            'formsg' : formsg,
            'ladyoperator' : ladyoperator,
        },
        success: function (response)
        {
            $(".badge-icon").html(response.count);
            $('#print-modal').modal('hide');
            toastr.success(response.msg);
        },
        error: function(xhr, status, error) {
            console.log(status);
        },
    });
});

$(".buyNow").click(function(){
    var option_id = $('#selectedOptionId').val();
    var qty = document.getElementById('productQty').value;
    var product_id = $("#productNames option:selected").val();
    var image = $('#printImage').attr('src');
    var shoppingmsg = '';
    var formsg = 0;
    var ladyoperator = 0;

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
        method: "post",
        url: baseUrl +'/add-to-cart',
        data:{
            'option_id' : option_id,
            'qty' : qty,
            'product_id' : product_id,
            'image' : image,
            'shoppingmsg' : shoppingmsg,
            'formsg' : formsg,
            'ladyoperator' : ladyoperator,
        },
        success: function (response)
        {
            url = baseUrl+'/shopping-cart';
            window.location.replace(url);
        }
    });
});