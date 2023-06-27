<?php
$strMenu = "<ul class='navbar-nav mr-auto'>";

    $cnt = 0;
    $languageCode = '';
    // if($langCode != $defaultLangCode)
    //     $languageCode = $langCode.'/';
    // else
        $languageCode = '';
        // dd($arrMegaMenu);
    $flagMore = false;

    $codes = ['MORE'];
    $megamenuLabels = getCodesMsg($langId, $codes);

    if(!empty($arrMegaMenu))
    {
        foreach($arrMegaMenu as $k => $menuData)
        {
             // Set menu data
            $type = $menuData->type;
            $related_id = $menuData->name;

            $cmsPages = App\Models\CmsPages::getCmsPages($related_id,$langId);
            $arrPhotographers = App\Models\Photographers::getPhotographers($langId);
            $arrEvents = App\Models\Events::getEvents($langId);

            $cnt += 1;

            // Menu icon data
            $smallIcon = $baseUrl.'/public/assets/images/megamenu/small/'.$menuData['small_image']; // small icon image path
            // dd($smallIcon);
            if($menuData['big_image'] != null)
                $bigIcon = $baseUrl.'/public/assets/images/megamenu/big/'.$menuData['big_image']; // big icon image path
            else
                $bigIcon = '';

            $flagMenuIcon = false;
            $menuIcon = ""; // empty
            $div1Class = "col-lg-12";
            $div2Class = "";

            // First 8 tabs
            if($cnt <= 9)
            {
                // Only CMS Page
                if($type == 0)
                {
                    if(!empty($cmsPages))
                    {
                        if($cmsPages[0]['slug'] != 'events-occasions' && $cmsPages[0]['slug'] != 'made-in-bahrain' && $cmsPages[0]['slug'] != 'contact-us')
                        {
                            $title = $cmsPages[0]['title'];
                            $cmsURL = $baseUrl.'/'.$languageCode.$cmsPages[0]['slug'];
                            $strMenu .= "<li class='nav-item'>
                                <a class='nav-link' href='".$cmsURL."'>".$title."</a>
                            </li>";
                        }

                        // CMS page but event page
                        if($cmsPages[0]['slug'] == 'events-occasions')
                        {
                            $title = $cmsPages[0]['title'];

                            // Main tab
                            $strMenu .= "<li class='nav-item'>
                            <a class='nav-link' href='{{ url('events-occasions') }}'>".$title."</a>";

                            // print_r($arrEvents);
                            // Total
                            $eventcounts = count($arrEvents);

                            // Menu Icon
                            if($eventcounts <= 4)
                            {
                                $flagMenuIcon = true;

                                if($eventcounts <= 2)
                                {
                                    $menuIcon = $bigIcon;
                                    $div1Class = "col-md-6 col-xl-5";
                                    $div2Class = "col-md-6 col-xl-7 full-img";
                                }
                                else
                                {
                                    $menuIcon = $smallIcon;
                                    $div1Class = "col-md-10 col-xl-9";
                                    $div2Class = "col-md-2 col-xl-3 full-img";
                                }
                            }

                            // Sub Menu
                            $strMenu .= "<div class='menu-contents'>
                            <div class='container'>
                                <div class='row'>
                                    <div class='".$div1Class."'>
                                        <table class='menu-box'>
                                            <tr>";

                                            $cntEvents = 1;
                                            foreach($arrEvents as $ek => $eventData)
                                            {
                                                // print_r($eventData);
                                                if($cntEvents > 5) break;

                                                // event data
                                                if(!empty($eventData->event_image))
                                                    $eventImage = $baseUrl.'/public/assets/images/events/'.$eventData->event_image;
                                                else
                                                    $eventImage = $baseUrl.'/public/assets/images/no_image.png';

                                                $eventName = $eventData->event_name;
                                                $eventId = $eventData->id;
                                                // $arrPackages = $eventData['packages'];
                                                $eventUrl = $baseUrl.'/'.$languageCode."events/" . $eventId;

                                                $strMenu .= "<td>
                                                    <img src='".$eventImage."'>
                                                    <p class='m-0'><a class='s1 right-arrow' href='".$eventUrl."'>".$eventName."</a></p>
                                                    <ul>";

                                                    // foreach ($eventData['children'] as $pk => $packageData)
                                                    // {
                                                    //     // pckage details
                                                    //     $pckName = $packageData->package_name;

                                                    //     $strMenu .= "<li><a href='".$eventUrl."'>".$pckName."</a></li>";
                                                    // }

                                                $strMenu .= "</ul>
                                                </td>";

                                                $cntEvents++;
                                            }

                                        $strMenu .= "</tr>
                                        </table>
                                    </div>";

                                    // Menu Banner
                                    if($flagMenuIcon)
                                    {
                                        if($menuIcon != '')
                                        {
                                            $strMenu .= "<div class='".$div2Class."'>
                                                <img src='".$menuIcon."'>
                                            </div>";
                                        }
                                    }

                                $strMenu .= "</div>
                                    </div>
                            </div></li>";
                        }

                        // CMS page but photographer page
                        if($cmsPages[0]['slug'] == 'made-in-bahrain')
                        {
                            $title = $cmsPages[0]['title'];

                            // Main tab
                            $strMenu .= "<li class='nav-item'>
                            <a class='nav-link' href='{{ url('made-in-bahrain') }}'>".$title."</a>";

                            // Total
                            $phgcounts = count($arrPhotographers);

                            // Menu Icon
                            if($phgcounts <= 4)
                            {
                                $flagMenuIcon = true;

                                if($phgcounts <= 2)
                                {
                                    $menuIcon = $bigIcon;
                                    $div1Class = "col-md-6 col-xl-5";
                                    $div2Class = "col-md-6 col-xl-7 full-img";
                                }
                                else
                                {
                                    $menuIcon = $smallIcon;
                                    $div1Class = "col-md-10 col-xl-9";
                                    $div2Class = "col-md-2 col-xl-3 full-img";
                                }
                            }

                            // Sub Menu
                            $strMenu .= "<div class='menu-contents'>
                            <div class='container'>
                                <div class='row'>
                                    <div class='".$div1Class."'>
                                        <table class='menu-box'>
                                            <tr>";

                                            $cntPhg = 1;
                                            foreach($arrPhotographers as $ek => $photoData)
                                            {
                                                if($cntPhg > 5) break;

                                                // photographer data
                                                if(!empty($photoData['profile_pic']))
                                                    $photoImage = $baseUrl.'/public/assets/images/photographers/'.$photoData['profile_pic'];
                                                else
                                                    $photoImage = $baseUrl.'/public/assets/images/no_image.png';

                                                $photoName = $photoData['name'];
                                                $photoGId = $photoData['id'];
                                                $photoUrl = $baseUrl.'/'.$languageCode."made-in-bahrain/" . $photoGId;

                                                $strMenu .= "<td>
                                                    <img src='".$photoImage."'>
                                                    <p class='m-0'><a class='s1 right-arrow' href='".$photoUrl."'>".$photoName."</a></p></td>";

                                                $cntPhg++;
                                            }

                                        $strMenu .= "</tr>
                                        </table>
                                    </div>";

                                    // Menu Banner
                                    if($flagMenuIcon)
                                    {
                                        if($menuIcon != '')
                                        {
                                            $strMenu .= "<div class='".$div2Class."'>
                                                <img src='".$menuIcon."'>
                                            </div>";
                                        }
                                    }

                                $strMenu .= "</div>
                                    </div>
                            </div></li>";
                        }
                    }
                }
                if($type == 1)
                {
                    $arrTopCategories = App\Models\Category::getTopCategories($related_id,$langId);

                    if(count($arrTopCategories) > 0)
                    {
                        // Category page but having sub categories
                        if(($arrTopCategories[0]['flag_product'] == '0'))
                        {
                            $title = $arrTopCategories[0]['title'];
                            $slug = $arrTopCategories[0]['slug'];
                            $catId = $related_id;
                            
                            if($catId == Config::get('app.photoBookCatId'))
                            {
                                $catURL =  $baseUrl.'/photo-book';
                            }
                            else
                                $catURL =  $baseUrl.'/'.$languageCode."category/" . $slug;

                            // Main tab
                            $strMenu .= "<li class='nav-item'>
                            <a class='nav-link' href='".$catURL."'>".$title."</a>";

                            // Get sub categories of this category with detail of id, title, slug, image
                            $arrSubCategories = App\Models\Category::getChildCategories($arrTopCategories[0]['id'],$langId);
                            
                            // function call with catId and get sub categories of it

                            // Total
                            $subCatcounts = count($arrSubCategories);
                            // Menu Icon
                            if($subCatcounts <= 4)
                            {
                                $flagMenuIcon = true;

                                if($subCatcounts <= 2)
                                {
                                    $menuIcon = $bigIcon;
                                    $div1Class = "col-md-6 col-xl-5";
                                    $div2Class = "col-md-6 col-xl-7 full-img";
                                }
                                else
                                {
                                    $menuIcon = $smallIcon;
                                    $div1Class = "col-md-10 col-xl-9";
                                    $div2Class = "col-md-2 col-xl-3 full-img";
                                }
                            }
                            // Sub Menu
                            if($subCatcounts > 0)
                            {
                                $strMenu .= "<div class='menu-contents'>
                                <div class='container'>
                                    <div class='row'>
                                        <div class='".$div1Class."'>
                                            <table class='menu-box'>
                                                <tr>";

                                                $cntSubCat = 1;
                                                if(count($arrSubCategories) > 0)
                                                {
                                                    foreach($arrSubCategories as $subCatData)
                                                    {
                                                        // foreach($subCategoryData as $ck => $subCatData)
                                                        // {
                                                            if($cntSubCat > 5) break;

                                                            if(!empty($subCatData['category_image']))
                                                                $subCatImage =  $baseUrl.'/public/assets/images/categories/'.$subCatData['category_image'];
                                                            else
                                                                $subCatImage = $baseUrl.'/public/assets/images/no_image.png';

                                                            $subCatName = $subCatData['title'];
                                                            $subCatSlug = $subCatData['slug'];
                                                            $subCatId = $subCatData['id'];
                                                            $subCatUrl = $baseUrl.'/'.$languageCode."category/" . $subCatSlug;


                                                            // get sub categories
                                                            // Get sub categories of this category with detail of id, title, slug, image
                                                            $arrSubSubCategories = App\Models\Category::getChildCategories($subCatData['id'],$langId);
                                                            // print_r($arrSubSubCategories);
                                                            // function call with subCatId and get sub categories of it

                                                            $strMenu .= "<td>
                                                                <img src='".$subCatImage."'>
                                                                <p class='m-0'><a class='s1 right-arrow' href='".$subCatUrl."'>".$subCatName."</a></p>";

                                                            if(count($arrSubSubCategories) > 0)
                                                            {
                                                                $strMenu .= "<ul>";

                                                                foreach ($arrSubSubCategories as $ssk => $subSubData)
                                                                {
                                                                    // sub sub cat details
                                                                    $subSubName = $subSubData->title;
                                                                    $subSubSlug = $subSubData->slug;
                                                                    $subSubUrl = $baseUrl.'/'.$languageCode."category/" . $subSubSlug;

                                                                    $strMenu .= "<li><a href='".$subSubUrl."'>".$subSubName."</a></li>";
                                                                }

                                                                $strMenu .= "</ul>";
                                                            }

                                                            $strMenu .= "</td>";

                                                            $cntSubCat++;
                                                        // }
                                                    }
                                                }

                                            $strMenu .= "</tr>
                                            </table>
                                        </div>";

                                        // Menu Banner
                                        if($flagMenuIcon)
                                        {
                                            if($menuIcon != '')
                                            {
                                                $strMenu .= "<div class='".$div2Class."'>
                                                    <img src='".$menuIcon."'>
                                                </div>";
                                            }
                                        }

                                    $strMenu .= "</div>
                                        </div>
                                </div></li>";
                            }
                        }

                        // Category page but having products
                        if(($arrTopCategories[0]['flag_product'] == '1'))
                        {
                            $title = $arrTopCategories[0]['title'];
                            $slug = $arrTopCategories[0]['slug'];

                            $catURL = $baseUrl.'/'.$languageCode."category/" . $slug;

                            // Main tab
                            $strMenu .= "<li class='nav-item'>
                            <a class='nav-link' href='".$catURL."'>".$title."</a>";

                            // Get products of this category with detail of id, title, slug, image
                            $arrProducts = App\Models\Product::getProducts($arrTopCategories[0]['id'],$langId);
                            // function call with catId and get sub categories of it

                            // Total
                            $productscounts = count($arrProducts);

                            // Menu Icon
                            if($productscounts <= 4)
                            {
                                $flagMenuIcon = true;

                                if($productscounts <= 2)
                                {
                                    $menuIcon = $bigIcon;
                                    $div1Class = "col-md-6 col-xl-5";
                                    $div2Class = "col-md-6 col-xl-7 full-img";
                                }
                                else
                                {
                                    $menuIcon = $smallIcon;
                                    $div1Class = "col-md-10 col-xl-9";
                                    $div2Class = "col-md-2 col-xl-3 full-img";
                                }
                            }

                            // Sub Menu
                            if($productscounts > 0)
                            {
                                $strMenu .= "<div class='menu-contents'>
                                <div class='container'>
                                    <div class='row'>
                                        <div class='".$div1Class."'>
                                            <table class='menu-box'>
                                                <tr>";

                                                $cntProducts = 1;
                                                if(!empty($arrProducts))
                                                {
                                                    foreach($arrProducts as $ck => $productData)
                                                    {
                                                        if($cntProducts > 5) break;

                                                        if(!empty($productData['imgName']))
                                                            $prodImage =  $baseUrl ."/public/images/product/".$productData['id']."/".$productData['imgName'];
                                                        else
                                                            $prodImage = $baseUrl.'/public/assets/images/no_image.png';

                                                        $prodName = $productData['title'];
                                                        $prodSlug = $productData['product_slug'];
                                                        $prodId = $productData['id'];
                                                        $prodUrl = $baseUrl.'/'.$languageCode."product/" . $prodSlug;

                                                        // get variations
                                                        // Get variations of this product with detail
                                                        $arrVariations = App\Models\ProductPricing::getProductVariants($prodId,$langId);
                                                        // function call with prodId and get variations of it
                                                        $strMenu .= "<td>
                                                            <img src='".$prodImage."'>
                                                            <p class='m-0'><a class='s1 right-arrow' href='".$prodUrl."'>".$prodName."</a></p>";
                                                            
                                                        if(!empty($arrVariations))
                                                        {
                                                            $strMenu .= "<ul>";

                                                            foreach ($arrVariations as $ssk => $variationData)
                                                            {
                                                                // print_r($variationData);

                                                                // sub sub cat details
                                                                $variationName = $variationData['displayName'];
                                                                $variationId = $variationData['ids'];
                                                                $prodUrl = $baseUrl.'/'.$languageCode."product/" . $prodSlug . "?variation=" . $variationId;

                                                                $strMenu .= "<li><a href='".$prodUrl."'>".$variationName."</a></li>";
                                                            }

                                                            $strMenu .= "</ul>";
                                                        }

                                                        $strMenu .= "</td>";

                                                        $cntProducts++;
                                                    }
                                                }


                                            $strMenu .= "</tr>
                                            </table>
                                        </div>";

                                        // Menu Banner
                                        if($flagMenuIcon)
                                        {
                                            if($menuIcon != '')
                                            {
                                                $strMenu .= "<div class='".$div2Class."'>
                                                <img src='".$menuIcon."'>
                                                </div>";
                                            }
                                        }

                                    $strMenu .= "</div>
                                        </div>
                                </div></li>";
                            }
                        }
                    }
                }
            }
            // menu under more
            else
            {
                // print_r($type);
                // Make menu for each case
                
                    if($cnt == 10)
                    {
                        $flagMore = true;

                        // initiate more tab html
                        $strMenu .= "<li class='nav-item'>
                            <a class='nav-link' href='#'>".$megamenuLabels['MORE']."</a>
                            <div class='menu-contents'>
                                <div class='container'>
                                    <div class='row'>
                                        <div class='col-lg-12'>
                                            <table class='menu-box'>
                                                <tr>";
                    }
                    if($type == 0)
                    {
                        if(!empty($cmsPages))
                        {
                        // Make menu for each case
                            if($cmsPages[0]['slug'] != 'events-occasions' && $cmsPages[0]['slug'] != 'made-in-bahrain')
                            {
        
                                // echo  "except";
                                // print_r($cmsPages);
                                $title = $cmsPages[0]['title'];
                                if(!empty($menuData['icon_image']))
                                    $menuIcon =  $baseUrl ."/public/assets/images/megamenu/icon/".$menuData['icon_image'];
                                else
                                    $menuIcon = $baseUrl.'/public/assets/images/no_image.png';
                                $cmsURL = $baseUrl.'/'.$languageCode.$cmsPages[0]['slug'];
        
                                $strMenu .= "<td>
                                    <img src='".$menuIcon."'>
                                <p class='m-0'><a class='s1 right-arrow' href='".$cmsURL."'>".$title."</a></p>
                                </td>";
                            }
        
                            // Make menu for each case
                            if($cmsPages[0]['slug'] == 'events-occasions')
                            {
                                $title = $cmsPages[0]['title'];
        
                                if(!empty($menuData['icon_image']))
                                    $menuIcon =  $baseUrl ."/public/assets/images/megamenu/icon/".$menuData['icon_image'];
                                else
                                    $menuIcon = $baseUrl.'/public/assets/images/no_image.png';
        
                                $cmsURL = $baseUrl.'/'.$languageCode.$cmsPages[0]['slug'];
        
                                $strMenu .= "<td>
                                    <img src='".$menuIcon."'>
                                <p class='m-0'><a class='s1 right-arrow' href='".$cmsURL."'>".$title."</a></p><ul>";
        
                                $cntEvents = 1;
                                foreach($arrEvents as $ek => $eventData)
                                {
                                    if($cntEvents > 5) break;
        
                                    // event data
                                    $eventName = $eventData->event_name;
                                    $eventId = $eventData->id;
                                    $eventUrl = $baseUrl.'/'.$languageCode."events/" . $eventId;
        
                                    $strMenu .= "<li><a href='".$eventUrl."'>".$eventName."</a></li>";
        
                                    $cntEvents++;
                                }

                                if($cntEvents > 5)
                                {
                                    $strMenu .= "<li><a href='{{ url('events-occasions') }}'>". $megamenuLabels['MORE'] . "</a></li>";
                                }
        
                                $strMenu .= "</ul></td>";
                            }
        
                            // Make menu for each case
                            if($cmsPages[0]['slug'] == 'made-in-bahrain')
                            {
                                $title = $cmsPages[0]['title'];
        
                                if(!empty($menuData['icon_image']))
                                    $menuIcon =  $baseUrl ."/public/assets/images/megamenu/icon/".$menuData['icon_image'];
                                else
                                    $menuIcon = $baseUrl.'/public/assets/images/no_image.png';
        
                                $cmsURL = $baseUrl.'/'.$languageCode.$cmsPages[0]['slug'];
        
                                $strMenu .= "<td>
                                    <img src='".$menuIcon."'>
                                <p class='m-0'><a class='s1 right-arrow' href='".$cmsURL."'>".$title."</a></p><ul>";
        
                                $cntPhg = 1;
                                if(!empty($arrPhotographers))
                                {
                                    foreach($arrPhotographers as $ek => $photoData)
                                    {
                                        if($cntPhg > 5) break;
        
                                        // event data
                                        $photoName = $photoData['name'];
                                        $photoGId = $photoData['id'];
                                        $photoUrl = $baseUrl.'/'.$languageCode."made-in-bahrain/" . $photoGId;
        
                                        $strMenu .= "<li><a href='".$photoUrl."'>".$photoName."</a></li>";
        
                                        $cntPhg++;
                                    }
                                }
                                if($cntPhg > 5)
                                {
                                    $strMenu .= "<li><a href='{{ url('made-in-bahrain') }}'>". $megamenuLabels['MORE'] . "</a></li>";
                                }
                                $strMenu .= "</ul></td>";
                            }
                        }
                    }
    
                    if($type == 1)
                    {
                        $arrTopCategories = App\Models\Category::getTopCategories($related_id,$langId);
                         // Category page but having sub categories
                        // print_r($arrTopCategories); die;
                        if(count($arrTopCategories) > 0)
                        {
                            if(($arrTopCategories[0]['flag_product'] == '0'))
                            {
                                $title = $arrTopCategories[0]['title'];
                                $slug = $arrTopCategories[0]['slug'];
                                $catId = $related_id;
    
                                if(!empty($arrTopCategories[0]['category_image']))
                                    $catimage =  $baseUrl.'/public/assets/images/categories/'.$arrTopCategories[0]['category_image'];
                                else
                                    $catimage = $baseUrl.'/public/assets/images/no_image.png';
    
                                $catURL =  $baseUrl.'/'.$languageCode."category/" . $slug;
    
                                // Main tab
                                $strMenu .= "<td>
                                    <img src='".$catimage."'>
                                <p class='m-0'><a class='s1 right-arrow' href='".$catURL."'>".$title."</a></p>";
    
                                // Get sub categories of this category with detail of id, title, slug, image
                                $arrSubCategories = App\Models\Category::getChildCategories($arrTopCategories[0]['id'],$langId);
                                // function call with catId and get sub categories of it
    
                                // Total
                                $subCatcounts = count($arrSubCategories);
    
                                // Sub Menu
                                // Sub Menu
                                if($subCatcounts > 0)
                                {
                                    $strMenu .= "<ul>";
    
                                    $cntSubCat = 1;
                                    if(!empty($arrSubCategories))
                                    {
                                        foreach($arrSubCategories as $ck => $subCatData)
                                        {
                                            // foreach($subCategoryData as $ck => $subCatData)
                                            // {
                                                if($cntSubCat > 5) break;

                                                // event data
                                                $subCatName = $subCatData['title'];
                                                $subCatSlug = $subCatData['slug'];
                                                $subCatUrl = $baseUrl.'/'.$languageCode."category/" . $subCatSlug;

                                                $strMenu .= "<li><a href='".$subCatUrl."'>".$subCatName."</a></li>";
                                            // }
                                        }
                                    }
                                    $strMenu .= "</ul>";
                                }
    
                                $strMenu .= "</td>";
                            }
    
                                // Category page but having products
                            if(($arrTopCategories[0]['flag_product'] == '1'))
                            {
                                $title = $arrTopCategories[0]['title'];
                                $slug = $arrTopCategories[0]['slug'];
    
                                if(!empty($arrTopCategories[0]['category_image']))
                                    $catimage =  $baseUrl.'/public/assets/images/categories/'.$arrTopCategories[0]['category_image'];
                                else
                                    $catimage = $baseUrl.'/public/assets/images/no_image.png';
    
                                $catURL = $baseUrl.'/'.$languageCode."category/" . $slug;
    
                                // Main tab
                                $strMenu .= "<td>
                                    <img src='".$catimage."'>
                                <p class='m-0'><a class='s1 right-arrow' href='".$catURL."'>".$title."</a></p>";
    
                                // Get products of this category with detail of id, title, slug, image
                                $arrProducts = App\Models\Product::getProducts($arrTopCategories[0]['id'],$langId);
                                // function call with catId and get sub categories of it
                                // print_r($arrProducts);
                                // Total
                                $productscounts = count($arrProducts);
    
                                // Sub Menu
                                if($productscounts > 0)
                                {
                                    $strMenu .= "<ul>";
    
                                    $cntProducts = 1;
    
                                    if(!empty($arrProducts))
                                    {
                                        foreach($arrProducts as $ck => $productData)
                                        {
                                            if($cntProducts > 5) break;
    
                                            // event data
                                            $prodName = $productData['title'] ;
                                            $prodSlug = $productData['product_slug'];
                                            $prodUrl = $baseUrl.'/'."product/" . $prodSlug;
    
                                            $strMenu .= "<li><a href='".$prodUrl."'>".$prodName."</a></li>";
                                        }
                                    }
                                    $strMenu .= "</ul>";
                                }
                                $strMenu .= "</td>";
                            }
                        }
                    }
                                
            }
        }            
                                                                                 
    }

    // get contact us details
    // $contact_us = App\Models\CmsPages::getCmsPages($related_id,$langId);

// die;
// If more tab set then closing tabs
if(isset($flagMore) && $flagMore == true)
{
	$strMenu .= "</tr></table></div></div></div></div></li>";
}
    
    $contactUsPage = App\Models\CmsPages::getContactUsCmsPage($langId);
    if($contactUsPage['slug'] == 'contact-us')
    {
        $title = $contactUsPage['title'];
        $cmsURL = $baseUrl.'/'.$languageCode.$contactUsPage['slug'];
        $strMenu .= "<li class='nav-item'>
            <a class='nav-link' href='".$cmsURL."'>".$title."</a>
        </li>";
    }
$strMenu .= "</ul>";

echo $strMenu;
?>
