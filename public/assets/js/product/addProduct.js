$(document).ready(function() {
    var rows_selected = [];
    var recomendedSelectedRows = imagesRow = [];

    init.loader();
    init.handler();

    $('#processOperands').addClass('d-none');
    $('.addMoreAttribute').addClass('d-none');


    //form validations
    formValidations.generalValidation();
    formValidations.seoValidation();
    formValidations.videoValidation();
    formValidations.inventoryValidation();
    formValidations.specificationValidation();


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
		ajaxCall.postRelatedProducts($('#productIdddd').val(), rows_selected);
	});

	$('body').on('click', '#saveRecomendedProducts', function () {
		ajaxCall.postRecomendedProducts($('#productIdddd').val(), recomendedSelectedRows);
	});


	// Handle click on checkbox
   	$('#relatedData tbody').on('click', 'input[type="checkbox"]', function(e){
      	var $row = $(this).closest('tr');

      	// Get row data
     	var data = $('#relatedData').DataTable().row($row).data();
     	console.log(data['id']);
      	// Get row ID
     	var rowId = data['id'];

      	// Determine whether row ID is in the list of selected row IDs
      	var index = $.inArray(rowId, rows_selected);

      	// If checkbox is checked and row ID is not in list of selected row IDs
      	if(this.checked && index === -1){
         	rows_selected.push(rowId);

      	// Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
      	} else if (!this.checked && index !== -1){
         	rows_selected.splice(index, 1);
      	}

      	if(this.checked){
         	$row.addClass('selected');
      	} else {
         	$row.removeClass('selected');
      	}

      	// Prevent click event from propagating to parent
      	e.stopPropagation();
   	});

   	// Handle click on checkbox
   	$('#recomendedData tbody').on('click', 'input[type="checkbox"]', function(e){
      	var $row = $(this).closest('tr');

      	// Get row data
       	var data = $('#recomendedData').DataTable().row($row).data();
       	console.log(data['id']);
      	// Get row ID
     	  var rowId = data['id'];

      	// Determine whether row ID is in the list of selected row IDs
      	var index = $.inArray(rowId, recomendedSelectedRows);

      	// If checkbox is checked and row ID is not in list of selected row IDs
      	if(this.checked && index === -1){
         	recomendedSelectedRows.push(rowId);

      	// Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
      	} else if (!this.checked && index !== -1){
         	recomendedSelectedRows.splice(index, 1);
      	}

      	if(this.checked){
         	$row.addClass('selected');
      	} else {
         	$row.removeClass('selected');
      	}
      	console.log(recomendedSelectedRows);
      	// Update state of "Select all" control
      	// updateDataTableSelectAllCtrl(table);

      	// Prevent click event from propagating to parent
      	e.stopPropagation();
   	});

    $('body').on('click', '#uploadImages', function () {
      $.ajaxSetup({
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
       });

      // $('#image-upload').change(function () {
          // event.preventDefault();
          let image_upload = new FormData();
          let TotalImages = $('#image-upload')[0].files.length;  //Total Images
          let images = $('#image-upload')[0];

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
                  // console.log(`ok ${result}`)
                  if (result.success == true) {
	                  toastr.success(result.message);
	                  ajaxCall.getImagesTableData($('#videoForm input[name=productId]').val())

                  }else {
                        toastr.error(result.message);
                    }
              },
              error: function () {
                console.log(`Failed`)
              }
          })
    });

    $('body').on('click', '#specificarionDetailsSubmit', function () {
		if ($("#specificationForm").valid()) {
			ajaxCall.productSpecificationDataSubmit();
		}
	});

	var counter = 0;

    $(".addMoreAttribute").click(function(){
        // Increment the cloned element count
        counter++;
        // Clone the element and assign it to a variable
        var clone = $("#processOperands").clone(true)
            // .append($('<a class="delete" href="#">Remove</a>'))
            .appendTo("#Padditionalselects");

        // Modify cloned element, using the counter variable
        // clone.find('input[name=sku]').attr('id', "promotionOn_"+counter);
        // clone.find('select.promotionOn').attr('data', counter);


        $('<a href="#" class="btn btn-danger delete" type=btn"">X</a>').insertAfter(clone.find("#addMoreAttribute"));

        clone.find('select.attribute').attr('data', counter);

        $(clone.find('select.attribute')).each(function(index, item) {
            var a = $(this).attr('id');
            // a.substring(0, a.indexOf('_'))
            a = a.split('_')[0];
            console.log(a);
            $(this).attr('id', a+"_"+counter);
        })

    });

	$("body").on('click',".delete", function() {
		$(this).closest(".process_input").remove();
		counter--; // Modify the counter
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

        $('#deleteImagesModal').modal('show');
    });

    $('body').on('click', '#confirmDeleteImage', function () {
        ajaxCall.postDeleteImages($("#videoForm input[name=productId]").val(), $('#selectedImages').val());
    })


});

