<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Agent;
use Hash;

class Customer extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_unique_id',
        'first_name',
        'last_name',
        'name',
        'email',
        'gender',
        'is_verify',
        'is_approve',
        'password',
        'provider',
        'provider_id',
        'ip_address','os_name','browser_name','browser_version' ,'first_time_login',
        'loyalty_number',
        'loyalty_flag'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * This function is used to get customer unique identification number
     * @return mixed|string
     */
    public static function getCustomerUniqueId()
    {
        $last_unique_id = Customer::orderBy('id', 'desc')->pluck('customer_unique_id')->first();
        $uniqueId = getIdByLastUniqueId($last_unique_id, 'CR');
        return $uniqueId;
    }

    public static function saveCustomerLoginObj($user, $provider)
    {
        $customer_unique_id = \App\Models\Customer::getCustomerUniqueId();
        $customer = new \App\Models\Customer;
        $customer->customer_unique_id = $customer_unique_id;
        $customer->first_name =  $user->name;
        $customer->email = $user->email;
        $customer->provider = $provider;
        $customer->provider_id = $user->id;
        //$customer->ip_address = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
        $customer->ip_address = "";
        $customer->os_name = Agent::platform();
        $customer->browser_name = Agent::browser();
        $customer->browser_version = Agent::version($customer->browser_name);
        $customer->first_time_login = 1;
        $customer->created_at = date("Y-m-d H:i:s");
        $customer->save();
        return $customer;
    }
    // code by Nivedita(2-02-2021) for login and register from socialmedia with api
    public static function saveCustomerSignInObj($user, $provider)
    {
        $customer_unique_id = \App\Models\Customer::getCustomerUniqueId();
        $customer = new \App\Models\Customer;
        $customer->customer_unique_id = $customer_unique_id;
        $customer->first_name =  $user->firstName;
        $customer->last_name =  $user->lastName;
        $customer->email = $user->email;
        $customer->provider = $provider;
        $customer->provider_id = $user->provider_id;
        $customer->language_id = $user->language_id;
        //$customer->ip_address = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
        $customer->ip_address = "";
        $customer->os_name = Agent::platform();
        $customer->browser_name = Agent::browser();
        $customer->browser_version = Agent::version($customer->browser_name);
        $customer->first_time_login = 1;
        $customer->is_verify = 1;
        $customer->created_at = date("Y-m-d H:i:s");
        $customer->save();
        $userdevice = new \App\Models\UserDevice;
        $userdevice->user_id=$customer->id;
        if($user->deviceOSType=='ios')
          $userdevice->user_device_type_id=1;
        else
          $userdevice->user_device_type_id=2;
        $userdevice->device_id=$user->deviceId;
        $userdevice->fcm_token=$user->firebaseToken;
        $userdevice->save();
        return $customer;
    }

    public static function saveCustomerRegisterObj($request)
    {
        $customer_unique_id = \App\Models\Customer::getCustomerUniqueId();
        $customer = new \App\Models\Customer;
        $customer->customer_unique_id = $customer_unique_id;
        $customer->first_name =  $request->firstName;
        $customer->last_name = $request->lastName;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        //$customer->ip_address = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
        $customer->ip_address = "";
        $customer->os_name = Agent::platform();
        $customer->browser_name = Agent::browser();
        $customer->browser_version = Agent::version($customer->browser_name);

        $customer->loyalty_number = $request->loyalty_number;
        $customer->loyalty_flag = ($request->loyalty_flag) ? $request->loyalty_flag : "0";

        $customer->password = Hash::make($request->password);
        $customer->created_at = date("Y-m-d H:i:s");
        $customer->save();
        
        if(isset($request->timezone) && isset($request->zone_time))
        {
            $customer_timezone = \App\Models\CustomerTimezone::where('customer_id', $customer->id)->first();
            if($customer_timezone)
            {
                $customer_timezone->timezone = $request->timezone;
                $customer_timezone->zone = $request->zone_time;
                $customer_timezone->save();
            }
            else
            {
                $customer_timezone = new \App\Models\CustomerTimezone;
                $customer_timezone->customer_id = $customer->id;
                $customer_timezone->timezone = $request->timezone;
                $customer_timezone->zone = $request->zone_time;
                $customer_timezone->save();
            }
        }        
        
        return $customer;
    }

    public static function updateMyAccount($request, $customer_id)
    {
        $my_account = Customer::where('id', $customer_id)->first();
        if($my_account)
        {
            $my_account->first_name = $request->firstName;
            $my_account->last_name = $request->lastName;
            $my_account->email = $request->email;
            $my_account->mobile = $request->mobile;
            $my_account->date_of_birth = $request->dateOfBirth;
            $my_account->gender = $request->gender;
            $my_account->loyalty_number = $request->loyalty_number ? $request->loyalty_number : "";
            $my_account->save();
            return "true";
        }
        else
        {
            return "false";
        }
    }

}
