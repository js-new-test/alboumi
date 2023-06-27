@extends('admin.layouts.master')

@section('content')
<div class="app-container app-theme-white body-tabs-shadow">
    <div class="app-container">
        <div class="h-100 bg-plum-plate bg-animation">
            <div class="d-flex h-100 justify-content-center align-items-center">
                <div class="mx-auto app-login-box col-md-8">
                    <!-- <div class="app-logo-inverse mx-auto mb-3"></div> -->
                        <div class="modal-dialog w-100" style="box-shadow: none">
                            <div class="text-center" style="font-size: 150px;">
                                <p>403</p>
                            </div>
                            <div class="text-center">
                                <h2>Access Denied</h2>
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

