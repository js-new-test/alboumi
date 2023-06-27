@extends('frontend.layouts.master')
@section('content')
<link rel="stylesheet" href="{{asset('public/assets/frontend/css/loader-style.css')}}">

<style>
    .pagination
    {
        float: right !important;
        padding-right: 5% !important;
    }
    #downloadImgForm
    {
        display:inline-block;
    }
    .addToCart, .buyNow, .fill-btn
    {
        cursor:pointer;
    }
    .custom-control-label img{
        width:35px
    }
    .debitImg img{
        width: 135px;
    }
    #debitGroup .custom-control-label::before{
        top: 1.5rem !important;
    }
    #debitGroup .custom-control-label::after{
        top: 1.5rem !important;
    }
</style>
<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var currencyCode = <?php echo json_encode($myEventGalleryLabels["BHD"]); ?>;
    var selectedLabel = <?php echo json_encode($myEventGalleryLabels["SELECTED"]); ?>;
    var pricePerPhoto = <?php echo json_encode($eventName->price_per_photo); ?>;
</script>

<div id="ajax-loader">
  <div class="cv-spinner">
  	<img src="{{ asset('public/assets/frontend/img/Loader.svg') }}" class="spinner">
  </div>
</div>

<!-- <input type="hidden" name="checkedImagesIds" id="checkedImagesIds" value="{{ $selectedImages }}"> -->
<input type="hidden" name="checkedImagesCount" id="checkedImagesCount" value="{{ $selectedImagesCount }}">
<input type="hidden" name="selectedImagePrice" id="selectedImagePrice" value="{{ $selectedImagesPrice }}">
@php
    $selectedImagesToBuy = Session::get('selectedImagesToDownload');
@endphp
<input type="hidden" name="selectedImagesToBuy" id="selectedImagesToBuy" value="{{ $selectedImages }}">
<div class="thumb-nav tb-11">
	<div class="container">
		<a href="{{ url('/') }}">{{ $myEventGalleryLabels["HOME"] }}</a>
		<a href="{{ url('/customer/myEventGallery') }}">{{ $myEventGalleryLabels["SIDEBARLABEL6"] }}</a>
		<span>{{ $eventName->eventName }}</span>
	</div>
</div>
<input type="hidden" id="enqId" value="{{ $enqId }}">
<input type="hidden" id="custId" value="{{ $custId->id }}">

