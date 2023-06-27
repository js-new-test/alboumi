$(document).ready(function(){
  $('#forMSG').click(function(){
    if($(this).is(':checked'))
    {
        $('.showWhenChecked').html('<p class="s1 mb12">'+writemsg+'</p><input type="text" name="textmsg" id="textmsg" maxlength="100" class="input wth" value="'+amazmsg+'">');
    }
    else
    {
        $('.showWhenChecked').html('');
    }
  });
  $('#multipleimage').click(function(){
    if($(this).is(':checked'))
    {
        $('#real-file').attr("multiple","multiple");
    }
    else
    {
        $('#real-file').attr("multiple",false);
    }
  });
})

	function openTab(evt, tabName) {
	  var i, tabcontent, tablinks;
	  tabcontent = document.getElementsByClassName("tabcontent1");
	  for (i = 0; i < tabcontent.length; i++) {
	    tabcontent[i].style.display = "none";
	  }
	  tablinks = document.getElementsByClassName("tablinks1");
	  for (i = 0; i < tablinks.length; i++) {
	    tablinks[i].className = tablinks[i].className.replace(" active", "");
	  }
	  document.getElementById(tabName).style.display = "block";
	  evt.currentTarget.className += " active";
	}
  if($('#defaultOpen').length>0){
	document.getElementById("defaultOpen").click();
  }



$(document).ready(function() {
    $("#size-preview").click(function(){
      $("body").addClass("scrol-hidden");
          $(".back-black").addClass("show-modals");
          $(".size-preview").addClass("show-for-modal-show");
    })

    $(".back-black").click(function(){
      $("body").removeClass("scrol-hidden");
        $(".back-black").removeClass("show-modals");
        $(".size-preview").removeClass("show-for-modal-show");
    })

    $(".closeModals").click(function(){
      $("body").removeClass("scrol-hidden");
        $(".back-black").removeClass("show-modals");
        $(".size-preview").removeClass("show-for-modal-show");
    })

});

//file upload
if($("#real-file").length){
const realFileBtn = document.getElementById("real-file");
const uploadFile = document.getElementById("upload-file");
const uploadTxt = document.getElementById("upload-text");

// uploadFile.addEventListener("click", function() {
//   realFileBtn.click();
// });

// realFileBtn.addEventListener("change", function() {
//   if (realFileBtn.value) {
//     uploadTxt.innerHTML = realFileBtn.value.match(
//       /[\/\\]([\w\d\s\.\-\(\)]+)$/
//     )[1];
//   } else {
//     uploadTxt.innerHTML = "No file chosen, yet.";
//   }
// });
}


// change color name

$(document).ready(function(){
  if (document.querySelector("#colornamefield") != null )
  var cn=document.getElementById("colornamefield").value;
  else
  var cn='';
	$(".colorName").html(cn);
	if ($(".colorName")){
		var colorName = $(".colorName").text().trim().toUpperCase();
		var a = document.getElementsByClassName("forBorder");
		for (var i = 0; i < a.length; i++ ) {
        	if(colorName == a[i].children[0].className.trim().toUpperCase()){
        		a[i].classList.add("activeColor")
        	}
        	else{
        		a[i].classList.remove("activeColor")
        	}
        }
		$(".forBorder").click(function(){
			var a = document.getElementsByClassName("forBorder");
			$(".colorName").text($(this).children().attr('class'))
      $("#color_id").val($(this).children().attr('id'))
      var colorName = $(".colorName").text().trim();
	        for (var i = 0; i < a.length; i++ ) {
	           a[i].classList.remove("activeColor");
	        }
          document.getElementById("colornamefield").value=colorName;
	        $(this).addClass("activeColor");
					getAttributeGroupData();
		})
	}
})

