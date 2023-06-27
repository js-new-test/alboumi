<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Traits\CommonTrait;

class TaxClass extends Model
{
	use CommonTrait;
    protected $table = 'tax_class';

    protected $fillable = ['id','name','tax_rate_ids','deleted_at'];


    public function taxClassDropDown()
    {
        $resultArr = [];

        $taxclass = TaxClass::select('id', 'name')
        ->whereNull('deleted_at')->get();

        $index = 0;
        foreach ($taxclass as $taxclass) {
            $resultArr[$index]['id'] = $taxclass->id;
            $resultArr[$index++]['category'] = $taxclass->name;
        }
        return $resultArr;
    }


}
