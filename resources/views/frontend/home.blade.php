@extends('frontend.layouts.master')
<!-- This meta is statically added, pls change it whoever gets this task -->
@section('description', 'Share text and photos with your friends and have fun')
@section('keywords', 'sharing, sharing text, text, sharing photo, photo,')
@section('content')
<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var langVisibility = <?php echo json_encode($langVisibility->visibility); ?>;
</script>
@if($home_page_banner)
<section class="home-banner">
    @if($home_page_banner->image)
    <img src="{{asset('public/assets/images/banners/desktop').'/'.$home_page_banner->image}}" class="desktop-img" loading="lazy">
    @endif
    @if($home_page_banner->mobile_image)
    <img src="{{asset('public/assets/images/banners/mobile').'/'.$home_page_banner->mobile_image}}" class="mobile-img" loading="lazy">
    @endif
    <div class="overlay" style="display: none;">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-6 col-lg-5">
                    <h4>{{$home_page_banner->title}}</h4>
                    <p>{{$home_page_banner->text}}</p>
                    @php $category = \App\Models\Category::where('id', $home_page_banner->category_id)->first() @endphp
                    @if($category)
                    <a style="color: inherit;" href="{{($category) ? $baseUrl.'/category/'.$category->slug : $baseUrl}}"><button class="fill-btn">{{$homePageLabels['PERSONALISENOW']}}</button></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if($home_page_text_is_active->is_active == 1)
@if($home_page_text)
<section class="we-create">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-mg-5 col-lg-5 col-xl-4">
                <h4 class="mb17">{{$home_page_text->content_1}}</h4>
            </div>
            <div class="col-sm-12 col-md-7 col-lg-7 offset-xl-1 col-xl-7">
                <div class="pl-24">
                    <p class="mb17">{{$home_page_text->content_2}}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endif

@if($our_services_is_active->is_active == 1)
@if($our_services)
<section class="our-service">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h5 class="sh">{{$homePageLabels['OURPRINTSERVICES']}}</h5>
                <h4>{{$homePageLabels['WHATWEDO']}}</h4>
            </div>
        </div>
    </div>
    <!--<div class="left-container">-->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="owl-carousel owl-theme our-service-carasoul">
                    @foreach($our_services as $our_service)
                    <div class="item text-center">
                        <img src="{{asset('public/assets/images/services').'/'.$our_service->service_image}}">
                        <h6>{{$our_service->service_name}}</h6>
                        <p>{{$our_service->short_desc}}
                        </p>
                        @php $category = \App\Models\Category::where('id', $our_service->category_id)->first() @endphp
                        <a class="border-btn" href="{{($category) ? $baseUrl.'/category/'.$category->slug : $baseUrl}}">{{$homePageLabels['EXPLORE']}}</a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endif

@if($home_page_content_is_active->is_active == 1)
@if($home_page_content)
<section class="our-collection">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <h5 class="sh">{{$homePageLabels['OURAMAZINGCOLLECTION']}}</h5>
                <h4>{{$homePageLabels['PHOTOGIFTSANDCARDS']}}</h4>
                <p>{{$home_page_content->description}}</p>
                @php $category = \App\Models\Category::where('id', $home_page_content->category_id_1)->first() @endphp
                <a class="fill-btn" href="{{($category) ? $baseUrl.'/category/'.$category->slug : $baseUrl}}">{{$homePageLabels['SHOPNOW']}}</a>
            </div>
            <div class="col-6 col-sm-6 col-md-4 text-center">
                <img src="{{asset('public/assets/images/home-page-content/desktop/').'/'.$home_page_content->image_1}}">
                <a style="color: #FFFFFF;text-decoration: none;" href="{{($category) ? $baseUrl.'/category/'.$category->slug : $baseUrl}}"><h6>{{$home_page_content->image_text_1}}</h6></a>
            </div>
            <div class="col-6 col-sm-6 col-md-4 text-center">
                @php $category_2 = \App\Models\Category::where('id', $home_page_content->category_id_2)->first() @endphp
                <img src="{{asset('public/assets/images/home-page-content/desktop/').'/'.$home_page_content->image_2}}">
                <a style="color: #FFFFFF;text-decoration: none;" href="{{($category_2) ? $baseUrl.'/category/'.$category_2->slug : $baseUrl}}"><h6>{{$home_page_content->image_text_2}}</h6></a>
            </div>
        </div>
    </div>
