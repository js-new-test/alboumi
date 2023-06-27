<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\FooterGenerator;
use App\Models\FooterLinks;
use App\Models\Locale;

class FooterController extends Controller
{
    public function getFooterLinks($language_id)
    {
        $footerData = FooterGenerator::getFooterData($language_id);
        $socialLinks = FooterLinks::getSocialLinks();
        $footerLabels = Locale::getFooterLabels($language_id);
        return response()->json(['status' => true,'footerData' => $footerData,'socialLinks' => $socialLinks, 'footerLabels' => $footerLabels]);
    }
}

?>