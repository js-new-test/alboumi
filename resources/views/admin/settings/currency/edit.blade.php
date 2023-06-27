@extends('admin.layouts.master')
<title>Add Currency | Alboumi</title>

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
                                        <i class="lnr-cog opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Currency</span>
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
                                                <a href="javascript:void(0);">Settings</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/currency/list')}}">Currency</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Edit Currency
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
                        <h5 class="card-title">Update Currency</h5>
                        <form id="addCurrency" class="col-md-6" method="post" action="{{url('admin/currency/update')}}">
                            @csrf
                            <input type="hidden" name="glb_curr_id" value="{{$glb_curr_id}}">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency">Currencies<span style="color:red">*</span></label>
                                    <select name="currency_id" id="currency_id" class="multiselect-dropdown form-control">
                                        <optgroup label="Select Currency">
                                            @foreach($currencies as $curr)
                                                @if($curr->currency_symbol != '' && $curr->currency_code != '')
                                                    <option value="{{$curr->id}}" {{($curr->id == $selected_currency->currency_id) ? 'selected' : ''}}>{{$curr->name}} - {{$curr->currency_symbol}}</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                          </div>
                      <div class="row">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="update_currency" value="update_currency">Update Currency</button>
                                <a href="{{url('/admin/currency/list')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
