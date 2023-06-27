$( document ).ready(function() {
    appendHtml.appendNonDefaultLanguage(nonDefaultLanguage);
    ajaxCall.getProduct($('#defaultLanguage').val(), "PRODUCT");
    ajaxCall.getProduct($('#defaultLanguage').val(), "EDITPRODUCT");
    ajaxCall.getPortfolio(photographer_id);
    var src1 = baseUrl + '/public/assets/images/photographers/' + profile_pic;
    $("#selected_profile_pic").attr("src", src1);

    var src2 = baseUrl + '/public/assets/images/photographers/' + conver_photo;
    $("#selected_cover_photo").attr("src", src2);
    /** Edit portfolio details */
    $('body').on('click', '#editPortfolio', function () {
        var portfolioId = $(this).attr('data');
        $('#editPortfolioModal').on('show.bs.modal', function(e){
            ajaxCall.loadEditPortfolio(portfolioId);
        });
        $('#editPortfolioModal').modal('show');
    });
    /** Delete Photographer for multi lang */
    $('body').on('click', '.portfolio_delete', function () {
        var portfolioId = $(this).attr('data');
        var message = "Are you sure?";
        $('#PortfolioDeleteModel').on('show.bs.modal', function(e){
          $('#message').text(message);
          $('body').on('click', '#deletePortfolio', function () {
              ajaxCall.deletePortfolio(portfolioId);
          });
        });
        $('#PortfolioDeleteModel').modal('show');
    });
});

var appendHtml = {
    appendNonDefaultLanguage : function (nonDefaultLanguage) {
        var defaultLanguage = document.getElementById('defaultLanguage');
        if(defaultLanguage != null)
        {
            $.each(nonDefaultLanguage, function (index,item) {
                var value = item['language_id'];
                var text = item['langEN'];
                var o = new Option(text, value);
                defaultLanguage.append(o);
            });
        }
    },
    //append Products dropdown
  	appendProductDropDown : function (products, elementId) {
        if (elementId == "PRODUCT") {
            $('#product_id').empty();
            var productId = document.getElementById('product_id');
          }
          if (elementId == "EDITPRODUCT") {
              $('#productId').empty();
              var productId = document.getElementById('productId');
            }
  		$.each(products, function(index, item) {
  			var value = item['id'];
  			var text = item['title'];
  			var o = new Option(text, value);
  			productId.append(o);
  		});
  	},

}

$('body').on('change','#defaultLanguage', function() {

    if($(this).val() == defaultLanguageId)
    {
        $('.commonElement').removeClass('d-none');
    }
    else
    {
        $('.commonElement').addClass('d-none');
    }
    jQuery.ajax({
        type: "get",
        url: window.location.href + '?page=otherLang' + '&lang=' + $(this).val(),
        async: true,
        dataType: 'json',
        success: function (response)
        {
            if(response.status == true)
            {
                $("#selected_profile_pic").hide();
                $("#selected_cover_photo").hide();
                $('#name').val(response.photographerDetails.name);
                $('#location').val(response.photographerDetails.location);
                $('#about').val(response.photographerDetails.about);
                $('#experience').val(response.photographerDetails.experience);
            }
        }
    });
})


