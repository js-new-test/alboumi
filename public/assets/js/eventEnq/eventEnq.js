$(document).ready(function(){

    var lang_id = $('#filterLanguage').find(":selected").val();
    
    var startDate = " ";
    var endDate = " ";

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate = picker.endDate.format('YYYY-MM-DD');
    });
  
    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    

    init.setDropdownValues();

    filterData = {'startDate' : startDate, 'endDate':endDate,'lang_id': lang_id, 'eventId': events,
    'packageId': pkg, 'status': status,
    'payment': paymentStatus,"photographer": photographerVal}

    ajaxCalls.getEventEnquiries(filterData);

    $('body').on('click', '#divFilterToggle', function () {
		$("#filterEnqDiv").slideToggle('slow');
    });

    // get default lang event n package
    ajaxCalls.getEvents(lang_id);
    ajaxCalls.getPackages(lang_id);

    // change event and pkg dropdown based on language
    $('#filterLanguage').change(function() {
        lang_id = $('#filterLanguage').find(":selected").val();
        ajaxCalls.getEvents(lang_id);
        ajaxCalls.getPackages(lang_id);
    })

    $('input[name="daterange"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });
    

    // on click of filter button
    $('body').on('click', '#btnFilterEventEnq', function () {

        init.setDropdownValues();

        // startDate = $('#daterange').data('daterangepicker').startDate;
        // endDate = $('#daterange').data('daterangepicker').endDate;

        // startDate = startDate.format('YYYY-MM-DD');
        // endDate = endDate.format('YYYY-MM-DD');

        filterData = {'startDate' : startDate, 'endDate':endDate,'lang_id': lang_id, 'eventId': events,
                 'packageId': pkg, 'status': status,
                 'payment': paymentStatus, "photographer": photographerVal}

        ajaxCalls.getEventEnquiries(filterData);
    });

    // Reset filter
    $('body').on('click','#resetFilter',function(){
        $('#filterEventEnqForm')[0].reset();
        startDate = " ";
        endDate = " ";
    
        filterData = {'startDate' : startDate, 'endDate':endDate,'lang_id': lang_id, 'eventId': events,
                 'packageId': pkg, 'status': status,
                 'payment': paymentStatus, "photographer": photographerVal}

        ajaxCalls.getEventEnquiries(filterData);

    });
});

