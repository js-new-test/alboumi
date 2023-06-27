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
    </style>
</head>

<body>
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
                    <td>: {{ $orderDetails->invoice_id }}</:>
                    </td>
                </tr>

                <tr>
                    <td>Order #</td>
                    <td>: {{ $orderDetails->order_id }}</td>
                </tr>
                <tr>
                    <td>Invoice Date </td>
                    <td>: {{ date('M d, Y',strtotime($orderDetails->invoiceDate)) }}</td>
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
                    @if($orderDetails->shipping_type == 'store_pickup')
                    {{ 'Store Pickup :'}}
                    @endif
                    @if($orderDetails->shipping_type == 'delivery')
                    {{ 'Ship To :'}}
                    @endif
                </th>
            </tr>
            <tr>
                <td>
                    <p class="margin">{{ $orderDetails->b_fullname }}</p>
                    <p class="margin">Address 1 : {{ $orderDetails->b_address_line_1 }},<br>
                        Flat No : {{ $orderDetails->b_address_line_2 }},<br>
                        Road : {{ $orderDetails->b_city }},<br>
                        Block : {{ $orderDetails->b_state }},<br>
                        Building : {{ $orderDetails->b_pincode }},<br>
                        {{ $orderDetails->b_country }}
                    </p>
                    <p class="margin"><i class="fa fa-phone-square"></i> Phone : {{ $orderDetails->b_phone1 }}</p>
                </td>
                <td>
                    @if($orderDetails->shipping_type == 'delivery')
                    <p class="margin">{{ $orderDetails->s_fullname }}</p>
                    <p class="margin">Address 1 : {{ $orderDetails->s_address_line_1 }},<br>
                        Flat No : {{ $orderDetails->s_address_line_2 }},<br>
                        Road : {{ $orderDetails->s_city }},<br>
                        Block : {{ $orderDetails->s_state }},<br>
                        Building : {{ $orderDetails->s_pincode }},<br>
                        {{ $orderDetails->s_country }}
                    </p>
                    <p class="margin"><i class="fa fa-phone-square"></i> Phone : {{ $orderDetails->s_phone1 }}</p>
                    @endif
                    @if($orderDetails->shipping_type == 'store_pickup')
                    @if(!empty($storePickupAddress))
                    <p>Title : {{ $storePickupAddress['title'] }}<br>
                        Address 1 : {{ $storePickupAddress->address_1 }},<br>
                        Address 2 : {{ $storePickupAddress->address_2 }}<br>
                    </p>
                    <p><i class="fa fa-phone-square"></i> Phone : {{ $storePickupAddress->phone }}</p>
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
                    <p class="margin">{{ $orderDetails->payment_method }} </p>
                </td>
                <td> {{ $orderDetails->shipping_method }} </td>
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
            @foreach($orderProductDetails as $productDetail)
            <tr>
                <td class="text-center" style="border: 1px solid black !important;">{{ $productDetail['srNo'] }}</td>
                <td style="border: 1px solid black !important; padding-left:2%;">
                    @if(!empty($productDetail['sku']))
                        SKU: {{ $productDetail['sku'] }}
                        <br>
                    @endif
                    {{ $productDetail['product_name'] }}
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
                        {{ number_format($orderDetails->subtotal,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            @if($orderDetails->discount_amount > 0)
            <tr>
                <td><b>Order Discount ({{ $orderDetails->promotions }}) </b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format($orderDetails->discount_amount,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            <tr>
                <td><b>Price After Discount</b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format(($orderDetails->subtotal - $orderDetails->discount_amount),$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            @endif
            <tr>
                <td><b>Shipping Cost</b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format($orderDetails->total_shipping_cost,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            <tr>
                <td><b>Tax</b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format($orderDetails->tax_amount,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
            <tr>
                <td><b>Grand Total</b></td>
                <td><b>: {{ $defaultCurrency->currency_code }}
                        {{ number_format($orderDetails->total,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</b>
                </td>
            </tr>
        </table>
        <br>
    </div>
</body>

</html>
