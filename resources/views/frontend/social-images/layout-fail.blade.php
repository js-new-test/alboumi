<!DOCTYPE html>
<html>
<head>
	<title>Select photo from facebook</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  	<link rel="stylesheet" href="{{asset('public/assets/frontend/css/owl.carousel.min.css')}}">
<link rel="stylesheet" href="{{asset('public/assets/frontend/css/owl.theme.default.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/frontend/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/frontend/css/responsive.css')}}">

</head>
<body>
<style type="text/css">
	.fb-dropdowns-toggles{
		display: block;
	}
</style>

<div class="spff-header">
	{{-- <a href="">Change Facebook Account</a> --}}
	<h5>Select Photos from {{$platform}}</h5>
</div>

{{-- <div class="new-exit-album">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 col-lg-6">
				<div class="new-album">
					<label class="rd">New Album
					  <input type="radio" checked="checked" name="pm">
					  <span class="rd-checkmark"></span>
					</label>
					<input type="text" placeholder="Album Name" class="input" name="">
				</div>
			</div>
			<div class="col-md-12 col-lg-6">
				<div class="exit-album">
					<label class="rd">Existing Album
					  <input type="radio" checked="checked" name="pm">
					  <span class="rd-checkmark"></span>
					</label>
					<select class="select">
						<option>My Family</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div> --}}



@yield('content')

<div class="fb-footer">
	{{-- <label class="ck">Log out of Facebook when done
	  <input type="checkbox">
	  <span class="checkmark"></span>
	</label> --}}
	<div class="fb-btn-group">
		<button onClick="javascript:window.close();" class="border-btn cancelBtn">Cancel</button>
		{{-- <button class="fill-btn" onClick="return CloseMySelf();">Upload</button> --}}
	</div>
</div>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
{{-- <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script> --}}
<script src="{{asset('public/assets/frontend/js/script.js')}}"></script>
</html>