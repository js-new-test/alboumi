$(document).ready(function() {

    var rows_selected = [];
    var recomendedSelectedRows = imagesRow = [];
    init.loader();

    setProductData.setDataForUpdae(productDetails);
    init.handler();
    // setTimeout(ajaxCall.getCategoryAttribute($('#categoryName').val()), 50000)
    //setTimeout(ajaxCall.getCategoryAttribute(productDetails['product']['category_id']), 5000)
    ajaxCall.getCategoryUploadImage(productDetails['product']['category_id']);
    // ajaxCall.getCategoryAttribute($('#categoryName').val());
    // $('#processOperands').addClass('d-none');
  //  $('.addMoreAttribute').addClass('d-none');
    //show common element on load when selected default language
    if ($('#defaultLanguage').val() == defaultLanguageId) {
        $('.commonElement').removeClass('d-none');
    }
    else{
      $('.commonElement').addClass('d-none');
    }

    if (quantityMatrix == 0) {
        $('#bulkPricing').addClass('d-none');
    }

    //form validations
    formValidations.generalValidation();
    formValidations.seoValidation();
    formValidations.videoValidation();
    formValidations.imageValidation();
    formValidations.advancePriceValidation();
    formValidations.inventoryValidation();
    formValidations.specificationValidation();

    ajaxCall.getRelatedProducts(productDetails['product_id']);
    ajaxCall.getRecommendedProducts(productDetails['product_id']);
    ajaxCall.getAdvancePricingData(productDetails['product_id']);
    ajaxCall.getImagesTableData(productDetails['product_id']);
    ajaxCall.bulkPricingTableHeaders(productDetails['product']['category_id'], productDetails['product_id']);
    //ajaxCall.getPricingOptionData(productDetails['product_id']);



	//add Product General details validation and submit event
	$('body').on('click', '#generalInfo', function () {
		if ($("#generalDetails").valid()) {
			ajaxCall.productGenerlDataSubmit();
		}
	});

	//add video details validation and submit event
	$('body').on('click', '#videoDetailsSubmit', function () {
		if ($("#videoForm").valid()) {
			ajaxCall.prodctVideoDataSubmit();
		}
	});

	//add inventory details validation and submit event
	$('body').on('click', '#inventoryDetailsSubmit', function () {
		if ($("#inventoryForm").valid()) {
			ajaxCall.prodctInventoryDataSubmit();
		}
	});

	$('body').on('click', '#saveRelatedProducts', function () {
        var table = $('#relatedData').DataTable();
        rows_selected = [];

        table.$('input[type="checkbox"]').each(function(){
                if(this.checked){
                    rows_selected.push(this.value);
                }
        });
		ajaxCall.postRelatedProducts($('#productIdddd').val(), rows_selected);
	});

	$('body').on('click', '#saveRecomendedProducts', function () {
        var table = $('#recomendedData').DataTable();
        recomendedSelectedRows = [];

        table.$('input[type="checkbox"]').each(function(){
            console.log(this);
                if(this.checked)
                {
                    console.log('yes');
                    recomendedSelectedRows.push(this.value);
                }
        });
		ajaxCall.postRecomendedProducts($('#productIdddd').val(), recomendedSelectedRows);
	});

    $('body').on('click', '#saveAdvancePricing', function () {
        ajaxCall.advancePricingDataSubmit();
  	});
    $('body').on('click', '#updateAdvancePricing', function () {
        ajaxCall.advancePricingDataUpdate();
  	});
    // $('body').on('click', '#specificarionDetailsSubmit', function () {
    //     if ($("#specificationForm").valid()) {
    //         ajaxCall.productSpecificationDataSubmit();
    //     }
    // });
    var counter = 0;
    $(".addMoreAttribute").click(function(){
        // Increment the cloned element count

        var counter=document.getElementById('optioncount').value;
        if(counter==0)
        var counter=1;
      //  counter++;
        //$('.offerStartDate').datepicker();
      //  $('.offerEndDate').datepicker();
        // Clone the element and assign it to a variable
        var clone = $("#processOperands").clone(true)
            // .append($('<a class="delete" href="#">Remove</a>'))
            .appendTo("#Padditionalselects");
          //  var counterdate=counter-1;
        // Modify cloned element, using the counter variable
         clone.find('#sku_0').attr('id', "sku_"+counter);
         clone.find('#mrp_0').attr('id', "mrp_"+counter);
         clone.find('#sellingPrice_0').attr('id', "sellingPrice_"+counter);
         clone.find('#offerPrice_0').attr('id', "offerPrice_"+counter);
         clone.find('#offerStartDate_0').attr('id', "offerStartDate_"+counter);
         clone.find('#offerEndDate_0').attr('id', "offerEndDate_"+counter);
         clone.find('#quantity_0').attr('id', "quantity_"+counter);
         clone.find('#isDefault_0').attr('id', "isDefault_"+counter);
         clone.find('#pricingId_0').attr('id', "pricingId_"+counter);
         document.getElementById("pricingId_"+counter).value=0;
         document.getElementById("isDefault_"+counter).value=counter;

        //$('<a href="#" class="btn btn-danger delete" type=btn"">X</a>').insertAfter(clone.find("#addMoreAttribute"));

        clone.find('select.attribute').attr('data', counter);
        $(clone.find('select.attribute')).each(function(index, item) {
            var a = $(this).attr('id');
            // var str = "hbeu50271385_612_21";
            // a.substring(0, a.indexOf('_'))
            a = a.split('_')[0];

            $(this).attr('id', a+"_"+counter);
        })

        // clone.find('input').attr('id', 'promotionValue_'+counter);
        // clone.find('input').val('');
        // clone.find('button.loadModal').attr('data', counter);
        ;
      $('#processOperands').find('.datepicker').datepicker();

    });

    $("body").on('click',".delete", function() {
        var id=$(this).attr("data-id");
        $('#optionIdForDelete').val(id)
        $('#pricingDataDeleteModel').modal('show');
        // ajaxCall.deletePricingOptionData(id);
        // $(this).closest(".process_input").remove();
        // counter--; // Modify the counter

    });
    // delete option pricing product - added by Nivedita (April 1, 2021)
    $('body').on('click', "#deleteOption", function () {
      ajaxCall.deletePricingOptionData($('#optionIdForDelete').val());
      $(this).closest(".process_input").remove();
      counter--; // Modify the counter
    });
    $("body").on('click',".delete-image", function() {
        var id=$(this).attr("data-id");
        $('#optionIdForImageDelete').val(id)
        $('#pricingImageDeleteModel').modal('show');
        // ajaxCall.deletePricingOptionData(id);
        // $(this).closest(".process_input").remove();
        // counter--; // Modify the counter

    });
    // delete option pricing product - added by Nivedita (April 1, 2021)
    $('body').on('click', "#deleteOptionImage", function () {
      ajaxCall.deletePricingOptionImage($('#optionIdForImageDelete').val());
      //$(this).closest(".process_input").remove();
      //counter--; // Modify the counter
    });

    $('body').on('click', '#bulkPricingSubmit', function () {
        var main = [];
        var textVal = inputId = rangeData = "";

        $("#tblBulkPricing tbody tr").each(function(){
            var j = 0;
            var temp = [];

            $(this).find("td input:text,td input:hidden").each(function() {
                textVal = this.value;
                inputId = $(this).attr("id");
                rangeData = $(this).attr("data");
                temp[j++] = {'key': inputId, 'value':textVal, 'rangeData':rangeData};
            });
            main.push(temp);
        });

        ajaxCall.postBulkPricing(main);
    });

    //delete single or multiple images
    $('body').on('click', '#deleteImages', function () {

        var table = $('#tableProductImage').DataTable();
        imagesRow = [];

        table.$('input[type="checkbox"]').each(function(){
                if(this.checked){
                    imagesRow.push(this.value);
                }
        });

        $('#selectedImages').val(imagesRow);

        // $('#deleteImagesModal').on('show.bs.modal', function(e){
        //         // ajaxCalls.loadDataTableForDeleteBrand(brandId);
        // });

        $('#deleteImagesModal').modal('show');
    });

    $('body').on('click', '#confirmDeleteImage', function () {
        ajaxCall.postDeleteImages($("#videoForm input[name=productId]").val(), $('#selectedImages').val());
    });

});

