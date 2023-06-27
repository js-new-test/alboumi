$(document).ready(function() {
	init.handler();
	ajaxCall.getPromotions(" ");

});

var init ={
	handler : function () {
		$('#divFilter').hide();
		
		$('body').on('click', '#divFilterToggle', function () {
			$("#divFilter").toggle();
		});

		/** toggle active switch and show confirmation */
		$('body').on('click', 'tbody .toggleIsActive', function () {
			var isActive = ($(this).attr('aria-pressed') === 'true') ? 0 : 1;
			var promotionId = $(this).attr('data');

			$('#confirmationModel').on('show.bs.modal', function(e){
				$('#promotionId').val(promotionId);
				$('#promotionStatusForDelete').val(isActive);
			});
			$('#confirmationModel').modal('show');
		});

		$('body').on('click', '#confirmStatus', function () {
			var promotionId = $('#promotionId').val();
			var promotionStatusForDelete = $('#promotionStatusForDelete').val();
			ajaxCall.activeInactivePromotion(promotionId, promotionStatusForDelete, "STATUS");
		});

		$('body').on('click', 'tbody .toggleAdminApproved', function () {
			var isApprove = ($(this).attr('aria-pressed') === 'true') ? 0 : 1;
			var promotionId = $(this).attr('data');
			$('#approvalModel').on('show.bs.modal', function(e){
				$('#promotionIdForApprove').val(promotionId);
				$('#promotionApprove').val(isApprove);
			});
			$('#approvalModel').modal('show');
		});

		$('body').on('click', '#confirmApprove', function () {
			var promotionId = $('#promotionIdForApprove').val();
			var promotionApprove = $('#promotionApprove').val();
			ajaxCall.activeInactivePromotion(promotionId, promotionApprove, "ADMINAPPROVE");
		});

		$('body').on('click', '#searchPromotionData', function () {
			var formData = {'promotionTitle': $('#promotionTitle').val(), 'promotionCode': $('#promotionCode').val(), 'promotionStatus':$('#promotionStatus').val()};

			ajaxCall.getPromotions(formData);
		});

		$('body').on('click', '.deletePromotion', function () {
			$('#promotionIdForDelete').val($(this).attr('data'))
		});

		$('body').on('click', '#confirmDelete', function () {
			ajaxCall.deletePromotion($('#promotionIdForDelete').val())
		})
	}
}

var ajaxCall = {
	getPromotions : function (formData) {
		$('#tblPromotions').DataTable().destroy();
        $('#tblPromotions').DataTable({
            processing: true,
            serverSide: true,
			"initComplete": function (settings, json) {  
				$("#tblPromotions").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
			},
            ajax: {
	            url: baseUrl + '/admin/promotions/list',
	            data: function (d) {
	                d.promotionTitle = formData['promotionTitle'];
	                d.promotionCode = formData['promotionCode'];
	                d.promotionStatus = formData['promotionStatus'];
	            }
	        },
            // "ajax": {
            //     'type': 'get',
            //     'url': baseUrl+'/admin/promotions/list'
            // },
            columns: [{
                "target": 0,
                "data":'id'
            },{
                "target": 1,
                "data":'title'
            },{
                "target": 2,
                "data":'coupon_code'
            },{
                "target": 3,
				render: function (data, type, row)
                {
					console.log(row)
					output = '';
					if(row.coupon_usage_limit == null)
						output += "N/A";
					else
						output += row.coupon_usage_limit;
					return output;
				}
                // "data":'coupon_usage_limit'
			},
			// {
            //     "target": 4,
            //     "data":'number_times_used'
			// },
			{
                "target": 5,
                "data":'discount_type'
			},
			{
                "target": 5,
                "data":'discount_amount'
			},
			{
                "target": 5,
				"data":'coupon_user_types'
			},
		
			{
                "target": 5,
                "data":'startdate'
            },{
                "target": 6,
                "data":'enddate'
            },{
                "target": 7,
                render: function (data, type, row)
                {
                    var output = '<button type="button" class="btn btn-sm btn-toggle toggleIsActive" data-toggle="button" data-target="confirmationModel" aria-pressed="false" autocomplete="off" data='+row['id']+'>'
                        +'<div class="handle"></div>'
                        +'</button>';
                    if(row.status == 'Active') {
                        output = '<button type="button" class="btn btn-sm btn-toggle active toggleIsActive" data-toggle="button" data-target="confirmationModel" aria-pressed="true" autocomplete="off" data='+row['id']+'>'
                        +'<div class="handle"></div>'
                        +'</button>'
                    }
                    return output;
                },
			},
			// {
            //     "target": 8,
            //     render: function (data, type, row)
            //     {
            //         var output = '<button type="button" class="btn btn-sm btn-toggle toggleAdminApproved" data-toggle="button" aria-pressed="false" autocomplete="off" data='+row['id']+'>'
            //             +'<div class="handle"></div>'
            //             +'</button>';
            //         if(row.is_admin_approved == 'Yes') {
            //             output = '<button type="button" class="btn btn-sm btn-toggle active toggleAdminApproved" data-toggle="button" aria-pressed="true" autocomplete="off" data='+row['id']+'>'
            //             +'<div class="handle"></div>'
            //             +'</button>'
            //         }
            //         return output;
            //     },
			// },
			{
                "target": -1,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) {
                    return "<a href='promotions/editPromotion/"+row['id']+"'><i class='fa fa-edit'></i></a>&nbsp &nbsp"+
                    '<i class="fa fa-trash deletePromotion text-danger" aria-hidden="true" data-toggle="modal" data="'+row['id']+'" data-target="#promotionDeleteModel"></i>';
                },
            }]
        });
	},

	activeInactivePromotion : function(promotionId, promotionStatusForDelete, part) {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url: baseUrl+'/admin/promotions/updatePromotion',
			type: 'POST',
			data: {promotionId : promotionId, promotionStatus : promotionStatusForDelete, part:part},
			success: function(result)
			{
				if (result['success'] == true) {
					toastr.success(result.message);
					if (part == 'STATUS') {
						$("#confirmationModel").modal('hide');
					}else if (part == 'ADMINAPPROVE') {
						$("#approvalModel").modal('hide');
					}
					ajaxCall.getPromotions(" ");
				}
			},
			error: function(data)
			{
				console.log(data);
			}
		});
	},

	deletePromotion: function (promotionId) {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url: baseUrl+'/admin/promotions/deletePromotion',
			type: 'POST',
			data: {promotionId : promotionId},
			success: function(result)
			{
				if (result['success'] == true) {
					$("#promotionDeleteModel").modal('hide');
					toastr.success(result.message);
					ajaxCall.getPromotions(" ");
				}
			},
			error: function(data)
			{
				console.log(data);
			}
		});
	}
}