//$(document).ready(function(){
  var _URL = window.URL || window.webkitURL;
  function checkforext(d){
    const imagename=d.name;
    const extension = imagename.split('.').pop().toLowerCase();
    var validFileExtensions = ['jpeg', 'jpg', 'png'];
    if ($.inArray(extension, validFileExtensions) == -1) {
      //toastr.error(filevalid);
      return 'Invalid';
    }
    else{
      return 'Valid';
    }
  }
  $(document).ready(function(){
  $("#real-file").on("change", function(){
    var len_files = $("#real-file").prop("files").length;;
    if(len_files>0 && len_files<=maxImage){
    var _URL = window.URL || window.webkitURL;
      //validation on selected images
    var files = document.getElementById('real-file').files;
    var newFileList = Array.from(files);
    for(var i = 0;i<len_files;i=i+1){
      // extension validation
        var d = $("#real-file").prop("files")[i];
      //  var image= $('#real-file')[i].files[i];
      //check with  document upload by changing type and see if it allows or not..it should not.
        var blob = $("#real-file").prop("files")[i]; // See step 1 above
        var fileReader = new FileReader();
        fileReader.onloadend = function(e) {
          var arr = (new Uint8Array(e.target.result)).subarray(0, 4);
          var header = "";
          for(var i = 0; i < arr.length; i++) {
             header += arr[i].toString(16);
          }
          switch (header) {
            case "89504e47":
                type = "image/png";
                break;
            case "47494638":
                type = "image/gif";
                break;
            case "ffd8ffe0":
            case "ffd8ffe1":
            case "ffd8ffe2":
            case "ffd8ffe3":
            case "ffd8ffe8":
                type = "image/jpeg";
                break;
            default:
                type = "unknown"; // Or you can use the blob.type as fallback
                break;
        }
          if(type == "unknown"){
            toastr.error(filevalid);
            return false;
          }
        };
        fileReader.readAsArrayBuffer(blob);
        //End check with  document upload by changing type and see if it allows or not..it should not.
        var imagename=d.name;
        var extension = imagename.split('.').pop().toLowerCase();
        var validFileExtensions = ['jpeg', 'jpg', 'png'];
        if ($.inArray(extension, validFileExtensions) == -1) {
          toastr.error(filevalid);
          return false;
        }
        // resolution validation
        img = new Image();
        img.src = _URL.createObjectURL(files[i]);
        img.onload = function () {
            imgwidth = this.width;
            imgheight = this.height;
            if(imgwidth < imageMinWidth || imgheight < imageMinHeight){
              toastr.error(resolutionMsg);
              return false;
            };
            if(imgwidth > imageMaxWidth || imgheight > imageMaxHeight){
              toastr.error(resolutionMsg);
              return false;
            };
          }
      }
      $('.image-counts').html(len_files+' image(s) selected.');
      $('.image-counts').show();
      $("#UploadPhoto").click();
    }
    else{
      toastr.error(uploadLimit);
      return false;
    }
  });
});



// side bar js

// $(document).ready(function(){
//   $(".menuIcons").on("click", function(){
//     $(".sideMenu").toggleClass("active");
//     $("body").toggleClass("scroll-stop")
//   });
//   $(".closeIcons").on("click", function(){
//     $(".sideMenu").toggleClass("active");
//     $("body").toggleClass("scroll-stop")
//   });
// });


$(document).ready(function(){
	//var frame_size = $("#frameSize").val();
  $.each( grrpArr, function( i, val ) {
  var frame_size = $( "#"+val+" option:selected" ).text();
	$("."+val).text(frame_size);

	$("#"+val).change(function(){
		var frame_size = $( "#"+val+" option:selected" ).text();
		$("."+val).text(frame_size);
	})
  });
})

$(document).ready(function(){
  $.each( grrpArrRadio, function( i, val ) {
    var finish_text=$("input[name='"+val+"']:checked").data('name');
	$("."+val+"_Name").text(finish_text);
	$('input[type=radio][name='+val+']').change(function(){
		var finish_text=$("input[name='"+val+"']:checked").data('name');
		$("."+val+"_Name").text(finish_text);
	})
  });
  // var finish_text=$("input[name='finish']:checked").data('name');
	// $(".finishName").text(finish_text);
  //
	// $("#finishName").change(function(){
	// 	var finish_text=$("input[name='finish']:checked").data('name');
	// 	$(".finishName").text(finish_text);
	// })
})



jQuery(document).ready(function(){

  var $this = $('.items');
  if ($this.find('div').length > 5) {
      $('.items').append('<div><a href="javascript:;" class="showMore"></a></div>');
  }

  // If more than 2 Education items, hide the remaining
	$('.items .item').slice(0,5).addClass('shown');
	$('.items .item').not('.shown').hide();
	$('.items .showMore').on('click',function(){
		$('.items .item').not('.shown').toggle(300);
		$(this).toggleClass('showLess');
	});

});



