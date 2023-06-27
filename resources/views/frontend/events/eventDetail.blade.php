@extends('frontend.layouts.master')

@php
	$seoDesc = $cmsPageDetails->seo_description.'-'.$eventData->event_name;
	if($seoDesc == null)
		$seoDesc = $eventData->event_name;

	$seoKeywords = $cmsPageDetails->seo_keyword.'-'.$eventData->event_name;
	if($seoKeywords == null)
		$seoKeywords = $eventData->event_name;
@endphp

@section('description', $seoDesc )
@section('keywords', $seoKeywords )

@section('content')
@if(Auth::guard('customer')->check())
    <input type="hidden" name="flagLoggedIn" id="flagLoggedIn" value="1">
@else
    <input type="hidden" name="flagLoggedIn" id="flagLoggedIn" value="0">
@endif
<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var fullNameError = <?php echo json_encode($eventDetailLabels['FULLNAMEREQ'] ); ?>;
    var emailError = <?php echo json_encode($eventDetailLabels['EMAILREQ'] ); ?>;
    var eventDateError = <?php echo json_encode($eventDetailLabels['EVEDATEREQ'] ); ?>;
    var eventTimeError = <?php echo json_encode($eventDetailLabels['EVETIMEREQ'] ); ?>;
    var langVisibility = <?php echo json_encode($langVisibility->visibility); ?>;
    var eventId = <?php echo json_encode($eventData->id); ?>;
</script>

<section class="wedding-banner">
  
    @if(empty($eventData->banner_image))
    <img src="{{asset('public/assets/frontend/img/Wedding.jpg')}}" class="desktop-img">
    @else
        <img src="{{asset('public/assets/images/events/banner/'.$eventData->banner_image)}}" class="desktop-img">
    @endif
    @if(empty($eventData->mobile_banner_image))
    <img src="{{asset('public/assets/frontend/img/MAboutUsBanner.jpg')}}" class="mobile-img">
    @else
        <img src="{{asset('public/assets/images/events/mobile_banner/'.$eventData->mobile_banner_image)}}" class="mobile-img">
    @endif
    {{--<div class="overlay">
        <div class="container">
            <div class="w808">
                <h3>@if(!empty($eventData->event_name)) {{ $eventData['event_name'] }} @endif</h3>
                <p class="m-0">@if(!empty($eventData->event_desc)) {!! $eventData['event_desc'] !!} @endif</p>
            </div>

        </div>
    </div>--}}
</section>

