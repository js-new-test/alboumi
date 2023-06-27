@extends('admin.layouts.master')
<title>Update Attribute Groups | Alboumi</title>

@section('content')
<script type="text/javascript">
	var nonDefaultLanguage = <?php echo json_encode($nonDefaultLanguage);?>;
    var defaultLanguageId = <?php echo json_encode($defaultLanguageId);?>;
    var attr_group = <?php echo json_encode($attr_group);?>;
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
                                                Edit Attribute Group
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
                        <h5 class="card-title">Update Attribute Group</h5>

                        <form id="updateAttriGroupForm" class="col-md-10 mx-auto" method="post"
                        action="{{url('/admin/attributeGroup/updateAttributeGroup')}}">
                        @csrf
                            <input type="hidden" name="id" value="{{ $attr_group['id'] }}">
                            <input type="hidden" name="category_id" id="categoryId">

                            <div class="row">
                                @if(!empty($otherLanguages))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="default_lang">Language :</label>
                                            </div>
                                            <div class="col-md-5">
                                                <select class="form-control multiselect-dropdown" name="language_id" id="defaultLanguage"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <input type="hidden" name="language_id" value="{{ $defaultLanguageId }}">
                                @endif
                                <div class="col-md-6 commonElement">
                                    <div class="form-group">
                                        <?php $selected = explode(",", $attr_group['category_ids']); ?>

                                        <label for="category_ids">Category</label>
                                        <select class="select2 form-control multiselect-dropdown" name="category_ids[]" id="category_ids" multiple>
                                            @foreach($allCategories as $category)
                                                <option value="{{ $category['id'] }}" {{ (in_array($category['id'], $selected)) ? 'selected' : '' }}>{{ $category['category']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="display_name">Display Name</label>
                                        <span class="text-danger">*</span>

                                        <div>
                                            <input type="text" class="form-control" id="display_name" name="display_name" value="{{ $attr_group['display_name']}}"/>
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <span class="text-danger">*</span>

                                        <div>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ $attr_group['name']}}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row commonElement">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ $attr_group['sort_order']}}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="font-weight-bold">Status
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" {{ ( $attr_group['status'] == 1 ) ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ ( $attr_group['status'] == 0 ) ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row commonElement">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="attribute_type_id">Attribute Type</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <select class="form-control multiselect-dropdown" name="attribute_type_id" id="attribute_type_id">
                                                @foreach($attributeTypes as $attribute_type)
                                                    <option value="{{ $attribute_type->id }}"
                                                    {{ $attr_group['attribute_type_id'] == $attribute_type->id  ? 'selected' : ''}}>  {{ $attribute_type->name}}</option>
																								  
                                                @endforeach
	                        				</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100" name="update_role"
                                                value="update_role">Update Group</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/attributeGroup') }}">
                                                    <button type="button" class="btn btn-light btn-shadow w-100" name="cancel"
                                                    value="Cancel">Cancel</button>
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
<script src="{{asset('public/assets/js/attribute/edit_attr_group.js')}}"></script>
@endpush
