@extends('admin.layouts.master')
<title>Add Email Templates | Alboumi</title>

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
                                        <i class="pe-7s-config"></i>
                                    </span>
                                    <span class="d-inline-block">Multilanguage Email Templates</span>
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
                                                <a href="javascript:void(0);">Multilanguage Email Templates</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/emailTemplates/list')}}">Multilanguage Email
                                                    Templates List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Template
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
                        <h5 class="card-title">Create Template</h5>
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <form id="addEmailTemplateForm" class="col-md-10 mx-auto" method="post"
                            action="{{url('/admin/emailTemplates/addTemplate')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code" class="font-weight-bold">Code</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="code" name="code"
                                                placeholder="Enter Code" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title" class="font-weight-bold">Title</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="title" name="title"
                                                placeholder="Enter Title" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="variables" class="font-weight-bold">Variables</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="variables" name="variables"
                                                placeholder="{variable 1}, {variable 2}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active" class="font-weight-bold">Status
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <select name="is_active" id="is_active" class="form-control">
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-header card-header-tab-animation">
                                        <ul class="nav nav-justified" id="myTab">
                                            @foreach($total_languages as $lang)
                                            <li class="nav-item">
                                                <a data-toggle="tab" href="#tab_{{$lang->id}}"
                                                    class="nav-link font-weight-bold">{{ $lang->langEN }}
                                                    ({{ $lang->alpha2 }})
                                                    <span class="text-danger">*</span>
                                                </a>
                                            </li>

                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="card-body">
                                        <div class="tab-content">
                                            @foreach($total_languages as $lang)
                                            <div class="tab-pane tab_content" id="tab_{{$lang->id}}" role="tabpanel">
                                                <label class="font-weight-bold"> Subject <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control mail_subject"
                                                    name="subject[{{ $lang->id }}]" id="subject_{{ $lang->id }}"
                                                    placeholder="Please enter subject">

                                                <input type="hidden" name="lang_id[]" value="{{ $lang->id }}">
                                                <div>
                                                    <label class="font-weight-bold mt-2"> Template Body <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="value[{{ $lang->id }}]" id="value_{{ $lang->id }}"
                                                        type="text" class="form-control ckeditor multi_lang_ckeditor"></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div id="ck_error"></div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100"
                                                    id="addTemplate">Add
                                                    Template</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/emailTemplates/list') }}">
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
<script src="{{asset('public/assets/js/email_templates/email_templates.js')}}"></script>
<script type="text/javascript">
    var multi_lang_ckeditor_name = $('.multi_lang_ckeditor').attr('name');
    CKEDITOR.replace(multi_lang_ckeditor_name, {                
        filebrowserUploadUrl: "{{route('ckeditor.upload_event_template_image', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });		 
</script>
@endpush