jQuery(document).ready(function(){

  var $this = $('.all-size-tables');
  if ($this.find('table').length > 2) {
      $('.all-size-tables').append('<div class="plus-sign"><a href="javascript:;" class="showMore"></a></div>');
  }

  // If more than 2 Education items, hide the remaining
	$('.all-size-tables .sizeTables').slice(0,1).addClass('shown');
	$('.all-size-tables .sizeTables').not('.shown').hide();
	$('.all-size-tables .showMore').on('click',function(){
		$('.all-size-tables .sizeTables').not('.shown').toggle();
		$(this).toggleClass('showLess');
	});

});
  $(document).ready(function() {
    if(langVisibility == 0)
            rtl = false;
        if(langVisibility == 1)
            rtl = true;
    $('.owl-carousel.owl2').owlCarousel({
      rtl : rtl,
      loop: false,
      margin: 10,
      responsiveClass: true,
      nav: true,
      dots: false,
      responsive: {
        0: {
          items: 2.2,
          margin: 16,
          loop: false,
	      nav: false,
	      dots: false
        },
        575: {
          items: 2.4,
          margin: 20
        },
        768: {
          items: 3,
          margin: 20
        },
        992: {
          items: 3,
          margin: 24
        },
        1200: {
          items: 4,
          margin: 24
        }
      }
    })
  })



  $(document).ready(function() {
    if(langVisibility == 0)
            rtl = false;
        if(langVisibility == 1)
            rtl = true;
    $('.owl-carousel.owl3').owlCarousel({
      rtl : rtl,
      loop: false,
      margin: 10,
      responsiveClass: true,
      nav: true,
      dots: false,
      responsive: {
        0: {
          items: 2.2,
          margin: 16,
          loop: false,
	      nav: false,
	      dots: false
        },
        575: {
          items: 2.4,
          margin: 20
        },
        768: {
          items: 3,
          margin: 20
        },
        992: {
          items: 3,
          margin: 24
        },
        1200: {
          items: 4,
          margin: 24
        }
      }
    })
  })


   $(document).ready(function() {
     if(langVisibility == 0)
             rtl = false;
         if(langVisibility == 1)
             rtl = true;
    $('.owl-carousel.owl4').owlCarousel({
      rtl : rtl,
      loop: true,
      margin: 10,
      responsiveClass: true,
      nav: false,
      dots: true,
      responsive: {
        0: {
          items: 1,
          margin: 16
        }
      }
    })
  })

	function getAttributeGroupData(){
    attr_selected = [];
    $.each( grrpArr, function( i, val ) {
      	var atrribute_id=document.getElementById(val).value;
      attr_selected.push(atrribute_id);
    });
    attr_radio=[];
    $.each( grrpArrRadio, function( i, val ) {
      	var atrribute_idradio=$("input[name='"+val+"']:checked").val();
      attr_radio.push(atrribute_idradio);
    });
    var atrribute_id3='';
    if($('#color_id').length > 0){
		var atrribute_id3=document.getElementById('color_id').value;
   }
    var product_id=document.getElementById('product_id').value;
    var customer_id=customerId;
    var language_id=langId;
    $.ajax({
      type: "get",
      url: baseUrl +'/api/v1/product/getAttributeGroupData',
      data:{
          atrribute_id1 : attr_selected,
          atrribute_id2 : attr_radio,
          atrribute_id3 : atrribute_id3,
          product_id    : product_id,
          language_id   :language_id,
          customer_id :customer_id,
      },
      success: function (response)
      {
          if(response.status == "OK")
          {
            $('.plusminus').numberPicker('refresh');
            $('.addToCart').attr("disabled", false);
            $('.buyNow').attr("disabled", false);
            $('.designTool').attr("disabled", false);
            $('.addToCart').removeClass("fill-btn-disabled");
            $('.buyNow').removeClass("fill-btn-disabled");
            $('.designTool').removeClass("fill-btn-disabled");
                if(response.Data.offeraplicable==0){
                  $('.pricespan').html('<span class="selling_price">'+response.Data.price+'</span>');
                }
                else{
                  $('.pricespan').html('<span class="original_price" style="text-decoration:line-through;">'+response.Data.orignalprice+'</span><span class="selling_price" style="color:red;margin-left: 5px;">'+response.Data.price+'</span>');
                }
                //$('.selling_price').html(response.Data.price);
                $("#available_qty").val(response.Data.quantity);
                $("#productQty")
                  .attr("min", 1)
                  .attr("max", response.Data.quantity)
                  .val(1);
                $("#productQty").attr('value', 1);
                document.getElementById('selected_option_id').value=response.Data.id;
                var src =response.Data.image;
                $(".big_image_1").html('<img id="bigImage" class="" src='+ src +' class="openImgInGallery" data-id='+src+' data-toggle="modal" data-bigimage='+src+' data-target="#gallery-modal">');
                $(".big_image_mobile_1").html('<img src='+ src +'>');
          }
          else
          {
                $('.addToCart').attr('disabled','disabled');
                $('.buyNow').attr('disabled','disabled');
                $('.designTool').attr('disabled','disabled');
                $('.addToCart').addClass("fill-btn-disabled");
                $('.buyNow').addClass("fill-btn-disabled");
                $('.designTool').addClass("fill-btn-disabled");
                toastr.error(response.message);
          }
      }
  });
	}
  $(document).ready(function(){
  	$(".addToCart").click(function(){
      if($("#real-file").length){
         if( $('#real-file').val() == "" && document.getElementById('socialImgCount').value==""){
             toastr.error(uploadfile);
             return false;
         }
      }
        var option_id=document.getElementById('selected_option_id').value;
        var qty=document.getElementById('productQty').value;
        var product_id=document.getElementById('product_id').value;
        //if (document.querySelector("textmsg") != null ){ alert('aaya');
        if($('#textmsg').length > 0){
        var shoppingmsg=document.getElementById('textmsg').value;
        var valLength = shoppingmsg.length;
          if(valLength>100){
            toastr.error(charlimit);
            return false;
          }
        }
        else
        var shoppingmsg='';

        if($('#printstaffmsg').length > 0){
        var printstaffmsg=document.getElementById('printstaffmsg').value;
        var valLength = printstaffmsg.length;
          if(valLength>100){
            toastr.error(charlimit);
            return false;
          }
        }
        else
        var printstaffmsg='';
        if($('input[name="forMSG"]:checked').length > 0)
        var formsg=1;
        else
        var formsg=0;
        if($('input[name="ladyoperator"]:checked').length > 0)
        var ladyoperator=1;
        else
        var ladyoperator=0;
       // Photo book photobook caption
       if($('#photobook_caption').length > 0){
       var caption=document.getElementById('photobook_caption').value;
       var capLength = caption.length;
         if(capLength>100){
           toastr.error(charlimit);
           return false;
         }
       }
       else
       var caption='';

        var datacart = new FormData();
        datacart.append('option_id', option_id);
        datacart.append('qty', qty);
        datacart.append('product_id', product_id);
        datacart.append('shoppingmsg', shoppingmsg);
        datacart.append('formsg', formsg);
        datacart.append('ladyoperator', ladyoperator);
        datacart.append('printstaffmsg', printstaffmsg);
        datacart.append('caption', caption);
        //multiple file upload
        var len_files=0;
        if($("#real-file").length){
        var len_files = $("#real-file").prop("files").length;
        }
        if(len_files>0){
          var _URL = window.URL || window.webkitURL;
            //validation on selected images
          var files = document.getElementById('real-file').files;
          //add to cart ajax call
          var imagesnames='';
          var validimages='';
          for(var k = 0;k<len_files;k=k+1){
            // extension validation
              var d = $("#real-file").prop("files")[k];
              //var changetype=checkforchagetype();alert(changetype);
              const extvalid=checkforext(files[k]);
            //var dimvalid=checkheightwidth(d);console.log(dimvalid);
              if(extvalid=='Valid'){
                      var fileReader = new FileReader();
                      fileReader.fileName = files[k].name;
                      const iname=files[k].name;
                      fileReader.onloadend = function(e) {
                        var arr = (new Uint8Array(e.target.result)).subarray(0, 4);
                        var header = "";
                        for(var i = 0; i < arr.length; i++) {
                           header += arr[i].toString(16);
                        }
                        switch (header) {
                          case "89504e47":
                              type = "image/png";
                              break;
                          case "47494638":
                              type = "image/gif";
                              break;
                          case "ffd8ffe0":
                          type = "image/jpg";
                          break;
                          case "ffd8ffe1":
                          type = "image/jpeg";
                          break;
                          case "ffd8ffe2":
                          type = "image/jpeg";
                          break;
                          case "ffd8ffe3":
                          type = "image/jpeg";
                          break;
                          case "ffd8ffe8":
                              type = "image/jpeg";
                              break;
                          default:
                              type = "unknown"; // Or you can use the blob.type as fallback
                              break;
                      }
                      if(type == "unknown"){
                        if(imagesnames!='')
                        imagesnames = imagesnames + ","+iname
                        else
                        imagesnames = iname;
                        $('.invalid-images').html(invalidImages+':'+imagesnames);
                      //  $('.invalid-images').show();
                      }
                    };
                    fileReader.readAsArrayBuffer(files[k]);
                          img = new Image();
                          img.src = _URL.createObjectURL(files[k]);
                          const uploadfile=files[k];
                          const imgname = files[k].name;
                          img.onload = function () {
                              imgwidth = this.width;
                              imgheight = this.height;
                              if(imgwidth < imageMinWidth || imgheight < imageMinHeight){
                                if(imagesnames!='')
                                imagesnames = imagesnames + ","+imgname;
                                else
                                imagesnames = imgname;
                                $('.invalid-images').html(invalidImages+':'+imagesnames);
                                //$('.invalid-images').show();
                              }
                              else if(imgwidth > imageMaxWidth || imgheight > imageMaxHeight){
                                if(imagesnames!='')
                                imagesnames = imagesnames + ","+imgname;
                                else
                                imagesnames = imgname;
                                $('.invalid-images').html(invalidImages+':'+imagesnames);
                              //  $('.invalid-images').show();
                              }
                              else{
                                $(".addToCart").html(pleaseWait);
                                toastr.success(pleaseWaitUpload);
                                datacart.append('image', uploadfile);
                                if(validimages!='')
                                validimages = validimages + ","+imgname;
                                else
                                validimages = imgname;
                                $.ajaxSetup({
                                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                });
                                $.ajax({
                                  enctype: 'multipart/form-data',
                                  processData: false,  // tell jQuery not to process the data
                                  contentType: false,   // tell jQuery not to set contentType
                                  type: "post",
                                  url: baseUrl +'/add-to-cart',
                                  data:datacart,
                                  async: false,
                                  success: function (response)
                                  {
                                    $(".addToCart").html(addToCart);
                                    $('.accept-images').html(acceptedImages+':'+validimages);
                                    $('.accept-images').show();
                                    $('.invalid-images').show();
                                      toastr.success(response.msg);
                                      // update count
                                      $(".badge-icon").html(response.count);
                                  }
                              });
                              }
                        };
                  }
              else{
                if(imagesnames!='')
                imagesnames = imagesnames + ","+files[k].name;
                else
                imagesnames = files[k].name;
                $('.invalid-images').html(invalidImages+':'+imagesnames);
                //$('.invalid-images').show();
              }
        }
        }
        else if($('#socialImgCount').length >0){
          //add to cart ajax call
          var socialImgCount=document.getElementById('socialImgCount').value;
          for(var k = 0;k<socialImgCount;k=k+1){
            var d = document.getElementById('socialimage_'+k).value;
            datacart.append('image', d);
            $.ajaxSetup({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });
            $.ajax({
              enctype: 'multipart/form-data',
              processData: false,  // tell jQuery not to process the data
              contentType: false,   // tell jQuery not to set contentType
              type: "post",
              url: baseUrl +'/add-to-cart',
              data:datacart,
              success: function (response)
              {
                  toastr.success(response.msg);
                  // update count
                  $(".badge-icon").html(response.count);
              }
          });
        }
        }
        else{
          //add to cart ajax call
            $.ajaxSetup({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });
            $.ajax({
              enctype: 'multipart/form-data',
              processData: false,  // tell jQuery not to process the data
              contentType: false,   // tell jQuery not to set contentType
              type: "post",
              url: baseUrl +'/add-to-cart',
              data:datacart,
              success: function (response)
              {
                  toastr.success(response.msg);
                  // update count
                  $(".badge-icon").html(response.count);
              }
          });
        }
  		});
    $(".buyNow").click(function(){
      if($("#real-file").length){
         if( $('#real-file').val() == "" && document.getElementById('socialImgCount').value==""){
             toastr.error(uploadfile);
             return false;
         }
      }
          var option_id=document.getElementById('selected_option_id').value;
          var qty=document.getElementById('productQty').value;
          var product_id=document.getElementById('product_id').value;
          if($('#textmsg').length > 0){
          var shoppingmsg=document.getElementById('textmsg').value;
          var valLength = shoppingmsg.length;
            if(valLength>100){
              toastr.error(charlimit);
              return false;
            }
          }
          else
          var shoppingmsg='';

          if($('#printstaffmsg').length > 0){
          var printstaffmsg=document.getElementById('printstaffmsg').value;
          var valLength = printstaffmsg.length;
            if(valLength>100){
              toastr.error(charlimit);
              return false;
            }
          }
          else
          var printstaffmsg='';

          if($('input[name="forMSG"]:checked').length > 0)
          var formsg=1;
          else
          var formsg=0;
          if($('input[name="ladyoperator"]:checked').length > 0)
          var ladyoperator=1;
          else
          var ladyoperator=0;
          // Photo book photobook caption
          if($('#photobook_caption').length > 0){
          var caption=document.getElementById('photobook_caption').value;
          var capLength = caption.length;
            if(capLength>100){
              toastr.error(charlimit);
              return false;
            }
          }
          else
          var caption='';

          var datacart = new FormData();
          datacart.append('option_id', option_id);
          datacart.append('qty', qty);
          datacart.append('product_id', product_id);
          datacart.append('shoppingmsg', shoppingmsg);
          datacart.append('formsg', formsg);
          datacart.append('ladyoperator', ladyoperator);
          datacart.append('printstaffmsg', printstaffmsg);
          datacart.append('caption', caption);
          //multiple file upload
          var len_files=0;
          if($("#real-file").length){
          var len_files = $("#real-file").prop("files").length;
          }
          if(len_files>0){
            var _URL = window.URL || window.webkitURL;
              //validation on selected images
            var files = document.getElementById('real-file').files;
            //add to cart ajax call
            var imagesnames='';
            var validimages='';
            for(var k = 0;k<len_files;k=k+1){
              // extension validation
                var d = $("#real-file").prop("files")[k];
                //var changetype=checkforchagetype();alert(changetype);
                const extvalid=checkforext(files[k]);
              //var dimvalid=checkheightwidth(d);console.log(dimvalid);
                if(extvalid=='Valid'){
                        var fileReader = new FileReader();
                        fileReader.fileName = files[k].name;
                        const iname=files[k].name;
                        fileReader.onloadend = function(e) {
                          var arr = (new Uint8Array(e.target.result)).subarray(0, 4);
                          var header = "";
                          for(var i = 0; i < arr.length; i++) {
                             header += arr[i].toString(16);
                          }
                          switch (header) {
                            case "89504e47":
                                type = "image/png";
                                break;
                            case "47494638":
                                type = "image/gif";
                                break;
                            case "ffd8ffe0":
                            type = "image/jpg";
                            break;
                            case "ffd8ffe1":
                            type = "image/jpeg";
                            break;
                            case "ffd8ffe2":
                            type = "image/jpeg";
                            break;
                            case "ffd8ffe3":
                            type = "image/jpeg";
                            break;
                            case "ffd8ffe8":
                                type = "image/jpeg";
                                break;
                            default:
                                type = "unknown"; // Or you can use the blob.type as fallback
                                break;
                        }
                        if(type == "unknown"){
                          if(imagesnames!='')
                          imagesnames = imagesnames + ","+iname
                          else
                          imagesnames = iname;
                          $('.invalid-images').html(invalidImages+':'+imagesnames);
                        //  $('.invalid-images').show();
                        }
                      };
                      fileReader.readAsArrayBuffer(files[k]);
                            img = new Image();
                            img.src = _URL.createObjectURL(files[k]);
                            const uploadfile=files[k];
                            const imgname = files[k].name;
                            img.onload = function () {
                                imgwidth = this.width;
                                imgheight = this.height;
                                if(imgwidth < imageMinWidth || imgheight < imageMinHeight){
                                  if(imagesnames!='')
                                  imagesnames = imagesnames + ","+imgname;
                                  else
                                  imagesnames = imgname;
                                  $('.invalid-images').html(invalidImages+':'+imagesnames);
                                  //$('.invalid-images').show();
                                }
                                else if(imgwidth > imageMaxWidth || imgheight > imageMaxHeight){
                                  if(imagesnames!='')
                                  imagesnames = imagesnames + ","+imgname;
                                  else
                                  imagesnames = imgname;
                                  $('.invalid-images').html(invalidImages+':'+imagesnames);
                                  //$('.invalid-images').show();
                                }
                                else{
                                  $(".buyNow").html(pleaseWait);
                                  toastr.success(pleaseWaitUpload);
                                  datacart.append('image', uploadfile);
                                  if(validimages!='')
                                  validimages = validimages + ","+imgname;
                                  else
                                  validimages = imgname;
                                  $.ajaxSetup({
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                  });
                                  $.ajax({
                                    enctype: 'multipart/form-data',
                                    processData: false,  // tell jQuery not to process the data
                                    contentType: false,   // tell jQuery not to set contentType
                                    type: "post",
                                    url: baseUrl +'/add-to-cart',
                                    data:datacart,
                                    success: function (response)
                                    {
                                      $(".buyNow").html(buyNow);
                                      $('.accept-images').html(acceptedImages+':'+validimages);
                                      $('.accept-images').show();
                                      $('.invalid-images').show();
                                      url = baseUrl+'/shopping-cart';
                                      window.location.replace(url);
                                    }
                                });
                                }
                          };
                    }
                else{
                  if(imagesnames!='')
                  imagesnames = imagesnames + ","+files[k].name;
                  else
                  imagesnames = files[k].name;
                  $('.invalid-images').html(invalidImages+':'+imagesnames);
                  //$('.invalid-images').show();
                }
          }
          }
          else if($('#socialImgCount').length >0){
            var socialImgCount=document.getElementById('socialImgCount').value;
            //add to cart ajax call
            for(var k = 0;k<socialImgCount;k=k+1){
              var d = document.getElementById('socialimage_'+k).value;
              datacart.append('image', d);
              $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
              });
              $.ajax({
                enctype: 'multipart/form-data',
                processData: false,  // tell jQuery not to process the data
                contentType: false,   // tell jQuery not to set contentType
                type: "post",
                url: baseUrl +'/add-to-cart',
                data:datacart,
                success: function (response)
                {
                  url = baseUrl+'/shopping-cart';
                  window.location.replace(url);
                }
            });
          }
          }
          else{
            //add to cart ajax call
              $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
              });
              $.ajax({
                enctype: 'multipart/form-data',
                processData: false,  // tell jQuery not to process the data
                contentType: false,   // tell jQuery not to set contentType
                type: "post",
                url: baseUrl +'/add-to-cart',
                data:datacart,
                success: function (response)
                {
                  url = baseUrl+'/shopping-cart';
                  window.location.replace(url);
                }
            });
          }
  });
  //To add recommended to cart
  $(".addtocartrecommended").click(function(){
      var product_id=document.getElementById('product_id').value;
        $.ajaxSetup({
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
          type: "post",
          url: baseUrl +'/addRecommendedToCart',
          data:{"product_id":product_id},
          success: function (response)
          {
              toastr.success(response.msg);

              // update count
              $(".badge-icon").html(response.count);
          }
      });
    });


});

