$(document).ready(function() {
	formValidations.generalValidation();
	formValidations.promotionConditionValidation();

	$('input[id$=activeTill]').datepicker({
	    dateFormat: 'dd-mm-yy'
	});
	var rows_selected = [];
	var productRowsSelected = [];

	// Cloned elements count
	var counter = 0;
	var promotionOnArray = ['brand', 'category', 'product', 'Grand Total']

	$(".add_PO").click(function(){
		// Increment the cloned element count
		counter++;
		// Clone the element and assign it to a variable
		if($("#process_operands").length == 0) 
		{
			var clone = $("#process_operand").clone(true)
			.append($('<a class="delete" href="#">Remove</a>'))
			.appendTo("#Padditionalselects");
		}
		else
		{
			var clone = $("#process_operands").clone(true)
			.append($('<a class="delete" href="#">Remove</a>'))
			.appendTo("#Padditionalselects");
			clone.find(':selected').removeAttr('selected');
		}

			
			// $cl2 = $cl1.clone();
		
		// Modify cloned element, using the counter variable
		clone.find('select.promotionOn').attr('id', "promotionOn_"+counter);
		clone.find('select.promotionOn').attr('data', counter);
		
		clone.find('select.conditionType').attr('data', counter);

		clone.find('input').attr('id', 'promotionValue_'+counter);
		clone.find('input').val('');
		clone.find('button.loadModal').attr('data', counter);
		
	});
	
	$("body").on('click',".delete", function() {
		$(this).closest(".process_input").remove();
		counter--; // Modify the counter
	});

	$('body').on('change', '.promotionOn', function () {
		var duplicate = false;
	    var dName = '';
	    var names = [];
		$('.promotionOn').each(function() {
	        if ($(this).val().trim()) {
	            if($.inArray($(this).val(), names) != -1) {
	                duplicate = true;
	                dName = $(this).val();
	                return false;
	            } else {
	                names.push($(this).val());
	            }
	        }
	    });
	    if(duplicate == true) {
	        alert('Duplicate `' + dName + '` field for promotion conditions not allowed and also not allowed for others.');
	        $(this).val(' ');
	        return false;
	    } else {
	        var temp = $(this).attr('data');
			switch($(this).val()) {
            	case 'Brand':
	               $('button[data="'+temp+'"]').removeClass('d-none');
                	break;
               	case 'Category':
	               $('button[data="'+temp+'"]').removeClass('d-none');
	               break;

	            case 'Product':
	               $('button[data="'+temp+'"]').removeClass('d-none');
	               break;

	            case 'Grand_Total':
	               $('button[data="'+temp+'"]').addClass('d-none');
	               break;
            }
        }
	});

	$('body').on('click', '.loadModal', function() {
		var thisAttrData = $(this).attr('data');
		var promotionOn = $('#promotionOn_'+ thisAttrData).val();

		switch(promotionOn) {
            case 'Brand':
            	$('#brandComponentModel').modal('toggle');
            	ajaxCall.loadBrandDataTable(thisAttrData, "", $('#promotionId').val());
                break;

            case 'Category':
               $('#categoryComponentModel').modal('toggle');
               ajaxCall.loadCategoryForPromotion(thisAttrData);
               break;

            case 'Product':
               $('#productComponentModel').modal('toggle');
               ajaxCall.getBrands();
               ajaxCall.getCategories();
               ajaxCall.loadProductForPromotion(thisAttrData, " ");
               break;
            }
	});

	// Handle click on checkbox
   	// $('#tableManufacturers tbody').on('click', 'input[type="checkbox"]', function(e){
    //   	var $row = $(this).closest('tr');

    //   	// Get row data
    //  	var data = $('#tableManufacturers').DataTable().row($row).data();

    //   	// Get row ID
    //  	var rowId = data['id'];

    //   	// Determine whether row ID is in the list of selected row IDs
    //   	var index = $.inArray(rowId, rows_selected);

    //   	// If checkbox is checked and row ID is not in list of selected row IDs
    //   	if(this.checked && index === -1){
    //   		$(this).attr('checked', true);
    //      	rows_selected.push(rowId);

    //   	// Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
    //   	} else if (!this.checked && index !== -1){
    //   		$(this).attr('checked', false);
    //      	rows_selected.splice(index, 1);
    //   	}

    //   	if(this.checked){
    //   		$(this).attr('checked', true);
    //      	$row.addClass('selected');
    //   	} else {
    //   		$(this).attr('checked', false);
    //      	$row.removeClass('selected');
    //   	}

    //   	// Prevent click event from propagating to parent
    //   	e.stopPropagation();
   	// });

   	$('body').on('click', '#saveBrands', function (e) {
   		var table = $('#tableManufacturers').DataTable();
   		rows_selected = [];
   		 // var form = this;
            	// rows_selected = [];
   		table.$('input[type="checkbox"]').each(function(){
         	// If checkbox doesn't exist in DOM
         	// if(!$.contains(document, this)){
            	// If checkbox is checked
            	if(this.checked){
            		// rows_selected.push($(this).val())
            		
            		console.log("iiiiiii");
            		rows_selected.push(this.value);
            	   // Create a hidden element 
            	   // $(form).append(
            	   //    $('<input>')
            	   //       .attr('type', 'text')
            	   //       .attr('class', 'idValues')
            	   //       .attr('name', this.name)
            	   //       .val(this.value)
            	   // );
            	}
         	// } 
      	});

   		var brandCounterValue = $('#brandCounterValue').val();
   		$('#promotionValue_'+brandCounterValue).val(rows_selected);
   		$("#brandComponentModel").modal('hide');

	});

	$('body').on('click', '#saveCategory', function () {
		// var categoryIds = [];
		// var selectedNodes = $('#category_list_div').jstree("get_selected", true);
		var selectedNodes = $('#categoryIds').val();

		var categoryCounterValue = $('#categoryCounterValue').val();

		$('#promotionValue_'+categoryCounterValue).val(selectedNodes);
		// $('#categoryComponentModel').hide();
		// $("#categoryComponentModel").dialog("close");
		$("#categoryComponentModel").modal('hide');
	});

	$('body').on('click', '#searchProduct', function () {
		var productCounterValue = $('#productCounterValue').val();
		var formData = {};
		formData = {'productTitle':$('#productName').val(), 'brandId':$('#brandNameId').val(),
					'categoryId':$('#selectCategory').val(), 'status':$('#status').val()};
		ajaxCall.loadProductForPromotion(productCounterValue, formData);
	});

	// Handle click on checkbox
   	// $('#tableProducts tbody').on('click', 'input[type="checkbox"]', function(e){
    //   	var $row = $(this).closest('tr');

    //   	// Get row data
    //  	var data = $('#tableProducts').DataTable().row($row).data();

    //   	// Get row ID
    //  	var rowId = data['id'];

    //   	// Determine whether row ID is in the list of selected row IDs
    //   	var index = $.inArray(rowId, productRowsSelected);

    //   	// If checkbox is checked and row ID is not in list of selected row IDs
    //   	if(this.checked && index === -1){
    //      	productRowsSelected.push(rowId);

    //   	// Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
    //   	} else if (!this.checked && index !== -1){
    //      	productRowsSelected.splice(index, 1);
    //   	}

    //   	if(this.checked){
    //      	$row.addClass('selected');
    //   	} else {
    //      	$row.removeClass('selected');
    //   	}

    //   	// Prevent click event from propagating to parent
    //   	e.stopPropagation();
   	// });

	$('body').on('click', '#saveProducts', function () {
		var tableProducts = $('#tableProducts').DataTable();
   		productRowsSelected = [];
   		 // var form = this;
            	// rows_selected = [];
   		tableProducts.$('input[type="checkbox"]').each(function(){
            if(this.checked){
            	productRowsSelected.push(this.value);
            }
      	});

   		var productCounterValue = $('#productCounterValue').val();
   		$('#promotionValue_'+productCounterValue).val(productRowsSelected);
   		$("#productComponentModel").modal('hide');
	});

	// Handle click on "Select all" control on brands componenet
   	$('body').on('click', '#brandCheckAll', function(){
      	// Get all rows with search applied
      	var table = $('#tableManufacturers').DataTable();
      	var rows = table.rows({ 'search': 'applied' }).nodes();

      	$('input[type="checkbox"]', rows).prop('checked', this.checked);
   	});

   	// Handle click on checkbox to set state of "Select all" control on brand component
   	$('#tableManufacturers tbody').on('change', 'input[type="checkbox"]', function(){
      	// If checkbox is not checked
      	if(!this.checked){
         	var el = $('#brandCheckAll').get(0);
         	// If "Select all" control is checked and has 'indeterminate' property
         	if(el && el.checked && ('indeterminate' in el)){
            	// Set visual state of "Select all" control
            	// as 'indeterminate'
            	el.indeterminate = true;
         	}
      	}
   	});

   	
   	// Handle click on "Select all" control on brands componenet
   	$('body').on('click', '#productCheckAll', function(){
      	// Get all rows with search applied
      	var tableProducts = $('#tableProducts').DataTable();
      	var rows = tableProducts.rows({ 'search': 'applied' }).nodes();

      	$('input[type="checkbox"]', rows).prop('checked', this.checked);
   	});

   	// Handle click on checkbox to set state of "Select all" control on brand component
   	$('#tableProducts tbody').on('change', 'input[type="checkbox"]', function(){
      	// If checkbox is not checked
      	if(!this.checked){
         	var el = $('#productCheckAll').get(0);
         	// If "Select all" control is checked and has 'indeterminate' property
         	if(el && el.checked && ('indeterminate' in el)){
            	// Set visual state of "Select all" control
            	// as 'indeterminate'
            	el.indeterminate = true;
         	}
      	}
   	});

});

