<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Events;
use App\Models\EventEnq;
use App\Models\PackageFeatures;
use App\Models\AdditionalService;
use App\Models\GlobalCurrency;
use App\Models\Currency;
use App\Models\GlobalLanguage;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Product;
use App\Models\WorldLanguage;
use App\Models\ProductDetails;
use App\Models\EventEnqUploadedImages;
use App\Models\EventPhotoOrders;
use App\Models\Cart;
use Config;
use App\Traits\CommonTrait;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
    use CommonTrait;

    protected $event, $eventEnq, $eventGallery;

	public function __construct(Events $event,EventEnq $eventEnq,EventEnqUploadedImages $eventGallery) {
        $this->event = $event;
        $this->eventEnq = $eventEnq;
        $this->eventGallery = $eventGallery;
    }

    public function getPackageslist(Request $request)
    {
        $langId = $request->language_id;
        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLangId = $defaultLanguageData['id'];
        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$langId)->first();

        $currenyCode =getCurrSymBasedOnLangId($langId);
        $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
        $decimalNumber=$currenyIdFromLang->decimal_number;
        $decimalSeparator=$currenyIdFromLang->decimal_separator;
        $thousandSeparator=$currenyIdFromLang->thousand_separator;

        $codes = ['AMAZINGPKG'];
        $pkgLabels = getCodesMsg($request->language_id, $codes);

        $cartCount = Cart::where('cart_master_id',$request->cart_master_id)->count();

        $eventData = Events::select('id','event_name','event_image','mobile_banner_image','event_desc','is_active')
                                ->with(['children' => function($query) use($langId)
                                    {
                                        $query->where('language_id', '=', $langId);
                                    }
                                ])
                                ->with("eventFeatures")
                                ->where('language_id',$langId)
                                ->where('id',$request->event_id)
                                ->whereNull('deleted_at')
                                ->first();
        if($eventData == null)
        {
            $eventData = Events::select('id','event_name','event_image','mobile_banner_image','event_desc','is_active')
                                ->with(['children' => function($query) use($defaultLangId)
                                    {
                                        $query->where('language_id', '=', $defaultLangId);
                                    }
                                ])
                                ->with("eventFeatures")
                                ->where('language_id',$defaultLangId)
                                ->where('id',$request->event_id)
                                ->whereNull('deleted_at')
                                ->first();
        }
        $mobileBannerImgWidth =Config::get('app.event_banner_for_app.width');
        $mobileBannerImgHeight =Config::get('app.event_banner_for_app.height');

        $eventDetails = [];
        $eventDetails["componentId"] = "banner";
        $eventDetails["sequenceId"] = "1";
        $eventDetails["isActive"] = "".$eventData->is_active."";
        $eventDetails["imageHeight"] = "".$mobileBannerImgHeight."";
        $eventDetails["imageWidth"] = "".$mobileBannerImgWidth."";

        $bannerData = $list = array();
        $list['id'] = "".$eventData->id."";
        $list['flagShowText'] = "1";
        $list['name'] = $eventData->event_name;
        $list['status'] = "1";
        $list['image'] = $this->getBaseUrl().'/public/assets/images/events/mobile_banner/'.$eventData->mobile_banner_image;
        $list['type'] = "1";
        $list['navigationFlag'] = "1";
        $list['query'] = "__";
        $bannerData['list'] = array($list);
        $eventDetails['bannerData'] = $bannerData;

        $packageData = array();
        $packageData['componentId'] = "packages";
        $packageData['sequenceId'] = "3";
        $packageData['isActive'] = "1";
        $packageData['componentId'] = "packages";

        $mobileFeatureValues = array();
        $mobilePackageValues = array();
        $k = 0;

        foreach($eventData['children'] as $pkg)
        {
            $mobilePackageValues[$k]['id'] = "".$pkg->id."";
            $mobilePackageValues[$k]['PackageName'] = $pkg->package_name;
            $mobilePackageValues[$k]['price'] = isset($pkg->discounted_price) ? $currenyCode." ".number_format($pkg->discounted_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode." ".number_format($pkg->price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
            $mobilePackageValues[$k]['isSelected'] = "0";

            $j = 0;
            foreach($eventData['eventFeatures'] as $feature)
            {
                $mobileFeatureValues[$j]['packageIncludes'] = $feature->feature_name;
                $featureData = PackageFeatures::where('package_id',$pkg['id'])->where('feature_id',$feature->id)->first();
                if(empty($featureData))
                {
                    $mobileFeatureValues[$j]['packageSpecification'] = " - ";
                }
                else
                {
                    $mobileFeatureValues[$j]['packageSpecification'] = $featureData->package_value;
                }
                $j++;
            }
            $mobilePackageValues[$k++]['itemList'] = $mobileFeatureValues;
        }
        $packageData['packagesData']['title'] =  $pkgLabels["AMAZINGPKG"];
        $packageData['packagesData']['list'] = $mobilePackageValues;

        $result['status'] = "OK";
        if(!empty($mobilePackageValues))
            $result['statusCode'] = 200;
        else
            $result['statusCode'] = 300;
        $result['message'] = "Success";
        $result['cartCount'] = "".$cartCount."";
        $result['component'][] = $eventDetails;
        $result['component'][] = $packageData;

        return response()->json($result);
    }

    public function getEventEnquiry(Request $request)
    {
        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$request->language_id)->first();

        $currenyCode =getCurrSymBasedOnLangId($request->language_id);

        $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
        $decimalNumber=$currenyIdFromLang->decimal_number;
        $decimalSeparator=$currenyIdFromLang->decimal_separator;
        $thousandSeparator=$currenyIdFromLang->thousand_separator;

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;

        $codes = ['AMAZINGPKG'];
        $pkgLabels = getCodesMsg($request->language_id, $codes);

        $codes = ['FROM','REQFOR','SAMPLE'];
        $addServiceLabels = getCodesMsg($request->language_id, $codes);

        $additionalReqs = AdditionalService::select('additional_service.id','name','image','text','price')
                                        ->with('addServRequirements')
                                        ->with('addServSamples')
                                        ->where('language_id',$request->language_id)
                                        ->where('status',0)
                                        ->where('is_deleted',0)
                                        ->get();

        if($additionalReqs->isEmpty())
        {
            $additionalReqs = AdditionalService::select('additional_service.id','name','image','text','price')
                                                ->with('addServRequirements')
                                                ->with('addServSamples')
                                                ->where('language_id',$defaultLanguageData['id'])
                                                ->where('status',0)
                                                ->where('is_deleted',0)
                                                ->get();
        }

        $i = 0;
        $additionalServicesList = [];
        if(!empty($additionalReqs))
        {
            foreach($additionalReqs as $addService)
            {
                $k = $j = 0;
                $list = $sampleData = [];
                $additionalServicesList[$i]['id'] = "".$addService->id."";
                $additionalServicesList[$i]['title'] = $addService->name;
                $additionalServicesList[$i]['subTitle'] = $addServiceLabels['FROM'].' '.$currenyCode." ".number_format($addService->price * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator);
                $additionalServicesList[$i]['image'] = $this->getBaseUrl().'/public/assets/images/additional-service/'.$addService->image;
                $additionalServicesList[$i]['isSeleceted'] = '0';
                $additionalServicesList[$i]['addedtoEnquiry'] = '0';
                $additionalServicesList[$i]['description'] = $addService->text;
                $additionalServicesList[$i]['requirementTitle'] = $addServiceLabels['REQFOR'].' '.$addService->name;

                foreach($addService['addServRequirements'] as $req)
                {
                    $list[$k]['leftText'] = $req->requirements;
                    $list[$k++]['rightText'] = $req->value;
                }
                if(!empty($addService['addServSamples']))
                {
                    foreach($addService['addServSamples'] as $sample)
                    {
                        $additionalServicesList[$i]['sampleTitle'] = $addServiceLabels['SAMPLE'].' '.$addService->name;
                        $sampleData[$j++]['image'] = $this->getBaseUrl().'/public/assets/images/additional-service/samples/'.$sample->image;
                    }
                }
                $additionalServicesList[$i]['list'] = $list;
                $additionalServicesList[$i++]['sampleList'] = $sampleData;
            }
        }

        $result['status'] = "OK";
        $result['statusCode'] = 200;
        $result['message'] = "Success";
        $result['cartCount'] = "1";
        $result['additionalServicesList'] = $additionalServicesList;
        return response()->json($result);
    }

    public function submitEventEnquiry(Request $request)
    {
        $codes = ['FULLNAMEREQ','EMAILREQ', 'NOTVALIDEMAIL','EVEDATEREQ','EVETIMEREQ','ENQSENTSUCCESS','ENQSENTERROR'];

        $enqSubmissionError = getCodesMsg($request->language_id, $codes);

        $msg = [
            'full_name.required' => $enqSubmissionError["FULLNAMEREQ"],
            'email.numeric' => $enqSubmissionError["EMAILREQ"],
            'event_date.required' => $enqSubmissionError["EVEDATEREQ"],
            'event_time.required' => $enqSubmissionError["EVETIMEREQ"],
        ];

        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric',
            'event_id' => 'required|numeric',
            'package_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
            'full_name' => 'required',
            'email' => 'required|email',
            'event_date' => 'required|date_format:Y-m-d',
            'event_time' => 'required|date_format:H:i'
        ],$msg);

        if ($validator->fails()) {
            return response()->json([
            'statusCode' => 300,
            'message' => $validator->errors(),
            ], 300);
        }

        $eventEnq = $this->event->submitEnquiryEnq($request,$request->language_id,$request->customer_id);
        $result = [];
        if($eventEnq == true)
            return response()->json(['status' => "OK",'statusCode' => 200, 'message' => $enqSubmissionError["ENQSENTSUCCESS"]]);
        else
            return response()->json(['status' => "OK",'statusCode' => 500, 'message' => $enqSubmissionError["ENQSENTERROR"]]);
    }

    public function myEventEnquiries(Request $request)
    {
        $custId = Auth::guard('api')->user()->token()->user_id;
        $eventEnquiries = $this->eventEnq->getEventEnquiriesByCustId($custId);

        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$request->language_id)->first();

        $currenyCode =getCurrSymBasedOnLangId($request->language_id);

        if(!($eventEnquiries->isEmpty()))
        {
            $result['status'] = "OK";
            $result['statusCode'] = 200;
            $result['message'] = "Success";
            $enqList = [];
            $i = 0;
            foreach($eventEnquiries as $myEnq)
            {
                $enqList[$i]['id'] = $myEnq['id'];
                $enqList[$i]['eventName'] = $myEnq['eventName'];
                $enqList[$i]['packageName'] = $myEnq['packageName'];
                $enqList[$i]['advPayment'] = $currenyCode." ".$myEnq['advance_payment'];
                $enqList[$i]['agreedAmt'] = $currenyCode." ".$myEnq['total_amount'];
                if($myEnq['advance_payment'] != null && $myEnq['advance_payment'] > 0 && $myEnq['payment_status'] == 0)
                {
                    $enqList[$i]['flagPayment'] = "1";
                }
                else
                {
                    $enqList[$i]['flagPayment'] = "0";
                }
                $enqList[$i++]['enqDate'] = date('d M Y', strtotime($myEnq['enqDate']));
            }
            $result['myEventEnquiries'] = $enqList;
            return response()->json($result);
        }
        else
        {
            $result['status'] = "OK";
            $result['statusCode'] = 300;
            $result['message'] = "Success";
            $result['myEventEnquiries'] = [];
            return response()->json($result);
        }
    }

    public function getEventsAndGalleryList(Request $request)
    {
        // input - language id
        $custId = Auth::guard('api')->user()->token()->user_id;
        $eventGallery = $this->eventGallery->getEventGalleryListingByCustId($custId);

        $eventGalleryData['list'] = $eventGallery;
        $componentData['componentId'] = "eventGallery";
        $componentData['sequenceId'] = "1";
        $componentData['isActive'] = "1";
        $componentData['eventGalleryData'] = $eventGalleryData;

        $result['status'] = "OK";
        if(!empty($eventGallery))
            $result['statusCode'] = 200;
        else
            $result['statusCode'] = 300;

        $result['message'] = "Success";
        $result['component'][] = $componentData;
        return $result;
    }

    public function getEventsAndGalleryDetails(Request $request)
    {

        // input : enq id, language_id
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $langId = $request->language_id;
        $custId = Auth::guard('api')->user()->token()->user_id;
        $enqId = $request->enquiry_id;
        $cust_group_id = Customer::select('cust_group_id')->where('id',$custId)->first();

        if(!empty($request->pageSize))
            $pageSize = $request->pageSize;
        else
            $pageSize = 0;

        if(!empty($request->pageNo))
            $pageNo = $request->pageNo;
        else
            $pageNo = 0;

        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$langId)->first();
        $currenyCode =getCurrSymBasedOnLangId($langId);

        $eventName = EventEnq::select('e.event_name as eventName','price_per_photo')
                                ->leftJoin('events as e',function ($join) {
                                    $join->on('e.id','=','event_enquiry.event_id');
                                    $join->where('e.is_active','=',1);
                                    $join->whereNull('e.deleted_at');
                                })
                                ->where('event_enquiry.id',$enqId)
                                ->whereNull('event_enquiry.deleted_at')
                                ->first();

        $allEventPhotos = EventEnqUploadedImages::select('event_enq_uploaded_images.photos','event_enq_uploaded_images.id','ee.price_per_photo','flag_purchased','ee.id as eventEnqid')
                                    ->join('event_enquiry as ee','ee.id','event_enq_uploaded_images.event_enq_id')
                                    ->where('event_enq_uploaded_images.event_enq_id',$enqId)
                                    ->whereNull('ee.deleted_at')
                                    ->whereNull('event_enq_uploaded_images.deleted_at')
                                    ->get();

        $eventPhotosList = [];
        $i = 0;

        $eventPhotosList = $this->paginate($allEventPhotos,$pageSize,$pageNo,$columns = ['*']);
        $eventPhotosList->withPath('');

        foreach($eventPhotosList as $eventPics)
        {
            if($eventPics->flag_purchased == 0)
                $folderPath = $eventPics['eventEnqid'].'/'.'watermark';
            else
                $folderPath = $eventPics['eventEnqid'];

            $eventPhotos[$i]['id'] = "".$eventPics->id."";
            $eventPhotos[$i]['image'] = $this->getS3ImagePath($folderPath,$eventPics['photos']);
            $eventPhotos[$i]['isPaid'] = "".$eventPics->flag_purchased."";
            $eventPhotos[$i]['price'] = $eventPics->price_per_photo;
            $imgDiem = @getimagesize($this->getS3ImagePath($folderPath,$eventPics['photos']));
            if($imgDiem){
                $eventPhotos[$i]['width'] = "".$imgDiem[0]."";
                $eventPhotos[$i++]['height'] = "".$imgDiem[1]."";
            }else{
                $eventPhotos[$i]['width'] = "";
                $eventPhotos[$i++]['height'] = "";
            }
            
        }

        $group_price = "NULL as group_price";
        if(!empty($custId) && $cust_group_id->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }

        $photoCategory = Category::select('id')->where('slug','photo')->first();
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
                                if(!empty($custId) && $cust_group_id->cust_group_id != 0)
                                {
                                    $products = $products->leftJoin('customer_group_price as cgp', function($join) use($cust_group_id){
                                                                $join->on('cgp.product_id', '=' , 'products.id');
                                                                $join->where('cgp.customer_group_id','=',$cust_group_id->cust_group_id);
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
        foreach($products as $nonDefaultProducts)
        {
            $prodTitle = $nonDefaultProducts['title'];
            // print_r($prodTitle);
            if($prodTitle == null)
            {
                $prodDetails = ProductDetails::select('title')
                                            ->where('product_id',$nonDefaultProducts['id'])
                                            ->where('language_id',$defaultLanguageId)
                                            ->whereNull('deleted_at')
                                            ->first();
                $prodTitle =  $prodDetails['title'];
            }
            $photoProducts[$i]['product_id'] = "".$nonDefaultProducts['id']."";
            $photoProducts[$i]['value'] = $prodTitle;
            $photoProducts[$i]['attributeId'] = "".$nonDefaultProducts['optionId']."";

            if(empty($nonDefaultProducts['group_price']))
            {
                if (!empty($nonDefaultProducts['discountedPrice']) && (date("Y-m-d", strtotime($nonDefaultProducts['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d", strtotime($nonDefaultProducts['offer_end_date']))))
                {
                    $photoProducts[$i]['discountedPrice'] = $nonDefaultProducts['discountedPrice'];
                    $photoProducts[$i]['price'] = $nonDefaultProducts['price'];
                }
                else if(!empty($nonDefaultProducts['discountedPrice']))
                {
                    $photoProducts[$i]['discountedPrice'] = $nonDefaultProducts['discountedPrice'];
                    $photoProducts[$i]['price'] = $nonDefaultProducts['price'];
                }
                else
                {
                    $photoProducts[$i]['discountedPrice'] = '';
                    $photoProducts[$i]['price'] = $nonDefaultProducts['price'];
                }
            }
            else
            {
                $photoProducts[$i]['discountedPrice'] = $nonDefaultProducts['group_price'];
                $photoProducts[$i]['price'] = $nonDefaultProducts['price'];
            }
            $photoProducts[$i++]['attributeId'] = "".$nonDefaultProducts['optionId']."";

        }
        $eventGalleryData['list'] = $eventPhotos;
        $imageData['componentId'] = "galleryImages";
        $imageData['isActive'] = "1";
        $imageData['galleryImagesData']['id'] = $enqId;
        $imageData['galleryImagesData']['currency_id'] = $currenyCode;
        $imageData['galleryImagesData']['list'] = $eventPhotos;

        $list['attributeGroupId'] = "".$photoCategory->id."";
        $list['title'] = "Product";
        $list['type'] = "SS";
        $list['options'] = $photoProducts;

        $prodList[] = $list;
        $productData['componentId'] = "attributes";
        $productData['attributeType'] = "1";
        $productData['isActive'] = "1";
        $productData['attributesData']['list'] = $prodList;

        $quantityData['componentId'] = "quantity";
        $quantityData['isActive'] = "1";
        $quantityData['quantityData'] = "999999";

        $result['status'] = "OK";
        if(!empty($eventPhotos))
            $result['statusCode'] = 200;
        else
            $result['statusCode'] = 300;

        $result['message'] = "Success";
        $result['component'][] = $imageData;
        $result['component'][] = $productData;
        $result['component'][] = $quantityData;

        return $result;
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function createEventOrderPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric',
            'event_enquiry_id' => 'required|numeric',
            'photo_ids' => 'required',
            'payment_method' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
            'statusCode' => "300",
            'message' => $validator->errors(),
            ],300);
        }

        $lang_id = $request->language_id;
        $custId = Auth::guard('api')->user()->token()->user_id;
        $lastOrderDetails = EventPhotoOrders::select('id')->orderBy('id','desc')->first();

        if(isset($lastOrderDetails))
        {
            $lastOrderId = $lastOrderDetails->id;
            $orderId = 'ALBEPHO'.(str_pad($lastOrderId + 1, 5, '0', STR_PAD_LEFT));
        }
        else
            $orderId = 'ALBEPHO00001';

        $totalAmount = EventEnq::select('price_per_photo')->where('id',$request['event_enquiry_id'])->first();
        $totalAmountToPay = $totalAmount->price_per_photo * (count(explode(",",$request['photo_ids'])));

        $saveOrder = new EventPhotoOrders;
        $saveOrder->event_enquiry_id = $request['event_enquiry_id'];
        $saveOrder->customer_id = $custId;
        $saveOrder->photo_ids = $request['photo_ids'];
        $saveOrder->order_id = $orderId;
        $saveOrder->language_id = $lang_id;
        $saveOrder->amount = $totalAmountToPay;
        $saveOrder->payment_type = $request['payment_method'];
        $saveOrder->created_at = Carbon::now();
        $saveOrder->save();

        $lastInsertedId = $saveOrder->id;
        $lastInsertedOrderId = $saveOrder->order_id;

        $order_id = $lastInsertedId;
        $merchant_order_id = $lastInsertedOrderId;

        if($request->payment_method == 1)
        {
            $decode = createCredimaxSessionForEventOrders($order_id, $merchant_order_id);
            $response = json_decode($decode, true);
            if($response['error'] == 0)
            {
                $session_id = $response['session_id'];

                // paymentURL
                $paymentURL = (!empty($session_id)) ? "https://credimax.gateway.mastercard.com/checkout/pay/".$session_id : '';

                $result['statusCode'] = "200";
                $result['order_id'] = $order_id;
                $result['merchant_order_id'] = $merchant_order_id;
                $result['paymentURL'] = $paymentURL;
                $result['message'] = '';
                return response()->json($result);
            }
            else
            {
                $msg = $response['msg'];
                $result['statusCode'] = "300";
                $result['order_id'] = $order_id;
                $result['merchant_order_id'] = $merchant_order_id;
                $result['paymentURL'] = '';
                $result['message'] = $msg;
                return response()->json($result, 300);
            }
        }

        if($request->payment_method == 2)
        {
            $baseUrl = $this->getBaseUrl();
            $arrContextOptions= [
                'ssl' => [
                    'verify_peer'=> false,
                    'verify_peer_name'=> false,
                ],
            ];
            $merchantOrderId = strtr(base64_encode($merchant_order_id), '+/=', '-_,');
            $amount = strtr(base64_encode($totalAmountToPay), '+/=', '-_,');

            // $json = json_decode(file_get_contents($baseUrl.'/benefits/request.php?merchantOrderId='.$merchant_order_id.'&mobile=1&orderAmount='.$totalAmountToPay,false,stream_context_create($arrContextOptions)), true);
            $result['statusCode'] = "200";
            $result['order_id'] = (string)$order_id;
            $result['merchant_order_id'] = (string)$merchant_order_id;
            $result['paymentURL'] = $baseUrl.'/benefits/request.php?merchantOrderId='.$merchantOrderId.'&mobile=1&orderAmount='.$amount;
            $result['message'] = '';
            return response()->json($result);
        }
    }

    // Developed by Nivedita
    //Function is for event list new screen
    public function getEventList()
    {
        $langId = $_GET['language_id'];
        $eventList = Events::getEventList($langId);
        $j = 0;
        $eventsList=[];
        $baseUrl = $this->getBaseUrl();
        //$photographerNotAvailable = ['NOPHOTOGRAPHERFOUND'];
        foreach($eventList as $eventData)
        {
            $eventsList[$j]['id'] = "".$eventData['id']."";
            $eventsList[$j]['title'] = $eventData['event_name'];
            $eventsList[$j]['image'] = $baseUrl.'/public/assets/images/events/'.$eventData['event_image'];
            $eventsList[$j]['query'] = $baseUrl."/api/v1/getPackageslist?language_id=".$langId.'&event_id='.$eventData['id'];
            $eventsList[$j]['type'] = "3";
            $eventsList[$j++]['navigationFlag'] = "1";
        }
        $event['componentId'] = "fourComponent";
        $event['sequenceId'] = "1";
        if(empty($eventsList) && count($eventsList)==0)
        $event['isActive'] = "0";
        else
        $event['isActive'] = "1";
        $event['fourComponentData']['list'] = $eventsList;
        if(!empty($eventsList) && count($eventsList)!=0)
        {
            $result['status'] = "OK";
            $result['statusCode'] = 200;
            $result['message'] = "Success";
            $result['component'][] = $event;
        }
        else
        {
            $result['status'] = "OK";
            $result['statusCode'] = 300;
            $result['message'] = "Success";
            $result['component'] = [];
        }
        return response()->json($result);

    }
}
