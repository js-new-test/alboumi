<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\CustGroupPrice;

class CustomerGroups extends Model
{
    protected $table = 'customer_groups';

    protected $fillable = ['group_name'];

    public function groupDropDown($product_id)
    {
      $priceGropId = CustGroupPrice::select('customer_group_id')->where('product_id','=',$product_id)->get();
        $CustGroupPrice = CustomerGroups::select('customer_groups.id', 'customer_groups.group_name')
        ->whereNotIn('id',$priceGropId)
        ->whereNull('customer_groups.deleted_at')->get();
        return $CustGroupPrice;
    }
}