<section class="amazing-packages">
    <div class="container">
        @if($eventData['children']->isEmpty())
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h4>{{ $eventDetailLabels['NOPKGAVAILABLE'] }}</h4>
            </div>
        </div>
        @else
        <div class="text-center">
            <h5 class="blurColor">{{ $eventDetailLabels['CHOOSEFROMOUR'] }}</h5>
            <h4>{{ $eventDetailLabels['AMAZINGPKG'] }}</h4>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="package-tbl">
                    <table id="pkgWithFeatureTable">
                        <thead>
                            <tr>
                                <th>
                                    <div class="left-top-header">
                                        <h5>{{ $eventDetailLabels['SENDENQ'] }}</h5>
                                        <a>{{ $eventDetailLabels['GETSTARTED'] }}</a>
                                    </div>
                                </th>
                                @if(!empty($eventData['children']))
                                    @foreach($eventData['children'] as $pkg)
                                    <th id="{{ $pkg['id'] }}">
                                        <p class="s1">{{ $pkg->package_name }}</p>
                                        <h6>{{ $currencyCode }}
                                            @if(empty($pkg->discounted_price))
                                                {{ number_format($pkg->price * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}

                                            @else
                                                {{ number_format($pkg->discounted_price * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}
                                            @endif
                                        </h6>
                                        <button class="border-btn choose-plan" onclick="showEnqForm({{ $pkg->id }},'{{$pkg->package_name}}')">{{ $eventDetailLabels['CHOOSEPLAN'] }}</button>
                                    </th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($featureValues as $value => $key)
                            <tr>
                                <th>{{ $key['featureName'] }}</th>
                                @for($i = 0; $i < count($key['featureValue']); $i++) 
                                <td id="{{ $key['featureValue'][$i]['pkg_id'] }}">
                                    <div>{{ $key['featureValue'][$i]['featurePackageData'] }}</div>
                                </td>
                                    @endfor
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                <!-- mobile view -->

                <div class="mobile-choose-plan">
                    <div class="row">
                        @if(!empty($mobilePackageValues['package']))
						@foreach($mobilePackageValues['package'] as $pkg)
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="package-box">
                                <input type="radio" name="package-ck" class="package-ck">
                                <div class="select-package-border" id="{{ $pkg['id'] }}">
                                    <h6>{{ $pkg['pkgName'] }}</h6>
                                    <h5>{{ $currencyCode }}
										@if(empty($pkg['pkgDiscountedPrice']))
                                            {{ number_format($pkg['pkgPrice'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}

                                        @else
                                            {{ number_format($pkg['pkgDiscountedPrice'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}
										@endif
									</h5>

                                    <div class="dividers"></div>

									@foreach($pkg['featureValues'] as $feature)
                                    <div class="name-value">
                                        <p>{{ $feature['featureName'] }} <b> {{ $feature['featurePackageData'] }} </b></p>
                                    </div>
                                    @endforeach
                                    <button class="fill-btn c-plan" onclick="showEnqForm({{ $pkg['id'] }},'{{$pkg['pkgName']}}')">{{ $eventDetailLabels['CHOOSEPLAN'] }}</button>
                                </div>
                            </div>
						</div>
                        @endforeach
                        @else
                        <h2>{{ $eventDetailLabels['NOPKGAVAILABLE'] }}</h2>
                        @endif
					</div>
                </div>
                <!-- mobile view -->
            </div>
        </div>
        @endif
    </div>
</section>

<section class="prince-package" id="enqForm">
    <div class="container">
        <div class="text-center">
            <h5 class="blurColor">{{ $eventDetailLabels['FILLINFOFOR'] }}</h5>
            <h4 class="pkgName"></h4>
            <input type="hidden" id="pkgName">
        </div>

        <form id="submitEventEnqForm">
			<input type="hidden" name="event_id" value="@if(!empty($eventData->id)) {{ $eventData->id }} @endif">
			<input type="hidden" name="package_id" id="package_id">
            <div class="row pb40">
                <div class="col-md-12 offset-lg-1 col-lg-10 offset-xl-2 col-xl-8">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb24">
                            <input type="text" class="input w100" placeholder="{{ $eventDetailLabels['YOURNAME'] }}" name="full_name" id="full_name">
                        </div>

                        <div class="col-sm-12 col-md-6 mb24">
                            <input type="email" class="input w100" placeholder="{{ $eventDetailLabels['FORGOTPASSLABEL2'] }}" name="email" id="email">
                        </div>

                        <div class="col-sm-12 col-md-6 mb24">
                            <input type="text" name="event_date" id="event_date" class="date input w-100" placeholder="{{ $eventDetailLabels['SELECTDATE'] }}" autocomplete="off" >
                        </div>

                        <div class="col-sm-12 col-md-6 mb24">
                            <input type="text" onfocus="(this.type='time')" onfocusout="(this.type='')"
                                class="time input w100" placeholder="{{ $eventDetailLabels['SELECTTIME'] }}" name="event_time" id="event_time">
                        </div>

                        <div class="col-sm-12 col-md-6 mb24">
                            <div class="number-gender">
                                <input type="number" class="input" placeholder="{{ $eventDetailLabels['NOOFPHOTOGRAPHERS'] }}" name="photographer_count" id="photographer_count">
                                <select class="select" name="photographer_gender" id="photographer_gender">
                                    <option value="{{ $eventDetailLabels['FEMALE'] }}">{{ $eventDetailLabels['FEMALE'] }}</option>
                                    <option value="{{ $eventDetailLabels['MALE'] }}">{{ $eventDetailLabels['MALE'] }}</option>
                                    <option value="{{ $eventDetailLabels['BOTH'] }}">{{ $eventDetailLabels['BOTH'] }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 mb24">
                            <div class="number-gender">
                                <input type="number" class="input" placeholder="{{ $eventDetailLabels['NOOFVIDEOGRAPHER'] }}" name="videographer_count" id="videographer_count">
                                <select class="select" name="videographer_gender" id="videographer_gender">
                                    <option value="{{ $eventDetailLabels['FEMALE'] }}">{{ $eventDetailLabels['FEMALE'] }}</option>
                                    <option value="{{ $eventDetailLabels['MALE'] }}">{{ $eventDetailLabels['MALE'] }}</option>
                                    <option value="{{ $eventDetailLabels['BOTH'] }}">{{ $eventDetailLabels['BOTH'] }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(!$additionalReqs->isEmpty())
            <div class="text-center mb40">
                <h4>{{ $eventDetailLabels['ADDITIONALSERV'] }}</h4>
            </div>
            @endif

            <div class="row additional-services">
                <div class="col-md-12 offset-lg-1 col-lg-10 offset-xl-2 col-xl-8">

                    <div class="additional-accordian">
                        <ul id="addServList">
                            @if(!$additionalReqs->isEmpty())
                            @foreach($additionalReqs as $addService)
                            <li id="{{ $addService->id }}" data-name="{{ $addService->id }}">
                                <a href="#" class="drop-arrow">
                                    <div class="title">
                                        <div class="service-icons">
                                            <img
                                                src="{{ asset('public/assets/images/additional-service/'. $addService['image']) }}">
                                        </div>
                                        <div>
                                            <h6>{{ $addService->name }}</h6>

                                            <p class="s1">{{ $eventDetailLabels['FROM'] }} {{ $currencyCode }} {{ number_format($addService->price * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</p>
                                        </div>
                                    </div>
                                </a>
                                <ul>
                                    <li>
                                        <div class="service-detail">
                                            <span>{{ $addService->text }}</span>
                                            <div class="content-img">
                                                <div class="requirement-booths">
                                                    @if(!empty($addService['addServRequirements']))
                                                    <p class="s1">{{ $eventDetailLabels['REQFOR'] }} {{ $addService->name }}</p>

                                                    @foreach($addService['addServRequirements'] as $req)
                                                    <p class="s2"> {{ $req->requirements }}
														<span class="blurColor">{{ $req->value }}</span>
													</p>
                                                    @endforeach
                                                    @endif

                                                </div>
                                                <div class="sample-booths">
                                                    <p class="s1">{{ $eventDetailLabels['SAMPLE'] }} {{ $addService->name }}</p>
                                                    <div class="owl-carousel owl5 owl-theme">
                                                        @foreach($addService['addServSamples'] as $sample)
                                                        <div class="item">
                                                            <img src="{{ asset('public/assets/images/additional-service/samples/'. $sample['image']) }}">
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <button class="border-btn addToEnq_{{ $addService->id }} addToEnq"  id="addBtn_{{ $addService->id }}" type="button" data-btn="{{ $addService->id }}">{{ $eventDetailLabels['ADDTOENQ'] }}</button>
												<button class="border-btn removeEnq_{{ $addService->id }} removeEnq d-none" id="removeBtn_{{ $addService->id }}" type="button" data-btn="{{ $addService->id }}">{{ $eventDetailLabels['REMOVE'] }}</button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>

                    <div class="text-center">
						{{--@if(Auth::guard('customer')->check())--}}
							<button class="fill-btn" type="submit" id="submitEnqBtn">{{ $eventDetailLabels['SENDENQ'] }}</button>
                        {{--@else
                            <button class="fill-btn" type="button" onclick="window.location='{{ url('/login').'?flagLogin=1&eventId='.$eventData->id }}'">{{ $eventDetailLabels['SENDENQ'] }}</button>
                        @endif--}}

                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/events/event.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/events/eventEnq.js')}}"></script>
@endpush
