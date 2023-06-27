@extends('admin.layouts.master')

@section('content')
<div class="app-container app-theme-white body-tabs-shadow">
    <div class="app-container">
        <div class="h-100 bg-plum-plate bg-animation">
            <div class="d-flex h-100 justify-content-center align-items-center">
                <div class="mx-auto app-login-box col-md-8">
                    <div class="app-logo-inverse mx-auto mb-3"></div>
                        <div class="modal-dialog w-100">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div class="h5 modal-title">Forgot your Password?<h6 class="mt-1 mb-0 opacity-8"><span>Use the form below to recover it.</span></h6></div>
                                </div>
                                <form class="" method="POST" action="{{url('/photographer/forgot-password')}}">
                                    @csrf
                                    @if(Session::has('success_msg'))                     
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ Session::get('success_msg') }}
                                            <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
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
                                    <div class="modal-body">
                                        <div>                                            
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group">
                                                        <label for="forgot_email" class="">Email</label>
                                                        <input name="forgot_email" id="forgot_email" placeholder="Email here..." type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="divider"></div> -->
                                        <!-- <h6 class="mb-0"><a href="javascript:void(0);" class="text-primary">Sign in existing account</a></h6></div> -->
                                    </div>
                                    <div class="modal-footer clearfix">
                                        <div class="float-left">
                                            <a href="{{url('/photographer/login')}}" class="btn-lg btn btn-link">Back</a>
                                        </div>
                                        <div class="float-right">
                                            <button class="btn btn-primary btn-lg">Recover Password</button>
                                        </div>                                       
                                    </div>
                                </form>
                            </div>
                        </div>
                    <div class="text-center text-white opacity-8 mt-3">Copyright © Alboumi 2020. All rights reserved.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@include('admin.include.bottom')