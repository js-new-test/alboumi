<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\DB\Traits\SortableTrait;

class Pincode extends Model
{
    // use SortableTrait;
    protected $table = 'pincodes';

    protected $fillable = [
        'state_id',
        'country_id',
        'city_id',
        'pincode',
        'status',
    ];

    /*
   *  Adding Pincode
   *  $params ['id','name'..]
   */
    public function savePincode($params)
    {
        $data = $params->all();
        $states = implode(',' , $data['states']);
        $cities = implode(',' , $data['cities']);
        return Pincode::create([
            'country_id' => $data['country_id'],
            'state_id' => $data['state_id'],
            'city_id' => $data['city_id'],
            'pincode' => $data['pincode'],
            'status' => $data['status']
        ]);


    }
    /*
 *  Updating pincode
 *  $params ['id','name'..]
 */
    public function updatePincode($id, $request)
    {
        $pincode = Pincode::findOrFail($id);
        $data = $request->all();

        $pincode->country_id = $data['country_id'];
        $pincode->state_id = $data['state_id'];
        $pincode->city_id = $data['city_id'];
        $pincode->pincode = $data['pincode'];
        $pincode->status = $data['status'];
        return $pincode->save();

    }
    /*
        *  Deleting pincode
        *  $params ['id']
        */
    public function deletePincode($id)
    {
        $pincode = Pincode::findOrFail($id);
        return $pincode->delete();

    }
}
