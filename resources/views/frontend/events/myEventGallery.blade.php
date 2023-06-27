@extends('frontend.layouts.master')
@section('content')
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
    <div class="thumb-nav tb-11">
		<div class="container">
			<a href="#">{{$myEventGalleryLabels['HOME']}}</a>
			<span>{{$myEventGalleryLabels['SIDEBARLABEL6']}}</span>
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
                                        
                                        <h4 class="profile-header">{{$myEventGalleryLabels['SIDEBARLABEL6']}}</h4>
                                        @if(!empty($eventGallery))
                                        <table>
                                            <tr>
                                                <th>{{$myEventGalleryLabels['EVENT']}}</th>
                                                <th>{{$myEventGalleryLabels['DATE']}}</th>
                                                <th>{{$myEventGalleryLabels['PHOTOSNVIDEOS']}}</th>
                                            </tr>
                                            @foreach($eventGallery as $myGallery)
                                            <tr>
                                                <td>{{$myGallery['title']}}</td>
                                                <td>{{ $myGallery['date'] }}</td>
                                                @if($myGallery['isPayable'] == 1)
                                                    <td><a href="{{ url('customer/eventGallery/'.$myGallery['id'].'/1') }}" class="btn fill-btn">{{$myEventGalleryLabels['PAYBUTTON']}}</a></td>
                                                @else
                                                    <td><a href="{{ url('customer/eventGallery/'.$myGallery['id'].'/0') }}" class="btn fill-btn">{{$myEventGalleryLabels['DOWNLOAD']}}</a></td>
                                                @endif
                                            </tr>
                                            @endforeach
                                        </table>
                                        @else
                                            <div class="row">
                                                <div class="col-md-12 text-center my-5">
                                                    <h4>{{$myEventGalleryLabels['NOPHOTOSFOUND']}}</h4>
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