var init = {
    setDropdownValues :function()
    {
        statusVal = $('#filterStatus').val();
        if(statusVal == -1)
            status = "";
        else
            status = statusVal;

        paymentStatusVal = $('#filterPaymentStatus').val();
        if(paymentStatusVal == -1)
            paymentStatus = "";
        else
            paymentStatus = paymentStatusVal;

        packageVal = $('#filterPackage').val();
        if(packageVal == -1)
            pkg = "";
        else
            pkg = packageVal;

        eventVal = $('#filterEvent').val();
        if(eventVal == -1)
            events = "";
        else
            events = eventVal;

        photographerVal = $('#photographerFilter').val();
        if(photographerVal == -1)
            photographerVal = "";
        else
            photographerVal = photographerVal;
    }
}
var table;
var ajaxCalls = {
    getEventEnquiries : function (filterData) {
        $('#eventEnqListing').DataTable().destroy();
        table = $('#eventEnqListing').DataTable({
            processing: true,
            serverSide: true,
            'scrollX': true,
            "ajax": {
                'type': 'get',
                'url': window.location.href + '/list',
                'data': filterData
            },
            columnDefs: [
                {
                    "targets": [0,1,2,8,9],
                    "className": "text-center",
                },
                {
                    "targets": [1,2,13,14,15],
                    "visible": false
                },
            ],
            'order': [[10, 'desc']],
            columns: [
            {
                "target": 0,
                "data":'rownum'
            },
            {
                "target": 1,
                "data":'id'
            },
            {
                "target": 2,
                "data":'price_per_photo'
            },
            {
                "target": 3,
                "data":'event_date'
            },
            {
                "target": 4,
                "data":'full_name'
            },
            {
                "target": 5,
                "data" :"event_name"
            },
            {
                "target": 6,
                "data" :"package_name"
            },
            {
                "target": 7,
                "data" :"additional_package_name"
            },
            {
                "target": 8,
                "render": function ( data, type, row )
                {
                    if(row.status == 0)
                        return '<b><span class="badge badge-danger">Pending</span></b>';
                    if(row.status == 1)
                        return '<b><span class="badge badge-primary">In Process</span></b>';
                    if(row.status == 2)
                        return '<b><span class="badge badge-warning">Images Uploaded</span></b>';
                    if(row.status == 3)
                        return '<b><span class="badge badge-success">Completed</span></b>';
                    if(row.status == 4)
                        return '<b><span class="badge badge-info">Approved</span></b>';
                },
            },
            {
                "target": 9,
                "render": function ( data, type, row )
                {
                    if(row.payment_status == 0)
                        return '<b><span class="badge badge-danger">Unpaid</span></b>';
                    if(row.payment_status == 1)
                        return '<b><span class="badge badge-success">Paid</span></b>';
                },
            },
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
            },
            {
                "target": 11,
                "render": function ( data, type, row )
                {
                    if(row.photographer_id == 0)
                        return "-----";
                    if(row.photographer_id != 0)
                        return row.firstname + " " + row.lastname;
                },
            },
            {
                "target": -1,
                "bSortable": false,
                "order":false,
                "render": function ( data, type, row )
                {
                    var output = "";
                    var disable = (row.photographer_id == 0) ? "" : "disabled";
                    output += '<div class="d-inline-block dropdown">'
                    output += '<button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn-shadow dropdown-toggle btn btn-primary">'
                    output += '<span class="btn-icon-wrapper pr-2 opacity-7"><i class="pe-7s-settings fa-w-20"></i></span></button>'
                    output += '<div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">'
                    output += '<ul class="nav flex-column"><li class="nav-item"><a class="nav-link" id="updatePhotoPrice" >Update Price</a></li>'
                    output += '<li class="nav-item"><a class="nav-link" onclick="return callViewEnqDetailsRoute('+row.id+');">View Details</a></li>'
                    output += '<li class="nav-item"><a class="nav-link" id="allocate_photo_model"'+disable+'>Photographer Allocation</a></li>'
                    output += '<li class="nav-item"><a class="nav-link" id="changeEnqStatus">Change Status</a></li>'
                    output += '<li class="nav-item"><a class="nav-link" onclick="return callShowPhotosListRoute('+row.id+');" id="showPhotosList">Manage Photos</a></li></ul>'
                    output += '</div></div>'
                    return output;
                },
            },
            {
                "target": 13,
                "data":'total_amount'
            },
            {
                "target": 14,
                "data":'advance_payment'
            },
            {
                "target": 14,
                "data":'free_photo_download'
            }
            ],
            fnDrawCallback: function() {
            // $('.toggle-is-active').bootstrapToggle();
            $('.toggle-is-approve').bootstrapToggle();
            $('.toggle-is-deleted').bootstrapToggle();
        },
        })
    },

    getEvents: function(lang_id) {
        $.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
          	type: 'post',
          	url: baseUrl +'/admin/event/list/filter-event',
            data:{'filter_event_lang':lang_id},
          	beforeSend: function() {
               	$('#loaderimage').css("display", "block");
               	$('#loadingorverlay').css("display", "block");
          	},
          	success: function (response) {
          		appendHtml.appendEventDropDown(response);
          	}
        });
    },

    getPackages: function(lang_id) {
        $.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
          	type: 'post',
          	url: baseUrl +'/admin/package/list/filter-package',
            data:{'filter_package_lang':lang_id},
          	beforeSend: function() {
               	$('#loaderimage').css("display", "block");
               	$('#loadingorverlay').css("display", "block");
          	},
          	success: function (response) {
          		appendHtml.appendPackageDropDown(response);
          	}
        });
    }
}

var appendHtml = {
	appendEventDropDown : function (events, eventId) {

      	$('#filterEvent').empty();
        eventId = document.getElementById('filterEvent');

		$.each(events.data, function(index, item) {

			var value = item['id'];
			var text = item['event_name'];
			var o = new Option(text, value);
			eventId.append(o);
        });
        $("#filterEvent").prepend("<option value='-1' selected='selected'>Select Event</option>");
    },
    appendPackageDropDown : function (packages, packageId) {
        $('#filterPackage').empty();
        packageId = document.getElementById('filterPackage');

        $.each(packages.data, function(index, item) {

            var value = item['id'];
            var text = item['package_name'];
            var o = new Option(text, value);
            packageId.append(o);
        });
        $("#filterPackage").prepend("<option value='-1' selected='selected'>Select Package</option>");
  },
}

