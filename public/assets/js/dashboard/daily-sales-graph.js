$(document).ready(function() {

    displayDailySalesGraph();
})

var baseUrl = $('#baseUrl').val();
function displayDailySalesGraph()
{
    $.ajax({
        url: baseUrl + '/admin/daily-sales-graph',
        method: 'GET',
        success: function(response){
            if(response.status == 'true')
            {            
                var options777 = {
                    chart: {
                        height: 397,
                        type: 'line',
                        toolbar: {
                            show: false,
                        }
                    },
                    series: [{
                        name: 'Total Sales',
                        type: 'column',
                        data: response.total_sales_arr                        
                    }, {
                        name: 'Total Qty Sold',
                        type: 'line',
                        data: response.total_qty_arr                        
                    }],
                    stroke: {
                        width: [0, 4]
                    },
                    // labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                    // labels: ['01 Jan 2001', '02 Jan 2001', '03 Jan 2001', '04 Jan 2001', '05 Jan 2001', '06 Jan 2001', '07 Jan 2001', '08 Jan 2001', '09 Jan 2001', '10 Jan 2001', '11 Jan 2001', '12 Jan 2001'],
                    labels: response.dates_arr,
                    xaxis: {
                        type: 'datetime'
                    },
                    yaxis: [{
                        title: {
                            text: 'Total Sales',
                        },
                
                    }, {
                        opposite: true,
                        title: {
                            text: 'Total Qty Sold'
                        }
                    }]
                
                };
                
                var chart777 = new ApexCharts(
                    document.querySelector("#daily-sales-graph"),
                    options777
                );

                setTimeout(function () {
            
                if (document.getElementById('daily-sales-graph')) {
                    chart777.render();
                }
            
                }, 1000);
            }
        } 
    })
}