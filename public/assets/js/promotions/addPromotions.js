$(document).ready(function() {
	formValidations.generalValidation();
	init.handler();

});

var init = {
	handler : function() {
		$('body').on('click', '#generatePromotionCode', function() {
			ajaxCall.generatePromotionCode();
		})
	}
}

var ajaxCall = {
	generatePromotionCode :function () {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url: baseUrl+'/admin/promotions/generateAutoPromotionCode',
			type: 'get',
			success: function(result)
			{
				if (result.success == true) {
					$('#couponCode').val(result.couponCode)
				}
			},
			error: function(data)
			{
				console.log(data);
			}
		});
	}
};

var formValidations = {
    generalValidation : function() {
        // Initialize form validation on the registration form.
        // It has the name attribute "registration"
        $("form[name='addPromotion']").validate({
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
    }
};