
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

    var filterData = {'orderId': $('#orderId').val(), 'orderStatus': $('#orderStatus :selected').val(),
    'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
    'paymentMethod': $('#paymentMethod').val(),
    'custGroup' : $('#custGroup :selected').val(),'shippingType' : $('input[name="shipping_type"]:checked').val(),
    }

    ajaxCall.getAllOrders(filterData);
    ajaxCall.getOrderNotes(orderId);
    ajaxCall.getOrderActivity(orderId);

    $('#FilterDiv').hide();
    $('body').on('click', '#divFilterToggle', function () {
		$("#FilterDiv").slideToggle('slow');
    });

    // startDate = endDate = "";
    // $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
    //     startDate = picker.startDate.format('YYYY-MM-DD');
    //     $('#startDate').val(startDate);
    //     endDate = picker.endDate.format('YYYY-MM-DD');
    //     $('#endDate').val(endDate);
    //     $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    // });
    // $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
    //     $(this).val('');
    // });

    /** Filter Orders */
    $('body').on('click', '#btnFilterOrders', function () {
        filterData = {'orderId': $('#orderId').val(), 'orderStatus': $('#orderStatus :selected').val(),
        'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
        'paymentMethod': $('#paymentMethod').val(),
        'startDate' :startDate, 'endDate':endDate,
        'custGroup' : $('#custGroup :selected').val(),'shippingType' : $('input[name="shipping_type"]:checked').val(),
        }
        ajaxCall.getAllOrders(filterData)
    });

    // Reset filter
    $('body').on('click','#resetFilter',function(){
        $('#filterOrderForm')[0].reset();

        filterData = {'orderId': $('#orderId').val(), 'orderStatus': $('#orderStatus :selected').val(),
        'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
        'paymentMethod': $('#paymentMethod').val(),
        'custGroup' : $('#custGroup :selected').val(),'shippingType' : $('input[name="shipping_type"]:checked').val(),
        }
        ajaxCall.getAllOrders(filterData)
    });

    // export
    $('body').on('click', '#ordersExport', function() {
        filterData = {'orderId': $('#orderId').val(), 'orderStatus': $('#orderStatus :selected').val(),
        'custName': $('#custName').val(), 'custEmail': $('#custEmail').val(),
        'paymentMethod': $('#paymentMethod').val(),
        'startDate' : $('#startDate').val(), 'endDate':$('#endDate').val(),
        'custGroup' : $('#custGroup :selected').val(),'shippingType' : $('input[name="shipping_type"]:checked').val(),
        }
        ajaxCall.exportOrders(filterData);
    });

});

