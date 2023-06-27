@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$loginLabels['LOGINSEODESC']}}">
<meta name="keywords" content="{{$loginLabels['LOGINSEOKEYWORD']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var emailReq = <?php echo json_encode($loginLabels['EMAILREQ']); ?>;
    var emailInvalid = <?php echo json_encode($loginLabels['NOTVALIDEMAIL']); ?>;
    var passwordReq = <?php echo json_encode($loginLabels['PASSWORDREQ']); ?>;
</script>
@section('content')
    <section class="login">
        <div class="container">
            <form id="frontLoginForm" class="w352" method="POST" action="{{url('/login')}}">
                @csrf
                @php $flagCheckout = request()->get('flagCheckout') @endphp
                <input type="hidden" name="flagCheckout" value="{{(isset($flagCheckout) ? '1' : '0')}}">

                @php $flagLogin = request()->get('flagLogin') @endphp
                <input type="hidden" name="flagLogin" value="{{(isset($flagLogin) ? '1' : '0')}}">

                @php $eventId = request()->get('eventId') @endphp
                <input type="hidden" name="eventId" value="{{(isset($eventId) ? $eventId : '0')}}">

                <h4>{{$loginLabels['LOGINLABEL']}}</h4>
                <span class="blurColor">{{$loginLabels['LOGINLABEL1']}}</span>
                @if(Session::has('invalid_credentials'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ Session::get('invalid_credentials') }}
                        <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <input type="text" class="input" placeholder="{{$loginLabels['LOGINLABEL2']}}" name="email" id="email" value="{{Cookie::get('customer_email')}}">
                @if($errors->has('email'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('email') }}</div>
                @endif
                <input type="password" class="input" placeholder="{{$loginLabels['LOGINLABEL3']}}" name="password" id="password" value="{{Cookie::get('customer_password')}}">
                @if($errors->has('password'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('password') }}</div>
                @endif
                <input type="hidden" name="timezone" id="timezone">
                <input type="hidden" name="zone_time" id="zone_time">
                <div class="row mt25">
                    <div class="col-6 normal-ck">
                        <label class="ck">{{$loginLabels['LOGINLABEL4']}}
                        <input type="checkbox" name="remember_me" id="remember_me" {{(Cookie::get('customer_remember') == 'checked') ? 'checked' : ''}}>
                        <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="col-6 text-right">
                        <a href="{{url('/forgot-password')}}" class="forgot-psw">{{$loginLabels['LOGINLABEL5']}}</a>
                    </div>
                </div>
                <input type="submit" class="fill-btn login-btn" value="{{$loginLabels['LOGINLABEL']}}" name="">
                <!-- <button type="button" class="fill-btn login-btn" id="login_api_ajx">Login</button> -->
                <div class="links d-flex justify-content-center">
                    <span>{{$loginLabels['LOGINLABEL7']}}</span>
                    <a href="{{url('/signup')}}">{{$loginLabels['LOGINLABEL6']}}</a>
                </div>

                <div class="or">{{$loginLabels['LOGINLABEL8']}}</div>

                <div class="with-social text-center">
                    <div class="d-flex">
                        {{-- <a class="facebook-btn" href="{{ url('auth/facebook') }}"><img src="{{asset('public/assets/frontend/img/Facebook.svg')}}"> {{$loginLabels['LOGINLABEL9']}}</a> --}}

                        {{-- <a href="{{ url('auth/google') }}" class="google-btn"><img src="{{asset('public/assets/frontend/img/Google.svg')}}"> {{$loginLabels['LOGINLABEL10']}}</a> --}}
                        <a class="facebook-btn-new" href="{{ url('auth/facebook') }}">
                            <div class="fb-bg">
                                <img src="{{asset('public/assets/frontend/img/icons8-facebook-f.svg')}}"> {{$loginLabels['LOGINLABEL9']}}
                            </div>
                        </a>
                        <a href="{{ url('auth/google') }}" class="google-assets-btn"></a>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/login/login.js')}}"></script>
<script>
$(document).ready(function(){               
    $('#timezone').val(moment.tz.guess());
    var zone_time = moment().format('Z'); 
    $('#zone_time').val(zone_time);       
})
</script>
@endpush