</section>
@endif
@endif

@if($how_it_works_is_active->is_active == 1)
@if($how_it_works_banners)
<section class="we-undertake">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h5 class="sh">{{$homePageLabels['WEUNDERTAKEALL']}}</h5>
                <h4>{{$homePageLabels['EVENTOCCASIONS']}}</h4>
            </div>
        </div>
        <div class="row mt32 d-not-767">
            <div class="col-12">
                <img src="{{asset('public/assets/images/how-it-works-banner').'/'.$how_it_works_banners->image}}">
            </div>
        </div>
        <div class="row mt32 d-show-767">
            <div class="col-12">
                @foreach($how_it_works as $hiw)
                <div class="w280 text-center">
                    <img src="{{asset('public/assets/images/howItWorks').'/'.$hiw->image}}">
                    <p class="s1">{{$hiw->title}}</p>
                    <span>{{$hiw->description}}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <h4>{{$homePageLabels['HOWITWORKS']}}</h4>
                <a href="{{url('/events-occasions')}}" class="fill-btn">{{$homePageLabels['BOOKYOUREVENT']}}</a>
            </div>
        </div>
    </div>
</section>
@endif
@endif

@if($our_collection_is_active->is_active == 1)
<section class="shop-from">
    <div class="container">
        <h5 class="sh">{{$homePageLabels['SHOPFROM']}}</h5>
        <h4>{{$homePageLabels['OURCOLLECTION']}}</h4>
        <div class="row">
            @foreach($our_collections as $our_collection)
            <div class="col-6 col-sm-6 col-md-3 shop-from-img">
                @php $category = \App\Models\Category::where('id', $our_collection->category_id)->first() @endphp
                <a style="color: inherit;" href="{{($category) ? $baseUrl.'/category/'.$category->slug : $baseUrl}}"><img src="{{asset('public/assets/images/collections').'/'.$our_collection->collection_image}}"></a>
                <a style="color: inherit;" href="{{($category) ? $baseUrl.'/category/'.$category->slug : $baseUrl}}"><h6>{{$our_collection->collection_title}}</h6></a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($behrain_photo_is_active->is_active == 1)