var table;
var ajaxCall = {
    getAllOrders : function (filterData) {
        // console.log(filterData);
        $('#allOrdersList').DataTable().destroy();
        table = $('#allOrdersList').DataTable({

        processing: true,
        serverSide: true,
        "scrollX": true,
        "ajax": {
            'type': 'get',
            'url': baseUrl + '/admin/orders/list',
            'data': filterData
        },
        'columnDefs': [
            {
                "targets": [0,1],
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
            {
                "targets": [2],
                "visible": false
            }
        ],
        // 'order': [12, 'desc'],

        columns: [
        {
            "target": 0,
            "data":'selectBox'
        },
        {
            "target": 1,
            render: function (data, type, row) {
                var output = "";
                output += '<a href="'+ baseUrl +'/admin/orders/orderDetails/'+ row.id +'">' + row.order_id + "</a><br>";
                return output;
            },
        },
        {
            "target": 2,
            "data":'id'
        },
        {
            "target" :3,
            "data":"customer_group"
        },
        {
            "target" :4,
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
            "target":5,
            "data" :"customerEmail"
        },
        {
            "target": 6,
            render: function (data, type, row) {
                var output = "";
                output += row.b_address_line_1 + "<br>";
                output += row.b_address_line_2 + "<br>";
                output += row.b_city + ' ' + row.b_state + "-" + row.b_pincode + "," + row.b_country + "<br>";
                return output;
            },
        },
        {
            "target" : 7,
            "data" : "shipping_method"
        },
        {
            "target": 8,
            render: function (data, type, row) {
                var output = "";
                output += row.s_address_line_1 + "<br>";
                output += row.s_address_line_2 + "<br>";
                output += row.s_city + ' ' + row.s_state + " " + row.s_pincode + " " + row.s_country + "<br>";
                return output;
            },
        },
        {
            "target" : 9,
            "data" : "payment_method"
        },
        {
            "target":10,
            render: function (data, type, row) {
                output =  defaultLangSym + ' ' + row.total;
                return output;
            },
        },
        {
            "target": 11,
            render: function (data, type, row) {
                var output = "";
                if(row.slug == "delivered" || row.slug == "ready-collect")
                    output += '<b><span class="badge badge-success">' + row.status + '</span></b>';

                if(row.slug == "shipped" || row.slug == "order-received" || row.slug == "ready-to-dispatch" ||row.slug == "in-transit" ||row.slug == "packed")
                    output += '<b><span class="badge badge-warning">' + row.status + '</span></b>';

                if(row.slug == "pending" || row.slug == "cancelled"|| row.slug == "payment-failed" ||row.slug == "awaiting-payment-confirmation" )
                    output += '<b><span class="badge badge-danger">' + row.status + '</span></b>';

                if(row.slug == "order-under-process" || row.slug == "out-for-delivery")
                    output += '<b><span class="badge badge-orange">' + row.status + '</span></b>';
                return output;
            },
        },
        {
            "target": 12,
            "data":'orderCreateddate'
        },
        {
            "target": -1,
            "bSortable": false,
            "order":false,
            "data":'action',
            render: function (data, type, row) {
                // console.log(row)
                var output = "";
                output += '<a href="'+ baseUrl +'/admin/orders/orderDetails/'+ row.id +'"><i class="fa fa-eye" aria-hidden="true" title="View"></i></a>&nbsp; &nbsp;'
                output += '<a href="'+ baseUrl +'/admin/orders/printOrder/'+ row.id +'" target="_blank"><i class="fa fa-print" aria-hidden="true" title="Print"></i></a>'
                if(row.slug == "order-received" || row.slug == "shipped" || row.slug == "delivered")
                {
                    output += '<div class="d-inline-block dropdown">'
                    output += '<button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn-shadow dropdown-toggle btn btn-primary">'
                    output += '<span class="btn-icon-wrapper pr-2 opacity-7"><i class="pe-7s-settings fa-w-20"></i></span></button>'
                    output += '<div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">'
                    output += '<ul class="nav flex-column">'
                    if(row.slug == "order-received")
                    {
                        output += '<li class="nav-item"><a class="nav-link" onclick="openMarkAsCancelledModal('+row.id+');" >Mark As Cancelled</a></li>'
                        output += '<li class="nav-item"><a class="nav-link orderMarkedShip" data-order_id="'+row.id+'">Mark as Shipped (Aramex)</a></li>'
                        output += '<li class="nav-item"><a class="nav-link"  onclick="openMarkAsShippedModal('+row.id+');" data-order_id="'+row.id+'">Mark as Shipped</a></li>'
                    }
                    if(row.slug == "shipped")
                    {
                        output += '<li class="nav-item"><a class="nav-link" onclick="openMarkAsDeliveredModal('+row.id+');" >Mark as Delivered</a></li>'
                        output += '<li class="nav-item"><a style="color: inherit;" class="nav-link" id="changeEnqStatus" href="'+row.label_url+'" target="_blank">Print Label</a></li>'
                    }
                    if(row.slug == "delivered")
                    {
                        output += '<li class="nav-item"><a style="color: inherit;" class="nav-link" id="changeEnqStatus" href="'+row.label_url+'" target="_blank">Print Label</a></li>'
                    }
                }

                output += '</div></div>'
                return output;
            },
        }
    ]
    })

    },
    getOrderNotes : function (orderId) {
        $('#tableAllOrderNotes').DataTable().destroy();
        table = $('#tableAllOrderNotes').DataTable({

          processing: true,
          serverSide: true,
          "ajax": {
              'type': 'get',
              'url': baseUrl + '/admin/orders/notes',
              'data': {'orderId':orderId}
          },
          "columnDefs": [
            { "width": '5px', "targets": 0 },
            { "width":'80', "targets": 1 },
            { "width": '10', "targets": 2 },
            { "width": '5', "targets": 3 }
          ],
          columns: [
          {
              "target": 0,
              "data":'rownum'
          },
          {
              "target": 1,
              "data":'notes'
          },
          {
              "target": 2,
              "data":'createdby'
          },
          {"data": 'createdat',"target": 3,
              render: function (data,type,row) {
                  if(row.user_zone != null)
                  {
                      var z = row.user_zone;
                      return moment.utc(row.createdat).utcOffset(z.replace('.', "")).format('YYYY-MM-DD HH:mm:ss')
                  }
                  else
                  {
                      return "-----"
                  }

              }
          },
          // {
          //     "target": 3,
          //     "data":'createdat'
          // }
        ]
      })

      },

    exportOrders: function (filterData) {
        $.ajax({
            type: "get",
            url: baseUrl + '/admin/orders/export',
            'data': filterData,
            success: function (response)
            {
                window.location.href = response;
            }
        });
    },

    getOrderActivity : function (orderId) {
        $('#tableOrderActivity').DataTable().destroy();
        table = $('#tableOrderActivity').DataTable({

          processing: true,
          serverSide: true,
          "ajax": {
              'type': 'get',
              'url': baseUrl + '/admin/orders/activities',
              'data': {'orderId':orderId}
          },
          "columnDefs": [
            { "width": '5px', "targets": 0 },
            { "width":'80', "targets": 1 },
            { "width": '10', "targets": 2 },
            { "width": '5', "targets": 3 },
          ],
          columns: [
          {
              "target": 0,
              "data":'rownum'
          },
          {
              "target": 1,
              "data":'activity'
          },
          {
              "target": 2,
              "data":'createdby'
          },
          {"data": 'createdat',"target": 3,
              render: function (data,type,row) {
                  if(row.user_zone != null)
                  {
                      var z = row.user_zone;
                      return moment.utc(row.createdat).utcOffset(z.replace('.', "")).format('YYYY-MM-DD HH:mm:ss')
                  }
                  else
                  {
                      return "-----"
                  }

              }
          },
        ]
      })

      },
};

// Select all checkbox at once
$('#selectAllOrders').on('click', function(){
  var checked = $(this).is(':checked'); // Checkbox state

    // Select all
    if(checked){
         $("input[type=checkbox]").prop('checked', true);
    }else{
        $("input[type=checkbox]").prop('checked', false);
    }
    // var rows = table.rows({ 'search': 'applied' }).nodes();
    // $('input[type="checkbox"]', rows).prop('checked', this.checked);
});

// if any checkbox is unchecked
$('#allOrdersList tbody').on('change', 'input[type="checkbox"]', function(){
    console.log(this)
    if(!this.checked)
    {
       var el = $('#selectAllOrders').get(0);
       if(el && el.checked && ('indeterminate' in el))
       {
          el.indeterminate = true;
       }
    }
});


// order mark as cancelled
function openMarkAsCancelledModal(id)
{
    $('#cancelOrderId').val(id);
    $('#orderMarkAsCancelled').modal('show');
}
$(document).on('click','#cancelOrderBtn', function(){
    var cancelOrderId = $('#cancelOrderId').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: baseUrl + '/admin/orders/markOrderAsCancelled',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            "orderId": cancelOrderId,
            "ajaxReq" : "yes"
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#orderMarkAsCancelled').modal('hide')
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#orderMarkAsCancelled').modal('hide')
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


// order mark as Delivered
function openMarkAsDeliveredModal(id)
{
    $('#deliveredOrderId').val(id);
    $('#orderMarkAsDelivered').modal('show');
}
$(document).on('click','#deliveredOrderBtn', function(){
    var deliveredOrderId = $('#deliveredOrderId').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: baseUrl + '/admin/orders/markOrderAsDelivered',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            "orderId": deliveredOrderId,
            "ajaxReq" : "yes"
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#orderMarkAsDelivered').modal('hide')
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#orderMarkAsDelivered').modal('hide')
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

 $(document).on('click','#cancelBtn', function(){
    //var selectedVal = $('#bulkActionDropdown :selected').val();
    var url;
    var checkedValues = [];
    url = baseUrl + '/admin/orders/cancelBulkOrders';

    // if(selectedVal == 'cancelOrder')
    //     url = baseUrl + '/admin/orders/cancelBulkOrders'
    //
    // if(selectedVal == 'printOrder')
    //     url = baseUrl + '/admin/orders/printBulkOrders'

    $('#allOrdersList').find('input[type="checkbox"]:checked').each(function ()
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

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: url,
        method: "POST",
        data: {
            "_token": $('#token').val(),
            "checkedValues": checkedValues,
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
            else
            {
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(response.msg);
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        }
    });
})
// To print bulk orders Nivedita April 15-04-2021
$(document).on('click','.printBtn', function(){
   var url;
   var checkedValues = [];
   $('#allOrdersList').find('input[type="checkbox"]:checked').each(function ()
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
      toastr.error('Please select at least one order.');
      return false;
    }
})

// To print bulk orders Nivedita April 15-04-2021
$(document).on('click','#generateBtn', function(){
   //var selectedVal = $('#bulkActionDropdown :selected').val();
   var url;
   var checkedValues = [];
   url = baseUrl + '/admin/orders/generateBulkOrderInvoice';

   // if(selectedVal == 'cancelOrder')
   //     url = baseUrl + '/admin/orders/cancelBulkOrders'
   //
   // if(selectedVal == 'printOrder')
   //     url = baseUrl + '/admin/orders/printBulkOrders'

   $('#allOrdersList').find('input[type="checkbox"]:checked').each(function ()
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
   $.ajaxSetup({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });
   $.ajax({
       url: url,
       method: "POST",
       data: {
           "_token": $('#token').val(),
           "checkedValues": checkedValues,
       },
       success: function(response)
       {
           if(response.status == 'true')
           {
               table.ajax.reload();
               toastr.clear();
               toastr.options.closeButton = true;
               toastr.options.timeOut = 0;
               toastr.success(response.msg);
               setTimeout(function() {
                   location.reload();
               }, 2000);
           }
           else
           {
               toastr.clear();
               toastr.options.closeButton = true;
               toastr.options.timeOut = 0;
               toastr.error(response.msg);
               setTimeout(function() {
                   location.reload();
               }, 2000);
           }
       }
   });
})

$("#updateBillingAddressForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "b_fullname": {
            required: true,
        },
        "b_address_line_1": {
            required: true,
        },
        "b_country": {
            required: true,
        },
        "b_state": {
            required: true,
        },
        "b_city": {
            required: true,
        },
        "b_pincode": {
            required: true,
        },
        "b_address_type": {
            required: true,
        },
        "b_phone1": {
            required: true,
        }
    },
    messages: {
        "b_fullname": {
            required: "Please enter full name"
        },
        "b_address_line_1": {
            required: "Please enter address line 1",
        },
        "b_country" :{
            required: "Please enter country",
        },
        "b_state" :{
            required: "Please enter state"
        },
        "b_city" :{
            required: "Please enter city"
        },
        "b_pincode":{
            required: "Please enter pincode",
        },
        "b_address_type" :{
            required: "Please select address type",
        },
        "b_phone1" :{
            required: "Please enter phone 1"
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter(element)
    },
    submitHandler: function(form)
    {
        var formData = $('#updateBillingAddressForm').serialize();

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: "post",
            url: baseUrl + '/admin/orders/updateBillingAddress',
            data:formData ,
            success: function (response)
            {
                if(response.status == true)
                {
                    $('#editBillingAddressModal').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#editBillingAddressModal').modal('hide')
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
    }
});

$("#updateShippingAddressForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "s_fullname": {
            required: true,
        },
        "s_address_line_1": {
            required: true,
        },
        "s_country": {
            required: true,
        },
        "s_state": {
            required: true,
        },
        "s_city": {
            required: true,
        },
        "s_pincode": {
            required: true,
        },
        "s_address_type": {
            required: true,
        },
        "s_phone1": {
            required: true,
        }
    },
    messages: {
        "s_fullname": {
            required: "Please enter full name"
        },
        "s_address_line_1": {
            required: "Please enter address line 1",
        },
        "s_country" :{
            required: "Please enter country",
        },
        "s_state" :{
            required: "Please enter state"
        },
        "s_city" :{
            required: "Please enter city"
        },
        "s_pincode":{
            required: "Please enter pincode",
        },
        "s_address_type" :{
            required: "Please select address type",
        },
        "s_phone1" :{
            required: "Please enter phone 1"
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter(element)
    },
    submitHandler: function(form)
    {
        var formData = $('#updateShippingAddressForm').serialize();

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: "post",
            url: baseUrl + '/admin/orders/updateShippingAddress',
            data:formData ,
            success: function (response)
            {
                if(response.status == true)
                {
                    $('#editShippingAddressModal').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                }
                else
                {
                    $('#editShippingAddressModal').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.error(response.msg);
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                }
                setTimeout(function(){
                    toastr.clear();
                }, 5000);
            }
        });
    }
});

$('#product_group').on('change', function(){
    var obj_val = $(this).val();
    var output = '';
    if(obj_val == 'EXP')
    {
        output += '<div class="form-group"></div>';
        output += '<label for="contact_name" class="font-weight-bold">Product Type</label>';
        output += '<select name="product_type" id="product_type" class="form-control">';
        output += '<option value="PDK">Priority Document Express</option>';
        output += '<option value="PPX">Priority Parcel Express</option>';
        output += '<option value="PLX">Priority Letter Express</option>';
        output += '<option value="DDX">Deferred Document Express</option>';
        output += '<option value="DPX">Deferred Parcel Express</option>';
        output += '<option value="GDX">Ground Document Express</option>';
        output += '<option value="GPX">Ground Parcel Express</option>';
        output += '<option value="EPX">Economy Parcel Express</option>';
        output += '</select>';
        output += '</div>';
        $('#product_type_section').html(output);
    }
    else if(obj_val == 'DOM')
    {
        output += '<div class="form-group"></div>';
        output += '<label for="contact_name" class="font-weight-bold">Product Type</label>';
        output += '<select name="product_type" id="product_type" class="form-control">';
        output += '<option value="OND">Overnight Document</option>';
        output += '</select>';
        output += '</div>';
        $('#product_type_section').html(output);
    }
    else
    {
        $('#product_type_section').html('');
    }
})

$('#btnMarkAsShipped').on('click', function(){
    var order_id = $(this).attr('data-order_id');
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
        url : baseUrl + '/admin/get-shipping-order',
        method: 'POST',
        data: {
            order_id : order_id,
        },
        success: function(response){
            if(response.status == 'true')
            {
                $('#aramexShippingModel').on('show.bs.modal', function(e){
                    $('#contact_name').val(response.aramex_config.contact_name);
                    $('#company_name').val(response.aramex_config.company_name);
                    $('#line_1').val(response.aramex_config.line_1);
                    $('#line_2').val(response.aramex_config.line_2);
                    $('#city').val(response.aramex_config.city);
                    $('#country_code').val(response.aramex_config.country_code);
                    $('#phone_ext').val(response.aramex_config.phone_extension);
                    $('#phone_number').val(response.aramex_config.phone);
                    $('#email').val(response.aramex_config.email);
                    $('#consignee_address_1').val(response.orders.s_address_line_1);
                    $('#consignee_address_2').val(response.orders.s_address_line_2);
                    $('#consignee_city').val(response.orders.s_city);
                    $('#consignee_phone_number').val(response.orders.s_phone1);
                    $('#consignee_email').val(response.orders.email);
                    $('#order_id').val(response.orders.order_id);
                    $('#order_primary_id').val(response.orders.id);
                    $('#order_first_name').val(response.orders.first_name);
                    $('#order_last_name').val(response.orders.last_name);
                })
                $('#aramexShippingModel').modal('show');
            }
        }
    })

})

