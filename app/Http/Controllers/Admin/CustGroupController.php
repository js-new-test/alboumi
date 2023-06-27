<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\GlobalLanguage;
use App\Models\CustomerGroups;
use App\Models\Customer;
use Validator;
use Carbon\Carbon;
use DataTables;
use DB;
use Session;

class CustGroupController extends Controller
{
    public function getCustomerGroups()
    {
        $page_name = 'Customer Groups';
        $project_name = 'Alboumi';
        $languages = GlobalLanguage::getAllLanguages();
        $baseUrl = $this->getBaseUrl();
        return view('admin.users.customerGroups.index',compact('languages','page_name','baseUrl','project_name'));
    }

    public function getCustomerGroupsList(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));

        $customerGroups = CustomerGroups::select('id','group_name',
                        DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                        ->whereNull('deleted_at')
                        ->orderBy('updated_at','desc')
                        ->get();

        return Datatables::of($customerGroups)->make(true);
    }
    public function groupAddView()
    {
        $page_name = 'Add Customer Groups';
        $project_name = 'Alboumi';
        return view('admin.users.customerGroups.add',compact('project_name','page_name'));
    }

    public function addGroup(Request $request)
    {
        try
        {
            $messsages = array(
                'group_name.required' => 'Please write group name'
            );

            $validator = Validator::make($request->all(), [
                'group_name'=>'required'
            ],$messsages);

            if ($validator->fails())
            {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }

            $custGroup = new CustomerGroups;
            $custGroup->group_name = $request->group_name;
            $custGroup->save();

            $notification = array(
                'message' => 'Customer Group added successfully!',
                'alert-type' => 'success'
            );
            return redirect('admin/custGroups')->with($notification);
        }
        catch (\Exception $e)
        {
                Session::flash('error', $e->getMessage());
            return redirect('admin/custGroups');
        }
    }

    public function groupEditView($id)
    {
        $custGroup = CustomerGroups::findOrFail($id);
        $page_name = 'Update Customer Groups';
        $project_name = 'Alboumi';
        if(!empty($custGroup))
        {
            $baseUrl = $this->getBaseUrl();
            return view('admin.users.customerGroups.edit',compact('custGroup','page_name','baseUrl','project_name'));
        }
    }

    public function updateSGroup(Request $request)
    {
        $custGroup = CustomerGroups::findOrFail($request->group_id);

        if(!empty($custGroup))
        {
            $messsages = array(
                'group_name.required' => 'Please write group name'
            );

            $validator = Validator::make($request->all(), [
                'group_name'=>'required'
            ],$messsages);

            if ($validator->fails())
            {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
            $custGroup->group_name = $request->group_name;
            $custGroup->save();

            $notification = array(
                'message' => 'Customer Group updated successfully!',
                'alert-type' => 'success'
            );

            return redirect('admin/custGroups')->with($notification);
        }
    }

    public function deleteGroup(Request $request)
    {
        $custGroup = CustomerGroups::select('id')
                        ->where('id', $request->group_id)
                        ->first();

        $custList = Customer::where('cust_group_id',$request->group_id)->get();
        if(!empty($custList))
        {
            foreach($custList as $cust)
            {
                $cust->cust_group_id = 0;
                $cust->save();
            }
        }
        if(!empty($custGroup))
        {
            $custGroup->deleted_at = Carbon::now();
            $custGroup->save();
            $result['status'] = 'true';
            $result['msg'] = "Customer group Deleted Successfully!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Something went wrong!!";
            return $result;
        }
    }
}