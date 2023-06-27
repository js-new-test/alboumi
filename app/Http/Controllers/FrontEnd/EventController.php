<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Events;
use App\Models\GlobalLanguage;
use App\Models\WorldLanguage;
use App\Models\CmsPages;
use App\Models\Package;
use App\Models\PackageFeatures;
use App\Models\EventEnq;
use App\Models\EventEnqUploadedImages;
use App\Models\AdditionalService;
use App\Models\GlobalCurrency;
use App\Models\Currency;
use App\Models\Category;
use App\Models\ProductDetails;
use App\Models\Product;
use App\Models\EventPhotoOrders;
use App\Models\ProductPricing;
use DB;
use Validator;
use App\Traits\CommonTrait;
use Illuminate\Pagination\Paginator;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EventController extends Controller
{
    use CommonTrait;
	protected $event, $eventEnq, $eventGallery, $eventPhotoOrders;

	public function __construct(Events $event,EventEnq $eventEnq,EventEnqUploadedImages $eventGallery,EventPhotoOrders $eventPhotoOrders) {
        $this->event = $event;
        $this->eventEnq = $eventEnq;
        $this->eventGallery = $eventGallery;
        $this->eventPhotoOrders = $eventPhotoOrders;
    }

    public function getEventsListing()
    {
        $codes = ['APPNAME','EVENT'];
        $eventsLabels = getCodesMsg(Session::get('language_id'), $codes); 

        $projectName = $eventsLabels['APPNAME'];

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $setSessionforLang=setSessionforLang($defaultLanguageData['language_id']);
        $defaultLangId = $defaultLanguageData['id'];

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $langId = Session::get('language_id');
        $langVisibility = GlobalLanguage::checkVisibility($langId);

        $eventsData = Events::getEvents($langId);

        $cmsPageDetails = CmsPages::select('title','description','cms_pages.banner_image as cms_banner','cms_pages.mobile_banner_image as cms_mobile_banner'
                                    ,'seo_title','seo_description','seo_keyword','cd.banner_image','cd.mobile_banner')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug','events-occasions')
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->where('language_id',$langId)
                                    ->first();


        $baseUrl = $this->getBaseUrl();

        if($cmsPageDetails == null)
        {
            $cmsPageDetails = CmsPages::select('title','description','cms_pages.banner_image as cms_banner','cms_pages.mobile_banner_image as cms_mobile_banner'
                                    ,'seo_title','seo_description','seo_keyword','cd.banner_image','cd.mobile_banner')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug','events-occasions')
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->where('language_id',$defaultLanguageData['id'])
                                    ->first();
        }
        $pageName = $cmsPageDetails['seo_title'];

        return view ('frontend/events/events',compact('eventsData','pageName','projectName','cmsPageDetails','megamenuFileName','baseUrl','mobileMegamenuFileName','langVisibility','defaultLangId'));
    }

    public function getEventDetails($id)
    {

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $defaultLangId = $defaultLanguageData['id'];

        $setSessionforLang=setSessionforLang($defaultLanguageData['language_id']);
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $decimalNumber=Session::get('decimal_number');
        $decimalSeparator=Session::get('decimal_separator');
        $thousandSeparator=Session::get('thousand_separator');

        $langId = Session::get('language_id');

        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$langId)->first();

        $currencyCode =getCurrSymBasedOnLangId($langId);

        $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
        $decimalNumber=$currenyIdFromLang->decimal_number;
        $decimalSeparator=$currenyIdFromLang->decimal_separator;
        $thousandSeparator=$currenyIdFromLang->thousand_separator;

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $langVisibility = GlobalLanguage::checkVisibility($langId);

        $codes = ['APPNAME','CHOOSEFROMOUR', 'AMAZINGPKG', 'SENDENQ', 'GETSTARTED','FEMALE','MALE','BOTH',
        'CHOOSEPLAN','FILLINFOFOR','YOURNAME','FORGOTPASSLABEL2','SELECTDATE','SELECTTIME','NOOFPHOTOGRAPHERS',
        'NOOFVIDEOGRAPHER','ADDITIONALSERV','FROM','REQFOR','SAMPLE','ADDTOENQ','REMOVE','SENDENQ','NOPKGAVAILABLE',
        'FULLNAMEREQ','EMAILREQ', 'NOTVALIDEMAIL','EVEDATEREQ','EVETIMEREQ'];

        $eventDetailLabels = getCodesMsg($langId, $codes);

        $eventName = Events::select('event_name')
                            ->where('language_id',$langId)
                            ->where('is_active',1)
                            ->where('id',$id)
                            ->whereNull('deleted_at')
                            ->first();

        $eName = isset($eventName->event_name) ? $eventName->event_name : '';
        $pageName = isset($cmsPageDetails->seo_title) ? $cmsPageDetails->seo_title.'-'.$eName : $eName;
        $projectName = $eventDetailLabels['APPNAME'];

        $featureValues = array();
        $a = [];
        $i = 0;

        $eventData = Events::select('id','event_name','event_image','banner_image','mobile_banner_image','event_desc')
                            ->with(['children' => function($query) use($langId)
                                {
                                    $query->where('language_id', '=', $langId);
                                    $query->orderBy('sort_order');
                                }
                            ])
                            ->with("eventFeatures")
                            ->where('language_id',$langId)
                            ->where('is_active',1)
                            ->where('id',$id)
                            ->whereNull('deleted_at')
                            ->orderBy('sort_order')
                            ->first();
   
        if(empty($eventData))
        {
            $eventData = Events::select('id','event_name','event_image','banner_image','mobile_banner_image','event_desc')
                            ->with(['children' => function($query) use($defaultLangId)
                                {
                                    $query->where('language_id', '=', $defaultLangId);
                                    $query->orderBy('sort_order');

                                }
                            ])
                            ->with("eventFeatures")
                            ->where('language_id',$defaultLangId)
                            ->where('is_active',1)
                            ->where('id',$id)
                            ->whereNull('deleted_at')
                            ->orderBy('sort_order')
                            ->first();
        }
        
        $additionalReqs = AdditionalService::select('additional_service.id','name','image','text','price')
                                        ->with('addServRequirements')
                                        ->with('addServSamples')
                                        ->where('language_id',$langId)
                                        ->where('status',0)
                                        ->where('is_deleted',0)
                                        ->get();

        $mobilePackageValues = [];
        if(!empty($eventData))
        {
            $packageIds = array_column($eventData['children']->toArray(),'id');
            $featureIds = array_column($eventData['eventFeatures']->toArray(),'id');
            foreach($eventData['eventFeatures'] as $feature)
            {
                $featureValues[$i]['featureName'] = $feature->feature_name;
                
                $j = 0;
                foreach($eventData['children'] as $pkg)
                {
                    $featureData = PackageFeatures::where('package_id',$pkg['id'])->where('feature_id',$feature->id)->first();
                    $a[$j]['pkg_id'] = $pkg['id'];

                    if(empty($featureData))
                    {
                        $a[$j]['featurePackageData'] = " - ";
                    }
                    else
                    {
                        $a[$j]['featurePackageData'] = $featureData->package_value;
                    }
                    $j++;
                }
                $featureValues[$i++]['featureValue'] = $a;
            }
            // dd($featureValues);
            $mobileFeatureValues = array();
            $mobilePackageValues = array();
            $k = 0;
            foreach($eventData['children'] as $pkg)
            {
                $mobilePackageValues['package'][$k]['pkgName'] = $pkg->package_name;
                $mobilePackageValues['package'][$k]['pkgPrice'] = $pkg->price;
                $mobilePackageValues['package'][$k]['pkgDiscountedPrice'] = $pkg->discounted_price;
                $mobilePackageValues['package'][$k]['id'] = $pkg->id;

                $j = 0;
                foreach($eventData['eventFeatures'] as $feature)
                {
                    $mobileFeatureValues[$j]['featureName'] = $feature->feature_name;
                    $featureData = PackageFeatures::where('package_id',$pkg['id'])->where('feature_id',$feature->id)->first();
                    if(empty($featureData))
                    {
                        $mobileFeatureValues[$j]['featurePackageData'] = " - ";
                    }
                    else
                    {
                        $mobileFeatureValues[$j]['featurePackageData'] = $featureData->package_value;
                    }
                    $j++;
                }
                $mobilePackageValues['package'][$k++]['featureValues'] = $mobileFeatureValues;
            }
            // dd($mobilePackageValues);
            $baseUrl = $this->getBaseUrl();
            $cmsPageDetails = CmsPages::select('title','description','cms_pages.banner_image as cms_banner','cms_pages.mobile_banner_image as cms_mobile_banner'
                                    ,'seo_title','seo_description','seo_keyword','cd.banner_image','cd.mobile_banner')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug','events-occasions')
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->where('language_id',$langId)
                                    ->first();

            if($cmsPageDetails == null)
            {
                $cmsPageDetails = CmsPages::select('title','description','cms_pages.banner_image as cms_banner','cms_pages.mobile_banner_image as cms_mobile_banner',
                                        'seo_title','seo_description','seo_keyword','cd.banner_image','cd.mobile_banner')
                                        ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                        ->where('slug','events-occasions')
                                        ->whereNull('cd.deleted_at')
                                        ->whereNull('cms_pages.deleted_at')
                                        ->where('language_id',$defaultLanguageData['id'])
                                        ->first();
            }
            return view('frontend/events/eventDetail',compact('megamenuFileName','pageName','projectName','eventData','additionalReqs','featureValues','defaultLangId',
            'baseUrl','mobilePackageValues','eventDetailLabels','cmsPageDetails','conversionRate','currencyCode','mobileMegamenuFileName','langVisibility','decimalNumber','decimalSeparator','thousandSeparator'));
        }
        else
        {
            return redirect('events-occasions');
        }
    }

    public function submitEventEnq(Request $request)
    {
        $langId = session('language_id');
        $customerId = session('customer_id');

        $codes = ['ENQSENTSUCCESS','ENQSENTERROR'];
        $enqSubmitMsg = getCodesMsg($langId, $codes);

        $eventEnq = $this->event->submitEnquiryEnq($request,$langId,$customerId);

        $msg = $enqSubmitMsg['ENQSENTSUCCESS'];
        $errorMsg = $enqSubmitMsg['ENQSENTERROR'];
        if($eventEnq == true)
        {
            return response()->json(['status' => true,'msg'=> $msg]);
        }
        else
        {
            return response()->json(['status' => false,'msg'=> $errorMsg]);
        }
    }

    public function enqSubmitSuccess()
    {
        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;

        $setSessionforLang=setSessionforLang($defaultLanguageData['language_id']);
        $langId = session('language_id');

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $codes = ['APPNAME','ENQSENTSUCCESS','RETURNHOME','DESCRIPTION2'];
        $notFoundLabels = getCodesMsg($langId, $codes);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $pageName = $notFoundLabels['ENQSENTSUCCESS'];
        $projectName = $notFoundLabels['APPNAME'];
        $baseUrl = $this->getBaseUrl();
        return view('frontend/events/enqSuccess',compact('projectName', 'pageName', 'baseUrl','megamenuFileName','mobileMegamenuFileName','notFoundLabels'));
    }

    public function myEventEnquiries()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang = setSessionforLang($defaultLanguageId);

        $custId = Session::get('customer_id');
        // dd($custId);
        $codes = ['SIDEBARLABEL5', 'EVENT', 'PLAN','ENQDATE','HOME','APPNAME','NOENQFOUND','STATUS','ADVPAYMENT','ACTION','PAYNOW','AGREEDAMT'];
        $myEventEnqLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $myEventEnqLabels["SIDEBARLABEL5"];
        $projectName = $myEventEnqLabels["APPNAME"];

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $eventEnquiries = $this->eventEnq->getEventEnquiriesByCustId($custId);
        $baseUrl = $this->getBaseUrl();
        $langId = Session::get('language_id');
        $currencyCode =getCurrSymBasedOnLangId($langId);
        return view('frontend.events.myEnquiries',compact('eventEnquiries','baseUrl','pageName','projectName','megamenuFileName','mobileMegamenuFileName','myEventEnqLabels','currencyCode'));
    }

    public function myEventGallery()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang = setSessionforLang($defaultLanguageId);

        $custId = Session::get('customer_id');

        $codes = ['SIDEBARLABEL6', 'EVENT', 'PHOTOSNVIDEOS','DATE','PAYBUTTON','NOPHOTOSFOUND','APPNAME','HOME','DOWNLOAD'];
        $myEventGalleryLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $myEventGalleryLabels["SIDEBARLABEL6"];
        $projectName = $myEventGalleryLabels["APPNAME"];

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        Session::forget('selectedImagesToDownload');
        Session::forget('selectedImagesPrice');

        $eventGallery = $this->eventGallery->getEventGalleryListingByCustId($custId);
        $baseUrl = $this->getBaseUrl();
        return view('frontend.events.myEventGallery',compact('eventGallery','baseUrl','pageName','projectName','megamenuFileName','mobileMegamenuFileName','myEventGalleryLabels'));
    }

    public function getEventGalleryImages($enqId,$btnValue)
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang = setSessionforLang($defaultLanguageId);

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $codes = ['SIDEBARLABEL6', 'APPNAME','HOME','PAYBUTTON','SELECTED','PRINT','QUANTITY','PRODUCT','ADDTOCART','BUYNOW',
        'PAYMENMETHOD','CONTINUE',"DOWNLOAD","BHD",'PRICE'];
        $myEventGalleryLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $myEventGalleryLabels["SIDEBARLABEL6"];
        $projectName = $myEventGalleryLabels["APPNAME"];

        $baseUrl = $this->getBaseUrl();

        $langId = Session::get('language_id');
        $custId = Auth::guard('customer')->user();
        
        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$langId)->first();
        $currencyCode =getCurrSymBasedOnLangId($langId);
      
        $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
        $decimalNumber=$currenyIdFromLang->decimal_number;
        $decimalSeparator=$currenyIdFromLang->decimal_separator;
        $thousandSeparator=$currenyIdFromLang->thousand_separator;

        $selectedImages = Session::get('selectedImagesToDownload');
        $selectedImagesPrice = Session::get('selectedImagesPrice');
        
        if($selectedImagesPrice == null)
        {
            $selectedImagesPrice = 0;
        }
        if($selectedImages != null)
        {
            $selectedImagesCount = count(explode(',',Session::get('selectedImagesToDownload')));
        }
        else
            $selectedImagesCount = 0;

        $eventName = EventEnq::select('e.event_name as eventName','price_per_photo')
                                ->leftJoin('events as e',function ($join) {
                                    $join->on('e.id','=','event_enquiry.event_id');
                                    $join->where('e.is_active','=',1);
                                    $join->whereNull('e.deleted_at');
                                })
                                ->where('event_enquiry.id',$enqId)
                                ->whereNull('event_enquiry.deleted_at')
                                ->first();

        $eventPhotosList = EventEnqUploadedImages::select('event_enq_uploaded_images.photos','event_enq_uploaded_images.id','ee.price_per_photo','flag_purchased','ee.id as eventEnqid')
                                    ->join('event_enquiry as ee','ee.id','event_enq_uploaded_images.event_enq_id')
                                    ->where('event_enq_uploaded_images.event_enq_id',$enqId)
                                    ->whereNull('ee.deleted_at')
                                    ->whereNull('event_enq_uploaded_images.deleted_at')
                                    ->get();
        
        $eventPhotos = [];
        $i = 0;
        foreach($eventPhotosList as $eventPics)
        {
            if($eventPics->flag_purchased == 0)
                $folderPath = $eventPics['eventEnqid'].'/'.'watermark';
            else
                $folderPath = $eventPics['eventEnqid'];

            if($eventPics['flag_purchased'] == 0)
            {
                $eventPhotos[$i]['isPayable'] = "1";
            }
            else
            {
                $eventPhotos[$i]['isPayable'] = "0";
            }

            $eventPhotos[$i]['photo'] = $this->getS3ImagePath($folderPath,$eventPics['photos']);
            $eventPhotos[$i]['id'] = $eventPics->id;
            $eventPhotos[$i]['imageName'] = $eventPics->photos;
            $eventPhotos[$i]['folderPath'] = $this->getS3ImagePath($folderPath,'');
            $eventPhotos[$i]['flag_purchased'] = $eventPics->flag_purchased;
            $eventPhotos[$i++]['perPhotoPrice'] = $eventPics->price_per_photo;
        }
        $data = $this->paginate($eventPhotos);
        $data->withPath('');

        $group_price = "NULL as group_price";
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }

        // $photoCategory = Category::select('id')->where('slug','photo')->first();
        $products = Product::select('products.id','product_pricing.id as optionId','pd.title','selling_price as price','offer_price as discountedPrice','offer_start_date','offer_end_date',DB::raw($group_price))
                                ->leftJoin('product_details as pd', function($join) use($langId) {
                                    $join->on('pd.product_id', '=' , 'products.id');
                                    $join->whereNull('pd.deleted_at');
                                })
                                ->leftJoin('product_pricing', function($join) {
                                    $join->on('product_pricing.product_id', '=' , 'products.id');
                                    $join->where('product_pricing.is_default','=',1);
                                    $join->whereNull('product_pricing.deleted_at');
                                });
                                if(!empty($custId) && $custId->cust_group_id != 0)
                                {
                                    $products = $products->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                                                $join->on('cgp.product_id', '=' , 'products.id');
                                                                $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
                                    });
                                };
                                // $products = $products->where('products.category_id',$photoCategory->id);
                                $products = $products->where('products.printing_product','=',1);
                                $products = $products->whereNull('products.deleted_at');
                                $products = $products->where('products.status', '=' , 'Active');
                                $products = $products->get();
                                // dd($products);
        $photoProducts = [];
        $i = 0;
        if(!empty($products))
        {
            foreach($products as $nonDefaultProducts)
            {
                $prodTitle = $nonDefaultProducts['title'];
                if($prodTitle == null)
                {
                    $prodDetails = ProductDetails::select('title')
                                                ->where('product_id',$nonDefaultProducts['id'])
                                                ->where('language_id',$defaultLanguageId)
                                                ->whereNull('deleted_at')
                                                ->first();
                                             
                    $prodTitle =  $prodDetails['title'];
                }
                $photoProducts[$i]['id'] = "".$nonDefaultProducts['id']."";
                $photoProducts[$i]['title'] = $prodTitle;

                if(empty($nonDefaultProducts['group_price']))
                {
                    if (!empty($nonDefaultProducts['discountedPrice']) && (date("Y-m-d", strtotime($nonDefaultProducts['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d", strtotime($nonDefaultProducts['offer_end_date'])))) 
                    {
                        $photoProducts[$i]['discountedPrice'] = $nonDefaultProducts['discountedPrice'];
                        $photoProducts[$i]['price'] = $nonDefaultProducts['price'];
                        $photoProducts[$i]['group_price'] = '';
                    }
                    else if(!empty($nonDefaultProducts['discountedPrice']))
                    {
                        $photoProducts[$i]['group_price'] = $nonDefaultProducts['discountedPrice'];
                        $photoProducts[$i]['discountedPrice'] = '';
                        $photoProducts[$i]['price'] = $nonDefaultProducts['price'];
                    }
                    else
                    {
                        $photoProducts[$i]['group_price'] = '';
                        $photoProducts[$i]['discountedPrice'] = '';
                        $photoProducts[$i]['price'] = $nonDefaultProducts['price'];
                    }
                }
                else
                {
                    $photoProducts[$i]['group_price'] = $nonDefaultProducts['group_price'];
                    $photoProducts[$i]['discountedPrice'] = '';
                    $photoProducts[$i]['price'] = $nonDefaultProducts['price'];
                }
                $photoProducts[$i++]['optionId'] = $nonDefaultProducts['optionId'];
            }
        }
        // dd($photoProducts);
        return view('frontend/events/eventGalleryImages',compact('custId','enqId','baseUrl','megamenuFileName','mobileMegamenuFileName','selectedImages','photoProducts',
        'selectedImagesCount','pageName','projectName','myEventGalleryLabels','eventName','eventPhotos','currencyCode','data','selectedImagesPrice','btnValue',
        'conversionRate','decimalNumber','decimalSeparator','thousandSeparator'));
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    // set checked images in session
    public function setSelectedImages(Request $request)
    {
        // dd($request->all());
        if(!empty($request->selectedImages))
        {
            Session::put('selectedImagesToDownload',implode(',', $request->selectedImages));
            if(!empty($request->totalPrice))
                Session::put('selectedImagesPrice',$request->totalPrice);
            return true;
        }
        else
        {
            Session::put('selectedImagesToDownload','');
            Session::put('selectedImagesPrice',0);
            return false;
        }

    }

    // function to download image
    public function downloadImage(Request $request)
    {
        $remoteURL = $request->imgPath;
        header("Content-type: application/x-file-to-save"); 
        header("Content-Disposition: attachment; filename=".basename($remoteURL));
        ob_end_clean();
        readfile($remoteURL);
    }

    public function buyEventPhotos(Request $request)
    {
        $eventOrders = $this->eventPhotoOrders->saveOrder($request->all());
        return response()->json($eventOrders);
    }

    public function getSelectedProdPrice(Request $request)
    {
        $custId = Auth::guard('customer')->user();

        $group_price = "NULL as group_price";
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }
        
        $prodDetails = ProductPricing::select('selling_price as price','offer_price as discountedPrice','offer_start_date','offer_end_date',DB::raw($group_price));
                                if(!empty($custId) && $custId->cust_group_id != 0)
                                {
                                    $prodDetails = $prodDetails->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                                                $join->on('cgp.product_id', '=' , 'product_pricing.product_id');
                                                                $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
                                    });
                                };
                                $prodDetails = $prodDetails->where('product_pricing.id',$request->option_id);
                                $prodDetails = $prodDetails->whereNull('product_pricing.deleted_at');
                                $prodDetails = $prodDetails->first();

        return response()->json(['status' => true,'prodDetails' => $prodDetails]);
    }
}
?>