// redirect to design-tool
$(".designTool").click(function(){
  // if($("#real-file").length){
  //    if( $('#real-file').val() == ""){
  //        toastr.error('Please upload a file');
  //        exit;
  //    }
  // }
      var option_id=document.getElementById('selected_option_id').value;
      var qty=document.getElementById('productQty').value;
      var product_id=document.getElementById('product_id').value;
      if($('#textmsg').length > 0){
      var shoppingmsg=document.getElementById('textmsg').value;
      var valLength = shoppingmsg.length;
        if(valLength>100){
          toastr.error(charlimit);
          return false;
        }
      }
      else
      var shoppingmsg='';

      if($('#printstaffmsg').length > 0){
      var printstaffmsg=document.getElementById('printstaffmsg').value;
      var valLength = printstaffmsg.length;
        if(valLength>100){
          toastr.error(charlimit);
          return false;
        }
      }
      else
      var printstaffmsg='';

      if($('input[name="forMSG"]:checked').length > 0)
      var formsg=1;
      else
      var formsg=0;
      if($('input[name="ladyoperator"]:checked').length > 0)
      var ladyoperator=1;
      else
      var ladyoperator=0;
      // Photo book photobook caption
      if($('#photobook_caption').length > 0){
      var caption=document.getElementById('photobook_caption').value;
      var capLength = caption.length;
        if(capLength>100){
          toastr.error(charlimit);
          return false;
        }
      }
      else
      var caption='';
      var backurl=baseUrl+'/product/'+slug;
      var datacart = new FormData();
      datacart.append('option_id', option_id);
      datacart.append('qty', qty);
      datacart.append('product_id', product_id);
      datacart.append('shoppingmsg', shoppingmsg);
      datacart.append('formsg', formsg);
      datacart.append('ladyoperator', ladyoperator);
      datacart.append('customerId', customerId);
      datacart.append('cartMasterId', cartMasterId);
      datacart.append('printstaffmsg', printstaffmsg);
      datacart.append('backurl', backurl);
      datacart.append('caption', caption);
      // if($("#real-file").length){
      //   var d = $('#real-file')[0].files[0];
      //   var imagename=$('#real-file').val();
      //   var extension = imagename.split('.').pop().toLowerCase();
      //   var validFileExtensions = ['jpeg', 'jpg', 'png'];
      //   if ($.inArray(extension, validFileExtensions) == -1) {
      //     toastr.error('Please upload file in these format only (png, jpg, jpeg).');
      //     return false;
      //   }
      // datacart.append('image', d)
      // }
      $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
      });
      $.ajax({
        enctype: 'multipart/form-data',
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        type: "post",
        url:baseUrl +'/design-tool/storecart.php',
        data:datacart,
        success: function (response)
        {
          url = baseUrl +'/design-tool/editor.php?product_base='+productBase;
          window.location.replace(url);
        }
    });
});


