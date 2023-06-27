<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Imports\CustomersImport;
use App\Exports\CustomersExport;
use App\Traits\ReuseFunctionTrait;
use App\Models\CustomerGroups;
use Auth;
use DataTables;
use Excel;
use DB;
use Session;

class CustomerController extends Controller
{
    use ReuseFunctionTrait;

    /* ###########################################
    // Function: getCustomerList
    // Description: Display list of customers  
    // Parameter: request : ajax 
    // ReturnType: datatable object
    */ ###########################################
    public function getCustomerList(Request $request)
    {
        if($request->ajax()) {
            try {
                $id = Auth::guard('admin')->user()->id;
                $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

                $customers = \App\Models\Customer::select('customers.id', 'customers.customer_unique_id','customers.first_name', 'customers.last_name', 'customers.mobile', 'customers.email','group_name',
                            'customers.ip_address','customers.os_name','customers.browser_name','customers.browser_version' 
                            ,'customers.is_active', 'customers.is_deleted', DB::raw("date_format(customers.created_at,'%Y-%m-%d %h:%i:%s') as customer_created_at"),'customer_timezone.zone')
                        ->leftJoin('customer_timezone','customer_timezone.customer_id','=','customers.id')
                        ->leftJoin('customer_groups','customer_groups.id','=','customers.cust_group_id')
                        ->whereNull('customer_groups.deleted_at')
                        ->where('is_deleted', '=', 0)
                        ->get();
                        
                return Datatables::of($customers)->editColumn('user_zone', function () use($timezone){
                    return $timezone;
                })->make(true);            
            } catch (\Exception $e) {
                return view('errors.500');
            }            
        }    
        $custGroups = CustomerGroups::select('id','group_name')->whereNull('deleted_at')->get()->toArray();
        $baseUrl = $this->getBaseUrl();
        return view('admin.users.customer.list',compact('custGroups','baseUrl')); 
    }

    /* ###########################################
    // Function: editCustomer
    // Description: Display customer from their id  
    // Parameter: id : Int 
    // ReturnType: view
    */ ###########################################
    public function editCustomer($id)
    {
        $customers = \App\Models\Customer::where('id', $id)->first(); 
        $countries = DB::table('countries')->where('id', 17)->get();     
        $address_arr = [];
        $i = 0;
        $customer_address = \App\Models\CustomerAddress::where('customer_id', $id)->where('is_deleted', 0)->get();  
        foreach ($customer_address as $value) {
            $address_arr[$i]['address_id'] = $value->id;
            $address_arr[$i]['customer_name'] = $value->fullname;
            $address_arr[$i]['address_1'] = $value->address_1;
            $address_arr[$i]['address_2'] = $value->address_2; 
            // $city = DB::table('cities')->where('id', $value->city)->first();
            // $address_arr[$i]['city'] = $city->name;
            $address_arr[$i]['city'] = $value->city;
            // $state = DB::table('states')->where('id', $value->state)->first();
            // $address_arr[$i]['state'] = $state->name; 
            $address_arr[$i]['state'] = $value->state; 
            $country = DB::table('countries')->where('id', $value->country)->first();
            $address_arr[$i]['country'] = $country->name; 
            $address_arr[$i]['pincode'] = $value->pincode;
            $address_arr[$i]['is_default'] = $value->is_default;
            $address_arr[$i]['address_type'] = ($value->address_type == 1) ? "Home" : "Office";
            $i++;
        }
        return view('admin.users.customer.edit', compact('customers','countries'))->with('address_arr', $address_arr)->with(['tab' => '#tab-account']);
    }
    
    /* ###########################################
    // Function: updateAccountCustomer
    // Description: Update customer information
    // Parameter: first_name: String, last_name: String, phone: Int, email: String, gender: String  
    // ReturnType: none
    */ ###########################################
    public function updateAccountCustomer(Request $request)
    {        

        $customer = \App\Models\Customer::where('id',$request->customer_id)->first();    
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->mobile = $request->phone;
        $customer->email = $request->email;
        $customer->gender = $request->gender;        
        $customer->save();
        $notification = array(
            'message' => 'Customer updated successfully!', 
            'alert-type' => 'success'
        );
        return redirect('/admin/customer/list')->with($notification);        
    }
    
    /* ###########################################
    // Function: exportCustomer
    // Description: Exporting Users to xlsx or xls file
    // Parameter: none  
    // ReturnType: none
    */ ###########################################
    public function exportCustomer(Request $request)
    {        
        // try{        
            return Excel::download(new CustomersExport, 'Alboumi_Customers.xlsx');    
        // } catch(\Exception $ex) {
        //     return view('errors.500');            
        // }
    }

