<?php

use Illuminate\Support\Facades\Route;

//Backend
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AttributeGroupController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ManufacturerController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\ContactUsController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\LocalizationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PromotionsController;
use App\Http\Controllers\Admin\AdditionalServiceController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\PhotographerController;
use App\Http\Controllers\Admin\FooterGeneratorController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\HomePageTextController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HowItWorksController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\HomePageContentController;
use App\Http\Controllers\Admin\HomePagePhotographerController;
use App\Http\Controllers\Admin\MegaMenuController;
use App\Http\Controllers\Admin\StoreLocationController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\EventEnqController;
use App\Http\Controllers\Admin\HowItWrksBnrsController;
use App\Http\Controllers\Admin\CustGroupController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\OrderInvoiceController;
use App\Http\Controllers\Admin\AramexController;
use App\Http\Controllers\Admin\EventPhotoSalesController;
use App\Http\Controllers\Admin\PhotoBookController;
use App\Http\Controllers\Admin\EventEnqPaymentsController;

//Frontend
use App\Http\Controllers\FrontEnd\FooterController;
use App\Http\Controllers\FrontEnd\SocialPhotosController;
use App\Http\Controllers\FrontEnd\HomeController as AlboumiHomeController;
use App\Http\Controllers\FrontEnd\CustomerDashboardController;
use App\Http\Controllers\FrontEnd\MyAccountController;
use App\Http\Controllers\FrontEnd\ChangePasswordController;
use App\Http\Controllers\FrontEnd\MyAddressController;
use App\Http\Controllers\FrontEnd\ContactUsController as FrontendContactUsController;
use App\Http\Controllers\FrontEnd\EventController;
use App\Http\Controllers\FrontEnd\LocaleController;
use App\Http\Controllers\FrontEnd\PhotographerController as FrontendPhotographerController;
use App\Http\Controllers\FrontEnd\CategoryController as FrontendCategoryController;
use App\Http\Controllers\FrontEnd\ProductController as FrontendProductController;
use App\Http\Controllers\FrontEnd\CurrencyController as FrontendCurrencyController;
use App\Http\Controllers\FrontEnd\CartController;
use App\Http\Controllers\FrontEnd\ShippindAddressController;
use App\Http\Controllers\FrontEnd\PaymentMethodController;
use App\Http\Controllers\FrontEnd\ReviewOrderController;
use App\Http\Controllers\FrontEnd\BillingController;
use App\Http\Controllers\FrontEnd\OrderController;
use App\Http\Controllers\FrontEnd\CredimaxController;
use App\Http\Controllers\FrontEnd\EventOrderController;
use App\Http\Controllers\FrontEnd\BenefitsController;
use App\Http\Controllers\FrontEnd\PhotoBookController as FrontendPhotoBookController;

//Middleware
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\PhotographerMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\PreventRouteAccessMiddleware;
use App\Http\Middleware\AuthenticateCustomer;
use App\Http\Middleware\AuthenticateAdmin;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Frontend
Route::get('/login', [AlboumiHomeController::class, 'showLogin'])->middleware(AuthenticateCustomer::class);
Route::post('/login', [AlboumiHomeController::class, 'customerLogin']);
Route::get('/signup', [AlboumiHomeController::class, 'showSignup'])->middleware(AuthenticateCustomer::class);
Route::post('/signup', [AlboumiHomeController::class, 'signup']);
Route::get('/forgot-password', [AlboumiHomeController::class, 'showForgotPassForm'])->middleware(AuthenticateCustomer::class);
Route::post('/forgot-password', [AlboumiHomeController::class, 'forgotPassword']);
Route::get('/verification-success/{code}', [AlboumiHomeController::class, 'emailVerificationSuccess']);
Route::get('/reset-password/{token}', [AlboumiHomeController::class, 'showResetPassForm']);
Route::post('/reset-password', [AlboumiHomeController::class, 'resetPassword'])->middleware(AuthenticateCustomer::class);;
Route::get('/reset-password-success', [AlboumiHomeController::class, 'resetPasswordSuccess']);

Route::get('auth/{provider}', [AlboumiHomeController::class, 'redirect']);
Route::get('auth/{provider?}/callback', [AlboumiHomeController::class, 'customerLogin']);


Route::get('authenticate/google-photos/{maxUpload?}/{type?}', [SocialPhotosController::class, 'redirectgp'])->name('gpredirect');
Route::get('authenticate/facebook-photos/{maxUpload?}/{type?}', [SocialPhotosController::class, 'redirectfb'])->name('fbredirect');
Route::get('authenticate/instagram-photos/{maxUpload?}/{type?}', [SocialPhotosController::class, 'redirectig'])->name('igredirect');
Route::get('auth/google/photo_callback', [SocialPhotosController::class, 'callbackgp']);
Route::get('auth/facebook/photo_callback', [SocialPhotosController::class, 'callbackfb']);
Route::get('auth/instagram/photo_callback', [SocialPhotosController::class, 'callbackig']);


Route::get('/getLocaleDetailsForLang', [CustomerDashboardController::class, 'getLocalDetailsForLang']);
Route::get('/getEmailTemplatesForLang', [CustomerDashboardController::class, 'getEmailTemplateForLang']);

Route::get('/contact-us', [FrontendContactUsController::class, 'showContactUs']);
Route::post('/contact-us', [FrontendContactUsController::class, 'responseInquery']);
Route::get('/{lang_code}/contact-us', [FrontendContactUsController::class, 'showContactUsWithLangCode']);
Route::post('/contact-us-with-lang-code', [FrontendContactUsController::class, 'responseInqueryWithLangCode']);
Route::get('/made-in-bahrain/{id}', [FrontendPhotographerController::class, 'photographerProfile']);
Route::get('/product/{slug}/{id?}', [FrontendProductController::class, 'showProductPage']);
Route::get('/getNextDeliveryDate/{qty}', [FrontendProductController::class, 'getNextDeliveryDateByajax']);
Route::post('/product/applyFilter', [FrontendProductController::class, 'getFilteredProducts']);
Route::post('/search', [FrontendProductController::class, 'getSearchResult']);

//Shopping Cart
Route::get('/shopping-cart/{id?}', [CartController::class, 'getShpCartData']);
Route::post('/add-to-cart', [CartController::class, 'postAddToCart']);
Route::post('/addRecommendedToCart', [CartController::class, 'postAddRecommendedToCart']);
Route::post('/add-remove-qty', [CartController::class, 'addRemoveQty']);
Route::post('/remove-product-from-cart', [CartController::class, 'removeProductFromCart']);
Route::post('/remove-promo-code', [CartController::class, 'removePromoCode']);
Route::post('/apply-promo-code', [CartController::class, 'applyPromoCode']);

// track order
Route::get('/trackOrder', [OrderController::class, 'showTrackOrdersFrom']);
Route::post('/trackOrder', [OrderController::class, 'getTrackOrdersDetails']);