$("#aramexShippingDetailsForm").validate({
    rules: {
        contact_name : {
            required: true,
        },
        company_name : {
            required: true,
        },
        line_1 : {
            required: true,
        },
        line_2 : {
            required: true,
        },
        city : {
            required: true,
        },
        country_code : {
            required: true,
        },
        phone_ext : {
            required: true,
        },
        phone_number : {
            required: true,
        },
        email : {
            required: true,
            email: true,
        },
        consignee_address_1 : {
            required: true,
        },
        consignee_city : {
            required: true,
        },
        consignee_country : {
            required: true,
        },
        consignee_phone_ext : {
            required: true,
        },
        consignee_phone_number : {
            required: true,
        },
        consignee_email : {
            required: true,
            email: true,
        },
        actual_weight : {
            required: true,
        },
        no_of_pieces : {
            required: true
        },
        goods_desc : {
            required: true,
        },
        goods_origin_country : {
            required: true,
        },
        product_group : {
            required: true,
        }
    },
    messages: {
        contact_name : {
            required: "Please enter contact name",
        },
        company_name : {
            required: "Please enter company name",
        },
        line_1 : {
            required: "Please enter line 1",
        },
        line_2 : {
            required: "Please enter line 2",
        },
        city : {
            required: "Please enter city",
        },
        country_code : {
            required: "Please enter country code",
        },
        phone_ext : {
            required: "Please enter phone extension",
        },
        phone_number : {
            required: "Please enter phone number",
        },
        email : {
            required: "Please enter email",
            email: "Please enter a valid email address"
        },
        consignee_address_1 : {
            required: "Please enter address 1",
        },
        consignee_city : {
            required: "Please enter city",
        },
        consignee_country : {
            required: "Please enter country",
        },
        consignee_phone_ext : {
            required: "Please enter phone extension",
        },
        consignee_phone_number : {
            required: "Please enter phone number",
        },
        consignee_email : {
            required: "Please enter email",
            email: "Please enter a valid email address"
        },
        actual_weight : {
            required: "Please enter actaul weight",
        },
        no_of_pieces : {
            required: "Please enter number of pieces",
        },
        goods_desc : {
            required: "Please enter goods description",
        },
        goods_origin_country : {
            required: "Please enter country origine goods",
        },
        product_group : {
            required: "Please select product group",
        }
    },
    errorPlacement: function ( error, element ) {
        // Add the `invalid-feedback` class to the error element
        if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.next( "label" ) );
        } else {
            error.insertAfter( element );
        }
    },
});

