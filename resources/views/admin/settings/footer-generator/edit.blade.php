@extends('admin.layouts.master')
<title>Edit Footer Generator | Alboumi</title>

@section('content')
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-white closed-sidebar">
    @include('admin.include.header')    
	<div class="app-main">
        @include('admin.include.sidebar') 
        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title app-page-title-simple">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">                            
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="pe-7s-note2 opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Footer Generator</span>
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
                                                <a href="javascript:void(0);">Footer Generator</a>
                                            </li>  
                                            <li class="breadcrumb-item">
                                                <a href="{{url('admin/footer-generator')}}">Footer Generator List</a>
                                            </li>                                                                                      
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Edit Footer Generator  
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
                        <h5 class="card-title">Update Footer Generator</h5>                          
                        <form id="footerGeneratorForm" method="post" action="{{url('/admin/footer-generator/update')}}">
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />                                                    
                            <div class="row">
                                @if(isset($languages))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="language"><strong>Select Language<span style="color:red">*</span></strong></label>   
                                        <select name="language" id="language" class="multiselect-dropdown form-control">
                                            <optgroup label="Select Language">
                                                @foreach($languages as $lang)                                            
                                                    <option value="{{$lang->id}}" {{($lang->id == $footer_generator->language_id) ? 'selected' : '' }}>{{$lang->langEN}}</option>                                            
                                                @endforeach                                            
                                            </optgroup>
                                        </select>                                                                                       
                                    </div>
                                </div>
                                @else   
                                    @if(isset($language))
                                    <div class="form-group">                                        
                                        <input type="hidden" name="language" id="language" value="{{$language->id}}">                                                                                     
                                    </div>
                                    @endif
                                @endif
                                <input type="hidden" name="foot_gen_id" id="foot_gen_id" value="{{ $footer_generator->id }}" />                                                    
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="footer_group"><strong>Footer Group<span style="color:red">*</span></strong></label>   
                                        <input type="text" name="footer_group" id="footer_group" class="form-control" value="{{$footer_generator->footer_group}}">                                                                                     
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="footer_sort_order"><strong>Sort Order<span style="color:red">*</span></strong></label>
                                        <input type="number" name="footer_sort_order" id="footer_sort_order" class="form-control" value="{{$footer_generator->sort_order}}">                                                                                     
                                    </div> 
                                </div>
                            </div>                            
        
                            <label class="font-weight-bold">Footer Links Section<span style="color:red">*</span></label>
                            <?php $counter = 0; ?>
                            @foreach($footer_link_section as $footer_link)                                                                
                                <div class="row remove_current_div">
                                    <div class="form-group">
                                        <div>
                                            <input type="hidden" name="common_arr[]" value="{{$footer_link->id}}">
                                        </div>
                                    </div>                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div>
                                            <label for="footer_name_required" class="font-weight-bold">Name<span style="color:red">*</span></label>
                                            <input type="text" name="common_arr[]" id="footer_name_required<?php echo $counter; ?>" data-inc-val="<?php echo $counter; ?>" class="form-control footer_name_sec" value="{{$footer_link->name}}">
                                            <span id="footer_name_error<?php echo $counter; ?>"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div>
                                                <label for="footer_link_required" class="font-weight-bold">Link<span style="color:red">*</span></label>
                                                <input type="text" name="common_arr[]" id="footer_link_required<?php echo $counter; ?>" data-inc-val="<?php echo $counter; ?>" class="form-control footer_link_sec" value="{{$footer_link->link}}">
                                                <span id="footer_link_error<?php echo $counter; ?>"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div><label for="footer_st_required" class="font-weight-bold">Sort Order<span style="color:red">*</span></label>
                                            <input type="number" name="common_arr[]" id="footer_st_required<?php echo $counter; ?>" data-inc-val="<?php echo $counter; ?>" class="form-control footer_st_sec" value="{{$footer_link->sort_order}}">
                                            <span id="footer_st_error<?php echo $counter; ?>"></span>
                                            </div>
                                        </div>
                                    </div> 
                                    <!-- <input type="hidden" name="common_arr[]" id="footer_st_id" value="{{$footer_link->id}}"> -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div style="margin-top: 38px;">
                                                <button type="button" class="mb-2 mr-2 btn-icon btn-icon-only btn-square btn btn-primary delete_footer_links_section" data-f-link-s-id="{{$footer_link->id}}"><i class="fa fa-fw" aria-hidden="true" title="Copy to use close"></i></button>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                                <?php $counter++; ?>
                            @endforeach                                                        
                                                                                    
                            <div id="dynamic_footer_gen_link_textbox"></div>
                            <div style="margin-bottom: 30px;">
                                <button type="button" class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="edit_footer_gen_link_section"><i class="fa fa-plus btn-icon-wrapper"> </i>Add</button>
                            </div>                                                                                                                                                                                                                                                                                    
                                                                                                                
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="update_footer" id="update_footer">Update Footer</button>
                                <a href="{{url('/admin/footer-generator')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                            </div>
                        </form>                
                    </div>
                </div>                                              
            </div>
            @include('admin.include.footer')
        </div>
    </div>  
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="footerGeneratorDeleteModel" tabindex="-1" role="dialog" aria-labelledby="footerGeneratorDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="footerGeneratorDeleteLabel">Confirmation</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="data_f_link_s_id" id="data_f_link_s_id">                    
                    <p class="mb-0">Are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeFooterGenModel" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="deleteFooterLinksSec">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->  
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/footer-generator/footer-generator.js')}}"></script>
<script>
$('.footer_name_error').blur(function(e) {    
    var textBox = $(this).val();
    if (textBox == "") {
        $("#footer_name-error").show();    
    }
    // e.preventDefault();
});
$('.footer_link_error').blur(function(e) {    
    var textBox = $(this).val();
    if (textBox == "") {
        $("#footer_link-error").show();    
    }
    // e.preventDefault();
});
$('.footer_st_error').blur(function(e) {    
    var textBox = $(this).val();
    if (textBox == "") {
        $("#footer_st-error").show();    
    }
    // e.preventDefault();
});
</script>
@endpush
