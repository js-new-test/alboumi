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
		<button class="fill-btn" onClick="return CloseMySelf();">Upload</button>
	</div>
</div>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
{{-- <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script> --}}
<script src="{{asset('public/assets/frontend/js/script.js')}}"></script>


<script type="text/javascript">
$(document).ready(function(){
  $(".fb-dropdowns").click(function(){
    $(this).toggleClass("active");
  })
  if($(".fb-dropdowns")){
    $(".fb-dropdowns").text($(".fb-dropdowns-toggles .active").text())
  }
})

function CloseMySelf() {
	var checkedArray = [];
	$("input:checkbox[name=selectedImages]:checked").each(function(){
	    checkedArray.push($(this).val());
	});
	console.log(checkedArray)
    window.opener.HandlePopupResult(checkedArray);
    window.close();
}
$(document).on('change','input.selectAll',function(){
	$(this).closest('.tab-pane').find('input.singleselect').prop('checked',$(this).prop('checked')).trigger('change');
})
$(document).on('change','input.singleselect',function(){
	// $selectionType!='single'
	var MULTIPLE_IMAGE_COUNT = '{{$maxUpload}}'
	var selectionType = '{{$selectionType}}'
	if(selectionType=='single'){
		if($(this).prop('checked')){
			$('input.singleselect').not(this).prop('checked', false);
		}
	}else{
		if($('input.singleselect:checked').not(this).length >= MULTIPLE_IMAGE_COUNT) {
	       	this.checked = false;
	   	}
	}
	var checkedLength = $('input.singleselect:checked').length;
	$('.selectedCounts').text(checkedLength);
});

$(document).on('click','.loadMore',function(){
    $(this).prop('disabled',true)
    var loadUrl = $(this).attr('nextURL');
    $.ajax({
        url:loadUrl,
        method:'GET',
        dataType:'json',
        success:function(resposnse){
            if(resposnse.data){
                var htm = "";
                $.each(resposnse.data, function( index, value ) {
					htm += '<div class="col-6 col-sm-4 col-md-4 col-lg-3">';
					htm += '<div class="fb-img-ck">';
					htm += '<label class="ck">';
					htm += '<input type="checkbox" name="selectedImages" class="singleselect" value="'+value.media_url+'">';
					htm += '<span class="checkmark"></span>';
					htm += '</label>';
					htm += '<img src="'+value.media_url+'">';
					htm += '</div>';
					htm += '</div>';
                });
                $('.appendImages').append(htm);
            }

            if(resposnse.paging.next){
                $('.loadMore').attr('nextURL',resposnse.paging.next).prop('disabled',false);
            }else{
                $('.showHideLoad').hide();
            }
        }
    })
})
</script>
</html>
