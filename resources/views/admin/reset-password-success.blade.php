@extends('admin.layouts.master')

@section('content')
<div class="app-container app-theme-white body-tabs-shadow">
    <div class="app-container">
        <div class="h-100 bg-plum-plate bg-animation">
            <div class="d-flex h-100 justify-content-center align-items-center">
                <div class="mx-auto app-login-box col-md-8">
                    <div class="app-logo-inverse mx-auto mb-3"></div>
                        <div class="modal-dialog w-100" style="box-shadow: none">
                            <div class="text-center">
                                <h2>Congratulations!</h2>
                                
                                <h2>Your password has been changed successfully.</h2>                                   
                            </div>                                                        
                        </div>
                    <div class="text-center">
                        <!-- <a href="{{url('/photographer/login')}}" style="color:white"><h4>Back to login</h4></a> -->
                        <a href="{{url('/admin/login')}}">
                            <button class="mb-2 mr-2 btn-icon btn btn-light"><i class="fa fa-arrow-circle-left btn-icon-wrapper"> </i>Go to login</button>                            
                        </a>
                    </div>
                    
                    <div class="text-center text-white opacity-8 mt-3">Copyright © Alboumi 2020. All rights reserved.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@include('admin.include.bottom')