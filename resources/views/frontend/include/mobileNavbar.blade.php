<?php
$strMenu = "<ul>";

    $cnt = 0;
    $languageCode = '';
    // if($langCode != $defaultLangCode)
    //     $languageCode = $langCode.'/';
    // else
        $languageCode = '';


    $codes = ['MORE'];
    $megamenuLabels = getCodesMsg($langId, $codes);
    
    $flagMore = false;
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
                            $strMenu .= "<li>
                                <a class='' href='".$cmsURL."'>".$title."</a>
                            </li>";
                        }

                        // CMS page but event page
                        if($cmsPages[0]['slug'] == 'events-occasions')
                        {
                            $title = $cmsPages[0]['title'];

                            if(count($arrEvents) > 0)
                            {
                                // Main tab
                                $strMenu .= "<li>
                                <a class='drop-arrow' href='{{ url('events-occasions') }}'>".$title."</a><ul>";
                                 // Sub Menu
                                $cntEvents = 1;
                                foreach($arrEvents as $ek => $eventData)
                                {
                                    // print_r($eventData);
                                    // if($cntEvents > 5) break;

                                    $eventName = $eventData->event_name;
                                    $eventId = $eventData->id;
                                    $eventUrl = $baseUrl.'/'.$languageCode."events/" . $eventId;

                                    $strMenu .= "<li>
                                        <a class='s1' href='".$eventUrl."'>".$eventName."</a>
                                        </li>";


                                    $cntEvents++;
                                }
                                $strMenu .= "</ul></li>";
                            }
                            else
                            {
                                $strMenu .= "<li>
                                <a href='{{ url('events-occasions') }}'>".$title."</a></li>";
                            }
                        }
                        // CMS page but photographer page
                        if($cmsPages[0]['slug'] == 'made-in-bahrain')
                        {
                            $title = $cmsPages[0]['title'];

                            // Main tab
                            $strMenu .= "<li>
                            <a class='drop-arrow' href='#'>".$title."</a><ul>";

                            // Sub Menu
                            $cntPhg = 1;
                            foreach($arrPhotographers as $ek => $photoData)
                            {
                                // if($cntPhg > 5) break;

                                $photoName = $photoData['name'];
                                $photoGId = $photoData['id'];
                                $photoUrl = $baseUrl.'/'.$languageCode."made-in-bahrain/" . $photoGId;

                                $strMenu .= "<li>
                                    <a class='s1' href='".$photoUrl."'>".$photoName."</a>
                                    </li>";

                                $cntPhg++;
                            }
                            $strMenu .= "</ul></li>";

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

                            $arrSubCategories = App\Models\Category::getChildCategories($arrTopCategories[0]['id'],$langId);

                            // Sub Menu
                            $cntSubCat = 1;
                            if(count($arrSubCategories) > 0)
                            {
                                // Main tab
                                $strMenu .= "<li>
                                <a class='drop-arrow' href='".$catURL."'>".$title."</a><ul>";

                                foreach($arrSubCategories as $ck => $subCatData)
                                {
                                    // if($cntSubCat > 5) break;

                                    $subCatName = $subCatData['title'];
                                    $subCatSlug = $subCatData['slug'];
                                    $subCatId = $subCatData['id'];
                                    $subCatUrl = $baseUrl.'/'.$languageCode."category/" . $subCatSlug;

                                    $strMenu .= "<li>
                                        <a class='s1' href='".$subCatUrl."'>".$subCatName."</a>
                                        </li>";

                                    $cntSubCat++;
                                }

                                $strMenu .= "</ul></li>";

                            }
                            else
                            {
                                 // Main tab
                                 $strMenu .= "<li>
                                 <a href='".$catURL."'>".$title."</a>";
                                 $strMenu .= "</li>";
                            }
                        }

                        // Category page but having products
                        if(($arrTopCategories[0]['flag_product'] == '1'))
                        {
                            $title = $arrTopCategories[0]['title'];
                            $slug = $arrTopCategories[0]['slug'];

                            $catURL = $baseUrl.'/'.$languageCode."category/" . $slug;

                            $arrProducts = App\Models\Product::getProducts($arrTopCategories[0]['id'],$langId);

                            $cntProducts = 1;
                            if(count($arrProducts) > 0)
                            {
                                // Main tab
                                $strMenu .= "<li>
                                <a class='drop-arrow' href='".$catURL."'>".$title."</a><ul>";
                                foreach($arrProducts as $ck => $productData)
                                {
                                    if($cntProducts > 5) break;

                                    $prodName = $productData['title'];
                                    $prodSlug = $productData['product_slug'];
                                    $prodId = $productData['id'];
                                    $prodUrl = $baseUrl.'/'.$languageCode."product/" . $prodSlug;

                                    $strMenu .= "<li>
                                        <a class='s1' href='".$prodUrl."'>".$prodName."</a>
                                        </li>";

                                    $cntProducts++;
                                }
                                if($cntProducts > 5)
                                    $strMenu .= "<li><a class='s1' href='".$catURL."'>". $megamenuLabels['MORE'] . "</a></li></ul></li>";
                                else
                                    $strMenu .= "</ul></li>";
                            }
                            else
                            {
                                // Main tab
                                $strMenu .= "<li>
                                <a class='drop-arrow' href='".$catURL."'>".$title."</a>";
                                $strMenu .= "</li>";
                            }
                        }
                    }
                }
            }
            else
            {
                if($cnt == 10)
                {
                    $flagMore = true;

                    // initiate more tab html
                    $strMenu .= "<li>
                        <a class='drop-arrow' href='#'>".$megamenuLabels['MORE']."</a>
                    <ul>";
                }

                // Only CMS Page
                if($type == 0)
                {
                    if(!empty($cmsPages))
                    {
                        if($cmsPages[0]['slug'] != 'events-occasions' && $cmsPages[0]['slug'] != 'made-in-bahrain')
                        {
                            $title = $cmsPages[0]['title'];
                            $cmsURL = $baseUrl.'/'.$languageCode.$cmsPages[0]['slug'];
                            $strMenu .= "<li>
                                <a class='' href='".$cmsURL."'>".$title."</a>
                            </li>";
                        }

                        // CMS page but event page
                        if($cmsPages[0]['slug'] == 'events-occasions')
                        {
                            $title = $cmsPages[0]['title'];

                            // Main tab
                            $strMenu .= "<li>
                            <a class='drop-arrow' href='{{ url('events-occasions') }}'>".$title."</a><ul>";

                            // Sub Menu
                            $cntEvents = 1;
                            foreach($arrEvents as $ek => $eventData)
                            {
                                // print_r($eventData);
                                // if($cntEvents > 5) break;

                                $eventName = $eventData->event_name;
                                $eventId = $eventData->id;
                                $eventUrl = $baseUrl.'/'.$languageCode."events/" . $eventId;

                                $strMenu .= "<li>
                                    <a class='s1' href='".$eventUrl."'>".$eventName."</a>
                                    </li>";


                                $cntEvents++;
                            }
                            if($cntEvents > 5)
                                $strMenu .= "<li><a class='s1' href='{{ url('events-occasions') }}'>". $megamenuLabels['MORE'] . "</a></li></ul></li>";
                            else
                                $strMenu .= "</ul></li>";
                            // $strMenu .= "</ul></li>";
                        }

                        // CMS page but photographer page
                        if($cmsPages[0]['slug'] == 'made-in-bahrain')
                        {
                            $title = $cmsPages[0]['title'];

                            // Main tab
                            $strMenu .= "<li>
                            <a class='drop-arrow' href='#'>".$title."</a><ul>";

                            // Sub Menu
                            $cntPhg = 1;
                            foreach($arrPhotographers as $ek => $photoData)
                            {
                                if($cntPhg > 5) break;

                                $photoName = $photoData['name'];
                                $photoGId = $photoData['id'];
                                $photoUrl = $baseUrl.'/'.$languageCode."made-in-bahrain/" . $photoGId;

                                $strMenu .= "<li>
                                    <a class='s1' href='".$photoUrl."'>".$photoName."</a>
                                    </li>";

                                $cntPhg++;
                            }
                            if($cntPhg > 5)
                                $strMenu .= "<li><a class='s1' href='{{ url('made-in-bahrain') }}'>". $megamenuLabels['MORE'] . "</a></li></ul></li>";
                            else
                                $strMenu .= "</ul></li>";
                            // $strMenu .= "</ul></li>";

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

                            $catURL =  $baseUrl.'/'.$languageCode."category/" . $slug;

                            $arrSubCategories = App\Models\Category::getChildCategories($arrTopCategories[0]['id'],$langId);

                            // Sub Menu
                            $cntSubCat = 1;
                            if(count($arrSubCategories) > 0)
                            {
                                // Main tab
                                $strMenu .= "<li>
                                <a class='drop-arrow' href='".$catURL."'>".$title."</a><ul>";

                                foreach($arrSubCategories as $subCatData)
                                {
                                    // foreach($subCategoryData as $ck => $subCatData)
                                    // {
                                        // if($cntSubCat > 5) break;

                                        $subCatName = $subCatData['title'];
                                        $subCatSlug = $subCatData['slug'];
                                        $subCatId = $subCatData['id'];
                                        $subCatUrl = $baseUrl.'/'.$languageCode."category/" . $subCatSlug;

                                        $strMenu .= "<li>
                                            <a class='s1' href='".$subCatUrl."'>".$subCatName."</a>
                                            </li>";

                                        $cntSubCat++;
                                    // }
                                }
                                $strMenu .= "</ul></li>";
                            }
                            else
                            {
                                // Main tab
                                $strMenu .= "<li>
                                <a href='".$catURL."'>".$title."</a>";
                                $strMenu .= "</li>";
                            }
                        }

                        // Category page but having products
                        if(($arrTopCategories[0]['flag_product'] == '1'))
                        {
                            $title = $arrTopCategories[0]['title'];
                            $slug = $arrTopCategories[0]['slug'];

                            $catURL = $baseUrl.'/'.$languageCode."category/" . $slug;

                            $arrProducts = App\Models\Product::getProducts($arrTopCategories[0]['id'],$langId);

                            $cntProducts = 1;
                            if(count($arrProducts) > 0)
                            {
                                // Main tab
                                $strMenu .= "<li>
                                <a class='drop-arrow' href='".$catURL."'>".$title."</a><ul>";
                                foreach($arrProducts as $ck => $productData)
                                {
                                    if($cntProducts > 5) break;

                                    $prodName = $productData['title'];
                                    $prodSlug = $productData['product_slug'];
                                    $prodId = $productData['id'];
                                    $prodUrl = $baseUrl.'/'.$languageCode."product/" . $prodSlug;

                                    $strMenu .= "<li>
                                        <a class='s1' href='".$prodUrl."'>".$prodName."</a>
                                        </li>";

                                    $cntProducts++;
                                }
                                if($cntProducts > 5)
                                    $strMenu .= "<li><a class='s1' href='".$catURL."'>". $megamenuLabels['MORE'] . "</a></li></ul></li>";
                                else
                                    $strMenu .= "</ul></li>";
                                
                            }
                            else
                            {
                                // Main tab
                                $strMenu .= "<li>
                                <a class='drop-arrow' href='".$catURL."'>".$title."</a>";
                                $strMenu .= "</li>";
                            }
                        }
                    }
                }
            }
        }
    }

// If more tab set then closing tabs
if(isset($flagMore) && $flagMore == true)
{
	$strMenu .= "</ul></li>";
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


    echo $strMenu;
?>