$('#save_aramex_details_btn').on('click', function(){
    if($("#aramexShippingDetailsForm").valid())
    {
        var form_data = $('#aramexShippingDetailsForm').serialize();
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
            url : baseUrl + '/admin/create-aramex-shipping',
            method: 'POST',
            data: form_data,
            beforeSend:function () {
                $("#ajax-loader").fadeIn();
            },
            success: function(response){
                if(response.status == 'true')
                {
                    $('#aramexShippingModel').modal('hide');
                    $("#ajax-loader").fadeIn();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                    setTimeout(function() {
                        toastr.clear();
                    }, 2000);
                    setTimeout(function(){
                        $("#ajax-loader").fadeOut();

                        location.reload();
                    }, 2000);
                }
                else
                {
                    var msg = '';
                    if(response.obj_count == 'multiple')
                    {
                        msg += '<ul>';
                        $.each(response.error, function (i,v) {
                            msg += '<li>'+v.Code + ' : ' + v.Message+'</li>';
                        });
                        msg += '</ul>';
                    }
                    else
                    {
                        msg += '<ul>';
                        msg += '<li>'+response.error.Code + ' : ' + response.error.Message+'</li>';
                        msg += '</ul>';
                    }
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.error(msg);
                    setTimeout(function() {
                        toastr.clear();
                    }, 2000);
                    setTimeout(function(){
                        $("#ajax-loader").fadeOut();
                    }, 2000);
                }
            }
        })
    }
})