Route::get('/order-confirmation', [OrderController::class, 'orderConfiremed'])->middleware(CustomerMiddleware::class);

//Route::post('/benefits/success', [BenefitsController::class, 'Success']);
Route::any('/benefits/success', [BenefitsController::class, 'Success']);
Route::any('/benefits/cancel', [BenefitsController::class, 'Cancel']);

// benefits
//Route::post('/eventorders/benefits/success', [EventOrderController::class, 'benefitsSuccess']);
Route::any('/eventorders/benefits/success', [EventOrderController::class, 'benefitsSuccess']);
Route::any('/eventorders/benefits/cancel', [EventOrderController::class, 'benefitsCancel']);

// Mobile credimax
Route::get('/mobile/credimax/success', [CredimaxController::class, 'Success']);
Route::get('/mobile/credimax/cancel', [CredimaxController::class, 'Cancel']);

// Mobile Event Photo Order Credimax
Route::get('/mobile/eventorders/credimax/success', [EventOrderController::class, 'credimaxSuccess']);
Route::get('/mobile/eventorders/credimax/cancel', [EventOrderController::class, 'credimaxCancel']);

//Delete Unused Cart Data
Route::get('/deleteunusedcart', [CartController::class, 'deleteUnusedCart']);
// PP 
Route::get('/page/privacy-policy', [AlboumiHomeController::class, 'getPrivacyPolicyContents']);
Route::get('/page/about-us', [AlboumiHomeController::class, 'getAboutUsPageContents']);
Route::get('/page/{slug}', [AlboumiHomeController::class, 'getPolicyPagesContents']);

Route::prefix('customer')->middleware([CustomerMiddleware::class])->group(function () {
    Route::get('/logout', [AlboumiHomeController::class, 'logout']);
    Route::get('/dashboard', [CustomerDashboardController::class, 'dashboard']);
    Route::get('/my-account', [MyAccountController::class, 'showMyAccount']);
    Route::post('/save-my-account', [MyAccountController::class, 'saveMyAccount']);
    Route::get('/change-password', [ChangePasswordController::class, 'showChangePassForm']);
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword']);
    Route::get('/my-address', [MyAddressController::class, 'showMyaddress']);
    Route::post('/my-address', [MyAddressController::class, 'saveMyaddress']);
    Route::post('/change-default-address', [MyAddressController::class, 'changeDefaultAddress']);
    Route::post('/delete-address', [MyAddressController::class, 'deleteAddress']);
    Route::post('/get-ajax-address', [MyAddressController::class, 'getAjaxAddress']);
    Route::get('/states/{id}', [MyAddressController::class, 'getStates']);
    Route::get('/cities/{id}', [MyAddressController::class, 'getCities']);
    Route::get('events/enqSubmitSuccess', [EventController::class, 'enqSubmitSuccess']);
    Route::post('events/submitEventEnq', [EventController::class, 'submitEventEnq']);
    Route::get('shipping-address', [ShippindAddressController::class, 'getShippingAddress'])->withoutMiddleware([CustomerMiddleware::class]);;
    Route::post('save-shipping-address', [ShippindAddressController::class, 'saveCustomerShipAddress']);
    Route::post('save-delivery-type', [ShippindAddressController::class, 'saveDeliveryType']);
    Route::post('edit-shippind-address', [ShippindAddressController::class, 'editShippingAddress']);
    Route::post('update-shippind-address', [ShippindAddressController::class, 'updateShippingAddress']);
    Route::get('payment-method', [PaymentMethodController::class, 'getPaymentMethod']);
    Route::post('save-payment-method', [PaymentMethodController::class, 'savePaymentMethod']);
    Route::get('review-order', [ReviewOrderController::class, 'getReviewOrder']);
    Route::get('/billing-address', [BillingController::class, 'showMyaddress']);
    Route::post('/billing-address', [BillingController::class, 'saveMyaddress']);
    Route::post('/change-default-billing-address', [BillingController::class, 'changeDefaultAddress']);
    Route::post('/delete-billing-address', [BillingController::class, 'deleteAddress']);
    Route::post('/get-ajax-billing-address', [BillingController::class, 'getAjaxAddress']);
    Route::get('/billing-address/states/{id}', [BillingController::class, 'getStates']);
    Route::get('/billing-address/cities/{id}', [BillingController::class, 'getCities']);
    Route::get('/my-orders', [OrderController::class, 'getOrders']);
    Route::get('/orderdetails', [OrderController::class, 'getOrdersDetails']);
    Route::get('/myEventEnquiries', [EventController::class, 'myEventEnquiries']);
    Route::get('/myEventGallery', [EventController::class, 'myEventGallery']);
    Route::get('/eventGallery/{id}/{btnVal}', [EventController::class, 'getEventGalleryImages']);
    Route::post('/add-billing-address', [ReviewOrderController::class, 'saveBillingAddress']);
    Route::post('/setSelectedImages', [EventController::class, 'setSelectedImages']);
    Route::post('/downloadImage', [EventController::class, 'downloadImage']);
    Route::get('/place-order', [ReviewOrderController::class, 'placeOrder']);
    Route::post('/buyEventPhotos', [EventController::class, 'buyEventPhotos']);
    // credimax
    Route::get('/eventorders/credimax/success', [EventOrderController::class, 'credimaxSuccess']);
    Route::get('/eventorders/credimax/cancel', [EventOrderController::class, 'credimaxCancel']);
    Route::get('/eventorders/createEventOrderPayment', [EventOrderController::class, 'createEventOrderPayment']);

    Route::get('/create-payment', [CredimaxController::class, 'createPayment']);
    Route::get('/credimax/success', [CredimaxController::class, 'Success']);
    Route::get('/credimax/cancel', [CredimaxController::class, 'Cancel']);

    Route::post('add-new-shippind-address', [ShippindAddressController::class, 'addNewShippingAddress']);

    Route::post('/store-order-message', [ReviewOrderController::class, 'storeOrderMessage']);

    Route::post('/getSelectedProdPrice', [EventController::class, 'getSelectedProdPrice']);

    Route::post('edit-billing-address', [ReviewOrderController::class, 'editBillingAddress']);
    Route::post('update-billing-address', [ReviewOrderController::class, 'updateBillingAddress']);
    Route::post('set-same-as-ship-address', [ReviewOrderController::class, 'setShippingAddress']);
    Route::post('set-same-as-deli-address', [ReviewOrderController::class, 'setDeliveryAddress']);

});
// Category
Route::get('/category/{slug}/{sortBy?}', [FrontendCategoryController::class, 'getCategoryAndProducts']);

// event enq payment
Route::get('/eventEnq/createEventEnqOrderPayment', [EventOrderController::class, 'createEventEnqOrderPayment']);
Route::get('eventEnq/payment/{eventEnqId}',[EventOrderController::class, 'getEventEnqPayment']);
Route::post('eventEnq/paymentOfEventEnq',[EventOrderController::class, 'paymentOfEventEnq']);
Route::get('/eventEnq/credimax/success', [EventOrderController::class, 'eventEnqCredimaxSuccess']);
Route::get('/eventEnq/credimax/cancel', [EventOrderController::class, 'eventEnqCredimaxCancel']);

