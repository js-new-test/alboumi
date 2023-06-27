@if(Request::path() != '/')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endif
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script>
<!-- <script src="https://unpkg.com/jquery@3.3.1/dist/jquery.slim.min.js"></script> -->
@if(Request::path() != '/')
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
@endif
<!-- <script src="https://unpkg.com/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js" data-src="https://unpkg.com/bootstrap@4.5.1/dist/js/bootstrap.min.js" ></script> -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/bootstrap-select@1.13.8/dist/js/bootstrap-select.min.js"></script>
<script src="{{asset('public/assets/frontend/js/owl.carousel.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/script.js')}}"></script>
<script src="{{asset('public/assets/js/vendors/form-components/form-validation.js')}}"></script>
@if(Request::path() != '/')
<script src="{{asset('public/assets/js/scripts-init/form-components/form-validation.js')}}"></script>
<script src="{{asset('/public/assets/custom/form-validation/role-form-validation.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/additional-methods.js"></script>
<script src="{{asset('public/assets/js/scripts-init/toastr.min.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>

<script src='https://www.google.com/recaptcha/api.js'></script>

<!-- Moment Js -->
<script src="{{asset('/public/assets/js/moment-js/moment.min.js')}}"></script>
<script src="{{asset('/public/assets/js/moment-js/moment-timezone.min.js')}}"></script>
<script src="{{asset('/public/assets/js/moment-js/moment-timezone-with-data-1970-2030.js')}}"></script>
@endif
