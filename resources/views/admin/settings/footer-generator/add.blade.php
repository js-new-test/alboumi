@extends('admin.layouts.master')
<title>Add Footer Generator | Alboumi</title>

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
                                                Add Footer Generator  
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
                        <h5 class="card-title">Add Footer Generator</h5>                          
                        <form id="footerGeneratorForm" method="post" action="{{url('/admin/footer-generator/add')}}">
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />                                                    
                            <div class="row">
                                @if(isset($languages))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="language"><strong>Select Language<span style="color:red">*</span></strong></label>   
                                        <select name="language" id="language" class="multiselect-dropdown form-control">
                                            <optgroup label="Select Language">
                                                @foreach($languages as $lang)                                            
                                                    <option value="{{$lang->id}}">{{$lang->langEN}}</option>                                            
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="footer_group"><strong>Footer Group<span style="color:red">*</span></strong></label>   
                                        <input type="text" name="footer_group" id="footer_group" class="form-control">                                                                                     
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="footer_sort_order"><strong>Sort Order<span style="color:red">*</span></strong></label>
                                        <input type="number" name="footer_sort_order" id="footer_sort_order" class="form-control">                                                                                     
                                    </div> 
                                </div>
                            </div>                            
        
                            <label class="font-weight-bold">Footer Links Section<span style="color:red">*</span></label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div>
                                        <label for="footer_name_required" class="font-weight-bold">Name<span style="color:red">*</span></label>
                                        <input type="text" name="add_common_arr[]" id="footer_name_required0" class="form-control footer_name_sec">
                                        <span id="footer_name_error0"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div>
                                            <label for="footer_link_required" class="font-weight-bold">Link<span style="color:red">*</span></label>
                                            <input type="text" name="add_common_arr[]" id="footer_link_required0" class="form-control footer_link_sec">
                                            <span id="footer_link_error0"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div><label for="footer_st_required" class="font-weight-bold">Sort Order<span style="color:red">*</span></label>
                                        <input type="number" name="add_common_arr[]" id="footer_st_required0" class="form-control footer_st_sec">
                                        <span id="footer_st_error0"></span>
                                        </div>
                                    </div>
                                </div>                                
                            </div>                            
                                                                                    
                            <div id="dynamic_footer_gen_link_textbox"></div>
                            <div style="margin-bottom: 30px;">
                                <button type="button" class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="add_footer_gen_link_section"><i class="fa fa-plus btn-icon-wrapper"> </i>Add</button>
                            </div>                                                                                                                                                                                                                                                                                    
                                                                                                                
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="add_footer" id="add_footer">Add Footer</button>
                                <a href="{{url('/admin/footer-generator')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                            </div>
                        </form>  
                        <!-- <form id="myform">
                            <input type="text" name="field_1" class="num"/>
                            <br/>
                            <input type="text" name="field_2" class="num"/>
                            <br/>
                            <input type="text" name="field_3" class="num"/>
                            <br/>
                            <input type="submit" />
                        </form>               -->
                    </div>
                </div>                                              
            </div>
            @include('admin.include.footer')
        </div>
    </div>    
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/footer-generator/footer-generator.js')}}"></script>
<script>

// $('.footer_common_error').rules("add", { 
//     required:true,
//     messages:{required:'This field is required'}
// });
// $('.footer_common_error').each(function() {
//     // var $this = $(this);
//     // $this.removeClass('alphanumeric_dash');
//     $(this).rules('add', {
//         required:true,
//         messages:{required:'This field is required'}
//     });
// });
</script>
@endpush
