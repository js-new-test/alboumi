<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $table = 'customer_address';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'fullname',
        'address_1',
        'address_2',
        'city',
        'state',
        'country',
        'address_type',
        'pincode',
        'phone1',
        'phone2',
        'is_default'
    ];

    public static function saveCustomerAddress($request)
    {        
        if($request->customer_id)
        {
            $customer_id = $request->customer_id;
        }
        if($request->customerId)
        {
            $customer_id = $request->customerId;
        }

        $cust_address = new CustomerAddress;
        $cust_address->city = $request->city;
        $cust_address->state = $request->state;        
        $cust_address->customer_id = $customer_id;
        $cust_address->fullname = $request->full_name;
        $cust_address->address_1 = $request->address_1;
        $cust_address->address_2 = $request->address_2;        
        $cust_address->country = $request->country;
        $cust_address->address_type = $request->address_type;
        $cust_address->pincode = $request->pincode;
        $cust_address->phone1 = $request->mobile;    
        if(isset($request->is_default) && $request->is_default == 1)
        {
            $is_default = CustomerAddress::where('customer_id', $customer_id)
            ->where('is_default', 1)->where('is_deleted', 0)->get();    
            if(count($is_default) >= 1)
            {
                CustomerAddress::where('customer_id', $customer_id)->update(['is_default' => 0]);
                $cust_address->is_default = 1;
            }               
        }    
        else
        {
            $cust_address->is_default = 0;   
        }                   
         
        if($cust_address->save())
        {
            $cart_master = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
            if($cart_master)
            {
                $cart_master->checkout_type = $request->Delivery;
                $cart_master->address_id = $cust_address->id;
                $cart_master->store_location_id = '0';
                $cart_master->save();                
            } 
            $result['status'] = 'true';
            return $result;
        }
        else
        {            
            return 'false';
        }
    }

    public static function updateCustomerAddress($request)
    {      
        if($request->customer_id)
        {
            $customer_id = $request->customer_id;
        }
        if($request->customerId)
        {
            $customer_id = $request->customerId;
        }

        $cust_address = CustomerAddress::where('id', $request->address_id)->where('is_deleted', 0)->first();  
        if(empty($cust_address))
        {
            $result['status'] = 'false';
            return $result;   
        }
        $cust_address->city = $request->city;
        $cust_address->state = $request->state;                       
        $cust_address->customer_id = $customer_id;
        $cust_address->fullname = $request->full_name;
        $cust_address->address_1 = $request->address_1;
        $cust_address->address_2 = $request->address_2;        
        $cust_address->country = $request->country;
        $cust_address->address_type = $request->address_type;
        $cust_address->pincode = $request->pincode;
        $cust_address->phone1 = $request->mobile;        
        if(isset($request->is_default) && $request->is_default == 1)
        {
            $is_default = CustomerAddress::where('id', '<>', $request->address_id)->where('customer_id', 
            $customer_id)->where('is_default', 1)->get();         
            if(count($is_default) >= 1)
            {
                CustomerAddress::where('customer_id', $customer_id)->update(['is_default' => 0]);
                $cust_address->is_default = 1;
            }    
            else
            {
                $cust_address->is_default = 1;
            }                          
        }    
        else
        {
            $cust_address->is_default = 0;   
        }  

        if($cust_address->save())
        {            
            $result['status'] = 'true';
            return $result;
        }
        else
        {            
            return 'false';
        }
    }
}
