@extends('admin.layouts.master')
<title>Add General Settings | Alboumi</title>
<style>
    textarea.form-control {
        height: 150px !important;
    }
</style>
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
                                    <span class="d-inline-block">General Settings</span>
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
                                                <a href="javascript:void(0);">General Settings</a>
                                            </li>

                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add General Settings
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
                        <h5 class="card-title">Add General Settings</h5>
                        <ul class="nav nav-tabs">

                            <li class="nav-item"><a data-toggle="tab" href="#footer_details"
                                    class="active nav-link">Contact
                                    Details</a></li>
                            <li class="nav-item"><a data-toggle="tab" href="#social_links" class="nav-link">Social Media
                                    Links</a></li>
                                    <li class="nav-item"><a data-toggle="tab" href="#home_page_components" class="nav-link">Home Page Components</a></li>
                            <li class="nav-item"><a data-toggle="tab" href="#home_page_mobile_app" class="nav-link">Home Page Mobile App</a></li>
                            <li class="nav-item"><a data-toggle="tab" href="#shipping_cost_tab" class="nav-link">Shipping</a></li>
                            <li class="nav-item"><a data-toggle="tab" href="#aramex_configuration" class="nav-link">Aramex Configuration</a></li>
                            <li class="nav-item"><a data-toggle="tab" href="#admin_emails" class="nav-link">Admin Email</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="footer_details" role="tabpanel">
                                @if(!isset($footerData))
                                <form id="addFooterDetailsForm" class="col-md-10 mx-auto" method="post"
                                    action="{{ url('admin/updateFooterDetails') }}">
                                    @csrf
                                    <input type="hidden" name="page_name" value = "footer_details">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card-header card-header-tab-animation">
                                                <ul class="nav nav-justified" id="footerTab">
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
                                                    <div class="tab-pane tab_content" id="tab_{{$lang->id}}"
                                                        role="tabpanel">
                                                        <input type="hidden" name="lang_id[]" value="{{ $lang->id }}">

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="font-weight-bold"> About <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea type="text" class="form-control footer_about"
                                                                    name="about_us[{{ $lang->id }}]"
                                                                    id="about_us{{ $lang->id }}"
                                                                    placeholder="Please write content here"></textarea>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="font-weight-bold mt-2"> Email ID <span
                                                                            class="text-danger">*</span></label>
                                                                    <input name="contact_email[{{ $lang->id }}]"
                                                                        id="contact_email{{ $lang->id }}" type="email"
                                                                        class="form-control contact_email"
                                                                        placeholder="Please enter email address">
                                                                </div>
                                                                <div class="row">
                                                                    <label class="font-weight-bold mt-2"> Contact Number
                                                                        <span class="text-danger">*</span></label>
                                                                    <input name="contact_number[{{ $lang->id }}]"
                                                                        id="contact_number{{ $lang->id }}" type="number"
                                                                        class="form-control contact_number" pattern=".{8,}" title = "Contact number must be 8 digits"
                                                                        placeholder="Please enter contact number">
                                                                </div>
                                                                <div class="row">
                                                                    <label class="font-weight-bold mt-2"> Whatsapp Number
                                                                        <span class="text-danger">*</span></label>
                                                                    <input name="whatsapp_number[{{ $lang->id }}]"
                                                                        id="whatsapp_number{{ $lang->id }}" type="number"
                                                                        class="form-control whatsapp_number" pattern=".{8,}" title = "Whatsapp number must be 8 digits"
                                                                        placeholder="Please enter whatsapp number">
                                                                </div>
                                                            </div>
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
                                                    <div class="col-md-2 offset-md-5">
                                                        <button type="submit" class="btn btn-primary btn-shadow w-100"
                                                            id="addFooterDetails">Add
                                                            Details</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @else
                                <form id="updateFooterDetailsForm" class="col-md-10 mx-auto" method="post"
                                    action="{{ url('admin/updateFooterDetails') }}">
                                    @csrf
                                    <input type="hidden" name="page_name" value="footer_details">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card-header card-header-tab-animation">
                                                <ul class="nav nav-justified" id="footerTab">
                                                    @foreach($total_languages as $lang)
                                                    <li class="nav-item">
                                                        <a data-toggle="tab" href="#tab_{{$lang->id}}"
                                                            class="nav-link">{{ $lang->langEN }} ({{ $lang->alpha2 }})
                                                            <span class="text-danger">*</span>
                                                        </a>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <div class="card-body">
                                                <div class="tab-content">
                                                    @foreach($total_languages as $lang)
                                                    <div class="tab-pane tab_content" id="tab_{{$lang->id}}"
                                                        role="tabpanel">
                                                        <input type="hidden" name="lang_id[]" value="{{ $lang->id }}">

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="font-weight-bold"> About <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea type="text" class="form-control update_footer_about" name="about_us[{{ $lang->id }}]" id="about_us{{ $lang->id }}" placeholder="Please write content here">@foreach($footerData as $detail){{ $lang->id == $detail->language_id ? $detail->about_us : '' }}@endforeach</textarea>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="font-weight-bold mt-2"> Email ID <span
                                                                            class="text-danger">*</span></label>
                                                                    <input name="contact_email[{{ $lang->id }}]" id="contact_email{{ $lang->id }}" type="email" class="form-control update_contact_email" placeholder="Please enter email address" value = "@foreach($footerData as $detail){{ $lang->id == $detail->language_id ? $detail->contact_email : '' }}@endforeach">
                                                                </div>
                                                                <div class="row">
                                                                    <label class="font-weight-bold mt-2"> Contact Number
                                                                        <span class="text-danger">*</span></label>
                                                                    <input name="contact_number[{{ $lang->id }}]" id="contact_number{{ $lang->id }}" type="text" class="form-control update_contact_number" pattern=".{8,}" title = "Contact number must be a valid number" placeholder="Please enter contact number" value ="@foreach($footerData as $detail){{ $lang->id == $detail->language_id ? $detail->contact_number : ''}}@endforeach" />
                                                                </div>
                                                                <div class="row">
                                                                    <label class="font-weight-bold mt-2"> Whatsapp Number
                                                                        <span class="text-danger">*</span></label>
                                                                    <input name="whatsapp_number[{{ $lang->id }}]" id="whatsapp_number{{ $lang->id }}" type="text" class="form-control update_whatsapp_number" pattern=".{8,}" title = "Whatsapp number must be a valid number" placeholder="Please enter whatsapp number" value ="@foreach($footerData as $detail){{ $lang->id == $detail->language_id ? $detail->whatsapp_number : ''}}@endforeach" />
                                                                </div>
                                                            </div>
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
                                                    <div class="col-md-2 offset-md-5">
                                                        <button type="submit" class="btn btn-primary btn-shadow w-100"
                                                            id="addFooterDetails">Update
                                                            Details</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @endif
                            </div>
                            <div class="tab-pane" id="social_links" role="tabpanel">
                                @if(!isset($social_links))
                                <form id="addSocialLinksForm" class="col-md-10 mx-auto" method="post"
                                    action="{{ url('admin/updateFooterDetails') }}">
                                    @csrf
                                    <input type="hidden" name="page_name" value = "social_links">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fb_link" class="font-weight-bold">Facebook Link</label>
                                                <div>
                                                    <input type="text" class="form-control" id="fb_link" name="fb_link"
                                                        placeholder="Enter FB Link" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="insta_link" class="font-weight-bold">Instagram Link</label>
                                                <div>
                                                    <input type="text" class="form-control" id="insta_link"
                                                        name="insta_link" placeholder="Enter Instagram Link" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="youtube_link" class="font-weight-bold">Youtube Link</label>
                                                <div>
                                                    <input type="text" class="form-control" id="youtube_link"
                                                        name="youtube_link" placeholder="Enter Youtube Link" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="twitter_link" class="font-weight-bold">Twitter Link</label>
                                                <div>
                                                    <input type="text" class="form-control" id="twitter_link"
                                                        name="twitter_link" placeholder="Enter Twitter Link" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-2 offset-md-5">
                                                        <button type="submit" class="btn btn-primary btn-shadow w-100"
                                                            id="addFooterDetails">Add
                                                            Links</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @else
                                <form id="addSocialLinksForm" class="col-md-10 mx-auto" method="post"
                                    action="{{ url('admin/updateFooterDetails') }}">
                                    @csrf
                                    <input type="hidden" name="page_name" value = "social_links">
                                    <input type="hidden" name="social_id" value = "{{ $social_links->id }}">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fb_link" class="font-weight-bold">Facebook Link</label>
                                                <div>
                                                    <input type="text" class="form-control" id="fb_link" name="fb_link"
                                                        placeholder="Enter FB Link" value = "{{ $social_links->fb_link }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="insta_link" class="font-weight-bold">Instagram Link</label>
                                                <div>
                                                    <input type="text" class="form-control" id="insta_link"
                                                        name="insta_link" placeholder="Enter Instagram Link" value = "{{ $social_links->insta_link }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="youtube_link" class="font-weight-bold">Youtube Link</label>
                                                <div>
                                                    <input type="text" class="form-control" id="youtube_link"
                                                        name="youtube_link" placeholder="Enter Youtube Link" value = "{{ $social_links->youtube_link }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="twitter_link" class="font-weight-bold">Twitter Link</label>
                                                <div>
                                                    <input type="text" class="form-control" id="twitter_link"
                                                        name="twitter_link" placeholder="Enter Twitter Link" value = "{{ $social_links->twitter_link }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-2 offset-md-5">
                                                        <button type="submit" class="btn btn-primary btn-shadow w-100"
                                                            id="addFooterDetails">Update
                                                            Links</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @endif
                            </div>
                            <div class="tab-pane" id="home_page_components" role="tabpanel">
                                <form id="">
                                    @csrf
                                    <input type="hidden" name="baseUrl" id="baseUrl" value="{{$baseUrl}}">
                                    @foreach($home_page_component as $component)
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for=""><strong>{{$component->section_name}}</strong></label>
                                        </div>
                                        @if($component->is_active == 1)
                                        <div class="col-sm-6">
                                            <div class="col-sm-5">
                                                <button type="button" data-id="{{$component->id}}" class="btn btn-sm btn-toggle active benner-component-is-active-switch" data-toggle="button" aria-pressed="true" autocomplete="off">
                                                <div class="handle"></div>
                                                </button>
                                            </div>
                                        </div>
                                        @else
                                        <div class="col-sm-6">
                                            <div class="col-sm-5">
                                                <button type="button" data-id="{{$component->id}}" class="btn btn-sm btn-toggle benner-component-is-active-switch" data-toggle="button" aria-pressed="false" autocomplete="off">
                                                <div class="handle"></div>
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </form>
                            </div>
                            <div class="tab-pane" id="home_page_mobile_app" role="tabpanel">
                                <form id="homePageMobileAppSection" method="POST" action="{{url('admin/add-home-page-mobile-app-image')}}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="baseUrl" id="baseUrl" value="{{$baseUrl}}">
                                    <table class="table" style="border:0;table-layout:fixed;" id="tblHomePageMobileApp">
                                    <tbody>
                                        @foreach($home_page_mobile_app as $component)
                                            <tr>
                                                <th>{{$component->name}}</th>
                                                <td>                                                   
                                                    <button type="button" data-name="{{$component->name}}" data-id="{{$component->id}}" class="btn btn-sm btn-toggle home-page-mobile-app-is-active-switch" data-toggle="button" aria-pressed="false" autocomplete="off">
                                                    <div class="handle"></div>
                                                    </button>                                                                                                            
                                                    <input type="hidden" name="home_page_mobile_app_image_height" id="home_page_mobile_app_image_height" value="{{config('app.home_page_mobile_app_image.height')}}">
                                                    <input type="hidden" name="home_page_mobile_app_image_width" id="home_page_mobile_app_image_width" value="{{config('app.home_page_mobile_app_image.width')}}">
                                                </td>
                                                @if($component->image)
                                                    <td><img height="50" width="50" src="{{asset('/public/assets/images/general-settings/mobile-app').'/'.$component->image}}" alt="" srcset=""></td>
                                                @endif
                                                <td>
                                                    <select name="event" class="form-control event">
                                                        @foreach($events as $event)
                                                            <option value="{{$event->id}}" {{($component->event_id == $event->id) ? 'selected' : ''}}>{{$event->event_name}}</option>                                            
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><div style="width: 100px;" class="dynamic_image_field"></div></td>                                                
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary" name="add_mobile_image" id="add_mobile_image">Add Mobile Image</button>
                                                <a href="{{url('/admin/footerDetails')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="shipping_cost_tab" role="tabpanel">
                                <form id="shippingCostForm" method="POST" action="{{url('admin/shipping-cost')}}">
                                    @csrf
                                    <input type="hidden" name="settings_id" id="settings_id" value="{{$settings->id}}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fb_link" class="font-weight-bold">Minimum Order Amount ({{$defaultCurr->currency_code}})<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" id="min_order_amt" name="min_order_amt" value="{{$settings->min_order_amt}}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="insta_link" class="font-weight-bold">Shipping Cost ({{$defaultCurr->currency_code}})<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" id="shipping_cost" name="shipping_cost" value="{{$settings->shipping_cost}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fb_link" class="font-weight-bold">Min Qty<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" id="min_qty" name="min_qty" value="{{$settings->min_qty}}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="insta_link" class="font-weight-bold">Delivery Day<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" id="delivery_days" name="delivery_days" value="{{$settings->delivery_days}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fb_link" class="font-weight-bold">For Exceeding Min Qty, Delivery Day(s)<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" id="delivery_days_exceed_min_qty" name="delivery_days_exceed_min_qty" value="{{$settings->delivery_days_exceed_min_qty}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary" name="add_shipping_cost" id="add_shipping_cost">Update Shipping Cost</button>
                                                <a href="{{url('/admin/footerDetails')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="aramex_configuration" role="tabpanel">
                                <form id="aramexShipping" method="POST" action="{{url('/admin/update-aramex-config')}}">
                                    @csrf
                                    <input type="hidden" name="aramex_cng_id" id="aramex_cng_id" value="{{$aramex_config->id}}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="contact_name" class="font-weight-bold">Contact Name<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="contact_name" name="contact_name" value="{{$aramex_config->contact_name}}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_name" class="font-weight-bold">Company Name<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{$aramex_config->company_name}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="line_1" class="font-weight-bold">Line 1<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="line_1" name="line_1" value="{{$aramex_config->line_1}}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="line_2" class="font-weight-bold">Line 2<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="line_2" name="line_2" value="{{$aramex_config->line_2}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="city" class="font-weight-bold">City<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="city" name="city" value="{{$aramex_config->city}}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="country_code" class="font-weight-bold">Country Code<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="country_code" name="country_code" value="{{$aramex_config->country_code}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone_ext" class="font-weight-bold">Phone Extension<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" id="phone_ext" name="phone_ext" value="{{$aramex_config->phone_extension}}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone_number" class="font-weight-bold">Phone Number<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" id="phone_number" name="phone_number" value="{{$aramex_config->phone}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="font-weight-bold">Email Address<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="email" name="email" value="{{$aramex_config->email}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary" name="add_shipping_config" id="add_shipping_config">Save</button>
                                                <a href="{{url('/admin/footerDetails')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="admin_emails" role="tabpanel">
                                <form id="adminMultipleEmails" method="POST" action="{{url('/admin/add-update-admin-email')}}">
                                    @csrf
                                    <input type="hidden" name="settings_id" id="settings_id" value="{{$settings->id}}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="admin_emails" class="font-weight-bold">Email<span class="text-danger">*</span></label>
                                                <input placeholder="example1@gmail.com, example2@gmail.com" class="form-control" id="admin_emails" name="admin_emails" value="{{$settings->email}}"/>
                                                <span id="admin_email_error"></span>
                                            </div>
                                        </div>                                    
                                    </div>                                                                        
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary" name="add_admin_emails" id="add_admin_emails">Save</button>
                                                <a href="{{url('/admin/footerDetails')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('admin.include.footer')
        </div>
    </div>
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="homePageComponentModel" tabindex="-1" role="dialog" aria-labelledby="homePageComponentModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="homePageComponentModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="home_page_com_id" id="home_page_com_id">
                    <input type="hidden" name="home_page_act_deact" id="home_page_act_deact">
                    <p class="mb-0" id="message">Are you sure ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="actDeactHomePageComponent">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="homePageMobileAppModel" tabindex="-1" role="dialog" aria-labelledby="homePageMobileAppModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="homePageMobileAppModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="home_page_ma_id" id="home_page_ma_id">
                    <input type="hidden" name="home_page_ma_act_deact" id="home_page_ma_act_deact">
                    <p class="mb-0" id="message">Are you sure ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="actDeactHomePageMobileApp">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/settings/footerDetails.js')}}"></script>
@endpush
