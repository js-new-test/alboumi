@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$registerLabels['REGISTERSEODESC']}}">
<meta name="keywords" content="{{$registerLabels['REGISTERSEOKEYWORD']}}">
<style>
.g-recaptcha {
    transform:scale(1.00);
    transform-origin:0 0;
}
</style>
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var emailReq = <?php echo json_encode($registerLabels['EMAILREQ']); ?>;
    var emailInvalid = <?php echo json_encode($registerLabels['NOTVALIDEMAIL']); ?>;
    var passwordReq = <?php echo json_encode($registerLabels['PASSWORDREQ']); ?>;
    var confirmPassReq = <?php echo json_encode($registerLabels['PASSCONFREQ']); ?>;
    var confirmPassMissMatch = <?php echo json_encode($registerLabels['error516']); ?>;
    var firstNameReq = <?php echo json_encode($registerLabels['FIRSTNAMEREQ']); ?>;
    var mobileMustBe = <?php echo json_encode($registerLabels['MOBILEMUSTBE8DIGIT']); ?>;
    var mobileNum = <?php echo json_encode($registerLabels['MOBILENUM']); ?>;
    var mobileReq = <?php echo json_encode($registerLabels['MOBILEREQ']); ?>;
</script>
@section('content')
    <section class="signup">
        <div class="container">
            <form class="w352" id="customerSignup" method="POST" action="{{url('/signup')}}">
                @csrf
                <input type="hidden" id="timezone" name="timezone">
                <input type="hidden" name="zone_time" id="zone_time">
                <h4>{{$registerLabels['REGISTERLABEL1']}}</h4>
                <span class="blurColor">{{$registerLabels['REGISTERLABEL2']}}</span>
                @if(Session::has('mail_sent_msg'))                     
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ Session::get('mail_sent_msg') }}
                        <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <input type="text" class="input" placeholder="{{$registerLabels['REGISTERLABEL3']}}" name="firstName">
                @if($errors->has('firstName'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('firstName') }}</div>
                @endif
                <input type="text" class="input" placeholder="{{$registerLabels['REGISTERLABEL4']}}" name="lastName">
                @if($errors->has('lastName'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('lastName') }}</div>
                @endif
                <input type="text" class="input" placeholder="{{$registerLabels['REGISTERLABEL5']}}" name="email">
                @if($errors->has('email'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('email') }}</div>
                @endif
                @if(session('msg'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{session('msg')}}</div>
                @endif                                                    
                <input type="number" class="input" placeholder="{{$registerLabels['REGISTERLABEL6']}}" name="mobile">
                @if($errors->has('mobile'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('mobile') }}</div>
                @endif
                <input type="password" class="input" placeholder="{{$registerLabels['REGISTERLABEL7']}}" id="password" name="password">
                @if($errors->has('password'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('password') }}</div>
                @endif
                <input type="password" class="input" placeholder="{{$registerLabels['REGISTERLABEL8']}}" name="confirm_password">
                @if($errors->has('confirm_password'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('confirm_password') }}</div>
                @endif
                <input type="text" class="input" placeholder="{{$registerLabels['REGISTERLABEL11']}}" name="loyalty_number" id="loyalty_number">                
                @if(config('services.recaptcha.key'))
                    <div class="g-recaptcha"
                        data-sitekey="{{config('services.recaptcha.key')}}">
                    </div>
                @endif
                @if($errors->has('g-recaptcha-response'))
                    <div class="error" style="margin: 0px 0px -10px 20px;color: red;">{{ $errors->first('g-recaptcha-response') }}</div>
                @endif
                <div class="position-relative form-check" style="margin-top: 20px;"><label class="form-check-label"><input type="checkbox" name="loyalty_flag" id="loyalty_flag" class="form-check-input" value="1"> {{$registerLabels['REGISTERLABEL12']}}</label></div>
                <input type="submit" class="fill-btn signup-btn" value="{{$registerLabels['REGISTERLABEL9']}}" name="signup">
                <div class="links d-flex justify-content-center">
                    <span>{{$registerLabels['REGISTERLABEL10']}}</span>
                    <a href="{{url('/login')}}">{{$registerLabels['LOGINLABEL']}}</a>
                </div>               
            </form>
        </div>
    </section>
@endsection
@push('scripts')
  <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
  <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
  <script src="{{asset('public/assets/frontend/js/signup/signup.js')}}"></script>
    <script>
        $(document).ready(function(){
            $('#timezone').val(moment.tz.guess());
            var zone_time = moment().format('Z'); 
            $('#zone_time').val(zone_time);       
        })
    </script>
@endpush