<section class="event-gallery-section">
	<div class="container">
		<div class="gallery-header">
			<div class="row">
				<div class="col-sm-12 col-md-6">
					<h4>{{ $eventName->eventName }}</h4>
				</div>
				<div class="col-sm-12 col-md-6 text-md-right">
                    <p class="s1" id="selectedPrice"></p>
                    @if($btnValue == 1)
                        <button class="fill-btn" id="payNdownloadBtn">{{ $myEventGalleryLabels["PAYBUTTON"] }}</button>
                    @else
                        <button class="fill-btn d-none">{{ $myEventGalleryLabels["PAYBUTTON"] }}</button>
                    @endif
				</div>
			</div>
        </div>
        <div class="gallery-grid">
            @if(!empty($data))
            @foreach($data as $photo)
			<div class="gallery-box">
				<img src="{{ $photo['photo'] }}" class="openImgInGallery" data-id="{{ $photo['photo'] }}" data-toggle="modal" data-bigimage="{{ $photo['photo'] }}" data-target="#gallery-modal">
                @if($photo['flag_purchased'] == 1)
				<div class="for-download">
					<button class="print-btn printImg" data-toggle="modal" data-target="#print-modal" data-id="{{ $photo['photo'] }}">
						<img src="{{ asset('public/assets/frontend/img/Event-Gallery/print.png') }}">
					</button>
                    <form method="POST" action="{{ url('customer/downloadImage') }}" id="downloadImgForm">
                    @csrf
                        <input type="hidden" name="imgPath" value="{{ $photo['photo'] }}">
                        <button class="download-btn">
                            <img src="{{ asset('public/assets/frontend/img/Event-Gallery/download.png') }}">
                        </button>
                    </form>
                </div>
                @else
                @if(in_array($photo['id'], explode(',',$selectedImages)))
                <div class="for-not-download">
					<label class="ck">
					  <input type="checkbox" class="imgCheckbox" name="photoId" value="{{ $photo['id'] }}" checked>
					  <span class="checkmark"></span>
					</label>
				</div>
                @else
                <div class="for-not-download">
					<label class="ck">
					  <input type="checkbox" class="imgCheckbox" name="photoId" value="{{ $photo['id'] }}">
					  <span class="checkmark"></span>
					</label>
				</div>
                @endif
                @endif
            </div>
            @endforeach
            @endif
		</div>
    </div>
    {!! $data->links() !!}

    {{--<div class="row">
        <div class="col-md-4 offset-md-9 mt-5">
            <p>
                Displaying {{$data->count()}} out of {{ $data->total() }} images.
            </p>
        </div>
    </div>--}}
    
    <!-- show image popup -->
    <div class="modal fade" id="gallery-modal" tabindex="-1" role="dialog" aria-labelledby="galleryModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		    <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="gallery-close-btn" data-dismiss="modal" aria-label="Close">
                        <img src="{{ asset('public/assets/frontend/img/Event-Gallery/Close.png')}}">
                    </button>
                    <img src="" alt="" id="galleryImage" class="img-fluid">
                </div>
		    </div>
		</div>
    </div>

    <!-- print image popup -->
    <div class="modal fade" id="print-modal" tabindex="-1" role="dialog" aria-labelledby="printModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		    <div class="modal-content">
                <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalCenterTitle">{{ $myEventGalleryLabels['PRINT'] }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <img src="{{ asset('public/assets/frontend/img/Event-Gallery/Close.png')}}">
                        </button>
                </div>
                <div class="modal-body">
                    <img src="" class="print-left-side" id="printImage">
                    <div class="print-right-side">
                        <p class="s1 mb12">{{ $myEventGalleryLabels['QUANTITY'] }}</p>
                        <div class="plusminus horiz">
                            <button><img src="{{ asset('public/assets/frontend/img/Minus1.png')}}"></button>
                                <input type="number" name="productQty" id="productQty" value="1" min="1" max="9999999" >
                            <button>
                            <img src="{{ asset('public/assets/frontend/img/Plus1.png')}}"></button> 
                        </div>

                        <div class="size-i">
                            <p class="s1">{{ $myEventGalleryLabels['PRODUCT'] }}</p>
                            <p id="selectedProdName"></p>
                            <input type="hidden" id="selectedOptionId">
                        </div>
                        <select class="select s-bold w256" name="productNames" id="productNames">
                            @foreach($photoProducts as $product)
                                <option value="{{ $product['id'] }}" data-option="{{ $product['optionId'] }}">{{ $product['title'] }}</option>
                            @endforeach
                        </select>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="s1">{{ $myEventGalleryLabels['PRICE'] }} : {{ $currencyCode }}
                                @if(!empty($photoProducts))
                                    <span id="prodPrice">@if(empty($photoProducts[0]['group_price']))
                                        @if(!empty($photoProducts[0]['discountedPrice']))
                                            <strike>{{ number_format($photoProducts[0]['price'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</strike>
                                            <span class="text-danger">{{ number_format($photoProducts[0]['discountedPrice'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</span>
                                        @else
                                            {{ number_format($photoProducts[0]['price'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}
                                        @endif
                                    @elseif(!empty($photoProducts[0]['group_price']))
                                        @if(!empty($photoProducts[0]['price']))
                                            <strike>{{ number_format($photoProducts[0]['price'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</strike>
                                        @endif
                                        <span class="text-danger">{{ number_format($photoProducts[0]['group_price'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</span>
                                    @endif
                                    </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <a class="border-btn w100 addToCart" >{{ $myEventGalleryLabels['ADDTOCART'] }}</a>
                            </div>
                            <div class="col-6">
                                <a class="fill-btn w100 buyNow">{{ $myEventGalleryLabels['BUYNOW'] }}</a>
                            </div>
                        </div>
                    </div>
                </div>
		    </div>
		</div>
	</div>

     <!-- payment method popup -->
     <div class="modal fade " id="pay_modal" tabindex="-1" role="dialog" aria-labelledby="pay_modal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">{{ $myEventGalleryLabels['PAYMENMETHOD'] }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <img src="{{ asset('public/assets/frontend/img/Event-Gallery/Close.png')}}">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-11 offset-md-1">
                            <div>
                                <div class="custom-radio custom-control form-group">
                                    <input class="custom-control-input form-control" type="radio" id="payment_type_credit" name="payment_type" value="1" checked>
                                    <label class="custom-control-label" for="payment_type_credit">Credit Card
                                        <img src="{{ asset('public/assets/frontend/img/payment/jcb.jpg')}}">
                                        <img src="{{ asset('public/assets/frontend/img/payment/visa.jpg')}}">
                                        <img src="{{ asset('public/assets/frontend/img/payment/mastercard.png')}}">
                                    </label>
                                </div>
                                <div class="custom-radio custom-control form-group" id="debitGroup">
                                    <input class="custom-control-input form-control mt-4" type="radio" id="payment_type_debit" name="payment_type" value="2">
                                    <label class="custom-control-label debitImg" for="payment_type_debit">Debit Card
                                        <img src="{{ asset('public/assets/frontend/img/payment/benefit.jpg')}}">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-4 offset-md-4">
                                <a class="w-100 fill-btn" id="continueBtn">{{ $myEventGalleryLabels['CONTINUE'] }}</a>
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
<script src="{{asset('public/assets/frontend/js/events/eventGallery.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/events/eventOrderPayment.js')}}"></script>
@endpush
