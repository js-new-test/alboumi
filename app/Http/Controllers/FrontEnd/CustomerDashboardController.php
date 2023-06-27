<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ReuseFunctionTrait;
use Illuminate\Support\Facades\Session;

class CustomerDashboardController extends Controller
{
    use ReuseFunctionTrait;

    /* ###########################################
    // Function: dashboard
    // Description: Display customer dashboard  
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function dashboard()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $codes = ['FORGOTPASSLABEL', 'FORGOTPASSLABEL1', 'FORGOTPASSLABEL2', 'FORGOTPASSLABEL3'
        ,'FORGOTPASSLABEL4','LOGINLABEL', 'FORGOTPASSLABEL','APPNAME', 'SIDEBARLABEL8','DASHBOARDSEODESC','DASHBOARDSEOKEYWORD'];
        $dashboardLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $dashboardLabels["SIDEBARLABEL8"];
        $projectName = $dashboardLabels["APPNAME"];
        $baseUrl = $this->getBaseUrl();        

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        
        return view('frontend.dashboard', compact('pageName','projectName','megamenuFileName','megamenuFileName',
        'baseUrl','dashboardLabels'));
    }

    public function getLocalDetailsForLang(Request $request)
    {
        return $this->getLocaleDetailsForLang($request);
    }

    public function getEmailTemplateForLang(Request $request)
    {
        return $this->getEmailTemplatesForLang($request);
    }
}
