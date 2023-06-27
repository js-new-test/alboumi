@extends('admin.layouts.master')
<title>Update Package</title>

@section('content')
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-gray closed-sidebar">
    @include('admin.include.header')
    <div class="app-main">
        @include('admin.include.sidebar')
        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="lnr-cog opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Packages</span>
                                </div>
                                <div class="page-title-subheading opacity-10">
                                    <nav class="" aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a>
                                                    <i aria-hidden="true" class="fa fa-home"></i>
                                                </a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="javascript:void(0);">Packages</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/package/list')}}">Package List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Edit Package
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Update Package</h5>
                        <form id="updatePackageForm" class="col-md-10 mx-auto" method="post"
                            action="{{ url('admin/package/updatePackage') }}">
                            @csrf

                            <input type="hidden" name="package_id" value="{{ $package->id }}">

                            <div class="row">
                                @if(isset($global_languages))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language"><strong>Select Language<span style="color:red">*</span></strong></label>   
                                        <select name="language" id="language" class="multiselect-dropdown form-control">
                                            <optgroup label="Select Language">
                                                @foreach($global_languages as $lang)                                            
                                                    <option value="{{$lang->gl_id}}" {{($package->language_id == $lang->gl_id) ? 'selected' : ''}}>{{$lang->lang_name}}</option>                                            
                                                @endforeach                                            
                                            </optgroup>
                                        </select>                                                                                       
                                    </div>
                                </div>
                                @else
                                <div class="form-group">                                    
                                    <input type="hidden" id="language" name="language" value="{{ $global_language->gl_id }}">
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="event_id" class="font-weight-bold">Event Name</label>
                                        <div>
                                            <select class="js-states browser-default form-control w-100" name="event_id"
                                                id="event_id" parsley-required="true" disabled>
                                                <option value="" disabled selected>Select Event</option>
                                                @foreach($events as $event)
                                                <option value="{{ $event->id }}"
                                                    {{ $package->event_id == $event->id ? 'selected' : '' }}>
                                                    {{ $event->event_name}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="event_id" value="{{ $package->event_id }}">                                
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="package_name" class="font-weight-bold">Package Name</label>
                                        <div>
                                            <input type="text" class="form-control" id="package_name"
                                                name="package_name" placeholder="Enter Package Name"
                                                value="{{ $package->package_name }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price" class="font-weight-bold">Price ({{ $default_currency->currency_code }})</label>
                                        <div>
                                            <input type="text" class="form-control" id="price" name="price"
                                                placeholder="Enter Price" value="{{ $package->price }}" />
                                        </div>
                                    </div>
                                </div>                                
                            </div>

                            <div class="row">
                                {{--<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active" class="font-weight-bold">Status
                                            <span class="required">*</span>
                                        </label>
                                        <div>
                                            <select name="is_active" id="is_active" class="form-control">
                                                <option value="1" {{ ( $package->is_active == 1 ) ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="0" {{ ( $package->is_active == 0 ) ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>--}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discounted_price" class="font-weight-bold">Discounted Price ({{ $default_currency->currency_code }})</label>
                                        <div>
                                            <input type="text" class="form-control" id="discounted_price"
                                                name="discounted_price" placeholder="Enter Discounted Price"
                                                value="{{ $package->discounted_price }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">                                        
                                        <label for="sort_order" class="font-weight-bold">Sort Order</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ $package->sort_order }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="other_details" class="font-weight-bold">Other Details
                                            <span class="required">*</span>
                                        </label>
                                        <div>
                                            <textarea name="other_details" id="other_details" type="text"
                                                class="form-control ckeditor"> {{ $package->other_details }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <label for="event_desc" class="font-weight-bold">Package Details</label>
                            <span class="text-danger">*</span>                            
                            <table class="table table-borderless">
                                @if($package_features_count > 0)
                                    @foreach($package_features_arr as $v)
                                        <tr>                            
                                            <th scope="row">{{$v['feature_name']}}</th>
                                            @php $pckg_val = $v['package_value'] ? $v['package_value'] : ''; @endphp
                                            <td style="width:50%"><input required name="feature_value[{{$v['package_id']}}]" id="dynamic_feature_value_id{{$v['package_id']}}" value="{{$pckg_val}}" class="form-control dynamic_feature_value"><span id="dynamic_feature_value_error{{$v['package_id']}}"></span></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100"
                                                    id="edit_pkg_btn">Update Package</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/package/list') }}">
                                                    <button type="button" class="btn btn-light btn-shadow w-100"
                                                        name="cancel" value="Cancel">Cancel</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            @include('admin.include.footer')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('public/assets/js/packages/package.js')}}"></script>
<script>
    let photographer_gender = '<?php echo $package->photographer_gender ; ?>'
    let videographer_gender = '<?php echo $package->videographer_gender ; ?>'
    let is_album_included = '<?php echo $package->is_album_included ; ?>'
    let page_name = '<?php echo $page_name ; ?>'

</script>
<script>
$(document).ready(function(){    
    // var package_features_count = '<?php //echo count(json_decode($package_features, true)); ?>'
    /*var package_features_count = '<?php //echo $package_features_count; ?>'    
    if(package_features_count > 0)
    {        
        var js_data = '<?php //echo json_encode($package_features_arr); ?>';
        s = js_data.replace(/[\u0000-\u0019]+/g,""); 
        var js_obj_data = JSON.parse(s);
        console.log(js_obj_data);    
        var output = '';
        var counter = 1;
        $.each(js_obj_data, function(i, v){                        
            // output += '<tbody>'
                output += '<tr>'                            
                    output += '<th scope="row">'+v.feature_name+'</th>'
                    var pckg_val = v.package_value ? v.package_value : '';
                    output += '<td style="width:50%"><input required name="feature_value['+v.package_id+']" id="dynamic_feature_value_id'+v.package_id+'" value="'+pckg_val+'" class="form-control dynamic_feature_value"><span id="dynamic_feature_value_error'+v.package_id+'"></span></td>'
                output += '</tr>'
            // output += '</tbody>'                            
        })                    
        $('#add_dynamic_event_feature').append(output);
    }*/
})
</script>
@endpush
