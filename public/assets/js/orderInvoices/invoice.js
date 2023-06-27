$(document).ready(function(){

    var filterData = {'invoiceId': $('#invoiceId').val(), 'orderId': $('#orderId').val(),
        'invoiceStatus': $('#invoiceStatus :selected').val(),
        'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
    }
    ajaxCall.getAllOrderInvoices(filterData);

    $('#FilterDiv').hide();
    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterDiv").slideToggle('slow');
    });

    // invoice date filter
    invoiceStartDate = invoiceEndDate = "";
    $('input[name="invoiceDaterange"]').on('apply.daterangepicker', function(ev, picker) {
        invoiceStartDate = picker.startDate.format('YYYY-MM-DD');
        $('#invoiceStartDate').val(invoiceStartDate);
        invoiceEndDate = picker.endDate.format('YYYY-MM-DD');
        $('#invoiceEndDate').val(invoiceEndDate);
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
    $('input[name="invoiceDaterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // order date filter
    orderStartDate = orderEndDate = "";
    $('input[name="orderDaterange"]').on('apply.daterangepicker', function(ev, picker) {
        orderStartDate = picker.startDate.format('YYYY-MM-DD');
        $('#orderStartDate').val(orderStartDate);
        orderEndDate = picker.endDate.format('YYYY-MM-DD');
        $('#orderEndDate').val(orderEndDate);
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
    $('input[name="orderDaterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });


    /** Filter Orders */
    $('body').on('click', '#btnFilterInvoices', function () {
        filterData = {'invoiceId': $('#invoiceId').val(), 'orderId': $('#orderId').val(),
            'invoiceStatus': $('#invoiceStatus :selected').val(),
            'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
            'invoiceStartDate' : $('#invoiceStartDate').val(), 'invoiceEndDate':$('#invoiceEndDate').val(),
            'orderStartDate' : $('#orderStartDate').val(), 'orderEndDate':$('#orderEndDate').val(),
        }
        ajaxCall.getAllOrderInvoices(filterData)
    });

    // Reset filter
    $('body').on('click','#resetFilter',function(){
        $('#filterOrderInvoicesForm')[0].reset();

        filterData = {'invoiceId': $('#invoiceId').val(),  'orderId': $('#orderId').val(),
        'invoiceStatus': $('#invoiceStatus :selected').val(),
        'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
        }
        ajaxCall.getAllOrders(filterData)
    });

});

var table;
var ajaxCall = {
    getAllOrderInvoices : function (filterData) {
        //console.log(filterData);
        $('#allInvoicesList').DataTable().destroy();
        table = $('#allInvoicesList').DataTable({

        processing: true,
        serverSide: true,  
        "initComplete": function (settings, json) {  
            $("#allInvoicesList").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },      
        "ajax": {
            'type': 'get',
            'url': baseUrl + '/admin/invoices/list',
            'data': filterData
        },
        'columnDefs': [
            {
                "targets": [0],
                "className": "text-center",
            },
            {
                'targets': 0,
                'searchable':false,
                'orderable':false,
                'className': 'dt-body-center',
                'render': function (data, type,row){
                    return '<input type="checkbox" name="id[]" value="'
                       + $('<div/>').text(row.id).html() + '">';
                    // return '<div class="custom-checkbox custom-control"><input type="checkbox" class="custom-control-input" name="id'+row.id+'[]" value="'
                    // + $('<div/>').text(row.id).html() + '"><label class="custom-control-label" for="id'+row.id+'[]"></label>'
                }
            },
            // {
            //     "targets": [2],
            //     "visible": false
            // }
        ],
        'order': [[8, 'desc']],
        columns: [
        {
            "target": 0,
            "data":'selectBox'
        },
        {
            "target": 1,
            "data":'action',
            render: function (data, type, row) {
                var output = "";
                output += '<a href="'+ baseUrl +'/admin/invoices/invoiceDetails/'+ row.id +'">'+row.invoice_id +'</a>';
                return output;
            },
        },
        {
            "target" :2,
            "data":'action',
            render: function (data, type, row) {
                var output = "";
                output += '<a href="'+ baseUrl +'/admin/orders/orderDetails/'+ row.orderId +'">'+row.order_id +'</a>';
                return output;
            },
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
            "data" :"customerEmail"
        },

        {
            "target":5,
            render: function (data, type, row) {
                output =  defaultLangSym + ' ' + row.total;
                return output;
            },
        },
        {
            "target": 6,
            render: function (data, type, row) {
                var output = "";
                if(row.status == 1)
                    output += "<span class='badge badge-success'>Paid</span>";

                if(row.status == 2)
                    output += "<span class='badge badge-danger'>Unpaid</span>";

                if(row.status == 3)
                    output += "<span class='badge badge-warning'>Cancelled</span>";

                return output;
            },
        },
        
        {"target": 7,"data": 'invoiceCreatedDate',
            "render": function (data,type,row) {
                if(row.user_zone != null)
                {
                    var z = row.user_zone;
                    return moment.utc(row.invoiceCreatedDate).utcOffset(z.replace('.', "")).format('YYYY-MM-DD HH:mm:ss')
                }
                else
                {
                    return "-----"
                }

            }
        },
        {
            "target": -1,
            "bSortable": false,
            "order":false,
            "data":'action',
            render: function (data, type, row) {
                var output = "";
                output += '<a href="'+ baseUrl +'/admin/invoices/invoiceDetails/'+ row.id +'"><i class="fa fa-eye" aria-hidden="true" title="View"></i></a>&nbsp; &nbsp;'
                output += '<a href="'+ baseUrl +'/admin/invoices/printInvoice/'+ row.id +'" target="_blank"><i class="fa fa-print" aria-hidden="true" title="Print"></i></a>'
                return output;
            },
        }
    ]
    })

    },

};

$('#selectAllInv').on('click', function(){
    var rows = table.rows({ 'search': 'applied' }).nodes();
    $('input[type="checkbox"]', rows).prop('checked', this.checked);
});
var checkedValues = [];
$(document).on('click','.printBtn', function(){
    table.$('input[type="checkbox"]').each(function()
    {
        // if(!$.contains(document, this))
        // {
            // console.log('in if')
            if(this.checked)
            {
                checkedValues.push(this.value)
            }
        // }
    });
    if(checkedValues.length==0){
      toastr.error('please select invoices');
      return false;
    }
});
