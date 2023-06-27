<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
class Manufacturer extends Model
{

    protected $table = 'manufacturers';
    protected $fillable = [
        'name',
        'desciription',
        'status',
        'slug'
    ];
    public function images() {
        return $this->morphMany('App\Models\Image', 'imageable');
    }

    public function brandDetails()
    {
        return $this->hasMany('App\Models\BrandDetails', 'brand_id', 'id');
    }

    /*
     *  Adding Manufacturer 
     *  $params ['id','name'..]
     */
    public function saveManufacturer($params)
    {
        $data = $params->all();

        $Manufacturer = new Manufacturer();
        $Manufacturer->name = $data['name'];
        $Manufacturer->status = $data['status'];
         $Manufacturer->save();
        dd($Manufacturer->id);
//        return Manufacturer::create([
//            'name' => $data['name'],
//            'status' => $data['status']
//        ]);


    }

    /*
     *  Updating Manufacturer 
     *  $params ['id','name'..]
     */
    public function updateManufacturer($id, $request)
    {
        $Manufacturer = Manufacturer::findOrFail($id);
        $data = $request->all();

        $Manufacturer->name = $data['name'];
        $Manufacturer->status = $data['status'];
        return $Manufacturer->save();

    }

    /*
     *  Deleting Manufacturer 
     *  $params ['id']
     */
    public function deleteManufacturer($id)
    {
        $Manufacturer = Manufacturer::findOrFail($id);
        return $Manufacturer->delete();

    }

    /*
     *  Population manufacturer into dropdown
     */
    public function getManufacturerList($type='',$id=null)
    {
        if(isset($id) && !empty($id)){
            $product=new Product();
            $manufacturerIds=$product->getSellerManufacturerIds($id);
            return Manufacturer::where('status', '=', 'Active')->whereIn('id',$manufacturerIds)->orderBy('name', 'asc')->lists('name', 'id')->toArray();
        }

        return Manufacturer::where('status', '=', 'Active')->orderBy('name', 'asc')->lists('name', 'id')->toArray();
    }

    public function getManufacturerDetails($column, $value, $selectedFields = array('id'))
    {
        $results = Manufacturer::where($column, '=', $value)->first($selectedFields)->toArray();
        return $results;
    }
    public function getBrandImageByType($image_type)
    {
        $image = $this->images()->where('image_type','=',$image_type)->first();
        if ($image) {
            return $image;
        } else {
            $image = new Image();
            $image->name        = 'default_main_image';
            $image->small_image = 'default_small_image';
            $image->thumb_image = 'default_thumb_image';
            return $image;
        }
        return $image;
    }
}
