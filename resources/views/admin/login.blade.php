@extends('admin.layouts.master')

@section('content')
<div class="app-container app-theme-white body-tabs-shadow">
    <div class="app-container">
        <div class="h-100 bg-plum-plate bg-animation">
            <div class="d-flex h-100 justify-content-center align-items-center">
                <div class="mx-auto app-login-box col-md-8">
                    <div class="app-logo-inverse mx-auto mb-3"></div>
                    <div class="modal-dialog w-100 mx-auto">
                        <div class="modal-content">                           
                            <form id="loginForm" method="POST" action="{{ url('/admin/login') }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="h5 modal-title text-center">
                                        <h4 class="mt-2">
                                            <div>Welcome back,</div>
                                            <span>Please sign in to your account below.</span>
                                        </h4>
                                    </div>
                                    @if(Session::has('msg'))                     
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ Session::get('msg') }}
                                            <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif 
                                    @if($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" style="padding-bottom: 0px;" role="alert">
                                        <ul>
                                            @foreach ($errors->all() as $error)                                                                                        
                                                <li>{{ $error }}</li>
                                                <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>                                            
                                            @endforeach
                                        </ul>
                                        </div>
                                    @endif                               
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="position-relative form-group">
                                                <input name="email" id="exampleEmail" placeholder="Email" type="text" class="form-control" value="{{Cookie::get('email')}}">                                                
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="position-relative form-group">
                                                <input name="password" id="examplePassword" placeholder="Password" type="password" class="form-control" value="{{Cookie::get('password')}}">                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="position-relative form-check"><input name="remember" id="remember" type="checkbox" {{(Cookie::get('remember') == 'checked') ? 'checked' : ''}} class="form-check-input"><label for="remember" class="form-check-label">Keep me logged in</label></div>                                

                                    <div class="form-row mt-2">
                                        <div class="col-md-12">
                                        @if(config('services.recaptcha.key'))
                                            <div class="g-recaptcha"
                                                data-sitekey="{{config('services.recaptcha.key')}}" data-callback="recaptchaCallback">
                                            </div>
                                            <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">

                                        @endif
                                        @if($errors->has('g-recaptcha-response'))
                                            <div class="error" style="margin: 0px 0px 0px 20px;color: red;">{{ $errors->first('g-recaptcha-response') }}</div>
                                        @endif
                                        </div>
                                    </div>
                                    <!-- <div class="divider"></div> -->
                                    <!-- <h6 class="mb-0">No account? <a href="javascript:void(0);" class="text-primary">Sign up now</a></h6> -->
                                </div>
                                <div class="modal-footer clearfix">
                                    <div class="float-left"><a href="{{url('/admin/forgot-password')}}" class="btn-lg btn btn-link">Forgot Password</a></div>
                                    <div class="float-right">
                                        <input type="submit" name="submit" class="btn btn-primary btn-lg" value="Login to Dashboard">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center opacity-8 mt-3">Copyright © Alboumi 2020. All rights reserved.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@include('admin.include.bottom')
