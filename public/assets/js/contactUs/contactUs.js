$(document).ready(function() {

	var startDate = " ";
	var endDate = " ";
	$('#divFilter').hide();
	ajaxCall.getContactUs(startDate, endDate);

	$('body').on('click', '.viewMessage', function () {
		var activity = "";
		if ($(this).attr('data-target') == "#contactUsReplyModal") {
			activity = "contactUsReplyModal";
		}else if ($(this).attr('data-target') == "#contactUsMessageModal") {
			activity = "contactUsMessageModal";
		}
		ajaxCall.getContactUsInquiryData($(this).attr('data'), activity);
	});

	$('body').on('click', '#reply', function() {
		var formData = {'inquiryId':$('#inquiryId').val(), 'replyMessage':CKEDITOR.instances['replyMessage'].getData()
	};
		ajaxCall.postReply(formData);
	})

	// $('body').on('click', '.applyBtn', function() {
	// 	startDate = $('#daterange').data('daterangepicker').startDate;
	// 	endDate = $('#daterange').data('daterangepicker').endDate;

	// 	startDate = startDate.format('DD/MM/YYYY');
	// 	endDate = endDate.format('DD/MM/YYYY');

	// 	ajaxCall.getContactUs(startDate, endDate);
	// });

	$('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        startDate = picker.startDate.format('YYYY-MM-DD');
		endDate = picker.endDate.format('YYYY-MM-DD');
		ajaxCall.getContactUs(startDate, endDate);
    });
    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
	});
	
	$('body').on('click', '#divFilterToggle', function () {
		$("#divFilter").toggle();
	});

	$('body').on('click', '#resetDate', function() {
		ajaxCall.getContactUs(" ", " ");
	});

	$('body').on('click', '.deleteInquiry', function () {
		$('#inquiryIdForDelete').val($(this).attr('data'));
	});

	$('body').on('click', '#confirmDelete', function () {
		ajaxCall.postDeleteInquiry($('#inquiryIdForDelete').val())
	})
});

var ajaxCall = {
	getContactUs : function (startDate, endDate) {
		$('#tblContactUs').DataTable().destroy();
		$('#tblContactUs').DataTable({
            processing: true,
            serverSide: true,
			"initComplete": function (settings, json) {  
				$("#tblContactUs").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
			},
            "ajax": {
		        'type': 'get',
		        'url': baseUrl+'/admin/contactUs/contactUsData',
		        'data': {startDate:startDate, endDate:endDate}
			},
			'order': [[4, 'desc']],
            columns: [/*{
            	"target": 0,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
                    return "<input type='checkbox'>";
                },
			},*/
			{
            	"target": 0,
            	"data":'id'
            },
			{
            	"target": 1,
            	"data":'name'
            },{
            	"target": 2,
            	"data":'email'
            },{
            	"target": 3,
            	"data":'message'
            },
			// {
            // 	"target": 4,
            //     "data": 'ip_address'
			// },
			{ data: 'a_created_at', name: 'a_created_at', target: 10,
                render: function (data,type,row) {
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone;
                        return moment.utc(row.a_created_at).utcOffset(z.replace(':', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }
                }
            },{
            	"target": -1,
                "bSortable": false,
                "order":false,
            	"render": function ( data, type, row ) {
            		var isReplied = "<a href='#'><i class='fas fa-reply viewMessage' data-toggle='modal' data='"+row['id']+"' data-target='#contactUsReplyModal'></i></a> <a href='#' class='text-danger'><i class='fas fa-trash deleteInquiry' data-toggle='modal' data-target='#contactUsDeleteModel' data='"+row['id']+"'></i></a>";
            		if(row['is_replied'] == 1){
            			isReplied = "<a href='#'><i class='fas fa-eye viewMessage' data-toggle='modal' data='"+row['id']+"' data-target='#contactUsMessageModal'></i></a> <a href='#' class='text-danger'><i class='fas fa-trash deleteInquiry' data-toggle='modal' data-target='#contactUsDeleteModel' data='"+row['id']+"'></i></a>";
            		}
                    return isReplied;
                },
            }]
        });
	},

	getContactUsInquiryData : function (inquiryId, activity) {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
          	type: 'get',
          	url: baseUrl +'/admin/contactUs/inquiry',
          	data: {'inquiryId':inquiryId, 'activity':activity},
          	beforeSend: function() {
               	$('#loaderimage').css("display", "block");
               	$('#loadingorverlay').css("display", "block");
          	},
          	success: function (response) {
          		if (typeof response['contact_us_reply'] === 'undefined') {
	          		$('#customerName').val(response['name']);
	          		$('#customerMessage').text(response['message']);
	          		$('#inquiryId').val(response['id']);
				} else if (typeof response['contact_us_reply'] !== 'undefined') {
					$('#customerNameView').text(response['name']);
	          		$('#customerMessageVew').text(response['message']);
	          		$('#replyMessageView').text(response['contact_us_reply']['reply']);
				}
          	}
        });
	},

	postReply : function(formData) {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});
		$.ajax({
	        url: baseUrl+'/admin/contactUs/reply',
	        type: 'POST',              
	        data: formData,
	        success: function(result)
	        {
	        	if (result['success'] == true) {
					$('#contactUsReplyModal').modal('hide');
	        		toastr.success(result.message);
	        	}
	        },
	        error: function(data)
	        {
	            console.log(data);
	        }
	    });
	},

	postDeleteInquiry : function (inquiryIdForDelete) {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});
		$.ajax({
	        url: baseUrl+'/admin/contactUs/deleteInquiry',
	        type: 'POST',              
	        data: {inquiryIdForDelete : inquiryIdForDelete},
	        success: function(result)
	        {
	        	if (result['success'] == true) {
					toastr.success(result.message);
					$("#contactUsDeleteModel").modal('hide');
					$('#tblContactUs').DataTable().ajax.reload();
	        	}
	        },
	        error: function(data)
	        {
	            console.log(data);
	        }
	    });
	}
}
