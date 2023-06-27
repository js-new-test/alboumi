<!DOCTYPE html>
<html>

<head>
    <!-- <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css"> -->
    <meta charset="utf-8">
    <title>Print Order</title>
    <style>
        body {
            border: 1px solid #000;
        }

        .logo {
            position: absolute;
            top: 3%;
            left: 4%;
        }

        .clearfix {
            margin-right: 4%;
            margin-top: 6%;
        }

        .page-header {
            background-color: #000;
        }

        .pdftbl {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            color: black !important;
        }

        .pdftbl th {
            border-style: solid;
            border-width: 1px;
            overflow: hidden;
            word-break: normal;
            color: black !important;
            height: 30px;
            background-color: #ddd;
            padding: 0 4px;
            text-align: left;
        }

        .margin {
            margin-left: 4%;
        }

        hr {
            margin-top: 3%;
        }

        .text-center {
            text-align: center;
        }
        div.breakNow { page-break-inside:avoid; page-break-after:always; }
    </style>
</head>

<body>
  <?php $k=0;?>
  @foreach($dataPdfArr as $data)
    <div class="page-header">
    <img src="{{ public_path('/assets/frontend/img/Alboumi_Logo.png') }}" class="logo" height="80" width="100px">
        <div
            style="  display: block; float: right; margin-right:0px; position: relative;margin-left: 350px;font-size: 15px !important;">
            <br>
            <br>
            <table width='100%' cellpadding='0' cellspacing='0' border='0' class='pdftbl'>
                <tr>
                    <td>Invoice #
                    </td>
                    <td>: {{ $data['orderDetails']->invoice_id }}</:>
                    </td>
                </tr>

                <tr>
                    <td>Order #</td>
                    <td>: {{ $data['orderDetails']->order_id }}</td>
                </tr>
                <tr>
                    <td>Invoice Date </td>
                    <td>: {{ date('M d, Y',strtotime($data['orderDetails']->invoiceDate)) }}</td>
                </tr>
            </table>
            <br>
        </div>
    </div>

    <div class="row" style="margin-top: 150px;padding-top: 20px;">
        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='pdftbl'
            style="margin-top:12%; font-size: 15px !important;">
            <tr>
                <th width="50%">Sold To : </th>
                <th width="50%">
                    @if($data['orderDetails']->shipping_type == 'store_pickup')
                    {{ 'Store Pickup :'}}
                    @endif
                    @if($data['orderDetails']->shipping_type == 'delivery')
                    {{ 'Ship To :'}}
                    @endif
                </th>
            </tr>
            <tr>
                <td>
                    <p class="margin">{{ $data['orderDetails']->b_fullname }}</p>
                    <p class="margin">Address 1 : {{ $data['orderDetails']->b_address_line_1 }},<br>
                        Flat No : {{ $data['orderDetails']->b_address_line_2 }},<br>
                        Road : {{ $data['orderDetails']->b_city }},<br>
                        Block : {{ $data['orderDetails']->b_state }},<br>
                        Building : {{ $data['orderDetails']->b_pincode }},<br>
                        {{ $data['orderDetails']->b_country }}
                    </p>
                    <p class="margin"><i class="fa fa-phone-square"></i> Phone : {{ $data['orderDetails']->b_phone1 }}</p>
                </td>
                <td>
                    @if($data['orderDetails']->shipping_type == 'delivery')
                    <p class="margin">{{ $data['orderDetails']->s_fullname }}</p>
                    <p class="margin">Address 1 : {{ $data['orderDetails']->s_address_line_1 }},<br>
                        Flat No : {{ $data['orderDetails']->s_address_line_2 }},<br>
                        Road : {{ $data['orderDetails']->s_city }},<br>
                        Block : {{ $data['orderDetails']->s_state }},<br>
                        Building : {{ $data['orderDetails']->s_pincode }},<br>
                        {{ $data['orderDetails']->s_country }}
                    </p>
                    <p class="margin"><i class="fa fa-phone-square"></i> Phone : {{ $data['orderDetails']->s_phone1 }}</p>
                    @endif
                    @if($data['orderDetails']->shipping_type == 'store_pickup')
                    @if(!empty($data['storePickupAddress']))
                    <p>Title : {{ $data['storePickupAddress']['title'] }}<br>
                        Address 1 : {{ $data['storePickupAddress']->address_1 }},<br>
                        Address 2 : {{ $data['storePickupAddress']->address_2 }}<br>
                    </p>
                    <p><i class="fa fa-phone-square"></i> Phone : {{ $data['storePickupAddress']->phone }}</p>
                    @endif
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <hr>

    <div class="row">
        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='pdftbl'
            style="margin-top:1%; font-size: 15px !important;">
            <tr>
                <th width="50%">Payment Method : </th>
                <th width="50%"> Shipping Method :</th>
            </tr>
            <tr>
                <td>
                    <p class="margin">{{ $data['orderDetails']->payment_method }} </p>
                </td>
                <td> {{ $data['orderDetails']->shipping_method }} </td>
            </tr>
        </table>
    </div>
    <hr style="margin-top:1%">

    <div class="row">
        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='pdftbl'
            style="margin-top:1%; font-size: 15px !important;" id="prodTable">
            <tr>
                <th>Sr. No</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Order Status</th>
                <th>Sub Total</th>
            </tr>
            @foreach($data['orderProductDetails'] as $productDetail)
            <tr>
                <td class="text-center" style="border: 1px solid black !important;">{{ $productDetail['srNo'] }}</td>
                <td style="border: 1px solid black !important; padding-left:2%;">{{ $productDetail['product_name'] }}
                    <br>
                    @if(!empty($productDetail['variants']))
                        @foreach($productDetail['variants'] as $prodVariants)
                            @if(!empty($prodVariants['title'])){{ $prodVariants['title'] }} : @endif  @if(!empty($prodVariants['value'])){{ $prodVariants['value'] }}@endif <br>
                        @endforeach
                    @endif
                </td>
                <td style="border: 1px solid black !important; padding-left:2%;">{{ $defaultCurrency->currency_code }}
                    {{ $productDetail['price'] }}</td>
                <td class="text-center" style="border: 1px solid black !important; padding-left:2%;">
                    {{ $productDetail['quantity'] }}</td>
                <td style="border: 1px solid black !important; padding-left:4%;">{{ $productDetail['status'] }}</td>
                <td style="border: 1px solid black !important; padding-left:2%;">{{ $defaultCurrency->currency_code }}
                    {{ number_format(($productDetail['quantity'] * $productDetail['price']),$decimalNumber, $decimalSeparator, $thousandSeparator) }}
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    <div
        style="  display: block; float: right; margin-right:0px; position: relative;margin-left: 350px;font-size: 15px !important;">
        <br>
        <br>
        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='pdftbl'>
            <tr>
                <td><b>Subtotal</b>
                </td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format($data['orderDetails']->subtotal,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            @if($data['orderDetails']->discount_amount > 0)
            <tr>
                <td><b>Order Discount ({{ $data['orderDetails']->promotions }}) </b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format($data['orderDetails']->discount_amount,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            <tr>
                <td><b>Price After Discount</b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format(($data['orderDetails']->subtotal - $data['orderDetails']->discount_amount),$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            @endif
            <tr>
                <td><b>Shipping Cost</b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format($data['orderDetails']->total_shipping_cost,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            <tr>
                <td><b>Tax</b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format($data['orderDetails']->tax_amount,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            <tr>
                <td><b>Grand Total</b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format($data['orderDetails']->total,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
        </table>
        <br>
    </div>
    <?php $k++;?>
    @if($k < count($dataPdfArr))
    <div class="breakNow"></div>
    @endif
    @endforeach
</body>

</html>