    /* ###########################################
    // Function: getimportCustomerForm
    // Description: Import customer form
    // Parameter: none  
    // ReturnType: none
    */ ###########################################
    public function getimportCustomerForm()
    {
        return view('admin.users.customer.import');
    }

    /* ###########################################
    // Function: importCustomer
    // Description: Import customer from xlsx or xls file to database
    // Parameter: none  
    // ReturnType: none
    */ ###########################################
    public function importCustomer(Request $request)
    {                              
        try{ 
            if($request->hasFile('import_customer_file'))
            {              
                $import = new CustomersImport;
                $extension = $request->file('import_customer_file')->extension();            
                if($extension == "xlsx"){
                    Excel::import($import, $request->file('import_customer_file'), null, \Maatwebsite\Excel\Excel::XLSX);
                }
                elseif ($extension == "xls") {
                    Excel::import($import, $request->file('import_customer_file')); 
                }                
                $collection = $import->getCommon();                

                $counter = 0;
                $errors = []; 
                $suc_uploaded = []; 
                $fail_uploaded = [];          
                foreach($collection as $row) 
                {                
                    $email_arr = \App\Models\Customer::select('*')->pluck('email')->toArray();
                    $counter++;
                    $flag = 'true';
                    if($row[0] == "" || $row[1] == "" || $row[2] == "" || $row[3] == "" || $row[4] == "" || $row[5] == "")
                    {                
                        $errors[] = "Record is incomplete for Row - ".$counter.". Please try again.";        
                        $flag = 'false';
                    }

                    if(in_array($row[3], $email_arr))
                    {
                        $errors[] = $row[3]. " is already exist. Please use different email.";  
                        $flag = 'false';                 
                    }

                    if(!in_array($row[3], $email_arr))
                    {                
                        if (!filter_var($row[3], FILTER_VALIDATE_EMAIL)) {                    
                            $errors[] = $row[3]. " is Invalid.";
                            $flag = 'false';                                 
                        }                    
                    }

                    if (!is_numeric($row[4])) {                
                        $errors[] = 'Mobile number must be digits. Please see on Row '.$counter;   
                        $flag = 'false';             
                    }

                    if (strlen($row[5]) < 6) {                
                        $errors[] = 'Password for '. $row[3] . ' should be 6 digits.';   
                        $flag = 'false';             
                    }
                    
                    if($flag == 'true')
                    {                                  
                        $brands = \App\Models\Customer::getCustomerUniqueId();                            
                        $customer = new \App\Models\Customer;                                
                        $customer->customer_unique_id =  $brands;
                        $customer->first_name =  $row[0];
                        $customer->last_name = $row[1];                        
                        // $customer->mobile = $row[2];
                        $customer->gender = $row[2];
                        $customer->email = $row[3];
                        // $customer->parent_id = Auth::guard('admin')->user()->id;
                        $customer->password = Hash::make($row[5]);
                        // $customer->token = $this->generateRandomString(60);
                        $customer->save();
                        $suc_uploaded[] = $counter;                                       
                    } 
                    else
                    {
                        $fail_uploaded[] = $counter;
                    }                            
                }                      
                return redirect()->back()->with('msg', $errors)->with('success', $suc_uploaded)->with('faile', $fail_uploaded);                                         
            }                            
        } catch(\Maatwebsite\Excel\Validators\ValidationException $ex) {
            return view('errors.500');            
        }
        
    }

    /* ###########################################
    // Function: deleteCustomer
    // Description: Delete customer from database
    // Parameter: customer_id: Int, is_deleted: Int 
    // ReturnType: array
    */ ###########################################
    public function deleteCustomer($customer_id, Request $request)
    {
        $is_deleted = $request->is_deleted;
        $customer = \App\Models\Customer::where('id', $customer_id)->first();
        if($customer)
        {
            if($is_deleted == 0)
            {
                $customer->is_deleted = 1;
            }            
            $customer->save();
            $result['status'] = 'true';
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            return $result;
        }
    }

    /* ###########################################
    // Function: customerActDeaAct
    // Description: Activate/Deactivate customer from admin panel
    // Parameter: customer_id: Int, is_active: Int 
    // ReturnType: array
    */ ###########################################
    public function customerActDeaAct(Request $request)
    {
        try {
            $user = \App\Models\Customer::where('id',$request->customer_id)->first();
            if($request->is_active == 1) 
            {
                $user->is_active = $request->is_active;
                $msg = "Customer Activated Successfully!";
            }
            else
            {
                $user->is_active = $request->is_active;
                $msg = "Customer Deactivated Successfully!";
            }            
            $user->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        } catch(\Exception $ex) {
            return view('errors.500');            
        }        
    }