// add photographer validation
$("#updatePhotographerForm").validate({
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
            extension: "jpg|jpeg|png|"
        },
        "cover_photo" :{
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
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        "cover_photo": {
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        }
    },
});
// add Portfolio validation
$("#addPortfolioForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "image": {
            required: true,
            extension: "jpg|jpeg|png|"
        },
        "product_id": {
            required: true,
        },
        'sort_order': {
            required: true
        },
    },
    messages: {
        "image": {
            required: "Please upload image",
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        "product_id": {
            required: "Please select product",
        },
        'sort_order': {
            required: "Please enter sord order"
        },
    },
});
//add portfolio submit event
$("#addPortfolioForm").submit(function () {
      var formData = new FormData(this);
      $.ajax({
          url: baseUrl+'/admin/photgraphers/addPortfolio',
          type: 'POST',
          data: formData,
          async: false,
          cache: false,
          contentType: false,
          processData: false,
          success: function (result)
            {
                if (result['success'] == true) {
                    toastr.success(result.message);
                } else {
                    toastr.error(result.message);
                }
                $('#addPortfolioModal').modal('hide');
                $('#portfolioList').DataTable().ajax.reload();

            },
            error: function(data)
            {
                console.log(data);
            }
      });
      return false;
  });
  // edit Portfolio validation
  $("#editPortfolioForm").validate({
      ignore: [], // ignore NOTHING
      rules: {
          "image": {
            extension: "jpg|jpeg|png|"
          },
          "product_id": {
              required: true,
          },
          'sort_order': {
              required: true
          },
      },
      messages: {
          "image": {
              extension: "Please upload file in these format only (png, jpg, jpeg)."
          },
          "product_id": {
              required: "Please select product",
          },
          'sort_order': {
              required: "Please enter sord order"
          },
      },
  });
  //edit portfolio submit event
  $("#editPortfolioForm").submit(function () {
        var formData = new FormData(this);
        $.ajax({
            url: baseUrl+'/admin/photgraphers/updatePortfolio',
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (result)
              {
                  if (result['success'] == true) {
                      toastr.success(result.message);
                  } else {
                      toastr.error(result.message);
                  }
                  $('#editPortfolioModal').modal('hide');
                  $('#portfolioList').DataTable().ajax.reload();
              },
              error: function(data)
              {
                  console.log(data);
              }
        });
        return false;
    });

  var ajaxCall = {
  	//get product list
  	getProduct : function (languageId, elementId) {
  		$.ajaxSetup({
  		    headers: {
  		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		    }
  		});

  		$.ajax({
            	type: 'get',
            	url: baseUrl +'/admin/product/products',
              data:{'languageId':languageId},
            	beforeSend: function() {
                 	$('#loaderimage').css("display", "block");
                 	$('#loadingorverlay').css("display", "block");
            	},
            	success: function (response) {
            		appendHtml.appendProductDropDown(response, elementId);
            	}
          });
  	},
    getPortfolio : function (photographer_id) {
  		$('#portfolioList').DataTable().destroy();
  		$('#portfolioList').DataTable({
              processing: true,
              serverSide: true,
              "ajax": {
  		        'type': 'get',
  		        'url': baseUrl+'/admin/photgraphers/portfolio/list',
  		        'data': {
  		           photographerId: photographer_id
  		        }
  		    },
              columns: [/*{
              	"target": 0,
                  "bSortable": false,
                  "visible":false,
                  "order":false,
              	"render": function ( data, type, row ) {
                      return "<input type='checkbox' class='relatedCheckBox' value='"+ row['id'] +"' data='"+ row['id'] +"'>";
                  },
              },*/
              {
                  "target": 0,
                  render: function (data, type, row)
                      {
                          return "<img src=\"" + baseUrl + '/public/assets/images/photographers/portfolio/' + row.image + "\" height=\"100\" width=\"100\"/>";
                      },
              },{
              	"target": 1,
              	"data":'title'
              },{
              	"target": 2,
              	"data":'sort_order'
              },{
                  "target": 3,
                  "bSortable": false,
                  "order":false,
                  "render": function ( data, type, row )
                  {
                      var output = "";
                      output += '<a ><i class="fa fa-edit" aria-hidden="true" id="editPortfolio" data-toggle="modal" data="'+row['id']+'" data-target="#editPortfolioModal"></i></a>&nbsp;&nbsp;'
                      output += '<a class="text-danger"><i class="fa fa-trash portfolio_delete" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#PortfolioDeleteModel"></i></a>';
                      return output;
                  }},]
          });
  	},
    loadEditPortfolio: function (id) {
        console.log(id);
        $.ajax({
            type: "get",
            url:baseUrl+'/admin/photgraphers/portfolio/getPortfolio',
            data:{'portfolioId':id},
            success: function (response) {
                if (response) {
                  $('#productId option[value="' + response.product_id +'"]').prop("selected", "selected");
                  $('#editPortfolioForm input[name=sort_order]').val(response.sort_order);
                  $('#statusEdit option[value="' + response.status +'"]').prop("selected", "selected");
                  var srcPortfolio = baseUrl + '/public/assets/images/photographers/portfolio/' + response.image;
                  $("#prodfolioImage").attr("src", srcPortfolio);
                  $('#editPortfolioForm input[name=portfolioId]').val(response.id);
                }
            }
        });
    },
    deletePortfolio: function (id) {
        console.log(id);
        $.ajax({
            type: "get",
            url:baseUrl+'/admin/photgraphers/deletePortfolio',
            data:{'portfolioId':id},
            success: function (response) {
                if (response['success'] == true) {
                  $('#PortfolioDeleteModel').modal('hide');
                  $('#portfolioList').DataTable().ajax.reload();
                    toastr.success(response.message);
                }
            }
        });
    },
  }
  // Show image dimensions for portfolio pic
  function _showPortfolioImgDimensions(image)
  {
      var file = image.files[0];
      img = new Image();
      var imgwidth = 0;
      var imgheight = 0;

      img.src = _URL.createObjectURL(file);
      img.onload = function() {
          imgwidth = this.width;
          imgheight = this.height;

          $("#portfolio_width").text(imgwidth);
          $("#portfolio_image_width").val(imgwidth);
      }
  };
  function _showPortfolioImgEditDimensions(image)
  {
      var file = image.files[0];
      img = new Image();
      var imgwidth = 0;
      var imgheight = 0;

      img.src = _URL.createObjectURL(file);
      img.onload = function() {
          imgwidth = this.width;
          imgheight = this.height;

          $("#portfolio_width_edit").text(imgwidth);
          $("#portfolio_image_width_edit").val(imgwidth);
      }
  };
