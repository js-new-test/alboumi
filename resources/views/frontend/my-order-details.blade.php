@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$ordersDetailsLabels['ORDERDETAILSDESC']}}">
<meta name="keywords" content="{{$ordersDetailsLabels['ORDERDETAILSKEYWORD']}}">
<style>
.order-td span{
  color: #666666;
  font-family: O_Regular;
  font-size: 12px;
  letter-spacing: 0.4px;
  line-height: 17px;
  display: block;
}


.order-td .red-class,
.order-td .green-class,
.order-td .orange-class,
.order-td .yellow-class{
  color: #212121;
  font-family: O_Regular;
  font-size: 12px;
  letter-spacing: 0.4px;
  line-height: 17px;
  display: inline-block;
  position: relative;
  padding-left: 16px;
}

.order-td .red-class::before,
.order-td .green-class::before,
.order-td .orange-class::before,
.order-td .yellow-class::before{
  content: '';
  position: absolute;
  height: 8px;
  width: 8px;
  border-radius: 100%;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
}
.order-td .red-class::before{
  background-color: red;
}
.order-td .green-class::before{
  background-color: #3EB658;
}
.order-td .orange-class::before{
  background-color: #FF851B;
}
.order-td .yellow-class::before{
  background-color: #f7b924;
}
</style>
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
@section('content')
    <div class="thumb-nav tb-11">
		<div class="container">
			<a href="{{url('/')}}">{{$ordersDetailsLabels['HOME']}}</a>
			<span>{{$ordersDetailsLabels['ORDER_DETAILS']}}</span>
		</div>
	</div>

    <section class="profile-pages">
        <div class="container">
            <div class="row">
                @include('frontend.include.sidebar')
                <div class="col-12 col-sm-12 col-md-8 col-lg-9">
                    <div class="pl-24">
                        <div class="right-side-items">
                            <div class="order-details">
                                <a href="{{url('customer/my-orders')}}" class="back-arrow"><img src="{{asset('public/assets/frontend/img/arrow-left.png')}}"></a>
                                <h4 class="profile-header">{{$ordersDetailsLabels['ORDER_DETAILS']}}</h4>
                                @if(count($orders_arr) > 0)
                                        <div class="detail-header">
                                            <div>
                                                <span>{{$orderIdText}}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="orderDate">{{$orderDate}}</span>
                                                <input type="hidden" id="zone" value="{{$timezone}}">
                                            </div>
                                        </div>

                                        @foreach($orders_arr['items'] as $item)
                                            <div class="order-table">
                                                <div class="order-tr">
                                                    <div class="order-td" style="width: 10%;">
                                                        <img src="{{$item['image']}}">
                                                    </div>
                                                    <div class="order-td" style="width: 65%;">
                                                        <span class="span">{{$item['itemName']}}</span>
                                                        <p class="s2">{{$item['price']}}</p>
                                                        @foreach($item['variant'] as $vari)
                                                        <span>{{$vari['title']}}: {{$vari['value']}}</span>
                                                        @endforeach
                                                    </div>
                                                    <div class="order-td" style="width: 10%;">
                                                        <p class="s2">{{$ordersDetailsLabels['QTY']}}: {{$item['qty']}}</p>
                                                    </div>
                                                    <div class="order-td" style="width: 15%;">
                                                      @if($item['slug'] == 'pending' || $item['slug'] == 'payment-failed' || $item['slug'] == 'awaiting-payment-confirmation' || $item['slug'] == 'cancelled')
                                                      <span class="red-class">{{$item['status']}}</span>
                                                      @endif
                                                      @if($item['slug'] == 'order-received' || $item['slug'] == 'shipped' || $item['slug'] == 'ready-to-dispatch' || $item['slug'] == 'in-transit' || $item['slug'] == 'packed')
                                                      <span class="yellow-class">{{$item['status']}}</span>
                                                      @endif
                                                      @if($item['slug'] == 'order-under-process' || $item['slug'] == 'out-for-delivery')
                                                      <span class="orange-class">{{$item['status']}}</span>
                                                      @endif
                                                      @if($item['slug'] == 'delivered' || $item['slug'] == 'ready-collect')
                                                      <span class="green-class">{{$item['status']}}</span>
                                                      @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    <div class="shiping-details">
                                        <h6>{{$ordersDetailsLabels['SHIPPINGDETAILS']}}</h6>
                                        <address>
                                            <span>{{$address_arr['fullName']}}</span>
                                            <span>{{$address_arr['addressLine1']}}, {{$address_arr['addressLine2']}}, {{$address_arr['city']}}, {{$address_arr['state']}}, {{$address_arr['country']}}</span>
                                        </address>
                                    </div>

                                    <div class="dividers tb30"></div>

                                    <div class="price-details">
                                        <h6>{{$ordersDetailsLabels['PRICEDETAILS']}}</h6>
                                        <table>
                                            <thead>
                                                @foreach($price_details_arr['listData'] as $listdata)
                                                <tr>
                                                    <td>{{$listdata['leftText']}}</td>
                                                    <td>{{$listdata['rightText']}}</td>
                                                </tr>
                                                @endforeach
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <th>{{$ordersDetailsLabels['TOTALAMOUNT']}}</th>
                                                    <th>{{$price_details_arr['payableAmount']}}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>


                                    <div class="dividers"></div>

                                    <span class="p-mode">{{$price_details_arr['paymentMode']}}</span>
                                @else
                                    <div style="margin: 25px 0px 0px 0px;">
                                        <p style="font-size: 25px;font-weight: 400;">{{$ordersDetailsLabels['ORDERNOTFOUND']}}</p>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('scripts')
  <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
  <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
  <script>
    $(document).ready(function(){
        var zone = $('#zone').val();
        $('.orderDate').each(function(){
            var date = moment.utc($(this).text()).utcOffset(zone.replace(':', "")).format("DD MMMM, YYYY HH:mm")
            $(this).text(date);
        })
    })
  </script>
@endpush
