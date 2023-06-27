<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use DataTables;
use Auth;
use DB;

class HolidayController extends Controller
{
    /* ###########################################
    // Function: listHoliday
    // Description: Display list of holidays
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function listHoliday(Request $request)
    {
        if($request->ajax())
        {
            $id = Auth::guard('admin')->user()->id;
            $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

            $holiday = \App\Models\Holiday::select('id','name', 'date', 
            DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as holiday_created_at"))
            ->where('is_deleted', 0)->get();            
            return Datatables::of($holiday)->editColumn('user_zone', function () use($timezone){
                return $timezone;
            })->make(true); 
        }
        
        return view('admin.settings.holiday.list');
    }

    public function showHolidayForm()
    {
        return view('admin.settings.holiday.add');
    }

    public function addHoliday(Request $request)
    {
        try {
            $holiday = new \App\Models\Holiday;
            $holiday->date = $request->holiday_date;
            $holiday->name = $request->name;
            $holiday->save();
            $notification = array(
                'message' => config('message.Holiday.HolidayAddSuccess'),                 
                'alert-type' => 'success'
            );
            return redirect('admin/holiday')->with($notification);    
        } catch (\Exception $e) {
            return view('errors.500');
        }
        
    }

    public function editHoliday($id)
    {
        $holiday = \App\Models\Holiday::where('id', $id)->first();
        return view('admin.settings.holiday.edit', compact('holiday'));
    }

    public function updateHoliday(Request $request)
    {
        try {
            $holiday = \App\Models\Holiday::where('id', $request->holiday_id)->first();
            $holiday->date = $request->holiday_date;
            $holiday->name = $request->name;
            $holiday->save();
            $notification = array(
                'message' => config('message.Holiday.HolidayUpdateSuccess'),                 
                'alert-type' => 'success'
            );
            return redirect('admin/holiday')->with($notification);    
        } catch (\Exception $e) {
            return view('errors.500');
        }
    }

    public function deleteHoliday(Request $request)
    {
        if($request->ajax())
        {
            $holiday = \App\Models\Holiday::where('id', $request->h_id)->first();
            if($holiday)
            {
                $holiday->is_deleted = 1;
                $holiday->save();
                $result['status'] = 'true';
                $result['msg'] = config('message.Holiday.HolidayDeleteSuccess');
                return $result;
            }
            else
            {
                $result['status'] = 'false';
                $result['msg'] = config('message.500.SomeThingWrong');
                return $result;
            }
        }   
    }

    public function filterHoliday(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $holiday = \App\Models\Holiday::select('id','name', 'date', 
        DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as holiday_created_at"))
        ->where('is_deleted', 0)
        ->whereBetween('date', [$request->start_date, $request->end_date])
        ->get();            
        return Datatables::of($holiday)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }
}