Route::post('/eventEnq/benefits/success', [EventOrderController::class, 'eventEnqBenefitsSuccess']);
Route::any('/eventEnq/benefits/cancel', [EventOrderController::class, 'eventEnqBenefitsCancel']);

// dynamic footer
Route::get('/getFooterLinks', [FooterController::class, 'getFooterLinks']);
Route::get('/about-us', [FooterController::class, 'getAboutUsPageContents']);
Route::post('/getLangSpecificData', [LocaleController::class, 'getLangSpecificData']);
Route::post('/getCurrSpecificData', [FrontendCurrencyController::class, 'getCurrSpecificData']);

// Events listing and detail
Route::get('events-occasions', [EventController::class, 'getEventsListing']);
Route::get('events/{id}', [EventController::class, 'getEventDetails']);

// photographer listing
Route::get('made-in-bahrain', [FrontendPhotographerController::class, 'getPhotographersListing']);

// Photo book page
Route::get('photo-book', [FrontendPhotoBookController::class, 'getBookDetails']);

//Home and footer pages
Route::get('/{slug?}', [AlboumiHomeController::class, 'home']);

//Clear Cache facade value:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

Route::get('brands', [ManufacturerController::class, 'getBrands']);

// Admin Group
Route::prefix('admin')->middleware([AdminMiddleware::class])->group(function () {

    Route::get('/access-denied', [AdminController::class, 'accessDenied'])->withoutMiddleware([AdminMiddleware::class]);

    // Login Routes...
    Route::get('login', [AdminController::class, 'showLoginForm'])->withoutMiddleware([AdminMiddleware::class])->middleware(AuthenticateAdmin::class);
    Route::post('login', [AdminController::class, 'login'])->withoutMiddleware([AdminMiddleware::class]);
    Route::get('/logout', [AdminController::class, 'logout']);

    //Admin Dashboard
    Route::get('dashboard', [DashboardController::class, 'dashboard']);

     //Roles & Permissions Routes
    Route::get('user/role/add', [RoleController::class, 'getRoleForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('user/role/add', [RoleController::class, 'addRole']);
    Route::get('user/role/list', [RoleController::class, 'getListOfRoles'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('role/permissions/{id}', [RoleController::class, 'getPermissions'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('role/permissions/{id}', [RoleController::class, 'getPermissions']);
    Route::get('user/role/edit/{id}', [RoleController::class, 'editRole'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('user/role/update', [RoleController::class, 'updateRole']);
    Route::post('user/role/delete', [RoleController::class, 'deleteRole'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('user/search_role', [RoleController::class, 'searchRole']);

    Route::get('manufacturers', [ManufacturerController::class, 'index'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('manufacturers/list', [ManufacturerController::class, 'getManufactuerList']);
    Route::get('manufacturers/add', [ManufacturerController::class, 'getAddManufactuer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('manufacturers/add', [ManufacturerController::class, 'postAddManufactuer']);
    Route::get('manufacturers/edit/{id}', [ManufacturerController::class, 'getEditManufactuer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('manufacturers/edit', [ManufacturerController::class, 'postEditManufactuer']);
    Route::get('/{id}/showBrand', [ManufacturerController::class, 'getShowBrand'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('manufacturers/deleteBrand', [ManufacturerController::class, 'getDeleteBrand'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('manufacturers/export', [ManufacturerController::class, 'getExportBrand'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('manufacturers/languageWiseBrand', [ManufacturerController::class, 'getLanguageWiseBrand'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('manufacturers/languageBrandData', [ManufacturerController::class, 'getLanguageBrandData'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('manufacturers/updateStatus', [ManufacturerController::class, 'getBrandUpdateStatus'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('manufacturers/upload-brand-image', [ManufacturerController::class, 'uploadCKeditorBrandImage'])->name('ckeditor.upload_brand_image');

    //Attribute Groups
    Route::get('attributeGroup', [AttributeGroupController::class, 'getListOfAttributeGroups'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('attributeGroup/list', [AttributeGroupController::class, 'getAttributeGroupData']);
    Route::get('attributeGroup/addAttributeGroup', [AttributeGroupController::class, 'attributeGroupAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('attributeGroup/addAttributeGroup', [AttributeGroupController::class, 'addAttributeGroup']);
    Route::get('attributeGroup/edit/{id}', [AttributeGroupController::class, 'attributeGroupEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('attributeGroup/updateAttributeGroup', [AttributeGroupController::class, 'updateAttributeGroup']);
    Route::get('attributeGroup/deleteAttributeGroup', [AttributeGroupController::class, 'deleteAttributeGroup'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('attributeGroup/languageWiseAttrGroup', [AttributeGroupController::class, 'getLanguageWiseAttrGroup'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('getcategories/{id}', [AttributeGroupController::class, 'getCategoriesForAttribute'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('attributeGroup/activeInactiveAttrGroup', [AttributeGroupController::class, 'attributeGroupActiveInactive']);

    // Attributes
    Route::get('attribute', [AttributeController::class, 'getListOfAttribute'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('attribute/list', [AttributeController::class, 'getAttributeData']);
    Route::get('attribute/addAttribute', [AttributeController::class, 'attributeAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('attribute/addAttribute', [AttributeController::class, 'addAttribute']);
    Route::get('attribute/edit/{id}', [AttributeController::class, 'attributeEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('attribute/updateAttribute', [AttributeController::class, 'updateAttribute']);
    Route::get('attribute/deleteAttribute', [AttributeController::class, 'deleteAttribute'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('addAttrTypeBlock', [AttributeController::class, 'addAttrTypeBlock']);
    Route::get('attribute/languageWiseAttr', [AttributeController::class, 'getLanguageWiseAttr'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('attribute/getAttributeType/{id}', [AttributeController::class, 'getAttributeType'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('attribute/activeInactiveAttribute', [AttributeController::class, 'attributeActiveInactive']);

    //Users Routes
    Route::get('user/list', [UserController::class, 'getUserList'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('user/add', [UserController::class, 'getUserForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('user/add', [UserController::class, 'addUser']);
    Route::get('user/edit/{id}', [UserController::class, 'editUser'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('user/update', [UserController::class, 'updateUser']);
    Route::get('user/export', [UserController::class, 'exportUsers'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('user/import', [UserController::class, 'getimportUsersForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('user/import', [UserController::class, 'importUser']);
    Route::get('user/{id}/delete', [UserController::class, 'deleteUser'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('user/activate-deactivate', [UserController::class, 'userActDeaAct']);

    // Events
    Route::get('event/list', [EventsController::class, 'getListOfEvents'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('event/addEvent', [EventsController::class, 'eventsAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('event/addEvent', [EventsController::class, 'addEvent']);
    Route::get('event/editEvent/{id}', [EventsController::class, 'eventEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('event/updateEvent', [EventsController::class, 'updateEvent']);
    Route::post('event/activeDeactiveEvent', [EventsController::class, 'eventActiveInactive'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('event/{id}/deleteEvent', [EventsController::class, 'deleteEvent'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('event/exportEvent', [EventsController::class, 'getExportEvents'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('event-feature/delete', [EventsController::class, 'deleteEventFeature']);
    Route::get('event-feature/onchange-event-feature/{id}', [EventsController::class, 'getEventFeature'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('event/list/filter-event', [EventsController::class, 'filterEvent']);
    Route::post('event/upload-event-image', [EventsController::class, 'uploadCKeditorEventImage'])->name('ckeditor.upload_event_image');

    // Packages
    Route::get('package/list', [PackageController::class, 'getListOfPackages'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('package/addPackage', [PackageController::class, 'packageAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('package/addPackage', [PackageController::class, 'addPackage']);
    Route::post('package/{id}/deletePackage', [PackageController::class, 'deletePackage'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('package/activeDeactivePackage', [PackageController::class, 'packageActiveInactive'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('package/editPackage/{id}', [PackageController::class, 'packageEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('package/updatePackage', [PackageController::class, 'updatePackage']);
    Route::get('package/exportPackages', [PackageController::class, 'getExportPackages'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('package/list/filter-package', [PackageController::class, 'filterPackage']);

    //Customer Routes
    Route::get('customer/list', [CustomerController::class, 'getCustomerList'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('customer/edit/{id}', [CustomerController::class, 'editCustomer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('customer/update', [CustomerController::class, 'updateAccountCustomer']);
    Route::get('customer/export', [CustomerController::class, 'exportCustomer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('customer/import', [CustomerController::class, 'getimportCustomerForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('customer/import', [CustomerController::class, 'importCustomer']);
    Route::get('customer/{id}/delete', [CustomerController::class, 'deleteCustomer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('customer/activate-deactivate', [CustomerController::class, 'customerActDeaAct'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('customer/states/{id}', [CustomerController::class, 'getStates']);
    Route::get('customer/cities/{id}', [CustomerController::class, 'getCities']);
    Route::post('customer/address', [CustomerController::class, 'addCustomerAddress']);
    Route::get('customer/address/edit/{id}', [CustomerController::class, 'editCustAddress'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('customer/address/update', [CustomerController::class, 'updateCustomerAddress']);
    Route::get('customer/address/delete/{id}', [CustomerController::class, 'deleteCustAddress'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('customer/assignCustGroup', [CustomerController::class, 'assignCustGroup']);
    Route::post('customer/removeCustGroup', [CustomerController::class, 'removeCustGroup']);

    // Customer groups
    Route::get('custGroups', [CustGroupController::class, 'getCustomerGroups'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('custGroups/list', [CustGroupController::class, 'getCustomerGroupsList']);
    Route::get('custGroups/addGroup', [CustGroupController::class, 'groupAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('custGroups/addGroup', [CustGroupController::class, 'addGroup'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('custGroups/{id}/deleteGroup', [CustGroupController::class, 'deleteGroup'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('custGroups/editGroup/{id}', [CustGroupController::class, 'groupEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('custGroups/updateGroup', [CustGroupController::class, 'updateSGroup']);

    //Currency
    Route::get('currency/list', [AdminController::class, 'listCurrency'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('currency/add', [AdminController::class, 'showAddCurrForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('currency/add', [AdminController::class, 'addCurrency']);
    Route::get('currency/edit/{id}', [AdminController::class, 'editCurrency'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('currency/update', [AdminController::class, 'updateCurrency']);
    Route::post('currency/delete', [AdminController::class, 'deleteCurrency'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('currency/default', [AdminController::class, 'defaultCurrency']);

    //Language
    Route::get('language/add', [AdminController::class, 'showAddLanguageForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('language/add', [AdminController::class, 'addLanguage']);
    Route::get('language/list', [AdminController::class, 'listLanguage'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('language/edit/{id}', [AdminController::class, 'editLanguage'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('language/update', [AdminController::class, 'updateLanguage']);
    Route::post('language/delete', [AdminController::class, 'deleteLanguage'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('language/default', [AdminController::class, 'defaultLanguage']);
    Route::post('language/changestatus', [AdminController::class, 'changeStatus']);

    //Profile
    Route::get('/profile', [AdminController::class, 'profile']);
    Route::post('/update-profile', [AdminController::class, 'updateProfile']);

    //Forgot Password
    Route::get('/forgot-password', [AdminController::class, 'showForgotPassForm'])->withoutMiddleware([AdminMiddleware::class])->middleware(AuthenticateAdmin::class);
    Route::post('/forgot-password', [AdminController::class, 'forgotPassword'])->withoutMiddleware([AdminMiddleware::class]);

    Route::get('/reset-password/{token}', [AdminController::class, 'showResetPassForm'])->withoutMiddleware([AdminMiddleware::class]);
    Route::post('/reset-password', [AdminController::class, 'resetPassword'])->withoutMiddleware([AdminMiddleware::class]);

    //Change Password
    Route::get('/change/password', [AdminController::class, 'changePasswordForm']);
    Route::post('/change/password', [AdminController::class, 'changePassword']);

    // Multilanguage email templates
    Route::get('emailTemplates/list', [EmailTemplateController::class, 'getmultiLangEmailTemplatesList'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('emailTemplates/addTemplate', [EmailTemplateController::class, 'templateAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('emailTemplates/addTemplate', [EmailTemplateController::class, 'addEmailTemplate']);
    Route::post('emailTemplates/activeDeactiveTemplate', [EmailTemplateController::class, 'templateActiveInactive']);
    Route::post('emailTemplates/{id}/deleteTemplate', [EmailTemplateController::class, 'deleteTemplate'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('emailTemplates/editTemplate/{id}', [EmailTemplateController::class, 'templateEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('emailTemplates/updateTemplate', [EmailTemplateController::class, 'updateTemplate']);
    Route::post('emailTemplates/upload-event-template-image', [EmailTemplateController::class, 'uploadCKeditorEventTemplateImage'])->name('ckeditor.upload_event_template_image');

	//Contact Us backend
    Route::get('/contactUs', [ContactUsController::class, 'getContactUs'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/contactUs/contactUsData', [ContactUsController::class, 'getContactUsData']);
    Route::get('/contactUs/inquiry', [ContactUsController::class, 'getContactUsInquiry'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/contactUs/reply', [ContactUsController::class, 'postContactUsReply']);
    Route::post('/contactUs/deleteInquiry', [ContactUsController::class, 'postDeleteInquiry'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/contactUs/upload-contact-us-image', [ContactUsController::class, 'uploadCKeditorContactUsImage'])->name('ckeditor.upload_contact_us_image');

    //Localization
    Route::get('/locale', [LocalizationController::class, 'listLocales'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/locale/add', [LocalizationController::class, 'showLocaleForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/locale/add', [LocalizationController::class, 'addLocale']);
    Route::get('/locale/edit/{id}', [LocalizationController::class, 'editLocale'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/locale/update', [LocalizationController::class, 'updateLocale']);
    Route::post('/locale/delete', [LocalizationController::class, 'deleteLocale'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/locale/filter', [LocalizationController::class, 'filterLocale']);
    Route::get('/locale/import', [LocalizationController::class, 'getImportForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/locale/import', [LocalizationController::class, 'importLocalization']);
    Route::get('/locale/export-locale/{id}', [LocalizationController::class, 'exportLocalization'])->middleware(PreventRouteAccessMiddleware::class);

    // Settings - FAQs
    Route::get('faq', [SettingsController::class, 'getFaqList'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('faq/list', [SettingsController::class, 'getFaqData']);
    Route::get('faq/addFaq', [SettingsController::class, 'faqAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('faq/addFaq', [SettingsController::class, 'addFaq']);
    Route::post('faq/activeDeactivefaq', [SettingsController::class, 'faqActiveInactive'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('faq/{id}/deleteFaq', [SettingsController::class, 'deleteFaq'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('faq/editFaq/{id}', [SettingsController::class, 'faqEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('faq/updateFaq', [SettingsController::class, 'updateFaq']);
    Route::get('faq/filterFaq/{lang_id}', [SettingsController::class, 'getFilteredData']);

    //currency conversion
    Route::get('/currencyConversion', [CurrencyController::class, 'getCurrencyConversion'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/currencyConversion', [CurrencyController::class, 'postCurrencyConversion']);
    Route::get('/currencyConversion/remainingCurrencies', [CurrencyController::class, 'getRemainingCurrencies']);

    // Settings - Country
    Route::get('/countries', [SettingsController::class, 'getCountries'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/countries/list', [SettingsController::class, 'getCountryData']);
    Route::post('countries/{id}/deleteCountry', [SettingsController::class, 'deleteCountry'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('countries/activeDeactiveCountry', [SettingsController::class, 'countryActiveInactive'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('countries/addCountry', [SettingsController::class, 'countryAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('countries/addCountry', [SettingsController::class, 'addCountry']);
    Route::get('countries/editCountry/{id}', [SettingsController::class, 'countryEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('countries/updateCountry', [SettingsController::class, 'updateCountry']);

    // Settings - Footer
    Route::get('/footerDetails', [SettingsController::class, 'footerDetailsView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/updateFooterDetails', [SettingsController::class, 'updateFooterDetails']);
    Route::post('/home-page-comp-act-deact', [SettingsController::class, 'homePageCompActDeact']);
    Route::post('/home-page-mobile-app-act-deact', [SettingsController::class, 'homePageMobileAppActDeact']);
    Route::post('/add-home-page-mobile-app-image', [SettingsController::class, 'addHomePageMobileAppImage']);
    Route::post('/add-update-admin-email', [SettingsController::class, 'addMultipleAdminEmails']);

    //Banner
    Route::get('/banner', [BannerController::class, 'listBanners'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/banner/add', [BannerController::class, 'showAddBannerFomr'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/banner/add', [BannerController::class, 'addBanner']);
    Route::get('/banner/edit/{id}', [BannerController::class, 'editBanner'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/banner/update', [BannerController::class, 'updateBanner']);
    Route::post('/banner/delete', [BannerController::class, 'deleteBanner'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/banner/activate-deactivate', [BannerController::class, 'bannerActDeact'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/banner/filter-banner', [BannerController::class, 'filterBanner']);

    //Promotions route
    Route::get('/promotions', [PromotionsController::class, 'getPromotions'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/promotions/list', [PromotionsController::class, 'getPromotionList']);
    Route::get('/promotions/addPromotion', [PromotionsController::class, 'getAddPromotion'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/promotions/addPromotion', [PromotionsController::class, 'postAddPromotion']);
    Route::get('/promotions/editPromotion/{id}', [PromotionsController::class, 'getEditPromotion'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/promotions/editPromotion', [PromotionsController::class, 'postEditPromotion']);
    Route::post('/promotions/updatePromotion', [PromotionsController::class, 'postUpdatePromotion']);
    Route::post('/promotions/promotionConditions', [PromotionsController::class, 'postPromotionConditions']);
    Route::get('/promotions/manufacturersList', [PromotionsController::class, 'getPromotionManufactuerList']);
    Route::get('/promotions/brands', [PromotionsController::class, 'getPromotionBrands']);
    Route::get('/promotions/categories', [PromotionsController::class, 'getPromotionCategories']);
    Route::get('/promotions/categoryList', [PromotionsController::class, 'getPromotionCategoryList']);
    Route::get('/promotions/productList', [PromotionsController::class, 'getPromotionProductList']);
    Route::get('/promotions/generateAutoPromotionCode', [PromotionsController::class, 'getGenerateAutoPromotionCode']);
    Route::post('/promotions/deletePromotion', [PromotionsController::class, 'postDeletePromotion']);
    Route::post('/promotions/upload-cms-page-image', [PromotionsController::class, 'uploadCKeditorPromotionImage'])->name('ckeditor.upload_promotion_image');


    //Event & Packages Additional Services
    Route::get('/additional-service', [AdditionalServiceController::class, 'listServices'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/additional-service/add', [AdditionalServiceController::class, 'showAdditionalServiceForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/additional-service/add', [AdditionalServiceController::class, 'addAdditionalService']);
    Route::get('/additional-service/edit/{id}', [AdditionalServiceController::class, 'editAdditionalService'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/additional-service/update', [AdditionalServiceController::class, 'updateService']);
    Route::post('/additional-service/delete', [AdditionalServiceController::class, 'deleteService'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/additional-service/activate-deactivate', [AdditionalServiceController::class, 'serviceActDeact'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/additional-service/filter-additional-service', [AdditionalServiceController::class, 'filterAdditionalService']);
    Route::post('/additional-service/requirement/delete', [AdditionalServiceController::class, 'deleteRequirement']);
    Route::post('/additional-service/samples/delete', [AdditionalServiceController::class, 'deleteSamples']);

    //CMS Pages
    Route::get('/cmsPages', [CmsController::class, 'getCmsPages'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/cmsPages/list', [CmsController::class, 'getCmsPagesList']);
    Route::get('cmsPages/addPage', [CmsController::class, 'pageAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('cmsPages/addPage', [CmsController::class, 'addPage']);
    Route::post('cmsPages/activeDeactivePage', [CmsController::class, 'pageActiveInactive'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('cmsPages/edit/{id}', [CmsController::class, 'pageEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('cmsPages/updatePage', [CmsController::class, 'updatePage']);
    Route::get('cmsPages/deletePage', [CmsController::class, 'deletePage'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('cmsPages/languageWisePage', [CmsController::class, 'getLanguageWisePage']);
    Route::post('cmsPages/upload-cms-page-image', [CmsController::class, 'uploadCMSPageImage'])->name('ckeditor.upload_cms_page_image');

    // Bahrain Photographers
    Route::get('/photgraphers', [PhotographerController::class, 'getPhotographers'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/photgraphers/list', [PhotographerController::class, 'getPhotographersList']);
    Route::get('photgraphers/addPhotographer', [PhotographerController::class, 'photgrapherAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('photgraphers/addPhotographer', [PhotographerController::class, 'addPhotgrapher'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('photgraphers/edit/{id}', [PhotographerController::class, 'photographerEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('photgraphers/updatePhotographer', [PhotographerController::class, 'updatePhotographer']);
    Route::post('photgraphers/activeDeactivePhotographer', [PhotographerController::class, 'photographerActiveInactive'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('photgraphers/deletePhotographer', [PhotographerController::class, 'deletePhotographer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('photgraphers/languageWisePhotographer', [PhotographerController::class, 'getLanguageWisePhotographer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('photgraphers/addPortfolio', [PhotographerController::class, 'postPortfolio']);
    Route::get('/photgraphers/portfolio/list', [PhotographerController::class, 'getPortfolioList'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/photgraphers/portfolio/getPortfolio', [PhotographerController::class, 'getPortfolioData']);
    Route::post('photgraphers/updatePortfolio', [PhotographerController::class, 'updatePortfolio']);
    Route::get('photgraphers/deletePortfolio', [PhotographerController::class, 'deletePortfolio'])->middleware(PreventRouteAccessMiddleware::class);

    // Footer Generator
    Route::get('/footer-generator', [FooterGeneratorController::class, 'listFooterGenerator'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/footer-generator/add', [FooterGeneratorController::class, 'showFooterGenForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/footer-generator/add', [FooterGeneratorController::class, 'addFooterGen']);
    Route::get('/footer-generator/edit/{id}', [FooterGeneratorController::class, 'editFooterGenForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/footer-generator/update', [FooterGeneratorController::class, 'updateFooterGen']);
    Route::post('/footer-generator/delete', [FooterGeneratorController::class, 'deleteFooterGen'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/footer-generator/parent/delete', [FooterGeneratorController::class, 'deleteParentFooterGen']);
    Route::post('/footer-generator/filter-footer-generator', [FooterGeneratorController::class, 'filterFooterGen']);

    //Seller routes
    Route::get('sellers', [SellerController::class, 'getSellers'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('sellers/list', [SellerController::class, 'getSellersList']);
    Route::get('seller/addSeller', [SellerController::class, 'getAddSeller'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('seller/addSeller', [SellerController::class, 'postAddSeller']);
    Route::get('seller/editSeller/{id}', [SellerController::class, 'getEditSeller'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('seller/editSeller', [SellerController::class, 'postEditSeller']);
    Route::get('seller/deleteSeller', [SellerController::class, 'getDeleteSeller'])->middleware(PreventRouteAccessMiddleware::class);

    //Phot Book routes
    Route::get('books', [PhotoBookController::class, 'getBooks'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('books/list', [PhotoBookController::class, 'getBooksList']);
    Route::get('books/addBook', [PhotoBookController::class, 'getAddBook'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('books/addBook', [PhotoBookController::class, 'postAddBook']);
    Route::get('books/editBook/{id}', [PhotoBookController::class, 'getEditBook'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('books/editBook', [PhotoBookController::class, 'postEditBook']);
    Route::get('books/deleteBook', [PhotoBookController::class, 'getDeleteBook'])->middleware(PreventRouteAccessMiddleware::class);

    //Home Page Text
    Route::get('/home-page-text', [HomePageTextController::class, 'listHomePageText'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/home-page-text/add', [HomePageTextController::class, 'showHomePageTextForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/home-page-text/add', [HomePageTextController::class, 'addHomePageText']);
    Route::get('/home-page-text/edit/{id}', [HomePageTextController::class, 'editHomePageText'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/home-page-text/update', [HomePageTextController::class, 'updateHomePageText']);
    Route::post('/home-page-text/delete', [HomePageTextController::class, 'deleteHomePageText'])->middleware(PreventRouteAccessMiddleware::class);

    // Services routes
    Route::get('services', [ServicesController::class, 'getServices'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('services/list', [ServicesController::class, 'getServicesList']);
    Route::get('services/addService', [ServicesController::class, 'serviceAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('services/addService', [ServicesController::class, 'addService'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('services/activeDeactiveService', [ServicesController::class, 'serviceActiveInactive']);
    Route::post('services/{id}/deleteService', [ServicesController::class, 'deleteService'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('services/editService/{id}', [ServicesController::class, 'serviceEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('services/updateService', [ServicesController::class, 'updateService']);

    // Collection routes
    Route::get('collection', [CollectionController::class, 'getCollection'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('collection/list', [CollectionController::class, 'getCollectionList']);
    Route::get('collection/addCollection', [CollectionController::class, 'collectionAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('collection/addCollection', [CollectionController::class, 'addCollection']);
    Route::post('collection/activeDeactiveCollection', [CollectionController::class, 'collectionActiveInactive']);
    Route::post('collection/{id}/deleteCollection', [CollectionController::class, 'deleteCollection'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('collection/editCollection/{id}', [CollectionController::class, 'collectionEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('collection/updateCollection', [CollectionController::class, 'updateCollection']);

    // Category Routes
    Route::get('/categories', [CategoryController::class, 'getCategories'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/categories/list', [CategoryController::class, 'getCategoriesList']);
    Route::get('categories/addCategory', [CategoryController::class, 'categoryAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('categories/addCategory', [CategoryController::class, 'addCategory']);
    Route::post('categories/activeDeactiveCategory', [CategoryController::class, 'categoryActiveInactive'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('categories/deleteCategory', [CategoryController::class, 'deleteCategory'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('categories/languageWiseCategory', [CategoryController::class, 'getLanguageWiseCategory']);
    Route::get('categories/edit/{id}', [CategoryController::class, 'categoryEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('categories/updateCategory', [CategoryController::class, 'updateCategory']);
    Route::get('categories/getCategory/{id}', [CategoryController::class, 'getCategory']);
    Route::post('categories/upload-category-image', [CategoryController::class, 'uploadCategoryImage'])->name('ckeditor.upload_category_image');

    //How it works
    Route::get('howitWorks', [HowItWorksController::class, 'getHowitWorks'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('howitWorks/list', [HowItWorksController::class, 'getHowitWorksList']);
    Route::get('howitWorks/addHowitWorks', [HowItWorksController::class, 'getAddHowitWorks'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('howitWorks/addHowitWorks', [HowItWorksController::class, 'postAddHowitWorks'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('howitWorks/editHowItWorks/{id}', [HowItWorksController::class, 'getEditHowitWorks'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('howitWorks/editHowitWorks', [HowItWorksController::class, 'postEditHowitWorks'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('howitWorks/updateHowitWorksStatus', [HowItWorksController::class, 'postUpdateHowitWorksStatus'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('howitWorks/deleteHowitWorks', [HowItWorksController::class, 'getDeleteHowitWorks'])->middleware(PreventRouteAccessMiddleware::class);

    Route::get('/products', [ProductController::class, 'getProduct']);
    Route::get('/product/list', [ProductController::class, 'getAllProducts']);
    Route::get('/product/activeList', [ProductController::class, 'getAllActiveProducts']);
    Route::get('/product/inactiveList', [ProductController::class, 'getAllInactiveProducts']);
    Route::get('/product/rejectedList', [ProductController::class, 'getAllRejectedProducts']);
    Route::get('/product/outOfStockList', [ProductController::class, 'getAllOutOfStockProducts']);
    Route::get('/product/addProduct', [ProductController::class, 'getAddProduct']);
    Route::post('/product/addProduct', [ProductController::class, 'postAddProduct']);
    Route::get('/product/relatedProduct', [ProductController::class, 'getRelatedProduct']);
    Route::get('/product/recommendedProduct', [ProductController::class, 'getRecommendedProduct']);
    Route::post('/product/recomendedProduct', [ProductController::class, 'postRecomendedProduct']);
    Route::post('/product/relatedProduct', [ProductController::class, 'postRelatedProduct']);
    Route::post('/product/editProduct', [ProductController::class, 'postEditProduct']);
    Route::post('/product/editPricingOption', [ProductController::class, 'postEditPricingOption']);
    Route::get('/product/advancePricing', [ProductController::class, 'getAdvancePricing']);
    Route::get('/product/images', [ProductController::class, 'getProductImages']);
    Route::get('/product/brands', [ProductController::class, 'getBrands']);
    Route::get('/product/products', [ProductController::class, 'getProducts']);
    Route::get('/product/related', [ProductController::class, 'getRelatedProducts']);
    Route::get('/product/recomended', [ProductController::class, 'getRecomendedProducts']);
    Route::get('/product/categories', [ProductController::class, 'getCategoryForProduct']);
    Route::get('/product/taxclass', [ProductController::class, 'getTaxClassForProduct']);
    Route::get('/product/lumiseproducts', [ProductController::class, 'getLumiseProduct']);
    Route::get('/product/editProduct/{id}', [ProductController::class, 'getEditProduct']);
    Route::get('/product/deleteProduct', [ProductController::class, 'getDeleteProduct']);
    Route::get('/product/languageData', [ProductController::class, 'getLanguageData']);
    Route::post('/product/imageUpload', [ProductController::class, 'postImageUpload']);
    Route::get('/product/categoryAttribute', [ProductController::class, 'getCategoryAttribute']);
    Route::get('/product/bulkSellingPrice', [ProductController::class, 'getBulkSellingPrice']);
    Route::get('/product/productPricingOptionData', [ProductController::class, 'getProductPricingOptionData']);
    Route::get('/product/advancePricingData', [ProductController::class, 'getAdvancePricingData']);
    Route::get('/product/deleteImages', [ProductController::class, 'getDeleteImages']);
    Route::post('/product/updateImageSortOrder', [ProductController::class, 'postUpdateImageSortOrder']);
    Route::post('/product/updateImageIsDefault', [ProductController::class, 'postUpdateImageIsDefault']);
    Route::post('/product/deleteProductPricingOptionData', [ProductController::class, 'deleteProductPricingOptionData']);
    Route::post('/product/deleteProductPricingOptionImage', [ProductController::class, 'deleteProductPricingOptionImage']);
    Route::post('/product/custGroups', [ProductController::class, 'getCustGroups']);
    Route::post('/product/upload-product-image', [ProductController::class, 'uploadProdImage'])->name('ckeditor.upload_prod_image');
    Route::post('/product/copyProduct', [ProductController::class, 'copyProduct']);

    // delete related & recommendedProduct
    Route::get('/product/deleteRelatedProduct', [ProductController::class, 'deleteRelatedProduct']);
    Route::get('/product/deleteRecommendedProduct', [ProductController::class, 'deleteRecommendedProduct']);
    Route::get('/product/deleteAdvancePrice', [ProductController::class, 'deleteAdvancePrice']);

    //Home Page Content
    Route::get('/home-page-content', [HomePageContentController::class, 'listHomePageContent'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/home-page-content/add', [HomePageContentController::class, 'homePageContentForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/home-page-content/add', [HomePageContentController::class, 'addHomePageContent']);
    Route::get('/home-page-content/edit/{id}', [HomePageContentController::class, 'editHomePageContent'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/home-page-content/update', [HomePageContentController::class, 'updateHomePageContent']);
    Route::post('/home-page-content/delete', [HomePageContentController::class, 'deleteHomePageContent']);
    Route::post('/home-page-content/filter', [HomePageContentController::class, 'filterHomePageContent']);

    //Home Page Photographer
    Route::get('/home-page-photographer', [HomePagePhotographerController::class, 'listHomePagePhotographer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/home-page-photographer/add', [HomePagePhotographerController::class, 'showHomePagePhotographerForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/home-page-photographer/add', [HomePagePhotographerController::class, 'addHomePagePhotographer']);
    Route::get('/home-page-photographer/edit/{id}', [HomePagePhotographerController::class, 'editHomePagePhotographer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/home-page-photographer/update', [HomePagePhotographerController::class, 'updateHomePagePhotographer']);
    Route::post('/home-page-photographer/delete', [HomePagePhotographerController::class, 'deleteHomePagePhotographer'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/home-page-photographer/act-inact', [HomePagePhotographerController::class, 'actInactHomePagePhotographer']);
    Route::post('/home-page-photographer/filter', [HomePagePhotographerController::class, 'filterHomePagePhotographer']);

    //Mega menu
    Route::get('mega-menu', [MegaMenuController::class, 'getMegamenu'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('mega-menu/list', [MegaMenuController::class, 'getMegamenuList']);
    Route::get('mega-menu/addMenu', [MegaMenuController::class, 'menuAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('mega-menu/addMenu', [MegaMenuController::class, 'addMegamenu']);
    Route::post('mega-menu/{id}/deleteMenu', [MegaMenuController::class, 'deleteMenu'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('mega-menu/editMenu/{id}', [MegaMenuController::class, 'menuEditView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('mega-menu/updateMenu', [MegaMenuController::class, 'updateMenu']);

    // Settings - Store Locations
    Route::get('/store-location', [StoreLocationController::class, 'listStoreLoc'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/store-location/add', [StoreLocationController::class, 'showStoreLocForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/store-location/add', [StoreLocationController::class, 'addStoreLoc']);
    Route::get('/store-location/edit/{id}', [StoreLocationController::class, 'editStoreLoc'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/store-location/update', [StoreLocationController::class, 'updateStoreLoc']);
    Route::post('/store-location/delete', [StoreLocationController::class, 'deleteStoreLoc'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/store-location/filter', [StoreLocationController::class, 'filterStoreLoc']);

    // Settings - Holidays
    Route::get('/holiday', [HolidayController::class, 'listHoliday'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/holiday/add', [HolidayController::class, 'showHolidayForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/holiday/add', [HolidayController::class, 'addHoliday']);
    Route::get('/holiday/edit/{id}', [HolidayController::class, 'editHoliday'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/holiday/update', [HolidayController::class, 'updateHoliday']);
    Route::post('/holiday/delete', [HolidayController::class, 'deleteHoliday'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/holiday/filter', [HolidayController::class, 'filterHoliday']);

    // event enq
    Route::get('eventEnq', [EventEnqController::class, 'getEventEnquiries'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('eventEnq/list', [EventEnqController::class, 'getEventEnqList']);
    Route::post('eventEnq/photographer-allocation', [EventEnqController::class, 'addAllocPhotographer']);
    Route::post('eventEnq/changeEventEnqStatus', [EventEnqController::class, 'changeEventEnqStatus']);
    Route::get('eventEnq/viewEnqDetails/{id}', [EventEnqController::class, 'viewEnqDetails']);
    Route::post('eventEnq/updatePhotoPrice', [EventEnqController::class, 'updatePhotoPrice']);

    // enq photos
    Route::get('eventEnq/photos/getPhotos/{id}', [EventEnqController::class, 'getPhotos'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('eventEnq/photos/getPhotosList', [EventEnqController::class, 'getPhotosList']);
    Route::get('eventEnq/photos/addPhotos/{id}', [EventEnqController::class, 'photosAddView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('eventEnq/photos/addPhotos', [EventEnqController::class, 'addPhotos']);
    Route::post('eventEnq/photos/deletePhoto', [EventEnqController::class, 'deletePhoto']);

    // Generate megamenu
    Route::get('generate-megamenu', [MegaMenuController::class, 'generateMegamenuView'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('generate-megamenu1', [MegaMenuController::class, 'generateMegamenu'])->middleware(PreventRouteAccessMiddleware::class);

    //how-it-works-banner
    Route::get('/how-it-works-banner', [HowItWrksBnrsController::class, 'listHowItWrksBnrs'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('/how-it-works-banner/add', [HowItWrksBnrsController::class, 'showHowItWrksBnrsForm'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/how-it-works-banner/add', [HowItWrksBnrsController::class, 'addHowItWrksBnrs']);
    Route::get('/how-it-works-banner/edit/{id}', [HowItWrksBnrsController::class, 'editHowItWrksBnrs'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('/how-it-works-banner/update', [HowItWrksBnrsController::class, 'updateHowItworksBnrs']);
    Route::post('/how-it-works-banner/delete', [HowItWrksBnrsController::class, 'deleteHowItWrksBnrs'])->middleware(PreventRouteAccessMiddleware::class);

    //Shipping Cost
    Route::post('/shipping-cost', [SettingsController::class, 'addShippingCost']);

    //Dashboard
    Route::get('/daily-sales-graph', [DashboardController::class, 'getDailySalesData']);
    Route::post('/dashboard-filter', [DashboardController::class, 'getTotalCount']);

    // Orders
    Route::get('orders', [OrdersController::class, 'getAllOrders'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('orders/list', [OrdersController::class, 'getAllOrdersList']);
    Route::get('orders/notes', [OrdersController::class, 'getAllOrdersNotes']);
    Route::post('orders/addNotes', [OrdersController::class, 'addNotes']);
    Route::get('orders/orderDetails/{id}', [OrdersController::class, 'getOrderDetails']);
    Route::post('orders/updateBillingAddress', [OrdersController::class, 'updateBillingAddress']);
    Route::post('orders/updateShippingAddress', [OrdersController::class, 'updateShippingAddress']);
    Route::post('orders/markOrderAsCancelled', [OrdersController::class, 'markOrderAsCancelled']);
    Route::post('orders/markBulkOrderAsCancelled', [OrdersController::class, 'markBulkOrderAsCancelled']);
    Route::post('orders/markOrderAsDelivered', [OrdersController::class, 'markOrderAsDelivered']);
    Route::post('orders/markOrderAsShipped', [OrdersController::class, 'markOrderAsShippedWOAramex']);
    Route::get('orders/printOrder/{id}', [OrdersController::class, 'printOrder']);
    Route::get('orders/export', [OrdersController::class, 'getExportOrders'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('orders/generateOrderInvoice', [OrdersController::class, 'generateOrderInvoice'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('orders/generateBulkOrderInvoice', [OrdersController::class, 'generateBulkOrderInvoice'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('orders/downloadOrderProdImages/{id}', [OrdersController::class, 'downloadOrderProdImages'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('orders/downloadOrderProductFiles/{id}', [OrdersController::class, 'downloadOrderProductFiles'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('orders/updateLoyaltyNumber', [OrdersController::class, 'updateLoyaltyNumber']);
    Route::post('orders/changeStatus', [OrdersController::class, 'changeStatus']);

    Route::post('orders/cancelBulkOrders', [OrdersController::class, 'cancelBulkOrders'])->middleware(PreventRouteAccessMiddleware::class);
    Route::post('orders/printBulkOrders', [OrdersController::class, 'printBulkOrders']);
    Route::get('orders/activities', [OrdersController::class, 'getOrderActivity']);

    Route::get('orders/downloadPrintFilesPdf/{id}', [OrdersController::class, 'downloadPrintFilesPdf'])->middleware(PreventRouteAccessMiddleware::class);

    // Order Invoices
    Route::get('invoices', [OrderInvoiceController::class, 'getAllInvoices'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('invoices/list', [OrderInvoiceController::class, 'getAllInvoicesList']);
    Route::get('invoices/printInvoice/{id}', [OrderInvoiceController::class, 'printInvoice']);
    Route::post('invoices/printbulkInvoice', [OrderInvoiceController::class, 'printBulkInvoice']);
    Route::get('invoices/invoiceDetails/{id}', [OrderInvoiceController::class, 'getInvoiceDetails']);

    // Event Photos Sales
    Route::get('eventPhotoSales', [EventPhotoSalesController::class, 'getAlleventPhotoSales'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('eventPhotoSales/list', [EventPhotoSalesController::class, 'getAllEventPhotoSalesList']);

    // Event enq Payments
    Route::get('eventEnqPayment', [EventEnqPaymentsController::class, 'getAllEventEnqPayment'])->middleware(PreventRouteAccessMiddleware::class);
    Route::get('eventEnqPayment/list', [EventEnqPaymentsController::class, 'getAllEventEnqPaymentList']);

    //aramex shipping
    Route::post('/get-shipping-order', [OrdersController::class, 'getShippingOrderData']);
    Route::post('/update-aramex-config', [SettingsController::class, 'saveAramexConfig']);
    Route::post('/create-aramex-shipping', [AramexController::class, 'createAramexShipping']);
});
