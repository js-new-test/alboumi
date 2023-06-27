<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use DataTables;
use App\Models\GlobalCurrency;
// use App\Models\PromotionConditions;
use App\Models\GlobalLanguage;
// use App\Traits\CommonTrait;
use App\Models\BestSellers;
use DB;
use Image;
use App\Traits\ReuseFunctionTrait;

class SellerController extends Controller
{
	use ReuseFunctionTrait;

	function __construct()
	{
		$this->projectName = "Alboumi";
	}

	public function getSellers()
	{
		$projectName = $this->projectName;
		$pageTitle = 'Best Seller';
		$baseUrl = $this->getBaseUrl();
		$languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                                    ->select('global_language.id','alpha2','langEN','is_default')
									->where('status',1)
									->where('is_deleted',0)
                                    ->get();

        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();

		return view('admin.seller.seller', compact('projectName', 'pageTitle', 'baseUrl', 'languages', 'defaultCurrency'));
	}

	public function getSellersList(Request $request)
	{
		$global_languages = \App\Models\GlobalLanguage::where('status',1)
		->where('is_deleted', 0)
		->pluck('id');
		$seller = BestSellers::whereIn('language_id', $global_languages)->whereNull('deleted_at');

		return DataTables::of($seller)
		->filter(function ($query) use ($request) {
            if ($request->has('languageId')) {
                $query->where('best_sellers.language_id', '=', $request->get('languageId'));
            }
        })->make(true);
	}

	public function getAddSeller()
	{
		$projectName = $this->projectName;
		$pageTitle = 'Add Seller';
		$baseUrl = $this->getBaseUrl();

		$defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();

		$defaultLang = $this->getDefaultLanguage();
		$categories = getParentCategories($defaultLang);

		$global_languages = \App\Models\GlobalLanguage::where('status',1)
		->where('is_deleted', 0)
		->count();
		if($global_languages >= 2)
		{
			$languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
			->select('global_language.id','alpha2','langEN')
			->where('status',1)
			->where('is_deleted',0)
			->get();
			return view('admin.seller.addSeller', compact('projectName', 'pageTitle', 'baseUrl',
			'languages','defaultCurrency', 'categories'));
		}
		else
		{
			$language = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status', 1)
            ->where('global_language.is_deleted', 0)
			->first();

			return view('admin.seller.addSeller', compact('projectName', 'pageTitle', 'baseUrl',
			'language','defaultCurrency','categories'));
		}
	}

	public function postAddSeller(Request $request)
	{
		$max_height = config('app.seller_image.height');
        $max_width = config('app.seller_image.width');
        $loaded_image_height = $request['loaded_image_height'];
        $loaded_image_width = $request['loaded_image_width'];

        // if(!($loaded_image_width > $max_width) && !($loaded_image_height > $max_height))
        // {
        //     return redirect()->back()->with('msg', "Seller Image should be 350(Height) X 350(Width))")->with('alert-class', false);
        // }

		$data = $request->all();
		$seller = new BestSellers;
		if($request->hasFile('sellerImage'))
        {
			if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
			{
				$image = $request->file('sellerImage');
				$ext = $request->file('sellerImage')->extension();
				$filename = "seller_".rand().'_'.time().'.'.$ext;

				$image_resize = Image::make($image->getRealPath());
				$image_resize->resize(config('app.seller_image.width'), config('app.seller_image.height'));
				$image->move(public_path().'/assets/images/seller/', $filename);
				$image_resize->save(public_path('/assets/images/seller/' .$filename));
			}
			else
			{
				$photo = $request->file('sellerImage');
				$ext = $request->file('sellerImage')->extension();
				$filename = "seller_".rand().'_'.time().'.'.$ext;
				$photo->move(public_path().'/assets/images/seller/', $filename);
			}

            // $photo = $request->file('sellerImage');
            // $ext = $request->file('sellerImage')->extension();
            // $filename = "seller_".rand().'_'.time().'.'.$ext;
            // $photo->move(public_path().'/assets/images/seller/', $filename);
        }

		$seller->language_id = $data['language'];
		$seller->title = $data['title'];
		$seller->image = $filename;
		$seller->category_id = $data['link'];
		$seller->price = $data['price'];
		$seller->sort_order = $data['sortOrder'];
		$seller->status = $data['status'];
		$seller->save();


		$notification = array(
            'message' => 'Seller added successfully!',
            'alert-type' => 'success'
        );

		return redirect('admin/sellers')->with($notification);
	}

