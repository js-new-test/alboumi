<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\MyAccountController;
use App\Http\Controllers\API\ChangePasswordController;
use App\Http\Controllers\API\MyAddressController;
use App\Http\Controllers\API\HeaderController;
use App\Http\Controllers\API\HomePageAPIController;
use App\Http\Controllers\API\PhotographerController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\LabelsController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\ContactUsController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\OrdersController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\BillingAddressController;
use App\Http\Controllers\API\PhotoBookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function() {
    Route::post('/signin', [LoginController::class, 'signIn']);
    Route::post('/signup', [RegisterController::class, 'signUp']);
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
    Route::get('/getAllLanguages', [HeaderController::class, 'getAllLanguages']);
    Route::any('/getPackageslist', [EventController::class, 'getPackageslist']);
    Route::post('/getEventEnquiry', [EventController::class, 'getEventEnquiry']);
    Route::post('/get-labels', [LabelsController::class, 'getLabels']);
    Route::get('/getCountrylist', [CountryController::class, 'getCountrylist']);
    Route::any('/getHelpCenterData', [SettingController::class, 'getHelpCenterData']);
    Route::post('/contact-us', [ContactUsController::class, 'contactUs']);
    Route::post('/getCategoryList', [CategoryController::class, 'getCategoryList']);
    Route::get('/getProductList', [ProductController::class, 'getProductList']);
    Route::get('/product/getAttributeGroupData', [ProductController::class, 'getAttributeGroupData']);
    Route::any('/getPhotographerProfile', [PhotographerController::class, 'getPhotographerProfile']);
    Route::any('/getNotificationList', [NotificationController::class, 'getNotificationList']);
    Route::get('/getAllCurrency', [HeaderController::class, 'getAllCurrency']);
    Route::post('/getFilterList', [CategoryController::class, 'getFilterList']);
    Route::post('/applyFilter', [ProductController::class, 'getFilteredProducts']);
    Route::post('/cart', [CartController::class, 'getMyCart']);
    Route::post('/remove-cart-item', [CartController::class, 'removeCartItem']);
    Route::post('/update-item-qty', [CartController::class, 'updateItemQTY']);
    Route::post('/remove-promo-code', [CartController::class, 'removePromoCode']);
    Route::post('/get-payment-method', [CartController::class, 'getPaymentMethod']);
    Route::post('/update-shipping-checkout-type', [CartController::class, 'updateShippingCheckoutType']);
    Route::post('/update-payment-method', [CartController::class, 'updatePaymentMethod']);
    Route::post('/applypromotion', [CartController::class, 'applyPromoCode']);
    Route::post('/uploadImage', [CartController::class, 'uploadImage']);
    Route::post('/nextdeliverydate', [ProductController::class, 'nextDeliveryDate']);
    Route::any('/getPhotoBookList', [PhotoBookController::class, 'getPhotoBookList']);
    Route::get('/getPhotographersList', [PhotographerController::class, 'getPhotographersList']);
    Route::post('/policies', [ApiController::class, 'getPolicies']);
});

Route::group(['prefix' => 'v1' , 'middleware'=>'auth:api'], function () {
    Route::post('/logout', [LoginController::class, 'logOut']);
    Route::post('/my-account', [MyAccountController::class, 'getMyAccount']);
    Route::post('/changepassword', [ChangePasswordController::class, 'changePassword']);
    Route::post('/my-profile', [MyAccountController::class, 'myProfile']);
    Route::post('/my-address-list', [MyAddressController::class, 'getCustomerAddressList']);
    Route::post('/add-address', [MyAddressController::class, 'addAddress']);
    Route::post('/update-address', [MyAddressController::class, 'updateAddress']);
    Route::post('/edit-address', [MyAddressController::class, 'editAddress']);
    Route::post('/delete-address', [MyAddressController::class, 'deleteAddress']);
    Route::get('/get-all-countries', [MyAddressController::class, 'getAllCountry']);
    Route::post('/submitEventEnquiry', [EventController::class, 'submitEventEnquiry']);
    Route::post('/update-profile', [MyAccountController::class, 'updateProfile']);
    Route::post('/my-orders', [OrdersController::class, 'getMyOrders']);
    Route::get('/orderdetails', [OrdersController::class, 'getMyOrderDetails']);
    Route::post('/track-order-item', [OrdersController::class, 'getTrackItem']);
    Route::post('/checkout-list', [CartController::class, 'getCheckoutList']);
    Route::post('/addtocart', [CartController::class, 'addToCart'])->withoutMiddleware('auth:api');
    Route::post('/design-tool-add-cart', [CartController::class, 'designToolAddToCart'])->withoutMiddleware('auth:api');
    Route::post('/addRecommendedToCart', [CartController::class, 'addRecommendedToCart'])->withoutMiddleware('auth:api');
    Route::post('/myEventEnquiries', [EventController::class, 'myEventEnquiries']);
    Route::get('/getProductDetails', [ProductController::class, 'getProductDetails'])->withoutMiddleware('auth:api');
    //Route::get('/getAttribute', [ProductController::class, 'getAttribute'])->withoutMiddleware('auth:api');
    Route::any('/applyVariant', [ProductController::class, 'applyVariant'])->withoutMiddleware('auth:api');
    Route::post('/getEventsAndGalleryList', [EventController::class, 'getEventsAndGalleryList']);
    Route::post('/getEventsAndGalleryDetails', [EventController::class, 'getEventsAndGalleryDetails']);
    Route::post('/eventOrderPayment', [EventController::class, 'createEventOrderPayment']);
    Route::get('/getEventList', [EventController::class, 'getEventList'])->withoutMiddleware('auth:api');

    //Billing Address
    Route::post('/billing-address-list', [BillingAddressController::class, 'getCustomerBillingAddressList']);
    Route::post('/add-billing-address', [BillingAddressController::class, 'addBillingAddress']);
    Route::post('/update-billing-address', [BillingAddressController::class, 'updateBillingAddress']);
    Route::post('/delete-billing-address', [BillingAddressController::class, 'deleteBillingAddress']);
    Route::post('/create-order', [OrdersController::class, 'createOrder']);

    // Order invoice
    Route::post('/generateOrderInvoice', [OrdersController::class, 'generateOrderInvoice']);

    Route::post('/home-page-component', [HomePageAPIController::class, 'homePageComponent'])->withoutMiddleware('auth:api');
});
