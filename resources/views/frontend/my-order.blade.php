@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$ordersLabels['MYORDERDEC']}}">
<meta name="keywords" content="{{$ordersLabels['MYORDERKEYWORD']}}">
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
			<a href="{{url('/')}}">{{$ordersLabels['HOME']}}</a>
			<span>{{$ordersLabels['MYORDER']}}</span>
		</div>
	</div>

    <section class="profile-pages">
        <div class="container">
            <div class="row">
                @include('frontend.include.sidebar')
                <div class="col-12 col-sm-12 col-md-8 col-lg-9">
                    <div class="pl-24">
                        <div class="right-side-items">
                            <div class="my-order">
                                <h4 class="profile-header">{{$ordersLabels['MYORDER']}}</h4>
                                @if(count($orders_arr) > 0)
                                    @foreach($orders_arr as $orders)
                                        <div class="detail-header">
                                            <div>
                                                <p class="s1">{{$orders['orderIdText']}}</p>
                                                <span>{{$orders['subText']}}</span>
                                            </div>
                                            <div class="text-right">
                                                <p class="s1"><a href="{{$orders['Orderdetails']}}">{{$ordersLabels['VIEW_DETAILS']}}</a></p>
                                                <span class="orderDate">{{$orders['orderDate']}}</span>
                                                <input type="hidden" id="zone" value="{{$timezone}}">
                                            </div>
                                        </div>
                                        @foreach($orders['items'] as $item)
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
                                                        <p class="s2">{{$ordersLabels['QTY']}}: {{$item['qty']}}</p>
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
                                    @endforeach
                                @else
                                    <div style="margin: 25px 0px 0px 0px;">
                                        <p style="font-size: 25px;font-weight: 400;">{{$ordersLabels['ORDERNOTFOUND']}}</p>
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
