@extends('frontend.layouts.master')
@section('content')
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
    <div class="thumb-nav tb-11">
		<div class="container">
			<a href="{{url('/')}}">{{$myEventEnqLabels['HOME']}}</a>
			<span>{{$myEventEnqLabels['SIDEBARLABEL5']}}</span>
		</div>
	</div>
	
	<section class="profile-pages">
		<div class="container">
			<div class="row">
				@include('frontend.include.sidebar')
				<div class="col-12 col-sm-12 col-md-8 col-lg-9">
					<div class="pl-24">
						<div class="right-side-items">
                            <div class="pl-24">
                                <div class="right-side-items">
                                    <div class="my-enquiries">
                                        
                                        <h4 class="profile-header">{{$myEventEnqLabels['SIDEBARLABEL5']}}</h4>
                                        @if(!($eventEnquiries->isEmpty()))
                                        <table>
                                            <tr>
                                                <th>{{$myEventEnqLabels['EVENT']}}</th>
                                                <th>{{$myEventEnqLabels['PLAN']}}</th>
                                                <th>{{$myEventEnqLabels['ENQDATE']}}</th>
                                                <th>{{$myEventEnqLabels['STATUS']}}</th>
                                                <th>{{$myEventEnqLabels['ADVPAYMENT']}}</th>
                                                <th>{{$myEventEnqLabels['AGREEDAMT']}}</th>
                                                <th>{{$myEventEnqLabels['ACTION']}}</th>
                                            </tr>
                                            @foreach($eventEnquiries as $myEnq)
                                            <tr>
                                                <td>{{$myEnq['eventName']}}</td>
                                                <td>{{$myEnq['packageName']}}</td>
                                                <td>{{ date('d M Y', strtotime($myEnq->enqDate)) }}</td>
                                                <td>
                                                    @if($myEnq['status'] == 0)
                                                        {{ 'Pending' }}
                                                    @endif
                                                    @if($myEnq['status'] == 1)
                                                        {{ 'In Process' }}
                                                    @endif
                                                    @if($myEnq['status'] == 2)
                                                        {{ 'Image Uploaded' }}
                                                    @endif
                                                    @if($myEnq['status'] == 3)
                                                        {{ 'Completed' }}
                                                    @endif
                                                    @if($myEnq['status'] == 4)
                                                        {{ 'Approved' }}
                                                    @endif
                                                </td>
                                                <td>{{ $currencyCode }} {{$myEnq['advance_payment']}}</td>
                                                <td>{{ $currencyCode }} {{$myEnq['total_amount']}}</td>
                                                <td>
                                                    @if($myEnq['advance_payment'] != null && $myEnq['advance_payment'] > 0 && $myEnq['payment_status'] == 0)
                                                    @php $id_encoded = rtrim(strtr(base64_encode($myEnq['id']), '+/', '-_'), '='); @endphp
                                                        <a href="{{ url('/eventEnq/payment/'.$id_encoded) }}" class="btn fill-btn" target="_blank">{{$myEventEnqLabels['PAYNOW']}}</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                        @else
                                            <div class="row">
                                                <div class="col-md-12 text-center my-5">
                                                    <h4>{{$myEventEnqLabels['NOENQFOUND']}}</h4>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
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
@endpush