    /* ###########################################
    // Function: getStates
    // Description: Get states/territory data from database
    // Parameter: country_id: Int
    // ReturnType: object
    */ ###########################################
    public function getStates($id, Request $request)
    {
        if($request->ajax())
        {
            $states = DB::table('states')->where('country_id',$id)->get();
            return $states;
        }        
    }

    /* ###########################################
    // Function: getCities
    // Description: Get city from state id from database
    // Parameter: state_id: Int
    // ReturnType: object
    */ ###########################################
    public function getCities($id, Request $request)
    {
        if($request->ajax())
        {
            $cities = DB::table('cities')->where('state_id',$id)->get();
            return $cities;
        }        
    }

    /* ###########################################
    // Function: addCustomerAddress
    // Description: Add customer address into database 
    // Parameter: customer_id: Int, full_name: String, address_1: String, address_2: String, cities: Int, 
        states: Int, country: Int, address_type: Int, pincode: Int, phone_1: Int, phone_2: Int, is_default: Int
    // ReturnType: none
    */ ###########################################
    public function addCustomerAddress(Request $request)
    {            
        $cust_address = new \App\Models\CustomerAddress;
        $cust_address->customer_id = $request->customer_id;
        $cust_address->fullname = $request->full_name;
        $cust_address->address_1 = $request->address_1;
        $cust_address->address_2 = $request->address_2;
        $cust_address->city = $request->cities;
        $cust_address->state = $request->states;
        $cust_address->country = $request->country;
        $cust_address->address_type = $request->address_type;
        $cust_address->pincode = $request->pincode;
        $cust_address->phone1 = $request->phone_1;
        $cust_address->phone2 = $request->phone_2;
        if($request->is_default == 1)
        {
            $is_default = \App\Models\CustomerAddress::where('customer_id', $request->customer_id)->where('is_default', 1)->count();
            if($is_default == 1)
            {
                \App\Models\CustomerAddress::where('customer_id', $request->customer_id)->update(['is_default' => 0]);
            }
            $cust_address->is_default = 1;   
        }
        else
        {
            $cust_address->is_default = 0;   
        }                
        if($cust_address->save())
        {
            // Session::flash('message', 'Address added successfully!'); 
            // Session::flash('alert-class', 'alert-success'); 
            return redirect('admin/customer/edit/'.$request->customer_id.'#tab-address')
            ->with(['tab' => '#tab-address'])->with('msg', 'Address added successfully!')
            ->with('alert_type', true);
        }
        else
        {
            // Session::flash('message', 'Falied to add address!'); 
            // Session::flash('alert-class', 'alert-danger'); 
            return redirect('admin/customer/edit/'.$request->customer_id.'#tab-address')
            ->with(['tab' => '#tab-address'])->with('msg', 'Falied to add address!')
            ->with('alert_type', false);
        }
    }

