@extends('admin.layouts.master')
<title>Add Attribute Groups | Alboumi</title>

@section('content')
<script type="text/javascript">
	var language = <?php echo json_encode($language);?>;
</script>
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
                                        <i class="pe-7s-cart opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Attribute Group</span>
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
                                                <a href="javascript:void(0);">Attribute Groups</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/attributeGroup')}}">Attribute Group List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Attribute Group
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
                        <h5 class="card-title">{{ $formTitle }}</h5>

                        <form id="addAttriGroupForm" class="col-md-10 mx-auto" method="post"
                            action="{{url('/admin/attributeGroup/addAttributeGroup')}}">
                            @csrf

                            <div class="row">
                            @if(!empty($otherLanguages))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="default_lang">Language :</label>
                                            </div>
                                            @if($page != 'anotherLanguage')
                                            <div class="col-md-5">
                                                <label for="default_lang"> {{ $defaultLanguage }} </label>
                                                <input type="hidden" name="defaultLanguageId" value="{{ $defaultLanguageId }}">
                                            </div>
                                            @else
                                            <div class="col-md-5">
                                                <input type="hidden" name="groupId" id="groupId" value="{{$groupId}}">
                                                <select class="form-control multiselect-dropdown" name="defaultLanguage" id="defaultLanguage"></select>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <input type="hidden" name="defaultLanguageId" value="{{ $defaultLanguageId }}">
                                @endif
                                @if($page != 'anotherLanguage')
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_ids">Category</label>
                                            <select class="select2 form-control multiselect-dropdown"
                                                name="category_ids[]" id="category_ids" multiple>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="display_name">Display Name</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="display_name"
                                                name="display_name" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="name" name="name" />

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($page != 'anotherLanguage')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <span class="text-danger">*</span>

                                        <div>
                                            <input type="number" class="form-control" id="sort_order"
                                                name="sort_order" />

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label> <span class="text-danger">*</span>
                                        <div>
                                            <select class="form-control multiselect-dropdown" name="status" id="status">
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
	                        				</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="attribute_type_id">Attribute Type</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <select class="form-control multiselect-dropdown" name="attribute_type_id" id="attribute_type_id">
                                                @foreach($attributeTypes as $attribute_type)
	                                                    <option value="{{ $attribute_type->id }}">
	                                                        {{ $attribute_type->name}}</option>																									
                                                @endforeach
	                        				</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100">Add
                                                    Group</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/attributeGroup') }}">
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
<script src="{{asset('public/assets/js/attribute/add_attr_group.js')}}"></script>
@endpush