@if($chunkedarray)
<section class="some-amazing">
    <div class="container text-center">
        <h5 class="sh">{{$homePageLabels['SOMEOFAMAZING']}}</h5>
        <h4>{{$homePageLabels['BAHRIANPHOTOGRAPHERPHOTO']}}</h4>

        <div class="row d-flex"><?php 
            $mainCnt = 0;
            ?>
            @foreach($chunkedarray as $k => $value)
                <?php 
                $innerCnt = 1;
                ?>
                @if($mainCnt % 2 == 0)
                    @foreach ($value as $key => $even)
                        <!-- <div class="col-12 col-sm-12 col-md-6 mb36">
                            <div class="row"> -->
                            @if($innerCnt != 3)
                                <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb36">
                                    <div class="content">
                                        <div class="content-overlay"></div>
                                        <img src="{{asset('public/assets/images/home-page-photographer/smallimage/').'/'.$even['small_image']}}">
                                        <div class="content-details fadeIn-left">
                                            <a href="{{url('/made-in-bahrain').'/'.$even['photographer_id']}}" class="blue-border-btn">{{$homePageLabels['EXPLORE']}}</a>
                                        </div>
                                    </div>
                                    <p class="s1">{{$even['name']}}</p>
                                </div>
                            @endif
                            <!-- </div>
                        </div> -->
                        @if($innerCnt == 3)
                            <div class="col-12 col-sm-12 col-md-6 mb36">
                                <div class="content">
                                    <div class="content-overlay"></div>
                                    <img src="{{asset('public/assets/images/home-page-photographer/bigimage/').'/'.$even['big_image']}}">
                                    <div class="content-details fadeIn-left">
                                        <a href="{{url('/made-in-bahrain').'/'.$even['photographer_id']}}" class="blue-border-btn">{{$homePageLabels['EXPLORE']}}</a>
                                    </div>
                                </div>
                                <p class="s1">{{$even['name']}}</p>
                            </div>
                        @endif
                        <?php 
                        $innerCnt++;
                        ?>
                    @endforeach

                @elseif($mainCnt % 2 != 0)
                    @foreach ($value as $key => $odd)
                        @if($innerCnt == 1)
                            <div class="col-12 col-sm-12 col-md-6 mb36">
                                <div class="content">
                                    <div class="content-overlay"></div>
                                    <img src="{{asset('public/assets/images/home-page-photographer/bigimage/').'/'.$odd['big_image']}}">
                                    <div class="content-details fadeIn-left">
                                        <a href="{{url('/made-in-bahrain').'/'.$odd['photographer_id']}}" class="blue-border-btn">{{$homePageLabels['EXPLORE']}}</a>
                                    </div>
                                </div>
                                <p class="s1">{{$odd['name']}}</p>
                            </div>
                        @endif
                        <!-- <div class="col-12 col-sm-12 col-md-6 mb36">
                            <div class="row"> -->
                            @if($innerCnt != 1)
                                <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb36">
                                    <div class="content">
                                        <div class="content-overlay"></div>
                                        <img src="{{asset('public/assets/images/home-page-photographer/smallimage/').'/'.$odd['small_image']}}">
                                        <div class="content-details fadeIn-left">
                                            <a href="{{url('/made-in-bahrain').'/'.$odd['photographer_id']}}" class="blue-border-btn">{{$homePageLabels['EXPLORE']}}</a>
                                        </div>
                                    </div>
                                    <p class="s1">{{$odd['name']}}</p>
                                </div>
                            @endif
                            <?php 
                            $innerCnt++;
                            ?>
                            <!-- </div>
                        </div> -->
                    @endforeach
                @endif
                <?php 
                $mainCnt++;
                ?>
            @endforeach
        </div>
    </div>
</section>
@endif
@endif

@if($best_seller_is_active->is_active == 1)
@if($best_sellers)
<section class="best-seller">
    <div class="container text-center">
    <!-- SOMEOFOUR -->
        <h5 class="sh">{{$homePageLabels['SOMEOFOUR']}}</h5>
        <h4>{{$homePageLabels['BESTSELLERS']}}</h4>
        <div class="row">
        @foreach($best_sellers as $best_seller)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                <div class="content">
                    <div class="content-overlay"></div>
                    <img src="{{asset('public/assets/images/seller/').'/'.$best_seller->image}}">
                    <div class="content-details fadeIn-left">
                        @php $category = \App\Models\Category::where('id', $best_seller->category_id)->first() @endphp
                        <a href="{{($category) ? $baseUrl.'/category/'.$category->slug : $baseUrl}}" class="blue-border-btn">{{$homePageLabels['EXPLORE']}}</a>
                    </div>
                </div>
                <p class="s1">{{$best_seller->title}}</p>
                <span>{{$homePageLabels['FROM']}} {{$Currency_symbol}} {{number_format($best_seller->price * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator)}}</span>
            </div>
        @endforeach
        </div>
    </div>
</section>
@endif
@endif

@endsection

@push('scripts')
    <script src="{{asset('public/assets/frontend/js/homepage/homepage.js')}}"></script>
    <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
    <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
@endpush