$('#saveAddNotes').click(function() {
    var notes = $("textarea#notes").val();
    var trimStr = $.trim(notes);
    if( trimStr.length > 0)
    {
        $('#desc_error').css('display','none','!important');
        var form_data = $('#addNotesForm').serialize();
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
            url : baseUrl + '/admin/orders/addNotes',
            method: 'POST',
            data: form_data,
            beforeSend:function () {
                $("#ajax-loader").fadeIn();
            },
            success: function(response){
                if(response)
                {
                    $('#addNotesModal').modal('hide');
                    $("#ajax-loader").fadeOut();
                        table.ajax.reload();
                        toastr.clear();
                        toastr.options.closeButton = true;
                        toastr.options.timeOut = 0;
                        toastr.success(response.msg);

                    setTimeout(function(){
                        toastr.clear();
                    }, 2000);
                }

            }
        })
    }
    else
    {
        $("#desc_error").html('Please enter notes');
        $("#desc_error").css('color', 'red');
    }
    return false;
});

$(document).on('click', '.orderMarkedShip',function(){
    var order_id = $(this).attr('data-order_id');
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
        url : baseUrl + '/admin/get-shipping-order',
        method: 'POST',
        data: {
            order_id : order_id,
        },
        success: function(response){
            if(response.status == 'true')
            {
                $('#aramexShippingModel').on('show.bs.modal', function(e){
                    $('#contact_name').val(response.aramex_config.contact_name);
                    $('#company_name').val(response.aramex_config.company_name);
                    $('#line_1').val(response.aramex_config.line_1);
                    $('#line_2').val(response.aramex_config.line_2);
                    $('#city').val(response.aramex_config.city);
                    $('#country_code').val(response.aramex_config.country_code);
                    $('#phone_ext').val(response.aramex_config.phone_extension);
                    $('#phone_number').val(response.aramex_config.phone);
                    $('#email').val(response.aramex_config.email);
                    $('#consignee_address_1').val(response.orders.s_address_line_1);
                    $('#consignee_address_2').val(response.orders.s_address_line_2);
                    $('#consignee_city').val(response.orders.s_city);
                    $('#consignee_phone_number').val(response.orders.s_phone1);
                    $('#consignee_email').val(response.orders.email);
                    $('#order_id').val(response.orders.order_id);
                    $('#order_primary_id').val(response.orders.id);
                    $('#order_first_name').val(response.orders.first_name);
                    $('#order_last_name').val(response.orders.last_name);
                })
                $('#aramexShippingModel').modal('show');
            }
        }
    })
})