var init = {
	loader: function() {
	    $('#activeFrom').datepicker();
		$('#activeTill').datepicker();
		$('.divShippingCharges').hide();

	    ajaxCall.getBrands($('#defaultLanguage').val(), "BRAND");

      ajaxCall.getCategories("CATEGORY", $('#defaultLanguage').val());
      ajaxCall.gettaxclass("TAXCLASS");
      ajaxCall.getlumiseproducts("LUMISEPRODUCT");


	    // appendHtml.appendCaregoryTree();
      appendHtml.languageDropdown(language);
	},

	handler: function () {

		$('.pincodeFileDownload').click(function() {
			ajaxCall.pincodeExport($(this).attr('part'));
		});

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

	    // $("#selectCategory").change(function () {
	    //     ajaxCall.getProductsForRelated($(this).val(), 1);
	    // });

	    // $("#selectCategoryForRecomended").change(function () {
	    //     ajaxCall.getProductsForRecomended($(this).val(), 1);
	    // });

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
                        ajaxCall.getImagesTableData($('#videoForm input[name=productId]').val())
                    } else {
                        toastr.error(result.message);
                    }
                },
                error: function () {
                    console.log(`Failed`)
                }
            })

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
            	"data":'sort_order'
            },{
            	"target": 3,
                "bSortable": false,
            	"data":'label'
            }]
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
                // "visible":false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox' class='relatedCheckBox' value='"+ row['id'] +"' data='"+ row['id'] +"'>";
                },
            },{
            	"target": 1,
            	"data":'id'
            },{
            	"target": 2,
            	"data":'id'
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
                // "visible":false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox'>";
                },
            },{
            	"target": 1,
            	"data":'id'
            },{
            	"target": 2,
            	"data":'id'
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

	    if ($('#page').val() == 'addProduct') {
            console.log($('input[name="printing_product"]:checked').val());
        if ($('#isCustomized').is(":checked")) {
	      	var formData = {'productSlug': $('#productSlug').val(),
	            'brandName':$('#brandName').val(), 'title': $('#title').val(),
	            'description':CKEDITOR.instances['description'].getData(), 'otherDescription':$('#otherDescription').val(),
	            'keyFeatures':CKEDITOR.instances['keyFeatures'].getData(), 'canGiftWrap':$('#canGiftWrap').val(), 'length':$('#length').val(), 'width':$('#width').val(), 'height':$('#height').val(),
	            'categoryId': $('#categoryName').val(), 'metaTitle':$('#metaTitle').val(), 'metaKeyword':$('#metaKeyword').val(),
	            'metaDescription':$('#metaDescription').val(), 'defaultLanguage':$('#defaultLanguage').val(), 'page':$('#page').val(),
                'tax_class_id': $('#tax_class_id').val(), 'flexmedia_code': $('#flexmedia_code').val(),'isCustomized': isCustomized, 'status': $('#status').val(),'lumise_product_id': $('#lumise_product_id').val(),'image_min_height': $('#image_min_height').val(),'image_min_width': $('#image_min_width').val(),'image_max_height': $('#image_max_height').val(),'image_max_width': $('#image_max_width').val(),'weight': $('#weight').val(),
                'product_type': $('#product_type').val(),'dateDisplay': dateDisplay, 'printing_product' : $('input[name="printing_product"]:checked').val(),'max_images': $('#max_images').val()
	        };
        }
        else{
          var formData = {'productSlug': $('#productSlug').val(),
              'brandName':$('#brandName').val(), 'title': $('#title').val(),
              'description':CKEDITOR.instances['description'].getData(), 'otherDescription':$('#otherDescription').val(),
              'keyFeatures':CKEDITOR.instances['keyFeatures'].getData(), 'canGiftWrap':$('#canGiftWrap').val(), 'length':$('#length').val(), 'width':$('#width').val(), 'height':$('#height').val(),
              'categoryId': $('#categoryName').val(), 'metaTitle':$('#metaTitle').val(), 'metaKeyword':$('#metaKeyword').val(),
              'metaDescription':$('#metaDescription').val(), 'defaultLanguage':$('#defaultLanguage').val(), 'page':$('#page').val(),'printing_product' : $('input[name="printing_product"]:checked').val(),
              'tax_class_id': $('#tax_class_id').val(),'max_images': $('#max_images').val(),'flexmedia_code': $('#flexmedia_code').val(),'isCustomized': isCustomized, 'status': $('#status').val(),'image_min_height': $('#image_min_height').val(),'image_min_width': $('#image_min_width').val(),'image_max_height': $('#image_max_height').val(),'image_max_width': $('#image_max_width').val(),'weight': $('#weight').val(),'product_type': $('#product_type').val(),'dateDisplay': dateDisplay
          };
          }
	    }else if ($('#page').val() == 'anotherLanguage') {
	        var formData = {'productId':$('#productId').val(), 'productSlug': $('#productSlug').val(),
	            'title': $('#title').val(), 'description':CKEDITOR.instances['description'].getData(), 'otherDescription':$('#otherDescription').val(),
	            'keyFeatures':CKEDITOR.instances['keyFeatures'].getData(), 'metaTitle':$('#metaTitle').val(), 'metaKeyword':$('#metaKeyword').val(),
	            'metaDescription':$('#metaDescription').val(), 'defaultLanguage':$('#defaultLanguage').val(), 'page':$('#page').val(),'product_type': $('#product_type').val()
	        };
	    }

	    $.ajax({
	        url: baseUrl+'/admin/product/addProduct',
	        type: 'POST',
	        data: formData,
	        success: function(result)
	        {
	        	if (result['success'] == true) {
	        		toastr.success(result.message);
              		// $("#videoForm input[name=productId]").val(result.productId)
              		// $("#inventoryForm input[name=productId]").val(result.productId)
              		// $("#productIdddd").val(result.productId)
              		// ajaxCall.getImagesTableData(result.productId);
              		// ajaxCall.getCategoryAttribute($("#generalDetails input[name=categoryName]").val());
              		// ajaxCall.bulkPricingTableHeaders($("#generalDetails input[name=categoryName]").val());
                  //location.reload('/admin/product/editProduct/'+result.productId);
                  url = baseUrl+'/admin/product/editProduct/'+result.productId;
                  window.location.replace(url);
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
	        		console.log(result)
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
	        		// $("#pinCodeForm input[name=productId]").val();
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
	        		// $("#pinCodeForm input[name=productId]").val();
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
	        		// $("#pinCodeForm input[name=productId]").val();
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
            	"data":'id'
            },{
            	"target": 1,
            	"data":'title'
            },{
            	"target": 2,
            	"data":'sku'
            },{
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
            }*/]
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
                "visible":false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox' value='"+ row['id'] +"' data='"+ row['id'] +"'>";
                },
            },*/{
            	"target": 0,
            	"data":'id'
            },{
            	"target": 1,
            	"data":'title'
            },{
            	"target": 2,
            	"data":'sku'
            },{
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
            }*/]
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
          	}
        });
	},

	productSpecificationDataSubmit : function () {
        var formData = {'productId':$("#specificationForm input[name=productId]").val(), 'editPage':$("#specificationForm input[name=editPage]").val(),
                        'sku':$("input[name='sku[]']").map(function(){return $(this).val();}).get(), 'mrp':$("input[name='mrp[]']").map(function(){return $(this).val();}).get(),
                        'sellingPrice':$("input[name='sellingPrice[]']").map(function(){return $(this).val();}).get(), 'offerPrice':$("input[name='offerPrice[]']").map(function(){return $(this).val();}).get(),
                        'quantity':$("input[name='quantity[]']").map(function(){return $(this).val();}).get(), 'categoryId':$('#generalDetails select[name=categoryName]').val()
                    };

        var temp = [];
        var attributes = [];
        var attributeGroups = [];
        var groupIds = [];

        $("#specificationForm select").each(function(){
            temp.push($(this).attr('name'))
            groupIds.push($(this).attr('attributeGroupId'))
        });

        var myNewArray = temp.filter(function(elem, index, self) {
            return index === self.indexOf(elem);
        });

        var arr = [];

        for (var i = 0; i < myNewArray.length; i++) {
            var ab = [];
            var groupArr = [];
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

    bulkPricingTableHeaders : function (categoryId) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'get',
            url: baseUrl +'/admin/product/bulkSellingPrice',
            'data': { categoryId: categoryId },
            // beforeSend: function() {
            //     $('#loaderimage').css("display", "block");
            //     $('#loadingorverlay').css("display", "block");
            // },
            success: function (response) {
                console.log(response);
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
                // console.log(result);
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

    postDeleteImages : function (productId, imageIds) {
        var formData = {'productId':productId, 'imageIds': imageIds };

        $.ajax({
            url: baseUrl+'/admin/product/deleteImages',
            type: 'get',
            data: formData,
            success: function(result)
            {
                console.log(result);
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
    }
};

var appendHtml = {
  languageDropdown : function (language) {
      var defaultLanguage = document.getElementById('defaultLanguage');
      $.each(language, function (index,item) {
          var text = item['languageName'];
          var value = item['globalLanguageId'];
          var o = new Option(text, value);
          defaultLanguage.append(o);
      })
  },
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
if($('#page').val() == 'addProduct') {

		$.each(brands, function(index, item) {
			var value = item['id'];
			var text = item['brandName'];
			var o = new Option(text, value);
			brandId.append(o);
		});
}
	    $("#selectBrandForRecomended").prepend("<option value='' selected='selected'>Select Brand</option>");
	    $("#selectBrandForRelated").prepend("<option value='' selected='selected'>Select Brand</option>");
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
      if ($('#page').val() == 'addProduct') {
		$.each(categories, function(index, item) {
			var value = item['id'];
			var text = item['category'];
			var o = new Option(text, value);
			selectCategory.append(o);
		});
  }

	    $("#selectCategory").prepend("<option value='' selected='selected'>Select Category</option>");
	    $("#selectCategoryForRecomended").prepend("<option value='' selected='selected'>Select Category</option>");
	},
  //append brands dropdown
	appendTaxClassDropDown : function (taxclass, elementId) {
		if (elementId == "TAXCLASS") {
	      	$('#tax_class_id').empty();
	      	var selectTaxClass = document.getElementById('tax_class_id');
	    }
      if ($('#page').val() == 'addProduct') {
		$.each(taxclass, function(index, item) {
			var value = item['id'];
			var text = item['name'];
			var o = new Option(text, value);
			selectTaxClass.append(o);
		});
  }

	    //$("#selectTaxClass").prepend("<option value='' selected='selected'>Select Category</option>");
	  //  $("#selectCategoryForRecomended").prepend("<option value='' selected='selected'>Select Category</option>");
	},
  //append lumise product dropdown

	appendLumiseProductDropDown : function (lumiseproduct, elementId) {
		if (elementId == "LUMISEPRODUCT") {
	      	$('#lumise_product_id').empty();
	      	var selectLumiseProduct = document.getElementById('lumise_product_id');
	    }
      if ($('#page').val() == 'addProduct') {
		$.each(lumiseproduct, function(index, item) {
			var value = item['id'];
			var text = item['name'];
			var o = new Option(text, value);
			selectLumiseProduct.append(o);
		});
  }

	    //$("#selectTaxClass").prepend("<option value='' selected='selected'>Select Category</option>");
	  //  $("#selectCategoryForRecomended").prepend("<option value='' selected='selected'>Select Category</option>");
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

    loadPricingOption : function (pricingOptionData) {
        if (pricingOptionData.length > 0) {
            $('#specificationForm #sku').val(pricingOptionData[0]['sku']);
            $('#specificationForm #mrp').val(pricingOptionData[0]['mrp']);
            $('#specificationForm #sellingPrice').val(pricingOptionData[0]['selling_price']);
            $('#specificationForm #offerPrice').val(pricingOptionData[0]['offer_price']);
            $('#specificationForm #quantity').val(pricingOptionData[0]['quantity']);
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


            $('<a href="#" class="btn btn-danger delete" type=btn"">X</a>').insertAfter(clone.find("#addMoreAttribute"));

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

            var quantity1 = clone.find('input.quantity').attr('id');
            var quantity = quantity1.split('_')[0];
            quantity = quantity + "_"+counter;
            $('#'+quantity1).attr('id', quantity);

            $(clone.find('select.attribute')).each(function(index, item) {
                var a = $(this).attr('id');
                a = a.split('_')[0];

                $(this).attr('id', a+"_"+counter);
                console.log(a+"_"+counter);
                $('#'+a+"_"+counter+' option[value="' + 3 +'"]').prop("selected", true);
            })

            $('#sku_'+counter).val(pricingOptionData[i]['sku']);
            $('#mrp_'+counter).val(pricingOptionData[i]['mrp']);
            $('#sellingPrice_'+counter).val(pricingOptionData[i]['selling_price']);
            $('#offerPrice_'+counter).val(pricingOptionData[i]['offer_price']);
            $('#quantity_'+counter).val(pricingOptionData[i]['quantity']);

        }
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
                        console.log("indexTr>>>"+indexTr);
                        console.log("i>>>"+i);
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
            console.log(varient['bulkPrice']);
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
	      		brandName: "required",
	      		title: "required",
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
            tax_class_id: "required",
	      		isRefundable: "required",
	      		isReplaceable: "required",
	      		returnTillDays: "required",
	            metaTitle: "required",
	            metaKeyword: "required",
	    	},
	    	// Specify validation error messages
	    	messages: {
	      		productSlug: "Please enter Product Slug",
	      		category: "Please Select category",
	      		brandName: "Please enter Brand Name",
	      		title: "Please enter Title",
	      		activeTill: "Please enter Activet till Date",
	      		canGiftWrap: "Please select if product can gift wrap",
	      		canCOD: "Please select is product can COD",
	      		length: "Please enter product length",
	      		width: "Please enter product width",
	      		height: "Please enter product height",
            weight: "Please enter product weight",
            image_min_width: "Please enter min image width",
	      		image_min_height: "Please enter min image height",
            image_max_width: "Please enter max image width",
	      		image_max_height: "Please enter max image height",
            max_images: "Please enter max number of image upload",
	      		isRefundable: "Please select product is Refeundable",
	      		isReplaceable: "Please select product id Replaceable",
	      		returnTillDays: "Please enter No of days for return ",
            tax_class_id: "Please select TAx Class ",
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
    }
}


function generateSlug()
{
    var titleValue = $("#title").val();
    $("#productSlug").val(titleValue.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-').toLowerCase());
}
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

});
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
