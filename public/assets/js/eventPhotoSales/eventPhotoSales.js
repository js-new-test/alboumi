
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

    var filterData = {'orderId': $('#orderId').val(),'startDate' : startDate, 'endDate':endDate,
    'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
    'status': $('#status :selected').val(),
    'paymentType' : $('#paymentType :selected').val()
    }

    ajaxCall.getAllEventPhotoSales(filterData);

    $('#FilterDiv').hide();
    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterDiv").slideToggle('slow');
    });


    /** Filter Orders */
    $('body').on('click', '#btnFilterEventPhotoSales', function () {
        // startDate = $('#daterange').data('daterangepicker').startDate;
        // endDate = $('#daterange').data('daterangepicker').endDate;

        // startDate = startDate.format('YYYY-MM-DD');
        // endDate = endDate.format('YYYY-MM-DD');

        filterData = {'orderId': $('#orderId').val(),'startDate' : startDate, 'endDate':endDate,
            'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
            'status': $('#status :selected').val(),
            'paymentType' : $('#paymentType :selected').val()
        }
        ajaxCall.getAllEventPhotoSales(filterData)
    });

    // Reset filter
    $('body').on('click','#resetFilter',function(){
        $('#filterEventPhotoSalesForm')[0].reset();
        startDate = " ";
        endDate = " ";
    
        filterData = {'orderId': $('#orderId').val(),'startDate' : startDate, 'endDate':endDate,
            'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
            'status': $('#status :selected').val(),
            'paymentType' : $('#paymentType :selected').val()
        }
        ajaxCall.getAllEventPhotoSales(filterData)
    });
});

var table;
var ajaxCall = {
    getAllEventPhotoSales : function (filterData) {
        console.log(filterData);
        $('#allEventPhotoSales').DataTable().destroy();
        table = $('#allEventPhotoSales').DataTable({

        processing: true,
        serverSide: true,
        "scrollX": true,
        "ajax": {
            'type': 'get',
            'url': baseUrl + '/admin/eventPhotoSales/list',
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
            render: function (data, type, row) {
                var output = "";
                if(row.first_name != null)
                    output += row.first_name + ' ';
                if(row.last_name != null)
                    output += row.last_name;
                return output;
            },
        },
        {
            "target":4,
            "data" :"email"
        },
        {
            "target": 5,
            "data" :"total"
        },
        {
            "target" : 6,
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
            "target": 7,
            render: function (data, type, row) {
                var output = "";
                if(row.status == 1)
                    output += '<b><span class="badge badge-success">Success</span></b>';

                if(row.status == 0)
                    output += '<b><span class="badge badge-warning">Pending</span></b>';

                if(row.status == 2)
                    output += '<b><span class="badge badge-danger">Failed</span></b>';

                return output;
            },
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
