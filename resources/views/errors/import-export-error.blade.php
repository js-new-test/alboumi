@if(!empty($dupArr))
    <div class="alert alert-danger margin-bottom-10">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
        @foreach($dupArr as $passerror)
            <i class="fa fa-times"></i> {{$passerror}}<br/>
        @endforeach
    </div>
@endif
@if(!empty($ColArr))
<div class="alert alert-danger margin-bottom-10">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
    @foreach($ColArr as $error)
        <i class="fa fa-times"></i> {{$error}}<br/>
    @endforeach
</div>
@endif
@if(!empty($GenArr))
<div class="alert alert-danger margin-bottom-10">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
    @foreach($GenArr as $error)
        <i class="fa fa-times"></i> {{$error}}<br/>
    @endforeach
</div>
@endif
@if(!empty($FileArr))
<div class="alert alert-danger margin-bottom-10">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
        <i class="fa fa-times"></i> {{$FileArr}}<br/>
</div>
@endif
@if(!empty($dataArr))
<div class="alert alert-danger margin-bottom-10">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
        <i class="fa fa-times"></i> {{$dataArr}}<br/>
</div>
@endif
@if(!empty($errorArr))
<div class="alert alert-danger margin-bottom-10">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
    @foreach($errorArr as $error)
        <i class="fa fa-times"></i> {{$error}}<br/>
    @endforeach
</div>
@endif
@if(!empty($passArr))
<div class="alert alert-danger margin-bottom-10">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
    @foreach($passArr as $passerror)
        <i class="fa fa-times"></i> {{$passerror}}<br/>
    @endforeach
</div>
@endif
@if(!empty($successArr))
    <div class="alert alert-success">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
        @foreach($successArr as $success)
            <i class="fa fa-check "></i> {{$success}}<br/>
        @endforeach
    </div>
@endif


