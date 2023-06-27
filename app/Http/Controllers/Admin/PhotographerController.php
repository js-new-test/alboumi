<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Photographers;
use App\Models\PhotographerDetails;
use App\Models\GlobalLanguage;
use App\Models\PhotographerPortfolio;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\CommonTrait;
use Image;
use Config;

class PhotographerController extends Controller
{
    use CommonTrait;

    public function getPhotographers()
    {
        $languages = GlobalLanguage::getAllLanguages();
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $baseUrl = $this->getBaseUrl();
        return view('admin.bahrainPhotographers.index',compact('otherLanguages','languages','baseUrl'));
    }

    public function getPhotographersList(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        DB::statement(DB::raw('set @rownum=0'));

        $photographers = Photographers::select('photographers.id','profile_pic','name','experience','status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                            DB::raw("date_format(pd.created_at,'%Y-%m-%d %h:%i:%s') as p_created_at"))
                            ->join('photographer_details as pd','pd.photographer_id','=','photographers.id')
                            ->where('pd.language_id', $defaultLanguageId)
                            ->whereNull('pd.deleted_at')
                            ->orderBy('pd.updated_at','desc')
                            ->get();

        if($request['lang_id'] != null)
        {
            DB::statement(DB::raw('set @rownum=0'));

            $photographers = Photographers::select('photographers.id','profile_pic','name','experience','status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                            DB::raw("date_format(pd.created_at,'%Y-%m-%d %h:%i:%s') as p_created_at"))
                            ->join('photographer_details as pd','pd.photographer_id','=','photographers.id')
                            ->where('language_id',$request['lang_id'])
                            ->whereNull('pd.deleted_at')
                            ->orderBy('pd.updated_at','desc')
                            ->get();
        }
        return Datatables::of($photographers)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }

    public function photgrapherAddView()
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguage = $defaultLanguageData['language']['langEN'];
        $defaultLanguageId = $defaultLanguageData['id'];
        $formTitle = "Add Photographer";

        $page = isset($_GET['page']) ? $_GET['page'] : "addPhotographer";
        $photoId = isset($_GET['photoId']) ? $_GET['photoId'] : "";

        if (!empty($photoId))
        {
            $photoDetails = PhotographerDetails::where(['photographer_id'=> $photoId, 'language_id'=>$defaultLanguageId])
                                                ->whereNull('deleted_at')
                                                ->first();
            $name = $photoDetails->name;
        }

        $language = "";
        if ($page == "anotherLanguage")
        {
            $existingLanguageIds = PhotographerDetails::where('photographer_id', $photoId)
                                                        ->whereNull('deleted_at')
                                                        ->get()
                                                        ->pluck('language_id')
                                                        ->toArray();

            $language = GlobalLanguage::select('global_language.id as globalLanguageId', 'world_languages.langEN as languageName')
                                    ->Join('world_languages', 'world_languages.id', '=', 'global_language.language_id')
                                    ->whereNotIn('global_language.id', $existingLanguageIds)
                                    ->where('global_language.status', 1)
                                    ->where('is_deleted',0)
                                    ->get()->toArray();

            $formTitle = "Add Photographer - Other Language ($name)";
        }
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        return view('admin.bahrainPhotographers.add',compact('defaultLanguage','defaultLanguageId','page','formTitle','otherLanguages','language','photoId'));
    }

    public function addPhotgrapher(Request $request)
    {
        if (isset($request->photoId))
        {
            $photographerDetails = new PhotographerDetails;
            $photographerDetails->photographer_id = $request->photoId;
            $photographerDetails->language_id = $request->defaultLanguage;
            $photographerDetails->name = $request->name;
            $photographerDetails->about = $request->about;
            $photographerDetails->location = $request->location;
            $photographerDetails->experience = $request->experience;
            $photographerDetails->seo_title = $request->seo_title;
            $photographerDetails->seo_description = $request->seo_description;
            $photographerDetails->seo_keyword = $request->seo_keyword;
            $photographerDetails->save();
        }
        else
        {
            $photographer = new Photographers;
            $photographer->web = $request->web;
            $photographer->status = $request->status;

            $reqdProfilePicImgWidth =Config::get('app.photographer_profile_pic.width');
            $reqdProfilePicImgHeight =Config::get('app.photographer_profile_pic.height');
            if($request->hasFile('profile_pic'))
            {
                if($request->profile_image_width != $reqdProfilePicImgWidth || $request->profile_image_height != $reqdProfilePicImgHeight)
                {
                    $image       = $request->file('profile_pic');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdProfilePicImgWidth, $reqdProfilePicImgHeight);
                    $image->move(public_path('assets/images/photographers/'), $filename);
                    $image_resize->save(public_path('assets/images/photographers/' .$filename));
                    $photographer->profile_pic = $filename;
                }
                else
                {
                    $image = $request->file('profile_pic');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/photographers/'), $filename);
                    $photographer->profile_pic = $request->file('profile_pic')->getClientOriginalName();
                }
            }

            $reqdCoverPicImgWidth =Config::get('app.photographer_cover_pic.width');
            $reqdCoverPicImgHeight =Config::get('app.photographer_cover_pic.height');
            if($request->hasFile('cover_photo'))
            {
                if($request->profile_image_width != $reqdCoverPicImgWidth || $request->profile_image_height != $reqdCoverPicImgHeight)
                {
                    $image       = $request->file('cover_photo');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdCoverPicImgWidth, $reqdCoverPicImgHeight);
                    $image->move(public_path('assets/images/photographers/'), $filename);
                    $image_resize->save(public_path('assets/images/photographers/' .$filename));
                    $photographer->cover_photo = $filename;
                }
                else
                {
                    $image = $request->file('cover_photo');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/photographers/'), $filename);
                    $photographer->cover_photo = $request->file('cover_photo')->getClientOriginalName();
                }
            }

            if($photographer->save())
            {
                $photographerDetails = new PhotographerDetails;
                $photographerDetails->photographer_id = $photographer->id;
                $photographerDetails->language_id = $request->defaultLanguageId;
                $photographerDetails->name = $request->name;
                $photographerDetails->about = $request->about;
                $photographerDetails->location = $request->location;
                $photographerDetails->experience = $request->experience;
                $photographerDetails->seo_title = $request->seo_title;
                $photographerDetails->seo_description = $request->seo_description;
                $photographerDetails->seo_keyword = $request->seo_keyword;
                $photographerDetails->save();
            }
        }

