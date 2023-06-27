<?php
/**
 * Created by PhpStorm.
 * User: Pinkal Vansia
 * Date: 3/13/2015
 * Time: 5:45 PM
 */

namespace App\DB\Traits;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

trait SortableTrait
{

    /**
     * Generate a table header link
     *
     * @param $col
     * @param null $title
     * @return string
     */

    public static function link_to_sorting_action($col, $title = null)
    {
        if (is_null($title)) {
            $title = str_replace('_', ' ', $col);
            $title = ucfirst($title);
        }
        $current_params = Route::current()->parameters();

        $indicator = (Input::get('s') == $col ? (Input::get('o') === 'asc' ? '&uarr;' : '&darr;') : null);
        $parameters = array_merge(Input::get(),
            array('s' => $col, 'o' => (Input::get('o') === 'asc' ? 'desc' : 'asc')));
        $parameters = $parameters + $current_params;

        return link_to_route(Route::currentRouteName(), "$title $indicator", $parameters);
    }

    /**
     * Sortable scope
     *
     * @param $query
     * @return mixed
     */
    public function scopeSortable($query)
    {
        if (Input::has('s') && Input::has('o')) {
            if (strpos(Input::get('s'), '.') !== false) {
                list($table, $searchColumn) = explode('.', Input::get('s'));
                $columns = Schema::getColumnListing($table);
            } else {
                $searchColumn = Input::get('s');
                $columns = Schema::getColumnListing($this->table);
            }
            if (in_array($searchColumn, $columns) && in_array(strtolower(Input::get('o')), ['asc', 'desc'])) {
                return $query->orderBy(Input::get('s'), Input::get('o'));
            } else {
                return $query;
            }
        } else {
            return $query;
        }
    }
} 