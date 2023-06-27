@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>    
</script>
<script src="https://credimax.gateway.mastercard.com/checkout/version/54/checkout.js"
        data-error="errorCallback"
        data-cancel="cancelCallback">
</script>

<script>    
    function errorCallback(error) {
        console.log(JSON.stringify(error));
    }
    function cancelCallback() {
        console.log('Payment cancelled');
    }
    Checkout.configure({
        // merchant: 'E01616950',
        merchant: '<?php echo $merchant_id = config('app.CREDIMAX_MERCHANT_ID'); ?>',
        order: {
            amount: function() {
                // return '1.000'; // Transaction Amount (order table total field)
                return '<?php echo $event_order_amount = Session::get('event_order_amount'); ?>';
            },
            currency: 'BHD',
            // description:'Payment For Order 1', // 1 means marchant_order id
            description:'Payment For Event Photos Order <?php echo $event_merchant_order_id = Session::get('event_merchant_order_id'); ?>',
            // id: '1' //session merchant_order id
            id: '<?php echo $event_merchant_order_id = Session::get('event_merchant_order_id'); ?>'
        }, 
        session: { 
            // id : 'SESSION0002588086103F3591966G06' // PASS the session id generated in the api  --> --> session_id from response
            id : '<?php echo $session_id = Session::get('session_id'); ?>'
        }, 
        interaction: { 
            operation: '<?php echo $action = config('app.CREDIMAX_ACTION'); ?>',
            merchant: { 
                name: 'ASHRAFS', 
                logo: baseUrl + 'public/assets/frontend/img/Alboumi_Logo.png' 
            }, 

            displayControl: { 
                billingAddress : "HIDE" 
            }, 
        }
    });

    //Checkout.showLightbox();
    Checkout.showPaymentPage();
</script>
@section('content')
<link rel="stylesheet" href="{{asset('public/assets/frontend/css/loader-style.css')}}">

<div id="ajax-loader">
  <div class="cv-spinner">
  	<img src="{{ asset('public/assets/frontend/img/Loader.svg') }}" class="spinner">
  </div>
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script>
$("#ajax-loader").fadeIn();
</script>
@endpush
