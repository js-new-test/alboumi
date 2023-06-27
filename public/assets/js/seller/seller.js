$(document).ready(function() {
	init.handler();
	ajaxCall.getSellers($('#languageId').val());

});

var init = {
	handler : function () {
		$('body').on('click', '#divFilterToggle', function () {
			$("#FilterLangDiv").slideToggle('slow');
	    });

		$('body').on('click', '.deleteSeller', function () {
			$('#sellerId').val($(this).attr('data'));
		});

		$('body').on('click', '#confirmDelete', function () {
			ajaxCall.getSellerDelete($('#sellerId').val());
		});

		$('body').on('click', '#filter_faq', function () {
	        var lang_id = $('#languageId').val();
	        ajaxCall.getSellers(lang_id)
	    });

	}
}

var ajaxCall = {
	getSellers : function (languageId) {
		$('#tblSellers').DataTable().destroy();
        $('#tblSellers').DataTable({
            processing: true,
            serverSide: true,
            // "scrollX": true,
            "initComplete": function (settings, json) {  
                $("#tblSellers").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            ajax: {
	            url: baseUrl + '/admin/sellers/list',
	            data: function (d) {
	                d.languageId = languageId;
	                // d.promotionCode = formData['promotionCode'];
	                // d.promotionStatus = formData['promotionStatus'];
	            }
	        },
            columns: [{
                "target": 0,
                "data":'id'
            },{
                "target": 1,
                "data":'title'
            },{
                "target": 2,
                // "data":'price'
                "bSortable": false,
                "render": function ( data, type, row ) {
                	var price = row['price'];
                    return "<span>"+ parseFloat(price).toFixed(3) +"</span>";
                }
            },{
                "target": 3,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) {
                    return "<img src='" + baseUrl + "/public/assets/images/seller/"+row['image']+"' class='rounded' style='height:50px; width:50px;'>";
                }
            },{
                "target": 4,
                "data":'sort_order'
            },{
                "target": -1,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) {
                    return "<a href='seller/editSeller/"+row['id']+"'><i class='fa fa-edit'></i></a>"+
                    "&nbsp <a class='text-danger'><i class='fa fa-trash deleteSeller' aria-hidden='true' data-toggle='modal' data='"+row['id']+"' data-target='#sellerDeleteModel'></i></a>";
                },
            }]
        });
	},

	getSellerDelete : function (sellerId) {
		$.ajax({
                type: "get",
                url:baseUrl + '/admin/seller/deleteSeller',
                data:{'sellerId':sellerId},
                success: function (result) {
                    if (result['alert-type'] == "success") {
                        toastr.success(result['message']);
                        $("#sellerDeleteModel").modal('hide');
                        ajaxCall.getSellers($('#languageId').val());
                    }
                }
            });
	}
}
