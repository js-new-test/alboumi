<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use DataTables;
use App\Models\GlobalLanguage;
use App\Models\HowItWorks;
use DB;
use Image;
use Config;

class HowItWorksController extends Controller
{
	function __construct()
	{
		$this->projectName = "Alboumi";
	}

	public function getHowitWorks()
	{
		$projectName = $this->projectName;
		$pageTitle = 'How it Works';
		$baseUrl = $this->getBaseUrl();
		$languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                                    ->select('global_language.id','alpha2','langEN','is_default')
									->where('global_language.status',1)
									->where('global_language.is_deleted',0)
                                    ->get();

		return view('admin.howItWorks.howItWorks', compact('projectName', 'pageTitle', 'baseUrl', 'languages'));
	}

	public function getHowitWorksList(Request $request)
	{
		$seller = HowItWorks::whereNull('deleted_at');
		return DataTables::of($seller)
		->filter(function ($query) use ($request) {
            if ($request->has('languageId')) {
                $query->where('how_it_works.language_id', '=', $request->get('languageId'));
            }
        })->make(true);
	}

	public function getAddHowitWorks()
	{
		$languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                                    ->select('global_language.id','alpha2','langEN')
                                    ->where('global_language.status',1)
									->where('global_language.is_deleted',0)
                                    ->get();

		$projectName = $this->projectName;
		$pageTitle = 'Add How it Works';
		$baseUrl = $this->getBaseUrl();

		return view('admin.howItWorks.addHowItWorks', compact('projectName', 'pageTitle', 'baseUrl', 'languages'));
	}

	public function postAddHowitWorks(Request $request)
	{
		$max_height = config('app.how_it_works_image.height');
        $max_width = config('app.how_it_works_image.width');
        $loaded_image_height = $request['loaded_image_height'];
        $loaded_image_width = $request['loaded_image_width'];
        
        // if(!($loaded_image_width > $max_width) && !($loaded_image_height > $max_height))
        // {
        //     return redirect()->back()->with('msg', "Image should be 300(Height) X 300(Width))")->with('alert-class', false);
        // }

		$data = $request->all();
		$howItWorks = new HowItWorks;

		$reqdImgWidth =Config::get('app.how_it_works_image.width');
		$reqdImgHeight =Config::get('app.how_it_works_image.height');
		if($request->hasFile('image')) 
		{
			if($loaded_image_width != $reqdImgWidth || $loaded_image_height != $reqdImgHeight)
			{
				$image       = $request->file('image');
				$filename    = $image->getClientOriginalName();

				$image_resize = Image::make($image->getRealPath());              
				$image_resize->resize($reqdImgWidth, $reqdImgHeight);
				$image->move(public_path('assets/images/howItWorks/'), $filename);
				$image_resize->save(public_path('assets/images/howItWorks/' .$filename));
				$howItWorks->image = $filename;
			}
			else
			{
				$image = $request->file('image');
				$filename = $image->getClientOriginalName();
				$image->move(public_path('assets/images/howItWorks/'), $filename);
				$howItWorks->image = $request->file('image')->getClientOriginalName();
			}
		}
		
		$howItWorks->language_id = $data['language'];
		$howItWorks->title = $data['title'];
		$howItWorks->image = $filename;
		$howItWorks->description = $data['description'];
		$howItWorks->sort_order = $data['sortOrder'];
		$howItWorks->status = $data['status'];
		$howItWorks->save();


		$notification = array(
            'message' => 'How it Works added successfully!', 
            'alert-type' => 'success'
        );

		return redirect('admin/howitWorks')->with($notification);
	}

	public function getEditHowitWorks($id)
	{
		$languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                                    ->select('global_language.id','alpha2','langEN')
                                    ->where('global_language.status',1)
									->where('global_language.is_deleted',0)
                                    ->get();

		$projectName = $this->projectName;
		$pageTitle = 'Edit How it Works';
		$baseUrl = $this->getBaseUrl();

		$howItWorks = HowItWorks::find($id);
		return view('admin.howItWorks.editHowItWorks', compact('projectName', 'pageTitle', 'baseUrl', 'languages', 'howItWorks'));
	}

	public function postEditHowitWorks(Request $request)
	{
		$data = $request->all();

		$howItWorks = HowItWorks::find($data['howitWorksId']);
		if($request->hasFile('image'))
        {

        	$max_height = config('app.seller_image.height');
	        $max_width = config('app.seller_image.width');
	        $loaded_image_height = $request['loaded_image_height'];
	        $loaded_image_width = $request['loaded_image_width'];

        	// if(!($loaded_image_width > $max_width) && !($loaded_image_height > $max_height))
	        // {
	        //     return redirect()->back()->with('msg', "Seller Image should be 350 (Height) X 350 (Width))")->with('alert-class', false);
	        // }
            $reqdImgWidth =Config::get('app.how_it_works_image.width');
			$reqdImgHeight =Config::get('app.how_it_works_image.height');

			if($loaded_image_width != $reqdImgWidth || $loaded_image_height != $reqdImgHeight)
			{
				$image       = $request->file('image');
				$filename    = $image->getClientOriginalName();

				$image_resize = Image::make($image->getRealPath());              
				$image_resize->resize($reqdImgWidth, $reqdImgHeight);
				$image->move(public_path('assets/images/howItWorks/'), $filename);
				$image_resize->save(public_path('assets/images/howItWorks/' .$filename));
				$howItWorks->image = $filename;
			}
			else
			{
				$image = $request->file('image');
				$filename = $image->getClientOriginalName();
				$image->move(public_path('assets/images/howItWorks/'), $filename);
				$howItWorks->image = $request->file('image')->getClientOriginalName();
			}
        }

		$howItWorks->language_id = $data['language'];
		$howItWorks->title = $data['title'];
		// $howItWorks->link = $data['link'];
		$howItWorks->description = $data['description'];
		$howItWorks->sort_order = $data['sortOrder'];
		$howItWorks->status = $data['status'];
		$howItWorks->save();

		$notification = array(
            'message' => 'How it Works updated successfully!', 
            'alert-type' => 'success'
        );

		return redirect('admin/howitWorks')->with($notification);
	}

	public function getDeleteHowitWorks(Request $request)
	{
		$seller = HowItWorks::find($request['howItWorksId']);
		$seller->deleted_at = now();
		$seller->save();

		$notification = array(
            'message' => 'How it Works Deleted successfully!', 
            'alert-type' => 'success'
        );

		return $notification;
	}

	public function postUpdateHowitWorksStatus(Request $request)
	{
		$data = $request->all();
		$howItWorks = HowItWorks::find($data['howItWorksId']);

		$status = 'Active';
		if ($request['statusForDelete'] == 0) {
			$status = 'Inactive';
		}
		$howItWorks->status = $status;
		$howItWorks->save();

		$message = "Status Changed Successfully";
		return array(
					'success' => true,
					'message' => $message
				);
	}
}