// Added by Pallavi (22 March 2021)
$(function () {
    $('.smallImageHover').hover( function () {
        $('.carousel-item').each(function(index, value) {
          $(this).removeClass('active');
        });
        $('.carousel-inner').find('.carousel-item:first' ).addClass('active');
        $('#bigImage').attr('src', $(this).attr('src').replace(/\.jpg/, '.jpg') );
        $('#bigImage').attr('data-id', $(this).attr('src').replace(/\.jpg/, '.jpg') );
        $('#bigImage').attr('data-bigimage', $(this).attr('src').replace(/\.jpg/, '.jpg') );
        $(".carousel").carousel('pause');
    }, function() {
      $(".carousel").carousel('cycle');
    });
});

// show image in popup
$(document).on("click", ".openImgInGallery", function () {
    console.log($(this));
    var imgsrc = '';
    imgsrc = $(this).attr('data-id');
    console.log(imgsrc);
    $('#galleryImage').removeAttr('src');
    $('#galleryImage').attr('src',imgsrc);
});

$(document).on("click", ".changeQty", function () {
    var productQty = $('#productQty').val();
    $.ajax({
      type: "get",
      url: baseUrl +'/getNextDeliveryDate/'+productQty,
      success: function (response)
      {
          if(response)
          {
              $('#deliverydate').html(response);
          }
      }
  });
});

//initiate the plugin and pass the id of the div containing gallery images
// $(window).on("load", function() {
//     $("#zoom_03").elevateZoom({
//         gallery:'gallery_01',
//         cursor: 'pointer',
//         easing : true,
//         galleryActiveClass: 'active',
//         imageCrossfade: true,
//         loadingIcon: 'https://www.elevateweb.co.uk/spinner.gif'
//     });
// });

// //pass the images to Fancybox
// $("#zoom_03").bind("click", function(e) {
// 	var ez = $('#zoom_03').data('elevateZoom');
// 	$.fancybox(ez.getGalleryList());
// 	return false;
// });
