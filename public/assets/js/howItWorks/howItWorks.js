$(document).ready(function() {
	init.handler();
	ajaxCall.getHowitWorks($('#languageId').val());

});

var init = {
	handler : function () {
		$('body').on('click', '#divFilterToggle', function () {
			$("#FilterLangDiv").slideToggle('slow');
	    });

		$('body').on('click', '.deleteHowitWorks', function () {
			$('#howItWorksIdForDelete').val($(this).attr('data'));
		});

		$('body').on('click', '#confirmDelete', function () {
			ajaxCall.getHowitWorksDelete($('#howItWorksIdForDelete').val());
		});

		$('body').on('click', '#filter_faq', function () {
	        var lang_id = $('#languageId').val();
	        ajaxCall.getHowitWorks(lang_id)
	    });

        /** toggle active switch and show confirmation */
        $('body').on('click', 'tbody .toggleIsActive', function () {
            var isActive = ($(this).attr('aria-pressed') === 'true') ? 0 : 1;
            var howItWorksId = $(this).attr('data');

            $('#confirmationModel').on('show.bs.modal', function(e){
                $('#howItWorksId').val(howItWorksId);
                $('#statusForDelete').val(isActive);
            });
            $('#confirmationModel').modal('show');
        });

        $('body').on('click', '#confirmStatus', function () {
            var howItWorksId = $('#howItWorksId').val();
            var statusForDelete = $('#statusForDelete').val();
            ajaxCall.activeInactiveHowItWorks(howItWorksId, statusForDelete);
        });

	}
}

var ajaxCall = {
	getHowitWorks : function (languageId) {
		$('#tblHowitWorks').DataTable().destroy();
        $('#tblHowitWorks').DataTable({
            processing: true,
            serverSide: true,
            // "scrollX": true,
            "initComplete": function (settings, json) {  
                $("#tblHowitWorks").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            ajax: {
	            url: baseUrl + '/admin/howitWorks/list',
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
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) {
                    return "<img src='" + baseUrl + "/public/assets/images/howItWorks/"+row['image']+"' class='rounded' style='height:50px; width:50px;'>";
                }
            },{
                "target": 3,
                "data":'sort_order'
            },{
                "target": 4,
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
                }
            },{
                "target": -1,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row ) {
                    return "<a href='howitWorks/editHowItWorks/"+row['id']+"'><i class='fa fa-edit'></i></a>"+
                    "&nbsp <a class='text-danger'><i class='fa fa-trash deleteHowitWorks' aria-hidden='true' data-toggle='modal' data='"+row['id']+"' data-target='#deleteConfirmationModel'></i></a>";
                },
            }]
        });
	},

	getHowitWorksDelete : function (howItWorksId) {
		$.ajax({
                type: "get",
                url:baseUrl + '/admin/howitWorks/deleteHowitWorks',
                data:{'howItWorksId':howItWorksId},
                success: function (result) {
                    if (result['alert-type'] == "success") {
                        toastr.success(result['message']);
                        $("#deleteConfirmationModel").modal('hide');
                        ajaxCall.getHowitWorks($('#languageId').val());
                    }
                }
            });
	},

    activeInactiveHowItWorks : function(howItWorksId, statusForDelete) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: baseUrl+'/admin/howitWorks/updateHowitWorksStatus',
            type: 'POST',
            data: {howItWorksId : howItWorksId, statusForDelete : statusForDelete},
            success: function(result)
            {
                if (result['success'] == true) {
                    toastr.success(result.message);
                    // if (part == 'STATUS') {
                        $("#confirmationModel").modal('hide');
                    // }else if (part == 'ADMINAPPROVE') {
                    //     $("#approvalModel").modal('hide');
                    // }
                    ajaxCall.getHowitWorks($('#languageId').val());
                }
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }
}
