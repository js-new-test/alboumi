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
use App\Models\PhotoBooks;
use App\Models\Product;
use DB;
use Image;
use App\Traits\ReuseFunctionTrait;

class PhotoBookController extends Controller
{
	use ReuseFunctionTrait;

	function __construct()
	{
		$this->projectName = "Alboumi";
	}

	public function getBooks()
	{
		$projectName = $this->projectName;
		$pageTitle = 'Photo Books';
		$baseUrl = $this->getBaseUrl();
		$languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                                    ->select('global_language.id','alpha2','langEN','is_default')
									->where('status',1)
									->where('is_deleted',0)
                                    ->get();

        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();

		return view('admin.photobooks.books', compact('projectName', 'pageTitle', 'baseUrl', 'languages', 'defaultCurrency'));
	}

	public function getBooksList(Request $request)
	{
		$global_languages = \App\Models\GlobalLanguage::where('status',1)
		->where('is_deleted', 0)
		->pluck('id');
		$books = PhotoBooks::whereIn('language_id', $global_languages)->whereNull('deleted_at');

		return DataTables::of($books)
		->filter(function ($query) use ($request) {
            if ($request->has('languageId')) {
                $query->where('photo_books.language_id', '=', $request->get('languageId'));
            }
        })->make(true);
	}

	public function getAddBook()
	{
		$projectName = $this->projectName;
		$pageTitle = 'Add Photo Book';
		$baseUrl = $this->getBaseUrl();

		$defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();

		$defaultLang = $this->getDefaultLanguage();
		$products = Product::select('products.id','product_details.title')->leftjoin('product_details','product_details.product_id','=','products.id')->where('product_details.language_id',$defaultLang)->whereNull('products.deleted_at')->get();

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
			return view('admin.photobooks.addBook', compact('projectName', 'pageTitle', 'baseUrl',
			'languages','defaultCurrency', 'products'));
		}
		else
		{
			$language = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status', 1)
            ->where('global_language.is_deleted', 0)
			->first();

			return view('admin.photobooks.addBook', compact('projectName', 'pageTitle', 'baseUrl',
			'language','defaultCurrency','products'));
		}
	}

	public function postAddBook(Request $request)
	{
		$max_height = config('app.photobook_image.height');
        $max_width = config('app.photobook_image.width');
        $loaded_image_height = $request['loaded_image_height'];
        $loaded_image_width = $request['loaded_image_width'];

        // if(!($loaded_image_width > $max_width) && !($loaded_image_height > $max_height))
        // {
        //     return redirect()->back()->with('msg', "Seller Image should be 350(Height) X 350(Width))")->with('alert-class', false);
        // }

		$data = $request->all();
		$books = new PhotoBooks;
		if($request->hasFile('bookImage'))
        {
			if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
			{
				$image = $request->file('bookImage');
				$ext = $request->file('bookImage')->extension();
				$filename = "book_".rand().'_'.time().'.'.$ext;

				$image_resize = Image::make($image->getRealPath());
				$image_resize->resize(config('app.photobook_image.width'), config('app.photobook_image.height'));
				$image->move(public_path().'/assets/images/books/', $filename);
				$image_resize->save(public_path('/assets/images/books/' .$filename));
			}
			else
			{
				$photo = $request->file('bookImage');
				$ext = $request->file('bookImage')->extension();
				$filename = "book_".rand().'_'.time().'.'.$ext;
				$photo->move(public_path().'/assets/images/books/', $filename);
			}

            // $photo = $request->file('sellerImage');
            // $ext = $request->file('sellerImage')->extension();
            // $filename = "seller_".rand().'_'.time().'.'.$ext;
            // $photo->move(public_path().'/assets/images/seller/', $filename);
        }

		$books->language_id = $data['language'];
		$books->title = $data['title'];
		$books->image = $filename;
		$books->link = $data['link'];
		$books->price = $data['price'];
		$books->sort_order = $data['sortOrder'];
		$books->description = $data['description'];
		$books->status = $data['status'];
		$books->save();


		$notification = array(
            'message' => 'Photo book added successfully!',
            'alert-type' => 'success'
        );

		return redirect('admin/books')->with($notification);
	}

	public function getEditBook($id)
	{
		$projectName = $this->projectName;
		$pageTitle = 'Edit Photo Book';
		$baseUrl = $this->getBaseUrl();

		$defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();
		$defaultLang = $this->getDefaultLanguage();
		$products = Product::select('products.id','product_details.title')->leftjoin('product_details','product_details.product_id','=','products.id')->where('product_details.language_id',$defaultLang)->whereNull('products.deleted_at')->get();

		$books = PhotoBooks::find($id);

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
			return view('admin.photobooks.editBook', compact('projectName', 'pageTitle', 'baseUrl',
			'languages', 'books', 'defaultCurrency','products'));
		}
		else
		{
			$language = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status', 1)
            ->where('global_language.is_deleted', 0)
			->first();
			return view('admin.photobooks.editBook', compact('projectName', 'pageTitle', 'baseUrl',
			'language', 'books', 'defaultCurrency','products'));
		}
	}

	public function postEditBook(Request $request)
	{
		$data = $request->all();

		$books = PhotoBooks::find($data['bookId']);
		if($request->hasFile('bookImage'))
        {
        	$path = public_path('/assets/images/books').'/'.$books->image;
			if(file_exists($path))
			{
				unlink($path);
			}
        	$max_height = config('app.photobook_image.height');
	        $max_width = config('app.photobook_image.width');
	        $loaded_image_height = $request['loaded_image_height'];
	        $loaded_image_width = $request['loaded_image_width'];

			if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
			{
				$image = $request->file('bookImage');
				$ext = $request->file('bookImage')->extension();
				$filename = "book_".rand().'_'.time().'.'.$ext;

				$image_resize = Image::make($image->getRealPath());
				$image_resize->resize(config('app.photobook_image.width'), config('app.photobook_image.height'));
				$image->move(public_path().'/assets/images/books/', $filename);
				$image_resize->save(public_path('/assets/images/books/' .$filename));
			}
			else
			{
				$photo = $request->file('bookImage');
				$ext = $request->file('bookImage')->extension();
				$filename = "book_".rand().'_'.time().'.'.$ext;
				$photo->move(public_path().'/assets/images/books/', $filename);
			}

            // $photo = $request->file('sellerImage');
            // $ext = $request->file('sellerImage')->extension();
            // $filename = "seller_".rand().'_'.time().'.'.$ext;
			// $photo->move(public_path().'/assets/images/seller/', $filename);
			$books->image = $filename;
        }

		$books->language_id = $data['language'];
		$books->title = $data['title'];
		$books->link = $data['link'];
		$books->price = $data['price'];
		$books->sort_order = $data['sortOrder'];
		$books->description = $data['description'];
		$books->status = $data['status'];
		$books->save();

		$notification = array(
            'message' => 'Photo book updated successfully!',
            'alert-type' => 'success'
        );

		return redirect('admin/books')->with($notification);
	}

	public function getDeleteBook(Request $request)
	{
		$books = PhotoBooks::find($request['bookId']);
		$books->deleted_at = now();
		$books->save();

		$notification = array(
            'message' => 'Photo Book Deleted successfully!',
            'alert-type' => 'success'
        );

		return $notification;
	}
}
