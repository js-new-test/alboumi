@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$resetPasswordLabels['RESTEPASSSEODEC']}}">
<meta name="keywords" content="{{$resetPasswordLabels['RESTEPASSSEOKEYWORD']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var passwordReq = <?php echo json_encode($resetPasswordLabels['PASSWORDREQ']); ?>;
    var confirmPassReq = <?php echo json_encode($resetPasswordLabels['PASSCONFREQ']); ?>;
    var confirmPassMissMatch = <?php echo json_encode($resetPasswordLabels['error516']); ?>;
</script>
@section('content')
    <section class="login">
        <div class="container">
            <form id="resetPassForm" class="w352" method="POST" action="{{url('/reset-password')}}">
                @csrf
                <input type="hidden" value="{{$email}}" name="reset_pass_email">
                <h4>{{$resetPasswordLabels['RESETPASSWORD']}}</h4>               
                @if(Session::has('msg'))                     
                    <div class="alert {{ (Session::get('msg') == true) ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
                        {{ Session::get('msg') }}
                        <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif                                
                <input type="password" class="input" placeholder="{{$resetPasswordLabels['PASSWORD']}}" name="password" id="password">
                @if($errors->has('password'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('password') }}</div>
                @endif
                <input type="password" class="input" placeholder="{{$resetPasswordLabels['CONFIRM_PASSWORD']}}" name="confirm_password" id="confirm_password">
                @if($errors->has('password'))
                    <div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('confirm_password') }}</div>
                @endif               
                <input type="submit" class="fill-btn login-btn" value="{{$resetPasswordLabels['RESETPASSWORD']}}" name="">               
                <div class="links d-flex justify-content-center">
                    <span>{{$resetPasswordLabels['FORGOTPASSLABEL3']}}</span>
                    <a href="{{url('/login')}}">{{$resetPasswordLabels['LOGINLABEL']}}</a>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/reset-password/reset-password.js')}}"></script>
@endpush
