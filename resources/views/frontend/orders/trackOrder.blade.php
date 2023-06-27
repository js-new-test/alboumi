@extends('frontend.layouts.master')

@section('content')
<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
	var orderIdError = <?php echo json_encode($trackOrderLabels['ORDERIDREQD'] ); ?>;
	var emailError = <?php echo json_encode($trackOrderLabels['EMAILREQ'] ); ?>;
	var trackingInfoLabel = <?php echo json_encode($trackOrderLabels['TRACKINGINFO'] ); ?>;
	var orderIdLabel = <?php echo json_encode($trackOrderLabels['ORDERID'] ); ?>;
</script>

<div class="thumb-nav tb-11">
    <div class="container">
        <a href="#">{{$trackOrderLabels['CONTACTUSPAGELABEL3']}}</a>
        <span>{{$trackOrderLabels['TRACKORDER']}}</span>
    </div>
</div>

<section class="contact-us">
    <div class="container">
        <div class="row">
            <div class="col-md-5 write-us">
                <form method="POST" id="trackOrderForm">
                    @csrf
                    <h4>{{$trackOrderLabels['TRACKORDER']}}</h4>
                    <div class="dividers"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="form-control input"
                                placeholder="{{$trackOrderLabels['ORDERID']}}*" name="orderId" id="orderId">
                        </div>
                        <div class="col-md-12">
                            <input type="text" class="form-control input" placeholder="{{$trackOrderLabels['EMAIL']}}"
                                name="email" id="email">
                        </div>
                    </div>
                    <button class="fill-btn" type="submit">{{$trackOrderLabels['TRACKORDER']}}</button>
                </form>
            </div>
            <div class="col-md-6 offset-md-1 contact" id="trackingInfoDiv">
				<h5 id="trackingInfoLabel" class="m-4"></h5>
				<div class="row">
					<div class="col-md-3">
						<p id="orderIdLabel"></p>
					</div>
					<div class="col-md-4">
						<p id="orderID"></p>
					</div>
				</div>
				<table class="table table-striped table-bordered" id="prodDetailTable">
					<thead>
						<tr>
							<th>{{$trackOrderLabels['PRODUCTNAME']}}</th>
							<th>{{$trackOrderLabels['QTY']}}</th>
							<th>{{$trackOrderLabels['STATUS']}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/order/trackOrder.js')}}"></script>
@endpush