var init = {
	loader: function() {
        $('#activeFrom').datepicker();
        $('#activeTill').datepicker();
        $('.divShippingCharges').hide();

	    ajaxCall.getBrands($('#defaultLanguage').val(), "BRAND");

        ajaxCall.getCategories("CATEGORY", $('#defaultLanguage').val());

        ajaxCall.gettaxclass("TAXCLASS");
        ajaxCall.getCustomerGroups("CUSTOMERGROUP");
        ajaxCall.getlumiseproducts("LUMISEPRODUCT");
        appendHtml.languageDropdown(language);
	},

	handler: function () {

		$('body').on('click', '#addRelatedProduct, #addRecomendedProduct', function () {
	    	ajaxCall.getCategories($(this).attr('id'), "");
            ajaxCall.getBrands("", $(this).attr('id'));
		});

        $('body').on('change', '#selectCategory, #selectBrandForRelated', function () {
            var categoryFilter = $('#selectCategory').val();
            var brandFilter = $('#selectBrandForRelated').val()
            var productId = $('#productIdddd').val();
            ajaxCall.getProductsForRelated(categoryFilter, brandFilter, productId);
        });

        $('body').on('change','#selectCategoryForRecomended, #selectBrandForRecomended', function () {
            var productId = $('#productIdddd').val();
            var categoryFilter = $('#selectCategoryForRecomended').val();
            var brandFilter = $('#selectBrandForRecomended').val()
            ajaxCall.getProductsForRecomended(categoryFilter, brandFilter, productId);
        });

        $('body').on('change', '#defaultLanguage', function () {
            if($(this).val() == defaultLanguageId){
                $('.commonElement').removeClass('d-none');
            }else{
                $('.commonElement').addClass('d-none');
            }
            ajaxCall.getLanguageWiseData($(this).val(), $('#generalDetails input[name=productId]').val());
        });

        $('body').on('click', '#uploadImages', function () {

            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });

            event.preventDefault();
            let image_upload = new FormData();
            let TotalImages = $('#image-upload')[0].files.length;  //Total Images
            let images = $('#image-upload')[0];
            if(TotalImages==0){
              toastr.error('Please select image');
            }
            else{
            for (let i = 0; i < TotalImages; i++) {
                image_upload.append('images' + i, images.files[i]);
            }
            image_upload.append('TotalImages', TotalImages);
            image_upload.append('productId', $('#videoForm input[name=productId]').val())

            $.ajax({
                method: 'POST',
                url: baseUrl +'/admin/product/imageUpload',
                data: image_upload,
                contentType: false,
                processData: false,
                success: function (result) {
                    // console.log(`ok ${images}`)
                    if (result.success == true) {
                        toastr.success(result.message);
                        $("#image-upload").val(null);
                        ajaxCall.getImagesTableData($('#videoForm input[name=productId]').val());
                    } else {
                        toastr.error(result.message);
                    }
                },

            })
          }

            // })
        })

        // Handle click on "Select all" control on brands componenet
        $('body').on('click', '#recommendedProductCheckAll', function(){
            // Get all rows with search applied
            var table = $('#recomendedData').DataTable();
            var rows = table.rows({ 'search': 'applied' }).nodes();

            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        // Handle click on checkbox to set state of "Select all" control on brand component
        $('#recomendedData tbody').on('change', 'input[type="checkbox"]', function(){
            // If checkbox is not checked
            if(!this.checked){
                var el = $('#recommendedProductCheckAll').get(0);
                // If "Select all" control is checked and has 'indeterminate' property
                if(el && el.checked && ('indeterminate' in el)){
                    // Set visual state of "Select all" control
                    // as 'indeterminate'
                    el.indeterminate = true;
                }
            }
        });

        // Handle click on "Select all" control on brands componenet
        $('body').on('click', '#relatedProductCheckAll', function(){
            // Get all rows with search applied
            var table = $('#relatedData').DataTable();
            var rows = table.rows({ 'search': 'applied' }).nodes();

            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        // Handle click on checkbox to set state of "Select all" control on brand component
        $('#relatedData tbody').on('change', 'input[type="checkbox"]', function(){
            // If checkbox is not checked
            if(!this.checked){
                var el = $('#relatedProductCheckAll').get(0);
                // If "Select all" control is checked and has 'indeterminate' property
                if(el && el.checked && ('indeterminate' in el)){
                    // Set visual state of "Select all" control
                    // as 'indeterminate'
                    el.indeterminate = true;
                }
            }
        });

        // Handle click on "Select all" control on images table
        $('body').on('click', '#imagesCheckAll', function(){
            // Get all rows with search applied
            var table = $('#tableProductImage').DataTable();
            var rows = table.rows({ 'search': 'applied' }).nodes();

            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        // Handle click on checkbox to set state of "Select all" control on images table
        $('#tableProductImage tbody').on('change', 'input[type="checkbox"]', function(){
            // If checkbox is not checked
            if(!this.checked){
                var el = $('#imagesCheckAll').get(0);
                // If "Select all" control is checked and has 'indeterminate' property
                if(el && el.checked && ('indeterminate' in el)){
                    // Set visual state of "Select all" control
                    // as 'indeterminate'
                    el.indeterminate = true;
                }
            }
        });

        // delete related product - added by Pallavi (March 9, 2021)
        $('body').on('click', ".deleteRelatedProduct", function () {
            var relatedId = $(this).attr('data');
            $('#relatedProductIdForDelete').val(relatedId)
            $('#relatedProductDeleteModel').modal('show');
        })

        // delete advance pricing
        $('body').on('click', ".deleteAdvancePrice", function () {
            var pricingId = $(this).attr('data');
            $('#advancePriceForDelete').val(pricingId)
            $('#advancePriceDeleteModel').modal('show');
        })

        // edit advance pricing
        $('body').on('click', ".editAdvancePrice", function () {
            var pricingId = $(this).attr('data');
            //$('#advancePriceForEdit').val(pricingId);
            $('#advancePriceEditModel').modal('show');
            appendHtml.loadAdvancePricing(pricingId);
        })

        $('body').on('click', '#confirmDelete', function () {
        	ajaxCall.deleteRelatedProduct($('#relatedProductIdForDelete').val());
        })

        $('body').on('click', '#confirmAdvanceDelete', function () {
          ajaxCall.deleteAdvancePrice($('#advancePriceForDelete').val());
        })

        // delete recommended product - added by Pallavi (March 9, 2021)
        $('body').on('click', ".deleteRecommendedProduct", function () {
            var recommendedId = $(this).attr('data');
            $('#recommendedProductDeleteModel').val(recommendedId)
            $('#recommendedProductDeleteModel').modal('show');
        })

        $('body').on('click', '#confirmDeleteRecom', function () {
        	ajaxCall.deleteRecommendedProduct($('#recommendedProductDeleteModel').val());
        })

	}
}

var ajaxCall = {
	//get brands list
	getBrands : function (languageId, elementId) {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
          	type: 'get',
          	url: baseUrl +'/admin/product/brands',
            data:{'languageId':languageId},
          	beforeSend: function() {
               	$('#loaderimage').css("display", "block");
               	$('#loadingorverlay').css("display", "block");
          	},
          	success: function (response) {
          		appendHtml.appendBrandDropDown(response, elementId);
          	}
        });
	},

	getCategories : function(elementId, languageId){
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
          	type: 'get',
          	// url: baseUrl +'/admin/categories',
            url: baseUrl +'/admin/product/categories',
            data:{'languageId':languageId},
          	beforeSend: function() {
               	$('#loaderimage').css("display", "block");
               	$('#loadingorverlay').css("display", "block");
          	},
          	success: function (response) {
          		appendHtml.appendCategoryDropDown(response, elementId);
          	}
        });
	},
  gettaxclass : function(elementId){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
            type: 'get',
            url: baseUrl +'/admin/product/taxclass',
            data:{},
            beforeSend: function() {
                $('#loaderimage').css("display", "block");
                $('#loadingorverlay').css("display", "block");
            },
            success: function (response) {
              appendHtml.appendTaxClassDropDown(response, elementId);
            }
        });
  },
  getlumiseproducts : function(elementId){
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
          	type: 'get',
            url: baseUrl +'/admin/product/lumiseproducts',
            data:{},
          	beforeSend: function() {
               	$('#loaderimage').css("display", "block");
               	$('#loadingorverlay').css("display", "block");
          	},
          	success: function (response) {
          		appendHtml.appendLumiseProductDropDown(response, elementId);
          	}
        });
	},
  getCustomerGroups : function(elementId){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
            type: 'post',
            url: baseUrl +'/admin/product/custGroups',
            data:{"product_id":productDetails['product_id']},
            beforeSend: function() {
                $('#loaderimage').css("display", "block");
                $('#loadingorverlay').css("display", "block");
            },
            success: function (response) {
              appendHtml.appendCustomerGroups(response, elementId);
            }
        });
  },
	getImagesTableData: function(productId) {
        $('#tableProductImage').DataTable().destroy();
		$('#tableProductImage').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
                'type': 'get',
                'url': baseUrl+'/admin/product/images',
                'data': { productId: productId }
            },
            // ajax: baseUrl+'/admin/product/images',
            columns: [{
            	"target": 0,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox' class='imageChckBox' value='"+ row['id'] +"' data='"+ row['id'] +"'>";
                },
            },{
            	"target": 1,
                "bSortable": false,
                "order": false,
                "render": function ( data, type, row ) {
                    return "<img src='" + baseUrl + "/public/images/product/"+row['imageable_id']+"/"+row['name']+"' class='rounded' style='height:50px; width:50px;'>";
                    // return a;
                },
            },{
            	"target": 2,
                "bSortable": false,
                "render": function ( data, type, row ) {
                      return "<input id='sortOrder' onblur='changeSortOrder("+ row['id'] +",this.value);' type='text' class='form-control ' value='"+ row['sort_order'] +"' data='"+ row['sort_order'] +"'>";
                  },
            	//"data":'sort_order'
            }
            ,{
            	"target": 3,
                "bSortable": false,
                "render": function ( data, type, row ) {
                      $Ischecked='';
                      if(row['is_default']=='yes')
                        $Ischecked='checked';
                      return "<input id='sortOrder' onclick='changeIsDefault("+ row['id'] +","+ row['imageable_id'] +");' type='radio' class='imageChckBox' value='yes' data='"+ row['id'] +"' "+$Ischecked+">";
                  },
            	//"data":'sort_order'
            }
          /*  ,{
            	"target": 3,
                "bSortable": false,
            	"data":'label'
            }/*,{
            	"target": 4,
                "bSortable": false,
                "order": false,
                "visible":false,
            	"render": function ( data, type, row ) {
            		var a = '<input type="checkbox" data-toggle="toggle" data-on="Ready" data-off="Not Ready" data-onstyle="success" data-offstyle="danger">';
            		if (row['is_primary_main'] == "yes") {
            			a = '<input type="checkbox" checked data-toggle="toggle" data-on="Ready" data-off="Not Ready" data-onstyle="success" data-offstyle="danger">';
            		}
            		return a;
                },
            },{
            	"target": 5,
                "bSortable": false,
                "order": false,
                "visible":false,
            	"render": function ( data, type, row ) {
            		var a = '<input type="checkbox" data-toggle="toggle" data-on="Ready" data-off="Not Ready" data-onstyle="success" data-offstyle="danger">';
            		if (row['is_primary_small'] == "yes") {
            			a = '<input type="checkbox" checked data-toggle="toggle" data-on="Ready" data-off="Not Ready" data-onstyle="success" data-offstyle="danger">';
            		}
            		return a;
                },
            },{
            	"target": 6,
                "bSortable": false,
                "order": false,
                "visible":false,
            	"render": function ( data, type, row ) {
            		var a = '<input type="checkbox" data-toggle="toggle" data-on="Ready" data-off="Not Ready" data-onstyle="success" data-offstyle="danger">';
            		if (row['is_primary_thumb'] == "yes") {
            			a = '<input type="checkbox" checked data-toggle="toggle" data-on="Ready" data-off="Not Ready" data-onstyle="success" data-offstyle="danger">';
            		}
            		return a;
                },
            },{
            	"target": 7,
                "bSortable": false,
                "order":false,
                "visible":false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox'>";
                },
            },{
            	"target": 8,
                "bSortable": false,
                "order":false,
                "visible":false,
            	"render": function ( data, type, row ) {
                    return "<input type='radio' name='isDefault'>";
                },
            },{
                "target": -1,
                "bSortable": false,
                "order":false,
                "visible":false,
            	"render": function ( data, type, row ) {
                    return "<a href='#'><i class='fas fa-trash'></i></a>";
                },
            }*/]
        });
	},

	//get products for related
	getProductsForRelated : function(categoryId, brandId, productId) {
		$('#relatedData').DataTable().destroy();
		$('#relatedData').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
    		        'type': 'get',
    		        'url': baseUrl+'/admin/product/related',
    		        'data': {
    		           categoryId: categoryId, productId: productId, brandId: brandId
    		        }
    		    },
            columns: [{
            	"target": 0,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox' class='relatedCheckBox' value='"+ row['id'] +"' data='"+ row['id'] +"'>";
                },
            },{
            	"target": 1,
            	"data":'id'
            },{
                "target": 1,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) {
                    return "<img src='" + baseUrl + "/public/images/product/"+row['id']+"/"+row['imageName']+"' class='rounded' style='height:50px; width:50px;'>";
                }
            },{
            	"target": 3,
            	"data":'title'
            },{
            	"target": 4,
                "data": 'sku'
            }]
        });
	},

	//get products for recomendation
	getProductsForRecomended : function(categoryId, brandId, productId) {
		$('#recomendedData').DataTable().destroy();
		$('#recomendedData').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
		        'type': 'get',
		        'url': baseUrl+'/admin/product/recomended',
		        'data': {
		           categoryId: categoryId, productId: productId, brandId: brandId
		        }
		    },
            columns: [{
            	"target": 0,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox' value='"+ row['id'] +"' data='"+ row['id'] +"'>";

                },
            },{
            	"target": 1,
            	"data":'id'
            },{
                "target": 1,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) {
                    return "<img src='" + baseUrl + "/public/images/product/"+row['id']+"/"+row['imageName']+"' class='rounded' style='height:50px; width:50px;'>";
                }
            },{
            	"target": 3,
            	"data":'title'
            },{
            	"target": 4,
                "data": 'sku'
            }]
        });
	},

	//submit product general details
	productGenerlDataSubmit: function () {

        var isCustomized = 0;
        if ($('#isCustomized').is(":checked")) {
            isCustomized = 1;
        }
        var dateDisplay = 0;
    		if ($('#flag_deliverydate').is(":checked")) {
    			dateDisplay = 1;
    		}
        if ($('#isCustomized').is(":checked")) {
        var formData = {'productId':$("#generalDetails input[name=productId]").val(), 'editPage':$("#generalDetails input[name=editPage]").val(),
            'productSlug': $('#productSlug').val(), 'brandName':$('#brandName').val(), 'title': $('#title').val(), 'description':CKEDITOR.instances['description'].getData(),//$('#description').val(),
            'keyFeatures':CKEDITOR.instances['keyFeatures'].getData(), 'canGiftWrap':$('#canGiftWrap').val(), 'length':$('#length').val(), 'width':$('#width').val(), 'height':$('#height').val(),
            'outOfStock':$('#outOfStock').val(), 'categoryId': $('#categoryName').val(), 'metaTitle':$('#metaTitle').val(), 'metaKeyword':$('#metaKeyword').val(),
            'metaDescription':$('#metaDescription').val(), 'defaultLanguage':$('#defaultLanguage').val(), 'page':$('#page').val(),
            'languageId' : $('#defaultLanguage').val(), 'isCustomized': isCustomized, 'status': $('#status').val(),'tax_class_id': $('#tax_class_id').val(), 'printing_product' : $('input[name="printing_product"]:checked').val(),
            'flexmedia_code': $('#flexmedia_code').val(),'max_images': $('#max_images').val(),'prevCategoryId' : $('#prevCategoryId').val(),'lumise_product_id': $('#lumise_product_id').val(),'image_min_height': $('#image_min_height').val(),'image_min_width': $('#image_min_width').val(),'image_max_height': $('#image_max_height').val(),'image_max_width': $('#image_max_width').val(),'weight': $('#weight').val(),'product_type': $('#product_type').val(),'dateDisplay': dateDisplay
        };
      }else{
        var formData = {'productId':$("#generalDetails input[name=productId]").val(), 'editPage':$("#generalDetails input[name=editPage]").val(),
            'productSlug': $('#productSlug').val(), 'brandName':$('#brandName').val(), 'title': $('#title').val(), 'description':CKEDITOR.instances['description'].getData(),//$('#description').val(),
            'keyFeatures':CKEDITOR.instances['keyFeatures'].getData(), 'canGiftWrap':$('#canGiftWrap').val(), 'length':$('#length').val(), 'width':$('#width').val(), 'height':$('#height').val(),
            'outOfStock':$('#outOfStock').val(), 'categoryId': $('#categoryName').val(), 'metaTitle':$('#metaTitle').val(), 'metaKeyword':$('#metaKeyword').val(),
            'metaDescription':$('#metaDescription').val(), 'defaultLanguage':$('#defaultLanguage').val(), 'page':$('#page').val(), 'printing_product' : $('input[name="printing_product"]:checked').val(),
            'languageId' : $('#defaultLanguage').val(), 'isCustomized': isCustomized, 'status': $('#status').val(),'tax_class_id': $('#tax_class_id').val(),
            'flexmedia_code': $('#flexmedia_code').val(),'max_images': $('#max_images').val(),'prevCategoryId' : $('#prevCategoryId').val(),'image_min_height': $('#image_min_height').val(),'image_min_width': $('#image_min_width').val(),'image_max_height': $('#image_max_height').val(),'image_max_width': $('#image_max_width').val(),'weight': $('#weight').val(),'product_type': $('#product_type').val(),'dateDisplay': dateDisplay
        };
      }

	    $.ajax({
            url: baseUrl+'/admin/product/editProduct',
	        type: 'POST',
	        data: formData,
	        success: function(result)
	        {
	        	if (result['success'] == true) {
	        		toastr.success(result.message);
              url = baseUrl+'/admin/product/editProduct/'+result.productId;
              window.location.replace(url);
                    // ajaxCall.getImagesTableData(result.productId);
	        	} else {
	        		toastr.error(result.message);
	        	}

	        },
	        error: function(data)
	        {
	            console.log(data);
	        }
	    });
	},

	//ajax for store video details
	prodctVideoDataSubmit : function () {
		var formData = {'productId':$("#videoForm input[name=productId]").val(), 'editPage':$("#videoForm input[name=editPage]").val(),
						'videoTitle':$('#videoTitle').val(), 'videoType':$('#videoType').val(),
						'videoURL':$('#videoURL').val(), 'videoStatus':$('#videoStatus').val()
	 				};

          if ($("#videoForm input[name=productId]").val() == "") {
            toastr.error("Please add General details first");
            return false;
          }

	    $.ajax({
	        url: baseUrl+'/admin/product/editProduct',
	        type: 'POST',
	        data: formData,
	        success: function(result)
	        {
	        	if (result['success'] == true) {
	        		toastr.success(result.message);
	        		// $("#inventoryForm input[name=productId]").val(result.productId)
	        	}else if(result['success'] == false){
	        		toastr.error(result.message);
	        	}
	        },
	        error: function(data)
	        {
	            console.log(data);
	        }
	    });
	},

	//ajax for store inventory details
	prodctInventoryDataSubmit : function () {
		var formData = {'productId':$("#inventoryForm input[name=productId]").val(), 'editPage':$("#inventoryForm input[name=editPage]").val(),
						'lowStockAlert':$('#lowStockAlert').val(), 'lowStockAlertQuantity':$('#lowStockAlertQuantity').val(),
						'maximumQuantityPerOrder':$('#maximumQuantityPerOrder').val()
	 				};

          if ($("#inventoryForm input[name=productId]").val() == "") {
            toastr.error("Please add General details first");
            return false;
          }

	    $.ajax({
	        url: baseUrl+'/admin/product/editProduct',
	        type: 'POST',
	        data: formData,
	        success: function(result)
	        {
	        	if (result['success'] == true) {
	        		toastr.success(result.message);
	        	}else if(result['success'] == false){
	        		toastr.error(result.message);
	        	}
	        },
	        error: function(data)
	        {
	            console.log(data);
	        }
	    });
	},

	//save related products
	postRelatedProducts: function (productId, selectedProducts) {
		var formData = {'productId':productId, 'selectedProducts': selectedProducts };

	    $.ajax({
	        url: baseUrl+'/admin/product/relatedProduct',
	        type: 'POST',
	        data: formData,
	        success: function(result)
	        {
	        	if (result['success'] == true) {
                    $('#addRelatedProductModal').modal('hide');
	        		toastr.success(result.message);
                    ajaxCall.getRelatedProducts(productId);
	        	}else if(result['success'] == false){
	        		toastr.error(result.message);
	        	}
	        },
	        error: function(data)
	        {
	            console.log(data);
	        }
	    });
	},

	//save recomended products
	postRecomendedProducts: function (productId, selectedProducts) {
		var formData = {'productId':productId, 'selectedProducts': selectedProducts };

	    $.ajax({
	        url: baseUrl+'/admin/product/recomendedProduct',
	        type: 'POST',
	        data: formData,
	        success: function(result)
	        {
	        	if (result['success'] == true) {
                    $('#addRecomendedProductModal').modal('hide');
	        		toastr.success(result.message);
                    ajaxCall.getRecommendedProducts(productId)
	        	}else if(result['success'] == false){
	        		toastr.error(result.message);
	        	}
	        },
	        error: function(data)
	        {
	            console.log(data);
	        }
	    });
	},
  //ajax for store advance pricing  details
	advancePricingDataSubmit : function () {
    document.getElementById('productIdAp').value=productDetails['product_id'];
		var formData = {'productId':$("#productIdAp").val(), 'price':$("#price").val(),'selectGroup':$('#selectGroup').val(),'editPage':"ADVANCEPRICING",
	 				};

          if ($("#advancePricingForm input[name=productId]").val() == "") {
            toastr.error("Please add General details first");
            return false;
          }

	    $.ajax({
	        url: baseUrl+'/admin/product/editProduct',
	        type: 'POST',
	        data: formData,
	        success: function(result)
	        {
	        	if (result['success'] == true) {
	        		toastr.success(result.message);
              setTimeout(function() {
                  location.reload();
              }, 2000);
	        	}else if(result['success'] == false){
	        		toastr.error(result.message);
	        	}
	        },
	        error: function(data)
	        {
	            console.log(data);
	        }
	    });
	},
  //ajax for update advance pricing  details
	advancePricingDataUpdate : function () {
    //document.getElementById('pricingId').value=productDetails['product_id'];
		var formData = {'pricingId':$("#pricingId").val(),'productId':$("#productIdApEdit").val(), 'price':$("#priceEdit").val(),'editPage':"EDITADVANCEPRICING",
	 				};
          //
          // if ($("#advancePricingForm input[name=productId]").val() == "") {
          //   toastr.error("Please add General details first");
          //   return false;
          // }

	    $.ajax({
	        url: baseUrl+'/admin/product/editProduct',
	        type: 'POST',
	        data: formData,
	        success: function(result)
	        {
	        	if (result['success'] == true) {
	        		toastr.success(result.message);
              setTimeout(function() {
                  location.reload();
              }, 2000);
	        	}else if(result['success'] == false){
	        		toastr.error(result.message);
	        	}
	        },
	        error: function(data)
	        {
	            console.log(data);
	        }
	    });
	},
	getRelatedProducts : function (productId) {
		$('#relatedProducts').DataTable().destroy();
		$('#relatedProducts').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
		        'type': 'get',
		        'url': baseUrl+'/admin/product/relatedProduct',
		        'data': {
		           productId: productId
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
            },*/{
            	"target": 0,
              "visible": false,
            	"data":'relatedId'
            },{
            	"target": 1,
            	"data":'title'
            }/*,{
            	"target": 2,
            	"data":'sku'
            }*/,{
            	"target": 3,
                "data": 'categoryTitle'
            }/*,{
            	"target": 5,
            	"data": 'category_products.mrp_price'
            },{
            	"target": 6,
            	"data": 'category_products.selling_price'
            },{
            	"target": 7,
            	"data": 'category_products.committed_quantity'
            },{
            	"target": 8,
            	"data": 'id'
            }*/,{
                "target": -1,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<a href='#' data="+row['relatedId']+" class='text-danger deleteRelatedProduct'><i class='fas fa-trash'></i></a> &nbsp &nbsp";
                },
            }]
        });
	},
  getCategoryUploadImage : function (Id){
    var id =Id;
    $.ajax({
        url: baseUrl+'/admin/categories/getCategory/'+id,
        type: 'get',
        success: function(result)
        {
            if(result.photo_upload!=0 && $('#defaultLanguage').val() == defaultLanguageId){
              $('.ImageRes').removeClass('d-none');
            }
            else{
              $('.ImageRes').addClass('d-none');
            }
        },
        error: function(data)
        {
            console.log(data);
        }
    });
  },
	getRecommendedProducts : function (productId) {
		$('#recomendedProducts').DataTable().destroy();
		$('#recomendedProducts').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
		        'type': 'get',
		        'url': baseUrl+'/admin/product/recommendedProduct',
		        'data': {
		           productId: productId
		        }
		    },
            columns: [/*{
            	"target": 0,
                "bSortable": false,
                "order":false,
                "visible": false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox' value='"+ row['id'] +"' data='"+ row['id'] +"'>";
                },
            },*/{
            	"target": 0,
              "visible": false,
            	"data":'recommendedId'
            },{
            	"target": 1,
            	"data":'title'
            }/*,{
            	"target": 2,
            	"data":'sku'
            }*/,{
            	"target": 3,
                "data": 'categoryTitle'
            }/*,{
            	"target": 5,
            	"data": 'category_products.mrp_price'
            },{
            	"target": 6,
            	"data": 'category_products.selling_price'
            },{
            	"target": 7,
            	"data": 'category_products.committed_quantity'
            },{
            	"target": 8,
            	"data": 'id'
            }*/,{
                "target": -1,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<a href='#' data="+row['recommendedId']+" class='text-danger deleteRecommendedProduct'><i class='fas fa-trash'></i></a> &nbsp &nbsp";
                }
            }]
        });
	},
  getAdvancePricingData : function (productId) {
		$('#advancePricing').DataTable().destroy();
		$('#advancePricing').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
		        'type': 'get',
		        'url': baseUrl+'/admin/product/advancePricing',
		        'data': {
		           productId: productId
		        }
		    },
            columns: [/*{
            	"target": 0,
                "bSortable": false,
                "order":false,
                "visible": false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox' value='"+ row['id'] +"' data='"+ row['id'] +"'>";
                },
            },*/{
            	"target": 0,
            	"data":'group_name'
            }/*,{
            	"target": 2,
            	"data":'sku'
            }*/,{
            	"target": 2,
                "data": 'price'
            }/*,{
            	"target": 5,
            	"data": 'category_products.mrp_price'
            },{
            	"target": 6,
            	"data": 'category_products.selling_price'
            },{
            	"target": 7,
            	"data": 'category_products.committed_quantity'
            },{
            	"target": 8,
            	"data": 'id'
            }*/,{
                "target": -1,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<a href='#' data="+row['pricingId']+" class='editAdvancePrice'><i class='fas fa-edit'></i></a> &nbsp &nbsp<a href='#' data="+row['pricingId']+" class='text-danger deleteAdvancePrice'><i class='fas fa-trash'></i></a> &nbsp &nbsp";
                }
            }]
        });
	},

    getLanguageWiseData : function (languageId, productId) {

        $.ajax({
            type: 'get',
            url: baseUrl +'/admin/product/languageData',
            data:{'languageId':languageId, 'productId': productId},
            success: function (response) {
              //  $('#productId').value=response.id;
                setProductData.setDataForUpdae(response);
            }
        });
    },

    getCategoryAttribute : function (categoryId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'get',
            url: baseUrl +'/admin/product/categoryAttribute',
            data:{'categoryId':categoryId},
            success: function (response) {
                if (response['attributeGroup'] == "") {
                    $('#processOperands').removeClass('d-none');
                    $('.addMoreAttribute').addClass('d-none');
                } else {
                    $('.addMoreAttribute').removeClass('d-none');
                    appendHtml.testAttribute(response)
                }
                // appendHtml.testAttribute(response)
                // appendHtml.appendBrandDropDown(response, elementId);
            }
        });
    },

    productSpecificationDataSubmit : function () {
      var counter=document.getElementById('optioncount').value;
      counter++;
        var formData = {'productId':$("#specificationForm input[name=productId]").val(), 'editPage':$("#specificationForm input[name=editPage]").val(),
                        'sku':$("input[name='sku[]']").map(function(){return $(this).val();}).get(), 'mrp':$("input[name='mrp[]']").map(function(){return $(this).val();}).get(),
                        'sellingPrice':$("input[name='sellingPrice[]']").map(function(){return $(this).val();}).get(), 'offerPrice':$("input[name='offerPrice[]']").map(function(){return $(this).val();}).get(),
                        'quantity':$("input[name='quantity[]']").map(function(){return $(this).val();}).get(), 'offerStartDate':$("input[name='offerStartDate[]']").map(function(){return $(this).val();}).get(), 'offerEndDate':$("input[name='offerEndDate[]']").map(function(){return $(this).val();}).get(), 'pricingId':$("input[name='pricingId[]']").map(function(){return $(this).val();}).get(),'categoryId':$('#generalDetails select[name=categoryName]').val(),'isDefault':$("input[name='isDefault[]']").map(function(){return $(this).val();}).get()
                    };

        var temp = [];
        var attributes = [];
        var attributeGroups = [];
        var groupIds = [];
        $("#specificationForm select").each(function(){
            temp.push($(this).attr('name'));
            groupIds.push($(this).attr('attributeGroupId'));
        });
        var myNewArray = temp.filter(function(elem, index, self) {
            return index === self.indexOf(elem);
        });
        var counter=$('select[name="Finish"]').length;console.log(counter);
        for (var i = 0; i < counter; i++) {
            var ab = [];
            var groupArr = [];
            // var ab = {};
            $.each(myNewArray, function (index, item) {
                // ab[item] = $('#'+item+'_'+i).val();
                ab.push($('#'+item+'_'+i).val());
                groupArr.push($('#'+item+'_'+i).attr('attributeGroupId'));
            });
            attributes[i] = ab;
            attributeGroups[i] = groupArr;
        }

        formData['attributes'] = attributes;
        formData['groupIds'] = attributeGroups;

        if ($("#specificationForm input[name=productId]").val() == "") {
            toastr.error("Please add General details first");
            return false;
        }

        $.ajax({
            url: baseUrl+'/admin/product/editProduct',
            type: 'POST',
            data: formData,
            success: function(result)
            {
                if (result['success'] == true) {
                    toastr.success(result.message);
                }else if(result['success'] == false){
                    toastr.error(result.message);
                }
            },
            error: function(data)
            {
                console.log(data);
            }
        });


    },

    bulkPricingTableHeaders : function (categoryId, productId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'get',
            url: baseUrl +'/admin/product/bulkSellingPrice',
            'data': { categoryId: categoryId, productId:productId },
            // beforeSend: function() {
            //     $('#loaderimage').css("display", "block");
            //     $('#loadingorverlay').css("display", "block");
            // },
            success: function (response) {
                appendHtml.bulkSellingPriceTableHeader(response['headers']);
                appendHtml.bulkSellingPriceTableBody(response);
            }
        });

        // $('#tblBulkPricing').DataTable().destroy();
        // $('#tblBulkPricing').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     "ajax": {
        //         'type': 'get',
        //         'url': baseUrl+'/admin/product/bulkSellingPrice',
        //         'data': { categoryId: categoryId }
        //     },
        //     columns: [{
        //         "target": 0,
        //         "bSortable": false,
        //         "order": false,
        //         "render": function ( data, type, row ) {
        //             return "<img src='" + baseUrl + "/public/images/product/"+row['id']+"/"+row['imageName']+"' class='rounded' style='height:50px; width:50px;'>";
        //         },
        //     },{
        //         "target": 2,
        //         "data":'sort_order'
        //     },{
        //         "target": 3,
        //         "data":'label'
        //     }]
        // });
    },

    postBulkPricing : function (bulkData) {
        var formData = {'productId':$("#generalDetails input[name=productId]").val(), 'editPage':"BULKPRICING",
            'bulkData':bulkData
        };

        $.ajax({
            url: baseUrl+'/admin/product/editProduct',
            type: 'POST',
            data: formData,
            success: function(result)
            {
                if (result['success'] == true) {
                    toastr.success(result.message);
                } else {
                    toastr.error(result.message);
                }

            },
            error: function(data)
            {
                console.log(data);
            }
        });
    },

    getPricingOptionData : function (productId) {
        $.ajax({
            url: baseUrl+'/admin/product/productPricingOptionData',
            type: 'get',
            data: {'productId': productId},
            success: function(result)
            {
                appendHtml.loadPricingOption(result);

            },
            error: function(data)
            {
                console.log(data);
            }
        });
    },
    deletePricingOptionData : function (optionId) {
        $.ajax({
            url: baseUrl+'/admin/product/deleteProductPricingOptionData',
            type: 'post',
            data: {'optionId': optionId},
            success: function(result)
            {
              appendHtml.loadPricingOption(result);
              $('#pricingDataDeleteModel').modal('hide');
              toastr.success(result.message);
              setTimeout(function() {
                  location.reload();
              }, 2000);


            },
            error: function(data)
            {
                console.log(data);
            }
        });
    },
    deletePricingOptionImage : function (optionId) {
        $.ajax({
            url: baseUrl+'/admin/product/deleteProductPricingOptionImage',
            type: 'post',
            data: {'optionId': optionId},
            success: function(result)
            {
              appendHtml.loadPricingOption(result);
              $('#pricingImageDeleteModel').modal('hide');
              toastr.success(result.message);
              setTimeout(function() {
                  location.reload();
              }, 2000);


            },
            error: function(data)
            {
                console.log(data);
            }
        });
    },
    postDeleteImages : function (productId, imageIds) {
        var formData = {'productId':productId, 'imageIds': imageIds };

        $.ajax({
            url: baseUrl+'/admin/product/deleteImages',
            type: 'get',
            data: formData,
            success: function(result)
            {
                //console.log(result);
                if (result['success'] == true) {
                    $('#deleteImagesModal').modal('hide');
                    toastr.success(result.message);
                    ajaxCall.getImagesTableData(productId);
                }else if(result['success'] == false){
                    toastr.error(result.message);
                }
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    },
    // delete advance pricing
    deleteAdvancePrice : function (pricingId) {
        $.ajax({
            url: baseUrl+'/admin/product/deleteAdvancePrice',
            type: 'get',
            data: {'pricingId': pricingId},
            success: function (response)
            {
                if (response['success'] == true)
                {
                    $('#relatedProductDeleteModel').modal('hide');
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    },
    // delete related products - Added by Pallavi (March 9, 2021)
    deleteRelatedProduct(relatedId){
		$.ajax({
            type: "get",
            url: baseUrl + '/admin/product/deleteRelatedProduct',
            data:{'relatedId':relatedId},
            success: function (response)
            {
                if (response['success'] == true)
                {
                    $('#relatedProductDeleteModel').modal('hide');
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            }
        });
    },

    // delete recommended products - Added by Pallavi (March 9, 2021)
    deleteRecommendedProduct(recommendedId){
		$.ajax({
            type: "get",
            url: baseUrl + '/admin/product/deleteRecommendedProduct',
            data:{'recommendedId':recommendedId},
            success: function (response)
            {
                if (response['success'] == true)
                {
                    $('#recommendedProductDeleteModel').modal('hide');
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            }
        });
	},
};

var appendHtml = {
	//append brands dropdown
	appendBrandDropDown : function (brands, elementId) {
        if (elementId == "addRelatedProduct") {
          $('#selectBrandForRelated').empty();
          var brandId = document.getElementById('selectBrandForRelated');
        }else if (elementId == "addRecomendedProduct") {
          $('#selectBrandForRecomended').empty();
          var brandId = document.getElementById('selectBrandForRecomended');
        }else if (elementId == "BRAND") {
          $('#brandName').empty();
          var brandId = document.getElementById('brandName');
        }


		$.each(brands, function(index, item) {
			var value = item['id'];
			var text = item['brandName'];
			var o = new Option(text, value);
			brandId.append(o);
		});

        //console.log('aaaaa'+productDetails);
        $('#brandName option[value="' + productDetails['product']['manufacturer_id'] +'"]').prop("selected", true);

        $("#selectBrandForRecomended").prepend("<option value='-1' selected='selected'>Select Brand</option>");
        $("#selectBrandForRelated").prepend("<option value='-1' selected='selected'>Select Brand</option>");
	},
  appendTaxClassDropDown : function (taxclass, elementId) {
		if (elementId == "TAXCLASS") {
	      	$('#tax_class_id').empty();
	      	var selectTaxClass = document.getElementById('tax_class_id');
	    }
		$.each(taxclass, function(index, item) {
			var value = item['id'];
			var text = item['name'];
			var o = new Option(text, value);
			selectTaxClass.append(o);
		});
    $('#tax_class_id option[value="' + productDetails['product']['tax_class_id'] +'"]').prop("selected", true);
	    //$("#selectTaxClass").prepend("<option value='' selected='selected'>Select Category</option>");
	  //  $("#selectCategoryForRecomended").prepend("<option value='' selected='selected'>Select Category</option>");
	},
  //append lumise product dropdown
	appendLumiseProductDropDown : function (lumiseproduct, elementId) {
		if (elementId == "LUMISEPRODUCT") {
	      	$('#lumise_product_id').empty();
	      	var selectLumiseProduct = document.getElementById('lumise_product_id');
	    }
		$.each(lumiseproduct, function(index, item) {
			var value = item['id'];
			var text = item['name'];
			var o = new Option(text, value);
			selectLumiseProduct.append(o);
		});
    $('#lumise_product_id option[value="' + productDetails['product']['design_tool_product_id'] +'"]').prop("selected", true);
	    //$("#selectTaxClass").prepend("<option value='' selected='selected'>Select Category</option>");
	  //  $("#selectCategoryForRecomended").prepend("<option value='' selected='selected'>Select Category</option>");
	},
  appendCustomerGroups : function (custgroup, elementId) {
		if (elementId == "CUSTOMERGROUP") {
	      	$('#selectGroup').empty();
	      	var selectGroups = document.getElementById('selectGroup');
	    }
		$.each(custgroup, function(index, item) {
			var value = item['id'];
			var text = item['group_name'];
			var o = new Option(text, value);
			selectGroups.append(o);
		});
    //$('#selectGroup option[value="' + productDetails['product']['tax_class_id'] +'"]').prop("selected", true);
	    //$("#selectTaxClass").prepend("<option value='' selected='selected'>Select Category</option>");
	  //  $("#selectCategoryForRecomended").prepend("<option value='' selected='selected'>Select Category</option>");
	},
	//append brands dropdown
	appendCategoryDropDown : function (categories, elementId) {
		if (elementId == "addRelatedProduct") {
			$('#selectCategory').empty();
			var selectCategory = document.getElementById('selectCategory');
		}else if (elementId == "addRecomendedProduct") {
			$('#selectCategoryForRecomended').empty();
			var selectCategory = document.getElementById('selectCategoryForRecomended');
		}else if (elementId == "CATEGORY") {
            $('#categoryName').empty();
            var selectCategory = document.getElementById('categoryName');
        }

		$.each(categories, function(index, item) {
			var value = item['id'];
			var text = item['category'];
			var o = new Option(text, value);
			selectCategory.append(o);
		});

        $('#categoryName option[value="' + productDetails['product']['category_id'] +'"]').prop("selected", true);
        $('#prevCategoryId').val(productDetails['product']['category_id']);
        $("#selectCategory").prepend("<option value='-1' selected='selected'>Select Category</option>");
        $("#selectCategoryForRecomended").prepend("<option value='-1' selected='selected'>Select Category</option>");
	},

    languageDropdown : function (language) {
        var defaultLanguage = document.getElementById('defaultLanguage');
        $.each(language, function (index,item) {
            var text = item['languageName'];
            var value = item['globalLanguageId'];
            var o = new Option(text, value);
            defaultLanguage.append(o);
        })
    },

    testAttribute : function (data1) {

        var resultLength = Object.keys(data1).length;

        $.each(data1.attributeGroup, function (index, item) {

            var option = '<option>Select '+item+'</option>';
            var temp = '<div class="col-md-3">'+
                                '<div class="position-relative form-group">'+
                                    '<label > ' + item + ' <span class="text-danger">*</span></label>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-md-9">'+
                                '<div class="position-relative form-group">'+
                                    '<select class="form-control attribute '+item+'" id="'+item+'_0" name="'+item+'" data=0 attributeGroupId="'+ index +'">'+
                                    '</select>'+
                                '</div>'+
                            '</div>';

            $(temp).insertAfter("#testing");

            $.each(data1.attributes[item], function (i, j) {
                option += '<option value="'+ j.id +'">'+ j.name +'</option>'
            })
            $("#"+item+"_0").append(option);

        })
    },
    loadAdvancePricing : function (pricingId) {
      $.ajax({
          url: baseUrl+'/admin/product/advancePricingData',
          type: 'get',
          data:{
              pricingId : pricingId,
          },
          success: function(result)
          {
              if (result) {
                $('#selectGroupEdit').html(result.group_name);
                $('#priceEdit').val(result.price);
                $('#productIdApEdit').val(result.product_id);
                $('#pricingId').val(result.id);
              }
          },
          error: function(data)
          {
              console.log(data);
          }
      });

    },
    loadPricingOption : function (pricingOptionData) {
        if (pricingOptionData.length > 0) {
            $('#specificationForm #sku').val(pricingOptionData[0]['sku']);
            $('#specificationForm #mrp').val(pricingOptionData[0]['mrp']);
            $('#specificationForm #sellingPrice').val(pricingOptionData[0]['selling_price']);
            $('#specificationForm #offerPrice').val(pricingOptionData[0]['offer_price']);
            $('#specificationForm #quantity').val(pricingOptionData[0]['quantity']);
            $('#specificationForm #offerStartDate').val(pricingOptionData[0]['offer_start_date']);
            $('#specificationForm #offerEndDate').val(pricingOptionData[0]['offer_end_date']);
            $('#specificationForm #pricingId').val(pricingOptionData[0]['id']);
            //$('#specificationForm #isdefaut').val(pricingOptionData[0]['id']);
            if(pricingOptionData[0]['is_default']==1){
            $("#specificationForm #isDefault").prop("checked", true);
            $("#specificationForm #isDefault").val(1);
            }
            else{
              $("#specificationForm #isDefault").prop("checked", false);
              $("#specificationForm #isDefault").val(0);
            }
            var attributeIds = pricingOptionData[0]['attribute_ids'].split(',');

            var j = 0;
            $('select.attribute').each(function(index, item) {
                var a = $(this).attr('id');
                a = a.split('_')[0];

                $(this).attr('id', a+"_"+0);
                $('#'+a+"_"+0+' option[value="' + attributeIds[j++] +'"]').prop("selected", true);
            })
        }

        var counter = 0;
        for (var i = 1; i< pricingOptionData.length; i++) {
            counter++;
            // Clone the element and assign it to a variable
            var clone = $("#processOperands").clone(true)
                .appendTo("#Padditionalselects");


            $('<a href="#" data-id='+pricingOptionData[0]['id']+' class="btn btn-danger delete" type=btn"">X</a>').insertAfter(clone.find("#addMoreAttribute"));

            clone.find('select.attribute').attr('data', counter);

            var sku1 = clone.find('input.sku').attr('id');
            var sku = sku1.split('_')[0];
            sku = sku + "_"+counter;
            $('#'+sku1).attr('id', sku);

            var mrp1 = clone.find('input.mrp').attr('id');
            var mrp = mrp1.split('_')[0];
            mrp = mrp + "_"+counter;
            $('#'+mrp1).attr('id', mrp);

            var sellingPrice1 = clone.find('input.sellingPrice').attr('id');
            var sellingPrice = sellingPrice1.split('_')[0];
            sellingPrice = sellingPrice + "_"+counter;
            $('#'+sellingPrice1).attr('id', sellingPrice);

            var offerPrice1 = clone.find('input.offerPrice').attr('id');
            var offerPrice = offerPrice1.split('_')[0];
            offerPrice = offerPrice + "_"+counter;
            $('#'+offerPrice1).attr('id', offerPrice);

            var offerStartDate1 = clone.find('input.offerStartDate').attr('id');
            var offerStartDate = offerStartDate1.split('_')[0];
            offerStartDate = offerStartDate + "_"+counter;
            $('#'+offerStartDate1).attr('id', offerStartDate);

            var offerEndDate1 = clone.find('input.offerEndDate').attr('id');
            var offerEndDate = offerEndDate1.split('_')[0];
            offerEndDate = offerEndDate + "_"+counter;
            $('#'+offerEndDate1).attr('id', offerEndDate);

            var quantity1 = clone.find('input.quantity').attr('id');
            var quantity = quantity1.split('_')[0];
            quantity = quantity + "_"+counter;
            $('#'+quantity1).attr('id', quantity);

            var pricingId1 = clone.find('input.pricingId').attr('id');
            var pricingId = pricingId1.split('_')[0];
            pricingId = pricingId + "_"+counter;
            $('#'+pricingId1).attr('id', pricingId);

            var isDefault1 = clone.find('input.isDefault').attr('id');
            var isDefault = isDefault1.split('_')[0];
            isDefault = isDefault + "_"+counter;
            $('#'+isDefault1).attr('id', isDefault);

            var attributeIds1 = pricingOptionData[counter]['attribute_ids'].split(',');
            var k = 0;
            $(clone.find('select.attribute')).each(function(index, item) {
                var a = $(this).attr('id');
                a = a.split('_')[0];

                $(this).attr('id', a+"_"+counter);
                //console.log(a+"_"+counter);
                $('#'+a+"_"+counter+' option[value="' + attributeIds1[k++] +'"]').prop("selected", true);
            })

            $('#sku_'+counter).val(pricingOptionData[i]['sku']);
            $('#mrp_'+counter).val(pricingOptionData[i]['mrp']);
            $('#sellingPrice_'+counter).val(pricingOptionData[i]['selling_price']);
            $('#offerPrice_'+counter).val(pricingOptionData[i]['offer_price']);
            $('#quantity_'+counter).val(pricingOptionData[i]['quantity']);
            $('#offerStartDate_'+counter).val(pricingOptionData[i]['offer_start_date']);
            $('#offerEndDate_'+counter).val(pricingOptionData[i]['offer_end_date']);
            $('#pricingId_'+counter).val(pricingOptionData[i]['id']);
            if(pricingOptionData[i]['is_default']==1){
            $("#specificationForm #isDefault_"+(counter)).prop("checked", true);
            $('#isDefault_'+(counter)).val(1);
            }
            else{
              $("#specificationForm #isDefault_"+(counter)).prop("checked", false);
              $('#isDefault_'+(counter)).val(0);
            }
        }
        $('#optioncount').val(counter);
        $( ".datepicker" ).datepicker('refresh');
    },

    bulkSellingPriceTableHeader : function (headers) {

        var thead = "";
        for (var i = 0; i < headers.length; i++) {
            thead = "<th>"+headers[i]+"</th>";
            $('#trBulkPricing').append(thead);
        }
    },

    bulkSellingPriceTableBody : function (varient) {
        var columnCount = 0;
        $("table#tblBulkPricing thead tr th").each(function(){
            columnCount++;
        });
        var td = "";
        var indexTr = 0;
        $.each(varient['variantOptions'], function(index, item) {
            var tr = "<tr>"
            for (var i = 0; i < columnCount; i++) {
                if (i == 0) {
                    tr = tr + "<td>"+ item['attributes'] +" <input type='hidden' value='"+item['optionId']+"' data=''> </td>";
                } else {
                    if (typeof varient['bulkPrice'][indexTr]['rangeValue'][i-1] !== 'undefined'/*varient['bulkPrice'][indexTr][i-1] != ""*/) {
                      //  console.log("indexTr>>>"+indexTr);
                      //  console.log("i>>>"+i);
                        // debugger;
                        tr = tr + "<td> <input type='text' name='"+indexTr+"_"+i+"' id='"+indexTr+"_"+varient['headers'][i]+"' style='width:100px;' data='"+varient['headersRange'][i]+"' value='"+varient['bulkPrice'][indexTr]['rangeValue'][i-1]['value']+"'> </td>";
                        // console.log(">>>>>>>>>>"+indexTr+'_'+varient["headers"][i]);
                        // $('#'+indexTr+'_'+varient["headers"][i]).val(1);
                        // $('#0_0-10').val(1);
                    } else {
                        tr = tr + "<td> <input type='text' name='"+indexTr+"_"+i+"' id='"+indexTr+"_"+varient['headers'][i]+"' style='width:100px;' data='"+varient['headersRange'][i]+"'> </td>";
                    }
                    // tr = tr + "<td> <input type='text' name='"+indexTr+"_"+i+"' id='"+indexTr+"_"+i+"' style='width:100px;'> </td>";
                }
            }
            indexTr++;
            tr = tr + "</tr>"
            $('#tblBodyBulkPricing').append(tr);
        });

        if (varient['bulkPrice'] != "") {
          //  console.log(varient['bulkPrice']);
        }
    }
}

var formValidations = {
	//general form validations
	generalValidation : function() {
	  	// Initialize form validation on the registration form.
	  	// It has the name attribute "registration"
	  	$("form[name='generalDetails']").validate({
	    	// Specify validation rules
	    	rules: {
	     		// The key name on the left side is the name attribute
	      		// of an input field. Validation rules are defined
	      		// on the right side
	      		productSlug: "required",
	      		category: "required",
	      		// productType: "required",
	      		brandName: "required",
	      		title: "required",
	      		// activeFrom: "required",
	      		activeTill: "required",
	      		canGiftWrap: "required",
	      		canCOD: "required",
	      		length: "required",
	      		width: "required",
	      		height: "required",
            weight: "required",
            image_min_width: "required",
	      		image_min_height: "required",
            image_max_width: "required",
	      		image_max_height: "required",
            max_images: {
                  required: true,
                  number: true
              },
	      		isRefundable: "required",
	      		isReplaceable: "required",
	      		returnTillDays: "required",
                metaTitle: "required",
                metaKeyword: "required",
	    	},
	    	// Specify validation error messages
	    	messages: {
	      		productSlug: "Please enter Product Slug",
	      		// productType: "Please enter Product type",
	      		category: "Please Select category",
	      		brandName: "Please enter Brand Name",
	      		title: "Please enter Title",
	      		// activeFrom: "Please enter Active from Date",
	      		activeTill: "Please enter Activet till Date",
	      		canGiftWrap: "Please select if product can gift wrap",
	      		canCOD: "Please select is product can COD",
	      		length: "Please enter product length",
	      		width: "Please enter product width",
	      		height: "Please enter product height",
            image_min_width: "Please enter min image width",
            image_min_height: "Please enter min image height",
            max_images: "Please enter max number of image upload",
	      		isRefundable: "Please select product is Refeundable",
	      		isReplaceable: "Please select product id Replaceable",
	      		returnTillDays: "Please enter No of days for return ",
                metaTitle: "Please enter meta title",
                metaKeyword: "Please enter meta keyword",
	    	},
	    	submitHandler: function(form) {
	      		form.submit();
	    	}
	  	});
	},

	// seo form validation
	seoValidation : function () {
		// Wait for the DOM to be ready

	  	// Initialize form validation on the registration form.
	  	// It has the name attribute "registration"
	  	$("form[name='seoForm']").validate({
	    	// Specify validation rules
    		rules: {
	      		// The key name on the left side is the name attribute
	      		// of an input field. Validation rules are defined
	      		// on the right side
	      		metaTitle: "required",
	      		metaKeyword: "required",
	    	},
	    	// Specify validation error messages
    		messages: {
	      		firstname: "Please enter your firstname",
	      		lastname: "Please enter your lastname",
	    	},
	    	submitHandler: function(form) {
	      		form.submit();
	    	}
	  	});
	},

	// video form validations
	videoValidation : function () {
		$("form[name='videoForm']").validate({
	    	// Specify validation rules
    		rules: {
	      		videoTitle: "required",
	      		videoType: "required",
	      		videoURL: "required",
            productId: "required",
	    	},
	    	// Specify validation error messages
    		messages: {
	      		videoTitle: "Please enter Video Title",
	      		videoType: "Please select video type",
	      		videoURL: "Please enter Video URL",
            productId: "Please add General details first",
	    	},
	    	submitHandler: function(form) {
	      		form.submit();
	    	}
	  	});
	},

  // image form validations
	imageValidation : function () {
		$("form[name='imageForm']").validate({
	    	// Specify validation rules
    		rules: {
          image_upload:{
               extension: "jpg|jpeg|png|"
          },

	    	},
	    	// Specify validation error messages
    		messages: {
          "image_upload" : {
              extension: "Please upload file in these format only (png, jpg, jpeg)."
          },
	    	},
	    	submitHandler: function(form) {
	      		form.submit();
	    	}
	  	});
	},

	//inventory form validations
	inventoryValidation : function () {
		$("form[name='inventoryForm']").validate({
	    	// Specify validation rules
    		rules: {
	      		lowStockAlert: "required",
	      		lowStockAlertQuantity: "required"
	    	},
	    	// Specify validation error messages
    		messages: {
	      		lowStockAlert: "Please select Low stock alert",
	      		lowStockAlertQuantity: "Please select Low stock alert"
	    	},
	    	submitHandler: function(form) {
	      		form.submit();
	    	}
	  	});
	},

    specificationValidation : function () {
        $("form[name='specificationForm']").validate({
            // Specify validation rules
            ignore: '',
            rules: {
                'sku[]': "required",
                'sellingPrice[]': "required",
                'quantity[]': "required",
            },
            // Specify validation error messages
            messages: {
                'sku[]': "Please enter SKU",
                'sellingPrice[]': "Please enter selling price",
                'quantity[]': "Please enter quantity",
            },
            submitHandler: function(form) {
               form.submit();
            }
        });
    },
    // Advance price
    advancePriceValidation : function () {
      $("form[name='advancePricingForm']").validate({
          // Specify validation rules
          rules: {
              selectGroup: "required",
              price: {
                    required: true,
                    min: 1,
                    number: true
                },
          },
          // Specify validation error messages
          messages: {
              selectGroup: "Please select group",
              price: "Please enter price",

          },
          submitHandler: function(form) {
              form.submit();
          }
        });
    },
};

var setProductData = {
    setDataForUpdae : function(productDetails) {
        $('#defaultLanguage option[value="' + productDetails['language_id'] +'"]').prop("selected", true);
        // $('#brandName option[value="' + productDetails['product']['manufacturer_id'] +'"]').prop("selected", "selected");
        // $('#categoryName option[value="' + productDetails['product']['category_id'] +'"]').prop("selected", "selected");
        $('#generalDetails input[name=title]').val(productDetails['title']);
        $('#generalDetails input[name=productId]').val(productDetails['product_id']);
        $('#generalDetails input[name=productSlug]').val(productDetails['product']['product_slug']);
        CKEDITOR.instances['description'].setData(productDetails['description'])
        CKEDITOR.instances['keyFeatures'].setData(productDetails['key_features'])
        $('#canGiftWrap option[value="' + productDetails['product']['can_giftwrap'] +'"]').prop("selected", true);
        $('#generalDetails input[name=length]').val(productDetails['product']['length']);
        $('#generalDetails input[name=width]').val(productDetails['product']['width']);
        $('#generalDetails input[name=height]').val(productDetails['product']['height']);
        $('#outOfStock option[value="' + productDetails['product']['out_of_stock'] +'"]').prop("selected", true);
        $('#generalDetails input[name=metaTitle]').val(productDetails['meta_title']);
        $('#generalDetails input[name=image_min_width]').val(productDetails['product']['image_min_width']);
        $('#generalDetails input[name=image_min_height]').val(productDetails['product']['image_min_height']);
        $('#generalDetails input[name=image_max_width]').val(productDetails['product']['image_max_width']);
        $('#generalDetails input[name=image_max_height]').val(productDetails['product']['image_max_height']);
        $('#generalDetails input[name=max_images]').val(productDetails['product']['max_images']);
        $('#generalDetails input[name=weight]').val(productDetails['product']['weight']);
        $('#generalDetails input[name=product_type]').val(productDetails['product_type']);
        //$('#generalDetails input[name=tax_class_id]').val(productDetails['tax_class_id']);
        $('#tax_class_id option[value="'+productDetails['product']['tax_class_id']+'"]').prop("selected", true);
        $('#metaKeyword').val(productDetails['meta_keyword']);
        $('#metaDescription').val(productDetails['meta_description']);
        $('#status option[value="' + productDetails['product']['status'] +'"]').prop("selected", true);
        $('#generalDetails input[name=flexmedia_code]').val(productDetails['product']['flexmedia_code']);
        $('#generalDetails input[name=printing_product][value="' + productDetails['product']['printing_product'] +'"]').prop("checked", true);

        if (productDetails['product']['is_customized'] == 1) {
            $('#isCustomized').prop('checked', true);
            $('.lumiseproduct').removeClass('d-none');
            $('#lumise_product_id option[value="'+productDetails['product']['design_tool_product_id']+'"]').prop("selected", true);
        }
        if (productDetails['product']['flag_deliverydate'] == 1) {
          $('#flag_deliverydate').prop('checked', true);
        }

        if (productDetails['product_video'] != null) {
            $('#videoForm input[name=videoTitle]').val(productDetails['product_video']['title']);
            $('#videoType option[value="' + productDetails['product_video']['type'] +'"]').prop("selected", true);
            $('#videoForm input[name=videoURL]').val(productDetails['product_video']['url']);
            $('#videoStatus option[value="' + productDetails['product_video']['status'] +'"]').prop("selected", true);
        }

        $('#lowStockAlert option[value="' + productDetails['product']['low_stock_alert'] +'"]').prop("selected", true);
        $('#inventoryForm input[name=lowStockAlertQuantity]').val(productDetails['product']['low_stock_alert_quantity']);
        $('#inventoryForm input[name=maximumQuantityPerOrder]').val(productDetails['product']['max_order_quantity']);

        $("#generalDetails input[name=productId]").val(productDetails['product_id']);
        $("#videoForm input[name=productId]").val(productDetails['product_id']);
        $("#inventoryForm input[name=productId]").val(productDetails['product_id']);
        $("#specificationForm input[name=productId]").val(productDetails['product_id']);
        $("#productId").val(productDetails['product_id']);
        $("#productIdddd").val(productDetails['product_id']);
    }
}


function generateSlug()
{
    var titleValue = $("#title").val();
    $("#productSlug").val(titleValue.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-').toLowerCase());
}

function changeSortOrder(id,sortValue){
  var id=id;
  var sortValue = sortValue;
  $.ajax({
      url: baseUrl+'/admin/product/updateImageSortOrder',
      type: 'post',
      data:{
          id : id,
          sortValue : sortValue,
      },
      success: function(result)
      {
          if (result['success'] == true) {
              toastr.success(result.message);
          }else if(result['success'] == false){
              toastr.error(result.message);
          }
          ajaxCall.getImagesTableData(productDetails['product']['id']);
      },
      error: function(data)
      {
          console.log(data);
      }
  });
}
  function changeIsDefault(id,product_id){
    var id=id;
    var product_id = product_id;
    $.ajax({
        url: baseUrl+'/admin/product/updateImageIsDefault',
        type: 'post',
        data:{
            id : id,
            product_id : product_id,
        },
        success: function(result)
        {
            if (result['success'] == true) {
                toastr.success(result.message);
            }else if(result['success'] == false){
                toastr.error(result.message);
            }
            ajaxCall.getImagesTableData(productDetails['product']['id']);
        },
        error: function(data)
        {
            console.log(data);
        }
    });
}
$( function() {
  $('.datepicker').on('click', function() {
        $(this).datepicker({showOn:'focus'}).focus();
    });
  } );
  // $( function() {
  //   $('.isDefault').on('click', function() {
  //         $(this).val(1);
  //     });
  //   } );
$(document).ready(function(){
      $('#isCustomized').change(function(){
        if($(this).is(':checked'))
        {
            $('.lumiseproduct').removeClass('d-none');
        }
        else
        {
          $('.lumiseproduct').addClass('d-none');
        }
      })

  })
  $(document).ready(function(){
    $('#categoryName').change(function(){
      var id =$(this).val();
      $.ajax({
          url: baseUrl+'/admin/categories/getCategory/'+id,
          type: 'get',
          success: function(result)
          {
              if(result.photo_upload!=0){
                $('.ImageRes').removeClass('d-none');
              }
              else{
                $('.ImageRes').addClass('d-none');
              }
          },
          error: function(data)
          {
              console.log(data);
          }
      });
    });
  })