var ajaxCall = {
	loadBrandDataTable: function (thisCounterValue, searchBrand, promotionId) {
		$('#brandCounterValue').val(thisCounterValue);
		$('#tableManufacturers').DataTable().destroy();
		$('#tableManufacturers').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
	            url: baseUrl + '/admin/promotions/manufacturersList',
	            data: function (d) {
	                d.name = searchBrand;
	                d.promotionId = promotionId;
	            }
	        },

            columns: [{
            	"target": 0,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
            		return '<input type="checkbox" name="id[]" value="' + row['id'] + '">'
                },
            },{
            	"target": 1,
            	"data":'brandName'
            },{
            	"target": 2,
            	"data":'status'
            }]
        });
	},

	loadCategoryForPromotion : function (thisCounterValue) {
		// categoryDropdown : function(language) {
		$('#categoryCounterValue').val(thisCounterValue);
        $.ajax({
            type: "get",
            url: baseUrl+"/admin/promotions/categoryList",
            success: function (response) {
            	$('#categoryIds').empty();
                $.each(response, function (index,item) {
                    var text = item['category'];
                    var value = item['id'];
                    var o = new Option(text, value);
                    $('#categoryIds').append(o);
                })
            }
        });
    // }
  //       url = baseUrl + "/admin/categories";
	    
	 //    $('#category_list_div').jstree({
	 //        'core': {
	 //            'data': {
	 //                'url': url,
	 //                'data': {},
	 //                "dataType": "json"
	 //            },
	 //            'check_callback': true,
	 //            'themes': {
	 //                'responsive': false
	 //            }
		// 	},
			
	 //        "plugins": ["dnd", "types", "checkbox"],
	       
	 //    });
	},

	loadProductForPromotion : function (thisCounterValue, formData) {
		$('#productCounterValue').val(thisCounterValue);
		$('#tableProducts').DataTable().destroy();
		$('#tableProducts').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
	            url: baseUrl + '/admin/promotions/productList',
	            data: function (d) {
	                d.title = formData['productTitle'];
	                d.brandId = formData['brandId'];
	                d.selectCategory = formData['categoryId'];
	                d.status = formData['status'];
	            }
	        },

            columns: [{
            	"target": 0,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox' value='"+ row['id'] +"' class='productCheckBox'>";
                },
            },{
            	"target": 1,
            	"data":'title'
            },{
            	"target": 2,
            	"data":'sku'
            },{
            	"target": 3,
            	"data":'categoryTitle'
            },{
            	"target": 4,
            	"data":'brandName'
            }]
        });
	},

	//get brands list
	getBrands : function () {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
          	type: 'get',
          	url: baseUrl +'/admin/promotions/brands',
          	beforeSend: function() {
               	$('#loaderimage').css("display", "block");
               	$('#loadingorverlay').css("display", "block");
          	},
          	success: function (response) {
          		appendHtml.appendBrandDropDown(response);
          	}
        });
	},

	getCategories : function(){
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
          	type: 'get',
          	url: baseUrl +'/admin/promotions/categories',
          	beforeSend: function() {
               	$('#loaderimage').css("display", "block");
               	$('#loadingorverlay').css("display", "block");
          	},
          	success: function (response) {
          		appendHtml.appendCategoryDropDown(response);
          	}
        });
	},
}