$(document).on('click', '#allocate_photo_model', function(){
    var data_row = table.row($(this).closest('tr')).data();
    var event_enq_id = data_row.id;
    $('#allocatePhotographerModel').on('show.bs.modal', function(e){
        $('#event_enq_id').val(event_enq_id);
    });
    $('#allocatePhotographerModel').modal('show');
})

$(document).on('click','#add_alloc_photographer', function(){
    var photographer_id = $('#photographer').val();
    var event_enq_id = $('#event_enq_id').val();
    $.ajax({
        url: window.location.href + '/photographer-allocation',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            event_enq_id: event_enq_id,
            photographer_id: photographer_id,
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#allocatePhotographerModel').modal('hide')
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#allocatePhotographerModel').modal('hide')
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(response.msg);
            }
            setTimeout(function(){
                toastr.clear();
            }, 5000);
        }
    });
})

// change event enq status
$(document).on('click', '#changeEnqStatus', function(){
    var data_row = table.row($(this).closest('tr')).data();
    var event_enq_id = data_row.id;
    var status = data_row.status;
    $("#enqStatus option").each(function(){
        if($(this).val() == status)
        {
            $(this).attr("selected","selected");
        }
    });

    $('#changeEnqStatusModal').on('show.bs.modal', function(e){
        $('#event_enq_id').val(event_enq_id);
    });
    $('#changeEnqStatusModal').modal('show');
})

$(document).on('click','#changeEnqStatusBtn', function(){
    var enqStatusId = $('#enqStatus').val();
    var event_enq_id = $('#event_enq_id').val();
    $.ajax({
        url: window.location.href + '/changeEventEnqStatus',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            event_enq_id: event_enq_id,
            enqStatusId: enqStatusId,
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#changeEnqStatusModal').modal('hide')
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#changeEnqStatusModal').modal('hide')
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(response.msg);
            }
            setTimeout(function(){
                toastr.clear();
            }, 5000);
        }
    });
})

function callShowPhotosListRoute(enqId)
{
    window.location.href = baseUrl + '/admin/eventEnq/photos/getPhotos/' + enqId;
}
function callViewEnqDetailsRoute(enqId)
{
    window.location.href = baseUrl + '/admin/eventEnq/viewEnqDetails/' + enqId;
}

// update photo price

$(document).on('click', '#updatePhotoPrice', function(){
    var data_row = table.row($(this).closest('tr')).data();
    var event_enq_id = data_row.id;
    var currentPrice = data_row.price_per_photo
    
    $('#updatePhotoPriceModal').on('show.bs.modal', function(e){
        $('#event_enq_id').val(event_enq_id);
        $('#currentPhotoPrice').val(currentPrice);
        $('#price_per_photo').val(currentPrice);
        $('#total_amt').val(data_row.total_amount);
        $('#advance_payment').val(data_row.advance_payment);
        if(data_row.free_photo_download == 1)
            $('#free_photo_download').prop('checked', true);
        else
            $('#free_photo_download').prop('checked', false);
    });
    $('#updatePhotoPriceModal').modal('show');
})

$(document).on('click','#updatePhotoPriceBtn', function(){
    var event_enq_id = $('#event_enq_id').val();
    var free_photo_download = 0;
    if($('#free_photo_download').is(':checked'))
        free_photo_download = 1;

    $.ajax({
        url: baseUrl + '/admin/eventEnq/updatePhotoPrice',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            event_enq_id: event_enq_id,
            price_per_photo: $('#price_per_photo').val(),
            total_amt: $('#total_amt').val(),
            advance_payment: $('#advance_payment').val(),
            free_photo_download: free_photo_download
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#updatePhotoPriceModal').modal('hide')
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#updatePhotoPriceModal').modal('hide')
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(response.msg);
            }
            setTimeout(function(){
                toastr.clear();
            }, 5000);
        }
    });
})