// mark order as shipped without aramex
function openMarkAsShippedModal(id)
{
    $('#shippedOrderId').val(id);
    $('#markOrderAsShipped').modal('show');
}

$(document).on('click','#markOrderAsShippedBtn', function(){
    var shippedOrderId = $('#shippedOrderId').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: baseUrl + '/admin/orders/markOrderAsShipped',
        method: "POST",
        data: {
            "_token": $('#token').val(),
            "orderId": shippedOrderId,
            "trackingNumber" : $('#trackingNumber').val(),
            "carrierName" : $('#carrierName').val(),
            "ajaxReq" : "yes"
        },
        success: function(response)
        {
            if(response.status == 'true')
            {
                $('#markOrderAsShipped').modal('hide')
                table.ajax.reload();
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);
            }
            else
            {
                $('#markOrderAsShipped').modal('hide')
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


// download image zip file
function downloadImages(orderId)
{
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
        url : baseUrl + '/admin/orders/downloadOrderProdImages/' + orderId,
        method: 'GET',
        success: function(response)
        {
            console.log(response);
            response.urls.forEach(function(url)
            {
                var filename = "filename";
                // loading a file and add it in a zip file
                JSZipUtils.getBinaryContent(url, function (err, data)
                {
                    if(err)
                    {
                        // console.log(err);
                        throw err; // or /handle the error
                    }
                   zip.file(filename, data, {binary:true});
                   count++;
                   if (count == urls.length)
                   {
                        var zipFile = zip.generate({type: "blob"});
                        saveAs(zipFile, zipFilename);
                   }
                });
            });
        }
    })
}

$("#updateLoyaltyNumberForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "loyaltyNumber": {
            required: true,
        }
    },
    messages: {
        "loyaltyNumber": {
            required: "Please enter full name"
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter(element)
    },
    submitHandler: function(form)
    {
        var formData = $('#updateLoyaltyNumberForm').serialize();

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: "post",
            url: baseUrl + '/admin/orders/updateLoyaltyNumber',
            data:formData ,
            success: function (response)
            {
                if(response.status == true)
                {
                    $('#editLoyaltyNumberModal').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                    $('#loyaltyNumberNew').html(response.loyaltynumber)
                }
                else
                {
                    $('#editLoyaltyNumberModal').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.error(response.msg);
                }
                setTimeout(function(){
                    toastr.clear();
                    // location.reload();
                }, 5000);
            }
        });
    }
});
$("#changeStatusForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "orderStatus": {
            required: true,
        }
    },
    messages: {
        "orderStatus": {
            required: "Please select order status"
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter(element)
    },
    submitHandler: function(form)
    {
        var formData = $('#changeStatusForm').serialize();

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: "post",
            url: baseUrl + '/admin/orders/changeStatus',
            data:formData ,
            success: function (response)
            {
                if(response.status == true)
                {
                    $('#changeStatusModel').modal('hide');
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#changeStatusModel').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.error(response.msg);
                }
                setTimeout(function(){
                    toastr.clear();
                     location.reload();
                }, 3000);
            }
        });
    }
});
