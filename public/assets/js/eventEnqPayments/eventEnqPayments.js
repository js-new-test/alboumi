$(document).ready(function(){

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

    var filterData = {'orderId': $('#orderId').val(),'paymentId' : $('#paymentId').val(),
    'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
    'paymentStatus': $('#paymentStatus :selected').val(),
    'paymentType' : $('#paymentType :selected').val(),
    "startDate" : startDate, 'endDate' : endDate
    }

    ajaxCall.getAllEventEnqPayments(filterData);

    $('#FilterDiv').hide();
    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterDiv").slideToggle('slow');
    });


    /** Filter Orders */
    $('body').on('click', '#btnFilterEventPhotoSales', function () {

        filterData = {'orderId': $('#orderId').val(),'paymentId' : $('#paymentId').val(),
            'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
            'paymentStatus': $('#paymentStatus :selected').val(),
            'paymentType' : $('#paymentType :selected').val(),"startDate" : startDate, 'endDate' : endDate
        }
        ajaxCall.getAllEventEnqPayments(filterData)
    });

    // Reset filter
    $('body').on('click','#resetFilter',function(){
        $('#filterEventPhotoSalesForm')[0].reset();
    
        filterData = {'orderId': $('#orderId').val(),'paymentId' : $('#paymentId').val(),
            'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
            'paymentStatus': $('#paymentStatus :selected').val(),
            'paymentType' : $('#paymentType :selected').val(),"startDate" : startDate, 'endDate' : endDate
        }
        ajaxCall.getAllEventEnqPayments(filterData)
    });
});

var table;
var ajaxCall = {
    getAllEventEnqPayments : function (filterData) {
        $('#allEventEnqPayments').DataTable().destroy();
        table = $('#allEventEnqPayments').DataTable({

        processing: true,
        serverSide: true,
        "scrollX": true,
        "ajax": {
            'type': 'get',
            'url': baseUrl + '/admin/eventEnqPayment/list',
            'data': filterData
        },
        'columnDefs': [
            {
                "targets": [0,1],
                "className": "text-center",
            },
            {
                "targets": [1],
                "visible": false
            }
        ],
        'order': [[8, 'desc']],

        columns: [
        {
            "target": 1,
            "data" : "order_id"
        },
        {
            "target": 2,
            "data":'id'
        },
        {
            "target" :3,
            "data" :"full_name"
        },
        {
            "target":4,
            "data" :"email"
        },
        {
            "target" : 5,
            render: function (data, type, row) {
                var output = "";
                if(row.payment_type == 1)
                    output += 'Credit Card';

                if(row.payment_type == 2)
                    output += 'Debit Card';

                return output;
            },
        },
        {
            "target": 6,
            render: function (data, type, row) {
                var output = "";
                if(row.payment_status == 1)
                    output += '<b><span class="badge badge-success">Success</span></b>';

                if(row.payment_status == 0)
                    output += '<b><span class="badge badge-warning">Pending</span></b>';

                if(row.payment_status == 2)
                    output += '<b><span class="badge badge-danger">Failed</span></b>';

                return output;
            },
        },
        {
            "target": 7,
            "data" :"amount"
        },
        {
            "target": 8,
            "data":'payment_id'
        },
        {
            "target": 9,
            "data":"created_at"
        },
    ]
    })

    },
};