	public function getEditSeller($id)
	{
		$projectName = $this->projectName;
		$pageTitle = 'Edit Seller';
		$baseUrl = $this->getBaseUrl();

		$defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();
		$defaultLang = $this->getDefaultLanguage();
		$categories = getParentCategories($defaultLang);

		$seller = BestSellers::find($id);

		$global_languages = \App\Models\GlobalLanguage::where('status',1)
		->where('is_deleted', 0)
		->count();
		if($global_languages >= 2)
		{
			$languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
			->select('global_language.id','alpha2','langEN')
			->where('status',1)
			->where('is_deleted',0)
			->get();
			return view('admin.seller.editSeller', compact('projectName', 'pageTitle', 'baseUrl',
			'languages', 'seller', 'defaultCurrency','categories'));
		}
		else
		{
			$language = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status', 1)
            ->where('global_language.is_deleted', 0)
			->first();
			return view('admin.seller.editSeller', compact('projectName', 'pageTitle', 'baseUrl', 
			'language', 'seller', 'defaultCurrency','categories'));
		}
	}

	public function postEditSeller(Request $request)
	{
		$data = $request->all();

		$seller = BestSellers::find($data['sellerId']);
		if($request->hasFile('sellerImage'))
        {
        	$path = public_path('/assets/images/seller').'/'.$seller->image;
			if(file_exists($path))
			{
				unlink($path);
			}
        	$max_height = config('app.seller_image.height');
	        $max_width = config('app.seller_image.width');
	        $loaded_image_height = $request['loaded_image_height'];
	        $loaded_image_width = $request['loaded_image_width'];

			if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
			{
				$image = $request->file('sellerImage');
				$ext = $request->file('sellerImage')->extension();
				$filename = "seller_".rand().'_'.time().'.'.$ext;

				$image_resize = Image::make($image->getRealPath());
				$image_resize->resize(config('app.seller_image.width'), config('app.seller_image.height'));
				$image->move(public_path().'/assets/images/seller/', $filename);
				$image_resize->save(public_path('/assets/images/seller/' .$filename));
			}
			else
			{
				$photo = $request->file('sellerImage');
				$ext = $request->file('sellerImage')->extension();
				$filename = "seller_".rand().'_'.time().'.'.$ext;
				$photo->move(public_path().'/assets/images/seller/', $filename);
			}

            // $photo = $request->file('sellerImage');
            // $ext = $request->file('sellerImage')->extension();
            // $filename = "seller_".rand().'_'.time().'.'.$ext;
			// $photo->move(public_path().'/assets/images/seller/', $filename);
			$seller->image = $filename;
        }

		$seller->language_id = $data['language'];
		$seller->title = $data['title'];
		$seller->category_id = $data['link'];
		$seller->price = $data['price'];
		$seller->sort_order = $data['sortOrder'];
		$seller->status = $data['status'];
		$seller->save();

		$notification = array(
            'message' => 'Seller updated successfully!',
            'alert-type' => 'success'
        );

		return redirect('admin/sellers')->with($notification);
	}

	public function getDeleteSeller(Request $request)
	{
		$seller = BestSellers::find($request['sellerId']);
		$seller->deleted_at = now();
		$seller->save();

		$notification = array(
            'message' => 'Seller Deleted successfully!',
            'alert-type' => 'success'
        );

		return $notification;
	}
}
