$(document).ready(function() {
	init.handler();
	// formValidation.currencyConversion();

	appendHtml.appendCurrencyDropdown(currency);




	$('#currencyConversionForm').on('submit', function() {
      var $form = $(this);
      // return false would prevent default submission
      // debugger;
      var tst = $form.find('.test').length;
      // alert();
      var res = "";
      for (var i = 0; i <= tst; i++) {

      	res = isNotEmpty($form.find('.'+ i), "Please enter conversion rate!",
               $form.find('#'+ i));
      	// debugger;
      		if (i+1 == tst) {
      			return res;
      		}
      	// debugger;
      }
   });
			
});
function isNotEmpty(inputElm, errMsg, errElm) {
   var isValid = (inputElm.val().trim() !== "");
   postValidate(isValid, errMsg, errElm, inputElm);
   return isValid;
}
function postValidate(isValid, errMsg, errElm, inputElm) {
   if (!isValid) {
      // Show errMsg on errElm, if provided.
      if (errElm !== undefined && errElm !== null
            && errMsg !== undefined && errMsg !== null) {
         errElm.html(errMsg);
      }
      // Set focus on Input Element for correcting error, if provided.
      if (inputElm !== undefined && inputElm !== null) {
         inputElm.addClass("errorBox");  // Add class for styling
         inputElm.focus();
      }
   } else {
      // Clear previous error message on errElm, if provided.
      if (errElm !== undefined && errElm !== null) {
         errElm.html('');
      }
      if (inputElm !== undefined && inputElm !== null) {
         inputElm.removeClass("errorBox");
      }
   }
}
var init = {
	handler : function () {
		$('body').on('change', '#currency', function () {
			var selectedText = $(this).children("option:selected").text().match(/\((.*)\)/)[1];
			var selectedValue = $(this).val();
			$('#selectedCurrencyId').val(selectedValue);
			ajaxCall.getRemainingCurrencies(selectedValue, selectedText);
		});

		// $('body').on('click', '#send', function () {
		// 	$("form#currencyConversionForm :input[type=text]").each(function(){
		// 	 	// var input = $(this); // This is the jquery object of the input, do what you will
		// 	});
			
		// })
	}
}

var ajaxCall = {
	getRemainingCurrencies : function (selectedValue, selectedText) {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
          	type: 'get',
          	url: baseUrl +'/admin/currencyConversion/remainingCurrencies',
          	data: {'selectCurrency':selectedValue},
          	// beforeSend: function() {
           //     	$('#loaderimage').css("display", "block");
           //     	$('#loadingorverlay').css("display", "block");
          	// },
          	success: function (response) {
          		// console.log(response);
          		appendHtml.appendCurrencyConversionHtml(selectedText, response);
          	}
        });
	}
}

var appendHtml = {
	appendCurrencyDropdown : function (currency) {
		$('#currency').empty();
		var selectCurrency = document.getElementById('currency');
		$.each(currency, function(index, item) {
			var value = item['id'];
			var text = item['currency_name'] + "(" + item['code'] + ")";
			var o = new Option(text, value);
			selectCurrency.append(o);
		});
		var selectedText = $('#currency').children("option:selected").text().match(/\((.*)\)/)[1];		
		var selectedValue = $('#currency').val()	
		ajaxCall.getRemainingCurrencies(selectedValue, selectedText);
	},

	appendCurrencyConversionHtml : function (selectedText, remainingCurrencies) {
		$('#tableBody').html(" ");
		var tr = "";
		var ind = 0;
		$.each(remainingCurrencies, function(index, item) {
			var rate = (item['rate'] === 0) ? '' :item['rate'];
			tr = "<tr><td> " + selectedText + " </td>"+
					"<td>" + item['code'] + "<span class='text-danger'>*</span></td>"+
					"<td><input type='hidden' name='currencyId[]' value="+item['currencyId']+"><input type='text' name='currencyCode[]' class='test "+ ind +"' value="+ rate +" ><span class='elmNameError' id='"+ ind +"' style='color:red;'></span></td>"+
					 "</tr>";
				ind++;
			$('#tableBody').append(tr);
		});
	}
};


//Removed partially will check this later and remove permanently
// var formValidation = {
// 	currencyConversion : function () {
// 		$("#currencyConversionForm").validate({
// 			ignore: [], // ignore NOTHING
// 			rules: {
// 				'currencyCode[]' :{
// 					required: true,
// 				}
// 			},
// 			messages: {
// 				"currencyCode[]" :{
// 					required: "Please enter value for each Coversion Rate",
// 				}
// 			},
// 			errorPlacement: function (error, element)
// 			{
// 				$("tbody").find("tr td input").each(function() {
// 					debugger;
// 		            console.log(element);
// 					error.insertAfter(element)
// 		            // Here you can write the logic for validation              
// 					// console.log(element);
// 		       });
				
// 			},
// 			submitHandler: function(form)
// 			{
// 				form.submit();
// 			}
// 		});

// 		// $(".test").each(function(){
// 		//     $(this).rules("add", {
// 		//         required:true,
// 		//         messages:{required:'Please enter value for each language'}
// 		//     });
// 		// });
// 	}
// }