<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\DB\Traits\SortableTrait;

class PincodeCod extends Model
{
    // use SortableTrait;
    protected $table = 'pincode_cod';


    /*
   *  Adding PincodeCod
   *  $params ['id','name'..]
   */
    public function savePincode($params)
    {
        $data = $params->all();
        $states = implode(',' , $data['states']);
        return PincodeCod::create([
            'state_id' => $data['state_id'],
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
        $pincode = PincodeCod::findOrFail($id);
        $data = $request->all();

        $pincode->country_id = $data['country_id'];
        $pincode->state_id = $data['state_id'];
        $pincode->city_id = $data['city_id'];
        $pincode->pincode = $data['pincode'];
        $pincode->status = $data['status'];
        return $pincode->save();

    }

    public function deletePincode($id)
    {
        $pincode = PincodeCod::findOrFail($id);
        return $pincode->delete();

    }
}