var appendHtml = {
	//append brands dropdown
	appendBrandDropDown : function (brands) {
		var brandId = document.getElementById('brandNameId');
		$.each(brands, function(index, item) {
			var value = item['id'];
			var text = item['brandName'];
			var o = new Option(text, value);
			brandId.append(o);
		});
	},

	//append brands dropdown
	appendCategoryDropDown : function (categories, elementId) {
		$('#selectCategory').empty();
		var selectCategory = document.getElementById('selectCategory');

		$.each(categories, function(index, item) {
			var value = item['id'];
			var text = item['category'];
			var o = new Option(text, value);
			selectCategory.append(o);
		});
	},
};

var formValidations = {
    generalValidation : function() {
        // Initialize form validation on the registration form.
        // It has the name attribute "registration"
        $("form[name='editPromotion']").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                couponTitle: "required",
                customTitle: "required",
                couponCode: "required",
                discountAmount: "required",
            },
            // Specify validation error messages
            messages: {
                couponTitle: "Please Enter Title",
                customTitle: "Please Enter Custom Title",
                couponCode: "Please Enter Coupon Code",
                discountAmount: "Please Enter Discount Amount",
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    },

    promotionConditionValidation : function() {
        // Initialize form validation on the registration form.
        // It has the name attribute "registration"
        $("form[name='promotionConditions']").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                "promotionOn[]": "required",
                "conditionType[]": "required",
                //"promotionValue[]": "required",
            },
            // Specify validation error messages
            messages: {
                "promotionOn[]": "Please Select Promotion On",
                "conditionType[]": "Please Select Condition Type",
                // "promotionValue[]": "Please Enter Promotion value",
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    }
};
