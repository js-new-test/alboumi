<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MegaMenu extends Model
{   
    protected $table = 'megamenu';
    protected $fillable = ['type', 'name','small_image','big_image','icon_image','description','sort_order','deleted_at'];

    public function getAllMegaMenus()
    {
        $megamenus = MegaMenu::select('type', 'name','small_image','big_image','icon_image','description','sort_order')
                            ->whereNull('deleted_at')
                            ->orderBy('sort_order', 'asc')
                            ->get();

        return $megamenus;
    }
}
