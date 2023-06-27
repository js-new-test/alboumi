@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$forgotPasswordLabels['FPASSWORDSEODESC']}}">
<meta name="keywords" content="{{$forgotPasswordLabels['FPASSWORDSEOKEYWORD']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var emailReq = <?php echo json_encode($forgotPasswordLabels['EMAILREQ']); ?>;
    var emailInvalid = <?php echo json_encode($forgotPasswordLabels['NOTVALIDEMAIL']); ?>;
</script>
@section('content')
    <section class="login">
        <div class="container">
            <form id="frontForgotPassForm" class="w352" method="POST" action="{{url('/forgot-password')}}">
                @csrf
                <h4>{{$forgotPasswordLabels['FORGOTPASSLABEL']}}</h4>
                <span class="blurColor">{{$forgotPasswordLabels['FORGOTPASSLABEL1']}}</span>
                @if(Session::has('msg'))                     
                    <div class="alert {{ (Session::get('msg_class') == true) ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
                        {{ Session::get('msg') }}
                        <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif                
                <input type="text" class="input" placeholder="{{$forgotPasswordLabels['FORGOTPASSLABEL2']}}" name="email">
                @if($errors->has('email'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('email') }}</div>
                @endif
                <!-- <input type="password" class="input" placeholder="Password*" name="password" value="{{Cookie::get('customer_password')}}">
                @if($errors->has('password'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('password') }}</div>
                @endif
                <div class="row mt25">
                    <div class="col-6 normal-ck">
                        <label class="ck">Remember Me
                        <input type="checkbox" name="remember_me" {{(Cookie::get('customer_remember') == 'checked') ? 'checked' : ''}}>  
                        <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="col-6 text-right">
                        <a href="" class="forgot-psw">Forgot Password?</a>
                    </div>
                </div> -->
                <input type="submit" class="fill-btn login-btn" value="{{$forgotPasswordLabels['FORGOTPASSLABEL4']}}" name="">
                <div class="links d-flex justify-content-center">
                    <span>{{$forgotPasswordLabels['FORGOTPASSLABEL3']}}</span>
                    <a href="{{url('/login')}}">{{$forgotPasswordLabels['LOGINLABEL']}}</a>
                </div>
                <!-- <div class="links d-flex justify-content-center">
                    <span>New to Alboumi?</span>
                    <a href="{{url('/signup')}}">Register Now</a>
                </div> -->

                <!-- <div class="or">OR</div>

                <div class="with-social text-center">
                    <div class="d-flex">
                        <button class="facebook-btn"><img src="{{asset('assets/frontend/img/Facebook.svg')}}"> Facebook</button>
                        <button class="google-btn"><img src="{{asset('assets/frontend/img/Google.svg')}}"> Google</button>
                    </div>
                </div> -->
            </form>
        </div>
    </section>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/forgot-password/forgot-password.js')}}"></script>
@endpush