    /* ###########################################
    // Function: editCustAddress
    // Description: Get customer address details from customer id
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function editCustAddress($id)
    {
        $address_arr = [];
        $i = 0;
        $customer_address = \App\Models\CustomerAddress::where('id', $id)->first();                    
        // $city = DB::table('cities')->where('id', $customer_address->city)->first();
        // $address_arr['city'] = $city->id; 
        $address_arr['city'] = $customer_address->city; 
        // $state = DB::table('states')->where('id', $customer_address->state)->first();
        // $address_arr['state'] = $state->id;
        $address_arr['state'] = $customer_address->state;         
        $country = DB::table('countries')->where('id', $customer_address->country)->first();            
        $address_arr['country'] = $country->id; 
        $cities = DB::table('cities')->get();
        $states = DB::table('states')->get();
        $countries = DB::table('countries')->where('id', 17)->get();
        return view('admin.users.customer.edit-address', compact('customer_address','cities','states','countries'))->with('address_arr', $address_arr);
    }

    /* ###########################################
    // Function: updateCustomerAddress
    // Description: Update existing customer address from their address id
    // Parameter: address_id: Int, customer_id: Int, full_name: String, address_1: String, address_2: String, cities: Int, 
        states: Int, country: Int, address_type: Int, pincode: Int, phone_1: Int, phone_2: Int, is_default: Int
    // ReturnType: none
    */ ###########################################
    public function updateCustomerAddress(Request $request)
    {        
        // return $request;
        $cust_address = \App\Models\CustomerAddress::where('id', $request->address_id)->first();
        $cust_address->customer_id = $request->customer_id;
        $cust_address->fullname = $request->full_name;
        $cust_address->address_1 = $request->address_1;
        $cust_address->address_2 = $request->address_2;
        $cust_address->city = $request->cities;
        $cust_address->state = $request->states;
        $cust_address->country = $request->country;
        $cust_address->address_type = $request->address_type;
        $cust_address->pincode = $request->pincode;
        $cust_address->phone1 = $request->phone_1;
        $cust_address->phone2 = $request->phone_2;        
        if($request->is_default == 1)
        {
            $is_default = \App\Models\CustomerAddress::where('id', $request->address_id)->where('is_default', 1)->first();            
            if(!$is_default)
            {
                \App\Models\CustomerAddress::where('id', '<>',$request->address_id)->where('customer_id', $request->customer_id)->update(['is_default' => 0]);            
                $cust_address->is_default = 1;
            }                        
        }
        else
		{
			$cust_address->is_default = 0;
		}                                                   
        if($cust_address->save())
        {
            // Session::flash('message', 'Address updated successfully!'); 
            // Session::flash('alert-class', 'alert-success'); 
            return redirect('admin/customer/edit/'.$request->customer_id.'#tab-address')
            ->with(['tab' => '#tab-address'])->with('msg', 'Address updated successfully!')
            ->with('alert_type', true);
        }
        else
        {
            // Session::flash('message', 'Falied to update address!'); 
            // Session::flash('alert-class', 'alert-danger'); 
            return redirect()->back()->with('msg', 'Falied to update address!')
            ->with('alert_type', false);
        }
    }

    /* ###########################################
    // Function: deleteCustAddress
    // Description: Delete customer address from their address id
    // Parameter: id: Int
    // ReturnType: array
    */ ###########################################
    public function deleteCustAddress($id)
    {
        $customer_address = \App\Models\CustomerAddress::where('id', $id)->first(); 
        if($customer_address)
        {
            $customer_address->is_deleted = 1;
            $customer_address->save();

            $address_arr = [];
            $i = 0;
            $customer_address = \App\Models\CustomerAddress::where('customer_id', $customer_address->customer_id)->where('is_deleted', 0)->get();  
            foreach ($customer_address as $value) {
                $address_arr[$i]['address_id'] = $value->id;
                $address_arr[$i]['customer_name'] = $value->fullname;
                $address_arr[$i]['address_1'] = $value->address_1;
                $address_arr[$i]['address_2'] = $value->address_2; 
                // $city = DB::table('cities')->where('id', $value->city)->first();
                // $address_arr[$i]['city'] = $city->name;
                $address_arr[$i]['city'] = $value->city; 
                // $state = DB::table('states')->where('id', $value->state)->first();
                // $address_arr[$i]['state'] = $state->name; 
                $address_arr[$i]['state'] = $value->state; 
                $country = DB::table('countries')->where('id', $value->country)->first();
                $address_arr[$i]['country'] = $country->name; 
                $address_arr[$i]['pincode'] = $value->pincode;
                $address_arr[$i]['is_default'] = $value->is_default;
                $address_arr[$i]['address_type'] = ($value->address_type == 1) ? "Home" : "Office";
                $i++;
            }
            $result['status'] = 'true';
            $result['address_arr'] = $address_arr;     
            $result['tab'] = '#tab-address';        
        }
        else
        {
            $result['status'] = 'false';        
        }
        return $result;
    }

    public function assignCustGroup(Request $request)
    {
        if(!empty($request->customerIds))
        {
            foreach($request->customerIds as $custId)
            {
                $assignGroup = \App\Models\Customer::where('id', $custId)->first();
                $assignGroup->cust_group_id = $request->groupId;
                $assignGroup->save();
            }
            $result['status'] = 'true';
            $result['msg'] = "Group assigned successfully !!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Please select at least one customer!!";
            return $result;
        }
        
    }

    public function removeCustGroup(Request $request)
    {
        if(!empty($request->customerIds))
        {
            foreach($request->customerIds as $custId)
            {
                $assignGroup = \App\Models\Customer::where('id', $custId)->first();
                $assignGroup->cust_group_id = 0;
                $assignGroup->save();
            }
            $result['status'] = 'true';
            $result['msg'] = "Group removed successfully !!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Please select at least one customer!!";
            return $result;
        }
        
    }
}