        $notification = array(
            'message' => 'Photographer added successfully!',
            'alert-type' => 'success'
        );

        return redirect('admin/photgraphers')->with($notification);
    }

    public function photographerEditView($id)
    {

        $page = isset($_GET['page']) ? $_GET['page'] : "getDefaultLangData";
        $lang_id = isset($_GET['lang']) ? $_GET['lang'] : "defaultLang";

        $nonDefaultLanguage = PhotographerDetails::select('photographer_details.language_id','langEN')
                                            ->join('global_language as gl','gl.id','=','photographer_details.language_id')
                                            ->leftjoin('world_languages as wl','wl.id','=','gl.language_id')
                                            ->whereNull('photographer_details.deleted_at')
                                            ->where('photographer_id',$id)
                                            ->get()->toArray();

        $defaultLanguage = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguage->id;
        $photographerDetails = [];

        if($page != 'otherLang')
        {
            $photographerDetails = Photographers::select('photographers.id as id', 'photographers.status as status','profile_pic','cover_photo','web',
                                    'pd.name','about','location','experience', 'pd.language_id as languageId','pd.seo_title','pd.seo_description','pd.seo_keyword')
                                    ->join('photographer_details as pd', 'pd.photographer_id', '=', 'photographers.id')
                                    ->findOrFail($id);

            if(!empty($photographerDetails))
            {
                $photographerDetails = $photographerDetails->toArray();
            }
        }
        else
        {
            $photographerDetails = Photographers::select('photographers.id as id',
                                    'pd.name','about','location','experience', 'pd.language_id as languageId','pd.seo_title','pd.seo_description','pd.seo_keyword')
                                    ->join('photographer_details as pd', 'pd.photographer_id', '=', 'photographers.id')
                                    ->where('language_id',$lang_id)
                                    ->where('photographer_id',$id)
                                    ->whereNull('pd.deleted_at')
                                    ->first($id);

            if(!empty($photographerDetails))
            {
                $photographerDetails = $photographerDetails->toArray();
                return response()->json(['status' => true, 'photographerDetails' => $photographerDetails]);
            }
        }
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $formTitle = "Edit Page";
        $baseUrl = $this->getBaseUrl();
        return view('admin.bahrainPhotographers.edit',compact('photographerDetails','nonDefaultLanguage','defaultLanguageId','otherLanguages','formTitle','baseUrl'));
    }

    public function updatePhotographer(Request $request)
    {
        $photographer = Photographers::findOrFail($request->photographer_id);

        if (isset($photographer))
        {
            $defaultLanguageData = $this->getDefaultLanguage(null);
            $defaultLanguage = $defaultLanguageData['language']['langEN'];
            $defaultLanguageId = $defaultLanguageData['id'];

            $photographer->web = $request->web;
            $photographer->status = $request->status;

            $reqdProfilePicImgWidth =Config::get('app.photographer_profile_pic.width');
            $reqdProfilePicImgHeight =Config::get('app.photographer_profile_pic.height');
            if($request->hasFile('profile_pic'))
            {
                if($request->profile_image_width != $reqdProfilePicImgWidth || $request->profile_image_height != $reqdProfilePicImgHeight)
                {
                    $image       = $request->file('profile_pic');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdProfilePicImgWidth, $reqdProfilePicImgHeight);
                    $image->move(public_path('assets/images/photographers/'), $filename);
                    $image_resize->save(public_path('assets/images/photographers/' .$filename));
                    $photographer->profile_pic = $filename;
                }
                else
                {
                    $image = $request->file('profile_pic');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/photographers/'), $filename);
                    $photographer->profile_pic = $request->file('profile_pic')->getClientOriginalName();
                }
            }

            $reqdCoverPicImgWidth =Config::get('app.photographer_cover_pic.width');
            $reqdCoverPicImgHeight =Config::get('app.photographer_cover_pic.height');
            if($request->hasFile('cover_photo'))
            {
                if($request->profile_image_width != $reqdCoverPicImgWidth || $request->profile_image_height != $reqdCoverPicImgHeight)
                {
                    $image       = $request->file('cover_photo');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdCoverPicImgWidth, $reqdCoverPicImgHeight);
                    $image->move(public_path('assets/images/photographers/'), $filename);
                    $image_resize->save(public_path('assets/images/photographers/' .$filename));
                    $photographer->cover_photo = $filename;
                }
                else
                {
                    $image = $request->file('cover_photo');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/photographers/'), $filename);
                    $photographer->cover_photo = $request->file('cover_photo')->getClientOriginalName();
                }
            }
            $photographer->save();

            $photographerDetails = PhotographerDetails::where(['photographer_id'=>$request->photographer_id, 'language_id'=>$request->language_id])->first();
            if (!empty($photographerDetails))
            {
                $photographerDetails->photographer_id = $request->photographer_id;
                $photographerDetails->language_id = $request->language_id;
                $photographerDetails->name = $request->name;
                $photographerDetails->about = $request->about;
                $photographerDetails->location = $request->location;
                $photographerDetails->experience = $request->experience;
                $photographerDetails->seo_title = $request->seo_title;
                $photographerDetails->seo_description = $request->seo_description;
                $photographerDetails->seo_keyword = $request->seo_keyword;

                if($photographerDetails->save())
                {
                    $notification = array(
                        'message' => 'Photographer updated successfully!',
                        'alert-type' => 'success'
                    );
                    return redirect('admin/photgraphers')->with($notification);
                }
            }
        }
    }

    public function photographerActiveInactive(Request $request)
    {
        try
        {
            $photographer = Photographers::where('id',$request->photographerId)->first();
            if($request->is_active == 1)
            {
                $photographer->status = $request->is_active;
                $msg = "Photographer Activated Successfully!";
            }
            else
            {
                $photographer->status = $request->is_active;
                $msg = "Photographer Deactivated Successfully!";
            }
            $photographer->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        }
        catch(\Exception $ex)
        {
            return view('errors.500');
        }
    }

    public function getLanguageWisePhotographer(Request $request)
    {
        $arrResult = [];

        $photographerDetails = PhotographerDetails::with('globalLanguage')
                                            ->where('photographer_id',$request['photographerId'])
                                            ->whereNull('photographer_details.deleted_at')
                                            ->get();
        $index = 0;
        foreach ($photographerDetails as $key)
        {
            $arrResult[$index]['id'] = $key->id;
            $arrResult[$index]['name'] = $key->name;
            $arrResult[$index]['languageName'] = $key['globalLanguage']['language']->langEN;
            $arrResult[$index++]['isDefault'] = $key['globalLanguage']->is_default;
        }

        return DataTables::of($arrResult)->make();
    }

    public function deletePhotographer(Request $request)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);

        $PhotographerDetails = PhotographerDetails::find($request->photographerDetailId);
        if ($PhotographerDetails->language_id == $defaultLanguageData['id'])
        {
            $temp = ['deleted_at'=>now()];
            $updatePhotographer = PhotographerDetails::where('photographer_id', $PhotographerDetails->photographer_id)->get();
            foreach($updatePhotographer as $photographer)
            {
                $photographer->deleted_at = Carbon::now();
                $photographer->save();
            }

            $photographerDelete = Photographers::find($PhotographerDetails->photographer_id);
            $photographerDelete->deleted_at = now();
            if ($photographerDelete->save())
            {
                return array(
                    'success' => true,
                    'message' => trans('Photographer Deleted Successfully!!')
                );
            }
        }
        else
        {
            $PhotographerDetails->deleted_at = now();
            if ($PhotographerDetails->save())
            {
                return array(
                    'success' => true,
                    'message' => trans('Photographer Deleted Successfully!!')
                );
            }
        }
    }
    public function postPortfolio(Request $request)
    {
      $data = $request->all();
      $photographerId = $data['photographerId'];
      if (isset($photographerId)){
      $portfolio = new PhotographerPortfolio;
      $portfolio->photographer_id=$photographerId;
      $portfolio->product_id=$data['product_id'];
      $portfolio->sort_order=$data['sort_order'];
      $portfolio->status=$data['status'];
      $reqportfolioImgWidth = Config::get('app.products.width');
      if($request->hasFile('image'))
      {
          if($data['portfolio_image_width'] != $reqportfolioImgWidth)
          {
              $image       = $request->file('image');
              $filename    = $image->getClientOriginalName();
              $image_resize = Image::make($image->getRealPath());
              $image_resize->resize($reqportfolioImgWidth);
              $image->move(public_path('assets/images/photographers/portfolio/'), $filename);
              $image_resize->save(public_path('assets/images/photographers/portfolio' .$filename));
              $portfolio->image = $filename;
          }
          else
          {
              $image = $request->file('image');
              $filename = $image->getClientOriginalName();
              $image->move(public_path('assets/images/photographers/portfolio/'), $filename);
              $portfolio->image = $request->file('image')->getClientOriginalName();
          }
        }
        if($portfolio->save()) {
            return array(
                'success' => true,
                'message' => "Portfolio added Successfully"
            );
        } else {
            return array(
                'success' => false,
                'message' => "Unable to add portfolio"
            );
        }
      }
    }
    public function getPortfolioList(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;
        $photographerID=$request['photographerId'];
        DB::statement(DB::raw('set @rownum=0'));

        $posrtfolios = PhotographerPortfolio::select('photographer_portfolio.id','image','sort_order','pd.title','photographer_portfolio.status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                            DB::raw("date_format(photographer_portfolio.created_at,'%Y-%m-%d %h:%i:%s') as p_created_at"))
                            ->leftjoin('products as P','P.id','=','photographer_portfolio.product_id')
                            ->join('product_details as pd','pd.product_id','=','P.id')
                            ->where('pd.language_id', $defaultLanguageId)
                            ->where('photographer_id', $photographerID)
                            ->whereNull('photographer_portfolio.deleted_at')
                            ->orderBy('photographer_portfolio.updated_at','desc')
                            ->get();

        return Datatables::of($posrtfolios)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }
    public function getPortfolioData(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;
        $portfolioId=$request['portfolioId'];
        DB::statement(DB::raw('set @rownum=0'));

        $portfolioDetails = PhotographerPortfolio::select('photographer_portfolio.id','photographer_portfolio.product_id','image','sort_order','pd.title','photographer_portfolio.status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                            DB::raw("date_format(photographer_portfolio.created_at,'%Y-%m-%d %h:%i:%s') as p_created_at"))
                            ->leftjoin('products as P','P.id','=','photographer_portfolio.product_id')
                            ->join('product_details as pd','pd.product_id','=','P.id')
                            ->where('pd.language_id', $defaultLanguageId)
                            ->where('photographer_portfolio.id', $portfolioId)
                            ->first();
        return $portfolioDetails;
    }
    public function updatePortfolio(Request $request)
    {
      $data = $request->all();
      $photographerId = $data['photographerId'];
      $portfolioId = $data['portfolioId'];
      if (isset($photographerId) && isset($portfolioId)){
      $portfolio = PhotographerPortfolio::findOrFail($portfolioId);
      $portfolio->product_id=$data['product_id'];
      $portfolio->sort_order=$data['sort_order'];
      $portfolio->status=$data['status'];
      $reqportfolioImgWidth = Config::get('app.products.width');
      if($request->hasFile('image'))
      {
          if($data['portfolio_image_width'] != $reqportfolioImgWidth)
          {
              $image       = $request->file('image');
              $filename    = $image->getClientOriginalName();
              $image_resize = Image::make($image->getRealPath());
              $image_resize->resize($reqportfolioImgWidth);
              $image->move(public_path('assets/images/photographers/portfolio/'), $filename);
              $image_resize->save(public_path('assets/images/photographers/portfolio' .$filename));
              $portfolio->image = $filename;
          }
          else
          {
              $image = $request->file('image');
              $filename = $image->getClientOriginalName();
              $image->move(public_path('assets/images/photographers/portfolio/'), $filename);
              $portfolio->image = $request->file('image')->getClientOriginalName();
          }
        }
        if($portfolio->save()) {
            return array(
                'success' => true,
                'message' => "Portfolio updated Successfully"
            );
        } else {
            return array(
                'success' => false,
                'message' => "Unable to update portfolio"
            );
        }
      }
    }
    public function deletePortfolio(Request $request)
    {
        $portfolio = PhotographerPortfolio::findOrFail($request->portfolioId);
        $portfolio->deleted_at = now();
        if ($portfolio->save())
        {
          return array(
            'success' => true,
            'message' => trans('Portfolio Deleted Successfully!!')
          );
        }
      }
}
?>
