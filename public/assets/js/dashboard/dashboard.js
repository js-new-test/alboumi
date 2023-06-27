$('#from_date').datepicker({
    "format": "yyyy-mm-dd",
    "autoclose": true,
    "orientation": "top",
    "endDate": "today"
});

$('#to_date').datepicker({
    "format": "yyyy-mm-dd",
    "autoclose": true,
    "orientation": "top",
    "endDate": "today"
});

$("#filterDashboardForm").validate({
    rules: {         
        from_date : {
            required: true,
        },
        to_date : {
            required: true,
            // greaterThan: "#start_date"
        },                                                    
    },
    messages: {  
        from_date : {
            required: 'From date is required',
        },
        to_date : {
            required: 'To date is required',
            // greaterThan: "Start date should be less then to end date"
        },                                                                       
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

var baseUrl = $('#baseUrl').val();
$(document).on('click','#filter_dashboard_count', function(){
    $("#filterDashboardForm").valid();
    var token = $('input[name="_token"]').val();
    var from_date = $('#from_date').val();    
    var to_date = $('#to_date').val();
    if(Date.parse(from_date) > Date.parse(to_date)){
        $('#from_date_error').html("<p>From date should be less then to To date</p>");
     }
     else{
        $('#from_date_error').html("");
     }        
    $.ajax({
        url: baseUrl + '/admin/dashboard-filter',
        method: "POST",
        data:{
            _token : token,
            from_date:from_date,
            to_date:to_date,
        },
        success:function(response){
            if(response.status == 'true')
            {
                $('#pending_orders').text(response.pending_orders);
                $('#total_orders').text(response.total_orders);
                $('#total_sales').text(response.total_sales);
                $('#total_enquiry').text(response.total_enquiry);
            }
        }
    });               
})